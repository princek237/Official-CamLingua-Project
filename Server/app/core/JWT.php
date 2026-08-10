<?php
/**
 * Minimal JWT — HS256 — PHP 7.4 compatible
 */

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class JWT
{
    private static function getSecret(): string
    {
        $config = require APP_PATH . '/config/config.php';
        return $config['jwt']['secret'];
    }

    private static function getExpiryHours(): int
    {
        $config = require APP_PATH . '/config/config.php';
        return $config['jwt']['expiry_hours'];
    }

    public static function encode(array $payload): string
    {
        $header = self::base64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));

        $payload['iat'] = time();
        $payload['exp'] = time() + self::getExpiryHours() * 3600;

        $body      = self::base64url(json_encode($payload));
        $signature = self::base64url(hash_hmac('sha256', "{$header}.{$body}", self::getSecret(), true));

        return "{$header}.{$body}.{$signature}";
    }

    public static function decode(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('Invalid token format');
        }

        [$header, $body, $signature] = $parts;

        $expectedSig = self::base64url(hash_hmac('sha256', "{$header}.{$body}", self::getSecret(), true));

        if (!hash_equals($expectedSig, $signature)) {
            throw new RuntimeException('Invalid token signature');
        }

        $payload = json_decode(self::base64urlDecode($body), true);
        if (!is_array($payload)) {
            throw new RuntimeException('Invalid token payload');
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new RuntimeException('Token has expired');
        }

        return $payload;
    }

    public static function fromHeader(): ?string
    {
        // Check all locations Apache may place the header
        $auth = $_SERVER['HTTP_AUTHORIZATION']
             ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
             ?? getenv('HTTP_AUTHORIZATION')
             ?? '';

        if (strpos($auth, 'Bearer ') === 0) {
            return substr($auth, 7);
        }
        return null;
    }

    private static function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64urlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
