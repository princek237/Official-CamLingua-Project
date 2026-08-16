(function () {
    'use strict';

    var BASE       = 'http://localhost/CamLingua/Server/api';
    var roleBadge  = { admin: 'badge-red', pro: 'badge-yellow', user: 'badge-gray' };
    var statusBadge = { active: 'badge-green', inactive: 'badge-gray', banned: 'badge-red' };
    var _token     = '';
    var _adminId   = 0;   // ID of the currently logged-in admin (used for self-checks)

    // Current page state per section
    var _pages = { users: 1, languages: 1, translations: 1, history: 1, subscriptions: 1, reports: 1 };

    // ── Utility ──────────────────────────────────────────────────────────────────

    function getHeaders() {
        return { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + _token };
    }

    function toast(message, type) {
        type = type || 'success';
        var container = document.getElementById('toast-container');
        if (!container) return;
        var el = document.createElement('div');
        el.className = 'toast ' + type;
        el.textContent = message;
        container.appendChild(el);
        setTimeout(function () {
            el.classList.add('toast-fadeout');
            setTimeout(function () { el.remove(); }, 350);
        }, 3500);
    }

    function debounce(fn, delay) {
        var timer;
        return function () { clearTimeout(timer); timer = setTimeout(fn, delay); };
    }

    function fmt(dateStr) {
        if (!dateStr) return '—';
        return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function truncate(str, n) {
        if (!str) return '—';
        return str.length > n ? str.substring(0, n) + '…' : str;
    }

    function renderPagination(containerId, pagination, loadFn, sectionKey) {
        var el = document.getElementById(containerId);
        if (!el) return;
        if (!pagination || pagination.total_pages <= 1) { el.innerHTML = ''; return; }

        var current = pagination.page;
        var total   = pagination.total_pages;
        var html    = '<span class="pagination-info">Page ' + current + ' of ' + total + '</span>';
        html += '<button ' + (current <= 1 ? 'disabled' : '') + ' onclick="' + sectionKey + 'GoToPage(' + (current - 1) + ')">&laquo; Prev</button>';
        var start = Math.max(1, current - 2);
        var end   = Math.min(total, start + 4);
        for (var i = start; i <= end; i++) {
            html += '<button class="' + (i === current ? 'active' : '') + '" onclick="' + sectionKey + 'GoToPage(' + i + ')">' + i + '</button>';
        }
        html += '<button ' + (current >= total ? 'disabled' : '') + ' onclick="' + sectionKey + 'GoToPage(' + (current + 1) + ')">Next &raquo;</button>';
        el.innerHTML = html;
    }

    // ── Access Denied / Auth ──────────────────────────────────────────────────────

    function showAccessDenied(reason) {
        document.body.innerHTML =
            '<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f9fafb;">' +
            '<div style="text-align:center;padding:48px 32px;background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);max-width:400px;">' +
            '<h1 style="font-size:1.375rem;font-weight:800;color:#111827;margin-bottom:8px;">Access Denied</h1>' +
            '<p style="font-size:0.9375rem;color:#6b7280;margin-bottom:28px;">' + (reason || 'You do not have permission.') + '</p>' +
            '<a href="login.php" style="display:inline-block;background:#15803d;color:#fff;font-weight:600;font-size:0.875rem;padding:10px 24px;border-radius:9999px;text-decoration:none;">Go to Login</a>' +
            '</div></div>';
    }

    // ── Counter Animation ────────────────────────────────────────────────────────

    function animateCounter(id, value) {
        var el = document.getElementById(id);
        if (!el) return;
        var target  = parseInt(String(value).replace(/,/g, ''), 10) || 0;
        var current = 0;
        var step    = Math.ceil(target / 60) || 1;
        var timer   = setInterval(function () {
            current = Math.min(current + step, target);
            el.textContent = current.toLocaleString();
            if (current >= target) clearInterval(timer);
        }, 20);
    }

    // ── Dashboard Charts ─────────────────────────────────────────────────────────

    function renderLineChart(labels, values) {
        var canvas = document.getElementById('translationsLineChart');
        if (!canvas) return;
        if (canvas._chart) { canvas._chart.destroy(); }
        canvas._chart = new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels || ['No Data'],
                datasets: [{
                    label: 'Translations', data: values || [0],
                    borderColor: '#15803d', backgroundColor: 'rgba(21,128,61,0.08)',
                    borderWidth: 2.5, pointBackgroundColor: '#15803d', pointRadius: 4,
                    fill: true, tension: 0.4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 10 } } },
                    y: { grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', font: { size: 10 } } }
                }
            }
        });
    }

    function renderDonutChart(langData) {
        var canvas = document.getElementById('languagesDonutChart');
        if (!canvas) return;
        if (canvas._chart) { canvas._chart.destroy(); }
        var languages = langData ? langData.map(function (l) { return l.name; }) : ['No Data'];
        var counts    = langData ? langData.map(function (l) { return l.count; }) : [1];
        var colors    = ['#15803d', '#16a34a', '#4ade80', '#f59e0b', '#e5e7eb'];
        canvas._chart = new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: { labels: languages, datasets: [{ data: counts, backgroundColor: colors, borderWidth: 0, hoverOffset: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
        });
        var legend = document.getElementById('donut-legend');
        if (legend) {
            legend.innerHTML = languages.map(function (lang, i) {
                return '<li><span class="legend-left"><span class="legend-dot" style="background:' + colors[i] + '"></span>' + lang + '</span>' +
                    '<span class="legend-pct">' + counts[i] + '</span></li>';
            }).join('');
        }
    }

    function renderRecentTranslations(rows) {
        var tbody = document.getElementById('translations-table-body');
        if (!tbody) return;
        if (!rows || !rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px;">No translations yet.</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(function (t) {
            return '<tr>' +
                '<td class="td-mono">#' + t.id + '</td>' +
                '<td><span class="badge badge-blue">'  + (t.source_lang || '') + '</span></td>' +
                '<td><span class="badge badge-green">' + (t.target_lang  || '') + '</span></td>' +
                '<td class="td-trunc" title="' + (t.source_text || '') + '">' + truncate(t.source_text, 40) + '</td>' +
                '<td class="td-user">'  + (t.user_id    || '—') + '</td>' +
                '<td class="td-date">'  + fmt(t.created_at)     + '</td>' +
                '</tr>';
        }).join('');
    }

    // ── Dashboard ────────────────────────────────────────────────────────────────

    async function loadDashboard() {
        try {
            var res  = await fetch(BASE + '/admin/dashboard', { headers: getHeaders() });
            var data = res.ok ? await res.json() : null;
            if (data && data.data) {
                var d  = data.data;
                animateCounter('stat-users',        d.stats.users         || 0);
                animateCounter('stat-translations', d.stats.translations  || 0);
                animateCounter('stat-languages',    d.stats.languages     || 0);
                animateCounter('stat-revenue',      d.stats.subscriptions || 0);
                renderRecentTranslations(d.recent_translations || []);
                var cd = d.chart_data || {};
                renderLineChart(cd.labels || null, cd.values || null);
                renderDonutChart(d.top_languages || null);
            }
        } catch (e) { console.error('Dashboard error:', e); }
        loadActiveLanguages();
    }

    async function loadActiveLanguages() {
        var container = document.getElementById('active-languages-list');
        if (!container) return;
        try {
            var res  = await fetch(BASE + '/admin/languages?limit=100&is_active=1', { headers: getHeaders() });
            var data = await res.json();
            var langs = (data && data.data && data.data.languages)
                ? data.data.languages.filter(function (l) { return parseInt(l.is_active) === 1; })
                : [];
            renderActiveLanguages(langs);
        } catch (e) {
            container.innerHTML = '<p style="color:#9ca3af;padding:16px;">Failed to load languages.</p>';
        }
    }

    function renderActiveLanguages(langs) {
        var container = document.getElementById('active-languages-list');
        if (!container) return;
        if (!langs.length) {
            container.innerHTML = '<p style="color:#9ca3af;padding:16px;">No active languages found.</p>';
            return;
        }
        container.innerHTML = langs.map(function (l) {
            return '<div class="active-lang-chip" title="' + (l.name || '') + '">' +
                '<span class="active-lang-code">' + (l.code || '') + '</span>' +
                '<span class="active-lang-name">' + (l.name || '') + '</span>' +
                '</div>';
        }).join('');
    }

    // ── Users ────────────────────────────────────────────────────────────────────

    async function loadUsers() {
        var search = (document.getElementById('users-search')        || {}).value || '';
        var status = (document.getElementById('users-status-filter') || {}).value || '';
        var page   = _pages.users;
        var tbody  = document.getElementById('full-users-table-body');
        if (tbody) tbody.innerHTML = '<tr><td colspan="8" class="td-loading">Loading…</td></tr>';
        try {
            var url  = BASE + '/admin/users?page=' + page + '&limit=10&search=' + encodeURIComponent(search) + '&status=' + encodeURIComponent(status);
            var res  = await fetch(url, { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data) {
                renderUsersTable(data.data.users || []);
                renderPagination('users-pagination', data.data.pagination, loadUsers, 'users');
            }
        } catch (e) { console.error('Users error:', e); }
    }

    function renderUsersTable(users) {
        var tbody = document.getElementById('full-users-table-body');
        if (!tbody) return;
        if (!users.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:24px;">No users found.</td></tr>';
            return;
        }
        tbody.innerHTML = users.map(function (u) {
            var sBadge = statusBadge[u.status] || 'badge-gray';
            var rBadge = roleBadge[u.role]     || 'badge-gray';
            var isSelf = (u.id === _adminId);

            // Role action button: grant admin or revoke admin
            var roleBtn;
            if (isSelf) {
                // Can't change own role — show a disabled hint
                roleBtn = '<button class="btn-icon btn-role-self" disabled title="You cannot change your own role">' + iconShield() + '</button>';
            } else if (u.role === 'admin') {
                roleBtn = '<button class="btn-icon btn-role-revoke" title="Revoke Admin" onclick="openRoleModal(' + u.id + ',\'user\',\'' + escAttr(u.username) + '\')">' + iconShieldOff() + '</button>';
            } else {
                roleBtn = '<button class="btn-icon btn-role-grant" title="Assign Admin" onclick="openRoleModal(' + u.id + ',\'admin\',\'' + escAttr(u.username) + '\')">' + iconShield() + '</button>';
            }

            return '<tr>' +
                '<td class="td-mono">#' + u.id + '</td>' +
                '<td class="td-user">' + (u.username || '—') + '</td>' +
                '<td>' + (u.email || '—') + '</td>' +
                '<td>' + (u.phone_number || '—') + '</td>' +
                '<td><span class="badge ' + rBadge + '">' + u.role + '</span></td>' +
                '<td><span class="badge ' + sBadge + '">' + u.status + '</span></td>' +
                '<td class="td-date">' + fmt(u.created_at) + '</td>' +
                '<td><div class="row-actions">' +
                    roleBtn +
                    '<button class="btn-icon" title="Edit" onclick="editUser(' + u.id + ')">' + iconEdit() + '</button>' +
                    '<button class="btn-icon danger" title="Delete" onclick="confirmDelete(\'user\',' + u.id + ')">' + iconTrash() + '</button>' +
                '</div></td>' +
                '</tr>';
        }).join('');
    }

    function escAttr(str) {
        return (str || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    window.usersGoToPage = function (p) { _pages.users = p; loadUsers(); };

    // ── Role Modal ───────────────────────────────────────────────────────────────

    /**
     * Open the role-change confirmation modal.
     * @param {number} userId
     * @param {string} targetRole  'admin' | 'user'
     * @param {string} username
     */
    window.openRoleModal = function (userId, targetRole, username) {
        document.getElementById('role-user-id').value     = userId;
        document.getElementById('role-target-role').value = targetRole;

        var isGrant = targetRole === 'admin';
        document.getElementById('modal-role-title').textContent = isGrant ? 'Assign Admin Role' : 'Revoke Admin Role';
        document.getElementById('role-confirm-message').textContent = isGrant
            ? 'Grant admin privileges to "' + username + '"?'
            : 'Revoke admin privileges from "' + username + '"?';
        document.getElementById('role-confirm-sub').textContent = isGrant
            ? 'This user will have full access to the admin dashboard and all management features.'
            : 'This user will lose admin access immediately and be downgraded to a regular user.';

        var btn = document.getElementById('role-confirm-btn');
        btn.className = isGrant ? 'btn-primary' : 'btn-danger';
        btn.textContent = isGrant ? 'Assign Admin' : 'Revoke Admin';

        openModal('role');
    };

    window.executeRoleChange = async function () {
        var userId     = document.getElementById('role-user-id').value;
        var targetRole = document.getElementById('role-target-role').value;
        var btn        = document.getElementById('role-confirm-btn');

        btn.disabled    = true;
        btn.textContent = 'Saving…';

        try {
            var res  = await fetch(BASE + '/admin/users/' + userId + '/role', {
                method:  'PUT',
                headers: getHeaders(),
                body:    JSON.stringify({ role: targetRole }),
            });
            var data = await res.json();

            if (res.ok) {
                toast(data.data ? data.data.message : 'Role updated successfully.');
                closeModal('role');
                loadUsers();
            } else {
                toast((data && data.message) || 'Error updating role.', 'error');
            }
        } catch (e) {
            toast('Server error.', 'error');
        }

        btn.disabled    = false;
        btn.textContent = targetRole === 'admin' ? 'Assign Admin' : 'Revoke Admin';
    };

    // ── Edit / Create User ────────────────────────────────────────────────────────

    window.editUser = async function (id) {
        try {
            var res  = await fetch(BASE + '/admin/users/' + id, { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data && data.data.user) {
                var u = data.data.user;
                document.getElementById('user-id').value           = u.id;
                document.getElementById('user-username').value     = u.username     || '';
                document.getElementById('user-email').value        = u.email        || '';
                document.getElementById('user-full_name').value    = u.full_name    || '';
                document.getElementById('user-phone_number').value = u.phone_number || '';
                document.getElementById('user-status').value       = u.status       || 'active';
                document.getElementById('user-password').value     = '';

                // Update role display badge (read-only)
                var badge    = document.getElementById('user-role-badge');
                var roleDisp = document.getElementById('user-role-display');
                if (badge) {
                    badge.textContent = u.role || 'user';
                    badge.className   = 'badge ' + (roleBadge[u.role] || 'badge-gray');
                }
                // Hide the note when viewing an admin — already admin
                var note = document.getElementById('user-role-note');
                if (note) note.style.display = u.role === 'admin' ? 'none' : '';

                document.getElementById('modal-user-title').textContent  = 'Edit User';
                document.getElementById('user-submit-btn').textContent   = 'Update User';
                document.getElementById('user-password-required').style.display = 'none';
                document.getElementById('user-password-hint').style.display     = 'block';
                openModal('user');
            }
        } catch (e) { toast('Failed to load user.', 'error'); }
    };

    window.submitUserForm = async function (e) {
        e.preventDefault();
        var id  = document.getElementById('user-id').value;
        // Note: 'role' is intentionally omitted — role is managed via assignRole endpoint only
        var body = {
            username:     document.getElementById('user-username').value,
            email:        document.getElementById('user-email').value,
            full_name:    document.getElementById('user-full_name').value,
            phone_number: document.getElementById('user-phone_number').value,
            status:       document.getElementById('user-status').value,
            password:     document.getElementById('user-password').value,
        };
        var btn = document.getElementById('user-submit-btn');
        btn.disabled = true; btn.textContent = 'Saving…';
        try {
            var res, msg;
            if (id) {
                res = await fetch(BASE + '/admin/users/' + id, { method: 'PUT', headers: getHeaders(), body: JSON.stringify(body) });
                msg = 'User updated.';
            } else {
                res = await fetch(BASE + '/admin/users', { method: 'POST', headers: getHeaders(), body: JSON.stringify(body) });
                msg = 'User created.';
            }
            var data = await res.json();
            if (res.ok) {
                toast(msg); closeModal('user'); loadUsers();
            } else {
                toast((data && data.message) || 'Error saving user.', 'error');
            }
        } catch (e) { toast('Server error.', 'error'); }
        btn.disabled    = false;
        btn.textContent = id ? 'Update User' : 'Create User';
    };

    // ── Languages ────────────────────────────────────────────────────────────────

    async function loadLanguages() {
        var search = (document.getElementById('languages-search')        || {}).value || '';
        var status = (document.getElementById('languages-status-filter') || {}).value || '';
        var page   = _pages.languages;
        var tbody  = document.getElementById('full-languages-table-body');
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="td-loading">Loading…</td></tr>';
        try {
            var url  = BASE + '/admin/languages?page=' + page + '&limit=10&search=' + encodeURIComponent(search);
            if (status !== '') url += '&is_active=' + encodeURIComponent(status);
            var res  = await fetch(url, { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data) {
                var langs = data.data.languages || [];
                if (status !== '') {
                    langs = langs.filter(function (l) { return String(l.is_active) === status; });
                }
                renderLanguagesTable(langs);
                renderPagination('languages-pagination', data.data.pagination, loadLanguages, 'languages');
            }
        } catch (e) { console.error('Languages error:', e); }
    }

    function renderLanguagesTable(languages) {
        var tbody = document.getElementById('full-languages-table-body');
        if (!tbody) return;
        if (!languages.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px;">No languages found.</td></tr>';
            return;
        }
        tbody.innerHTML = languages.map(function (l) {
            var active = parseInt(l.is_active)
                ? '<span class="badge badge-green">Active</span>'
                : '<span class="badge badge-gray">Inactive</span>';
            return '<tr>' +
                '<td class="td-mono">#' + l.id + '</td>' +
                '<td class="td-user">' + (l.name || '—') + '</td>' +
                '<td><span class="badge badge-blue">' + (l.code || '') + '</span></td>' +
                '<td>' + (l.translations_count || 0) + '</td>' +
                '<td>' + active + '</td>' +
                '<td><div class="row-actions">' +
                    '<button class="btn-icon" title="Edit" onclick="editLanguage(' + l.id + ')">' + iconEdit() + '</button>' +
                    '<button class="btn-icon danger" title="Delete" onclick="confirmDelete(\'language\',' + l.id + ')">' + iconTrash() + '</button>' +
                '</div></td>' +
                '</tr>';
        }).join('');
    }

    window.languagesGoToPage = function (p) { _pages.languages = p; loadLanguages(); };

    window.editLanguage = async function (id) {
        try {
            var res  = await fetch(BASE + '/admin/languages/' + id, { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data && data.data.language) {
                var l = data.data.language;
                document.getElementById('language-id').value        = l.id;
                document.getElementById('language-name').value      = l.name      || '';
                document.getElementById('language-code').value      = l.code      || '';
                document.getElementById('language-is_active').value = l.is_active;
                document.getElementById('modal-language-title').textContent  = 'Edit Language';
                document.getElementById('language-submit-btn').textContent   = 'Update Language';
                openModal('language');
            }
        } catch (e) { toast('Failed to load language.', 'error'); }
    };

    window.submitLanguageForm = async function (e) {
        e.preventDefault();
        var id   = document.getElementById('language-id').value;
        var body = {
            name:      document.getElementById('language-name').value,
            code:      document.getElementById('language-code').value,
            is_active: document.getElementById('language-is_active').value,
        };
        var btn = document.getElementById('language-submit-btn');
        btn.disabled = true; btn.textContent = 'Saving…';
        try {
            var res, msg;
            if (id) {
                res = await fetch(BASE + '/admin/languages/' + id, { method: 'PUT', headers: getHeaders(), body: JSON.stringify(body) });
                msg = 'Language updated.';
            } else {
                res = await fetch(BASE + '/admin/languages', { method: 'POST', headers: getHeaders(), body: JSON.stringify(body) });
                msg = 'Language created.';
            }
            var data = await res.json();
            if (res.ok) {
                toast(msg); closeModal('language'); loadLanguages();
            } else {
                toast((data && data.message) || 'Error saving language.', 'error');
            }
        } catch (e) { toast('Server error.', 'error'); }
        btn.disabled    = false;
        btn.textContent = id ? 'Update Language' : 'Create Language';
    };

    // ── Translations ─────────────────────────────────────────────────────────────

    async function loadTranslations() {
        var search = (document.getElementById('translations-search')     || {}).value || '';
        var lang   = (document.getElementById('translations-lang-filter')|| {}).value || '';
        var date   = (document.getElementById('translations-date-filter')|| {}).value || '';
        var page   = _pages.translations;
        var tbody  = document.getElementById('full-translations-table-body');
        if (tbody) tbody.innerHTML = '<tr><td colspan="9" class="td-loading">Loading…</td></tr>';
        try {
            var url  = BASE + '/admin/translations?page=' + page + '&limit=10&search=' + encodeURIComponent(search) + '&language=' + encodeURIComponent(lang) + '&date=' + encodeURIComponent(date);
            var res  = await fetch(url, { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data) {
                renderTranslationsTable(data.data.translations || [], 'full-translations-table-body');
                renderPagination('translations-pagination', data.data.pagination, loadTranslations, 'translations');
            }
        } catch (e) { console.error('Translations error:', e); }
    }

    async function loadHistory() {
        var search = (document.getElementById('history-search')      || {}).value || '';
        var lang   = (document.getElementById('history-lang-filter') || {}).value || '';
        var date   = (document.getElementById('history-date-filter') || {}).value || '';
        var page   = _pages.history;
        var tbody  = document.getElementById('full-history-table-body');
        if (tbody) tbody.innerHTML = '<tr><td colspan="9" class="td-loading">Loading…</td></tr>';
        try {
            var url  = BASE + '/admin/translations?page=' + page + '&limit=10&search=' + encodeURIComponent(search) + '&language=' + encodeURIComponent(lang) + '&date=' + encodeURIComponent(date);
            var res  = await fetch(url, { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data) {
                renderTranslationsTable(data.data.translations || [], 'full-history-table-body');
                renderPagination('history-pagination', data.data.pagination, loadHistory, 'history');
            }
        } catch (e) { console.error('History error:', e); }
    }

    function renderTranslationsTable(translations, tbodyId) {
        var tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        if (!translations.length) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:24px;">No translations found.</td></tr>';
            return;
        }
        tbody.innerHTML = translations.map(function (t) {
            var stat      = t.status || 'completed';
            var statBadge = stat === 'completed' ? 'badge-green' : (stat === 'pending' ? 'badge-yellow' : 'badge-red');
            return '<tr>' +
                '<td class="td-mono">#' + t.id + '</td>' +
                '<td><span class="badge badge-blue">'  + (t.source_lang || '') + '</span></td>' +
                '<td><span class="badge badge-green">' + (t.target_lang  || '') + '</span></td>' +
                '<td class="td-trunc" title="' + (t.source_text      || '') + '">' + truncate(t.source_text,      30) + '</td>' +
                '<td class="td-trunc" title="' + (t.translated_text  || '') + '">' + truncate(t.translated_text,  30) + '</td>' +
                '<td><span class="badge ' + statBadge + '">' + stat + '</span></td>' +
                '<td class="td-user">' + (t.user_id || 'Guest') + '</td>' +
                '<td class="td-date">' + fmt(t.created_at)      + '</td>' +
                '<td><div class="row-actions">' +
                    '<button class="btn-icon danger" title="Delete" onclick="confirmDelete(\'translation\',' + t.id + ')">' + iconTrash() + '</button>' +
                '</div></td>' +
                '</tr>';
        }).join('');
    }

    window.translationsGoToPage = function (p) { _pages.translations = p; loadTranslations(); };
    window.historyGoToPage      = function (p) { _pages.history      = p; loadHistory();      };

    async function populateLangFilters() {
        try {
            var res  = await fetch(BASE + '/admin/languages?limit=100', { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data && data.data.languages) {
                var opts = data.data.languages.map(function (l) {
                    return '<option value="' + l.code + '">' + l.name + ' (' + l.code + ')</option>';
                }).join('');
                ['translations-lang-filter', 'history-lang-filter'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) el.innerHTML = '<option value="">All Languages</option>' + opts;
                });
            }
        } catch (e) {}
    }

    // ── Subscriptions ────────────────────────────────────────────────────────────

    async function loadSubscriptions() {
        var page  = _pages.subscriptions;
        var tbody = document.getElementById('full-subscriptions-table-body');
        if (tbody) tbody.innerHTML = '<tr><td colspan="8" class="td-loading">Loading…</td></tr>';
        try {
            var res  = await fetch(BASE + '/admin/subscriptions?page=' + page + '&limit=10', { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data) {
                renderSubscriptionsTable(data.data.subscriptions || []);
                renderPagination('subscriptions-pagination', data.data.pagination, loadSubscriptions, 'subscriptions');
            }
        } catch (e) { console.error('Subscriptions error:', e); }
    }

    function renderSubscriptionsTable(subs) {
        var tbody = document.getElementById('full-subscriptions-table-body');
        if (!tbody) return;
        if (!subs.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:24px;">No plans found.</td></tr>';
            return;
        }
        tbody.innerHTML = subs.map(function (s) {
            var active = parseInt(s.is_active)
                ? '<span class="badge badge-green">Active</span>'
                : '<span class="badge badge-gray">Inactive</span>';
            return '<tr>' +
                '<td class="td-mono">#' + s.id + '</td>' +
                '<td class="td-user">' + (s.name || '—') + '</td>' +
                '<td><span class="badge badge-blue">' + (s.slug || '') + '</span></td>' +
                '<td>' + Number(s.price_monthly).toLocaleString() + ' XAF</td>' +
                '<td>' + Number(s.price_yearly).toLocaleString()  + ' XAF</td>' +
                '<td>' + (s.active_subscribers || 0) + '</td>' +
                '<td>' + active + '</td>' +
                '<td><div class="row-actions">' +
                    '<button class="btn-icon" title="Edit" onclick="editSubscription(' + s.id + ')">' + iconEdit() + '</button>' +
                    '<button class="btn-icon danger" title="Delete" onclick="confirmDelete(\'subscription\',' + s.id + ')">' + iconTrash() + '</button>' +
                '</div></td>' +
                '</tr>';
        }).join('');
    }

    window.subscriptionsGoToPage = function (p) { _pages.subscriptions = p; loadSubscriptions(); };

    window.editSubscription = async function (id) {
        try {
            var res  = await fetch(BASE + '/admin/subscriptions/' + id, { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data && data.data.subscription) {
                var s = data.data.subscription;
                document.getElementById('subscription-id').value      = s.id;
                document.getElementById('sub-name').value             = s.name          || '';
                document.getElementById('sub-slug').value             = s.slug          || '';
                document.getElementById('sub-price-monthly').value    = s.price_monthly || 0;
                document.getElementById('sub-price-yearly').value     = s.price_yearly  || 0;
                document.getElementById('sub-is-active').value        = s.is_active;
                document.getElementById('sub-description').value      = s.description   || '';
                document.getElementById('modal-sub-title').textContent         = 'Edit Plan';
                document.getElementById('subscription-submit-btn').textContent = 'Update Plan';
                openModal('subscription');
            }
        } catch (e) { toast('Failed to load subscription.', 'error'); }
    };

    window.submitSubscriptionForm = async function (e) {
        e.preventDefault();
        var id   = document.getElementById('subscription-id').value;
        var body = {
            name:          document.getElementById('sub-name').value,
            slug:          document.getElementById('sub-slug').value,
            price_monthly: document.getElementById('sub-price-monthly').value,
            price_yearly:  document.getElementById('sub-price-yearly').value,
            is_active:     document.getElementById('sub-is-active').value,
            description:   document.getElementById('sub-description').value,
        };
        var btn = document.getElementById('subscription-submit-btn');
        btn.disabled = true; btn.textContent = 'Saving…';
        try {
            var res, msg;
            if (id) {
                res = await fetch(BASE + '/admin/subscriptions/' + id, { method: 'PUT', headers: getHeaders(), body: JSON.stringify(body) });
                msg = 'Plan updated.';
            } else {
                res = await fetch(BASE + '/admin/subscriptions', { method: 'POST', headers: getHeaders(), body: JSON.stringify(body) });
                msg = 'Plan created.';
            }
            var data = await res.json();
            if (res.ok) {
                toast(msg); closeModal('subscription'); loadSubscriptions();
            } else {
                toast((data && data.message) || 'Error saving plan.', 'error');
            }
        } catch (e) { toast('Server error.', 'error'); }
        btn.disabled    = false;
        btn.textContent = id ? 'Update Plan' : 'Create Plan';
    };

    // ── Reports ──────────────────────────────────────────────────────────────────

    async function loadReports() {
        var page  = _pages.reports;
        var tbody = document.getElementById('full-reports-table-body');
        if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="td-loading">Loading…</td></tr>';
        try {
            var res  = await fetch(BASE + '/admin/reports?page=' + page + '&limit=10', { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data) {
                renderReportsTable(data.data.reports || []);
                var pbar = document.getElementById('reports-pagination');
                if (pbar) pbar.innerHTML = '';
            }
        } catch (e) { console.error('Reports error:', e); }
    }

    function renderReportsTable(reports) {
        var tbody = document.getElementById('full-reports-table-body');
        if (!tbody) return;
        if (!reports.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:24px;">No messages found.</td></tr>';
            return;
        }
        tbody.innerHTML = reports.map(function (r) {
            var statBadge = r.status === 'new' ? 'badge-yellow' : (r.status === 'replied' ? 'badge-green' : 'badge-gray');
            return '<tr>' +
                '<td class="td-mono">#' + r.id + '</td>' +
                '<td class="td-user">' + (r.full_name || '—') + '</td>' +
                '<td>' + (r.email || '—') + '</td>' +
                '<td>' + truncate(r.subject, 25) + '</td>' +
                '<td class="td-trunc" title="' + (r.message || '') + '">' + truncate(r.message, 40) + '</td>' +
                '<td><span class="badge ' + statBadge + '">' + r.status + '</span></td>' +
                '<td class="td-date">' + fmt(r.created_at) + '</td>' +
                '</tr>';
        }).join('');
    }

    // ── Settings ─────────────────────────────────────────────────────────────────

    async function loadSettings() {
        try {
            var res  = await fetch(BASE + '/admin/settings', { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data && data.data.settings) {
                var s = data.data.settings;
                Object.keys(s).forEach(function (key) {
                    var el = document.getElementById('setting-' + key);
                    if (el) el.value = s[key].value || '';
                });
            }
        } catch (e) { console.error('Settings error:', e); }
    }

    window.saveSettings = async function (e) {
        e.preventDefault();
        var form   = document.getElementById('settings-form');
        var inputs = form.querySelectorAll('input, select, textarea');
        var body   = {};
        inputs.forEach(function (el) { if (el.name) body[el.name] = el.value; });
        var btn = document.getElementById('settings-save-btn');
        btn.disabled = true; btn.textContent = 'Saving…';
        try {
            var res  = await fetch(BASE + '/admin/settings', { method: 'PUT', headers: getHeaders(), body: JSON.stringify(body) });
            var data = await res.json();
            if (res.ok) {
                toast('Settings saved successfully.');
            } else {
                toast((data && data.message) || 'Error saving settings.', 'error');
            }
        } catch (e) { toast('Server error.', 'error'); }
        btn.disabled    = false;
        btn.textContent = 'Save Settings';
    };

    // ── Delete Confirmation ──────────────────────────────────────────────────────

    var _deletePayload = null;

    window.confirmDelete = function (type, id) {
        _deletePayload = { type: type, id: id };
        var messages = {
            user:         'Are you sure you want to delete this user? All their data will be removed permanently.',
            language:     'Are you sure you want to delete this language?',
            translation:  'Are you sure you want to delete this translation record?',
            subscription: 'Are you sure you want to delete this plan? Plans with active subscribers cannot be deleted.',
        };
        document.getElementById('confirm-message').textContent = messages[type] || 'Are you sure?';
        document.getElementById('modal-confirm').style.display = 'flex';
    };

    window.closeConfirm = function () {
        _deletePayload = null;
        document.getElementById('modal-confirm').style.display = 'none';
    };

    document.addEventListener('DOMContentLoaded', function () {
        var confirmOkBtn = document.getElementById('confirm-ok-btn');
        if (confirmOkBtn) {
            confirmOkBtn.addEventListener('click', async function () {
                if (!_deletePayload) return;
                var type = _deletePayload.type;
                var id   = _deletePayload.id;
                var endpoints = {
                    user:         '/admin/users/',
                    language:     '/admin/languages/',
                    translation:  '/admin/translations/',
                    subscription: '/admin/subscriptions/',
                };
                var ep = endpoints[type];
                if (!ep) { closeConfirm(); return; }
                var btn = document.getElementById('confirm-ok-btn');
                btn.disabled = true; btn.textContent = 'Deleting…';
                try {
                    var res  = await fetch(BASE + ep + id, { method: 'DELETE', headers: getHeaders() });
                    var data = await res.json();
                    if (res.ok) {
                        toast('Deleted successfully.');
                        closeConfirm();
                        var reloaders = { user: loadUsers, language: loadLanguages, translation: loadTranslations, subscription: loadSubscriptions };
                        if (reloaders[type]) reloaders[type]();
                    } else {
                        toast((data && data.message) || 'Error deleting item.', 'error');
                    }
                } catch (e) { toast('Server error.', 'error'); }
                btn.disabled    = false;
                btn.textContent = 'Delete';
            });
        }
    });

    // ── Modal helpers ────────────────────────────────────────────────────────────

    window.openModal = function (name) {
        var m = document.getElementById('modal-' + name);
        if (m) m.style.display = 'flex';
    };

    window.closeModal = function (name) {
        var m = document.getElementById('modal-' + name);
        if (!m) return;
        m.style.display = 'none';
        var form = m.querySelector('form');
        if (form) form.reset();
        var hiddenId = m.querySelector('input[type="hidden"]');
        if (hiddenId) hiddenId.value = '';
        var title     = m.querySelector('.modal-title');
        var submitBtn = m.querySelector('[type="submit"]');
        var origTitles = { 'modal-user': 'Add User', 'modal-language': 'Add Language', 'modal-subscription': 'Add Plan' };
        var origBtns   = { 'modal-user': 'Create User', 'modal-language': 'Create Language', 'modal-subscription': 'Create Plan' };
        if (title     && origTitles[m.id]) title.textContent     = origTitles[m.id];
        if (submitBtn && origBtns[m.id])   submitBtn.textContent = origBtns[m.id];
        if (m.id === 'modal-user') {
            document.getElementById('user-password-required').style.display = '';
            document.getElementById('user-password-hint').style.display     = 'none';
            // Reset role badge display to default
            var badge = document.getElementById('user-role-badge');
            if (badge) { badge.textContent = 'user'; badge.className = 'badge badge-gray'; }
            var note = document.getElementById('user-role-note');
            if (note) note.style.display = '';
        }
    };

    // Close modals when clicking the backdrop
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('modal-overlay')) {
            var id = e.target.id;
            if (id === 'modal-confirm') { closeConfirm(); return; }
            if (id) closeModal(id.replace('modal-', ''));
        }
    });

    // ── SVG Icons ────────────────────────────────────────────────────────────────

    function iconEdit() {
        return '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
    }
    function iconTrash() {
        return '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
    }
    // Shield icon — grant admin
    function iconShield() {
        return '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>';
    }
    // Shield-off icon — revoke admin
    function iconShieldOff() {
        return '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 1.772-.455 3.38-1.31 4.738-2.464M3 3l18 18"/></svg>';
    }

    // ── SPA Navigation ───────────────────────────────────────────────────────────

    var _sectionLoaders = {
        dashboard:     loadDashboard,
        users:         loadUsers,
        languages:     loadLanguages,
        translations:  loadTranslations,
        history:       loadHistory,
        subscriptions: loadSubscriptions,
        reports:       loadReports,
        content:       loadCmsContent,
        settings:      loadSettings,
    };

    function initSPA() {
        var navItems = document.querySelectorAll('.admin-nav-item[data-target]');
        var sections = document.querySelectorAll('.admin-section');

        navItems.forEach(function (item) {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                var target = this.getAttribute('data-target');
                if (!target) return;
                navItems.forEach(function (nav) { nav.classList.remove('active'); });
                this.classList.add('active');
                sections.forEach(function (sec) {
                    sec.style.display = sec.id === 'section-' + target ? 'block' : 'none';
                });
                _pages[target] = 1;
                if (_sectionLoaders[target]) _sectionLoaders[target]();
            });
        });

        // Logout
        var logoutBtn = document.getElementById('btn-logout');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function (e) {
                e.preventDefault();
                Api.logout();
                window.location.href = 'login.php';
            });
        }

        // Expose debounce globally for inline oninput handlers
        window.debounce = debounce;
    }

    // ── Init ─────────────────────────────────────────────────────────────────────

    async function init() {
        if (!Api.isLoggedIn()) {
            showAccessDenied('Please log in to access the admin dashboard.');
            return;
        }

        _token = Api.getToken();
        var res = await Api.me();

        if (!res.ok) {
            showAccessDenied('Your session has expired. Please log in again.');
            Api.logout();
            return;
        }

        var user = res.data && res.data.data ? res.data.data.user : null;

        if (!user || user.role !== 'admin') {
            showAccessDenied('This page is restricted to administrators only.');
            return;
        }

        Api.setUser(user);
        _adminId = parseInt(user.id, 10) || 0;   // store for self-check in role buttons

        // Update header
        var initials = (user.username || 'A').substring(0, 1).toUpperCase();
        var avatarEl = document.getElementById('admin-avatar-initials');
        var nameEl   = document.getElementById('admin-profile-name');
        if (avatarEl) avatarEl.textContent = initials;
        if (nameEl)   nameEl.textContent   = user.username || 'Admin';

        var wrap = document.getElementById('admin-wrap');
        if (wrap) wrap.style.display = 'block';

        initSPA();
        populateLangFilters();
        loadDashboard();
    }

    document.addEventListener('DOMContentLoaded', init);

    // ── CMS Content Management ────────────────────────────────────────────────────

    // Maps each tab name to the field keys it owns
    var _cmsPanelKeys = {
        homepage: [
            'home_hero_badge','home_hero_title','home_hero_desc','home_hero_btn1','home_hero_btn2',
            'home_feat1_title','home_feat1_desc','home_feat2_title','home_feat2_desc',
            'home_feat3_title','home_feat3_desc','home_feat4_title','home_feat4_desc'
        ],
        about: [
            'about_hero_title','about_hero_desc',
            'about_story_p1','about_story_p2','about_story_p3',
            'about_mission_title','about_mission_text',
            'about_vision_title','about_vision_text',
            'about_stats_langs','about_stats_trans','about_stats_users',
            'about_cta_title','about_cta_desc'
        ],
        contact: [
            'contact_hero_title','contact_hero_desc',
            'contact_email','contact_phone','contact_location','contact_response_time'
        ],
        pricing: [
            'pricing_hero_badge','pricing_hero_title','pricing_hero_desc',
            'pricing_cta_title','pricing_cta_desc',
            'pricing_faq_1_q','pricing_faq_1_a',
            'pricing_faq_2_q','pricing_faq_2_a',
            'pricing_faq_3_q','pricing_faq_3_a'
        ],
        global: [
            'site_name','site_tagline','platform_logo',
            'social_github','social_twitter','social_linkedin'
        ]
    };

    // Cached content loaded from the API
    var _cmsData = {};

    async function loadCmsContent() {
        try {
            var res  = await fetch(BASE + '/admin/cms', { headers: getHeaders() });
            var data = await res.json();
            if (data && data.data && data.data.content) {
                _cmsData = data.data.content;
                // Populate whichever panel is currently visible
                var activeTab = document.querySelector('.cms-tab.active');
                if (activeTab) {
                    fillCmsPanel(activeTab.getAttribute('data-cms-tab'));
                }
            }
        } catch (e) { console.error('CMS load error:', e); }
    }

    function fillCmsPanel(tabName) {
        var keys = _cmsPanelKeys[tabName] || [];
        keys.forEach(function (key) {
            var el = document.getElementById('cms-' + key);
            if (!el) return;
            var val = _cmsData[key] ? (_cmsData[key].value !== undefined ? _cmsData[key].value : _cmsData[key]) : '';
            el.value = val;
        });
    }

    window.switchCmsTab = function (tabName) {
        // Update tab button states
        document.querySelectorAll('.cms-tab').forEach(function (btn) {
            var isActive = btn.getAttribute('data-cms-tab') === tabName;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-selected', isActive);
        });
        // Show/hide panels
        document.querySelectorAll('.cms-panel').forEach(function (panel) {
            panel.style.display = panel.id === 'cms-panel-' + tabName ? 'block' : 'none';
        });
        // Fill fields from cache (or trigger a load if cache is empty)
        if (Object.keys(_cmsData).length === 0) {
            loadCmsContent();
        } else {
            fillCmsPanel(tabName);
        }
    };

    window.saveCmsPanel = async function (e, panelName) {
        e.preventDefault();
        var keys   = _cmsPanelKeys[panelName] || [];
        var body   = {};
        keys.forEach(function (key) {
            var el = document.getElementById('cms-' + key);
            if (el) body[key] = el.value;
        });
        var btnId = 'cms-save-' + panelName;
        var btn   = document.getElementById(btnId);
        if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
        try {
            var res  = await fetch(BASE + '/admin/cms', { method: 'PUT', headers: getHeaders(), body: JSON.stringify(body) });
            var data = await res.json();
            if (res.ok) {
                toast('Content saved — changes are live on the website.');
                // Refresh cache
                Object.keys(body).forEach(function (k) {
                    if (_cmsData[k]) { _cmsData[k].value = body[k]; }
                    else             { _cmsData[k] = { value: body[k] }; }
                });
            } else {
                toast((data && data.message) || 'Error saving content.', 'error');
            }
        } catch (err) { toast('Server error.', 'error'); }
        if (btn) { btn.disabled = false; btn.textContent = _cmsBtnLabels[panelName] || 'Save'; }
    };

    var _cmsBtnLabels = {
        homepage: 'Save Homepage',
        about:    'Save About Page',
        contact:  'Save Contact Info',
        pricing:  'Save Pricing Page',
        global:   'Save Global Settings'
    };

    // Allow other parts of the SPA to jump to a named section
    window.showSection = function (sectionName) {
        var target = document.querySelector('[data-target="' + sectionName + '"]');
        if (target) target.click();
    };

})();
