<?php
// Accessible via browser: http://localhost/CamLingua/Server/reset.php
// Resets test user daily count AND verifies HF API config

define('ROOT_PATH', __DIR__);
define('APP_PATH',  __DIR__ . '/app');

foreach (file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if (!$line || $line[0] === '#' || strpos($line,'=') === false) continue;
    [$k,$v] = explode('=', $line, 2);
    $k = trim($k); $v = trim($v);
    if (!isset($_ENV[$k])) { $_ENV[$k]=$v; putenv("$k=$v"); }
}

$pdo = new PDO(
    "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
    $_ENV['DB_USER'], $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Delete all translations made TODAY by any user (full reset for testing)
$pdo->exec("DELETE FROM translation_history WHERE DATE(created_at) = CURDATE()");

// Report config
$config  = require APP_PATH . '/config/config.php';
$hfUrl   = $config['nllb']['api_url'];
$hfKey   = $config['nllb']['api_key'];

// Test HF from Apache (has internet)
$hfStatus = '(not tested)';
if ($hfUrl && $hfKey) {
    $payload = json_encode([
        'inputs'     => 'Hello',
        'parameters' => ['src_lang'=>'eng_Latn','tgt_lang'=>'fra_Latn'],
    ]);
    $ch = curl_init($hfUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$hfKey,'Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 40,
    ]);
    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $cerr = curl_error($ch);
    curl_close($ch);

    if ($cerr) {
        $hfStatus = "CURL ERROR: $cerr";
    } elseif ($code === 200) {
        $d = json_decode($raw, true);
        $translation = $d[0]['translation_text'] ?? '(no translation_text key)';
        $hfStatus = "HTTP 200 — 'Hello' => '$translation'";
    } elseif ($code === 503) {
        $d = json_decode($raw, true);
        $hfStatus = "HTTP 503 — model loading, ~" . ($d['estimated_time'] ?? '?') . "s";
    } else {
        $hfStatus = "HTTP $code — $raw";
    }
}

header('Content-Type: text/plain');
echo "=== CamLingua Reset & HF Test ===\n\n";
echo "Today's translations cleared.\n\n";
echo "HF API URL: $hfUrl\n";
echo "HF API Key: " . ($hfKey ? substr($hfKey,0,12).'...' : '(empty)') . "\n";
echo "HF Test:    $hfStatus\n\n";
echo "You can now visit http://localhost/CamLingua/translator.php to test.\n";
