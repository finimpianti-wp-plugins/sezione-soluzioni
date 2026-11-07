<?php
/**
 * Plugin Name:       Sezione Soluzioni
 * Description:       Sezione a schede animate (fino a 5) con testo, link, immagine o video, dati ed etichette, da inserire con lo shortcode [sezione_soluzioni]. Contenuti e stile in Bacheca → Sezione Soluzioni.
 * Version:           1.2.0
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Update URI:        false
 * Text Domain:       sezione-soluzioni
 * License:           GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'SDS_VERSION', '1.2.0' );
define( 'SDS_URL', plugin_dir_url( __FILE__ ) );
define( 'SDS_OPTION', 'sds_impostazioni' );
define( 'SDS_SHORTCODE', 'sezione_soluzioni' );
define( 'SDS_MAX_SCHEDE', 5 );
define( 'SDS_MAX_DATI', 3 );
define( 'SDS_MONO', 'ui-monospace, SFMono-Regular, Menlo, Consolas, monospace' );

/* -------------------------------------------------------------------------
 * Impostazioni
 * ---------------------------------------------------------------------- */

/**
 * Impostazioni di stile, organizzate in pagine (schede del menu) e gruppi (riquadri richiudibili).
 * "var" è la variabile CSS in cui finisce il valore.
 */
function sds_style_pages() {
	$px  = array( 'unit' => 'px' );
	$bp  = 'Desktop: da 1200 px. Tablet: da 768 a 1199 px. Mobile: sotto 768 px.';
	return array(
		'testi'      => array(
			'title'  => 'Testi e font',
			'groups' => array(
				'titoli'    => array(
					'title'  => 'Titoli delle schede',
					'fields' => array(
						'font_schede'       => array( 'type' => 'font', 'label' => 'Font', 'default' => 'Barlow', 'var' => '--sds-font-tab', 'desc' => 'Nome esatto del font, es. "Barlow Semi Condensed". Vuoto = font del tema.' ),
						'peso_schede'       => array( 'type' => 'int', 'label' => 'Spessore', 'default' => 300, 'min' => 100, 'max' => 900, 'step' => 100, 'var' => '--sds-w-tab', 'desc' => '300 = sottile, 400 = normale, 500 = medio, 600 = semi-grassetto, 700 = grassetto.' ),
						'dim_schede'        => array( 'type' => 'int', 'label' => 'Dimensione – desktop', 'default' => 56, 'min' => 10, 'max' => 160, 'var' => '--sds-fs-tab-d' ) + $px,
						'dim_schede_tablet' => array( 'type' => 'int', 'label' => 'Dimensione – tablet', 'default' => 32, 'min' => 10, 'max' => 120, 'var' => '--sds-fs-tab-t' ) + $px,
						'dim_schede_mobile' => array( 'type' => 'int', 'label' => 'Dimensione – mobile', 'default' => 24, 'min' => 10, 'max' => 80, 'var' => '--sds-fs-tab-m' ) + $px,
						'ls_schede'         => array( 'type' => 'float', 'label' => 'Spaziatura lettere – desktop', 'default' => -1.4, 'min' => -10, 'max' => 20, 'step' => 0.1, 'var' => '--sds-ls-tab-d', 'desc' => 'Negativo = lettere più strette, 0 = normale.' ) + $px,
						'ls_schede_mobile'  => array( 'type' => 'float', 'label' => 'Spaziatura lettere – tablet e mobile', 'default' => -0.4, 'min' => -10, 'max' => 20, 'step' => 0.1, 'var' => '--sds-ls-tab-m' ) + $px,
						'gap_schede'        => array( 'type' => 'int', 'label' => 'Distanza tra i titoli – desktop', 'default' => 48, 'min' => 0, 'max' => 200, 'var' => '--sds-tab-gap-d' ) + $px,
						'gap_schede_mobile' => array( 'type' => 'int', 'label' => 'Distanza tra i titoli – tablet e mobile', 'default' => 16, 'min' => 0, 'max' => 120, 'var' => '--sds-tab-gap-m' ) + $px,
					),
				),
				'testo'     => array(
					'title'  => 'Testo',
					'desc'   => $bp,
					'fields' => array(
						'font_testo'        => array( 'type' => 'font', 'label' => 'Font', 'default' => 'Barlow Condensed', 'var' => '--sds-font-text', 'desc' => 'Nome esatto del font. Vuoto = font del tema.' ),
						'peso_testo'        => array( 'type' => 'int', 'label' => 'Spessore', 'default' => 400, 'min' => 100, 'max' => 900, 'step' => 100, 'var' => '--sds-w-text' ),
						'dim_testo'         => array( 'type' => 'int', 'label' => 'Dimensione – desktop', 'default' => 28, 'min' => 10, 'max' => 80, 'var' => '--sds-fs-text-d' ) + $px,
						'dim_testo_tablet'  => array( 'type' => 'int', 'label' => 'Dimensione – tablet', 'default' => 20, 'min' => 10, 'max' => 60, 'var' => '--sds-fs-text-t' ) + $px,
						'dim_testo_mobile'  => array( 'type' => 'int', 'label' => 'Dimensione – mobile', 'default' => 20, 'min' => 10, 'max' => 60, 'var' => '--sds-fs-text-m' ) + $px,
						'lh_testo'          => array( 'type' => 'float', 'label' => 'Interlinea – desktop', 'default' => 1.4, 'min' => 0.8, 'max' => 3, 'step' => 0.05, 'var' => '--sds-lh-text-d', 'desc' => 'Distanza tra le righe, in proporzione alla dimensione: 1 = stretta, 1.4 = normale, 1.8 = ariosa.' ),
						'lh_testo_mobile'   => array( 'type' => 'float', 'label' => 'Interlinea – tablet e mobile', 'default' => 1.4, 'min' => 0.8, 'max' => 3, 'step' => 0.05, 'var' => '--sds-lh-text-m' ),
						'ls_testo'          => array( 'type' => 'float', 'label' => 'Spaziatura lettere – desktop', 'default' => 0, 'min' => -10, 'max' => 20, 'step' => 0.1, 'var' => '--sds-ls-text-d', 'desc' => '0 = come il font è disegnato.' ) + $px,
						'ls_testo_mobile'   => array( 'type' => 'float', 'label' => 'Spaziatura lettere – tablet e mobile', 'default' => 0, 'min' => -10, 'max' => 20, 'step' => 0.1, 'var' => '--sds-ls-text-m' ) + $px,
						'ws_testo'          => array( 'type' => 'float', 'label' => 'Spaziatura parole', 'default' => 0, 'min' => -10, 'max' => 40, 'step' => 0.5, 'var' => '--sds-ws-text' ) + $px,
						'spazio_paragrafi'  => array( 'type' => 'float', 'label' => 'Spazio tra i paragrafi', 'default' => 1, 'min' => 0, 'max' => 5, 'step' => 0.1, 'var' => '--sds-p-gap', 'unit' => 'em', 'desc' => 'In righe: 1 = una riga vuota.' ),
						'larghezza_testo'   => array( 'type' => 'int', 'label' => 'Larghezza massima del testo', 'default' => 0, 'min' => 0, 'max' => 2000, 'var' => '--sds-text-max', 'zero' => 'none', 'desc' => '0 = occupa tutta la colonna.' ) + $px,
						'stile_testo'       => array( 'type' => 'select', 'label' => 'Stile', 'default' => 'normal', 'var' => '--sds-fst-text', 'options' => array( 'normal' => 'Normale', 'italic' => 'Corsivo' ) ),
						'maiuscolo_testo'   => array( 'type' => 'select', 'label' => 'Maiuscole', 'default' => 'none', 'var' => '--sds-tt-text', 'options' => array( 'none' => 'Come scritto', 'uppercase' => 'TUTTO MAIUSCOLO', 'lowercase' => 'tutto minuscolo' ) ),
					),
				),
				'link'      => array(
					'title'  => 'Link',
					'fields' => array(
						'font_link'         => array( 'type' => 'font', 'label' => 'Font', 'default' => '', 'var' => '--sds-font-link', 'empty' => 'var(--sds-font-text)', 'desc' => 'Vuoto = stesso font del testo.' ),
						'peso_link'         => array( 'type' => 'int', 'label' => 'Spessore', 'default' => 400, 'min' => 100, 'max' => 900, 'step' => 100, 'var' => '--sds-w-link' ),
						'dim_link'          => array( 'type' => 'int', 'label' => 'Dimensione – desktop', 'default' => 28, 'min' => 10, 'max' => 80, 'var' => '--sds-fs-link-d' ) + $px,
						'dim_link_tablet'   => array( 'type' => 'int', 'label' => 'Dimensione – tablet', 'default' => 20, 'min' => 10, 'max' => 60, 'var' => '--sds-fs-link-t' ) + $px,
						'dim_link_mobile'   => array( 'type' => 'int', 'label' => 'Dimensione – mobile', 'default' => 20, 'min' => 10, 'max' => 60, 'var' => '--sds-fs-link-m' ) + $px,
						'ls_link'           => array( 'type' => 'float', 'label' => 'Spaziatura lettere', 'default' => 0, 'min' => -10, 'max' => 20, 'step' => 0.1, 'var' => '--sds-ls-link' ) + $px,
						'link_sottolineato' => array( 'type' => 'bool', 'label' => 'Sottolineato', 'default' => 1, 'var' => '--sds-link-deco', 'map' => array( 'none', 'underline' ) ),
					),
				),
				'etichette' => array(
					'title'  => 'Etichette e tabella dati',
					'fields' => array(
						'font_mono'        => array( 'type' => 'font', 'label' => 'Font delle etichette', 'default' => '', 'var' => '--sds-font-mono', 'generic' => SDS_MONO, 'empty' => SDS_MONO, 'desc' => 'Per bollini e nomi dei dati, in maiuscolo. Vuoto = font monospaziato di sistema.' ),
						'dim_etichette'    => array( 'type' => 'int', 'label' => 'Dimensione etichette', 'default' => 12, 'min' => 8, 'max' => 30, 'var' => '--sds-fs-label' ) + $px,
						'dim_valori'       => array( 'type' => 'int', 'label' => 'Dimensione valori – desktop e tablet', 'default' => 32, 'min' => 10, 'max' => 80, 'var' => '--sds-fs-value-d' ) + $px,
						'dim_valori_mobile' => array( 'type' => 'int', 'label' => 'Dimensione valori – mobile', 'default' => 24, 'min' => 10, 'max' => 60, 'var' => '--sds-fs-value-m' ) + $px,
					),
				),
				'caricamento' => array(
					'title'  => 'Caricamento dei font',
					'fields' => array(
						'google_fonts' => array( 'type' => 'bool', 'label' => 'Carica i font da Google Fonts', 'default' => 0, 'desc' => 'Attivalo solo se i font non sono già caricati da Elementor (Elementor → Impostazioni sito → Tipografia o Font personalizzati).' ),
					),
				),
			),
		),
		'layout'     => array(
			'title'  => 'Layout e colori',
			'groups' => array(
				'dimensioni' => array(
					'title'  => 'Dimensioni',
					'fields' => array(
						'altezza'          => array( 'type' => 'int', 'label' => 'Altezza della sezione – desktop', 'default' => 720, 'min' => 300, 'max' => 1400, 'var' => '--sds-h', 'desc' => 'Altezza di colonna testo e immagine da 1200 px di larghezza in su.' ) + $px,
						'altezza_tablet'   => array( 'type' => 'int', 'label' => 'Altezza immagine – tablet', 'default' => 512, 'min' => 200, 'max' => 1200, 'var' => '--sds-h-tab', 'desc' => 'Da 768 a 1199 px. Su mobile l\'immagine è in formato 16:9.' ) + $px,
						'nav_fissa_mobile' => array( 'type' => 'bool', 'label' => 'Titoli delle schede fissi in alto su mobile', 'default' => 1, 'desc' => 'Scorrendo la sezione su mobile e tablet, la barra delle schede resta visibile in alto.' ),
						'nav_offset'       => array( 'type' => 'int', 'label' => 'Distanza dall\'alto della barra fissa', 'default' => 0, 'min' => 0, 'max' => 300, 'var' => '--sds-sticky-top', 'desc' => 'Se il tuo header resta fisso in alto, inserisci qui la sua altezza.' ) + $px,
					),
				),
				'colori'     => array(
					'title'  => 'Colori',
					'fields' => array(
						'colore_attivo'   => array( 'type' => 'color', 'label' => 'Scheda attiva e linea sotto', 'default' => '#6E8075', 'var' => '--sds-active' ),
						'colore_inattivo' => array( 'type' => 'color', 'label' => 'Schede non attive', 'default' => '#C5CCC8', 'var' => '--sds-inactive' ),
						'colore_testo'    => array( 'type' => 'color', 'label' => 'Testo', 'default' => '#3E4A43', 'var' => '--sds-text' ),
						'colore_link'     => array( 'type' => 'color', 'label' => 'Link', 'default' => '#06A34A', 'var' => '--sds-link' ),
						'colore_linee'    => array( 'type' => 'color', 'label' => 'Linee, bordi ed etichette', 'default' => '#9AA39E', 'var' => '--sds-line' ),
						'colore_sfondo'   => array( 'type' => 'color', 'label' => 'Sfondo della barra fissa su mobile', 'default' => '#FFFFFF', 'var' => '--sds-bg' ),
						'colore_partenza' => array( 'type' => 'color', 'label' => 'Animazione testo – colore iniziale', 'default' => '#C7CCCB' ),
						'colore_lampo'    => array( 'type' => 'color', 'label' => 'Animazione testo – colore intermedio', 'default' => '#EAECED' ),
					),
				),
			),
		),
		'animazioni' => array(
			'title'  => 'Animazioni',
			'groups' => array(
				'anim' => array(
					'title'  => 'Animazioni',
					'fields' => array(
						'anim_testo'   => array( 'type' => 'select', 'label' => 'Testo che si accende', 'default' => 'lettera', 'options' => array(
							'lettera' => 'Lettera per lettera (come il riferimento)',
							'parola'  => 'Parola per parola',
							'off'     => 'Disattivata',
						), 'desc' => 'Durante l\'animazione "lettera per lettera" le lettere sono separate; finita l\'animazione il testo torna identico a quello delle altre sezioni.' ),
						'vel_testo'    => array( 'type' => 'int', 'label' => 'Velocità animazione testo', 'default' => 100, 'min' => 25, 'max' => 400, 'unit' => '%', 'desc' => '100 = come il riferimento, 200 = due volte più veloce, 50 = due volte più lenta.' ),
						'anim_mescola' => array( 'type' => 'bool', 'label' => 'Lettere che si mescolano al passaggio del mouse', 'default' => 1, 'desc' => 'Titoli delle schede e dati. Solo su desktop, come nel riferimento.' ),
						'anim_media'   => array( 'type' => 'bool', 'label' => 'Immagine che si apre a tendina', 'default' => 1 ),
					),
				),
			),
		),
	);
}

function sds_flat_fields() {
	$flat = array();
	foreach ( sds_style_pages() as $page ) {
		foreach ( $page['groups'] as $group ) {
			$flat += $group['fields'];
		}
	}
	return $flat;
}

function sds_empty_row() {
	return array( 'etichetta1' => '', 'valore1' => '', 'etichetta2' => '', 'valore2' => '', 'barra' => 0 );
}

function sds_empty_tab() {
	return array(
		'titolo'       => '',
		'testo'        => '',
		'link_testo'   => '',
		'link_url'     => '',
		'nuova_scheda' => 0,
		'immagine'     => 0, // immagine o video della Libreria media
		'etichette'    => '',
		'dati'         => array_fill( 0, SDS_MAX_DATI, sds_empty_row() ),
	);
}

function sds_defaults() {
	$d           = wp_list_pluck( sds_flat_fields(), 'default' );
	$d['schede'] = array_fill( 0, SDS_MAX_SCHEDE, sds_empty_tab() );
	return $d;
}

function sds_get_options() {
	$saved = get_option( SDS_OPTION, array() );
	$saved = is_array( $saved ) ? $saved : array();

	// Fino alla 1.0.x: "anim_testo" era sì/no e le dimensioni "mobile" valevano anche per il tablet.
	if ( isset( $saved['anim_testo'] ) && ! is_string( $saved['anim_testo'] ) ) {
		$saved['anim_testo'] = $saved['anim_testo'] ? 'lettera' : 'off';
	}
	foreach ( array( 'dim_testo', 'dim_schede' ) as $k ) {
		if ( isset( $saved[ $k . '_mobile' ] ) && ! isset( $saved[ $k . '_tablet' ] ) ) {
			$saved[ $k . '_tablet' ] = $saved[ $k . '_mobile' ];
		}
	}

	$o           = wp_parse_args( $saved, sds_defaults() );
	$o['schede'] = sds_normalize_schede( $o['schede'] );
	$lingue      = array();
	foreach ( (array) ( isset( $o['schede_lingue'] ) ? $o['schede_lingue'] : array() ) as $lingua => $schede ) {
		$lingue[ (string) $lingua ] = sds_normalize_schede( $schede );
	}
	$o['schede_lingue'] = $lingue;
	return $o;
}

/** Completa le schede con i valori vuoti mancanti. */
function sds_normalize_schede( $schede ) {
	$schede = is_array( $schede ) ? $schede : array();
	$out    = array();
	for ( $i = 0; $i < SDS_MAX_SCHEDE; $i++ ) {
		$tab = wp_parse_args( isset( $schede[ $i ] ) ? (array) $schede[ $i ] : array(), sds_empty_tab() );
		for ( $r = 0; $r < SDS_MAX_DATI; $r++ ) {
			$tab['dati'][ $r ] = wp_parse_args( isset( $tab['dati'][ $r ] ) ? (array) $tab['dati'][ $r ] : array(), sds_empty_row() );
		}
		$out[ $i ] = $tab;
	}
	return $out;
}

/* -------------------------------------------------------------------------
 * Lingue (facoltativo, con "Elementor Page Translator")
 * Senza il traduttore il plugin funziona come prima: solo i contenuti italiani.
 * ---------------------------------------------------------------------- */

add_action( 'init', function () {
	load_plugin_textdomain( 'sezione-soluzioni', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

/** Lingue diverse da quella predefinita: [ codice => nome ]. */
function sds_lingue() {
	if ( ! function_exists( 'ept_languages' ) || ! function_exists( 'ept_default_language' ) ) {
		return array();
	}
	$out = array();
	foreach ( ept_languages() as $codice => $lingua ) {
		if ( ept_default_language() !== $codice ) {
			$out[ (string) $codice ] = (string) $lingua['name'];
		}
	}
	return $out;
}

/** Nome della lingua predefinita. */
function sds_nome_lingua_predefinita() {
	if ( function_exists( 'ept_languages' ) && function_exists( 'ept_default_language' ) ) {
		$lingue = ept_languages();
		return (string) ( $lingue[ ept_default_language() ]['name'] ?? 'Italiano' );
	}
	return 'Italiano';
}

/** Schede compilate (con titolo). */
function sds_schede_compilate( $schede ) {
	return array_values( array_filter( (array) $schede, function ( $t ) {
		return '' !== $t['titolo'];
	} ) );
}

/**
 * Schede da mostrare: quelle della lingua della pagina; se in quella lingua non ce
 * n'è nessuna compilata, quelle italiane.
 */
function sds_schede_correnti( $o ) {
	if ( function_exists( 'ept_current_language' ) && function_exists( 'ept_default_language' ) ) {
		$lingua = ept_current_language();
		if ( ept_default_language() !== $lingua && ! empty( $o['schede_lingue'][ $lingua ] ) ) {
			$tabs = sds_schede_compilate( $o['schede_lingue'][ $lingua ] );
			if ( $tabs ) {
				return $tabs;
			}
		}
	}
	return sds_schede_compilate( $o['schede'] );
}

function sds_sanitize_value( $f, $v ) {
	switch ( $f['type'] ) {
		case 'bool':
			return empty( $v ) ? 0 : 1;
		case 'int':
			return '' === $v ? $f['default'] : max( $f['min'], min( $f['max'], (int) $v ) );
		case 'float':
			return '' === $v ? $f['default'] : round( max( $f['min'], min( $f['max'], (float) str_replace( ',', '.', (string) $v ) ) ), 3 );
		case 'color':
			$c = sanitize_hex_color( $v );
			return $c ? $c : $f['default'];
		case 'select':
			return isset( $f['options'][ $v ] ) ? $v : $f['default'];
		case 'font': // solo lettere, numeri, spazi e trattini
			return trim( preg_replace( '/[^A-Za-z0-9 \-]/', '', (string) $v ) );
	}
	return $f['default'];
}

function sds_sanitize( $input ) {
	$input = is_array( $input ) ? $input : array();
	$clean = array();

	foreach ( sds_flat_fields() as $key => $f ) {
		$clean[ $key ] = sds_sanitize_value( $f, isset( $input[ $key ] ) ? $input[ $key ] : '' );
	}

	$clean['schede'] = sds_sanitize_schede( isset( $input['schede'] ) ? $input['schede'] : array() );

	// Contenuti nelle altre lingue. Se il form non li contiene (traduttore non attivo)
	// restano quelli salvati.
	if ( isset( $input['schede_lingue'] ) && is_array( $input['schede_lingue'] ) ) {
		$clean['schede_lingue'] = array();
		foreach ( $input['schede_lingue'] as $lingua => $schede ) {
			$lingua = sanitize_key( (string) $lingua );
			if ( '' !== $lingua ) {
				$clean['schede_lingue'][ $lingua ] = sds_sanitize_schede( $schede );
			}
		}
	} else {
		$saved                  = get_option( SDS_OPTION, array() );
		$clean['schede_lingue'] = is_array( $saved ) && isset( $saved['schede_lingue'] ) && is_array( $saved['schede_lingue'] ) ? $saved['schede_lingue'] : array();
	}
	return $clean;
}

/** Sanifica le schede di una lingua. */
function sds_sanitize_schede( $input_schede ) {
	$input_schede = is_array( $input_schede ) ? $input_schede : array();
	$schede       = array();
	for ( $i = 0; $i < SDS_MAX_SCHEDE; $i++ ) {
		$t    = isset( $input_schede[ $i ] ) ? (array) $input_schede[ $i ] : array();
		$get  = function ( $k ) use ( $t ) {
			return isset( $t[ $k ] ) ? $t[ $k ] : '';
		};
		$dati = array();
		for ( $r = 0; $r < SDS_MAX_DATI; $r++ ) {
			$row    = isset( $t['dati'][ $r ] ) ? (array) $t['dati'][ $r ] : array();
			$dati[] = array(
				'etichetta1' => sanitize_text_field( isset( $row['etichetta1'] ) ? $row['etichetta1'] : '' ),
				'valore1'    => sanitize_text_field( isset( $row['valore1'] ) ? $row['valore1'] : '' ),
				'etichetta2' => sanitize_text_field( isset( $row['etichetta2'] ) ? $row['etichetta2'] : '' ),
				'valore2'    => sanitize_text_field( isset( $row['valore2'] ) ? $row['valore2'] : '' ),
				'barra'      => max( 0, min( 100, (int) ( isset( $row['barra'] ) ? $row['barra'] : 0 ) ) ),
			);
		}
		$schede[] = array(
			'titolo'       => sanitize_text_field( $get( 'titolo' ) ),
			'testo'        => wp_kses_post( $get( 'testo' ) ),
			'link_testo'   => sanitize_text_field( $get( 'link_testo' ) ),
			'link_url'     => esc_url_raw( $get( 'link_url' ) ),
			'nuova_scheda' => empty( $t['nuova_scheda'] ) ? 0 : 1,
			'immagine'     => absint( $get( 'immagine' ) ),
			'etichette'    => sanitize_text_field( $get( 'etichette' ) ),
			'dati'         => $dati,
		);
	}
	return $schede;
}

/** Variabili CSS generate dalle impostazioni. */
function sds_css_vars( $o ) {
	$css = '';
	foreach ( sds_flat_fields() as $key => $f ) {
		if ( empty( $f['var'] ) ) {
			continue;
		}
		$v = $o[ $key ];
		switch ( $f['type'] ) {
			case 'font':
				// "inherit" vale solo da solo: in un elenco ("Nome", inherit) invaliderebbe la regola.
				$empty   = isset( $f['empty'] ) ? $f['empty'] : 'inherit';
				$generic = isset( $f['generic'] ) ? $f['generic'] : 'sans-serif';
				$value   = '' === $v ? $empty : '"' . $v . '", ' . $generic;
				break;
			case 'bool':
				$value = isset( $f['map'] ) ? $f['map'][ $v ? 1 : 0 ] : ( $v ? '1' : '0' );
				break;
			case 'int':
			case 'float':
				$value = ( 0 == $v && isset( $f['zero'] ) ) ? $f['zero'] : $v . ( isset( $f['unit'] ) ? $f['unit'] : '' );
				break;
			default:
				$value = $v;
		}
		$css .= $f['var'] . ':' . $value . ';';
	}
	return $css;
}

/* -------------------------------------------------------------------------
 * Pagina di amministrazione
 * ---------------------------------------------------------------------- */

add_action( 'admin_menu', function () {
	add_menu_page( 'Sezione Soluzioni', 'Sezione Soluzioni', 'manage_options', 'sezione-soluzioni', 'sds_render_admin_page', 'dashicons-index-card', 59 );
} );

add_action( 'admin_init', function () {
	register_setting( 'sds_group', SDS_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'sds_sanitize',
		'default'           => sds_defaults(),
	) );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'toplevel_page_sezione-soluzioni' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'sds-admin', SDS_URL . 'assets/admin.css', array(), SDS_VERSION );
	wp_enqueue_script( 'sds-admin', SDS_URL . 'assets/admin.js', array( 'jquery', 'wp-color-picker' ), SDS_VERSION, true );
} );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=sezione-soluzioni' ) ) . '">Impostazioni</a>' );
	return $links;
} );

function sds_render_field( $key, $field, $value ) {
	$id   = 'sds_' . $key;
	$name = SDS_OPTION . '[' . $key . ']';
	$unit = isset( $field['unit'] ) ? $field['unit'] : '';

	switch ( $field['type'] ) {
		case 'int':
		case 'float':
			printf( '<input type="number" id="%s" name="%s" value="%s" min="%s" max="%s" step="%s" class="small-text"> %s',
				esc_attr( $id ), esc_attr( $name ), esc_attr( $value ), esc_attr( $field['min'] ), esc_attr( $field['max'] ),
				esc_attr( isset( $field['step'] ) ? $field['step'] : 1 ), esc_html( $unit ) );
			break;
		case 'bool':
			printf( '<input type="checkbox" id="%s" name="%s" value="1" %s>', esc_attr( $id ), esc_attr( $name ), checked( $value, 1, false ) );
			break;
		case 'select':
			printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $field['options'] as $opt => $label ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $opt ), selected( $value, $opt, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;
		case 'color':
			printf( '<input type="text" id="%s" name="%s" value="%s" class="sds-color" data-default-color="%s">',
				esc_attr( $id ), esc_attr( $name ), esc_attr( $value ), esc_attr( $field['default'] ) );
			break;
		default:
			printf( '<input type="text" id="%s" name="%s" value="%s" class="regular-text">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
	}

	$default = $field['default'];
	if ( 'bool' !== $field['type'] && 'color' !== $field['type'] ) {
		if ( 'select' === $field['type'] ) {
			$default = $field['options'][ $default ];
		} elseif ( '' === $default ) {
			$default = 'vuoto';
		} else {
			$default .= $unit;
		}
		printf( ' <span class="sds-default">predefinito: %s</span>', esc_html( $default ) );
	}
	if ( ! empty( $field['desc'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $field['desc'] ) );
	}
}

/** Anteprima di un allegato (immagine o nome del video) nella pagina impostazioni. */
function sds_media_preview_html( $id ) {
	if ( ! $id ) {
		return '';
	}
	if ( wp_attachment_is( 'video', $id ) ) {
		return '<p><span class="dashicons dashicons-video-alt3"></span> ' . esc_html( basename( (string) get_attached_file( $id ) ) ) . '</p>';
	}
	$url = wp_get_attachment_image_url( $id, 'medium' );
	return $url ? '<img src="' . esc_url( $url ) . '" style="max-width:240px;height:auto;display:block">' : '';
}

function sds_render_tab_fields( $i, $tab, $lingua = '' ) {
	$base     = '' === $lingua ? SDS_OPTION . '[schede][' . $i . ']' : SDS_OPTION . '[schede_lingue][' . $lingua . '][' . $i . ']';
	$pid      = '' === $lingua ? 'sds_t' . $i : 'sds_' . $lingua . '_t' . $i; // id univoci per lingua
	$preview  = sds_media_preview_html( $tab['immagine'] );
	$has_dati = false;
	foreach ( $tab['dati'] as $row ) {
		if ( $row['etichetta1'] || $row['valore1'] || $row['etichetta2'] || $row['valore2'] ) {
			$has_dati = true;
		}
	}
	?>
	<details class="sds-box" data-sds-box="scheda-<?php echo esc_attr( ( '' === $lingua ? '' : $lingua . '-' ) . $i ); ?>">
		<summary>
			<span class="sds-box-title">Scheda <?php echo (int) ( $i + 1 ); ?></span>
			<span class="sds-box-sub" data-empty="vuota, non viene mostrata"><?php echo esc_html( $tab['titolo'] ? $tab['titolo'] : 'vuota, non viene mostrata' ); ?></span>
		</summary>
		<div class="sds-box-body">
			<table class="form-table" role="presentation">
				<tr>
					<th><label for="<?php echo esc_attr( $pid ); ?>_titolo">Titolo della scheda</label></th>
					<td><input type="text" class="regular-text sds-tab-title" id="<?php echo esc_attr( $pid ); ?>_titolo" name="<?php echo esc_attr( $base ); ?>[titolo]" value="<?php echo esc_attr( $tab['titolo'] ); ?>" placeholder="es. Primo Impianto"></td>
				</tr>
				<tr>
					<th><label for="<?php echo esc_attr( $pid ); ?>_testo">Testo</label></th>
					<td>
						<textarea class="large-text" rows="5" id="<?php echo esc_attr( $pid ); ?>_testo" name="<?php echo esc_attr( $base ); ?>[testo]"><?php echo esc_textarea( $tab['testo'] ); ?></textarea>
						<p class="description">Una riga vuota crea un nuovo paragrafo.</p>
					</td>
				</tr>
				<tr>
					<th><label for="<?php echo esc_attr( $pid ); ?>_lt">Link</label></th>
					<td>
						<input type="text" id="<?php echo esc_attr( $pid ); ?>_lt" name="<?php echo esc_attr( $base ); ?>[link_testo]" value="<?php echo esc_attr( $tab['link_testo'] ); ?>" placeholder="Testo, es. Scopri di più">
						<input type="url" class="regular-text" name="<?php echo esc_attr( $base ); ?>[link_url]" value="<?php echo esc_attr( $tab['link_url'] ); ?>" placeholder="https://…">
						<label><input type="checkbox" name="<?php echo esc_attr( $base ); ?>[nuova_scheda]" value="1" <?php checked( $tab['nuova_scheda'], 1 ); ?>> Apri in una nuova scheda</label>
					</td>
				</tr>
				<tr>
					<th>Immagine o video</th>
					<td class="sds-media">
						<input type="hidden" class="sds-media-id" name="<?php echo esc_attr( $base ); ?>[immagine]" value="<?php echo (int) $tab['immagine']; ?>">
						<div class="sds-media-preview" style="margin-bottom:8px"><?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<button type="button" class="button sds-media-select">Scegli dalla Libreria media</button>
						<button type="button" class="button-link-delete sds-media-remove" <?php echo $preview ? '' : 'style="display:none"'; ?>>Rimuovi</button>
						<p class="description">Un'immagine oppure un video MP4 (parte da solo, senza audio, in loop).</p>
					</td>
				</tr>
				<tr>
					<th><label for="<?php echo esc_attr( $pid ); ?>_tag">Etichette</label></th>
					<td>
						<input type="text" class="large-text" id="<?php echo esc_attr( $pid ); ?>_tag" name="<?php echo esc_attr( $base ); ?>[etichette]" value="<?php echo esc_attr( $tab['etichette'] ); ?>" placeholder="es. Pistoni, Cilindri, Bielle">
						<p class="description">Facoltative. Separate da virgola, compaiono come piccoli bollini sotto l'immagine.</p>
					</td>
				</tr>
				<tr>
					<th>Tabella dati</th>
					<td>
						<details <?php echo $has_dati ? 'open' : ''; ?>>
							<summary style="cursor:pointer">Facoltativa – fino a <?php echo (int) SDS_MAX_DATI; ?> righe</summary>
							<p class="description">Ogni riga ha due celle: la prima con etichetta e valore, la seconda con etichetta, valore e una barra (0–100%). Le righe vuote non vengono mostrate.</p>
							<table class="widefat" style="max-width:820px;margin-top:8px">
								<thead><tr><th>Etichetta 1</th><th>Valore 1</th><th>Etichetta 2</th><th>Valore 2</th><th>Barra %</th></tr></thead>
								<tbody>
								<?php foreach ( $tab['dati'] as $r => $row ) : $rb = $base . '[dati][' . $r . ']'; ?>
									<tr>
										<td><input type="text" name="<?php echo esc_attr( $rb ); ?>[etichetta1]" value="<?php echo esc_attr( $row['etichetta1'] ); ?>" placeholder="es. Anni di esperienza" style="width:100%"></td>
										<td><input type="text" name="<?php echo esc_attr( $rb ); ?>[valore1]" value="<?php echo esc_attr( $row['valore1'] ); ?>" placeholder="es. 40+" style="width:100%"></td>
										<td><input type="text" name="<?php echo esc_attr( $rb ); ?>[etichetta2]" value="<?php echo esc_attr( $row['etichetta2'] ); ?>" placeholder="es. Paesi serviti" style="width:100%"></td>
										<td><input type="text" name="<?php echo esc_attr( $rb ); ?>[valore2]" value="<?php echo esc_attr( $row['valore2'] ); ?>" placeholder="es. 60" style="width:100%"></td>
										<td><input type="number" min="0" max="100" name="<?php echo esc_attr( $rb ); ?>[barra]" value="<?php echo (int) $row['barra']; ?>" class="small-text"></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</details>
					</td>
				</tr>
			</table>
		</div>
	</details>
	<?php
}

function sds_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$o     = sds_get_options();
	$pages = array( 'contenuti' => 'Contenuti' ) + wp_list_pluck( sds_style_pages(), 'title' );
	?>
	<div class="wrap sds-admin">
		<h1>Sezione Soluzioni</h1>
		<p>Inserisci la sezione nella pagina con il widget <strong>Shortcode</strong> di Elementor: <code>[<?php echo esc_html( SDS_SHORTCODE ); ?>]</code></p>
		<?php settings_errors(); ?>

		<nav class="nav-tab-wrapper sds-nav">
			<?php foreach ( $pages as $id => $title ) : ?>
				<a href="#<?php echo esc_attr( $id ); ?>" class="nav-tab" data-sds-page="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php">
			<?php settings_fields( 'sds_group' ); ?>

			<div class="sds-page" data-sds-page="contenuti">
				<div class="sds-toolbar">
					<p>Fino a <?php echo (int) SDS_MAX_SCHEDE; ?> schede, mostrate in quest'ordine. Le schede senza titolo non vengono mostrate.</p>
					<p><button type="button" class="button-link sds-open-all">Apri tutte</button> · <button type="button" class="button-link sds-close-all">Chiudi tutte</button></p>
				</div>
				<?php $lingue = sds_lingue(); ?>
				<?php if ( $lingue ) : ?>
					<div class="sds-lingue" role="group" aria-label="Lingua dei contenuti">
						<button type="button" class="button sds-lingua-btn is-active" data-sds-lingua=""><?php echo esc_html( sds_nome_lingua_predefinita() ); ?></button>
						<?php foreach ( $lingue as $codice => $nome ) : ?>
							<button type="button" class="button sds-lingua-btn" data-sds-lingua="<?php echo esc_attr( $codice ); ?>"><?php echo esc_html( $nome ); ?></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="sds-lingua-blocco is-active" data-sds-lingua="">
					<?php
					foreach ( $o['schede'] as $i => $tab ) {
						sds_render_tab_fields( $i, $tab );
					}
					?>
				</div>
				<?php foreach ( $lingue as $codice => $nome ) : ?>
					<div class="sds-lingua-blocco" data-sds-lingua="<?php echo esc_attr( $codice ); ?>">
						<p class="description">
							Contenuti mostrati sulle pagine in <?php echo esc_html( $nome ); ?>. Se qui non compili nessuna scheda, in questa lingua vengono mostrate quelle italiane.
							<button type="button" class="button-link sds-copia-it">Copia i contenuti italiani da tradurre</button>
						</p>
						<?php
						$schede_lingua = isset( $o['schede_lingue'][ $codice ] ) ? $o['schede_lingue'][ $codice ] : sds_normalize_schede( array() );
						foreach ( $schede_lingua as $i => $tab ) {
							sds_render_tab_fields( $i, $tab, $codice );
						}
						?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php foreach ( sds_style_pages() as $page_id => $page ) : ?>
				<div class="sds-page" data-sds-page="<?php echo esc_attr( $page_id ); ?>">
					<div class="sds-toolbar">
						<p></p>
						<p><button type="button" class="button-link sds-open-all">Apri tutte</button> · <button type="button" class="button-link sds-close-all">Chiudi tutte</button></p>
					</div>
					<?php foreach ( $page['groups'] as $group_id => $group ) : ?>
						<details class="sds-box" data-sds-box="<?php echo esc_attr( $group_id ); ?>">
							<summary>
								<span class="sds-box-title"><?php echo esc_html( $group['title'] ); ?></span>
								<span class="sds-box-sub"><?php echo (int) count( $group['fields'] ); ?> impostazioni</span>
							</summary>
							<div class="sds-box-body">
								<?php if ( ! empty( $group['desc'] ) ) : ?>
									<p class="description"><?php echo esc_html( $group['desc'] ); ?></p>
								<?php endif; ?>
								<table class="form-table" role="presentation">
									<?php foreach ( $group['fields'] as $key => $field ) : ?>
										<tr>
											<th><label for="sds_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
											<td><?php sds_render_field( $key, $field, $o[ $key ] ); ?></td>
										</tr>
									<?php endforeach; ?>
								</table>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<div class="sds-savebar">
				<?php submit_button( 'Salva', 'primary', 'submit', false ); ?>
				<span class="description">Salva le modifiche di tutte le schede del menu.</span>
			</div>
		</form>
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 * Frontend
 * ---------------------------------------------------------------------- */

function sds_page_has_shortcode() {
	if ( ! is_singular() ) {
		return false;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return false;
	}
	if ( has_shortcode( $post->post_content, SDS_SHORTCODE ) ) {
		return true;
	}
	$data = get_post_meta( $post->ID, '_elementor_data', true );
	return is_string( $data ) && false !== strpos( $data, SDS_SHORTCODE );
}

/**
 * Indirizzo Google Fonts con solo gli spessori (e il corsivo) effettivamente usati:
 * se si chiede uno spessore che il font non ha, Google rifiuta l'intera richiesta.
 */
function sds_google_fonts_url( $o ) {
	if ( ! $o['google_fonts'] ) {
		return '';
	}
	$italic = 'italic' === $o['stile_testo'];
	$link   = '' === $o['font_link'] ? $o['font_testo'] : $o['font_link'];
	$uses   = array(
		array( $o['font_schede'], $o['peso_schede'], false ),
		array( $o['font_testo'], $o['peso_testo'], $italic ),
		array( $link, $o['peso_link'], $italic && '' === $o['font_link'] ),
		array( $o['font_mono'], 400, false ),
		array( $o['font_testo'], $o['peso_testo'], false ), // valori della tabella dati
	);
	$families = array();
	foreach ( $uses as $use ) {
		list( $family, $weight, $ital ) = $use;
		if ( '' === $family ) {
			continue;
		}
		$families[ $family ][ ( $ital ? '1,' : '0,' ) . (int) $weight ] = true;
	}
	if ( ! $families ) {
		return '';
	}
	$parts = array();
	foreach ( $families as $family => $axes ) {
		$axes = array_keys( $axes );
		sort( $axes );
		$parts[] = 'family=' . str_replace( ' ', '+', $family ) . ':ital,wght@' . implode( ';', $axes );
	}
	return 'https://fonts.googleapis.com/css2?' . implode( '&', $parts ) . '&display=swap';
}

add_action( 'wp_enqueue_scripts', function () {
	$o = sds_get_options();
	wp_register_style( 'sds-sezione', SDS_URL . 'assets/sezione.css', array(), SDS_VERSION );
	wp_register_script( 'sds-sezione', SDS_URL . 'assets/sezione.js', array(), SDS_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	$fonts = sds_google_fonts_url( $o );
	if ( $fonts ) {
		wp_register_style( 'sds-fonts', $fonts, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	$elementor_preview = isset( $_GET['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( $elementor_preview || sds_page_has_shortcode() ) {
		wp_enqueue_style( 'sds-sezione' );
		if ( $fonts ) {
			wp_enqueue_style( 'sds-fonts' );
		}
	}
	if ( $elementor_preview ) {
		wp_enqueue_script( 'sds-sezione' );
	}
} );

add_shortcode( SDS_SHORTCODE, 'sds_shortcode' );

/** Immagine o video della scheda. */
function sds_media_html( $id ) {
	if ( ! $id ) {
		return '';
	}
	if ( wp_attachment_is( 'video', $id ) ) {
		$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		return sprintf( '<video class="sds__img" src="%s" muted loop playsinline preload="metadata"%s></video>',
			esc_url( wp_get_attachment_url( $id ) ), $alt ? ' aria-label="' . esc_attr( $alt ) . '"' : ' aria-hidden="true"' );
	}
	return wp_get_attachment_image( $id, 'large', false, array(
		'class'   => 'sds__img',
		'loading' => 'lazy',
		'sizes'   => '(max-width: 767px) 100vw, 50vw',
	) );
}

function sds_shortcode() {
	static $instance = 0;
	$o    = sds_get_options();
	$tabs = sds_schede_correnti( $o );

	if ( ! $tabs ) {
		return current_user_can( 'edit_posts' )
			? '<p class="sds-vuoto">' . esc_html__( 'Sezione Soluzioni: nessuna scheda compilata. Vai in Bacheca → Sezione Soluzioni. (Messaggio visibile solo agli amministratori.)', 'sezione-soluzioni' ) . '</p>'
			: '';
	}

	wp_enqueue_style( 'sds-sezione' );
	wp_enqueue_script( 'sds-sezione' );
	if ( wp_style_is( 'sds-fonts', 'registered' ) ) {
		wp_enqueue_style( 'sds-fonts' );
	}

	$instance++;
	$uid = 'sds-' . $instance;

	$config = array(
		'shuffle'   => (bool) $o['anim_mescola'],
		'reveal'    => $o['anim_testo'],
		'speed'     => $o['vel_testo'] / 100,
		'wipe'      => (bool) $o['anim_media'],
		'colors'    => array( $o['colore_partenza'], $o['colore_lampo'], $o['colore_testo'] ),
	);

	$classes = array( 'sds' );
	if ( $o['nav_fissa_mobile'] ) {
		$classes[] = 'sds--sticky';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" id="<?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( sds_css_vars( $o ) ); ?>" data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
		<div class="sds__nav">
			<div class="sds__tabs" role="tablist">
				<?php foreach ( $tabs as $i => $t ) : ?>
					<button type="button" class="sds__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab"
						id="<?php echo esc_attr( $uid . '-tab-' . $i ); ?>"
						aria-controls="<?php echo esc_attr( $uid . '-panel-' . $i ); ?>"
						aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>"><span class="sds__shuffle" data-sds-shuffle><?php echo esc_html( $t['titolo'] ); ?></span></button>
				<?php endforeach; ?>
				<span class="sds__underline" aria-hidden="true"></span>
			</div>
		</div>
		<?php foreach ( $tabs as $i => $t ) : ?>
			<?php
			$tags = array_filter( array_map( 'trim', explode( ',', $t['etichette'] ) ) );
			$rows = array_values( array_filter( $t['dati'], function ( $r ) {
				return $r['etichetta1'] || $r['valore1'] || $r['etichetta2'] || $r['valore2'];
			} ) );
			$link = $t['link_testo'] && $t['link_url'];
			?>
			<div class="sds__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel"
				id="<?php echo esc_attr( $uid . '-panel-' . $i ); ?>"
				aria-labelledby="<?php echo esc_attr( $uid . '-tab-' . $i ); ?>"
				<?php echo 0 === $i ? '' : 'hidden'; ?>>
				<div class="sds__left">
					<div class="sds__text"><?php echo wp_kses_post( wpautop( $t['testo'] ) ); ?></div>
					<?php if ( $rows || $link ) : ?>
						<div class="sds__bottom">
							<?php if ( $rows ) : ?>
								<div class="sds__stats">
									<?php foreach ( $rows as $r => $row ) : ?>
										<div class="sds__row<?php echo count( $rows ) - 1 === $r ? ' is-last' : ''; ?>">
											<div class="sds__cell" data-delay="<?php echo esc_attr( 2 * $r * 0.08 ); ?>">
												<span class="sds__label" data-sds-shuffle><?php echo esc_html( $row['etichetta1'] ); ?></span>
												<span class="sds__value" data-sds-shuffle><?php echo esc_html( $row['valore1'] ); ?></span>
											</div>
											<div class="sds__cell sds__cell--meter" data-delay="<?php echo esc_attr( ( 2 * $r + 1 ) * 0.08 ); ?>">
												<div class="sds__bar" data-width="<?php echo (int) $row['barra']; ?>" style="width:<?php echo (int) $row['barra']; ?>%"></div>
												<div class="sds__meter-text">
													<span class="sds__label" data-sds-shuffle><?php echo esc_html( $row['etichetta2'] ); ?></span>
													<span class="sds__value" data-sds-shuffle><?php echo esc_html( $row['valore2'] ); ?></span>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<?php if ( $link ) : ?>
								<a class="sds__link" href="<?php echo esc_url( $t['link_url'] ); ?>"<?php echo $t['nuova_scheda'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $t['link_testo'] ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="sds__right">
					<div class="sds__media">
						<div class="sds__wipe"><?php echo sds_media_html( $t['immagine'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					</div>
					<?php if ( $tags ) : ?>
						<div class="sds__tags">
							<?php foreach ( $tags as $tag ) : ?>
								<span class="sds__tag"><?php echo esc_html( $tag ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
