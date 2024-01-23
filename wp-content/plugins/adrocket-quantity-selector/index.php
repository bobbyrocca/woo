<?php
/**
 * Plugin Name: Adrocket Quantity Selector
 * Description: Seleziona la quantità e variante del prodotto.
 * Version: 1.0
 * Author: Halexo Limited
 */

function adrocket_quantity_selectors(): string {
	global $product;

	// Check if on a product page
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return 'This shortcode only works on product pages.';
	}

	$product_id = $product->get_id();

	$bundle_policy = get_post_meta( $product_id, 'bundle_policy', true );

	if ( '1' == $bundle_policy ) {

		$output = '<div id="quantity-selector-radio" class="radio-flex">';
		if ( $product->is_type( 'variable' ) ) {

			$default_sales_price   = $product->get_sale_price() ?: $product->get_regular_price();
			$default_regular_price = $product->get_regular_price();

			for ( $i = 1; $i <= 3; $i ++ ) {
				$checked  = ( $i === 1 ) ? ' checked' : ''; // Aggiungi checked per il primo radio button
				$selected = ( $i === 1 ) ? 'selected' : ''; // Aggiungi checked per il primo radio button
				$output   .= '<div class="radio-box">';
				$output   .= '<input class="radio-1" type="radio" id="quantity' . $i . '" name="quantity" value="' . $i . '"' . $checked . '><label for="quantity' . $i . '" class="radio-1 ' . $selected . '"><span class="radio-1">' . $i . '</span></label>';
				$output   .= '</div>';
			}
		} else {

			$default_sales_price   = $product->get_sale_price() ?: $product->get_regular_price();
			$default_regular_price = $product->get_regular_price();

			// Generate radio buttons for quantity
			for ( $i = 1; $i <= 3; $i ++ ) {

				/* if qty_based_price_ is not set, set sale_price to regular_price */

				if ( get_post_meta( $product_id, 'qty_based_price_' . $i, true ) == '' ) {
					$sale_price = $product->get_regular_price() * $i;
				} else {
					$sale_price = get_post_meta( $product_id, 'qty_based_price_' . $i, true );
				}

				$claim     = ( $i == 2 ) ? 'Most Popular' : '';
				$shipping  = ( $i > 1 ) ? 'Free Shipping' : 'Shipping ' . wc_price( 5 );
				$tag_color = ( $i > 1 ) ? 'blue' : 'grey';

				$unit_price          = $sale_price / $i;
				$regular_price       = $product->get_regular_price();
				$total_regular_price = $regular_price * $i;
				$saving              = $total_regular_price - $sale_price;
				$discount            = floor( ( ( $total_regular_price - $sale_price ) / $total_regular_price ) * 100 );

				$unit_price_note = ( $i > 1 ) ? wc_price( $unit_price ) . ' each' : '';

				$checked  = ( $i === 1 ) ? ' checked' : ''; // Aggiungi checked per il primo radio button
				$selected = ( $i === 1 ) ? 'selected' : ''; // Aggiungi checked per il primo radio button
				$output   .= '<div class="radio-box">';
				$output   .= '<input class="radio-1" type="radio" id="quantity' . $i . '" name="quantity" value="' . $i . '"' . $checked . '>';
				$output   .= '<label for="quantity' . $i . '" class="radio-1 ' . $selected . '">';
				if ( $claim != '' ) {
					$output .= '<div class="label-1">';
					$output .= '<p class="radio-1 centre absolute"><span class="tag2">' . $claim . '</span></p>';
					$output .= '</div>';
				}
				$output .= '<div class="label-1">';
				$output .= '<p class="radio-1"><span class="radio-1"><strong>' . $i . '</strong></span><span>' . $product->get_name() . '</span></p>';
				if ( $total_regular_price > $sale_price ) {
					$output .= '<p class="radio-1 right"><del class="s1">' . wc_price( $total_regular_price ) . '</del><strong class="s1">' . wc_price( $sale_price ) . '</strong></p>';
				} else {
					$output .= '<p class="radio-1 right"><strong class="s1">' . wc_price( $sale_price ) . '</strong></p>';
				}
				$output .= '</div>';
				$output .= '<div class="label-1">';
				$output .= '<p class="radio-1 right"><span class="tag grey1">' . $unit_price_note . '</span>';
				if ( $total_regular_price > $sale_price ) {
					$output .= '<span class="tag green">-' . $discount . '%</span>';
				}
				$output .= '<span class="tag ' . $tag_color . '">' . $shipping . '</span></p>';
				$output .= '</div>';
				$output .= '</label>';
				$output .= '</div>';
			}
		}
		$output .= '</div>';

	} else {
		// Genera il menu a tendina per la quantità
		$output = '<select id="quantity-selector">';
		for ( $i = 1; $i <= 3; $i ++ ) {
			$output .= '<option value="' . $i . '">' . $i . '</option>';
		}
		$output .= '</select>';

		if ( $product->is_type( 'variable' ) ) {

			$child_id              = $product->get_children()[0];
			$default_sales_price   = get_post_meta( $child_id, '_price', true );
			$default_regular_price = get_post_meta( $child_id, '_regular_price', true );
		} else {
			$default_sales_price   = $product->get_sale_price() ?: $product->get_regular_price();
			$default_regular_price = $product->get_regular_price();
		}
	}
	if ( $product->is_type( 'variable' ) ) {
		$variable_product = new WC_Product_Variable( $product_id );
		if ( is_a( $variable_product, 'WC_Product_Variable' ) ) {
			$output .= '<div id="variant-selectors-container" class="variants"><div class="flex-1"><div>Product <span class="product-index">1</span></div><div class="variant-radios">';
			$first  = true; // Indica se siamo al primo elemento del ciclo
			$i      = 0;
			foreach ( $variable_product->get_children() as $child_id ) {
				$child_product = wc_get_product( $child_id );
				$checked       = $first ? 'checked' : ''; // Se è il primo elemento, aggiungi 'checked'
				$selected      = $first ? 'selected' : ''; // Se è il primo elemento, aggiungi 'checked'

				$image_url = wp_get_attachment_url( $child_product->get_image_id() ); // Ottieni l'URL dell'immagine della variante

				$output .= '<input class="radio-1 hide" type="radio" id="variant1_' . $i . '" name="variant[0]" value="' . esc_attr( $child_id ) . '" ' . $checked . '><label class="center radio-1 ' . $selected . '" for="variant1_' . $i . '"><span class="radio-1"><img class="variant-1" src="' . $image_url . '" alt="' . esc_attr( $child_product->get_name() ) . '">';
				$output .= implode( ", ", $child_product->get_variation_attributes() ); // Aggiungi altri attributi della variante se necessario
				$output .= '</span></label>';
				$first  = false; // Imposta a false dopo il primo ciclo
				$i ++;
			}
			$output .= '</div></div></div>';
		}
	}

	// total box

	if ( $default_regular_price > $default_sales_price ) {
		$default_regular_price = wc_price( $default_regular_price );
	} else {
		$default_regular_price = '';
	}

	$output .= '<div id="total-box" class="total-box"><span class="amount-title">Total: </span>';
	$output .= '<div class="total-amount"><span id="sales-price" class="sales">' . wc_price( $default_sales_price ) . '</span>';
	$output .= '<del id="regular-price" class="regular">' . $default_regular_price . '</del></div>';
	$output .= '</div>';

	return $output;
}

add_shortcode( 'quantity_selector_radio', 'adrocket_quantity_selectors' );

function add_custom_css() {
	wp_enqueue_style( 'adrocket-quantity-selector-css', plugin_dir_url( __FILE__ ) . 'css/style.css?v=' . microtime() );
}

add_action( 'wp_enqueue_scripts', 'add_custom_css' );


function selector_enqueue_scripts() {
	if ( is_product() ) {
		wp_enqueue_script( 'adrocket-quantity-selector-js', plugin_dir_url( __FILE__ ) . 'js/selector.js', array( 'jquery' ), microtime(), true );
	}
}

add_action( 'wp_enqueue_scripts', 'selector_enqueue_scripts' );