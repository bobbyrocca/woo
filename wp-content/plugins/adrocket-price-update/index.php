<?php
/**
 * Plugin Name: Adrocket Product Price Update
 * Description: Aggiorni prezzi i prezzi in product page in base alla quantità selezionata.
 * Version: 1.0
 * Author: Halexo Limited
 */

function get_updated_price_callback() {
	$product_id    = $_POST['product_id'];
	$quantity      = 0 + $_POST['quantity'];
	$variation_ids = $_POST['variation_ids'] ?? array();

	$total_regular_price = 0;
	$total_sale_price    = 0;

	if ( empty( $variation_ids ) ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			echo json_encode( array( 'error' => 'Prodotto non trovato' ) );
			wp_die();
		}

		// Prezzi basati sulla quantità
		$price_for_one   = get_post_meta( $product_id, 'qty_based_price_1', true );
		$price_for_two   = get_post_meta( $product_id, 'qty_based_price_2', true );
		$price_for_three = get_post_meta( $product_id, 'qty_based_price_3', true );

		// Calcola il prezzo in base alla quantità
		if ( $quantity == 1 && ! empty( $price_for_one ) ) {
			$sale_price = $price_for_one;
		} elseif ( $quantity == 2 && ! empty( $price_for_two ) ) {
			$sale_price = $price_for_two;
		} elseif ( $quantity >= 3 && ! empty( $price_for_three ) ) {
			$sale_price = $price_for_three + $price_for_three * ($quantity - 3) / 3;
		} else {
			$sale_price = $product->get_sale_price() ?: $product->get_regular_price();
		}

		// Calcola il prezzo per il prodotto principale
		$total_regular_price = floatval( $product->get_regular_price() * $quantity );
		$total_sale_price    = floatval( $sale_price );
	} else {

		$quantity = count( $variation_ids );

		foreach ( $variation_ids as $variation_id ) {
			$product = wc_get_product( $variation_id );

			if ( ! $product ) {
				continue; // Salta se il prodotto non esiste
			}

			$id_to_use = $variation_id ?: $product_id;

			// Prezzi basati sulla quantità per la variante
			$price_for_one   = get_post_meta( $id_to_use, 'qty_based_price_1', true );
			$price_for_two   = get_post_meta( $id_to_use, 'qty_based_price_2', true );
			$price_for_three = get_post_meta( $id_to_use, 'qty_based_price_3', true );

			// Calcola il prezzo in base alla quantità

			if ( $quantity == 1 && ! empty( $price_for_one ) ) {
				$sale_price = $price_for_one;
			} elseif ( $quantity == 2 && ! empty( $price_for_two ) ) {
				$sale_price = $price_for_two;
			} elseif ( $quantity >= 3 && ! empty( $price_for_three ) ) {
				$sale_price = $price_for_three + $price_for_three * ($quantity - 3) / 3;
			} else {
				$sale_price = $product->get_sale_price() ?: $product->get_regular_price();
			}

			$regular_price = $product->get_regular_price();

			// Calcola il prezzo per ogni variante (considerando una quantità di 1 per variante)
			$total_regular_price += $regular_price;
			$total_sale_price    += $sale_price / $quantity;
		}

		$total_regular_price = floatval( $total_regular_price );
		$total_sale_price    = floatval( $total_sale_price );
	}

	// Costruisci e invia la risposta JSON
	$response = array(
		'wp_sale_price'       => wc_price( $total_sale_price ),
		'wp_regular_price'    => wc_price( $total_regular_price ),
		'sale_price'          => $total_sale_price,
		'regular_price'       => $total_regular_price,
		'unit_sale_price'     => $total_sale_price / $quantity,
		'unit_regular_price'  => $total_regular_price / $quantity,
		'quantity'            => $quantity,
		'discount_percentage' => $total_regular_price > 0 ? floor( ( ( $total_regular_price - $total_sale_price ) / $total_regular_price ) * 100 ) : 0,
		'saving'              => $total_regular_price - $total_sale_price
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







