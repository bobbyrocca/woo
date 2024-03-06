<?php
function remove_actions() {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

	remove_action( 'storefront_header', 'storefront_skip_links', 5 );
	remove_action( 'storefront_header', 'storefront_social_icons', 10 );

	// Remove storefront_primary_navigation
	remove_action( 'storefront_header', 'storefront_header_container_close', 41 );
	add_action( 'storefront_header', 'storefront_header_cart', 41 );
	add_action( 'storefront_header', 'storefront_header_container_close', 42 );
	remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 );
	remove_action( 'storefront_header', 'storefront_primary_navigation', 50 );
	remove_action( 'storefront_header', 'storefront_header_cart', 60 );
	remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close',68  );

	remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );

	remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );

	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

}
add_action( 'init', 'remove_actions' );
