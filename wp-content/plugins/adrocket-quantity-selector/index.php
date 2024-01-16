<?php
/**
 * Plugin Name: Adrocket Quantity Selector
 * Description: Seleziona la quantità e variante del prodotto.
 * Version: 1.0
 * Author: Halexo Limited
 */
function adrocket_quantity_selector(): string {
	global $product;

	// Verifica se ci si trova in una pagina di prodotto
	if ( !is_a( $product, 'WC_Product' ) ) {
		return 'Questo shortcode funziona solo nelle pagine dei prodotti.';
	}

	$product_id = $product->get_id();

	// Genera il menu a tendina per la quantità
	$output = '<select id="quantity-selector">';
	for ( $i = 1; $i <= 3; $i++ ) {
		$output .= '<option value="' . $i . '">' . $i . '</option>';
	}
	$output .= '</select>';

	// Controlla se il prodotto è variabile
	if ( $product->is_type( 'variable' ) ) {
		$variable_product = new WC_Product_Variable( $product_id );
		if ( is_a( $variable_product, 'WC_Product_Variable' ) ) {
			$output .= '<div id="variant-selectors-container">';
			$output .= '<select class="individual-variant-selector">';
			foreach ( $variable_product->get_children() as $child_id ) {
				$child_product = wc_get_product( $child_id );
				$output .= '<option value="' . esc_attr( $child_id ) . '">' . $child_product->get_name() . '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';
		}
	}

	$output .= '<div id="price-display"></div>';

	return $output;
}

add_shortcode( 'quantity_selector', 'adrocket_quantity_selector' );

function selector_enqueue_scripts() {
	if (is_product()) {
		wp_enqueue_script('adrocket-quantity-selector-js', plugin_dir_url(__FILE__) . 'js/selector.js', array('jquery'), '1.0', true);
	}
}
add_action('wp_enqueue_scripts', 'selector_enqueue_scripts');

function adrocket_quantity_selector_radio(): string {
	global $product;

	// Check if on a product page
	if ( !is_a( $product, 'WC_Product' ) ) {
		return 'This shortcode only works on product pages.';
	}

	$product_id = $product->get_id();

	// Generate radio buttons for quantity
	$output = '<div id="quantity-selector-radio">';
	for ( $i = 1; $i <= 3; $i++ ) {
		$checked = ($i === 1) ? ' checked' : ''; // Aggiungi checked per il primo radio button
		$output .= '<input type="radio" id="quantity' . $i . '" name="quantity" value="' . $i . '"' . $checked . '>';
		$output .= '<label for="quantity' . $i . '">' . $i . '</label>';
	}
	$output .= '</div>';

	// Check if product is variable
	if ( $product->is_type( 'variable' ) ) {
		$variable_product = new WC_Product_Variable( $product_id );
		if ( is_a( $variable_product, 'WC_Product_Variable' ) ) {
			$output .= '<div id="variant-selectors-container">';
			$output .= '<select class="individual-variant-selector">';
			foreach ( $variable_product->get_children() as $child_id ) {
				$child_product = wc_get_product( $child_id );
				$output .= '<option value="' . esc_attr( $child_id ) . '">' . $child_product->get_name() . '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';
		}
	}

	$output .= '<div id="price-display"></div>';

	return $output;
}

add_shortcode( 'quantity_selector_radio', 'adrocket_quantity_selector_radio' );
