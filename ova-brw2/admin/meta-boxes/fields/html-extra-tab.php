<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Extra tab', 'ova-brw' ); ?>
	</strong>
	<?php
		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'manage_extra_tab' ),
			'value' 		=> $this->get_meta_value( 'manage_extra_tab', 'in_setting' ),
			'label' 		=> esc_html__( 'Display content by', 'ova-brw' ),
			'options' 		=> [
				'in_setting' 	=> esc_html__( 'Global setting', 'ova-brw' ),
				'new_form' 		=> esc_html__( 'Local', 'ova-brw' ),
				'no' 			=> esc_html__( 'None', 'ova-brw' )
			],
			'desc_tip'		=> true,
	        'description' 	=> esc_html__( '- Display extra tab beside Description & Reviews tab. <br/>- Global setting: WooCommerce >> Settings >> Booking & Rental >> Product Details <br/>- Empty content: The tab will hide ', 'ova-brw' )
		]);

		woocommerce_wp_textarea_input([
			'id' 			=> $this->get_meta_name('extra_tab_shortcode'),
			'wrapper_class' => 'ovabrw-required',
	        'placeholder' 	=> esc_html__( '[contact-form-7 id="205" title="Contact form 1"]', 'ova-brw' ),
	        'label' 		=> esc_html__( 'New shortcode', 'ova-brw' ),
	        'value' 		=> $this->get_meta_value('extra_tab_shortcode'),
	        'desc_tip'		=> true,
	        'description' 	=> esc_html__( 'Insert shortcode or text', 'ova-brw' )
		]);
	?>
</div>