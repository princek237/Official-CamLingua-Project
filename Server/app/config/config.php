<?php
/**
 * CamLingua Configuration — PHP 7.4 compatible
 */

declare(strict_types=1);

if (!function_exists('loadEnv')) {
    function loadEnv(string $path): void
    {
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || substr($line, 0, 1) === '#' || strpos($line, '=') === false) continue;
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);
            if (
                (substr($value, 0, 1) === '"'  && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'"  && substr($value, -1) === "'")
            ) {
                $value = substr($value, 1, -1);
            }
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key]    = $value;
                $_SERVER[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

loadEnv(ROOT_PATH . '/.env');

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === '') return $default;
        switch (strtolower((string)$value)) {
            case 'true':  return true;
            case 'false': return false;
            case 'null':  return null;
            default:      return $value;
        }
    }
}

return [
    'app' => [
        'env'  => env('APP_ENV', 'production'),
        'url'  => env('APP_URL', 'http://localhost'),
        'name' => 'CamLingua',
    ],
    'db' => [
        'host'    => env('DB_HOST', 'localhost'),
        'port'    => (int) env('DB_PORT', 3306),
        'name'    => env('DB_NAME', 'camlingua'),
        'user'    => env('DB_USER', 'root'),
        'pass'    => env('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
    'jwt' => [
        'secret'       => env('JWT_SECRET', 'fallback_dev_secret'),
        'expiry_hours' => (int) env('JWT_EXPIRY_HOURS', 24),
    ],
    'nllb' => [
        'api_url' => env('NLLB_API_URL', ''),
        'api_key' => env('NLLB_API_KEY', ''),
    ],
    'mail' => [
        'host'      => env('MAIL_HOST', 'smtp.mailtrap.io'),
        'port'      => (int) env('MAIL_PORT', 587),
        'user'      => env('MAIL_USER', ''),
        'pass'      => env('MAIL_PASS', ''),
        'from'      => env('MAIL_FROM', 'no-reply@camlingua.com'),
        'from_name' => env('MAIL_FROM_NAME', 'CamLingua'),
    ],
];
