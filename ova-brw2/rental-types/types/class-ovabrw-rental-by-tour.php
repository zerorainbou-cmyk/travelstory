<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Rental By Tour
 */
if ( !class_exists( 'OVABRW_Rental_By_Tour' ) ) {

	class OVABRW_Rental_By_Tour extends OVABRW_Rental_Types {

		/**
		 * Constructor
		 */
		public function __construct( $rental_id = 0 ) {
			parent::__construct( $rental_id );
		}

		/**
		 * Get type
		 */
		public function get_type() {
			return 'tour';
		}

		/**
	     * is fixed date
	     * @return string boolean
	     */
	    public function is_fixed_date() {
	        return 'fixed' === $this->get_duration() ? true : false;
	    }

	    /**
	     * is timeslots
	     * @return string boolean
	     */
	    public function is_timeslots() {
	        return 'timeslots' === $this->get_duration() ? true : false;
	    }

	    /**
	     * is period time
	     * @return string boolean
	     */
	    public function is_period_time() {
	        return 'period' === $this->get_duration() ? true : false;
	    }

	    /**
	     * Get duration type
	     * @return string $type
	     */
	    public function get_duration() {
	        return apply_filters( $this->prefix.'get_duration', $this->get_meta_value( 'duration_type' ), $this );
	    }

		/**
		 * Get meta fields
		 */
		public function get_meta_fields() {
			return (array)apply_filters( $this->prefix.$this->get_type().'_get_meta_fields', [
				'rental-type',
				'tour-duration',
				'tour-specific-time',
				'tour-discounts',
				'tour-guests',
				'deposit',
				'specifications',
				'features',
				'tour-services',
				'allowed-dates',
				'disabled-dates',
				'map',
				'sync-calendar',
				'advanced-start',
				'product-templates',
				'booking-conditions',
				'custom-checkout-fields',
				'pickup-date',
				'dropoff-date',
				'guest-options',
				'extra-tab',
				'price-format',
				'frontend-order',
				'advanced-end'
			], $this );
		}

		public function get_create_booking_meta_fields() {
			return (array)apply_filters( $this->prefix.$this->get_type().'_get_create_booking_meta_fields', [
				'price',
				'tour-period',
				'pickup-date',
				'tour-time-slots',
				'dropoff-date',
				'guests-fields',
				'custom-checkout-fields',
				'extra-services',
				'deposit',
				'remaining',
				'total',
				'error'
			], $this );
		}

		/**
		 * Get html price
		 */
		public function get_price_html( $price_html = '', $currency = '' ) {
			// Price
			$price = (float)get_post_meta( $this->id, '_regular_price', true );

			$new_price = sprintf( esc_html__( '%s / Guest', 'ova-brw' ), ovabrw_wc_price( $price, [ 'currency' => $currency ] ) );

			return apply_filters( $this->prefix.'get_product_price_html', $new_price, $price_html, $currency, $this );
		}

		/**
	     * Get datepicker options
	     */
	    public function get_datepicker_options() {
	    	// Get datepicker options
        	$datepicker = OVABRW()->options->get_datepicker_options();

        	// Date format
	    	$date_format = OVABRW()->options->get_date_format();

	    	// Time format
	    	$time_format = OVABRW()->options->get_time_format();

	    	// Rental type
	    	$datepicker['rentalType'] = $this->get_type();

	    	// Global min date
	        $min_date = $datepicker['LockPlugin']['minDate'];
	        if ( !$min_date || strtotime( $min_date ) < current_time( 'timestamp' ) ) {
	            $min_date = gmdate( $date_format, current_time( 'timestamp' ) );
	        }

	        // Preparation time
	        $preparation_time = $this->get_preparation_time();
	        if ( $preparation_time && strtotime( $preparation_time ) > strtotime( $min_date ) ) {
	        	$min_date = $preparation_time;
	        }

	        // Update min date & start date
	        $datepicker['LockPlugin']['minDate']    = $min_date;
	        $datepicker['startDate']                = $min_date;
	        $datepicker['timestamp'] 				= time();

	        // Get duration type
        	if ( $this->is_fixed_date() ) {
	            $numberof_days = (int) $this->get_meta_value( 'numberof_days' );
	            if ( !$numberof_days ) $numberof_days = 1;

	            $datepicker['LockPlugin']['minDays'] = $numberof_days;
	            $datepicker['LockPlugin']['maxDays'] = $numberof_days;
	        } elseif ( $this->is_timeslots() ) {
	            // Get specific time
	            $datepicker['specificTime'] = $this->get_specific_time();
	        }

	        // Disable weekdays
	        $disable_weekdays = $this->get_disable_weekdays();
	        if ( ovabrw_array_exists( $disable_weekdays ) ) {
	            $datepicker['disableWeekDays'] = $disable_weekdays;

        		// Check today
	            $have_today = array_search( 'today', $datepicker['disableWeekDays'] );
	            if ( $have_today !== false ) {
	                unset( $datepicker['disableWeekDays'][$have_today] );
	                $datepicker['disableDates'][] = gmdate( $date_format, current_time( 'timestamp' ) );
	            }
	        }

	        // Allowed dates
	        $allowed_dates = $this->get_allowed_dates( $date_format );
	        if ( ovabrw_array_exists( $allowed_dates ) ) {
	        	$datepicker['allowedDates'] = ovabrw_array_merge_unique( $datepicker['allowedDates'], $allowed_dates );

	        	// Get start date
				$start_date = $this->get_start_date( $date_format );
				if ( $start_date ) $datepicker['startDate'] = $start_date;
	        }

	        // Disabled dates
	        $disabled_dates = $this->get_disabled_dates();
	        if ( ovabrw_array_exists( ovabrw_get_meta_data( 'full_day', $disabled_dates ) ) ) {
	        	$datepicker['disableDates'] = ovabrw_array_merge_unique( $datepicker['disableDates'], $disabled_dates['full_day'] );
	        }

	        // Booked dates
	        $booked_dates = $this->get_booked_dates();
	        if ( ovabrw_array_exists( ovabrw_get_meta_data( 'full_day', $booked_dates ) ) ) {
	        	$datepicker['bookedDates'] = ovabrw_array_merge_unique( $datepicker['bookedDates'], $booked_dates['full_day'] );
	        }

	        // Get booked dates from sync
	    	$dates_synced = $this->get_booked_dates_from_sync( $date_format );
	    	if ( ovabrw_array_exists( $dates_synced ) ) {
	    		$datepicker['bookedDates'] = ovabrw_array_merge_unique( $datepicker['bookedDates'], $dates_synced );
	    	}

	        // Show price on Calendar
	        if ( 'yes' === ovabrw_get_option( 'show_price_input_calendar', 'yes' ) ) {
	        	// Regular price
				$datepicker['regularPrice'] = $this->get_calendar_regular_price();

				// Daily prices - only apply for duration: One-day Tour
	            $daily_prices = $this->get_calendar_daily_prices();
	            if ( ovabrw_array_exists( $daily_prices ) ) {
	                $datepicker['dailyPrices'] = $daily_prices;
	            }

	            // Special prices
	            $special_prices = $this->get_calendar_special_prices();
	            if ( ovabrw_array_exists( $special_prices ) ) {
	                $datepicker['specialPrices'] = $special_prices;
	            }
	        }

	    	return apply_filters( $this->prefix.'get_datepicker_options', $datepicker, $this );
	    }

	    /**
	     * Get regular rental price
	     */
	    public function get_calendar_regular_price() {
	    	// Get regular price
            $regular_price = get_post_meta( $this->id, '_regular_price', true );

	    	// Get price html
	    	if ( $regular_price ) {
	    		$regular_price = OVABRW()->options->get_calendar_price_html( $regular_price );
	    	}

	    	return apply_filters( $this->prefix.'get_calendar_regular_price', $regular_price, $this );
	    }

	    /**
	     * Get specific time
	     * @return 	array 	$specific_time
	     */
	    public function get_specific_time() {
	        // init
	        $specific_time = [];

	        // Specific from
	        $specific_from = $this->get_meta_value( 'specific_from' );

	        // Specific to
	        $specific_to = $this->get_meta_value( 'specific_to' );

	        if ( ovabrw_array_exists( $specific_from ) && ovabrw_array_exists( $specific_to ) ) {
	            // Guest options
	            $guest_options = $this->get_guest_options();

	            // Time format
	            $time_format = OVABRW()->options->get_time_format();

	            // Get date format
	            $date_format = OVABRW()->options->get_date_format();

	            // Weekdays array
	            $weekdays = [
	                '1' => 'monday',
	                '2' => 'tuesday',
	                '3' => 'wednesday',
	                '4' => 'thursday',
	                '5' => 'friday',
	                '6' => 'saturday',
	                '7' => 'sunday'
	            ];

	            // Current time
	            $current_time = current_time( 'timestamp' );

	            // Specific start
	            $specific_start = $this->get_meta_value( 'specific_start' );

	            // Specific end
	            $specific_end = $this->get_meta_value( 'specific_end' );

	            // Specific
	            $specific_max_guests = $this->get_meta_value( 'specific_max_guests' );

	            foreach ( $specific_from as $i => $from_date ) {
	            	// From date
	                $from_date = (int)$from_date;
	                if ( !$from_date ) continue;

	                // Get to date
	                $to_date = (int)ovabrw_get_meta_data( $i, $specific_to );
	                if ( !$to_date ) continue;

	                // Timeslots start
	                $timeslots_start = ovabrw_get_meta_data( $i, $specific_start );

	                // Timeslots end
	                $timeslots_end = ovabrw_get_meta_data( $i, $specific_end );

	                // Timeslots max guests
	                $timeslots_max_guests = ovabrw_get_meta_data( $i, $specific_max_guests );

	                // Get daily prices
	                $daily_prices = [];
	                if ( 'yes' === ovabrw_get_option( 'show_price_input_calendar', 'yes' ) ) {
	                    // Daily price type
	                    $daily_price_type = apply_filters( OVABRW_PREFIX.'daily_price_type', 'highest' );            
	                    // Guest name
	                    $guest_name = isset( $guest_options[0]['name'] ) ? $guest_options[0]['name'] : '';

	                    // Get guest prices
	                    $guest_prices = $this->get_meta_value( 'specific_'.$guest_name.'_price' );
	                    $guest_prices = ovabrw_get_meta_data( $i, $guest_prices, [] );

	                    if ( ovabrw_array_exists( $guest_prices ) ) {
	                        foreach ( $guest_prices as $dayofweek => $prices ) {
	                            if ( ovabrw_array_exists( $prices ) ) {
	                                switch ( $daily_price_type ) {
	                                    case 'highest':
	                                        $daily_prices[$dayofweek] = OVABRW()->options->get_calendar_price_html(max( $prices ) );
	                                        break;
	                                    case 'average':
	                                        $daily_prices[$dayofweek] = OVABRW()->options->get_calendar_price_html( array_sum( $prices ) / count( $prices ) );
	                                        break;
	                                    case 'lowest':
	                                        $daily_prices[$dayofweek] = OVABRW()->options->get_calendar_price_html( min( $prices ) );
	                                        break;
	                                    default:
	                                        // code...
	                                        break;
	                                }
	                            }
	                        }
	                    }
	                } // END daily prices

	                if ( !$timeslots_start || !$timeslots_end || !$timeslots_max_guests ) {
	                    $specific_time[] = [
	                        'from'          => (int)gmdate( 'md', $from_date ),
	                        'to'            => (int)gmdate( 'md', $to_date ),
	                        'disabled'      => true,
	                        'dailyPrices'   => $daily_prices
	                    ];
	                } else {
	                    // Disabled weekdays
	                    $disable_weekdays = [];

	                    foreach ( $weekdays as $number_day => $string_day ) {
	                        // Start times
	                        $start_times = ovabrw_get_meta_data( $string_day, $timeslots_start );

	                        // End times
	                        $end_times = ovabrw_get_meta_data( $string_day, $timeslots_end );

	                        // Max guests
	                        $max_guests = ovabrw_get_meta_data( $string_day, $timeslots_max_guests );

	                        if ( !$start_times || !$end_times || !$max_guests ) {
	                            $disable_weekdays[] = (string)$number_day;
	                        } else {
	                            $is_blocked = true;

	                            foreach ( $start_times as $k => $start_time ) {
	                                $end_time   = (int)ovabrw_get_meta_data( $k, $end_times );
	                                $max_guest  = (int)ovabrw_get_meta_data( $k, $max_guests );

	                                if ( !$start_time || !$end_time || !$max_guest ) continue;

	                                // Unblocked
	                                $is_blocked = false;
	                                break;
	                            }

	                            // is blocked
	                            if ( $is_blocked ) {
	                                $disable_weekdays[] = (string)$number_day;
	                            }
	                        }

	                        // Get enable disable weekdays
	                        $product_disable_weekdays = $this->get_disable_weekdays();
	                        if ( ovabrw_array_exists( $product_disable_weekdays ) ) {
	                        	$disable_weekdays = array_unique( array_merge( $disable_weekdays, $product_disable_weekdays ) );
	                        }
	                    } // END disabled weekdays

	                    $specific_time[] = [
	                        'from'              => (int)gmdate( 'md', $from_date ),
	                        'to'                => (int)gmdate( 'md', $to_date ),
	                        'disabledWeekDays'  => $disable_weekdays,
	                        'dailyPrices'       => $daily_prices
	                    ];
	                }
	            }
	        }

	        return apply_filters( $this->prefix.'get_specific_time', $specific_time, $this );
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

			// Get disable weekdays by timeslots
	        if ( $this->is_timeslots() ) {
	        	$disable_weekdays = $this->get_disable_weekdays_by_timeslots( $disable_weekdays );
	        }

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

	    	// Loop
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
	    		} // END loop
	    	} // END if

	    	// is period
	    	if ( $this->is_period_time() ) {
	    		// Get period start
	    		$period_start = $this->get_meta_value( 'period_start' );
	    		if ( ovabrw_array_exists( $period_start ) ) {
	    			// Get period end
		    		$period_end = $this->get_meta_value( 'period_end' );

		    		// Get period max guest
		    		$period_max_guests = $this->get_meta_value( 'period_max_guests' );

	    			foreach ( $period_start as $k => $start_date ) {
	    				// Get start date
	    				$start_date = strtotime( $start_date );
	    				if ( !$start_date ) continue;

	    				// Get end date
	    				$end_date = strtotime( ovabrw_get_meta_data( $k, $period_end ) );
	    				if ( !$end_date ) continue;

	    				// Get max guests
	    				$max_guests = (int)ovabrw_get_meta_data( $k, $period_max_guests );
	    				if ( !$max_guests ) continue;

	    				// Add start date to allowed date
	    				$start = gmdate( $date_format, $start_date );
	    				if ( !in_array( $start, $allowed_dates ) ) {
	    					array_push( $allowed_dates, $start );
	    				}

	    				// Between days
	    				while ( $start_date < $end_date ) {
	    					$start_date += 86400; // +1day

	    					// Day string
	    					$day_str = gmdate( $date_format, $start_date );
	    					if ( !in_array( $day_str, $allowed_dates ) ) {
		    					array_push( $allowed_dates, $day_str );
		    				}
	    				}

	    				// Add end date to allowed date
	    				$end = gmdate( $date_format, $end_date );
	    				if ( !in_array( $end, $allowed_dates ) ) {
	    					array_push( $allowed_dates, $end );
	    				}
	    			} // END loop
	    		} // END if
	    	}

	    	return apply_filters( $this->prefix.'get_allowed_dates', $allowed_dates, $date_format, $this );
	    }

	    /**
	     * Get booked dates
	     * @param 	string 	$views
	     * @return 	array 	$booked_dates
	     */
	    public function get_booked_dates( $view = '' ) {
	    	// Date format
    		$date_format = OVABRW()->options->get_date_format();
    		if ( 'calendar' === $view ) $date_format = 'Y-m-d';

    		// Full day
	    	$full_day = [];

	    	// Get booked dates from order
	        $order_booked_dates = $this->get_booked_dates_from_order();

	        // Get booked dates from cart
	        $cart_booked_dates = $this->get_booked_dates_from_cart();
	        if ( ovabrw_array_exists( $cart_booked_dates ) ) {
	            foreach ( $cart_booked_dates as $timestamp => $numberof_guests ) {
	                if ( array_key_exists( $timestamp, $order_booked_dates ) ) {
	                    $order_booked_dates[$timestamp] += (int)$numberof_guests;
	                } else {
	                    $order_booked_dates[$timestamp] = (int)$numberof_guests;
	                }
	            }
	        }

	        // Get booked dates
	        if ( ovabrw_array_exists( $order_booked_dates ) ) {
	        	// Durataion: Fixed
	        	if ( $this->is_fixed_date() ) {
	        		// Get max number of guests
	                $max_guests = (int)$this->get_meta_value( 'max_guest' );

	                // Loop
	                foreach ( $order_booked_dates as $date => $numberof_guests ) {
	                    // Check number of available guests
	                    if ( $numberof_guests < $max_guests ) continue;

	                    // Convert string date
	                    $str_date = gmdate( $date_format, $date );
	                    if ( !in_array( $str_date, $full_day ) ) {
	                        $full_day[] = $str_date;
	                    }
	                } // END loop
	        	} elseif ( $this->is_timeslots() ) {
	        		// Loop
	                foreach ( $order_booked_dates as $date => $numberof_guests ) {
	                    // Get max number of guests
	                    $max_guests = $this->get_max_guests_by_date( $date );

	                    // Check number of available guests
	                    if ( $max_guests && $numberof_guests < $max_guests ) continue;

	                    // Convert string date
	                    $str_date = gmdate( $date_format, $date );
	                    if ( !in_array( $str_date, $full_day ) ) {
	                        $full_day[] = $str_date;
	                    }
	                } // END loop
	        	}
	        } // END if

	    	return apply_filters( $this->prefix.'get_booked_dates', [
	    		'full_day' => $full_day,
	    		'part_day' => []
	    	], $view, $this );
	    }

	    /**
	     * Get max guests by date
	     * @param 	int 	$date
	     * @return 	int 	$numberof_guests
	     */
	    public function get_max_guests_by_date( $date ) {
	    	// init
	    	$numberof_guests = 0;

	    	// Specific times
	        $specific_times = $this->in_specific_times( $date );
	        if ( ovabrw_array_exists( $specific_times ) ) {
	        	// Get time slots start
		    	$timeslots_start = ovabrw_get_meta_data( 'timeslots_start', $specific_times, [] );

		    	// Get time slots end
		    	$timeslots_end = ovabrw_get_meta_data( 'timeslots_end', $specific_times, [] );

		    	// Get max guests
	            $timeslots_max_guests = ovabrw_get_meta_data( 'max_guests', $specific_times, [] );
	        } else {
	        	// Get time slots start
		    	$timeslots_start = $this->get_meta_value( 'tour_timeslots_start', [] );

		    	// Get time slots end
		    	$timeslots_end = $this->get_meta_value( 'tour_timeslots_end', [] );

		    	// Get max guests
		    	$timeslots_max_guests = $this->get_meta_value( 'tour_timeslots_max_guests', [] );
	        }

        	// Day of week
        	$dayofweek = OVABRW()->options->get_string_dayofweek( $date );

        	// Current time
        	$current_time = current_time( 'timestamp' );

        	// is today
        	if ( strtotime( gmdate( 'Y-m-d', $date ) ) == strtotime( gmdate( 'Y-m-d', $current_time ) ) ) {
	            // Start times
	            $start_times = ovabrw_get_meta_data( $dayofweek, $timeslots_start );

	            // End times
	            $end_times = ovabrw_get_meta_data( $dayofweek, $timeslots_end );

	            // Max number of guests
	            $max_guests = ovabrw_get_meta_data( $dayofweek, $timeslots_max_guests );

	            // Date & time format
	            $date_format = OVABRW()->options->get_date_format();
	            $time_format = OVABRW()->options->get_time_format();

	            foreach ( $start_times as $k => $start_time ) {
	                if ( !$start_time ) continue;
	                $start_date = strtotime( gmdate( $date_format, $current_time ).' '.gmdate( $time_format, $start_time ) );
	                if ( !$start_date || $start_date < $current_time ) continue;

	                // Get end time
	                $end_time = (int)ovabrw_get_meta_data( $k, $end_times );
	                if ( !$end_time ) continue;
	                $end_date = strtotime( gmdate( $date_format, $current_time ).' '.gmdate( $time_format, $end_time ) );
	                if ( !$end_date || $end_date < $current_time ) continue;

	                // Get item max number of guests
	                $item_max_guests = (int)ovabrw_get_meta_data( $k, $max_guests );
	                if ( !$item_max_guests ) continue;
	                
	                // Number of guests
	                $numberof_guests += $item_max_guests;
	            }
	        } else {
	            // Get max guests by day of week
	            $max_guests = ovabrw_get_meta_data( $dayofweek, $timeslots_max_guests );
	            if ( ovabrw_array_exists( $max_guests ) ) $numberof_guests = array_sum( $max_guests );
	        }

	    	return apply_filters( $this->prefix.'get_max_guests_by_date', $numberof_guests, $date, $this );
	    }

	    /**
	     * Get booked dates from order
	     */
	    public function get_booked_dates_from_order() {
	    	// init
	        $booked_dates = [];

	        // Booked dates from order queues table
	    	if ( OVABRW()->options->is_order_queues_completed() ) {
	    		$order_queues = OVABRW()->options->get_order_queues_data( $this->get_id() );
	    		if ( ovabrw_array_exists( $order_queues ) ) {
	    			// Date format
			        $date_format = OVABRW()->options->get_date_format();

			        // Current time
			        $current_time = current_time( 'timestamp' );
			        if ( !$this->is_timeslots() ) {
			            $current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );
			        }

			        // Loop
			        foreach ( $order_queues as $order ) {
			        	// Get pick-up date
	    				$pickup_date = (int)ovabrw_get_meta_data( 'pickup_date', $order );
	    				if ( !$pickup_date || $pickup_date < $current_time ) continue;

	    				// Get drop-off date
	    				$dropoff_date = (int)ovabrw_get_meta_data( 'dropoff_date', $order );
	    				if ( !$dropoff_date || $dropoff_date < $current_time ) continue;

	    				// Convert check-in and checkout date by date format
                        $timestamp = strtotime( gmdate( $date_format, $pickup_date ) );

                        // Get item
                        $item = WC_Order_Factory::get_order_item( absint( ovabrw_get_meta_data( 'item_id', $order ) ) );
                        if ( !$item ) continue;

                        // Add checkin date
                        if ( array_key_exists( $timestamp, $booked_dates ) ) {
                            $booked_dates[$timestamp] += (int)$item->get_meta( 'ovabrw_numberof_guests' );
                        } else {
                            $booked_dates[$timestamp] = (int)$item->get_meta( 'ovabrw_numberof_guests' );
                        } // END if
			        } // END loop
	    		}
	    	} else {
		        // Get order booked ids
		    	$order_ids = OVABRW()->options->get_order_booked_ids( $this->get_id() );
		        if ( ovabrw_array_exists( $order_ids ) ) {
		        	// Date format
			        $date_format = OVABRW()->options->get_date_format();

			        // Current time
			        $current_time = current_time( 'timestamp' );
			        if ( !$this->is_timeslots() ) {
			            $current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );
			        }

			        // Get product ids multi language
	    			$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

		        	foreach ( $order_ids as $order_id ) {
		                // Get order
		                $order = wc_get_order( $order_id );
		                if ( !$order ) continue;

		                // Get items
		                $items = $order->get_items();
		                if ( !ovabrw_array_exists( $items ) ) continue;

		                // Loop items
		                foreach ( $items as $item_id => $item ) {
		                	// Get product id
		                    $product_id = $item->get_product_id();
		                    if ( !in_array( $product_id, $product_ids ) ) continue;

	                        // Pick-up date
	                        $pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
	                        if ( !$pickup_date || $pickup_date < $current_time ) continue;

	                        // Drop-off date
	                        $checkout_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
	                        if ( !$checkout_date || $checkout_date < $current_time ) continue;

	                        // Convert check-in and checkout date by date format
	                        $timestamp = strtotime( gmdate( $date_format, $pickup_date ) );

	                        // Add checkin date
	                        if ( array_key_exists( $timestamp, $booked_dates ) ) {
	                            $booked_dates[$timestamp] += (int)$item->get_meta( 'ovabrw_numberof_guests' );
	                        } else {
	                            $booked_dates[$timestamp] = (int)$item->get_meta( 'ovabrw_numberof_guests' );
	                        } // END if
		                } // END loop items
		            } // END loop order ids
		        } // END if
		    }

	        return apply_filters( $this->prefix.'get_booked_dates_from_order', $booked_dates, $this );
	    }

	    /**
	     * Get booked date from Cart
	     */
	    public function get_booked_dates_from_cart() {
	        // init
	        $booked_dates = [];

	        if ( !is_admin() && ovabrw_array_exists( WC()->cart->get_cart() ) ) {
	            // Get product ids multi language
	    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

	            // Date format
	            $date_format = OVABRW()->options->get_date_format();

	            // Current time
		        $current_time = current_time( 'timestamp' );
		        if ( !$this->is_timeslots() ) {
		            $current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );
		        }

	            // Loop
	            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
	            	// Get product id
	                $product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
	                if ( !in_array( $product_id, $product_ids ) ) continue;

                    // Pick-up date
                    $pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );
                    if ( !$pickup_date ) continue;

                    // Drop-off date
                    $dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );
                    if ( !$dropoff_date ) continue;

                    // Check current time
                    if ( $pickup_date < $current_time || $dropoff_date < $current_time ) {
                        WC()->cart->remove_cart_item( $cart_item_key );
                    }

                    // Convert check-in and checkout date by date format
                    $timestamp = strtotime( gmdate( $date_format, $pickup_date ) );

                    // Add checkin date
                    if ( array_key_exists( $timestamp, $booked_dates ) ) {
                        $booked_dates[$timestamp] += (int)ovabrw_get_meta_data( 'numberof_guests', $cart_item );
                    } else {
                        $booked_dates[$timestamp] = (int)ovabrw_get_meta_data( 'numberof_guests', $cart_item );
                    }
	            } // END loop cart
	        } // END if

	        return apply_filters( $this->prefix.'get_booked_dates_from_cart', $booked_dates, $this );
	    }

	    /**
	     * Get disabled weekdays by timeslots
	     */
	    public function get_disable_weekdays_by_timeslots( $disable_weekdays ) {
	    	// Get time slots start
	        $timeslots_start = $this->get_meta_value( 'tour_timeslots_start', [] );

	        // Get time slots end
	        $timeslots_end = $this->get_meta_value( 'tour_timeslots_end', [] );

	        // Get time slots max guests
	        $timeslots_max_guests = $this->get_meta_value( 'tour_timeslots_max_guests', [] );

	        // Weekdays array
	        $weekdays = [
	            '1' => 'monday',
	            '2' => 'tuesday',
	            '3' => 'wednesday',
	            '4' => 'thursday',
	            '5' => 'friday',
	            '6' => 'saturday',
	            '7' => 'sunday'
	        ];

	        // Loop weekdays
	        foreach ( $weekdays as $number_day => $string_day ) {
	            if ( !in_array( $number_day, $disable_weekdays ) ) {
	                $start_times    = ovabrw_get_meta_data( $string_day, $timeslots_start, [] );
	                $end_times      = ovabrw_get_meta_data( $string_day, $timeslots_end, [] );
	                $max_guests     = ovabrw_get_meta_data( $string_day, $timeslots_max_guests, [] );

	                if ( !$start_times || !$end_times || !$max_guests ) {
	                    $disable_weekdays[] = (string)$number_day;
	                } else {
	                    $is_blocked = true;

	                    foreach ( $start_times as $k => $start_time ) {
	                        $end_time   = (int)ovabrw_get_meta_data( $k, $end_times );
	                        $max_guest  = (int)ovabrw_get_meta_data( $k, $max_guests );

	                        if ( !$start_time || !$end_time || !$max_guest ) continue;

	                        // Unblocked
	                        $is_blocked = false;
	                        break;
	                    }

	                    // is blocked
	                    if ( $is_blocked ) {
	                        $disable_weekdays[] = (string)$number_day;
	                    }
	                }
	            }
	        } // END loop weekdays

	        // Today
	        $current_time = current_time( 'timestamp' );

	        // Day of week
	        $dayofweek = OVABRW()->options->get_string_dayofweek( $current_time );
	        
	        // Get specific times
			$specific_times = $this->in_specific_times( $current_time );
			if ( ovabrw_array_exists( $specific_times ) ) {
				// Get today start
		        $timeslots_start = ovabrw_get_meta_data( 'timeslots_start', $specific_times, [] );

		        // Get today end
		        $timeslots_end = ovabrw_get_meta_data( 'timeslots_end', $specific_times, [] );

		        // Get totay max guests
		        $timeslots_max_guests = ovabrw_get_meta_data( 'timeslots_end', $specific_times, [] );
			}

	        // Get today start
	        $today_start = ovabrw_get_meta_data( $dayofweek, $timeslots_start, [] );

	        // Get today end
	        $today_end = ovabrw_get_meta_data( $dayofweek, $timeslots_end, [] );

	        // Get totay max guests
	        $today_max_guests = ovabrw_get_meta_data( $dayofweek, $timeslots_max_guests, [] );

	        if ( !$today_start || !$today_end || !$today_max_guests ) {
	            $disable_weekdays[] = 'today';
	        } else {
	            // Time format
	            $time_format = OVABRW()->options->get_time_format();

	            // Date format
	            $date_format = OVABRW()->options->get_date_format();

	            // Today blocked
	            $today_blocked = true;

	            foreach ( $today_start as $k => $start_time ) {
	                $end_time   = (int)ovabrw_get_meta_data( $k, $today_end );
	                $max_guests = (int)ovabrw_get_meta_data( $k, $today_max_guests );

	                if ( !$start_time || !$end_time || !$max_guests ) continue;

	                // Convert start time
	                $start_time = strtotime( gmdate( $date_format, $current_time ).' '.gmdate( $time_format, $start_time ) );
	                if ( $start_time < $current_time ) continue;

	                // Convert end time
	                $end_time = strtotime( gmdate( $date_format, $current_time ).' '.gmdate( $time_format, $end_time ) );
	                if ( $end_time < $current_time ) continue;

	                // Unblocked
	                $today_blocked = false;
	                break;
	            }

	            // is blocked
	            if ( $today_blocked ) {
	                $disable_weekdays[] = 'today';
	            }
	        }

	        return apply_filters( $this->prefix.'get_disable_weekdays_by_timeslots', $disable_weekdays, $this );
	    }

	    /**
	     * Get disabled dates
	     */
	    public function get_disabled_dates( $view = '' ) {
	    	// Disabled full day
	    	$full_day = [];

	    	// Disabled part of day
	    	$part_day = [];

	    	// From dates
	    	$from_dates = $this->get_meta_value( 'untime_startdate' );

	    	// To dates
	    	$to_dates = $this->get_meta_value( 'untime_enddate' );

	    	if ( ovabrw_array_exists( $from_dates ) ) {
	    		// Date format
	    		$date_format = OVABRW()->options->get_date_format();
	    		if ( 'calendar' === $view ) $date_format = 'Y-m-d';

	    		foreach ( $from_dates as $k => $from ) {
	    			// From date
	    			$from = strtotime( $from );
	    			if ( !$from ) continue;

	    			// To date
	    			$to = strtotime( ovabrw_get_meta_data( $k, $to_dates ) );
	    			if ( !$to || $to < current_time( 'timestamp' ) ) continue;

	    			// Number of days between
	    			$days_between = ovabrw_numberof_days_between( $from, $to );

	    			if ( 0 == $days_between ) {
    					$full_day[] = gmdate( $date_format, $from );
    				} elseif ( 1 == $days_between ) {
    					$full_day[] = gmdate( $date_format, $from );
    					$full_day[] = gmdate( $date_format, $to );
    				} else {
    					// Get date range
    					$date_range = ovabrw_get_date_range( $from, $to, $date_format );
    					if ( ovabrw_array_exists( $date_range ) ) {
    						$full_day = ovabrw_array_merge_unique( $full_day, $date_range );
    					}
    				}
    			}
	    	}

	    	// Results
	    	$results = [
	    		'full_day' => $full_day,
	    		'part_day' => $part_day
	    	];

	    	return apply_filters( $this->prefix.'get_disabled_dates', $results, $view, $this );
	    }

	    /**
	     * Get calendar daily prices - only apply for duration: One-day Tour
	     */
	    public function get_calendar_daily_prices() {
	        // init
	        $daily_prices = [];

	        if ( $this->is_timeslots() ) {
	        	// Number days of the week
		    	$number_daysofweek = [
		    		'monday' 	=> 1,
		    		'tuesday' 	=> 2,
		    		'wednesday' => 3,
		    		'thursday' 	=> 4,
		    		'friday' 	=> 5,
		    		'saturday' 	=> 6,
		    		'sunday' 	=> 7
		    	];

                // Daily price type
                $daily_price_type = apply_filters( OVABRW_PREFIX.'daily_price_type', 'highest' );

                // Guest options
                $guest_options  = $this->get_guest_options();
                $guest_name     = isset( $guest_options[0]['name'] ) ? $guest_options[0]['name'] : '';

                // Get guest prices
                $guest_prices = $this->get_meta_value( 'tour_timeslots_'.$guest_name.'_price' );
                
                if ( ovabrw_array_exists( $guest_prices ) ) {
                    foreach ( $guest_prices as $dayofweek => $prices ) {
                    	// Get number dayofweek
                    	$number_day = ovabrw_get_meta_data( $dayofweek, $number_daysofweek );
                    	if ( !$number_day ) continue;

                    	// Get daili price
                        if ( ovabrw_array_exists( $prices ) ) {
                        	switch ( $daily_price_type ) {
                                case 'highest':
                                    $daily_prices[$number_day] = OVABRW()->options->get_calendar_price_html(max( $prices ) );
                                    break;
                                case 'average':
                                    $daily_prices[$number_day] = OVABRW()->options->get_calendar_price_html( array_sum( $prices ) / count( $prices ) );
                                    break;
                                case 'lowest':
                                    $daily_prices[$number_day] = OVABRW()->options->get_calendar_price_html( min( $prices ) );
                                    break;
                                default:
                                    // code...
                                    break;
                            } // END switch
                        } // END if
                    } // END foreach
                } // END if
	        } // END if

	        return apply_filters( $this->prefix.'get_calendar_daily_prices', $daily_prices, $this );
	    }

	    /**
	     * Get calendar special prices
	     */
	    public function get_calendar_special_prices() {
	        // init
	        $special_prices = [];

	        // Get special dates from
	        $special_from = $this->get_meta_value( 'special_from' );
	        if ( ovabrw_array_exists( $special_from ) ) {
	            // Guest options
	            $guest_options  = $this->get_guest_options();
	            $guest_name     = isset( $guest_options[0]['name'] ) ? $guest_options[0]['name'] : '';

	            // Get special dates to
	            $special_to = $this->get_meta_value( 'special_to' );

	            // Get special price
	            $special_guest_price = $this->get_meta_value( 'special_'.$guest_name.'_price' );

	            foreach ( $special_from as $k => $from ) {
	                $to     = ovabrw_get_meta_data( $k, $special_to );
	                $price  = ovabrw_get_meta_data( $k, $special_guest_price );
	                if ( !$from || !$to || '' == $price ) continue;

	                $special_prices[] = [
	                    'from'  => (int)gmdate( 'md', $from ),
	                    'to'    => (int)gmdate( 'md', $to ),
	                    'price' => OVABRW()->options->get_calendar_price_html( $price )
	                ];
	            }
	        }

	        return apply_filters( $this->prefix.'get_calendar_special_prices', $special_prices, $this );
	    }

	    /**
	     * Get calendar specific prices
	     */
	    public function get_calendar_specific_prices() {
	    	// init
	        $specific_prices = [];

	        // is period
	    	if ( $this->is_period_time() ) {
	    		// Get period start
	    		$period_start = $this->get_meta_value( 'period_start' );
	    		if ( ovabrw_array_exists( $period_start ) ) {
	    			// Guest options
		            $guest_options  = $this->get_guest_options();
		            $guest_name     = isset( $guest_options[0]['name'] ) ? $guest_options[0]['name'] : '';

		            // Get period end
		    		$period_end = $this->get_meta_value( 'period_end' );

		    		// Get period max guest
		    		$period_max_guests = $this->get_meta_value( 'period_max_guests' );

		    		// Get guest price
	            	$guest_price = $this->get_meta_value( 'period_'.$guest_name.'_price' );

	    			foreach ( $period_start as $k => $start_date ) {
	    				// Get start date
	    				$start_date = strtotime( $start_date );
	    				if ( !$start_date ) continue;

	    				// Get end date
	    				$end_date = strtotime( ovabrw_get_meta_data( $k, $period_end ) );
	    				if ( !$end_date ) continue;

	    				// Get max guests
	    				$max_guests = (int)ovabrw_get_meta_data( $k, $period_max_guests );
	    				if ( !$max_guests ) continue;

	    				// Get price
	    				$price = ovabrw_get_meta_data( $k, $guest_price );

		                $specific_prices[] = [
		                    'from'  => $start_date,
		                    'to'    => $end_date,
		                    'price' => OVABRW()->options->get_calendar_price_html( $price )
		                ];
	    			} // END loop
	    		} // END if
	    	}

	    	return apply_filters( $this->prefix.'get_calendar_specific_prices', $specific_prices, $this );
	    }

	    /**
	     * Get time slots
	     */
	    public function get_time_slots( $pickup_date ) {
	    	if ( !$pickup_date ) return false;

	    	// Get time slots from specific
			$specific_timeslots = $this->get_time_slots_from_specific_time( $pickup_date );
			if ( !is_bool( $specific_timeslots ) ) {
				return apply_filters( $this->prefix.'get_time_slots', $specific_timeslots, $pickup_date, $this );
			}

			// init time slots
			$time_slots = [];

			// Get string day of week
			$dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

			// Get time slots
			$timeslots_label 	= $this->get_meta_value( 'tour_timeslots_label', [] );
			$timeslots_start 	= $this->get_meta_value( 'tour_timeslots_start', [] );
			$timeslots_end 		= $this->get_meta_value( 'tour_timeslots_end', [] );
			$timeslots_guests 	= $this->get_meta_value( 'tour_timeslots_max_guests', [] );

			// Time slots by day of week
			$labels 		= ovabrw_get_meta_data( $dayofweek, $timeslots_label );
			$start_times 	= ovabrw_get_meta_data( $dayofweek, $timeslots_start );
			$end_times 		= ovabrw_get_meta_data( $dayofweek, $timeslots_end );
			$max_guests 	= ovabrw_get_meta_data( $dayofweek, $timeslots_guests );

			// Loop
			if ( ovabrw_array_exists( $start_times ) ) {
				$date_format = OVABRW()->options->get_date_format();
				$time_format = OVABRW()->options->get_time_format();

				foreach ( $start_times as $k => $start_time ) {
					// Disabled
					$disabled = false;

					// Get label
					$label = ovabrw_get_meta_data( $k, $labels );

					// Get end time
					$end_time = ovabrw_get_meta_data( $k, $end_times );

					if ( $start_time && $end_time ) {
						// Get string start date
						$start_date = OVABRW()->options->get_string_date( $pickup_date, $start_time );

						// Check start date > current time
						if ( !$start_date || strtotime( $start_date ) <= current_time( 'timestamp' ) ) {
							$disabled = true;
						}

						// Get string end date
						if ( $start_time > $end_time ) {
							// $end_date = $pickup_date + 1 day
							$end_date = OVABRW()->options->get_string_date( $pickup_date + 86400, $end_time );
						} else {
							$end_date = OVABRW()->options->get_string_date( $pickup_date, $end_time );
						}

						// Check end date > current time
						if ( !$disabled ) {
							if ( !$end_date || strtotime( $end_date ) <= current_time( 'timestamp' ) ) {
								$disabled = true;
							}
						}

						// Max guests
						$max_guest = (int)ovabrw_get_meta_data( $k, $max_guests );
						if ( !$max_guest ) $disabled = true;

						// Get label if not exists
						if ( !$label ) {
							$label = apply_filters( OVABRW_PREFIX.'get_time_slots_label', sprintf( esc_html__( '%s - %s', 'ova-brw' ), gmdate( $time_format, strtotime( $start_date ) ), gmdate( $time_format, strtotime( $end_date ) ) ) );
						}

						// Booking validation
	                    if ( !$disabled ) {
	                    	$booking_validation = $this->booking_validation( strtotime( $start_date ), strtotime( $end_date ) );
	                    	if ( $booking_validation ) $disabled = true;
	                    }

	                    // Get number of available guests
	                    if ( !$disabled ) {
	                    	$numberof_available_guests = $this->get_numberof_available_guests( strtotime( $start_date ), strtotime( $end_date ), 1 );
	                    	if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
								$disabled = true;
							}
	                    }

						$time_slots[] = [
							'label' 		=> $label,
							'start_date' 	=> strtotime( $start_date ),
							'end_date' 		=> strtotime( $end_date ),
							'disabled' 		=> $disabled
						];
					}
				}
			}

			return apply_filters( $this->prefix.'get_time_slots', $time_slots, $pickup_date, $this );
	    }

	    /**
	     * Get time slots from specific time
	     */
	    public function get_time_slots_from_specific_time( $pickup_date ) {
	    	if ( !$pickup_date ) return false;

	    	// Get pickup timestamp
	    	$pickup_timestamp = OVABRW()->options->convert_to_current_year( $pickup_date );

	    	// init time slots
			$time_slots = false;

			// Date range from
	        $from_dates = $this->get_meta_value( 'specific_from' );

	        // Date range to
	        $to_dates = $this->get_meta_value( 'specific_to' );

	        // Specific max guests
	        $specific_max_guests = $this->get_meta_value( 'specific_max_guests' );

	        // Loop
	        if ( ovabrw_array_exists( $from_dates ) ) {
	        	foreach ( $from_dates as $i => $from_date ) {
	        		// Get from date
	        		$from_date = (int)$from_date;
	        		$from_date = OVABRW()->options->convert_to_current_year( $from_date );
	        		if ( !$from_date ) continue;

	        		// Get to date
	        		$to_date = (int)ovabrw_get_meta_data( $i, $to_dates );
	        		$to_date = OVABRW()->options->convert_to_current_year( $to_date );
	        		if ( !$to_date ) continue;

	        		// Check pickup-date
	        		if ( $pickup_timestamp >= $from_date && $pickup_timestamp <= $to_date ) {
	        			// init time slots
	        			$time_slots = [];

	        			// Get specific label
	        			$specific_label = $this->get_meta_value( 'specific_label' );

	        			// Get specific start
			            $specific_start = $this->get_meta_value( 'specific_start' );

			            // Get specific end
			            $specific_end = $this->get_meta_value( 'specific_end' );

			            // Get string day of week
						$dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

						// Labels
						$labels = isset( $specific_label[$i][$dayofweek] ) ? $specific_label[$i][$dayofweek] : '';

						// Start times
						$start_times = isset( $specific_start[$i][$dayofweek] ) ? $specific_start[$i][$dayofweek] : '';

						// End times
						$end_times = isset( $specific_end[$i][$dayofweek] ) ? $specific_end[$i][$dayofweek] : '';

						// Max guests
						$max_guests = isset( $specific_max_guests[$i][$dayofweek] ) ? $specific_max_guests[$i][$dayofweek] : '';

			            if ( ovabrw_array_exists( $start_times ) ) {
							$date_format = OVABRW()->options->get_date_format();
							$time_format = OVABRW()->options->get_time_format();

							foreach ( $start_times as $k => $start_time ) {
								if ( !$start_time ) continue;

								// Get end time
								$end_time = ovabrw_get_meta_data( $k, $end_times );
								if ( !$end_time ) continue;

								// Get max guest
								if ( !(int)ovabrw_get_meta_data( $k, $max_guests ) ) continue;

								// Disabled
								$disabled = false;

								// Get label
								$label = ovabrw_get_meta_data( $k, $labels );

								// Get string start date
								$start_date = OVABRW()->options->get_string_date( $pickup_date, $start_time );

								// Check start date > current time
								if ( !$start_date || strtotime( $start_date ) <= current_time( 'timestamp' ) ) {
									$disabled = true;
								}

								// Get string end date
								if ( $start_time > $end_time ) {
									// $end_date = $pickup_date + 1 day
									$end_date = OVABRW()->options->get_string_date( $pickup_date + 86400, $end_time );
								} else {
									$end_date = OVABRW()->options->get_string_date( $pickup_date, $end_time );
								}

								// Check end date > current time
								if ( !$disabled ) {
									if ( !$end_date || strtotime( $end_date ) <= current_time( 'timestamp' ) ) {
										$disabled = true;
									}
								}

								// Get label if not exists
								if ( !$label ) {
									$label = apply_filters( OVABRW_PREFIX.'get_time_slots_label', sprintf( esc_html__( '%s - %s', 'ova-brw' ), gmdate( $time_format, strtotime( $start_date ) ), gmdate( $time_format, strtotime( $end_date ) ) ) );
								}

			                    // Booking validation
			                    if ( !$disabled ) {
			                    	$booking_validation = $this->booking_validation( strtotime( $start_date ), strtotime( $end_date ) );
			                    	if ( $booking_validation ) $disabled = true;
			                    }

			                    // Get number of available guests
			                    if ( !$disabled ) {
			                    	$numberof_available_guests = $this->get_numberof_available_guests( strtotime( $start_date ), strtotime( $end_date ), 1 );
			                    	if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
										$disabled = true;
									}
			                    }

								$time_slots[] = [
									'label' 		=> $label,
									'start_date' 	=> strtotime( $start_date ),
									'end_date' 		=> strtotime( $end_date ),
									'disabled' 		=> $disabled
								];
							} // END loop
						} // END if

	        			break; // Break loop
	        		}
	        	}
	        } // END

			return apply_filters( $this->prefix.'get_time_slots_from_specific_time', $time_slots, $pickup_date, $this );
	    }

	    /**
		 * Get time slots HTML
		 */
		public function get_time_slots_html( $time_slots = [], $name = '' ) {
			// init html
			$html = '';

			// Default dates
			$default_start = $default_end = '';

			if ( ovabrw_array_exists( $time_slots ) ) {
				if ( !$name ) $name = $this->get_meta_name( 'start_time' );

				// Date format
				$date_format = OVABRW()->options->get_date_format();

				// Time format
				$time_format = OVABRW()->options->get_time_format();

				// Have active
				$have_active = false;

				ob_start();

				foreach ( $time_slots as $time_slot ):
					// init class
					$class = '';

					// is active
					if ( !$have_active && !$time_slot['disabled'] ) {
						$have_active 	= true;
						$class 			= 'active';
						$default_start 	= $time_slot['start_date'];
						$default_end 	= $time_slot['end_date'];
					}

					// is disabled
					if ( $time_slot['disabled'] ) {
						$class = 'disabled';
					}
				?>
					<label class="time-slot <?php echo esc_attr( $class ); ?>">
						<?php echo esc_html( $time_slot['label'] ); ?>
						<input
							type="radio"
							class="ovabrw-start-time"
							name="<?php echo esc_attr( $name ); ?>"
							value="<?php echo esc_attr( gmdate( $time_format, $time_slot['start_date'] ) ); ?>"
							data-end-date="<?php echo esc_attr( gmdate( $date_format.' '.$time_format, $time_slot['end_date'] ) ); ?>"
							<?php checked( 'active', $class ); ?>
							<?php disabled( 'disabled', $class ); ?>
						/>
					</label>
				<?php endforeach;

				$html = ob_get_contents();
				ob_end_clean();
			}

			return apply_filters( $this->prefix.'get_time_slots_html', [
				'html' 			=> $html,
				'default_start' => $default_start,
				'default_end' 	=> $default_end,
			], $time_slots, $name, $this );
		}

		/**
		 * Get time periods
		 */
		public function get_time_periods( $next_points = 0 ) {
			// init time periods
			$time_periods = [];

			// is load more
			$is_load_more = false;

			// Get time periods
			$period_labels 	= $this->get_meta_value( 'period_label' );
			$start_dates 	= $this->get_meta_value( 'period_start' );
			$end_dates 		= $this->get_meta_value( 'period_end' );
			$max_guests 	= $this->get_meta_value( 'period_max_guests' );

			// Loop
			if ( ovabrw_array_exists( $start_dates ) && ovabrw_array_exists( $end_dates ) ) {
				// Date format
				$date_format = OVABRW()->options->get_date_format();

				// Current time
				$current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );

				// Limited time periods
				$limited = (int)apply_filters( OVABRW_PREFIX.'limited_time_periods', 5 );

				if ( $limited ) {
					// Get from next points
					if ( $next_points ) {
						$is_load_more 	= true;
	                    $period_labels 	= array_slice( $period_labels, $next_points );
	                    $start_dates  	= array_slice( $start_dates, $next_points );
	                    $end_dates 		= array_slice( $end_dates, $next_points );
					}

					// Loop
					foreach ( $start_dates as $k => $start_date ) {
						$next_points += 1;

						// Disabled
						$disabled = false;

						// Start date
						$start_date = strtotime( $start_date );
						if ( !$start_date || $start_date <= $current_time ) {
							$disabled = true;
						}

						// END date
						$end_date = strtotime( ovabrw_get_meta_data( $k, $end_dates ) );
						if ( !$end_date || $end_date <= $current_time || $end_date < $start_date ) {
							$disabled = true;
						}

						// Max guest
						$max_guest = (int)ovabrw_get_meta_data( $k, $max_guests );
						if ( !$max_guest ) $disabled = true;

						// Validation
	                    if ( !$disabled ) {
	                    	$validation_booking = $this->booking_validation( $start_date, $end_date );
	                    	if ( $validation_booking ) $disabled = true;
	                    }

	                    // Get number of available guests
	                    if ( !$disabled ) {
	                    	$numberof_available_guests = $this->get_numberof_available_guests( $start_date, $end_date, 1 );
	                    	if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
								$disabled = true;
							}
	                    }
						
						// Period label
						$label = ovabrw_get_meta_data( $k, $period_labels, sprintf( esc_html__( 'From %s to %s', 'ova-brw' ), gmdate( $date_format, $start_date ), gmdate( $date_format, $end_date ) ) );

						// Add time period
						$time_periods[] = [
							'start' 	=> $start_date,
							'end' 		=> $end_date,
							'label' 	=> $label,
							'disabled' 	=> $disabled
						];

						// Break loop
						if ( count( $time_periods ) === $limited ) break;
					} // END loop
					
					// Loading more option
	                if ( ovabrw_get_meta_data( $k+1, $start_dates ) ) {
	                    $time_periods[] = [
	                    	'next' 	=> $next_points,
	                    	'label' => esc_html__( 'more...', 'ova-brw' )
	                    ];
	                }
				} else {
					// Loop
					foreach ( $start_dates as $k => $start_date ) {
						// Disabled
						$disabled = false;

						// Start date
						$start_date = strtotime( $start_date );
						if ( !$start_date || $start_date <= $current_time ) $disabled = true;

						// End date
						$end_date = strtotime( ovabrw_get_meta_data( $k, $end_dates ) );
						if ( !$end_date || $end_date <= $current_time || $end_date < $start_date ) $disabled = true;

	                    // Validation
	                    if ( !$disabled ) {
	                    	$validation_booking = $this->booking_validation( $start_date, $end_date );
	                    	if ( $validation_booking ) $disabled = true;
	                    }

	                    // Get number of available guests
	                    if ( !$disabled ) {
	                    	$numberof_available_guests = $this->get_numberof_available_guests( $start_date, $end_date, 1 );
	                    	if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
								$disabled = true;
							}
	                    }

						// Period label
						$label = ovabrw_get_meta_data( $k, $period_labels, sprintf( esc_html__( 'From %s to %s', 'ova-brw' ), gmdate( $date_format, $start_date ), gmdate( $date_format, $end_date ) ) );

						// Add time period
						$time_periods[] = [
							'start' 	=> $start_date,
							'end' 		=> $end_date,
							'label' 	=> $label,
							'disabled' 	=> $disabled
						];
					} // END loop
				} // END if
			} // END loop

			// No time periods
			if ( !ovabrw_array_exists( $time_periods ) && !$is_load_more ) {
				$time_periods[] = [
					'label' => esc_html__( 'There are no time periods available', 'ova-brw' )
				];
			}

			return apply_filters( $this->prefix.'get_time_periods', $time_periods, $next_points, $this );
		}

		/**
	     * Get new date
	     */
	    public function get_new_date( $args = [] ) {
	    	// init
			$new_date = [
				'pickup_date' 	=> false,
				'dropoff_date' 	=> false
			];

			// Get pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $args );
			if ( !$pickup_date ) {
				return apply_filters( $this->prefix.'get_new_date', $new_date, $args );
			}

			// Get drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $args );

			// Get start time
			$start_time = ovabrw_get_meta_data( 'start_time', $args );

			// Date format
			$date_format = OVABRW()->options->get_date_format();

			// Time format
			$time_format = OVABRW()->options->get_time_format();

			// Get duration
			if ( $this->is_timeslots() && $start_time ) {
				// New pick-up date
				$new_pickup_date = strtotime( gmdate( $date_format, $pickup_date ).' '.gmdate( $time_format, $start_time ) );
				if ( $new_pickup_date ) {
					$new_date['pickup_date'] = gmdate( $date_format.' '.$time_format, $new_pickup_date );
				}

				// Drop-off date
				if ( !$dropoff_date && $new_pickup_date ) {
					// Get number of hours
					$numberof_hours = (int)$this->get_meta_value( 'number_hours', 1 );

					// New drop-off date
					$new_dropoff_date = $new_pickup_date + $numberof_hours*3600;
					if ( $new_dropoff_date ) {
						$new_date['dropoff_date'] = gmdate( $date_format.' '.$time_format, $new_dropoff_date );
					}
				} else {
					$new_date['dropoff_date'] = gmdate( $date_format.' '.$time_format, $dropoff_date );
				} // END if
			} elseif ( $this->is_fixed_date() ) {
				// New pick-up date
				$new_pickup_date = strtotime( gmdate( $date_format, $pickup_date ) );
				if ( $new_pickup_date ) {
					$new_date['pickup_date'] = gmdate( $date_format, $new_pickup_date );
				}

				// Drop-off date
				if ( !$dropoff_date && $new_pickup_date ) {
					// Get number of days
					$numberof_days = (int)$this->get_meta_value( 'numberof_days' );
					if ( $numberof_days ) $numberof_days -= 1;

					// New drop-off date
					$new_dropoff_date = $new_pickup_date + $numberof_days*86400;
					if ( $new_dropoff_date ) {
						$new_date['dropoff_date'] = gmdate( $date_format, $new_dropoff_date );
					}
				} else {
					$new_date['dropoff_date'] = gmdate( $date_format, $dropoff_date );
				} // END if
			} else {
				// New pick-in date
				$new_pickup_date = strtotime( gmdate( $date_format, $pickup_date ) );
				if ( $new_pickup_date ) {
					$new_date['pickup_date'] = gmdate( $date_format, $new_pickup_date );
				}

				// Drop-off date
				if ( !$dropoff_date && $new_pickup_date ) {
					$new_date['dropoff_date'] = gmdate( $date_format, $new_pickup_date );
				} else {
					$new_date['dropoff_date'] = gmdate( $date_format, $dropoff_date );
				} // END if
			} // END if

	    	return apply_filters( $this->prefix.'get_new_date', $new_date, $args );
	    }

	    /**
	     * Booking validation
	     * @param 	int 			$pickup_date
	     * @param 	int 			$dropoff_date
	     * @param 	array 			$args
	     * @return 	string|boolead 	$mesg
	     */
	    public function booking_validation( $pickup_date, $dropoff_date, $args = [] ) {
	    	// Hook name
	    	$hook_name = $this->prefix.'booking_validation';

	    	// Pick-up date
	    	$pickup_label = $this->product->get_date_label();
	    	if ( !$pickup_date ) {
	    		$mesg = sprintf( esc_html__( '%s is required', 'ova-brw' ), $pickup_label );
	    		return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
	    	}

	    	// Current time
			$current_time = $this->get_current_time();
			if ( $pickup_date < $current_time ) {
				$mesg = sprintf( esc_html__( '%s must be greater than current time', 'ova-brw' ), $pickup_label );
	    		return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

	    	// Drop-off date
	    	$dropoff_label = $this->product->get_date_label( 'dropoff' );
	    	if ( !$dropoff_date ) {
				$mesg = sprintf( esc_html__( '%s is required', 'ova-brw' ), $dropoff_label );
	    		return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Pick-up & Drop-off dates
			if ( $pickup_date > $dropoff_date ) {
				$mesg = sprintf( esc_html__( '%s must be greater than %s', 'ova-brw' ), $dropoff_label, $pickup_label );
	    		return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Preparation time
			$mesg = $this->preparation_time_validation( $pickup_date, $dropoff_date );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Disable weekdays
			$mesg = $this->disable_weekdays_validation( $pickup_date, $dropoff_date );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Disabled dates
			$mesg = $this->disabled_dates_validation( $pickup_date, $dropoff_date );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			return apply_filters( $hook_name, false, $pickup_date, $dropoff_date, $args, $this );
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
	    		// Charged by
	    		$charged_by = $this->product->get_charged_by();

	    		if ( $pickup_date < ( $preparation_time*86400 + current_time( 'timestamp' ) ) ) {
	    			if ( 1 == $preparation_time ) {
	    				if ( 'hotel' === $charged_by ) {
	    					$mesg = sprintf( esc_html__( 'Book in advance %s night from the current date', 'ova-brw' ), $preparation_time );
	    				} else {
	    					$mesg = sprintf( esc_html__( 'Book in advance %s day from the current date', 'ova-brw' ), $preparation_time );
	    				}
	    			} else {
	    				if ( 'hotel' === $charged_by ) {
	    					$mesg = sprintf( esc_html__( 'Book in advance %s nights from the current date', 'ova-brw' ), $preparation_time );
	    				} else {
	    					$mesg = sprintf( esc_html__( 'Book in advance %s days from the current date', 'ova-brw' ), $preparation_time );
	    				}
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'preparation_time_validation', $mesg, $pickup_date, $dropoff_date, $this );
	    }

	    /**
	     * Get items available
	     */
	    public function get_items_available( $pickup_date, $dropoff_date, $pickup_location = '', $dropoff_location = '', $validation = 'cart' ) {
	    	// Get numberof available guests
	    	$numberof_available_guests = $this->get_numberof_available_guests( $pickup_date, $dropoff_date, 1, $validation );

			// Check items available
			if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
				return apply_filters( $this->prefix.'get_items_available', 0, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $validation, $this );
			}

			// Add available items
			$numberof_available_guests = (int)$numberof_available_guests['numberof_guests'];

	    	return apply_filters( $this->prefix.'get_items_available', $numberof_available_guests, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $validation, $this );
	    }

	    /**
	     * Get total data
	     * @param 	array 	$args
	     * @return 	array 	$total_data
	     */
	    public function get_total_data( $args = [] ) {
	    	// init results
			$results = [];

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $args );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $args );

			// Number of guests
			$numberof_guests = (int)ovabrw_get_meta_data( 'numberof_guests', $args );

			// Get regular prices
			$regular_prices = $this->get_regular_prices( $pickup_date, $dropoff_date, $args );

			// Get discount prices
			$discount_prices = $this->get_discount_prices( $numberof_guests, $args );
			
			// Get special prices
			$special_prices = $this->get_special_prices( $pickup_date, $dropoff_date, $numberof_guests, $args );
			
			// Priority discount prices
			if ( apply_filters( OVABRW_PREFIX.'priority_special_prices', true ) ) {
				if ( ovabrw_array_exists( $special_prices ) ) { // Special prices
					foreach ( array_keys( $regular_prices ) as $key ) {
						if ( '' !== ovabrw_get_meta_data( $key, $special_prices ) ) {
							$regular_prices[$key] = (float)$special_prices[$key];
						}
					}
				} elseif ( ovabrw_array_exists( $discount_prices ) ) { // Discount prices
					foreach ( array_keys( $regular_prices ) as $key ) {
						if ( '' !== ovabrw_get_meta_data( $key, $discount_prices ) ) {
							$regular_prices[$key] = (float)$discount_prices[$key];
						}
					}
				}
			} else {
				if ( ovabrw_array_exists( $discount_prices ) ) { // Discount prices
					// Loop regular prices
					foreach ( array_keys( $regular_prices ) as $key ) {
						if ( '' !== ovabrw_get_meta_data( $key, $discount_prices ) ) {
							$regular_prices[$key] = (float)$discount_prices[$key];
						}
					}
				} elseif ( ovabrw_array_exists( $special_prices ) ) { // Special prices
					foreach ( array_keys( $regular_prices ) as $key ) {
						if ( '' !== ovabrw_get_meta_data( $key, $special_prices ) ) {
							$regular_prices[$key] = (float)$special_prices[$key];
						}
					}
				}
			}

			// init
	    	$total = 0;

			// Guests prices
			$guest_options = $this->get_guest_options();
			foreach ( $guest_options as $guest ) {
				$var_numberof_guests 	= 'numberof_'.$guest['name'];
				$var_guest_price 		= (float)ovabrw_get_meta_data( $guest['name'].'_price', $regular_prices );

				if ( isset( $args[$var_numberof_guests] ) ) {
					$total += (int)$args[$var_numberof_guests]*$var_guest_price;
				}

				// Add guest price to results
				$results[$guest['name'].'_price'] = $var_guest_price;
			}

			// CCKF
	    	$cckf = ovabrw_get_meta_data( 'cckf', $args );

	    	// CCKF qty
	    	$cckf_qty = ovabrw_get_meta_data( 'cckf_qty', $args );

	    	// CCKF prices
	    	$cckf_prices = $this->get_cckf_prices( $cckf, $cckf_qty );
	    	if ( $cckf_prices ) $total += $cckf_prices;

	    	// Extra services
			$extra_services = ovabrw_get_meta_data( 'extra_services', $args );

			// Get extra services prices
			$service_prices = $this->get_extra_services_prices( $extra_services, $args );
			if ( $service_prices ) $total += $service_prices;

	    	// Add $total to $results
			$results['total'] = $total;

	    	return apply_filters( $this->prefix.'get_total_data', $results, $args, $this );
	    }

	    /**
	     * Get total
	     * @param 	array 	$args
	     * @return 	float 	$total
	     */
	    public function get_total( $args = [] ) {
	    	// Get total data
	    	$total_data = $this->get_total_data( $args );

	    	// Get total
	    	$total = (float)ovabrw_get_meta_data( 'total', $total_data );

	    	return apply_filters( $this->prefix.'get_total_data', $total, $args, $this );
	    }

	    /**
	     * Get regular prices
	     */
	    public function get_regular_prices( $pickup_date, $dropoff_date, $data ) {
	    	// init regular prices
			$regular_prices = [];

			// Guest prices
			$guest_prices = [];

			// Get regular price duration fixed
			if ( $this->is_fixed_date() ) {
				$guest_prices = $this->get_regular_prices_fixed( $pickup_date, $dropoff_date );
			} elseif ( $this->is_timeslots() ) { // Get regular prices duration time slots
				$guest_prices = $this->get_regular_prices_timeslot( $pickup_date, $dropoff_date );
			} elseif ( $this->is_period_time() ) { // Get regular prices duration period
				$guest_prices = $this->get_regular_prices_period( $pickup_date, $dropoff_date );
			}

			// Check guest prices
			if ( ovabrw_array_exists( $guest_prices ) ) {
				// Loop regular prices
				foreach ( array_keys( $guest_prices ) as $key ) {
					if ( '' !== ovabrw_get_meta_data( $key, $guest_prices ) ) {
						$regular_prices[$key] = (float)$guest_prices[$key];
					}
				}
			}

			return apply_filters( $this->prefix.'get_regular_prices', $regular_prices, $pickup_date, $dropoff_date, $data );
	    }

	    /**
		 * Get regular prices duration fixed
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	array 	$regular_prices
		 */
		public function get_regular_prices_fixed( $pickup_date, $dropoff_date ) {
			// init
			$regular_prices = [];

			// Guest options
			$guest_options = $this->get_guest_options();

			// Get regular price
			foreach ( $guest_options as $k => $guest ) {
				$regular_prices[$guest['name'].'_price'] = floatval( $this->get_meta_value( $guest['name'].'_price' ) );
			}

			return apply_filters( $this->prefix.'get_regular_prices_fixed', $regular_prices, $pickup_date, $dropoff_date );
		}

		/**
		 * Get regular prices duration time slots
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	array 	$regular_prices
		 */
		public function get_regular_prices_timeslot( $pickup_date, $dropoff_date ) {
			// init
			$regular_prices = [];

			// Get regular prices from specific time
			$regular_prices_specific = $this->get_regular_prices_from_specific_time( $pickup_date, $dropoff_date );
			if ( !is_bool( $regular_prices_specific ) ) {
				$regular_prices = $regular_prices_specific;
			} else {
				// Get time slots start
				$timeslots_start = $this->get_meta_value( 'tour_timeslots_start' );
				if ( ovabrw_array_exists( $timeslots_start ) ) {
					// Time format
					$time_format = OVABRW()->options->get_time_format();

					// Pick-up time
					$pickup_time = strtotime( gmdate( $time_format, $pickup_date ) );

					// Drop-off time
					$dropoff_time = strtotime( gmdate( $time_format, $dropoff_date ) );

					// Get string day of week
					$dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

					// Get the weekday prices start
					$weekday_prices_start = ovabrw_get_meta_data( $dayofweek, $timeslots_start );
					if ( ovabrw_array_exists( $weekday_prices_start ) ) {
						// Guest options
						$guest_options = $this->get_guest_options();

						// Get time slots end
						$timeslots_end = $this->get_meta_value( 'tour_timeslots_end' );

						// Loop
						foreach ( $weekday_prices_start as $k => $start_time ) {
							if ( !$start_time ) continue;

							// Get end time
							$end_time = isset( $timeslots_end[$dayofweek][$k] ) ? $timeslots_end[$dayofweek][$k] : '';
							if ( !$end_time ) continue;

							// Convert times
							$start_time = strtotime( gmdate( $time_format, $start_time ) );
							$end_time 	= strtotime( gmdate( $time_format, $end_time ) );

							if ( $start_time === $pickup_time && $end_time === $dropoff_time ) {
								foreach ( $guest_options as $guest ) {
									// Get guest prices from database
									$guest_prices = $this->get_meta_value( 'tour_timeslots_'.$guest['name'].'_price' );

									// Add guest price to regular prices
									$regular_prices[$guest['name'].'_price'] = isset( $guest_prices[$dayofweek][$k] ) ? (float)$guest_prices[$dayofweek][$k] : '';
								}

								// Break out of loop
								break;
							}
						} // END loop
					} // END if
				} // END if
			} // END if

			return apply_filters( $this->prefix.'get_regular_prices_timeslot', $regular_prices, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get regular prices from specific time
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	array 	$regular_prices
		 */
		public function get_regular_prices_from_specific_time( $pickup_date, $dropoff_date ) {
			// init
			$regular_prices = false;

			// Get specific prices
			$specific_prices = $this->get_specific_prices( $pickup_date, $dropoff_date );
			if ( ovabrw_array_exists( $specific_prices ) ) {
				// init
				$regular_prices = [];

				// Get time slots data
				$timeslots_start 	= ovabrw_get_meta_data( 'timeslots_start', $specific_prices );
				$timeslots_end 		= ovabrw_get_meta_data( 'timeslots_end', $specific_prices );

				if ( ovabrw_array_exists( $timeslots_start ) ) {
					// Time format
					$time_format = OVABRW()->options->get_time_format();

					// Pick-up time
					$pickup_time = strtotime( gmdate( $time_format, $pickup_date ) );

					// Drop-off time
					$dropoff_time = strtotime( gmdate( $time_format, $dropoff_date ) );

					// Get string day of week
					$dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

					// Get the weekday prices start
					$weekday_prices_start = ovabrw_get_meta_data( $dayofweek, $timeslots_start );
					if ( ovabrw_array_exists( $weekday_prices_start ) ) {
						// Guest options
						$guest_options = $this->get_guest_options();

						// Loop
						foreach ( $weekday_prices_start as $k => $start_time ) {
							if ( !$start_time ) continue;

							// Get end time
							$end_time = isset( $timeslots_end[$dayofweek][$k] ) ? $timeslots_end[$dayofweek][$k] : '';
							if ( !$end_time ) continue;

							// Convert times
							$start_time = strtotime( gmdate( $time_format, $start_time ) );
							$end_time 	= strtotime( gmdate( $time_format, $end_time ) );

							if ( $start_time === $pickup_time && $end_time === $dropoff_time ) {
								foreach ( $guest_options as $guest ) {
									// Get guest prices from database
									$guest_prices = ovabrw_get_meta_data( $guest['name'].'_price', $specific_prices );

									// Add guest price to regular prices
									$regular_prices[$guest['name'].'_price'] = isset( $guest_prices[$dayofweek][$k] ) ? $guest_prices[$dayofweek][$k] : '';
								}

								// Break out of loop
								break;
							} // END if
						} // END Loop
					} // END if
				} // END if
			}

			return apply_filters( $this->prefix.'get_regular_prices_from_specific_time', $regular_prices, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get specific price
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	array 	$specific_prices
		 */
		public function get_specific_prices( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date ) return false;        

	        // init specifix prices
	        $specific_prices = false;

	        // Get from dates
	        $from_dates = $this->get_meta_value( 'specific_from' );

	        // Get to dates
	        $to_dates = $this->get_meta_value( 'specific_to' );

	        if ( ovabrw_array_exists( $from_dates ) ) {
	        	// Convert pick-up date
            	$timestamp = OVABRW()->options->convert_to_current_year( $pickup_date );

            	// Guest options
				$guest_options = $this->get_guest_options();

				// Loop
				foreach ( $from_dates as $k => $from_date ) {
					// Convert from date
					$from_date = OVABRW()->options->convert_to_current_year( $from_date );
					if ( !$from_date ) continue;

					// Get to date
					$to_date = OVABRW()->options->convert_to_current_year( ovabrw_get_meta_data( $k, $to_dates ) );
					if ( !$to_date ) continue;

					if ( $timestamp >= $from_date && $timestamp <= $to_date ) {
						// Specific lables
	                    $specific_labels = $this->get_meta_value( 'specific_label' );

	                    // Specific start
	                    $specific_start = $this->get_meta_value( 'specific_start' );

	                    // Specific end
	                    $specific_end = $this->get_meta_value( 'specific_end' );

	                    // Specific max guests
	                    $specific_max_guests = $this->get_meta_value( 'specific_max_guests' );

	                    // Update specific prices
	                    $specific_prices = [
	                    	'timeslots_label'   => ovabrw_get_meta_data( $k, $specific_labels ),
	                        'timeslots_start'   => ovabrw_get_meta_data( $k, $specific_start ),
	                        'timeslots_end'     => ovabrw_get_meta_data( $k, $specific_end ),
	                        'max_guests'        => ovabrw_get_meta_data( $k, $specific_max_guests )
	                    ];

	                    if ( ovabrw_array_exists( $guest_options ) ) {
	                        foreach ( $guest_options as $guest ) {
	                            $guest_price = $this->get_meta_value( 'specific_'.$guest['name'].'_price' );
	                            $specific_prices[$guest['name'].'_price'] = ovabrw_get_meta_data( $k, $guest_price );
	                        }
	                    } // END guest options

	                    // Break out of loop
	                    break;
					} // END if
				} // END foreach
	        } // END if

	        return apply_filters( $this->prefix.'get_specific_prices', $specific_prices, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get regular prices duration period
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	array 	$regular_prices
		 */
		public function get_regular_prices_period( $pickup_date, $dropoff_date ) {
			// init
			$regular_prices = [];

			// Get period start
			$period_start = $this->get_meta_value( 'period_start' );
			if ( ovabrw_array_exists( $period_start ) ) {
				// Get period end
				$period_end = $this->get_meta_value( 'period_end' );

				// Loop
				foreach ( $period_start as $k => $start_date ) {
					// Convert start date
					$start_date = strtotime( $start_date );
					if ( !$start_date ) continue;

					// End date
					$end_date = strtotime( ovabrw_get_meta_data( $k, $period_end ) );
					if ( !$end_date ) continue;

					// Check check-in date and check-out date
					if ( $start_date === $pickup_date && $end_date === $dropoff_date ) {
						// Guest options
						$guest_options = $this->get_guest_options();

						foreach ( $guest_options as $guest ) {
							// Get guest prices from database
							$guest_prices = $this->get_meta_value( 'period_'.$guest['name'].'_price' );

							// Add guest price to regular prices
							$regular_prices[$guest['name'].'_price'] = isset( $guest_prices[$k] ) ? (float)$guest_prices[$k] : '';
						}

						// Break out of loop
						break;
					} // END if
				} // END Loop
			} // END if

			return apply_filters( $this->prefix.'get_regular_prices_period', $regular_prices, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get discount prices
		 * @param  int 		$numberof_guests
		 * @return array 	$discount_prices
		 */
		public function get_discount_prices( $numberof_guests, $data = [] ) {
			// Init discount prices
			$discount_prices = [];

			// Get discount dates from
			$discount_from = $this->get_meta_value( 'discount_from' );
			if ( ovabrw_array_exists( $discount_from ) ) {
				// Get discount dates to
				$discount_to = $this->get_meta_value( 'discount_to' );

				// Guest options
				$guest_options = $this->get_guest_options();

				// Get number of persons
				$numberof_persons = $numberof_guests;

				// Get discount applicable
				$applicable = $this->get_meta_value( 'discount_applicable', 'only' );
				if ( 'only' == $applicable ) {
					// Get first guest name
					$guest_name = isset( $guest_options[0]['name'] ) ? $guest_options[0]['name'] : '';

					// Get number of persons
					$numberof_persons = (int)ovabrw_get_meta_data( 'numberof_'.$guest_name, $data );
				}

				// Loop
				foreach ( $discount_from as $k => $from ) {
					// Discount from
					$from = absint( $from );

					// Discount to
					$to = absint( ovabrw_get_meta_data( $k, $discount_to ) );

					if ( $from <= $numberof_persons && $numberof_persons <= $to ) {
						foreach ( $guest_options as $guest ) {
							// Get guest prices from database
							$guest_prices = $this->get_meta_value( 'discount_'.$guest['name'].'_price' );

							// Add guest price to discount prices
							$discount_prices[$guest['name'].'_price'] = isset( $guest_prices[$k] ) ? $guest_prices[$k] : '';
						}

						// Break out of loop
						break;
					} // END if
				} // END foreach
			} // END if

			return apply_filters( $this->prefix.'get_discount_prices', $discount_prices, $numberof_guests, $data, $this );
		}

		/**
		 * Get special prices
		 * @param  int 		$pickup_date
		 * @param  int 		$dropoff_date
		 * @param  int 		$numberof_guests
		 * @return array 	$special_prices
		 */
		public function get_special_prices( $pickup_date, $dropoff_date, $numberof_guests, $data = [] ) {
			// Init special prices
			$special_prices = [];

			// Get special dates from
			$special_from = $this->get_meta_value( 'special_from' );
			if ( ovabrw_array_exists( $special_from ) ) {
				// Get special dates to
				$special_to = $this->get_meta_value( 'special_to' );

				// Get discount during special times
				$special_discounts = $this->get_meta_value( 'special_discount' );

				// Guest options
				$guest_options = $this->get_guest_options();

				// Get number of persons
				$numberof_persons = $numberof_guests;

				// Get discount applicable
				$applicable = $this->get_meta_value( 'discount_applicable', 'only' );
				if ( 'only' == $applicable ) {
					// Get first guest name
					$guest_name = isset( $guest_options[0]['name'] ) ? $guest_options[0]['name'] : '';

					// Get number of persons
					$numberof_persons = (int)ovabrw_get_meta_data( 'numberof_'.$guest_name, $data );
				}

				// Convert pick-up date
            	$timestamp = OVABRW()->options->convert_to_current_year( $pickup_date );

				// Loop
				foreach ( $special_from as $k => $from_date ) {
					// Convert from date
					$from_date = OVABRW()->options->convert_to_current_year( $from_date );
					if ( !$from_date ) continue;

					// Get to date
					$to_date = ovabrw_get_meta_data( $k, $special_to );
					if ( !$to_date ) continue;

					// Get discounts
					$discounts = ovabrw_get_meta_data( $k, $special_discounts );

					if ( $from_date && $to_date && $from_date <= $timestamp && $timestamp <= $to_date ) {
						foreach ( $guest_options as $guest ) {
							// Get guest prices from database
							$guest_prices = $this->get_meta_value( 'special_'.$guest['name'].'_price' );

							// Add guest price to special prices
							$special_prices[$guest['name'].'_price'] = isset( $guest_prices[$k] ) ? $guest_prices[$k] : '';
						}

						// Get discount prices
						$discount_prices = $this->get_discounts_during_special_times( $discounts, $numberof_persons );

						if ( ovabrw_array_exists( $discount_prices ) ) {
							foreach ( array_keys( $special_prices ) as $key ) {
								if ( '' !== ovabrw_get_meta_data( $key, $discount_prices ) ) {
									$special_prices[$key] = $discount_prices[$key];
								}
							}
						}

						// Break out of loop
						break;
					} // END if
				} // END loop
			} // END if

			return apply_filters( $this->prefix.'get_special_prices', $special_prices, $pickup_date, $dropoff_date, $numberof_guests, $data, $this );
		}

		/**
		 * Get discounts during special times
		 * @param  array 	$discounts
		 * @param  int 		$numberof_guests
		 * @return array 	$discount_prices
		 */
		public function get_discounts_during_special_times( $discounts, $numberof_guests ) {
			// Init discount prices
			$discount_prices = [];

			// Discounts
			if ( ovabrw_array_exists( $discounts ) ) {
				$discount_from = ovabrw_get_meta_data( 'from', $discounts );

				if ( ovabrw_array_exists( $discount_from ) ) {
					$discount_to = ovabrw_get_meta_data( 'to', $discounts );

					// Loop
					foreach ( $discount_from as $k => $from ) {
						$to = absint( ovabrw_get_meta_data( $k, $discount_to ) );

						if ( absint( $from ) <= $numberof_guests && $numberof_guests <= $to ) {
							// Guest options
							$guest_options = $this->get_guest_options();

							foreach ( $guest_options as $guest ) {
								// Get guest prices from database
								$guest_prices = ovabrw_get_meta_data( $guest['name'].'_price', $discounts );

								// Add guest price to special prices
								$discount_prices[$guest['name'].'_price'] = isset( $guest_prices[$k] ) ? $guest_prices[$k] : '';
							}
							
							// Break out of loop
							break;
						} // END if
					} // END loop
				} // END if
			} // END if

			return apply_filters( $this->prefix.'get_discounts_during_special_times', $discount_prices, $numberof_guests, $this );
		}

		/**
		 * Get extra services prices
		 * @param  array 	$services
		 * @param  array 	$data
		 * @return float 	$service_prices
		 */
		public function get_extra_services_prices( $services, $data = [] ) {
			// Init extra services prices
			$service_prices = 0;

			if ( ovabrw_array_exists( $services ) ) {
				// Get service ids
				$service_ids = $this->get_meta_value( 'extra_service_id' );

				if ( ovabrw_array_exists( $service_ids ) ) {
					// Get service options ids
					$serv_opt_ids = $this->get_meta_value( 'extra_service_option_id' );

					// Get service options types
					$serv_opt_types = $this->get_meta_value( 'extra_service_option_type' );

					// Guest options
					$guest_options = $this->get_guest_options();

					// Loop
					foreach ( $services as $serv_id => $serv_item ) {
						// Get service index
						$serv_key = array_search( $serv_id, $service_ids );
						if ( false === $serv_key ) continue;

						// Get service option id
						$serv_option_id = ovabrw_get_meta_data( 'option_id', $serv_item );

						// Options data
						$opt_ids 	= ovabrw_get_meta_data( $serv_key, $serv_opt_ids, [] );
						$opt_types 	= ovabrw_get_meta_data( $serv_key, $serv_opt_types, [] );

						if ( is_array( $serv_option_id ) ) {
							foreach ( $serv_option_id as $k => $opt_id ) {
								// Get option index
								$opt_key = array_search( $opt_id, $opt_ids );
								if ( false === $opt_key ) continue;

								// Get option type
								$opt_type = ovabrw_get_meta_data( $opt_key, $opt_types, 'person' );

								// Price / Person
								if ( 'person' === $opt_type ) {
									// Get service prices
									foreach ( $guest_options as $guest ) {
										// Get number of guests
										$numberof_guests = isset( $serv_item[$guest['name']][$k] ) ? (int)$serv_item[$guest['name']][$k] : 0;

										// Get guest price
										$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

										if ( isset( $guest_prices[$serv_key][$opt_key] ) ) {
											$service_prices += $numberof_guests*(float)$guest_prices[$serv_key][$opt_key];
										}
									}
								} elseif ( 'order' === $opt_type ) { // Price / Order
									// Get service prices
									foreach ( $guest_options as $guest ) {
										// Get number of guests
										$numberof_guests = isset( $serv_item[$guest['name']][$k] ) ? (int)$serv_item[$guest['name']][$k] : 0;

										// Get guest price
										$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

										if ( $numberof_guests && isset( $guest_prices[$serv_key][$opt_key] ) ) {
											$service_prices += (float)$guest_prices[$serv_key][$opt_key];
										}
									} // END loop
								} // END if
							} // END loop
						} else {
							// Get option index
							$opt_key = array_search( $serv_option_id, $opt_ids );
							if ( false === $opt_key ) continue;

							// Get option type
							$opt_type = ovabrw_get_meta_data( $opt_key, $opt_types, 'person' );

							// Price / Person
							if ( 'person' === $opt_type ) {
								// Get service prices
								foreach ( $guest_options as $guest ) {
									// Get number of guests
									$numberof_guests = isset( $serv_item[$guest['name']] ) ? (int)$serv_item[$guest['name']] : 0;

									// Get guest price
									$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

									if ( isset( $guest_prices[$serv_key][$opt_key] ) ) {
										$service_prices += $numberof_guests*(float)$guest_prices[$serv_key][$opt_key];
									}
								}
							} elseif ( 'order' === $opt_type ) { // Price / Order
								// Get service prices
								foreach ( $guest_options as $guest ) {
									// Get number of guests
									$numberof_guests = isset( $serv_item[$guest['name']] ) ? (int)$serv_item[$guest['name']] : 0;

									// Get guest price
									$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

									if ( $numberof_guests && isset( $guest_prices[$serv_key][$opt_key] ) ) {
										$service_prices += (float)$guest_prices[$serv_key][$opt_key];
									}
								} // END loop
							} // END if
						} // END if
					} // END loop
				} // END if
			} // END if

			return apply_filters( $this->prefix.'get_extra_services_prices', floatval( $service_prices ), $services, $data, $this );
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

			// Post data
			$post_data = [];

			// Current form
			$current_form = sanitize_text_field( ovabrw_get_meta_data( 'form_name', $data, 'booking' ) );

			// Pick-up date
			$pickup_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $data ) ) );

			// Start time
			$start_time = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'start_time', $data ) ) );

			// Drop-off date
			$dropoff_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $data ) ) );

			// Get new date
			$new_date = $this->get_new_date([
				'pickup_date' 	=> $pickup_date,
				'dropoff_date' 	=> $dropoff_date,
				'start_time' 	=> $start_time
			]);
			if ( !ovabrw_array_exists( $new_date ) ) return false;

			// Add post data
			$post_data['pickup_date'] 	= strtotime( $new_date['pickup_date'] );
			$post_data['dropoff_date'] 	= strtotime( $new_date['dropoff_date'] );

			// Add results data
			$results['pickup_date'] 	= $new_date['pickup_date'];
			$results['dropoff_date'] 	= $new_date['dropoff_date'];

			// Total number of guests
			$numberof_guests = 0;

			// Guest options
			$guest_options = $this->get_guest_options();
			foreach ( $guest_options as $guest ) {
				$numberof_guest = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guest['name'], $data );

				// Add number of guests to data
				$post_data['numberof_'.$guest['name']] = $numberof_guest;

				// Total number of guests
				$numberof_guests += $numberof_guest;
			}

			// Add number of guests
			$post_data['numberof_guests'] = $numberof_guests;

			// Number of guests
			$mesg = $this->numberof_guests_validation( $post_data );
			if ( $mesg && $mesg !== true ) {
				$results['error'] = $mesg;
				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Extra services
			$extra_services = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'extra_services', $data ) );
			if ( $extra_services ) $extra_services = ovabrw_object_to_array( json_decode( $extra_services ) );
			$post_data['extra_services'] = $extra_services;

			// Custom checkout fields
			$cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'cckf', $data ) );
			if ( $cckf ) $cckf = ovabrw_object_to_array( json_decode( $cckf ) );
			$post_data['cckf'] = $cckf;

			// Quantity custom checkout fields
			$cckf_qty = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'cckf_qty', $data ) );
			if ( $cckf_qty ) $cckf_qty = ovabrw_object_to_array( json_decode( $cckf_qty ) );
			$post_data['cckf_qty'] = $cckf_qty;

			// Validates
			$passed = $this->booking_validation( $pickup_date, $dropoff_date );
			if ( $passed && $passed !== true ) {
				$results['error'] = $passed;
				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Get numberof available guests
			$numberof_available_guests = $this->get_numberof_available_guests( $pickup_date, $dropoff_date, $numberof_guests );

			// Check items available
			if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
				$results['error'] = $numberof_available_guests['error'];
				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Max guests
			$results['max_guests'] = (int)$numberof_available_guests['numberof_guests'];

			// Add available items
			$results['items_available'] = (int)$numberof_available_guests['numberof_guests'];

			// Get total data
			$total_data = $this->get_total_data( $post_data );

			// Line total
			$line_total = (float)ovabrw_get_meta_data( 'total', $total_data );

			// Multi Currency
        	if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
                $line_total = ovabrw_convert_price( $line_total );

                // Guest prices
                foreach ( $guest_options as $guest ) {
					$total_data[$guest['name'].'_price'] = ovabrw_convert_price( ovabrw_get_meta_data( $guest['name'].'_price', $total_data ) );
				}
            }

			// Add guest prices to $results
			foreach ( $guest_options as $guest ) {
				$results[$guest['name'].'_price'] = ovabrw_wc_price( ovabrw_get_meta_data( $guest['name'].'_price', $total_data ) ).esc_html__( '/guest', 'ova-brw' );
			}

            // Total amount
            $total_amount = $line_total;

            // Deposit
			$deposit = sanitize_text_field( ovabrw_get_meta_data( 'deposit', $data ) );
            if ( 'deposit' === $deposit ) {
            	$deposit_type 	= $this->get_meta_value( 'type_deposit' );
            	$deposit_value 	= (float)$this->get_meta_value( 'amount_deposit' );

            	// Calculate deposit
            	if ( 'percent' === $deposit_type ) { // Percent
            		$line_total = floatval( ( $line_total * $deposit_value ) / 100 );
            	} elseif ( 'value' === $deposit_type ) { // Fixed
            		$line_total = floatval( $deposit_value );
            	}
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
		        	$price_details = $this->get_price_details( $post_data );
		        	if ( $price_details ) $results['price_details'] = $price_details;
		        }
			}

			return apply_filters( $hook_name, $results, $data, $this );
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

			// Post data
			$post_data = [];

			// Current form
			$current_form = sanitize_text_field( ovabrw_get_meta_data( 'form_name', $data, 'booking' ) );

			// Pick-up date
			$pickup_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $data ) ) );

			// Start time
			$start_time = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'start_time', $data ) ) );

			// Drop-off date
			$dropoff_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $data ) ) );

			// Get new date
			$new_date = $this->get_new_date([
				'pickup_date' 	=> $pickup_date,
				'dropoff_date' 	=> $dropoff_date,
				'start_time' 	=> $start_time
			]);
			if ( !ovabrw_array_exists( $new_date ) ) return false;

			// Add post data
			$post_data['pickup_date'] 	= strtotime( $new_date['pickup_date'] );
			$post_data['dropoff_date'] 	= strtotime( $new_date['dropoff_date'] );

			// Add results data
			$results['pickup_date'] 	= $new_date['pickup_date'];
			$results['dropoff_date'] 	= $new_date['dropoff_date'];

			// Total number of guests
			$numberof_guests = 0;

			// Guest options
			$guest_options = $this->get_guest_options();
			foreach ( $guest_options as $guest ) {
				$numberof_guest = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guest['name'], $data );

				// Add number of guests to data
				$post_data['numberof_'.$guest['name']] = $numberof_guest;

				// Total number of guests
				$numberof_guests += $numberof_guest;
			}

			// Add number of guests
			$post_data['numberof_guests'] = $numberof_guests;

			// Number of guests
			$mesg = $this->numberof_guests_validation( $post_data );
			if ( $mesg && $mesg !== true ) {
				$results['error'] = $mesg;
				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Extra services
			$extra_services = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'extra_services', $data ) );
			if ( $extra_services ) $extra_services = ovabrw_object_to_array( json_decode( $extra_services ) );
			$post_data['extra_services'] = $extra_services;

			// Custom checkout fields
			$cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'cckf', $data ) );
			if ( $cckf ) $cckf = ovabrw_object_to_array( json_decode( $cckf ) );
			$post_data['cckf'] = $cckf;

			// Quantity custom checkout fields
			$cckf_qty = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'cckf_qty', $data ) );
			if ( $cckf_qty ) $cckf_qty = ovabrw_object_to_array( json_decode( $cckf_qty ) );
			$post_data['cckf_qty'] = $cckf_qty;

			// Validates
			$passed = $this->booking_validation( $pickup_date, $dropoff_date );
			if ( $passed && $passed !== true ) {
				$results['error'] = $passed;
				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Get numberof available guests
			$numberof_available_guests = $this->get_numberof_available_guests( $pickup_date, $dropoff_date, $numberof_guests );

			// Check items available
			if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
				$results['error'] = $numberof_available_guests['error'];
				return apply_filters( $hook_name, $results, $data, $this );
			}

			// Max guests
			$results['max_guests'] = (int)$numberof_available_guests['numberof_guests'];

			// Add available items
			$results['items_available'] = (int)$numberof_available_guests['numberof_guests'];

			// Get total data
			$total_data = $this->get_total_data( $post_data );

			// Line total
			$line_total = (float)ovabrw_get_meta_data( 'total', $total_data );

			// Multi Currency
        	if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
                $line_total = ovabrw_convert_price( $line_total, [ 'currency' => $currency ] );

                // Guest prices
                foreach ( $guest_options as $guest ) {
					$total_data[$guest['name'].'_price'] = ovabrw_convert_price( ovabrw_get_meta_data( $guest['name'].'_price', $total_data ), [ 'currency' => $currency ] );
				}
            }

			// Add guest prices to $results
			foreach ( $guest_options as $guest ) {
				$results[$guest['name'].'_price'] = ovabrw_wc_price( ovabrw_get_meta_data( $guest['name'].'_price', $total_data ) ).esc_html__( '/guest', 'ova-brw' );
			}

			if ( $line_total <= 0 && apply_filters( OVABRW_PREFIX.'required_total', false ) ) {
				return false;
			} else {
				// Line total
				$results['line_total'] = round( $line_total, wc_get_price_decimals() );
			}

			return apply_filters( $hook_name, $results, $data, $this );
		}

		/**
		 * Guests validation
		 * @param 	array 	$guests
		 * @return 	bool 	$passed
		 */
		public function numberof_guests_validation( $guests ) {
			// init passed
			$passed = true;

			// Number of guests
			$numberof_guests = (int)ovabrw_get_meta_data( 'numberof_guests', $guests );
			if ( !$numberof_guests ) {
				$passed = esc_html__( 'Number of guests is required.', 'ova-brw' );
				return apply_filters( $this->prefix.'numberof_guests_required', $passed, $guests, $this );
			}

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
		 * Get number of available guests
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @param 	int 	$numberof_guests
		 * @return 	array 	$available
		 */
		public function get_numberof_available_guests( $pickup_date, $dropoff_date, $numberof_guests = 1, $valid = 'booking', $reserved_guests = 0  ) {
			// Hook name
			$hook_name = $this->prefix.'get_numberof_available_guests';

			// Available
			$available = [
				'numberof_guests' => 0
			];

			// Duration
			if ( $this->is_fixed_date() ) {
				$available['numberof_guests'] = (int)$this->get_numberof_available_guests_duration_fixed( $pickup_date, $dropoff_date, $valid );
			} elseif ( $this->is_timeslots() ) {
				$available['numberof_guests'] = (int)$this->get_numberof_available_guests_duration_timeslots( $pickup_date, $dropoff_date, $valid );
			} elseif ( $this->is_period_time() ) {
				$available['numberof_guests'] = (int)$this->get_numberof_available_guests_duration_period( $pickup_date, $dropoff_date, $valid );
			}

			// Reserved guests
			if ( $reserved_guests ) {
				$available['numberof_guests'] -= (int)$reserved_guests;
			}

			// Number of guests
			if ( !$available['numberof_guests'] ) {
				$available['error'] = esc_html__( 'The tour is fully booked.', 'ova-brw' );

				return apply_filters( $hook_name, $available, $pickup_date, $dropoff_date, $numberof_guests, $reserved_guests, $this );
			} elseif ( $available['numberof_guests'] < $numberof_guests ) {
				if ( $available['numberof_guests'] > 0 ) {
					$available['error'] = sprintf( esc_html__( 'Maximum number of available guests: %s.', 'ova-brw' ), $available['numberof_guests'] );
				} else {
					$available['error'] = esc_html__( 'The tour is fully booked.', 'ova-brw' );
				}

				return apply_filters( $hook_name, $available, $pickup_date, $dropoff_date, $numberof_guests, $reserved_guests, $this );
			}

			return apply_filters( $hook_name, $available, $pickup_date, $dropoff_date, $numberof_guests, $reserved_guests, $this );
		}

		/**
		 * Get number of available guests duration fixed
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @param 	string 	$valid
		 * @return 	int 	$numberof_guests
		 */
		public function get_numberof_available_guests_duration_fixed( $pickup_date, $dropoff_date, $valid ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// Max number of guests
			$numberof_guests = (int)$this->get_meta_value( 'max_guest' );

			// Get number of guests booked from Cart
			if ( 'checkout' !== $valid ) {
				$cart_booked = $this->get_numberof_guests_booked_from_cart_duration_fixed( $pickup_date, $dropoff_date );
				$numberof_guests -= (int)$cart_booked;
			}

			// Get number of guests booked from Order
			$order_booked = $this->get_numberof_guests_booked_from_order_duration_fixed( $pickup_date, $dropoff_date );
			$numberof_guests -= (int)$order_booked;

			return apply_filters( $this->prefix.'get_numberof_available_guests_duration_fixed', $numberof_guests, $pickup_date, $dropoff_date, $valid, $this );
		}

		/**
		 * Get number of guests booked from Cart duration fixed
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$numberof_booked
		 */
		public function get_numberof_guests_booked_from_cart_duration_fixed( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// init
			$numberof_booked = 0;

			// Get cart
			if ( ovabrw_array_exists( WC()->cart->get_cart() ) ) {
				// Get date format
				$date_format = OVABRW()->options->get_date_format();

				// Current time
        		$current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );
        		
				// Get product IDs multi-lang
            	$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->id );

            	// Loop
            	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            		// Get product id in Cart
            		$cart_product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
            		if ( !in_array( $cart_product_id, $product_ids ) ) continue;

        			// Cart pick-up date
        			$cart_pickup = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

                	// Check current time
                    if ( !$cart_pickup || $cart_pickup < $current_time ) {
                        WC()->cart->remove_cart_item( $cart_item_key );
                        continue;
                    }

                    // Check period time
                    if ( $pickup_date == $cart_pickup ) {
                    	$numberof_booked += (int)ovabrw_get_meta_data( 'numberof_guests', $cart_item );
                    }
            	} // END loop
			}

			return apply_filters( $this->prefix.'get_numberof_guests_booked_from_cart_duration_fixed', $numberof_booked, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get number of guests booked from Order duration fixed
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$numberof_booked
		 */
		public function get_numberof_guests_booked_from_order_duration_fixed( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// init
			$numberof_booked = 0;

			// Get items booked from order queues table
	    	if ( OVABRW()->options->is_order_queues_completed() ) {
	    		$order_queues = OVABRW()->options->get_order_queues_data( $this->get_id() );
	    		if ( ovabrw_array_exists( $order_queues ) ) {
	    			// Date format
	        		$date_format = OVABRW()->options->get_date_format();

					// Current time
	        		$current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );

	        		// Loop
	        		foreach ( $order_queues as $order ) {
	        			// Item pick-up date
                    	$item_pickup = (int)ovabrw_get_meta_data( 'pickup_date', $order );
                        if ( !$item_pickup || $item_pickup < $current_time ) continue;

                        // Check period time
	                    if ( $pickup_date == $item_pickup ) {
	                    	$numberof_booked += (int)ovabrw_get_meta_data( 'quantity', $order );
	                    }
	        		} // END loop
	    		} // END if
	    	} else {
				// Get order booked ids
		    	$order_ids = OVABRW()->options->get_order_booked_ids( $this->id );
		    	if ( ovabrw_array_exists( $order_ids ) ) {
					// Date format
	        		$date_format = OVABRW()->options->get_date_format();

					// Current time
	        		$current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );

					// Get product IDs multi-lang
	        		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->id );

					// Loop Order IDs
					foreach ( $order_ids as $order_id ) {
		                // Get order
		                $order = wc_get_order( $order_id );
		                if ( !$order ) continue;

		                // Get items
		                $items = $order->get_items();
		                if ( !ovabrw_array_exists( $items ) ) continue;

		                // Loop items
		                foreach ( $items as $item_id => $item ) {
		                	// Get item product id
		                    $item_product_id = $item->get_product_id();
		                    if ( !in_array( $item_product_id, $product_ids ) ) continue;

	                    	// Item pick-up date
	                    	$item_pickup = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );

	                    	// Check current time
	                        if ( !$item_pickup || $item_pickup < $current_time ) continue;

	                        // Check period time
		                    if ( $pickup_date == $item_pickup ) {
		                    	$numberof_booked += (int)$item->get_meta( 'ovabrw_numberof_guests' );
		                    }
		                }
		            } // END loop order ids
				} // END if
			}

			return apply_filters( $this->prefix.'get_numberof_guests_booked_from_order_duration_fixed', $numberof_booked, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get number of available guests duration time slots
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @param 	string 	$valid
		 * @return 	int 	$numberof_guests
		 */
		public function get_numberof_available_guests_duration_timeslots( $pickup_date, $dropoff_date, $valid ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// Max number of guests
			$numberof_guests = (int)$this->get_max_guests_duration_timeslots( $pickup_date, $dropoff_date );

			// Get items booked from Cart
			if ( 'checkout' !== $valid ) {
				$cart_booked = $this->get_numberof_guests_booked_from_cart_duration_timeslots( $pickup_date, $dropoff_date );
				$numberof_guests -= (int)$cart_booked;
			}

			// Get items booked from Order
			$order_booked = $this->get_numberof_guests_booked_from_order_duration_timeslots( $pickup_date, $dropoff_date );
			$numberof_guests -= (int)$order_booked;

			return apply_filters( $this->prefix.'get_numberof_available_guests_duration_timeslots', $numberof_guests, $pickup_date, $dropoff_date, $valid, $this );
		}

		/**
		 * Get max number of guests duration timeslots
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$max_guests
		 */
		public function get_max_guests_duration_timeslots( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// init
			$max_guests = 0;

			// Time format
        	$time_format = OVABRW()->options->get_time_format();

			// Pick-up time
			$pickup_time = gmdate( $time_format, $pickup_date );

			// Drop-off time
			$dropoff_time = gmdate( $time_format, $dropoff_date );

			// Get specific times
			$specific_times = $this->in_specific_times( $pickup_date, $dropoff_date );

			// Get time slots
			if ( ovabrw_array_exists( $specific_times ) ) {
				$timeslots_start 		= ovabrw_get_meta_data( 'timeslots_start', $specific_times );
				$timeslots_end 			= ovabrw_get_meta_data( 'timeslots_end', $specific_times );
				$timeslots_max_guests 	= ovabrw_get_meta_data( 'max_guests', $specific_times );
			} else {
				$timeslots_start 		= $this->get_meta_value( 'tour_timeslots_start', [] );
				$timeslots_end 			= $this->get_meta_value( 'tour_timeslots_end', [] );
				$timeslots_max_guests 	= $this->get_meta_value( 'tour_timeslots_max_guests', [] );
			}

			// Day of week
            $dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

            // Start times
            $start_times = ovabrw_get_meta_data( $dayofweek, $timeslots_start );

            // End times
            $end_times = ovabrw_get_meta_data( $dayofweek, $timeslots_end );

            // Daily max guests
            $daily_max_guests = ovabrw_get_meta_data( $dayofweek, $timeslots_max_guests );

            // Loop daily data
            if ( ovabrw_array_exists( $start_times ) ) {
            	foreach ( $start_times as $k => $start_time ) {
            		if ( !$start_time ) continue;

            		// Get end time
            		$end_time = (int)ovabrw_get_meta_data( $k, $end_times );
            		if ( !$end_time ) continue;

            		// Get item max number of guests
            		$item_max_guests = (int)ovabrw_get_meta_data( $k, $daily_max_guests );
            		if ( !$item_max_guests ) continue;
            		
            		// Convert times by time format
            		$start_time = gmdate( $time_format, $start_time );
            		$end_time 	= gmdate( $time_format, $end_time );

            		if ( $start_time == $pickup_time && $end_time == $dropoff_time ) {
            			$max_guests = $item_max_guests;
            			break;
            		}
            	}
            }

			return apply_filters( $this->prefix.'get_max_guests_duration_timeslots', $max_guests, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * In specific times
		 * @param 	init 	$pickup_date
		 * @param 	init 	$dropoff_date
		 * @return 	array 	$time_slots
		 */
		public function in_specific_times( $pickup_date = '', $dropoff_date = '' ) {
			if ( !$pickup_date ) return false;

			// init time slots
			$time_slots = [];

			// Get specific from dates
			$from_dates = $this->get_meta_value( 'specific_from' );
			if ( ovabrw_array_exists( $from_dates ) ) {
				// Get specific to dates
				$to_dates = $this->get_meta_value( 'specific_to' );

				// Pick-up timestamp
				$timestamp = OVABRW()->options->convert_to_current_year( $pickup_date );

				// Guest options
            	$guest_options = $this->get_guest_options();
				
				// Loop from dates
            	foreach ( $from_dates as $k => $from_date ) {
            		// Get from date
	        		$from_date = (int)$from_date;
	        		$from_date = OVABRW()->options->convert_to_current_year( $from_date );
	        		if ( !$from_date ) continue;

	        		// Get to date
	        		$to_date = (int)ovabrw_get_meta_data( $k, $to_dates );
	        		$to_date = OVABRW()->options->convert_to_current_year( $to_date );
	        		if ( !$to_date ) continue;

	        		// in specific times
                	if ( $timestamp >= $from_date && $timestamp <= $to_date ) {
                		// Time slot labels
                		$timeslot_labels = $this->get_meta_value( 'specific_label' );

                		// Time slot start
                		$timeslot_start = $this->get_meta_value( 'specific_start' );

                		// Time slot end
                		$timeslot_end = $this->get_meta_value( 'specific_end' );

                		// Max guests
						$max_guests = $this->get_meta_value( 'specific_max_guests' );

						// Add time slots
						$time_slots = [
							'timeslots_label'   => ovabrw_get_meta_data( $k, $timeslot_labels ),
	                        'timeslots_start'   => ovabrw_get_meta_data( $k, $timeslot_start ),
	                        'timeslots_end'     => ovabrw_get_meta_data( $k, $timeslot_end ),
	                        'max_guests'        => ovabrw_get_meta_data( $k, $max_guests ),
						];

						// Guest prices
                        foreach ( $guest_options as $guest ) {
                            $guest_price = $this->get_meta_value( 'specific_'.$guest['name'].'_price' );
                            $time_slots[$guest['name'].'_price'] = ovabrw_get_meta_data( $k, $guest_price );
                        }

	                    break;
                	} // END if
            	} // END loop
			} // END if

			return apply_filters( $this->prefix.'in_specific_times', $time_slots, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get number of guests booked from Cart duration time slots
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$numberof_booked
		 */
		public function get_numberof_guests_booked_from_cart_duration_timeslots( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// init
			$numberof_booked = 0;

			// Get cart
			if ( ovabrw_array_exists( WC()->cart->get_cart() ) ) {
				// Get product IDs multi-lang
            	$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->id );

            	// Get current time
            	$current_time = current_time( 'timestamp' );

            	// Loop
            	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            		// Get product id in Cart
            		$cart_product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
            		if ( !in_array( $cart_product_id, $product_ids ) ) continue;

        			// Cart pick-up date
        			$cart_pickup = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

                	// Cart drop-off date
                	$cart_dropoff = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

                	// Check current time
                    if ( !$cart_pickup || !$cart_dropoff || $cart_pickup < $current_time || $cart_dropoff < $current_time ) {
                        WC()->cart->remove_cart_item( $cart_item_key );
                        continue;
                    }

                    // Check period time
                    if ( $pickup_date == $cart_pickup && $dropoff_date == $cart_dropoff ) {
                    	$numberof_booked += (int)ovabrw_get_meta_data( 'numberof_guests', $cart_item );
                    }
            	} // END loop
			} // END cart

			return apply_filters( $this->prefix.'get_numberof_guests_booked_from_cart_duration_timeslots', $numberof_booked, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get number of guests booked from Order duration time slots
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$numberof_booked
		 */
		public function get_numberof_guests_booked_from_order_duration_timeslots( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// init
			$numberof_booked = 0;

			// Get items booked from order queues table
	    	if ( OVABRW()->options->is_order_queues_completed() ) {
	    		$order_queues = OVABRW()->options->get_order_queues_data( $this->get_id() );
	    		if ( ovabrw_array_exists( $order_queues ) ) {
	    			// Loop
	    			foreach ( $order_queues as $order ) {
	    				// Item pick-up date
                    	$item_pickup = (int)ovabrw_get_meta_data( 'pickup_date', $order );
                    	if ( !$item_pickup || $item_pickup < current_time( 'timestamp' ) ) continue;

                    	// Item drop-off date
                    	$item_dropoff = (int)ovabrw_get_meta_data( 'dropoff_date', $order );
                    	if ( !$item_dropoff || $item_dropoff < current_time( 'timestamp' ) ) continue;

                        // Check period time
	                    if ( $pickup_date == $item_pickup && $dropoff_date == $item_dropoff ) {
	                    	$numberof_booked += (int)ovabrw_get_meta_data( 'quantity', $order );
	                    }
	    			} // END foreach
	    		} // END if
	    	} else {
				// Get order booked ids
		    	$order_ids = OVABRW()->options->get_order_booked_ids( $this->id );
		    	if ( ovabrw_array_exists( $order_ids ) ) {
					// Get product IDs multi-lang
	        		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->id );

					// Loop Order IDs
					foreach ( $order_ids as $order_id ) {
		                // Get order
		                $order = wc_get_order( $order_id );
		                if ( !$order ) continue;

		                // Get items
		                $items = $order->get_items();
		                if ( !ovabrw_array_exists( $items ) ) continue;

		                // Loop items
		                foreach ( $items as $item_id => $item ) {
		                	// Get item product id
		                    $item_product_id = $item->get_product_id();
		                    if ( !in_array( $item_product_id, $product_ids ) ) continue;

	                    	// Item pick-up date
	                    	$item_pickup = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
	                    	if ( !$item_pickup || $item_pickup < current_time( 'timestamp' ) ) continue;

	                    	// Item drop-off date
	                    	$item_dropoff = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
	                    	if ( !$item_dropoff || $item_dropoff < current_time( 'timestamp' ) ) continue;

	                        // Check period time
		                    if ( $pickup_date == $item_pickup && $dropoff_date == $item_dropoff ) {
		                    	$numberof_booked += (int)$item->get_meta( 'ovabrw_numberof_guests' );
		                    }
		                }
		            } // END loop order ids
				} // END if
			}

			return apply_filters( $this->prefix.'get_numberof_guests_booked_from_order_duration_timeslots', $numberof_booked, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get number of available guests duration period
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @param 	string 	$valid
		 * @return 	int 	$numberof_guests
		 */
		public function get_numberof_available_guests_duration_period( $pickup_date, $dropoff_date, $valid ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// Max number of guests
			$numberof_guests = $this->get_max_guests_duration_period( $pickup_date, $dropoff_date );

			if ( 'checkout' !== $valid ) {
				// Get items booked from Cart
				$cart_booked = $this->get_numberof_guests_booked_from_cart_duration_period( $pickup_date, $dropoff_date );

				// Update max guests
				$numberof_guests -= (int)$cart_booked;
			}

			// Get items booked from Order
			$order_booked = $this->get_numberof_guests_booked_from_order_duration_period( $pickup_date, $dropoff_date );

			// Update max guests
			$numberof_guests -= (int)$order_booked;

			return apply_filters( $this->prefix.'get_numberof_available_guests_duration_period', $numberof_guests, $pickup_date, $dropoff_date, $valid, $this );
		}

		/**
		 * Get max guest duration period
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$max_guests
		 */
		public function get_max_guests_duration_period( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// init
			$max_guests = 0;

			// Get period start
			$period_start = $this->get_meta_value( 'period_start' );
			if ( ovabrw_array_exists( $period_start ) ) {
				// Get period end
				$period_end = $this->get_meta_value( 'period_end' );

				// Period max guests
				$period_max_guests = $this->get_meta_value( 'period_max_guests' );

				// Loop
				foreach ( $period_start as $k => $start_date ) {
					// Start date
					$start_date = strtotime( $start_date );
					if ( !$start_date ) continue;

					// END date
					$end_date = strtotime( ovabrw_get_meta_data( $k, $period_end ) );
					if ( !$end_date ) continue;

					// Max guests
					$item_max_guests = (int)ovabrw_get_meta_data( $k, $period_max_guests );
					if ( !$item_max_guests ) continue;

					// Compare check-in & check-out date
					if ( $start_date == $pickup_date && $end_date == $dropoff_date ) {
						$max_guests = $item_max_guests;

						// Break out of loop
						break;
					}
				} // END loop
			} // END if

			return apply_filters( $this->prefix.'get_max_guests_duration_period', $max_guests, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get number of guests booked from cart duration period
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$numberof_booked
		 */
		public function get_numberof_guests_booked_from_cart_duration_period( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// init
			$numberof_booked = 0;

			// Get cart
			if ( ovabrw_array_exists( WC()->cart->get_cart() ) ) {
				// Get date format
				$date_format = OVABRW()->options->get_date_format();

				// Current time
        		$current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );
        		
				// Get product IDs multi-lang
            	$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->id );

            	// Loop
            	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            		// Get product id in Cart
            		$cart_product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
            		if ( !in_array( $cart_product_id, $product_ids ) ) continue;

        			// Cart pick-up date
        			$cart_pickup = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

        			// Cart drop-off date
        			$cart_dropoff = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

                	// Check current time
                    if ( !$cart_pickup || $cart_pickup < $current_time || !$cart_dropoff || $cart_dropoff < $current_time ) {
                        WC()->cart->remove_cart_item( $cart_item_key );
                        continue;
                    }

                    // Check period time
                    if ( $pickup_date == $cart_pickup && $dropoff_date == $cart_dropoff ) {
                    	$numberof_booked += (int)ovabrw_get_meta_data( 'numberof_guests', $cart_item );
                    }
            	} // END loop
			}

			return apply_filters( $this->prefix.'get_numberof_guests_booked_from_cart_duration_period', $numberof_booked, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get number of guests booked from order duration period
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$number_booked
		 */
		public function get_numberof_guests_booked_from_order_duration_period( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return 0;

			// init
			$number_booked = 0;

			// Get items booked from order queues table
	    	if ( OVABRW()->options->is_order_queues_completed() ) {
	    		$order_queues = OVABRW()->options->get_order_queues_data( $this->get_id() );
	    		if ( ovabrw_array_exists( $order_queues ) ) {
	    			// Get date format
					$date_format = OVABRW()->options->get_date_format();

					// Current time
	        		$current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );

	    			// Loop
	    			foreach ( $order_queues as $order ) {
	    				// Item pick-up date
                    	$item_pickup = (int)ovabrw_get_meta_data( 'pickup_date', $order );
                    	if ( !$item_pickup || $item_pickup < $current_time ) continue;

                    	// Item drop-off date
                    	$item_dropoff = (int)ovabrw_get_meta_data( 'dropoff_date', $order );
                    	if ( !$item_dropoff || $item_dropoff < $current_time ) continue;

                        // Check period time
	                    if ( $pickup_date == $item_pickup && $dropoff_date == $item_dropoff ) {
	                    	$number_booked += (int)ovabrw_get_meta_data( 'quantity', $order );
	                    }
	    			} // END loop
	    		} // END if
	    	} else {
				// Get order booked ids
		    	$order_ids = OVABRW()->options->get_order_booked_ids( $this->id );
		    	if ( ovabrw_array_exists( $order_ids ) ) {
		    		// Get date format
					$date_format = OVABRW()->options->get_date_format();

					// Current time
	        		$current_time = strtotime( gmdate( $date_format, current_time( 'timestamp' ) ) );

					// Get product IDs multi-lang
	        		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->id );

					// Loop Order IDs
					foreach ( $order_ids as $order_id ) {
		                // Get order
		                $order = wc_get_order( $order_id );
		                if ( !$order ) continue;

		                // Get items
		                $items = $order->get_items();
		                if ( !ovabrw_array_exists( $items ) ) continue;

		                // Loop items
		                foreach ( $items as $item_id => $item ) {
		                	// Get item product id
		                    $item_product_id = $item->get_product_id();
		                    if ( !in_array( $item_product_id, $product_ids ) ) continue;

	                    	// Item pick-up date
	                    	$item_pickup = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
	                    	if ( !$item_pickup || $item_pickup < $current_time ) continue;

	                    	// Item drop-off date
	                    	$item_dropoff = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
	                    	if ( !$item_dropoff || $item_dropoff < $current_time ) continue;

	                        // Check period time
		                    if ( $pickup_date == $item_pickup && $dropoff_date == $item_dropoff ) {
		                    	$number_booked += (int)$item->get_meta( 'ovabrw_numberof_guests' );
		                    }
		                }
		            } // END loop order ids
				} // END if
			} // END if

			return apply_filters( $this->prefix.'get_numberof_guests_booked_from_order_duration_period', $number_booked, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get rental calculations
		 * @param 	array 	$cart_item
		 * @return 	float 	$rental_price
		 */
		public function get_rental_calculations( $cart_item = [] ) {
			// Pick-up date
	    	$pickup_date = ovabrw_get_meta_data( 'pickup_date', $cart_item );
	    	if ( !$pickup_date ) return 0;

	    	// Drop-off date
	    	$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $cart_item );
	    	if ( !$dropoff_date ) return 0;

	    	// Number of guests
			$numberof_guests = (int)ovabrw_get_meta_data( 'numberof_guests', $cart_item );

			// Get regular prices
			$regular_prices = $this->get_regular_prices( $pickup_date, $dropoff_date, $cart_item );

			// Get discount prices
			$discount_prices = $this->get_discount_prices( $numberof_guests, $cart_item );
			
			// Get special prices
			$special_prices = $this->get_special_prices( $pickup_date, $dropoff_date, $numberof_guests, $cart_item );
			
			// Priority discount prices
			if ( apply_filters( OVABRW_PREFIX.'priority_special_prices', true ) ) {
				if ( ovabrw_array_exists( $special_prices ) ) { // Special prices
					foreach ( array_keys( $regular_prices ) as $key ) {
						if ( '' !== ovabrw_get_meta_data( $key, $special_prices ) ) {
							$regular_prices[$key] = (float)$special_prices[$key];
						}
					}
				} elseif ( ovabrw_array_exists( $discount_prices ) ) { // Discount prices
					foreach ( array_keys( $regular_prices ) as $key ) {
						if ( '' !== ovabrw_get_meta_data( $key, $discount_prices ) ) {
							$regular_prices[$key] = (float)$discount_prices[$key];
						}
					}
				}
			} else {
				if ( ovabrw_array_exists( $discount_prices ) ) { // Discount prices
					// Loop regular prices
					foreach ( array_keys( $regular_prices ) as $key ) {
						if ( '' !== ovabrw_get_meta_data( $key, $discount_prices ) ) {
							$regular_prices[$key] = (float)$discount_prices[$key];
						}
					}
				} elseif ( ovabrw_array_exists( $special_prices ) ) { // Special prices
					foreach ( array_keys( $regular_prices ) as $key ) {
						if ( '' !== ovabrw_get_meta_data( $key, $special_prices ) ) {
							$regular_prices[$key] = (float)$special_prices[$key];
						}
					}
				}
			}

	    	// init
	    	$rental_price = 0;

			// Guests prices
			$guest_options = $this->get_guest_options();
			foreach ( $guest_options as $guest ) {
				$var_numberof_guests 	= 'numberof_'.$guest['name'];
				$var_guest_price 		= (float)ovabrw_get_meta_data( $guest['name'].'_price', $regular_prices );

				if ( isset( $cart_item[$var_numberof_guests] ) ) {
					$rental_price += (int)$cart_item[$var_numberof_guests]*$var_guest_price;
				}
			} // END loop

	    	return apply_filters( $this->prefix.'get_rental_calculations', $rental_price, $cart_item, $this );
		}

		/**
		 * Get price details
		 * @param 	array 	$cart_item
		 * @return 	array 	$price_details
		 */
		public function get_price_details( $cart_item = [] ) {
			// init
	        $price_details = [];

	        // Get sub-total
	        $subtotal = $this->get_rental_calculations( $cart_item );
	        $price_details['subtotal'] = sprintf(
        		esc_html__( 'Total price for all guests: %s', 'ova-brw' ),
        		ovabrw_wc_price( $subtotal )
        	);

        	// Show cckf prices
	        if ( apply_filters( OVABRW_PREFIX.'show_cckf_prices', true ) ) {
	        	// Get cckf
	        	$cckf = ovabrw_get_meta_data( 'cckf', $cart_item );

	        	// Get cckf qty
		    	$cckf_qty = ovabrw_get_meta_data( 'cckf_qty', $cart_item );

		    	// Get cckf prices
		    	$cckf_prices = $this->get_cckf_prices( $cckf, $cckf_qty );
		    	if ( $cckf_prices ) {
		    		$price_details['cckf_prices'] = sprintf(
		        		esc_html__( 'Extra service prices: %s', 'ova-brw' ),
		        		ovabrw_wc_price( $cckf_prices )
		        	);
		    	}
	        } // END if

	        // Show service prices
	        if ( apply_filters( OVABRW_PREFIX.'show_service_prices', true ) ) {
	        	// Services
				$extra_service = ovabrw_get_meta_data( 'extra_services', $cart_item );

				// Get extra services prices
				$service_prices = $this->get_extra_services_prices( $extra_service, $cart_item );

		    	if ( $service_prices ) {
		    		$price_details['service_prices'] = sprintf(
		        		esc_html__( 'Service prices: %s', 'ova-brw' ),
		        		ovabrw_wc_price( $service_prices )
		        	);
		    	}
	        } // END if

	        return apply_filters( $this->prefix.'get_price_details', $price_details, $cart_item, $this );
		}

		/**
	     * Add to cart validation
	     */
	    public function add_to_cart_validation( $passed, $product_id, $quantity ) {
	    	// Clear all notices
			wc_clear_notices();

			// Get date format
			$date_format = OVABRW()->options->get_date_format();

			// Get time format
			$time_format = OVABRW()->options->get_time_format();

			// Pick-up date
	    	$pickup_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickup_date', $_POST ) );

			// Start time
	    	$start_time = strtotime( ovabrw_get_meta_data( 'ovabrw_start_time', $_POST ) );

	    	// Drop-off date
	    	$dropoff_date = strtotime( ovabrw_get_meta_data( 'ovabrw_dropoff_date', $_POST ) );

	    	// Get new date
	    	$new_date = $this->get_new_date([
	    		'pickup_date' 	=> $pickup_date,
				'dropoff_date' 	=> $dropoff_date,
				'start_time' 	=> $start_time
	    	]);

	    	// New pick-up date
			$pickup_date = strtotime( $new_date['pickup_date'] );

			// New drop-off date
			$dropoff_date = strtotime( $new_date['dropoff_date'] );

			// Booking validation
			$booking_validation = $this->booking_validation( $pickup_date, $dropoff_date, $_REQUEST );
			if ( $booking_validation && $booking_validation !== true ) {
				wc_clear_notices();
				wc_add_notice( $booking_validation, 'error' );
				return false;
			}

			// Get guest options
			$guest_options = $this->get_guest_options();

			// Number of guests
			$numberof_guests = 0;

			// Guest data
			$guests = [];

			// Loop
			foreach ( $guest_options as $guest ) {
				$number = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guest['name'], $_REQUEST );
				$guests['numberof_'.$guest['name']] = $number;

				$numberof_guests += $number;
			} // END foreach

			// Add number of guests to guest data
			$guests['numberof_guests'] = $numberof_guests;

			// Number of guests validation
			$mesg = $this->numberof_guests_validation( $guests );
			if ( $mesg && $mesg !== true ) {
				wc_clear_notices();
				wc_add_notice( $mesg, 'error' );
				return false;
			}

			// Get number of available guests
			$numberof_available_guests = $this->get_numberof_available_guests( $pickup_date, $dropoff_date, $numberof_guests );
			if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
				wc_clear_notices();
				wc_add_notice( $numberof_available_guests['error'], 'error' );
				return false;
			}

	    	return apply_filters( $this->prefix.'add_to_cart_validation', $passed, $product_id, $quantity, $this );
	    }

	    /**
		 * Add cart item data
		 */
		public function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
	    	// Get pick-up date
    		$pickup_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickup_date', $_POST ) );

    		// Get start time
    		$start_time = strtotime( ovabrw_get_meta_data( 'ovabrw_start_time', $_POST ) );

    		// Get drop-off date
    		$dropoff_date = strtotime( ovabrw_get_meta_data( 'ovabrw_dropoff_date', $_POST ) );

    		// Get new date
			$new_date = $this->get_new_date([
				'pickup_date' 	=> $pickup_date,
				'dropoff_date' 	=> $dropoff_date,
				'start_time' 	=> $start_time
			]);

			// Pick-up date
			$pickup_date = $new_date['pickup_date'];

			// Drop-off date
			$dropoff_date = $new_date['dropoff_date'];

			// Check pick-up & drop-off dates
			if ( !strtotime( $pickup_date ) || !strtotime( $dropoff_date ) ) return $cart_item_data;

			// Rental type
	    	$cart_item_data['rental_type'] = $this->get_type();

	    	// Add cart item data
	    	$cart_item_data['pickup_date'] = $pickup_date;
	    	$cart_item_data['dropoff_date'] = $dropoff_date;

	    	// Pick-up real date
	    	$cart_item_data['pickup_real'] = $pickup_date;

	    	// Drop-off real dates
	    	$cart_item_data['dropoff_real'] = $dropoff_date;

			// Get number of guests
			$numberof_guests = 0;

			// Guest options
			$guest_options = $this->get_guest_options();

			// Guest data
			foreach ( $guest_options as $guest ) {
				$number_guest = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guest['name'], $_POST );

				// Guest data
				$cart_item_data['numberof_'.$guest['name']] = $number_guest;

				// Total number of guests
				$numberof_guests += $number_guest;
			}

			// Add number of guests to guest data
			$cart_item_data['numberof_guests'] = $numberof_guests;

			// Guest information
			$guest_info = ovabrw_get_meta_data( 'ovabrw_guest_info', $_POST );
			if ( ovabrw_array_exists( $guest_info ) ) {
				$cart_item_data['ovabrw_guest_info'] = $guest_info;
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

	    	// Extra services
			$service_ids = $this->get_meta_value( 'extra_service_id' );
			if ( ovabrw_array_exists( $service_ids ) ) {
				// Extra services
				$extra_services = [];

				// Services data
        		$service_labels = $this->get_meta_value( 'extra_service_label' );
        		$service_types 	= $this->get_meta_value( 'extra_service_display' );
        		$service_guests = $this->get_meta_value( 'extra_service_guests' );

	            // Options data
	            $option_ids 	= $this->get_meta_value( 'extra_service_option_id' );
	            $option_names 	= $this->get_meta_value( 'extra_service_option_name' );
	            $option_types 	= $this->get_meta_value( 'extra_service_option_type' );

	            // Loop
	            foreach ( $service_ids as $k => $service_id ) {
	            	$value 	= ovabrw_get_meta_data( $service_id, $_POST );
	            	$guests = ovabrw_get_meta_data( $service_id.'_guests', $_POST, [] );

	            	if ( !empty( $value ) ) {
	            		// Label
	            		$label = ovabrw_get_meta_data( $k, $service_labels );

	            		// Display
	            		$display = ovabrw_get_meta_data( $k, $service_types );

	            		// Choose guests
	            		$choose_guests = ovabrw_get_meta_data( $k, $service_guests );

	            		// Option ids
	            		$opt_ids = ovabrw_get_meta_data( $k, $option_ids, [] );

	            		// Option names
	            		$opt_names = ovabrw_get_meta_data( $k, $option_names, [] );

	            		// Option types
	            		$opt_types = ovabrw_get_meta_data( $k, $option_types, [] );

	            		if ( is_array( $value ) ) {
	            			// init
	            			$data_opt_ids = $data_opt_names = $data_opt_types = $data_opt_guests = $data_opt_prices = [];

	            			foreach ( $value as $opt_id ) {
	            				// Get option index
								$opt_index = array_search( $opt_id, $opt_ids );

								if ( false !== $opt_index ) {
									$opt_name = ovabrw_get_meta_data( $opt_index, $opt_names );
									$opt_type = ovabrw_get_meta_data( $opt_index, $opt_types );

									// Add data
									$data_opt_ids[] 	= $opt_id;
									$data_opt_names[] 	= $opt_name;
									$data_opt_types[] 	= $opt_type;

									// Guest data
									foreach ( $guest_options as $guest ) {
										// Get option number of guests
										if ( !isset( $data_opt_guests[$guest['name']] ) ) {
											$data_opt_guests[$guest['name']] = [];
										}

										if ( 'auto' === $choose_guests ) {
											$opt_guest = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guest['name'], $_POST );
										} else {
											$opt_guest = isset( $guests[$opt_id][$guest['name']] ) ? (int)$guests[$opt_id][$guest['name']] : 0;
										}

										array_push( $data_opt_guests[$guest['name']], $opt_guest );

										// Get guest price
										if ( !isset( $data_opt_prices[$guest['name'].'_price'] ) ) {
											$data_opt_prices[$guest['name'].'_price'] = [];
										}

										$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

										// Guest option price
										$guest_price = isset( $guest_prices[$k][$opt_index] ) ? (float)$guest_prices[$k][$opt_index] : 0;

										array_push( $data_opt_prices[$guest['name'].'_price'], $guest_price );
									}
								}
	            			}

	            			// Add data
	            			if ( ovabrw_array_exists( $data_opt_ids ) ) {
	            				// Add extra services
	            				$extra_services[$service_id] = array_merge_recursive([
	            					'label' 		=> $label,
	            					'display' 		=> $display,
	            					'option_id' 	=> $data_opt_ids,
	            					'option_name' 	=> $data_opt_names,
	            					'option_type' 	=> $data_opt_types
	            				], $data_opt_guests, $data_opt_prices );
	            			}
	            		} else {
	            			// Option id
	            			$opt_id = $value;

	            			// Get option index
							$opt_index = array_search( $opt_id, $opt_ids );

							if ( false !== $opt_index ) {
								$opt_name = ovabrw_get_meta_data( $opt_index, $opt_names );
								$opt_type = ovabrw_get_meta_data( $opt_index, $opt_types );

								// Add extra services
								$extra_services[$service_id] = [
									'label' 		=> $label,
	            					'display' 		=> $display,
	            					'option_id' 	=> $opt_id,
	            					'option_name' 	=> $opt_name,
	            					'option_type' 	=> $opt_type
								];

								// Guest data
								foreach ( $guest_options as $guest ) {
									// Opiton number of guests
									if ( 'auto' === $choose_guests ) {
										$opt_guest = (int)ovabrw_get_meta_data( 'ovabrw_numberof_'.$guest['name'], $_POST );
									} else {
										$opt_guest = isset( $guests[$opt_id][$guest['name']] ) ? (int)$guests[$opt_id][$guest['name']] : 0;
									}

									// Add number of guests to service data
									$extra_services[$service_id][$guest['name']] = $opt_guest;

									// Get guest price
									$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

									// Guest price
									$guest_price = isset( $guest_prices[$k][$opt_index] ) ? (float)$guest_prices[$k][$opt_index] : 0;

									// Add guest price to service data
									$extra_services[$service_id][$guest['name'].'_price'] = $guest_price;
								}
							}
	            		}
	            	}
	            } // END loop
	            
	            // Add service data to cart item data
				if ( ovabrw_array_exists( $extra_services ) ) {
					$cart_item_data['extra_services'] = $extra_services;
				}
			} // END extra services

			// Deposit
	    	$deposit = ovabrw_get_meta_data( 'ovabrw_type_deposit', $_REQUEST );
	    	if ( 'deposit' === $deposit ) {
	    		$cart_item_data['is_deposit'] = true;
	    	}

			return apply_filters( $this->prefix.'add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity, $this );
		}

		/**
		 * Get cart item data
		 */
		public function get_cart_item_data( $item_data, $cart_item ) {
			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $cart_item );
			if ( $pickup_date ) {
				$item_data[] = [
					'key'     => $this->product->get_date_label(),
		            'value'   => wc_clean( $pickup_date ),
		            'display' => wc_clean( $pickup_date ),
				];
			}

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $cart_item );
			if ( $dropoff_date ) {
				$item_data[] = [
					'key'     => $this->product->get_date_label( 'dropoff' ),
		            'value'   => wc_clean( $dropoff_date ),
		            'display' => wc_clean( $dropoff_date ),
		            'hidden'  => !$this->product->show_date_field( 'dropoff' ) ? true : false
				];
			}

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

			// Add extra services
			$extra_services = ovabrw_get_meta_data( 'extra_services', $cart_item );
			if ( ovabrw_array_exists( $extra_services ) ) {
				// Loop
				foreach ( $extra_services as $item_service ) {
					$label 		= ovabrw_get_meta_data( 'label', $item_service );
					$display 	= ovabrw_get_meta_data( 'display', $item_service );

					if ( 'dropdown' === $display ) {
						$opt_name = ovabrw_get_meta_data( 'option_name', $item_service );
						if ( !$opt_name ) continue;

						if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
							$opt_guests = [];

							// Loop guests
							foreach ( $guest_options as $guest ) {
								$numberof_guest = isset( $item_service[$guest['name']] ) ? (int)$item_service[$guest['name']] : 0;

								if ( $numberof_guest ) {
									array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
								}
							} // END loop guests
							
							$opt_name .= ' ('.implode(", ", $opt_guests).')';
						}

						$item_data[] = [
							'key' 	=> $label,
							'value' => apply_filters( OVABRW_PREFIX.'item_data_service_dropdown', $opt_name, $item_service )
						];
					} elseif ( 'checkbox' === $display ) {
						$opt_value 	= '';
						$opt_names 	= ovabrw_get_meta_data( 'option_name', $item_service, [] );

						foreach ( $opt_names as $k => $opt_name ) {
							if ( !$opt_name ) continue;

							$opt_value .= $opt_name;

							if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
								$opt_guests = [];

								// Loop guests
								foreach ( $guest_options as $guest ) {
									$numberof_guest = isset( $item_service[$guest['name']][$k] ) ? (int)$item_service[$guest['name']][$k] : 0;

									if ( $numberof_guest ) {
										array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
									}
								} // END loop guests
								
								$opt_value .= ' ('.implode(", ", $opt_guests).')';
							}

							if ( $k < count( $opt_names ) - 1 ) $opt_value .= ', ';
						}

						$item_data[] = [
							'key' 	=> $label,
							'value' => apply_filters( OVABRW_PREFIX.'item_data_service_checkbox', $opt_value, $item_service )
						];
					}
				} // END loop
			} // END if

			return apply_filters( $this->prefix.'get_cart_item_data', $item_data, $cart_item, $this );
		}

		/**
		 * Cart item validation
		 * @param 	array 	$cart_item
		 * @return 	bool 	$passed
		 */
		public function cart_item_validation( $cart_item = [] ) {
			// init
			$mesg = false;

			// Pick-up date
			$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

			// Drop-off date
			$dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

			// Number of guests
			$numberof_guests = (int)ovabrw_get_meta_data( 'numberof_guests', $cart_item );

			// Number of reserved
			$numberof_reserved = $this->get_numberof_reserved( $cart_item['data'], $pickup_date, $dropoff_date );

			// Get number of available guests
			$numberof_available_guests = $this->get_numberof_available_guests( $pickup_date, $dropoff_date, $numberof_guests, 'checkout', $numberof_reserved );
			if ( ovabrw_get_meta_data( 'error', $numberof_available_guests ) ) {
				$mesg = $numberof_available_guests['error'];
			}

			return apply_filters( $this->prefix.'cart_item_validation', $mesg, $cart_item, $this );
		}

		/**
		 * Get numberof reserved
		 * @param 	object 	$product
		 * @param 	int 	$pickup_date
		 * @param 	int 	$dropoff_date
		 * @return 	int 	$numberof_reserved
		 */
		public function get_numberof_reserved( $product, $pickup_date, $dropoff_date ) {
			// init
			$numberof_reserved = 0;

			// Manage stock enabled
            if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
                global $wpdb;

                // Product id
            	$product_id = $product->get_id();

            	// Get draft order id
	    		$draft_order_id = OVABRW()->options->get_draft_order_id();

	            // Order ids
	            $order_ids = [];

	            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
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
                    	$product_id,
                        $draft_order_id
                    ]
                ));
	            // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
	            
	            if ( ovabrw_array_exists( $order_ids ) ) {
	            	// Duration
					$duration = ovabrw_get_post_meta( $product_id, 'duration_type' );

        			// Get product ids multi language
	    			$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->id );

	            	// Loop order ids
	            	foreach ( $order_ids as $order_id ) {
	            		// Get order
		                $order = wc_get_order( $order_id );
		                if ( !$order ) continue;

		                // Get items
		                $items = $order->get_items();
		                if ( !ovabrw_array_exists( $items ) ) continue;

		                // Loop items
		                foreach ( $items as $item_id => $item ) {
		                    $item_product_id = $item->get_product_id();
		                    if ( !in_array( $item_product_id, $product_ids ) ) continue;

	                    	// Item pick-up date
	                    	$item_pickup = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                        	if ( !$item_pickup || $item_pickup < current_time( 'timestamp' ) ) continue;

                        	// Item drop-off date
                        	$item_dropoff = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
                        	if ( !$item_dropoff || $item_dropoff < current_time( 'timestamp' ) ) continue;

	                        // Number of guests
	                        $numberof_guests = (int)$item->get_meta( 'ovabrw_numberof_guests' );

	                        switch ( $duration ) {
	                        	case 'fixed':
				                    if ( $pickup_date == $item_pickup ) {
				                    	$numberof_reserved += $numberof_guests;
				                    }
	                        		break;
	                        	case 'timeslots':
	                        		if ( $pickup_date == $item_pickup && $dropoff_date == $item_dropoff ) {
				                    	$numberof_reserved += $numberof_guests;
				                    }
	                        		break;
	                        	case 'period':
	                        		if ( $pickup_date == $item_pickup && $dropoff_date == $item_dropoff ) {
				                    	$numberof_reserved += $numberof_guests;
				                    }
	                        		break;
	                        	default:
	                        		// code...
	                        		break;
	                        } // END switch
		                } // END loop
	            	} // END loop
	            } // END if
            } // END if

			return apply_filters( $this->prefix.'get_numberof_reserved', $numberof_reserved, $product, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Save order line item
		 * @param 	object 	$item
		 * @param 	array 	$values
		 */
		public function save_order_line_item( $item, $values ) {
			// Rental type
			$rental_type = ovabrw_get_meta_data( 'rental_type', $values );
			if ( $rental_type ) {
				$item->add_meta_data( 'rental_type', $rental_type, true );
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

			// Number of guests
        	$numberof_guests = (int)ovabrw_get_meta_data( 'numberof_guests', $values );
        	$item->add_meta_data( 'ovabrw_numberof_guests', $numberof_guests, true );

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

	    	// Extra services
        	$extra_services = ovabrw_get_meta_data( 'extra_services', $values, [] );
        	if ( ovabrw_array_exists( $extra_services ) ) {
        		// Loop
				foreach ( $extra_services as $item_service ) {
					$label 		= ovabrw_get_meta_data( 'label', $item_service );
					$display 	= ovabrw_get_meta_data( 'display', $item_service );

					if ( 'dropdown' === $display ) {
						$opt_name = ovabrw_get_meta_data( 'option_name', $item_service );
						if ( !$opt_name ) continue;

						if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
							$opt_guests = [];

							// Loop guests
							foreach ( $guest_options as $guest ) {
								$numberof_guest = isset( $item_service[$guest['name']] ) ? (int)$item_service[$guest['name']] : 0;

								if ( $numberof_guest ) {
									array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
								}
							} // END loop guests
							
							$opt_name .= ' ('.implode(", ", $opt_guests).')';
						}

						// Add meta data
						$item->add_meta_data( $label, apply_filters( OVABRW_PREFIX.'item_data_service_dropdown', $opt_name, $item_service ), true );
					} elseif ( 'checkbox' === $display ) {
						$opt_value 	= '';
						$opt_names 	= ovabrw_get_meta_data( 'option_name', $item_service, [] );

						foreach ( $opt_names as $k => $opt_name ) {
							if ( !$opt_name ) continue;

							$opt_value .= $opt_name;

							if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
								$opt_guests = [];

								// Loop guests
								foreach ( $guest_options as $guest ) {
									$numberof_guest = isset( $item_service[$guest['name']][$k] ) ? (int)$item_service[$guest['name']][$k] : 0;

									if ( $numberof_guest ) {
										array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
									}
								} // END loop guests
								
								$opt_value .= ' ('.implode(", ", $opt_guests).')';
							}

							if ( $k < count( $opt_names ) - 1 ) $opt_value .= ', ';
						}

						// Add meta data
						$item->add_meta_data( $label, apply_filters( OVABRW_PREFIX.'item_data_service_checkbox', $opt_value, $item_service ), true );
					}
				} // END loop

				// Add service data to item meta data
				$item->add_meta_data( 'ovabrw_extra_services', $extra_services, true );
        	}

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
			// Order details
			$order_details = '<h2>' . esc_html__( 'Order details: ', 'ova-brw' ) . '</h2>';

			// Open <table> tag
			$order_details .= '<table>';

			// Product link
			$product_link = '<a href="' . esc_url( $this->product->get_permalink() ) . '">' . wp_kses_post( $this->product->get_title() ) . '</a>';
			$order_details .= '<tr>';
    			$order_details .= '<td style="width: 15%">' . esc_html__( 'Product: ', 'ova-brw' ) . '</td>';
    			$order_details .= '<td style="width: 85%">';
    				$order_details .= $product_link;
    			$order_details .= '</td>';
    		$order_details .= '</tr>';

    		// Customer name
    		$customer_name = ovabrw_sanitize_customer_name( ovabrw_get_meta_data( 'ovabrw_name', $data ) );
    		if ( !$customer_name ) return false;
			$order_details .= '<tr>';
				$order_details .= '<td>' . esc_html__( 'Name: ', 'ova-brw' ) . '</td>';
				$order_details .= '<td>' . esc_html( $customer_name ) . '</td>';
			$order_details .= '</tr>';

    		// Customer email
    		$customer_email = sanitize_email( ovabrw_get_meta_data( 'ovabrw_email', $data ) );
    		if ( !is_email( $customer_email ) ) return false;

			$order_details .= '<tr>';
				$order_details .= '<td>' . esc_html__( 'Email: ', 'ova-brw' ) . '</td>';
				$order_details .= '<td>' . esc_html( $customer_email ) . '</td>';
			$order_details .= '</tr>';

    		// Customer phone
    		$customer_phone = ovabrw_sanitize_phone( ovabrw_get_meta_data( 'ovabrw_phone', $data ) );
    		if ( 'yes' === ovabrw_get_setting( 'request_booking_form_show_number', 'yes' ) ) {
    			// Check phone
    			if ( !$customer_phone ) return false;

    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Phone: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $customer_phone ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Customer address
    		$customer_address = wp_strip_all_tags( ovabrw_get_meta_data( 'ovabrw_address', $data, '' ) );
    		if ( $customer_address && 'yes' === ovabrw_get_setting( 'request_booking_form_show_address', 'yes' ) ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Address: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $customer_address ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Pick-up date
    		$pickup_date = strtotime( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) );
    		if ( !$pickup_date ) return false;

    		// Start time
			$start_time = strtotime( ovabrw_get_meta_data( 'ovabrw_start_time', $data ) );

    		// Drop-off date
    		$dropoff_date = strtotime( ovabrw_get_meta_data( 'ovabrw_dropoff_date', $data ) );

    		// Get new dates
    		$new_date = $this->get_new_date([
    			'pickup_date' 	=> $pickup_date,
    			'dropoff_date' 	=> $dropoff_date,
    			'start_time' 	=> $start_time
    		]);

    		// New pick-up date
    		$pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );
    		if ( !$pickup_date ) return false;

    		// New drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );
			if ( !$dropoff_date ) return false;

    		// Show pick-up date
			$order_details .= '<tr>';
				$order_details .= '<td>' . esc_html( $this->product->get_date_label() ) . ':</td>';
				$order_details .= '<td>' . esc_html( $pickup_date ) . '</td>';
			$order_details .= '</tr>';

			// Show drop-off date
    		if ( $this->product->show_date_field( 'dropoff', 'request' ) && $dropoff_date ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html( $this->product->get_date_label( 'dropoff' ) ) . ':</td>';
    				$order_details .= '<td>' . esc_html( $dropoff_date ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Number of guests
    		$numberof_guests = 0;

    		// Get guest options
			$guest_options = $this->get_guest_options();

			// Loop guest options
			foreach ( $guest_options as $guest ) {
				// Number of guest
				$number_guest = (int)ovabrw_get_meta_data( OVABRW_PREFIX.'numberof_'.$guest['name'], $data );

				if ( $number_guest || apply_filters( OVABRW_PREFIX.'numberof_guests_is_zero', false ) ) {
					$order_details .= '<tr>';
	    				$order_details .= '<td>'.$guest['label'].': </td>';
	    				$order_details .= '<td>'.$number_guest.'</td>';
	    			$order_details .= '</tr>';
				}

				// Number of guests
    			$numberof_guests += $number_guest;
			} // END loop guest options

    		// Custom checkout fields
			$cckf = $cckf_qty = $cckf_value = [];

			// Get product cckf
	    	$product_cckf = $this->product->get_cckf();
	    	if ( ovabrw_array_exists( $product_cckf ) ) {
	    		// Loop
	    		foreach ( $product_cckf as $name => $fields ) {
	    			if ( 'on' !== ovabrw_get_meta_data( 'enabled', $fields ) ) continue;

	    			// Get type
	    			$type = ovabrw_get_meta_data( 'type', $fields );

	    			// Label
	    			$label = ovabrw_get_meta_data( 'label', $fields );

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

	                        if ( !function_exists( 'wp_handle_upload' ) ) {
								require_once( ABSPATH . 'wp-admin/includes/file.php' );
							}

							// Upload
							$upload = wp_handle_upload( $files, $overrides );
							if ( ovabrw_get_meta_data( 'error', $upload ) ) continue;

	                        // File url
	                        $file_url = '<a href="'.esc_url( $upload['url'] ).'" target="_blank">';
								$file_url .= basename( $upload['file'] );
							$file_url .= '</a>';

							// Order details
							$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>';
	                        		$order_details .= $file_url;
	                        	$order_details .= '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = $file_url;
    					}
	    			} elseif ( 'checkbox' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options values
	    				$opt_values = ovabrw_get_meta_data( $name, $data );
	    				if ( ovabrw_array_exists( $opt_values ) ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_values;

	    					// Option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $data );
	    					if ( ovabrw_array_exists( $opt_qtys ) ) {
	    						foreach ( $opt_qtys as $opt_id => $qty ) {
	    							$cckf_qty[$opt_id] = $qty;
	    						}
	    					}

	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_checkbox_key', $fields, [] );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_checkbox_text', $fields, [] );

	    					// Loop
	    					foreach ( $opt_values as $val ) {
	    						$val = sanitize_text_field( $val );

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

	    				if ( ovabrw_array_exists( $opt_names ) ) {
	    					$opt_names = implode( ', ', $opt_names );

	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . esc_html( $opt_names ) . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = esc_html( $opt_names );
	    				}
	    			} elseif ( 'radio' === $type ) {
	    				// Get option value
	    				$opt_value = sanitize_text_field( ovabrw_get_meta_data( $name, $data ) );
	    				if ( $opt_value ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_value;

	    					// Get option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $data, [] );

	    					// Option qty
	    					$opt_qty = (int)ovabrw_get_meta_data( $opt_value, $opt_qtys );

	    					// Add cart item data
	    					if ( $opt_qty ) {
	    						// Add cckf quantity
	    						$cckf_qty[$name] 	= $opt_qty;
	    						$opt_value 			= sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_value, $opt_qty );
	    					}

	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . esc_html( $opt_value ) . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = esc_html( $opt_value );
	    				}
	    			} elseif ( 'select' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options value
	    				$opt_value = sanitize_text_field( ovabrw_get_meta_data( $name, $data ) );
	    				if ( $opt_value ) {
	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_options_key', $fields );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_options_text', $fields );

	    					// Option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $data );
	    					
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
	    						$opt_text 				= sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_text, $opt_qty );
	    					}

	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . esc_html( $opt_text ) . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = esc_html( $opt_text );
	    				}
	    			} elseif ( 'price' === $type ) {
	    				// Price value
	    				$opt_value = sanitize_text_field( ovabrw_get_meta_data( $name, $data ) );

	    				if ( $opt_value ) {
	    					// Format price
	    					$display_value = ovabrw_wc_price( $opt_value );

	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . $display_value . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = $display_value;
	    				}
	    			} else {
	    				// Option value
	    				$opt_value = sanitize_text_field( ovabrw_get_meta_data( $name, $data ) );

	    				if ( $opt_value ) {
	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . esc_html( $opt_value ) . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = esc_html( $opt_value );
	    				}
	    			}
	    		} // END loop
	    	}

	    	// Extra services
	    	$extra_services = [];

	    	// Get service ids
	    	$service_ids = $this->get_meta_value( 'extra_service_id' );
	    	if ( apply_filters( OVABRW_PREFIX.'show_extra_services', true ) && ovabrw_array_exists( $service_ids ) ) {
	    		// Service labels
	    		$service_labels = $this->get_meta_value( 'extra_service_label' );

	    		// Get service types
	    		$service_types = $this->get_meta_value( 'extra_service_display' );

	    		// Get service guests
	    		$service_guests = $this->get_meta_value( 'extra_service_guests' );

	    		// Get option ids
	            $option_ids = $this->get_meta_value( 'extra_service_option_id' );

	            // Get option names
	            $option_names = $this->get_meta_value( 'extra_service_option_name' );

	            // Get option types
	            $option_types = $this->get_meta_value( 'extra_service_option_type' );

	            // Loop
	            foreach ( $service_ids as $k => $service_id ) {
	            	// Get option guests
	            	$option_guests = ovabrw_get_meta_data( $service_id.'_guests', $_POST, [] );

	            	// Get option values
	            	$option_values = ovabrw_get_meta_data( $service_id, $_POST );
	            	if ( !empty( $option_values ) ) {
	            		// Label
	            		$label = ovabrw_get_meta_data( $k, $service_labels );

	            		// Display
	            		$display = ovabrw_get_meta_data( $k, $service_types );

	            		// Choose guests
	            		$choose_guests = ovabrw_get_meta_data( $k, $service_guests );

	            		// Option ids
	            		$opt_ids = ovabrw_get_meta_data( $k, $option_ids, [] );

	            		// Option names
	            		$opt_names = ovabrw_get_meta_data( $k, $option_names, [] );

	            		// Option types
	            		$opt_types = ovabrw_get_meta_data( $k, $option_types, [] );

	            		if ( is_array( $option_values ) ) {
	            			$data_opt_ids = $data_opt_names = $data_opt_types = $data_opt_guests = $data_opt_prices = [];

	            			foreach ( $option_values as $opt_id ) {
	            				// Get option index
								$opt_index = array_search( $opt_id, $opt_ids );
								if ( false === $opt_index ) continue;

								// Get option name
								$opt_name = ovabrw_get_meta_data( $opt_index, $opt_names );

								// Get option type
								$opt_type = ovabrw_get_meta_data( $opt_index, $opt_types );

								// Add data
								$data_opt_ids[] 	= $opt_id;
								$data_opt_names[] 	= $opt_name;
								$data_opt_types[] 	= $opt_type;

								// Guest data
								foreach ( $guest_options as $guest ) {
									// Get option number of guests
									if ( !isset( $data_opt_guests[$guest['name']] ) ) {
										$data_opt_guests[$guest['name']] = [];
									}

									if ( 'auto' === $choose_guests ) {
										$opt_guest = (int)ovabrw_get_meta_data( OVABRW_PREFIX.'numberof_'.$guest['name'], $_POST );
									} else {
										$opt_guest = isset( $option_guests[$opt_id][$guest['name']] ) ? (int)$option_guests[$opt_id][$guest['name']] : 0;
									}

									array_push( $data_opt_guests[$guest['name']], $opt_guest );

									// Get option prices
									if ( !isset( $data_opt_prices[$guest['name'].'_price'] ) ) {
										$data_opt_prices[$guest['name'].'_price'] = [];
									}

									// Get guest price
									$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

									// Guest option price
									$guest_price = isset( $guest_prices[$k][$opt_index] ) ? (float)$guest_prices[$k][$opt_index] : 0;

									array_push( $data_opt_prices[$guest['name'].'_price'], $guest_price );
								} // END foreach
	            			} // END foreach

	            			// Add data
	            			if ( ovabrw_array_exists( $data_opt_ids ) ) {
	            				// Add service data
	            				$extra_services[$service_id] = array_merge_recursive([
	            					'label' 		=> $label,
	            					'display' 		=> $display,
	            					'option_id' 	=> $data_opt_ids,
	            					'option_name' 	=> $data_opt_names,
	            					'option_type' 	=> $data_opt_types
	            				], $data_opt_guests, $data_opt_prices );
	            			}
	            		} else {
	            			// Option id
	            			$opt_id = $option_values;

	            			// Get option index
							$opt_index = array_search( $opt_id, $opt_ids );
							if ( false === $opt_index ) continue;

							// Get option name
							$opt_name = ovabrw_get_meta_data( $opt_index, $opt_names );

							// Get option type
							$opt_type = ovabrw_get_meta_data( $opt_index, $opt_types );

							// Add service data
							$extra_services[$service_id] = [
								'label' 		=> $label,
            					'display' 		=> $display,
            					'option_id' 	=> $opt_id,
            					'option_name' 	=> $opt_name,
            					'option_type' 	=> $opt_type
							];

							// Guest data
							foreach ( $guest_options as $guest ) {
								// Opiton number of guests
								if ( 'auto' === $choose_guests ) {
									$opt_guest = (int)ovabrw_get_meta_data( OVABRW_PREFIX.'numberof_'.$guest['name'], $_POST );
								} else {
									$opt_guest = isset( $option_guests[$opt_id][$guest['name']] ) ? (int)$option_guests[$opt_id][$guest['name']] : 0;
								}

								// Add number of guests to service data
								$extra_services[$service_id][$guest['name']] = $opt_guest;

								// Get guest price
								$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

								// Guest price
								$guest_price = isset( $guest_prices[$k][$opt_index] ) ? (float)$guest_prices[$k][$opt_index] : 0;

								// Add guest price to service data
								$extra_services[$service_id][$guest['name'].'_price'] 	= $guest_price;
							}
	            		} // END if
	            	} // END if
	            } // END foreach

	            // Add service data to item data
				if ( ovabrw_array_exists( $extra_services ) ) {
					// Add service data to item data
					$item_data['extra_services'] = $extra_services;

					// Loop
					foreach ( $extra_services as $item_service ) {
						$label 		= ovabrw_get_meta_data( 'label', $item_service );
						$display 	= ovabrw_get_meta_data( 'display', $item_service );

						if ( 'dropdown' === $display ) {
							// Get option name
							$opt_name = ovabrw_get_meta_data( 'option_name', $item_service );
							if ( !$opt_name ) continue;

							// Add number of guests
							if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
								$opt_guests = [];

								// Loop guests
								foreach ( $guest_options as $guest ) {
									$numberof_guest = isset( $item_service[$guest['name']] ) ? (int)$item_service[$guest['name']] : 0;

									if ( $numberof_guest ) {
										array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
									}
								} // END loop guests
								
								$opt_name .= ' ('.implode(", ", $opt_guests).')';
							} // END if

							// Order details
							$order_details .= '<tr>';
			    				$order_details .= '<td>'.$label.': </td>';
			    				$order_details .= '<td>'.apply_filters( OVABRW_PREFIX.'item_data_service_dropdown', $opt_name, $item_service ).'</td>';
			    			$order_details .= '</tr>';
						} elseif ( 'checkbox' === $display ) {
							$opt_value 	= '';
							$opt_names 	= ovabrw_get_meta_data( 'option_name', $item_service, [] );

							foreach ( $opt_names as $k => $opt_name ) {
								if ( !$opt_name ) continue;
								$opt_value .= $opt_name;

								if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
									$opt_guests = [];

									// Loop guests
									foreach ( $guest_options as $guest ) {
										$numberof_guest = isset( $item_service[$guest['name']][$k] ) ? (int)$item_service[$guest['name']][$k] : 0;

										if ( $numberof_guest ) {
											array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
										}
									} // END loop guests
									
									$opt_value .= ' ('.implode(", ", $opt_guests).')';
								} // END if

								if ( $k < count( $opt_names ) - 1 ) $opt_value .= ', ';
							} // END foreach

							// Order details
							$order_details .= '<tr>';
			    				$order_details .= '<td style="vertical-align: top;">'.$label.': </td>';
			    				$order_details .= '<td>'.apply_filters( OVABRW_PREFIX.'item_data_service_checkbox', $opt_value, $item_service ).'</td>';
			    			$order_details .= '</tr>';
						} // END if
					} // END Loop
				} // END if
	    	} // END if

	    	// Customer note
	    	$customer_note = wp_strip_all_tags( ovabrw_get_meta_data( 'extra', $data ) );
	    	if ( 'yes' === ovabrw_get_setting( 'request_booking_form_show_extra_info', 'yes' ) && $customer_note ) {
	    		$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Extra: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $customer_note ) . '</td>';
    			$order_details .= '</tr>';
	    	}

    		// Close <table> tag
			$order_details .= '</table>';

			// Create new order
			if ( 'yes' === ovabrw_get_setting( 'request_booking_create_order', 'no' ) ) {
				$order_data = [
					'customer_name' 	=> $customer_name,
					'customer_email' 	=> $customer_email,
					'customer_phone' 	=> $customer_phone,
					'customer_address' 	=> $customer_address,
					'customer_note' 	=> $customer_note,
					'pickup_date' 		=> $pickup_date,
					'dropoff_date' 		=> $dropoff_date,
					'numberof_guests' 	=> $numberof_guests,
					'cckf' 				=> $cckf,
					'cckf_qty' 			=> $cckf_qty,
					'cckf_value' 		=> $cckf_value,
					'extra_services' 	=> $extra_services
				];

				// Create new order
				$order_id = $this->request_booking_create_new_order( $order_data, $data );

				do_action( $this->prefix.'after_request_booking_create_new_order', $order_id, $order_data, $data, $this );
			}

			// Get mail body
			$mail_body = ovabrw_get_setting( 'request_booking_mail_content' );
        	if ( !$mail_body ) {
        		$mail_body = esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] - [ovabrw_order_pickoff_date]. [ovabrw_order_details]', 'ova-brw' );
        	}

        	// Replace body
        	$mail_body = str_replace( '[ovabrw_vehicle_name]', $product_link, $mail_body );
        	$mail_body = str_replace( '[ovabrw_order_pickup_date]', $pickup_date, $mail_body );
        	$mail_body = str_replace( '[ovabrw_order_pickoff_date]', $dropoff_date, $mail_body );
        	$mail_body = str_replace( '[ovabrw_order_details]', $order_details, $mail_body );

			return apply_filters( $this->prefix.'get_request_booking_mail_content', $mail_body, $data, $this );
		}

		/**
		 * Request booking create new order
		 */
		public function request_booking_create_new_order( $data = [], $args = [] ) {
			if ( !ovabrw_array_exists( $data ) ) return false;

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $data );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $data );

			// Number of guests
			$numberof_guests = (int)ovabrw_get_meta_data( 'numberof_guests', $data );

			// Custom checkout fields
			$cckf 		= ovabrw_get_meta_data( 'cckf', $data );
			$cckf_qty 	= ovabrw_get_meta_data( 'cckf_qty', $data );

			// Extra services
			$extra_services = ovabrw_get_meta_data( 'extra_services', $data );

			// Cart item
			$cart_item = [
				'pickup_date' 		=> strtotime( $pickup_date ),
	        	'dropoff_date' 		=> strtotime( $dropoff_date ),
	        	'numberof_guests' 	=> $numberof_guests,
	        	'cckf'  			=> $cckf,
	        	'cckf_qty' 			=> $cckf_qty,
	        	'extra_services' 	=> $extra_services
			];

			// Guest data
			$guests = $guest_info = [];

			// Get guest options
			$guest_options = OVABRW()->options->get_guest_options( $this->get_id() );

			// Guest information enabled
			$guest_info_enabled = OVABRW()->options->guest_info_enabled( $this->get_id() );

			// Loop guest options
			foreach ( $guest_options as $guest ) {
				// Number of guest
				$numberof_guest = (int)ovabrw_get_meta_data( OVABRW_PREFIX.'numberof_'.$guest['name'], $args );

				// Add to cart item
				$cart_item['numberof_'.$guest['name']] = $numberof_guest;

				// Add number of guest to guest data
				if ( $numberof_guest || apply_filters( OVABRW_PREFIX.'numberof_guests_is_zero', false ) ) {
					$guests['ovabrw_numberof_'.$guest['name']] = $numberof_guest;

					// Get guest information data
					if ( $guest_info_enabled && $numberof_guest ) {
						$guest_info_item = OVABRW()->options->get_guest_info_data( $guest['name'] );
						
						if ( ovabrw_array_exists( $guest_info_item ) ) {
							$guest_info[$guest['name']] = $guest_info_item;
						}
					}
				}
			} // END loop guest options

			// Subtotal
	        $subtotal = ovabrw_convert_price( $this->get_total( $cart_item ));
	        
	        // Set order total
	        $order_total = $subtotal;

	        // Create new order
	        $new_order = wc_create_order([
	        	'status'        => '',
	        	'customer_note' => ovabrw_get_meta_data( 'customer_note', $data )
	        ]);

	        // Order id
	        $order_id = $new_order->get_id();

	        // Billing
	        $new_order->set_address([
	        	'first_name' 	=> ovabrw_get_meta_data( 'customer_name', $data ), // First name
			    'last_name' 	=> '', // Last name
			    'company'       => '', // Company name
			    'address_1'     => ovabrw_get_meta_data( 'customer_address', $data ), // Address line 1
			    'address_2'     => '', // Address line 2
			    'city'          => '', // City
			    'state'         => '', // State or county
			    'postcode'      => '', // Postcode or ZIP
			    'country'       => '', // Country code (ISO 3166-1 alpha-2)
			    'email'         => ovabrw_get_meta_data( 'customer_email', $data ), // Email address
			    'phone'         => ovabrw_get_meta_data( 'customer_phone', $data ) // Phone number
	        ], 'billing' );

	        // Set customer
            $user = get_user_by( 'email', ovabrw_get_meta_data( 'customer_email', $data ) );
            if ( $user ) {
                $new_order->set_customer_id( $user->ID );
            }

            // Tax enabled
	        $tax_amount = $tax_rate_id = 0;

	        // Taxable
            $item_taxes = false;
	        if ( wc_tax_enabled() ) {
	        	// Get tax rates
	        	$tax_rates = WC_Tax::get_rates( $this->product->get_tax_class() );

	        	// Tax rate id
                if ( ovabrw_array_exists( $tax_rates ) ) {
		            $tax_rate_id = key( $tax_rates );
		        }

		        // Prices include tax
	        	if ( wc_prices_include_tax() ) {
		        	$taxes 		= WC_Tax::calc_inclusive_tax( $subtotal, $tax_rates );
		        	$tax_amount = WC_Tax::get_tax_total( $taxes );
                    $subtotal 	-= $tax_amount;
	        	} else {
	        		$taxes 		= WC_Tax::calc_exclusive_tax( $subtotal, $tax_rates );
                    $tax_amount = WC_Tax::get_tax_total( $taxes );
                    $order_total += $tax_amount;
	        	}

	        	// Item taxes
	        	$item_taxes = [
	        		'total'    => $taxes,
                    'subtotal' => $taxes
	        	];
	        }

	        // Handle items
	        $item_id = $new_order->add_product( $this->product, 1, [
	        	'totals' => [
	        		'subtotal' 	=> $subtotal,
	                'total' 	=> $subtotal
	        	]
	        ]);

	        // Get order line item
        	$line_item = $new_order->get_item( $item_id );
        	if ( $line_item ) {
        		// Rental type
        		$line_item->add_meta_data( 'rental_type', $this->get_type(), true );

        		// Charged by
        		$line_item->add_meta_data( 'define_day', $this->product->get_charged_by(), true );

        		// Pick-up date
        		if ( $pickup_date ) {
        			$line_item->add_meta_data( 'ovabrw_pickup_date', $pickup_date, true );
        			$line_item->add_meta_data( 'ovabrw_pickup_date_strtotime', strtotime( $pickup_date ), true );
        			$line_item->add_meta_data( 'ovabrw_pickup_date_real', $pickup_date, true );
        		}

        		// Drop-off date
        		if ( $dropoff_date ) {
        			$line_item->add_meta_data( 'ovabrw_pickoff_date', $dropoff_date, true );
        			$line_item->add_meta_data( 'ovabrw_pickoff_date_strtotime', strtotime( $dropoff_date ), true );
        			$line_item->add_meta_data( 'ovabrw_pickoff_date_real', $dropoff_date, true );
        		}

				// Number of guests
	        	$line_item->add_meta_data( 'ovabrw_numberof_guests', $numberof_guests, true );

		    	// Guests
		    	if ( ovabrw_array_exists( $guests ) ) {
		    		foreach ( $guests as $guest_name => $numberof_guest ) {
		    			$line_item->add_meta_data( $guest_name, $numberof_guest, true );
		    		}
		    	}

		    	// Guest info
		    	if ( ovabrw_array_exists( $guest_info ) ) {
		    		$line_item->add_meta_data( 'ovabrw_guest_info', $guest_info, true );
		    	}

		    	// Custom checkout fields
		    	if ( ovabrw_array_exists( $cckf ) ) {
		    		// CCKF value
		    		$cckf_value = ovabrw_get_meta_data( 'cckf_value', $data );
		    		if ( ovabrw_array_exists( $cckf_value ) ) {
		    			foreach ( $cckf_value as $name => $value ) {
		    				$line_item->add_meta_data( $name, $value, true );
		    			}
		    		}

		    		// CCKF
		    		$line_item->add_meta_data( 'ovabrw_custom_ckf', $cckf, true );

		    		// CCKF quantity
		    		if ( ovabrw_array_exists( $cckf_qty ) ) {
		    			$line_item->add_meta_data( 'ovabrw_custom_ckf_qty', $cckf_qty, true );
		    		}
		    	} // END if

		    	// Extra services
	        	if ( ovabrw_array_exists( $extra_services ) ) {
	        		// Loop
					foreach ( $extra_services as $item_service ) {
						$label 		= ovabrw_get_meta_data( 'label', $item_service );
						$display 	= ovabrw_get_meta_data( 'display', $item_service );

						if ( 'dropdown' === $display ) {
							$opt_name = ovabrw_get_meta_data( 'option_name', $item_service );
							if ( !$opt_name ) continue;

							if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
								$opt_guests = [];

								// Loop guests
								foreach ( $guest_options as $guest ) {
									$numberof_guest = isset( $item_service[$guest['name']] ) ? (int)$item_service[$guest['name']] : 0;

									if ( $numberof_guest ) {
										array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
									}
								} // END loop guests
								
								$opt_name .= ' ('.implode(", ", $opt_guests).')';
							}

							// Add meta data
							$line_item->add_meta_data( $label, apply_filters( OVABRW_PREFIX.'item_data_service_dropdown', $opt_name, $item_service ), true );
						} elseif ( 'checkbox' === $display ) {
							$opt_value 	= '';
							$opt_names 	= ovabrw_get_meta_data( 'option_name', $item_service, [] );

							foreach ( $opt_names as $k => $opt_name ) {
								if ( !$opt_name ) continue;

								$opt_value .= $opt_name;

								if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
									$opt_guests = [];

									// Loop guests
									foreach ( $guest_options as $guest ) {
										$numberof_guest = isset( $item_service[$guest['name']][$k] ) ? (int)$item_service[$guest['name']][$k] : 0;

										if ( $numberof_guest ) {
											array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
										}
									} // END loop guests
									
									$opt_value .= ' ('.implode(", ", $opt_guests).')';
								}

								if ( $k < count( $opt_names ) - 1 ) $opt_value .= ', ';
							}

							// Add meta data
							$line_item->add_meta_data( $label, apply_filters( OVABRW_PREFIX.'item_data_service_checkbox', $opt_value, $item_service ), true );
						}
					} // END loop

					// Add service data to item meta data
					$line_item->add_meta_data( 'ovabrw_extra_services', $extra_services, true );
	        	}

		    	// Update item tax
	            $line_item->set_props([
	            	'taxes' => $item_taxes
	            ]);

	            // Save item
	            $line_item->save();
        	}

        	// Set order tax
	        if ( wc_tax_enabled() && $tax_amount ) {
	        	// New order item tax
	            $item_tax = new WC_Order_Item_Tax();

	            // Set taxes
	            $item_tax->set_props([
	            	'rate_id'            => $tax_rate_id,
	                'tax_total'          => $tax_amount,
	                'shipping_tax_total' => 0,
	                'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
	                'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
	                'compound'           => WC_Tax::is_compound( $tax_rate_id ),
	                'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id )
	            ]);

	            // Save
	            $item_tax->save();
	            $new_order->add_item( $item_tax );
	            $new_order->set_cart_tax( $tax_amount );
	        } // END if

	        // Set order status
	        $new_order->set_status( ovabrw_get_setting( 'request_booking_order_status', 'wc-on-hold' ) );

	        // Set date created
	        $new_order->set_date_created( gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );

	        // Set total
	        $new_order->set_total( $order_total );

	        // Save
	        $new_order->save();

	        // Insert order queue
            OVABRW_Order_Queues::instance()->insert_order( $new_order );

			return apply_filters( $this->prefix.'request_booking_create_new_order', $order_id, $data, $args, $this );
		}

		/**
		 * New booking handle item
		 */
		public function new_booking_handle_item( $meta_key, $args, $order ) {
			// init
			$results = [
				'is_deposit' 		=> false,
				'deposit_amount' 	=> 0,
				'remaining_amount' 	=> 0,
				'remaining_tax' 	=> 0,
				'tax_rate_id' 		=> 0,
				'tax_amount' 		=> 0,
				'subtotal' 			=> 0
			];

			// Item meta data
			$item_meta = [];

			// Rental type
			$item_meta['rental_type'] = $this->get_type();

			// Pick-up date
			$pickup_date = isset( $args['ovabrw_pickup_date'][$meta_key] ) ? $args['ovabrw_pickup_date'][$meta_key] : '';
			if ( $pickup_date ) {
				$item_meta['ovabrw_pickup_date'] 			= $pickup_date;
				$item_meta['ovabrw_pickup_date_strtotime'] 	= strtotime( $pickup_date );
	    		$item_meta['ovabrw_pickup_date_real'] 		= $pickup_date;
			}

			// Drop-off date
			$dropoff_date = isset( $args['ovabrw_dropoff_date'][$meta_key] ) ? $args['ovabrw_dropoff_date'][$meta_key] : '';
			if ( !$dropoff_date ) $dropoff_date = $pickup_date;
			if ( $dropoff_date ) {
				$item_meta['ovabrw_pickoff_date'] 			= $dropoff_date;
				$item_meta['ovabrw_pickoff_date_strtotime'] = strtotime( $dropoff_date );
				$item_meta['ovabrw_pickoff_date_real'] 		= $dropoff_date;
			}

			// Number of guests
			$numberof_guests = 0;

	    	// Guest info
	    	$guest_info = [];

	    	// Get guest options
			$guest_options = $this->get_guest_options();
			foreach ( $guest_options as $guest ) {
				// Get number of guest
				$numbe_guest = isset( $args['ovabrw_numberof_'.$guest['name']][$meta_key] ) ? (int)$args['ovabrw_numberof_'.$guest['name']][$meta_key] : 0;

				// Number number of guests
				$numberof_guests += $numbe_guest;

				// Add number of guest
				if ( $numbe_guest || apply_filters( OVABRW_PREFIX.'numberof_guests_is_zero', false ) ) {
					$item_meta['ovabrw_numberof_'.$guest['name']] = $numbe_guest;
        		}

				// Guest info item
				$guest_info_item = isset( $args['ovabrw_'.$guest['name'].'_info'][$meta_key] ) ? $args['ovabrw_'.$guest['name'].'_info'][$meta_key] : '';
				if ( ovabrw_array_exists( $guest_info_item ) ) {
					// Get guest info data
					$guest_info_data = $this->get_guest_info_data( $guest['name'], $guest_info_item );

					if ( ovabrw_array_exists( $guest_info_data ) ) {
						$guest_info[$guest['name']] = $guest_info_data;
					}
				}
			} // END loop

			// Add number of guests to item meta
			$item_meta['ovabrw_numberof_guests'] = $numberof_guests;

			// Guest info
			if ( ovabrw_array_exists( $guest_info ) ) {
				$item_meta['ovabrw_guest_info'] = $guest_info;
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
	    				// Attachment ID
						$attachment_id = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';
						if ( $attachment_id ) {
							$file_name 	= get_the_title( $attachment_id );
							$file_url 	= wp_get_attachment_url( $attachment_id );

							// File url
	                        $file_url = '<a href="'.esc_url( $file_url ).'" target="_blank">';
								$file_url .= $file_name;
							$file_url .= '</a>';

							// Add cart item data
							$item_meta[$name] = $file_url;
						}
	    			} elseif ( 'checkbox' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options values
	    				$opt_values = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';

	    				if ( ovabrw_array_exists( $opt_values ) ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_values;

	    					// Option quantities
	    					$opt_qtys = isset( $args[$name.'_qty'][$meta_key] ) ? $args[$name.'_qty'][$meta_key] : '';
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
	    					$item_meta[$name] = implode( ', ', $opt_names );
	    				}
	    			} elseif ( 'radio' === $type ) {
	    				// Get option value
	    				$opt_value = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';
	    				if ( $opt_value ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_value;

	    					// Get option quantities
	    					$opt_qtys = isset( $args[$name.'_qty'][$meta_key] ) ? $args[$name.'_qty'][$meta_key] : '';

	    					// Option qty
	    					$opt_qty = (int)ovabrw_get_meta_data( $opt_value, $opt_qtys );

	    					// Add cart item data
	    					if ( $opt_qty ) {
	    						// Add cckf quantity
	    						$cckf_qty[$name] = $opt_qty;
	    						$item_meta[$name] = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_value, $opt_qty );
	    					} else {
	    						$item_meta[$name] = $opt_value;
	    					}
	    				}
	    			} elseif ( 'select' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options value
	    				$opt_value = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';
	    				if ( $opt_value ) {
	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_options_key', $fields );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_options_text', $fields );

	    					// Option quantities
	    					$opt_qtys = isset( $args[$name.'_qty'][$meta_key] ) ? $args[$name.'_qty'][$meta_key] : '';
	    					
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
	    						$cckf_qty[$name] = $opt_qty;
	    						$item_meta[$name] = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_text, $opt_qty );
	    					} else {
	    						$item_meta[$name] = $opt_text;
	    					}
	    				}
	    			} else {
	    				// Option value
	    				$opt_value = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';

	    				if ( $opt_value ) {
	    					// Add cart item data
	    					$item_meta[$name] = $opt_value;
	    				}
	    			}
	    		} // END loop
	    	}

	    	// Add cckf to cart item data
	    	if ( ovabrw_array_exists( $cckf ) ) {
	    		$item_meta['ovabrw_custom_ckf'] 	= $cckf;
	    		$item_meta['ovabrw_custom_ckf_qty'] = $cckf_qty;
	    	}

	    	// Extra services
	    	$service_ids = $this->get_meta_value( 'extra_service_id' );
	    	if ( ovabrw_array_exists( $service_ids ) ) {
	    		// Extra services
				$extra_services = [];

				// Services data
				$service_labels = $this->get_meta_value( 'extra_service_label' );
				$service_types 	= $this->get_meta_value( 'extra_service_display' );
				$service_guests = $this->get_meta_value( 'extra_service_guests' );

			    // Options data
			    $option_ids 	= $this->get_meta_value( 'extra_service_option_id' );
			    $option_names 	= $this->get_meta_value( 'extra_service_option_name' );
			    $option_types 	= $this->get_meta_value( 'extra_service_option_type' );

			    // Loop
    			foreach ( $service_ids as $k => $service_id ) {
    				// Get service values
    				$serv_values = isset( $args[$service_id][$meta_key] ) ? $args[$service_id][$meta_key] : '';
    				if ( empty( $serv_values ) ) continue;

    				// Get service guests
    				$serv_guests = isset( $args[$service_id.'_guests'][$meta_key] ) ? $args[$service_id.'_guests'][$meta_key] : [];

    				// Label
		    		$label = ovabrw_get_meta_data( $k, $service_labels );

		    		// Display
		    		$display = ovabrw_get_meta_data( $k, $service_types );

		    		// Choose guests
		    		$choose_guests = ovabrw_get_meta_data( $k, $service_guests );

		    		// Option ids
		    		$opt_ids = ovabrw_get_meta_data( $k, $option_ids, [] );

		    		// Option names
		    		$opt_names = ovabrw_get_meta_data( $k, $option_names, [] );

		    		// Option types
		    		$opt_types = ovabrw_get_meta_data( $k, $option_types, [] );

		    		if ( is_array( $serv_values ) ) {
		    			// init
		    			$data_opt_ids = $data_opt_names = $data_opt_types = $data_opt_guests = $data_opt_prices = [];

		    			foreach ( $serv_values as $opt_id ) {
		    				// Get option index
							$opt_index = array_search( $opt_id, $opt_ids );

							if ( false !== $opt_index ) {
								$opt_name = ovabrw_get_meta_data( $opt_index, $opt_names );
								$opt_type = ovabrw_get_meta_data( $opt_index, $opt_types );

								// Add data
								$data_opt_ids[] 	= $opt_id;
								$data_opt_names[] 	= $opt_name;
								$data_opt_types[] 	= $opt_type;

								// Guest data
								foreach ( $guest_options as $guest ) {
									// Get option number of guests
									if ( !isset( $data_opt_guests[$guest['name']] ) ) {
										$data_opt_guests[$guest['name']] = [];
									}

									if ( 'auto' === $choose_guests ) {
										$opt_guest = isset( $args['ovabrw_numberof_'.$guest['name']][$meta_key] ) ? (int)$args['ovabrw_numberof_'.$guest['name']][$meta_key] : 0;
									} else {
										$opt_guest = isset( $serv_guests[$opt_id][$guest['name']] ) ? (int)$serv_guests[$opt_id][$guest['name']] : 0;
									}

									array_push( $data_opt_guests[$guest['name']], $opt_guest );

									// Get guest price
									if ( !isset( $data_opt_prices[$guest['name'].'_price'] ) ) {
										$data_opt_prices[$guest['name'].'_price'] = [];
									}

									$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

									// Guest option price
									$guest_price = isset( $guest_prices[$k][$opt_index] ) ? (float)$guest_prices[$k][$opt_index] : 0;

									array_push( $data_opt_prices[$guest['name'].'_price'], $guest_price );
								}
							}
		    			}

		    			// Add data
		    			if ( ovabrw_array_exists( $data_opt_ids ) ) {
		    				// Add extra services
		    				$extra_services[$service_id] = array_merge_recursive([
		    					'label' 		=> $label,
		    					'display' 		=> $display,
		    					'option_id' 	=> $data_opt_ids,
		    					'option_name' 	=> $data_opt_names,
		    					'option_type' 	=> $data_opt_types
		    				], $data_opt_guests, $data_opt_prices );
		    			}
		    		} else {
		    			// Option id
		    			$opt_id = $serv_values;

		    			// Get option index
						$opt_index = array_search( $opt_id, $opt_ids );

						if ( false !== $opt_index ) {
							$opt_name = ovabrw_get_meta_data( $opt_index, $opt_names );
							$opt_type = ovabrw_get_meta_data( $opt_index, $opt_types );

							// Add extra services
							$extra_services[$service_id] = [
								'label' 		=> $label,
		    					'display' 		=> $display,
		    					'option_id' 	=> $opt_id,
		    					'option_name' 	=> $opt_name,
		    					'option_type' 	=> $opt_type
							];

							// Guest data
							foreach ( $guest_options as $guest ) {
								// Opiton number of guests
								if ( 'auto' === $choose_guests ) {
									$opt_guest = isset( $args['ovabrw_numberof_'.$guest['name']][$meta_key] ) ? (int)$args['ovabrw_numberof_'.$guest['name']][$meta_key] : 0;
								} else {
									$opt_guest = isset( $serv_guests[$opt_id][$guest['name']] ) ? (int)$serv_guests[$opt_id][$guest['name']] : 0;
								}

								// Add number of guests to service data
								$extra_services[$service_id][$guest['name']] = $opt_guest;

								// Get guest price
								$guest_prices = $this->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );

								// Guest price
								$guest_price = isset( $guest_prices[$k][$opt_index] ) ? (float)$guest_prices[$k][$opt_index] : 0;

								// Add guest price to service data
								$extra_services[$service_id][$guest['name'].'_price'] = $guest_price;
							}
						}
		    		}
    			} // END foreach

    			// Add service data to cart item data
				if ( ovabrw_array_exists( $extra_services ) ) {
					// Loop
					foreach ( $extra_services as $item_service ) {
						$label 		= ovabrw_get_meta_data( 'label', $item_service );
						$display 	= ovabrw_get_meta_data( 'display', $item_service );

						if ( 'dropdown' === $display ) {
							$opt_name = ovabrw_get_meta_data( 'option_name', $item_service );
							if ( !$opt_name ) continue;

							if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
								$opt_guests = [];

								// Loop guests
								foreach ( $guest_options as $guest ) {
									$numberof_guest = isset( $item_service[$guest['name']] ) ? (int)$item_service[$guest['name']] : 0;

									if ( $numberof_guest ) {
										array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
									}
								} // END loop guests
								
								$opt_name .= ' ('.implode(", ", $opt_guests).')';
							}

							// Add meta data
							$item_meta[$label] = apply_filters( OVABRW_PREFIX.'item_data_service_dropdown', $opt_name, $item_service );
						} elseif ( 'checkbox' === $display ) {
							$opt_value 	= '';
							$opt_names 	= ovabrw_get_meta_data( 'option_name', $item_service, [] );

							foreach ( $opt_names as $k => $opt_name ) {
								if ( !$opt_name ) continue;

								$opt_value .= $opt_name;

								if ( apply_filters( OVABRW_PREFIX.'extra_service_add_numberof_guests_to_name', true ) ) {
									$opt_guests = [];

									// Loop guests
									foreach ( $guest_options as $guest ) {
										$numberof_guest = isset( $item_service[$guest['name']][$k] ) ? (int)$item_service[$guest['name']][$k] : 0;

										if ( $numberof_guest ) {
											array_push( $opt_guests, sprintf( '%s: %s', $guest['label'], $numberof_guest ) );
										}
									} // END loop guests
									
									$opt_value .= ' ('.implode(", ", $opt_guests).')';
								}

								if ( $k < count( $opt_names ) - 1 ) $opt_value .= ', ';
							}

							// Add meta data
							$item_meta[$label] = apply_filters( OVABRW_PREFIX.'item_data_service_checkbox', $opt_value, $item_service );
						}
					} // END loop

					// Add extra services
					$item_meta['ovabrw_extra_services'] = $extra_services;
				}
	    	} // END extra services

			// Deposit
			$deposit = isset( $args['ovabrw_amount_deposite'][$meta_key] ) ? (float)$args['ovabrw_amount_deposite'][$meta_key] : '';

			// Remaining
			$remaining = isset( $args['ovabrw_amount_remaining'][$meta_key] ) ? (float)$args['ovabrw_amount_remaining'][$meta_key] : '';

			// Subtotal
			$subtotal = isset( $args['ovabrw_total'][$meta_key] ) ? (float)$args['ovabrw_total'][$meta_key] : 0;
			if ( $deposit ) {
				// Item data
				$results['is_deposit'] = true;

				// Deposit amount
				$results['deposit_amount'] = $deposit;

				// Remaining amount
				$results['remaining_amount'] = $remaining;

				// Subtotal
				$results['subtotal'] = $deposit;

				// Deposit type
				$item_meta['ovabrw_deposit_type'] = 'value';

				// Deposit value
				$item_meta['ovabrw_deposit_value'] = $deposit;

				// Deposit amount
				$item_meta['ovabrw_deposit_amount'] = $deposit;

				// Remaining
				$item_meta['ovabrw_remaining_amount'] = $remaining;

				// Total payable
				$item_meta['ovabrw_total_payable'] = $subtotal;

				// Subtotal
				$subtotal = $deposit;
			} else {
				// Subtotal
				$results['subtotal'] = $subtotal;
			} // END if

			// Taxable
			$taxes = false;
			if ( wc_tax_enabled() ) {
				$tax_rates = WC_Tax::get_rates( $this->product->get_tax_class() );
                if ( ovabrw_array_exists( $tax_rates ) ) {
                    $results['tax_rate_id'] = key( $tax_rates );
                }

                // Remaining tax
                $remaining_tax = OVABRW()->options->get_taxes_by_price( $this->product, $remaining );
                if ( $remaining_tax ) {
                	// Item data
                	$results['remaining_tax'] = $remaining_tax;

                	// Item meta
                	$item_meta['ovabrw_remaining_tax'] = $remaining_tax;
                }

                // Prices include tax
                if ( wc_prices_include_tax() ) {
                	// Get taxes
                    $taxes = WC_Tax::calc_inclusive_tax( $subtotal, $tax_rates );

                    // Tax total
                    $tax_total = WC_Tax::get_tax_total( $taxes );

                    // Subtotal
                    $subtotal -= $tax_total;

                    // Item data
                    $results['tax_amount'] += $tax_total;
                } else {
                	// Get taxes
                    $taxes = WC_Tax::calc_exclusive_tax( $item_subtotal, $tax_rates );

                    // Tax total
                    $tax_total = WC_Tax::get_tax_total( $taxes );
                    
                    // Item data
                    $results['tax_amount'] += $tax_total;

                    // Item data
                    $results['subtotal'] += $remaining_tax;
                }

                // Taxes
                $taxes = [
                	'total'    => $taxes,
                    'subtotal' => $taxes
                ];
			} // END if

			// Get item id
            $item_id = $order->add_product( $this->product, 1, [
                'totals' => [
                    'subtotal'  => $subtotal,
                    'total'     => $subtotal
                ],
                'taxes'  => $taxes
            ]);

            // Get order line item
            $item = $order->get_item( $item_id );

            // Update item meta data
            foreach ( $item_meta as $meta_key => $meta_value ) {
                $item->add_meta_data( $meta_key, $meta_value, true );
            }

            // Save item
            $item->save();

			return apply_filters( $this->prefix.'new_booking_handle_item', $results, $meta_key, $args, $order, $this );
		}
	}
}