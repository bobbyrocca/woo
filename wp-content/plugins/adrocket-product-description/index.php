<?php
/**
 * Plugin Name: Adrocket Product Description Shortcodes
 * Description: Adds a shortcode to display the product description in a custom tab.
 * Version: 1.0
 * Author: Halexo Limited
 */
defined( 'ABSPATH' ) || exit;

// Enqueue Scripts and Styles
function adrocket_p_description_enqueue_assets() {
	if ( is_product() ) {
		wp_enqueue_script( 'adrocket-product-description-js', plugin_dir_url( __FILE__ ) . 'js/product-description.js', array( 'jquery' ), microtime(), true );
		wp_enqueue_style( 'adrocket-product-description-css', plugin_dir_url( __FILE__ ) . 'css/style.css?v=' . microtime() );
	}
}

add_action( 'wp_enqueue_scripts', 'adrocket_p_description_enqueue_assets' );

function adrocket_p_description_shortcode( array $input ) {

	ob_start();
	?>
	<?php if ( $input['type'] == '1' ) { ?>
        <div class="custom-icons-grid">
            <div class="custom-icon-block">
                <img class="icon-img" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/guarantee.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <strong class="icon-title">Pagamento Sicuro</strong>
                    <p class="icon-p">Il pagamento è garantito e sicuro grazie alla crittografia SSL.</p>
                </div>
            </div>
            <div class="custom-icon-block">
                <img class="icon-img" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/cod.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <strong class="icon-title">Paga alla Consegna</strong>
                    <p class="icon-p">Puoi pagare direttamente al corriere al momento della consegna del tuo ordine.</p>
                </div>
            </div>
            <div class="custom-icon-block">
                <img class="icon-img" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/support.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <strong class="icon-title">Supporto e Assistenza</strong>
                    <p class="icon-p">Il nostro team di supporto è sempre a disposizione per risolvere qualsiasi problema.</p>
                </div>
            </div>
            <div class="custom-icon-block">
                <img class="icon-img" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/truck.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <strong class="icon-title">Spedizione rapida</strong>
                    <p class="icon-p">La spedizione è rapida e tracciata, in modo da poter seguire il percorso del tuo ordine.</p>
                </div>
            </div>
        </div>
	<?php } elseif ( $input['type'] == 2 ) { ?>
        <div class="custom-icons-grid cols-5">
            <div class="custom-icon-block type-2">
                <img class="icon-img max-high" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/arrows-to-eye-regular.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <p class="icon-p"><strong>Stimola creatività e immaginazione</strong></p>
                </div>
            </div>
            <div class="custom-icon-block type-2">
                <img class="icon-img max-high" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/brain-circuit-thin.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <p class="icon-p"><strong>Promuove sviluppo cognitivo</strong></p>
                </div>
            </div>
            <div class="custom-icon-block type-2">
                <img class="icon-img max-high" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/head-side-brain-sharp-light.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <p class="icon-p"><strong>Aiuta a sviluppare la concentrazione</strong></p>
                </div>
            </div>
            <div class="custom-icon-block type-2">
                <img class="icon-img max-high" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/wand-magic-sparkles-solid.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <p class="icon-p"><strong>Coordinamento occhio-mano</strong></p>
                </div>
            </div>
            <div class="custom-icon-block type-2">
                <img class="icon-img max-high" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/heart-solid.svg'; ?>" alt="Description Icon">
                <div class="icon-text">
                    <p class="icon-p"><strong>Momenti in famiglia</strong></p>
                </div>
            </div>
        </div>
	<?php } ?>
	<?php
	return ob_get_clean();
}

add_shortcode( 'product_icons_section', 'adrocket_p_description_shortcode' );
