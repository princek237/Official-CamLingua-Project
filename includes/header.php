<?php
$pageTitle  = $pageTitle  ?? 'CamLingua - Bridge the Language Gap';
$extraCss   = $extraCss   ?? [];
$activePage = $activePage ?? '';

// Load CMS content (safe — uses defaults if DB is unreachable)
require_once __DIR__ . '/cms_helper.php';

$_siteName = cms('site_name');
$_logoUrl  = cms('platform_logo', false); // raw URL, may be empty
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Base styles for layout/header/footer -->
    <link rel="stylesheet" href="assets/css/shared.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/CamLingua/assets/css/shared.css') ?>">
    <?php foreach ($extraCss as $css): ?>
        <link rel="stylesheet" href="assets/css/<?= htmlspecialchars($css) ?>?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/CamLingua/assets/css/' . $css) ?>">
    <?php endforeach; ?>
</head>
<body>
    <header class="site-header">
        <div class="container nav-inner">
            <a href="index.php" class="logo">
                <?php if (!empty($_logoUrl)): ?>
                    <img src="<?= htmlspecialchars($_logoUrl) ?>" alt="<?= $_siteName ?>" class="logo-img">
                <?php else: ?>
                    <img src="assets/logo.png" alt="<?= $_siteName ?>" class="logo-img">
                <?php endif; ?>
                <span class="logo-text"><?= $_siteName ?></span>
            </a>
            <nav class="nav-links" id="mainNav">
                <a href="index.php" class="<?= $activePage === 'index' ? 'active' : '' ?>">Home</a>
                <a href="languages.php" class="<?= $activePage === 'languages' ? 'active' : '' ?>">Languages</a>
                <a href="subscription.php" class="<?= $activePage === 'pricing' ? 'active' : '' ?>">Pricing</a>
                <a href="about.php" class="<?= $activePage === 'about' ? 'active' : '' ?>">About</a>
                <!-- auth-only links — hidden until JS confirms login -->
                <a href="translator.php" class="nav-auth-only <?= $activePage === 'translator' ? 'active' : '' ?>" style="display:none;">Translator</a>
                <a href="translation-history.php" class="nav-auth-only <?= $activePage === 'history' ? 'active' : '' ?>" style="display:none;">History</a>
                <!-- admin link injected by JS only for role=admin -->
            </nav>
            <div class="nav-actions">
                <a href="login.php" class="btn btn-ghost">Log In</a>
                <a href="login.php#signup" class="btn btn-primary">Sign Up</a>
                <button class="nav-hamburger" id="navHamburger" aria-label="Toggle navigation" aria-expanded="false" aria-controls="mobileNavMenu">
                    <svg id="navIconMenu" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                        <line x1="3" y1="6"  x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg id="navIconClose" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" style="display:none;">
                        <line x1="18" y1="6"  x2="6"  y2="18"/>
                        <line x1="6"  y1="6"  x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <div class="mobile-nav-menu" id="mobileNavMenu" role="navigation" aria-label="Mobile navigation">
        <a href="index.php" class="<?= $activePage === 'index' ? 'active' : '' ?>">Home</a>
        <a href="languages.php" class="<?= $activePage === 'languages' ? 'active' : '' ?>">Languages</a>
        <a href="subscription.php" class="<?= $activePage === 'pricing' ? 'active' : '' ?>">Pricing</a>
        <a href="about.php" class="<?= $activePage === 'about' ? 'active' : '' ?>">About</a>
        <!-- auth-only links -->
        <a href="translator.php" class="nav-auth-only <?= $activePage === 'translator' ? 'active' : '' ?>" style="display:none;">Translator</a>
        <a href="translation-history.php" class="nav-auth-only <?= $activePage === 'history' ? 'active' : '' ?>" style="display:none;">History</a>
        <!-- admin link injected by JS only for role=admin -->
        <div class="mob-nav-auth">
            <a href="login.php" class="btn btn-ghost">Log In</a>
            <a href="login.php#signup" class="btn btn-primary">Sign Up</a>
        </div>
    </div>
