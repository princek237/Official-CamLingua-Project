<?php
$pageTitle  = 'My Profile – CamLingua';
$extraCss   = ['pages.css'];
$extraJs    = ['profile.js'];
$activePage = '';
include 'includes/header.php';
?>

<div class="profile-layout">

  <!-- Sidebar -->
  <aside class="profile-sidebar">
    <button class="profile-sidebar-item active">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      Profile Overview
    </button>
    <button class="profile-sidebar-item">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      Personal Information
    </button>
    <button class="profile-sidebar-item">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      Security
    </button>
    <button class="profile-sidebar-item">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      Preferences
    </button>
    <a href="subscription.php" class="profile-sidebar-item">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      Subscription
    </a>
    <button class="profile-sidebar-item">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      Payment Methods
    </button>
    <button class="profile-sidebar-item">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      Notifications
    </button>
    <div class="profile-upgrade-box">
      <p>🌟 Go Premium</p>
      <p>Unlock unlimited translations and premium features.</p>
      <a href="subscription.php">Upgrade Now</a>
    </div>
  </aside>

  <!-- Main content -->
  <div class="profile-main">

    <div class="profile-card">
      <div class="photo-wrap" id="photoWrap" tabindex="0" aria-label="Change photo">
        <img id="profileImage" src="https://ui-avatars.com/api/?name=Your+Name&background=166534&color=fff&size=140" alt="Profile photo">
        <div class="photo-overlay">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Change
        </div>
      </div>
      <input id="imageInput" type="file" accept="image/*" hidden aria-hidden="true">

      <div class="profile-info">
        <div class="profile-name-row">
          <h2 class="profile-name" id="name">Your Name</h2>
          <span class="profile-badge">Free Plan</span>
        </div>
        <p class="profile-bio" id="bio">A short bio goes here. Talk about who you are and what you do.</p>
        <div class="profile-meta">
          <a class="profile-email" id="email" href="mailto:you@example.com">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            you@example.com
          </a>
          <button class="btn-edit" id="editBtn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Profile
          </button>
        </div>
      </div>

      <a href="translation-history.php" class="btn-outline-sm" style="margin-left:auto;align-self:flex-start;">View History</a>
    </div>

    <!-- Stats strip -->
    <div class="stats-strip">
      <div class="strip-stat"><p class="strip-value">0</p><p class="strip-label">Translations</p></div>
      <div class="strip-stat"><p class="strip-value">0</p><p class="strip-label">Saved Words</p></div>
      <div class="strip-stat"><p class="strip-value">—</p><p class="strip-label">Member Since</p></div>
      <div class="strip-stat"><p class="strip-value">Free</p><p class="strip-label">Current Plan</p></div>
    </div>

    <!-- Recent Activity heading -->
    <div style="background:var(--w);border:1px solid var(--b);border-radius:16px;padding:1.5rem;">
      <h3 style="font-size:1rem;font-weight:700;color:var(--th);margin-bottom:1rem;">Recent Activity</h3>
      <p id="recent-activity-placeholder" style="font-size:.875rem;color:var(--ts);text-align:center;padding:2rem 0;">Load your profile to see recent activity.</p>
    </div>

    <!-- Edit form -->
    <section class="editor-card" id="editor" hidden aria-label="Edit profile form">
      <h3 class="editor-title">Edit Profile</h3>
      <div class="form-grid">
        <div class="form-group"><label class="form-label" for="nameInput">Display Name</label><input class="form-input-field" id="nameInput" type="text" placeholder="Your name" autocomplete="name"></div>
        <div class="form-group"><label class="form-label" for="emailInput">Email Address</label><input class="form-input-field" id="emailInput" type="email" placeholder="you@example.com" autocomplete="email"></div>
        <div class="form-group form-group-full"><label class="form-label" for="bioInput">Short Bio</label><textarea class="form-input-field" id="bioInput" rows="3" placeholder="Tell us a bit about yourself…"></textarea></div>
      </div>
      <div class="editor-actions">
        <button class="btn-cancel" id="cancelBtn">Cancel</button>
        <button class="btn-save" id="saveBtn">Save Changes</button>
      </div>
    </section>

  </div>
</div>

<?php include 'includes/footer.php'; ?>
