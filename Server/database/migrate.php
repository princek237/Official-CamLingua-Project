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
echo str_repeat('-', 40) . $nl;

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

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "OK Database '{$name}' ensured{$nl}";

    $pdo->exec("USE `{$name}`");

    $sql = file_get_contents(__DIR__ . '/schema.sql');
    if (!$sql) throw new RuntimeException('Could not read schema.sql');

    $statements = array_filter(array_map('trim', explode(';', $sql)), function($s) { return $s !== ''; });

    $count = 0;
    foreach ($statements as $statement) {
        if (trim($statement) === '') continue;
        try { $pdo->exec($statement); $count++; }
        catch (PDOException $e) { echo "WARN: " . $e->getMessage() . $nl; }
    }

    echo "OK Executed {$count} SQL statements{$nl}";
    echo str_repeat('-', 40) . $nl;
    echo "OK Migration complete!{$nl}{$nl}";
    echo "Default credentials:{$nl}";
    echo "  Admin  — admin@camlingua.com  / admin123{$nl}";
    echo "  Tester — test@camlingua.com   / test123{$nl}";

} catch (PDOException $e) {
    echo "FAIL Migration failed: " . $e->getMessage() . $nl;
    exit(1);
}

if (!$isCli) echo "</pre>\n";
