<?php
$pageTitle  = 'Terms of Service – CamLingua';
$activePage = '';
include 'includes/header.php';
?>

<div class="page-wrapper" style="max-width:1200px;margin:2rem auto;padding:0 1.5rem;display:grid;grid-template-columns:220px 1fr 260px;gap:28px;align-items:start;">

    <!-- Left sidebar -->
    <aside style="position:sticky;top:80px;">
        <nav style="background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;">
            <a href="privacy.php" style="display:flex;align-items:center;gap:10px;padding:14px 18px;font-size:14px;font-weight:500;color:#374151;border-left:3px solid transparent;text-decoration:none;transition:background .15s,color .15s;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Privacy Policy
            </a>
            <a href="terms-of-service.php" style="display:flex;align-items:center;gap:10px;padding:14px 18px;font-size:14px;font-weight:600;color:#15803d;border-left:3px solid #15803d;background:#f0fdf4;text-decoration:none;" aria-current="page">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Terms of Service
            </a>
        </nav>
    </aside>

    <!-- Main content -->
    <main style="background:#fff;border-radius:16px;padding:40px;box-shadow:0 1px 6px rgba(0,0,0,.08);">
        <h1 style="font-size:1.625rem;font-weight:800;color:#111827;margin-bottom:4px;">Terms of Service</h1>
        <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:1.5rem;">Last updated: July 24, 2025</p>
        <p style="font-size:0.875rem;color:#4b5563;line-height:1.7;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid #f3f4f6;">
            Welcome to CamLingua. By accessing or using our services, you agree to be bound by these Terms of Service.
            Please read them carefully before using our translation platform.
        </p>

        <?php
        $sections = [
            ['Acceptance of Terms',      'By creating an account or using CamLingua, you agree to these Terms of Service and our Privacy Policy. If you do not agree, please do not use our services.'],
            ['Use of Our Services',       'You agree to use CamLingua only for lawful purposes. You must not misuse our services or interfere with the security or functionality of the platform. Automated scraping, reverse engineering, or bulk data extraction without written consent is strictly prohibited.'],
            ['User Accounts',             'You are responsible for maintaining the confidentiality of your account and password and for all activities that occur under your account. Notify us immediately at <a href="mailto:support@camlingua.com" style="color:#15803d;font-weight:600;">support@camlingua.com</a> of any unauthorized use.'],
            ['Intellectual Property',     'All content, features, and functionalities on CamLingua are owned by us and are protected by copyright, trademark, and other laws. You may not reproduce, distribute, or create derivative works without our express written permission.'],
            ['Limitation of Liability',   'CamLingua is provided "as is." We are not liable for any indirect, incidental, or consequential damages arising from the use of our services. While we strive for translation accuracy, we do not guarantee error-free translations and recommend verification for critical communications.'],
            ['Changes to Terms',          'We may update these Terms of Service from time to time. Continued use of the platform means you accept the updated terms. We will notify registered users of significant changes via email at least 14 days in advance.'],
        ];
        foreach ($sections as $i => $s):
        ?>
        <section style="margin-bottom:2rem;" aria-labelledby="s<?= $i ?>">
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:10px;">
                <div style="width:28px;height:28px;min-width:28px;border-radius:50%;background:#f0fdf4;color:#15803d;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;margin-top:2px;"><?= $i + 1 ?></div>
                <h2 id="s<?= $i ?>" style="font-size:1rem;font-weight:700;color:#111827;"><?= htmlspecialchars($s[0]) ?></h2>
            </div>
            <div style="padding-left:42px;font-size:0.875rem;color:#4b5563;line-height:1.75;"><?= $s[1] ?></div>
        </section>
        <?php if ($i < count($sections) - 1): ?><hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 2rem;"><?php endif; ?>
        <?php endforeach; ?>

        <p style="margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;font-size:0.875rem;color:#6b7280;">
            If you have any questions, contact us at <a href="mailto:legal@camlingua.com" style="color:#15803d;font-weight:600;">legal@camlingua.com</a>
        </p>
    </main>

    <!-- Right panel -->
    <aside style="position:sticky;top:80px;">
        <div style="background:#15803d;border-radius:16px;padding:28px 24px;color:#fff;text-align:center;margin-bottom:20px;">
            <div style="width:64px;height:64px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="32" height="32" fill="none" stroke="#fff" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3L22 4"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#bbf7d0;margin-bottom:8px;">Our Commitment</p>
            <p style="font-size:1.125rem;font-weight:800;line-height:1.3;margin-bottom:12px;">Fair Use,<br>Better Experience</p>
            <p style="font-size:0.8125rem;color:#d1fae5;line-height:1.6;">By using CamLingua, you agree to our terms and help keep our community safe and respectful.</p>
        </div>
        <div style="background:#fff;border-radius:16px;box-shadow:0 1px 6px rgba(0,0,0,.08);padding:24px;">
            <h3 style="font-size:0.9375rem;font-weight:700;color:#111827;margin-bottom:18px;">Key Points</h3>
            <?php foreach (['Use responsibly','Respect others','Keep your account secure','Follow the law',"We're here to help"] as $pt): ?>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                <div style="width:20px;height:20px;min-width:20px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <svg width="11" height="11" fill="none" stroke="#15803d" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span style="font-size:0.8125rem;color:#374151;font-weight:500;"><?= htmlspecialchars($pt) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </aside>

</div>

<style>
@media(max-width:1024px){.page-wrapper{grid-template-columns:1fr!important;}}
</style>

<?php include 'includes/footer.php'; ?>
