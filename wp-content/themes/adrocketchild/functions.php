<?php
add_action( 'wp_enqueue_scripts', 'grand_sunrise_enqueue_styles' );

function grand_sunrise_enqueue_styles() {
	wp_enqueue_style(
		'adrocket-child-style',
		get_stylesheet_uri()
	);
}