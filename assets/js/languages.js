/**
 * languages.js — CamLingua Supported Languages page
 * Depends on api.js (loaded by footer.php).
 */
(function () {
    'use strict';

    var LANGUAGES_DATA = [
        { id: 1, code: 'ewo', name_en: 'Ewondo', name_native: 'Ewondo', category: 'cameroonian', flag: '🇨🇲', speakers: '~780K', region: 'Centre, Cameroon', description: 'A Bantu language spoken mainly in the Centre region of Cameroon, especially around Yaoundé. It belongs to the Beti language family.', sample: { text: 'Mbolo', meaning: 'Hello' }, status: 'supported' },
        { id: 2, code: 'bas', name_en: 'Bassa', name_native: "Bassa'a", category: 'cameroonian', flag: '🇨🇲', speakers: '~800K', region: 'Littoral, Cameroon', description: 'A Bantu language of the Littoral Region. One of the most widely spoken indigenous languages in Cameroon.', sample: { text: 'Ndínawo', meaning: 'Hello' }, status: 'supported' },
        { id: 3, code: 'dua', name_en: 'Duala', name_native: 'Duala', category: 'cameroonian', flag: '🇨🇲', speakers: '~87K', region: 'Littoral, Cameroon', description: 'A Bantu language historically used as a trade language along the Cameroonian coast, particularly in Douala.', sample: { text: 'Mbote', meaning: 'Hello' }, status: 'supported' },
        { id: 4, code: 'bam', name_en: 'Bamileke', name_native: "Ghomálá'", category: 'cameroonian', flag: '🇨🇲', speakers: '~1.2M', region: 'West, Cameroon', description: 'A cluster of closely related Grassfields Bantu languages spoken in the Western Highlands of Cameroon.', sample: { text: 'Welé', meaning: 'Hello' }, status: 'supported' },
        { id: 5, code: 'fuf', name_en: 'Fulfulde', name_native: 'Fulfulde', category: 'cameroonian', flag: '🇨🇲', speakers: '~4M+', region: 'Adamawa, North, Far North', description: 'A major language of the Sahel belt. Widely spoken across northern Cameroon and used as a regional lingua franca.', sample: { text: 'Jam waali', meaning: 'Hello / Good morning' }, status: 'supported' },
        { id: 6, code: 'en', name_en: 'English', name_native: 'English', category: 'international', flag: '🇬🇧', speakers: '~1.5B', region: 'Worldwide', description: 'One of the two official languages of Cameroon, used in government, education, and business in the anglophone regions.', sample: { text: 'Hello', meaning: 'Hello' }, status: 'supported' },
        { id: 7, code: 'fr', name_en: 'French', name_native: 'Français', category: 'international', flag: '🇫🇷', speakers: '~321M', region: 'Worldwide', description: 'The dominant official language of Cameroon, used widely in government, media, and education across the francophone regions.', sample: { text: 'Bonjour', meaning: 'Hello / Good morning' }, status: 'supported' },
        { id: 8, code: 'ybb', name_en: 'Yemba', name_native: 'Yemba', category: 'cameroonian', flag: '🇨🇲', speakers: '~300K', region: 'West, Cameroon', description: 'A Grassfields language spoken in Dschang and surrounding areas of the West Region.', sample: { text: 'Yì', meaning: 'Yes' }, status: 'coming_soon' },
        { id: 9, code: 'pidgin', name_en: 'Cameroonian Pidgin', name_native: 'Pidgin', category: 'cameroonian', flag: '🇨🇲', speakers: '~8M+', region: 'Nationwide', description: 'An English-based creole and the most widely spoken contact language in Cameroon, serving as a national lingua franca.', sample: { text: 'How you dey?', meaning: 'How are you?' }, status: 'coming_soon' },
        { id: 10, code: 'spa', name_en: 'Spanish', name_native: 'Español', category: 'international', flag: '🇪🇸', speakers: '~500M', region: 'Worldwide', description: "A widely spoken Romance language. Planned for future support to serve Cameroon's equatorial neighbours.", sample: { text: 'Hola', meaning: 'Hello' }, status: 'coming_soon' },
    ];

    var activeCategory = 'all';
    var searchQuery    = '';

    var grid        = document.getElementById('lang-grid');
    var emptyState  = document.getElementById('empty-state');
    var resultCount = document.getElementById('result-count');
    var searchInput = document.getElementById('search-input');
    var clearBtn    = document.getElementById('clear-search');
    var catSelect   = document.getElementById('cat-select');
    var modal       = document.getElementById('lang-modal');
    var modalContent= document.getElementById('modal-content');

    function escapeHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function highlight(str, query) {
        if (!query) return escapeHtml(str);
        var esc = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return escapeHtml(str).replace(new RegExp('(' + esc + ')', 'gi'), '<mark>$1</mark>');
    }

    function buildCard(lang) {
        var isComing = lang.status === 'coming_soon';
        var card = document.createElement('div');
        card.className  = 'lang-card' + (isComing ? ' dimmed' : ' clickable');
        card.dataset.id = lang.id;
        var badge  = isComing ? '<span class="card-badge badge-soon">Soon</span>' : '<span class="card-badge badge-live">Live</span>';
        var catTag = lang.category === 'cameroonian' ? '<span class="tag-cameroon">Cameroon</span>' : '<span class="tag-international">International</span>';
        card.innerHTML = badge +
            '<div class="card-flag">' + lang.flag + '</div>' +
            '<div><p class="card-name-en">' + highlight(lang.name_en, searchQuery) + '</p><p class="card-name-native">' + escapeHtml(lang.name_native) + '</p></div>' +
            '<p class="card-region">' + escapeHtml(lang.region) + '</p>' + catTag +
            '<p class="card-speakers">' + lang.speakers + ' speakers</p>';
        if (!isComing) card.addEventListener('click', function () { openModal(lang); });
        return card;
    }

    function render() {
        var q = searchQuery.toLowerCase();
        var filtered = LANGUAGES_DATA.filter(function (lang) {
            var matchesCat = activeCategory === 'all' || lang.category === activeCategory;
            var matchesQ   = !q || lang.name_en.toLowerCase().indexOf(q) !== -1 || lang.name_native.toLowerCase().indexOf(q) !== -1 || lang.region.toLowerCase().indexOf(q) !== -1;
            return matchesCat && matchesQ;
        });
        grid.innerHTML = '';
        if (filtered.length === 0) {
            grid.style.display = 'none'; emptyState.style.display = 'flex'; resultCount.textContent = 'No languages found'; return;
        }
        grid.style.display = 'grid'; emptyState.style.display = 'none';
        resultCount.textContent = 'Showing ' + filtered.length + ' language' + (filtered.length !== 1 ? 's' : '');
        filtered.forEach(function (lang) { grid.appendChild(buildCard(lang)); });
    }

    function speak(text, langCode) {
        if (!window.speechSynthesis) return;
        var utt = new SpeechSynthesisUtterance(text);
        utt.lang = langCode === 'fr' ? 'fr-FR' : 'en-US';
        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(utt);
    }

    function openModal(lang) {
        modalContent.innerHTML =
            '<div class="modal-lang-header"><div class="modal-flag">' + lang.flag + '</div>' +
            '<div><h2>' + escapeHtml(lang.name_en) + '</h2><p>' + escapeHtml(lang.name_native) + ' · ' + escapeHtml(lang.region) + '</p></div></div>' +
            '<p class="modal-desc">' + escapeHtml(lang.description) + '</p>' +
            '<div class="modal-stats"><div class="modal-stat"><p class="label">Speakers</p><p class="value">' + lang.speakers + '</p></div>' +
            '<div class="modal-stat"><p class="label">Category</p><p class="value" style="text-transform:capitalize;">' + lang.category + '</p></div></div>' +
            '<div class="modal-sample"><div><p class="modal-sample-label">Sample phrase</p><p class="modal-sample-text">"' + escapeHtml(lang.sample.text) + '"</p><p class="modal-sample-meaning">' + escapeHtml(lang.sample.meaning) + '</p></div>' +
            '<button class="modal-speak-btn" data-text="' + escapeHtml(lang.sample.text) + '" data-lang="' + lang.code + '" title="Listen"><svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5L6 9H2v6h4l5 4V5zM15.54 8.46a5 5 0 010 7.07"/></svg></button></div>';
        modalContent.querySelectorAll('.modal-speak-btn').forEach(function (btn) {
            btn.addEventListener('click', function () { speak(btn.dataset.text, btn.dataset.lang); });
        });
        var translateBtn = document.getElementById('modal-translate-btn');
        if (translateBtn) translateBtn.href = 'translator.php';
        modal.style.display = 'flex';
    }

    document.getElementById('close-lang-modal').addEventListener('click', function () { modal.style.display = 'none'; });
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.style.display = 'none'; });

    searchInput.addEventListener('input', function () { searchQuery = searchInput.value.trim(); clearBtn.style.display = searchQuery ? 'block' : 'none'; render(); });
    clearBtn.addEventListener('click', function () { searchInput.value = ''; searchQuery = ''; clearBtn.style.display = 'none'; render(); searchInput.focus(); });

    document.getElementById('reset-btn').addEventListener('click', function () {
        searchInput.value = ''; searchQuery = ''; activeCategory = 'all'; clearBtn.style.display = 'none';
        document.querySelectorAll('.cat-pill').forEach(function (p) { p.classList.toggle('active', p.dataset.cat === 'all'); });
        catSelect.value = 'all'; render();
    });

    document.querySelectorAll('.cat-pill').forEach(function (pill) {
        pill.addEventListener('click', function () {
            activeCategory = pill.dataset.cat;
            document.querySelectorAll('.cat-pill').forEach(function (p) { p.classList.remove('active'); });
            pill.classList.add('active'); catSelect.value = activeCategory; render();
        });
    });

    catSelect.addEventListener('change', function () {
        activeCategory = catSelect.value;
        document.querySelectorAll('.cat-pill').forEach(function (p) { p.classList.toggle('active', p.dataset.cat === activeCategory); });
        render();
    });

    render();
})();
