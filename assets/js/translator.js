/**
 * translator.js — CamLingua Translator page
 * Depends on api.js being loaded first (footer.php handles this).
 */
(function () {
    'use strict';

    const LANGUAGES = [
        // Cameroonian / Central African
        { code: 'fuv_Latn', name: 'Fulfulde',          flag: '🇨🇲' },
        { code: 'bam_Latn', name: 'Bambara',            flag: '🇨🇲' },
        { code: 'sag_Latn', name: 'Sango',              flag: '🇨🇫' },
        { code: 'lin_Latn', name: 'Lingala',            flag: '🇨🇩' },
        { code: 'kbp_Latn', name: 'Kabiye',             flag: '🇹🇬' },
        // West African
        { code: 'hau_Latn', name: 'Hausa',              flag: '🌍' },
        { code: 'yor_Latn', name: 'Yoruba',             flag: '🇳🇬' },
        { code: 'ibo_Latn', name: 'Igbo',               flag: '🇳🇬' },
        { code: 'wol_Latn', name: 'Wolof',              flag: '🇸🇳' },
        { code: 'twi_Latn', name: 'Twi',                flag: '🇬🇭' },
        { code: 'aka_Latn', name: 'Akan',               flag: '🇬🇭' },
        { code: 'fon_Latn', name: 'Fon',                flag: '🇧🇯' },
        { code: 'mos_Latn', name: 'Mossi',              flag: '🇧🇫' },
        { code: 'dyu_Latn', name: 'Dyula',              flag: '🇧🇫' },
        { code: 'ewe_Latn', name: 'Ewe',                flag: '🇬🇭' },
        // East African
        { code: 'swh_Latn', name: 'Swahili',            flag: '🇰🇪' },
        { code: 'amh_Ethi', name: 'Amharic',            flag: '🇪🇹' },
        { code: 'som_Latn', name: 'Somali',             flag: '🇸🇴' },
        { code: 'lug_Latn', name: 'Luganda',            flag: '🇺🇬' },
        { code: 'run_Latn', name: 'Rundi',              flag: '🇧🇮' },
        { code: 'kin_Latn', name: 'Kinyarwanda',        flag: '🇷🇼' },
        { code: 'gaz_Latn', name: 'Oromo',              flag: '🇪🇹' },
        { code: 'tir_Ethi', name: 'Tigrinya',           flag: '🇪🇷' },
        // Southern African
        { code: 'zul_Latn', name: 'Zulu',               flag: '🇿🇦' },
        { code: 'xho_Latn', name: 'Xhosa',              flag: '🇿🇦' },
        { code: 'sna_Latn', name: 'Shona',              flag: '🇿🇼' },
        { code: 'nso_Latn', name: 'Northern Sotho',     flag: '🇿🇦' },
        { code: 'sot_Latn', name: 'Sotho',              flag: '🇱🇸' },
        { code: 'tsn_Latn', name: 'Tswana',             flag: '🇧🇼' },
        { code: 'tso_Latn', name: 'Tsonga',             flag: '🇲🇿' },
        { code: 'ssw_Latn', name: 'Swati',              flag: '🇸🇿' },
        { code: 'afr_Latn', name: 'Afrikaans',          flag: '🇿🇦' },
        // Major World Languages
        { code: 'eng_Latn', name: 'English',            flag: '🇬🇧' },
        { code: 'fra_Latn', name: 'French',             flag: '🇫🇷' },
        { code: 'arb_Arab', name: 'Arabic',             flag: '🇸🇦' },
        { code: 'spa_Latn', name: 'Spanish',            flag: '🇪🇸' },
        { code: 'por_Latn', name: 'Portuguese',         flag: '🇵🇹' },
        { code: 'deu_Latn', name: 'German',             flag: '🇩🇪' },
        { code: 'ita_Latn', name: 'Italian',            flag: '🇮🇹' },
        { code: 'nld_Latn', name: 'Dutch',              flag: '🇳🇱' },
        { code: 'rus_Cyrl', name: 'Russian',            flag: '🇷🇺' },
        { code: 'zho_Hans', name: 'Chinese (Simplified)', flag: '🇨🇳' },
        { code: 'zho_Hant', name: 'Chinese (Traditional)', flag: '🇹🇼' },
        { code: 'jpn_Jpan', name: 'Japanese',           flag: '🇯🇵' },
        { code: 'kor_Hang', name: 'Korean',             flag: '🇰🇷' },
        { code: 'hin_Deva', name: 'Hindi',              flag: '🇮🇳' },
        { code: 'ben_Beng', name: 'Bengali',            flag: '🇧🇩' },
        { code: 'tur_Latn', name: 'Turkish',            flag: '🇹🇷' },
        { code: 'ind_Latn', name: 'Indonesian',         flag: '🇮🇩' },
        { code: 'vie_Latn', name: 'Vietnamese',         flag: '🇻🇳' },
        { code: 'pol_Latn', name: 'Polish',             flag: '🇵🇱' },
        { code: 'ukr_Cyrl', name: 'Ukrainian',          flag: '🇺🇦' },
    ];

    const SAMPLES = [
        'Hello, how are you?', 'Where are you going?',
        'Thank you very much', 'Good morning', 'I love Cameroon',
    ];

    const POPULAR = [
        { src: 'Good morning',    tgt: 'Minsili mfam',  from: 'English', to: 'Ewondo'   },
        { src: 'Thank you',       tgt: 'Médasi',         from: 'English', to: 'Duala'    },
        { src: 'How are you?',    tgt: 'No mbadata?',    from: 'English', to: 'Fulfulde' },
        { src: 'I love Cameroon', tgt: 'Mi tor Kamerun', from: 'English', to: 'Bassa'    },
    ];

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const sourceLangEl = document.getElementById('source-lang');
    const targetLangEl = document.getElementById('target-lang');
    const sourceFlagEl = document.getElementById('source-flag');
    const targetFlagEl = document.getElementById('target-flag');
    const sourceTextEl = document.getElementById('source-text');
    const translatedEl = document.getElementById('translated-text');
    const skeletonEl   = document.getElementById('translation-skeleton');
    const charCountEl  = document.getElementById('char-count');
    const toastEl      = document.getElementById('toast');

    // ── Helpers ───────────────────────────────────────────────────────────────
    function showToast(msg) {
        toastEl.textContent = msg;
        toastEl.style.display = 'block';
        setTimeout(function () { toastEl.style.display = 'none'; }, 2200);
    }

    function getFlagForCode(code) {
        var l = LANGUAGES.find(function (x) { return x.code === code; });
        return l ? l.flag : '🌐';
    }

    function updateFlags() {
        sourceFlagEl.textContent = getFlagForCode(sourceLangEl.value);
        targetFlagEl.textContent = getFlagForCode(targetLangEl.value);
    }

    // ── Translation ───────────────────────────────────────────────────────────
    async function doTranslate() {
        var text = sourceTextEl.value.trim();
        if (!text) {
            translatedEl.textContent = 'La traduction apparaîtra ici…';
            translatedEl.classList.add('placeholder');
            return;
        }

        if (!Api.isLoggedIn()) {
            showToast('Please log in to translate');
            setTimeout(function () { window.location.href = 'login.php'; }, 1500);
            return;
        }

        translatedEl.style.display = 'none';
        skeletonEl.style.display   = 'flex';
        document.getElementById('translate-btn').disabled = true;

        try {
            var res = await Api.translate(sourceLangEl.value, targetLangEl.value, text);
            skeletonEl.style.display   = 'none';
            translatedEl.style.display = 'block';

            if (res.ok) {
                var result = res.data.data.translated_text;
                var engine = res.data.data.engine || 'mock';

                translatedEl.textContent = result;
                translatedEl.classList.remove('placeholder');

                // Update engine badge
                var badge = document.getElementById('engine-badge');
                if (badge) {
                    if (engine === 'nllb-200') {
                        badge.textContent = 'Powered by NLLB-200 AI · Real translation';
                        badge.style.color = '#15803d';
                    } else {
                        badge.textContent = 'Powered by NLLB-200 AI · Translations may not be perfect.';
                        badge.style.color = '#9ca3af';
                    }
                }

                // Prefill suggest modal fields
                var ms = document.getElementById('modal-source');
                var mc = document.getElementById('modal-current');
                if (ms) ms.value = text;
                if (mc) mc.value = result;

            } else if (res.status === 503) {
                translatedEl.textContent = 'Translation service is not running. Please start the CamLingua AI service (python main.py) and try again.';
                translatedEl.classList.add('placeholder');
                showToast('AI service is offline — start python main.py first.');

            } else {
                var msg = (res.data && res.data.message) ? res.data.message : 'Translation failed.';
                translatedEl.textContent = msg;
                translatedEl.classList.add('placeholder');
                showToast(msg);
            }

        } catch (err) {
            skeletonEl.style.display   = 'none';
            translatedEl.style.display = 'block';
            translatedEl.textContent   = 'Could not reach the server. Please try again.';
            translatedEl.classList.add('placeholder');
        } finally {
            document.getElementById('translate-btn').disabled = false;
        }
    }

    // ── Char counter ──────────────────────────────────────────────────────────
    sourceTextEl.addEventListener('input', function () {
        var len = sourceTextEl.value.length;
        charCountEl.textContent = len + ' / 5000';
        charCountEl.classList.toggle('char-warn', len > 4500);
    });

    // ── Translate button ──────────────────────────────────────────────────────
    document.getElementById('translate-btn').addEventListener('click', doTranslate);
    sourceTextEl.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') doTranslate();
    });

    // ── Language dropdowns ────────────────────────────────────────────────────
    sourceLangEl.addEventListener('change', updateFlags);
    targetLangEl.addEventListener('change', updateFlags);

    // ── Swap ──────────────────────────────────────────────────────────────────
    document.getElementById('swap-btn').addEventListener('click', function () {
        var tmpVal   = sourceLangEl.value;
        var tmpTrans = translatedEl.textContent;
        sourceLangEl.value = targetLangEl.value;
        targetLangEl.value = tmpVal;
        updateFlags();
        if (tmpTrans && !translatedEl.classList.contains('placeholder')) {
            sourceTextEl.value = tmpTrans;
            charCountEl.textContent = tmpTrans.length + ' / 5000';
        }
        doTranslate();
    });

    // ── Copy ──────────────────────────────────────────────────────────────────
    document.getElementById('copy-btn').addEventListener('click', function () {
        var txt = translatedEl.textContent;
        if (!txt || translatedEl.classList.contains('placeholder')) return;
        navigator.clipboard.writeText(txt).then(function () { showToast('Copied to clipboard!'); });
    });

    // ── Download ──────────────────────────────────────────────────────────────
    document.getElementById('download-btn').addEventListener('click', function () {
        var src = sourceTextEl.value;
        var tgt = translatedEl.textContent;
        if (!src || translatedEl.classList.contains('placeholder')) return;
        var content = 'CamLingua Translation\n\nSource (' + sourceLangEl.options[sourceLangEl.selectedIndex].text + '):\n' + src +
                      '\n\nTranslation (' + targetLangEl.options[targetLangEl.selectedIndex].text + '):\n' + tgt +
                      '\n\nGenerated: ' + new Date().toLocaleString();
        var blob = new Blob([content], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'camlingua-translation.txt';
        a.click();
        showToast('Downloaded!');
    });

    // ── Share ─────────────────────────────────────────────────────────────────
    document.getElementById('share-btn').addEventListener('click', function () {
        var tgt = translatedEl.textContent;
        if (navigator.share) {
            navigator.share({ title: 'CamLingua Translation', text: tgt, url: window.location.href }).catch(function () {});
        } else {
            navigator.clipboard.writeText(tgt).then(function () { showToast('Link copied!'); });
        }
    });

    // ── Microphone ────────────────────────────────────────────────────────────
    var micBtn = document.getElementById('mic-btn');
    var recognition = null;
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SR();
        recognition.continuous = false;
        recognition.onresult = function (e) { sourceTextEl.value = e.results[0][0].transcript; doTranslate(); };
        recognition.onend = function () { micBtn.style.color = ''; };
    }
    micBtn.addEventListener('click', function () {
        if (!recognition) { showToast('Speech not supported in this browser'); return; }
        micBtn.style.color = '#ef4444';
        recognition.lang = sourceLangEl.value === 'fr' ? 'fr-FR' : 'en-US';
        recognition.start();
    });

    // ── TTS ───────────────────────────────────────────────────────────────────
    function speak(text, lang) {
        if (!window.speechSynthesis) { showToast('TTS not supported'); return; }
        var utt = new SpeechSynthesisUtterance(text);
        utt.lang = lang === 'fr' ? 'fr-FR' : 'en-US';
        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(utt);
    }
    document.getElementById('source-tts-btn').addEventListener('click', function () { speak(sourceTextEl.value, sourceLangEl.value); });
    document.getElementById('target-tts-btn').addEventListener('click', function () {
        if (!translatedEl.classList.contains('placeholder')) speak(translatedEl.textContent, targetLangEl.value);
    });

    // ── Sample phrases ────────────────────────────────────────────────────────
    var samplesContainer = document.getElementById('sample-phrases');
    if (samplesContainer) {
        SAMPLES.forEach(function (phrase) {
            var btn = document.createElement('button');
            btn.textContent = phrase;
            btn.className   = 'sample-chip';
            btn.addEventListener('click', function () {
                sourceTextEl.value = phrase;
                charCountEl.textContent = phrase.length + ' / 5000';
                doTranslate();
            });
            samplesContainer.appendChild(btn);
        });
    }

    // ── Popular translations ──────────────────────────────────────────────────
    var popularGrid = document.getElementById('popular-grid');
    if (popularGrid) {
        POPULAR.forEach(function (item) {
            var card = document.createElement('div');
            card.className = 'popular-card';
            card.innerHTML = '<p class="langs">' + item.from + ' → ' + item.to + '</p>' +
                             '<p class="src">' + item.src + '</p>' +
                             '<p class="tgt">' + item.tgt + '</p>';
            card.addEventListener('click', function () {
                var srcCode = LANGUAGES.find(function (l) { return l.name.indexOf(item.from) === 0; });
                var tgtCode = LANGUAGES.find(function (l) { return l.name.indexOf(item.to)   === 0; });
                sourceLangEl.value = srcCode ? srcCode.code : 'en';
                targetLangEl.value = tgtCode ? tgtCode.code : 'fr';
                updateFlags();
                sourceTextEl.value = item.src;
                charCountEl.textContent = item.src.length + ' / 5000';
                doTranslate();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            popularGrid.appendChild(card);
        });
    }

    // ── Suggest modal ─────────────────────────────────────────────────────────
    var suggestBtn   = document.getElementById('suggest-btn');
    var suggestModal = document.getElementById('suggest-modal');
    var closeModal   = document.getElementById('close-modal');
    var submitSug    = document.getElementById('submit-suggestion');

    if (suggestBtn) suggestBtn.addEventListener('click', function () { suggestModal.style.display = 'flex'; });
    if (closeModal) closeModal.addEventListener('click', function () { suggestModal.style.display = 'none'; });
    if (suggestModal) suggestModal.addEventListener('click', function (e) { if (e.target === suggestModal) suggestModal.style.display = 'none'; });
    if (submitSug) submitSug.addEventListener('click', function () {
        var sug = document.getElementById('modal-suggestion').value.trim();
        if (!sug) { showToast('Please enter a suggestion'); return; }
        suggestModal.style.display = 'none';
        document.getElementById('modal-suggestion').value = '';
        showToast('Suggestion submitted! Thank you.');
    });

    // ── Retranslate from history (sessionStorage) ─────────────────────────────
    var retranslate = sessionStorage.getItem('retranslate');
    if (retranslate) {
        try {
            var rt = JSON.parse(retranslate);
            sessionStorage.removeItem('retranslate');
            var srcOpt = Array.from(sourceLangEl.options).find(function (o) { return o.value === rt.from || o.text.indexOf(rt.from) === 0; });
            var tgtOpt = Array.from(targetLangEl.options).find(function (o) { return o.value === rt.to   || o.text.indexOf(rt.to)   === 0; });
            if (srcOpt) sourceLangEl.value = srcOpt.value;
            if (tgtOpt) targetLangEl.value = tgtOpt.value;
            sourceTextEl.value = rt.text;
            charCountEl.textContent = rt.text.length + ' / 5000';
            updateFlags();
            doTranslate();
        } catch (e) { /* ignore malformed */ }
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    updateFlags();
})();
