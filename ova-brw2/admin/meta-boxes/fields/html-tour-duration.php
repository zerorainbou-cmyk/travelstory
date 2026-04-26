<?php if ( !defined( 'ABSPATH' ) ) exit();

do_action( OVABRW_PREFIX.'before_tour_product_options_duration', $this ); 

?>

<div id="ovabrw-options-duration" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Duration (Price Priority - No. 3)', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_tour_duration_content', $this ); ?>
		<span class="ovabrw-note" style="margin: 0 12px;">
			<?php esc_html_e( 'Set the duration of the tour', 'ova-brw' ); ?>
		</span>
		<?php
		// Duration type
		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'duration_type' ),
			'value' 		=> $this->get_meta_value( 'duration_type', 'fixed' ),
			'label' 		=> esc_html__( 'Type', 'ova-brw' ),
			'options' 		=> [
				'fixed' 	=> esc_html__( 'Multiple-day Tour', 'ova-brw' ),
				'timeslots' => esc_html__( 'One-day Tour', 'ova-brw' ),
				'period' 	=> esc_html__( 'Specific Date Ranges', 'ova-brw' )
			]
		]);

		// Get guest options
		$guest_options = $this->get_guest_options();
		foreach ( $guest_options as $k => $guest ) {
			$guest_class 	= 'ovabrw-guest-price';
			$wrapper_class 	= '';

			if ( 0 === $k ) {
				$guest_class 	.= ' ovabrw-regular-price';
				$wrapper_class 	= 'ovabrw-required';
			}

			woocommerce_wp_text_input([
				'id'        	=> $this->get_meta_name( $guest['name'].'_price' ),
				'class' 		=> $guest_class,
				'value'     	=> $this->get_meta_value( $guest['name'].'_price' ),
				'label'     	=> sprintf( esc_html__( '%s price/guest', 'ova-brw' ) . ' (' . esc_html( get_woocommerce_currency_symbol() ) . ')', esc_html( $guest['label'] ) ),
				'desc_tip' 		=> true,
				'description' 	=> sprintf( esc_html__( 'Regular %s price/guest', 'ova-brw' ), esc_html( $guest['label'] ) ),
				'data_type' 	=> 'price',
				'wrapper_class' => $wrapper_class
			]);
		}

		// Number of days
		woocommerce_wp_text_input([
			'type'              => 'number',
			'id'                => $this->get_meta_name( 'numberof_days' ),
			'class' 			=> 'ovabrw-input-required',
			'value'             => $this->get_meta_value( 'numberof_days' ),
			'placeholder' 		=> 1,
			'label'             => esc_html__( 'Number of days', 'ova-brw' ),
			'desc_tip' 			=> true,
			'description' 		=> esc_html__( 'Check out = Check in + Number of days', 'ova-brw' ),
			'custom_attributes' => [
				'step' 		=> 'any',
				'data-min' 	=> 1
			],
			'wrapper_class' 	=> 'ovabrw-required'
		]);

		woocommerce_wp_text_input([
			'id'        	=> $this->get_meta_name( 'standard_price' ),
			'class' 		=> 'ovabrw-input-required',
			'value'     	=> $this->get_meta_value( 'standard_price' ),
			'label'     	=> esc_html__( 'Standard price/guest', 'ova-brw' ) . ' (' . esc_html( get_woocommerce_currency_symbol() ) . ')',
			'desc_tip' 		=> true,
			'description' 	=> esc_html__( 'This price will be displayed on the Listing and Detail pages.', 'ova-brw' ),
			'data_type' 	=> 'price',
			'wrapper_class' => 'ovabrw-required'
		]);

		// Min - Max number of Guests
		woocommerce_wp_text_input([
			'type'              => 'number',
			'id'                => $this->get_meta_name( 'min_guest' ),
			'value'             => $this->get_meta_value( 'min_guest' ) ? $this->get_meta_value( 'min_guest' ) : 1,
			'label'             => esc_html__( 'Minimum number of guests', 'ova-brw' ),
			'desc_tip' 			=> true,	
			'description' 		=> esc_html__( 'Minimum number of guests', 'ova-brw' ),
			'custom_attributes' => [
				'step' 		=> 'any',
				'data-min' 	=> 0
			]
		]);
		woocommerce_wp_text_input([
			'type'              => 'number',
			'id'                => $this->get_meta_name( 'max_guest' ),
			'class' 			=> 'ovabrw-input-required',
			'value'             => $this->get_meta_value( 'max_guest' ),
			'label'             => esc_html__( 'Maximum number of guests', 'ova-brw' ),
			'desc_tip' 			=> true,	
			'description' 		=> esc_html__( 'Maximum number of guests', 'ova-brw' ),
			'custom_attributes' => [
				'step' 		=> 'any',
				'data-min' 	=> 1
			],
			'wrapper_class' 	=> 'ovabrw-required'
		]);

		// Duration type: Fixed
		include OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-duration-fixed.php';

		// Duration type: Time slots
		include OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-duration-time-slots.php';

		// Duration type: Period of Time
		include OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-duration-period.php';

		do_action( OVABRW_PREFIX.'tour_product_options_duration', $this ); 
		?>
		<?php do_action( $this->prefix.'after_tour_duration_content', $this ); ?>
	</div>
</div>