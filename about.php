<?php
require_once 'includes/cms_helper.php';
$pageTitle  = 'About – ' . cms('site_name');
$extraCss   = ['about.css'];
$activePage = 'about';
$extraJs    = ['about.js'];
include 'includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="container">
        <div class="hero-grid">
            <div class="reveal">
                <span class="badge-pill">Our Story</span>
                <h1 class="hero-title"><?= cms_nl2br('about_hero_title') ?></h1>
                <p class="hero-desc"><?= cms('about_hero_desc') ?></p>
                <div class="hero-actions">
                    <a href="#mission" class="btn-primary">Our Mission</a>
                    <a href="#team"    class="btn-outline">Meet the Team</a>
                </div>
            </div>
            <div class="reveal reveal-d1">
                <div class="hero-img-wrap">
                    <div class="hero-img-card">
                        <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=600&q=80" alt="Diverse Cameroonian community" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="hero-img-fallback"><svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/></svg></div>
                    </div>
                    <div class="float-badge badge-langs">
                        <div class="badge-icon green"><svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10"/></svg></div>
                        <div class="badge-meta"><p>Languages</p><p><?= cms('about_stats_langs') ?></p></div>
                    </div>
                    <div class="float-badge badge-trans delay">
                        <div class="badge-icon yellow"><svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <div class="badge-meta"><p>Translations</p><p><?= cms('about_stats_trans') ?></p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- OUR STORY -->
<section class="section white">
    <div class="container">
        <div class="two-col">
            <div class="story-text reveal">
                <span class="badge-pill">How We Started</span>
                <h2 class="section-title" style="text-align:left;margin-bottom:1.25rem;">Born from a passion for <span class="green">cultural preservation</span></h2>
                <p><?= cms('about_story_p1') ?></p>
                <p><?= cms('about_story_p2') ?></p>
                <p><?= cms('about_story_p3') ?></p>
            </div>
            <div class="reveal reveal-d1">
                <div class="timeline">
                    <div class="tl-item"><div class="tl-dot"></div><p class="tl-year">2024</p><h3 class="tl-title">The Idea Sparks</h3><p class="tl-desc">A group of students at a Cameroonian university identify the gap in digital language tools for local languages.</p></div>
                    <div class="tl-item"><div class="tl-dot"></div><p class="tl-year">Early 2025</p><h3 class="tl-title">First Prototype</h3><p class="tl-desc">First working prototype translating between English and Ewondo using the NLLB-200 model.</p></div>
                    <div class="tl-item"><div class="tl-dot"></div><p class="tl-year">Late 2025</p><h3 class="tl-title">Community Dictionary</h3><p class="tl-desc">Launched the community contribution feature allowing native speakers to suggest and verify translations.</p></div>
                    <div class="tl-item"><div class="tl-dot active"></div><p class="tl-year">2026 – Today</p><h3 class="tl-title">Full Platform Launch</h3><p class="tl-desc">CamLingua goes live with support for 20+ languages, a web app, subscription plans, and 50K+ happy users.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MISSION / VISION / VALUES -->
<section id="mission" class="section gray">
    <div class="container">
        <div class="section-header reveal">
            <span class="badge-pill">What Drives Us</span>
            <h2 class="section-title">Our Mission, Vision &amp; Values</h2>
            <p class="section-desc">Three pillars that guide everything we build at <?= cms('site_name') ?>.</p>
        </div>
        <div class="mvv-grid">
            <div class="mvv-card reveal">
                <div class="mvv-icon green"><svg viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
                <div>
                    <h3 class="mvv-title"><?= cms('about_mission_title') ?></h3>
                    <p class="mvv-body"><?= cms('about_mission_text') ?></p>
                </div>
                <div class="mvv-footer"><p class="mvv-tags">Accessibility · Accuracy · Inclusion</p></div>
            </div>
            <div class="mvv-card dark reveal reveal-d1">
                <div class="mvv-icon glass"><svg viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></div>
                <div>
                    <h3 class="mvv-title"><?= cms('about_vision_title') ?></h3>
                    <p class="mvv-body"><?= cms('about_vision_text') ?></p>
                </div>
                <div class="mvv-footer"><p class="mvv-tags">Preservation · Future · Unity</p></div>
            </div>
            <div class="mvv-card reveal reveal-d2">
                <div class="mvv-icon green"><svg viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                <div><h3 class="mvv-title">Our Values</h3><ul class="values-list"><li class="value-item"><span class="value-dot"></span><span><strong>Integrity</strong> — Honest, transparent AI with community oversight.</span></li><li class="value-item"><span class="value-dot"></span><span><strong>Accuracy</strong> — Constantly improving quality through community feedback.</span></li><li class="value-item"><span class="value-dot"></span><span><strong>Privacy</strong> — User data is encrypted and never sold.</span></li><li class="value-item"><span class="value-dot"></span><span><strong>Culture</strong> — Languages carry identity; we treat them with respect.</span></li></ul></div>
            </div>
        </div>
    </div>
</section>

<!-- LANGUAGES TEASER -->
<section class="section white">
    <div class="container">
        <div class="section-header reveal">
            <span class="badge-pill">Languages We Support</span>
            <h2 class="section-title">Bridging Cameroon's linguistic diversity</h2>
            <p class="section-desc">From the Adamawa highlands to the coastal cities, <?= cms('site_name') ?> speaks the languages of Cameroon.</p>
        </div>
        <div class="lang-grid reveal">
            <div class="lang-pill"><span class="lang-flag">🇨🇲</span><span class="lang-name">Ewondo</span><span class="lang-sub">Cameroon</span></div>
            <div class="lang-pill"><span class="lang-flag">🇨🇲</span><span class="lang-name">Bassa</span><span class="lang-sub">Cameroon</span></div>
            <div class="lang-pill"><span class="lang-flag">🇨🇲</span><span class="lang-name">Duala</span><span class="lang-sub">Cameroon</span></div>
            <div class="lang-pill"><span class="lang-flag">🇨🇲</span><span class="lang-name">Bamileke</span><span class="lang-sub">Cameroon</span></div>
            <div class="lang-pill"><span class="lang-flag">🇨🇲</span><span class="lang-name">Fulfulde</span><span class="lang-sub">Cameroon</span></div>
            <div class="lang-pill more"><span class="lang-flag">+15</span><span class="lang-name">More</span><span class="lang-sub">Worldwide</span></div>
        </div>
    </div>
</section>

<!-- TEAM -->
<section id="team" class="section gray">
    <div class="container">
        <div class="section-header reveal">
            <span class="badge-pill">The People Behind <?= cms('site_name') ?></span>
            <h2 class="section-title">Meet our team</h2>
            <p class="section-desc">A passionate group of engineers, linguists, and designers united by a love for Cameroonian culture.</p>
        </div>
        <div class="team-grid">
            <div class="team-card reveal"><div class="team-avatar green">AM</div><p class="team-name">Adama Moussa</p><p class="team-role">Team Lead &amp; Backend Engineer</p><p class="team-bio">Architect of the PHP MVC backend and database design. Expert in Fulfulde and Hausa linguistics.</p><div class="team-socials"><a href="#" class="team-social" aria-label="GitHub"><svg viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg></a></div></div>
            <div class="team-card reveal reveal-d1"><div class="team-avatar blue">EN</div><p class="team-name">Esther Ngo</p><p class="team-role">UI/UX &amp; Frontend Developer</p><p class="team-bio">Designed the CamLingua experience from scratch. Passionate about accessible design and Bamileke heritage.</p><div class="team-socials"><a href="#" class="team-social" aria-label="GitHub"><svg viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg></a></div></div>
            <div class="team-card reveal reveal-d2"><div class="team-avatar yellow">KB</div><p class="team-name">Kodjo Beyala</p><p class="team-role">AI &amp; NLP Engineer</p><p class="team-bio">Integrated NLLB-200 and fine-tuned translation models for Cameroonian languages. Native Ewondo speaker.</p><div class="team-socials"><a href="#" class="team-social" aria-label="GitHub"><svg viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg></a></div></div>
        </div>
    </div>
</section>

<!-- STATS BANNER -->
<section id="stats" class="stats-section">
    <div class="map-silhouette" aria-hidden="true"></div>
    <div class="container">
        <div class="stats-heading reveal"><h2><?= cms('site_name') ?> by the numbers</h2><p>Real impact across Cameroon and beyond.</p></div>
        <div class="stats-grid">
            <div class="stat-card reveal"><p class="stat-number"><?= cms('about_stats_langs') ?></p><p class="stat-label">Languages Supported</p></div>
            <div class="stat-card reveal reveal-d1"><p class="stat-number"><?= cms('about_stats_trans') ?></p><p class="stat-label">Translations Completed</p></div>
            <div class="stat-card reveal reveal-d2"><p class="stat-number"><?= cms('about_stats_users') ?></p><p class="stat-label">Happy Users</p></div>
            <div class="stat-card reveal reveal-d3"><p class="stat-number">100%</p><p class="stat-label">Made in Cameroon</p></div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section white">
    <div class="container">
        <div class="cta-box reveal">
            <span class="badge-pill">Join Us</span>
            <h2 class="cta-title"><?= cms('about_cta_title') ?></h2>
            <p class="cta-desc"><?= cms('about_cta_desc') ?></p>
            <div class="cta-actions">
                <a href="login.php#signup" class="btn-primary">Start Translating — It's Free</a>
                <a href="translator.php"   class="btn-outline">Contribute to Dictionary</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
