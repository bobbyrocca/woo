<?php
add_action( 'wp_enqueue_scripts', 'adrocket_child_enqueue_styles', 1000 );

function adrocket_child_enqueue_styles() {
	wp_enqueue_style(
		'adrocket-child-style',
		get_stylesheet_uri()
	);
}