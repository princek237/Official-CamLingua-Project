<?php
define('ROOT_PATH', __DIR__);

$un = '3SrMy8X1Vr-d1iMMEenjHr1lnjGOm62izuYVn4us0OPkSK0Zahz1um59BA5puVM5WiCtwHbWWgZfC-h1w_51Lg';
$pw = 'kwhqJYuGdoTYYetVNKGosXiYTSDXDyzONE1_e-wkJBwcAtor_-TYWP9F4FjSMtTBai56EUaW7bjyHxz11SrpjA';
$base = 'https://demo.campay.net/api';
$ref  = 'd33110bf-9a02-4acb-b6c5-ada6d834bba6'; // latest campay_reference

// Step 1: get token
$ch = curl_init($base . '/token/');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>json_encode(['username'=>$un,'password'=>$pw]),
    CURLOPT_HTTPHEADER=>['Content-Type: application/json'], CURLOPT_TIMEOUT=>15]);
$r = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($r['token'])) { echo "Token fetch failed: " . print_r($r, true); exit; }
$token = $r['token'];
echo "Token OK\n\n";

// Step 2: check transaction status
$ch = curl_init($base . '/transaction/' . urlencode($ref) . '/');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>['Authorization: Token ' . $token, 'Content-Type: application/json'],
    CURLOPT_TIMEOUT=>15]);
$raw  = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $code\n";
echo $raw . "\n";
