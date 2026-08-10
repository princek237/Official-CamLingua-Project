<?php
$pageTitle  = 'Privacy Policy – CamLingua';
$activePage = '';
include 'includes/header.php';
?>

<div class="page-wrapper" style="max-width:1200px;margin:2rem auto;padding:0 1.5rem;display:grid;grid-template-columns:220px 1fr 260px;gap:28px;align-items:start;">

    <!-- Left sidebar -->
    <aside style="position:sticky;top:80px;display:flex;flex-direction:column;gap:24px;">
        <nav style="background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.07);overflow:hidden;">
            <a href="privacy.php" style="display:flex;align-items:center;gap:10px;padding:14px 18px;font-size:14px;font-weight:600;color:#15803d;border-left:3px solid #15803d;background:#f0fdf4;text-decoration:none;" aria-current="page">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Privacy Policy
            </a>
            <a href="terms-of-service.php" style="display:flex;align-items:center;gap:10px;padding:14px 18px;font-size:14px;font-weight:500;color:#374151;border-left:3px solid transparent;text-decoration:none;transition:background .15s,color .15s;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Terms of Service
            </a>
            <a href="#" style="display:flex;align-items:center;gap:10px;padding:14px 18px;font-size:14px;font-weight:500;color:#374151;border-left:3px solid transparent;text-decoration:none;transition:background .15s,color .15s;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Data Protection
            </a>
        </nav>

        <div style="background:#f0fdf4;border-radius:12px;padding:24px;text-align:center;">
            <p style="font-size:1rem;font-weight:700;color:#111827;margin-bottom:8px;">Your privacy matters</p>
            <p style="font-size:0.875rem;color:#4b5563;line-height:1.5;margin-bottom:20px;">We are committed to protecting your data and your rights.</p>
            <div style="width:48px;height:48px;background:#15803d;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                <svg width="24" height="24" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>
    </aside>

    <!-- Main content -->
    <main style="background:#fff;border-radius:16px;padding:40px;box-shadow:0 1px 6px rgba(0,0,0,.08);">
        <h1 style="font-size:1.625rem;font-weight:800;color:#111827;margin-bottom:4px;">Privacy Policy</h1>
        <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:1.5rem;">Last updated: July 24, 2025</p>
        <p style="font-size:0.875rem;color:#4b5563;line-height:1.7;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid #f3f4f6;">
            CamLingua ("we", "our", "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, share, and protect your information when you use our language translation services.
        </p>

        <?php
        $sections = [
            ['Information We Collect', 'We collect information you provide directly, such as your name, email address, and account details. We also collect translation data, usage statistics, and device information automatically when you use our platform.', '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
            ['How We Use Your Information', 'We use your information to provide and improve our services, personalize your experience, communicate with you, and ensure the security of our platform.', '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'],
            ['Data Sharing', 'We do not sell your personal information. We may share data with trusted service providers who help us operate our services, under strict confidentiality obligations.', '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>'],
            ['Data Security', 'We use industry-standard security measures to protect your data. However, no method of transmission over the internet is 100% secure. We continuously review our systems to protect your information.', '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>'],
            ['Your Rights', 'You have the right to access, update, or delete your personal information at any time. You can also opt out of certain communications via your account settings.', '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'],
        ];
        foreach ($sections as $i => $s):
        ?>
        <section style="margin-bottom:2rem;" aria-labelledby="s<?= $i ?>">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px;">
                <div style="width:28px;height:28px;min-width:28px;border-radius:50%;background:#f0fdf4;color:#15803d;display:flex;align-items:center;justify-content:center;"><?= $s[2] ?></div>
                <h2 id="s<?= $i ?>" style="font-size:1rem;font-weight:700;color:#111827;"><?= $i + 1 ?>. <?= htmlspecialchars($s[0]) ?></h2>
            </div>
            <div style="padding-left:42px;font-size:0.875rem;color:#4b5563;line-height:1.75;"><?= $s[1] ?></div>
        </section>
        <?php endforeach; ?>

        <p style="margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;font-size:0.875rem;color:#6b7280;">
            For any privacy-related questions, contact us at <a href="mailto:privacy@camlingua.com" style="color:#15803d;font-weight:600;">privacy@camlingua.com</a>
        </p>
    </main>

    <!-- Right panel -->
    <aside style="position:sticky;top:80px;">
        <div style="background:#fff;border-radius:16px;box-shadow:0 1px 6px rgba(0,0,0,.08);padding:24px;">
            <p style="font-size:1rem;font-weight:700;color:#111827;margin-bottom:4px;">At a glance</p>
            <p style="font-size:0.8125rem;color:#6b7280;margin-bottom:18px;">Key privacy commitments</p>
            
            <?php 
            $points = [
                ['We collect <strong>only what we need</strong>'],
                ['We use data to <strong>improve your experience</strong>'],
                ['We protect your data with <strong>best practices</strong>'],
                ['You are in <strong>control of your information</strong>']
            ];
            foreach ($points as $pt): 
            ?>
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:16px;">
                <div style="width:20px;height:20px;min-width:20px;border-radius:50%;border:1.5px solid #d1fae5;color:#10b981;display:flex;align-items:center;justify-content:center;margin-top:2px;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span style="font-size:0.8125rem;color:#374151;line-height:1.5;"><?= $pt[0] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </aside>

</div>

<style>
@media(max-width:1024px){.page-wrapper{grid-template-columns:1fr!important;}}
</style>

<?php include 'includes/footer.php'; ?>
