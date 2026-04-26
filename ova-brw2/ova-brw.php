<?php
/*
Plugin Name: BRW - Booking & Rental Plugin
Plugin URI: https://themeforest.net/user/ovatheme/portfolio
Description: OvaTheme Booking, Rental WooCommerce Plugin.
Author: Ovatheme
Version: 2.0.2
Author URI: https://themeforest.net/user/ovatheme
Text Domain: ova-brw
Domain Path: /languages/
Requires Plugins: woocommerce
*/

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW class.
 */
if ( !class_exists( 'OVABRW' ) ) {

	final class OVABRW {
		/**
		 * OVABRW version.
		 *
		 * @var string
		 */
		protected $version = null;

		/**
		 * The single instance of the class.
		 *
		 * @var OVABRW
		 * @since 1.0
		 */
		protected static $_instance = null;

		/**
		 * Get data
		 */
		public $options = null;

		/**
		 * Booking
		 */
		public $booking = null;

		/**
		 * Rental
		 */
		public $rental = null;

		/**
		 * OVABRW Constructor
		 */
		public function __construct() {
			$this->set_version();
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Set plugin version
		 */
		private function set_version() {
			$plugin_data 	= get_plugin_data( __FILE__, false, false );
			$this->version 	= isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : null;
		}

		/**
		 * Get plugin version
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Define constants
		 */
		public function define_constants() {
			define( 'OVABRW_PLUGIN_FILE', __FILE__ );
			define( 'OVABRW_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
			define( 'OVABRW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			define( 'OVABRW_PLUGIN_ADMIN', OVABRW_PLUGIN_PATH . 'admin/' );
			define( 'OVABRW_PLUGIN_INC', OVABRW_PLUGIN_PATH . 'includes/' );
			define( 'OVABRW_PLUGIN_RENTAL', OVABRW_PLUGIN_PATH . 'rental-types/' );

			// Global prefix
			define( 'OVABRW_PREFIX', 'ovabrw_' );
			define( 'OVABRW_PREFIX_OPTIONS', 'ova_brw_' );
			define( 'OVABRW_RENTAL', 'ovabrw_car_rental' );
		}

		/**
		 * Includes
		 */
		public function includes() {
			// Core functions
			require_once OVABRW_PLUGIN_INC . 'ovabrw-core-functions.php';

			// Get data
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-get-data.php';
			if ( class_exists( 'OVABRW_Get_Data', false ) ) {
				$this->options = OVABRW_Get_Data::instance();
			}

			// Abstract rental types
			require_once( OVABRW_PLUGIN_RENTAL . 'abstracts/abstract-ovabrw-rental-types.php' );

			// Load rental types
			ovabrw_autoload( OVABRW_PLUGIN_RENTAL . 'types/class-ovabrw-rental-by-*.php' );

			// Rental
			require_once( OVABRW_PLUGIN_RENTAL . 'class-ovabrw-rental-factory.php' );
			if ( class_exists( 'OVABRW_Rental_Factory', false ) ) {
				$this->rental = OVABRW_Rental_Factory::instance();
			}

			// Booking
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-booking.php';
			if ( class_exists( 'OVABRW_Booking', false ) ) {
				$this->booking = OVABRW_Booking::instance();
			}

			// Admin access
			add_action( 'init', function() {
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin.php';
			});

			// Assets
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-assets.php';

			// Order queues
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-order-queues.php';

			// Hooks
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-hooks.php';

			// Ajaxs
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-ajax.php';

			// Deposit
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-deposit.php';

			// CPT
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-cpt.php';

			// Shortcodes
			require_once OVABRW_PLUGIN_INC. 'class-ovabrw-shortcodes.php';

			// Mail
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-mail.php';

			// Cron
			require_once OVABRW_PLUGIN_INC .  'class-ovabrw-cron.php';

			// Templates
			require_once( OVABRW_PLUGIN_INC . 'ovabrw-template-functions.php' );
			require_once( OVABRW_PLUGIN_INC . 'ovabrw-template-hooks.php' );

			// Elementor
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once( OVABRW_PLUGIN_INC . 'class-ovabrw-elementor.php' );
			}
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			// Load textdomain
			add_action( 'init', [ $this, 'load_textdomain' ] );

			// Woocommerce loaded
			add_action( 'woocommerce_loaded', [ $this, 'woocommerce_loaded' ] );

			// Register activation
			register_activation_hook( OVABRW_PLUGIN_FILE, [ $this, 'install' ] );

			// Admin notices
			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
		}

		/**
		 * Load textdomain
		 */
		public function load_textdomain() {
			if ( is_textdomain_loaded( 'ova-brw' ) ) return;

			load_plugin_textdomain( 'ova-brw', false, basename( dirname( __FILE__ ) ) .'/languages' );
		}

		/**
		 * Woocommerce loaded
		 */
		public function woocommerce_loaded() {
			// init rental product
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-rental-product.php';

			// Cart & Checkout Blocks Integrations
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-blocks.php';

			// Register IntegrationInterface
			if ( class_exists( 'OVABRW_Blocks' ) ) {
				add_action(
				    'woocommerce_blocks_mini-cart_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new OVABRW_Blocks() );
				    }
				);
				add_action(
				    'woocommerce_blocks_cart_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new OVABRW_Blocks() );
				    }
				);
				add_action(
				    'woocommerce_blocks_checkout_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new OVABRW_Blocks() );
				    }
				);
			}
		}

		/**
		 * install OVABRW
		 */
		public function install() {
			// Add install time
			add_option( 'ovabrw_admin_install_timestamp', time() );

			// Create the order queues table
			if ( class_exists( 'OVABRW_Order_Queues' ) ) {
				OVABRW_Order_Queues::instance()->create_table();

				// Order sync completed
				add_option( 'ovabrw_order_sync_completed', true );
			}
		}

		/**
		 * Admin notices
		 */
		public function admin_notices() {
			// Get screen
			$screen = get_current_screen();

			// Get is settings page
			$is_settings_page = ( 
				$screen->id === 'woocommerce_page_wc-settings' &&
		        isset( $_GET['page'] ) && $_GET['page'] === 'wc-settings' && 
		        isset( $_GET['tab'] ) && $_GET['tab'] === 'ova_brw' 
		    );

			// Check order queue table exists
			if ( OVABRW_Order_Queues::instance()->is_completed() || $is_settings_page ) {
				return;
			}

			// Get table name
			$table_name = OVABRW_Order_Queues::instance()->get_table_name();

			// Get settings URL
			$settings_url = admin_url( 'admin.php?page=wc-settings&tab=ova_brw' );

			// Check if error already exists
    		$errors = get_settings_errors( $table_name );
    		if ( !$errors ) {
    			add_settings_error(
			        $table_name, // Unique ID for the error
			        $table_name,
			        /* Translators: 1: Start link tag, 2: End link tag. */
			        sprintf(
			        	__( 'BRW - You can optimize availability validation by syncing order data to the order queues table. Please go to %1$sBRW > Settings > General%2$s to continue.', 'ova-brw' ),
			        	'<a href="' . esc_url( $settings_url ) . '">',
	    				'</a>'
			        ),
			        'error'
			    );
    		}
		    
		    // Display the errors
		    settings_errors( $table_name );
		}

		/**
		 * Main OVABRW Instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}

// Require plugin.php to use is_plugin_active() below
if ( !function_exists( 'is_plugin_active' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Returns the main instance of OVABRW.
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	function OVABRW() {
		return OVABRW::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['OVABRW'] = OVABRW();
}