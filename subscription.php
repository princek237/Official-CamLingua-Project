<?php
require_once 'includes/cms_helper.php';

// Load subscription plans from DB
$_plans = [];
try {
    $envPath = __DIR__ . '/Server/.env';
    $envVars = [];
    if (file_exists($envPath)) {
        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k); $v = trim($v);
            if (strlen($v) >= 2 && (($v[0] === '"' && $v[-1] === '"') || ($v[0] === "'" && $v[-1] === "'"))) {
                $v = substr($v, 1, -1);
            }
            $envVars[$k] = $v;
        }
    }
    $pdo = new PDO(
        "mysql:host={$envVars['DB_HOST']};port={$envVars['DB_PORT']};dbname={$envVars['DB_NAME']};charset=utf8mb4",
        $envVars['DB_USER'], $envVars['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $rows = $pdo->query("SELECT * FROM subscriptions WHERE is_active = 1 ORDER BY price_monthly ASC")->fetchAll();
    foreach ($rows as $row) {
        $row['features_arr'] = json_decode($row['features'] ?? '[]', true) ?: [];
        $_plans[$row['slug']] = $row;
    }
} catch (\Exception $e) {
    $_plans = [];
}

// Fallback plan data when DB is unavailable
$_planDefaults = [
    'free'    => ['name' => 'Free',    'icon' => '🌿', 'price_monthly' => 0,     'price_yearly' => 0,      'description' => 'Perfect for occasional translations and language exploration.',  'features_arr' => ['Up to 500 characters / translation','5 translations per day','Access to 5 Cameroonian languages','Basic translation history (7 days)']],
    'pro'     => ['name' => 'Pro',     'icon' => '⭐', 'price_monthly' => 9900,  'price_yearly' => 95040,  'description' => 'For students, researchers, and active language enthusiasts.',      'features_arr' => ['Unlimited characters per translation','Unlimited translations','Access to all 20+ languages','Full translation history (90 days)','Audio pronunciation','Priority support']],
    'premium' => ['name' => 'Premium', 'icon' => '💎', 'price_monthly' => 19900, 'price_yearly' => 190080, 'description' => 'For developers, institutions, and businesses integrating our API.', 'features_arr' => ['Everything in Pro','Full REST API access','Unlimited translation history','Custom glossaries & phrases','Dedicated account manager','Team collaboration (up to 10 seats)']],
];

foreach ($_planDefaults as $slug => $defaults) {
    if (!isset($_plans[$slug])) {
        $_plans[$slug] = array_merge($defaults, ['slug' => $slug, 'is_active' => 1]);
    } else {
        // Merge icon from defaults (not stored in DB)
        $_plans[$slug]['icon'] = $defaults['icon'];
    }
}

function fmtPrice($n) {
    return number_format((float)$n, 0, '.', ',');
}

$pageTitle  = cms('site_name') . ' – Choose Your Plan';
$extraCss   = ['pages.css'];
$extraJs    = ['subscription.js'];
$activePage = 'pricing';
include 'includes/header.php';
?>

<div class="sub-hero">
  <div class="tag">
    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    <?= cms('pricing_hero_badge') ?>
  </div>
  <h1><?= cms('pricing_hero_title') ?></h1>
  <p><?= cms('pricing_hero_desc') ?></p>
  <div class="billing-toggle">
    <span id="lbl-month" class="on">Monthly</span>
    <label class="switch" for="billing-toggle">
      <input type="checkbox" id="billing-toggle">
      <span class="slider"></span>
    </label>
    <span id="lbl-year">Yearly</span>
    <span class="save-tag">Save 20%</span>
  </div>
</div>

<div class="sub-wrap">
  <section class="plans" aria-label="Pricing plans">

    <?php
    $planOrder   = ['free', 'pro', 'premium'];
    $popularSlug = 'pro';
    foreach ($planOrder as $slug):
        $plan = $_plans[$slug] ?? null;
        if (!$plan) continue;
        $isPopular    = ($slug === $popularSlug);
        $priceMonthly = fmtPrice($plan['price_monthly']);
        $priceYearly  = fmtPrice(($plan['price_yearly'] ?? 0) / 12);
        $icon         = htmlspecialchars($plan['icon'] ?? '🌿');
        $name         = htmlspecialchars($plan['name']);
        $desc         = htmlspecialchars($plan['description'] ?? '');
        $features     = $plan['features_arr'];
        $btnClass     = $slug === 'free' ? 'outline' : ($slug === 'pro' ? 'solid' : 'green-line');
        $btnLabel     = $slug === 'free' ? 'Current Plan' : 'Choose ' . $name;
        $onclickPrice = $slug === 'free' ? '0' : $priceMonthly;
    ?>
    <article class="plan-card<?= $isPopular ? ' popular' : '' ?>" aria-label="<?= $name ?> plan">
        <?php if ($isPopular): ?><div class="popular-badge">Most Popular</div><?php endif; ?>
        <div class="plan-icon-box"><?= $icon ?></div>
        <div class="plan-name"><?= $name ?></div>
        <div class="plan-desc"><?= $desc ?></div>
        <div class="plan-price">
            <sup>FCFA</sup>
            <span class="amount" id="<?= $slug ?>-price" data-monthly="<?= $priceMonthly ?>" data-yearly="<?= $priceYearly ?>"><?= $priceMonthly ?></span>
            <sub>/mo</sub>
        </div>
        <p class="plan-billing" id="<?= $slug ?>-note">
            <?= $slug === 'free' ? 'No credit card required' : 'Billed monthly · Cancel anytime' ?>
        </p>
        <hr class="plan-divider">
        <ul class="plan-features">
            <?php foreach ($features as $feat): ?>
                <li><span class="tick">✓</span> <?= htmlspecialchars($feat) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" id="<?= $slug ?>-cta" class="plan-btn <?= $btnClass ?>"
                onclick="openModal('<?= $name ?>','<?= $onclickPrice ?>')"><?= $btnLabel ?></button>
    </article>
    <?php endforeach; ?>

  </section>

  <!-- Comparison table -->
  <div class="table-section" aria-label="Plan feature comparison">
    <h2>Compare all features</h2>
    <p class="sub">A detailed breakdown of what each plan includes.</p>
    <table class="table">
      <thead><tr><th style="text-align:left;">Feature</th><th>Free</th><th class="col-pro">Pro</th><th>Premium</th></tr></thead>
      <tbody>
        <tr class="group-row"><td colspan="4">Translation</td></tr>
        <tr><td class="feature">Characters per translation</td><td class="val">500</td><td class="col-pro val">Unlimited</td><td class="val">Unlimited</td></tr>
        <tr><td class="feature">Daily translation limit</td><td class="val">5 / day</td><td class="col-pro val">Unlimited</td><td class="val">Unlimited</td></tr>
        <tr><td class="feature">Supported languages</td><td class="val">5</td><td class="col-pro val">All 20+</td><td class="val">All 20+</td></tr>
        <tr class="group-row"><td colspan="4">Features</td></tr>
        <tr><td class="feature">Translation history</td><td class="val">7 days</td><td class="col-pro val">90 days</td><td class="val">Unlimited</td></tr>
        <tr><td class="feature">Audio pronunciation</td><td class="no">✕</td><td class="col-pro yes">✓</td><td class="yes">✓</td></tr>
        <tr><td class="feature">Custom glossaries</td><td class="no">✕</td><td class="col-pro no">✕</td><td class="yes">✓</td></tr>
        <tr class="group-row"><td colspan="4">Developer &amp; API</td></tr>
        <tr><td class="feature">REST API access</td><td class="no">✕</td><td class="col-pro no">✕</td><td class="yes">✓</td></tr>
        <tr><td class="feature">Team seats</td><td class="no">—</td><td class="col-pro no">—</td><td class="val">Up to 10</td></tr>
      </tbody>
    </table>
  </div>

  <!-- Payment methods -->
  <div class="payment">
    <p>Secure payments powered by <strong>CamPay</strong></p>
    <div class="pay-logos">
      <div class="pay-logo">MTN Mobile Money</div>
      <div class="pay-logo">Orange Money</div>
    </div>
  </div>

  <!-- FAQ -->
  <div class="faq">
    <h2>Frequently Asked Questions</h2>
    <p class="sub">Still have questions? We've got you covered.</p>
    <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)" aria-expanded="false">
            <?= cms('pricing_faq_1_q') ?><span class="faq-icon">+</span>
        </button>
        <div class="faq-a"><?= cms('pricing_faq_1_a') ?></div>
    </div>
    <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)" aria-expanded="false">
            <?= cms('pricing_faq_2_q') ?><span class="faq-icon">+</span>
        </button>
        <div class="faq-a"><?= cms('pricing_faq_2_a') ?></div>
    </div>
    <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)" aria-expanded="false">
            <?= cms('pricing_faq_3_q') ?><span class="faq-icon">+</span>
        </button>
        <div class="faq-a"><?= cms('pricing_faq_3_a') ?></div>
    </div>
  </div>

  <!-- CTA -->
  <div class="cta-box">
    <h2><?= cms('pricing_cta_title') ?></h2>
    <p><?= cms('pricing_cta_desc') ?></p>
    <div class="cta-btns">
      <a href="login.php" class="btn-white">Get started free</a>
      <a href="#main-content" class="btn-border-white">Compare plans</a>
    </div>
  </div>
</div>

<!-- Checkout Modal -->
<div class="overlay" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-plan">
  <div class="modal-box">
    <button class="modal-close-btn" onclick="closeModal()" aria-label="Close">✕</button>
    <h3>Subscribe to <span id="modal-plan">Pro</span></h3>
    <p class="note" id="modal-desc">Get started with unlimited translations.</p>

    <form id="subscribe-form" novalidate>
      <input type="hidden" id="modal-plan-input" name="plan">
      <input type="hidden" id="modal-price-input" name="price">

      <div class="field">
        <label for="modal-name">Full Name</label>
        <input type="text" id="modal-name" name="full_name" placeholder="Jean Paul Mballa" required autocomplete="name">
      </div>

      <div class="field">
        <label for="checkout-email">Email Address</label>
        <input type="email" id="checkout-email" name="email" placeholder="you@example.com" required autocomplete="email">
      </div>

      <div class="field">
        <label for="payment-phone">Mobile Money Number</label>
        <div style="display:flex;gap:8px;align-items:center;">
          <span style="padding:10px 12px;background:var(--gl,#f5f5f5);border:1px solid #ddd;border-radius:8px;font-size:.9rem;white-space:nowrap;">+237</span>
          <input type="tel" id="payment-phone" name="phone" placeholder="677 123 456" required
                 pattern="[67]\d{8}" maxlength="9" autocomplete="tel" style="flex:1;"
                 aria-describedby="phone-hint">
        </div>
        <small id="phone-hint" style="color:#888;font-size:.78rem;">MTN or Orange 9-digit number (e.g. 677 123 456)</small>
      </div>

      <div class="row-2">
        <div class="field">
          <label for="sel-billing">Billing Period</label>
          <select id="sel-billing" name="billing_period">
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly (Save 20%)</option>
          </select>
        </div>
        <div class="field">
          <label>Total Due</label>
          <input type="text" id="modal-total" readonly
                 style="font-weight:700;color:var(--g);background:var(--gl);cursor:default;">
        </div>
      </div>

      <button type="submit" class="btn-modal-submit" id="submit-btn">Pay with Mobile Money</button>
    </form>

    <div id="payment-pending" style="display:none;text-align:center;padding:24px 0;">
      <div id="ussd-instruction" style="font-size:1rem;font-weight:600;margin-bottom:12px;"></div>
      <p style="color:#666;font-size:.9rem;">A payment prompt has been sent to your phone.<br>
         Please approve it with your PIN to complete the subscription.</p>
      <div class="spinner" id="pay-spinner" style="margin:20px auto;width:36px;height:36px;border:4px solid #eee;border-top-color:var(--g,#22c55e);border-radius:50%;animation:spin .8s linear infinite;"></div>
      <p style="color:#aaa;font-size:.8rem;margin-top:8px;" id="poll-status-msg">Checking payment status…</p>
      <button type="button" onclick="cancelPolling()" style="margin-top:16px;background:none;border:none;color:#888;cursor:pointer;font-size:.85rem;text-decoration:underline;">Cancel</button>
    </div>

    <p class="lock-note">🔒 Secured by CamPay · MTN &amp; Orange Money · Cancel anytime</p>
  </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="toast-notification" id="toast"></div>

<?php include 'includes/footer.php'; ?>
