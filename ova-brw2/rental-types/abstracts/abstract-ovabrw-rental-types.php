<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * abstract OVABRW Rental Types
 */
if ( !class_exists( 'OVABRW_Rental_Types', false ) ) {

	abstract class OVABRW_Rental_Types {

		/**
		 * Rental ID
		 */
		protected $id = null;

		/**
		 * Rental type
		 */
		protected $type = null;

		/**
		 * Product object
		 */
		public $product = null;

		/**
		 * Prefix rental
		 */
		protected $prefix = OVABRW_PREFIX.'rental_';

		/**
		 * Constructor
		 */
		public function __construct( $rental_id = 0 ) {
			// Set product ID
			$this->set_id( $rental_id );

			// Set product object
			$this->set_product();

			// Set rental type
			$this->set_type();
		}

		/**
		 * Set id
		 */
		public function set_id( $id ) {
			$this->id = absint( $id );
		}

		/**
		 * Set product
		 */
		public function set_product() {
			if ( !$this->product ) {
				// Get product
				$product = wc_get_product( $this->id );
				if ( $product && $product->is_type( OVABRW_RENTAL ) ) {
					$this->product = $product;
				}
			}
		}

		/**
		 * Set type
		 */
		public function set_type() {
			if ( !$this->get_type() ) {
				if ( $this->product ) {
					$this->type = $this->product->get_rental_type();
				} elseif ( $this->id ) {
					// Get product
					$product = wc_get_product( $this->id );

					if ( $product && $product->is_type( OVABRW_RENTAL ) ) {
						$this->product 	= $product;
						$this->type 	= $product->get_meta_value( 'price_type' );
					}
				}
			}

			$this->type = $this->get_type();
		}

		/**
		 * Get id
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get type
		 */
		public function get_type() {
			return $this->type ? $this->type : 'day';
		}

		/**
		 * Get title
		 */
		public function get_title() {
			return $this->product->get_title();
		}

		/**
		 * is type
		 */
		public function is_type( $type ) {
			if ( $type && $type === $this->type ) {
				return true;
			}

			return false;
		}

		/**
	     * Get meta name with prefix
	     */
	    public function get_meta_name( $name = '' ) {
	        if ( $name ) $name = OVABRW_PREFIX.$name;

	        return apply_filters( $this->prefix.'get_meta_name', $name, $this );
	    }

	    /**
		 * Get meta value from database
		 */
		public function get_meta_value( $name = '', $default = false ) {
			if ( !$this->id ) $this->id = get_the_ID();
			
			$value = get_post_meta( $this->id, $this->get_meta_name( $name ), true );
			if ( !$value && $default !== false ) $value = $default;

			return apply_filters( $this->prefix.'get_meta_value', $value, $name, $default, $this );
		}

		/**
		 * Get meta fields
		 */
		public function get_meta_fields() {
			return (array)apply_filters( $this->prefix.'get_meta_fields', [], $this );
		}

		/**
		 * View meta boxes
		 */
		public function view_meta_boxes() {
			foreach ( $this->get_meta_fields() as $field ) {
				// Show/hide field
				if ( !apply_filters( OVABRW_PREFIX."product_editor_{$field}_field", true ) ) {
					continue;
				}

				do_action( $this->prefix.'view_meta_boxes_before', $this );

				$file_path = OVABRW_PLUGIN_ADMIN . "meta-boxes/fields/html-{$field}.php";
				
				do_action( $this->prefix.'view_meta_boxes_after', $this );

				if ( file_exists( $file_path ) ) {
					include $file_path;
				}
			}
		}

		/**
		 * Get create booking meta fields
		 */
		public function get_create_booking_meta_fields() {
			return (array)apply_filters( $this->prefix.'get_create_booking_meta_fields', [], $this );
		}

		/**
		 * Create booking view meta boxes
		 */
		public function create_booking_view_meta_boxes( $args = [] ) {
			ob_start();

			$file_path = OVABRW_PLUGIN_ADMIN.'bookings/views/html-create-booking-meta-boxes.php';

			if ( file_exists( $file_path ) ) {
				include $file_path;
			}

			return ob_get_clean();
		}

		/**
		 * Get html price
		 */
		public function get_price_html( $price_html = '', $currency = '' ) {
			return apply_filters( $this->prefix.'get_product_price_html', $price_html, $price_html, $currency, $this );
		}

		/**
		 * Get total
		 */
		public function get_total( $args = [] ) {
			// Pick-up date
	    	$pickup_date = ovabrw_get_meta_data( 'pickup_date', $args );
	    	if ( !$pickup_date ) return 0;

	    	// Drop-off date
	    	$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $args );
	    	if ( !$dropoff_date ) return 0;

	    	// init
	    	$line_total = 0;

	    	// Get rental calculations
	    	$rental_calculations = $this->get_rental_calculations( $args );
	    	if ( $rental_calculations ) {
	    		$line_total += $rental_calculations;
	    	}

	    	// Pick-up location
	    	$pickup_location = ovabrw_get_meta_data( 'pickup_location', $args );

	    	// Drop-off location
	    	$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $args );

	    	// Location prices
	    	$location_prices = $this->get_location_prices( $pickup_location, $dropoff_location );
	    	if ( $location_prices ) $line_total += $location_prices;

	    	// Get location surcharge
	    	$location_surcharge = $this->get_location_surcharge( $pickup_location, $dropoff_location );
	    	if ( ovabrw_array_exists( $location_surcharge ) ) {
	    		$line_total += (float)ovabrw_get_meta_data( 'pickup_location', $location_surcharge ) + (float)ovabrw_get_meta_data( 'dropoff_location', $location_surcharge );
	    	}

	    	// Guest options
	        $guest_options = $this->get_guest_options();
	        foreach ( $guest_options as $guests ) {
	        	// Check required price
	        	if ( 'yes' != ovabrw_get_meta_data( 'required_price', $guests ) ) continue;

	        	$numberof_guests = (int)ovabrw_get_meta_data( 'numberof_'.$guests['name'], $args );
	        	if ( $numberof_guests ) {
	        		$guests_price = (float)$this->get_meta_value( $guests['name'].'_price' );

	        		// Update line total
	        		$line_total += $guests_price * $numberof_guests;
	        	}
	        }

	    	// CCKF
	    	$cckf 			= ovabrw_get_meta_data( 'cckf', $args );
	    	$cckf_qty 		= ovabrw_get_meta_data( 'cckf_qty', $args );
	    	$cckf_prices 	= $this->get_cckf_prices( $cckf, $cckf_qty );
	    	if ( $cckf_prices ) $line_total += $cckf_prices;

	    	// Resources
	    	$resources 			= ovabrw_get_meta_data( 'resources', $args );
	    	$resources_qty 		= ovabrw_get_meta_data( 'resources_qty', $args );
	    	$resource_prices 	= $this->get_resource_prices( $pickup_date, $dropoff_date, $resources, $resources_qty );
	    	if ( $resource_prices ) $line_total += $resource_prices;

	    	// Services
	    	$services 		= ovabrw_get_meta_data( 'services', $args );
	    	$services_qty 	= ovabrw_get_meta_data( 'services_qty', $args );
	    	$service_prices = $this->get_service_prices( $pickup_date, $dropoff_date, $services, $services_qty );
	    	if ( $service_prices ) $line_total += $service_prices;

	    	// Quantity
	    	$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $args );
	    	if ( !$quantity ) $quantity = (int)ovabrw_get_meta_data( 'quantity', $args, 1 );
	    	$line_total *= $quantity;

			return apply_filters( $this->prefix.'get_total', $line_total, $args, $this );
		}

		/**
		 * Get price details
		 */
		public function get_price_details( $cart_item = [] ) {
		    return apply_filters( $this->prefix.'get_price_details', 0, $cart_item, $this );
		}

		/**
		 * Get rental calculations
		 */
		public function get_rental_calculations( $args = [] ) {
			return apply_filters( $this->prefix.'get_rental_calculations', 0, $args, $this );
		}

		/**
	     * Get datepicker options
	     */
	    public function get_datepicker_options() {
        	return apply_filters( $this->prefix.'get_datepicker_options', [], $this );
	    }

	    /**
	     * Get preparation time
	     */
	    public function get_preparation_time( $date_format = '' ) {
	    	// init
	    	$date = '';

	    	// Get preparation time
	    	$preparation_time = (float)$this->get_meta_value( 'preparation_time' );
	    	if ( $preparation_time ) {
	    		// Date format
	    		if ( !$date_format ) {
	    			$date_format = OVABRW()->options->get_date_format();
	    		} // END

	    		if ( $preparation_time == 1 ) {
	                $date = gmdate( $date_format, strtotime( '+1 day' ) );
	            } else {
	                $strtotime  = current_time( 'timestamp' ) + $preparation_time*86400;
	                $date       = gmdate( $date_format, $strtotime );
	            }
	    	}

	    	return apply_filters( $this->prefix.'get_preparation_time', $date, $date_format, $this );
	    }

	    /**
	     * Get disable weekdays
	     */
	    public function get_disable_weekdays() {
	    	// init
	    	$disable_weekdays = [];

	    	// Choose disable weekdays
	    	$choose_disable_weekdays = $this->get_meta_value( 'choose_disable_weekdays' );
	    	if ( '' == $choose_disable_weekdays ) {
	    		$disable_weekdays = $this->get_meta_value( 'product_disable_week_day' );
	    		if ( empty( $disable_weekdays ) ) {
	    			$disable_weekdays = ovabrw_get_setting( 'calendar_disable_week_day', '' );
	    		}
	    	} elseif ( 'none' === $choose_disable_weekdays ) {
	    		return apply_filters( $this->prefix.'get_disable_weekdays', $disable_weekdays, $this );
	    	} elseif ( 'local' === $choose_disable_weekdays ) {
	    		$disable_weekdays = $this->get_meta_value( 'product_disable_week_day' );
	    	} else {
	    		$disable_weekdays = ovabrw_get_setting( 'calendar_disable_week_day', '' );
	    	}

			if ( ovabrw_array_exists( $disable_weekdays ) ) {
				$key = array_search( '7', $disable_weekdays );
				if ( $key !== false ) $disable_weekdays[$key] = '7';
			} else {
				if ( $disable_weekdays && !is_array( $disable_weekdays ) ) {
					$disable_weekdays = explode( ',', $disable_weekdays );
					$disable_weekdays = array_map( 'trim', $disable_weekdays );
				}
			}
			if ( !ovabrw_array_exists( $disable_weekdays ) ) $disable_weekdays = [];

			return apply_filters( $this->prefix.'get_disable_weekdays', $disable_weekdays, $this );
	    }

	    /**
	     * Get allowed dates
	     */
	    public function get_allowed_dates( $date_format = 'Y-m-d' ) {
	    	// Allowed dates
	    	$allowed_dates 	= [];
	    	$start_dates 	= $this->get_meta_value( 'allowed_startdate' );
	    	$end_dates 		= $this->get_meta_value( 'allowed_enddate' );

	    	if ( ovabrw_array_exists( $start_dates ) ) {
	    		// Today
	    		$today = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );

	    		foreach ( $start_dates as $k => $start ) {
	    			$start 	= strtotime( $start );
	    			$end 	= strtotime( ovabrw_get_meta_data( $k, $end_dates ) );

	    			if ( !$start || !$end || $end < $today ) continue;
	    			if ( $start < $today ) $start = $today;

	    			while ( $start <= $end ) {
	    				array_push( $allowed_dates, gmdate( $date_format, $start ) );
	    				$start = strtotime( '+1 day', $start );
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_allowed_dates', $allowed_dates, $date_format, $this );
	    }

		/**
		 * Get disabled dates
		 */
		public function get_disabled_dates( $view = '' ) {
			// Disabled full day
	    	$full_day = [];

	    	// Disabled part of day
	    	$part_day = [];

	    	// Date format
    		$date_format = OVABRW()->options->get_date_format();
    		if ( 'calendar' === $view ) {
    			$date_format = 'Y-m-d';

    			// Add current time
    			$part_day[] = [
					'title' 			=> '',
					'start' 			=> gmdate( $date_format, current_time( 'timestamp' ) ) . ' 00:00',
					'end' 				=> gmdate( $date_format . ' H:i', current_time( 'timestamp' ) ),
					'display' 			=> 'background',
					'backgroundColor' 	=> ovabrw_get_option( 'bg_disable_calendar', '#E56E00' ),
					'borderColor' 		=> ovabrw_get_option( 'bg_disable_calendar', '#E56E00' ),
					'textColor' 		=> ovabrw_get_option( 'color_disable_calendar', '#FFFFFF' )
				];

				// Today
	            $today = gmdate( 'Y-m-d', current_time( 'timestamp' ) );

	            // Preparation time
		        $preparation_time = $this->get_preparation_time( 'Y-m-d' );
		        if ( $preparation_time && strtotime( $preparation_time ) > strtotime( $today ) ) {
		        	// Get calendar events
	    			$calendar_events = $this->get_calendar_events( strtotime( $today ), strtotime( $preparation_time ), $date_format );

	    			if ( ovabrw_array_exists( $calendar_events ) ) {
	    				$full_day = array_merge_recursive( $full_day, $calendar_events['full_day'] );
	    			}
		        }
    		}

	    	// From dates
	    	$from_dates = $this->get_meta_value( 'untime_startdate' );

	    	// To dates
	    	$to_dates = $this->get_meta_value( 'untime_enddate' );

	    	// Loop
	    	if ( ovabrw_array_exists( $from_dates ) ) {
	    		foreach ( $from_dates as $k => $from_date ) {
	    			// From date
	    			$from_date = strtotime( $from_date );
	    			if ( !$from_date ) continue;

	    			// To date
	    			$to_date = strtotime( ovabrw_get_meta_data( $k, $to_dates ) );
	    			if ( !$to_date || $to_date < current_time( 'timestamp' ) ) continue;

	    			// Get calendar events
	    			$calendar_events = $this->get_calendar_events( $from_date, $to_date, $date_format );
	    			if ( ovabrw_array_exists( $calendar_events ) ) {
	    				$full_day = array_merge_recursive( $full_day, $calendar_events['full_day'] );
	    				$part_day = array_merge_recursive( $part_day, $calendar_events['part_day'] );
	    			}
	    		} // END foreach
	    	} // END if

			return apply_filters( $this->prefix.'get_disabled_dates', [
				'full_day' => $full_day,
				'part_day' => $part_day
			], $view, $this );
		}

		/**
		 * Get calendar events
		 */
		public function get_calendar_events( $from_date, $to_date, $date_format = '', $type = 'disabled' ) {
			if ( !$from_date || !$to_date ) return false;

			// Date format
			if ( !$date_format ) $date_format = OVABRW()->options->get_date_format();

			// Event display
			$display = 'block';

			// Event background
			$background = ovabrw_get_option( 'bg_disable_calendar', '#E56E00' );

			// Event color
			$color = ovabrw_get_option( 'color_disable_calendar', '#FFFFFF' );

			// Type
			if ( 'booked' === $type ) {
				// Event background
				$background = ovabrw_get_option( 'bg_booked_calendar', '#E56E00' );

				// Event color
				$color = ovabrw_get_option( 'color_booked_calendar', '#FFFFFF' );
			}

			// Disabled full day
	    	$full_day = [];

	    	// Disabled part of day
	    	$part_day = [];

	    	// Start date
			$start_date = gmdate( $date_format, $from_date );

			// End date
			$end_date = gmdate( $date_format, $to_date );

			// Number of days between
			$days_between = ovabrw_numberof_days_between( strtotime( $start_date ), strtotime( $end_date ) );

			// Start time
			$start_time = gmdate( 'H:i', $from_date );

			// End time
			$end_time = gmdate( 'H:i', $to_date );

			if ( '00:00' === $end_time ) {
				$end_date = gmdate( $date_format, $to_date - 86400 );
				$end_time = '23:59';
			}

			if ( 0 == $days_between ) {
				if ( '00:00' === $start_time && '23:59' === $end_time ) {
					// Add full day
					$full_day[] = $start_date;
				} else {
					// Render title
					$title = sprintf( esc_html__( '%s - %s', 'ova-brw' ), OVABRW()->options->get_string_time( $start_time ), OVABRW()->options->get_string_time( $end_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $start_date . ' ' . $start_time,
						'end' 				=> $end_date . ' ' . $end_time,
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];
				}
			} elseif ( 1 == $days_between ) {
				if ( '00:00' === $start_time && '23:59' === $end_time ) {
					// Add full day
					if ( $start_date === $end_date ) {
						$full_day[] = $start_date;
					} else {
						$full_day[] = $start_date;
						$full_day[] = $end_date;
					}
				} elseif ( '00:00' === $start_time ) {
					// Add full day
					if ( $start_date != $end_date ) {
						$full_day[] = $start_date;
					}

					// Render title
					$title = sprintf( esc_html__( 'until %s', 'ova-brw' ), OVABRW()->options->get_string_time( $end_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $end_date . ' 00:00',
						'end' 				=> $end_date . ' ' . $end_time,
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];
				} elseif ( '23:59' === $end_time ) {
					// Add full day
					if ( $start_date != $end_date ) {
						$full_day[] = $end_date;
					}

					// Render title
					$title = sprintf( esc_html__( 'from %s', 'ova-brw' ), OVABRW()->options->get_string_time( $start_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $start_date . ' ' . $start_time,
						'end' 				=> $start_date . ' 23:59',
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];
				} else {
					// Render title
					$title = sprintf( esc_html__( 'until %s', 'ova-brw' ), OVABRW()->options->get_string_time( $end_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $end_date . ' 00:00',
						'end' 				=> $end_date . ' ' . $end_time,
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];

					// Render title
					$title = sprintf( esc_html__( 'from %s', 'ova-brw' ), OVABRW()->options->get_string_time( $start_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $start_date . ' ' . $start_time,
						'end' 				=> $start_date . ' 23:59',
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];
				}
			} else {
				if ( '00:00' === $start_time && '23:59' === $end_time ) {
					// Add full day
					if ( $start_date === $end_date ) {
						$full_day[] = $start_date;
					} else {
						$full_day[] = $start_date;
						$full_day[] = $end_date;
					}
				} elseif ( '00:00' === $start_time ) {
					// Add full day
					if ( $start_date != $end_date ) {
						$full_day[] = $start_date;
					}

					// Render title
					$title = sprintf( esc_html__( 'until %s', 'ova-brw' ), OVABRW()->options->get_string_time( $end_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $end_date . ' 00:00',
						'end' 				=> $end_date . ' ' . $end_time,
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];
				} elseif ( '23:59' === $end_time ) {
					// Add full day
					if ( $start_date != $end_date ) {
						$full_day[] = $end_date;
					}

					// Render title
					$title = sprintf( esc_html__( 'from %s', 'ova-brw' ), OVABRW()->options->get_string_time( $start_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $start_date . ' ' . $start_time,
						'end' 				=> $start_date . ' 23:59',
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];
				} else {
					// Render title
					$title = sprintf( esc_html__( 'from %s', 'ova-brw' ), OVABRW()->options->get_string_time( $start_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $start_date . ' ' . $start_time,
						'end' 				=> $start_date . ' 23:59',
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];

					// Render title
					$title = sprintf( esc_html__( 'until %s', 'ova-brw' ), OVABRW()->options->get_string_time( $end_time ) );

					// Add date
					$part_day[] = [
						'title' 			=> apply_filters( OVABRW_PREFIX.'calendar_event_show_title', false ) ? $title : '',
						'start' 			=> $end_date . ' 00:00',
						'end' 				=> $end_date . ' ' . $end_time,
						'display' 			=> $display,
						'backgroundColor' 	=> $background,
						'borderColor' 		=> $background,
						'textColor' 		=> $color
					];
				}

				// Get date range
				$date_range = ovabrw_get_date_range( strtotime( $start_date ), strtotime( $end_date ), $date_format );

				if ( ovabrw_array_exists( $date_range ) ) {
					// Remove first and last array
	                array_shift( $date_range );
	                array_pop( $date_range );

					$full_day = array_merge_recursive( $full_day, $date_range );
				}
			}

			return apply_filters( $this->prefix.'get_calendar_events', [
				'full_day' => $full_day,
				'part_day' => $part_day
			], $from_date, $to_date, $date_format, $this );
		}

		/**
	     * Get start date
	     */
	    public function get_start_date( $date_format = 'Y-m-d' ) {
	    	// init
	    	$start_date = '';

	    	// Today
	    	$today = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );

	    	// Allowed dates
	    	$allowed_dates 	= [];
	    	$start_dates 	= $this->get_meta_value( 'allowed_startdate' );
	    	$end_dates 		= $this->get_meta_value( 'allowed_enddate' );

	    	if ( ovabrw_array_exists( $start_dates ) ) {
	    		foreach ( $start_dates as $k => $start ) {
	    			$start 	= strtotime( $start );
	    			$end 	= strtotime( ovabrw_get_meta_data( $k, $end_dates ) );

	    			if ( !$start || !$end || $end < $today ) continue;
	    			if ( $start < $today ) $start = $today;
	    			
	    			$allowed_dates[] = $start;
	    		}
	    	}

	    	if ( ovabrw_array_exists( $allowed_dates ) ) {
	    		$start_date = gmdate( $date_format, min( $allowed_dates ) );
	    	}

	    	return apply_filters( $this->prefix.'get_start_date', $start_date, $this );
	    }

	    /**
	     * Get booked dates
	     */
	    public function get_booked_dates( $view = '' ) {
	    	// Date format
    		$date_format = OVABRW()->options->get_date_format();
    		if ( 'calendar' === $view ) $date_format = 'Y-m-d';

	    	// Get booking dates
	    	$booking_dates = $this->get_booking_dates( $view );

	    	// Stock quantity
	    	$stock_qty = $this->product->get_number_quantity();

	    	// Full day
	    	$full_day = [];
	    	if ( ovabrw_array_exists( ovabrw_get_meta_data( 'full_day', $booking_dates ) ) ) {
	    		foreach ( $booking_dates['full_day'] as $date => $quantity ) {
	    			// Check exists
	    			if ( in_array( $date, $full_day ) ) continue;

	    			// Check quantity
	    			if ( $quantity >= $stock_qty ) {
	    				array_push( $full_day, $date );
	    			}
	    		}
	    	}

	    	// Part day
	    	$part_day = ovabrw_get_meta_data( 'part_day', $booking_dates, [] );
	    	if ( ovabrw_array_exists( $part_day ) ) {
	    		// Get full days from part day
		    	$dates = OVABRW()->options->get_full_days( $part_day, $date_format );
		    	if ( ovabrw_array_exists( $dates ) ) {
		    		foreach ( $dates as $date => $quantity ) {
		    			// Check exists
		    			if ( in_array( $date, $full_day ) ) continue;

		    			// Check quantity
		    			if ( $quantity >= $stock_qty ) {
		    				array_push( $full_day, $date );
		    			}
		    		}
		    	}
	    	}

	    	// Get booked dates from sync
	    	$dates_synced = $this->get_booked_dates_from_sync( $date_format );
	    	if ( ovabrw_array_exists( $dates_synced ) ) {
	    		$full_day = ovabrw_array_merge_unique( $full_day, $dates_synced );
	    	}

	    	return apply_filters( $this->prefix.'get_booked_dates', [
	    		'full_day' => $full_day,
	    		'part_day' => apply_filters( $this->prefix.'show_part_day', true ) ? $part_day : []
	    	], $view, $this );
	    }

	    /**
	     * Get booking dates
	     */
	    public function get_booking_dates( $view = '' ) {
	    	// Date format
    		$date_format = '';
    		if ( 'calendar' === $view ) $date_format = 'Y-m-d';

	    	// init
	    	$full_day = $part_day = [];

	    	// Booked dates from order queues table
	    	if ( OVABRW()->options->is_order_queues_completed() ) {
	    		$order_queues = OVABRW()->options->get_order_queues_data( $this->get_id() );
	    		if ( ovabrw_array_exists( $order_queues ) ) {
		    		// Time between leases
		    		$time_between_leases = $this->get_time_between_leases();

		    		// Loop
		    		foreach ( $order_queues as $order ) {
		    			// Get pick-up date
	    				$pickup_date = (int)ovabrw_get_meta_data( 'pickup_date', $order );
	    				if ( !$pickup_date ) continue;

	    				// Get drop-off date
	    				$dropoff_date = (int)ovabrw_get_meta_data( 'dropoff_date', $order );
	    				if ( !$dropoff_date ) continue;

	    				// Time between leases
        				if ( $time_between_leases ) {
        					$dropoff_date += $time_between_leases;
        				}

	    				// Get booked quantity
	    				$booked_qty = (int)ovabrw_get_meta_data( 'quantity', $order );
	    				if ( !$booked_qty ) $booked_qty = 1;

	    				// Get booked events
	    				$booked_events = $this->get_calendar_events( $pickup_date, $dropoff_date, $date_format, 'booked' );
	    				if ( ovabrw_array_exists( $booked_events ) ) {
	    					// Loop full day
	    					foreach ( $booked_events['full_day'] as $date ) {
	    						if ( array_key_exists( $date, $full_day ) ) {
	    							$full_day[$date] += $booked_qty;
	    						} else {
	    							$full_day[$date] = $booked_qty;
	    						}
	    					} // END loop

	    					// Loop part day
							foreach ( $booked_events['part_day'] as $item ) {
								$start 	= ovabrw_get_meta_data( 'start', $item );
								$end 	= ovabrw_get_meta_data( 'end', $item );

								// Get index
								$index = array_search( true, array_map( function( $item ) use ( $start, $end ) {
								    return $item['start'] === $start && $item['end'] === $end;
								}, $part_day ) );

								// Add part day
								if ( is_bool( $index ) ) {
									$item['quantity'] = $booked_qty;
									array_push( $part_day, $item );
								} else {
									$part_day[$index]['quantity'] += $booked_qty;
								}
							}
	    				} // END if
		    		} // END loop
	    		} // END if
	    	} else {
		    	// Get order booked ids
		    	$order_ids = OVABRW()->options->get_order_booked_ids( $this->get_id() );
		    	if ( ovabrw_array_exists( $order_ids ) ) {
		    		// Get product ids multi language
		    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

		    		// Time between leases
		    		$time_between_leases = $this->get_time_between_leases();

		    		// Loop order ids
		    		foreach ( $order_ids as $order_id ) {
		    			// Get order
		    			$order = wc_get_order( $order_id );
		    			if ( !$order ) continue;

		    			// Get order items
		    			$items = $order->get_items();
		    			if ( !ovabrw_array_exists( $items ) ) continue;

		    			// Loop order items
		    			foreach ( $items as $item_id => $item ) {
		    				// Get produdct id
		    				$product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';
		    				if ( !in_array( $product_id, $product_ids ) ) continue;

		    				// Get pick-up date
		    				$pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date_real' ) );
		    				if ( !$pickup_date ) continue;

		    				// Get drop-off date
		    				$dropoff_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date_real' ) );
		    				if ( !$dropoff_date ) continue;

		    				// Time between leases
	        				if ( $time_between_leases ) {
	        					$dropoff_date += $time_between_leases;
	        				}

		    				// Get booked quantity
		    				$booked_qty = (int)$item->get_meta( 'ovabrw_number_vehicle' );
		    				if ( !$booked_qty ) $booked_qty = 1;

		    				// Get booked events
		    				$booked_events = $this->get_calendar_events( $pickup_date, $dropoff_date, $date_format, 'booked' );
		    				if ( ovabrw_array_exists( $booked_events ) ) {
		    					// Loop full day
		    					foreach ( $booked_events['full_day'] as $date ) {
		    						if ( array_key_exists( $date, $full_day ) ) {
		    							$full_day[$date] += $booked_qty;
		    						} else {
		    							$full_day[$date] = $booked_qty;
		    						}
		    					} // END loop

		    					// Loop part day
								foreach ( $booked_events['part_day'] as $item ) {
									$start 	= ovabrw_get_meta_data( 'start', $item );
									$end 	= ovabrw_get_meta_data( 'end', $item );

									// Get index
									$index = array_search( true, array_map( function( $item ) use ( $start, $end ) {
									    return $item['start'] === $start && $item['end'] === $end;
									}, $part_day ) );

									// Add part day
									if ( is_bool( $index ) ) {
										$item['quantity'] = $booked_qty;
										array_push( $part_day, $item );
									} else {
										$part_day[$index]['quantity'] += $booked_qty;
									}
								}
		    				} // END if
		    			} // END loop order items
		    		} // END loop
		    	} // END if
		    } // END if

	    	return apply_filters( $this->prefix.'get_booking_dates', [
	    		'full_day' => $full_day,
	    		'part_day' => $part_day
	    	], $view, $this );
	    }

	    /**
	     * Get regular rental price
	     */
	    public function get_calendar_regular_price() {
	    	return apply_filters( $this->prefix.'get_calendar_regular_price', '', $this );
	    }

	    /**
	     * Get calendar daily prices
	     */
	    public function get_calendar_daily_prices() {
	    	return apply_filters( $this->prefix.'get_calendar_daily_prices', [], $this );
	    }

	    /**
	     * Get calendar special prices
	     */
	    public function get_calendar_special_prices() {
	    	return apply_filters( $this->prefix.'get_calendar_special_prices', [], $this );
	    }

	    /**
	     * Get calendar specific prices
	     */
	    public function get_calendar_specific_prices() {
	    	return apply_filters( $this->prefix.'get_calendar_specific_prices', [], $this );
	    }

	    /**
	     * Get new date
	     */
	    public function get_new_date( $args = [] ) {
	    	return apply_filters( $this->prefix.'get_new_date', false, $args );
	    }

	    /**
	     * Get time between leases
	     */
	    public function get_time_between_leases() {
	    	return apply_filters( $this->prefix.'get_time_between_leases', 0, $this );
	    }

	    /**
	     * Get current time
	     */
	    public function get_current_time() {
	    	// Current time
	    	$current_time = current_time( 'timestamp' );

	    	if ( !$this->product->has_timepicker() ) {
	    		$current_time = strtotime( gmdate( 'Y-m-d', $current_time ) . ' 00:00' );
	    	}

	    	return apply_filters( $this->prefix.'get_current_time', $current_time, $this );
	    }

	    /**
	     * Get rental time between
	     */
	    public function get_rental_time_between( $from_date, $end_date ) {
	    	// init
	    	$rental_time = [
	    		'numberof_rental_days' 	=> 0,
	    		'numberof_rental_hours' => 0
	    	];

	    	if ( $from_date && $end_date ) {
	    		$rental_time['numberof_rental_days'] 	= ceil( ( $end_date - $from_date ) / 86400 );
				$rental_time['numberof_rental_hours'] 	= ceil( ( $end_date - $from_date ) / 3600 );
	    	}

	    	return apply_filters( $this->prefix.'get_rental_time_between', $rental_time, $from_date, $end_date, $this );
	    }

	    /**
	     * Booking validation
	     */
	    public function booking_validation( $pickup_date, $dropoff_date, $args = [] ) {
	    	return apply_filters( $this->prefix.'booking_validation', false, $pickup_date, $dropoff_date, $args, $this );
	    }

	    /**
	     * Number of guests validation
	     * @param 	array 	$guests
	     */
	    public function numberof_guests_validation( $guests ) {
	    	// init passed
			$passed = true;

			// Get number of guests
			$numberof_guests = (int)ovabrw_get_meta_data( 'numberof_guests', $guests );

			// Get minimum number of guests
			$min_guests = $this->get_meta_value( 'min_guest' );
			if ( '' !== $min_guests && (int)$min_guests > $numberof_guests ) {
				$passed = sprintf( esc_html__( 'Minimum number of guests: %s.', 'ova-brw' ), $min_guests );
				return apply_filters( $this->prefix.'min_numberof_guests_validation', $passed, $guests, $this );
			}

			// Get maximum number of guests
			$max_guests = $this->get_meta_value( 'max_guest' );
			if ( '' !== $max_guests && (int)$max_guests < $numberof_guests ) {
				$passed = sprintf( esc_html__( 'Maximum number of guests: %s.', 'ova-brw' ), $max_guests );
				return apply_filters( $this->prefix.'max_numberof_guests_validation', $passed, $guests, $this );
			}

			// Guest information enabled
			$guest_info_enabled = $this->guest_info_enabled();

			// Guest options
			$guest_options = $this->get_guest_options();
			foreach ( $guest_options as $guest ) {
				// Number of guest
				$numberof_guest = (int)ovabrw_get_meta_data( 'numberof_'.$guest['name'], $guests );

				// Get minimum number of guest
				$min_guest = $this->get_meta_value( 'min_'.$guest['name'] );
				if ( '' !== $min_guest && (int)$min_guest > $numberof_guest ) {
					$passed = sprintf( esc_html__( 'Minimum number of %s: %s.', 'ova-brw' ), $guest['label'], $min_guest );
					return apply_filters( $this->prefix.'min_numberof_'.$guest['name'].'_validation', $passed, $guests, $this );
				}

				// Get maximum number of guest
				$max_guest = $this->get_meta_value( 'max_'.$guest['name'] );
				if ( '' !== $max_guest && $max_guest < $numberof_guest ) {
					$passed = sprintf( esc_html__( 'Maximum number of %s: %s.', 'ova-brw' ), $guest['label'], $max_guest );
					return apply_filters( $this->prefix.'max_numberof_'.$guest['name'].'_validation', $passed, $guests, $this );
				}

				// Get guest information data
				if ( $guest_info_enabled && $numberof_guest ) {
					$guest_info_item = OVABRW()->options->get_guest_info_data( $guest['name'] );
					
					if ( ovabrw_array_exists( $guest_info_item ) ) {
						$_POST['ovabrw_guest_info'][$guest['name']] = $guest_info_item;
					}
				}
			} // END loop

			return apply_filters( $this->prefix.'numberof_guests_validation', $passed, $guests, $this );
	    }

	    /**
	     * Preparation time validation
	     */
	    public function preparation_time_validation( $pickup_date, $dropoff_date ) {
	    	// init
	    	$mesg = false;

	    	// Get preparation time
	    	$preparation_time = (float)$this->get_meta_value( 'preparation_time' );

	    	if ( $preparation_time ) {
	    		if ( $pickup_date < ( $preparation_time*86400 + current_time( 'timestamp' ) ) ) {
	    			if ( 1 == $preparation_time ) {
	    				$mesg = sprintf( esc_html__( 'Book in advance %s day from the current date', 'ova-brw' ), $preparation_time );
	    			} else {
	    				$mesg = sprintf( esc_html__( 'Book in advance %s days from the current date', 'ova-brw' ), $preparation_time );
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'preparation_time_validation', $mesg, $pickup_date, $dropoff_date, $this );
	    }

	    /**
	     * Disable weekdays validation
	     */
	    public function disable_weekdays_validation( $pickup_date, $dropoff_date ) {
	    	// init
	    	$mesg = false;

	    	// Get disable weekdays
	    	$disable_weekdays = $this->get_disable_weekdays();

	    	if ( ovabrw_array_exists( $disable_weekdays ) ) {
	    		if ( OVABRW()->options->overcome_disabled_dates() ) {
	    			// Number of pickup week
	                $numberof_pickup_week = gmdate( 'w', $pickup_date );
	                if ( '0' == $numberof_pickup_week ) $numberof_pickup_week = '7';

	                // Number of dropoff week
	                $numberof_dropoff_week = gmdate( 'w', $dropoff_date );
	                if ( '0' == $numberof_dropoff_week ) $numberof_dropoff_week = '7';

	                if ( in_array( $numberof_pickup_week, $disable_weekdays ) || in_array( $numberof_dropoff_week, $disable_weekdays ) ) {
	                	$mesg = esc_html__( 'No items are available. Please choose another time.', 'ova-brw' );
	                }
	    		} else {
	    			// Get date diff
	                $datediff = round( ( (int)$dropoff_date - (int)$pickup_date ) / 86400, wc_get_price_decimals() ) + 1;

	                // Number of pickup week
	                $numberof_week = gmdate( 'w', $pickup_date );
	                if ( '0' == $numberof_week ) $numberof_week = '7';

	                // Number of pickup timestamp
	                $timestamp = $pickup_date;

	                // Loop
	                $i = 0;
	                while ( $i <= $datediff ) {
	                	if ( in_array( $numberof_week, $disable_weekdays ) ) {
	                		$mesg = esc_html__( 'No items are available. Please choose another time.', 'ova-brw' );
	                		break;
	                	}

	                	$numberof_week 	= gmdate( 'w', $timestamp );
	                	$timestamp 		= strtotime('+1 day', $timestamp );
	                	if ( '0' == $numberof_week ) $numberof_week = '7';
	                	$i++;
	                }
	    		}
	    	}

	    	return apply_filters( $this->prefix.'disable_weekdays_validation', $mesg, $pickup_date, $dropoff_date, $this );
	    }

	    /**
	     * Disabled dates validation
	     */
	    public function disabled_dates_validation( $pickup_date, $dropoff_date ) {
	    	// init
	    	$mesg = false;

	    	// Get disabled dates
	    	$start_dates 	= $this->get_meta_value( 'untime_startdate' );
	    	$end_dates 		= $this->get_meta_value( 'untime_enddate' );

	    	if ( ovabrw_array_exists( $start_dates ) ) {
	    		foreach ( $start_dates as $k => $start_date ) {
	    			$start_date = strtotime( $start_date );
	    			$end_date 	= strtotime( ovabrw_get_meta_data( $k, $end_dates ) );

	    			if ( !$start_date || !$end_date ) continue;

	    			if ( !( $start_date > $dropoff_date || $end_date < $pickup_date ) ) {
        				$mesg = esc_html__( 'No items are available. Please choose another time.', 'ova-brw' );
        			}
	    		}
	    	}

	    	// Get synced order ids
	    	$order_ids = OVABRW()->options->get_synced_order_ids( $this->get_id() );
	    	if ( ovabrw_array_exists( $order_ids ) ) {
	    		foreach ( $order_ids as $order_id ) {
	    			// Get order
					$order = wc_get_order( $order_id );

					// Get start date
					$start_date = (int)$order->get_meta( '_ovabrw_pickup_date' );
					if ( !$start_date ) continue;

					// Get end date
					$end_date = (int)$order->get_meta( '_ovabrw_dropoff_date' );
					if ( !$end_date ) continue;

					if ( !( $start_date > $dropoff_date || $end_date < $pickup_date ) ) {
        				$mesg = esc_html__( 'No items are available. Please choose another time.', 'ova-brw' );
        			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'disabled_dates_validation', $mesg, $pickup_date, $dropoff_date, $this );
	    }

	    /**
	     * Location validation
	     */
	    public function location_validation( $pickup_location, $dropoff_location ) {
	    	// Get data pickup location
	    	$data_pickup = [];

	    	// Get data dropoff location
	    	$data_dropoff = [];

	    	if ( $this->is_type( 'transportation' ) ) {
	    		$data_pickup 	= $this->get_meta_value( 'pickup_location' );
	    		$data_dropoff 	= $this->get_meta_value( 'dropoff_location' );
	    	} else {
	    		$data_pickup 	= $this->get_meta_value( 'st_pickup_loc' );
	    		$data_dropoff 	= $this->get_meta_value( 'st_dropoff_loc' );
	    	}

	    	// Loop
	    	if ( ovabrw_array_exists( $data_pickup ) && ovabrw_array_exists( $data_dropoff ) ) {
	    		foreach ( $data_pickup as $i => $pickup ) {
	    			// Pick-up location
	    			$pickup = trim( $pickup );

	    			// Drop-off location
	    			$dropoff = trim( ovabrw_get_meta_data( $i, $data_dropoff ) );

	    			if ( $pickup_location && $dropoff_location && $pickup_location === $pickup && $dropoff_location === $dropoff ) {
	    				return apply_filters( $this->prefix.'location_validation', true, $pickup_location, $dropoff_location, $this );
	    			} elseif ( $pickup_location && !$dropoff_location && $pickup_location === $pickup ) {
	    				return apply_filters( $this->prefix.'location_validation', true, $pickup_location, $dropoff_location, $this );
	    			} elseif ( !$pickup_location && $dropoff_location && $dropoff_location === $dropoff ) {
	    				return apply_filters( $this->prefix.'location_validation', true, $pickup_location, $dropoff_location, $this );
	    			}
	    		}
	    	} else {
	    		return apply_filters( $this->prefix.'location_validation', true, $pickup_location, $dropoff_location, $this );
	    	} // END

	    	return apply_filters( $this->prefix.'location_validation', false, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get items available
	     */
	    public function get_items_available( $pickup_date, $dropoff_date, $pickup_location = '', $dropoff_location = '', $validation = 'cart' ) {
	    	// init
	    	$items_available = 0;

	    	// Check pick-up & drop-off date
	    	if ( !$pickup_date || !$dropoff_date ) {
	    		return apply_filters( $this->prefix.'get_items_available', $items_available, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $validation, $this );
	    	}

	    	if ( 'store' === $this->product->get_manage_store() ) {
	    		// Get stock quantity
	    		$stock_qty = $this->product->get_number_quantity();

	    		// Items booked from Cart
	    		$cart_booked = 0;

	    		if ( 'cart' === $validation ) {
	    			$cart_booked = $this->get_items_booked_from_cart( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location );
	    		}

	    		// Items booled from Order
	    		$order_booked = $this->get_items_booked_from_order( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location );

	    		// Items available
	    		$items_available = $stock_qty - ( $cart_booked + $order_booked );
	    		if ( $items_available < 0 ) $items_available = 0;
	    	} else {
	    		// Get vehicles available
	    		$vehicles_available = $this->get_vehicles_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location );

	    		// Vehicles booked from Cart
	    		$cart_booked = [];

	    		if ( 'cart' === $validation ) {
	    			$cart_booked = $this->get_vehicles_booked_from_cart( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location );
	    		}

	    		// Vehicles booked from Order
	    		$order_booked = $this->get_vehicles_booked_from_order( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location );

	    		// Merge
	    		$vehicles_booked = ovabrw_array_merge_unique( $cart_booked, $order_booked );

	    		// Get vehicles available
	    		$items_available = array_diff( $vehicles_available, $vehicles_booked );

	    		if ( ovabrw_array_exists( $items_available ) ) {
	    			foreach ( $items_available as $k => $vehicle_id ) {
	    				// Get post vehicle
	    				$post_vehicle = OVABRW()->options->get_post_vehicle( $vehicle_id );

	    				// Required location
	    				$required_location = ovabrw_get_meta_data( 'required_location', $post_vehicle );

	    				// Vehicle location
	    				$vehicle_location = ovabrw_get_meta_data( 'location', $post_vehicle );

	    				if ( 'yes' === $required_location && $vehicle_location != $pickup_location ) {
	    					unset( $items_available[$k] );
	    					continue;
	    				}

	    				// Disabled start
	    				$disabled_start = ovabrw_get_meta_data( 'disabled_start', $post_vehicle );

	    				// Disabled end
	    				$disabled_end = ovabrw_get_meta_data( 'disabled_end', $post_vehicle );

	    				if ( $disabled_start && $disabled_end && !( $pickup_date >= $disabled_end || $dropoff_date <= $disabled_start ) ) {
	    					unset( $items_available[$k] );
	    					continue;
	    				}
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_items_available', $items_available, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $validation, $this );
	    }

	    /**
	     * Get items booked from Cart
	     */
	    public function get_items_booked_from_cart( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_date || !$dropoff_date ) return 0;

	    	// Items booked
	    	$items_booked = 0;

	    	if ( ovabrw_array_exists( WC()->cart->get_cart() ) ) {
	    		// Get product ids multi language
	    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

	    		// Time between leases
	    		$time_between_leases = $this->get_time_between_leases();

	    		// Loop
	    		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );

					if ( in_array( $product_id, $product_ids ) ) {
						// Item pick-up date
						$item_pickup = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

						// Pick-up date real
						$pickup_real = strtotime( ovabrw_get_meta_data( 'pickup_real', $cart_item ) );

						if ( !$item_pickup || !$pickup_real ) continue;

						// Item drop-off date
						$item_dropoff = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

						// Drop-off date real
						$dropoff_real = strtotime( ovabrw_get_meta_data( 'dropoff_real', $cart_item ) );

						if ( !$item_dropoff || !$dropoff_real ) continue;

						// Time between leases
						if ( $time_between_leases ) {
							$dropoff_real += $time_between_leases;
						}

						// When Drop-off date hidden
						if ( $item_pickup === $item_dropoff ) {
							$dropoff_real -= 1;
							$dropoff_date += 1;
						}

						// Qty Cart
						$cart_qty = absint( ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item ) );
						if ( !$cart_qty ) continue;

						// Check booking period
						if ( apply_filters( OVABRW_PREFIX.'check_equal_booking_times', false, $product_id ) ) {
							if ( !( $pickup_date > $dropoff_real || $dropoff_date < $pickup_real ) ) {
								$items_booked += $cart_qty;
							}
						} else {
							if ( !( $pickup_date >= $dropoff_real || $dropoff_date <= $pickup_real ) ) {
								$items_booked += $cart_qty;
							}
						} // END if
					} // END if
				} // END loop
	    	} // END if

	    	return apply_filters( $this->prefix.'get_items_booked_from_cart', $items_booked, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get items booked from Order
	     */
	    public function get_items_booked_from_order( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_date || !$dropoff_date ) return 0;

	    	// Items booked
	    	$items_booked = 0;

	    	// Get items booked from order queues table
	    	if ( OVABRW()->options->is_order_queues_completed() ) {
	    		$order_queues = OVABRW()->options->get_order_queues_data( $this->get_id() );
	    		if ( ovabrw_array_exists( $order_queues ) ) {
	    			// Get date format
	    			$date_format = OVABRW()->options->get_date_format();

	    			// Time between leases
		    		$time_between_leases = $this->get_time_between_leases();

		    		// Loop
		    		foreach ( $order_queues as $order ) {
		    			// Pick-up date real
        				$pickup_real = (int)ovabrw_get_meta_data( 'pickup_date', $order );
        				if ( !$pickup_real ) continue;

		    			// Item pick-up date
        				$item_pickup = strtotime( gmdate( $date_format, $pickup_real ) );
        				if ( !$item_pickup ) continue;

        				// Drop-off date real
    					$dropoff_real = (int)ovabrw_get_meta_data( 'dropoff_date', $order );
    					if ( !$dropoff_real || $dropoff_real < current_time( 'timestamp' ) ) continue;

        				// Item drop-off date
    					$item_dropoff = strtotime( gmdate( $date_format, $dropoff_real ) );
    					if ( !$item_dropoff ) continue;

    					// Time between leases
        				if ( $time_between_leases ) {
        					$dropoff_real += $time_between_leases;
        				}

        				// When Drop-off date hidden
						if ( $item_pickup === $item_dropoff ) {
							$dropoff_real -= 1;
							$dropoff_date += 1;
						}

        				// Booked qty
    					$booked_qty = (int)ovabrw_get_meta_data( 'quantity', $order );
	    				if ( !$booked_qty ) $booked_qty = 1;
    					
    					// Check booking
    					if ( apply_filters( OVABRW_PREFIX.'check_equal_booking_times', false, $this->get_id() ) ) {
    						if ( !( $pickup_date > $dropoff_real || $dropoff_date < $pickup_real ) ) {
        						$items_booked += $booked_qty;
        					}
    					} else {
    						if ( !( $pickup_date >= $dropoff_real || $dropoff_date <= $pickup_real ) ) {
        						$items_booked += $booked_qty;
        					}
    					}
		    		} // END loop
	    		} // END if
	    	} else {
		    	// Get order booked ids
		    	$order_ids = OVABRW()->options->get_order_booked_ids( $this->get_id() );
		    	if ( ovabrw_array_exists( $order_ids ) ) {
		    		// Get product ids multi language
		    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

		    		// Time between leases
		    		$time_between_leases = $this->get_time_between_leases();

		    		// Loop order ids
		    		foreach ( $order_ids as $order_id ) {
		    			// Get order
		    			$order = wc_get_order( $order_id );
		    			if ( !$order || !is_object( $order ) ) continue;

		    			// Get order items
		    			$items = $order->get_items();
		    			if ( !ovabrw_array_exists( $items ) ) continue;

		    			// Loop order items
		    			foreach ( $items as $item_id => $item ) {
		    				// Product ID
		    				$product_id = $item->get_product_id();

		    				if ( in_array( $product_id, $product_ids ) ) {
		    					// Item pick-up date
	            				$item_pickup = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );

	            				// Pick-up date real
	            				$pickup_real = strtotime( $item->get_meta( 'ovabrw_pickup_date_real' ) );

	            				if ( !$item_pickup || !$pickup_real ) continue;

	            				// Item drop-off date
	        					$item_dropoff = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );

	            				// Drop-off date real
	        					$dropoff_real = strtotime( $item->get_meta( 'ovabrw_pickoff_date_real' ) );

	        					if ( !$item_dropoff || !$dropoff_real || $dropoff_real < current_time( 'timestamp' ) ) continue;

	        					// Time between leases
	            				if ( $time_between_leases ) {
	            					$dropoff_real += $time_between_leases;
	            				}

	            				// When Drop-off date hidden
								if ( $item_pickup === $item_dropoff ) {
									$dropoff_real -= 1;
									$dropoff_date += 1;
								}

	            				// Booked qty
	        					$booked_qty = absint( $item->get_meta( 'ovabrw_number_vehicle' ) );
	        					if ( !$booked_qty ) continue;

	        					// Check booking
	        					if ( apply_filters( OVABRW_PREFIX.'check_equal_booking_times', false, $this->get_id() ) ) {
	        						if ( !( $pickup_date > $dropoff_real || $dropoff_date < $pickup_real ) ) {
	            						$items_booked += $booked_qty;
	            					}
	        					} else {
	        						if ( !( $pickup_date >= $dropoff_real || $dropoff_date <= $pickup_real ) ) {
	            						$items_booked += $booked_qty;
	            					}
	        					}
		    				}
		    			} // END loop order items
		    		} // END loop order ids
		    	}
		    }

	    	return apply_filters( $this->prefix.'get_items_booked_from_order', $items_booked, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get vehicles available
	     */
	    public function get_vehicles_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location ) {
	    	// init
	    	$vehicles_available = [];

	    	// Product vehicles
    		$product_vehicles = $this->get_meta_value( 'id_vehicles', [] );
    		if ( ovabrw_array_exists( $product_vehicles ) ) {
    			foreach ( $product_vehicles as $i => $vehicle_id ) {
    				// Get vehicle data
    				$vehicle_data = OVABRW()->options->get_post_vehicle( $vehicle_id );

    				// Check required location
    				if ( 'yes' === $vehicle_data['required_location'] && $pickup_location && $pickup_location != $vehicle_data['location'] ) {
    					unset( $product_vehicles[$i] );
    					continue;
    				} // END if

    				// Check disabled dates
    				if ( $vehicle_data['disabled_start'] && $vehicle_data['disabled_end'] && $pickup_date && $dropoff_date ) {
    					if ( !( $pickup_date > $vehicle_data['disabled_end'] || $dropoff_date < $vehicle_data['disabled_start'] ) ) {
    						unset( $product_vehicles[$i] );
    						continue;
    					}
    				} // END if

    				// Add vehicles available
    				array_push( $vehicles_available, $vehicle_id );
    			} // END foreach
    		} // END if

    		return apply_filters( $this->prefix.'get_vehicles_available', $vehicles_available, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get vehicles booked from Cart
	     */
	    public function get_vehicles_booked_from_cart( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_date || !$dropoff_date ) return [];

	    	// Vehicles booked
	    	$vehicles_booked = [];

	    	if ( ovabrw_array_exists( WC()->cart->get_cart() ) ) {
	    		// Get product ids multi language
	    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

	    		// Time between leases
	    		$time_between_leases = $this->get_time_between_leases();

	    		// Loop
	    		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );

					if ( in_array( $product_id, $product_ids ) ) {
						// Item pick-up date
						$item_pickup = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

						// Pick-up date real
						$pickup_real = strtotime( ovabrw_get_meta_data( 'pickup_real', $cart_item ) );

						if ( !$item_pickup || !$pickup_real ) continue;

						// Item drop-off date
						$item_dropoff = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

						// Drop-off date real
						$dropoff_real = strtotime( ovabrw_get_meta_data( 'dropoff_real', $cart_item ) );

						if ( !$item_dropoff || !$dropoff_real ) continue;

						// Time between leases
						if ( $time_between_leases ) {
							$dropoff_real += $time_between_leases;
						}

						// When Drop-off date hidden
						if ( $item_pickup === $item_dropoff ) {
							$dropoff_real -= 1;
							$dropoff_date += 1;
						}

						// Item vehicle ids
						$item_vehicles = ovabrw_get_meta_data( 'vehicles_available', $cart_item );
						if ( !$item_vehicles ) continue;

						// Check booking period
						if ( apply_filters( OVABRW_PREFIX.'check_equal_booking_times', false, $product_id ) ) {
							if ( !( $pickup_date > $dropoff_real || $dropoff_date < $pickup_real ) ) {
								// Convert string to array
								$item_vehicles = explode( ',', $item_vehicles );
								if ( ovabrw_array_exists( $item_vehicles ) ) {
									$item_vehicles = array_map( 'trim', $item_vehicles );

									// Vehicles booked
									$vehicles_booked = ovabrw_array_merge_unique( $vehicles_booked, $item_vehicles );
								}
							}
						} else {
							if ( !( $pickup_date >= $dropoff_real || $dropoff_date <= $pickup_real ) ) {
								// Convert string to array
								$item_vehicles = explode( ',', $item_vehicles );
								if ( ovabrw_array_exists( $item_vehicles ) ) {
									$item_vehicles = array_map( 'trim', $item_vehicles );

									// Vehicles booked
									$vehicles_booked = ovabrw_array_merge_unique( $vehicles_booked, $item_vehicles );
								}
							}
						}
					}
				}
			}

			return apply_filters( $this->prefix.'get_vehicles_booked_from_cart', $vehicles_booked, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get vehicles booked from Order
	     */
	    public function get_vehicles_booked_from_order( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_date || !$dropoff_date ) return 0;

	    	// Vehicles booked
	    	$vehicles_booked = [];

	    	// Get vehicles booked from order queues table
	    	if ( OVABRW()->options->is_order_queues_completed() ) {
	    		$order_queues = OVABRW()->options->get_order_queues_data( $this->get_id() );
	    		if ( ovabrw_array_exists( $order_queues ) ) {
	    			// Get date format
	    			$date_format = OVABRW()->options->get_date_format();

	    			// Time between leases
		    		$time_between_leases = $this->get_time_between_leases();

		    		// Loop
		    		foreach ( $order_queues as $order ) {
		    			// Product ID
	    				$product_id = (int)ovabrw_get_meta_data( 'product_id', $order );

		    			// Pick-up date real
        				$pickup_real = (int)ovabrw_get_meta_data( 'pickup_date', $order );
        				if ( !$pickup_real ) continue;

		    			// Item pick-up date
        				$item_pickup = strtotime( gmdate( $date_format, $pickup_real ) );
        				if ( !$item_pickup ) continue;

        				// Drop-off date real
    					$dropoff_real = (int)ovabrw_get_meta_data( 'dropoff_date', $order );
    					if ( !$dropoff_real || $dropoff_real < current_time( 'timestamp' ) ) continue;

        				// Item drop-off date
    					$item_dropoff = strtotime( gmdate( $date_format, $dropoff_real ) );
    					if ( !$item_dropoff ) continue;

    					// Time between leases
        				if ( $time_between_leases ) {
        					$dropoff_real += $time_between_leases;
        				}

        				// When Drop-off date hidden
						if ( $item_pickup === $item_dropoff ) {
							$dropoff_real -= 1;
							$dropoff_date += 1;
						}

						// Get vehicle ids
    					$item_vehicles = ovabrw_get_meta_data( 'vehicle_id', $order );
    					if ( !$item_vehicles ) continue;

    					// Check booking
    					if ( apply_filters( OVABRW_PREFIX.'check_equal_booking_times', false, $this->get_id() ) ) {
    						if ( !( $pickup_date > $dropoff_real || $dropoff_date < $pickup_real ) ) {
        						// Convert string to array
								$item_vehicles = explode( ',', $item_vehicles );
								if ( ovabrw_array_exists( $item_vehicles ) ) {
									$item_vehicles = array_map( 'trim', $item_vehicles );

									// Vehicles booked
									$vehicles_booked = ovabrw_array_merge_unique( $vehicles_booked, $item_vehicles );
								}
        					}
    					} else {
    						if ( !( $pickup_date >= $dropoff_real || $dropoff_date <= $pickup_real ) ) {
        						// Convert string to array
								$item_vehicles = explode( ',', $item_vehicles );
								if ( ovabrw_array_exists( $item_vehicles ) ) {
									$item_vehicles = array_map( 'trim', $item_vehicles );

									// Vehicles booked
									$vehicles_booked = ovabrw_array_merge_unique( $vehicles_booked, $item_vehicles );
								}
        					}
    					} // END if
		    		} // END loop
	    		} // END if
	    	} else {
		    	// Get order booked ids
		    	$order_ids = OVABRW()->options->get_order_booked_ids( $this->get_id() );
		    	if ( ovabrw_array_exists( $order_ids ) ) {
		    		// Get product ids multi language
		    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

		    		// Time between leases
		    		$time_between_leases = $this->get_time_between_leases();

		    		// Loop order ids
		    		foreach ( $order_ids as $order_id ) {
		    			// Get order
		    			$order = wc_get_order( $order_id );
		    			if ( !$order || !is_object( $order ) ) continue;

		    			// Get order items
		    			$items = $order->get_items();
		    			if ( !ovabrw_array_exists( $items ) ) continue;

		    			// Loop order items
		    			foreach ( $items as $item_id => $item ) {
		    				// Product ID
		    				$product_id = $item->get_product_id();

		    				if ( in_array( $product_id, $product_ids ) ) {
		    					// Item pick-up date
	            				$item_pickup = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );

	            				// Pick-up date real
	            				$pickup_real = strtotime( $item->get_meta( 'ovabrw_pickup_date_real' ) );

	            				if ( !$item_pickup || !$pickup_real ) continue;

	            				// Item drop-off date
	        					$item_dropoff = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );

	            				// Drop-off date real
	        					$dropoff_real = strtotime( $item->get_meta( 'ovabrw_pickoff_date_real' ) );

	        					if ( !$item_dropoff || !$dropoff_real || $dropoff_real < current_time( 'timestamp' ) ) continue;

	        					// Time between leases
	            				if ( $time_between_leases ) {
	            					$dropoff_real += $time_between_leases;
	            				}

	            				// When Drop-off date hidden
								if ( $item_pickup === $item_dropoff ) {
									$dropoff_real -= 1;
									$dropoff_date += 1;
								}

	            				// Item vehicle ids
	        					$item_vehicles = $item->get_meta( 'id_vehicle' );
	        					if ( !$item_vehicles ) continue;

	        					// Check booking
	        					if ( apply_filters( OVABRW_PREFIX.'check_equal_booking_times', false, $this->get_id() ) ) {
	        						if ( !( $pickup_date > $dropoff_real || $dropoff_date < $pickup_real ) ) {
	            						// Convert string to array
										$item_vehicles = explode( ',', $item_vehicles );
										if ( ovabrw_array_exists( $item_vehicles ) ) {
											$item_vehicles = array_map( 'trim', $item_vehicles );

											// Vehicles booked
											$vehicles_booked = ovabrw_array_merge_unique( $vehicles_booked, $item_vehicles );
										}
	            					}
	        					} else {
	        						if ( !( $pickup_date >= $dropoff_real || $dropoff_date <= $pickup_real ) ) {
	            						// Convert string to array
										$item_vehicles = explode( ',', $item_vehicles );
										if ( ovabrw_array_exists( $item_vehicles ) ) {
											$item_vehicles = array_map( 'trim', $item_vehicles );

											// Vehicles booked
											$vehicles_booked = ovabrw_array_merge_unique( $vehicles_booked, $item_vehicles );
										}
	            					}
	        					}
		    				}
		    			} // END loop order items
		    		} // END loop order ids
		    	}
		    } // END if

	    	return apply_filters( $this->prefix.'get_vehicles_booked_from_order', $vehicles_booked, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get reserved items
	     */
	    public function get_reserved_items( $pickup_date, $dropoff_date ) {
	    	// init
	    	$reserved_items = 0;

	    	// Check pick-up & drop-off dates exists
	    	if ( !$pickup_date || !$dropoff_date ) return $reserved_items;

        	// Booked validation - Frontend
        	if ( !apply_filters( OVABRW_PREFIX.'booked_dates_validation', true ) ) return $reserved_items;

	    	// Manage stock enabaled
	    	if ( 'yes' !== get_option( 'woocommerce_manage_stock', 'yes' ) ) {
	    		return $reserved_items;
	    	}

	    	// WPDB
	    	global $wpdb;

	    	// Get draft order id
	    	$draft_order_id = OVABRW()->options->get_draft_order_id();

	    	// init
	    	$order_ids = [];

	    	// Get order ids
	    	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
            if ( OVABRW()->options->custom_orders_table_usage() ) {
                $order_ids = $wpdb->get_col( $wpdb->prepare( "
                    SELECT DISTINCT stock_table.order_id
                    FROM {$wpdb->prefix}wc_reserved_stock AS stock_table
                    LEFT JOIN {$wpdb->prefix}wc_orders AS orders
                        ON orders.id = stock_table.order_id
                    WHERE orders.status IN ( 'wc-checkout-draft', 'wc-pending' )
                        AND stock_table.expires > NOW()
                        AND stock_table.product_id = %d
                        AND stock_table.order_id != %d
                    ",
                    [
                    	$this->get_id(),
                        $draft_order_id
                    ]
                ));
            } else {
                $order_ids = $wpdb->get_col( $wpdb->prepare( "
                    SELECT DISTINCT stock_table.order_id
                    FROM {$wpdb->prefix}wc_reserved_stock AS stock_table
                    LEFT JOIN {$wpdb->prefix}posts AS posts
                        ON posts.ID = stock_table.order_id
                    WHERE posts.post_status IN ( 'wc-checkout-draft', 'wc-pending' )
                        AND stock_table.expires > NOW()
                        AND stock_table.product_id = %d
                        AND stock_table.order_id != %d
                    ",
                    [
                    	$this->get_id(),
                        $draft_order_id
                    ]
                ));
            }
            // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
            
            if ( ovabrw_array_exists( $order_ids ) ) {
            	// Get product ids multi language
	    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

	    		// Time between leases
	    		$time_between_leases = $this->get_time_between_leases();

            	foreach ( $order_ids as $order_id ) {
            		// Get order
            		$order = wc_get_order( $order_id );
            		if ( !$order ) continue;

            		// Get order items
            		$order_items = $order->get_items();
            		if ( !ovabrw_array_exists( $order_items ) ) continue;

            		foreach ( $order_items as $item_id => $item ) {
            			// Get product ID
            			$product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';
						if ( !$product_id || !in_array( $product_id, $product_ids ) ) continue;

						// Item pick-up date
        				$item_pickup = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );

        				// Pick-up date real
        				$pickup_real = strtotime( $item->get_meta( 'ovabrw_pickup_date_real' ) );
        				if ( !$item_pickup || !$pickup_real ) continue;

        				// Item drop-off date
    					$item_dropoff = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );

        				// Drop-off date real
    					$dropoff_real = strtotime( $item->get_meta( 'ovabrw_pickoff_date_real' ) );
    					if ( !$item_dropoff || !$dropoff_real || $dropoff_real < current_time( 'timestamp' ) ) continue;

    					// Time between leases
        				if ( $time_between_leases ) {
        					$dropoff_real += $time_between_leases;
        				}

        				// When Drop-off date hidden
						if ( $item_pickup === $item_dropoff ) {
							$dropoff_real -= 1;
							$dropoff_date += 1;
						}

        				// Booked qty
    					$booked_qty = absint( $item->get_meta( 'ovabrw_number_vehicle' ) );
    					if ( !$booked_qty ) continue;

    					// Check booking
    					if ( apply_filters( OVABRW_PREFIX.'check_equal_booking_times', false, $product_id ) ) {
    						if ( !( $pickup_date > $dropoff_real || $dropoff_date < $pickup_real ) ) {
        						$reserved_items += $booked_qty;
        					}
    					} else {
    						if ( !( $pickup_date >= $dropoff_real || $dropoff_date <= $pickup_real ) ) {
        						$reserved_items += $booked_qty;
        					}
    					}
            		} // END loop order items
            	} // END loop order ids
            } // END

            return apply_filters( $this->prefix.'get_reserved_items', $reserved_items, $pickup_date, $dropoff_date, $this );
	    }

	    /**
	     * Get price by weekday start
	     */
	    public function get_price_by_weekday_start( $weekstart, $numberof_rental_days, $daily_prices ) {
	    	if ( !$weekstart || !$numberof_rental_days || !ovabrw_array_exists( $daily_prices ) ) {
	    		return 0;
	    	}

	    	// init
	    	$total = $week_price = $rental_price = 0;

	    	// Day of week
	    	$dayof_week = $numberof_rental_days % 7;

	    	// Number of week
	    	$numberof_week = floor( $numberof_rental_days / 7 );

	    	// Daily prices
	    	$monday 	= (float)ovabrw_get_meta_data( 'monday', $daily_prices );
	    	$tuesday 	= (float)ovabrw_get_meta_data( 'tuesday', $daily_prices );
	    	$wednesday 	= (float)ovabrw_get_meta_data( 'wednesday', $daily_prices );
	    	$thursday 	= (float)ovabrw_get_meta_data( 'thursday', $daily_prices );
	    	$friday 	= (float)ovabrw_get_meta_data( 'friday', $daily_prices );
	    	$saturday 	= (float)ovabrw_get_meta_data( 'saturday', $daily_prices );
	    	$sunday 	= (float)ovabrw_get_meta_data( 'sunday', $daily_prices );

	    	// Week price ( > 7 days )
	    	if ( $numberof_week > 0 ) {
	    		$week_price = $numberof_week * ( $monday + $tuesday + $wednesday + $thursday + $friday + $saturday + $sunday );
	    	}

	    	// Day of week
	    	if ( $dayof_week > 0 ) {
	    		// Week start
		    	switch ( $weekstart ) {
		    		case 1: // Monday
	            		switch ( $dayof_week ) {
	                		case 1:
	                			$rental_price = $monday;
	                			break;
	                		case 2:
	                			$rental_price = $monday + $tuesday;
	                			break;
	                		case 3:
	                			$rental_price = $monday + $tuesday + $wednesday;
	                			break;
	                		case 4:
	                			$rental_price = $monday + $tuesday + $wednesday + $thursday;
	                			break;
	                		case 5:
	                			$rental_price = $monday + $tuesday + $wednesday + $thursday + $friday;
	                			break;
	                		case 6:
	                			$rental_price = $monday + $tuesday + $wednesday + $thursday + $friday + $saturday;
	                			break;
	                	}
		    			break;
		    		case 2: // Tuesday
	            		switch ( $dayof_week ) {
	                		case 1:
	                			$rental_price = $tuesday;
	                			break;
	                		case 2:
	                			$rental_price = $tuesday + $wednesday;
	                			break;
	                		case 3:
	                			$rental_price = $tuesday + $wednesday + $thursday;
	                			break;
	                		case 4:
	                			$rental_price = $tuesday + $wednesday + $thursday + $friday;
	                			break;
	                		case 5:
	                			$rental_price = $tuesday + $wednesday + $thursday + $friday + $saturday;
	                			break;
	                		case 6:
	                			$rental_price = $tuesday + $wednesday + $thursday + $friday + $saturday + $sunday;
	                			break;
	                	}
		    			break;
		    		case 3: // Wednesday
		    			switch ( $dayof_week ) {
	                		case 1:
	                			$rental_price = $wednesday;
	                			break;
	                		case 2:
	                			$rental_price = $wednesday + $thursday;
	                			break;
	                		case 3:
	                			$rental_price = $wednesday + $thursday + $friday;
	                			break;
	                		case 4:
	                			$rental_price = $wednesday + $thursday + $friday + $saturday;
	                			break;
	                		case 5:
	                			$rental_price = $wednesday + $thursday + $friday + $saturday + $sunday;
	                			break;
	                		case 6:
	                			$rental_price = $wednesday + $thursday + $friday + $saturday + $sunday + $monday;
	                			break;
	                	}
		    			break;
		    		case 4: // Thursday
		    			switch ( $dayof_week ) {
	                		case 1:
	                			$rental_price = $thursday;
	                			break;
	                		case 2:
	                			$rental_price = $thursday + $friday;
	                			break;
	                		case 3:
	                			$rental_price = $thursday + $friday + $saturday;
	                			break;
	                		case 4:
	                			$rental_price = $thursday + $friday + $saturday + $sunday;
	                			break;
	                		case 5:
	                			$rental_price = $thursday + $friday + $saturday + $sunday + $monday;
	                			break;
	                		case 6:
	                			$rental_price = $thursday + $friday + $saturday + $sunday + $monday + $tuesday;
	                			break;
	                	}
		    			break;
		    		case 5: // Friday
		    			switch ( $dayof_week ) {
	                		case 1:
	                			$rental_price = $friday;
	                			break;
	                		case 2:
	                			$rental_price = $friday + $saturday;
	                			break;
	                		case 3:
	                			$rental_price = $friday + $saturday + $sunday;
	                			break;
	                		case 4:
	                			$rental_price = $friday + $saturday + $sunday + $monday;
	                			break;
	                		case 5:
	                			$rental_price = $friday + $saturday + $sunday + $monday + $tuesday;
	                			break;
	                		case 6:
	                			$rental_price = $friday + $saturday + $sunday + $monday + $tuesday + $wednesday;
	                			break;
	                	}
		    			break;
		    		case 6: // Saturday
		    			switch ( $dayof_week ) {
	                		case 1:
	                			$rental_price = $saturday;
	                			break;
	                		case 2:
	                			$rental_price = $saturday + $sunday;
	                			break;
	                		case 3:
	                			$rental_price = $saturday + $sunday + $monday;
	                			break;
	                		case 4:
	                			$rental_price = $saturday + $sunday + $monday + $tuesday;
	                			break;
	                		case 5:
	                			$rental_price = $saturday + $sunday + $monday + $tuesday + $wednesday;
	                			break;
	                		case 6:
	                			$rental_price = $saturday + $sunday + $monday + $tuesday + $wednesday + $thursday;
	                			break;
	                	}
		    			break;
		    		case 7: // Sunday
		    			switch ( $dayof_week ) {
	                		case 1:
	                			$rental_price = $sunday;
	                			break;
	                		case 2:
	                			$rental_price = $sunday + $monday;
	                			break;
	                		case 3:
	                			$rental_price = $sunday + $monday + $tuesday;
	                			break;
	                		case 4:
	                			$rental_price = $sunday + $monday + $tuesday + $wednesday;
	                			break;
	                		case 5:
	                			$rental_price = $sunday + $monday + $tuesday + $wednesday + $thursday;
	                			break;
	                		case 6:
	                			$rental_price = $sunday + $monday + $tuesday + $wednesday + $thursday + $friday;
	                			break;
	                	}
		    			break;
		    		default:
		    			// code...
		    			break;
		    	}
	    	}

	    	// Update total
	    	$total = $week_price + $rental_price;

	    	return apply_filters( $this->prefix.'get_price_by_weekday_start', $total, $weekstart, $numberof_rental_days, $daily_prices );
	    }

	    /**
	     * Get location prices
	     */
	    public function get_location_prices( $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_location || !$dropoff_location ) return 0;

	    	// init
	    	$location_prices = 0;

	    	// Location prices
	    	$prices = $this->get_meta_value( 'st_price_location' );
	    	if ( ovabrw_array_exists( $prices ) ) {
	    		// Remove space
		    	$pickup_location 	= trim( $pickup_location );
		    	$dropoff_location 	= trim( $dropoff_location );

		    	// Pick-up locations
		    	$pickup = $this->get_meta_value( 'st_pickup_loc' );

		    	// Drop-off location
		    	$dropoff = $this->get_meta_value( 'st_dropoff_loc' );

	    		foreach ( $prices as $i => $price ) {
		    		$price = (float)$price;
		    		if ( !$price ) continue;

		    		// Pick-up location
		    		$pickup_loc = ovabrw_get_meta_data( $i, $pickup );
		    		if ( !$pickup_loc ) continue;
		    		$pickup_loc = trim( $pickup_loc );

		    		// Drop-off location
		    		$dropoff_loc = ovabrw_get_meta_data( $i, $dropoff );
		    		if ( !$dropoff_loc ) continue;
		    		$dropoff_loc = trim( $dropoff_loc );

		    		if ( $pickup_location === $pickup_loc && $dropoff_location === $dropoff_loc ) {
		    			$location_prices = $price;
		    			break;
		    		}
		    	} // END foreach
	    	} // END if

	    	return apply_filters( $this->prefix.'get_location_prices', $location_prices, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get location surcharge
	     * @param 	string 			$pickup_location
	     * @param 	string 			$dropoff_location
	     * @return 	array|boolean 	$location_surcharge
	     */
	    public function get_location_surcharge( $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_location && !$dropoff_location ) return false;

	    	// Check rental type
	    	if ( !in_array( $this->get_type(), apply_filters( $this->prefix.'location_surcharge_for_rental', [ 'day', 'hour', 'mixed', 'period_time' ] ) ) ) {
	    		return false;
	    	}

	    	// init
	    	$location_surcharge = [];

	    	// Get calculate location surcharge
	    	$cal_location_surcharge = $this->get_meta_value( 'cal_location_surcharge', 'no' );

	    	// Pick-up location surcharge
	    	$pickup_location_surcharge = $this->get_meta_value( 'pickup_location_surcharge', [] );
	    	if ( $pickup_location && ovabrw_array_exists( $pickup_location_surcharge ) ) {
		    	// Get pick-up location prices
		    	$pickup_prices = $this->get_meta_value( 'pickup_surcharge_price', [] );

		    	// Get index
	    		$index_pickup = array_search( $pickup_location, $pickup_location_surcharge );
	    		if ( false !== $index_pickup ) {	    			
	    			$location_surcharge['pickup_location'] = (float)ovabrw_get_meta_data( $index_pickup, $pickup_prices );
	    		}
	    	}

	    	// Drop-off location surcharge
	    	$dropoff_location_surcharge = $this->get_meta_value( 'dropoff_location_surcharge', [] );
	    	if ( $dropoff_location && ovabrw_array_exists( $dropoff_location_surcharge ) ) {
	    		// Charge only once if the pick-up location and drop-off location are the same
	    		if ( $pickup_location && $pickup_location === $dropoff_location && 'yes' === $cal_location_surcharge ) {
	    			return apply_filters( $this->prefix.'get_location_surcharge', $location_surcharge, $pickup_location, $dropoff_location, $this );
	    		} else {
			    	// Get drop-off location prices
			    	$dropoff_prices = $this->get_meta_value( 'dropoff_surcharge_price', [] );

			    	// Get index
		    		$index_dropoff = array_search( $dropoff_location, $dropoff_location_surcharge );
		    		if ( false !== $index_dropoff ) {
		    			$location_surcharge['dropoff_location'] = (float)ovabrw_get_meta_data( $index_dropoff, $dropoff_prices );
		    		}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_location_surcharge', $location_surcharge, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get custom checkout fields prices
	     */
	    public function get_cckf_prices( $cckf, $cckf_qty = [] ) {
	    	if ( !ovabrw_array_exists( $cckf ) ) return 0;

	    	// CCKF prices
	    	$cckf_prices = 0;

	    	// Product custom checkout fields
	    	$product_cckf = $this->product->get_cckf();

	    	// Loop
	    	foreach ( $cckf as $k => $val ) {
	    		if ( ovabrw_get_meta_data( $k, $product_cckf ) ) {
	    			// Fields
	    			$fields = $product_cckf[$k];

	    			// Get type
	    			$type = ovabrw_get_meta_data( 'type', $fields );
	    			if ( !$type || !in_array( $type, [ 'radio', 'checkbox', 'select' ] ) ) {
	    				continue;
	    			}

	    			if ( 'radio' === $type ) {
	    				// Option values
	    				$opt_values = ovabrw_get_meta_data( 'ova_values', $fields );

	    				// Option prices
	    				$opt_prices = ovabrw_get_meta_data( 'ova_prices', $fields );

	    				if ( ovabrw_array_exists( $opt_values ) ) {
	    					foreach ( $opt_values as $i => $opt_val ) {
	    						if ( $val === $opt_val ) {
	    							// Get quantity
	    							$qty = (int)ovabrw_get_meta_data( $k, $cckf_qty, 1 );

	    							// Get price
	    							$price = (float)ovabrw_get_meta_data( $i, $opt_prices );

	    							$cckf_prices += ( $price * $qty );
	    						}
	    					}
	    				}
	    			} elseif ( 'checkbox' === $type && ovabrw_array_exists( $val ) ) {
	    				// Option keys
	    				$opt_keys = ovabrw_get_meta_data( 'ova_checkbox_key', $fields );

	    				// Option prices
	    				$opt_prices = ovabrw_get_meta_data( 'ova_checkbox_price', $fields );

	    				foreach ( $val as $opt_val ) {
	    					// Get index option
	    					$index = array_search( $opt_val, $opt_keys );

	    					if ( !is_bool( $index ) ) {
	    						// Get quantity
	    						$qty = (int)ovabrw_get_meta_data( $opt_val, $cckf_qty, 1 );

	    						// Get price
	    						$price = (float)ovabrw_get_meta_data( $index, $opt_prices );

	    						$cckf_prices += ( $price * $qty );
	    					}
	    				}
	    			} elseif ( 'select' === $type ) {
	    				// Option keys
	    				$opt_keys = ovabrw_get_meta_data( 'ova_options_key', $fields );

	    				// Option prices
	    				$opt_prices = ovabrw_get_meta_data( 'ova_options_price', $fields );

	    				if ( ovabrw_array_exists( $opt_keys ) ) {
	    					foreach ( $opt_keys as $i => $opt_val ) {
	    						if ( $val === $opt_val ) {
	    							// Get quantity
	    							$qty = (int)ovabrw_get_meta_data( $opt_val, $cckf_qty, 1 );

	    							// Get price
	    							$price = (float)ovabrw_get_meta_data( $i, $opt_prices );

	    							$cckf_prices += ( $price * $qty );
	    						}
	    					}
	    				}
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_cckf_prices', $cckf_prices, $cckf, $cckf_qty, $this );
	    }

	    /**
	     * Get resource prices
	     */
	    public function get_resource_prices( $pickup_date, $dropoff_date, $resources, $resources_qty = [] ) {
	    	if ( !$pickup_date || !$dropoff_date || !ovabrw_array_exists( $resources ) ) {
	    		return 0;
	    	}

	    	// init
	    	$resource_prices = 0;

	    	// Get resource ids
	    	$resc_ids = $this->get_meta_value( 'resource_id' );

	    	if ( ovabrw_array_exists( $resc_ids ) ) {
	    		// Rental time
	    		$rental_time = $this->get_rental_time_between( $pickup_date, $dropoff_date );

	    		// Number of rental days
	    		$numberof_rental_days = (int)ovabrw_get_meta_data( 'numberof_rental_days', $rental_time );

	    		// Number of rental hours
	    		$numberof_rental_hours = (int)ovabrw_get_meta_data( 'numberof_rental_hours', $rental_time );

	    		// Get resource prices
	    		$resc_prices = $this->get_meta_value( 'resource_price' );

	    		// Get resource types
	    		$resc_types = $this->get_meta_value( 'resource_duration_type' );

	    		foreach ( array_keys( $resources ) as $opt_id ) {
	    			// Get index
	    			$index = array_search( $opt_id, $resc_ids );

	    			if ( is_bool( $index ) ) continue;

	    			// Get resource quantity
	    			$qty = (int)ovabrw_get_meta_data( $opt_id, $resources_qty, 1 );

	    			// Get resource price
	    			$price = (float)ovabrw_get_meta_data( $index, $resc_prices );

	    			// Get resource type
	    			$type = ovabrw_get_meta_data( $index, $resc_types );

	    			if ( 'total' === $type ) {
	    				$resource_prices += ( $price * $qty );
	    			} elseif ( 'days' === $type ) {
	    				$resource_prices += ( $price * $numberof_rental_days * $qty );
	    			} elseif ( 'hours' === $type ) {
	    				$resource_prices += ( $price * $numberof_rental_hours * $qty );
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_resource_prices', $resource_prices, $pickup_date, $dropoff_date, $resources, $resources_qty, $this );
	    }

	    /**
	     * Get service prices
	     */
	    public function get_service_prices( $pickup_date, $dropoff_date, $services, $services_qty = [] ) {
	    	if ( !$pickup_date || !$dropoff_date || !ovabrw_array_exists( $services ) ) {
	    		return 0;
	    	}

	    	// init
	    	$service_prices = 0;

	    	// Get service ids
	    	$serv_ids = $this->get_meta_value( 'service_id' );

	    	if ( ovabrw_array_exists( $serv_ids ) ) {
	    		// Rental time
	    		$rental_time = $this->get_rental_time_between( $pickup_date, $dropoff_date );

	    		// Number of rental days
	    		$numberof_rental_days = (int)ovabrw_get_meta_data( 'numberof_rental_days', $rental_time );

	    		// Number of rental hours
	    		$numberof_rental_hours = (int)ovabrw_get_meta_data( 'numberof_rental_hours', $rental_time );

	    		// Get service prices
	    		$serv_prices = $this->get_meta_value( 'service_price' );

	    		// Get service types
	    		$serv_types = $this->get_meta_value( 'service_duration_type' );

	    		foreach ( $services as $opt_id ) {
	    			foreach ( $serv_ids as $i => $ids ) {
	    				// Get index
	    				$index = array_search( $opt_id, $ids );
	    				if ( is_bool( $index ) ) continue;

	    				// Get quantity
	    				$qty = (int)ovabrw_get_meta_data( $opt_id, $services_qty, 1 );

	    				// Get price
	    				$price = isset( $serv_prices[$i][$index] ) ? (float)$serv_prices[$i][$index] : 0;

	    				// Get type
	    				$type = isset( $serv_types[$i][$index] ) ? $serv_types[$i][$index] : '';

	    				if ( 'total' === $type ) {
	    					$service_prices += ( $price * $qty );
	    				} elseif ( 'days' === $type ) {
	    					$service_prices += ( $price * $numberof_rental_days * $qty );
	    				} elseif ( 'hours' === $type ) {
	    					$service_prices += ( $price * $numberof_rental_hours * $qty );
	    				}
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_service_prices', $service_prices, $pickup_date, $dropoff_date, $services, $services_qty, $this );
	    }

	    /**
	     * Add to cart validation
	     */
	    public function add_to_cart_validation( $passed, $product_id, $quantity ) {
	    	// Pick-up location
	    	$pickup_location = trim( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_location', $_REQUEST ) ) );
	    	if ( !$pickup_location ) {
	    		$pickup_location = trim( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_location', $_REQUEST ) ) );
	    	}

	    	// Drop-off location
	    	$dropoff_location = trim( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_dropoff_location', $_REQUEST ) ) );

	    	// Pick-up date
	    	$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $_REQUEST ) );

	    	// Start time
	    	$start_time = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_start_time', $_REQUEST ) );
	    	if ( $start_time ) {
	    		$pickup_date = strtotime( $pickup_date . ' ' . $start_time );
	    	} else {
	    		$pickup_date = strtotime( $pickup_date );
	    	}

	    	// Drop-off date
	    	$dropoff_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_dropoff_date', $_REQUEST ) ) );

	    	// Package id
	    	$package_id = trim( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_package_id', $_REQUEST ) ) );

	    	// Duration
	    	$duration = (int)ovabrw_get_meta_data( 'ovabrw_duration', $_REQUEST );

	    	// Number of adults
	    	$numberof_adults = (int)ovabrw_get_meta_data( 'ovabrw_adults', $_REQUEST );

	    	// Number of children
	    	$numberof_children = (int)ovabrw_get_meta_data( 'ovabrw_children', $_REQUEST );

	    	// Number of babies
	    	$numberof_babies = (int)ovabrw_get_meta_data( 'ovabrw_babies', $_REQUEST );

	    	// Post data
			$post_data = apply_filters( OVABRW_PREFIX.'post_data_add_to_cart_validation', [
				'pickup_location' 	=> $pickup_location,
				'dropoff_location' 	=> $dropoff_location,
				'pickup_date' 		=> $pickup_date,
				'dropoff_date' 		=> $dropoff_date,
				'package_id' 		=> $package_id,
				'duration' 			=> $duration,
				'adults' 			=> $numberof_adults,
				'children' 			=> $numberof_children,
				'babies' 			=> $numberof_babies
			], $_REQUEST );

	    	// Get new date
	    	$new_date = $this->get_new_date( $post_data );

	    	// New pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );

			// New drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );

	    	// Booking validation
			$booking_validation = $this->booking_validation( $pickup_date, $dropoff_date, $post_data );
			if ( $booking_validation ) {
				wc_clear_notices();
                wc_add_notice( $booking_validation, 'error' );
                return false;
			}

			// Get guest options
			$guest_options = $this->get_guest_options();
			if ( ovabrw_array_exists( $guest_options ) ) {
				// init
				$guests = [];

				// Number of guests
				$numberof_guests = 0;

				foreach ( $guest_options as $guest ) {
					$number = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guest['name'], $_REQUEST );
					$guests['numberof_'.$guest['name']] = $number;

					$numberof_guests += $number;
				}

				if ( $numberof_guests ) {
					$guests['numberof_guests'] = $numberof_guests;

					// Number of guests validation
					$mesg = $this->numberof_guests_validation( $guests );
					if ( $mesg && $mesg !== true ) {
						wc_clear_notices();
						wc_add_notice( $mesg, 'error' );
						return false;
					}
				}
			}

			// Quantity
	    	$input_quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $_REQUEST, 1 );
	    	if ( 1 > $input_quantity ) {
	    		wc_clear_notices();
                wc_add_notice( esc_html__( 'Quantity must be greater than 0', 'ova-brw' ), 'error' );
                return false;
	    	}

	    	// Get items available
	    	$items_available = $this->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'cart' );

	    	// Add vehicles available
	    	if ( is_array( $items_available ) ) {
	    		WC()->session->set( 'vehicles_available', implode( ', ', array_slice( $items_available, 0, $input_quantity ) ) );

	    		$items_available = count( $items_available );
	    	}

	    	// Check items available
	    	if ( $input_quantity > $items_available ) {
	    		wc_clear_notices();

	    		if ( $items_available > 0 ) {
	    			wc_add_notice( sprintf( esc_html__( 'Items available is %s', 'ova-brw'  ), $items_available ), 'error' );
	    		} else {
	    			wc_add_notice( esc_html__( 'No items are available. Please choose another time.', 'ova-brw'  ), 'error' );
	    		}

	    		return false;
	    	} // END if

	    	return apply_filters( $this->prefix.'add_to_cart_validation', $passed, $product_id, $quantity, $this );
	    }

	    /**
		 * Add cart item data
		 */
		public function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
			// Get cart item data
			$cart_item_data = $this->add_rental_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity );

			// Guest options
			$guest_options = $this->get_guest_options();
			foreach ( $guest_options as $guest ) {
				$numberof_guests = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guest['name'], $_REQUEST );

				// Guest data
				$cart_item_data['numberof_'.$guest['name']] = $numberof_guests;
			}

			// Guest information
			$guest_info = ovabrw_get_meta_data( 'ovabrw_guest_info', $_POST );
			if ( ovabrw_array_exists( $guest_info ) ) {
				$cart_item_data['ovabrw_guest_info'] = $guest_info;
			}

    		// Quantity
    		if ( !ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item_data ) ) {
    			$input_quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $_REQUEST, 1 );
		    	$cart_item_data['ovabrw_quantity'] = $input_quantity;
    		}

	    	// Vehicles available
	    	if ( WC()->session->__isset( 'vehicles_available' ) && 'id_vehicle' === $this->product->get_manage_store() ) {
	            $cart_item_data['vehicles_available'] = WC()->session->get( 'vehicles_available' );

	            // Remove vehicles available session
	            WC()->session->__unset( 'vehicles_available' );
	        }

			// Custom checkout fields
			$cckf = $cckf_qty = [];

			// Get product cckf
	    	$product_cckf = $this->product->get_cckf();
	    	if ( ovabrw_array_exists( $product_cckf ) ) {
	    		// Loop
	    		foreach ( $product_cckf as $name => $fields ) {
	    			if ( 'on' !== ovabrw_get_meta_data( 'enabled', $fields ) ) continue;

	    			// Get type
	    			$type = ovabrw_get_meta_data( 'type', $fields );

	    			if ( 'file' === $type ) {
	    				// Get file
	    				$files = ovabrw_get_meta_data( $name, $_FILES );

    					// File name
    					$file_name = ovabrw_get_meta_data( 'name', $files );
    					if ( $file_name ) {
    						// File size
    						$file_size = (int)ovabrw_get_meta_data( 'size', $files );
    						$file_size = $file_size / 1048576;

    						// Max size
    						$max_size = (float)ovabrw_get_meta_data( 'max_file_size', $fields );
    						if ( $max_size < $file_size ) continue;

    						// File type
	                        $accept = apply_filters( OVABRW_PREFIX.'accept_file_upload', '.jpg, .jpeg, .png, .pdf, .doc, .docx', $this );

	                        // Get file extension
	                        $extension = pathinfo( $file_name, PATHINFO_EXTENSION );
	                        if ( strpos( $accept, $extension ) === false ) continue;

	                         // Upload file
	                        $overrides = [ 'test_form' => false ];

	                        if ( ! function_exists( 'wp_handle_upload' ) ) {
								require_once( ABSPATH . 'wp-admin/includes/file.php' );
							}

							// Upload
							$upload = wp_handle_upload( $files, $overrides );
							if ( ovabrw_get_meta_data( 'error', $upload ) ) continue;

	                        // File url
	                        $file_url = '<a href="'.esc_url( $upload['url'] ).'" target="_blank">';
								$file_url .= basename( $upload['file'] );
							$file_url .= '</a>';

							// Add cart item data
							$cart_item_data[$name] = $file_url;
    					}
	    			} elseif ( 'checkbox' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options values
	    				$opt_values = ovabrw_get_meta_data( $name, $_REQUEST );

	    				if ( ovabrw_array_exists( $opt_values ) ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_values;

	    					// Option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $_REQUEST );
	    					if ( ovabrw_array_exists( $opt_qtys ) ) {
	    						$cckf_qty = array_merge( $cckf_qty, $opt_qtys );
	    					}

	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_checkbox_key', $fields, [] );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_checkbox_text', $fields, [] );

	    					// Loop
	    					foreach ( $opt_values as $val ) {
	    						// Get index option
	    						$index = array_search( $val, $opt_keys );
	    						if ( is_bool( $index ) ) continue;

	    						// Option text
	    						$opt_text = ovabrw_get_meta_data( $index, $opt_texts );

	    						// Option qty
	    						$opt_qty = (int)ovabrw_get_meta_data( $val, $cckf_qty );

	    						if ( $opt_qty ) {
	    							array_push( $opt_names, sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_text, $opt_qty ) );
	    						} else {
	    							array_push( $opt_names, $opt_text );
	    						}
	    					} // END loop
	    				}

	    				// Add cart item data
	    				if ( ovabrw_array_exists( $opt_names ) ) {
	    					$cart_item_data[$name] = implode( ', ', $opt_names );
	    				}
	    			} elseif ( 'radio' === $type ) {
	    				// Get option value
	    				$opt_value = ovabrw_get_meta_data( $name, $_REQUEST );
	    				if ( $opt_value ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_value;

	    					// Get option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $_REQUEST, [] );

	    					// Option qty
	    					$opt_qty = (int)ovabrw_get_meta_data( $opt_value, $opt_qtys );

	    					// Add cart item data
	    					if ( $opt_qty ) {
	    						// Add cckf quantity
	    						$cckf_qty[$name] = $opt_qty;
	    						$cart_item_data[$name] = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_value, $opt_qty );
	    					} else {
	    						$cart_item_data[$name] = $opt_value;
	    					}
	    				}
	    			} elseif ( 'select' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options value
	    				$opt_value = ovabrw_get_meta_data( $name, $_REQUEST );
	    				if ( $opt_value ) {
	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_options_key', $fields );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_options_text', $fields );

	    					// Option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $_REQUEST );
	    					
	    					// Option quantity
	    					$opt_qty = (int)ovabrw_get_meta_data( $opt_value, $opt_qtys );

	    					// index option
	    					$index = array_search( $opt_value, $opt_keys );
	    					if ( is_bool( $index ) ) continue;

	    					// Add cckf
	    					$cckf[$name] = $opt_value;

	    					// Option text
	    					$opt_text = ovabrw_get_meta_data( $index, $opt_texts );

	    					// Add cart item data
	    					if ( $opt_qty ) {
	    						// Add cckf quantity
	    						$cckf_qty[$opt_value] 	= $opt_qty;
	    						$cart_item_data[$name] 	= sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_text, $opt_qty );
	    					} else {
	    						$cart_item_data[$name] = $opt_text;
	    					}
	    				}
	    			} else {
	    				// Option value
	    				$opt_value = ovabrw_get_meta_data( $name, $_REQUEST );

	    				if ( $opt_value ) {
	    					// Add cart item data
	    					$cart_item_data[$name] = $opt_value;
	    				}
	    			}
	    		} // END loop
	    	}

	    	// Add cckf to cart item data
	    	if ( ovabrw_array_exists( $cckf ) ) {
	    		$cart_item_data['cckf'] 	= $cckf;
	    		$cart_item_data['cckf_qty'] = $cckf_qty;
	    	}

	    	// Resources
	    	$resources 		= ovabrw_get_meta_data( 'ovabrw_resource_checkboxs', $_REQUEST );
	    	$resources_qty 	= ovabrw_get_meta_data( 'ovabrw_resource_quantity', $_REQUEST );
	    	if ( ovabrw_array_exists( $resources ) ) {
	    		$cart_item_data['resources'] 		= $resources;
	    		$cart_item_data['resources_qty'] 	= $resources_qty;
	    	}

	    	// Services
	    	$services 		= ovabrw_get_meta_data( 'ovabrw_service', $_REQUEST );
	    	$services_qty 	= ovabrw_get_meta_data( 'ovabrw_service_qty', $_REQUEST );
	    	if ( ovabrw_array_exists( $services ) ) {
	    		$serv_opts = $serv_qtys = [];

	    		foreach ( $services as $opt_id ) {
	    			if ( $opt_id ) {
	    				$serv_opts[] = $opt_id;

	    				// Service qty
	    				$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $services_qty );
	    				if ( $opt_qty ) {
	    					$serv_qtys[$opt_id] = $opt_qty;
	    				}
	    			}
	    		}

	    		// Add services to cart item data
	    		if ( ovabrw_array_exists( $serv_opts ) ) {
	    			$cart_item_data['services'] 	= $serv_opts;
		    		$cart_item_data['services_qty'] = $serv_qtys;
	    		}
	    	}

	    	// Insurance amount
    		$insurance = (float)$this->get_meta_value( 'amount_insurance' ) * $quantity;

	    	// Deposit
	    	$deposit = ovabrw_get_meta_data( 'ovabrw_type_deposit', $_REQUEST );
	    	if ( 'deposit' === $deposit ) {
	    		$cart_item_data['is_deposit'] = true;
	    	}

			return apply_filters( $this->prefix.'add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity, $this );
		}

		/**
		 * Add rental cart item data
		 */
		public function add_rental_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
			return apply_filters( $this->prefix.'add_rental_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity, $this );
		}

		/**
		 * Get cart item data
		 */
		public function get_cart_item_data( $item_data, $cart_item ) {
			// Get item data
			$item_data = $this->get_rental_cart_item_data( $item_data, $cart_item );

			// Guest options
			$guest_options = $cart_item['data']->get_guests();

			// Guest information
			$guest_info = ovabrw_get_meta_data( 'ovabrw_guest_info', $cart_item );
			if ( apply_filters( OVABRW_PREFIX.'view_guest_info_in_cart', true ) && ovabrw_array_exists( $guest_info ) ) {
				$guest_info = OVABRW()->options->get_guest_info_html( $guest_info );
			}

			// Add number of guests
			foreach ( $guest_options as $guest ) {
				// Get number of guest
				$numberof_guest = (int)ovabrw_get_meta_data( 'numberof_'.$guest['name'], $cart_item );

				if ( $numberof_guest || apply_filters( OVABRW_PREFIX.'numberof_guests_is_zero', false ) ) {
					// Get info
					if ( ovabrw_get_meta_data( $guest['name'], $guest_info ) ) {
						if ( OVABRW()->options->is_cart_shortcode() || OVABRW()->options->is_checkout_shortcode() ) {
							$numberof_guest .= ovabrw_get_meta_data( $guest['name'], $guest_info );
						}
					}

					$item_data[] = [
						'key' 	=> $guest['label'],
						'value' => wp_kses_post( $numberof_guest )
					];
				}
			}

			// Quantity
			$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item, 1 );
			if ( $quantity ) {
				$item_data[] = [
					'key'     => esc_html__( 'Quantity', 'ova-brw' ),
		            'value'   => wc_clean( $quantity ),
		            'display' => wc_clean( $quantity ),
		            'hidden'  => !$this->product->show_quantity() ? true : false
				];
			}

			// Vehicles available
			$vehicles_available = ovabrw_get_meta_data( 'vehicles_available', $cart_item );
			if ( $vehicles_available ) {
				$item_data[] = [
					'key'     => esc_html__( 'Vehicle ID(s)', 'ova-brw' ),
		            'value'   => wc_clean( $vehicles_available ),
		            'display' => wc_clean( $vehicles_available ),
		            'hidden'  => !$this->product->show_quantity() ? true : false
				];
			}

			// Custom checkout fields
			$product_cckf = $this->product->get_cckf();
			if ( ovabrw_array_exists( $product_cckf ) ) {
				foreach ( $product_cckf as $name => $fields ) {
					if ( 'on' !== ovabrw_get_meta_data( 'enabled', $fields ) ) continue;

					// Get value
					$value = ovabrw_get_meta_data( $name, $cart_item );
					if ( $value ) {
						$type 	= ovabrw_get_meta_data( 'type', $fields );
						$label 	= ovabrw_get_meta_data( 'label', $fields );

						if ( 'file' === $type ) { // For input type = file
							$item_data[] = [
								'key'     => $label,
	                            'value'   => wp_kses_post( $value ),
	                            'display' => wp_kses_post( $value )
							];
						} elseif ( 'price' === $type ) { // For input type = price
							$item_data[] = [
								'key'     => $label,
	                            'value'   => wc_clean( $value ),
	                            'display' => ovabrw_wc_price( $value )
							];
						} else {
							$item_data[] = [
								'key'     => $label,
	                            'value'   => wc_clean( $value ),
	                            'display' => wc_clean( $value )
							];
						}
					}
				}
			}

			// Resources
			$resources = ovabrw_get_meta_data( 'resources', $cart_item );
			if ( ovabrw_array_exists( $resources ) ) {
				// Resources quantity
				$resources_qty = ovabrw_get_meta_data( 'resources_qty', $cart_item );

				// Get resource ids
				$resc_ids = $this->get_meta_value( 'resource_id', [] );

				// Get resource name
				$resc_names = $this->get_meta_value( 'resource_name', [] );

				// init resource values
				$resc_values = [];

				// Loop
				foreach ( $resources as $opt_id ) {
					// index option
					$index = array_search( $opt_id, $resc_ids );
					if ( is_bool( $index ) ) continue;

					// Option name
					$opt_name = ovabrw_get_meta_data( $index, $resc_names );
					if ( !$opt_name ) continue;

					// Option quantity
					$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $resources_qty );

					if ( $opt_qty ) {
						$resc_values[] = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_name, $opt_qty );
					} else {
						$resc_values[] = $opt_name;
					}
				} // END loop

				// Add item data
				if ( ovabrw_array_exists( $resc_values ) ) {
					$item_data[] = [
						'key'     => sprintf( _n( 'Resource%s', 'Resources%s', count( $resc_values ), 'ova-brw' ), '' ),
                        'value'   => wc_clean( implode( ', ', $resc_values ) ),
                        'display' => wc_clean( implode( ', ', $resc_values ) )
					];
				}
			}

			// Services
			$services = ovabrw_get_meta_data( 'services', $cart_item );
			if ( ovabrw_array_exists( $services ) ) {
				// Services quantity
				$serv_qtys = ovabrw_get_meta_data( 'services_qty', $cart_item );

				// Service labels
				$serv_labels = $this->get_meta_value( 'label_service' );

				// Service ids
				$serv_ids = $this->get_meta_value( 'service_id' );

				// Service names
				$serv_names = $this->get_meta_value( 'service_name' );

				// Loop
				foreach ( $services as $opt_id ) {
					// Option quantity
					$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $serv_qtys );

					if ( ovabrw_array_exists( $serv_ids ) ) {
						foreach ( $serv_ids as $i => $opt_ids ) {
							// Option index
							$index = array_search( $opt_id, $opt_ids );
							if ( is_bool( $index ) ) continue;

							// Service label
							$label = ovabrw_get_meta_data( $i, $serv_labels );
							if ( !$label ) continue;

							// Option name
							$opt_name = isset( $serv_names[$i][$index] ) ? $serv_names[$i][$index] : '';
							if ( !$opt_name ) continue;

							// Add item data
							if ( $opt_qty ) {
								$item_data[] = [
									'key'     => $label,
                                    'value'   => wc_clean( $opt_name ),
                                    'display' => sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_name, $opt_qty )
								];
							} else {
								$item_data[] = [
									'key'     => $label,
                                    'value'   => wc_clean( $opt_name ),
                                    'display' => wc_clean( $opt_name )
								];
							}
						} // END loop
					}
				} // END loop
			}

			return apply_filters( $this->prefix.'get_cart_item_data', $item_data, $cart_item, $this );
		}

		/**
		 * Get rental cart item data
		 */
		public function get_rental_cart_item_data( $item_data, $cart_item ) {
			return apply_filters( $this->prefix.'get_rental_cart_item_data', $item_data, $cart_item, $this );
		}

		/**
		 * Get cart item price
		 */
		public function get_cart_item_price( $product_price, $cart_item, $cart_item_key ) {
			// Price real
			$price_real = ovabrw_get_meta_data( 'price_real', $cart_item );
			if ( $price_real ) {
				return apply_filters( $this->prefix.'get_cart_item_price', $price_real, $cart_item, $cart_item_key, $this );
			}

			return $product_price;
		}

		/**
		 * Get cart item quantity
		 */
		public function get_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
			// Quantity reald
			$quantity_real = ovabrw_get_meta_data( 'quantity_real', $cart_item, 1 );

			// Number of vehicles
			$numberof_vehicles = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item );
			if ( !$numberof_vehicles ) {
				$numberof_vehicles = (int)ovabrw_get_meta_data( 'quantity', $cart_item, 1 );
			}

			if ( $quantity_real ) {
				if ( $numberof_vehicles > 1 ) {
					$product_quantity = sprintf( esc_html__( '%s x %s', 'ova-brw' ), $numberof_vehicles, $quantity_real );
				} else {
					$product_quantity = $quantity_real;
				}
			} else {
				$product_quantity = sprintf( '%s', $numberof_vehicles );
			}

			return apply_filters( $this->prefix.'get_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item, $this );
		}

		/**
		 * Get checkout cart item quantity
		 */
		public function get_checkout_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
			$quantity = (int)ovabrw_get_meta_data( 'quantity', $cart_item, 1 );

			if ( $quantity && $quantity > 1 ) {
				$product_quantity = sprintf( 'x %s', $quantity );
			} else {
				$product_quantity = '';
			}

			return apply_filters( $this->prefix.'get_checkout_cart_item_quantity', $product_quantity, $cart_item, $cart_item_key );
		}

		/**
		 * Order item quantity HTML
		 */
		public function get_order_item_quantity_html( $item_quantity, $item ) {
			// Get total days
			$total_days = $item->get_meta( 'ovabrw_total_days' );
			if ( $total_days ) {
				$item_quantity = ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', esc_html( $total_days ) ) . '</strong>';
			}

			return apply_filters( $this->prefix.'get_order_item_quantity_html', $item_quantity, $item );
		}

		/**
		 * Save order line item
		 */
		public function save_order_line_item( $item, $values ) {
			// Rental type
			$rental_type = ovabrw_get_meta_data( 'rental_type', $values );
			if ( $rental_type ) {
				$item->add_meta_data( 'rental_type', $rental_type, true );
			}

			// Charged by
			$charged_by = ovabrw_get_meta_data( 'charged_by', $values );
			if ( $charged_by ) {
				$item->add_meta_data( 'define_day', $charged_by, true );
			}
			
			// Pick-up location
			$pickup_location = ovabrw_get_meta_data( 'pickup_location', $values );
			if ( $pickup_location ) {
				// For rental type: Appointment
				if ( 'appointment' === $this->get_type() ) {
					$item->add_meta_data( 'ovabrw_location', $pickup_location, true );
				} else {
					$item->add_meta_data( 'ovabrw_pickup_loc', $pickup_location, true );
				}
			}

			// Waypoints
			$waypoints = ovabrw_get_meta_data( 'waypoints', $values );
			if ( ovabrw_array_exists( $waypoints ) ) {
				foreach ( $waypoints as $i => $address ) {
					$item->add_meta_data( sprintf( esc_html__( 'Waypoint %s', 'ova-brw' ), $i + 1 ), $address, true );
				}
			}

			// Drop-off location
			$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $values );
			if ( $dropoff_location ) {
				$item->add_meta_data( 'ovabrw_pickoff_loc', $dropoff_location, true );
			}

			// Location prices
			$location_prices = (float)ovabrw_get_meta_data( 'location_prices', $values );
			if ( $location_prices ) {
				$item->add_meta_data( 'ovabrw_location_prices', $location_prices, true );
			}

			// Pick-up location surcharge
			$pickup_location_surcharge = (float)ovabrw_get_meta_data( 'pickup_location_surcharge', $values );
			if ( $pickup_location_surcharge ) {
				$item->add_meta_data( 'ovabrw_pickup_location_surcharge', $pickup_location_surcharge, true );
			}

			// Drop-off location surcharge
			$dropoff_location_surcharge = (float)ovabrw_get_meta_data( 'dropoff_location_surcharge', $values );
			if ( $dropoff_location_surcharge ) {
				$item->add_meta_data( 'ovabrw_dropoff_location_surcharge', $dropoff_location_surcharge, true );
			}

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $values );
			if ( $pickup_date ) {
				$item->add_meta_data( 'ovabrw_pickup_date', $pickup_date, true );
				$item->add_meta_data( 'ovabrw_pickup_date_strtotime', strtotime( $pickup_date ), true );
			}

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $values );
			if ( $dropoff_date ) {
				$item->add_meta_data( 'ovabrw_pickoff_date', $dropoff_date, true );
				$item->add_meta_data( 'ovabrw_pickoff_date_strtotime', strtotime( $dropoff_date ), true );
			}

			// Pick-up date real
			$pickup_real = ovabrw_get_meta_data( 'pickup_real', $values );
			if ( $pickup_real ) {
				$item->add_meta_data( 'ovabrw_pickup_date_real', $pickup_real, true );
			}

			// Drop-off date real
			$dropoff_real = ovabrw_get_meta_data( 'dropoff_real', $values );
			if ( $dropoff_real ) {
				$item->add_meta_data( 'ovabrw_pickoff_date_real', $dropoff_real, true );
			}

			// Number of adults
			$numberof_adults = (int)ovabrw_get_meta_data( 'numberof_adults', $values );
			if ( $numberof_adults ) {
				$item->add_meta_data( 'ovabrw_adults', $numberof_adults, true );
			}

			// Number of children
			$numberof_children = (int)ovabrw_get_meta_data( 'numberof_children', $values );
			if ( $numberof_children ) {
				$item->add_meta_data( 'ovabrw_children', $numberof_children, true );
			}

			// Number of babies
			$numberof_babies = (int)ovabrw_get_meta_data( 'numberof_babies', $values );
			if ( $numberof_babies ) {
				$item->add_meta_data( 'ovabrw_babies', $numberof_babies, true );
			}

			// Guest options
        	$guest_options = $this->get_guest_options();

        	// Loop guest options
        	foreach ( $guest_options as $guest ) {
        		// Number of guest
        		$numberof_guest = (int)ovabrw_get_meta_data( 'numberof_'.$guest['name'], $values );

        		if ( $numberof_guest || apply_filters( OVABRW_PREFIX.'numberof_guests_is_zero', false ) ) {
        			$item->add_meta_data( 'ovabrw_numberof_'.$guest['name'], $numberof_guest, true );
        		}
        	} // END

        	// Guest information
			$guest_info = ovabrw_get_meta_data( 'ovabrw_guest_info', $values );
			if ( ovabrw_array_exists( $guest_info ) ) {
        		$item->add_meta_data( 'ovabrw_guest_info', $guest_info, true );
        	}

			// Package id
			$package_id = ovabrw_get_meta_data( 'package_id', $values );
			if ( $package_id ) {
				$item->add_meta_data( 'package_id', $package_id, true );
			}

			// Package type
			$package_type = ovabrw_get_meta_data( 'package_type', $values );
			if ( $package_type ) {
				$item->add_meta_data( 'package_type', $package_type, true );
			}

			// Package label
			$package_label = ovabrw_get_meta_data( 'period_label', $values );
			if ( $package_label ) {
				$item->add_meta_data( 'period_label', $package_label, true );
			}

			// Distance
			$distance = (float)ovabrw_get_meta_data( 'distance', $values );
			if ( $distance ) {
				$item->add_meta_data( 'ovabrw_distance', $this->get_distance_unit( $distance ), true );
			}

			// Extra time
			$extra_time = ovabrw_get_meta_data( 'extra_time', $values );
			if ( $extra_time ) {
				$item->add_meta_data( 'ovabrw_extra_time', sprintf( esc_html__( '%s hour(s)', 'ova-brw' ), $extra_time ), true );
			}

			// Duration
			$duration = (int)ovabrw_get_meta_data( 'duration', $values );
			if ( $duration ) {
				$item->add_meta_data( 'ovabrw_duration', $this->get_duration_time( $duration ), true );
			}

			// Quantity
			$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $values, 1 );
			$item->add_meta_data( 'ovabrw_number_vehicle', $quantity, true );

			// Vehicle IDs
			$vehicles_available = ovabrw_get_meta_data( 'vehicles_available', $values );
			if ( $vehicles_available ) {
				$item->add_meta_data( 'id_vehicle', $vehicles_available, true );
			}

			// Quantity real
			$quantity_real = ovabrw_get_meta_data( 'quantity_real', $values );
			if ( $quantity_real ) {
				$item->add_meta_data( 'ovabrw_total_days', str_replace( '<br>', ', ', $quantity_real ), true );
			}

			// Price real
			$price_real = ovabrw_get_meta_data( 'price_real', $values );
			if ( $price_real ) {
				$item->add_meta_data( 'ovabrw_price_detail', str_replace( '<br>', ', ', $price_real ), true );
			}

			// Get product cckf
	    	$product_cckf = $this->product->get_cckf();
	    	if ( ovabrw_array_exists( $product_cckf ) ) {
	    		// Loop
	    		foreach ( $product_cckf as $name => $fields ) {
	    			if ( 'on' !== ovabrw_get_meta_data( 'enabled', $fields ) ) continue;

	    			// Get value
	    			$value = ovabrw_get_meta_data( $name, $values );
	    			if ( $value ) {
	    				$item->add_meta_data( $name, $value, true );
	    			}
	    		}
	    	}

	    	// Custom checkout fields
	    	$cckf = ovabrw_get_meta_data( 'cckf', $values );
	    	if ( ovabrw_array_exists( $cckf ) ) {
	    		$item->add_meta_data( 'ovabrw_custom_ckf', $cckf, true );

	    		// Quantity
	    		$cckf_qty = ovabrw_get_meta_data( 'cckf_qty', $values );
	    		if ( ovabrw_array_exists( $cckf_qty ) ) {
	    			$item->add_meta_data( 'ovabrw_custom_ckf_qty', $cckf_qty, true );
	    		}
	    	}

	    	// Resources
	    	$resources = ovabrw_get_meta_data( 'resources', $values );
	    	if ( ovabrw_array_exists( $resources ) ) {
	    		// Resources quantity
	    		$resources_qty = ovabrw_get_meta_data( 'resources_qty', $values );

	    		// Get resource ids
	    		$resc_ids = $this->get_meta_value( 'resource_id', [] );

	    		// Get resource names
	    		$resc_names = $this->get_meta_value( 'resource_name', [] );

	    		// init option names
	    		$opt_names = [];

	    		foreach ( $resources as $opt_id ) {
	    			// Get index option
	    			$index = array_search( $opt_id, $resc_ids );
	    			if ( is_bool( $index ) ) continue;

	    			// Option name
	    			$opt_name = ovabrw_get_meta_data( $index, $resc_names );
	    			if ( !$opt_name ) continue;

	    			// Option quantity
	    			$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $resources_qty );
	    			if ( $opt_qty ) {
	    				$opt_names[] = sprintf( esc_html__( '%s (%s)', 'ova-brw' ), $opt_name, $opt_qty );
	    			} else {
	    				$opt_names[] = $opt_name;
	    			}
	    		}

	    		// Add option names
	    		if ( ovabrw_array_exists( $opt_names ) ) {
	    			$item->add_meta_data( sprintf( _n( 'Resource%s', 'Resources%s', count( $opt_names ), 'ova-brw' ), '' ), implode( ', ', $opt_names ), true );
	    		}

	    		// Add resources
	    		$item->add_meta_data( 'ovabrw_resources', $resources, true );

	    		// Add resources quantity
	    		if ( ovabrw_array_exists( $resources_qty ) ) {
	    			$item->add_meta_data( 'ovabrw_resources_qty', $resources_qty, true );
	    		}
	    	} // END if

	    	// Services
	    	$services = ovabrw_get_meta_data( 'services', $values );
	    	if ( ovabrw_array_exists( $services ) ) {
	    		// Services quantity
	    		$services_qty = ovabrw_get_meta_data( 'services_qty', $values );

	    		// Get service labels
	    		$serv_labels = $this->get_meta_value( 'label_service', [] );

	    		// Get option ids
	    		$opt_ids = $this->get_meta_value( 'service_id', [] );

	    		// Get option names
	    		$opt_names = $this->get_meta_value( 'service_name', [] );

	    		// Loop
	    		foreach ( $services as $opt_id ) {
	    			// Option quantity
	    			$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $services_qty );

	    			// Loop option ids
	    			foreach ( $opt_ids as $i => $ids ) {
	    				// Get index option
	    				$index = array_search( $opt_id, $ids );
	    				if ( is_bool( $index ) ) continue;

	    				// Service label
	    				$label = ovabrw_get_meta_data( $i, $serv_labels );
	    				if ( !$label ) continue;

	    				// Option name
	    				$opt_name = isset( $opt_names[$i][$index] ) ? $opt_names[$i][$index] : '';
	    				if ( !$opt_name ) continue;

	    				// Opt qty
	    				if ( $opt_qty ) {
	    					$opt_name = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_name, $opt_qty );
	    				}

	    				// Add option name
	    				$item->add_meta_data( $label, $opt_name, true );
	    			}
	    			// END loop option ids
	    		} // END loop services

	    		// Add services
	    		$item->add_meta_data( 'ovabrw_services', $services, true );

	    		// Add services quantity
	    		if ( ovabrw_array_exists( $services_qty ) ) {
	    			$item->add_meta_data( 'ovabrw_services_qty', $services_qty, true );
	    		}
	    	} // END if

	    	// Insurance amount
	        $insurance_amount = (float)$values['data']->get_meta( 'insurance_amount' );
	        if ( $insurance_amount ) {
	        	$item->add_meta_data( 'ovabrw_insurance_amount', ovabrw_convert_price( $insurance_amount ), true );

	        	// Get insurance tax
	        	$insurance_tax = (float)$values['data']->get_meta( 'insurance_tax' );
	        	if ( $insurance_tax ) {
	        		$item->add_meta_data( 'ovabrw_insurance_tax', ovabrw_convert_price( $insurance_tax ), true );
	        	}

	        	// Get remaining insurance
	        	$remaining_insurance = (float)$values['data']->get_meta( 'remaining_insurance' );
	        	if ( $remaining_insurance ) {
	        		$item->add_meta_data( 'ovabrw_remaining_insurance', ovabrw_convert_price( $remaining_insurance ), true );
	        	}

	        	// Get remaining insurance tax
	        	$remaining_insurance_tax = (float)$values['data']->get_meta( 'remaining_insurance_tax' );
	        	if ( $remaining_insurance_tax ) {
	        		$item->add_meta_data( 'ovabrw_remaining_insurance_tax', ovabrw_convert_price( $remaining_insurance_tax ), true );
	        	}
	        } // END if

	        // Has deposit
	        $has_deposit = isset( WC()->cart->deposit_info[ 'has_deposit' ] ) ? WC()->cart->deposit_info[ 'has_deposit' ] : '';
	        if ( $has_deposit ) {
	        	// Deposit type
	        	$deposit_type = $values['data']->get_meta( 'deposit_type' );
	        	if ( $deposit_type ) {
					$item->add_meta_data( 'ovabrw_deposit_type', $deposit_type, true );
				}

				// Deposit value
	        	$deposit_value = $values['data']->get_meta( 'deposit_value' );
	        	if ( $deposit_value ) {
	        		$item->add_meta_data( 'ovabrw_deposit_value', $deposit_value, true );
	        	}

	        	// Deposit amount
	        	$deposit_amount = $values['data']->get_meta( 'deposit_amount' );
	        	if ( $deposit_amount ) {
					$item->add_meta_data( 'ovabrw_deposit_amount', ovabrw_convert_price( $deposit_amount ), true );
				}

				// Remaning amount
				$remaining_amount = $values['data']->get_meta( 'remaining_amount' );
				if ( $remaining_amount ) {
					$item->add_meta_data( 'ovabrw_remaining_amount', ovabrw_convert_price( $remaining_amount ), true );
				}

				// Remaining tax
				$remaining_tax = $values['data']->get_meta('remaining_tax');
				if ( $remaining_tax ) {
					$item->add_meta_data( 'ovabrw_remaining_tax', $remaining_tax, true );
				}

				// Total payable
				$total_payable = $values['data']->get_meta('total_payable');
				if ( $total_payable ) {
					$item->add_meta_data( 'ovabrw_total_payable', ovabrw_convert_price( $total_payable ), true );
				}
	        } // END if
		}

		/**
		 * Get request booking mail content
		 */
		public function get_request_booking_mail_content( $data = [] ) {
			return apply_filters( $this->prefix.'get_request_booking_mail_content', '', $data, $this );
		}

		/**
		 * Request booking create new order
		 */
		public function request_booking_create_new_order( $order_data = [], $args = [] ) {
			return apply_filters( $this->prefix.'request_booking_create_new_order', false, $order_data, $args, $this );
		}

		/**
		 * New booking handle item
		 */
		public function new_booking_handle_item( $meta_key, $args, $order ) {
			return apply_filters( $this->prefix.'new_booking_handle_item', [], $meta_key, $args, $order, $this );
		}

		/**
	     * Get permalink
	     */
	    public function get_permalink() {
	    	// Product permalink
	    	$permalink = $this->product->get_permalink();

	    	// Pick-up location
	    	$pickup_location = ovabrw_get_meta_data( 'pickup_location', $_REQUEST );
	    	if ( $pickup_location ) {
	    		$permalink = add_query_arg( 'pickup_location', $pickup_location, $permalink );
	    	}

	    	// Origin location
	    	$origin_location = stripslashes( stripslashes( ovabrw_get_meta_data( 'origin', $_REQUEST ) ) );
	    	if ( $origin_location ) {
	    		$permalink = add_query_arg( 'origin', $origin_location, $permalink );
	    	}

	    	// Drop-off location
	    	$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $_REQUEST );
	    	if ( $dropoff_location ) {
	    		$permalink = add_query_arg( 'dropoff_location', $dropoff_location, $permalink );
	    	}

	    	// Product tag
    		$product_tag = sanitize_text_field( ovabrw_get_meta_data( 'product_tag', $_REQUEST ) );
    		if ( $product_tag ) {
    			$permalink = add_query_arg( 'product_tag', $dropoff_location, $permalink );
    		}

	    	// Destination
            $destination = stripslashes( stripslashes( ovabrw_get_meta_data( 'destination', $_REQUEST ) ) );
            if ( $destination ) {
                $permalink = add_query_arg( 'destination', $destination, $permalink );
            }

            // Pick-up date
            $pickup_date = ovabrw_get_meta_data( 'pickup_date', $_REQUEST );
            if ( $pickup_date ) {
                $permalink = add_query_arg( 'pickup_date', $pickup_date, $permalink );
            }

            // Drop-off date
            $dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $_REQUEST );
            if ( $dropoff_date ) {
                $permalink = add_query_arg( 'dropoff_date', $dropoff_date, $permalink );
            }

            // Duration
            $duration = (int)ovabrw_get_meta_data( 'duration', $_REQUEST );
            if ( $duration ) {
                $permalink = add_query_arg( 'duration', $duration, $permalink );
            }

            // Distance
            $distance = (int)ovabrw_get_meta_data( 'distance', $_REQUEST );
            if ( $distance ) {
                $permalink = add_query_arg( 'distance', $distance, $permalink );
            }

            // Quantity
            $quantity = (int)ovabrw_get_meta_data( 'quantity', $_REQUEST );
            if ( $quantity ) {
                $permalink = add_query_arg( 'quantity', $quantity, $permalink );
            }

            // Package
            $package = ovabrw_get_meta_data( 'package', $_REQUEST );
            if ( $package ) {
                $permalink = add_query_arg( 'package', $package, $permalink );
            }

            // Number of adults
            $numberof_adults = ovabrw_get_meta_data( 'adults', $_REQUEST );
            if ( $numberof_adults ) {
                $permalink = add_query_arg( 'adults', $numberof_adults, $permalink );
            }

            // Number of children
            $numberof_children = ovabrw_get_meta_data( 'children', $_REQUEST );
            if ( $numberof_children ) {
                $permalink = add_query_arg( 'children', $numberof_children, $permalink );
            }

            // Number of babies
            $numberof_babies = ovabrw_get_meta_data( 'babies', $_REQUEST );
            if ( $numberof_babies ) {
                $permalink = add_query_arg( 'babies', $numberof_babies, $permalink );
            }

            // Loader
            if ( 'yes' !== ovabrw_get_meta_data( 'loader', $_REQUEST ) ) {
	            // Guest options
				$guest_options = OVABRW()->options->get_guest_options();
				foreach ( $guest_options as $k => $guest_item ) {
					// Get name
					$guest_name = ovabrw_get_meta_data( 'name', $guest_item );
					if ( !$guest_name ) continue;

					// Get guest number
					$guest_num = (int)sanitize_text_field( ovabrw_get_meta_data( $guest_name, $_REQUEST ) );
					if ( $guest_num ) {
						$permalink = add_query_arg( $guest_name, $guest_num, $permalink );
					}
				}
			}

            return apply_filters( $this->prefix.'get_permalink', $permalink, $this );
	    }

	    /**
	     * Get add to cart data
	     */
	    public function get_add_to_cart_data() {
	    	return apply_filters( $this->prefix.'get_add_to_cart_data', false, $this );
	    }

	    /**
		 * Get guest options
		 */
		public function get_guest_options() {
			// Get guest options
			$guest_options = OVABRW()->options->get_guest_options();

			// Show guests
	    	if ( !$this->is_type( 'tour' ) && 'yes' != $this->get_meta_value( 'show_guests' ) ) {
	    		return $guest_options;
	    	}

			// Get guest type
			$guest_type = $this->get_meta_value( 'guest_type' );

			// Get special guests
			$special_guests = $this->get_meta_value( 'special_guests' );
			if ( 'special' === $guest_type && ovabrw_array_exists( $special_guests ) ) {
				// Loop
				foreach ( $guest_options as $i => $guest ) {
					$name = ovabrw_get_meta_data( 'name', $guest );
					if ( !in_array( $name, $special_guests ) ) {
						unset( $guest_options[$i] );
					}
				} // END loop

				// re-indexes
				$guest_options = array_values( $guest_options );
			}

			return apply_filters( $this->prefix.'get_guest_options', $guest_options, $this );
		}

		/**
		 * Get guest info data
		 */
		public function get_guest_info_data( $guest_name = '', $guest_info = [] ) {
			if ( !$guest_name || !ovabrw_array_exists( $guest_info ) ) return;

			// init
			$info_data = [];

			// Get guest fields
			$guest_fields = OVABRW()->options->get_guest_fields( $guest_name );

			foreach ( $guest_fields as $name => $fields ) {
				// Enable
				$enable = ovabrw_get_meta_data( 'enable', $fields );
				if ( !$enable ) continue;

				// Label
				$label = ovabrw_get_meta_data( 'label', $fields );

				// Type
				$type = ovabrw_get_meta_data( 'type', $fields );

				// Option IDs
				$option_ids = ovabrw_get_meta_data( 'option_ids', $fields, [] );

				// Option Names
				$option_names = ovabrw_get_meta_data( 'option_names', $fields, [] );

				// Option Prices
				$option_prices = ovabrw_get_meta_data( 'option_prices', $fields, [] );

				if ( ovabrw_array_exists( $guest_info ) ) {
					foreach ( $guest_info as $k => $field_data ) {
						if ( 'file' == $type ) {
							// Attachment ID
							$attachment_id = ovabrw_get_meta_data( $name, $field_data );

							if ( $attachment_id ) {
								$file_path 		= get_attached_file( $attachment_id );
								$file_name 		= get_the_title( $attachment_id );
								$file_url 		= wp_get_attachment_url( $attachment_id );
								$file_type 		= get_post_mime_type( $attachment_id );

								if ( $file_path && $file_name && $file_url ) {
									$info_data[$k][$name] = [
										'label' 		=> $label,
										'type' 			=> $type,
										'path' 			=> $file_path,
										'value' 		=> $file_name,
										'url' 			=> $file_url,
										'extention' 	=> $file_type,
										'attachment_id' => $attachment_id
									];
								}
							}
						} elseif ( 'date' == $type ) {
							$date = ovabrw_get_meta_data( $name, $field_data );
							if ( !strtotime( $date ) ) continue;

							// Check min date
							$min_date = ovabrw_get_meta_data( 'min', $fields );
							$min_date = strtotime( $min_date );
							if ( $min_date && $min_date > strtotime( $date ) ) continue;

							// Check max date
							$max_date = ovabrw_get_meta_data( 'max', $fields );
							$max_date = strtotime( $max_date );
							if ( $max_date && $max_date < strtotime( $date ) ) continue;
							
							// Add info data
							$info_data[$k][$name] = [
								'label' => $label,
								'type' 	=> $type,
								'value' => $date
							];
						} elseif ( 'radio' == $type || 'select' == $type ) {
							$opt_id = ovabrw_get_meta_data( $name, $field_data );
							$qtys 	= ovabrw_get_meta_data( $name.'_qty', $field_data, [] );

							if ( $opt_id ) {
								$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $qtys, 1 );

								// Get option index
								$opt_index = array_search( $opt_id, $option_ids );

								if ( false !== $opt_index ) {
									$opt_name = ovabrw_get_meta_data( $opt_index, $option_names );

									// Add info data
									$info_data[$k][$name] = [
										'label' 		=> $label,
										'type' 			=> $type,
										'option_id' 	=> $opt_id,
										'option_name' 	=> $opt_name,
										'option_qty' 	=> $opt_qty
									];
								}
							}
						} elseif ( 'checkbox' == $type ) {
							$opt_ids 	= ovabrw_get_meta_data( $name, $field_data );
							$qtys 		= ovabrw_get_meta_data( $name.'_qty', $field_data, [] );

							if ( ovabrw_array_exists( $opt_ids ) ) {
								$data_opt_ids = $data_opt_names = $data_opt_qtys = [];

								// Loop
								foreach ( $opt_ids as $opt_id ) {
									if ( $opt_id ) {
										$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $qtys, 1 );

										// Get option index
										$opt_index = array_search( $opt_id, $option_ids );

										if ( false !== $opt_index ) {
											$opt_name = ovabrw_get_meta_data( $opt_index, $option_names );

											$data_opt_ids[] 	= $opt_id;
											$data_opt_names[] 	= $opt_name;
											$data_opt_qtys[] 	= $opt_qty;
										}
									}
								} // END loop
								
								// Add data
								if ( ovabrw_array_exists( $data_opt_ids ) ) {
									// Add info data
									$info_data[$k][$name] = [
										'label' 		=> $label,
										'type' 			=> $type,
										'option_id' 	=> $data_opt_ids,
										'option_name' 	=> $data_opt_names,
										'option_qty' 	=> $data_opt_qtys
									];
								}
							}
						} else {
							$value = ovabrw_get_meta_data( $name, $field_data );
							if ( !$value ) continue;

							// Add info data
							$info_data[$k][$name] = [
								'label' => $label,
								'type' 	=> $type,
								'value' => $value
							];
						}
					}
				}
			}

			return apply_filters( $this->prefix.'add_new_booking_get_guest_info_data', $info_data, $guest_name, $guest_info );
		}

		/**
		 * Calculate total
		 * @param  array 		$data
		 * @return array|bool 	$results
		 */
		public function calculate_total( $data = [] ) {
			if ( !ovabrw_array_exists( $data ) ) return false;

			// Hook name
			$hook_name = $this->prefix.'calculate_total';

			// init results
			$results = [];

			// Current form
			$current_form = sanitize_text_field( ovabrw_get_meta_data( 'form_name', $data, 'booking' ) );

			// Pick-up location
			$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $data ) );

			// Drop-off location
			$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $data ) );

			// Pick-up date
			$pickup_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $data ) ) );
			if ( !$pickup_date ) return false;

			// Drop-off date
			$dropoff_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $data ) ) );

			// Package ID
			$package_id = sanitize_text_field( ovabrw_get_meta_data( 'package_id', $data ) );

			// Quantity
			$quantity = sanitize_text_field( ovabrw_get_meta_data( 'quantity', $data, 1 ) );

			// Deposit
			$deposit = sanitize_text_field( ovabrw_get_meta_data( 'deposit', $data ) );

			// Custom checkout fields
			$cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'cckf', $data ) );
			if ( $cckf ) $cckf = ovabrw_object_to_array( json_decode( $cckf ) );

			// Quantity custom checkout fields
			$cckf_qty = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'cckf_qty', $data ) );
			if ( $cckf_qty ) $cckf_qty = ovabrw_object_to_array( json_decode( $cckf_qty ) );

			// Resources
			$resources = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'resources', $data ) );
			if ( $resources ) $resources = ovabrw_object_to_array( json_decode( $resources ) );

			// Quantity resource
			$resources_qty = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'resources_qty', $data ) );
			if ( $resources_qty ) $resources_qty = ovabrw_object_to_array( json_decode( $resources_qty ) );

			// Services
			$services = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'services', $data ) );
			if ( $services ) $services = ovabrw_object_to_array( json_decode( $services ) );

			// Quantity services
			$services_qty = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'services_qty', $data ) );
			if ( $services_qty ) $services_qty = ovabrw_object_to_array( json_decode( $services_qty ) );

			// Extra services
			$extra_services = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'extra_services', $data ) );
			if ( $extra_services ) $extra_services = ovabrw_object_to_array( json_decode( $extra_services ) );

			// Taxi
			$duration_map 	= sanitize_text_field( ovabrw_get_meta_data( 'duration_map', $data ) );
			$duration 		= sanitize_text_field( ovabrw_get_meta_data( 'duration', $data, 0 ) );
			$distance 		= sanitize_text_field( ovabrw_get_meta_data( 'distance', $data ) );
			$extra_time 	= sanitize_text_field( ovabrw_get_meta_data( 'extra_time', $data ) );

			// Get new date
			$new_date = $this->get_new_date([
				'pickup_location' 	=> $pickup_location,
				'dropoff_location' 	=> $dropoff_location,
				'pickup_date' 		=> $pickup_date,
				'dropoff_date' 		=> $dropoff_date,
				'package_id' 		=> $package_id,
				'duration' 			=> $duration
			]);
			if ( !ovabrw_array_exists( $new_date ) ) return false;

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );

			// Booking validation
			$booking_validation = $this->booking_validation( $pickup_date, $dropoff_date, $data );
			if ( $booking_validation ) {
				$results['error'] = $booking_validation;
				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Get items available
			$items_available = $this->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'cart' );

			// Vehicles available
			if ( is_array( $items_available ) ) {
				$items_available = count( $items_available );
			}

			// Check quantity
			if ( $items_available < $quantity ) {
				if ( $items_available > 0 ) {
					$results['error'] = sprintf( esc_html__( 'Items available: %s', 'ova-brw'  ), $items_available );
				} else {
					$results['error'] = esc_html__( 'Out stock!', 'ova-brw' );
				}

				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Qty available
		    $results['items_available'] = $items_available;

		    // Insurance amount
		    $insurance_amount = (float)$this->get_meta_value( 'amount_insurance' ) * $quantity;

	        // Add Cart item
	        $cart_item = apply_filters( OVABRW_PREFIX.'cart_item_calculate_total', [
	        	'pickup_date' 		=> $pickup_date,
	        	'dropoff_date' 		=> $dropoff_date,
	        	'pickup_location' 	=> $pickup_location,
	        	'dropoff_location' 	=> $dropoff_location,
	        	'package_id' 		=> $package_id,
	        	'duration' 			=> $duration,
	        	'distance' 			=> $distance,
	        	'extra_time' 		=> $extra_time,
	        	'quantity' 			=> $quantity,
	        	'cckf'  			=> $cckf,
	        	'cckf_qty' 			=> $cckf_qty,
	        	'resources' 		=> $resources,
	        	'resources_qty' 	=> $resources_qty,
	        	'services' 			=> $services,
	        	'services_qty' 		=> $services_qty,
	        	'extra_services' 	=> $extra_services
	        ], $data );

	        // Number of guests
	        $numberof_guests = 0;

	        // Guest options
	        $guest_options = $this->get_guest_options();
	        foreach ( $guest_options as $guests ) {
	        	// Get number of guests
	        	$number_guest = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guests['name'], $data );

	        	// Add cart item
	        	$cart_item['numberof_'.$guests['name']] = $number_guest;

	        	// Update number of total guests
	        	$numberof_guests += $number_guest;
	        }

	        // Add number of guests to cart item
	        if ( $numberof_guests ) {
	        	$cart_item['numberof_guests'] = $numberof_guests;
	        }

	        // Get subtotal
	        $line_total = $this->get_total( $cart_item );
	        if ( !$line_total ) $line_total = 0;

			// Multi Currency
        	if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
                $line_total 		= ovabrw_convert_price( $line_total );
                $insurance_amount 	= ovabrw_convert_price( $insurance_amount );
            }

            // Total amount
            $total_amount = $line_total;
            if ( $insurance_amount ) $total_amount += $insurance_amount;

            // Deposit
            if ( 'deposit' === $deposit ) {
            	$deposit_type 	= $this->get_meta_value( 'type_deposit' );
            	$deposit_value 	= (float)$this->get_meta_value( 'amount_deposit' );

            	// Calculate deposit
            	if ( 'percent' === $deposit_type ) { // Percent
            		$line_total = floatval( ( $line_total * $deposit_value ) / 100 );

            		if ( $insurance_amount && 'yes' !== ovabrw_get_setting( 'only_add_insurance_to_deposit', 'no' ) ) {
		            	$insurance_amount = floatval( ( $insurance_amount * $deposit_value ) / 100 );
		            }
            	} elseif ( 'value' === $deposit_type ) { // Fixed
            		$line_total = floatval( $deposit_value );
            	}
            }

            // Insurance amount
            if ( $insurance_amount ) {
            	$line_total += $insurance_amount;

            	$insurance_html = sprintf( esc_html__( '(includes %s insurance)', 'ova-brw' ), ovabrw_wc_price( $insurance_amount ) );

            	$results['insurance_amount'] = apply_filters( OVABRW_PREFIX.'ajax_insurance_html', $insurance_html, $this->id );
            }

            if ( $line_total <= 0 && apply_filters( OVABRW_PREFIX.'required_total', false ) ) {
				return false;
			} else {
				if ( 'deposit' === $deposit ) {
					$line_total = wp_kses_post( sprintf( __( 'Deposit: <span class="show_total">%s</span> (of %s)', 'ova-brw' ), ovabrw_wc_price( $line_total ), ovabrw_wc_price( $total_amount ) ) );
				} else {
					$line_total = wp_kses_post( sprintf( __( 'Total: <span class="show_total">%s</span>', 'ova-brw' ), ovabrw_wc_price( $line_total ) ) );
				}

				// Tax enabled
				if ( wc_tax_enabled() && apply_filters( OVABRW_PREFIX.'show_tax_label', true ) ) {
					if ( $this->product->is_taxable() && !wc_prices_include_tax() ) {
						$line_total .= ' <small class="tax_label">'.esc_html__( '(excludes tax)', 'ova-brw' ).'</small>';
					}
				}

				// Add line total
				$results['line_total'] = apply_filters( OVABRW_PREFIX.'ajax_total_filter', $line_total, $this->id );

				// Get price details
				if ( $this->show_price_details( $current_form ) ) {
		        	$price_details = $this->get_price_details( $cart_item );
		        	if ( $price_details ) $results['price_details'] = $price_details;
		        }
			}

			return apply_filters( $hook_name, $results, $data, $this );
		}

		/**
		 * Show price details
		 * @param 	string 	$form
		 * @return 	bool
		 */
		public function show_price_details( $form = 'booking' ) {
			if ( 'booking' === $form ) {
				return 'yes' === ovabrw_get_setting( 'booking_form_show_price_details', 'no' ) ? true : false;
			} elseif ( 'request' === $form ) {
				return 'yes' === ovabrw_get_setting( 'request_booking_show_price_details', 'no' ) ? true : false;
			}

			return false;
		}

		/**
		 * Guest info enabled
		 */
		public function guest_info_enabled() {
			// init
			$result = false;

			// Guest info option
			$guest_info = ovabrw_get_option( 'guest_info' );

			// Get product guest info
			$product_guest_info = $this->get_meta_value( 'guest_info_fields', 'all' );
			if ( 'none' === $product_guest_info ) $guest_info = '';
			if ( 'yes' == $guest_info ) {
				// Get guest information fields data
				$guest_fields = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'guest_fields', [] ) );

				foreach ( $guest_fields as $name => $fields ) {
					$enable = ovabrw_get_meta_data( 'enable', $fields );

					if ( 'on' == $enable ) {
						$result = true;
						break;
					}
				}
			}

			return apply_filters( $this->prefix.'guest_info_enabled', $result, $this );
		}

		/**
		 * Cart item validation
		 * @param 	array 	$cart_item
		 * @return 	mixed 	$mesg
		 */
		public function cart_item_validation( $cart_item = [] ) {
			// init
			$mesg = false;

			// Get quantity
			$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item, 1 );

			// Pick-up location
			$pickup_location = ovabrw_get_meta_data( 'pickup_location', $cart_item );

			// Drop-off location
			$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $cart_item );

			// Pick-up date
			$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

			// Drop-off date
			$dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

			// Package ID
			$package_id = ovabrw_get_meta_data( 'package_id', $cart_item );

			// Duration
			$duration = ovabrw_get_meta_data( 'duration', $cart_item, 0 );

			// Get new date
			$new_date = $this->get_new_date([
				'pickup_location' 	=> $pickup_location,
				'dropoff_location' 	=> $dropoff_location,
				'pickup_date' 		=> $pickup_date,
				'dropoff_date' 		=> $dropoff_date,
				'package_id' 		=> $package_id,
				'duration' 			=> $duration
			]);
			if ( !ovabrw_array_exists( $new_date ) ) wp_die();

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );

			// Get items available
			$items_available = $this->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'checkout' );

			// Vehicles available
			if ( is_array( $items_available ) ) $items_available = count( $items_available );

			// Get reserved items
    		$reserved_items = $this->get_reserved_items( $pickup_date, $dropoff_date );
    		if ( $reserved_items ) $items_available -= $reserved_items;

    		// Check items availabel
    		if ( $items_available < $quantity ) {
    			$mesg = sprintf( esc_html__( '%s isn\'t available for this time, Please book other time.', 'ova-brw' ), $this->product->get_title() );
    		}

			return apply_filters( $this->prefix.'cart_item_validation', $mesg, $cart_item, $this );
		}

		/**
		 * Create booking calculate total
		 * @param 	array 	$data
		 * @return 	mixed 	$resutls
		 */
		public function create_booking_calculate_total( $data = [] ) {
			if ( !ovabrw_array_exists( $data ) ) return false;

			// Hook name
			$hook_name = $this->prefix.'create_booking_calculate_total';

			// init results
			$results = [];

			// Currency
			$currency = sanitize_text_field( ovabrw_get_meta_data( 'currency', $data ) );

			// Pick-up location
			$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $data ) );

			// Drop-off location
			$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $data ) );

			// Pick-up date
			$pickup_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $data ) ) );
			if ( !$pickup_date ) return false;

			// Drop-off date
			$dropoff_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $data ) ) );

			// Package ID
			$package_id = sanitize_text_field( ovabrw_get_meta_data( 'package_id', $data ) );

			// Quantity
			$quantity = (int)ovabrw_get_meta_data( 'quantity', $data, 1 );

			// Vehicle ID
			$vehicle_id = sanitize_text_field( ovabrw_get_meta_data( 'vehicle_id', $data ) );

			// Extra time
			$extra_time = sanitize_text_field( ovabrw_get_meta_data( 'extra_time', $data ) );

			// Duration map
			$duration_map = sanitize_text_field( ovabrw_get_meta_data( 'duration_map', $data ) );

			// Duration
			$duration = sanitize_text_field( ovabrw_get_meta_data( 'duration', $data ) );

			// Distance
			$distance = sanitize_text_field( ovabrw_get_meta_data( 'distance', $data ) );

			// Custom checkout fields
			$cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'cckf', $data ) );
			if ( $cckf ) $cckf = ovabrw_object_to_array( json_decode( $cckf ) );

			// Quantity custom checkout fields
			$cckf_qty = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'cckf_qty', $data ) );
			if ( $cckf_qty ) $cckf_qty = ovabrw_object_to_array( json_decode( $cckf_qty ) );

			// Resources
			$resources = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'resources', $data ) );
			if ( $resources ) $resources = ovabrw_object_to_array( json_decode( $resources ) );

			// Quantity resource
			$resources_qty = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'resources_qty', $data ) );
			if ( $resources_qty ) $resources_qty = ovabrw_object_to_array( json_decode( $resources_qty ) );

			// Services
			$services = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'services', $data ) );
			if ( $services ) $services = ovabrw_object_to_array( json_decode( $services ) );

			// Quantity services
			$services_qty = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'services_qty', $data ) );
			if ( $services_qty ) $services_qty = ovabrw_object_to_array( json_decode( $services_qty ) );

			// Extra services
			$extra_services = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'extra_services', $data ) );
			if ( $extra_services ) $extra_services = ovabrw_object_to_array( json_decode( $extra_services ) );

			// Get new date
			$new_date = $this->get_new_date([
				'pickup_location' 	=> $pickup_location,
				'dropoff_location' 	=> $dropoff_location,
				'pickup_date' 		=> $pickup_date,
				'dropoff_date' 		=> $dropoff_date,
				'package_id' 		=> $package_id,
				'duration' 			=> $duration
			]);
			if ( !ovabrw_array_exists( $new_date ) ) wp_die();

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );

			// Booking validation
			$booking_validation = $this->booking_validation( $pickup_date, $dropoff_date, $data );
			if ( $booking_validation ) {
				$results['error'] = $booking_validation;
				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Get items available
			$items_available = $this->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'check' );

			// Vehicles available
			if ( is_array( $items_available ) ) {
				// Check vehicle id
				if ( $vehicle_id && !in_array( $vehicle_id, $items_available ) ) {
					$results['error'] = sprintf( esc_html__( 'Vehicle ID: %s is not available!', 'ova-brw' ), $vehicle_id );
					return apply_filters( $hook_name, $results, $data, $this );
				}

				$items_available = count( $items_available );
			}

			// Check quantity
			if ( $items_available < $quantity ) {
				if ( $items_available > 0 ) {
					$results['error'] = sprintf( esc_html__( 'Items available: %s', 'ova-brw'  ), $items_available );
					return apply_filters( $hook_name, $results, $data, $this );
				} else {
					$results['error'] = esc_html__( 'Out stock!', 'ova-brw' );
					return apply_filters( $hook_name, $results, $data, $this );
				}
			}

	        // Qty available
		    $results['items_available'] = $items_available;

		    // Insurance amount
		    $insurance = (float)$this->get_meta_value( 'amount_insurance' ) * $quantity;

	        // Add Cart item
	        $cart_item = apply_filters( OVABRW_PREFIX.'cart_item_calculate_total', [
	        	'pickup_date' 		=> $pickup_date,
	        	'dropoff_date' 		=> $dropoff_date,
	        	'pickup_location' 	=> $pickup_location,
	        	'dropoff_location' 	=> $dropoff_location,
	        	'package_id' 		=> $package_id,
	        	'duration' 			=> $duration,
	        	'distance' 			=> $distance,
	        	'extra_time' 		=> $extra_time,
	        	'quantity' 			=> $quantity,
	        	'cckf'  			=> $cckf,
	        	'cckf_qty' 			=> $cckf_qty,
	        	'resources' 		=> $resources,
	        	'resources_qty' 	=> $resources_qty,
	        	'services' 			=> $services,
	        	'services_qty' 		=> $services_qty
	        ], $data );

	        // Guest options
	        $guest_options = $this->get_guest_options();
	        foreach ( $guest_options as $guests ) {
	        	$cart_item['numberof_'.$guests['name']] = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guests['name'], $_POST );
	        }

	        // Get line total
	        $line_total = $this->get_total( $cart_item );
	        if ( !$line_total ) $line_total = 0;

			// Multi Currency
        	if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
        		// Line total
                $line_total = ovabrw_convert_price( $line_total, [ 'currency' => $currency ] );

                // Insurance amount
                $insurance = ovabrw_convert_price( $insurance, [ 'currency' => $currency ] );
            }

            // Insurance
            $line_total += $insurance;

            if ( $line_total <= 0 && apply_filters( OVABRW_PREFIX.'required_total', false ) ) {
				return false;
			} else {
				// Insurance amount
				$results['insurance'] = round( $insurance, wc_get_price_decimals() );

				// Line total
				$results['line_total'] = round( $line_total, wc_get_price_decimals() );
			}

			return apply_filters( $hook_name, $results, $data, $this );
		}

		/**
		 * Get ical events
		 */
		public function get_ical_events() {
	        $ics = "BEGIN:VCALENDAR\r\n";
		    $ics .= "PRODID:-//OVABRW//Ical Calendar//EN\r\n";
		    $ics .= "CALSCALE:GREGORIAN\r\n";
		    $ics .= "VERSION:2.0\r\n";

		 	// Get domain
		    $domain = parse_url( get_site_url(), PHP_URL_HOST );

		    // Get today
		    $today = strtotime( gmdate( 'Y-m-d', current_time( 'timestamp' ) ) );

		    // Get disabled dates
		    $disabled_dates = ovabrw_get_meta_data( 'full_day', $this->get_disabled_dates(), [] );

		    // Get booked dates
		    $booked_dates = ovabrw_get_meta_data( 'full_day', $this->get_booked_dates(), [] );

		    // Merge dates
		    $arr_dates = array_values( array_unique( array_merge( $disabled_dates, $booked_dates ) ) );
		    if ( ovabrw_array_exists( $arr_dates ) ) {
		    	// Get summary
		    	$summary = $this->get_title();

		    	foreach ( $arr_dates as $date ) {
		    		// Convert date to timestamp
		    		$timestamp = strtotime( $date );
		    		if ( !$timestamp || $timestamp < $today ) continue;

		    		$ics .= "BEGIN:VEVENT\r\n";
			        $ics .= "UID:" . md5( $this->id . '-' . $timestamp ) . '@'.$domain . "\r\n";
			        $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
			        $ics .= "DTSTART;VALUE=DATE:" . gmdate('Ymd', $timestamp) . "\r\n";
			        $ics .= "DTEND;VALUE=DATE:" . gmdate('Ymd', strtotime('+1 day', $timestamp)) . "\r\n";
			        $ics .= "SUMMARY:" . $summary . "\r\n";
			        $ics .= "STATUS:CONFIRMED\r\n";
			        $ics .= "END:VEVENT\r\n";
		    	}
		    }

		    $ics .= "END:VCALENDAR"; // END calendar
		    
		    return apply_filters( $this->prefix.'get_ical_events', $ics, $this );
		}

		/**
		 * Get booked dates from sync
		 */
		public function get_booked_dates_from_sync( $date_format = 'Y-m-d' ) {
			// init booked dates
			$booked_dates = [];

			// Get order ids
			$order_ids = OVABRW()->options->get_synced_order_ids( $this->get_id() );
			if ( ovabrw_array_exists( $order_ids ) ) {
				foreach ( $order_ids as $order_id ) {
					// Get order
					$order = wc_get_order( $order_id );

					// Get pick-up date
					$pickup_date = (int)$order->get_meta( '_ovabrw_pickup_date' );
					if ( !$pickup_date ) continue;

					// Get drop-off date
					$dropoff_date = (int)$order->get_meta( '_ovabrw_dropoff_date' );
					if ( !$dropoff_date ) continue;

					// Number of days between
	    			$days_between = ovabrw_numberof_days_between( $pickup_date, $dropoff_date );
	    			if ( 0 == $days_between ) {
    					$booked_dates[] = gmdate( $date_format, $pickup_date );
    				} elseif ( 1 == $days_between ) {
    					$booked_dates[] = gmdate( $date_format, $pickup_date );
    					$booked_dates[] = gmdate( $date_format, $dropoff_date );
    				} else {
    					// Get date range
    					$date_range = ovabrw_get_date_range( $pickup_date, $dropoff_date, $date_format );
    					if ( ovabrw_array_exists( $date_range ) ) {
    						$booked_dates = ovabrw_array_merge_unique( $booked_dates, $date_range );
    					}
    				}
				}
			}
			
			return apply_filters( $this->prefix.'get_booked_dates_from_sync', $booked_dates, $date_format, $this );
		}
	}
}