<?php
/**
 * Plugin Name: Adrocket Remove Auto P
 * Description: Rimuove i tag <p> e <br> dall'editor di Gutenberg.
 * Version: 1.0
 * Author: Halexo Limited
 */

add_action('init', function() {
	remove_filter('the_content', 'wpautop');
	remove_filter('the_excerpt', 'wpautop');
}, 20);

function remove_the_wpautop_function() {
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_excerpt', 'wpautop' );
}

add_action( 'after_setup_theme', 'remove_the_wpautop_function' );