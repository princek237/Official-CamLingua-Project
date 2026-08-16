<?php
/**
 * cms_helper.php
 *
 * Loads all site content/settings from the database once per page request
 * and exposes a helper function cms() for safe HTML output.
 *
 * Usage in any PHP page:
 *   require_once 'includes/cms_helper.php';
 *   echo cms('home_hero_title');          // HTML-escaped string
 *   echo cms('home_hero_title', false);   // raw (for JS/JSON contexts)
 *
 * The $CMS global is populated once and reused on the same request.
 */

declare(strict_types=1);

if (!isset($CMS)) {

    $CMS = [];

    // ── Defaults (shown when DB has no value yet or before migration runs) ────
    $CMS_DEFAULTS = [
        // Global
        'site_name'             => 'CamLingua',
        'site_tagline'          => "Translate. Connect. Preserve Cameroon's Languages.",
        'platform_logo'         => '',
        'contact_email'         => 'support@camlingua.com',
        'contact_phone'         => '+237 6 12 34 56 78',
        'contact_location'      => 'Buea, Cameroon',
        'contact_response_time' => 'We typically reply within 24 hours',
        'social_github'         => '',
        'social_twitter'        => '',
        'social_linkedin'       => '',

        // Homepage
        'home_hero_badge'  => 'Cameroonian Language Translation System',
        'home_hero_title'  => "Translate. Connect.\nPreserve Cameroon's\nLanguages.",
        'home_hero_desc'   => 'CamLingua helps you translate between Cameroonian languages and the world. Fast, accurate and easy to use.',
        'home_hero_btn1'   => 'Start Translating',
        'home_hero_btn2'   => 'Explore Languages',
        'home_feat1_title' => 'AI-Powered',
        'home_feat1_desc'  => 'Accurate translations powered by advanced AI models.',
        'home_feat2_title' => 'Local Languages',
        'home_feat2_desc'  => 'Support for major Cameroonian languages and dialects.',
        'home_feat3_title' => 'Secure & Private',
        'home_feat3_desc'  => 'Your data is encrypted and your privacy is protected.',
        'home_feat4_title' => 'History & Sync',
        'home_feat4_desc'  => 'Access your past translations anytime, anywhere.',

        // About
        'about_hero_title'    => 'Building bridges through language.',
        'about_hero_desc'     => 'CamLingua is a Cameroonian language translation system built to break language barriers and promote access to linguistic heritage for our cultures.',
        'about_story_p1'      => 'CamLingua was born from a simple observation: millions of Cameroonians speak rich, vibrant languages — Ewondo, Bassa, Duala, Bamileke, Fulfulde — yet digital tools barely recognize their existence.',
        'about_story_p2'      => "We started as a small academic project determined to change that. By combining the power of Meta's NLLB-200 AI model with a community-driven dictionary, we created a system that not only translates words but carries the culture within them.",
        'about_story_p3'      => 'Today, CamLingua supports major Cameroonian languages and dialects. We believe language is identity — and every word preserved is a story kept alive.',
        'about_mission_title' => 'Our Mission',
        'about_mission_text'  => 'To break language barriers and make Cameroonian languages accessible to everyone through technology.',
        'about_vision_title'  => 'Our Vision',
        'about_vision_text'   => 'A world where every Cameroonian language is preserved, accessible, and celebrated in the digital age.',
        'about_stats_langs'   => '20+',
        'about_stats_trans'   => '50K+',
        'about_stats_users'   => '10K+',
        'about_cta_title'     => 'Ready to bridge the gap?',
        'about_cta_desc'      => 'Start translating today or contribute to our community dictionary. Every word you add helps preserve a language.',

        // Pricing
        'pricing_hero_badge' => 'Simple, Transparent Pricing',
        'pricing_hero_title' => 'Translate without limits',
        'pricing_hero_desc'  => 'Choose the plan that fits your needs. Upgrade or downgrade anytime — no hidden fees.',
        'pricing_cta_title'  => "Start preserving Cameroon's languages today",
        'pricing_cta_desc'   => 'Join over 50,000 users translating across 20+ Cameroonian languages.',
        'pricing_faq_1_q'    => 'Can I change my plan at any time?',
        'pricing_faq_1_a'    => 'Yes! You can upgrade or downgrade at any time. Changes take effect immediately for upgrades, and at the end of your billing cycle for downgrades.',
        'pricing_faq_2_q'    => 'Is there a free trial?',
        'pricing_faq_2_a'    => 'We offer a 7-day free trial for both Pro and Premium plans. No credit card required.',
        'pricing_faq_3_q'    => 'Can I use Mobile Money to pay?',
        'pricing_faq_3_a'    => 'Absolutely. CamLingua fully supports MTN Mobile Money and Orange Money.',

        // Contact
        'contact_hero_title' => 'Get in touch',
        'contact_hero_desc'  => "Have a question, suggestion, or just want to say hello? We'd love to hear from you.",
    ];

    // ── Load from database ────────────────────────────────────────────────────
    try {
        // Parse the same .env that the Server uses
        $envPath = __DIR__ . '/../Server/.env';
        $envVars = [];
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
                [$k, $v] = explode('=', $line, 2);
                $k = trim($k);
                $v = trim($v);
                if (strlen($v) >= 2 &&
                    (($v[0] === '"' && $v[-1] === '"') || ($v[0] === "'" && $v[-1] === "'"))) {
                    $v = substr($v, 1, -1);
                }
                $envVars[$k] = $v;
            }
        }

        $dbHost = $envVars['DB_HOST'] ?? 'localhost';
        $dbPort = $envVars['DB_PORT'] ?? '3306';
        $dbName = $envVars['DB_NAME'] ?? 'camlingua';
        $dbUser = $envVars['DB_USER'] ?? 'root';
        $dbPass = $envVars['DB_PASS'] ?? '';

        $pdo = new PDO(
            "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
            $dbUser,
            $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );

        $rows = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        foreach ($rows as $row) {
            $CMS[$row['setting_key']] = $row['setting_value'];
        }

    } catch (\Exception $e) {
        // Silently fall through to defaults — page still renders
        $CMS = [];
    }

    // Merge defaults underneath DB values (DB wins)
    foreach ($CMS_DEFAULTS as $k => $default) {
        if (!isset($CMS[$k]) || $CMS[$k] === '') {
            $CMS[$k] = $default;
        }
    }
}

/**
 * Get a CMS value, HTML-escaped by default.
 *
 * @param string $key      The settings key.
 * @param bool   $escape   Whether to htmlspecialchars the output (default true).
 * @param string $default  Fallback if key is missing entirely.
 */
function cms(string $key, bool $escape = true, string $default = ''): string
{
    global $CMS;
    $val = $CMS[$key] ?? $default;
    return $escape ? htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $val;
}

/**
 * Same as cms() but converts \n to <br> — useful for multi-line hero titles.
 */
function cms_nl2br(string $key): string
{
    global $CMS;
    $val = $CMS[$key] ?? '';
    return nl2br(htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
}
