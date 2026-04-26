<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Meta Boxes class.
 */
if ( !class_exists( 'OVABRW_Admin_Meta_Boxes' ) ) {

	class OVABRW_Admin_Meta_Boxes {

		/**
		 * Instance init
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Product rental selector
			add_filter( 'product_type_selector', [ $this, 'product_rental_selector' ] );

			// Default product rental query
			add_filter( 'woocommerce_product_type_query', [ $this, 'default_product_rental_query' ], 10, 2 );

			// Show if rental product
			add_action( 'woocommerce_product_options_tax', [ $this, 'show_if_rental_product' ] );

			// Product options general
			add_action( 'woocommerce_product_options_general_product_data', [ $this, 'rental_options_general' ] );

			// Save rental meta
			add_action( 'woocommerce_process_product_meta_'.OVABRW_RENTAL, [ $this, 'save_rental_meta' ], 11 );

			// Get rental price in dashboard
			add_filter( 'woocommerce_get_price_html', [ $this, 'get_rental_price_html' ], 11, 2 );
		}

		/**
		 * Product rental selector
		 */
		public function product_rental_selector( $product_types ) {
			if ( ovabrw_array_exists( $product_types ) ) {
				$product_types[OVABRW_RENTAL] = esc_html__( 'Rental', 'ova-brw' );
			}

	        return $product_types;
		}

		/**
		 * Default product rental query
		 */
		public function default_product_rental_query( $product_type, $product_id ) {
			global $pagenow, $post_type;

		    if ( 'post-new.php' == $pagenow && 'product' == $post_type ) {
		        return OVABRW_RENTAL;
		    }

		    return $product_type;
		}

		/**
		 * Show if rental product
		 */
		public function show_if_rental_product() { ?>
			<div class="ovabrw-show-if-rental-product" style="display: none;"></div>
		<?php }

		/**
		 * Get name
		 * @param  string $name
		 * @return string
		 */
		public function get_name( $name = '' ) {
			if ( $name ) $name = OVABRW_PREFIX.$name;

			return apply_filters( OVABRW_PREFIX.'admin_meta_boxes_get_name', $name );
		}

		/**
		 * Get value
		 * @param  string $name
		 * @param  string $default
		 * @return string
		 */
		public function get_value( $name = '', $default = false ) {
			// Get post id
			$post_id = get_the_ID();
			if ( !$post_id ) return '';

			// Get value
			$value = get_post_meta( $post_id, $this->get_name( $name ), true );

			// Default value
			if ( !$value && $default !== false ) $value = $default;

			return apply_filters( OVABRW_PREFIX.'admin_meta_boxes_get_value', $value, $name, $default );
		}

		/**
		 * Update meta
		 */
		public function update_meta( $post_id = '', $name = '', $data = [], $type = '', $default = false, $delete = true ) {
			do_action( OVABRW_PREFIX.'before_update_meta', $post_id, $name, $data, $type, $default );

			if ( !$post_id || !$name ) return;

			// Meta key
			$meta_key = $this->get_name( $name );

			if ( '' !== ovabrw_get_meta_data( $meta_key, $data ) ) {
				if ( 'html' == $type ) {
					$meta_value = wp_kses_post( trim( $data[$meta_key] ) );
				} else {
					$meta_value = wc_clean( wp_unslash( $data[$meta_key] ) );
				}

				if ( !$meta_value && $default !== false ) {
					$meta_value = $default;
				}

				if ( '' !== $meta_value ) {
					if ( 'date' === $type ) {
						$meta_value = ovabrw_format_date( $meta_value );
					} elseif ( 'timestamp' === $type ) {
						$meta_value = ovabrw_recursive_array_date( $meta_value );
					} elseif ( 'date-no-year' === $type ) {
						$meta_value = ovabrw_recursive_array_date_no_year( $meta_value );
					} elseif ( 'number' === $type ) {
						$meta_value = ovabrw_format_number( $meta_value );
					} elseif ( 'price' === $type ) {
						$meta_value = ovabrw_format_price( $meta_value );
					} elseif ( 'slug' === $type ) {
						$meta_value = ovabrw_sanitize_title( $meta_value );
					} elseif ( 'exists' === $type ) {
						$meta_value = ovabrw_recursive_array_exists( $meta_value );
					}

					update_post_meta( $post_id, $meta_key, $meta_value );
				} else {
					if ( $delete ) delete_post_meta( $post_id, $meta_key );
				}
			} else {
				if ( $delete ) delete_post_meta( $post_id, $meta_key );
			}

			do_action( OVABRW_PREFIX.'after_update_meta', $post_id, $name, $data, $type, $default );
		}

		/**
		 * Rental options general
		 */
		public function rental_options_general() {
			global $product_object, $rental_product;

			$rental_id = $product_object->get_id();

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $rental_id );
			if ( !$rental_product ) {
				$rental_product = OVABRW()->rental->get_rental_product( $rental_id, 'day' );
			}

			include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/views/html-rental-data-general.php' );
		}

		/**
		 * Save rental meta
		 */
		public function save_rental_meta( $post_id, $data = [] ) {
			if ( !ovabrw_array_exists( $data ) ) $data = $_POST;

			// Get product type
            $product_type = ovabrw_get_meta_data( 'product-type', $data );
            if ( !$product_type ) $product_type = ovabrw_get_meta_data( 'product_type', $data );
            if ( OVABRW_RENTAL !== $product_type ) return;

			// Rental type
			$rental_type = ovabrw_get_meta_data( $this->get_name( 'price_type' ), $data );
			if ( !$rental_type ) return;

			// Guest options
			$guest_options = OVABRW()->options->get_guest_options( $post_id );

			// Update rental type
			$this->update_meta( $post_id, 'price_type', $data );

			// Regular price by day
			$this->update_meta( $post_id, 'regular_price_day', $data, 'price' );

			// Regular price by hour
			$this->update_meta( $post_id, 'regul_price_hour', $data, 'price' );

			// Regular price by taxi
			$this->update_meta( $post_id, 'regul_price_taxi', $data, 'price' );
			$this->update_meta( $post_id, 'base_price', $data, 'price' );

			// Regular price by hotel
			$this->update_meta( $post_id, 'regular_price_hotel', $data, 'price' );

			// Charged by
			$this->update_meta( $post_id, 'define_1_day', $data );

			// Unfixed time
			$this->update_meta( $post_id, 'unfixed_time', $data );

			// Insurance amount
			$this->update_meta( $post_id, 'amount_insurance', $data, 'price' );

			// Use location
			$this->update_meta( $post_id, 'use_location', $data );

			// Time slots
			$this->update_meta( $post_id, 'time_slots_label', $data );
			$this->update_meta( $post_id, 'time_slots_location', $data );
			$this->update_meta( $post_id, 'time_slots_start', $data, 'date' );
			$this->update_meta( $post_id, 'time_slots_end', $data, 'date' );
			$this->update_meta( $post_id, 'time_slots_price', $data, 'price' );
			$this->update_meta( $post_id, 'time_slots_quantity', $data, 'number' );

			// Max seats
			$this->update_meta( $post_id, 'max_seats', $data, 'number' );

			// Tour - Duration type
			$this->update_meta( $post_id, 'duration_type', $data );

			// Tour - Number of days
			$this->update_meta( $post_id, 'numberof_days', $data, 'number' );

			// Tour - Standard price
			$this->update_meta( $post_id, 'standard_price', $data, 'price' );

			// Tour - Time slots
			$this->update_meta( $post_id, 'tour_timeslots_label', $data );
			$this->update_meta( $post_id, 'tour_timeslots_start', $data, 'timestamp' );
			$this->update_meta( $post_id, 'tour_timeslots_end', $data, 'timestamp' );
			$this->update_meta( $post_id, 'tour_timeslots_max_guests', $data, 'number' );

			// Tour - Period
			$this->update_meta( $post_id, 'period_label', $data );
			$this->update_meta( $post_id, 'period_start', $data, 'date' );
			$this->update_meta( $post_id, 'period_end', $data, 'date' );
			$this->update_meta( $post_id, 'period_max_guests', $data, 'number' );

			// Tour - Specific time
			$this->update_meta( $post_id, 'specific_from', $data, 'date-no-year' );
			$this->update_meta( $post_id, 'specific_to', $data, 'date-no-year' );
			$this->update_meta( $post_id, 'specific_label', $data );
			$this->update_meta( $post_id, 'specific_start', $data, 'timestamp' );
			$this->update_meta( $post_id, 'specific_end', $data, 'timestamp' );
			$this->update_meta( $post_id, 'specific_max_guests', $data, 'number' );

			// Tour - Discounts
			$this->update_meta( $post_id, 'discount_applicable', $data );
			$this->update_meta( $post_id, 'discount_from', $data, 'number' );
			$this->update_meta( $post_id, 'discount_to', $data, 'number' );

			// Tour - Special times
			$this->update_meta( $post_id, 'special_from', $data, 'date-no-year' );
			$this->update_meta( $post_id, 'special_to', $data, 'date-no-year' );

			// Special discount
			$key = $this->get_name( 'special_discount' );
			if ( ovabrw_get_meta_data( $key, $data ) ) {
				foreach ( ovabrw_get_meta_data( $key, $data ) as $k => $items ) {
					// From
					if ( isset( $data[$key][$k]['from'] ) ) {
						$data[$key][$k]['from'] = ovabrw_recursive_array_number( $items['from'] );
					}
					
					// To
					if ( isset( $data[$key][$k]['to'] ) ) {
						$data[$key][$k]['to'] = ovabrw_recursive_array_number( $items['to'] );
					}

					// Guest prices
					foreach ( $guest_options as $guest ) {
						if ( isset( $data[$key][$k][$guest['name'].'_price'] ) ) {
							$data[$key][$k][$guest['name'].'_price'] = ovabrw_recursive_array_price( $items[$guest['name'].'_price'] );
						}
					}
				}
			}
			$this->update_meta( $post_id, 'special_discount', $data );

			// Tour - Extra services
			$this->update_meta( $post_id, 'extra_service_id', $data, 'slug' );
			$this->update_meta( $post_id, 'extra_service_label', $data );
			$this->update_meta( $post_id, 'extra_service_required', $data );
			$this->update_meta( $post_id, 'extra_service_display', $data );
			$this->update_meta( $post_id, 'extra_service_guests', $data );
			$this->update_meta( $post_id, 'extra_service_description', $data );

			// Tour - Extra service options
			$this->update_meta( $post_id, 'extra_service_option_id', $data, 'slug' );
			$this->update_meta( $post_id, 'extra_service_option_name', $data );
			$this->update_meta( $post_id, 'extra_service_option_guest', $data, 'number' );
			$this->update_meta( $post_id, 'extra_service_option_type', $data );

			// Enable deposit
			$this->update_meta( $post_id, 'enable_deposit', $data );

			// Show full payment
			$this->update_meta( $post_id, 'force_deposit', $data );

			// Default deposit
			$this->update_meta( $post_id, 'default_deposit', $data );

			// Deposit type
			$this->update_meta( $post_id, 'type_deposit', $data );

			// Deposit amount
			$this->update_meta( $post_id, 'amount_deposit', $data, 'price' );

			// Inventory
			$this->update_meta( $post_id, 'manage_store', $data );

			// Stock quantity
			$this->update_meta( $post_id, 'car_count', $data, 'number' );

			// Guest prices
			if ( ovabrw_array_exists( $guest_options ) ) {
				foreach ( $guest_options as $guest ) {
					// Guest price
					if ( 'tour' === $rental_type ) {
						// Get duration type
						$duration = ovabrw_get_meta_data( $this->get_name( 'duration_type' ), $data );
						if ( 'fixed' === $duration ) {
							$this->update_meta( $post_id, $guest['name'].'_price', $data, 'price' );
						} else {
							// Get standard price
							$standard_price = ovabrw_format_price( $this->get_value( 'standard_price' ) );
							update_post_meta( $post_id, $this->get_name( $guest['name'].'_price' ), $standard_price );
						}
					} else {
						$this->update_meta( $post_id, $guest['name'].'_price', $data, 'price' );
					}

					// Min & Max number of guest
					$this->update_meta( $post_id, 'min_'.$guest['name'], $data, 'number' );
					$this->update_meta( $post_id, 'max_'.$guest['name'], $data, 'number' );

					// Tour - Time slots
					$this->update_meta( $post_id, 'tour_timeslots_'.$guest['name'].'_price', $data, 'price', false, false );

					// Tour - Period
					$this->update_meta( $post_id, 'period_'.$guest['name'].'_price', $data, 'price', false, false );

					// Tour - Specific time
					$this->update_meta( $post_id, 'specific_'.$guest['name'].'_price', $data, 'price', false, false );

					// Tour - Discount
					$this->update_meta( $post_id, 'discount_'.$guest['name'].'_price', $data, 'price', false, false );

					// Tour - Special Time
					$this->update_meta( $post_id, 'special_'.$guest['name'].'_price', $data, 'price', false, false );

					// Tour - Extra service
					$this->update_meta( $post_id, 'extra_service_option_'.$guest['name'].'_price', $data, 'price', false, false );
				}

				// Min guests
				$this->update_meta( $post_id, 'min_guest', $data, 'number' );

				// Max guests
				$this->update_meta( $post_id, 'max_guest', $data, 'number' );
			} // END

			// Get regular price
			switch ( $rental_type ) {
				case 'day':
					$regular_price = ovabrw_format_price( ovabrw_get_meta_data( $this->get_name( 'regular_price_day' ), $data ) );
					break;
				case 'hour':
		            $regular_price = ovabrw_format_price( ovabrw_get_meta_data( $this->get_name( 'regul_price_hour' ), $data ) );
					break;
				case 'mixed':
					$regular_price = ovabrw_format_price( ovabrw_get_meta_data( $this->get_name( 'regular_price_day' ), $data ) );
					break;
				case 'period_time':
					$petime_prices = ovabrw_format_price( ovabrw_get_meta_data( $this->get_name( 'petime_price' ), $data ) );
					if ( ovabrw_array_exists( $petime_prices ) ) {
						$regular_price = min( $petime_prices );
					}
					break;
				case 'transportation':
					$location_prices = ovabrw_format_price( ovabrw_get_meta_data( $this->get_name( 'price_location' ), $data ) );
					if ( ovabrw_array_exists( $location_prices ) ) {
						$regular_price = min( $location_prices );
					}
					break;
				case 'taxi':
		            $regular_price = ovabrw_format_price( ovabrw_get_meta_data( $this->get_name( 'regul_price_taxi' ), $data ) );
					break;
				case 'hotel':
					$regular_price = ovabrw_format_price( ovabrw_get_meta_data( $this->get_name( 'regular_price_hotel' ), $data ) );
					break;
				case 'appointment':
					$timeslots_prices = ovabrw_format_price( ovabrw_get_meta_data( $this->get_name( 'time_slots_price' ), $data ) );
					if ( ovabrw_array_exists( $timeslots_prices ) ) {
						$flat_array = array_merge(...array_values( $timeslots_prices ) );
						if ( ovabrw_array_exists( $flat_array ) ) {
							$regular_price = min( $flat_array );
						}
					}
					break;
				case 'tour':
					// Get duration type
					$duration = ovabrw_get_meta_data( $this->get_name( 'duration_type' ), $data );
					if ( 'fixed' === $duration ) {
						// Get first guest name
						$guest_name = isset( $guest_options[0]['name'] ) ? $guest_options[0]['name'] : '';
						if ( $guest_name ) {
							$regular_price = wc_format_decimal( ovabrw_get_meta_data( $this->get_name( $guest_name.'_price' ), $data, 0 ) );
							if ( !$regular_price ) {
								$regular_price = wc_format_decimal( $this->get_value( $guest_name.'_price' ) );
							}

							update_post_meta( $post_id, 'ovabrw_standard_price', $regular_price );
						}
					} elseif ( 'timeslots' === $duration || 'period' === $duration ) {
						$regular_price = wc_format_decimal( ovabrw_get_meta_data( $this->get_name( 'standard_price' ), $data ) );
					}
					break;
				default:
					$regular_price = 0;
					break;
			} // END

			// Filter
			$regular_price = apply_filters( OVABRW_PREFIX.'update_regular_price', $regular_price, $post_id, $data );

			// Update regular price
			update_post_meta( $post_id, '_regular_price', $regular_price );
            update_post_meta( $post_id, '_price', $regular_price );

            // Update product lookup
            $regular_price = apply_filters( OVABRW_PREFIX.'update_product_meta_look', $regular_price, $post_id, $data );
            $this->update_product_meta_look( $post_id, $regular_price );

			// Vehicle ids
			$this->update_meta( $post_id, 'id_vehicles', $data );

            // Daily
            $this->update_meta( $post_id, 'daily_monday', $data, 'price' );
            $this->update_meta( $post_id, 'daily_tuesday', $data, 'price' );
            $this->update_meta( $post_id, 'daily_wednesday', $data, 'price' );
            $this->update_meta( $post_id, 'daily_thursday', $data, 'price' );
            $this->update_meta( $post_id, 'daily_friday', $data, 'price' );
            $this->update_meta( $post_id, 'daily_saturday', $data, 'price' );
            $this->update_meta( $post_id, 'daily_sunday', $data, 'price' );

            // Packages
            $this->update_meta( $post_id, 'petime_id', $data, 'slug' );
            $this->update_meta( $post_id, 'petime_price', $data, 'price' );
            $this->update_meta( $post_id, 'package_type', $data );
            $this->update_meta( $post_id, 'petime_days', $data, 'price' );
            $this->update_meta( $post_id, 'pehour_start_time', $data, 'date' );
            $this->update_meta( $post_id, 'pehour_end_time', $data, 'date' );
            $this->update_meta( $post_id, 'pehour_unfixed', $data, 'price' );
            $this->update_meta( $post_id, 'petime_label', $data );

            // Package discounts
            $key = ovabrw_meta_key( 'petime_discount' );
            if ( ovabrw_get_meta_data( $key, $data ) ) {
            	foreach ( ovabrw_get_meta_data( $key, $data ) as $k => $item ) {
            		// Price
            		if ( isset( $item['price'] ) ) {
            			$data[$key][$k]['price'] = ovabrw_format_price( $item['price'] );
            		}

            		// Start time
            		if ( isset( $item['start_time'] ) ) {
            			$data[$key][$k]['start_time'] = ovabrw_format_date( $item['start_time'] );
            		}

            		// End time
            		if ( isset( $item['end_time'] ) ) {
            			$data[$key][$k]['end_time'] = ovabrw_format_date( $item['end_time'] );
            		}
            	}
            }
            $this->update_meta( $post_id, 'petime_discount', $data );
            // END package discounts
            
            // Setup Map
            $this->update_meta( $post_id, 'map_price_by', $data );
            $this->update_meta( $post_id, 'waypoint', $data );
            $this->update_meta( $post_id, 'max_waypoint', $data, 'number' );
            $this->update_meta( $post_id, 'zoom_map', $data, 'number' );
            $this->update_meta( $post_id, 'map_types', $data );
            $this->update_meta( $post_id, 'bounds', $data );
            $this->update_meta( $post_id, 'bounds_lat', $data, 'price' );
            $this->update_meta( $post_id, 'bounds_lng', $data, 'price' );
            $this->update_meta( $post_id, 'bounds_radius', $data, 'price' );
            $this->update_meta( $post_id, 'restrictions', $data );

            // OpenStreetMap
            $this->update_meta( $post_id, 'map_layer', $data );
            $this->update_meta( $post_id, 'map_feature_type', $data );
            $this->update_meta( $post_id, 'bounded', $data );
            $this->update_meta( $post_id, 'min_lng', $data, 'price' );
            $this->update_meta( $post_id, 'min_lat', $data, 'price' );
            $this->update_meta( $post_id, 'max_lng', $data, 'price' );
            $this->update_meta( $post_id, 'max_lat', $data, 'price' );

            // Guests
            $this->update_meta( $post_id, 'max_guests', $data, 'number' );
            $this->update_meta( $post_id, 'min_guests', $data, 'number' );
            $this->update_meta( $post_id, 'max_adults', $data, 'number' );
            $this->update_meta( $post_id, 'min_adults', $data, 'number' );
            $this->update_meta( $post_id, 'max_children', $data, 'number' );
            $this->update_meta( $post_id, 'min_children', $data, 'number' );
            $this->update_meta( $post_id, 'max_babies', $data, 'number' );
            $this->update_meta( $post_id, 'min_babies', $data, 'number' );

            // Locations
            $this->update_meta( $post_id, 'st_pickup_loc', $data );
            $this->update_meta( $post_id, 'st_dropoff_loc', $data );
            $this->update_meta( $post_id, 'st_price_location', $data, 'price' );

            // Location Price
            $this->update_meta( $post_id, 'pickup_location', $data );
            $this->update_meta( $post_id, 'dropoff_location', $data );
            $this->update_meta( $post_id, 'price_location', $data, 'price' );
            $this->update_meta( $post_id, 'location_time', $data, 'price' );

            // Location surcharge
            $this->update_meta( $post_id, 'cal_location_surcharge', $data );
            $this->update_meta( $post_id, 'pickup_location_surcharge', $data );
            $this->update_meta( $post_id, 'pickup_surcharge_price', $data, 'price' );
            $this->update_meta( $post_id, 'dropoff_location_surcharge', $data );
            $this->update_meta( $post_id, 'dropoff_surcharge_price', $data, 'price' );

            // Extra Time
            $this->update_meta( $post_id, 'extra_time_hour', $data, 'price' );
            $this->update_meta( $post_id, 'extra_time_label', $data );
            $this->update_meta( $post_id, 'extra_time_price', $data, 'price' );

            // Specifications
            $this->update_meta( $post_id, 'specifications', $data );

            // Features
            $this->update_meta( $post_id, 'features_icons', $data );
            $this->update_meta( $post_id, 'features_label', $data );
            $this->update_meta( $post_id, 'features_desc', $data );
            $this->update_meta( $post_id, 'features_special', $data );
            $this->update_meta( $post_id, 'features_featured', $data );

            // Global Discount
            $this->update_meta( $post_id, 'global_discount_price', $data, 'price' );
            $this->update_meta( $post_id, 'global_discount_duration_val_min', $data, 'price' );
            $this->update_meta( $post_id, 'global_discount_duration_val_max', $data, 'price' );
            $this->update_meta( $post_id, 'global_discount_duration_type', $data );

            // Discount by Distance
            $this->update_meta( $post_id, 'discount_distance_from', $data, 'price' );
            $this->update_meta( $post_id, 'discount_distance_to', $data, 'price' );
            $this->update_meta( $post_id, 'discount_distance_price', $data, 'price' );

            // Special Time
            $this->update_meta( $post_id, 'rt_price', $data, 'price' );
            $this->update_meta( $post_id, 'rt_price_hour', $data, 'price' );
            $this->update_meta( $post_id, 'rt_startdate', $data, 'date' );
            $this->update_meta( $post_id, 'rt_starttime', $data, 'date' );
            $this->update_meta( $post_id, 'rt_enddate', $data, 'date' );
            $this->update_meta( $post_id, 'rt_endtime', $data, 'date' );

            // Special time discounts
            $key = ovabrw_meta_key( 'rt_discount' );
            if ( ovabrw_get_meta_data( $key, $data ) ) {
            	foreach ( ovabrw_get_meta_data( $key, $data ) as $k => $item ) {
            		// Price
            		if ( isset( $item['price'] ) ) {
            			$data[$key][$k]['price'] = ovabrw_format_price( $item['price'] );
            		}

            		// From
            		if ( isset( $item['min'] ) ) {
            			$data[$key][$k]['min'] = ovabrw_format_price( $item['min'] );
            		}

            		// To
            		if ( isset( $item['max'] ) ) {
            			$data[$key][$k]['max'] = ovabrw_format_price( $item['max'] );
            		}
            	}
            }
            $this->update_meta( $post_id, 'rt_discount', $data );
            // END special time discounts
            
            // Special Time by Distance
            $this->update_meta( $post_id, 'st_pickup_distance', $data, 'date' );
            $this->update_meta( $post_id, 'st_pickoff_distance', $data, 'date' );
            $this->update_meta( $post_id, 'st_price_distance', $data, 'price' );

            // Discount distance
            $key = ovabrw_meta_key( 'st_discount_distance' );
            if ( ovabrw_get_meta_data( $key, $data ) ) {
            	foreach ( ovabrw_get_meta_data( $key, $data ) as $k => $item ) {
            		// Price
            		if ( isset( $item['from'] ) ) {
            			$data[$key][$k]['from'] = ovabrw_format_price( $item['from'] );
            		}

            		// Start time
            		if ( isset( $item['to'] ) ) {
            			$data[$key][$k]['to'] = ovabrw_format_price( $item['to'] );
            		}

            		// End time
            		if ( isset( $item['price'] ) ) {
            			$data[$key][$k]['price'] = ovabrw_format_price( $item['price'] );
            		}
            	}
            }
            $this->update_meta( $post_id, 'st_discount_distance', $data );
            // END discount distance
            
            // Special Time - Appointment
            $this->update_meta( $post_id, 'special_price', $data, 'price' );
            $this->update_meta( $post_id, 'special_startdate', $data, 'date' );
            $this->update_meta( $post_id, 'special_enddate', $data, 'date' );

            // Resources
            $this->update_meta( $post_id, 'resource_id', $data, 'slug' );
            $this->update_meta( $post_id, 'resource_name', $data );
            $this->update_meta( $post_id, 'resource_price', $data, 'price' );
            $this->update_meta( $post_id, 'resource_quantity', $data, 'number' );
            $this->update_meta( $post_id, 'resource_duration_type', $data );

            // Services
            $this->update_meta( $post_id, 'label_service', $data );
            $this->update_meta( $post_id, 'service_required', $data );
            $this->update_meta( $post_id, 'service_id', $data, 'slug' );
            $this->update_meta( $post_id, 'service_name', $data );
            $this->update_meta( $post_id, 'service_price', $data, 'price' );
            $this->update_meta( $post_id, 'service_qty', $data, 'number' );
            $this->update_meta( $post_id, 'service_duration_type', $data );

            // Allowed date
            $this->update_meta( $post_id, 'allowed_startdate', $data, 'date' );
            $this->update_meta( $post_id, 'allowed_enddate', $data, 'date' );

            // Unavailable time
            $this->update_meta( $post_id, 'untime_startdate', $data, 'date' );
            $this->update_meta( $post_id, 'untime_enddate', $data, 'date' );

            // Product template
            $this->update_meta( $post_id, 'product_template', $data );

            // Disable weekday
            $this->update_meta( $post_id, 'choose_disable_weekdays', $data );
            $this->update_meta( $post_id, 'product_disable_week_day', $data );

            // Min rent day
            $this->update_meta( $post_id, 'rent_day_min', $data, 'price' );

            // Min rent hour
            $this->update_meta( $post_id, 'rent_hour_min', $data, 'price' );

            // Max rent day
            $this->update_meta( $post_id, 'rent_day_max', $data, 'price' );

            // Max rent hour
            $this->update_meta( $post_id, 'rent_hour_max', $data, 'price' );

            // Prepare vehicle day
			$this->update_meta( $post_id, 'prepare_vehicle_day', $data, 'price' );

			// Prepare vehicle hour
            $this->update_meta( $post_id, 'prepare_vehicle', $data, 'price' );

			// Preparation time
            $this->update_meta( $post_id, 'preparation_time', $data, 'price' );

            // Show guests
            $this->update_meta( $post_id, 'show_guests', $data );
            $this->update_meta( $post_id, 'guest_type', $data );
            $this->update_meta( $post_id, 'special_guests', $data );
            $this->update_meta( $post_id, 'guest_info_fields', $data );
            $this->update_meta( $post_id, 'special_guest_fields', $data );
            
            // Extra Tab
            $this->update_meta( $post_id, 'manage_extra_tab', $data );
            $this->update_meta( $post_id, 'extra_tab_shortcode', $data );

            // Start Date For Booking
            $this->update_meta( $post_id, 'manage_time_book_start', $data );
            $this->update_meta( $post_id, 'product_time_to_book_start', $data );
            $this->update_meta( $post_id, 'manage_default_hour_start', $data );
            $this->update_meta( $post_id, 'product_default_hour_start', $data );

            // Daily pick-up time step
            $this->update_meta( $post_id, 'daily_pickup_time_step', $data, 'number' );

            // Daily pick-up times
            $this->update_meta( $post_id, 'daily_pickup_times', $data );

            // Daily drop-off time step
            $this->update_meta( $post_id, 'daily_dropoff_time_step', $data, 'number' );

            // Daily drop-off times
            $this->update_meta( $post_id, 'daily_dropoff_times', $data );

            // End Date For Booking
            $this->update_meta( $post_id, 'manage_time_book_end', $data );
            $this->update_meta( $post_id, 'product_time_to_book_end', $data );
            $this->update_meta( $post_id, 'manage_default_hour_end', $data );
            $this->update_meta( $post_id, 'product_default_hour_end', $data );

            // Custom Checkout Field
            $this->update_meta( $post_id, 'manage_custom_checkout_field', $data );
            $this->update_meta( $post_id, 'product_custom_checkout_field', $data );

            // Show Pick-Up Location
            $this->update_meta( $post_id, 'show_pickup_location_product', $data );
            $this->update_meta( $post_id, 'show_other_location_pickup_product', $data );

            // Show Drop-Off Location
            $this->update_meta( $post_id, 'show_pickoff_location_product', $data );
            $this->update_meta( $post_id, 'show_other_location_dropoff_product', $data );

            // Show Pick-up Date
            $this->update_meta( $post_id, 'show_pickup_date_product', $data );
            $this->update_meta( $post_id, 'label_pickup_date_product', $data );
            $this->update_meta( $post_id, 'new_pickup_date_product', $data );

            // Show Drop-off Date
            $this->update_meta( $post_id, 'dropoff_date_by_setting', $data );
            $this->update_meta( $post_id, 'show_pickoff_date_product', $data );
            $this->update_meta( $post_id, 'label_dropoff_date_product', $data );
            $this->update_meta( $post_id, 'new_dropoff_date_product', $data );

            // Show Quantity
            $this->update_meta( $post_id, 'show_number_vehicle', $data );

            // Display Price In Format
            $this->update_meta( $post_id, 'single_price_format', $data );
            $this->update_meta( $post_id, 'single_price_new_format', $data, 'html' );
            $this->update_meta( $post_id, 'archive_price_format', $data );
            $this->update_meta( $post_id, 'archive_price_new_format', $data, 'html' );

            // Order Frontend
            $this->update_meta( $post_id, 'car_order', $data, 'number' );

            // Open StreetMap
            if ( OVABRW()->options->osm_enabled() ) {
	            $this->update_meta( $post_id, 'address', $data );
	            $this->update_meta( $post_id, 'latitude', $data );
	            $this->update_meta( $post_id, 'longitude', $data );
            } else {
            	// Google map
	            if ( ovabrw_get_meta_data( 'pac-input', $data ) ) {
		            $this->update_meta( $post_id, 'address', $data );
		            $this->update_meta( $post_id, 'latitude', $data );
		            $this->update_meta( $post_id, 'longitude', $data );
	            } else {
	            	delete_post_meta( $post_id, OVABRW_PREFIX.'address' );
	            	delete_post_meta( $post_id, OVABRW_PREFIX.'latitude' );
	            	delete_post_meta( $post_id, OVABRW_PREFIX.'longitude' );
	            }
            }

            // Sync calendar
            $this->update_meta( $post_id, 'import_calendar_links', $data, 'exists' );
		}

		/**
		 * Update product meta look
		 */
		public function update_product_meta_look( $product_id, $price ) {
			if ( !$product_id ) return;

			global $wpdb;

			// Table name
			$table_name = $wpdb->prefix . 'wc_product_meta_lookup';

			// Check product exists
			$exists = $wpdb->get_var( $wpdb->prepare(
			    "SELECT COUNT(*) FROM {$table_name} WHERE product_id = %d", 
			    $product_id
			));

			if ( $exists ) {
				$wpdb->update(
			        $table_name,
			        [
			        	'min_price' => $price,
			            'max_price' => $price
			        ],
			        [ 'product_id' => $product_id ],
			        [ '%f', '%f' ],
			        [ '%d' ]
			    );
			} else {
				$wpdb->insert(
			        $table_name,
			        [
			        	'product_id' => $product_id,
			            'min_price'  => $price,
			            'max_price'  => $price,
			            'onsale'     => 0
			        ],
			        [ '%d', '%f', '%f', '%d' ]
			    );
			}
		}

		/**
		 * Rental price
		 */
		public function get_rental_price_html( $price_html, $product ) {
			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product->get_id() );
			if ( $rental_product ) {
				$price_html = apply_filters( OVABRW_PREFIX.'get_rental_price_html_in_dashboard', $rental_product->get_price_html( $price_html ) );
			}

			return $price_html;
		}

		/**
		 * Main OVABRW_Admin_Meta_Boxes instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}

	new OVABRW_Admin_Meta_Boxes();
}