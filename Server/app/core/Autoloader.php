<?php
/**
 * PSR-4 style autoloader — PHP 7.4 compatible
 */

declare(strict_types=1);

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    public static function load(string $class): void
    {
        $prefixes = [
            'App\\Controllers\\' => APP_PATH . '/controllers/',
            'App\\Models\\'      => APP_PATH . '/models/',
            'App\\Middleware\\'  => APP_PATH . '/middleware/',
            'App\\Services\\'   => APP_PATH . '/services/',
            'App\\Core\\'       => APP_PATH . '/core/',
        ];

        foreach ($prefixes as $prefix => $baseDir) {
            if (strpos($class, $prefix) !== 0) continue;
            $relative = substr($class, strlen($prefix));
            $file     = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
}
