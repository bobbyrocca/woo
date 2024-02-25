<?php
/**
 * Plugin Name: Adrocket Quantity Selector
 * Description: Seleziona la quantità e variante del prodotto.
 * Version: 1.0
 * Author: Halexo Limited
 */

// Prevenire accesso diretto al file.
defined( 'ABSPATH' ) || exit;

function adrocket_quantity_selectors(): string {
	global $product;

	// Check if on a product page
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return 'This shortcode only works on product pages.';
	}

	$product_id = $product->get_id();

	$bundle_policy = get_post_meta( $product_id, 'bundle_policy', true );
	$output        = '<div class="adrocket-block" id="adrocket-block">';
	$output        .= '<div id="blocker" class="blocker hide"><div class="spinner"></div></div>';

	$shipping_policy = get_post_meta( $product_id, 'shipping_policy', true );

	$standard_shipping_fee = get_post_meta( $product_id, 'standard_shipping_fee', true ) ?: 5;

	$free_shipping_price_threshold    = 50;
	$free_shipping_quantity_threshold = 2;

	if ( '1' == $bundle_policy ) {

		$output .= '<div id="quantity-selector-radio" class="radio-flex">';
		if ( $product->is_type( 'variable' ) ) {

			$child_id              = $product->get_children()[0]; // Ottieni l'ID del primo figlio
			$default_regular_price = get_post_meta( $child_id, '_regular_price', true ); // Ottieni il prezzo regolare del primo figlio

			if ( get_post_meta( $child_id, 'qty_based_price_1', true ) == '' ) {

				$default_sales_price = get_post_meta( $child_id, '_price', true ) ?: $default_regular_price;
			} else {

				$default_sales_price = get_post_meta( $child_id, 'qty_based_price_1', true );
			}

			$output .= create_bundles( $child_id, $product, true, $standard_shipping_fee, $shipping_policy );
		} else {

			$default_regular_price = $product->get_regular_price();

			if ( get_post_meta( $product_id, 'qty_based_price_1', true ) == '' ) {

				$default_sales_price = get_post_meta( $product_id, '_price', true ) ?: $default_regular_price;
			} else {

				$default_sales_price = get_post_meta( $product_id, 'qty_based_price_1', true );
			}

			// Generate radio buttons for quantity
			$output .= create_bundles( $product_id, $product, $standard_shipping_fee, $shipping_policy );
		}
		$output .= '</div>';

	} else {
		// Genera il menu a tendina per la quantità
		$output .= '<select id="quantity-selector">';
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
			$output .= '<div id="variant-selectors-container" class="variants"><div class="flex-1"><div>Product <span class="product-index">1</span></div><div id="variant-radios" class="variant-radios">';
			$first  = true; // Indica se siamo al primo elemento del ciclo
			$i      = 0;
			foreach ( $variable_product->get_children() as $child_id ) {
				$child_product = wc_get_product( $child_id );
				$checked       = $first ? 'checked' : ''; // Se è il primo elemento, aggiungi 'checked'
				$selected      = $first ? 'selected' : ''; // Se è il primo elemento, aggiungi 'checked'

				$image_url = wp_get_attachment_url( $child_product->get_image_id() ); // Ottieni l'URL dell'immagine della variante

				$output .= '<input class="radio-1 hide" type="radio" id="variant1_' . $i . '" name="variant[0]" value="' . esc_attr( $child_id ) . '" ' . $checked . '><label class="center radio-1 ' . $selected . '" for="variant1_' . $i . '"><span class="radio-1"><img class="variant-1" src="' . $image_url . '" alt="' . esc_attr( $child_product->get_name() ) . '">';
				$output .= '<span class="caption-1">' . implode( ", ", $child_product->get_variation_attributes() ) . '</span>'; // Aggiungi altri attributi della variante se necessario
				$output .= '</span></label>';
				$first  = false; // Imposta a false dopo il primo ciclo
				$i ++;
			}
			$output .= '</div></div></div>';
		}
	}

	// total box

	if ( $default_regular_price > $default_sales_price ) {
		$default_discount      = floor( ( ( $default_regular_price - $default_sales_price ) / $default_regular_price ) * 100 );
		$default_regular_price = wc_price( $default_regular_price );
	} else {
		$default_regular_price = '';
		$default_discount      = '';
	}

	$hide_class = ( $default_discount == '' ) ? 'hide' : '';

	$output .= '<div id="total-box" class="total-box">';
	$output .= '<div class="total-amount"><span class="amount-title">Prezzo totale: </span><del id="regular-price" class="regular">' . $default_regular_price . '</del></div>';
	$output .= '<div class="sale-row"><span id="sales-price" class="sales">' . wc_price( $default_sales_price ) . '</span>';

	$output .= '<div id="discount-row" class="discount-row ' . $hide_class . '"><span>Sconto </span></spna><span id="discount_percentage" class="discount">' . $default_discount . '%</span></div>';
	$output .= '</div>';
	$output .= '</div>';

	// Parte finale della tua funzione adrocket_quantity_selectors
	$output .= '    <div class="status-container">
        <div class="spia available"></div>
        <div class="spia-text"><span class="testo"><strong class="testo available">In magazzino!</strong> Ordina ora e ricevi <strong class="testo blue">' . calcola_giorno_consegna( 3 ) . '.</strong></span></div></div>';
	$output .= '<div id="adrocket-add-to-cart" class="add-1 green enabled" data-enabled="1" data-product-id="' . esc_attr( $product_id ) . '"><span class="cart-shopping-solid"></span><span class="add-1">Add To Cart</span></div>';
	$output .= '</div>';

	$output .= trust_badges( 'IT' );

	$output .= payment_badges();

	$output .= guarantee_badge( 'IT' );

	$output .= sticky_button( 'IT' );

	return $output;
}

add_shortcode( 'quantity_selector_radio', 'adrocket_quantity_selectors' );

add_action( 'woocommerce_before_add_to_cart_form', function () {
	echo adrocket_quantity_selectors();
} );

function sticky_button( $language ): string {

	$html = '<div class="sticky-button hide" id="stick-single">';
	$html .= '<div class="sticky-button-inner">';
	$html .= '<div class="sticky-button-text">Ordina ora e ricevi <strong class="blue">' . calcola_giorno_consegna( 3 ) . '</strong></div>';
	$html .= '<div class="sticky-button-scroll-to-cart" id="scroll-to-cart"><span class="add-1">Sì, lo voglio ordinare!</span><img class="button-icon" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/pointer.svg" alt="Secure Payment"></div>';
	$html .= '</div>';
	$html .= '</div>';

	return $html;

}

function trust_badges( $language ): string {

	$html = '<div class="trust-badges">';
	$html .= '<div class="trust-row"><img class="trust-img flag" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/icons-it.svg" alt="Secure Payment"><span class="trust-text">Spedito dal magazzino in Italia</span></div>';
	$html .= '<div class="trust-row"><img class="trust-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/truck.svg" alt="Secure Payment"><span class="trust-text"><strong>Spedizione rapida</strong></span></div>';
	$html .= '<div class="trust-row"><img class="trust-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/guarantee.svg" alt="Secure Payment"><span class="trust-text">Soddisfazione garantita 30 giorni</span></div>';
	$html .= '<div class="trust-row"><img class="trust-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/cod.svg" alt="30 Days Money Back Guarantee"><span class="trust-text">Pagamento alla consegna</span></div>';
	$html .= '<div class="trust-row"><img class="trust-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/support.svg" alt="30 Days Money Back Guarantee"><span class="trust-text">Servizio clienti sempre attivo</span></div>';
	$html .= '</div>';

	return $html;
}

function payment_badges(): string {

	$html = '<div class="payment-badges">';
	$html .= '<img class="payment-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/visa.svg" alt="Visa">';
	$html .= '<img class="payment-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/mastercard.svg" alt="Mastercard">';
	$html .= '<img class="payment-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/paypal.svg" alt="Paypal">';
	$html .= '<img class="payment-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/amex.svg" alt="American Express">';
	$html .= '<img class="payment-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/maestro.svg" alt="Maestro">';
	$html .= '<img class="payment-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/cod-pay.svg" alt="Pagamento alla consegna">';
	$html .= '<img class="payment-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/poste-pay.svg" alt="Poste Pay">';
	$html .= '</div>';

	return $html;

}

function guarantee_badge( $language ): string {
	$html = '<div class="guarantee-badge">';
	$html .= '<img class="guarantee-img" src="' . plugin_dir_url( __FILE__ ) . 'images/icons/satisfy.svg" alt="30 Days Money Back Guarantee">';
	$html .= '<div class="guarantee-box"><span class="guarantee-text title"><strong>Soddisfatti o rimborsati 30 giorni</strong></span><span class="guarantee-text">Se il prodotto non la soddisfa, ce lo restituisca e le rimborseremo il suo denaro.</span></div>';
	$html .= '</div>';

	return $html;

}

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

function create_bundles( $product_id, $product, $is_variable = false, $standard_shipping_fee = 5, $shipping_policy = 2 ): string {

	$output = '';

	for ( $i = 1; $i <= 3; $i ++ ) {

		/* if qty_based_price_ is not set, set sale_price to regular_price */

		if ( $is_variable ) {
			$regular_price = get_post_meta( $product_id, '_regular_price', true );
		} else {
			$regular_price = $product->get_regular_price();
		}

		if ( get_post_meta( $product_id, 'qty_based_price_' . $i, true ) == '' ) {

			$sale_price = get_post_meta( $product_id, '_price', true ) * $i ?: $regular_price * $i;
		} else {

			$sale_price = get_post_meta( $product_id, 'qty_based_price_' . $i, true );
		}

		$claim = ( $i == 2 ) ? 'Most Popular' : '';

		if ( $shipping_policy == 2 ) {

			$shipping  = ( $i > 1 ) ? 'Free Shipping' : 'Shipping ' . wc_price( $standard_shipping_fee );
			$tag_color = ( $i > 1 ) ? 'blue' : 'grey';

		} elseif ( $shipping_policy == 1 ) {
			// Free is price is over 50
			$shipping  = ( $sale_price > 50 ) ? 'Free Shipping' : 'Shipping ' . wc_price( $standard_shipping_fee );
			$tag_color = ( $sale_price > 50 ) ? 'blue' : 'grey';

		} else {
			$shipping  = 'Shipping ' . wc_price( $standard_shipping_fee );
			$tag_color = 'grey';
		}

		$unit_price = $sale_price / $i;

		$total_regular_price = $regular_price * $i;
		$saving              = $total_regular_price - $sale_price;
		$discount            = floor( ( ( $total_regular_price - $sale_price ) / $total_regular_price ) * 100 );

		$unit_price_note = ( $i > 1 ) ? wc_price( $unit_price ) . ' each' : '';

		$checked  = ( $i === 1 ) ? ' checked' : ''; // Aggiungi checked per il primo radio button
		$selected = ( $i === 1 ) ? 'selected' : ''; // Aggiungi checked per il primo radio button
		$output   .= '<div class="radio-box">';
		$output   .= '<input class="radio-1 hide" type="radio" id="quantity' . $i . '" name="quantity" value="' . $i . '"' . $checked . '>';
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

	return $output;
}

function calcola_giorno_consegna( $shipping_days ): string {
	date_default_timezone_set( 'Europe/Rome' ); // Imposta il fuso orario su +1 (Roma)

	$shipping_days = (int) $shipping_days - 1; // Sottrai 1 per ottenere il giorno di consegna

	$now                 = new DateTime(); // Data e ora corrente
	$current_hour        = $now->format( 'H' ); // Ora corrente
	$current_day_of_week = $now->format( 'N' ); // Giorno della settimana (1 = Lunedì, ..., 7 = Domenica)

	// Se l'ordine è ricevuto dopo le 13:00, sposta la data al giorno successivo
	if ( $current_hour >= 13 ) {
		$now->modify( '+1 day' );
		$current_day_of_week = $now->format( 'N' ); // Aggiorna il giorno della settimana

		// Se è venerdì dopo le 13:00, sposta al lunedì successivo
		if ( $current_day_of_week == 5 ) { // 5 = Venerdì
			$now->modify( 'next Monday' );
		}
	}

	// Aggiungi due giorni di spedizione
	$now->modify( '+' . $shipping_days . ' days' );

	// Controlla se ci sono weekend nel mezzo e aggiusta di conseguenza
	for ( $i = 0; $i < $shipping_days; $i ++ ) {
		if ( $now->format( 'N' ) >= 6 ) { // 6 = Sabato, 7 = Domenica
			$now->modify( 'next Monday' );
		}
	}

	// Array dei nomi dei giorni della settimana e dei mesi in italiano
	$giorni_settimana = array( 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica' );
	$mesi             = array(
		'',
		'Gennaio',
		'Febbraio',
		'Marzo',
		'Aprile',
		'Maggio',
		'Giugno',
		'Luglio',
		'Agosto',
		'Settembre',
		'Ottobre',
		'Novembre',
		'Dicembre'
	);

	// Formatta la data nel formato desiderato in italiano
	$day_of_week = $giorni_settimana[ $now->format( 'N' ) - 1 ]; // Ottiene il nome del giorno della settimana in italiano
	$month       = $mesi[ intval( $now->format( 'n' ) ) ]; // Ottiene il nome del mese in italiano
	// Restituisce il giorno della settimana, la data completa e l'anno

	return $day_of_week . ', ' . $now->format( 'd' ) . ' ' . $month; // R
}

add_action( 'woocommerce_before_cart_table', 'delivery_eta' );
add_action( 'woocommerce_before_cart_collaterals', 'free_shipping_notice' );

function delivery_eta() {
	echo '<div class="delivery-eta margin">Ordina ora e ricevi <strong class="orange">' . calcola_giorno_consegna( 3 ) . '</strong></div>';
}

function free_shipping_notice() {
	echo '<div class="free-shipping-notice"><strong class="blue">Spedizione gratuita</strong> per ordini superiori a <strong class="blue">50€</strong></div>';
}