<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field">
	<?php woocommerce_wp_text_input([
		'type' 			=> 'number',
		'id' 			=> $this->get_meta_name( 'car_order' ),
		'class' 		=> 'short ',
		'value' 		=> $this->get_meta_value( 'car_order' ),
		'label' 		=> esc_html__( 'Product Display Position', 'ova-brw' ),
		'placeholder' 	=> '1',
		'desc_tip' 		=> true,
		'description' 	=> esc_html__( 'Product display position in the  product listing page. Use in some elements.', 'ova-brw' )
	]); ?>
</div>