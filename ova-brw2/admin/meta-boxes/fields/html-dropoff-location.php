<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field manager-show-dropoff-location">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Drop-off location', 'ova-brw' ); ?>
	</strong>
	<?php
		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'show_pickoff_location_product' ),
			'value' 		=> $this->get_meta_value( 'show_pickoff_location_product', 'in_setting' ),
			'options' 		=> [
				'in_setting' 	=> esc_html__( 'Category setting', 'ova-brw' ),
				'yes' 			=> esc_html__( 'Yes', 'ova-brw' ),
				'no' 			=> esc_html__( 'No', 'ova-brw' )
			],
			'label'       	=> esc_html__( 'Show', 'ova-brw' ),
			'desc_tip'		=> true,
			'description'	=> esc_html__( 'Category setting: Setup in per category', 'ova-brw' )
		]);

		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'show_other_location_dropoff_product' ),
			'value' 		=> $this->get_meta_value( 'show_other_location_dropoff_product', 'yes' ),
			'options' 		=> [
				'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
				'no'	=> esc_html__( 'No', 'ova-brw' )
			],
			'label' 		=> esc_html__( 'Enter another location in form', 'ova-brw' ),
			'desc_tip'		=> true,
			'description'	=> esc_html__( 'Enter another location in booking or request booking form', 'ova-brw' )
		]);
	?>
</div>