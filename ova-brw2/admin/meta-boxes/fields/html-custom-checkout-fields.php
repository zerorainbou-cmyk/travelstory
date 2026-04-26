<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get custom checkout fields
$cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

// Get special custom checkout fields
$special_cckf = $this->get_meta_value( 'product_custom_checkout_field' );
if ( $special_cckf && !is_array( $special_cckf ) ) {
	$special_cckf = explode( ',', $special_cckf );
	$special_cckf = array_map( 'trim', $special_cckf );
}

?>

<div class="ovabrw-form-field">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Custom checkout fields', 'ova-brw' ); ?>
	</strong>
	<?php
		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'manage_custom_checkout_field' ),
			'value' 		=> $this->get_meta_value( 'manage_custom_checkout_field', 'all' ),
			'label' 		=> esc_html__( 'Display custom fields from', 'ova-brw' ),
			'desc_tip'		=> true,
			'description' 	=> esc_html__( '- Category setting: Display all fields that setup per category. <br/>- Local: Only display some fields for this product', 'ova-brw' ),
			'options' 		=> [
				'all' 	=> esc_html__( 'Category setting', 'ova-brw' ),
				'new' 	=> esc_html__( 'Local', 'ova-brw' ),
				'none' 	=> esc_html__( 'None', 'ova-brw' )
			]
		]);
	?>
	<p class="form-field ovabrw_product_custom_checkout_field ovabrw-required">
		<label for="ovabrw_product_custom_checkout_field">
			<?php esc_html_e( 'Select checkout fields', 'ova-brw' ); ?>
		</label>
		<select
			name="<?php echo esc_attr( $this->get_meta_name( 'product_custom_checkout_field[]' ) ); ?>"
			id="ovabrw_product_custom_checkout_field"
			class="wc-enhanced-select-nostd"
			data-placeholder="<?php esc_html_e( 'Select custom checkout field...', 'ova-brw' ); ?>"
			multiple>
			<?php if ( ovabrw_array_exists( $cckf ) ):
				foreach ( $cckf as $name => $field ):
					if ( !$field['enabled'] ) continue;

					$label = ovabrw_get_meta_data( 'label', $field );
			?>
				<option value="<?php echo esc_attr( $name ); ?>"<?php ovabrw_selected( $name, $special_cckf ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; endif; ?>
		</select>
	</p>
</div>