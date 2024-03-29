<?php
/**
 * Plugin Name: Adrocket Sales Badge
 * Description: Sales Badge for Adrocket
 * Version: 1.0
 * Author: Halexo Limited
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Customize the sales badge to show the discount percentage instead of the Sale string

add_filter( 'woocommerce_sale_flash', 'custom_sale_badge', 99, 3 );

function custom_sale_badge( $html, $post, $product ) {
	if ( ! $product->is_on_sale() ) {
		return $html;
	}

	// Imposta i valori iniziali per i prezzi.
	$regular_price = 0;
	$sale_price    = 0;

	if ( $product->is_type( 'variable' ) ) {
		// Prendi le varianti del prodotto.
		$variations = $product->get_available_variations();

		// Se esistono varianti, usa la prima.
		if ( ! empty( $variations ) ) {
			$first_variation_id = $variations[0]['variation_id'];
			$variation_obj      = wc_get_product( $first_variation_id );

			$regular_price = floatval( $variation_obj->get_regular_price() );
			$sale_price    = floatval( $variation_obj->get_sale_price() );
		}
	} else {
		// Per prodotti non variabili, usa i prezzi direttamente dal prodotto.
		$regular_price = floatval( $product->get_regular_price() );
		$sale_price    = floatval( $product->get_sale_price() );
	}

	// Calcola la percentuale di sconto.
	if ( $regular_price > 0 && ( $sale_price < $regular_price ) ) {
		$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );

		//return '<span class="onsale">-' . $percentage . '%</span>';
		return '';
	}

	return $html;
}

add_action( 'woocommerce_after_shop_loop_item', 'add_sale_badge_to_products' );

/* Add the sales badge to the product thumbnails

add_action( 'woocommerce_product_thumbnails', function () {
	add_sale_badge_to_products( true );
} );

*/

function add_sale_badge_to_products($is_main_product = false) {
	global $product;

	// Controlla se il prodotto è una variante.
	if ( $product->is_type( 'variable' ) ) {
		// Ottieni le varianti del prodotto.
		$variations = $product->get_available_variations();

		// Prendi la prima variante.
		if ( ! empty( $variations ) ) {
			$first_variation_id = $variations[0]['variation_id'];
			$variation_obj      = wc_get_product( $first_variation_id );

			// Ottieni i prezzi della prima variante.
			$regular_price = floatval( $variation_obj->get_regular_price() );
			$sale_price    = floatval( $variation_obj->get_sale_price() );

			// Calcola la percentuale di sconto.
			if ( $regular_price > 0 && $variation_obj->is_on_sale() ) {
				$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );

			}
		}
	} else {
		// Gestione per prodotti non variabili.
		$regular_price = floatval( $product->get_regular_price() );
		$sale_price    = floatval( $product->get_sale_price() );

		if ( $regular_price > 0 && $product->is_on_sale() ) {
			$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
		}
	}

	if ( isset( $percentage ) && $percentage > 0 ) {
		// Scegli la classe in base alla pagina
		$badge_class = $is_main_product  ? 'sale-badge-bigger' : 'sale-badge';

		echo '<div class="sale-badge-box top"><span class="' . $badge_class . '">-' . $percentage . '%</span></div>';
	}
}

// Customize the price to show the regular price and the sale price

add_filter( 'woocommerce_get_price_html', 'custom_price_order', 100, 2 );

function custom_price_order( $price, $product ) {
	if ( ! $product->is_on_sale() ) {
		return $price;
	}

	$regular_price = 0;
	$sale_price    = 0;

	if ( $product->is_type( 'variable' ) ) {
		// Prendi le varianti del prodotto.
		$variations = $product->get_available_variations();

		// Se esistono varianti, usa la prima.
		if ( ! empty( $variations ) ) {
			$first_variation_id = $variations[0]['variation_id'];
			$variation_obj      = wc_get_product( $first_variation_id );

			$regular_price = wc_get_price_to_display( $variation_obj, array( 'price' => $variation_obj->get_regular_price() ) );
			$sale_price    = wc_get_price_to_display( $variation_obj );
		}
	} else {
		// Per prodotti non variabili, usa i prezzi direttamente dal prodotto.
		$regular_price = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );
		$sale_price    = wc_get_price_to_display( $product );
	}

	// Se il prezzo di vendita è minore del prezzo regolare, mostra entrambi.
	if ( $sale_price < $regular_price ) {
		return '<ins class="sale-price">' . wc_price( $sale_price ) . '</ins> ' . '<del class="regular-price">' . wc_price( $regular_price ) . '</del>';
	}

	return $price;
}


function add_sale_badge_to_single_product() {
	global $product;

	if ( $product->is_on_sale() ) {
		$regular_price = floatval( $product->get_regular_price() );
		$sale_price    = floatval( $product->get_sale_price() );

		if ( $regular_price > 0 && $sale_price < $regular_price ) {
			$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
			echo '<div class="sale-badge-box-single"><span class="sale-badge-single">-' . $percentage . '%</span></div>';
		}
	}
}

function adrocket_sales_badge_css() {
	wp_enqueue_style( 'adrocket-sales-badge-css', plugin_dir_url( __FILE__ ) . 'style.css' );
}

add_action( 'wp_enqueue_scripts', 'adrocket_sales_badge_css' );

