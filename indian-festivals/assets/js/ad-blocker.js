/* Indian Festivals ad blocker detector.
 * Places a bait element with common ad class names and checks whether the blocker
 * hid it. Detection is a heuristic: some blockers and browsers are not detected.
 * Shows the message box only after the delay set in Theme Options.
 */
(function () {
  'use strict';

  var box = document.querySelector('.ifs-adblock');
  if (!box) {
    return;
  }

  var delay = parseInt(box.getAttribute('data-delay'), 10);
  if (isNaN(delay) || delay < 0) {
    delay = 10;
  }

  function looksBlocked(bait) {
    var style = window.getComputedStyle(bait);
    return bait.offsetHeight === 0 || bait.offsetParent === null || style.display === 'none' || style.visibility === 'hidden';
  }

  function check(done) {
    var bait = document.createElement('div');
    bait.className = 'adsbox ad-banner ad_unit advertisement pub_300x250 textads';
    bait.style.cssText = 'position:absolute;top:-999px;left:-999px;width:300px;height:250px;';
    bait.innerHTML = '&nbsp;';
    document.body.appendChild(bait);
    window.setTimeout(function () {
      var blocked = looksBlocked(bait);
      if (bait.parentNode) {
        bait.parentNode.removeChild(bait);
      }
      done(blocked);
    }, 150);
  }

  check(function (blocked) {
    if (!blocked) {
      return;
    }
    window.setTimeout(function () {
      box.hidden = false;
      var close = box.querySelector('.ifs-adblock-close');
      if (close) {
        close.focus();
        close.addEventListener('click', function () {
          box.hidden = true;
        });
      }
    }, delay * 1000);
  });
})();
