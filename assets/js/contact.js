(function () {
    'use strict';

    var textarea  = document.getElementById('message');
    var charCount = document.getElementById('char-count');
    var form      = document.querySelector('.contact-form');
    if (!form || !textarea) return;

    var submitBtn = form.querySelector('button[type="submit"]');

    textarea.addEventListener('input', function () {
        charCount.textContent = textarea.value.length + '/1000';
    });

    function showMsg(msg, isError) {
        var el = document.getElementById('contact-form-msg');
        if (!el) {
            el = document.createElement('p');
            el.id = 'contact-form-msg';
            el.style.cssText = 'margin-top:.75rem;font-size:.9rem;font-weight:500;';
            form.appendChild(el);
        }
        el.textContent = msg;
        el.style.color = isError ? '#dc2626' : '#166534';
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        var fullName = document.getElementById('full-name').value.trim();
        var email    = document.getElementById('email').value.trim();
        var subject  = document.getElementById('subject').value;
        var message  = textarea.value.trim();

        if (!fullName || !email || !subject || !message) {
            showMsg('Please fill in all fields.', true);
            return;
        }

        submitBtn.disabled    = true;
        submitBtn.textContent = 'Sending…';

        var res = await Api.submitContact(fullName, email, subject, message);

        submitBtn.disabled   = false;
        submitBtn.innerHTML  = 'Send Message <svg class="btn__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M22 2L11 13" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        if (res.ok) {
            showMsg(res.data.message || "Thank you! We'll get back to you within 24 hours.");
            form.reset();
            charCount.textContent = '0/1000';
        } else {
            var errors = res.data.errors ? Object.values(res.data.errors).join(' ') : null;
            showMsg(errors || res.data.message || 'Failed to send. Please try again.', true);
        }
    });
})();
