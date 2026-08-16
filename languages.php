<?php
$pageTitle  = 'CamLingua – Supported Languages';
$extraCss   = ['pages.css'];
$extraJs    = ['languages.js'];
$activePage = 'languages';
include 'includes/header.php';
?>

<div class="lang-page-header">
  <p style="font-size:.75rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:#86efac;margin-bottom:.5rem;">Explore</p>
  <h1>Supported Languages</h1>
  <p>Explore and translate between Cameroonian languages and more. CamLingua bridges communities through language.</p>
  <div class="lang-stats">
    <div class="lang-stat"><p>202</p><p>Languages Supported</p></div>
    <div class="lang-stat"><p>3</p><p>Cameroonian Languages</p></div>
    <div class="lang-stat"><p>250+</p><p>Languages in Cameroon</p></div>
  </div>
</div>

<div class="lang-main">
  <div class="filter-bar">
    <div class="search-wrap">
      <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
      <input id="search-input" type="text" placeholder="Search language…">
      <button id="clear-search" class="clear-btn" style="display:none;" aria-label="Clear">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="cat-pills" id="cat-pills">
      <button class="cat-pill active" data-cat="all">All</button>
      <button class="cat-pill" data-cat="cameroonian">🇨🇲 Cameroonian</button>
      <button class="cat-pill" data-cat="african">🌍 African</button>
      <button class="cat-pill" data-cat="european">🇪🇺 European</button>
      <button class="cat-pill" data-cat="asian">🌏 Asian</button>
      <button class="cat-pill" data-cat="middleeastern">🕌 Middle East</button>
      <button class="cat-pill" data-cat="americas">🌎 Americas</button>
      <button class="cat-pill" data-cat="pacific">🌊 Pacific</button>
      <button class="cat-pill" data-cat="international">🌐 International</button>
    </div>
  </div>

  <p id="result-count" class="result-count">Showing 202 languages</p>
  <div id="lang-grid" class="lang-grid"></div>

  <div id="empty-state" class="empty-state" style="display:none;">
    <div class="empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg></div>
    <p class="title">No languages found</p>
    <p class="sub">Try a different search term or category.</p>
    <button id="reset-btn" class="btn-green-outline">Clear search</button>
  </div>

  <div class="suggest-cta">
    <div class="suggest-cta-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
    <div class="suggest-cta-text">
      <p class="title">Don't see your language?</p>
      <p class="sub">Cameroon has over 250 languages. Help us expand by suggesting a language.</p>
    </div>
    <a href="translator.php" class="suggest-cta-btn">Suggest a Language</a>
  </div>
</div>

<!-- Language Detail Modal -->
<div id="lang-modal" class="modal-overlay" style="display:none;">
  <div class="modal-box lang-modal">
    <button id="close-lang-modal" class="modal-close" aria-label="Close">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div id="modal-content"></div>
    <a id="modal-translate-btn" href="translator.php" class="modal-translate-btn">Translate in this Language</a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
