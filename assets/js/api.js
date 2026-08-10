/**
 * CamLingua API Client
 * Shared module used by every page to talk to the PHP backend.
 * Stores JWT token in localStorage under the key 'cl_token'.
 */

const API_BASE = 'http://localhost/CamLingua/Server/api';

const Api = (() => {

  // ── Token helpers ────────────────────────────────────────────────────────────
  const getToken  = ()        => localStorage.getItem('cl_token');
  const setToken  = (t)       => localStorage.setItem('cl_token', t);
  const clearToken = ()       => localStorage.removeItem('cl_token');

  const getUser   = ()        => {
    try { return JSON.parse(localStorage.getItem('cl_user') || 'null'); } catch { return null; }
  };
  const setUser   = (u)       => localStorage.setItem('cl_user', JSON.stringify(u));
  const clearUser = ()        => localStorage.removeItem('cl_user');

  const isLoggedIn = ()       => !!getToken();

  const logout = () => {
    clearToken();
    clearUser();
  };

  // ── Core fetch wrapper ────────────────────────────────────────────────────────
  async function request(method, path, body = null, auth = true) {
    const headers = { 'Content-Type': 'application/json' };
    if (auth) {
      const token = getToken();
      if (token) headers['Authorization'] = `Bearer ${token}`;
    }

    const opts = { method, headers };
    if (body !== null) opts.body = JSON.stringify(body);

    const res  = await fetch(`${API_BASE}${path}`, opts);
    const data = await res.json().catch(() => ({ success: false, message: 'Invalid server response' }));

    // If 401 anywhere, clear local auth and redirect to login
    if (res.status === 401) {
      clearToken();
      clearUser();
      if (!window.location.pathname.includes('login.php')) {
        window.location.href = 'login.php';
      }
    }

    return { ok: res.ok, status: res.status, data };
  }

  // ── Convenience methods ───────────────────────────────────────────────────────
  const get    = (path, auth = true)         => request('GET',    path, null, auth);
  const post   = (path, body, auth = true)   => request('POST',   path, body, auth);
  const put    = (path, body, auth = true)   => request('PUT',    path, body, auth);
  const del    = (path, auth = true)         => request('DELETE', path, null, auth);

  // ── Auth ─────────────────────────────────────────────────────────────────────
  async function login(email, password) {
    const res = await post('/auth/login', { email, password }, false);
    if (res.ok && res.data.data?.token) {
      setToken(res.data.data.token);
      setUser(res.data.data.user);
    }
    return res;
  }

  async function register(username, email, password) {
    const res = await post('/auth/register', { username, email, password }, false);
    if (res.ok && res.data.data?.token) {
      setToken(res.data.data.token);
      setUser(res.data.data.user);
    }
    return res;
  }

  async function logoutRemote() {
    await post('/auth/logout');
    logout();
  }

  async function me() {
    return get('/auth/me');
  }

  // ── Translation ───────────────────────────────────────────────────────────────
  async function translate(sourceLang, targetLang, text) {
    return post('/translate', { source_lang: sourceLang, target_lang: targetLang, text });
  }

  // ── History ───────────────────────────────────────────────────────────────────
  async function getHistory(params = {}) {
    const qs = new URLSearchParams(params).toString();
    return get(`/history${qs ? '?' + qs : ''}`);
  }

  async function deleteHistory(id) {
    return del(`/history/${id}`);
  }

  async function toggleFavorite(id) {
    return post(`/history/${id}/favorite`);
  }

  // ── Contact ───────────────────────────────────────────────────────────────────
  async function submitContact(fullName, email, subject, message) {
    return post('/contact', { full_name: fullName, email, subject, message }, false);
  }

  // ── User profile ──────────────────────────────────────────────────────────────
  async function getProfile() {
    return get('/user/profile');
  }

  async function updateProfile(data) {
    return put('/user/profile', data);
  }

  async function getSubscription() {
    return get('/user/subscription');
  }

  async function getPlans() {
    return get('/subscriptions', false);
  }

  async function subscribePlan(plan, billingPeriod) {
    return post('/user/subscribe', { plan: plan.toLowerCase(), billing_period: billingPeriod || 'monthly' });
  }

  // ── Nav helper: update header auth state ─────────────────────────────────────
  function updateNavAuth() {
    const user = getUser();
    // Buttons that exist on index, about, etc.
    const loginBtns  = document.querySelectorAll('a[href="login.php"], a[href="login.php#signup"]');
    const logoutBtns = document.querySelectorAll('[data-action="logout"]');

    if (user) {
      // Show logout button (if present), hide login/signup
      loginBtns.forEach(el => el.closest('div, li')?.classList.add('hidden'));
      logoutBtns.forEach(el => el.classList.remove('hidden'));
    } else {
      loginBtns.forEach(el => el.closest('div, li')?.classList.remove('hidden'));
      logoutBtns.forEach(el => el.classList.add('hidden'));
    }
  }

  // Public API
  return {
    getToken, setToken, clearToken,
    getUser, setUser, clearUser,
    isLoggedIn, logout, logoutRemote,
    login, register, me,
    translate,
    getHistory, deleteHistory, toggleFavorite,
    submitContact,
    getProfile, updateProfile, getSubscription, getPlans, subscribePlan,
    updateNavAuth,
  };

})();

// ── Global admin redirect guard ───────────────────────────────────────────────
// Runs on pages for logged-in users to catch role=admin mid-session.
// Does NOT run on admin.php, login.php, or within 3s of a login redirect.
(async function adminRedirectGuard() {
  var page = window.location.pathname;
  if (page.includes('admin.php') || page.includes('login.php')) return;
  if (!Api.isLoggedIn()) return;

  // Skip if we just landed here from a login redirect (prevent double-redirect race)
  var lastLogin = parseInt(sessionStorage.getItem('cl_last_login') || '0', 10);
  if (Date.now() - lastLogin < 3000) return;

  try {
    var res = await Api.me();
    if (res.ok && res.data && res.data.data && res.data.data.user) {
      var liveUser = res.data.data.user;
      Api.setUser(liveUser);
      if (liveUser.role === 'admin' && !page.includes('admin.php')) {
        window.location.href = 'admin.php';
      }
    } else if (res.status === 401) {
      Api.logout();
      window.location.href = 'login.php';
    }
  } catch (e) {
    // Network error — do nothing
  }
})();
