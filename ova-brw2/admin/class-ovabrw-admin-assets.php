<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Assets class
 */
if ( !class_exists( 'OVABRW_Admin_Assets' ) ) {

	class OVABRW_Admin_Assets {
		/**
		 * Constructor.
		 */
		public function __construct() {
			// Admin head
			add_action( 'admin_head', [ $this, 'admin_head' ] );

			// Admin styles
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );

			// Admin scripts
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}

		/**
		 * Head
		 */
		public function admin_head() {
			$screen    	= get_current_screen();
			$screen_id 	= $screen ? $screen->id : '';

			// Product edit page
			if ( 'product' === $screen_id ) {
				// Custom taxonomies
				$taxonomies = [];

				// Get custom taxonomies
				$custom_taxonomies = ovabrw_create_type_taxonomies();

				if ( ovabrw_array_exists( $custom_taxonomies ) ) {
					foreach ( $custom_taxonomies as $taxonomy ) {
						array_push( $taxonomies, $taxonomy['slug'] );
					}
				}

				// Taxonomies depend category
				$depend = ovabrw_get_setting( 'search_show_tax_depend_cat', 'yes' );
				if ( 'yes' !== $depend ) $taxonomies = [];

				echo '<script type="text/javascript">
					var ovabrwTaxonomies = "'.implode( ',', $taxonomies ).'";
				</script>';
			}
		}

		/**
		 * Enqueue styles
		 */
		public function enqueue_styles() {
			// Get version
			$version = OVABRW()->get_version();

			// Get screen
			$screen    	= get_current_screen();
			$screen_id 	= $screen ? $screen->id : '';

			// Get page
			$page = ovabrw_get_meta_data( 'page', $_GET );

			// Admin styles
			wp_register_style( 'ovabrw-admin', OVABRW_PLUGIN_URI.'assets/css/admin/admin.css', [], $version );

			// noUiSlider
			wp_register_style( 'ova-nouislider', OVABRW_PLUGIN_URI.'assets/libs/nouislider/nouislider.min.css', [], $version );

			// Tippy scale stype
			wp_register_style( 'ovabrw-tippy-scale', OVABRW_PLUGIN_URI.'assets/libs/tippy/scale.css', [], $version );

			// Register timepicker style
			wp_register_style( 'ovabrw-admin-timepicker', OVABRW_PLUGIN_URI.'assets/libs/timepicker/timepicker.min.css', [], $version );

			// Jquery UI
			wp_register_style( 'ovabrw-jquery-ui', OVABRW_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.min.css', [], $version );
			
			// Select2
			wp_register_style( 'ovabrw-select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.css', [], $version );

			// Flaticon
			wp_register_style( 'ovabrw-flaticon', OVABRW_PLUGIN_URI.'assets/libs/flaticons/essential_set/flaticon.css', [], $version );

			// Brwicon 2
			wp_register_style( 'ovabrw-flaticon2', OVABRW_PLUGIN_URI.'assets/libs/flaticons/brwicon2/font/brwicon2.css', [], $version );

			// Open StreetMap
			if ( OVABRW()->options->osm_enabled() ) {
				// Leaflet
				wp_register_style( 'ovabrw-osm-leaflet', OVABRW_PLUGIN_URI.'assets/libs/osm/leaflet.css', [], $version );

				// Autocomplete
				wp_register_style( 'ovabrw-osm-autocomplete-button', OVABRW_PLUGIN_URI.'assets/libs/osm/autocomplete-button.css', [], $version );
				wp_register_style( 'ovabrw-osm-autocomplete', OVABRW_PLUGIN_URI.'assets/libs/osm/autocomplete.min.css', [], $version );

				// Routing
				wp_register_style( 'ovabrw-osm-routing', OVABRW_PLUGIN_URI.'assets/libs/osm/leaflet-routing-machine.css', [], $version );
			}

			// Create new booking
			wp_register_style( 'ovabrw-create-new-booking', OVABRW_PLUGIN_URI.'assets/css/admin/create-new-booking.css', [], $version );

			// Specifications
			wp_register_style( 'ovabrw-specifications', OVABRW_PLUGIN_URI.'assets/css/admin/specifications.css', [], $version );

			// Settings
			wp_register_style( 'ovabrw-settings', OVABRW_PLUGIN_URI.'assets/css/admin/settings.css', [], $version );

			// Import location
			wp_register_style( 'ovabrw-import-locations', OVABRW_PLUGIN_URI.'assets/css/admin/import-locations.css', [], $version );

			// Custom taxonomies
			wp_register_style( 'ovabrw-custom-taxonomies', OVABRW_PLUGIN_URI.'assets/css/admin/custom-taxonomies.css', [], $version );

			// Manage bookings
			wp_register_style( 'ovabrw-manage-bookings', OVABRW_PLUGIN_URI.'assets/css/admin/manage-bookings.css', [], $version );

			// Custom checkout fields
			wp_register_style( 'ovabrw-custom-checkout-fields', OVABRW_PLUGIN_URI.'assets/css/admin/custom-checkout-fields.css', [], $version );

			// Product Category
			wp_register_style( 'ovabrw-product-category', OVABRW_PLUGIN_URI.'assets/css/admin/product-category.css', [], $version );
			
			// Booking Calendar
			wp_register_style( 'ovabrw-booking-calendar', OVABRW_PLUGIN_URI.'assets/css/admin/booking-calendar.css', [], $version );

			// Edit order
			wp_register_style( 'ovabrw-wc-orders', OVABRW_PLUGIN_URI.'assets/css/admin/edit-order.css', [], $version );

			// Product editor
			wp_register_style( 'ovabrw-product-editor', OVABRW_PLUGIN_URI.'assets/css/admin/product-editor.css', [], $version );

			// Vehicle ID
			wp_register_style( 'ovabrw-vehicle', OVABRW_PLUGIN_URI.'assets/css/admin/vehicle.css', [], $version );

			// Register guest information fields style
			wp_register_style( 'ovabrw-guest-information', OVABRW_PLUGIN_URI.'assets/css/admin/guest-information.css', [], $version );

			// Global CSS
	        $css    = OVABRW()->options->datepicker_global_css();
	        $root   = ":root{{$css}}";
	        wp_add_inline_style( 'ovabrw-admin', $root );

			// Product edit page
			if ( 'product' == $screen_id ) {
				// Tippy scale stype
				wp_enqueue_style( 'ovabrw-tippy-scale' );

				// Timepicker
				wp_enqueue_style( 'ovabrw-admin-timepicker' );

				// Product editor
				wp_enqueue_style( 'ovabrw-product-editor' );

				// OSM - Autocomplete
				if ( OVABRW()->options->osm_enabled() ) {
					wp_enqueue_style( 'ovabrw-osm-leaflet' );

					// Autocomplete
			    	if ( OVABRW()->options->is_osm_lib_enabled( 'autocomplete' ) ) {
			    		wp_enqueue_style( 'ovabrw-osm-autocomplete' );
			    		wp_enqueue_style( 'ovabrw-osm-autocomplete-button' );
			    	}

			    	// Routing
			    	if ( OVABRW()->options->is_osm_lib_enabled( 'routing' ) ) {
			    		wp_enqueue_style( 'ovabrw-osm-routing' );
			    	}
				}
			} elseif ( 'brw_page_ovabrw-create-booking' == $screen_id || 'ovabrw-create-booking' === $page ) { // Add new booking
				// OSM - Autocomplete
				if ( OVABRW()->options->osm_enabled() ) {
					wp_enqueue_style( 'ovabrw-osm-leaflet' );

					// Autocomplete
			    	if ( OVABRW()->options->is_osm_lib_enabled( 'autocomplete' ) ) {
			    		wp_enqueue_style( 'ovabrw-osm-autocomplete' );
			    		wp_enqueue_style( 'ovabrw-osm-autocomplete-button' );
			    	}

			    	// Routing
			    	if ( OVABRW()->options->is_osm_lib_enabled( 'routing' ) ) {
			    		wp_enqueue_style( 'ovabrw-osm-routing' );
			    	}
				}
				
				// noUiSlider
				wp_enqueue_style( 'ova-nouislider' );

				// Select2
				wp_enqueue_style( 'ovabrw-select2' );

				// Tippy scale stype
				wp_enqueue_style( 'ovabrw-tippy-scale' );

				// Timepicker
				wp_enqueue_style( 'ovabrw-admin-timepicker' );

				wp_enqueue_style( 'ovabrw-create-new-booking' );
			} elseif ( 'vehicle' == $screen_id ) {
				// Timepicker
				wp_enqueue_style( 'ovabrw-admin-timepicker' );

				// Vehicle ID
				wp_enqueue_style( 'ovabrw-vehicle' );
			} elseif ( 'brw_page_ovabrw-import-location' == $screen_id || 'ovabrw-import-location' === $page ) { // Import location
				wp_enqueue_style( 'ovabrw-select2' );
				wp_enqueue_style( 'ovabrw-import-locations' );
			} elseif ( 'brw_page_ovabrw-specifications' == $screen_id || 'ovabrw-specifications' === $page ) { // Specifications
				wp_enqueue_style( 'ovabrw-jquery-ui' );
				wp_enqueue_style( 'ovabrw-specifications' );
			} elseif ( 'brw_page_ovabrw-manage-bookings' == $screen_id || 'ovabrw-manage-bookings' === $page ) { // Manage bookings
				// Tippy scale stype
				wp_enqueue_style( 'ovabrw-tippy-scale' );

				// Timepicker
				wp_enqueue_style( 'ovabrw-admin-timepicker' );

				wp_enqueue_style( 'ovabrw-manage-bookings' );
			} elseif ( 'brw_page_ovabrw-custom-checkout-field' == $screen_id || 'ovabrw-custom-checkout-field' === $page ) { // Custom checkout fields
				// Tippy scale stype
				wp_enqueue_style( 'ovabrw-tippy-scale' );
				
				wp_enqueue_style( 'ovabrw-jquery-ui' );
				wp_enqueue_style( 'ovabrw-custom-checkout-fields' );
			} elseif ( 'brw_page_ovabrw-booking-calendar' == $screen_id || 'ovabrw-booking-calendar' === $page ) { // Booking calendar
				// Select2
				wp_enqueue_style( 'ovabrw-select2' );

				// Timepicker
				wp_enqueue_style( 'ovabrw-admin-timepicker' );

				// Tippy
				wp_enqueue_style( 'ovabrw-tippy-scale' );

				// Booking Calendar
				wp_enqueue_style( 'ovabrw-booking-calendar' );
			}
			elseif ( 'brw_page_ovabrw-custom-taxonomy' == $screen_id || 'ovabrw-custom-taxonomy' === $page ) { // Custom taxonomies
				wp_enqueue_style( 'ovabrw-jquery-ui' );

				// Tippy scale stype
				wp_enqueue_style( 'ovabrw-tippy-scale' );

				// Custom taxonomy
				wp_enqueue_style( 'ovabrw-custom-taxonomies' );
			} elseif ( 'woocommerce_page_wc-settings' == $screen_id ) { // Rental settings
				// Tippy
				wp_enqueue_style( 'ovabrw-tippy-scale' );

				// Guest information
				wp_enqueue_style( 'ovabrw-guest-information' );

				// Settings
				wp_enqueue_style( 'ovabrw-settings' );
			} elseif ( 'edit-product_cat' == $screen_id ) { // Product category
				wp_enqueue_style( 'ovabrw-product-category' );
			} elseif ( 'woocommerce_page_wc-orders' == $screen_id || 'edit-shop_order' == $screen_id || 'shop_order' == $screen_id ) { // Shop order page
				wp_enqueue_style( 'ovabrw-wc-orders' );
			}

			// Flaticon
			wp_enqueue_style( 'ovabrw-flaticon' );
			wp_enqueue_style( 'ovabrw-flaticon2' );

			// Admin styles
			wp_enqueue_style( 'ovabrw-admin' );
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

			// Admin scripts
			wp_register_script( 'ovabrw-admin-scripts', OVABRW_PLUGIN_URI.'assets/js/admin/admin_script.min.js', [ 'jquery' ], $version, true );

			// noUiSlider
			wp_register_script( 'ova-wnumb', OVABRW_PLUGIN_URI.'assets/libs/nouislider/wNumb.min.js', [ 'jquery' ], $version, true );
			wp_register_script( 'ova-nouislider', OVABRW_PLUGIN_URI.'assets/libs/nouislider/nouislider.min.js', [ 'jquery' ], $version, true );

			// Tippy
			wp_register_script( 'ovabrw-popper', OVABRW_PLUGIN_URI.'assets/libs/tippy/popper.min.js', [ 'jquery' ], $version, true );
			wp_register_script( 'ovabrw-tippy-bundle', OVABRW_PLUGIN_URI.'assets/libs/tippy/tippy-bundle.min.js', [ 'jquery' ], $version, true );

			// Register timepicker script
			wp_register_script( 'ovabrw-admin-timepicker', OVABRW_PLUGIN_URI.'assets/libs/timepicker/timepicker.min.js', [ 'jquery' ], $version, true );

			// Register easepick script
			wp_register_script( 'ovabrw-admin-easepick', OVABRW_PLUGIN_URI.'assets/libs/easepick/easepick.min.js', [ 'jquery' ], $version, true );

			// Jquery UI
			wp_register_script( 'ovabrw-jquery-ui', OVABRW_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.min.js', [ 'jquery' ], $version, true );
			
			// Jquery UI Slider for price fields
			wp_enqueue_script( 'jquery-ui-slider' );

			// Calendar
		    wp_register_script( 'ovabrw-calendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/index.global.min.js', [ 'jquery' ], $version, true );
		    wp_register_script( 'ovabrw-calendar-locales', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/locales-all.global.min.js', [ 'jquery' ], $version, true );

		    // Select2
		    wp_register_script( 'ovabrw-select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.js', [ 'jquery' ], $version, true );

		    // Open StreetMap
		    if ( OVABRW()->options->osm_enabled() ) {
		    	// Leaflet
		    	wp_register_script( 'ovabrw-osm-leaflet', OVABRW_PLUGIN_URI.'assets/libs/osm/leaflet.js', [ 'jquery' ], $version, true );

		    	// Autocomplete
		    	wp_register_script( 'ovabrw-osm-autocomplete', OVABRW_PLUGIN_URI.'assets/libs/osm/autocomplete.min.js', [ 'jquery', 'ovabrw-osm-leaflet' ], $version, true );

		    	// Routing
		    	wp_register_script( 'ovabrw-osm-routing', OVABRW_PLUGIN_URI.'assets/libs/osm/leaflet-routing-machine.min.js', [ 'jquery', 'ovabrw-osm-leaflet' ], $version, true );
		    	
		    	// OSM
		    	wp_register_script( 'ovabrw-osm', OVABRW_PLUGIN_URI.'assets/js/admin/osm.min.js', [ 'jquery', 'ovabrw-osm-leaflet', 'ovabrw-osm-autocomplete' ], $version, true );
		    }

		    // Custom taxonomies
		   	wp_register_script( 'ovabrw-custom-taxonomies', OVABRW_PLUGIN_URI.'assets/js/admin/custom-taxonomies.min.js', [ 'jquery' ], $version, true );

		    // Custom checkout fields
		   	wp_register_script( 'ovabrw-custom-checkout-fields', OVABRW_PLUGIN_URI.'assets/js/admin/custom-checkout-fields.min.js', [ 'jquery' ], $version, true );

		   	// Booking Calendar
		   	wp_register_script( 'ovabrw-booking-calendar', OVABRW_PLUGIN_URI.'assets/js/admin/booking-calendar.min.js', [ 'jquery' ], $version, true );

		   	// Manage booking
		   	wp_register_script( 'ovabrw-manage-bookings', OVABRW_PLUGIN_URI.'assets/js/admin/manage-bookings.min.js', [ 'jquery' ], $version, true );

		   	// Specifications
		   	wp_register_script( 'ovabrw-specifications', OVABRW_PLUGIN_URI.'assets/js/admin/specifications.min.js', [ 'jquery' ], $version, true );

		   	// Create new booking
		   	wp_register_script( 'ovabrw-create-new-booking', OVABRW_PLUGIN_URI.'assets/js/admin/create-new-booking.min.js', [ 'jquery', 'jquery-ui-autocomplete', 'jquery-ui-slider' ], $version, true );

		   	// Settings
		   	wp_register_script( 'ovabrw-settings', OVABRW_PLUGIN_URI.'assets/js/admin/settings.min.js', [ 'jquery' ], $version, true );

		   	// Edit order
		   	wp_register_script( 'ovabrw-wc-orders', OVABRW_PLUGIN_URI.'assets/js/admin/edit-order.min.js', [ 'jquery' ], $version, true );

		   	// Product editor
		   	wp_register_script( 'ovabrw-product-editor', OVABRW_PLUGIN_URI.'assets/js/admin/product-editor.min.js', [ 'jquery' ], $version, true );

		   	// Manange locations
		   	wp_register_script( 'ovabrw-manage-locations', OVABRW_PLUGIN_URI.'assets/js/admin/manage-locations.min.js', [ 'jquery' ], $version, true );

		   	// Manange vehicles
		   	wp_register_script( 'ovabrw-vehicle', OVABRW_PLUGIN_URI.'assets/js/admin/vehicle.min.js', [ 'jquery' ], $version, true );

		   	// Register guest information script
			wp_register_script( 'ovabrw-guest-information', OVABRW_PLUGIN_URI.'assets/js/admin/guest-information.min.js', ['jquery'], $version, true );

			// Product edit page
			if ( 'product' === $screen_id ) {
				// Google API Key Maps
				$api_key = ovabrw_get_setting( 'google_key_map', false );

				// Open StreetMap
			    if ( OVABRW()->options->osm_enabled() ) {
			    	// Leaflet
			    	wp_enqueue_script( 'ovabrw-osm-leaflet' );

			    	// Autocomplete
			    	if ( OVABRW()->options->is_osm_lib_enabled( 'autocomplete' ) ) {
		    			wp_enqueue_script( 'ovabrw-osm-autocomplete' );
			    		wp_enqueue_script( 'ovabrw-osm' );
		    		}
			    } elseif ( $api_key ) {
					wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&loading=async&callback=Function.prototype&libraries=places', $version, true );
			    }

				// Tippy
		        wp_enqueue_script( 'ovabrw-popper' );
		        wp_enqueue_script( 'ovabrw-tippy-bundle' );

		        // Timepicker
				wp_enqueue_script( 'ovabrw-admin-timepicker' );

				// Easepick - Datepicker
				wp_enqueue_script( 'ovabrw-admin-easepick' );

				// Product editor
				wp_enqueue_script( 'ovabrw-product-editor' );
				wp_localize_script( 'ovabrw-product-editor', 'ovabrwErrorMessages', ovabrw_get_validation_messages() );
			} elseif ( 'brw_page_ovabrw-create-booking' === $screen_id || 'ovabrw-create-booking' === $page ) { // Add new order
				// Google API Key Maps
				$api_key = ovabrw_get_setting( 'google_key_map', false );

				// Open StreetMap
			    if ( OVABRW()->options->osm_enabled() ) {
			    	// Leaflet
			    	wp_enqueue_script( 'ovabrw-osm-leaflet' );

			    	// Autocomplete
			    	if ( OVABRW()->options->is_osm_lib_enabled( 'autocomplete' ) ) {
		    			wp_enqueue_script( 'ovabrw-osm-autocomplete' );
			    		wp_enqueue_script( 'ovabrw-osm' );
		    		}

		    		// Routing
		    		if ( OVABRW()->options->is_osm_lib_enabled( 'routing' ) ) {
		    			wp_enqueue_script( 'ovabrw-osm-routing' );
		    		}
			    } elseif ( $api_key ) {
					wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&loading=async&callback=Function.prototype&libraries=places', $version, true );
			    }

				// noUiSlider
			    wp_enqueue_script( 'ova-wnumb' );
			    wp_enqueue_script( 'ova-nouislider' );

				// WP media
				wp_enqueue_media();
				
				// Select2
				wp_enqueue_script( 'ovabrw-select2' );

				// Tippy
		        wp_enqueue_script( 'ovabrw-popper' );
		        wp_enqueue_script( 'ovabrw-tippy-bundle' );

		        // Timepicker
				wp_enqueue_script( 'ovabrw-admin-timepicker' );

				// Easepick - Datepicker
				wp_enqueue_script( 'ovabrw-admin-easepick' );

				// Create new booking
				wp_enqueue_style( 'ovabrw-jquery-ui' );
				wp_enqueue_script( 'ovabrw-create-new-booking' );
			} elseif ( 'edit-location' === $screen_id ) {
				wp_enqueue_script( 'ovabrw-manage-locations' );
				wp_localize_script( 'ovabrw-manage-locations', 'ovabrwImportLocations', [
					'url' 	=> get_admin_url().'admin.php?page=ovabrw-import-location',
					'title' => esc_html__( 'Import locations', 'ova-brw' )
				]);
			} elseif ( 'vehicle' === $screen_id ) {
				// Timepicker
				wp_enqueue_script( 'ovabrw-admin-timepicker' );

				// Easepick - Datepicker
				wp_enqueue_script( 'ovabrw-admin-easepick' );

				// Vehicle
				wp_enqueue_script( 'ovabrw-vehicle' );
			} elseif ( 'brw_page_ovabrw-import-location' === $screen_id || 'ovabrw-import-location' === $page ) { // Import location
				// Select2
				wp_enqueue_script( 'ovabrw-select2' );
			} elseif ( 'brw_page_ovabrw-custom-checkout-field' === $screen_id || 'ovabrw-custom-checkout-field' === $page ) {
				// Tippy
		        wp_enqueue_script( 'ovabrw-popper' );
		        wp_enqueue_script( 'ovabrw-tippy-bundle' );

				// Easepick - Datepicker
				wp_enqueue_script( 'ovabrw-admin-easepick' );

				// Jquery UI
				wp_enqueue_script( 'ovabrw-jquery-ui' );

				// Custom checkout fields
				wp_enqueue_script( 'ovabrw-custom-checkout-fields' );
				wp_localize_script( 'ovabrw-custom-checkout-fields', 'ovabrwErrorMessages', ovabrw_get_validation_messages() );
				wp_localize_script( 'ovabrw-custom-checkout-fields', 'ovabrwConfirm', [
					'delete' 	=> esc_html__( 'Are you sure you want to delete this field?', 'ova-brw' ),
					'continue' 	=> esc_html__( 'Are you sure you want to continue?', 'ova-brw' )
				]);
			} elseif ( 'brw_page_ovabrw-booking-calendar' === $screen_id || 'ovabrw-booking-calendar' === $page ) { // Booking calendar
				// Select2
				wp_enqueue_script( 'ovabrw-select2' );
				
				// Tippy
		        wp_enqueue_script( 'ovabrw-popper' );
		        wp_enqueue_script( 'ovabrw-tippy-bundle' );

		        // Timepicker
				wp_enqueue_script( 'ovabrw-admin-timepicker' );

				// Easepick - Datepicker
				wp_enqueue_script( 'ovabrw-admin-easepick' );
				
				// Calendar
				wp_enqueue_script( 'ovabrw-calendar' );
				wp_enqueue_script( 'ovabrw-calendar-locales' );

				// Booking Calendar
				wp_enqueue_script( 'ovabrw-booking-calendar');
			} elseif ( 'brw_page_ovabrw-manage-bookings' === $screen_id || 'ovabrw-manage-bookings' === $page ) { // Manage orders

				// Tippy
		        wp_enqueue_script( 'ovabrw-popper' );
		        wp_enqueue_script( 'ovabrw-tippy-bundle' );
		        
				// Timepicker
				wp_enqueue_script( 'ovabrw-admin-timepicker' );

				// Easepick - Datepicker
				wp_enqueue_script( 'ovabrw-admin-easepick' );

				// Manage bookings
				wp_enqueue_script( 'ovabrw-manage-bookings' );
			} elseif ( 'brw_page_ovabrw-specifications' === $screen_id || 'ovabrw-specifications' === $page ) {
				// jquery UI
				wp_enqueue_script( 'ovabrw-jquery-ui' );

				// Easepick - Datepicker
				wp_enqueue_script( 'ovabrw-admin-easepick' );
				
				// Specifications
				wp_enqueue_script( 'ovabrw-specifications' );
				wp_localize_script( 'ovabrw-specifications', 'ovabrwConfirm', [
					'delete' => esc_html__( 'Are you sure you want to delete this field?', 'ova-brw' )
				]);
			} elseif ( 'brw_page_ovabrw-custom-taxonomy' === $screen_id || 'ovabrw-custom-taxonomy' === $page ) { // Custom taxonomies
				// Tippy
		        wp_enqueue_script( 'ovabrw-popper' );
		        wp_enqueue_script( 'ovabrw-tippy-bundle' );

				// Jquery UI
				wp_enqueue_script( 'ovabrw-jquery-ui' );

				// Custom taxonomy
				wp_enqueue_script( 'ovabrw-custom-taxonomies' );
				wp_localize_script( 'ovabrw-custom-taxonomies', 'ovabrwErrorMessages', ovabrw_get_validation_messages() );
				wp_localize_script( 'ovabrw-custom-taxonomies', 'ovabrwConfirm', [
					'delete' 	=> esc_html__( 'Are you sure you want to delete this taxonomy?', 'ova-brw' ),
					'continue' 	=> esc_html__( 'Are you sure you want to continue?', 'ova-brw' )
				]);
			} elseif ( 'woocommerce_page_wc-settings' === $screen_id ) {
				// Tippy
		        wp_enqueue_script( 'ovabrw-popper' );
		        wp_enqueue_script( 'ovabrw-tippy-bundle' );

				// Easepick - Datepicker
				wp_enqueue_script( 'ovabrw-admin-easepick' );

				// CodeMirror
				wp_enqueue_code_editor( [ 'type' => 'text/css' ] );
			    wp_enqueue_script( 'wp-theme-plugin-editor' );
			    wp_enqueue_style( 'wp-codemirror' );

			    // Guest information
				wp_enqueue_script( 'ovabrw-guest-information' );
				wp_localize_script( 'ovabrw-guest-information', 'ovabrwConfirm', [
					'delete' 				=> esc_html__( 'Are you sure you want to delete this field?', 'ova-brw' ),
					'continue' 				=> esc_html__( 'Are you sure you want to continue?', 'ova-brw' ),
					'continueDisconnect' 	=> esc_html__( 'Are you sure you want to disconnect?', 'ova-brw' ),
					'processing' 			=> esc_html__( 'Processing...', 'ova-brw' ),
					'startingSync' 			=> esc_html__( 'Starting sync…', 'ova-brw' ),
                    'syncing'  				=> esc_html__( 'Syncing orders…', 'ova-brw' ),
                    'syncDone'     			=> esc_html__( 'Sync completed successfully.', 'ova-brw' )
				]);
				
				// Settings
				wp_enqueue_script( 'ovabrw-settings' );
			} elseif ( 'woocommerce_page_wc-orders' === $screen_id || 'edit-shop_order' === $screen_id || 'shop_order' == $screen_id ) { // Shop order
				wp_enqueue_script( 'ovabrw-wc-orders' );
			}

			// Ajax object
			wp_localize_script( 'ovabrw-admin-scripts', 'ajax_object', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'ovabrw-security-ajax' )
			]);

			// Admin scripts
			wp_enqueue_script( 'ovabrw-admin-scripts' );

			// Error messages
			wp_localize_script( 'ovabrw-admin-scripts', 'ovabrwErrorMessages', ovabrw_get_validation_messages() );

			// Timepicker options
			wp_localize_script( 'ovabrw-admin-scripts', 'timePickerOptions', OVABRW()->options->get_timepicker_options() );

			// Datepicker options
			wp_localize_script( 'ovabrw-admin-scripts', 'datePickerOptions', OVABRW()->options->get_datepicker_options() );
		}
	}

	new OVABRW_Admin_Assets();
}