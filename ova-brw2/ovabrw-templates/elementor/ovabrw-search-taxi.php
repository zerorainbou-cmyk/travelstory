<?php if ( !defined( 'ABSPATH' ) ) exit();

// Search results
$search_results = ovabrw_get_meta_data( 'search_result', $args );

// Action
$action = home_url();
if ( 'default' !== $search_results ) {
	$action = isset( $args['result_url']['url'] ) ? $args['result_url']['url'] : '';
}

// Column
$column = ovabrw_get_meta_data( 'layout1_columns', $args, 2 );

// Fields
$search_fields = ovabrw_get_meta_data( 'fields', $args );

// Has location field
$has_location = false;

// Custom Taxonomies
$custom_taxonomies = ovabrw_get_meta_data( 'custom_taxonomies', $args );

// Order by
$orderby = ovabrw_get_meta_data( 'orderby', $args, 'date' );

// Order
$order = ovabrw_get_meta_data( 'order', $args, 'DESC' );
	
if ( ovabrw_array_exists( $search_fields ) ): ?>
<div class="ovabrw-search-taxi">
	<form action="<?php echo esc_url( $action ); ?>" method="get" class="search-taxi-form" autocomplete="off">
		<?php ovabrw_text_input([
        	'type' 	=> 'hidden',
        	'name' 	=> 'ovabrw_search_url',
        	'value' => $action
        ]); ?>
		<div class="search-taxi-fields search-grid-<?php echo esc_attr( $column ); ?>">
			<?php foreach ( $search_fields as $field ): ?>
				<div class="search-field <?php echo esc_attr( $field['field_name'] ); ?>">
					<?php if ( $field['field_label'] ): ?>
						<label for="<?php echo esc_attr( $field['field_name'] ); ?>">
							<?php echo esc_html( $field['field_label'] ); ?>
						</label>
						<?php if ( 'pickup-location' == $field['field_name'] ):
							$has_location = true;

							ovabrw_text_input([
								'type' 			=> 'text',
								'id' 			=> $field['field_name'],
								'name' 			=> 'pickup_location',
								'placeholder' 	=> $field['field_placeholder'],
								'required' 		=> true
							]);
							ovabrw_text_input([
								'type' 		=> 'hidden',
								'name' 		=> 'origin',
								'required' 	=> true
							]);
						?>
							<i aria-hidden="true" class="brwicon3-map"></i>
						<?php elseif ( 'dropoff-location' == $field['field_name'] ):
							$has_location = true;

							ovabrw_text_input([
								'type' 			=> 'text',
								'id' 			=> $field['field_name'],
								'name' 			=> 'dropoff_location',
								'placeholder' 	=> $field['field_placeholder'],
								'required' 		=> true
							]);
							ovabrw_text_input([
								'type' 		=> 'hidden',
								'name' 		=> 'destination',
								'required' 	=> true
							]);
						?>
							<i aria-hidden="true" class="brwicon3-map"></i>
						<?php elseif ( 'pickup-date' == $field['field_name'] ):
							ovabrw_text_input([
								'type' 			=> 'text',
								'id' 			=> ovabrw_unique_id( $field['field_name'] ),
								'name' 			=> 'pickup_date',
								'placeholder' 	=> $field['field_placeholder'],
								'required' 		=> true,
								'data_type' 	=> 'datetimepicker-start'
							]);
						?>
							<i aria-hidden="true" class="brwicon3-calendar"></i>
						<?php elseif ( 'category' == $field['field_name'] ):
							$default_category 	= ovabrw_get_meta_data( 'default_category', $args );
							$incl_category 		= ovabrw_get_meta_data( 'incl_category', $args );
							$excl_category 		= ovabrw_get_meta_data( 'excl_category', $args );
							
							echo OVABRW()->options->get_html_dropdown_categories( $default_category, '', $excl_category, $field['field_placeholder'], $incl_category );
						?>
							<i aria-hidden="true" class="brwicon3-car-1"></i>
						<?php elseif ( 'number-seats' == $field['field_name'] ):
							ovabrw_text_input([
								'type' 			=> 'number',
								'id' 			=> $field['field_name'],
								'name' 			=> 'seats',
								'value' 		=> apply_filters( OVABRW_PREFIX.'default_number_of_seats', 4 ),
								'placeholder' 	=> $field['field_placeholder'],
								'attrs' 		=> [
									'min' => 1
								]
							]);
						?>
							<i aria-hidden="true" class="brwicon3-user"></i>
						<?php elseif ( 'quantity' == $field['field_name'] ):
							ovabrw_text_input([
								'type' 			=> 'number',
								'id' 			=> $field['field_name'],
								'name' 			=> 'quantity',
								'value' 		=> 1,
								'placeholder' 	=> $field['field_placeholder'],
								'attrs' 		=> [
									'min' => 1
								]
							]);
						?>
							<i aria-hidden="true" class="flaticon-add"></i>
						<?php elseif ( 'price-filter' == $field['field_name'] ):
							// Get prices
			                $prices = OVABRW()->options->get_product_lookup_prices();

			                // Get min price
			                $min_price = (int)ovabrw_get_meta_data( 'min_price', $prices );
			                if ( '' !== $min_price ) $min_price = floor( $min_price );

			                // Max price
			                $max_price = (int)ovabrw_get_meta_data( 'max_price', $prices );
			                if ( '' !== $max_price ) $max_price = ceil( $max_price );

			                // Get current min price
			                $current_min_price = sanitize_text_field( ovabrw_get_meta_data( 'min_price', $_GET ) );
			                if ( '' === $current_min_price ) $current_min_price = $min_price;

			                // Get current max price
			                $current_max_price = sanitize_text_field( ovabrw_get_meta_data( 'max_price', $_GET ) );
			                if ( '' === $current_max_price ) $current_max_price = $max_price;
						?>
							<div class="ovabrw-filter-price-slider"
	                            data-step="1"
	                            data-currency-symbol="<?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>"
	                            data-currency-position="<?php echo esc_attr( get_option( 'woocommerce_currency_pos', 'left' ) ); ?>"
	                            data-thousand-separator="<?php echo esc_attr( wc_get_price_thousand_separator() ); ?>">
	                            <div class="product-filter-price"></div>
	                            <?php ovabrw_text_input([
	                                'type'  => 'hidden',
	                                'name'  => 'min_price',
	                                'value' => (int)$current_min_price,
	                                'attrs' => [
	                                    'data-min-price' => $min_price
	                                ]
	                            ]); ?>
	                            <?php ovabrw_text_input([
	                                'type'  => 'hidden',
	                                'name'  => 'max_price',
	                                'value' => (int)$current_max_price,
	                                'attrs' => [
	                                    'data-max-price' => $max_price
	                                ]
	                            ]); ?>
	                        </div>
						<?php endif;
					endif; ?>
				</div>
			<?php endforeach;

			// Custom Taxonomies
			$taxonomies = ovabrw_get_option( 'custom_taxonomy', [] );
			if ( ovabrw_array_exists( $custom_taxonomies ) ) {
				foreach ( $custom_taxonomies as $obj_taxonomy ) {
					$taxonomy_slug 	= $obj_taxonomy['custom_taxonomy'];
					$selected 		= ovabrw_get_meta_data( $taxonomy_slug.'_name', $_GET );

					if ( ovabrw_get_meta_data( $taxonomy_slug, $taxonomies ) ) {
						$taxonomy_name = $taxonomies[$taxonomy_slug]['name'];
						$html_taxonomy = OVABRW()->options->get_html_dropdown_taxonomies_search( $taxonomy_slug, $taxonomy_name, $selected );

						if ( !empty( $taxonomy_name ) && $html_taxonomy ): ?>
							<div class="search-field wrap_search_taxonomies <?php echo esc_attr( $taxonomy_slug ); ?>">
								<label class="field-label">
									<?php echo esc_html( $taxonomy_name ); ?>
								</label>
								<?php echo $html_taxonomy; ?>
							</div>
						<?php
						endif;
					}
				}
			}

			// Config map
			if ( $has_location ) {
				if ( OVABRW()->options->osm_enabled() ) {
					$map_opts = [];

					// Get layer
					$layer = ovabrw_get_meta_data( 'layer', $args );
					if ( $layer ) {
						$map_opts['data-layer'] = $layer;

						// Get feature type
						$feature_type = ovabrw_get_meta_data( 'feature_type', $args );
						if ( $feature_type ) {
							$map_opts['data-feature-type'] = $feature_type;
						}
					}

					// Get bounded
					$bounded = ovabrw_get_meta_data( 'bounded', $args );
					if ( $bounded ) {
						$map_opts['data-bounded'] = 1;

						// Get viewbox
						$min_lng = ovabrw_get_meta_data( 'min_lng', $args );
						$min_lat = ovabrw_get_meta_data( 'min_lat', $args );
						$max_lng = ovabrw_get_meta_data( 'max_lng', $args );
						$max_lat = ovabrw_get_meta_data( 'max_lat', $args );

						if ( $min_lng && $min_lat && $max_lng && $max_lat ) {
							$map_opts['data-viewbox'] = $min_lng.','.$min_lat.','.$max_lng.','.$max_lat;
						}
					}

					// Get restrictions
					$restrictions = ovabrw_get_meta_data( 'restrictions', $args );
					if ( ovabrw_array_exists( $restrictions ) ) {
						$map_opts['data-countrycodes'] = implode(",", $restrictions);
					}

					ovabrw_text_input([
						'type' 	=> 'hidden',
						'name' 	=> 'search-taxi-options',
						'attrs' => $map_opts
					]); ?>
					<div
						id="ovabrw-map"
						style="display: none;"
						data-lat=<?php echo esc_attr( ovabrw_get_setting( 'latitude_map_default', 36.778261 ) ); ?>
						data-lng=<?php echo esc_attr( ovabrw_get_setting( 'longitude_map_default', -119.4179324 ) ); ?>
						data-zoom=<?php echo esc_attr( ovabrw_get_setting( 'google_map_zoom', 17 ) ); ?>
					></div>
				<?php } else {
					// Get map types
					$map_types = ovabrw_get_meta_data( 'map_type', $args );
					if ( 'all' === $map_types ) {
						$map_types = '';
					} else {
						$map_types = json_encode([ $map_types ]);
					}
					
					$bounds 		= 'yes' == ovabrw_get_meta_data( 'bounds', $args ) ? '1' : '';
					$bounds_lat 	= ovabrw_get_meta_data( 'lat', $args );
					$bounds_lng 	= ovabrw_get_meta_data( 'lng', $args );
					$radius 		= ovabrw_get_meta_data( 'radius', $args );
					$restrictions 	= ovabrw_get_meta_data( 'restrictions', $args );

					ovabrw_text_input([
						'type' 	=> 'hidden',
						'name' 	=> 'search-taxi-options',
						'attrs' => [
							'data-map-types' 		=> $map_types,
							'data-bounds' 			=> $bounds,
							'data-bounds-lat' 		=> $bounds_lat,
							'data-bounds-lng' 		=> $bounds_lng,
							'data-bounds-radius' 	=> $radius,
							'data-restrictions' 	=> json_encode( $restrictions )
						]
					]);
				}
				ovabrw_text_input([
					'type' => 'hidden',
					'name' => 'duration'
				]);
				ovabrw_text_input([
					'type' => 'hidden',
					'name' => 'distance'
				]);
			} // END config map
		?>
		</div>
		<button class="search-taxi-btn" type="submit">
        	<i class="flaticon-search-1"></i>
        	<?php esc_html_e( 'Search Taxi', 'ova-brw' ); ?>
        </button>
        <?php if ( 'default' == $search_results ) {
        	ovabrw_text_input([
        		'type' 	=> 'hidden',
        		'name' 	=> 'orderby',
        		'value' => $orderby
        	]);
        	ovabrw_text_input([
        		'type' 	=> 'hidden',
        		'name' 	=> 'order',
        		'value' => $order
        	]);
        	ovabrw_text_input([
        		'type' 	=> 'hidden',
        		'name' 	=> 'ovabrw_search',
        		'value' => 'search_item'
        	]);
        } ?>
	</form>
</div>
<?php endif;