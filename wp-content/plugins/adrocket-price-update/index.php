<?php
/**
 * Plugin Name: Adrocket Product Price Update
 * Description: Aggiorni prezzi i prezzi in product page in base alla quantità selezionata.
 * Version: 1.0
 * Author: Halexo Limited
 */

// Prevenire accesso diretto al file.
defined( 'ABSPATH' ) || exit;

function adrocket_enqueue_scripts( $product_id ) {

	echo '<script>console.log("adrocket_enqueue_scripts");</script>';

	echo '<script>console.log("product: ' . $product_id . '");</script>';

	// Verifica se la funzione is_product esiste e se la pagina corrente è una pagina di prodotto
	if ( function_exists( 'is_product' ) && is_product() ) {

		wp_enqueue_script( 'adrocket-price-js', plugin_dir_url( __FILE__ ) . 'js/price.js', array( 'jquery' ), microtime(), true );

		wp_localize_script( 'adrocket-price-js', 'adrocket_ajax_object', array(
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'product_id' => $product_id
		) );
	}
}

add_action( 'wp_enqueue_scripts', function () {
	$product = wc_get_product( get_the_ID() ); // Get the product object
	if ( $product ) {
		adrocket_enqueue_scripts( $product->get_id() );
	}
} );

function get_updated_price_callback() {
	$product_id    = $_POST['product_id'];
	$quantity      = 0 + $_POST['quantity'];
	$variation_ids = $_POST['variation_ids'] ?? array();

	$total_regular_price = 0;
	$total_sale_price    = 0;

	$bundle_policy = get_post_meta( $product_id, 'bundle_policy', true );

	if ( empty( $variation_ids ) ) {
		$product_data = wc_get_product( $product_id );

		if ( ! $product_data ) {
			echo json_encode( array( 'error' => 'Prodotto non trovato' ) );
			wp_die();
		}

		if ( '1' === $bundle_policy ) {

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
				$sale_price = $price_for_three + $price_for_three * ( $quantity - 3 ) / 3;
			} else {
				$sale_price = $product_data->get_sale_price() ?: $product_data->get_regular_price();
			}

			// Calcola il prezzo per il prodotto principale
			$total_regular_price = floatval( $product_data->get_regular_price() * $quantity );
			$total_sale_price    = floatval( $sale_price );
		} else {
			$total_regular_price = floatval( $product_data->get_regular_price() * $quantity );
			$total_sale_price    = floatval( $product_data->get_sale_price() ?: $product_data->get_regular_price() );
			$total_sale_price    = floatval( $total_sale_price * $quantity );
		}
	} else {

		$quantity = count( $variation_ids );

		foreach ( $variation_ids as $variation_id ) {
			$product_variant = wc_get_product( $variation_id );

			if ( ! $product_variant ) {
				continue; // Salta se il prodotto non esiste
			}

			$id_to_use = $variation_id ?: $product_id;
			if ( '1' === $bundle_policy ) {
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
					$sale_price = $price_for_three + $price_for_three * ( $quantity - 3 ) / 3;
				} else {
					$sale_price = $product_variant->get_sale_price() ?: $product_variant->get_regular_price();
				}

				$regular_price = floatval( $product_variant->get_regular_price() );

				// Calcola il prezzo per ogni variante (considerando una quantità di 1 per variante)
				$total_regular_price += $regular_price;
				$total_sale_price    += $sale_price / $quantity;
			} else {
				$total_regular_price += floatval( $product_variant->get_regular_price() );
				$total_sale_price    += floatval( $product_variant->get_sale_price() ?: $product_variant->get_regular_price() );
			}
		}

		$total_regular_price = floatval( $total_regular_price );
		$total_sale_price    = floatval( $total_sale_price );
	}

	// Costruisci e invia la risposta JSON
	$response = array(
		'bundle_policy'       => floatval( $bundle_policy ),
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

/**
 * @throws Exception
 */
function adrocket_add_to_cart_callback() {
	$product_id    = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
	$quantity      = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1;
	$variation_ids = $_POST['variation_ids'] ?? array();

	if ( ! empty( $variation_ids ) ) {
		foreach ( $variation_ids as $variation_id ) {
			WC()->cart->add_to_cart( $variation_id, 1 );
		}
	} else {
		WC()->cart->add_to_cart( $product_id, $quantity );
	}

	$response = array(
		'product_id'    => $product_id,
		'quantity'      => $quantity,
		'variation_ids' => $variation_ids,
		'message'       => 'Prodotti aggiunti al carrello',
		'cart_url'      => wc_get_cart_url()
	);

	echo json_encode( $response );
	wp_die();
}

add_action( 'wp_ajax_adrocket_add_to_cart', 'adrocket_add_to_cart_callback' );
add_action( 'wp_ajax_nopriv_adrocket_add_to_cart', 'adrocket_add_to_cart_callback' );