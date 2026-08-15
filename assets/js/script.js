/**
 * script.js - CamLingua Auth Page Logic
 * Handles login / signup tab switching and form submission via real API.
 */

// -- Tab switching ------------------------------------------------------------

function switchTab(tab) {
  var panels = { login: 'panel-login', signup: 'panel-signup' };
  var tabs   = { login: 'tab-login',   signup: 'tab-signup'   };
  var titles = {
    login:  { title: 'Welcome Back',      subtitle: 'Log in to access your saved translations and history' },
    signup: { title: 'Create an Account', subtitle: 'Join the CamLingua community today' },
  };

  Object.keys(panels).forEach(function (key) {
    var panel = document.getElementById(panels[key]);
    var tabEl = document.getElementById(tabs[key]);
    if (!panel || !tabEl) return;

    if (key === tab) {
      panel.classList.remove('hidden');
      tabEl.classList.add('active');
      tabEl.setAttribute('aria-selected', 'true');
    } else {
      panel.classList.add('hidden');
      tabEl.classList.remove('active');
      tabEl.setAttribute('aria-selected', 'false');
    }
  });

  var titleEl    = document.getElementById('auth-title');
  var subtitleEl = document.getElementById('auth-subtitle');
  if (titleEl)    titleEl.textContent    = titles[tab].title;
  if (subtitleEl) subtitleEl.textContent = titles[tab].subtitle;

  history.replaceState(null, '', '#' + tab);
}

// -- Form UI helpers ----------------------------------------------------------

function setFormLoading(formId, loading) {
  var form = document.getElementById(formId);
  if (!form) return;
  var btn = form.querySelector('button[type="submit"]');
  if (!btn) return;
  btn.disabled    = loading;
  btn.textContent = loading
    ? (formId === 'form-login' ? 'Logging in...' : 'Creating account...')
    : (formId === 'form-login' ? 'Log In'         : 'Create Account');
}

function showFormError(formId, message) {
  var errEl = document.getElementById(formId + '-error');
  if (!errEl) {
    errEl = document.createElement('p');
    errEl.id = formId + '-error';
    errEl.className = 'form-error';
    errEl.style.cssText = 'color:#dc2626;font-size:.875rem;margin-top:.5rem;';
    var form = document.getElementById(formId);
    if (form) form.prepend(errEl);
  }
  errEl.textContent = message;
  errEl.style.display = 'block';
}

function clearFormError(formId) {
  var el = document.getElementById(formId + '-error');
  if (el) el.style.display = 'none';
}

// -- Role-based redirect (single source of truth) -----------------------------

/**
 * After login, everyone lands on the main site (translator.php).
 * Admins can reach the dashboard via the Admin badge in the nav.
 * @param {string|null} role
 */
function redirectByRole(role) {
  window.location.href = 'translator.php';
}

// -- Form submission ----------------------------------------------------------

async function handleAuthSubmit(event, type) {
  event.preventDefault();
  var formId = 'form-' + type;
  clearFormError(formId);
  setFormLoading(formId, true);

  try {
    var res;

    if (type === 'login') {
      var email    = document.getElementById('login-email').value.trim();
      var password = document.getElementById('login-password').value;
      res = await Api.login(email, password);
    } else {
      var username = document.getElementById('signup-username').value.trim();
      var signupEmail    = document.getElementById('signup-email').value.trim();
      var signupPassword = document.getElementById('signup-password').value;
      res = await Api.register(username, signupEmail, signupPassword);
    }

    if (res.ok) {
      var role = null;
      if (res.data && res.data.data && res.data.data.user) {
        role = res.data.data.user.role;
      }
      if (!role) {
        var stored = Api.getUser();
        role = stored ? stored.role : null;
      }
      // Stamp time so the adminRedirectGuard skips on the landing page
      sessionStorage.setItem('cl_last_login', String(Date.now()));
      redirectByRole(role);
    } else {
      var data = res.data;
      if (data.errors) {
        var messages = Object.values(data.errors).join(' ');
        showFormError(formId, messages);
      } else {
        showFormError(formId, data.message || 'Something went wrong. Please try again.');
      }
    }
  } catch (err) {
    showFormError(formId, 'Could not reach the server. Please check your connection.');
  } finally {
    setFormLoading(formId, false);
  }
}

// -- On page load -------------------------------------------------------------

(async function initAuthPage() {
  if (!document.getElementById('panel-login')) return;

  // If a token exists, verify the role live from the server.
  // Never trust localStorage alone -- the role may have changed since last login.
  if (Api.isLoggedIn()) {
    try {
      var meRes = await Api.me();
      if (meRes.ok && meRes.data && meRes.data.data && meRes.data.data.user) {
        var liveUser = meRes.data.data.user;
        Api.setUser(liveUser);        // refresh localStorage with server-verified data
        redirectByRole(liveUser.role);
      } else {
        // Token expired or invalid -- clear it and show the login form
        Api.logout();
      }
    } catch (e) {
      // Network error -- fall back to cached role so the user is not stuck
      var cached = Api.getUser();
      if (cached) redirectByRole(cached.role);
    }
    return;
  }

  var hash = window.location.hash.replace('#', '');
  switchTab(hash === 'signup' ? 'signup' : 'login');
})();
