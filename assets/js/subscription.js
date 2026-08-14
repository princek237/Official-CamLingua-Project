// js/subscription.js
// Handles billing toggle, modal checkout (CamPay), FAQ accordion, and toasts.

// ── Billing Toggle ────────────────────────────────────────────────────────────
var toggle   = document.getElementById('billing-toggle');
var lblMonth = document.getElementById('lbl-month');
var lblYear  = document.getElementById('lbl-year');
var proPrice = document.getElementById('pro-price');
var proNote  = document.getElementById('pro-note');
var prePrice = document.getElementById('pre-price');
var preNote  = document.getElementById('pre-note');

if (toggle) {
    toggle.addEventListener('change', function () {
        if (toggle.checked) {
            lblMonth.classList.remove('on');
            lblYear.classList.add('on');
            if (proPrice) proPrice.textContent = '20';
            if (prePrice) prePrice.textContent = '15,920';
            if (proNote)  proNote.textContent  = 'Billed yearly · Save 20% · Cancel anytime';
            if (preNote)  preNote.textContent  = 'Billed yearly · Save 20% · Cancel anytime';
        } else {
            lblMonth.classList.add('on');
            lblYear.classList.remove('on');
            if (proPrice) proPrice.textContent = '25';
            if (prePrice) prePrice.textContent = '19,900';
            if (proNote)  proNote.textContent  = 'Billed monthly · Cancel anytime';
            if (preNote)  preNote.textContent  = 'Billed monthly · Cancel anytime';
        }
    });
}

// ── Modal state ───────────────────────────────────────────────────────────────
var currentPlan  = '';
var currentPrice = '';

/** @type {number|null} — setInterval ID for status polling */
var pollInterval  = null;
/** @type {string|null} — external_reference UUID from /payment/initiate */
var pendingPayRef = null;
/** How many poll attempts before we give up automatically */
var MAX_POLL_ATTEMPTS = 40; // 40 × 5 s = ~3 min 20 s
var pollAttempts = 0;

// ── Open / Close modal ────────────────────────────────────────────────────────
function openModal(plan, price) {
    currentPlan  = plan;
    currentPrice = price;

    if (plan === 'Free') {
        if (!Api.isLoggedIn()) {
            showToast('Please log in first.');
            setTimeout(function () { window.location.href = 'login.php'; }, 1500);
            return;
        }
        // Downgrade to free — no payment needed
        Api.subscribePlan('free', 'monthly').then(function (res) {
            if (res && res.ok) {
                showToast('Switched to the Free plan.');
            } else {
                showToast('Could not switch plan. Please try again.');
            }
        });
        return;
    }

    // Reset to form step
    showFormStep();

    var els = {
        plan:  document.getElementById('modal-plan'),
        desc:  document.getElementById('modal-desc'),
        total: document.getElementById('modal-total'),
        planInput: document.getElementById('modal-plan-input'),
        billing:   document.getElementById('sel-billing'),
        overlay:   document.getElementById('modal'),
        nameInput: document.getElementById('modal-name'),
    };

    if (els.plan)      els.plan.textContent = plan;
    if (els.desc)      els.desc.textContent = 'Subscribe to ' + plan + ' for FCFA ' + price + '/month.';
    if (els.total)     els.total.value      = 'FCFA ' + price;
    if (els.planInput) els.planInput.value  = plan;
    if (els.billing)   els.billing.value    = 'monthly';
    if (els.overlay)   els.overlay.classList.add('show');
    if (els.nameInput) els.nameInput.focus();
}

function closeModal() {
    cancelPolling();
    var overlay = document.getElementById('modal');
    if (overlay) overlay.classList.remove('show');
    showFormStep();
}

// ── Step helpers ──────────────────────────────────────────────────────────────
function showFormStep() {
    var form    = document.getElementById('subscribe-form');
    var pending = document.getElementById('payment-pending');
    if (form)    form.style.display    = '';
    if (pending) pending.style.display = 'none';

    var btn = document.getElementById('submit-btn');
    if (btn) {
        btn.textContent = 'Pay with Mobile Money';
        btn.disabled    = false;
    }
}

function showPendingStep(ussdCode, operator) {
    var form    = document.getElementById('subscribe-form');
    var pending = document.getElementById('payment-pending');
    if (form)    form.style.display    = 'none';
    if (pending) pending.style.display = '';

    var instruction = document.getElementById('ussd-instruction');
    if (instruction) {
        if (ussdCode) {
            instruction.textContent = 'Dial ' + ussdCode + ' on your ' + (operator || 'Mobile Money') + ' phone to confirm.';
        } else {
            instruction.textContent = 'Check your ' + (operator || 'Mobile Money') + ' phone for a payment prompt.';
        }
    }

    var statusMsg = document.getElementById('poll-status-msg');
    if (statusMsg) statusMsg.textContent = 'Checking payment status…';
}

// ── Close on overlay click / Escape ──────────────────────────────────────────
var modalOverlay = document.getElementById('modal');
if (modalOverlay) {
    modalOverlay.addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
});

// ── Dynamic total when billing changes ───────────────────────────────────────
var selBilling = document.getElementById('sel-billing');
if (selBilling) {
    selBilling.addEventListener('change', function () {
        var totalField = document.getElementById('modal-total');
        if (!totalField) return;
        if (this.value === 'yearly') {
            var monthlyNum = parseInt(currentPrice.replace(/,/g, ''), 10) || 0;
            var yearlyNum  = Math.round(monthlyNum * 0.8);
            totalField.value = 'FCFA ' + yearlyNum.toLocaleString() + ' /mo';
        } else {
            totalField.value = 'FCFA ' + currentPrice;
        }
    });
}

// ── Form Submit → CamPay payment initiation ───────────────────────────────────
var subscribeForm = document.getElementById('subscribe-form');
if (subscribeForm) {
    subscribeForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (typeof Api === 'undefined') {
            showToast('API not loaded. Please refresh and try again.');
            return;
        }
        if (!Api.isLoggedIn()) {
            showToast('Please log in to subscribe.');
            setTimeout(function () { window.location.href = 'login.php'; }, 1500);
            return;
        }

        var btn          = document.getElementById('submit-btn');
        var planSlug     = (document.getElementById('modal-plan-input')?.value || '').toLowerCase();
        var billingCycle = document.getElementById('sel-billing')?.value || 'monthly';
        var phoneRaw     = (document.getElementById('payment-phone')?.value || '').replace(/\s+/g, '');

        // Client-side phone validation
        if (!/^[67]\d{8}$/.test(phoneRaw)) {
            showToast('Enter a valid 9-digit MTN or Orange number (e.g. 677123456).');
            document.getElementById('payment-phone')?.focus();
            return;
        }

        if (btn) {
            btn.textContent = 'Initiating…';
            btn.disabled    = true;
        }

        try {
            var res = await Api.initPayment(planSlug, billingCycle, phoneRaw);

            if (!res || !res.ok) {
                var errMsg = res?.data?.message || 'Payment initiation failed. Please try again.';
                showToast('Error: ' + errMsg);
                if (btn) { btn.textContent = 'Pay with Mobile Money'; btn.disabled = false; }
                return;
            }

            var payData   = res.data.data || {};
            pendingPayRef = payData.external_reference || null;

            if (!pendingPayRef) {
                showToast('Something went wrong. Please try again.');
                if (btn) { btn.textContent = 'Pay with Mobile Money'; btn.disabled = false; }
                return;
            }

            // Show the waiting screen
            showPendingStep(payData.ussd_code, payData.operator);

            // Start polling
            pollAttempts = 0;
            pollInterval = setInterval(pollPaymentStatus, 5000);

        } catch (err) {
            showToast('Something went wrong. Please try again.');
            if (btn) { btn.textContent = 'Pay with Mobile Money'; btn.disabled = false; }
        }
    });
}

// ── Status Polling ────────────────────────────────────────────────────────────
async function pollPaymentStatus() {
    if (!pendingPayRef) { cancelPolling(); return; }

    pollAttempts++;
    var statusMsg = document.getElementById('poll-status-msg');

    if (pollAttempts > MAX_POLL_ATTEMPTS) {
        cancelPolling();
        if (statusMsg) statusMsg.textContent = 'Payment timed out. Please try again.';
        showToast('Payment confirmation timed out. If you approved it, please contact support.');
        return;
    }

    try {
        var res = await Api.checkPaymentStatus(pendingPayRef);
        if (!res || !res.ok) return; // transient error — keep polling

        var payStatus = res.data?.data?.status || 'PENDING';

        if (payStatus === 'SUCCESSFUL') {
            cancelPolling();
            closeModal();
            showToast('🎉 Subscribed to ' + currentPlan + ' successfully! Welcome aboard.');
            // Refresh page so the UI reflects the new subscription
            setTimeout(function () { window.location.reload(); }, 2000);

        } else if (payStatus === 'FAILED') {
            cancelPolling();
            var reason = res.data?.data?.reason || 'Payment was declined. Please try again.';
            showFormStep();
            showToast('Payment failed: ' + reason);

        } else {
            // PENDING — update the status message with elapsed time
            var elapsed = pollAttempts * 5;
            if (statusMsg) statusMsg.textContent = 'Waiting for confirmation… (' + elapsed + 's)';
        }
    } catch (err) {
        // Network hiccup — keep polling silently
    }
}

function cancelPolling() {
    if (pollInterval !== null) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
    pendingPayRef = null;
    pollAttempts  = 0;
}

// ── Toast Notification ────────────────────────────────────────────────────────
function showToast(msg) {
    var toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(function () { toast.classList.remove('show'); }, 4000);
}

// ── FAQ Accordion ─────────────────────────────────────────────────────────────
function toggleFaq(btn) {
    var item = btn.closest('.faq-item');
    if (!item) return;
    var isOpen = item.classList.contains('open');

    document.querySelectorAll('.faq-item.open').forEach(function (el) {
        el.classList.remove('open');
        var q = el.querySelector('.faq-q');
        if (q) q.setAttribute('aria-expanded', 'false');
    });

    if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
    }
}
