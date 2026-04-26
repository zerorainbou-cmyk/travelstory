<?php defined( 'ABSPATH' ) || exit; ?>

<div id="ovabrw-options-inventory" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Inventory', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_inventory_content', $this );

			if ( $this->is_type( 'hotel' ) ) {
				woocommerce_wp_radio([
					'id' 			=> $this->get_meta_name( 'manage_store' ),
					'value' 		=> $this->get_meta_value( 'manage_store', 'store' ),
					'label' 		=> esc_html__( 'Control inventory', 'ova-brw' ),
					'options' 		=> [
						'store'		=> esc_html__( 'Automatically', 'ova-brw' )
					]
				]);
			} else {
				woocommerce_wp_radio([
					'id' 			=> $this->get_meta_name( 'manage_store' ),
					'value' 		=> $this->get_meta_value( 'manage_store', 'store' ),
					'label' 		=> esc_html__( 'Control inventory', 'ova-brw' ),
					'desc_tip'		=> true,
					'description'	=> esc_html__( '- Manually: If you want to assign names to each specific product in stock, the customer will see that name on the order detail page. <br/> - Recommend: Automatically', 'ova-brw' ),
					'options' 		=> [
						'store'			=> esc_html__( 'Automatically', 'ova-brw' ),
						'id_vehicle' 	=> esc_html__( 'Manually', 'ova-brw' )
					]
				]);
			}

			// Stock Quantity
			woocommerce_wp_text_input([
				'id' 				=> $this->get_meta_name( 'car_count' ),
				'class' 			=> 'ovabrw-input-required',
				'wrapper_class' 	=> 'ovabrw-required',
				'label' 			=> esc_html__( 'Stock quantity', 'ova-brw' ),
				'placeholder' 		=> '10',
				'type' 				=> 'number',
				'value' 			=> $this->get_meta_value( 'car_count' ) ? $this->get_meta_value( 'car_count' ) : 1,
				'custom_attributes' => [
					'min' => 1
				]
			]);

			// Vehicle IDs
			woocommerce_wp_select([
				'id' 				=> $this->get_meta_name( 'id_vehicles[]' ),
				'class' 			=> 'ovabrw-select2 ovabrw-input-required',
				'wrapper_class' 	=> 'ovabrw-form-field ovabrw-vehicle-ids ovabrw-required',
				'label' 			=> esc_html__( 'Choose Vehicles', 'ova-brw' ),
				'desc_tip'    		=> 'true',
				'options' 			=> OVABRW()->options->get_vehicldes(),
				'description' 		=> esc_html__( 'You must select at least one vehicle. If you want to add a Vehicle, you need to go to: Manage Vehicle >> Add Vehicles', 'ova-brw' ),
				'value' 			=> $this->get_meta_value( 'id_vehicles' ),
				'custom_attributes' => [
					'data-placeholder' 	=> esc_html__( 'Choose Vehicle ID...', 'ova-brw' ),
					'multiple' 			=> 'multiple'
				]
			]);

		do_action( $this->prefix.'after_inventory_content', $this ); ?>
	</div>
</div>