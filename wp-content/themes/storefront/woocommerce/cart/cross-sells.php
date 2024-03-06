<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) : ?>
    <div class="cross-sells">
		<?php woocommerce_product_loop_start(); ?>

		<?php foreach ( $cross_sells as $cross_sell ) : ?>
			<?php
			$post_object = get_post( $cross_sell->get_id() );

			setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

			$x_seller = wc_get_product( $cross_sell->get_id() );

			//wc_get_template_part( 'content', 'product' );
			?>
            <div class="product-item">
                <!-- Div per l'immagine del prodotto -->
                <div class="product-image">
					<?php if ( has_post_thumbnail() ) {
						echo get_the_post_thumbnail( $post_object->ID, 'shop_catalog' );
					} ?>
                </div>
                <div class="product-info-wrap">
                    <!-- Div per il nome del prodotto -->
                    <div class="x-sell-product-name">
                        <h2 class="up-seller"><?php echo esc_html( $x_seller->get_name() ); ?></h2>
                    </div>
                    <!-- Descrizione breve del prodotto -->
                    <div class="product-short-description">
						<?php echo apply_filters( 'woocommerce_short_description', $post_object->post_excerpt ); ?>
                    </div>
                    <div class="price-button">
                        <!-- Prezzi del prodotto -->
                        <div class="product-pricing">
							<?php if ( $x_seller->is_on_sale() ) : ?>
                                <span class="price sale-price"><?php echo wc_price( $x_seller->get_sale_price() ); ?></span>
                                <del class="price regular-price"><?php echo wc_price( $x_seller->get_regular_price() ); ?></del>
							<?php else : ?>
                                <span class="price"><?php echo esc_html( $x_seller->get_regular_price() ); ?></span>
							<?php endif; ?>
                        </div>
                        <!-- Link per aggiungere al carrello -->
                        <a href="?add-to-cart=<?= esc_attr( $x_seller->get_id() ); ?>" class="custom-add-to-cart-1"><?= esc_html__( 'Aggiungi al carrello', 'woocommerce' ); ?></a>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>
    </div>
<?php
endif;

wp_reset_postdata();
