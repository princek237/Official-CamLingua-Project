<?php
/**
 * CamPayService — PHP 7.4 compatible
 *
 * Wraps the CamPay REST API.
 * Docs: https://campay.net  |  Sandbox: https://demo.campay.net
 *
 * Authentication:
 *   Option A (preferred): set CAMPAY_PERMANENT_TOKEN in .env
 *   Option B: set CAMPAY_USERNAME + CAMPAY_PASSWORD → fetches a short-lived token
 *
 * Key endpoints used:
 *   POST /api/token/           → get short-lived access token (Option B)
 *   POST /api/collect/         → initiate a Mobile Money charge
 *   GET  /api/transaction/{ref}/ → poll transaction status
 */

declare(strict_types=1);

namespace App\Services;

class CamPayService
{
    private string $baseUrl;
    private string $permanentToken;
    private string $username;
    private string $password;

    /** Cached short-lived token (Option B) */
    private ?string $accessToken = null;

    public function __construct(array $config)
    {
        $env = strtoupper($config['environment'] ?? 'DEV');
        $this->baseUrl = ($env === 'PROD')
            ? 'https://campay.net/api'
            : 'https://demo.campay.net/api';

        $this->permanentToken = $config['permanent_token'] ?? '';
        $this->username       = $config['username']        ?? '';
        $this->password       = $config['password']        ?? '';
    }

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Initiate a Mobile Money collection (USSD push).
     *
     * @param  string $phone             International format, e.g. "237677123456"
     * @param  int    $amount            Amount in XAF (whole integer)
     * @param  string $description       Short label shown to payer
     * @param  string $externalReference Your unique reference (UUID)
     * @return array  CamPay response: {reference, ussd_code, operator} on success
     * @throws \RuntimeException on HTTP / curl error
     */
    public function collect(string $phone, int $amount, string $description, string $externalReference): array
    {
        return $this->post('/collect/', [
            'amount'             => (string) $amount,
            'currency'           => 'XAF',
            'from'               => $phone,
            'description'        => $description,
            'external_reference' => $externalReference,
        ]);
    }

    /**
     * Poll the status of an initiated transaction.
     *
     * @param  string $reference The campay_reference returned by collect()
     * @return array  {reference, status, amount, currency, operator, …}
     *                status: PENDING | SUCCESSFUL | FAILED
     */
    public function getTransactionStatus(string $reference): array
    {
        return $this->get('/transaction/' . urlencode($reference) . '/');
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    /**
     * Return the Bearer token to use for all API calls.
     * Prefers the permanent token; falls back to username/password flow.
     */
    private function resolveToken(): string
    {
        if ($this->permanentToken !== '') {
            return $this->permanentToken;
        }

        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        // Fetch a short-lived token
        $response = $this->rawPost('/token/', [
            'username' => $this->username,
            'password' => $this->password,
        ], false);

        if (empty($response['token'])) {
            throw new \RuntimeException('CamPay: failed to obtain access token.');
        }

        $this->accessToken = $response['token'];
        return $this->accessToken;
    }

    /** Authenticated POST */
    private function post(string $path, array $payload): array
    {
        return $this->rawPost($path, $payload, true);
    }

    /** Authenticated GET */
    private function get(string $path): array
    {
        $token = $this->resolveToken();
        $url   = $this->baseUrl . $path;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Token ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $raw   = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            throw new \RuntimeException("CamPay cURL error ({$errno}): {$error}");
        }

        $data = json_decode((string) $raw, true) ?? [];

        if ($httpCode >= 400) {
            $msg = $data['detail'] ?? $data['message'] ?? "HTTP {$httpCode}";
            throw new \RuntimeException("CamPay API error: {$msg}");
        }

        return $data;
    }

    /**
     * Raw POST — optionally adds Bearer auth.
     *
     * @param bool $authenticated  false for the /token/ endpoint itself
     */
    private function rawPost(string $path, array $payload, bool $authenticated): array
    {
        $headers = ['Content-Type: application/json'];

        if ($authenticated) {
            $headers[] = 'Authorization: Token ' . $this->resolveToken();
        }

        $url = $this->baseUrl . $path;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $raw      = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            throw new \RuntimeException("CamPay cURL error ({$errno}): {$error}");
        }

        $data = json_decode((string) $raw, true) ?? [];

        if ($httpCode >= 400) {
            $msg = $data['detail'] ?? $data['message'] ?? "HTTP {$httpCode}";
            throw new \RuntimeException("CamPay API error: {$msg}");
        }

        return $data;
    }
}
