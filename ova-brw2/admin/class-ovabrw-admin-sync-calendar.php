<?php use Automattic\WooCommerce\Utilities\OrderUtil;

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Get OVABRW_Sync_Calendar
 */
if ( !class_exists( 'OVABRW_Sync_Calendar' ) ) {

	class OVABRW_Sync_Calendar {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * API Key
		 */
		protected $api_key = null;

		/**
		 * Client ID
		 */
		protected $client_id = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Get Google API Key
			$this->api_key = ovabrw_get_setting( 'google_key_map' );

			// Get client ID
			$this->client_id = ovabrw_get_setting( 'gcal_client_id' );

			// Admin scripts
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

			// Load libraries
			add_action( 'admin_print_footer_scripts', [ $this, 'load_libraries' ], 99 );

			// Define ajax
			$define_ajax = [
				'download_ical',
				'get_orders_for_google_calendar'
			];
			foreach ( $define_ajax as $name ) {
				add_action( 'wp_ajax_'.OVABRW_PREFIX.$name, [ $this, OVABRW_PREFIX.$name ] );
				add_action( 'wp_ajax_nopriv_'.OVABRW_PREFIX.$name, [ $this, OVABRW_PREFIX.$name ] );
			}
		}

		/**
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {
			// Get version
			$version = OVABRW()->get_version();

			// Get screen
			$screen    	= get_current_screen();
			$screen_id 	= $screen ? $screen->id : '';

			// Get page
			$page = ovabrw_get_meta_data( 'page', $_GET );

			// Google calendar
			wp_register_script( 'ovabrw-google-calendar', OVABRW_PLUGIN_URI.'assets/js/admin/google-calendar.min.js', [ 'jquery' ], $version, true );

			// Sync calendar
			wp_register_script( 'ovabrw-sync-calendar', OVABRW_PLUGIN_URI.'assets/js/admin/sync-calendar.min.js', [ 'jquery' ], $version, true );

			// Manage bookings page
			if ( 'brw_page_ovabrw-manage-bookings' === $screen_id || 'ovabrw-manage-bookings' === $page ) {
				wp_enqueue_script( 'ovabrw-sync-calendar' );
				wp_localize_script( 'ovabrw-sync-calendar', 'ovabrwGgCal', [
					'apiKey' 	=> $this->api_key,
					'clientId' 	=> $this->client_id
				]);

				// Google calendar
				wp_enqueue_script( 'ovabrw-google-calendar' );
			}
		}

		/**
		 * Loading libs
		 */
		public function load_libraries() {
			// Get version
			$version = OVABRW()->get_version();

			// Get screen
			$screen    	= get_current_screen();
			$screen_id 	= $screen ? $screen->id : '';

			// Get page
			$page = ovabrw_get_meta_data( 'page', $_GET );

			// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
			if ( 'brw_page_ovabrw-manage-bookings' == $screen_id || 'ovabrw-manage-bookings' === $page && $this->api_key && $this->client_id ): ?>
				<script async defer src="https://apis.google.com/js/api.js" onload="ovabrwGapiLoaded()"></script>
		    	<script async defer src="https://accounts.google.com/gsi/client" onload="ovabrwGisLoaded()"></script>
			<?php endif; // phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript
		}

		/**
         * Get order ids between dates
         */
        public function get_order_ids_between_dates( $from_date, $to_date ) {
        	if ( !$from_date || !$to_date ) return false;

        	// Get order status
        	$order_status = apply_filters( OVABRW_PREFIX.'sync_order_status', [ 'wc-processing' ] );

        	// init
        	$order_ids = [];

        	global $wpdb;

        	if ( OVABRW()->options->custom_orders_table_usage() ) {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT o.id
                    FROM {$wpdb->prefix}wc_orders AS o
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi
                        ON o.id = oi.order_id
                        AND oi.order_item_type = %s
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
                        ON oi.order_item_id = oim.order_item_id
                    WHERE o.type = %s
                    	AND oim.meta_key = %s
                    	AND oim.meta_value BETWEEN %d AND %d
                        AND o.status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_pickup_date_strtotime',
                    	$from_date,
                    	$to_date
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	} else {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT oitems.order_id
                    FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                        ON oitems.order_item_id = oitem_meta.order_item_id
                    LEFT JOIN {$wpdb->posts} AS p
                        ON oitems.order_id = p.ID
                    WHERE oitems.order_item_type = %s
                        AND p.post_type = %s
                        AND oitem_meta.meta_key = %s
                        AND oitem_meta.meta_value BETWEEN %d AND %d
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_pickup_date_strtotime',
                    	$from_date,
                    	$to_date
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	}

        	return apply_filters( OVABRW_PREFIX.'get_order_ids_between_dates', $order_ids, $from_date, $to_date );
        }

        /**
         * Get events for ical
         */
        public function get_events_for_ical( $order_ids = [] ) {
        	if ( !ovabrw_array_exists( $order_ids ) ) return false;

        	// init file ICS
        	header('Content-Type: text/calendar; charset=utf-8');
		    $ics_content = "BEGIN:VCALENDAR\r\n";
		    $ics_content .= "PRODID:-//OVABRW//Ical Calendar//EN\r\n";
		    $ics_content .= "CALSCALE:GREGORIAN\r\n";
		    $ics_content .= "VERSION:2.0\r\n";
		    $ics_content .= "METHOD:PUBLISH\r\n";

		    // Get domain
		    $domain = parse_url( get_site_url(), PHP_URL_HOST );

		    // Loop
		    foreach ( $order_ids as $order_id ) {
		    	// Get order
                $order = wc_get_order( $order_id );
                if ( !$order ) continue;

                // Get currency
				$currency = $order->get_currency();

                // Get buyer
                $buyer = '';
                if ( ( method_exists( $order, 'get_billing_first_name' ) && $order->get_billing_first_name() ) || ( method_exists( $order, 'get_billing_last_name') && $order->get_billing_last_name() ) ) {
                    /* translators: 1: first name 2: last name */
                    $buyer = trim( sprintf( _x( '%1$s %2$s', 'full name', 'ova-brw' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
                } elseif ( method_exists( $order, 'get_billing_company' ) && $order->get_billing_company() ) {
                    $buyer = trim( $order->get_billing_company() );
                } elseif ( method_exists( $order, 'get_customer_id' ) && $order->get_customer_id() ) {
                    $user  = get_user_by( 'id', $order->get_customer_id() );
                    $buyer = ucwords( $user->display_name );
                }

		        // Get order items
                $order_items = $order->get_items();
                if ( !ovabrw_array_exists( $order_items ) ) continue;

                // Loop order items
                foreach ( $order_items as $item_id => $item ) {
                    // Get product
                    $product = method_exists( $item, 'get_product' ) ? $item->get_product() : '';
                    if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) continue;

                    // Get product name
    				$product_name = $item->get_name();

                    // Get summary
                    $summary = apply_filters( OVABRW_PREFIX.'ics_summary', sprintf( '#%d - %s', $order_id, $item->get_name() ), $order_id, $product_name );

                    // Get UID
                    $uid = apply_filters( OVABRW_PREFIX.'ics_uid', sprintf( '%d-%d@%s', $order_id, $item_id, $domain ), $order_id, $item_id, $domain );

                    // Pick-up date
                    $pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                    if ( !$pickup_date ) continue;

                    // Drop-off date
                    $dropoff_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
                    if ( !$dropoff_date ) continue;

                    // Get start date
                    $start_date = date( 'Ymd\THis\Z', $pickup_date );

                    // Get end date
			        $end_date = date( 'Ymd\THis\Z', $dropoff_date );

			        // Get address
			        $address = $order->get_shipping_address_1() . ', ' . $order->get_shipping_city();

			        // Get item total
			        $item_total = wc_format_decimal( $item->get_total(), wc_get_price_decimals() );

			        // Description
			        $description = sprintf( esc_html__( 'Customer: %s', 'ova-brw' ), esc_html( $buyer ) )."\n";
			        $description .= sprintf( esc_html__( 'Product: %s', 'ova-brw' ), esc_html( $product_name ) )."\n";
			        $description .= sprintf( esc_html__( 'Total: %s', 'ova-brw' ), esc_html( $item_total.$currency ) );
			        $description = str_replace( [ "\r\n", "\n", "\r" ], "\\n", $description );
			        $description = apply_filters( OVABRW_PREFIX.'ics_description', $description, $order_id, $item_id );

			        $ics_content .= "BEGIN:VEVENT\r\n";
			        $ics_content .= "UID:" . $uid . "\r\n";
			        $ics_content .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
			        $ics_content .= "DTSTART:" . $start_date . "\r\n";
			        $ics_content .= "DTEND:" . $end_date . "\r\n";
			        $ics_content .= "SUMMARY:" . $summary . "\r\n";
			        $ics_content .= "LOCATION:" . $address . "\r\n";
			        $ics_content .= "DESCRIPTION:" . $description . ".\r\n";
			        $ics_content .= "STATUS:CONFIRMED\r\n";
			        $ics_content .= "END:VEVENT\r\n";
                } // END loop
		    } // END if

		    $ics_content .= "END:VCALENDAR";
		    
		    return apply_filters( OVABRW_PREFIX.'get_events_for_ical', $ics_content, $order_ids );
        }

        /**
         * Get events for google calendar
         */
        public function get_events_for_gcal( $order_ids = [] ) {
        	if ( !ovabrw_array_exists( $order_ids ) ) return false;

        	// init events
        	$events = [];

        	// Get timezone
        	$timezone_string = wp_timezone_string();

        	// Get current user
        	$current_user = wp_get_current_user();

        	// Get user email
        	$user_email = $current_user->user_email;

        	// Loop
		    foreach ( $order_ids as $order_id ) {
		    	// Get order
                $order = wc_get_order( $order_id );
                if ( !$order ) continue;

                // Get currency
				$currency = $order->get_currency();

                // Get buyer
                $buyer = '';
                if ( ( method_exists( $order, 'get_billing_first_name' ) && $order->get_billing_first_name() ) || ( method_exists( $order, 'get_billing_last_name') && $order->get_billing_last_name() ) ) {
                    /* translators: 1: first name 2: last name */
                    $buyer = trim( sprintf( _x( '%1$s %2$s', 'full name', 'ova-brw' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
                } elseif ( method_exists( $order, 'get_billing_company' ) && $order->get_billing_company() ) {
                    $buyer = trim( $order->get_billing_company() );
                } elseif ( method_exists( $order, 'get_customer_id' ) && $order->get_customer_id() ) {
                    $user  = get_user_by( 'id', $order->get_customer_id() );
                    $buyer = ucwords( $user->display_name );
                }

		        // Get order items
                $order_items = $order->get_items();
                if ( !ovabrw_array_exists( $order_items ) ) continue;

                // Loop order items
                foreach ( $order_items as $item_id => $item ) {
                    // Get product
                    $product = method_exists( $item, 'get_product' ) ? $item->get_product() : '';
                    if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) continue;

                    // Get product name
    				$product_name = $item->get_name();

    				// Get product URL
    				$product_url = $product->get_permalink();

                    // Get summary
                    $summary = apply_filters( OVABRW_PREFIX.'gcal_summary', sprintf( '#%d - %s', $order_id, $item->get_name() ), $order_id, $product_name );

                    // Pick-up date
                    $pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                    if ( !$pickup_date ) continue;

                    // Drop-off date
                    $dropoff_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
                    if ( !$dropoff_date ) continue;

                    // Get start date
                    $start_date = date( 'c', $pickup_date );

                    // Get end date
			        $end_date = date( 'c', $dropoff_date );

			        // Get location
			        $location = $order->get_shipping_address_1() . ', ' . $order->get_shipping_city();

			        // Get item total
			        $item_total = wc_format_decimal( $item->get_total(), wc_get_price_decimals() );

			        // Product display name
			        $display_name = '<a href="' . esc_url( $product_url ) . '">' . esc_html( $product_name ) . '</a>';

			        // Description
			        $description = sprintf( esc_html__( 'Customer: %s', 'ova-brw' ), $buyer )."\n";
			        $description .= sprintf( esc_html__( 'Product: %s', 'ova-brw' ), $display_name )."\n";
			        $description .= sprintf( esc_html__( 'Total: %s', 'ova-brw' ), esc_html( $item_total.$currency ) );
			        $description = apply_filters( OVABRW_PREFIX.'gcal_description', $description, $order_id, $item_id );

			        // Add item to events
			        $events[] = [
			        	'successText' 	=> sprintf( esc_html__( 'Order #%s: Sync success.', 'ova-brw' ), $order_id ),
			        	'summary' 		=> $summary,
			        	'location' 		=> $location,
			        	'description' 	=> $description,
			        	'start' 		=> [
			        		'dateTime' => $start_date,
			        		'timeZone' => $timezone_string
			        	],
			        	'end' 			=> [
			        		'dateTime' => $end_date,
			        		'timeZone' => $timezone_string
			        	],
			        	'attendees' 	=> [
			        		[ 'email' => $user_email ]
			        	],
			        	'reminders' 	=> [
			        		'useDefault' 	=> false,
			        		'overrides' 	=> [
			        			[
			        				'method' 	=> 'email',
			        				'minutes' 	=> 24 * 60
			        			],
			        			[
			        				'method' 	=> 'popup',
			        				'minutes' 	=> 10
			        			]
			        		]
			        	]
			        ];
                }
            }

            return apply_filters( OVABRW_PREFIX.'get_events_for_gcal', $events, $order_ids );
        }

		/**
		 * Ajax download ical (.ics)
		 */
		public function ovabrw_download_ical() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get order id
			$order_id = absint( ovabrw_get_meta_data( 'order_id', $_POST ) );
			if ( $order_id ) {
				$order_ids = [ $order_id ];
			} else {
				// Get from date
				$from_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'from_date', $_POST ) ) );

				// Get to date
				$to_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'to_date', $_POST ) ) );

				// Get order ids
				$order_ids = $this->get_order_ids_between_dates( $from_date, $to_date );
				if ( !ovabrw_array_exists( $order_ids ) ) {
					wp_send_json_error([
			            'message' => esc_html__( 'No orders found!', 'ova-brw' )
			        ]);
					wp_die();
				}
			}

			// Get events
			$ics = $this->get_events_for_ical( $order_ids );
			wp_send_json_success([
		        'base64'   => base64_encode($ics),
		        'filename' => 'orders-' . gmdate('Y-m-d') . '.ics'
		    ]);

			wp_die();
		}

		/**
		 * Ajax get orders for google calendar
		 */
		public function ovabrw_get_orders_for_google_calendar() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get order id
			$order_id = absint( ovabrw_get_meta_data( 'order_id', $_POST ) );
			if ( $order_id ) {
				$order_ids = [ $order_id ];
			} else {
				// Get from date
				$from_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'from_date', $_POST ) ) );

				// Get to date
				$to_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'to_date', $_POST ) ) );

				// Get order ids
				$order_ids = $this->get_order_ids_between_dates( $from_date, $to_date );
				if ( !ovabrw_array_exists( $order_ids ) ) {
					echo json_encode([
						'mesg' => esc_html__( 'No orders found!', 'ova-brw' )
					]);
					wp_die();
				}
			}

			// Get events for google calendar
			$events = $this->get_events_for_gcal( $order_ids );
			if ( !ovabrw_array_exists( $events ) ) {
		        echo json_encode([
					'mesg' => esc_html__( 'No events found!', 'ova-brw' )
				]);
				wp_die();
			}

			// Results
			echo json_encode([
				'events' => $events
			]);
			wp_die();
		}

		/**
		 * Main OVABRW_Get_Data Instance.
		 */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
        }
	}

	// init class
	new OVABRW_Sync_Calendar();
}