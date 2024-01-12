<?php
/**
 * Plugin Name: Adrocket Quantity Selector
 * Description: Seleziona la quantità e variante del prodotto.
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
