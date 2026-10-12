/* Indian Festivals live context: today's date and the visitor's location.
 * Shared by the Next.js preview and the WordPress theme. Progressive enhancement:
 * the server already rendered India's date, this script refreshes it in the
 * visitor's own timezone and fills in their city.
 *
 * Markup hooks:
 *   [data-live="date-long"]  26 April 2025
 *   [data-live="samvat"]     Vikram Samvat 2082
 *   [data-live="day"]        26
 *   [data-live="month"]      April
 *   [data-live="month-year"] April 2025
 *   [data-live="city"]       Varanasi
 *   [data-live-week]         week row of a mini calendar (7 cells)
 *   [data-live-locate]       click to use precise GPS location
 *
 * Location: an approximate city from the visitor's network (BigDataCloud's free
 * client endpoint, no key, no cookies) is used automatically. Precise GPS is only
 * requested when the visitor clicks the location, never on page load.
 */
(function () {
  'use strict';

  var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  var DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  var CACHE_KEY = 'ifs-location-v1';
  var CACHE_MS = 12 * 60 * 60 * 1000;
  var GEO_URL = 'https://api.bigdatacloud.net/data/reverse-geocode-client?localityLanguage=en';

  function setAll(name, text) {
    var nodes = document.querySelectorAll('[data-live="' + name + '"]');
    for (var i = 0; i < nodes.length; i++) { nodes[i].textContent = text; }
  }

  function renderDate() {
    var now = new Date();
    var y = now.getFullYear();
    var m = now.getMonth();
    var d = now.getDate();
    setAll('date-long', d + ' ' + MONTHS[m] + ' ' + y);
    setAll('samvat', 'Vikram Samvat ' + (m >= 3 ? y + 57 : y + 56));
    setAll('day', String(d));
    setAll('month', MONTHS[m]);
    setAll('month-year', MONTHS[m] + ' ' + y);

    var weeks = document.querySelectorAll('[data-live-week]');
    for (var w = 0; w < weeks.length; w++) {
      var cells = weeks[w].querySelectorAll('[data-live-cell]');
      var labels = weeks[w].querySelectorAll('[data-live-dayname]');
      for (var c = 0; c < cells.length; c++) {
        var cellDate = new Date(y, m, d - now.getDay() + c);
        cells[c].textContent = String(cellDate.getDate());
        cells[c].classList.toggle('today', c === now.getDay());
        cells[c].setAttribute('title', cellDate.getDate() + ' ' + MONTHS[cellDate.getMonth()] + ' ' + cellDate.getFullYear());
      }
      for (var l = 0; l < labels.length; l++) { labels[l].textContent = DAYS[l]; }
    }
  }

  function cityFrom(data) {
    if (!data) { return ''; }
    return data.city || data.locality || data.principalSubdivision || data.countryName || '';
  }

  function showCity(city) {
    if (city) { setAll('city', city); }
  }

  function readCache() {
    try {
      var raw = window.localStorage.getItem(CACHE_KEY);
      if (!raw) { return null; }
      var parsed = JSON.parse(raw);
      return parsed && Date.now() - parsed.t < CACHE_MS ? parsed : null;
    } catch (e) { return null; }
  }

  function writeCache(city, precise) {
    try { window.localStorage.setItem(CACHE_KEY, JSON.stringify({ city: city, precise: precise, t: Date.now() })); } catch (e) { /* private mode */ }
  }

  function lookup(url, precise) {
    if (!window.fetch) { return; }
    var controller = window.AbortController ? new AbortController() : null;
    var timer = setTimeout(function () { if (controller) { controller.abort(); } }, 6000);
    fetch(url, { credentials: 'omit', signal: controller ? controller.signal : undefined })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (data) {
        var city = cityFrom(data);
        if (city) { showCity(city); writeCache(city, precise); }
      })
      .catch(function () { /* keep the server default */ })
      .then(function () { clearTimeout(timer); });
  }

  function autoLocate() {
    var cached = readCache();
    if (cached && cached.city) { showCity(cached.city); return; }
    lookup(GEO_URL, false);
  }

  function preciseLocate() {
    if (!navigator.geolocation) { return; }
    setAll('city', 'Locating…');
    navigator.geolocation.getCurrentPosition(
      function (pos) {
        lookup(GEO_URL + '&latitude=' + pos.coords.latitude + '&longitude=' + pos.coords.longitude, true);
      },
      function () {
        var cached = readCache();
        setAll('city', cached && cached.city ? cached.city : 'Varanasi');
      },
      { enableHighAccuracy: false, timeout: 8000, maximumAge: 600000 }
    );
  }

  /* Tool category filters for server-rendered markup (WordPress). */
  function initToolFilters() {
    var sections = document.querySelectorAll('[data-tools]');
    for (var s = 0; s < sections.length; s++) {
      (function (section) {
        var buttons = section.querySelectorAll('[data-tools-filter]');
        var tiles = section.querySelectorAll('[data-tool-group]');
        for (var b = 0; b < buttons.length; b++) {
          buttons[b].addEventListener('click', function (event) {
            var key = event.currentTarget.getAttribute('data-tools-filter');
            for (var i = 0; i < buttons.length; i++) {
              var on = buttons[i] === event.currentTarget;
              buttons[i].classList.toggle('is-active', on);
              buttons[i].setAttribute('aria-selected', on ? 'true' : 'false');
            }
            for (var t = 0; t < tiles.length; t++) {
              tiles[t].hidden = !(key === 'all' || tiles[t].getAttribute('data-tool-group') === key);
            }
          });
        }
      })(sections[s]);
    }
  }

  /* 12/24-hour toggle for server-rendered timings (WordPress festival pages). */
  function to24(t) {
    var m = String(t).match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
    if (!m) { return t; }
    var h = parseInt(m[1], 10) % 12;
    if (/pm/i.test(m[3])) { h += 12; }
    return (h < 10 ? '0' : '') + h + ':' + m[2];
  }
  function initTimeScopes() {
    var scopes = document.querySelectorAll('[data-time-scope]');
    for (var s = 0; s < scopes.length; s++) {
      (function (scope) {
        var buttons = scope.querySelectorAll('[data-time-format]');
        function setFormat(fmt) {
          for (var i = 0; i < buttons.length; i++) {
            var on = buttons[i].getAttribute('data-time-format') === fmt;
            buttons[i].classList.toggle('is-active', on);
            buttons[i].setAttribute('aria-pressed', on ? 'true' : 'false');
          }
          var spans = scope.querySelectorAll('[data-time]');
          for (var j = 0; j < spans.length; j++) {
            if (!spans[j].getAttribute('data-time-orig')) {
              spans[j].setAttribute('data-time-orig', spans[j].textContent);
            }
            var orig = spans[j].getAttribute('data-time-orig');
            spans[j].textContent = fmt === '24' ? to24(orig) : orig;
          }
          var texts = scope.querySelectorAll('[data-time-text]');
          for (var k = 0; k < texts.length; k++) {
            if (!texts[k].getAttribute('data-time-orig')) {
              texts[k].setAttribute('data-time-orig', texts[k].textContent);
            }
            var src = texts[k].getAttribute('data-time-orig');
            texts[k].textContent = fmt === '24' ? src.replace(/(\d{1,2}:\d{2}\s*(?:AM|PM))/gi, to24) : src;
          }
        }
        for (var b = 0; b < buttons.length; b++) {
          buttons[b].addEventListener('click', function (event) {
            setFormat(event.currentTarget.getAttribute('data-time-format'));
          });
        }
      })(scopes[s]);
    }
  }

  function init() {
    renderDate();
    autoLocate();
    initToolFilters();
    initTimeScopes();
    var triggers = document.querySelectorAll('[data-live-locate]');
    for (var i = 0; i < triggers.length; i++) {
      triggers[i].addEventListener('click', preciseLocate);
    }
    // Roll the date over at midnight for visitors who keep the tab open.
    var now = new Date();
    var msToMidnight = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1).getTime() - now.getTime() + 1000;
    setTimeout(function () { renderDate(); setInterval(renderDate, 86400000); }, msToMidnight);
  }

  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
})();
