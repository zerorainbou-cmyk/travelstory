<?php if ( !defined( 'ABSPATH' ) ) exit;

// Show guests
if ( 'yes' != $this->get_meta_value( 'show_guests' ) ) return;

// Get guest options
$guest_options = $this->get_guest_options();
if ( !ovabrw_array_exists( $guest_options ) ) return;

?>
<div id="ovabrw-options-guests" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Guests', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<?php do_action( $this->prefix.'before_guests_fields_content', $this ); ?>
	<div class="advanced-content">
		<?php
			foreach ( $guest_options as $guest ) {
				// Required price
				if ( 'yes' === ovabrw_get_meta_data( 'required_price', $guest ) ) {
					woocommerce_wp_text_input([
						'id'                => $this->get_meta_name( $guest['name'].'_price' ),
						'class' 			=> 'ovabrw-input-required',
						'value'             => $this->get_meta_value( $guest['name'].'_price' ),
						'label'             => sprintf( esc_html__( '%s price/guest', 'ova-brw' ) . ' (' . esc_html( get_woocommerce_currency_symbol() ) . ')', esc_html( $guest['label'] ) ),
						'desc_tip' 			=> true,	
						'description' 		=> sprintf( esc_html__( 'Regular %s price/guest', 'ova-brw' ), esc_html( $guest['label'] ) ),
						'data_type' 		=> 'price',
						'wrapper_class' 	=> 'ovabrw-required'
					]);
				}

				// Min guests
				woocommerce_wp_text_input([
					'type'              => 'number',
					'id'                => $this->get_meta_name( 'min_'.$guest['name'] ),
					'class' 			=> 'ovabrw-input-number',
					'wrapper_class' 	=> 'ovabrw-min-guest-field',
					'value'             => $this->get_meta_value( 'min_'.$guest['name'] ),
					'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
					'label'             => sprintf( esc_html__( 'Minimum number of %s', 'ova-brw' ), $guest['label'] ),
					'desc_tip' 			=> true,	
					'description' 		=> sprintf( esc_html__( 'Minimum number of %s per booking', 'ova-brw' ), $guest['label'] ),
					'custom_attributes' => [
						'step' 		=> 'any',
						'data-min' 	=> 0
					]
				]);

				// Max guests
				woocommerce_wp_text_input([
					'type'              => 'number',
					'id'                => $this->get_meta_name( 'max_'.$guest['name'] ),
					'class' 			=> 'ovabrw-input-number',
					'wrapper_class' 	=> 'ovabrw-max-guest-field',
					'value'             => $this->get_meta_value( 'max_'.$guest['name'] ),
					'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
					'label'             => sprintf( esc_html__( 'Maximum number of %s', 'ova-brw' ), $guest['label'] ),
					'desc_tip' 			=> true,	
					'description' 		=> sprintf( esc_html__( 'Maximum number of %s per booking', 'ova-brw' ), $guest['label'] ),
					'custom_attributes' => [
						'step' 		=> 'any',
						'data-min' 	=> 0
					]
				]);
			} // END loop

			// Minimum number of guest
			woocommerce_wp_text_input([
				'type' 				=> 'number',
				'id' 				=> $this->get_meta_name( 'min_guest' ),
				'class' 			=> 'ovabrw-input-number',
				'value' 			=> $this->get_meta_value( 'min_guest' ),
				'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
				'label' 			=> esc_html__( 'Minimum number of guests', 'ova-brw' ),
				'desc_tip'    		=> true,
				'description' 		=> esc_html__( 'Minimum total number of guests.', 'ova-brw' ),
				'custom_attributes' => [
					'step' 		=> 'any',
					'data-min' 	=> 0
				]
			]);

			woocommerce_wp_text_input([
				'type'              => 'number',
				'id'                => $this->get_meta_name( 'max_guest' ),
				'class' 			=> 'ovabrw-input-number',
				'value'             => $this->get_meta_value( 'max_guest' ),
				'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
				'label'             => esc_html__( 'Maximum number of guests', 'ova-brw' ),
				'desc_tip' 			=> true,	
				'description' 		=> esc_html__( 'Maximum total number of guests', 'ova-brw' ),
				'custom_attributes' => [
					'step' 		=> 'any',
					'data-min' 	=> 1
				]
			]);
		?>
	</div>
	<?php do_action( $this->prefix.'after_guests_fields_content', $this ); ?>
</div>