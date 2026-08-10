// js/subscription.js
// Handles billing toggle, modal checkout, FAQ accordion, toasts, and form submits

// --- Billing Toggle ---
var toggle   = document.getElementById('billing-toggle');
var lblMonth = document.getElementById('lbl-month');
var lblYear  = document.getElementById('lbl-year');
var proPrice = document.getElementById('pro-price');
var proNote  = document.getElementById('pro-note');
var prePrice = document.getElementById('pre-price');
var preNote  = document.getElementById('pre-note');

if (toggle) {
    toggle.addEventListener('change', function() {
        if (toggle.checked) {
            // Yearly prices (20% off)
            lblMonth.classList.remove('on');
            lblYear.classList.add('on');
            if (proPrice) proPrice.textContent = '7,920';
            if (prePrice) prePrice.textContent = '15,920';
            if (proNote)  proNote.textContent  = 'Billed yearly · Save 20% · Cancel anytime';
            if (preNote)  preNote.textContent  = 'Billed yearly · Save 20% · Cancel anytime';
        } else {
            // Monthly prices
            lblMonth.classList.add('on');
            lblYear.classList.remove('on');
            if (proPrice) proPrice.textContent = '9,900';
            if (prePrice) prePrice.textContent = '19,900';
            if (proNote)  proNote.textContent  = 'Billed monthly · Cancel anytime';
            if (preNote)  preNote.textContent  = 'Billed monthly · Cancel anytime';
        }
    });
}


// --- Checkout Modal ---
var currentPlan  = '';
var currentPrice = '';

function openModal(plan, price) {
    currentPlan  = plan;
    currentPrice = price;

    // Free plan check - just show toast notification
    if (plan === 'Free') {
        showToast('You are already on the Free plan!');
        return;
    }

    var modalPlanElem  = document.getElementById('modal-plan');
    var modalDescElem  = document.getElementById('modal-desc');
    var modalTotalElem = document.getElementById('modal-total');
    var planInputElem  = document.getElementById('modal-plan-input');
    var priceInputElem = document.getElementById('modal-price-input');
    var modalOverlay   = document.getElementById('modal');
    var modalNameElem  = document.getElementById('modal-name');

    if (modalPlanElem)  modalPlanElem.textContent  = plan;
    if (modalDescElem)  modalDescElem.textContent  = 'Subscribe to ' + plan + ' for FCFA ' + price + '/month.';
    if (modalTotalElem) modalTotalElem.value       = 'FCFA ' + price;
    if (planInputElem)  planInputElem.value        = plan;
    if (priceInputElem) priceInputElem.value       = price;

    // Reset billing selector to monthly when opening modal
    var selBilling = document.getElementById('sel-billing');
    if (selBilling) selBilling.value = 'monthly';

    if (modalOverlay) {
        modalOverlay.classList.add('show');
    }
    if (modalNameElem) {
        modalNameElem.focus();
    }
}

function closeModal() {
    var modalOverlay = document.getElementById('modal');
    if (modalOverlay) {
        modalOverlay.classList.remove('show');
    }
}

// Close if user clicks outside the modal box
var modalOverlay = document.getElementById('modal');
if (modalOverlay) {
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
}

// Close on Escape key press
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// Dynamic Total calculation when billing period changes inside modal
var selBilling = document.getElementById('sel-billing');
if (selBilling) {
    selBilling.addEventListener('change', function() {
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


// --- Form Submit via AJAX (POST to API) ---
var subscribeForm = document.getElementById('subscribe-form');
if (subscribeForm) {
    subscribeForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (typeof Api === 'undefined') {
            showToast('API not loaded. Please try again.');
            return;
        }

        if (!Api.isLoggedIn()) {
            showToast('Please log in to subscribe.');
            setTimeout(() => window.location.href = 'login.php', 1500);
            return;
        }

        var btn  = document.getElementById('submit-btn');
        var form = this;
        var data = new FormData(form);
        var plan = data.get('plan');
        var billingPeriod = data.get('billing_period');

        if (btn) {
            btn.textContent = 'Processing...';
            btn.disabled    = true;
        }

        try {
            var res = await Api.subscribePlan(plan, billingPeriod);
            closeModal();
            
            if (btn) {
                btn.textContent = 'Complete Subscription';
                btn.disabled    = false;
            }
            form.reset();

            if (res && res.ok) {
                showToast('Subscribed to ' + currentPlan + ' successfully!');
            } else {
                var errorMsg = (res && res.data && res.data.message) ? res.data.message : 'Subscription failed.';
                showToast('Error: ' + errorMsg);
            }
        } catch (error) {
            closeModal();
            if (btn) {
                btn.textContent = 'Complete Subscription';
                btn.disabled    = false;
            }
            showToast('Something went wrong. Please try again.');
        }
    });
}


// --- Toast Notification ---
function showToast(msg) {
    var toast = document.getElementById('toast');
    if (!toast) return;

    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(function() {
        toast.classList.remove('show');
    }, 3500);
}


// --- FAQ Accordion ---
function toggleFaq(btn) {
    var item   = btn.closest('.faq-item');
    if (!item) return;

    var isOpen = item.classList.contains('open');

    // Close all open items
    var allItems = document.querySelectorAll('.faq-item.open');
    for (var i = 0; i < allItems.length; i++) {
        allItems[i].classList.remove('open');
        var qBtn = allItems[i].querySelector('.faq-q');
        if (qBtn) qBtn.setAttribute('aria-expanded', 'false');
    }

    // Open clicked item if closed
    if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
    }
}