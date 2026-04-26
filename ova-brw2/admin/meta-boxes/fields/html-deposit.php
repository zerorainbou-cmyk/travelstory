<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div id="ovabrw-options-deposit" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Deposit', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_deposit_content', $this );

		woocommerce_wp_checkbox([
			'id' 			=> $this->get_meta_name( 'enable_deposit' ),
			'value' 		=> $this->get_meta_value( 'enable_deposit' ),
			'cbvalue' 		=> 'yes',
			'label' 		=> esc_html__( 'Enable deposit', 'ova-brw' ),
			'desc_tip' 		=> true,
			'description' 	=> esc_html__( 'An advance payment', 'ova-brw' )
		]);
		woocommerce_wp_checkbox([
			'id' 			=> $this->get_meta_name( 'force_deposit' ),
			'value' 		=> $this->get_meta_value( 'force_deposit' ),
			'cbvalue' 		=> 'yes',
			'label' 		=> esc_html__( 'Show full payment', 'ova-brw' ),
			'desc_tip' 		=> true,
			'description' 	=> esc_html__( '- Checked: Show full payment option in booking form. <br/>- Unchecked: Only deposit payment option', 'ova-brw' )
		]);
		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'default_deposit' ),
			'value' 		=> $this->get_meta_value( 'default_deposit', 'full' ),
			'label' 		=> esc_html__( 'Default selected', 'ova-brw' ),
			'options' 		=> [
				'full' 		=> esc_html__( 'Full Payment', 'ova-brw' ),
				'deposit' 	=> esc_html__( 'Pay Deposit', 'ova-brw' )
			],
			'desc_tip' 		=> true,
			'description' 	=> esc_html__( 'Full payment or Pay Deposit selected by default.', 'ova-brw' )
		]);
		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'type_deposit' ),
			'value' 		=> $this->get_meta_value( 'type_deposit', 'percent' ),
			'label' 		=> esc_html__( 'Deposit type', 'ova-brw' ),
			'options' 		=> [
				'percent'	=> esc_html__( 'a percentage amount of payment', 'ova-brw' ),
				'value'		=> esc_html__( 'a fixed amount of payment', 'ova-brw' )
			]
		]);
		woocommerce_wp_text_input([
			'id' 					=> $this->get_meta_name( 'amount_deposit' ),
			'label'					=> '',
			'desc_tip'				=> true,
			'description' 			=> esc_html__( 'Insert deposit amount', 'ova-brw' ),
			'placeholder' 			=> '50',
			'data_type' 			=> 'price',
			'value' 				=> $this->get_meta_value( 'amount_deposit' ),
			'custom_attributes' 	=> [
				'data-percent-unit'	=> '%',
				'data-fixed-unit'	=> get_woocommerce_currency_symbol()
			]
		]);
		
		do_action( $this->prefix.'after_deposit_content', $this ); ?>
	</div>
</div>