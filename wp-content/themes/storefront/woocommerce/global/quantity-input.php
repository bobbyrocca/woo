<?php
defined( 'ABSPATH' ) || exit;

// Definisci la label per il campo di quantità
$label = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'woocommerce' ), wp_strip_all_tags( $args['product_name'] ) ) : esc_html__( 'Quantity', 'woocommerce' );

// Assicurati che i valori di min, max e step siano impostati
$min_value = $min_value ?? 1; // Default to 1 se non è impostato
$max_value = $max_value ?? 10; // Default to 10 se non è impostato (modifica questo secondo necessità)
$step = $step ?? 1; // Default to 1 se non è impostato
?>
<div class="quantity">
	<?php do_action( 'woocommerce_before_quantity_input_field' ); ?>
    <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_attr( $label ); ?></label>
    <select
            name="<?php echo esc_attr( $input_name ); ?>"
            id="<?php echo esc_attr( $input_id ); ?>"
            class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
            aria-label="<?php esc_attr_e( 'Product quantity', 'woocommerce' ); ?>"
		<?php if ( $readonly ) : ?>
            disabled="disabled"
		<?php endif; ?>
    >
		<?php
		for ( $count = $min_value; $count <= $max_value; $count += $step ) {
			echo '<option value="' . esc_attr( $count ) . '"' . selected( $input_value, $count, false ) . '>' . esc_html( $count ) . '</option>';
		}
		?>
    </select>
	<?php do_action( 'woocommerce_after_quantity_input_field' ); ?>
</div>
