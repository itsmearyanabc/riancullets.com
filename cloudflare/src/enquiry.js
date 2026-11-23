/**
 * Enquiry form, Cloudflare build.
 *
 * Submits to /api/enquiry without leaving the page, validates before
 * sending, and shows the result in the same notice styles the WordPress
 * version used. Without JavaScript the form still posts normally and the
 * function redirects back with ?enquiry=sent, which this script also reads.
 */
(function () {
  'use strict';

  var forms = document.querySelectorAll('form.rc-form');
  if (!forms.length) { return; }

  var SENT = 'Thank you. Your enquiry has been received and our team will be in touch.';
  var FAILED = 'Your enquiry could not be sent. Please check your connection and try again.';

  function notice(wrap, ok, items) {
    var old = wrap.querySelector('.rc-notice');
    if (old) { old.remove(); }

    var el;
    if (ok) {
      el = document.createElement('p');
      el.className = 'rc-notice';
      el.setAttribute('role', 'status');
      el.textContent = SENT;
    } else {
      el = document.createElement('div');
      el.className = 'rc-notice rc-notice--error';
      el.setAttribute('role', 'alert');
      var strong = document.createElement('strong');
      strong.textContent = 'Please check the following:';
      var ul = document.createElement('ul');
      ul.className = 'rc-notice__list';
      items.forEach(function (msg) {
        var li = document.createElement('li');
        li.textContent = msg;
        ul.appendChild(li);
      });
      el.appendChild(strong);
      el.appendChild(ul);
    }
    el.tabIndex = -1;
    wrap.insertBefore(el, wrap.firstChild);
    el.focus({ preventScroll: true });
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function validate(form) {
    var errors = [];
    Array.prototype.forEach.call(form.querySelectorAll('[required]'), function (field) {
      var bad = !field.value.trim();
      field.setAttribute('aria-invalid', bad ? 'true' : 'false');
      if (bad) {
        var label = form.querySelector('label[for="' + field.id + '"]');
        var name = label ? label.childNodes[0].textContent.trim() : field.name;
        errors.push(name + ' is required.');
      }
    });
    var email = form.querySelector('input[type="email"]');
    if (email && email.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.value.trim())) {
      email.setAttribute('aria-invalid', 'true');
      errors.push('Please enter a valid email address.');
    }
    return errors;
  }

  var params = new URLSearchParams(window.location.search);
  var status = params.get('enquiry');

  Array.prototype.forEach.call(forms, function (form) {
    var wrap = form.parentNode;
    var time = form.querySelector('input[name="rc_time"]');
    if (time) { time.value = String(Math.floor(Date.now() / 1000)); }

    if (status === 'sent') { notice(wrap, true); }
    if (status === 'error') { notice(wrap, false, ['Your enquiry could not be sent. Please try again.']); }

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      var errors = validate(form);
      if (errors.length) {
        notice(wrap, false, errors);
        return;
      }

      var button = form.querySelector('[type="submit"]');
      if (button) { button.disabled = true; button.setAttribute('aria-busy', 'true'); }

      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json' },
      })
        .then(function (res) { return res.json().catch(function () { return { ok: false, errors: [FAILED] }; }); })
        .then(function (data) {
          if (data && data.ok) {
            form.reset();
            if (time) { time.value = String(Math.floor(Date.now() / 1000)); }
            notice(wrap, true);
          } else {
            notice(wrap, false, (data && data.errors && data.errors.length) ? data.errors : [FAILED]);
          }
        })
        .catch(function () { notice(wrap, false, [FAILED]); })
        .then(function () {
          if (button) { button.disabled = false; button.removeAttribute('aria-busy'); }
        });
    });
  });
})();
