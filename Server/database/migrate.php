<?php
/**
 * CamLingua — DB Migration Runner
 * Run from CLI:    php database/migrate.php
 * Or browser:      http://localhost/CamLingua/Server/database/migrate.php  (dev only)
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');

function loadEnv(string $path): void
{
    if (!file_exists($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || substr($line, 0, 1) === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k); $v = trim($v);
        if (
            (substr($v, 0, 1) === '"' && substr($v, -1) === '"') ||
            (substr($v, 0, 1) === "'" && substr($v, -1) === "'")
        ) {
            $v = substr($v, 1, -1);
        }
        if (!isset($_ENV[$k])) { $_ENV[$k] = $v; putenv("{$k}={$v}"); }
    }
}

loadEnv(ROOT_PATH . '/.env');

$isCli     = PHP_SAPI === 'cli';
$nl        = $isCli ? "\n" : "<br>\n";
$isDevMode = ($_ENV['APP_ENV'] ?? 'production') === 'development';

if (!$isCli && !$isDevMode) {
    http_response_code(403);
    die('Migration script is only accessible in development mode or via CLI.');
}

if (!$isCli) echo "<pre>\n";

echo "CamLingua — Database Migration{$nl}";
echo str_repeat('=', 48) . $nl;

$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = (int)($_ENV['DB_PORT'] ?? 3306);
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$name = $_ENV['DB_NAME'] ?? 'camlingua';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // ── 1. Ensure database exists ─────────────────────────────────────────────
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "OK  Database '{$name}' ensured{$nl}";
    $pdo->exec("USE `{$name}`");

    // ── 2. Run schema.sql (full fresh install) ────────────────────────────────
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    if (!$sql) throw new RuntimeException('Could not read schema.sql');

    $count = 0;
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if ($stmt === '') continue;
        try { $pdo->exec($stmt); $count++; }
        catch (PDOException $e) {
            // Warn but continue — duplicate key errors on re-run are expected
            echo "WARN [{$count}] " . $e->getMessage() . $nl;
        }
    }
    echo "OK  schema.sql — {$count} statements executed{$nl}";

    // ── 3. Run incremental migrations ─────────────────────────────────────────
    $migrations = [
        'migration_v2.sql'          => 'v2 — status column, languages & settings tables',
        'migration_campay.sql'      => 'CamPay — payments table',
        'migration_role_audit.sql'  => 'Role audit — role_assigned_by / role_assigned_at columns',
    ];

    foreach ($migrations as $file => $label) {
        $path = __DIR__ . '/' . $file;
        if (!file_exists($path)) {
            echo "SKIP {$file} not found{$nl}";
            continue;
        }
        $sql = file_get_contents($path);
        $ran = 0;
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
            if ($stmt === '' || strpos(ltrim($stmt), '--') === 0) continue;
            try { $pdo->exec($stmt); $ran++; }
            catch (PDOException $e) {
                echo "WARN [{$file}] " . $e->getMessage() . $nl;
            }
        }
        echo "OK  {$label} — {$ran} statements{$nl}";
    }

    // ── 4. Summary ────────────────────────────────────────────────────────────
    echo str_repeat('=', 48) . $nl;
    echo "OK  Migration complete!{$nl}{$nl}";

    echo "Default login credentials{$nl}";
    echo str_repeat('-', 48) . $nl;
    echo "  ADMIN   URL  : http://localhost/CamLingua/login.php{$nl}";
    echo "  Email        : admin@camlingua.com{$nl}";
    echo "  Password     : admin123{$nl}";
    echo "{$nl}";
    echo "  After login the admin sees an [Admin] badge in the nav.{$nl}";
    echo "  Click it to open the Admin Dashboard (admin.php).{$nl}";
    echo "{$nl}";
    echo "  TESTER  URL  : http://localhost/CamLingua/login.php{$nl}";
    echo "  Email        : test@camlingua.com{$nl}";
    echo "  Password     : test123{$nl}";
    echo str_repeat('-', 48) . $nl;
    echo "{$nl}";
    echo "IMPORTANT: Change the admin password after first login!{$nl}";

} catch (PDOException $e) {
    echo "FAIL Migration failed: " . $e->getMessage() . $nl;
    exit(1);
}

if (!$isCli) echo "</pre>\n";
