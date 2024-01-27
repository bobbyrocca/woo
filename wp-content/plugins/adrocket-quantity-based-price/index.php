<?php
/**
 * Plugin Name: Adrocket Quantity Based Pricing
 * Description: Imposta prezzi speciali in base alla quantità per i prodotti.
 * Version: 1.0
 * Author: Halexo Limited
 */

// Aggiunta del campo dropdown 'bundle_policy'
function add_bundle_policy_field() {
	echo '<div class="options_group">';
	woocommerce_wp_select( array(
		'id'          => 'bundle_policy',
		'label'       => 'Politica di bundle:',
		'options'     => array(
			'0' => 'Non attiva',
			'1' => 'Prezzo basato sulla quantità',
			'2' => 'Prezzo basato sulla varianti'
		),
		'desc_tip'    => true,
		'description' => 'Seleziona la politica di bundle per questo prodotto.',
	) );
	echo '</div>';
}

add_action( 'woocommerce_product_options_general_product_data', 'add_bundle_policy_field' );

// Salvataggio del campo 'bundle_policy'
function save_bundle_policy_field( $post_id ) {
	if ( isset( $_POST['bundle_policy'] ) ) {
		update_post_meta( $post_id, 'bundle_policy', $_POST['bundle_policy'] );
	}
}

add_action( 'woocommerce_process_product_meta', 'save_bundle_policy_field' );

function add_quantity_based_pricing_fields() {

	// Ottieni il valore corrente di 'bundle_policy'
	global $post;
	$bundle_policy = get_post_meta( $post->ID, 'bundle_policy', true );

	// Mostra i campi solo se 'bundle_policy' è impostato su '1'
	if ( '1' === $bundle_policy ) {
		woocommerce_wp_text_input( array(
			'id'                => 'qty_based_price_1',
			'label'             => 'Prezzo per 1 unità:',
			'desc_tip'          => 'true',
			'description'       => 'Imposta il prezzo per 1 unità',
			'type'              => 'number',
			'custom_attributes' => array(
				'step' => 'any',
				'min'  => '0'
			)
		) );

		// Campo per 2 unità
		woocommerce_wp_text_input( array(
			'id'                => 'qty_based_price_2',
			'label'             => 'Prezzo per 2 unità:',
			'desc_tip'          => 'true',
			'description'       => 'Imposta il prezzo per 2 unità',
			'type'              => 'number',
			'custom_attributes' => array(
				'step' => 'any',
				'min'  => '0'
			)
		) );

		// Campo per 3 unità
		woocommerce_wp_text_input( array(
			'id'                => 'qty_based_price_3',
			'label'             => 'Prezzo per 3 unità:',
			'desc_tip'          => 'true',
			'description'       => 'Imposta il prezzo per 3 unità',
			'type'              => 'number',
			'custom_attributes' => array(
				'step' => 'any',
				'min'  => '0'
			)
		) );
	}
}

add_action( 'woocommerce_product_options_pricing', 'add_quantity_based_pricing_fields' );

function save_quantity_based_pricing_fields( $post_id ) {

	// $logger  = wc_get_logger();
	$context = array( 'source' => 'save_quantity_based_pricing_fields' );

	// $logger->info( '----------------------------------------', $context );

	// $logger->info( 'Inizio salvataggio campi per il prodotto ID: ' . $post_id, $context );

	$product = wc_get_product( $post_id );

	// log the POST

	// $logger->info( 'POST: ' . print_r( $_POST, true ), $context );

	if ( $product && $product->is_type( 'variation' ) ) {
		// $logger->info( 'Il prodotto è una variante', $context );
		$parent_id     = $product->get_parent_id();
		$bundle_policy = get_post_meta( $parent_id, 'bundle_policy', true );

		if ( '1' === $bundle_policy ) {
			$parent_product = wc_get_product( $parent_id );

			// $logger->info( 'Il prodotto genitore è ID: ' . $parent_id, $context );

			$children = $parent_product->get_children();

			// log $children
			// $logger->info( 'children: ' . implode( ', ', $children ), $context );

			// read $_POST['qty_based_price_1'] from $_POST['qty_based_price_1_0'] or $_POST['qty_based_price_1_1'] or $_POST['qty_based_price_1_2']

			foreach ( $_POST as $key => $value ) {
				// Utilizza una regular expression per trovare il suffisso numerico
				if ( preg_match( '/^qty_based_price_(\d+)(?:_\d+)?$/', $key, $matches ) ) {
					$index = $matches[1]; // Estrai l'indice numerico
					unset( $_POST[ $key ] ); // Rimuovi la chiave originale
					$_POST["qty_based_price_$index"] = $value; // Aggiungi la chiave con il nome pulito
				}
			}

			// $logger->info( 'POST: ' . print_r( $_POST, true ), $context );

			foreach ( $children as $child_id ) {
				// $logger->info( 'child_id: ' . $child_id, $context );
				if ( isset( $_POST['qty_based_price_1'] ) ) {
					// $logger->info( 'child_id: ' . $child_id . ' qty_based_price_1: ' . $_POST['qty_based_price_1'], $context );
					update_post_meta( $child_id, 'qty_based_price_1', $_POST['qty_based_price_1'] );
				}
				if ( isset( $_POST['qty_based_price_2'] ) ) {
					// $logger->info( 'child_id: ' . $child_id . ' qty_based_price_2: ' . $_POST['qty_based_price_2'], $context );
					update_post_meta( $child_id, 'qty_based_price_2', $_POST['qty_based_price_2'] );
				}
				if ( isset( $_POST['qty_based_price_3'] ) ) {
					// $logger->info( 'child_id: ' . $child_id . ' qty_based_price_3: ' . $_POST['qty_based_price_3'], $context );
					update_post_meta( $child_id, 'qty_based_price_3', $_POST['qty_based_price_3'] );
				}
			}

			// $logger->info( 'Prezzi aggiornati applicati a tutte le varianti del prodotto genitore ID ' . $parent_id, $context );
		}
	} else {
		// $logger->info( 'Il prodotto non è una variante', $context );
		$bundle_policy = get_post_meta( $post_id, 'bundle_policy', true );

		// Salva i dati personalizzati
		if ( isset( $_POST['qty_based_price_1'] ) ) {
			// $logger->info( 'qty_based_price_1: ' . $_POST['qty_based_price_1'], $context );
			update_post_meta( $post_id, 'qty_based_price_1', $_POST['qty_based_price_1'] );
		}
		// Ripeti per quantità 2
		if ( isset( $_POST['qty_based_price_2'] ) ) {
			// $logger->info( 'qty_based_price_2: ' . $_POST['qty_based_price_2'], $context );
			update_post_meta( $post_id, 'qty_based_price_2', $_POST['qty_based_price_2'] );
		}
		// Ripeti per quantità 3
		if ( isset( $_POST['qty_based_price_3'] ) ) {
			// $logger->info( 'qty_based_price_3: ' . $_POST['qty_based_price_3'], $context );
			update_post_meta( $post_id, 'qty_based_price_3', $_POST['qty_based_price_3'] );
		}
	}

	// $logger->info( 'bundle_policy: ' . $bundle_policy, $context );
}

add_action( 'woocommerce_process_product_meta', 'save_quantity_based_pricing_fields' );
add_action( 'woocommerce_save_product_variation', 'save_quantity_based_pricing_fields' );

function apply_custom_pricing( $cart ) {

	// $logger  = wc_get_logger();
	$context = array( 'source' => 'apply_custom_pricing' );
	// $logger->info( '----------------------------------------', $context );

	// Prima passata: raccoglie la quantità totale per ciascun prodotto genitore
	$parent_quantities = array();
	foreach ( $cart->get_cart() as $cart_item ) {
		$product_id = $cart_item['variation_id'] ?: $cart_item['product_id'];
		$product    = wc_get_product( $product_id );

		if ( $product->is_type( 'variation' ) ) {
			$parent_id = $product->get_parent_id();
			if ( ! isset( $parent_quantities[ $parent_id ] ) ) {
				$parent_quantities[ $parent_id ] = 0;
			}
			$parent_quantities[ $parent_id ] += $cart_item['quantity'];
		}
	}

	foreach ( $cart->get_cart() as $cart_item ) {

		// Ottiene l'ID del prodotto o dell'ID della variante del prodotto, se esiste
		$product_id = $cart_item['variation_id'] ?: $cart_item['product_id'];

		$quantity   = $cart_item['quantity'];
		// $logger->info( 'item quantity: ' . $quantity, $context );

		$product    = wc_get_product( $product_id );

		// log $product_id

		// $logger->info( 'product_id: ' . $product_id, $context );

		if ( $product->is_type( 'variation' ) ) {
			// $logger->info( 'Il prodotto è una variante', $context );

			$parent_id = $product->get_parent_id();
			// $logger->info( 'parent_id: ' . $parent_id, $context );

			$bundle_policy = get_post_meta( $parent_id, 'bundle_policy', true );

			$total_quantity = $parent_quantities[$parent_id];
			// $logger->info( 'total_quantity: ' . $total_quantity, $context );
		} else {
			// $logger->info( 'Il prodotto non è una variante', $context );
			$bundle_policy = get_post_meta( $product_id, 'bundle_policy', true );

			$total_quantity = $quantity;
		}

		if ( '1' == $bundle_policy ) {

			// $logger->info( 'bundle_policy: ' . $bundle_policy, $context );

			$default_regular_price = get_post_meta( $product_id, '_regular_price', true ); // Ottieni il prezzo regolare del primo figlio
			if ( get_post_meta( $product_id, 'qty_based_price_1', true ) == '' ) {

				$default_sales_price = get_post_meta( $product_id, '_price', true ) ?: $default_regular_price;
			} else {

				$default_sales_price = get_post_meta( $product_id, 'qty_based_price_1', true );
			}

			$price_for_one   = get_post_meta( $product_id, 'qty_based_price_1', true );
			$price_for_two   = get_post_meta( $product_id, 'qty_based_price_2', true );
			$price_for_three = get_post_meta( $product_id, 'qty_based_price_3', true );

			$cart_item['data']->set_price( calc_price_bundle_1( $default_regular_price, $default_sales_price, $price_for_one, $price_for_two, $price_for_three, $total_quantity ) );
		}
	}
}

add_action( 'woocommerce_before_calculate_totals', 'apply_custom_pricing', 10, 1 );

// Aggiunta dei campi per i prezzi basati sulla quantità per ciascuna variazione
function add_variation_quantity_based_pricing_fields( $loop, $variation_data, $variation ) {
	// Aggiungi campi per i prezzi basati sulla quantità
	for ( $i = 1; $i <= 3; $i ++ ) {
		woocommerce_wp_text_input( array(
			'id'                => "qty_based_price_{$i}_{$loop}",
			'label'             => "Prezzo per {$i} unità:",
			'description'       => "Imposta il prezzo per {$i} unità per questa variazione",
			'value'             => get_post_meta( $variation->ID, "qty_based_price_{$i}", true ),
			'wrapper_class'     => 'form-row form-row-full',
			'type'              => 'number',
			'custom_attributes' => array(
				'step' => 'any',
				'min'  => '0'
			)
		) );
	}
}

add_action( 'woocommerce_product_after_variable_attributes', 'add_variation_quantity_based_pricing_fields', 10, 3 );

// Salvataggio dei campi per i prezzi basati sulla quantità nelle variazioni
function save_variation_quantity_based_pricing_fields( $variation_id, $i ) {
	for ( $j = 1; $j <= 3; $j ++ ) {
		$price_field = "qty_based_price_{$j}_{$i}";
		if ( isset( $_POST[ $price_field ] ) ) {
			update_post_meta( $variation_id, "qty_based_price_{$j}", sanitize_text_field( $_POST[ $price_field ] ) );
		}
	}
}

add_action( 'woocommerce_save_product_variation', 'save_variation_quantity_based_pricing_fields', 10, 2 );


function calc_price_bundle_1( $default_regular_price, $default_sale_price, $price_for_one, $price_for_two, $price_for_three, $quantity ): float {
	if ( $quantity == 1 && ! empty( $price_for_one ) ) {
		$sale_price = $price_for_one;
	} elseif ( $quantity == 2 && ! empty( $price_for_two ) ) {
		$sale_price = $price_for_two;
	} elseif ( $quantity >= 3 && ! empty( $price_for_three ) ) {
		$sale_price = $price_for_three + $price_for_three * ( $quantity - 3 ) / 3;
	} else {
		$sale_price = $default_sale_price ?: $default_regular_price;
		$sale_price = $sale_price * $quantity;
	}

	return floatval( $sale_price / $quantity );

}