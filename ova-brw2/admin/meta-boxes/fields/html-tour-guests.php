<?php if ( !defined( 'ABSPATH' ) ) exit;

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
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_tour_guests_content', $this );

			foreach ( $guest_options as $guest ) {
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

		do_action( $this->prefix.'after_tour_guests_content', $this );
		?>
	</div>
</div>