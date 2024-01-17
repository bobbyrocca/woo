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
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return 'Questo shortcode funziona solo nelle pagine dei prodotti.';
	}

	$product_id = $product->get_id();

	// Genera il menu a tendina per la quantità
	$output = '<select id="quantity-selector">';
	for ( $i = 1; $i <= 3; $i ++ ) {
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
				$output        .= '<option value="' . esc_attr( $child_id ) . '">' . $child_product->get_name() . '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';
		}
	}

	$output .= '<div id="price-display"></div>';

	return $output;
}

add_shortcode( 'quantity_selector', 'adrocket_quantity_selector' );

function adrocket_quantity_selector_radio(): string {
	global $product;

	// Check if on a product page
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return 'This shortcode only works on product pages.';
	}

	$product_id = $product->get_id();

	// Generate radio buttons for quantity
	$output = '<div id="quantity-selector-radio" class="radio-flex">';
	for ( $i = 1; $i <= 3; $i ++ ) {
		$checked  = ( $i === 1 ) ? ' checked' : ''; // Aggiungi checked per il primo radio button
		$selected = ( $i === 1 ) ? 'selected' : ''; // Aggiungi checked per il primo radio button
		$output   .= '<div class="radio-box">';
		$output   .= '<label for="quantity' . $i . '" class="radio-1 ' . $selected . '"><input class="radio-1" type="radio" id="quantity' . $i . '" name="quantity" value="' . $i . '"' . $checked . '><span class="radio-1">' . $i . '</span></label>';
		$output   .= '</div>';
	}
	$output .= '</div>';

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

				$output .= '<label class="center radio-1 ' . $selected . '" for="variant1_' . $i . '"><input class="radio-1 hide" type="radio" id="variant1_' . $i . '" name="variant[0]" value="' . esc_attr( $child_id ) . '" ' . $checked . '><span class="radio-1"><img class="variant-1" src="' . $image_url . '" alt="' . esc_attr( $child_product->get_name() ) . '">';
				$output .= implode( ", ", $child_product->get_variation_attributes() ); // Aggiungi altri attributi della variante se necessario
				$output .= '</span></label>';
				$first  = false; // Imposta a false dopo il primo ciclo
				$i ++;
			}
			$output .= '</div></div></div>';
		}
	}


	$output .= '<div id="price-display"></div>';

	return $output;
}

add_shortcode( 'quantity_selector_radio', 'adrocket_quantity_selector_radio' );

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