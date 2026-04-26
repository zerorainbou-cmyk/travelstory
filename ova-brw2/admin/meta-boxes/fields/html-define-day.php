<?php if ( !defined( 'ABSPATH' ) ) exit();

// Duration type
woocommerce_wp_radio([
	'id' 			=> $this->get_meta_name( 'define_1_day' ),
	'class' 		=> 'ovabrw-input-required',
	'wrapper_class' => 'ovabrw-required',
	'value' 		=> $this->get_meta_value( 'define_1_day', 'day' ),
	'label' 		=> esc_html__( 'Charge by', 'ova-brw' ),
	'desc_tip'    	=> true,
	'description' 	=> esc_html__( 'Calculate rental period:<br/> <strong>- Day</strong>: (Drop-off date) - (Pick-up date) + 1 <br/> <strong>- Night</strong>: (Drop-off date) - (Pick-up date) <br/> <strong>- Hour</strong>: (Drop-off date) - (Pick-up date) + X <br/> X = 1:  if (Drop-off Time) - (Pick-up Time) > 0 <br/>X = 0:  if (Drop-off Time) - (Pick-up Time) < 0', 'ova-brw' ),
	'options' 		=> [
		'day'	=> esc_html__( 'Day', 'ova-brw' ),
		'hotel'	=> esc_html__( 'Night', 'ova-brw' ),
		'hour'	=> esc_html__( 'Hour', 'ova-brw' )
	]
]);