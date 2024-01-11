<?php
/**
 * Plugin Name: Adrocket Quantity Based Pricing per WooCommerce
 * Description: Imposta prezzi speciali in base alla quantità per i prodotti WooCommerce.
 * Version: 1.0
 * Author: Halexo Limited
 */

function adrocket_quantity_selector(): string {
	global $product;

	// Controlla se ci si trova in una pagina di prodotto
	if ( is_a( $product, 'WC_Product' ) ) {
		$product_id = $product->get_id();

		// Genera il menu a tendina per la quantità
		$output = '<select id="quantity-selector">';
		for ( $i = 1; $i <= 10; $i ++ ) {
			$output .= '<option value="' . $i . '">' . $i . '</option>';
		}
		$output .= '</select>';

		// Controlla se il prodotto è variabile
		if ( $product->is_type( 'variable' ) ) {
			$variable_product = new WC_Product_Variable( $product_id );
			if ( is_a( $variable_product, 'WC_Product_Variable' ) ) {
				$output .= '<select id="variant-selector" data-product-id="' . esc_attr( $product_id ) . '">';
				foreach ( $variable_product->get_available_variations() as $variation ) {
					$variation_obj = wc_get_product( $variation['variation_id'] );
					$output        .= '<option value="' . esc_attr( $variation_obj->get_id() ) . '">' . implode( ' / ', $variation_obj->get_variation_attributes() ) . '</option>';
				}
				$output .= '</select>';
			}
		}

		// Placeholder per la visualizzazione dei prezzi
		$output .= '<div id="price-display"></div>';

		return $output;
	} else {
		return 'Questo shortcode funziona solo nelle pagine dei prodotti.';
	}
}

add_shortcode( 'quantity_selector', 'adrocket_quantity_selector' );

function add_quantity_based_pricing_fields() {

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
