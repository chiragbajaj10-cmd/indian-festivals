/* Indian Festivals shared 3D scene controller: Next.js + WordPress.
 * Progressive enhancement: content and links work with JS disabled.
 * Respects reduced-motion and coarse pointers, and never uses WebGL. */
(function () {
  'use strict';

  var currentRoot = null;
  var destroyCurrent = null;
  var watching = false;

  function setup(root) {
    var cleanups = [];
    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    var saved = null;
    try { saved = window.localStorage.getItem('ifs-3d-motion'); } catch (error) { /* private browsing */ }
    var defaultEnabled = root.getAttribute('data-motion-default') !== 'off';
    var enabled = saved === 'on' ? !reduced.matches : saved === 'off' ? false : defaultEnabled && !reduced.matches;
    var toggle = root.querySelector('[data-motion-toggle]');
    var tiltTargets = Array.prototype.slice.call(root.querySelectorAll('[data-tilt]'));
    var observer = null;
    var reveals = Array.prototype.slice.call(root.querySelectorAll('.reveal-block'));

    function listen(target, type, handler, options) {
      target.addEventListener(type, handler, options);
      cleanups.push(function () { target.removeEventListener(type, handler, options); });
    }

    function showEverything() {
      reveals.forEach(function (section) { section.classList.add('is-visible'); });
    }

    function resetTilt(el) {
      el.style.removeProperty('--tilt-x');
      el.style.removeProperty('--tilt-y');
      el.style.removeProperty('--glare-x');
      el.style.removeProperty('--glare-y');
    }

    function setMotion(active) {
      enabled = Boolean(active && !reduced.matches);
      root.classList.toggle('motion-off', !enabled);
      root.classList.toggle('has-motion', enabled);
      if (toggle) {
        toggle.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        toggle.setAttribute('aria-label', enabled ? 'Turn off 3D motion' : 'Turn on 3D motion');
        var label = toggle.querySelector('[data-motion-label]');
        if (label) label.textContent = enabled ? '3D ON' : '3D OFF';
      }
      if (!enabled) {
        tiltTargets.forEach(resetTilt);
        showEverything();
      }
    }

    setMotion(enabled);

    if (toggle) {
      listen(toggle, 'click', function () {
        var newValue = !enabled;
        saved = newValue ? 'on' : 'off';
        try { window.localStorage.setItem('ifs-3d-motion', saved); } catch (error) { /* private browsing */ }
        setMotion(newValue);
      });
    }

    function onReducedChange() {
      if (reduced.matches) {
        setMotion(false);
      } else if (saved !== 'off') {
        setMotion(true);
      }
    }
    if (reduced.addEventListener) {
      listen(reduced, 'change', onReducedChange);
    }

    if ('IntersectionObserver' in window && enabled) {
      observer = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });
      reveals.forEach(function (section) { observer.observe(section); });
    } else {
      showEverything();
    }

    if (finePointer.matches) {
      tiltTargets.forEach(function (el) {
        var frame = 0;
        var x = 0;
        var y = 0;
        var glareX = 50;
        var glareY = 50;
        function paint() {
          frame = 0;
          el.style.setProperty('--tilt-x', x.toFixed(2) + 'deg');
          el.style.setProperty('--tilt-y', y.toFixed(2) + 'deg');
          el.style.setProperty('--glare-x', glareX.toFixed(1) + '%');
          el.style.setProperty('--glare-y', glareY.toFixed(1) + '%');
        }
        function move(event) {
          if (!enabled) return;
          var rect = el.getBoundingClientRect();
          if (rect.width <= 0 || rect.height <= 0) return;
          var px = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
          var py = Math.max(0, Math.min(1, (event.clientY - rect.top) / rect.height));
          var max = el.classList.contains('hero-postcard') ? 2.5 : 3.5;
          x = (0.5 - py) * max * 2;
          y = (px - 0.5) * max * 2;
          glareX = px * 100;
          glareY = py * 100;
          if (!frame) frame = window.requestAnimationFrame(paint);
        }
        function leave() {
          if (frame) window.cancelAnimationFrame(frame);
          frame = 0;
          resetTilt(el);
        }
        listen(el, 'pointermove', move, { passive: true });
        listen(el, 'pointerleave', leave);
        listen(el, 'blur', leave);
        cleanups.push(function () { if (frame) window.cancelAnimationFrame(frame); });
      });
    }

    /* Mobile menu: works with keyboard, closes with Escape / outside tap. */
    var menuButton = root.querySelector('[data-mobile-menu]');
    var menuPanel = root.querySelector('[data-mobile-panel]');
    if (menuButton && menuPanel) {
      function closeMenu() {
        menuPanel.classList.remove('is-open');
        menuButton.setAttribute('aria-expanded', 'false');
      }
      listen(menuButton, 'click', function () {
        var open = menuPanel.classList.toggle('is-open');
        menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      listen(document, 'keydown', function (event) {
        if (event.key === 'Escape') closeMenu();
      });
      listen(document, 'click', function (event) {
        if (!root.contains(event.target) || (!menuButton.contains(event.target) && !menuPanel.contains(event.target))) closeMenu();
      });
    }

    /* Tools rail: chevrons scroll the sideways tool track. */
    var toolsTrack = root.querySelector('.tools-track');
    if (toolsTrack) {
      var shiftTools = function (direction) {
        toolsTrack.scrollBy({ left: direction * Math.max(240, toolsTrack.clientWidth * 0.8), behavior: enabled ? 'smooth' : 'auto' });
      };
      var toolsPrev = root.querySelector('[data-journey-prev]');
      var toolsNext = root.querySelector('[data-journey-next]');
      if (toolsPrev) { listen(toolsPrev, 'click', function () { shiftTools(-1); }); }
      if (toolsNext) { listen(toolsNext, 'click', function () { shiftTools(1); }); }
    }

    /* Journey chevrons now step through the five illustrated milestones. */
    var journey = root.querySelector('[data-journey]');
    if (journey) {
      var steps = Array.prototype.slice.call(journey.querySelectorAll('[data-journey-step]'));
      var activeStep = 0;
      function selectStep(index) {
        activeStep = (index + steps.length) % steps.length;
        steps.forEach(function (step, i) {
          var active = i === activeStep;
          step.classList.toggle('is-current', active);
          if (active) step.setAttribute('aria-current', 'step');
          else step.removeAttribute('aria-current');
        });
      }
      if (steps.length) {
        selectStep(0);
        var prev = journey.querySelector('[data-journey-prev]');
        var next = journey.querySelector('[data-journey-next]');
        if (prev) listen(prev, 'click', function () { selectStep(activeStep - 1); });
        if (next) listen(next, 'click', function () { selectStep(activeStep + 1); });
      }
    }

    return function () {
      if (observer) observer.disconnect();
      cleanups.forEach(function (cleanup) { cleanup(); });
    };
  }

  function checkRoot() {
    var root = document.querySelector('.wood-canvas');
    if (root === currentRoot) return;
    if (destroyCurrent) destroyCurrent();
    currentRoot = root;
    destroyCurrent = root ? setup(root) : null;
  }

  function start() {
    checkRoot();
    if (!watching) {
      watching = true;
      // Handles Next.js client navigation to and from the home page.
      var mutation = new MutationObserver(function () { checkRoot(); });
      mutation.observe(document.body, { childList: true, subtree: true });
    }
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true });
  else start();
})();
