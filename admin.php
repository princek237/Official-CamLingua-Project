<?php
$pageTitle  = 'CamLingua – Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="CamLingua Admin Dashboard – manage users, languages, translations, subscriptions and settings.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="assets/css/shared.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<!-- Content hidden until admin role confirmed by JS -->
<div id="admin-wrap" style="display:none;">
    <div class="admin-layout">
        
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <img src="assets/logo.png" alt="CamLingua" class="logo-img" style="height:36px;">
                <span class="logo-text">Cam<span>Lingua</span> <span style="font-size:0.75rem; font-weight:500; color:#4ade80;">Admin</span></span>
            </div>
            <nav class="admin-nav">
                <a href="#" class="admin-nav-item active" data-target="dashboard" id="nav-dashboard">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="#" class="admin-nav-item" data-target="users" id="nav-users">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Users
                </a>
                <a href="#" class="admin-nav-item" data-target="languages" id="nav-languages">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    Languages
                </a>
                <a href="#" class="admin-nav-item" data-target="translations" id="nav-translations">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Translations
                </a>
                <a href="#" class="admin-nav-item" data-target="history" id="nav-history">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    History
                </a>
                <a href="#" class="admin-nav-item" data-target="subscriptions" id="nav-subscriptions">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Subscriptions
                </a>
                <a href="#" class="admin-nav-item" data-target="reports" id="nav-reports">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Reports
                </a>
                <div class="admin-nav-spacer"></div>
                <a href="#" class="admin-nav-item" data-target="settings" id="nav-settings">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </a>
                <a href="#" id="btn-logout" class="admin-nav-item">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Header -->
            <header class="admin-header">
                <nav class="admin-top-nav">
                    <a href="index.php" target="_blank" rel="noopener">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        View Website
                    </a>
                </nav>
                <a href="profile.php" class="admin-profile">
                    <div class="admin-profile-avatar" id="admin-avatar-initials">A</div>
                    <span class="admin-profile-name" id="admin-profile-name">Admin</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
            </header>

            <!-- Toast notification -->
            <div id="toast-container" aria-live="polite"></div>

            <!-- Dashboard Content -->
            <div class="admin-content">
                
                <!-- ===== DASHBOARD SECTION ===== -->
                <div id="section-dashboard" class="admin-section">
                    <h1 class="admin-page-title">Dashboard</h1>

                    <!-- STAT CARDS -->
                    <section class="stats-grid" aria-label="Summary statistics">
                        <article class="stat-card">
                            <div class="stat-card-top">
                                <span class="stat-label">Total Users</span>
                                <div class="stat-icon blue">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                            </div>
                            <p class="stat-value" id="stat-users">—</p>
                            <p class="stat-trend">Registered accounts</p>
                        </article>
                        <article class="stat-card">
                            <div class="stat-card-top">
                                <span class="stat-label">Total Translations</span>
                                <div class="stat-icon green">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </div>
                            </div>
                            <p class="stat-value" id="stat-translations">—</p>
                            <p class="stat-trend">All time requests</p>
                        </article>
                        <article class="stat-card">
                            <div class="stat-card-top">
                                <span class="stat-label">Languages</span>
                                <div class="stat-icon purple">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                                </div>
                            </div>
                            <p class="stat-value" id="stat-languages">—</p>
                            <p class="stat-trend">Supported languages</p>
                        </article>
                        <article class="stat-card">
                            <div class="stat-card-top">
                                <span class="stat-label">Active Subscriptions</span>
                                <div class="stat-icon yellow">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <p class="stat-value" id="stat-revenue">—</p>
                            <p class="stat-trend">Active subscribers</p>
                        </article>
                    </section>

                    <!-- RECENT TRANSLATIONS TABLE -->
                    <section class="activity-grid" aria-label="Recent activity" style="margin-bottom: 32px;">
                        <article class="data-card">
                            <div class="data-card-header">
                                <p class="data-card-title">Recent Translations</p>
                                <button class="btn-view-all" onclick="document.querySelector('[data-target=translations]').click()">View all</button>
                            </div>
                            <div class="table-scroll">
                                <table aria-label="Recent translations table">
                                    <thead>
                                        <tr><th>ID</th><th>From</th><th>To</th><th>Text Preview</th><th>User</th><th>Date</th></tr>
                                    </thead>
                                    <tbody id="translations-table-body"><tr><td colspan="6" class="td-loading">Loading…</td></tr></tbody>
                                </table>
                            </div>
                        </article>
                    </section>

                    <!-- CHARTS -->
                    <section class="charts-grid" aria-label="Analytics charts">
                        <article class="chart-card">
                            <div class="chart-header">
                                <div>
                                    <p class="chart-title">Translations (This Month)</p>
                                    <p class="chart-subtitle">Daily translation volume</p>
                                </div>
                            </div>
                            <div class="chart-canvas-wrap">
                                <canvas id="translationsLineChart" aria-label="Translations line chart"></canvas>
                            </div>
                        </article>
                        <article class="chart-card">
                            <div style="margin-bottom:16px;">
                                <p class="chart-title">Top Languages</p>
                                <p class="chart-subtitle">By translation volume</p>
                            </div>
                            <div class="donut-wrap">
                                <canvas id="languagesDonutChart" aria-label="Top languages donut chart"></canvas>
                            </div>
                            <ul id="donut-legend"></ul>
                        </article>
                    </section>
                </div>

                <!-- ===== USERS SECTION ===== -->
                <div id="section-users" class="admin-section" style="display:none;">
                    <div class="section-header">
                        <h1 class="admin-page-title">Users</h1>
                        <button class="btn-primary" id="btn-add-user" onclick="openModal('user')">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add User
                        </button>
                    </div>
                    <article class="data-card">
                        <div class="table-toolbar">
                            <div class="toolbar-left">
                                <div class="search-input-wrap">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input type="text" id="users-search" placeholder="Search users…" oninput="debounce(loadUsers, 350)()">
                                </div>
                                <select id="users-status-filter" onchange="loadUsers()">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="banned">Banned</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-scroll">
                            <table aria-label="Users table">
                                <thead>
                                    <tr><th>ID</th><th>Username</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
                                </thead>
                                <tbody id="full-users-table-body"><tr><td colspan="8" class="td-loading">Loading…</td></tr></tbody>
                            </table>
                        </div>
                        <div id="users-pagination" class="pagination-bar"></div>
                    </article>
                </div>

                <!-- ===== LANGUAGES SECTION ===== -->
                <div id="section-languages" class="admin-section" style="display:none;">
                    <div class="section-header">
                        <h1 class="admin-page-title">Languages</h1>
                        <button class="btn-primary" onclick="openModal('language')">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add Language
                        </button>
                    </div>
                    <article class="data-card">
                        <div class="table-toolbar">
                            <div class="toolbar-left">
                                <div class="search-input-wrap">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input type="text" id="languages-search" placeholder="Search languages…" oninput="debounce(loadLanguages, 350)()">
                                </div>
                            </div>
                        </div>
                        <div class="table-scroll">
                            <table aria-label="Languages table">
                                <thead>
                                    <tr><th>ID</th><th>Name</th><th>Code</th><th>Translations</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                                <tbody id="full-languages-table-body"><tr><td colspan="6" class="td-loading">Loading…</td></tr></tbody>
                            </table>
                        </div>
                        <div id="languages-pagination" class="pagination-bar"></div>
                    </article>
                </div>

                <!-- ===== TRANSLATIONS SECTION ===== -->
                <div id="section-translations" class="admin-section" style="display:none;">
                    <div class="section-header">
                        <h1 class="admin-page-title">Translations</h1>
                    </div>
                    <article class="data-card">
                        <div class="table-toolbar">
                            <div class="toolbar-left">
                                <div class="search-input-wrap">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input type="text" id="translations-search" placeholder="Search text…" oninput="debounce(loadTranslations, 350)()">
                                </div>
                                <select id="translations-lang-filter" onchange="loadTranslations()">
                                    <option value="">All Languages</option>
                                </select>
                                <input type="date" id="translations-date-filter" onchange="loadTranslations()" class="filter-date">
                            </div>
                        </div>
                        <div class="table-scroll">
                            <table aria-label="Translations table">
                                <thead>
                                    <tr><th>ID</th><th>From</th><th>To</th><th>Source Text</th><th>Translated Text</th><th>Status</th><th>User</th><th>Date</th><th>Actions</th></tr>
                                </thead>
                                <tbody id="full-translations-table-body"><tr><td colspan="9" class="td-loading">Loading…</td></tr></tbody>
                            </table>
                        </div>
                        <div id="translations-pagination" class="pagination-bar"></div>
                    </article>
                </div>

                <!-- ===== HISTORY SECTION ===== -->
                <div id="section-history" class="admin-section" style="display:none;">
                    <div class="section-header">
                        <h1 class="admin-page-title">Translation History</h1>
                    </div>
                    <article class="data-card">
                        <div class="table-toolbar">
                            <div class="toolbar-left">
                                <div class="search-input-wrap">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input type="text" id="history-search" placeholder="Search text…" oninput="debounce(loadHistory, 350)()">
                                </div>
                                <select id="history-lang-filter" onchange="loadHistory()">
                                    <option value="">All Languages</option>
                                </select>
                                <input type="date" id="history-date-filter" onchange="loadHistory()" class="filter-date">
                            </div>
                        </div>
                        <div class="table-scroll">
                            <table aria-label="Translation history table">
                                <thead>
                                    <tr><th>ID</th><th>From</th><th>To</th><th>Source Text</th><th>Translated Text</th><th>Status</th><th>User</th><th>Date</th><th>Actions</th></tr>
                                </thead>
                                <tbody id="full-history-table-body"><tr><td colspan="9" class="td-loading">Loading…</td></tr></tbody>
                            </table>
                        </div>
                        <div id="history-pagination" class="pagination-bar"></div>
                    </article>
                </div>

                <!-- ===== SUBSCRIPTIONS SECTION ===== -->
                <div id="section-subscriptions" class="admin-section" style="display:none;">
                    <div class="section-header">
                        <h1 class="admin-page-title">Subscription Plans</h1>
                        <button class="btn-primary" onclick="openModal('subscription')">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            New Plan
                        </button>
                    </div>
                    <article class="data-card">
                        <div class="table-scroll">
                            <table aria-label="Subscription plans table">
                                <thead>
                                    <tr><th>ID</th><th>Name</th><th>Slug</th><th>Monthly Price</th><th>Yearly Price</th><th>Subscribers</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                                <tbody id="full-subscriptions-table-body"><tr><td colspan="8" class="td-loading">Loading…</td></tr></tbody>
                            </table>
                        </div>
                        <div id="subscriptions-pagination" class="pagination-bar"></div>
                    </article>
                </div>

                <!-- ===== REPORTS SECTION ===== -->
                <div id="section-reports" class="admin-section" style="display:none;">
                    <div class="section-header">
                        <h1 class="admin-page-title">Reports &amp; Messages</h1>
                    </div>
                    <article class="data-card">
                        <div class="table-scroll">
                            <table aria-label="Reports table">
                                <thead>
                                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Status</th><th>Date</th></tr>
                                </thead>
                                <tbody id="full-reports-table-body"><tr><td colspan="7" class="td-loading">Loading…</td></tr></tbody>
                            </table>
                        </div>
                        <div id="reports-pagination" class="pagination-bar"></div>
                    </article>
                </div>

                <!-- ===== SETTINGS SECTION ===== -->
                <div id="section-settings" class="admin-section" style="display:none;">
                    <div class="section-header">
                        <h1 class="admin-page-title">Settings</h1>
                    </div>
                    <article class="data-card" style="max-width:640px;">
                        <form id="settings-form" onsubmit="saveSettings(event)">
                            <div class="form-group">
                                <label for="setting-site_name">Site Name</label>
                                <input type="text" id="setting-site_name" name="site_name" placeholder="CamLingua">
                                <p class="form-hint">The public name of your platform.</p>
                            </div>
                            <div class="form-group">
                                <label for="setting-contact_email">Contact Email</label>
                                <input type="email" id="setting-contact_email" name="contact_email" placeholder="support@camlingua.com">
                                <p class="form-hint">Email address for contact form submissions.</p>
                            </div>
                            <div class="form-group">
                                <label for="setting-default_language">Default Language</label>
                                <select id="setting-default_language" name="default_language">
                                    <option value="en">English</option>
                                    <option value="fr">French</option>
                                </select>
                                <p class="form-hint">Default interface language for the platform.</p>
                            </div>
                            <div class="form-group">
                                <label for="setting-translation_api_provider">Translation API Provider</label>
                                <input type="text" id="setting-translation_api_provider" name="translation_api_provider" placeholder="nllb">
                                <p class="form-hint">The translation engine used for processing requests.</p>
                            </div>
                            <div class="form-group">
                                <label for="setting-platform_logo">Platform Logo URL</label>
                                <input type="text" id="setting-platform_logo" name="platform_logo" placeholder="https://…">
                                <p class="form-hint">Full URL to the platform logo image.</p>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-primary" id="settings-save-btn">Save Settings</button>
                            </div>
                        </form>
                    </article>
                </div>

            </div><!-- /admin-content -->
        </main>
    </div>
</div><!-- /admin-wrap -->

<!-- ===== MODALS ===== -->

<!-- User Modal -->
<div id="modal-user" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-user-title" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h2 class="modal-title" id="modal-user-title">Add User</h2>
            <button class="modal-close" onclick="closeModal('user')" aria-label="Close">&times;</button>
        </div>
        <form id="user-form" onsubmit="submitUserForm(event)" novalidate>
            <input type="hidden" id="user-id">
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="user-username">Username <span class="required">*</span></label>
                    <input type="text" id="user-username" required minlength="3">
                </div>
                <div class="form-group">
                    <label for="user-email">Email <span class="required">*</span></label>
                    <input type="email" id="user-email" required>
                </div>
                <div class="form-group">
                    <label for="user-full_name">Full Name</label>
                    <input type="text" id="user-full_name">
                </div>
                <div class="form-group">
                    <label for="user-phone_number">Phone Number</label>
                    <input type="tel" id="user-phone_number">
                </div>
                <div class="form-group">
                    <label for="user-role">Role <span class="required">*</span></label>
                    <select id="user-role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="user-status">Status <span class="required">*</span></label>
                    <select id="user-status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="banned">Banned</option>
                    </select>
                </div>
                <div class="form-group form-col-2">
                    <label for="user-password">Password <span class="required" id="user-password-required">*</span></label>
                    <input type="password" id="user-password" placeholder="Leave blank to keep existing">
                    <p class="form-hint" id="user-password-hint" style="display:none;">Leave blank to keep the existing password.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('user')">Cancel</button>
                <button type="submit" class="btn-primary" id="user-submit-btn">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- Language Modal -->
<div id="modal-language" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-language-title" style="display:none;">
    <div class="modal-box modal-box-sm">
        <div class="modal-header">
            <h2 class="modal-title" id="modal-language-title">Add Language</h2>
            <button class="modal-close" onclick="closeModal('language')" aria-label="Close">&times;</button>
        </div>
        <form id="language-form" onsubmit="submitLanguageForm(event)" novalidate>
            <input type="hidden" id="language-id">
            <div class="form-group">
                <label for="language-name">Language Name <span class="required">*</span></label>
                <input type="text" id="language-name" required placeholder="e.g. French">
            </div>
            <div class="form-group">
                <label for="language-code">Language Code <span class="required">*</span></label>
                <input type="text" id="language-code" required placeholder="e.g. fr" maxlength="10">
                <p class="form-hint">ISO 639 code (e.g. en, fr, ewo)</p>
            </div>
            <div class="form-group">
                <label for="language-is_active">Status</label>
                <select id="language-is_active">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('language')">Cancel</button>
                <button type="submit" class="btn-primary" id="language-submit-btn">Create Language</button>
            </div>
        </form>
    </div>
</div>

<!-- Subscription Modal -->
<div id="modal-subscription" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-sub-title" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h2 class="modal-title" id="modal-sub-title">Add Plan</h2>
            <button class="modal-close" onclick="closeModal('subscription')" aria-label="Close">&times;</button>
        </div>
        <form id="subscription-form" onsubmit="submitSubscriptionForm(event)" novalidate>
            <input type="hidden" id="subscription-id">
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="sub-name">Plan Name <span class="required">*</span></label>
                    <input type="text" id="sub-name" required placeholder="e.g. Pro">
                </div>
                <div class="form-group">
                    <label for="sub-slug">Slug <span class="required">*</span></label>
                    <input type="text" id="sub-slug" required placeholder="e.g. pro">
                </div>
                <div class="form-group">
                    <label for="sub-price-monthly">Monthly Price (XAF) <span class="required">*</span></label>
                    <input type="number" id="sub-price-monthly" required min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="sub-price-yearly">Yearly Price (XAF) <span class="required">*</span></label>
                    <input type="number" id="sub-price-yearly" required min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="sub-is-active">Status</label>
                    <select id="sub-is-active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-group form-col-2">
                    <label for="sub-description">Description</label>
                    <textarea id="sub-description" rows="3" placeholder="Plan description…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('subscription')">Cancel</button>
                <button type="submit" class="btn-primary" id="subscription-submit-btn">Create Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Delete Dialog -->
<div id="modal-confirm" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="confirm-title" style="display:none;">
    <div class="modal-box modal-box-sm">
        <div class="modal-header">
            <h2 class="modal-title" id="confirm-title">Confirm Delete</h2>
        </div>
        <p id="confirm-message" style="color:#374151;margin:0 0 24px;">Are you sure you want to delete this item? This action cannot be undone.</p>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeConfirm()">Cancel</button>
            <button type="button" class="btn-danger" id="confirm-ok-btn">Delete</button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="assets/js/api.js"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>
