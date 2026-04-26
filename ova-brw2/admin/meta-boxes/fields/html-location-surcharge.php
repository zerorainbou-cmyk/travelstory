<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get locations
$locations = OVABRW()->options->get_locations();

// Get pick-up location surcharge
$pickup_locations = $this->get_meta_value( 'pickup_location_surcharge', [] );

// Get pick-up location surcharge price
$pickup_surcharge_price = $this->get_meta_value( 'pickup_surcharge_price', [] );

// Get drop-off location surcharge
$dropoff_locations = $this->get_meta_value( 'dropoff_location_surcharge', [] );

// Get drop-off location surcharge price
$dropoff_surcharge_price = $this->get_meta_value( 'dropoff_surcharge_price', [] );

?>

<div id="options-location-surcharge" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label"><?php esc_html_e( 'Location surcharge', 'ova-brw' ); ?></h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_location_surcharge_content', $this ); ?>
		<?php woocommerce_wp_checkbox([
			'id' 			=> $this->get_meta_name( 'cal_location_surcharge' ),
			'value' 		=> $this->get_meta_value( 'cal_location_surcharge' ),
			'cbvalue' 		=> 'yes',
			'label' 		=> esc_html__( 'Charge only once if the pick-up location and drop-off location are the same', 'ova-brw' )
		]); ?>
		<div class="ovabrw-table">
			<table class="widefat" style="margin-bottom: 20px;">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Price', 'ova-brw' ); ?>
						</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody class="ovabrw-sortable">
				<?php if ( ovabrw_array_exists( $locations ) ):
					// Loop
					foreach ( $pickup_locations as $i => $pickup ):
						$price = ovabrw_get_meta_data( $i, $pickup_surcharge_price );
					?>
						<tr>
							<td width="49%">
								<?php ovabrw_wp_select_input([
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name( 'pickup_location_surcharge[]' ),
									'value' 		=> $pickup,
									'placeholder' 	=> esc_html__( 'Select location', 'ova-brw' ),
									'options' 		=> $locations
								]); ?>
						    </td>
						    <td width="49%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-price',
									'name' 			=> $this->get_meta_name( 'pickup_surcharge_price[]' ),
									'value' 		=> $price,
									'data_type' 	=> 'price',
									'placeholder' 	=> '10'
								]); ?>
						    </td>
						    <td width="1%" class="ovabrw-sort-icon">
								<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
							</td>
							<td width="1%">
								<button class="button ovabrw-remove-location-surcharge" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
							</td>
						</tr>
					<?php endforeach;
				endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4">
							<button class="button ovabrw-add-location-surcharge" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-pickup-location-surcharge-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add location', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
			<table class="widefat">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Drop-off location', 'ova-brw' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Price', 'ova-brw' ); ?>
						</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody class="ovabrw-sortable">
				<?php if ( ovabrw_array_exists( $locations ) ):
					// Loop
					foreach ( $dropoff_locations as $i => $dropoff ):
						$price = ovabrw_get_meta_data( $i, $dropoff_surcharge_price );
					?>
						<tr>
							<td width="49%">
								<?php ovabrw_wp_select_input([
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name( 'dropoff_location_surcharge[]' ),
									'value' 		=> $dropoff,
									'placeholder' 	=> esc_html__( 'Select location', 'ova-brw' ),
									'options' 		=> $locations
								]); ?>
						    </td>
						    <td width="49%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-price',
									'name' 			=> $this->get_meta_name( 'dropoff_surcharge_price[]' ),
									'value' 		=> $price,
									'data_type' 	=> 'price',
									'placeholder' 	=> '10'
								]); ?>
						    </td>
						    <td width="1%" class="ovabrw-sort-icon">
								<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
							</td>
							<td width="1%">
								<button class="button ovabrw-remove-location-surcharge" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
							</td>
						</tr>
					<?php endforeach;
				endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4">
							<button class="button ovabrw-add-location-surcharge" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-dropoff-location-surcharge-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add location', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php do_action( $this->prefix.'after_location_surcharge_content', $this ); ?>
	</div>
</div>