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
                <a href="#" class="admin-nav-item" data-target="content" id="nav-content">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Content
                </a>
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

                    <!-- ACTIVE LANGUAGES PANEL -->
                    <section aria-label="Active languages" style="margin-bottom: 32px;">
                        <article class="data-card">
                            <div class="data-card-header">
                                <p class="data-card-title">Active Languages</p>
                                <button class="btn-view-all" onclick="document.querySelector('[data-target=languages]').click()">Manage</button>
                            </div>
                            <div id="active-languages-list" class="active-languages-grid">
                                <p class="td-loading" style="padding:16px;color:#9ca3af;">Loading…</p>
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
                                <select id="languages-status-filter" onchange="loadLanguages()">
                                    <option value="">All Statuses</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
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

                <!-- ===== CONTENT MANAGEMENT SECTION ===== -->
                <div id="section-content" class="admin-section" style="display:none;">
                    <div class="section-header">
                        <h1 class="admin-page-title">Content Management</h1>
                        <a href="index.php" target="_blank" rel="noopener" class="btn-secondary" style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Preview Site
                        </a>
                    </div>

                    <!-- Tab bar -->
                    <div class="cms-tabs" role="tablist" aria-label="Content sections">
                        <button class="cms-tab active" role="tab" aria-selected="true"  aria-controls="cms-panel-homepage"     data-cms-tab="homepage"     onclick="switchCmsTab('homepage')">Homepage</button>
                        <button class="cms-tab"        role="tab" aria-selected="false" aria-controls="cms-panel-about"        data-cms-tab="about"        onclick="switchCmsTab('about')">About</button>
                        <button class="cms-tab"        role="tab" aria-selected="false" aria-controls="cms-panel-contact"      data-cms-tab="contact"      onclick="switchCmsTab('contact')">Contact</button>
                        <button class="cms-tab"        role="tab" aria-selected="false" aria-controls="cms-panel-pricing"      data-cms-tab="pricing"      onclick="switchCmsTab('pricing')">Pricing</button>
                        <button class="cms-tab"        role="tab" aria-selected="false" aria-controls="cms-panel-global"       data-cms-tab="global"       onclick="switchCmsTab('global')">Global / Brand</button>
                    </div>

                    <!-- ── Homepage panel ───────────────────────────────── -->
                    <div id="cms-panel-homepage" class="cms-panel" role="tabpanel">
                        <form class="cms-form" onsubmit="saveCmsPanel(event,'homepage')">
                            <div class="cms-form-grid">
                                <div class="form-group form-col-2">
                                    <label for="cms-home_hero_badge">Hero Badge Text</label>
                                    <input type="text" id="cms-home_hero_badge" name="home_hero_badge" placeholder="Cameroonian Language Translation System">
                                    <p class="form-hint">Small label above the main headline.</p>
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-home_hero_title">Hero Headline</label>
                                    <textarea id="cms-home_hero_title" name="home_hero_title" rows="3" placeholder="Translate. Connect.&#10;Preserve Cameroon's&#10;Languages."></textarea>
                                    <p class="form-hint">Main headline. Use a new line for each line break.</p>
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-home_hero_desc">Hero Description</label>
                                    <textarea id="cms-home_hero_desc" name="home_hero_desc" rows="2"></textarea>
                                    <p class="form-hint">Subtitle paragraph below the headline.</p>
                                </div>
                                <div class="form-group">
                                    <label for="cms-home_hero_btn1">Primary Button Label</label>
                                    <input type="text" id="cms-home_hero_btn1" name="home_hero_btn1" placeholder="Start Translating">
                                </div>
                                <div class="form-group">
                                    <label for="cms-home_hero_btn2">Secondary Button Label</label>
                                    <input type="text" id="cms-home_hero_btn2" name="home_hero_btn2" placeholder="Explore Languages">
                                </div>
                                <div class="form-group"><label for="cms-home_feat1_title">Feature 1 Title</label><input type="text" id="cms-home_feat1_title" name="home_feat1_title"></div>
                                <div class="form-group"><label for="cms-home_feat1_desc">Feature 1 Description</label><input type="text" id="cms-home_feat1_desc" name="home_feat1_desc"></div>
                                <div class="form-group"><label for="cms-home_feat2_title">Feature 2 Title</label><input type="text" id="cms-home_feat2_title" name="home_feat2_title"></div>
                                <div class="form-group"><label for="cms-home_feat2_desc">Feature 2 Description</label><input type="text" id="cms-home_feat2_desc" name="home_feat2_desc"></div>
                                <div class="form-group"><label for="cms-home_feat3_title">Feature 3 Title</label><input type="text" id="cms-home_feat3_title" name="home_feat3_title"></div>
                                <div class="form-group"><label for="cms-home_feat3_desc">Feature 3 Description</label><input type="text" id="cms-home_feat3_desc" name="home_feat3_desc"></div>
                                <div class="form-group"><label for="cms-home_feat4_title">Feature 4 Title</label><input type="text" id="cms-home_feat4_title" name="home_feat4_title"></div>
                                <div class="form-group"><label for="cms-home_feat4_desc">Feature 4 Description</label><input type="text" id="cms-home_feat4_desc" name="home_feat4_desc"></div>
                            </div>
                            <div class="form-actions"><button type="submit" class="btn-primary" id="cms-save-homepage">Save Homepage</button></div>
                        </form>
                    </div>

                    <!-- ── About panel ──────────────────────────────────── -->
                    <div id="cms-panel-about" class="cms-panel" style="display:none;" role="tabpanel">
                        <form class="cms-form" onsubmit="saveCmsPanel(event,'about')">
                            <div class="cms-form-grid">
                                <div class="form-group form-col-2">
                                    <label for="cms-about_hero_title">Hero Headline</label>
                                    <textarea id="cms-about_hero_title" name="about_hero_title" rows="2"></textarea>
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-about_hero_desc">Hero Subtitle</label>
                                    <textarea id="cms-about_hero_desc" name="about_hero_desc" rows="2"></textarea>
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-about_story_p1">Story Paragraph 1</label>
                                    <textarea id="cms-about_story_p1" name="about_story_p1" rows="3"></textarea>
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-about_story_p2">Story Paragraph 2</label>
                                    <textarea id="cms-about_story_p2" name="about_story_p2" rows="3"></textarea>
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-about_story_p3">Story Paragraph 3</label>
                                    <textarea id="cms-about_story_p3" name="about_story_p3" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="cms-about_mission_title">Mission Title</label>
                                    <input type="text" id="cms-about_mission_title" name="about_mission_title">
                                </div>
                                <div class="form-group">
                                    <label for="cms-about_mission_text">Mission Text</label>
                                    <textarea id="cms-about_mission_text" name="about_mission_text" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="cms-about_vision_title">Vision Title</label>
                                    <input type="text" id="cms-about_vision_title" name="about_vision_title">
                                </div>
                                <div class="form-group">
                                    <label for="cms-about_vision_text">Vision Text</label>
                                    <textarea id="cms-about_vision_text" name="about_vision_text" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="cms-about_stats_langs">Languages Stat</label>
                                    <input type="text" id="cms-about_stats_langs" name="about_stats_langs" placeholder="20+">
                                    <p class="form-hint">Displayed in the stats strip (e.g. "20+")</p>
                                </div>
                                <div class="form-group">
                                    <label for="cms-about_stats_trans">Translations Stat</label>
                                    <input type="text" id="cms-about_stats_trans" name="about_stats_trans" placeholder="50K+">
                                </div>
                                <div class="form-group">
                                    <label for="cms-about_stats_users">Users Stat</label>
                                    <input type="text" id="cms-about_stats_users" name="about_stats_users" placeholder="10K+">
                                </div>
                                <div class="form-group">
                                    <label for="cms-about_cta_title">CTA Heading</label>
                                    <input type="text" id="cms-about_cta_title" name="about_cta_title">
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-about_cta_desc">CTA Subtext</label>
                                    <textarea id="cms-about_cta_desc" name="about_cta_desc" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="form-actions"><button type="submit" class="btn-primary" id="cms-save-about">Save About Page</button></div>
                        </form>
                    </div>

                    <!-- ── Contact panel ────────────────────────────────── -->
                    <div id="cms-panel-contact" class="cms-panel" style="display:none;" role="tabpanel">
                        <form class="cms-form" onsubmit="saveCmsPanel(event,'contact')">
                            <div class="cms-form-grid">
                                <div class="form-group">
                                    <label for="cms-contact_hero_title">Page Heading</label>
                                    <input type="text" id="cms-contact_hero_title" name="contact_hero_title" placeholder="Get in touch">
                                </div>
                                <div class="form-group">
                                    <label for="cms-contact_hero_desc">Page Subtitle</label>
                                    <textarea id="cms-contact_hero_desc" name="contact_hero_desc" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="cms-contact_email">Contact Email</label>
                                    <input type="email" id="cms-contact_email" name="contact_email" placeholder="support@camlingua.com">
                                    <p class="form-hint">Shown on the contact page and used for form submissions.</p>
                                </div>
                                <div class="form-group">
                                    <label for="cms-contact_phone">Phone Number</label>
                                    <input type="text" id="cms-contact_phone" name="contact_phone" placeholder="+237 6 12 34 56 78">
                                </div>
                                <div class="form-group">
                                    <label for="cms-contact_location">Office Location</label>
                                    <input type="text" id="cms-contact_location" name="contact_location" placeholder="Buea, Cameroon">
                                </div>
                                <div class="form-group">
                                    <label for="cms-contact_response_time">Response Time Message</label>
                                    <input type="text" id="cms-contact_response_time" name="contact_response_time" placeholder="We typically reply within 24 hours">
                                </div>
                            </div>
                            <div class="form-actions"><button type="submit" class="btn-primary" id="cms-save-contact">Save Contact Info</button></div>
                        </form>
                    </div>

                    <!-- ── Pricing panel ────────────────────────────────── -->
                    <div id="cms-panel-pricing" class="cms-panel" style="display:none;" role="tabpanel">
                        <form class="cms-form" onsubmit="saveCmsPanel(event,'pricing')">
                            <div class="cms-form-grid">
                                <div class="form-group">
                                    <label for="cms-pricing_hero_badge">Badge Text</label>
                                    <input type="text" id="cms-pricing_hero_badge" name="pricing_hero_badge" placeholder="Simple, Transparent Pricing">
                                </div>
                                <div class="form-group">
                                    <label for="cms-pricing_hero_title">Hero Headline</label>
                                    <input type="text" id="cms-pricing_hero_title" name="pricing_hero_title" placeholder="Translate without limits">
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-pricing_hero_desc">Hero Subtitle</label>
                                    <textarea id="cms-pricing_hero_desc" name="pricing_hero_desc" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="cms-pricing_cta_title">Bottom CTA Heading</label>
                                    <input type="text" id="cms-pricing_cta_title" name="pricing_cta_title">
                                </div>
                                <div class="form-group">
                                    <label for="cms-pricing_cta_desc">Bottom CTA Subtext</label>
                                    <textarea id="cms-pricing_cta_desc" name="pricing_cta_desc" rows="2"></textarea>
                                </div>
                                <p class="form-hint form-col-2" style="margin:0;">
                                    <strong>FAQ items</strong> — question and answer pairs shown on the pricing page.
                                </p>
                                <div class="form-group"><label for="cms-pricing_faq_1_q">FAQ 1 — Question</label><input type="text" id="cms-pricing_faq_1_q" name="pricing_faq_1_q"></div>
                                <div class="form-group"><label for="cms-pricing_faq_1_a">FAQ 1 — Answer</label><textarea id="cms-pricing_faq_1_a" name="pricing_faq_1_a" rows="2"></textarea></div>
                                <div class="form-group"><label for="cms-pricing_faq_2_q">FAQ 2 — Question</label><input type="text" id="cms-pricing_faq_2_q" name="pricing_faq_2_q"></div>
                                <div class="form-group"><label for="cms-pricing_faq_2_a">FAQ 2 — Answer</label><textarea id="cms-pricing_faq_2_a" name="pricing_faq_2_a" rows="2"></textarea></div>
                                <div class="form-group"><label for="cms-pricing_faq_3_q">FAQ 3 — Question</label><input type="text" id="cms-pricing_faq_3_q" name="pricing_faq_3_q"></div>
                                <div class="form-group"><label for="cms-pricing_faq_3_a">FAQ 3 — Answer</label><textarea id="cms-pricing_faq_3_a" name="pricing_faq_3_a" rows="2"></textarea></div>
                                <p class="form-hint form-col-2" style="margin:4px 0 0;">
                                    To edit plan names, descriptions and prices go to <a href="#" onclick="showSection('subscriptions');return false;" style="color:#15803d;">Subscriptions</a>.
                                </p>
                            </div>
                            <div class="form-actions"><button type="submit" class="btn-primary" id="cms-save-pricing">Save Pricing Page</button></div>
                        </form>
                    </div>

                    <!-- ── Global / Brand panel ─────────────────────────── -->
                    <div id="cms-panel-global" class="cms-panel" style="display:none;" role="tabpanel">
                        <form class="cms-form" onsubmit="saveCmsPanel(event,'global')">
                            <div class="cms-form-grid">
                                <div class="form-group">
                                    <label for="cms-site_name">Site Name</label>
                                    <input type="text" id="cms-site_name" name="site_name" placeholder="CamLingua">
                                    <p class="form-hint">Appears in the browser tab, header, and footer.</p>
                                </div>
                                <div class="form-group">
                                    <label for="cms-site_tagline">Site Tagline</label>
                                    <input type="text" id="cms-site_tagline" name="site_tagline" placeholder="Translate. Connect. Preserve Cameroon's Languages.">
                                    <p class="form-hint">Short tagline shown in the footer.</p>
                                </div>
                                <div class="form-group form-col-2">
                                    <label for="cms-platform_logo">Logo URL</label>
                                    <input type="url" id="cms-platform_logo" name="platform_logo" placeholder="https://…">
                                    <p class="form-hint">Leave blank to use the default logo file (assets/logo.png).</p>
                                </div>
                                <div class="form-group">
                                    <label for="cms-social_github">GitHub URL</label>
                                    <input type="url" id="cms-social_github" name="social_github" placeholder="https://github.com/…">
                                </div>
                                <div class="form-group">
                                    <label for="cms-social_twitter">Twitter / X URL</label>
                                    <input type="url" id="cms-social_twitter" name="social_twitter" placeholder="https://twitter.com/…">
                                </div>
                                <div class="form-group">
                                    <label for="cms-social_linkedin">LinkedIn URL</label>
                                    <input type="url" id="cms-social_linkedin" name="social_linkedin" placeholder="https://linkedin.com/company/…">
                                </div>
                            </div>
                            <div class="form-actions"><button type="submit" class="btn-primary" id="cms-save-global">Save Global Settings</button></div>
                        </form>
                    </div>
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
                <!-- Role is READ-ONLY here. Use the "Assign Admin" button in the table to change it. -->
                <div class="form-group">
                    <label>Current Role</label>
                    <div id="user-role-display" class="role-display-box">
                        <span id="user-role-badge" class="badge badge-gray">user</span>
                        <span id="user-role-note" class="form-hint" style="margin:0;">Use the <strong>Assign&nbsp;Admin</strong> / <strong>Revoke&nbsp;Admin</strong> button in the users table to change this.</span>
                    </div>
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

<!-- Assign / Revoke Admin Role Modal -->
<div id="modal-role" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-role-title" style="display:none;">
    <div class="modal-box modal-box-sm">
        <div class="modal-header">
            <h2 class="modal-title" id="modal-role-title">Change Role</h2>
            <button class="modal-close" onclick="closeModal('role')" aria-label="Close">&times;</button>
        </div>
        <input type="hidden" id="role-user-id">
        <input type="hidden" id="role-target-role">
        <p id="role-confirm-message" style="color:#374151;margin:0 0 8px;font-size:.9375rem;"></p>
        <p id="role-confirm-sub" style="color:#6b7280;margin:0 0 24px;font-size:.875rem;"></p>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('role')">Cancel</button>
            <button type="button" class="btn-primary" id="role-confirm-btn" onclick="executeRoleChange()">Confirm</button>
        </div>
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
<style>
/* Role display box inside user modal */
.role-display-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px 14px;
    flex-wrap: wrap;
}
/* Assign/Revoke admin button in table rows */
.btn-role-grant  { color: #15803d; border-color: #bbf7d0; background: #f0fdf4; }
.btn-role-revoke { color: #b91c1c; border-color: #fecaca; background: #fff5f5; }
.btn-role-grant:hover  { background: #dcfce7; }
.btn-role-revoke:hover { background: #fee2e2; }
.btn-role-self   { opacity: .4; cursor: not-allowed; }

/* ── CMS tabs ── */
.cms-tabs {
    display: flex;
    gap: 4px;
    background: #f3f4f6;
    border-radius: 10px;
    padding: 4px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.cms-tab {
    flex: 1;
    min-width: 100px;
    padding: 8px 16px;
    font-size: .875rem;
    font-weight: 500;
    color: #6b7280;
    background: transparent;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    transition: background .15s, color .15s;
    white-space: nowrap;
}
.cms-tab:hover  { background: #e5e7eb; color: #111827; }
.cms-tab.active { background: #fff; color: #15803d; font-weight: 600;
                   box-shadow: 0 1px 4px rgba(0,0,0,.08); }
.cms-panel { background: #fff; border-radius: 12px; padding: 28px;
              border: 1px solid #e5e7eb; }
.cms-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px 24px;
}
.cms-form-grid .form-col-2 { grid-column: 1 / -1; }
@media (max-width: 640px) {
    .cms-form-grid { grid-template-columns: 1fr; }
    .cms-form-grid .form-col-2 { grid-column: 1; }
    .cms-tab { flex: unset; width: 100%; }
}
</style>
</body>
</html>
