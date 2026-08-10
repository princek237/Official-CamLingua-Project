<?php
$pageTitle  = 'Translation History – CamLingua';
$extraCss   = ['pages.css'];
$extraJs    = ['history.js'];
$activePage = 'history';
include 'includes/header.php';
?>

<div class="history-wrap">
  <h1>Translation History</h1>
  <p>View and manage your past translations</p>

  <div class="hist-filters">
    <div class="hist-search-wrap">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input id="searchInput" type="text" placeholder="Search by text...">
    </div>
    <select id="languageFilter" class="hist-select">
      <option value="">All Languages</option>
      <option value="en">English</option>
      <option value="fr">French</option>
      <option value="ewo">Ewondo</option>
      <option value="bas">Bassa</option>
      <option value="dua">Duala</option>
      <option value="bam">Bamileke</option>
      <option value="fuf">Fulfulde</option>
    </select>
    <select id="dateFilter" class="hist-select">
      <option value="">All Time</option>
      <option value="today">Today</option>
      <option value="week">This Week</option>
      <option value="month">This Month</option>
      <option value="year">This Year</option>
    </select>
  </div>

  <div class="hist-action-row">
    <p><span id="totalCount">0</span> translations found</p>
    <div style="display:flex;gap:.75rem;">
      <button id="clearHistoryBtn" class="btn-outline-red">Clear All</button>
    </div>
  </div>

  <div id="historyContainer" class="history-list"></div>

  <div id="emptyState" class="history-empty is-hidden">
    <div class="empty-icon-wrap">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    </div>
    <h3>No translation history yet</h3>
    <p>Start translating to see your history here</p>
    <a href="translator.php" class="btn-pill-green">Start Translating</a>
  </div>

  <div id="loadMoreContainer" class="load-more-wrap is-hidden">
    <button id="loadMoreBtn" class="btn-pill-outline-green">Load more</button>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
