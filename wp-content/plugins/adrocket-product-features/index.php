<?php
/**
 * Plugin Name: Adrocket Product Features Accordion
 * Description: Mostra le caratteristiche del prodotto in un accordion.
 * Version: 1.0
 * Author: Halexo Limited
 */
// Prevenire accesso diretto al file.
defined( 'ABSPATH' ) || exit;

function adrocket_features_enqueue_scripts() {
	if ( is_product() ) {
		wp_enqueue_script( 'adrocket-features-js', plugin_dir_url( __FILE__ ) . 'js/features.js', array( 'jquery' ), microtime(), true );
	}
}

add_action( 'wp_enqueue_scripts', 'adrocket_features_enqueue_scripts' );

function adrocket_features_enqueue_css() {
	wp_enqueue_style( 'adrocket-features-css', plugin_dir_url( __FILE__ ) . 'css/style.css?v=' . microtime() );
}

add_action( 'wp_enqueue_scripts', 'adrocket_features_enqueue_css' );

// Crea i meta_box personalizzati
function crea_meta_box_prodotto() {
	add_meta_box(
		'custom_features_meta_box', // ID del meta_box
		'Caratteristiche Personalizzate del Prodotto', // Titolo del meta_box
		'mostra_meta_box_prodotto', // Funzione di callback per il contenuto del meta_box
		'product', // Post type (in questo caso, prodotto di WooCommerce)
		'normal', // Posizione del meta_box
		'high' // Priorità del meta_box
	);
}

add_action( 'add_meta_boxes', 'crea_meta_box_prodotto' );

// Mostra il contenuto del meta_box
function mostra_meta_box_prodotto( $post ) {
	// Utilizza nonce per la verifica
	wp_nonce_field( basename( __FILE__ ), 'custom_features_nonce' );

	// Recupera i valori correnti dei metadati
	$valori = array(
		'specifiche_prodotto' => get_post_meta( $post->ID, 'specifiche_prodotto', true ),
		'spedizione'          => get_post_meta( $post->ID, 'spedizione', true ),
		'cosa_include'        => get_post_meta( $post->ID, 'cosa_include', true ),
		'garanzia'            => get_post_meta( $post->ID, 'garanzia', true ),
		'valori'              => get_post_meta( $post->ID, 'valori', true )
	);

	// Crea gli editor per ogni campo
	foreach ( $valori as $chiave => $valore ) {
		echo '<label for="' . $chiave . '"><strong>' . ucfirst( str_replace( '_', ' ', $chiave ) ) . '</strong></label>';
		wp_editor( htmlspecialchars_decode( $valore ), $chiave, array(
			'textarea_name' => $chiave,
			'textarea_rows' => 5,
			'media_buttons' => false
		) );
		echo '<br>';
	}
}

// Salva i dati del meta_box
function salva_meta_box_prodotto( $post_id ) {
	// Controlla il nonce, i permessi, ecc.

	$campi = [ 'specifiche_prodotto', 'spedizione', 'cosa_include', 'garanzia', 'valori' ];

	foreach ( $campi as $campo ) {
		if ( isset( $_POST[ $campo ] ) ) {
			// wp_kses_post permette tag HTML sicuri, inclusi <p>
			update_post_meta( $post_id, $campo, wp_kses_post( $_POST[ $campo ] ) );
		}
	}
}

add_action( 'save_post', 'salva_meta_box_prodotto' );

// Aggiungi accordion alla pagina del prodotto
function aggiungi_accordion_prodotto() {
	global $post;

	// Percorsi delle icone SVG nel tuo plugin
	$icons = [
		'specifiche_prodotto' => plugins_url('images/features.svg', __FILE__),
		'spedizione' => plugins_url('images/shipping.svg', __FILE__),
		'cosa_include' => plugins_url('images/parcel.svg', __FILE__),
		'garanzia' => plugins_url('images/locker.svg', __FILE__),
		'valori' => plugins_url('images/values.svg', __FILE__),
        'chevron' => plugins_url('images/chevron-down.svg', __FILE__) // Percorso dell'icona chevron
	];

	// Titoli delle sezioni dell'accordion
	$titoli = [
		'specifiche_prodotto' => 'Specifiche prodotto',
		'spedizione' => 'Spedizione',
		'cosa_include' => 'Cosa include il pacco',
		'garanzia' => 'Garanzia acquisto sicuro',
		'valori' => 'I nostri valori'
    ];

	// Inizio dell'accordion
	echo '<div class="product__accordion accordion">';

	foreach ($titoli as $chiave => $titolo) {
		// Contenuto per ogni sezione dell'accordion
		$contenuto_sezione = get_post_meta($post->ID, $chiave, true);

		if(empty($contenuto_sezione)) {
			continue;
		}
		// $details_class = ($chiave === 'specifiche_prodotto') ? 'first' : 'none';

		echo '<details>';
		echo '<summary>';
		echo '<div class="summary__title">';
		echo '<div class="summary-img-title">';
		// Icona SVG
		if (isset($icons[$chiave])) {
			echo '<img src="' . esc_url($icons[$chiave]) . '" class="icon icon-accordion" alt="Icona">';
		}

		// Titolo della sezione
		echo '<span class="accordion-text"><strong>' . esc_html($titolo) . '</strong></span>';
		echo '</div>';
		if (isset($icons['chevron'])) {
			echo '<img src="' . esc_url($icons['chevron']) . '" class="icon icon-chevron" alt="Chevron">';
		}
		echo '</div>'; // Chiusura .summary__title
		echo '</summary>';

		// Contenuto della sezione
		echo '<div class="accordion-content">' . wp_kses_post($contenuto_sezione) . '</div>';
		echo '</details>';
	}

	echo '</div>'; // Chiusura .product__accordion
}



add_action( 'woocommerce_single_product_summary', 'aggiungi_accordion_prodotto', 60 );

