<?php
$pageTitle  = 'CamLingua – Log In / Sign Up';
$extraCss   = ['main.css'];
$activePage = '';
include 'includes/header.php';
?>

<main class="auth-page">
  <div class="auth-card">

    <div class="auth-logo">
      <div class="logo-badge">CL</div>
      <span class="logo-text">Cam<span>Lingua</span></span>
    </div>

    <!-- Tab switcher -->
    <div class="auth-tabs" role="tablist">
      <button class="auth-tab active" id="tab-login"  role="tab" aria-selected="true"  onclick="switchTab('login')">Log In</button>
      <button class="auth-tab"        id="tab-signup" role="tab" aria-selected="false" onclick="switchTab('signup')">Sign Up</button>
    </div>

    <!-- Header text -->
    <div class="auth-card-header">
      <p class="auth-title"    id="auth-title">Welcome back!</p>
      <p class="auth-subtitle" id="auth-subtitle">Log in to continue translating and exploring.</p>
    </div>

    <!-- Login form -->
    <div id="panel-login" role="tabpanel">
      <form id="form-login" onsubmit="handleAuthSubmit(event,'login')" novalidate>
        <div class="form-group">
          <label class="form-label" for="login-email">Email</label>
          <input type="email" id="login-email" class="form-input" placeholder="Enter your email" required autocomplete="email">
        </div>
        <div class="form-group">
          <div class="form-row-header">
            <label class="form-label" for="login-password" style="margin:0">Password</label>
            <a href="#" class="form-forgot">Forgot password?</a>
          </div>
          <div class="pw-wrap">
            <input type="password" id="login-password" class="form-input" placeholder="Enter your password" required autocomplete="current-password">
            <button type="button" class="pw-toggle" onclick="togglePw('login-password',this)" aria-label="Show password">
              <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-off-icon" style="display:none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>
        <div class="form-check">
          <input type="checkbox" id="login-terms">
          <label for="login-terms">I agree to the <a href="terms-of-service.php" style="color:#15803d;">Terms of Service</a></label>
        </div>
        <button type="submit" class="btn-auth-submit">Log In</button>
      </form>
      <p class="form-footer">Don't have an account? <button class="link-btn" onclick="switchTab('signup')">Sign up</button></p>
    </div>

    <!-- Sign Up form -->
    <div id="panel-signup" role="tabpanel" class="hidden">
      <form id="form-signup" onsubmit="handleAuthSubmit(event,'signup')" novalidate>
        <div class="form-group">
          <label class="form-label" for="signup-username">Full Name</label>
          <input type="text" id="signup-username" class="form-input" placeholder="Enter your full name" required autocomplete="name">
        </div>
        <div class="form-group">
          <label class="form-label" for="signup-email">Email</label>
          <input type="email" id="signup-email" class="form-input" placeholder="Enter your email" required autocomplete="email">
        </div>
        <div class="form-group">
          <label class="form-label" for="signup-password">Create a password</label>
          <div class="pw-wrap">
            <input type="password" id="signup-password" class="form-input" placeholder="Create a password" required autocomplete="new-password">
            <button type="button" class="pw-toggle" onclick="togglePw('signup-password',this)" aria-label="Show password">
              <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-off-icon" style="display:none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>
        <div class="form-check">
          <input type="checkbox" id="signup-terms">
          <label for="signup-terms">I agree to the <a href="terms-of-service.php" style="color:#15803d;">Terms of Service</a></label>
        </div>
        <button type="submit" class="btn-auth-submit">Sign Up</button>
      </form>
      <p class="form-footer">Already have an account? <button class="link-btn" onclick="switchTab('login')">Log in</button></p>
    </div>

    <div class="auth-divider">or</div>
    <p style="text-align:center;font-size:.8125rem;color:#6b7280;">"Language is the bridge that connects us all."</p>

  </div>
</main>

<script src="assets/js/api.js"></script>
<script src="assets/js/script.js"></script>
<style>
.pw-wrap {
  position: relative;
  display: flex;
  align-items: center;
}
.pw-wrap .form-input {
  width: 100%;
  padding-right: 44px;
}
.pw-toggle {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
  color: #9ca3af;
  display: flex;
  align-items: center;
  transition: color .15s;
  line-height: 1;
}
.pw-toggle:hover { color: #15803d; }
</style>
<script>
function togglePw(inputId, btn) {
  var input   = document.getElementById(inputId);
  var eyeOn   = btn.querySelector('.eye-icon');
  var eyeOff  = btn.querySelector('.eye-off-icon');
  var showing = input.type === 'text';
  input.type         = showing ? 'password' : 'text';
  eyeOn.style.display  = showing ? ''     : 'none';
  eyeOff.style.display = showing ? 'none' : '';
  btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
}
</script>
