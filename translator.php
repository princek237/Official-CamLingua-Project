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
          <optgroup label="-- Cameroonian / Central African --">
            <option value="fuv_Latn" data-flag="🇨🇲">Fulfulde (Cameroon)</option>
            <option value="bam_Latn" data-flag="🇨🇲">Bambara / Bamileke</option>
            <option value="sag_Latn" data-flag="🇨🇫">Sango (Central Africa)</option>
            <option value="lin_Latn" data-flag="🇨🇩">Lingala</option>
            <option value="kbp_Latn" data-flag="🇹🇬">Kabiye</option>
          </optgroup>
          <optgroup label="-- West African --">
            <option value="hau_Latn" data-flag="🌍">Hausa</option>
            <option value="yor_Latn" data-flag="🇳🇬">Yoruba</option>
            <option value="ibo_Latn" data-flag="🇳🇬">Igbo</option>
            <option value="wol_Latn" data-flag="🇸🇳">Wolof</option>
            <option value="twi_Latn" data-flag="🇬🇭">Twi / Akan</option>
            <option value="aka_Latn" data-flag="🇬🇭">Akan</option>
            <option value="fon_Latn" data-flag="🇧🇯">Fon</option>
            <option value="mos_Latn" data-flag="🇧🇫">Mossi</option>
            <option value="dyu_Latn" data-flag="🇧🇫">Dyula</option>
            <option value="ewe_Latn" data-flag="🇬🇭">Ewe</option>
          </optgroup>
          <optgroup label="-- East African --">
            <option value="swh_Latn" data-flag="🇰🇪">Swahili</option>
            <option value="amh_Ethi" data-flag="🇪🇹">Amharic</option>
            <option value="som_Latn" data-flag="🇸🇴">Somali</option>
            <option value="lug_Latn" data-flag="🇺🇬">Luganda</option>
            <option value="run_Latn" data-flag="🇧🇮">Rundi / Kirundi</option>
            <option value="kin_Latn" data-flag="🇷🇼">Kinyarwanda</option>
            <option value="gaz_Latn" data-flag="🇪🇹">Oromo</option>
            <option value="tir_Ethi" data-flag="🇪🇷">Tigrinya</option>
          </optgroup>
          <optgroup label="-- Southern African --">
            <option value="zul_Latn" data-flag="🇿🇦">Zulu</option>
            <option value="xho_Latn" data-flag="🇿🇦">Xhosa</option>
            <option value="sna_Latn" data-flag="🇿🇼">Shona</option>
            <option value="nso_Latn" data-flag="🇿🇦">Northern Sotho</option>
            <option value="sot_Latn" data-flag="🇱🇸">Sotho</option>
            <option value="tsn_Latn" data-flag="🇧🇼">Tswana</option>
            <option value="tso_Latn" data-flag="🇲🇿">Tsonga</option>
            <option value="ssw_Latn" data-flag="🇸🇿">Swati</option>
            <option value="afr_Latn" data-flag="🇿🇦">Afrikaans</option>
          </optgroup>
          <optgroup label="-- Major World Languages --">
            <option value="eng_Latn" data-flag="🇬🇧" selected>English</option>
            <option value="fra_Latn" data-flag="🇫🇷">French</option>
            <option value="arb_Arab" data-flag="🇸🇦">Arabic</option>
            <option value="spa_Latn" data-flag="🇪🇸">Spanish</option>
            <option value="por_Latn" data-flag="🇵🇹">Portuguese</option>
            <option value="deu_Latn" data-flag="🇩🇪">German</option>
            <option value="ita_Latn" data-flag="🇮🇹">Italian</option>
            <option value="nld_Latn" data-flag="🇳🇱">Dutch</option>
            <option value="rus_Cyrl" data-flag="🇷🇺">Russian</option>
            <option value="zho_Hans" data-flag="🇨🇳">Chinese (Simplified)</option>
            <option value="zho_Hant" data-flag="🇹🇼">Chinese (Traditional)</option>
            <option value="jpn_Jpan" data-flag="🇯🇵">Japanese</option>
            <option value="kor_Hang" data-flag="🇰🇷">Korean</option>
            <option value="hin_Deva" data-flag="🇮🇳">Hindi</option>
            <option value="ben_Beng" data-flag="🇧🇩">Bengali</option>
            <option value="tur_Latn" data-flag="🇹🇷">Turkish</option>
            <option value="ind_Latn" data-flag="🇮🇩">Indonesian</option>
            <option value="vie_Latn" data-flag="🇻🇳">Vietnamese</option>
            <option value="pol_Latn" data-flag="🇵🇱">Polish</option>
            <option value="ukr_Cyrl" data-flag="🇺🇦">Ukrainian</option>
          </optgroup>
        </select>
        <svg class="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </div>
      <button id="swap-btn" class="swap-btn" title="Swap languages">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
      </button>
      <div class="lang-sel-wrap" style="justify-content:flex-end;">
        <span class="lang-flag" id="target-flag">🇫🇷</span>
        <select id="target-lang">
          <optgroup label="-- Cameroonian / Central African --">
            <option value="fuv_Latn" data-flag="🇨🇲">Fulfulde (Cameroon)</option>
            <option value="bam_Latn" data-flag="🇨🇲">Bambara / Bamileke</option>
            <option value="sag_Latn" data-flag="🇨🇫">Sango (Central Africa)</option>
            <option value="lin_Latn" data-flag="🇨🇩">Lingala</option>
            <option value="kbp_Latn" data-flag="🇹🇬">Kabiye</option>
          </optgroup>
          <optgroup label="-- West African --">
            <option value="hau_Latn" data-flag="🌍">Hausa</option>
            <option value="yor_Latn" data-flag="🇳🇬">Yoruba</option>
            <option value="ibo_Latn" data-flag="🇳🇬">Igbo</option>
            <option value="wol_Latn" data-flag="🇸🇳">Wolof</option>
            <option value="twi_Latn" data-flag="🇬🇭">Twi / Akan</option>
            <option value="aka_Latn" data-flag="🇬🇭">Akan</option>
            <option value="fon_Latn" data-flag="🇧🇯">Fon</option>
            <option value="mos_Latn" data-flag="🇧🇫">Mossi</option>
            <option value="dyu_Latn" data-flag="🇧🇫">Dyula</option>
            <option value="ewe_Latn" data-flag="🇬🇭">Ewe</option>
          </optgroup>
          <optgroup label="-- East African --">
            <option value="swh_Latn" data-flag="🇰🇪">Swahili</option>
            <option value="amh_Ethi" data-flag="🇪🇹">Amharic</option>
            <option value="som_Latn" data-flag="🇸🇴">Somali</option>
            <option value="lug_Latn" data-flag="🇺🇬">Luganda</option>
            <option value="run_Latn" data-flag="🇧🇮">Rundi / Kirundi</option>
            <option value="kin_Latn" data-flag="🇷🇼">Kinyarwanda</option>
            <option value="gaz_Latn" data-flag="🇪🇹">Oromo</option>
            <option value="tir_Ethi" data-flag="🇪🇷">Tigrinya</option>
          </optgroup>
          <optgroup label="-- Southern African --">
            <option value="zul_Latn" data-flag="🇿🇦">Zulu</option>
            <option value="xho_Latn" data-flag="🇿🇦">Xhosa</option>
            <option value="sna_Latn" data-flag="🇿🇼">Shona</option>
            <option value="nso_Latn" data-flag="🇿🇦">Northern Sotho</option>
            <option value="sot_Latn" data-flag="🇱🇸">Sotho</option>
            <option value="tsn_Latn" data-flag="🇧🇼">Tswana</option>
            <option value="tso_Latn" data-flag="🇲🇿">Tsonga</option>
            <option value="ssw_Latn" data-flag="🇸🇿">Swati</option>
            <option value="afr_Latn" data-flag="🇿🇦">Afrikaans</option>
          </optgroup>
          <optgroup label="-- Major World Languages --">
            <option value="fra_Latn" data-flag="🇫🇷" selected>French</option>
            <option value="eng_Latn" data-flag="🇬🇧">English</option>
            <option value="arb_Arab" data-flag="🇸🇦">Arabic</option>
            <option value="spa_Latn" data-flag="🇪🇸">Spanish</option>
            <option value="por_Latn" data-flag="🇵🇹">Portuguese</option>
            <option value="deu_Latn" data-flag="🇩🇪">German</option>
            <option value="ita_Latn" data-flag="🇮🇹">Italian</option>
            <option value="nld_Latn" data-flag="🇳🇱">Dutch</option>
            <option value="rus_Cyrl" data-flag="🇷🇺">Russian</option>
            <option value="zho_Hans" data-flag="🇨🇳">Chinese (Simplified)</option>
            <option value="zho_Hant" data-flag="🇹🇼">Chinese (Traditional)</option>
            <option value="jpn_Jpan" data-flag="🇯🇵">Japanese</option>
            <option value="kor_Hang" data-flag="🇰🇷">Korean</option>
            <option value="hin_Deva" data-flag="🇮🇳">Hindi</option>
            <option value="ben_Beng" data-flag="🇧🇩">Bengali</option>
            <option value="tur_Latn" data-flag="🇹🇷">Turkish</option>
            <option value="ind_Latn" data-flag="🇮🇩">Indonesian</option>
            <option value="vie_Latn" data-flag="🇻🇳">Vietnamese</option>
            <option value="pol_Latn" data-flag="🇵🇱">Polish</option>
            <option value="ukr_Cyrl" data-flag="🇺🇦">Ukrainian</option>
          </optgroup>
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
      <button id="translate-btn" class="translate-btn" onclick="translateText()">
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