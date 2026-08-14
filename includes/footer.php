<?php $extraJs = $extraJs ?? []; ?>
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <a href="index.php" class="logo">
                    <img src="assets/logo.png" alt="CamLingua" class="logo-img">
                    <span class="logo-text">Cam<span>Lingua</span></span>
                </a>
                <p class="footer-tagline">Translate. Connect. Preserve Cameroon's Languages.</p>
            </div>
            <nav class="footer-col">
                <p class="footer-col-heading">Product</p>
                <a href="translator.php">Translator</a>
                <a href="languages.php">Languages</a>
                <a href="translation-history.php">History</a>
                <a href="subscription.php">Pricing</a>
            </nav>
            <nav class="footer-col">
                <p class="footer-col-heading">Company</p>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="support.php">Help Center</a>
            </nav>
            <nav class="footer-col">
                <p class="footer-col-heading">Legal</p>
                <a href="privacy.php">Privacy Policy</a>
                <a href="terms-of-service.php">Terms of Service</a>
            </nav>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 CamLingua. All rights reserved. Made with ❤️ in Cameroon.</p>
        </div>
    </footer>

    <script src="assets/js/api.js?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/CamLingua/assets/js/api.js') ?>"></script>
    <script src="assets/js/script.js?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/CamLingua/assets/js/script.js') ?>"></script>
    <?php foreach ($extraJs as $js): ?>
        <script src="assets/js/<?= htmlspecialchars($js) ?>?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/CamLingua/assets/js/' . $js) ?>"></script>
    <?php endforeach; ?>

    <script>
    (function () {
        if (typeof Api === 'undefined') return;

        const user       = Api.getUser();
        const navActions = document.querySelector('.nav-actions');
        const mobAuth    = document.querySelector('.mob-nav-auth');

        // Show/hide auth-only nav links (Translator, History)
        document.querySelectorAll('.nav-auth-only').forEach(function (el) {
            el.style.display = user ? '' : 'none';
        });

        if (user) {
            const avatarSrc = user.avatar_url ||
                'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.full_name || user.username || 'U') + '&background=15803d&color=fff&size=40';

            if (navActions) {
                const ham = navActions.querySelector('.nav-hamburger');
                navActions.innerHTML = '';

                const wrapper = document.createElement('div');
                wrapper.className = 'nav-user-wrap';

                const pill = document.createElement('button');
                pill.className = 'nav-avatar-btn';
                pill.setAttribute('aria-haspopup', 'true');
                pill.setAttribute('aria-expanded', 'false');
                pill.innerHTML =
                    '<img src="' + avatarSrc + '" alt="avatar" class="nav-avatar-img">' +
                    '<span class="nav-avatar-name">' + (user.username || 'Account') + '</span>' +
                    '<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>';

                const drop = document.createElement('div');
                drop.className = 'nav-dropdown';
                drop.innerHTML =
                    '<a href="profile.php"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> My Profile</a>' +
                    '<a href="translation-history.php"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> History</a>' +
                    '<a href="subscription.php"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg> Subscription</a>' +
                    '<hr class="nav-drop-divider">' +
                    '<button id="logoutBtn"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg> Log Out</button>';

                wrapper.appendChild(pill);
                wrapper.appendChild(drop);
                navActions.appendChild(wrapper);
                if (ham) navActions.appendChild(ham);

                pill.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const open = drop.classList.toggle('open');
                    pill.setAttribute('aria-expanded', open);
                });
                document.addEventListener('click', function () {
                    drop.classList.remove('open');
                    pill.setAttribute('aria-expanded', 'false');
                });
            }

            if (mobAuth) {
                mobAuth.innerHTML =
                    '<a href="profile.php" class="btn btn-ghost">My Profile</a>' +
                    '<button class="btn btn-primary" id="logoutBtnMob">Log Out</button>';
            }

            document.addEventListener('click', async function (e) {
                if (e.target.id === 'logoutBtn' || e.target.closest('#logoutBtn') ||
                    e.target.id === 'logoutBtnMob') {
                    await Api.logoutRemote();
                    window.location.href = 'index.php';
                }
            });
        }

        const btn       = document.getElementById('navHamburger');
        const menu      = document.getElementById('mobileNavMenu');
        const iconOpen  = document.getElementById('navIconMenu');
        const iconClose = document.getElementById('navIconClose');
        if (btn && menu) {
            btn.addEventListener('click', function () {
                const open = menu.classList.toggle('open');
                btn.setAttribute('aria-expanded', open);
                if (iconOpen)  iconOpen.style.display  = open ? 'none'  : 'block';
                if (iconClose) iconClose.style.display = open ? 'block' : 'none';
            });
            menu.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () {
                    menu.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                    if (iconOpen)  iconOpen.style.display  = 'block';
                    if (iconClose) iconClose.style.display = 'none';
                });
            });
        }
    })();
    </script>
</body>
</html>
