<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Salsa&display=swap" rel="stylesheet">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php do_action( 'storefront_before_site' ); ?>
<div id="page" class="hfeed site">
	<?php do_action( 'storefront_before_header' ); ?>

    <header class="custom-site-header">
        <div class="custom-header-container">
            <div class="custom-site-branding">
	        <?php if ( ! is_cart() ) : ?>
				<?php storefront_site_title_or_logo(); ?>
	        <?php endif; ?>
            </div>
	        <?php if ( ! is_checkout() ) : ?>
            <div class="header-text">
                <strong>Spedizione gratuita</strong> per ordini superiori a <strong>50€</strong>
            </div>
	        <?php endif; ?>
            <div id="site-header-cart" class="custom-header-cart">
				<?php
				if ( ! is_cart() && ! is_checkout() ) {
					storefront_cart_link();
				}
				?>
            </div>
        </div>
    </header>

	<?php if ( ! is_checkout() ) : ?>
        <div class="top-header">
            <p class="top-header-text">
                <strong>Spedizione gratuita</strong> per ordini superiori a <strong>50€</strong>
            </p>
            <!--<img class="header-stripe" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/storefront/images/header.svg" alt="">-->
        </div>
	<?php endif; ?>
    <!-- #masthead -->
	<?php
	/**
	 * Functions hooked in to storefront_before_content
	 *
	 * @hooked storefront_header_widget_region - 10
	 * @hooked woocommerce_breadcrumb - 10
	 */
	do_action( 'storefront_before_content' );
	?>
    <div id="content" class="site-content" tabindex="-1">
        <div class="col-full column">
<?php
do_action( 'storefront_content_top' );
