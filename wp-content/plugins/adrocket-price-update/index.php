<?php
/**
 * Plugin Name: Adrocket Product Price Update
 * Description: Aggiorni prezzi speciali in base alla quantità per i prodotti WooCommerce.
 * Version: 1.0
 * Author: Halexo Limited
 */

function get_updated_price_callback() {
	$product_id   = $_POST['product_id'];
	$quantity     = $_POST['quantity'];
	$variation_id = $_POST['variation_id'] ?? null;

	// Ottieni l'oggetto prodotto
	$product = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );


	if ( ! $product ) {
		wp_die(); // Termina se il prodotto non esiste
	}

	// Utilizza l'ID della variante se disponibile, altrimenti l'ID del prodotto
	$id_to_use = $variation_id ?: $product_id;

	// Prezzi basati sulla quantità
	$price_for_one   = get_post_meta( $id_to_use, 'qty_based_price_1', true );
	$price_for_two   = get_post_meta( $id_to_use, 'qty_based_price_2', true );
	$price_for_three = get_post_meta( $id_to_use, 'qty_based_price_3', true );

	// Definisci il prezzo regolare e il prezzo di vendita
	$regular_price = $product->get_regular_price(); // Prezzo regolare
	$sale_price    = $regular_price; // Inizializza sale_price con il prezzo regolare

	// Calcola il prezzo in base alla quantità
	if ( $quantity == 1 && ! empty( $price_for_one ) ) {
		$sale_price = $price_for_one;
	} elseif ( $quantity == 2 && ! empty( $price_for_two ) ) {
		$sale_price = $price_for_two / 2;
	} elseif ( $quantity >= 3 && ! empty( $price_for_three ) ) {
		$sale_price = $price_for_three / 3;
	}

	// Costruisci e invia la risposta JSON
	$response = array(
		'sale_price'         => wc_price( $sale_price * $quantity ),
		'regular_price'      => wc_price( $regular_price * $quantity ),
		'unit_sale_price'    => wc_price( $sale_price ),
		'unit_regular_price' => wc_price( $regular_price  ),
		'quantity'           => $quantity
	);

	echo json_encode( $response );
	wp_die(); // Termina correttamente la richiesta AJAX
}

add_action( 'wp_ajax_get_updated_price', 'get_updated_price_callback' );
add_action( 'wp_ajax_nopriv_get_updated_price', 'get_updated_price_callback' );

function adrocket_enqueue_scripts() {
	// Verifica se la funzione is_product esiste e se la pagina corrente è una pagina di prodotto
	if ( function_exists( 'is_product' ) && is_product() ) {
		global $product;

		wp_enqueue_script( 'adrocket-price-js', plugin_dir_url( __FILE__ ) . 'js/price.js', array( 'jquery' ), microtime(), true );

		wp_localize_script( 'adrocket-price-js', 'adrocket_ajax_object', array(
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'product_id' => $product->get_id()
		) );
	}
}

add_action( 'wp_enqueue_scripts', 'adrocket_enqueue_scripts' );







