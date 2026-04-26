<?php if ( !defined( 'ABSPATH' ) ) exit();

$map_price_by 	= $this->get_meta_value( 'map_price_by', 'km' );
$waypoint 		= $this->get_meta_value( 'waypoint', 'on' );
$max_waypoint 	= $this->get_meta_value( 'max_waypoint' );
$zoom_map 		= $this->get_meta_value( 'zoom_map', 4 );
$countries 		= ovabrw_iso_alpha2();
$restrictions 	= $this->get_meta_value( 'restrictions' );

?>
<!-- Setup Map -->
<div id="ovabrw-options-setup-map" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Setup Map', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_map_taxi_content', $this ); ?>
		<div class="ovabrw-form-field ovabrw-taxi-map">
			<!-- Types -->
			<p class="form-field ovabrw_map_price_by_field">
				<label for="ovabrw_map_price_by">
					<?php esc_html_e( 'Price by per', 'ova-brw' ); ?>
				</label>
				<span class="map-price-by">
					<span class="map-price-by-input">
						<input
							type="radio"
							id="map-price-km"
							name="<?php echo esc_attr( $this->get_meta_name( 'map_price_by' ) ); ?>"
							value="km"
							<?php ovabrw_checked( $map_price_by, 'km' ); ?>
						/>
						<label class="label" for="map-price-km">
							<?php esc_html_e( 'km', 'ova-brw' ); ?>
						</label>
					</span>
					<span class="map-price-by-input">
						<input
							type="radio"
							id="map-price-mi"
							name="<?php echo esc_attr( $this->get_meta_name( 'map_price_by' ) ); ?>"
							value="mi"
							<?php ovabrw_checked( $map_price_by, 'mi' ); ?>
						/>
						<label class="label" for="map-price-mi">
							<?php esc_html_e( 'mi', 'ova-brw' ); ?>
						</label>
					</span>
				</span>
			</p>
			<p class="form-field ovabrw_waypoint_field ">
				<label for="ovabrw_waypoint">
					<?php esc_html_e( 'Waypoints', 'ova-brw' ); ?>
				</label>
				<input 
					type="checkbox"
					id="ovabrw_waypoint"
					name="<?php echo esc_attr( $this->get_meta_name( 'waypoint' ) ); ?>"
					<?php ovabrw_checked( $waypoint, 'on' ); ?>
				>
				<span class="max_waypoint">
					<span class="label"><?php esc_html_e( 'Maximum Waypoint' ); ?></span>
					<input
						type="number"
						name="<?php echo esc_attr( $this->get_meta_name( 'max_waypoint' ) ); ?>"
						value="<?php echo esc_attr( $max_waypoint ); ?>"
						placeholder="0"
						autocomplete="off"
					/>
				</span>
			</p>
			<?php woocommerce_wp_text_input([
		 		'id' 			=> $this->get_meta_name( 'zoom_map' ),
				'class' 		=> 'short ',
				'label' 		=> esc_html__( 'Zoom', 'ova-brw' ),
				'placeholder' 	=> '4',
				'type' 			=> 'number',
				'value' 		=> $zoom_map
		 	]); ?>

		 	<?php if ( OVABRW()->options->osm_enabled() ):
		 		// Get map layer
		 		$map_layer = $this->get_meta_value( 'map_layer', 'none' );

		 		// Get map feature type
		 		$map_feature_type = $this->get_meta_value( 'map_feature_type', 'none' );

		 		// Get bounded
				$bounded = $this->get_meta_value( 'bounded' );
				$min_lng = $this->get_meta_value( 'min_lng' );
				$min_lat = $this->get_meta_value( 'min_lat' );
				$max_lng = $this->get_meta_value( 'max_lng' );
				$max_lat = $this->get_meta_value( 'max_lat' );
		 	?>
		 		<p class="form-field ovabrw_map_layer_field">
					<label for="ovabrw_map_layer">
						<?php esc_html_e( 'Layer', 'ova-brw' ); ?>
					</label>
					<span class="map-layer">
						<span class="map-layer-input">
							<input
								type="radio"
								id="map-layer"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_layer' ) ); ?>"
								value="none"
								<?php ovabrw_checked( 'none', $map_layer ); ?>
							/>
							<label class="label" for="map-layer">
								<?php esc_html_e( 'None', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-layer-input">
							<input
								type="radio"
								id="map-layer-address"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_layer' ) ); ?>"
								value="address"
								<?php ovabrw_checked( 'address', $map_layer ); ?>
							/>
							<label class="label" for="map-layer-address">
								<?php esc_html_e( 'Address', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-layer-input">
							<input
								type="radio"
								id="map-layer-poi"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_layer' ) ); ?>"
								value="poi"
								<?php ovabrw_checked( 'poi', $map_layer ); ?>
							/>
							<label class="label" for="map-layer-poi">
								<?php esc_html_e( 'Poi', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-layer-input">
							<input
								type="radio"
								id="map-layer-railway"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_layer' ) ); ?>"
								value="railway"
								<?php ovabrw_checked( 'railway', $map_layer ); ?>
							/>
							<label class="label" for="map-layer-railway">
								<?php esc_html_e( 'Railway', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-layer-input">
							<input
								type="radio"
								id="map-layer-natural"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_layer' ) ); ?>"
								value="natural"
								<?php ovabrw_checked( 'natural', $map_layer ); ?>
							/>
							<label class="label" for="map-layer-natural">
								<?php esc_html_e( 'Natural', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-layer-input">
							<input
								type="radio"
								id="map-layer-manmade"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_layer' ) ); ?>"
								value="manmade"
								<?php ovabrw_checked( 'manmade', $map_layer ); ?>
							/>
							<label class="label" for="map-layer-manmade">
								<?php esc_html_e( 'Manmade', 'ova-brw' ); ?>
							</label>
						</span>
					</span>
				</p>
				<p class="form-field ovabrw_map_feature_type_field">
					<label for="ovabrw_map_feature_type">
						<?php esc_html_e( 'Feature type', 'ova-brw' ); ?>
					</label>
					<span class="map-feature-type">
						<span class="map-feature-type-input">
							<input
								type="radio"
								id="map-feature-type"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_feature_type' ) ); ?>"
								value="none"
								<?php ovabrw_checked( 'none', $map_feature_type ); ?>
							/>
							<label class="label" for="map-feature-type">
								<?php esc_html_e( 'None', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-feature-type-input">
							<input
								type="radio"
								id="map-feature-type-country"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_feature_type' ) ); ?>"
								value="country"
								<?php ovabrw_checked( 'country', $map_feature_type ); ?>
							/>
							<label class="label" for="map-feature-type-country">
								<?php esc_html_e( 'Country', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-feature-type-input">
							<input
								type="radio"
								id="map-feature-type-state"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_feature_type' ) ); ?>"
								value="state"
								<?php ovabrw_checked( 'state', $map_feature_type ); ?>
							/>
							<label class="label" for="map-feature-type-state">
								<?php esc_html_e( 'State', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-feature-type-input">
							<input
								type="radio"
								id="map-feature-type-city"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_feature_type' ) ); ?>"
								value="city"
								<?php ovabrw_checked( 'city', $map_feature_type ); ?>
							/>
							<label class="label" for="map-feature-type-city">
								<?php esc_html_e( 'City', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-feature-type-input">
							<input
								type="radio"
								id="map-feature-type-settlement"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_feature_type' ) ); ?>"
								value="settlement"
								<?php ovabrw_checked( 'settlement', $map_feature_type ); ?>
							/>
							<label class="label" for="map-feature-type-settlement">
								<?php esc_html_e( 'Settlement', 'ova-brw' ); ?>
							</label>
						</span>
					</span>
				</p>
				<p class="form-field ovabrw_bounded_field">
					<label for="ovabrw_bounded">
						<?php esc_html_e( 'Bounded', 'ova-brw' ); ?>
					</label>
					<input 
						type="checkbox"
						id="ovabrw_bounded"
						name="<?php echo esc_attr( $this->get_meta_name( 'bounded' ) ); ?>"
						<?php ovabrw_checked( $bounded, 'on' ); ?>
					>
					<span class="coordinates">
						<span class="bounded-min-lng">
							<span class="label">
								<?php esc_html_e( 'Western longitude', 'ova-brw' ); ?>
							</span>
							<input
								type="text"
								name="<?php echo esc_attr( $this->get_meta_name( 'min_lng' ) ); ?>"
								value="<?php echo esc_attr( $min_lng ); ?>"
								autocomplete="off"
							/>
						</span>
						<span class="bounded-min-lat">
							<span class="label">
								<?php esc_html_e( 'Southern latitude', 'ova-brw' ); ?>
							</span>
							<input
								type="text"
								name="<?php echo esc_attr( $this->get_meta_name( 'min_lat' ) ); ?>"
								value="<?php echo esc_attr( $min_lat ); ?>"
								autocomplete="off"
							/>
						</span>
						<span class="bounded-max-lng">
							<span class="label">
								<?php esc_html_e( 'Eastern longitude', 'ova-brw' ); ?>
							</span>
							<input
								type="text"
								name="<?php echo esc_attr( $this->get_meta_name( 'max_lng' ) ); ?>"
								value="<?php echo esc_attr( $max_lng ); ?>"
								autocomplete="off"
							/>
						</span>
						<span class="bounded-max-lat">
							<span class="label">
								<?php esc_html_e( 'Northern latitude', 'ova-brw' ); ?>
							</span>
							<input
								type="text"
								name="<?php echo esc_attr( $this->get_meta_name( 'max_lat' ) ); ?>"
								value="<?php echo esc_attr( $max_lat ); ?>"
								autocomplete="off"
							/>
						</span>
					</span>
					<?php echo wc_help_tip( esc_html__( 'Please visit https://boundingbox.klokantech.com/ to define the coordinates. Once done, select "CSV" from the "Copy & Paste" format options.', 'ova-brw' ), true ); ?>
				</p>
		 	<?php else:
		 		$map_types = $this->get_meta_value( 'map_types' );
		 		if ( !$map_types ) $map_types = [ 'all' ];

		 		// Get bounds
				$bounds 		= $this->get_meta_value( 'bounds' );
				$bounds_lat 	= $this->get_meta_value( 'bounds_lat' );
				$bounds_lng 	= $this->get_meta_value( 'bounds_lng' );
				$bounds_radius 	= $this->get_meta_value( 'bounds_radius' );
		 	?>
				<p class="form-field ovabrw_map_types_field">
					<label for="ovabrw_map_types">
						<?php esc_html_e( 'Types', 'ova-brw' ); ?>
					</label>
					<span class="map-types">
						<span class="map-type-input">
							<input
								type="radio"
								id="map-all"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
								value="all"
								<?php ovabrw_checked( 'all', $map_types ); ?>
							/>
							<label class="label" for="map-all">
								<?php esc_html_e( 'All', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-type-input">
							<input
								type="radio"
								id="map-geocode"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
								value="geocode"
								<?php ovabrw_checked( 'geocode', $map_types ); ?>
							/>
							<label class="label" for="map-geocode">
								<?php esc_html_e( 'Geocode', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-type-input">
							<input
								type="radio"
								id="map-address"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
								value="address"
								<?php ovabrw_checked( 'address', $map_types ); ?>
							/>
							<label class="label" for="map-address">
								<?php esc_html_e( 'Address', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-type-input">
							<input
								type="radio"
								id="map-establishment"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
								value="establishment"
								<?php ovabrw_checked( 'establishment', $map_types ); ?>
							/>
							<label class="label" for="map-establishment">
								<?php esc_html_e( 'Establishment', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-type-input">
							<input
								type="radio"
								id="map-cities"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
								value="(cities)"
								<?php ovabrw_checked( '(cities)', $map_types ); ?>
							/>
							<label class="label" for="map-cities">
								<?php esc_html_e( 'Cities', 'ova-brw' ); ?>
							</label>
						</span>
						<span class="map-type-input">
							<input
								type="radio"
								id="map-regions"
								name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
								value="(regions)"
								<?php ovabrw_checked( '(regions)', $map_types ); ?>
							/>
							<label class="label" for="map-regions">
								<?php esc_html_e( 'Regions', 'ova-brw' ); ?>
							</label>
						</span>
					</span>
				</p>
				<p class="form-field ovabrw_bounds_field">
					<label for="ovabrw_bounds">
						<?php esc_html_e( 'Bounds', 'ova-brw' ); ?>
					</label>
					<input 
						type="checkbox"
						id="ovabrw_bounds"
						name="<?php echo esc_attr( $this->get_meta_name( 'bounds' ) ); ?>"
						<?php ovabrw_checked( $bounds, 'on' ); ?>
					>
					<span class="coordinates">
						<span class="bounds-lat">
							<span class="label">
								<?php esc_html_e( 'Latitude', 'ova-brw' ); ?>
							</span>
							<input
								type="text"
								name="<?php echo esc_attr( $this->get_meta_name( 'bounds_lat' ) ); ?>"
								value="<?php echo esc_attr( $bounds_lat ); ?>"
								autocomplete="off"
							/>
						</span>
						<span class="bounds-lng">
							<span class="label">
								<?php esc_html_e( 'Longitude', 'ova-brw' ); ?>
							</span>
							<input
								type="text"
								name="<?php echo esc_attr( $this->get_meta_name( 'bounds_lng' ) ); ?>"
								value="<?php echo esc_attr( $bounds_lng ); ?>"
								autocomplete="off"
							/>
						</span>
						<span class="bounds-radius">
							<span class="label">
								<?php esc_html_e( 'Radius(meters)', 'ova-brw' ); ?>
							</span>
							<input
								type="text"
								name="<?php echo esc_attr( $this->get_meta_name( 'bounds_radius' ) ); ?>"
								value="<?php echo esc_attr( $bounds_radius ); ?>"
								autocomplete="off"
							/>
						</span>
					</span>
				</p>
			<?php endif; ?>

			<!-- Component Restrictions -->
			<p class="form-field ovabrw_restrictions_field">
				<label for="ovabrw_restrictions">
					<?php esc_html_e( 'Restrictions', 'ova-brw' ); ?>
				</label>
				<select name="<?php echo esc_attr( $this->get_meta_name( 'restrictions[]' ) ); ?>" id="ovabrw_restrictions" data-placeholder="<?php esc_html_e( 'Select country', 'ova-brw' ); ?>" multiple>
					<?php foreach ( $countries as $country_code => $country_name ): ?>
						<option value="<?php echo esc_attr( $country_code ); ?>"<?php ovabrw_selected( $country_code, $restrictions ); ?>>
							<?php echo esc_html( $country_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
		<?php do_action( $this->prefix.'after_map_taxi_content', $this ); ?>
	</div>
</div>