/* Sezione Soluzioni
 * Animazioni riprodotte dal sito di riferimento (tempi e curve uguali):
 * - titoli e dati: lettere che si mescolano (20 lettere/s, solo desktop)
 * - linea sotto la scheda attiva: molla (rigidità 400, smorzamento 35)
 * - testo: si accende lettera per lettera o parola per parola (grigio → chiaro → colore finale)
 * - immagine: si apre a tendina (0,9 s) mentre si rimpicciolisce da 1,15 a 1 (1,1 s)
 * - tabella dati: righe che si aprono una dopo l'altra (0,55 s) con barre che crescono
 */
(function () {
	'use strict';

	var MOBILE = '(max-width: 1023.98px)';
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var EASE_OUT_EXPO = 'cubic-bezier(.22,1,.36,1)';
	var EASE_IN_OUT = 'cubic-bezier(.76,0,.24,1)';
	var WIPE_CLOSED = 'inset(0% 100% 0% 0%)';
	var WIPE_OPEN = 'inset(0% 0% 0% 0%)';

	function isMobile() {
		return window.matchMedia(MOBILE).matches;
	}

	/* Curva di Bézier cubica come funzione JS (per le animazioni fatte frame per frame). */
	function bezier(x1, y1, x2, y2) {
		function a(p1, p2) { return 1 - 3 * p2 + 3 * p1; }
		function b(p1, p2) { return 3 * p2 - 6 * p1; }
		function c(p1) { return 3 * p1; }
		function calc(t, p1, p2) { return ((a(p1, p2) * t + b(p1, p2)) * t + c(p1)) * t; }
		function slope(t, p1, p2) { return 3 * a(p1, p2) * t * t + 2 * b(p1, p2) * t + c(p1); }
		return function (x) {
			if (x <= 0) return 0;
			if (x >= 1) return 1;
			var t = x;
			for (var i = 0; i < 8; i++) {
				var d = calc(t, x1, x2) - x;
				if (Math.abs(d) < 1e-6) break;
				var s = slope(t, x1, x2);
				if (Math.abs(s) < 1e-6) break;
				t -= d / s;
			}
			return calc(t, y1, y2);
		};
	}
	var easeOutExpo = bezier(0.22, 1, 0.36, 1);

	function hexToRgb(hex) {
		hex = String(hex).replace('#', '');
		if (hex.length === 3) hex = hex.replace(/./g, '$&$&');
		var n = parseInt(hex, 16);
		return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
	}

	/* Miscela di colori come framer-motion (media dei quadrati, più naturale della lineare). */
	function mix(a, b, t) {
		return [0, 1, 2].map(function (i) {
			return Math.round(Math.sqrt(a[i] * a[i] * (1 - t) + b[i] * b[i] * t));
		});
	}

	/* ------------------------------------------------------------------
	 * Lettere che si mescolano
	 * ---------------------------------------------------------------- */
	function Shuffle(el) {
		var text = el.textContent;
		el.textContent = '';
		var wrap = document.createElement('span');
		wrap.className = 'sds__shuffle-wrap';
		var size = document.createElement('span');
		size.className = 'sds__shuffle-size';
		size.setAttribute('aria-hidden', 'true');
		size.textContent = text;
		var live = document.createElement('span');
		live.className = 'sds__shuffle-live';
		live.setAttribute('aria-hidden', 'true');
		live.textContent = text;
		var sr = document.createElement('span');
		sr.className = 'sds__sr';
		sr.textContent = text;
		wrap.appendChild(size);
		wrap.appendChild(live);
		el.appendChild(wrap);
		el.appendChild(sr);

		var raf = null;

		function scramble(s) {
			var arr = s.split('');
			for (var i = arr.length - 1; i > 0; i--) {
				var j = Math.floor(Math.random() * (i + 1));
				var tmp = arr[i]; arr[i] = arr[j]; arr[j] = tmp;
			}
			return arr.join('');
		}

		this.play = function () {
			if (isMobile() || reduceMotion || !text) return;
			if (raf) cancelAnimationFrame(raf);
			var len = text.length;
			var duration = len / 20 * 1000;
			var start = performance.now() + 100;
			var last = -1;
			var frame = function (now) {
				var t = now - start;
				if (t < 0) { raf = requestAnimationFrame(frame); return; }
				var p = Math.min(t / duration, 1);
				var u = Math.floor(p * len);
				if (u !== last) {
					last = u;
					live.textContent = text.substring(0, u) + scramble(text.substring(u));
				}
				if (p < 1) raf = requestAnimationFrame(frame);
				else { live.textContent = text; raf = null; }
			};
			raf = requestAnimationFrame(frame);
		};

		this.setVisible = function (on) {
			wrap.style.visibility = on ? '' : 'hidden';
		};
	}

	/* ------------------------------------------------------------------
	 * Testo che si accende lettera per lettera (o parola per parola)
	 * Durante l'animazione si mostra una copia divisa in pezzi; alla fine
	 * torna visibile il testo originale, così la resa (crenatura, legature)
	 * è identica a quella delle altre sezioni del sito.
	 * ---------------------------------------------------------------- */
	function Reveal(container, colors, mode, speed) {
		var pieces = [];
		var index = 0;
		var stops = colors.map(hexToRgb);

		var orig = document.createElement('div');
		orig.className = 'sds__orig';
		while (container.firstChild) orig.appendChild(container.firstChild);
		var anim = orig.cloneNode(true);
		anim.className = 'sds__anim';
		anim.setAttribute('aria-hidden', 'true');
		anim.hidden = true;
		container.appendChild(orig);
		container.appendChild(anim);

		var paragraphs = anim.querySelectorAll('p');
		paragraphs.forEach(function (p, pi) {
			if (pi > 0) index += 2; // come il riferimento: l'a capo conta nello scorrere
			split(p);
		});
		if (!paragraphs.length) split(anim);

		var C = index + 8;
		var k = 8 / C;
		pieces.forEach(function (c) { c.start = c.i / C; });
		var duration = Math.min(Math.max(index / 60, 0.6), 1.8) * 1000 / (speed || 1);
		var raf = null;

		function split(node) {
			Array.prototype.slice.call(node.childNodes).forEach(function (child) {
				if (child.nodeType === 3) {
					var frag = document.createDocumentFragment();
					child.textContent.split(/(\s+)/).forEach(function (part) {
						if (!part) return;
						if (/^\s+$/.test(part)) {
							frag.appendChild(document.createTextNode(' '));
							index += 1;
							return;
						}
						var word = document.createElement('span');
						if (mode === 'parola') {
							word.textContent = part;
							pieces.push({ el: word, i: index });
							index += part.length;
						} else {
							word.className = 'sds__word';
							Array.from(part).forEach(function (ch) {
								var s = document.createElement('span');
								s.textContent = ch;
								word.appendChild(s);
								pieces.push({ el: s, i: index });
								index += 1;
							});
						}
						frag.appendChild(word);
					});
					child.parentNode.replaceChild(frag, child);
				} else if (child.nodeType === 1 && child.tagName !== 'BR') {
					split(child);
				}
			});
		}

		function colorAt(t) {
			var rgb = t <= 0.5 ? mix(stops[0], stops[1], t / 0.5) : mix(stops[1], stops[2], (t - 0.5) / 0.5);
			return 'rgb(' + rgb.join(',') + ')';
		}

		function render(p) {
			for (var n = 0; n < pieces.length; n++) {
				var t = (p - pieces[n].start) / k;
				t = t < 0 ? 0 : (t > 1 ? 1 : t);
				if (pieces[n].t !== t) {
					pieces[n].t = t;
					pieces[n].el.style.color = t === 1 ? '' : colorAt(t);
				}
			}
		}

		// on = mostra la copia animata; il testo originale resta leggibile dagli screen reader
		function showAnim(on) {
			anim.hidden = !on;
			orig.classList.toggle('sds__sr', on);
		}

		this.reset = function () {
			if (raf) cancelAnimationFrame(raf);
			raf = null;
			render(0);
			showAnim(true);
		};

		this.play = function () {
			this.reset();
			var start = performance.now();
			var frame = function (now) {
				var x = Math.min((now - start) / duration, 1);
				render(easeOutExpo(x));
				if (x < 1) {
					raf = requestAnimationFrame(frame);
				} else {
					raf = null;
					showAnim(false);
				}
			};
			raf = requestAnimationFrame(frame);
		};

		this.finish = function () {
			if (raf) cancelAnimationFrame(raf);
			raf = null;
			render(1);
			showAnim(false);
		};
	}

	/* ------------------------------------------------------------------
	 * Linea sotto la scheda attiva (molla)
	 * ---------------------------------------------------------------- */
	function Underline(el, tabsWrap) {
		var x = null, w = 0, y = 0, vx = 0, vw = 0;
		var tx = 0, tw = 0;
		var raf = null, last = 0;

		function target(btn) {
			var cs = getComputedStyle(el);
			var h = parseFloat(cs.getPropertyValue('--sds-ul-h')) || 1;
			var gap = parseFloat(cs.getPropertyValue('--sds-ul-gap')) || 2;
			tx = btn.offsetLeft;
			tw = btn.offsetWidth;
			y = btn.offsetTop + btn.offsetHeight + gap - h;
		}

		function draw() {
			el.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0) scaleX(' + w + ')';
		}

		function step(now) {
			var dt = Math.min((now - last) / 1000, 0.064);
			last = now;
			// integrazione a piccoli passi per stabilità
			var n = Math.ceil(dt / 0.004);
			var h = dt / n;
			for (var i = 0; i < n; i++) {
				var ax = -400 * (x - tx) - 35 * vx;
				var aw = -400 * (w - tw) - 35 * vw;
				vx += ax * h; x += vx * h;
				vw += aw * h; w += vw * h;
			}
			if (Math.abs(x - tx) < 0.05 && Math.abs(vx) < 0.05 && Math.abs(w - tw) < 0.05 && Math.abs(vw) < 0.05) {
				x = tx; w = tw; vx = vw = 0;
				draw();
				raf = null;
				return;
			}
			draw();
			raf = requestAnimationFrame(step);
		}

		this.moveTo = function (btn, animate) {
			target(btn);
			if (x === null || !animate || reduceMotion) {
				if (raf) cancelAnimationFrame(raf);
				raf = null;
				x = tx; w = tw; vx = vw = 0;
				draw();
				return;
			}
			if (!raf) {
				last = performance.now();
				raf = requestAnimationFrame(step);
			}
		};
	}

	/* ------------------------------------------------------------------
	 * Sezione
	 * ---------------------------------------------------------------- */
	function setup(root) {
		var cfg = JSON.parse(root.getAttribute('data-config') || '{}');
		var tabs = Array.from(root.querySelectorAll('.sds__tab'));
		var panels = Array.from(root.querySelectorAll('.sds__panel'));
		var nav = root.querySelector('.sds__nav');
		var tabsWrap = root.querySelector('.sds__tabs');
		var underline = new Underline(root.querySelector('.sds__underline'), tabsWrap);
		var active = 0;
		var canAnimate = !reduceMotion && typeof Element.prototype.animate === 'function';

		// Titoli delle schede: mescola al passaggio del mouse
		tabs.forEach(function (t) {
			var label = t.querySelector('[data-sds-shuffle]');
			var sh = new Shuffle(label);
			if (cfg.shuffle) t.addEventListener('pointerenter', function () { sh.play(); });
		});

		// Stato di ogni pannello
		var state = panels.map(function (panel) {
			var s = { panel: panel };

			var text = panel.querySelector('.sds__text');
			if (text && cfg.reveal && cfg.reveal !== 'off' && !reduceMotion) s.reveal = new Reveal(text, cfg.colors, cfg.reveal, cfg.speed);

			s.wipe = panel.querySelector('.sds__wipe');
			s.media = s.wipe && s.wipe.querySelector('.sds__img');
			s.video = s.wipe && s.wipe.querySelector('video');
			if (s.video) s.video.muted = true;

			s.stats = panel.querySelector('.sds__stats');
			s.cells = Array.from(panel.querySelectorAll('.sds__cell'));
			s.shuffles = Array.from(panel.querySelectorAll('.sds__stats [data-sds-shuffle]')).map(function (el) {
				return new Shuffle(el);
			});
			return s;
		});

		function closeMedia(s) {
			if (!s.wipe || !cfg.wipe || !canAnimate) return;
			s.wipe.style.clipPath = WIPE_CLOSED;
			if (s.media) s.media.style.transform = 'scale(1.15)';
			s.mediaArmed = true;
		}

		function openMedia(s) {
			s.mediaArmed = false;
			s.wipe.style.clipPath = WIPE_OPEN;
			if (s.media) s.media.style.transform = '';
			s.wipe.animate([{ clipPath: WIPE_CLOSED }, { clipPath: WIPE_OPEN }], { duration: 900, easing: EASE_IN_OUT });
			if (s.media) s.media.animate([{ transform: 'scale(1.15)' }, { transform: 'scale(1)' }], { duration: 1100, easing: EASE_IN_OUT });
		}

		function closeStats(s) {
			if (!s.stats || !canAnimate) return;
			s.cells.forEach(function (c) { c.style.clipPath = 'inset(0px 100% 0px 0px)'; });
			s.shuffles.forEach(function (sh) { sh.setVisible(false); });
			s.statsArmed = true;
		}

		function openStats(s) {
			s.statsArmed = false;
			s.cells.forEach(function (c) {
				var delay = parseFloat(c.getAttribute('data-delay')) * 1000 || 0;
				c.style.clipPath = '';
				c.animate([{ clipPath: 'inset(0px 100% 0px 0px)' }, { clipPath: 'inset(0px 0% 0px 0px)' }], { duration: 550, easing: EASE_OUT_EXPO, delay: delay, fill: 'backwards' });
				var bar = c.querySelector('.sds__bar');
				if (bar) {
					bar.animate([{ width: '0%' }, { width: bar.getAttribute('data-width') + '%' }], { duration: 550, easing: EASE_OUT_EXPO, delay: delay, fill: 'backwards' });
				}
			});
			s.shuffles.forEach(function (sh) {
				sh.setVisible(true);
				if (cfg.shuffle) sh.play();
			});
		}

		// Avvio delle animazioni quando gli elementi entrano nello schermo
		function observe(getEl, amount, armedKey, open) {
			if (!('IntersectionObserver' in window)) {
				state.forEach(function (s) { if (s[armedKey]) open(s); });
				return;
			}
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (e) {
					var s = state.filter(function (st) { return getEl(st) === e.target; })[0];
					if (s && s[armedKey] && e.isIntersecting && e.intersectionRatio >= amount - 0.01) open(s);
				});
			}, { threshold: [0, amount] });
			state.forEach(function (s) { if (getEl(s)) io.observe(getEl(s)); });
		}

		state.forEach(function (s) {
			closeMedia(s);
			closeStats(s);
		});
		// si osserva il contenitore: la tendina chiusa renderebbe "invisibile" l'elemento stesso
		observe(function (s) { return s.wipe && s.wipe.parentNode; }, 0.25, 'mediaArmed', openMedia);
		observe(function (s) { return s.stats; }, 0.35, 'statsArmed', openStats);

		// Testo del primo pannello: si accende quando entra nello schermo (una volta)
		if (state[0].reveal) {
			state[0].reveal.reset();
			if ('IntersectionObserver' in window) {
				var textIo = new IntersectionObserver(function (entries) {
					if (entries[0].isIntersecting && entries[0].intersectionRatio >= 0.19) {
						if (active === 0) state[0].reveal.play();
						textIo.disconnect();
					}
				}, { threshold: [0, 0.2] });
				textIo.observe(state[0].panel.querySelector('.sds__text'));
			} else {
				state[0].reveal.finish();
			}
		}

		// Video: in riproduzione solo se visibile
		if ('IntersectionObserver' in window) {
			var videoIo = new IntersectionObserver(function (entries) {
				entries.forEach(function (e) {
					if (e.isIntersecting) {
						var p = e.target.play();
						if (p && p.catch) p.catch(function () {});
					} else {
						e.target.pause();
					}
				});
			});
			state.forEach(function (s) { if (s.video) videoIo.observe(s.video); });
		}

		function activate(i, focus) {
			if (i === active) return;
			var prev = state[active];
			active = i;

			tabs.forEach(function (t, k) {
				var on = k === i;
				t.classList.toggle('is-active', on);
				t.setAttribute('aria-selected', on ? 'true' : 'false');
				t.tabIndex = on ? 0 : -1;
			});

			// prepara il nuovo pannello prima di mostrarlo
			var s = state[i];
			closeMedia(s);
			closeStats(s);
			if (s.reveal) s.reveal.reset();
			if (prev.reveal) prev.reveal.finish();

			panels.forEach(function (p, k) { p.hidden = k !== i; });
			if (s.reveal) s.reveal.play();

			underline.moveTo(tabs[i], true);

			// su mobile la barra scorre per mostrare la scheda scelta
			var t = tabs[i];
			if (nav.scrollWidth > nav.clientWidth) {
				var left = t.offsetLeft - (nav.clientWidth - t.offsetWidth) / 2;
				nav.scrollTo({ left: Math.max(0, left), behavior: reduceMotion ? 'auto' : 'smooth' });
			}
			if (focus) t.focus();
		}

		tabs.forEach(function (t, i) {
			t.addEventListener('click', function () { activate(i, false); });
			t.addEventListener('keydown', function (e) {
				var n = tabs.length, j = null;
				if (e.key === 'ArrowRight') j = (i + 1) % n;
				if (e.key === 'ArrowLeft') j = (i - 1 + n) % n;
				if (e.key === 'Home') j = 0;
				if (e.key === 'End') j = n - 1;
				if (j !== null) {
					e.preventDefault();
					activate(j, true);
				}
			});
		});

		underline.moveTo(tabs[0], false);
		root.classList.add('is-ready');

		var resizeTimer;
		var onResize = function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function () { underline.moveTo(tabs[active], false); }, 100);
		};
		window.addEventListener('resize', onResize);
		if ('ResizeObserver' in window) new ResizeObserver(onResize).observe(tabsWrap);
		if (document.fonts && document.fonts.ready) document.fonts.ready.then(onResize);
	}

	function init(scope) {
		(scope || document).querySelectorAll('.sds:not([data-sds-ready])').forEach(function (el) {
			el.setAttribute('data-sds-ready', '');
			try {
				setup(el);
			} catch (err) {
				if (window.console) console.error('Sezione Soluzioni:', err);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { init(); });
	} else {
		init();
	}

	// Editor di Elementor: widget renderizzati dinamicamente.
	function hookElementor() {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
				init($scope && $scope[0]);
			});
		}
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', hookElementor);
	}
	hookElementor();
})();
