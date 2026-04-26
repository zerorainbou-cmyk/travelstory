<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field ovabrw_show_number_vehicle_wrap">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
	</strong>
	<?php woocommerce_wp_radio([
		'id' 			=> $this->get_meta_name( 'show_number_vehicle' ),
		'value' 		=> $this->get_meta_value( 'show_number_vehicle', 'in_setting' ),
		'label' 		=> esc_html__( 'Show quantity', 'ova-brw' ),
		'desc_tip'		=> true,
		'description'	=> esc_html__( 'Global setting: Go to WooCommerce >> Settings >> Booking & Rental >> Product Details', 'ova-brw' ),
		'options' 		=> [
			'in_setting' 	=> esc_html__( 'Global setting', 'ova-brw' ),
			'yes'			=> esc_html__( 'Yes', 'ova-brw' ),
			'no'			=> esc_html__( 'No', 'ova-brw' )
		]
	]); ?>
</div>