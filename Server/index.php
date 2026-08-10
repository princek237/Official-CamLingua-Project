<?php
/**
 * CamLingua API — Entry Point
 * All HTTP requests are routed through this file via .htaccess
 */

declare(strict_types=1);

// ── Path constants ────────────────────────────────────────────────────────────
define('ROOT_PATH', __DIR__);
define('APP_PATH',  __DIR__ . '/app');

// ── Load config (parses .env) ─────────────────────────────────────────────────
$config = require APP_PATH . '/config/config.php';

// ── Environment & error display ───────────────────────────────────────────────
$env = $config['app']['env'] ?? 'production';
define('APP_ENV', $env);

if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// ── Autoloader ────────────────────────────────────────────────────────────────
require_once APP_PATH . '/core/Autoloader.php';
Autoloader::register();

// ── Bootstrap ─────────────────────────────────────────────────────────────────
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/routes/api.php';

// ── Handle the request ────────────────────────────────────────────────────────
$router = Router::getInstance();
$router->dispatch();
