/**
 * history.js — CamLingua Translation History page
 * Depends on api.js (loaded by footer.php).
 */
(function () {
    'use strict';

    // ── Auth guard ────────────────────────────────────────────────────────────
    if (!Api.isLoggedIn()) {
        window.location.href = 'login.php';
        return;
    }

    // ── State ─────────────────────────────────────────────────────────────────
    var historyData    = [];
    var currentPage    = 1;
    var totalPages     = 1;
    var itemsPerPage   = 10;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    var historyContainer  = document.getElementById('historyContainer');
    var emptyState        = document.getElementById('emptyState');
    var loadMoreContainer = document.getElementById('loadMoreContainer');
    var totalCount        = document.getElementById('totalCount');
    var searchInput       = document.getElementById('searchInput');
    var languageFilter    = document.getElementById('languageFilter');
    var dateFilter        = document.getElementById('dateFilter');
    var clearHistoryBtn   = document.getElementById('clearHistoryBtn');
    var loadMoreBtn       = document.getElementById('loadMoreBtn');

    // ── Update avatar ─────────────────────────────────────────────────────────
    var storedUser = Api.getUser();
    if (storedUser) {
        var avatarImg = document.querySelector('.user-avatar');
        if (avatarImg) {
            avatarImg.src = storedUser.avatar_url ||
                'https://ui-avatars.com/api/?name=' + encodeURIComponent(storedUser.full_name || storedUser.username) + '&background=065f46&color=fff';
        }
    }

    // ── Toast ─────────────────────────────────────────────────────────────────
    function showToast(message, type) {
        var t = document.createElement('div');
        t.className = 'toast toast-' + (type || 'success');
        t.textContent = message;
        document.body.appendChild(t);
        setTimeout(function () { t.remove(); }, 3000);
    }

    // ── Format date ───────────────────────────────────────────────────────────
    function formatDate(dateStr) {
        var date = new Date(dateStr);
        var now  = new Date();
        var diff = now - date;
        var days = Math.floor(diff / 86400000);
        if (days === 0) {
            var hrs = Math.floor(diff / 3600000);
            if (hrs === 0) { var m = Math.floor(diff / 60000); return m <= 1 ? 'Just now' : m + ' min ago'; }
            return hrs === 1 ? '1 hour ago' : hrs + ' hours ago';
        }
        if (days === 1) return 'Yesterday';
        if (days < 7)   return days + ' days ago';
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric',
            year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined });
    }

    // ── Render card ───────────────────────────────────────────────────────────
    function renderItem(item) {
        var favClass = item.is_favorite ? 'fav-active' : '';
        var favFill  = item.is_favorite ? 'currentColor' : 'none';
        var srcText  = String(item.source_text).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        var tgtText  = String(item.translated_text).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        return '<div class="history-card" data-id="' + item.id + '">' +
            '<div class="card-body">' +
                '<div class="card-top-row">' +
                    '<span class="badge-source">' + item.source_lang + '</span>' +
                    '<svg class="card-arrow" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>' +
                    '<span class="badge-target">' + item.target_lang + '</span>' +
                    '<span class="card-timestamp">' + formatDate(item.created_at) + '</span>' +
                '</div>' +
                '<div class="card-texts">' +
                    '<div class="card-text-block"><p class="label">Original</p><p class="content">' + srcText + '</p></div>' +
                    '<div class="card-text-block"><p class="label">Translation</p><p class="content">' + tgtText + '</p></div>' +
                '</div>' +
            '</div>' +
            '<div class="card-actions">' +
                '<button class="card-action-btn favorite-btn ' + favClass + '" data-id="' + item.id + '" title="Favorite">' +
                    '<svg width="18" height="18" fill="' + favFill + '" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>' +
                '</button>' +
                '<button class="card-action-btn copy-btn" data-text="' + item.translated_text.replace(/"/g, '&quot;') + '" title="Copy">' +
                    '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' +
                '</button>' +
                '<button class="card-action-btn retranslate-btn" data-src="' + item.source_text.replace(/"/g, '&quot;') + '" data-from="' + item.source_lang + '" data-to="' + item.target_lang + '" title="Translate again">' +
                    '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>' +
                '</button>' +
                '<button class="card-action-btn delete-btn" data-id="' + item.id + '" title="Delete">' +
                    '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
                '</button>' +
            '</div>' +
        '</div>';
    }

    // ── Fetch & render ────────────────────────────────────────────────────────
    async function loadHistory(append) {
        var params = { page: currentPage, per_page: itemsPerPage };
        var s = searchInput.value.trim();
        var l = languageFilter.value;
        var d = dateFilter.value;
        if (s) params.search = s;
        if (l) params.lang   = l;
        if (d) params.date   = d;

        var res = await Api.getHistory(params);
        if (!res.ok) { showToast(res.data.message || 'Failed to load history', 'error'); return; }

        var payload    = res.data.data;
        totalPages     = payload.total_pages;
        totalCount.textContent = payload.total;

        if (!append) {
            historyData = payload.items;
            historyContainer.innerHTML = '';
        } else {
            historyData = historyData.concat(payload.items);
        }

        if (historyData.length === 0) {
            emptyState.classList.remove('is-hidden');
            loadMoreContainer.classList.add('is-hidden');
        } else {
            emptyState.classList.add('is-hidden');
            historyContainer.innerHTML = historyData.map(renderItem).join('');
            loadMoreContainer.classList.toggle('is-hidden', currentPage >= totalPages);
        }
        attachListeners();
    }

    function applyFilters() { currentPage = 1; loadHistory(false); }

    // ── Card listeners ────────────────────────────────────────────────────────
    function attachListeners() {
        document.querySelectorAll('.favorite-btn').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                var id  = parseInt(btn.dataset.id);
                var res = await Api.toggleFavorite(id);
                if (res.ok) {
                    var item = historyData.find(function (i) { return i.id === id; });
                    if (item) item.is_favorite = res.data.data.is_favorite;
                    historyContainer.innerHTML = historyData.map(renderItem).join('');
                    attachListeners();
                }
            });
        });

        document.querySelectorAll('.copy-btn').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                try { await navigator.clipboard.writeText(btn.dataset.text); showToast('Copied!'); }
                catch (e) { showToast('Copy failed', 'error'); }
            });
        });

        document.querySelectorAll('.retranslate-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                sessionStorage.setItem('retranslate', JSON.stringify({
                    text: btn.dataset.src, from: btn.dataset.from, to: btn.dataset.to
                }));
                window.location.href = 'translator.php';
            });
        });

        document.querySelectorAll('.delete-btn').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                if (!confirm('Delete this translation?')) return;
                var id  = parseInt(btn.dataset.id);
                var res = await Api.deleteHistory(id);
                if (res.ok) { showToast('Deleted'); applyFilters(); }
                else { showToast(res.data.message || 'Delete failed', 'error'); }
            });
        });
    }

    // ── Filter listeners ──────────────────────────────────────────────────────
    var debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 350);
    });
    languageFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);

    loadMoreBtn.addEventListener('click', function () { currentPage++; loadHistory(true); });

    clearHistoryBtn.addEventListener('click', async function () {
        if (!confirm('Clear ALL history? This cannot be undone.')) return;
        var res = await Api.getHistory({ per_page: 50 });
        if (res.ok) {
            await Promise.all(res.data.data.items.map(function (i) { return Api.deleteHistory(i.id); }));
            showToast('All history cleared');
            applyFilters();
        }
    });

    // ── Initial load ──────────────────────────────────────────────────────────
    applyFilters();
})();
