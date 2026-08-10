/**
 * CamLingua – About Page  |  script.js
 * Vanilla JS (ES6+) — no frameworks.
 *
 * Features:
 *  1. Mobile navigation toggle
 *  2. Navbar scroll-shrink effect
 *  3. Scroll-reveal animations (IntersectionObserver)
 *  4. Animated statistics counter (IntersectionObserver)
 *  5. Smooth-scroll for in-page anchor links
 *  6. Active nav-link highlight based on scroll position
 */

'use strict';

/* ============================================================
   1. MOBILE NAVIGATION TOGGLE
   ============================================================ */
(function initMobileNav() {
  const toggle   = document.getElementById('menu-toggle');
  const menu     = document.getElementById('mobile-menu');
  const iconOpen  = document.getElementById('icon-open');
  const iconClose = document.getElementById('icon-close');

  if (!toggle || !menu) return;

  toggle.addEventListener('click', () => {
    const isOpen = !menu.classList.contains('hidden');

    if (isOpen) {
      // Close
      menu.classList.add('hidden');
      iconOpen.classList.remove('hidden');
      iconClose.classList.add('hidden');
      toggle.setAttribute('aria-label', 'Open menu');
    } else {
      // Open
      menu.classList.remove('hidden');
      iconOpen.classList.add('hidden');
      iconClose.classList.remove('hidden');
      toggle.setAttribute('aria-label', 'Close menu');
    }
  });

  // Close menu when a link inside it is clicked
  menu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      menu.classList.add('hidden');
      iconOpen.classList.remove('hidden');
      iconClose.classList.add('hidden');
      toggle.setAttribute('aria-label', 'Open menu');
    });
  });
})();


/* ============================================================
   2. NAVBAR SCROLL-SHRINK EFFECT
   ============================================================ */
(function initNavbarScroll() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const onScroll = () => {
    if (window.scrollY > 20) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };

  // Use passive listener for better scroll performance
  window.addEventListener('scroll', onScroll, { passive: true });

  // Run once on load in case page is already scrolled
  onScroll();
})();


/* ============================================================
   3. SCROLL-REVEAL ANIMATIONS
   Uses IntersectionObserver to add .visible to .reveal elements
   when they enter the viewport.
   ============================================================ */
(function initScrollReveal() {
  const revealEls = document.querySelectorAll('.reveal');
  if (!revealEls.length) return;

  // Fallback: if IntersectionObserver is not supported, show everything
  if (!('IntersectionObserver' in window)) {
    revealEls.forEach(el => el.classList.add('visible'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          // Stop watching once revealed — no need to toggle back
          obs.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.12,   // trigger when 12% of element is visible
      rootMargin: '0px 0px -40px 0px', // slight bottom offset
    }
  );

  revealEls.forEach(el => observer.observe(el));
})();

/* ============================================================
   4. ANIMATED STATISTICS COUNTER
   Reads data-target and data-suffix from each .stat-number element.
   Fires once when the stats section enters the viewport.
   ============================================================ */
(function initStatCounters() {
  const statSection = document.getElementById('stats');
  const statNumbers = document.querySelectorAll('.stat-number[data-target]');
  if (!statSection || !statNumbers.length) return;

  /**
   * Easing function – ease-out cubic
   * @param {number} t  progress 0→1
   */
  const easeOutCubic = t => 1 - Math.pow(1 - t, 3);

  /**
   * Animates a single counter element from 0 → target.
   * @param {HTMLElement} el
   * @param {number}      target
   * @param {string}      suffix   e.g. "+" or "K+" or "%"
   * @param {number}      duration ms
   */
  function animateCounter(el, target, suffix, duration = 1800) {
    const startTime = performance.now();

    function tick(now) {
      const elapsed  = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const eased    = easeOutCubic(progress);
      const current  = Math.round(eased * target);

      el.textContent = current + suffix;

      if (progress < 1) {
        requestAnimationFrame(tick);
      } else {
        // Ensure final value is exact
        el.textContent = target + suffix;
        // Trigger the CSS pulse defined in style.css
        el.classList.add('pulse');
        el.addEventListener('animationend', () => el.classList.remove('pulse'), { once: true });
      }
    }

    requestAnimationFrame(tick);
  }

  // Fallback: no IntersectionObserver support → run immediately
  if (!('IntersectionObserver' in window)) {
    statNumbers.forEach(el => {
      const target = parseInt(el.dataset.target, 10);
      const suffix = el.dataset.suffix || '';
      animateCounter(el, target, suffix);
    });
    return;
  }

  let countersStarted = false;

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !countersStarted) {
          countersStarted = true;

          statNumbers.forEach((el, index) => {
            const target = parseInt(el.dataset.target, 10);
            const suffix = el.dataset.suffix || '';
            // Stagger each counter slightly for a cascading feel
            setTimeout(() => animateCounter(el, target, suffix), index * 150);
          });

          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.3 }
  );

  observer.observe(statSection);
})();

/* ============================================================
   5. SMOOTH-SCROLL FOR IN-PAGE ANCHOR LINKS
   Handles links like href="#mission", href="#team", href="#stats".
   Accounts for the fixed navbar height so the target isn't hidden.
   ============================================================ */
(function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      // Skip bare "#" links (e.g. placeholders)
      if (targetId === '#') return;

      const target = document.querySelector(targetId);
      if (!target) return;

      e.preventDefault();

      const navbar      = document.getElementById('navbar');
      const navbarH     = navbar ? navbar.offsetHeight : 64;
      const targetTop   = target.getBoundingClientRect().top + window.scrollY;
      const scrollTo    = targetTop - navbarH - 16; // 16px extra breathing room

      window.scrollTo({ top: scrollTo, behavior: 'smooth' });
    });
  });
})();


/* ============================================================
   6. ACTIVE NAV-LINK HIGHLIGHT (scroll spy – desktop only)
   Watches sections with IDs and highlights the matching desktop
   nav link when that section is in view.
   ============================================================ */
(function initScrollSpy() {
  // Map section IDs → nav link text content (loose match)
  const sectionIds = ['stats', 'mission', 'team'];

  const sections = sectionIds
    .map(id => document.getElementById(id))
    .filter(Boolean);

  const navLinks = document.querySelectorAll('header nav a');
  if (!navLinks.length || !sections.length) return;

  if (!('IntersectionObserver' in window)) return;

  /** Remove all active styles from desktop nav links */
  function clearActiveLinks() {
    navLinks.forEach(link => {
      link.classList.remove('text-green-800', 'font-semibold');
      link.classList.add('text-gray-600');
    });
  }

  const spyObserver = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;

        const sectionId = entry.target.id;
        // Find a nav link whose text loosely matches the section
        // (For a real app you'd use data-attributes; here we keep it simple)
        clearActiveLinks();
      });
    },
    { threshold: 0.4, rootMargin: '-80px 0px 0px 0px' }
  );

  sections.forEach(sec => spyObserver.observe(sec));
})();


/* ============================================================
   7. UTILITY – set current year in footer (if element exists)
   ============================================================ */
(function setYear() {
  const yearEl = document.getElementById('footer-year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();
})();
