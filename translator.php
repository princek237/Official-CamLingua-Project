<?php
$pageTitle  = 'CamLingua – Translator';
$extraCss   = ['pages.css'];
$extraJs    = ['translator.js'];
$activePage = 'translator';
include 'includes/header.php';
?>

<div class="translator-wrap">
  <div class="translator-header">
    <h1>Translator</h1>
    <p>Translate instantly between English, French and Cameroonian languages.</p>
  </div>

  <div class="translator-card">
    <!-- Language selector -->
    <div class="lang-bar">
      <div class="lang-sel-wrap">
        <span class="lang-flag" id="source-flag">🇬🇧</span>
        <select id="source-lang">
          <option value="en" data-flag="🇬🇧">English</option>
          <option value="fr" data-flag="🇫🇷">French</option>
          <option value="ewo">Ewondo (Cameroon)</option>
          <option value="bas">Bassa (Cameroon)</option>
          <option value="dua">Duala (Cameroon)</option>
          <option value="bam">Bamileke (Cameroon)</option>
          <option value="fuf">Fulfulde (Cameroon)</option>
        </select>
        <svg class="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </div>
      <button id="swap-btn" class="swap-btn" title="Swap languages">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
      </button>
      <div class="lang-sel-wrap" style="justify-content:flex-end;">
        <span class="lang-flag" id="target-flag">🇫🇷</span>
        <select id="target-lang">
          <option value="fr" data-flag="🇫🇷">French (Cameroon)</option>
          <option value="en" data-flag="🇬🇧">English</option>
          <option value="ewo">Ewondo (Cameroon)</option>
          <option value="bas">Bassa (Cameroon)</option>
          <option value="dua">Duala (Cameroon)</option>
          <option value="bam">Bamileke (Cameroon)</option>
          <option value="fuf">Fulfulde (Cameroon)</option>
        </select>
        <svg class="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </div>
    </div>

    <!-- Dual panel -->
    <div class="dual-panel">
      <div class="source-panel">
        <textarea id="source-text" placeholder="Type or paste your text here…" maxlength="5000"></textarea>
        <div class="panel-bar">
          <div class="panel-bar-left">
            <button id="mic-btn" class="icon-btn" title="Speak">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8"/></svg>
            </button>
            <button id="source-tts-btn" class="icon-btn" title="Listen">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5L6 9H2v6h4l5 4V5zM15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/></svg>
            </button>
          </div>
          <span id="char-count" class="char-count">0 / 5000</span>
        </div>
      </div>

      <div class="target-panel">
        <div id="translation-skeleton" class="skeleton-loader" style="display:none;">
          <div class="skeleton-line w-75"></div>
          <div class="skeleton-line w-50"></div>
          <div class="skeleton-line w-66"></div>
        </div>
        <div id="translated-text" class="translated-text placeholder">La traduction apparaîtra ici…</div>
        <div class="panel-bar">
          <button id="target-tts-btn" class="icon-btn" title="Listen to translation">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5L6 9H2v6h4l5 4V5zM15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/></svg>
          </button>
          <div class="panel-bar-right">
            <button id="copy-btn" class="icon-btn" title="Copy">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3"/></svg>
            </button>
            <button id="download-btn" class="icon-btn" title="Download">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M16 12l-4 4m0 0l-4-4m4 4V4"/></svg>
            </button>
            <button id="share-btn" class="icon-btn" title="Share">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Translate bar -->
    <div class="translate-bar">
      <p id="engine-badge">Powered by NLLB-200 AI · Translations may not be perfect.</p>
      <button id="translate-btn" class="translate-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
        Translate
      </button>
    </div>
  </div>

  <!-- Sample phrases -->
  <section style="margin-top:2rem;">
    <h2 class="section-title">Try a sample</h2>
    <div class="samples-row" id="sample-phrases"></div>
  </section>

  <!-- Popular translations -->
  <section style="margin-top:2rem;margin-bottom:1rem;">
    <h2 class="section-title">Popular Translations</h2>
    <div class="popular-grid" id="popular-grid"></div>
  </section>

  <!-- Community banner -->
  <div class="community-banner">
    <div class="community-icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
    </div>
    <div class="community-text">
      <p class="title">Know a better translation?</p>
      <p class="sub">Help improve CamLingua by suggesting a more accurate translation.</p>
    </div>
    <button id="suggest-btn" class="btn-green-sm">Suggest Translation</button>
  </div>
</div>

<!-- Suggest Modal -->
<div id="suggest-modal" class="modal-overlay" style="display:none;">
  <div class="modal-box suggest-modal">
    <button id="close-modal" class="modal-close" aria-label="Close">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <h3>Suggest a Better Translation</h3>
    <p class="sub">Your suggestion will be reviewed by our community moderators.</p>
    <div class="form-group"><label>Original text</label><input id="modal-source" type="text" class="input-field" readonly></div>
    <div class="form-group"><label>Current translation</label><input id="modal-current" type="text" class="input-field" readonly></div>
    <div class="form-group"><label>Your suggestion <span class="required">*</span></label><textarea id="modal-suggestion" rows="3" class="input-field" placeholder="Enter your better translation…"></textarea></div>
    <button id="submit-suggestion" class="btn-green-full">Submit Suggestion</button>
  </div>
</div>

<div id="toast" class="toast" style="display:none;"></div>

<?php include 'includes/footer.php'; ?>

<script src="assets\js\translatetext.js"></script>