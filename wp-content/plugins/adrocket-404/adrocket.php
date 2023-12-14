<?php

/**
 * Plugin Name: Adrocket Custom 404 Redirect
 * Description: Redirects specified pages to a 404 page.
 * Version: 1.0
 * Author: Halexo Limited
 */

add_action( 'template_redirect', 'custom_404_redirect' );

function custom_404_redirect() {

	// home page redirect to shop page

	if ( is_front_page() ) {
		wp_redirect( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) );
		exit;
	}

	$redirect_pages = [
		'blog',
		'another-page',
		'sample-page',
		'archive',
		'category',
		'tag',
		'author',
		'year',
		'month',
		'day',
	]; // Aggiungi gli slug delle pagine qui

	if ( in_array( $GLOBALS['wp_query']->query_vars['pagename'], $redirect_pages ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		get_template_part( 404 );
		exit();
	}

	// also disable all nested pages e.g. year/month/day

	if ( is_year() || is_month() || is_day() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		get_template_part( 404 );
		exit();
	}

	// also disable all nested categories directories

	if ( is_category() || is_tag() || is_author() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		get_template_part( 404 );
		exit();
	}

	// also disable all nested non shop pages

	if ( is_page() && ! is_shop() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		get_template_part( 404 );
		exit();
	}
}
