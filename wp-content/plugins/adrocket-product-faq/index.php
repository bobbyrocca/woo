<?php
/**
 * Plugin Name: Adrocket Product FAQ Accordion
 * Description: Mostra le FAQ del prodotto in un accordion personalizzabile, con un massimo di sei FAQ.
 * Version: 1.0
 * Author: Halexo Limited
 */
defined( 'ABSPATH' ) || exit;

// Enqueue Scripts and Styles
function adrocket_faq_enqueue_assets() {
	if ( is_product() ) {
		wp_enqueue_script( 'adrocket-faq-js', plugin_dir_url( __FILE__ ) . 'js/faq.js', array( 'jquery' ), microtime(), true );
		wp_enqueue_style( 'adrocket-faq-css', plugin_dir_url( __FILE__ ) . 'css/style.css?v=' . microtime() );
	}
}

add_action( 'wp_enqueue_scripts', 'adrocket_faq_enqueue_assets' );

// Create Custom Meta Box
function crea_meta_box_faq_prodotto() {
	add_meta_box(
		'custom_faq_meta_box',
		'FAQ del Prodotto',
		'mostra_meta_box_faq_prodotto',
		'product',
		'normal',
		'high'
	);
}

add_action( 'add_meta_boxes', 'crea_meta_box_faq_prodotto' );

// Display Meta Box Content
function mostra_meta_box_faq_prodotto( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'custom_faq_nonce' );

	for ( $i = 1; $i <= 6; $i ++ ) {
		$faq_title   = get_post_meta( $post->ID, 'faq_' . $i . '_title', true );
		$faq_content = get_post_meta( $post->ID, 'faq_' . $i . '_content', true );

		echo '<p><strong>FAQ ' . $i . ' Titolo:</strong></p>';
		echo '<input type="text" name="faq_' . $i . '_title" value="' . esc_attr( $faq_title ) . '" style="width:100%;">';

		echo '<p><strong>FAQ ' . $i . ' Contenuto:</strong></p>';
		wp_editor( htmlspecialchars_decode( $faq_content ), 'faq_' . $i . '_content', [
			'textarea_name' => 'faq_' . $i . '_content',
			'textarea_rows' => 5,
			'media_buttons' => false
		] );
		echo '<br>';
	}
}

// Save Meta Box Data
function salva_meta_box_faq_prodotto( $post_id ) {
	if ( ! isset( $_POST['custom_faq_nonce'] ) || ! wp_verify_nonce( $_POST['custom_faq_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	for ( $i = 1; $i <= 6; $i ++ ) {
		if ( isset( $_POST[ 'faq_' . $i . '_title' ] ) ) {
			update_post_meta( $post_id, 'faq_' . $i . '_title', sanitize_text_field( $_POST[ 'faq_' . $i . '_title' ] ) );
		}

		if ( isset( $_POST[ 'faq_' . $i . '_content' ] ) ) {
			update_post_meta( $post_id, 'faq_' . $i . '_content', wp_kses_post( $_POST[ 'faq_' . $i . '_content' ] ) );
		}
	}
}

add_action( 'save_post', 'salva_meta_box_faq_prodotto' );

// Add FAQ Accordion to Product Page
function aggiungi_faq_accordion_prodotto() {
	global $post;

	$icons = [
		'mark'    => plugins_url( 'images/mark.svg', __FILE__ ),
		'chevron' => plugins_url( 'images/chevron-down.svg', __FILE__ ) // Percorso dell'icona chevron
	];

	echo '<h2 class="faq">Domande Frequenti</h2>';
	echo '<div class="product__accordion accordion faq">';

	for ( $i = 1; $i <= 6; $i ++ ) {
		$faq_title   = get_post_meta( $post->ID, 'faq_' . $i . '_title', true );
		$faq_content = get_post_meta( $post->ID, 'faq_' . $i . '_content', true );

		if ( ! empty( $faq_title ) && ! empty( $faq_content ) ) {
			echo '<details class="faq">';
			echo '<summary>';
			echo '<div class="summary__title">';
			echo '<div class="summary-img-title">';
			echo '<img src="' . esc_url( $icons['mark'] ) . '" class="icon icon-accordion" alt="Icona">';
			echo '<span class="accordion-text">' . esc_html( $faq_title ) . '</span>';
			echo '</div>';
			echo '<img src="' . esc_url( $icons['chevron'] ) . '" class="icon icon-chevron" alt="Chevron">';
			echo '</div>'; // Chiusura .summary__title
			echo '</summary>';
			echo '<div class="accordion-content">' . wp_kses_post( $faq_content ) . '</div>';
			echo '</details>';
		}
	}

	echo '</div>';
}

add_action( 'woocommerce_product_additional_information', 'aggiungi_faq_accordion_prodotto', 60 );