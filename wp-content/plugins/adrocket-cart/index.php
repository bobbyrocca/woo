<?php
/**
 * Plugin Name: Adrocket Cart Item Manager
 * Description: Gestisce gli articoli nel carrello.
 * Version: 1.0
 * Author: Halexo Limited
 */
// Prevenire accesso diretto al file.
defined( 'ABSPATH' ) || exit;

function adrocket_cart_enqueue_scripts() {
	if ( is_cart() ) {
		wp_enqueue_script( 'adrocket-cart-manager-js', plugin_dir_url( __FILE__ ) . 'js/cart.js', array( 'jquery' ), microtime(), true );
	}
}

add_action( 'wp_enqueue_scripts', 'adrocket_cart_enqueue_scripts' );

function adrocket_cart_enqueue_css() {
	if ( is_cart() ) {
		wp_enqueue_style( 'adrocket-cart-manager-css', plugin_dir_url( __FILE__ ) . 'css/style.css?v=' . microtime() );
	}
}

add_action( 'wp_enqueue_scripts', 'adrocket_cart_enqueue_css' );