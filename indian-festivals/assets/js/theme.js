/**
 * Indian Festivals theme scripts: mobile menu, countdown, wish generator.
 * No dependencies. Vanilla JS, progressive enhancement.
 */
(function () {
	'use strict';

	/* Mobile menu toggle */
	document.addEventListener('click', function (event) {
		var toggle = event.target.closest('[data-menu-toggle]');
		if (!toggle) {
			return;
		}
		var panel = document.getElementById('mobile-nav');
		if (!panel) {
			return;
		}
		var open = panel.classList.toggle('open');
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
	});

	/* Countdown widgets: data-date="YYYY-MM-DD" (festival start, India time) */
	function updateCountdown(el) {
		var date = el.getAttribute('data-date');
		if (!date) {
			return;
		}
		var target = new Date(date + 'T00:00:00+05:30').getTime();
		var diff = Math.max(0, target - Date.now());
		var set = function (unit, value) {
			var node = el.querySelector('[data-unit="' + unit + '"]');
			if (node) {
				node.textContent = String(value).padStart(2, '0');
			}
		};
		set('d', Math.floor(diff / 86400000));
		set('h', Math.floor((diff % 86400000) / 3600000));
		set('m', Math.floor((diff % 3600000) / 60000));
	}

	function startCountdowns() {
		var widgets = document.querySelectorAll('.ifs-countdown');
		if (!widgets.length) {
			return;
		}
		widgets.forEach(updateCountdown);
		setInterval(function () {
			widgets.forEach(updateCountdown);
		}, 30000);
	}

	/* Wish generator */
	var TEMPLATES = {
		en: function (festival, name) {
			var who = name ? name + ' and family' : 'you and your family';
			return 'Wishing ' + who + ' a joyful ' + festival + '. May this festival bring light, health and happiness.';
		},
		hi: function (festival, name) {
			var who = name ? name : 'आप';
			return who + ' और परिवार को ' + festival + ' की हार्दिक शुभकामनाएँ। सुख, शांति और समृद्धि बनी रहे।';
		}
	};

	var DESIGNS = {
		gold: { bg1: '#f4e7c9', bg2: '#d9b872', ink: '#4a2a16', accent: '#9d7330' },
		maroon: { bg1: '#7e1c1c', bg2: '#3a0d0d', ink: '#f7e5b2', accent: '#f2d98e' },
		navy: { bg1: '#18305e', bg2: '#0f1d3c', ink: '#fbf3dc', accent: '#c69a43' }
	};

	function wishText(root) {
		var festival = root.querySelector('#wish-festival').value;
		var name = root.querySelector('#wish-name').value.trim();
		var lang = root.querySelector('#wish-lang').value;
		return (TEMPLATES[lang] || TEMPLATES.en)(festival, name);
	}

	function drawWishImage(root, text) {
		var design = DESIGNS[root.querySelector('#wish-design').value] || DESIGNS.gold;
		var canvas = document.createElement('canvas');
		canvas.width = 1080;
		canvas.height = 1080;
		var ctx = canvas.getContext('2d');
		var grad = ctx.createLinearGradient(0, 0, 0, 1080);
		grad.addColorStop(0, design.bg1);
		grad.addColorStop(1, design.bg2);
		ctx.fillStyle = grad;
		ctx.fillRect(0, 0, 1080, 1080);
		ctx.strokeStyle = design.accent;
		ctx.lineWidth = 12;
		ctx.strokeRect(40, 40, 1000, 1000);
		ctx.strokeRect(70, 70, 940, 940);
		ctx.fillStyle = design.ink;
		ctx.textAlign = 'center';
		ctx.font = '64px Georgia, serif';
		ctx.fillText('ॐ', 540, 200);
		ctx.font = '48px Georgia, serif';
		var words = text.split(' ');
		var line = '';
		var y = 380;
		words.forEach(function (word) {
			var test = line + word + ' ';
			if (ctx.measureText(test).width > 820 && line) {
				ctx.fillText(line.trim(), 540, y);
				line = word + ' ';
				y += 66;
			} else {
				line = test;
			}
		});
		ctx.fillText(line.trim(), 540, y);
		ctx.font = '36px Georgia, serif';
		ctx.fillText(root.querySelector('#wish-festival').value, 540, 960);
		return canvas;
	}

	function initWish(root) {
		var output = root.querySelector('[data-wish-output]');
		var generate = function () {
			output.textContent = wishText(root);
		};
		root.querySelector('[data-wish-generate]').addEventListener('click', generate);

		var whatsapp = root.querySelector('[data-wish-whatsapp]');
		whatsapp.addEventListener('click', function (event) {
			var text = output.textContent || wishText(root);
			whatsapp.href = 'https://wa.me/?text=' + encodeURIComponent(text);
		});

		root.querySelector('[data-wish-image]').addEventListener('click', function () {
			var text = output.textContent || wishText(root);
			drawWishImage(root, text).toBlob(function (blob) {
				if (!blob) {
					return;
				}
				var link = document.createElement('a');
				link.href = URL.createObjectURL(blob);
				link.download = 'wish.png';
				document.body.appendChild(link);
				link.click();
				link.remove();
			});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		startCountdowns();
		document.querySelectorAll('[data-wish]').forEach(initWish);
	});
})();
