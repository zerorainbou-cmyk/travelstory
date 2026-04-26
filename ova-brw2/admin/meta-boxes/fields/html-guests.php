<?php if ( !defined( 'ABSPATH' ) ) exit;

// Show guest options (new)
if ( 'yes' === $this->get_meta_value( 'show_guests' ) ) return;

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Guests', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_guests_content', $this );

			// Guests
			woocommerce_wp_text_input([
				'id' 				=> $this->get_meta_name('min_guests'),
				'class' 			=> 'short',
				'label' 			=> esc_html__( 'Minimum number of guests', 'ova-brw' ),
				'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
				'desc_tip'    		=> true,
				'description' 		=> esc_html__( 'Minimum number of guests that can be booked', 'ova-brw' ),
				'type' 				=> 'number',
				'value' 			=> $this->get_meta_value( 'min_guests' ),
				'custom_attributes' => [
					'data-min' => 0
				]
			]);
			
			woocommerce_wp_text_input([
				'id' 				=> $this->get_meta_name('max_guests'),
				'class' 			=> 'short',
				'label' 			=> esc_html__( 'Maximum number of guests', 'ova-brw' ),
				'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
				'desc_tip'    		=> true,
				'description' 		=> esc_html__( 'Maximum number of guests that can be booked', 'ova-brw' ),
				'type' 				=> 'number',
				'value' 			=> $this->get_meta_value( 'max_guests' ),
				'custom_attributes' => [
					'data-min' => 0
				]
			]); // End

			// Adults
			woocommerce_wp_text_input([
				'id' 				=> $this->get_meta_name('min_adults'),
				'class' 			=> 'short',
				'label' 			=> esc_html__( 'Minimum number of Adults', 'ova-brw' ),
				'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
				'desc_tip'    		=> true,
				'description' 		=> esc_html__( 'Minimum number of Adults that can be booked', 'ova-brw' ),
				'type' 				=> 'number',
				'value' 			=> $this->get_meta_value( 'min_adults' ),
				'custom_attributes' => [
					'data-min' => 0
				]
			]);

			woocommerce_wp_text_input([
				'id' 				=> $this->get_meta_name('max_adults'),
				'class' 			=> 'short',
				'label' 			=> esc_html__( 'Maximum number of Adults', 'ova-brw' ),
				'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
				'desc_tip'    		=> true,
				'description' 		=> esc_html__( 'Maximum number of Adults that can be booked', 'ova-brw' ),
				'type' 				=> 'number',
				'value' 			=> $this->get_meta_value( 'max_adults' ),
				'custom_attributes' => [
					'data-min' => 0
				]
			]); // End

			// Children
			if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ) {
				woocommerce_wp_text_input([
					'id' 				=> $this->get_meta_name('min_children'),
					'class' 			=> 'short',
					'label' 			=> esc_html__( 'Minimum number of Children', 'ova-brw' ),
					'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
					'desc_tip'    		=> true,
					'description' 		=> esc_html__( 'Minimum number of Children that can be booked', 'ova-brw' ),
					'type' 				=> 'number',
					'value' 			=> $this->get_meta_value( 'min_children' ),
					'custom_attributes' => [
						'data-min' => 0
					]
				]);

				woocommerce_wp_text_input([
					'id' 				=> $this->get_meta_name('max_children'),
					'class' 			=> 'short',
					'label' 			=> esc_html__( 'Maximum number of Children', 'ova-brw' ),
					'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
					'desc_tip'    		=> true,
					'description' 		=> esc_html__( 'Maximum number of Children that can be booked', 'ova-brw' ),
					'type' 				=> 'number',
					'value' 			=> $this->get_meta_value( 'max_children' ),
					'custom_attributes' => [
						'data-min' => 0
					]
				]);
			} // End

			// Babies
			if ( apply_filters( OVABRW_PREFIX.'show_babies', true ) ) {
				woocommerce_wp_text_input([
					'id' 				=> $this->get_meta_name('min_babies'),
					'class' 			=> 'short',
					'label' 			=> esc_html__( 'Minimum number of Babies', 'ova-brw' ),
					'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
					'desc_tip'    		=> true,
					'description' 		=> esc_html__( 'Minimum number of Babies that can be booked', 'ova-brw' ),
					'type' 				=> 'number',
					'value' 			=> $this->get_meta_value( 'min_babies' ),
					'custom_attributes' => [
						'data-min' => 0
					]
				]);

				woocommerce_wp_text_input([
					'id' 				=> $this->get_meta_name('max_babies'),
					'class' 			=> 'short',
					'label' 			=> esc_html__( 'Maximum number of Babies', 'ova-brw' ),
					'placeholder' 		=> esc_html__( 'number', 'ova-brw' ),
					'desc_tip'    		=> true,
					'description' 		=> esc_html__( 'Maximum number of Babies that can be booked', 'ova-brw' ),
					'type' 				=> 'number',
					'value' 			=> $this->get_meta_value( 'max_babies' ),
					'custom_attributes' => [
						'data-min' => 0
					]
				]);
			} // END
		
		do_action( $this->prefix.'after_guests_content', $this ); ?>
	</div>
</div>