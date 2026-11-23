/**
 * Header behaviour and mobile navigation.
 *
 * Three jobs:
 *   1. Resolve the transparent hero header into a solid bar on scroll.
 *   2. Flip the header to light-on-dark whenever a section marked
 *      data-nav="dark" is passing beneath it.
 *   3. Run the mobile overlay, including focus trapping and scroll lock.
 */
(function () {
  'use strict';

  var header = document.querySelector('[data-rc-header]');
  if (!header) { return; }

  /* --- 1. Solid on scroll ------------------------------------------------ */

  var SOLID_AT = 80;
  var ticking = false;

  function syncSolid() {
    header.classList.toggle('is-solid', window.scrollY > SOLID_AT);
    ticking = false;
  }

  function onScroll() {
    if (!ticking) {
      ticking = true;
      window.requestAnimationFrame(syncSolid);
    }
  }

  if (header.classList.contains('rc-header--over-hero')) {
    window.addEventListener('scroll', onScroll, { passive: true });
    syncSolid();
  }

  /* --- 2. Dark-section detection -----------------------------------------
     A 1px observation band sits at the vertical centre of the header.
     Any dark section intersecting that band owns the header's colour. */

  var darkSections = document.querySelectorAll('[data-nav="dark"]');

  if (darkSections.length && 'IntersectionObserver' in window) {
    var active = new Set();
    var themeObserver = null;

    /*
     * rootMargin must be a valid length. In a prerendered page (speculation
     * rules), a background tab or some in-app browsers, innerHeight can be
     * 0 at this point, which used to produce "--1px" and throw, killing the
     * whole script. Round and clamp, and rebuild the band on resize.
     */
    var buildThemeObserver = function () {
      var band = Math.max(0, Math.round(header.offsetHeight / 2));
      var below = Math.max(0, Math.round((window.innerHeight || document.documentElement.clientHeight || 0) - band - 1));

      if (themeObserver) { themeObserver.disconnect(); }
      active.clear();

      try {
        themeObserver = new IntersectionObserver(
          function (entries) {
            entries.forEach(function (entry) {
              if (entry.isIntersecting) {
                active.add(entry.target);
              } else {
                active.delete(entry.target);
              }
            });
            header.classList.toggle('is-dark', active.size > 0);
          },
          { rootMargin: '-' + band + 'px 0px -' + below + 'px 0px' }
        );
      } catch (e) {
        themeObserver = null;
        return;
      }

      darkSections.forEach(function (section) { themeObserver.observe(section); });
    };

    buildThemeObserver();

    var resizeTimer = null;
    var lastHeight = window.innerHeight;
    window.addEventListener('resize', function () {
      // Mobile browsers fire resize when the URL bar slides; only rebuild
      // on a meaningful change.
      if (Math.abs(window.innerHeight - lastHeight) < 80) { return; }
      lastHeight = window.innerHeight;
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(buildThemeObserver, 150);
    }, { passive: true });

    // A page prerendered at zero height is rebuilt when it is shown.
    document.addEventListener('visibilitychange', function () {
      if (!document.hidden && window.innerHeight !== lastHeight) {
        lastHeight = window.innerHeight;
        buildThemeObserver();
      }
    });
  }

  /* --- 3. Mobile overlay -------------------------------------------------- */

  var burger = document.querySelector('[data-rc-burger]');
  var overlay = document.querySelector('[data-rc-overlay]');

  if (!burger || !overlay) { return; }

  var FOCUSABLE = 'a[href], button:not([disabled]), input, textarea, select, [tabindex]:not([tabindex="-1"])';
  var lastFocused = null;

  function openNav() {
    lastFocused = document.activeElement;
    overlay.hidden = false;
    // Force a reflow so the transition runs from the closed state.
    void overlay.offsetHeight;
    overlay.classList.add('is-open');
    burger.setAttribute('aria-expanded', 'true');
    document.body.classList.add('rc-scroll-locked');

    var first = overlay.querySelector(FOCUSABLE);
    if (first) { first.focus(); }

    document.addEventListener('keydown', onKeydown);
  }

  function closeNav() {
    overlay.classList.remove('is-open');
    burger.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('rc-scroll-locked');
    document.removeEventListener('keydown', onKeydown);

    if (lastFocused) { lastFocused.focus(); }

    // Keep it out of the accessibility tree once the transition ends.
    window.setTimeout(function () {
      if (!overlay.classList.contains('is-open')) { overlay.hidden = true; }
    }, 700);
  }

  function onKeydown(event) {
    if (event.key === 'Escape') {
      closeNav();
      return;
    }

    if (event.key !== 'Tab') { return; }

    var items = Array.prototype.slice.call(overlay.querySelectorAll(FOCUSABLE));
    if (!items.length) { return; }

    var first = items[0];
    var last = items[items.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  burger.addEventListener('click', function () {
    if (overlay.classList.contains('is-open')) { closeNav(); } else { openNav(); }
  });

  overlay.addEventListener('click', function (event) {
    if (event.target.closest('a')) { closeNav(); }
  });

  // A resize into the desktop layout must not leave the overlay latched open.
  window.addEventListener('resize', function () {
    if (window.innerWidth >= 992 && overlay.classList.contains('is-open')) { closeNav(); }
  });
})();
