<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Assets class
 */
if ( !class_exists( 'OVABRW_Assets', false ) ) {

	class OVABRW_Assets {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Load google fonts
			add_action( 'wp_enqueue_scripts', [ $this, 'load_google_fonts' ] );

			// Enqueue styles
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );

			// Enqueue scripts
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

			// Inline global CSS
			add_action( 'wp_enqueue_scripts', [ $this, 'inline_global_css' ] );
		}

		/**
		 * Load google fonts
		 */
		public function load_google_fonts() {
			if ( ovabrw_global_typography() ) {
				$primary_font 	= ovabrw_get_option( 'glb_primary_font', 'Poppins' );
				$font_weight 	= ovabrw_get_option( 'glb_primary_font_weight', [
					"100",
			        "100italic",
			        "200",
			        "200italic",
			        "300",
			        "300italic",
			        "regular",
			        "italic",
			        "500",
			        "500italic",
			        "600",
			        "600italic",
			        "700",
			        "700italic",
			        "800",
			        "800italic",
			        "900",
			        "900italic"
				]);

				$str_font_weight = '100,200,300,400,500,600,700,800,900';

				if ( ovabrw_array_exists( $font_weight ) ) {
					$str_font_weight = implode( ',', $font_weight );
				}

				if ( $primary_font && $str_font_weight ) {
					$font_url = add_query_arg(
						[
							'family' => urlencode( $primary_font.':'.$str_font_weight )
						],
						'//fonts.googleapis.com/css'
					);

					$google_font = esc_url_raw( $font_url );

					wp_enqueue_style( 'ovabrw-google-font', $google_font, [], null );
				}
			}
		}

		/**
		 * Enqueue styles
		 */
		public function enqueue_styles() {
			// Get plugin version
			$version = OVABRW()->get_version();

			/*====================== Register Styles ======================*/

			// Tippy scale
			wp_register_style( 'ova-tippy-scale', OVABRW_PLUGIN_URI.'assets/libs/tippy/scale.css', [], $version );

			// noUiSlider
			wp_register_style( 'ova-nouislider', OVABRW_PLUGIN_URI.'assets/libs/nouislider/nouislider.min.css', [], $version );

			// Timepicker
			wp_register_style( 'ova-timepicker', OVABRW_PLUGIN_URI.'assets/libs/timepicker/timepicker.min.css', [], $version );

			// Fancybox
			wp_register_style( 'ova-fancybox', OVABRW_PLUGIN_URI.'/assets/libs/fancybox/fancybox.css', [], $version );

			// Swiper
			wp_register_style( 'swiper', OVABRW_PLUGIN_URI.'assets/libs/swiper/swiper-bundle.min.css', [], $version );

			// Flaticon
			wp_register_style( 'ovabrw-icon', OVABRW_PLUGIN_URI.'assets/libs/flaticons/brwicon/font/flaticon_brw.css', [], $version );
			wp_register_style( 'ovabrw-flaticon-car-service', OVABRW_PLUGIN_URI.'assets/libs/flaticons/car_service/flaticon.css', [], $version );
			wp_register_style( 'ovabrw-flaticon-car2', OVABRW_PLUGIN_URI.'assets/libs/flaticons/car2/flaticon.css', [], $version );	
			wp_register_style( 'ovabrw-flaticon-essential', OVABRW_PLUGIN_URI.'assets/libs/flaticons/essential_set/flaticon.css', [], $version );
	    	wp_register_style('ovabrw-flaticon-remons2', OVABRW_PLUGIN_URI.'assets/libs/flaticons/brwicon2/font/brwicon2.css', [], $version );
	    	wp_register_style('ovabrw-flaticon-remons3', OVABRW_PLUGIN_URI.'assets/libs/flaticons/brwicon3/font/brwicon3.css', [], $version );

			// Elegant font
			wp_register_style( 'ovabrw-elegant-font', OVABRW_PLUGIN_URI.'assets/libs/elegant_font/style.css', [], $version );

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

			// Front-end
			wp_register_style( 'ovabrw-frontend', OVABRW_PLUGIN_URI.'assets/css/frontend/ovabrw_frontend.css', [], $version );

			/*====================== Enqueue Styles ======================*/

			// Tippy scale
			wp_enqueue_style( 'ova-tippy-scale' );

			// noUiSlider
			wp_enqueue_style( 'ova-nouislider' );

			// Timepicker
			wp_enqueue_style( 'ova-timepicker' );

			// Global typography enabled
			if ( ovabrw_global_typography() ) {
				// Fancybox
				wp_enqueue_style( 'ova-fancybox' );

				// Swiper
				wp_enqueue_style( 'swiper' );

				// Flaticon
			    if ( apply_filters( OVABRW_PREFIX.'use_brwicon', true ) ) {
			    	wp_enqueue_style( 'ovabrw-icon' );
			    }
			} // END if

			// Elegant font
			if ( apply_filters( OVABRW_PREFIX.'use_elegant_font', true ) ) {
				wp_enqueue_style( 'ovabrw-elegant-font' );	
			}

			// Flaticon
			if ( apply_filters( OVABRW_PREFIX.'use_flaticon_font', true ) ) {
				wp_enqueue_style( 'ovabrw-flaticon-car-service' );
				wp_enqueue_style( 'ovabrw-flaticon-car2' );	
				wp_enqueue_style( 'ovabrw-flaticon-essential' );
		    	wp_enqueue_style( 'ovabrw-flaticon-remons2' );
		    	wp_enqueue_style( 'ovabrw-flaticon-remons3' );
			}

			// OSM - Autocomplete
			if ( OVABRW()->options->osm_enabled() ) {
				wp_enqueue_style( 'ovabrw-osm-leaflet' );

				// Autocomplete
		    	if ( OVABRW()->options->is_osm_lib_enabled( 'autocomplete' ) ) {
		    		wp_enqueue_style( 'ovabrw-osm-autocomplete-button' );
		    		wp_enqueue_style( 'ovabrw-osm-autocomplete' );
		    	}

		    	// Routing
		    	if ( OVABRW()->options->is_osm_lib_enabled( 'routing' ) ) {
		    		wp_enqueue_style( 'ovabrw-osm-routing' );
		    	}
			}

			// Front-end
			wp_enqueue_style( 'ovabrw-frontend' );
		}

		/**
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {
			// Get plugin version
			$version = OVABRW()->get_version();

			/*====================== Register Scripts ======================*/

		    // Tippy
			wp_register_script( 'ovabrw-popper', OVABRW_PLUGIN_URI.'assets/libs/tippy/popper.min.js', [ 'jquery' ], $version, true );
			wp_register_script( 'ovabrw-tippy-bundle', OVABRW_PLUGIN_URI.'assets/libs/tippy/tippy-bundle.min.js', [ 'jquery' ], $version, true );

			// noUiSlider
			wp_register_script( 'ova-wnumb', OVABRW_PLUGIN_URI.'assets/libs/nouislider/wNumb.min.js', [ 'jquery' ], $version, true );
			wp_register_script( 'ova-nouislider', OVABRW_PLUGIN_URI.'assets/libs/nouislider/nouislider.min.js', [ 'jquery' ], $version, true );

			// Timepicker script
			wp_register_script( 'ova-timepicker', OVABRW_PLUGIN_URI.'assets/libs/timepicker/timepicker.min.js', [ 'jquery' ], $version, true );

			// Easepick script
			wp_register_script( 'ova-easepick', OVABRW_PLUGIN_URI.'assets/libs/easepick/easepick.min.js', [ 'jquery' ], $version, true );

			// Get google api key maps
			$api_key = ovabrw_get_setting( 'google_key_map' );

			// Open StreetMap
		    if ( OVABRW()->options->osm_enabled() ) {
		    	// Leaflet
		    	wp_register_script( 'ovabrw-osm-leaflet', OVABRW_PLUGIN_URI.'assets/libs/osm/leaflet.js', [ 'jquery' ], $version, true );

		    	// Autocomplete
		    	wp_register_script( 'ovabrw-osm-autocomplete', OVABRW_PLUGIN_URI.'assets/libs/osm/autocomplete.min.js', [ 'jquery', 'ovabrw-osm-leaflet' ], $version, true );

		    	// Routing
		    	wp_register_script( 'ovabrw-osm-routing', OVABRW_PLUGIN_URI.'assets/libs/osm/leaflet-routing-machine.min.js', [ 'jquery', 'ovabrw-osm-leaflet' ], $version, true );
		    	
		    	// OSM
		    	wp_register_script( 'ovabrw-osm', OVABRW_PLUGIN_URI.'assets/js/frontend/osm.min.js', [ 'jquery', 'ovabrw-osm-leaflet', 'ovabrw-osm-autocomplete' ], $version, true );
		    } elseif ( $api_key ) {
		    	wp_register_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='.$api_key.'&libraries=places&loading=async&callback=Function.prototype', $version, true );
		    }

			// Google market
			wp_register_script( 'ovabrw-google-marker', OVABRW_PLUGIN_URI.'assets/libs/google_map/markerclusterer.js', [ 'jquery' ], false, true );

			// Override market google map when more product the same location
			wp_register_script( 'ovabrw-oms', OVABRW_PLUGIN_URI.'assets/libs/google_map/oms.js', [ 'jquery' ], false, true );

		    // Calendar
		    wp_register_script( 'ova-calendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/index.global.min.js', [ 'jquery' ], $version, true );
		    wp_register_script( 'ova-calendar-locales', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/locales-all.global.min.js', [ 'jquery' ], $version, true );

		    // Fancybox
			wp_register_script( 'ova-fancybox', OVABRW_PLUGIN_URI.'/assets/libs/fancybox/fancybox.umd.js', [ 'jquery' ], $version, true );

			// Swiper
			wp_register_script( 'swiper', OVABRW_PLUGIN_URI.'assets/libs/swiper/swiper-bundle.min.js', [ 'jquery' ], $version, true );

			// Front-end
			wp_register_script( 'ovabrw-frontend', OVABRW_PLUGIN_URI.'assets/js/frontend/ova-brw-frontend.min.js', [ 'jquery' ], $version, true );

			/*====================== Enqueue Scripts ======================*/

			// Ui autocomplete
		    wp_enqueue_script( 'jquery-ui-autocomplete' );

		    // noUiSlider
		    wp_enqueue_script( 'ova-wnumb' );
		    wp_enqueue_script( 'ova-nouislider' );

		    // Tippy
		    wp_enqueue_script( 'ovabrw-popper' );
		    wp_enqueue_script( 'ovabrw-tippy-bundle' );

		    // Timepicker
		    wp_enqueue_script( 'ova-timepicker' );

		    // Datepicker
		    wp_enqueue_script( 'ova-easepick' );

		    // Single Product
			if ( is_product() ) {
	            $product = wc_get_product( get_the_id() );
				if ( $product && $product->is_type( OVABRW_RENTAL ) && $api_key && !OVABRW()->options->osm_enabled() ) {
			    	wp_enqueue_script( 'ovabrw-google-maps' );
				}
			}

			// Calendar
			wp_enqueue_script( 'ova-calendar' );
			wp_enqueue_script( 'ova-calendar-locales' );

			// Global typography enabled
		    if ( ovabrw_global_typography() ) {
		    	// Fancybox
				wp_enqueue_script( 'ova-fancybox' );

				// Swiper
				wp_enqueue_script( 'swiper' );
		    } // END if

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
		    }

		    // Front-end
			wp_enqueue_script( 'ovabrw-frontend' );

			// Error messages
			wp_localize_script( 'ovabrw-frontend', 'ovabrwErrorMessages', ovabrw_get_validation_messages() );

			// Timepicker options
			wp_localize_script( 'ovabrw-frontend', 'timePickerOptions', OVABRW()->options->get_timepicker_options() );

			// Datepicker options
			wp_localize_script( 'ovabrw-frontend', 'datePickerOptions', OVABRW()->options->get_datepicker_options() );

			// Ajax object
			wp_localize_script( 'ovabrw-frontend', 'ajax_object', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'ovabrw-security-ajax' ),
				'ggApiKey' => $api_key ? true : false
			]);
		}

		/**
		 * Inline global CSS
		 */
		public function inline_global_css() {
			$css = '';

			// Calendar 
			$color_available_calendar 		= ovabrw_get_option( 'color_available_calendar', '#222222' );
			$background_available_calendar 	= ovabrw_get_option( 'bg_calendar_available', '#FFF' );
            $color_disable_calendar 		= ovabrw_get_option( 'color_disable_calendar', '#FFF' );
            $background_disable_calendar 	= ovabrw_get_option( 'bg_disable_calendar', '#e56e00' );
            $color_booked_calendar 			= ovabrw_get_option( 'color_booked_calendar', '#FFF' );
            $background_booked_calendar 	= ovabrw_get_option( 'bg_booked_calendar', '#e56e00' );
            $primary_background_calendar 	= ovabrw_get_option( 'primary_background_calendar', '#00bb98' );

            $css .= '--ovabrw-available-color-calendar:'.$color_available_calendar.';';
            $css .= '--ovabrw-available-background-calendar:'.$background_available_calendar.';';
            $css .= '--ovabrw-disable-color-calendar:'.$color_disable_calendar.';';
            $css .= '--ovabrw-disable-background-calendar:'.$background_disable_calendar.';';
            $css .= '--ovabrw-booked-color-calendar:'.$color_booked_calendar.';';
            $css .= '--ovabrw-booked-background-calendar:'.$background_booked_calendar.';';
            $css .= '--ovabrw-primary-background-calendar:'.$primary_background_calendar.';';
            
			if ( ovabrw_global_typography() ) {
				add_filter( 'body_class', function( $classes ) {
					return array_merge( $classes, [ 'ovabrw-modern' ] );
				});

				// Primary
				$primary_font 	= ovabrw_get_option( 'glb_primary_font', 'Poppins' );
				$primary_color 	= ovabrw_get_option( 'glb_primary_color', '#E56E00' );
				$light_color 	= ovabrw_get_option( 'glb_light_color', '#C3C3C3' );
				$css .= '--ovabrw-primary-font:"'.$primary_font.'";';
				$css .= '--ovabrw-primary-color:'.$primary_color.';';
				$css .= '--ovabrw-light-color:'.$light_color.';';

				// Heading
				$heading_size 			= ovabrw_get_option( 'glb_heading_font_size', '24px' );
				$heading_weight 		= ovabrw_get_option( 'glb_heading_font_weight', '600' );
				$heading_line_height 	= ovabrw_get_option( 'glb_heading_line_height', '36px' );
				$heading_color 			= ovabrw_get_option( 'glb_heading_color', '#222222' );
				$css .= '--ovabrw-heading-size:'.$heading_size.';';
				$css .= '--ovabrw-heading-weight:'.$heading_weight.';';
				$css .= '--ovabrw-heading-line-height:'.$heading_line_height.';';
				$css .= '--ovabrw-heading-color:'.$heading_color.';';

				// Second Heading
				$second_heading_size 		= ovabrw_get_option( 'glb_second_heading_font_size', '22px' );
				$second_heading_weight 		= ovabrw_get_option( 'glb_second_heading_font_weight', '600' );
				$second_heading_line_height = ovabrw_get_option( 'glb_second_heading_line_height', '33px' );
				$second_heading_color 		= ovabrw_get_option( 'glb_second_heading_color', '#222222' );
				$css .= '--ovabrw-second-heading-size:'.$second_heading_size.';';
				$css .= '--ovabrw-second-heading-weight:'.$second_heading_weight.';';
				$css .= '--ovabrw-second-heading-line-height:'.$second_heading_line_height.';';
				$css .= '--ovabrw-second-heading-color:'.$second_heading_color.';';

				// Label
				$label_size 		= ovabrw_get_option( 'glb_label_font_size', '16px' );
				$label_weight 		= ovabrw_get_option( 'glb_label_font_weight', '500' );
				$label_line_height 	= ovabrw_get_option( 'glb_label_line_height', '24px' );
				$label_color 		= ovabrw_get_option( 'glb_label_color', '#222222' );
				$css .= '--ovabrw-label-size:'.$label_size.';';
				$css .= '--ovabrw-label-weight:'.$label_weight.';';
				$css .= '--ovabrw-label-line-height:'.$label_line_height.';';
				$css .= '--ovabrw-label-color:'.$label_color.';';

				// Text
				$text_size 			= ovabrw_get_option( 'glb_text_font_size', '14px' );
				$text_weight 		= ovabrw_get_option( 'glb_text_font_weight', '400' );
				$text_line_height 	= ovabrw_get_option( 'glb_text_line_height', '22px' );
				$text_color 		= ovabrw_get_option( 'glb_text_color', '#555555' );
				$css .= '--ovabrw-text-size:'.$text_size.';';
				$css .= '--ovabrw-text-weight:'.$text_weight.';';
				$css .= '--ovabrw-text-line-height:'.$text_line_height.';';
				$css .= '--ovabrw-text-color:'.$text_color.';';

				// Get all card templates
    			$card_templates = ovabrw_get_card_templates();
    			if ( !ovabrw_array_exists( $card_templates ) ) $card_templates = [];

    			foreach ( array_keys( $card_templates ) as $card ) {
    				// Card thumbnail size
    				$card_thumbnail_size = ovabrw_get_option( 'glb_'.$card.'_thumbnail_size', 'woocommerce_thumbnail' );
    				if ( $card_thumbnail_size === 'custom_height' ) {
    					$card_thumbnail_height 	= ovabrw_get_option( 'glb_'.$card.'_thumbnail_height', '300px' );
    					$css .= '--ovabrw-'.$card.'-thumbnail-height:'.$card_thumbnail_height.';';
    				}

    				// Card display thumbnail
    				$card_display_thumbnail = ovabrw_get_option( 'glb_'.$card.'_display_thumbnail', 'cover' );
    				$css .= '--ovabrw-'.$card.'-display-thumbnail:'.$card_display_thumbnail.';';
    			}
			}

			// Datepicker css
	        $datepicker_css = OVABRW()->options->datepicker_global_css();
	        $css .= $datepicker_css;

			$root = ":root{{$css}}";

			wp_add_inline_style( 'ovabrw-frontend', $root );
		}
	}

	// init class
	new OVABRW_Assets();
}