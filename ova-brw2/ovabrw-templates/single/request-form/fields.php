<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Rental type
$rental_type = $product->get_rental_type();

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

// Show phone
$show_phone = ovabrw_get_setting( 'request_booking_form_show_number', 'yes' );

// Show address
$show_address = ovabrw_get_setting( 'request_booking_form_show_address', 'yes' );

// Pick-up location
$pickup_location = ovabrw_get_meta_data( 'pickup_location', $_GET );

// Drop-off location
$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $_GET );

// Pick-up date
$pickup_date = ovabrw_get_meta_data( 'pickup_date', $_GET );

// Drop-off date
$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $_GET );

?>
<div class="rental_item">
	<label for="<?php echo esc_attr( $product->get_meta_key( 'name' ) ); ?>">
		<?php esc_html_e( 'Name', 'ova-brw' ); ?>
	</label>
	<?php ovabrw_text_input([
		'type' 			=> 'text',
		'id' 			=> $product->get_meta_key( 'name' ),
		'name' 			=> $product->get_meta_key( 'name' ),
		'placeholder' 	=> esc_html__( 'Your name', 'ova-brw' ),
		'required' 		=> true
	]); ?>
</div>
<div class="rental_item">
	<label for="<?php echo esc_attr( $product->get_meta_key( 'email' ) ); ?>">
		<?php esc_html_e( 'Email', 'ova-brw' ); ?>
	</label>
	<?php ovabrw_text_input([
		'type' 			=> 'email',
		'id' 			=> $product->get_meta_key( 'email' ),
		'name' 			=> $product->get_meta_key( 'email' ),
		'placeholder' 	=> esc_html__( 'your_email@gmail.com', 'ova-brw' ),
		'required' 		=> true
	]); ?>
</div>
<?php if ( 'yes' == $show_phone ): ?>
	<div class="rental_item">
		<label for="<?php echo esc_attr( $product->get_meta_key( 'phone' ) ); ?>">
			<?php esc_html_e( 'Phone', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_text_input([
			'type' 			=> 'tel',
			'id' 			=> $product->get_meta_key( 'phone' ),
			'name' 			=> $product->get_meta_key( 'phone' ),
			'placeholder' 	=> esc_html__( 'Your phone', 'ova-brw' ),
			'required' 		=> true
		]); ?>
	</div>
<?php endif;
if ( 'yes' == $show_address ): ?>
	<div class="rental_item">
		<label for="<?php echo esc_attr( $product->get_meta_key( 'address' ) ); ?>">
			<?php esc_html_e( 'Address', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_text_input([
			'type' 			=> 'text',
			'id' 			=> $product->get_meta_key( 'address' ),
			'name' 			=> $product->get_meta_key( 'address' ),
			'placeholder' 	=> esc_html__( 'Your address', 'ova-brw' ),
			'required' 		=> true
		]); ?>
	</div>
<?php endif;

// Location fields
if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel', 'period_time' ] ) ): ?>
	<?php if ( $product->show_location_field( 'pickup', 'request' ) ):
		// Pick-up location id
		$pickup_location_id = ovabrw_unique_id( 'pickup_location' );
	?>
		<div class="rental_item">
			<label for="<?php echo esc_attr( $pickup_location_id ); ?>">
				<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
			</label>
			<?php echo $product->get_html_location( 'pickup', $product->get_meta_key( 'pickup_location' ), 'ovabrw-input-required', $pickup_location, $pickup_location_id ); ?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; // END pick-up location

	// Drop-off location
	if ( $product->show_location_field( 'dropoff', 'request' ) ):
		// Drop-off location id
		$dropoff_location_id = ovabrw_unique_id( 'dropoff_location' );
	?>
		<div class="rental_item">
			<label for="<?php echo esc_attr( $dropoff_location_id ); ?>">
				<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
			</label>
			<?php echo $product->get_html_location( 'dropoff', $product->get_meta_key( 'dropoff_location' ), 'ovabrw-input-required', $dropoff_location, $dropoff_location_id ); ?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; // END drop-off location
endif; // END location fields

// Date fields
if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel' ] ) ):
	// Pick-up date id
	$pickup_date_id = ovabrw_unique_id( 'pickup_date' );
?>
	<div class="rental_item">
		<label for="<?php echo esc_attr( $pickup_date_id ); ?>">
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php if ( $product->has_timepicker() ) {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datetimepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
					'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} else {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} ?>
	    <span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div>
	<?php if ( $product->show_date_field( 'dropoff', 'request' ) ):
		// Drop-off date id
		$dropoff_date_id = ovabrw_unique_id( 'dropoff_date' );
	?>
		<div class="rental_item">
			<label for="<?php echo esc_attr( $dropoff_date_id ); ?>">
				<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
			</label>
			<?php if ( $product->has_timepicker( 'dropoff' ) ) {
				ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> $dropoff_date_id,
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datetimepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '',
						'data-time' => strtotime( $dropoff_date ) ? gmdate( $time_format, strtotime( $dropoff_date ) ) : ''
					]
				]);
			} else {
				ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> $dropoff_date_id,
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : ''
					]
				]);
			} ?>
			<span class="ovabrw-loader-date">
		    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
	<?php endif; // END drop-off date
endif; // END date fields

// Rental type: Hotel - Guest fields
if ( 'hotel' == $rental_type ) {
	ovabrw_get_template('modern/single/detail/ovabrw-product-guests.php');
}

// Rental type: Period Time
if ( 'period_time' === $rental_type ):
	$package_ids 	= $product->get_meta_value( 'petime_id' );
	$package_names 	= $product->get_meta_value( 'petime_label' );

	// Pick-up date id
	$pickup_date_id = ovabrw_unique_id( 'pickup_date' );
?>
	<div class="rental_item">
		<label for="<?php echo esc_attr( $pickup_date_id ); ?>">
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php if ( $product->has_timepicker() ) {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datetimepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
					'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} else {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} ?>
	    <span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div>
	<?php if ( ovabrw_array_exists( $package_ids ) ):
		$default_package 	= '';
		$package_duration 	= ovabrw_get_meta_data( 'package', $_GET );
		
		if ( $package_duration ) {
			$default_package = $product->get_package_id( $package_duration );
		}

		// Package field id
		$package_field_id = ovabrw_unique_id( 'package_id' );
	?>
		<div class="rental_item">
			<label for="<?php echo esc_attr( $package_field_id ); ?>">
				<?php esc_html_e( 'Choose Package', 'ova-brw' ); ?>
			</label>
			<div class="period_package">
				<select
					id="<?php echo esc_attr( $package_field_id ); ?>"
					name="<?php echo esc_attr( $product->get_meta_key( 'package_id' ) ); ?>"
					class="ovabrw-input-required">
					<option value="">
						<?php esc_html_e( 'Select Package', 'ova-brw' ); ?>
					</option>
					<?php foreach ( $package_ids as $k => $package_id ):
						$package_name = ovabrw_get_meta_data( $k, $package_names );
						if ( !$package_id || !$package_name ) continue;
					?>
						<option value="<?php echo esc_attr( trim( $package_id ) ); ?>"<?php ovabrw_selected( $package_id, $default_package ); ?>> 
							<?php echo esc_html( $package_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif;
endif; // END rental type: Period Time

// Rental type: Transportation
if ( 'transportation' == $rental_type ):
	// Pick-up location id
	$pickup_location_id = ovabrw_unique_id( 'pickup_location' );

	// Drop-off location id
	$dropoff_location_id = ovabrw_unique_id( 'dropoff_location' );

	// Pick-up date id
	$pickup_date_id = ovabrw_unique_id( 'pickup_date' );
?>
	<div class="rental_item">
		<label for="<?php echo esc_attr( $pickup_location_id ); ?>">
			<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
		</label>
		<?php echo $product->get_html_location( 'pickup', $product->get_meta_key( 'pickup_location' ), 'ovabrw-input-required', $pickup_location, $pickup_location_id ); ?>
		<div class="ovabrw-other-location"></div>
	</div>
	<div class="rental_item">
		<label for="<?php echo esc_attr( $dropoff_location_id ); ?>">
			<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
		</label>
		<?php echo $product->get_html_location( 'dropoff', $product->get_meta_key( 'dropoff_location' ), 'ovabrw-input-required', $dropoff_location, $dropoff_location_id ); ?>
		<div class="ovabrw-other-location"></div>
	</div>
	<div class="rental_item">
		<label for="<?php echo esc_attr( $pickup_date_id ); ?>">
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php if ( $product->has_timepicker() ) {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datetimepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
					'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} else {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} ?>
	    <span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div>
	<?php if ( $product->show_date_field( 'dropoff', 'request' ) ):
		// Drop-off date id
		$dropoff_date_id = ovabrw_unique_id( 'dropoff_date' );
	?>
		<div class="rental_item">
			<label for="<?php echo esc_attr( $dropoff_date_id ); ?>">
				<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
			</label>
			<?php if ( $product->has_timepicker( 'dropoff' ) ) {
				ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> $dropoff_date_id,
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datetimepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '',
						'data-time' => strtotime( $dropoff_date ) ? gmdate( $time_format, strtotime( $dropoff_date ) ) : ''
					]
				]);
			} else {
				ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> $dropoff_date_id,
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : ''
					]
				]);
			} ?>
			<span class="ovabrw-loader-date">
		    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
	<?php endif; // END drop-off date
endif; // END rental type: Transportation

// Rental type: Taxi 
if ( 'taxi' === $rental_type ):
	$origin 		= ovabrw_get_meta_data( 'origin', $_GET );
	$destination 	= ovabrw_get_meta_data( 'destination', $_GET );
	$duration 		= ovabrw_get_meta_data( 'duration', $_GET );
	$distance 		= ovabrw_get_meta_data( 'distance', $_GET );

	// Get data by product ID
	$price_by 		= $product->get_meta_value( 'map_price_by' );
	$waypoint 		= $product->get_meta_value( 'waypoint' );
	$zoom_map 		= $product->get_meta_value( 'zoom_map' );
	$extra_hour 	= $product->get_meta_value( 'extra_time_hour' );
	$extra_label 	= $product->get_meta_value( 'extra_time_label' );
	$latitude 		= $product->get_meta_value( 'latitude' );
	$longitude 		= $product->get_meta_value( 'longitude' );

	// Price by
	if ( !$price_by ) $price_by = 'km';

	// Latitude
	if ( !$latitude ) $latitude = ovabrw_get_setting( 'latitude_map_default', 39.177972 );

	// Longitude
	if ( !$longitude ) $longitude = ovabrw_get_setting( 'longitude_map_default', -100.36375 );

	// Get max waypoint
	$max_waypoint = $product->get_meta_value( 'max_waypoint' );

	// OpenStreetMap enabled
	$osm_enabled = OVABRW()->options->osm_enabled();
	if ( $osm_enabled ) {
		$map_layer 			= $product->get_meta_value( 'map_layer' );
		$map_feature_type 	= $product->get_meta_value( 'map_feature_type' );
		$bounded 			= $product->get_meta_value( 'bounded' );
		
		// Get viewbox
		$viewbox = '';
		if ( $bounded ) {
			$min_lng = $product->get_meta_value( 'min_lng' );
			$min_lat = $product->get_meta_value( 'min_lat' );
			$max_lng = $product->get_meta_value( 'max_lng' );
			$max_lat = $product->get_meta_value( 'max_lat' );

			if ( $min_lng && $min_lat && $max_lng && $max_lat ) {
				$viewbox = $min_lng.','.$min_lat.','.$max_lng.','.$max_lat;
			}
		}
		
		// Get countrycodes
		$countrycodes = $product->get_meta_value( 'restrictions' );
		if ( ovabrw_array_exists( $countrycodes ) ) {
			$countrycodes = implode( ',', $countrycodes );
		}
	} else {
		$map_types 		= $product->get_meta_value( 'map_types' );
		$bounds 		= $product->get_meta_value( 'bounds' );
		$bounds_lat 	= $product->get_meta_value( 'bounds_lat' );
		$bounds_lng 	= $product->get_meta_value( 'bounds_lng' );
		$bounds_radius 	= $product->get_meta_value( 'bounds_radius' );
		$restrictions 	= $product->get_meta_value( 'restrictions' );

		if ( !$map_types || 'all' == ovabrw_get_meta_data( 0, $map_types ) ) $map_types = [];
		if ( !$restrictions ) $restrictions = [];
	}

	// Pick-up date id
	$pickup_date_id = ovabrw_unique_id( 'pickup_date' );
?>
	<div class="rental_item full-width">
		<label for="<?php echo esc_attr( $pickup_date_id ); ?>">
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php ovabrw_text_input([
			'type' 		=> 'text',
	        'id' 		=> $pickup_date_id,
	        'class' 	=> 'pickup-date',
	        'name' 		=> $product->get_meta_key( 'pickup_date' ),
	        'value' 	=> $pickup_date,
	        'required' 	=> true,
	        'data_type' => 'datetimepicker',
	        'attrs' 	=> [
				'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
				'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
			]
		]); ?>
		<span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div>
	<div class="rental_item form-location-field">
		<label for="<?php echo esc_attr( $product->get_meta_key( 'req_pickup_location' ) ); ?>">
			<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_text_input([
			'type'          => 'text',
	        'id'            => $product->get_meta_key( 'req_pickup_location' ),
	        'name'          => $product->get_meta_key( 'pickup_location' ),
	        'value' 		=> $pickup_location,
	        'placeholder' 	=> esc_html__( 'Enter your location', 'ova-brw' ),
	        'required'      => true
		]); ?>
		<?php ovabrw_text_input([
			'type' 		=> 'hidden',
	        'id' 		=> $product->get_meta_key( 'req_origin' ),
	        'name' 		=> $product->get_meta_key( 'origin' ),
	        'value' 	=> esc_attr( stripslashes( stripslashes( $origin ) ) ),
	        'required' 	=> true
		]); ?>
		<?php if ( 'on' === $waypoint ): ?>
			<i aria-hidden="true" class="flaticon-add btn-req-add-waypoint"></i>
		<?php endif; ?>
	</div>
	<div class="rental_item form-location-field">
		<label for="<?php echo esc_attr( $product->get_meta_key( 'req_dropoff_location' ) ); ?>">
			<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_text_input([
			'type'          => 'text',
	        'id'            => $product->get_meta_key( 'req_dropoff_location' ),
	        'name'          => $product->get_meta_key( 'dropoff_location' ),
	        'value' 		=> $dropoff_location,
	        'placeholder' 	=> esc_html__( 'Enter your location', 'ova-brw' ),
	        'required'      => true
		]); ?>
		<?php ovabrw_text_input([
			'type' 		=> 'hidden',
	        'id' 		=> $product->get_meta_key( 'req_destination' ),
	        'name' 		=> $product->get_meta_key( 'destination' ),
	        'value' 	=> esc_attr( stripslashes( stripslashes( $destination ) ) ),
	        'required' 	=> true
		]); ?>
	</div>
	<?php if ( ovabrw_array_exists( $extra_hour ) ): ?>
		<div class="rental_item">
			<label for="<?php echo esc_attr( $product->get_meta_key( 'extra_time' ) ); ?>">
				<?php esc_html_e( 'Extra Time', 'ova-brw' ); ?>
			</label>
			<select id="<?php echo esc_attr( $product->get_meta_key( 'extra_time' ) ); ?>" name="<?php echo esc_attr( $product->get_meta_key( 'extra_time' ) ); ?>">
				<option value="">
					<?php esc_html_e( 'Select Time', 'ova-brw' ); ?>
				</option>
				<?php foreach ( $extra_hour as $k => $time ):
					$label = ovabrw_get_meta_data( $k, $extra_label );
				?>
					<option value="<?php echo esc_attr( $time ); ?>">
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif;

	// OpenStreetMap
	if ( $osm_enabled ) {
		ovabrw_text_input([
			'type' 	=> 'hidden',
			'name' 	=> $product->get_meta_key( 'data_location' ),
			'attrs' => [
				'data-price-by' 		=> $price_by,
				'data-waypoint-text' 	=> esc_html__( 'Waypoint', 'ova-brw' ),
				'data-max-waypoint' 	=> $max_waypoint,
				'data-map-layer' 		=> $map_layer,
				'data-map-feature-type'	=> $map_feature_type,
				'data-lat' 				=> $latitude,
				'data-lng' 				=> $longitude,
				'data-zoom' 			=> $zoom_map,
				'data-bounded' 			=> $bounded,
				'data-viewbox' 			=> $viewbox,
				'data-countrycodes' 	=> $countrycodes
			]
		]);
	} else {
		ovabrw_text_input([
			'type' 	=> 'hidden',
			'name' 	=> $product->get_meta_key( 'data_location' ),
			'attrs' => [
				'data-price-by' 		=> $price_by,
				'data-waypoint-text' 	=> esc_html__( 'Waypoint', 'ova-brw' ),
				'data-max-waypoint' 	=> $max_waypoint,
				'data-map-types' 		=> json_encode( $map_types ),
				'data-lat' 				=> $latitude,
				'data-lng' 				=> $longitude,
				'data-zoom' 			=> $zoom_map,
				'data-bounds' 			=> $bounds,
				'data-bounds-lat' 		=> $bounds_lat,
				'data-bounds-lng' 		=> $bounds_lng,
				'data-bounds-radius' 	=> $bounds_radius,
				'data-restrictions' 	=> json_encode( $restrictions )
			]
		]);
	}

	// Duration map
	ovabrw_text_input([
		'type' 	=> 'hidden',
		'name' 	=> $product->get_meta_key( 'duration_map' ),
		'value' => $duration
	]);

	// Duration
	ovabrw_text_input([
		'type' 	=> 'hidden',
		'name' 	=> $product->get_meta_key( 'duration' ),
		'value' => $duration
	]);

	// Distance
	ovabrw_text_input([
		'type' 	=> 'hidden',
		'name' 	=> $product->get_meta_key( 'distance' ),
		'value' => $distance
	]); ?>
	<div class="ovabrw-req-directions">
		<div id="ovabrw_req_map" class="ovabrw_req_map"></div>
		<div class="directions-info">
			<div class="distance-sum">
				<h3 class="label"><?php esc_html_e( 'Total Distance', 'ova-brw' ); ?></h3>
				<span class="distance-value">0</span>
				<?php if ( $price_by === 'km' ): ?>
					<span class="distance-unit">
						<?php esc_html_e( 'km', 'ova-brw' ); ?>
					</span>
				<?php else: ?>
					<span class="distance-unit">
						<?php esc_html_e( 'mi', 'ova-brw' ); ?>
					</span>
				<?php endif; ?>
			</div>
			<div class="duration-sum">
				<h3 class="label"><?php esc_html_e( 'Total Time', 'ova-brw' ); ?></h3>
				<span class="hour">0</span>
				<span class="unit"><?php esc_html_e( 'h', 'ova-brw' ); ?></span>
				<span class="minute">0</span>
				<span class="unit"><?php esc_html_e( 'm', 'ova-brw' ); ?></span>
			</div>
		</div>
	</div>
<?php endif; // END rental type: Taxi

// Rental type: Appointment
if ( 'appointment' === $rental_type ):
	// Use location
	$use_location = $product->get_meta_value( 'use_location' );

	// Pick-up date id
	$pickup_date_id = ovabrw_unique_id( 'pickup_date' );
?>
	<!-- Pick-up date -->
	<div class="rental_item">
		<label for="<?php echo esc_attr( $pickup_date_id ); ?>">
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php ovabrw_text_input([
			'type' 		=> 'text',
	        'id' 		=> $pickup_date_id,
	        'class' 	=> 'pickup-date',
	        'name' 		=> $product->get_meta_key( 'pickup_date' ),
	        'value' 	=> $pickup_date,
	        'required' 	=> true,
	        'data_type' => 'datepicker',
	        'attrs' 	=> [
				'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
			]
		]); ?>
	    <span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div>
	<?php if ( $use_location ): ?>
		<div class="rental_item ovabrw-time-slots-location-field">
			<div class="ovabrw-label">
				<?php esc_html_e( 'Select Location', 'ova-brw' ); ?>
			</div>
			<div class="ovabrw-time-slots-location"></div>
		</div>
	<?php endif; ?>
	<div class="rental_item full-width ovabrw-time-slots-field">
		<div class="ovabrw-label">
			<?php esc_html_e( 'Select Time', 'ova-brw' ); ?>
		</div>
		<div class="ovabrw-time-slots ovabrw-input-required"></div>
	</div>
	<?php if ( $product->show_location_field( 'dropoff', 'request' ) ):
		$dropoff_date_id = ovabrw_unique_id( 'dropoff_date' );
	?>
		<div class="rental_item">
			<label for="<?php echo esc_attr( $dropoff_date_id ); ?>">
				<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
			</label>
			<?php ovabrw_text_input([
				'type' 			=> 'text',
				'id' 			=> $dropoff_date_id,
		        'class' 		=> 'appointment-dropoff-date',
		        'name' 			=> $product->get_meta_key( 'dropoff_date' ),
		        'required' 		=> true,
		        'placeholder' 	=> OVABRW()->options->get_datetime_placeholder(),
		        'readonly' 		=> true
			]); ?>
			<span class="ovabrw-loader-date">
		    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
	<?php else: ?>
		<?php ovabrw_text_input([
			'type' => 'hidden',
	        'name' => $product->get_meta_key( 'dropoff_date' )
		]); ?>
	<?php endif; // END drop-off date
endif; // END rental type: Appointment

// Rental type: Tour
if ( 'tour' === $rental_type ):
	// Pick-up date id
	$pickup_date_id = ovabrw_unique_id( 'pickup_date' );

	// Get duration type
	$duration_type = $product->get_meta_value( 'duration_type' );
	if ( 'fixed' === $duration_type ): ?>
		<div class="rental_item">
			<label for="<?php echo esc_attr( $pickup_date_id ); ?>">
				<?php echo esc_html( $product->get_date_label() ); ?>
			</label>
			<?php ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
				]
			]); ?>
			<span class="ovabrw-loader-date">
		    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
		<?php if ( $product->show_date_field( 'dropoff' ) ):
			// Drop-off date id
			$dropoff_date_id = ovabrw_unique_id( 'dropoff_date' );
		?>
			<div class="rental_item">
				<label for="<?php echo esc_attr( $dropoff_date_id ); ?>">
					<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
				</label>
				<?php ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> $dropoff_date_id,
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : ''
					]
				]); ?>
				<span class="ovabrw-loader-date">
			    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
			    </span>
			</div>
		<?php else:
			ovabrw_text_input([
				'type' 		=> 'hidden',
		        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
		        'class' 	=> 'dropoff-date',
		        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
		        'value' 	=> $dropoff_date
			]);
		endif; // END drop-off date
	elseif ( 'timeslots' === $duration_type ): ?>
		<div class="rental_item full-width">
			<label for="<?php echo esc_attr( $pickup_date_id ); ?>">
				<?php echo esc_html( $product->get_date_label() ); ?>
			</label>
			<?php ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
				]
			]); ?>
			<span class="ovabrw-loader-date">
		    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
		<div class="rental_item full-width ovabrw-tour-timeslots-field">
			<h3 class="ovabrw-label ovabrw-required">
				<?php esc_html_e( 'Select Time', 'ova-brw' ); ?>
			</h3>
			<div class="ovabrw-tour-timeslots ovabrw-input-required"></div>
		</div>
		<?php if ( $product->show_date_field( 'dropoff' ) ):
			// Drop-off date id
			$dropoff_date_id = ovabrw_unique_id( 'dropoff_date' );
		?>
			<div class="rental_item full-width">
				<label for="<?php echo esc_attr( $dropoff_date_id ); ?>">
					<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
				</label>
				<?php ovabrw_text_input([
					'type' 			=> 'text',
			        'id' 			=> $dropoff_date_id,
			        'class' 		=> 'dropoff-date',
			        'name' 			=> $product->get_meta_key( 'dropoff_date' ),
			        'placeholder'   => OVABRW()->options->get_date_placeholder(),
			        'required' 		=> true,
			        'readonly'      => true
				]); ?>
				<span class="ovabrw-loader-date">
			    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
			    </span>
			</div>
		<?php else:
			ovabrw_text_input([
				'type' 		=> 'hidden',
		        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
		        'class' 	=> 'dropoff-date',
		        'name' 		=> $product->get_meta_key( 'dropoff_date' )
			]);
		endif; // END drop-off date
	elseif ( 'period' === $duration_type ):
		// Get period id
		$period_id = ovabrw_unique_id( 'booking_period' );
	?>
		<div class="rental_item full-width ovabrw-period-field">
			<label for="<?php echo esc_attr( $period_id ); ?>">
				<?php esc_html_e( 'Select period', 'ova-brw' ); ?>
			</label>
			<select
				id="<?php echo esc_attr( $period_id ); ?>"
				clas="ovabrw-input-required"
				name="<?php echo esc_attr( $product->get_meta_key( 'period' ) ); ?>"
				data-no-time="<?php esc_attr_e( 'There are no time periods available', 'ova-brw' ); ?>">
				<option value="">
					<?php esc_html_e( 'Select ...', 'ova-brw' ); ?>
				</option>
			</select>
		    <span class="ovabrw-loader-period">
		        <i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
		<?php if ( apply_filters( OVABRW_PREFIX.'tour_period_show_pickup_date', true ) ): ?>
			<div class="rental_item">
				<label for="<?php echo esc_attr( $pickup_date_id ); ?>">
					<?php echo esc_html( $product->get_date_label() ); ?>
				</label>
				<?php ovabrw_text_input([
					'type' 			=> 'text',
			        'id' 			=> $pickup_date_id,
			        'class' 		=> 'pickup-date',
			        'name' 			=> $product->get_meta_key( 'pickup_date' ),
			        'placeholder'   => OVABRW()->options->get_date_placeholder(),
			        'required' 		=> true,
			        'readonly'      => true
				]); ?>
				<span class="ovabrw-loader-date">
			    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
			    </span>
			</div>
		<?php else:
			ovabrw_text_input([
				'type' 		=> 'hidden',
		        'id' 		=> $pickup_date_id,
		        'class' 	=> 'dropoff-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
			]);
		endif; // END pick-up date

		// Drop-off date
		if ( apply_filters( OVABRW_PREFIX.'tour_period_show_dropoff_date', true ) ):
			// Drop-off date id
			$dropoff_date_id = ovabrw_unique_id( 'dropoff_date' );
		?>
			<div class="rental_item">
				<label for="<?php echo esc_attr( $dropoff_date_id ); ?>">
					<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
				</label>
				<?php ovabrw_text_input([
					'type' 			=> 'text',
			        'id' 			=> $dropoff_date_id,
			        'class' 		=> 'dropoff-date',
			        'name' 			=> $product->get_meta_key( 'dropoff_date' ),
			        'placeholder'   => OVABRW()->options->get_date_placeholder(),
			        'required' 		=> true,
			        'readonly'      => true
				]); ?>
				<span class="ovabrw-loader-date">
			    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
			    </span>
			</div>
		<?php else:
			ovabrw_text_input([
				'type' 		=> 'hidden',
		        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
		        'class' 	=> 'dropoff-date',
		        'name' 		=> $product->get_meta_key( 'dropoff_date' )
			]);
		endif; // END drop-off date
	endif; // END duration type
endif; // END rental type: Tour

// Guests fields
ovabrw_get_template('modern/single/detail/guests/guests.php');

// Quatity
if ( $product->show_quantity( 'request' ) ):
	// Get quantity
	$quantity = $product->get_number_quantity();

	// Get current quantity
	$default_quantity = ovabrw_get_meta_data( 'quantity', $_GET, 1 );

	// Quantity field id
	$quantity_field_id = ovabrw_unique_id( 'quantity' );
?>
	<div class="rental_item">
		<label for="<?php echo esc_attr( $quantity_field_id ); ?>">
			<?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_text_input([
			'type'          => 'number',
			'id' 			=> $quantity_field_id,
	        'name'          => $product->get_meta_key( 'quantity' ),
	        'value' 		=> $default_quantity,
	        'required'      => true,
	        'data_type' 	=> 'number',
	        'attrs' 		=> [
	        	'min' 		=> 1,
	        	'max' 		=> $quantity ? $quantity : '',
	        	'current' 	=> $default_quantity
	        ]
		]); ?>
	</div>
<?php endif; // END quantity ?>