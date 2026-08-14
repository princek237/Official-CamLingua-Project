<?php
/**
 * TranslationController — PHP 7.4 compatible
 * POST /api/translate
 *
 * Uses the Hugging Face Inference API with the NLLB-200 distilled-600M model.
 * Falls back to a hardcoded mock if the HF API is unavailable.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Middleware\AuthMiddleware;

class TranslationController extends Controller
{
    /** @var Database */
    private $db;

    /**
     * Maps short language codes to NLLB-200 BCP-47 flower codes.
     * The translator page sends full NLLB codes directly (e.g. eng_Latn),
     * so these mappings handle any legacy short codes that might come through.
     * Full list: https://github.com/facebookresearch/flores/blob/main/flores200/README.md
     */
    private static $LANG_CODES = [
        // Legacy short codes
        'en'  => 'eng_Latn', 'fr'  => 'fra_Latn', 'ar'  => 'arb_Arab',
        'es'  => 'spa_Latn', 'pt'  => 'por_Latn', 'de'  => 'deu_Latn',
        'it'  => 'ita_Latn', 'nl'  => 'nld_Latn', 'ru'  => 'rus_Cyrl',
        'zh'  => 'zho_Hans', 'ja'  => 'jpn_Jpan', 'ko'  => 'kor_Hang',
        'hi'  => 'hin_Deva', 'tr'  => 'tur_Latn', 'id'  => 'ind_Latn',
        'vi'  => 'vie_Latn', 'pl'  => 'pol_Latn', 'uk'  => 'ukr_Cyrl',
        'sw'  => 'swh_Latn', 'ha'  => 'hau_Latn', 'yo'  => 'yor_Latn',
        'ig'  => 'ibo_Latn', 'am'  => 'amh_Ethi', 'so'  => 'som_Latn',
        'bam' => 'bam_Latn', 'fuf' => 'fuv_Latn', 'fuv' => 'fuv_Latn',
        'ewo' => 'ewo_Latn', 'bas' => 'bas_Latn', 'dua' => 'dua_Latn',
        // Full NLLB codes pass through unchanged (handled in callLocalNllbApi)
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── POST /api/translate ────────────────────────────────────────────────────
    public function translate(): void
    {
        $user = AuthMiddleware::user();
        $body = $this->getBody();

        $errors = $this->validateRequired($body, ['source_lang', 'target_lang', 'text']);
        if ($errors) {
            $this->validationError($errors);
        }

        $sourceLang = $this->sanitize($body['source_lang']);
        $targetLang = $this->sanitize($body['target_lang']);
        $text       = trim($body['text']);

        if (empty($text)) {
            Response::badRequest('Text cannot be empty.');
        }

        $this->checkLimits($user, $text);

        // Attempt real NLLB-200 translation; fall back to mock on any failure
        $engine         = 'mock';
        $translatedText = null;

        $config = require APP_PATH . '/config/config.php';
        $apiUrl = $config['nllb']['api_url'];

        if ($apiUrl) {
            try {
                $translatedText = $this->callLocalNllbApi($apiUrl, $sourceLang, $targetLang, $text);
                $engine         = 'nllb-200';
            } catch (\RuntimeException $e) {
                $msg = $e->getMessage();
                // Service is down — tell the user clearly
                if (strpos($msg, 'unavailable') !== false || strpos($msg, 'connect') !== false) {
                    Response::error(
                        'Translation service is currently unavailable. Please make sure the CamLingua AI service is running.',
                        503
                    );
                }
                // Unsupported language — surface to the user
                if (strpos($msg, 'Unsupported language') !== false) {
                    Response::error($msg, 422);
                }
                // Any other error — fall through to mock silently
            } catch (\Exception $e) {
                // Network / unexpected error — fall through to mock
            }
        }

        if ($translatedText === null) {
            $translatedText = $this->mockTranslate($sourceLang, $targetLang, $text);
        }

        $historyId = $this->db->insert('translation_history', [
            'user_id'            => $user['id'],
            'source_lang'        => $sourceLang,
            'target_lang'        => $targetLang,
            'source_text'        => $text,
            'translated_text'    => $translatedText,
            'translation_engine' => $engine,
        ]);

        Response::success([
            'source_lang'     => $sourceLang,
            'target_lang'     => $targetLang,
            'source_text'     => $text,
            'translated_text' => $translatedText,
            'engine'          => $engine,
            'history_id'      => (int)$historyId,
        ], 'Translation completed.');
    }

    // ── Local Flask / NLLB-200 API ────────────────────────────────────────────

    /**
     * Calls the local Flask server (Server/main.py) running on port 5000.
     *
     * Request body (POST /translate):
     *   { "text": "Hello", "src_lang": "eng_Latn", "tgt_lang": "fra_Latn" }
     *
     * Response body (200 OK):
     *   { "translated_text": "Bonjour" }
     *
     * Error response (400):
     *   { "error": "No text provided" }
     *
     * @throws \RuntimeException on curl error, non-200 HTTP, service down, or unexpected response
     */
    private function callLocalNllbApi(string $url, string $from, string $to, string $text): string
    {
        // The selectors on the translator page already send full NLLB codes (e.g. eng_Latn).
        // If a short code slips through, map it; otherwise use as-is.
        $srcNllb = self::$LANG_CODES[$from] ?? $from;
        $tgtNllb = self::$LANG_CODES[$to]   ?? $to;

        // Validate that the codes look like NLLB codes (xxx_Xxxx)
        if (!preg_match('/^[a-z]{3}_[A-Z][a-z]{3}$/', $srcNllb) ||
            !preg_match('/^[a-z]{3}_[A-Z][a-z]{3}$/', $tgtNllb)) {
            throw new \RuntimeException(
                "Unsupported language: '{$from}' or '{$to}' is not supported by the translation model."
            );
        }

        // Same-language: return text unchanged
        if ($srcNllb === $tgtNllb) {
            return $text;
        }

        $payload = json_encode([
            'text'     => $text,
            'src_lang' => $srcNllb,
            'tgt_lang' => $tgtNllb,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 120,  // NLLB can be slow on first run; keep connection open
            CURLOPT_CONNECTTIMEOUT => 5,    // If Flask isn't up, fail fast
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        $curlErrNo = curl_errno($ch);
        curl_close($ch);

        // Connection refused / Flask not running
        if ($curlErrNo === CURLE_COULDNT_CONNECT || $curlErrNo === CURLE_OPERATION_TIMEDOUT) {
            throw new \RuntimeException('unavailable: could not connect to local translation service');
        }

        if ($curlErr) {
            throw new \RuntimeException('connect error: ' . $curlErr);
        }

        // Flask returned an application-level error (400)
        if ($httpCode === 400) {
            $err = json_decode($response, true);
            throw new \RuntimeException(
                isset($err['error']) ? $err['error'] : 'Bad request to translation service'
            );
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("Translation service returned HTTP {$httpCode}");
        }

        // Successful response: { "translated_text": "..." }
        $data = json_decode($response, true);

        if (is_array($data) && isset($data['translated_text'])) {
            return trim($data['translated_text']);
        }

        throw new \RuntimeException('Unexpected response from translation service');
    }

    // ── Mock fallback ─────────────────────────────────────────────────────────

    private function mockTranslate(string $from, string $to, string $text): string
    {
        $key = strtolower(trim($text)) . '|' . $from . '|' . $to;

        $db = [
            'hello|en|fr'               => 'Bonjour',
            'hello|en|ewo'              => 'Mbolo',
            'hello|en|bas'              => 'Ndínawo',
            'hello|en|dua'              => 'Mbote',
            'hello|en|bam'              => 'Welé',
            'hello|en|fuf'              => 'Jam waali',
            'good morning|en|ewo'       => 'Mboë',
            'good morning|en|fr'        => 'Bonjour',
            'thank you|en|fr'           => 'Merci',
            'thank you|en|ewo'          => 'Akiba',
            'how are you?|en|fr'        => 'Comment allez-vous?',
            'hello, how are you?|en|fr' => 'Bonjour, comment allez-vous?',
            'thank you very much|fr|bam'=> 'Ndo ndo',
            'where are you going?|en|dua' => 'O kɛ́ na wáa?',
            'i love cameroon|en|fr'     => "J'aime le Cameroun",
            'i love cameroon|en|ewo'    => 'Me dzing Kamerun',
        ];

        return $db[$key] ?? "[NLLB-200 unavailable] \"{$text}\" ({$from} → {$to})";
    }

    // ── Subscription limits ───────────────────────────────────────────────────

    private function checkLimits(array $user, string $text): void
    {
        $sub = $this->db->fetchOne(
            'SELECT s.limits FROM user_subscriptions us
             JOIN subscriptions s ON us.subscription_id = s.id
             WHERE us.user_id = ? AND us.status = ?',
            [$user['id'], 'active']
        );

        if (!$sub) {
            Response::forbidden('No active subscription found. Please subscribe to a plan.');
        }

        $limits   = json_decode($sub['limits'], true) ?? [];
        $maxChars = $limits['max_chars'] ?? -1;

        if ($maxChars > 0 && mb_strlen($text) > $maxChars) {
            Response::forbidden(
                "Your plan allows a maximum of {$maxChars} characters per translation. Upgrade for unlimited access."
            );
        }

        $perDay = $limits['translations_per_day'] ?? -1;
        if ($perDay > 0) {
            $count = $this->db->fetchOne(
                'SELECT COUNT(*) as cnt FROM translation_history
                 WHERE user_id = ? AND DATE(created_at) = CURDATE()',
                [$user['id']]
            );
            if ((int)$count['cnt'] >= $perDay) {
                Response::forbidden(
                    "You have reached your daily limit of {$perDay} translations. Upgrade for unlimited access."
                );
            }
        }
    }
}
