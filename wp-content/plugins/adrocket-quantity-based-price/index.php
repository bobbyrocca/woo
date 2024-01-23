<?php
/**
 * Plugin Name: Adrocket Quantity Based Pricing
 * Description: Imposta prezzi speciali in base alla quantità per i prodotti.
 * Version: 1.0
 * Author: Halexo Limited
 */

// Aggiunta del campo dropdown 'bundle_policy'
function add_bundle_policy_field() {
	woocommerce_wp_select( array(
		'id'          => 'bundle_policy',
		'label'       => 'Politica di bundle:',
		'options'     => array(
			'0' => 'Non attiva',
			'1' => 'Prezzo basato sulla quantità'
		),
		'desc_tip'    => true,
		'description' => 'Seleziona la politica di bundle per questo prodotto.',
	) );
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
	// Salva i dati personalizzati
	if ( isset( $_POST['qty_based_price_1'] ) ) {
		update_post_meta( $post_id, 'qty_based_price_1', $_POST['qty_based_price_1'] );
	}
	// Ripeti per quantità 2
	if ( isset( $_POST['qty_based_price_2'] ) ) {
		update_post_meta( $post_id, 'qty_based_price_2', $_POST['qty_based_price_2'] );
	}
	// Ripeti per quantità 3
	if ( isset( $_POST['qty_based_price_3'] ) ) {
		update_post_meta( $post_id, 'qty_based_price_3', $_POST['qty_based_price_3'] );
	}
}

add_action( 'woocommerce_process_product_meta', 'save_quantity_based_pricing_fields' );

function apply_custom_pricing( $cart ) {
	foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
		// Ottiene l'ID del prodotto o dell'ID della variante del prodotto, se esiste
		$product_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
		$quantity   = $cart_item['quantity'];

		$price_for_one   = get_post_meta( $product_id, 'qty_based_price_1', true );
		$price_for_two   = get_post_meta( $product_id, 'qty_based_price_2', true );
		$price_for_three = get_post_meta( $product_id, 'qty_based_price_3', true );

		// Applica il prezzo per 1 unità se la quantità è 1
		if ( $quantity == 1 && ! empty( $price_for_one ) ) {
			$cart_item['data']->set_price( $price_for_one );
		} // Applica il prezzo per 2 unità se la quantità è 2
		elseif ( $quantity == 2 && ! empty( $price_for_two ) ) {
			$cart_item['data']->set_price( $price_for_two / 2 );
		} // Applica il prezzo per 3 unità se la quantità è 3 o maggiore
		elseif ( $quantity >= 3 && ! empty( $price_for_three ) ) {
			$cart_item['data']->set_price( $price_for_three / 3 );
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
