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
     * Maps our short language codes to NLLB-200 BCP-47 flower codes.
     * Full list: https://github.com/facebookresearch/flores/blob/main/flores200/README.md
     */
    private static $LANG_CODES = [
        'en'     => 'eng_Latn',   // English
        'fr'     => 'fra_Latn',   // French
        'ewo'    => 'ewo_Latn',   // Ewondo
        'bas'    => 'bas_Latn',   // Bassa (Mbene)
        'dua'    => 'dua_Latn',   // Duala
        'bam'    => 'bam_Latn',   // Bambara / Bamileke approximation
        'fuf'    => 'fuf_Adlm',   // Fulfulde (Pular)
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
        $apiKey = $config['nllb']['api_key'];

        if ($apiUrl && $apiKey) {
            try {
                $translatedText = $this->callNllbApi($apiUrl, $apiKey, $sourceLang, $targetLang, $text);
                $engine         = 'nllb-200';
            } catch (\RuntimeException $e) {
                // If the model is still loading, surface that to the user
                if (strpos($e->getMessage(), 'loading') !== false) {
                    Response::error($e->getMessage() . ' — The AI model is warming up, please try again in a moment.', 503);
                }
                // Any other HF error — fall through to mock silently
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

    // ── Hugging Face NLLB-200 Inference API ───────────────────────────────────

    /**
     * Calls the HF Inference API exactly as described in the project's PHP snippet.
     *
     * Request body:
     *   { "inputs": "<text>", "parameters": { "src_lang": "eng_Latn", "tgt_lang": "fra_Latn" } }
     *
     * Response body (success):
     *   [{ "translation_text": "..." }]
     *
     * @throws \RuntimeException on curl error, non-200 HTTP, or unexpected response shape
     */
    private function callNllbApi(string $url, string $key, string $from, string $to, string $text): string
    {
        // Convert short codes to NLLB BCP-47 codes
        $srcNllb = self::$LANG_CODES[$from] ?? null;
        $tgtNllb = self::$LANG_CODES[$to]   ?? null;

        if (!$srcNllb || !$tgtNllb) {
            throw new \RuntimeException("Unsupported language code: {$from} or {$to}");
        }

        // Same-language pass-through
        if ($srcNllb === $tgtNllb) {
            return $text;
        }

        $payload = [
            'inputs'     => $text,
            'parameters' => [
                'src_lang' => $srcNllb,
                'tgt_lang' => $tgtNllb,
            ],
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $key,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,   // HF cold-start can be slow
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            throw new \RuntimeException('cURL error: ' . $curlErr);
        }

        // HF returns 503 while the model is loading ("estimated_time" in body)
        if ($httpCode === 503) {
            $loading = json_decode($response, true);
            $wait    = isset($loading['estimated_time']) ? round($loading['estimated_time']) : '?';
            throw new \RuntimeException("Model is loading, try again in ~{$wait}s");
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("HF API returned HTTP {$httpCode}: {$response}");
        }

        // Success response is an array: [{"translation_text": "..."}]
        $data = json_decode($response, true);

        if (is_array($data) && isset($data[0]['translation_text'])) {
            return trim($data[0]['translation_text']);
        }

        // Some HF models return a flat object
        if (is_array($data) && isset($data['translation_text'])) {
            return trim($data['translation_text']);
        }

        throw new \RuntimeException('Unexpected HF API response: ' . $response);
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
