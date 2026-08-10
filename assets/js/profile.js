/**
 * profile.js — CamLingua Profile page
 * Depends on api.js (loaded by footer.php).
 */
(function () {
    'use strict';

    // ── Auth guard ────────────────────────────────────────────────────────────
    if (!Api.isLoggedIn()) {
        window.location.href = 'login.php';
        return;
    }

    // ── DOM refs ──────────────────────────────────────────────────────────────
    var editBtn    = document.getElementById('editBtn');
    var editor     = document.getElementById('editor');
    var saveBtn    = document.getElementById('saveBtn');
    var cancelBtn  = document.getElementById('cancelBtn');
    var nameEl     = document.getElementById('name');
    var bioEl      = document.getElementById('bio');
    var emailEl    = document.getElementById('email');
    var nameInput  = document.getElementById('nameInput');
    var bioInput   = document.getElementById('bioInput');
    var emailInput = document.getElementById('emailInput');
    var photoWrap  = document.getElementById('photoWrap');
    var profileImg = document.getElementById('profileImage');
    var imageInput = document.getElementById('imageInput');

    // ── Load profile from API ─────────────────────────────────────────────────
    async function loadProfile() {
        var res = await Api.getProfile();
        if (!res.ok) return;
        var user = res.data.data.user;

        var displayName = user.full_name || user.username || 'Your Name';
        if (nameEl)  nameEl.textContent  = displayName;
        if (bioEl)   bioEl.textContent   = user.bio || 'A short bio goes here.';
        if (emailEl) { emailEl.textContent = user.email; emailEl.href = 'mailto:' + user.email; }

        var avatarSrc = user.avatar_url ||
            'https://ui-avatars.com/api/?name=' + encodeURIComponent(displayName) + '&background=166534&color=fff&size=140';
        if (profileImg) profileImg.src = avatarSrc;

        setNavInitials(displayName);

        var badge = document.querySelector('.profile-badge');
        if (badge && user.subscription_name) badge.textContent = user.subscription_name + ' Plan';

        var stripValues = document.querySelectorAll('.strip-value');
        if (stripValues[0]) stripValues[0].textContent = (user.stats && user.stats.total_translations) || 0;
        if (stripValues[1]) stripValues[1].textContent = (user.stats && user.stats.saved_words) || 0;
        if (stripValues[2]) stripValues[2].textContent = user.created_at
            ? new Date(user.created_at).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) : '—';
        if (stripValues[3]) stripValues[3].textContent = user.subscription_name || 'Free';

        // Update "View History" link to .php
        var histLink = document.querySelector('a[href*="translation-history"]');
        if (histLink) histLink.href = 'translation-history.php';
    }

    function setNavInitials(name) {
        var initials = name.split(' ').map(function (w) { return w[0]; }).filter(Boolean).slice(0, 2).join('').toUpperCase();
        var el = document.getElementById('navAvatarInitials');
        if (el) el.textContent = initials || '?';
    }

    // ── Edit / Save ───────────────────────────────────────────────────────────
    function openEditor() {
        if (nameInput)  nameInput.value  = nameEl  ? nameEl.textContent.trim()  : '';
        if (bioInput)   bioInput.value   = bioEl   ? bioEl.textContent.trim()   : '';
        if (emailInput) emailInput.value = emailEl ? emailEl.textContent.trim() : '';
        if (editor) { editor.hidden = false; editor.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        if (nameInput) nameInput.focus();
    }

    function closeEditor() { if (editor) editor.hidden = true; }

    if (editBtn)   editBtn.addEventListener('click', openEditor);
    if (cancelBtn) cancelBtn.addEventListener('click', closeEditor);

    if (saveBtn) {
        saveBtn.addEventListener('click', async function () {
            var newName = nameInput ? nameInput.value.trim() : '';
            var newBio  = bioInput  ? bioInput.value.trim()  : '';
            saveBtn.disabled    = true;
            saveBtn.textContent = 'Saving…';

            var res = await Api.updateProfile({ full_name: newName, bio: newBio });
            saveBtn.disabled    = false;
            saveBtn.textContent = 'Save Changes';

            if (res.ok) {
                var user = res.data.data.user;
                if (nameEl) nameEl.textContent = user.full_name || user.username;
                if (bioEl)  bioEl.textContent  = user.bio || '';
                Api.setUser(user);
                setNavInitials(user.full_name || user.username || '');
                closeEditor();
            } else {
                alert(res.data.message || 'Failed to save. Please try again.');
            }
        });
    }

    // ── Photo change ──────────────────────────────────────────────────────────
    function triggerPhotoSelect() { if (imageInput) imageInput.click(); }
    if (photoWrap) {
        photoWrap.addEventListener('click', triggerPhotoSelect);
        photoWrap.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); triggerPhotoSelect(); }
        });
    }
    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            var file = e.target.files && e.target.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function () { if (profileImg) profileImg.src = reader.result; };
            reader.readAsDataURL(file);
        });
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    loadProfile();
})();
