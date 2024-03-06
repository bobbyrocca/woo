<?php
/**
 * Plugin Name: Adrocket Shortcodes
 * Description: Shortcodes for Adrocket
 * Version: 1.0
 * Author: Halexo Limited
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function adrocket_categories_shortcode(): string {
	$categorie = get_terms( 'product_cat', array( 'hide_empty' => false ) );
	$menu_html = '<select onchange="if (this.value) window.location.href=this.value;" class="category-menu">';
	$menu_html .= '<option value="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '">All Categories</option>';

	foreach ( $categorie as $categoria ) {
		$link      = get_term_link( $categoria->term_id );

		// if category is current category, add selected attribute, else go to next category

		if ( is_product_category( $categoria->slug ) ) {
			$menu_html .= '<option value="' . esc_url( $link ) . '" selected>' . esc_html( $categoria->name ) . '</option>';
		} else {
			$menu_html .= '<option value="' . esc_url( $link ) . '">' . esc_html( $categoria->name ) . '</option>';
		}
	}

	$menu_html .= '</select>';

	return $menu_html;
}

add_shortcode( 'categories_menu', 'adrocket_categories_shortcode' );
