/* Sezione Soluzioni – pagina impostazioni */
jQuery(function ($) {
	var $wrap = $('.sds-admin');
	var STORE = 'sds-admin-state';

	function load() {
		try { return JSON.parse(window.localStorage.getItem(STORE)) || {}; } catch (e) { return {}; }
	}
	function save(state) {
		try { window.localStorage.setItem(STORE, JSON.stringify(state)); } catch (e) { /* ignora */ }
	}
	var state = load();
	state.open = state.open || {};

	/* Schede del menu (Contenuti, Testi e font, …) */
	function showPage(id) {
		if (!$wrap.find('.sds-page[data-sds-page="' + id + '"]').length) id = 'contenuti';
		$wrap.find('.sds-nav .nav-tab').each(function () {
			$(this).toggleClass('nav-tab-active', $(this).data('sds-page') === id);
		});
		$wrap.find('.sds-page').each(function () {
			$(this).toggleClass('is-active', $(this).data('sds-page') === id);
		});
		state.page = id;
		save(state);
	}
	$wrap.addClass('is-js');
	$wrap.on('click', '.sds-nav .nav-tab', function (e) {
		e.preventDefault();
		showPage($(this).data('sds-page'));
	});
	showPage(window.location.hash ? window.location.hash.slice(1) : (state.page || 'contenuti'));

	/* Riquadri richiudibili: ricorda quali erano aperti */
	$wrap.find('.sds-box').each(function () {
		var key = $(this).data('sds-box');
		if (state.open[key]) this.open = true;
	});
	// "toggle" non risale nel DOM: lo ascoltiamo su ogni riquadro
	$wrap.find('.sds-box').each(function () {
		this.addEventListener('toggle', function () {
			state.open[$(this).data('sds-box')] = this.open;
			save(state);
		});
	});

	$wrap.on('click', '.sds-open-all, .sds-close-all', function () {
		var open = $(this).hasClass('sds-open-all');
		$(this).closest('.sds-page').find('.sds-box').each(function () { this.open = open; });
	});

	/* Il titolo della scheda si aggiorna mentre lo scrivi */
	$wrap.on('input', '.sds-tab-title', function () {
		var $sub = $(this).closest('.sds-box').find('> summary .sds-box-sub');
		$sub.text(this.value.trim() || $sub.data('empty'));
	});

	/* Colori */
	$('.sds-color').wpColorPicker();

	/* Immagine o video dalla Libreria media */
	$('.sds-media-select').on('click', function (e) {
		e.preventDefault();
		var $cell = $(this).closest('.sds-media');
		var frame = wp.media({
			title: 'Scegli l\'immagine o il video della scheda',
			button: { text: 'Usa questo file' },
			library: { type: ['image', 'video'] },
			multiple: false
		});
		frame.on('select', function () {
			var file = frame.state().get('selection').first().toJSON();
			var html;
			if (file.type === 'video') {
				html = $('<p>').append('<span class="dashicons dashicons-video-alt3"></span> ').append(document.createTextNode(file.filename));
			} else {
				var url = file.sizes && file.sizes.medium ? file.sizes.medium.url : file.url;
				html = $('<img>', { src: url, css: { maxWidth: '240px', height: 'auto', display: 'block' } });
			}
			$cell.find('.sds-media-id').val(file.id);
			$cell.find('.sds-media-preview').empty().append(html);
			$cell.find('.sds-media-remove').show();
		});
		frame.open();
	});

	$('.sds-media-remove').on('click', function (e) {
		e.preventDefault();
		var $cell = $(this).closest('.sds-media');
		$cell.find('.sds-media-id').val('0');
		$cell.find('.sds-media-preview').empty();
		$(this).hide();
	});

	/* Lingua dei contenuti (con Elementor Page Translator) */
	function showLingua(codice) {
		$wrap.find('.sds-lingua-btn').each(function () {
			$(this).toggleClass('is-active', String($(this).data('sds-lingua')) === codice);
		});
		$wrap.find('.sds-lingua-blocco').each(function () {
			$(this).toggleClass('is-active', String($(this).data('sds-lingua')) === codice);
		});
		state.lingua = codice;
		save(state);
	}
	if ($wrap.find('.sds-lingua-btn').length) {
		$wrap.on('click', '.sds-lingua-btn', function () { showLingua(String($(this).data('sds-lingua'))); });
		if (state.lingua && $wrap.find('.sds-lingua-blocco[data-sds-lingua="' + state.lingua + '"]').length) showLingua(state.lingua);
	}

	/* Copia i contenuti italiani nei campi di un'altra lingua (immagini comprese) */
	$wrap.on('click', '.sds-copia-it', function () {
		var $dest = $(this).closest('.sds-lingua-blocco');
		var codice = String($dest.data('sds-lingua'));
		var pieno = $dest.find('input[type="text"], input[type="url"], textarea').filter(function () { return this.value.trim() !== ''; }).length;
		if (pieno && !window.confirm('I campi già compilati in questa lingua verranno sostituiti. Continuare?')) return;
		var $src = $wrap.find('.sds-lingua-blocco[data-sds-lingua=""]');
		var da = '[schede]', a = '[schede_lingue][' + codice + ']';
		$src.find('input, textarea').each(function () {
			if (!this.name) return;
			var $t = $dest.find('[name="' + this.name.replace(da, a) + '"]');
			if (!$t.length) return;
			if (this.type === 'checkbox') $t.prop('checked', this.checked);
			else $t.val(this.value).trigger('input');
		});
		$src.find('.sds-media').each(function (i) {
			var $d = $dest.find('.sds-media').eq(i);
			$d.find('.sds-media-preview').html($(this).find('.sds-media-preview').html());
			$d.find('.sds-media-remove').toggle($(this).find('.sds-media-id').val() !== '0');
		});
	});
});
