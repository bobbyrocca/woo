<?php
/**
 * Plugin Name: Adrocket Custom Cart Icon
 * Description: Sostituisce il mini-carrello di WooCommerce con un link alla pagina del carrello.
 * Version: 1.0
 * Author: Halexo Limited
 */

// Prevenire accesso diretto al file.
defined( 'ABSPATH' ) || exit;

function custom_cart_link_shortcode(): string {
	$cart_count = WC()->cart->get_cart_contents_count();
	$cart_url   = wc_get_cart_url();

	// Ottieni il totale del carrello come numero
	$cart_total = WC()->cart->get_total();

	$cart_icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" class="custom-cart-svg" aria-hidden="true" focusable="false"><circle cx="12.6667" cy="24.6667" r="2" fill="currentColor"></circle><circle cx="23.3333" cy="24.6667" r="2" fill="currentColor"></circle><path fill-rule="evenodd" clip-rule="evenodd" d="M9.28491 10.0356C9.47481 9.80216 9.75971 9.66667 10.0606 9.66667H25.3333C25.6232 9.66667 25.8989 9.79247 26.0888 10.0115C26.2787 10.2305 26.3643 10.5211 26.3233 10.8081L24.99 20.1414C24.9196 20.6341 24.4977 21 24 21H12C11.5261 21 11.1173 20.6674 11.0209 20.2034L9.08153 10.8701C9.02031 10.5755 9.09501 10.269 9.28491 10.0356ZM11.2898 11.6667L12.8136 19H23.1327L24.1803 11.6667H11.2898Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M5.66669 6.66667C5.66669 6.11438 6.1144 5.66667 6.66669 5.66667H9.33335C9.81664 5.66667 10.2308 6.01229 10.3172 6.48778L11.0445 10.4878C11.1433 11.0312 10.7829 11.5517 10.2395 11.6505C9.69614 11.7493 9.17555 11.3889 9.07676 10.8456L8.49878 7.66667H6.66669C6.1144 7.66667 5.66669 7.21895 5.66669 6.66667Z" fill="currentColor"></path></svg>';

	return '<div class="my-custom-mini-cart">
                <a href="' . esc_url( $cart_url ) . '" class="my-custom-mini-cart-button">
                	<span class="my-custom-cart-quantity-badge">' . esc_html( $cart_count ) . '</span>
                	<span class="my-custom-cart-icon">' . $cart_icon_svg . '</span>
                    <span class="my-custom-cart-amount">' . $cart_total . '</span> 
                </a>
            </div>';
}


add_shortcode( 'custom_cart_link', 'custom_cart_link_shortcode' );

function custom_cart_link_enqueue_style() {
	wp_enqueue_style( 'custom-cart-link-style', plugin_dir_url( __FILE__ ) . 'style.css' );
}

add_action( 'wp_enqueue_scripts', 'custom_cart_link_enqueue_style' );

remove_action( 'storefront_header', 'storefront_header_cart', 10 );
