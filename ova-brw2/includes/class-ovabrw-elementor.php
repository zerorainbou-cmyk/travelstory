<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Elementor
 */
if ( !class_exists( 'OVABRW_Elementor', false ) ) {

	class OVABRW_Elementor {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Register categories
			add_action( 'elementor/elements/categories_registered', [ $this, 'categories_registered' ] );

			// Register widgets
			add_action( 'elementor/widgets/register', [ $this, 'widgets_register' ] );

			// After register styles
	    	add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );

			// After register 
			add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
		}

		/**
		 * Register categories
		 */
		public function categories_registered() {
		    \Elementor\Plugin::instance()->elements_manager->add_category(
		        'ovabrw-product',
		        [
		            'title' => esc_html__( 'BRW Product', 'ova-brw' ),
		            'icon' 	=> 'fa fa-plug'
		        ]
		    );

		    \Elementor\Plugin::instance()->elements_manager->add_category(
		        'ovabrw-products',
		        [
		            'title' => esc_html__( 'BRW Products', 'ova-brw' ),
		            'icon' 	=> 'fa fa-plug'
		        ]
		    );
		}

		/**
		 * Register widgets
		 */
		public function widgets_register( $widgets_manager ) {
			$widget_files = glob( OVABRW_PLUGIN_PATH . 'elementor/widgets/*.php' );

			if ( ovabrw_array_exists( $widget_files ) ) {
				foreach ( $widget_files as $file ) {
		            $path = OVABRW_PLUGIN_PATH . 'elementor/widgets/' . wp_basename( $file );
		            if ( file_exists( $path ) ) {
		                require_once $path;
		            }
		        }
			}
		}

		/**
		 * Register styles
		 */
		public function register_styles() {
			// Get all css files
	        $css_files = glob( OVABRW_PLUGIN_PATH.'assets/css/elementor/*.css' );
	        if ( ovabrw_array_exists( $css_files ) ) {
	        	foreach ( $css_files as $file ) {
		            $file_name = wp_basename( $file );
		            $handle    = str_replace( '.css', '', $file_name );
		            $src       = OVABRW_PLUGIN_URI.'assets/css/elementor/' . sanitize_file_name( $file_name );

		            if ( file_exists( $file ) ) {
		                wp_register_style( $handle, $src );
		            }
		        }
	        }
		}

		/**
		 * Register scripts
		 */
		public function register_scripts() {
			// Get all js files
	        $js_files = glob( OVABRW_PLUGIN_PATH.'assets/js/elementor/*.min.js' );
	        if ( ovabrw_array_exists( $js_files ) ) {
	        	foreach ( $js_files as $file ) {
		            $file_name = wp_basename( $file );
		            $handle    = str_replace( '.min.js', '', $file_name );
		            $src       = OVABRW_PLUGIN_URI.'assets/js/elementor/' . sanitize_file_name( $handle ) . '.min.js';

		            if ( file_exists( $file ) ) {
		                wp_register_script( $handle, $src, ['jquery'], false, true );
		            }
		        }
	        }
		}
	}

	new OVABRW_Elementor();
}