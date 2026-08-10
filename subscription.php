<?php
$pageTitle  = 'CamLingua – Choose Your Plan';
$extraCss   = ['pages.css'];
$extraJs    = ['subscription.js'];
$activePage = 'pricing';
include 'includes/header.php';
?>

<div class="sub-hero">
  <div class="tag">
    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    Simple, Transparent Pricing
  </div>
  <h1>Translate without <span>limits</span></h1>
  <p>Choose the plan that fits your needs. Upgrade or downgrade anytime — no hidden fees.</p>
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

    <article class="plan-card" aria-label="Free plan">
      <div class="plan-icon-box">🌿</div>
      <div class="plan-name">Free</div>
      <div class="plan-desc">Perfect for occasional translations and language exploration.</div>
      <div class="plan-price"><sup>FCFA</sup><span class="amount" id="free-price">0</span><sub>/mo</sub></div>
      <p class="plan-billing">No credit card required</p>
      <hr class="plan-divider">
      <ul class="plan-features">
        <li><span class="tick">✓</span> Up to 500 characters / translation</li>
        <li><span class="tick">✓</span> 5 translations per day</li>
        <li><span class="tick">✓</span> Access to 5 Cameroonian languages</li>
        <li><span class="tick">✓</span> Basic translation history (7 days)</li>
        <li class="off"><span class="cross">✕</span> Audio pronunciation</li>
        <li class="off"><span class="cross">✕</span> API access</li>
      </ul>
      <button type="button" id="free-cta" class="plan-btn outline" onclick="openModal('Free','0')">Current Plan</button>
    </article>

    <article class="plan-card popular" aria-label="Pro plan">
      <div class="popular-badge">Most Popular</div>
      <div class="plan-icon-box">⭐</div>
      <div class="plan-name">Pro</div>
      <div class="plan-desc">For students, researchers, and active language enthusiasts.</div>
      <div class="plan-price"><sup>FCFA</sup><span class="amount" id="pro-price">9,900</span><sub>/mo</sub></div>
      <p class="plan-billing" id="pro-note">Billed monthly · Cancel anytime</p>
      <hr class="plan-divider">
      <ul class="plan-features">
        <li><span class="tick">✓</span> Unlimited characters per translation</li>
        <li><span class="tick">✓</span> Unlimited translations</li>
        <li><span class="tick">✓</span> Access to all 20+ languages</li>
        <li><span class="tick">✓</span> Full translation history (90 days)</li>
        <li><span class="tick">✓</span> Audio pronunciation</li>
        <li><span class="tick">✓</span> Priority support</li>
      </ul>
      <button type="button" id="pro-cta" class="plan-btn solid" onclick="openModal('Pro','9,900')">Choose Pro</button>
    </article>

    <article class="plan-card" aria-label="Premium plan">
      <div class="plan-icon-box">💎</div>
      <div class="plan-name">Premium</div>
      <div class="plan-desc">For developers, institutions, and businesses integrating our API.</div>
      <div class="plan-price"><sup>FCFA</sup><span class="amount" id="pre-price">19,900</span><sub>/mo</sub></div>
      <p class="plan-billing" id="pre-note">Billed monthly · Cancel anytime</p>
      <hr class="plan-divider">
      <ul class="plan-features">
        <li><span class="tick">✓</span> Everything in Pro</li>
        <li><span class="tick">✓</span> Full REST API access</li>
        <li><span class="tick">✓</span> Unlimited translation history</li>
        <li><span class="tick">✓</span> Custom glossaries &amp; phrases</li>
        <li><span class="tick">✓</span> Dedicated account manager</li>
        <li><span class="tick">✓</span> Team collaboration (up to 10 seats)</li>
      </ul>
      <button type="button" id="premium-cta" class="plan-btn green-line" onclick="openModal('Premium','19,900')">Choose Premium</button>
    </article>

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
    <p>Secure payments powered by</p>
    <div class="pay-logos">
      <div class="pay-logo"><div class="dot" style="background:#f79e1b;"></div><div class="dot" style="background:#eb001b;margin-left:-4px;opacity:.8;"></div>Mastercard</div>
      <div class="pay-logo"><div class="dot" style="background:#1434cb;"></div>Visa</div>
      <div class="pay-logo">MTN Mobile Money</div>
      <div class="pay-logo">Orange Money</div>
    </div>
  </div>

  <!-- FAQ -->
  <div class="faq">
    <h2>Frequently Asked Questions</h2>
    <p class="sub">Still have questions? We've got you covered.</p>
    <div class="faq-item"><button class="faq-q" onclick="toggleFaq(this)" aria-expanded="false">Can I change my plan at any time?<span class="faq-icon">+</span></button><div class="faq-a">Yes! You can upgrade or downgrade at any time. Changes take effect immediately for upgrades, and at the end of your billing cycle for downgrades.</div></div>
    <div class="faq-item"><button class="faq-q" onclick="toggleFaq(this)" aria-expanded="false">Is there a free trial?<span class="faq-icon">+</span></button><div class="faq-a">We offer a 7-day free trial for both Pro and Premium plans. No credit card required.</div></div>
    <div class="faq-item"><button class="faq-q" onclick="toggleFaq(this)" aria-expanded="false">Can I use Mobile Money to pay?<span class="faq-icon">+</span></button><div class="faq-a">Absolutely. CamLingua fully supports MTN Mobile Money and Orange Money.</div></div>
  </div>

  <!-- CTA -->
  <div class="cta-box">
    <h2>Start preserving Cameroon's languages today</h2>
    <p>Join over 50,000 users translating across 20+ Cameroonian languages.</p>
    <div class="cta-btns">
      <a href="login.php" class="btn-white">Get started free</a>
      <a href="#main-content" class="btn-border-white">Compare plans</a>
    </div>
  </div>
</div>

<!-- Checkout Modal -->
<div class="overlay" id="modal" role="dialog" aria-modal="true">
  <div class="modal-box">
    <button class="modal-close-btn" onclick="closeModal()">✕</button>
    <h3>Subscribe to <span id="modal-plan">Pro</span></h3>
    <p class="note" id="modal-desc">Get started with unlimited translations.</p>
    <form id="subscribe-form" novalidate>
      <input type="hidden" id="modal-plan-input" name="plan">
      <input type="hidden" id="modal-price-input" name="price">
      <div class="field"><label>Full Name</label><input type="text" id="modal-name" placeholder="Jean Paul Mballa" required autocomplete="name"></div>
      <div class="field"><label>Email Address</label><input type="email" id="checkout-email" placeholder="you@example.com" required autocomplete="email"></div>
      <div class="field"><label>Payment Method</label><select id="payment-method" required><option value="">Select payment method</option><option value="mtn">MTN Mobile Money</option><option value="orange">Orange Money</option><option value="visa">Visa / Mastercard</option><option value="bank">Bank Transfer</option></select></div>
      <div class="row-2">
        <div class="field"><label>Billing Period</label><select id="sel-billing"><option value="monthly">Monthly</option><option value="yearly">Yearly (Save 20%)</option></select></div>
        <div class="field"><label>Total Due</label><input type="text" id="modal-total" readonly style="font-weight:700;color:var(--g);background:var(--gl);cursor:default;"></div>
      </div>
      <button type="submit" class="btn-modal-submit" id="submit-btn">Complete Subscription</button>
    </form>
    <p class="lock-note">🔒 Secure &amp; encrypted · Cancel anytime</p>
  </div>
</div>

<div class="toast-notification" id="toast"></div>

<?php include 'includes/footer.php'; ?>
