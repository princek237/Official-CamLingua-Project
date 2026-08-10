<?php
$pageTitle  = 'CamLingua – Translate. Connect. Preserve Cameroon\'s Languages.';
$extraCss   = ['main.css'];
$activePage = 'index';
include 'includes/header.php';
?>

<!-- ── HERO ──────────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-wrap">
    <div class="hero-left">
      <span class="hero-badge">
        <span class="hero-badge-dot"></span>
        Cameroonian Language Translation System
      </span>
      <h1 class="hero-title">
        Translate. Connect.<br>
        Preserve Cameroon's<br>
        <span class="highlight">Languages.</span>
      </h1>
      <p class="hero-desc">
        CamLingua helps you translate between Cameroonian languages and the world.
        Fast, accurate and easy to use.
      </p>
      <div class="hero-btns">
        <a href="translator.php" class="btn-cta-green">Start Translating</a>
        <a href="languages.php" class="btn-cta-outline">Explore Languages</a>
      </div>
    </div>

    <div class="hero-right">
      <!-- Cameroon map card with speech bubbles -->
      <div class="map-card">
        <div class="bubble bubble-1">Hello!</div>
        <div class="bubble bubble-2">Ndinawô!</div>
        <div class="bubble bubble-3">Mbolo!</div>
        <svg class="cameroon-svg" viewBox="0 0 200 280" xmlns="http://www.w3.org/2000/svg">
          <path d="M95,12 Q120,8 145,25 Q170,45 160,80 Q175,110 155,145 Q165,180 140,215
                   Q115,248 90,258 Q65,265 45,240 Q25,215 35,180 Q20,148 40,118
                   Q30,85 50,58 Q70,30 95,12 Z"
                fill="#15803d" opacity="0.15" stroke="#15803d" stroke-width="2"/>
          <circle cx="75"  cy="160" r="7" fill="#15803d"/>
          <circle cx="110" cy="130" r="7" fill="#15803d"/>
          <circle cx="130" cy="80"  r="7" fill="#f59e0b"/>
          <circle cx="80"  cy="200" r="7" fill="#166534"/>
        </svg>
      </div>
    </div>
  </div>
</section>

<!-- ── FEATURE CARDS ─────────────────────────────────────────── -->
<section class="features-section">
  <div class="features-wrap">

    <div class="feat-card">
      <div class="feat-icon feat-icon-blue">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <h3>AI-Powered</h3>
      <p>Accurate translations powered by advanced AI models.</p>
    </div>

    <div class="feat-card">
      <div class="feat-icon feat-icon-orange">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
      </div>
      <h3>Local Languages</h3>
      <p>Support for major Cameroonian languages and dialects.</p>
    </div>

    <div class="feat-card">
      <div class="feat-icon feat-icon-green">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      </div>
      <h3>Secure &amp; Private</h3>
      <p>Your data is encrypted and your privacy is protected.</p>
    </div>

    <div class="feat-card">
      <div class="feat-icon feat-icon-purple">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <h3>History &amp; Sync</h3>
      <p>Access your past translations anytime, anywhere.</p>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>
