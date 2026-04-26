<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Custom Taxonomies class.
 */
if ( !class_exists( 'OVABRW_Admin_Taxonomies', false ) ) {

	class OVABRW_Admin_Taxonomies {

		/**
		 * instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add sub-menu
            add_action( 'admin_menu', [ $this, 'add_submenu' ] );
		}

		/**
		 * Add sub-menu: Custom taxonomies
		 */
		public function add_submenu() {
			add_submenu_page(
                'ovabrw-settings',
                esc_html__( 'Custom taxonomies', 'ova-brw' ),
                esc_html__( 'Custom taxonomies', 'ova-brw' ),
                apply_filters( OVABRW_PREFIX.'submenu_custom_taxonomies_capability', 'edit_posts' ),
                'ovabrw-custom-taxonomy',
                [ $this, 'view_custom_taxonomy' ],
                3
            );
		}

		/**
		 * View custom taxonomies
		 */
		public function view_custom_taxonomy() {
			include( OVABRW_PLUGIN_ADMIN . 'custom-taxonomies/views/html-custom-taxonomies.php' );
		}

		/**
		 * Popup custom taxonomy form
		 */
		public function ovabrw_popup_custom_taxonomy( $action = 'new', $slug = '' ) {
			// Before popup custom taxonomy HTML
            do_action( OVABRW_PREFIX.'before_popup_taxonomy_html', $this );

            if ( 'edit' == $action ) {
                include( OVABRW_PLUGIN_ADMIN.'custom-taxonomies/views/html-popup-edit-taxonomy.php' );
            } elseif ( 'new' == $action ) {
                include( OVABRW_PLUGIN_ADMIN.'custom-taxonomies/views/html-popup-new-taxonomy.php' );
            }

            // After popup custom taxonomy HTML
            do_action( OVABRW_PLUGIN_ADMIN.'after_popup_taxonomy_html', $this );
		}

		/**
		 * Enabled
		 */
		public function enabled( $post = [] ) {
			// Before enable custom taxonomy
            do_action( OVABRW_PREFIX.'before_enabled_custom_taxonomy', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Custom taxonomies
				$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

				// Get slug
                $slug = ovabrw_get_meta_data( 'slug', $post );
                if ( ovabrw_array_exists( $slug ) ) {
                    foreach ( $slug as $s ) {
                        // Update field
                        if ( isset( $taxonomies[$s]['enabled'] ) ) {
                            $taxonomies[$s]['enabled'] = 'on';
                        }
                    }
                }

                update_option( 'ovabrw_custom_taxonomy', $taxonomies );
            }

            // After enabled custom taxonomy
            do_action( OVABRW_PREFIX.'after_enabled_custom_taxonomy', $post, $this );
		}

		/**
		 * Disable
		 */
		public function disable( $post = [] ) {
			// Before disable custom taxonomy
            do_action( OVABRW_PREFIX.'before_disable_custom_taxonomy', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Custom taxonomies
				$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

				// Get slug
                $slug = ovabrw_get_meta_data( 'slug', $post );
                if ( ovabrw_array_exists( $slug ) ) {
                    foreach ( $slug as $s ) {
                        // Update field
                        if ( isset( $taxonomies[$s]['enabled'] ) ) {
                            $taxonomies[$s]['enabled'] = '';
                        }
                    }
                }

                update_option( 'ovabrw_custom_taxonomy', $taxonomies );
            }

            // After disable custom taxonomy
            do_action( OVABRW_PREFIX.'after_disable_custom_taxonomy', $post, $this );
		}

		/**
		 * Show in listing
		 */
		public function show( $post = [] ) {
			// Before show custom taxonomy
            do_action( OVABRW_PREFIX.'before_show_custom_taxonomy', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Custom taxonomies
				$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

				// Get slug
                $slug = ovabrw_get_meta_data( 'slug', $post );
                if ( ovabrw_array_exists( $slug ) ) {
                    foreach ( $slug as $s ) {
                        // Update field
                        if ( isset( $taxonomies[$s]['show_listing'] ) ) {
                            $taxonomies[$s]['show_listing'] = 'on';
                        }
                    }
                }

                update_option( 'ovabrw_custom_taxonomy', $taxonomies );
            }

            // After show custom taxonomy
            do_action( OVABRW_PREFIX.'after_show_custom_taxonomy', $post, $this );
		}

		/**
		 * Hide in listing
		 */
		public function hide( $post = [] ) {
			// Before hide custom taxonomy
            do_action( OVABRW_PREFIX.'before_hide_custom_taxonomy', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Custom taxonomies
				$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

				// Get slug
                $slug = ovabrw_get_meta_data( 'slug', $post );
                if ( ovabrw_array_exists( $slug ) ) {
                    foreach ( $slug as $s ) {
                        // Update field
                        if ( isset( $taxonomies[$s]['show_listing'] ) ) {
                            $taxonomies[$s]['show_listing'] = '';
                        }
                    }
                }

                update_option( 'ovabrw_custom_taxonomy', $taxonomies );
            }

            // After hide custom taxonomy
            do_action( OVABRW_PREFIX.'after_hide_custom_taxonomy', $post, $this );
		}

		/**
		 * Delete
		 */
		public function delete( $post = [] ) {
			// Before delete custom taxonomy
            do_action( OVABRW_PREFIX.'before_delete_custom_taxonomy', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Custom taxonomies
				$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

				// Get slug
                $slug = ovabrw_get_meta_data( 'slug', $post );
                if ( ovabrw_array_exists( $slug ) ) {
                    foreach ( $slug as $s ) {
                        // Update field
                        if ( isset( $taxonomies[$s] ) ) unset( $taxonomies[$s] );
                    }
                }

                update_option( 'ovabrw_custom_taxonomy', $taxonomies );
            }

            // After hide custom taxonomy
            do_action( OVABRW_PREFIX.'after_delete_custom_taxonomy', $post, $this );
		}

		/**
		 * Save custom taxonomy
		 */
        public function save( $post = [] ) {
            // Before save custom taxonomy
            do_action( OVABRW_PREFIX.'before_save_custom_taxonomy', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Get action
                $action = ovabrw_get_meta_data( 'action', $post );

                if ( 'new' == $action ) {
                    $this->add( $post );
                } elseif ( 'edit' == $action ) {
                    $this->edit( $post );
                }
            }

            // After save custom taxonomy
            do_action( OVABRW_PREFIX.'after_save_custom_taxonomy', $post, $this );
        }

        /**
         * Add new custom taxonomy
         */
        public function add( $post = [] ) {
        	// Before add custom taxonomy
            do_action( OVABRW_PREFIX.'before_add_custom_taxonomy', $post, $this );

			if ( ovabrw_array_exists( $post ) && current_user_can( 'publish_posts' ) ) {
                // Custom taxonomies
				$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

                // Get slug
                $slug = sanitize_text_field( sanitize_title( ovabrw_get_meta_data( 'slug', $post ) ) );
                $slug = apply_filters( OVABRW_PREFIX.'prefix_custom_taxonomy', 'brw_' ) . $slug;

                if ( $slug ) {
                    $taxonomies[$slug] = [
                        'name'          	=> ovabrw_get_meta_data( 'name', $post ),
                        'singular_name' 	=> ovabrw_get_meta_data( 'singular_name', $post ),
                        'label_frontend' 	=> ovabrw_get_meta_data( 'label_frontend', $post ),
                        'enabled' 			=> ovabrw_get_meta_data( 'enabled', $post ),
                        'show_listing' 		=> ovabrw_get_meta_data( 'show_listing', $post )
                    ];
                }

                update_option( 'ovabrw_custom_taxonomy', $taxonomies );
            }

            // After add custom taxonomy
            do_action( OVABRW_PREFIX.'after_add_custom_taxonomy', $taxonomies, $this );
        }

        /**
         * Edit custom taxonomy
         */
        public function edit( $post = [] ) {
            // Before edit custom taxonomy
            do_action( OVABRW_PREFIX.'before_edit_custom_taxonomy', $post, $this );

			if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Custom taxonomies
				$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

				// Get slug
                $slug = sanitize_text_field( sanitize_title( ovabrw_get_meta_data( 'slug', $post ) ) );
                if ( $slug ) {
                	$taxonomies[$slug] = [
	                    'name'          	=> ovabrw_get_meta_data( 'name', $post ),
	                    'singular_name' 	=> ovabrw_get_meta_data( 'singular_name', $post ),
	                    'label_frontend' 	=> ovabrw_get_meta_data( 'label_frontend', $post ),
	                    'enabled' 			=> ovabrw_get_meta_data( 'enabled', $post ),
	                    'show_listing' 		=> ovabrw_get_meta_data( 'show_listing', $post )
	                ];
                }

                update_option( 'ovabrw_custom_taxonomy', $taxonomies );
            }

            // After edit custom taxonomy
            do_action( OVABRW_PREFIX.'after_edit_custom_taxonomy', $post, $this );
		}

		/**
		 * Sort
		 */
		public function sort( $args = [] ) {
			// Before sort custom taxonomy
            do_action( OVABRW_PREFIX.'before_sort_custom_taxonomies', $args, $this );

            // Get slug
			$slug = ovabrw_get_meta_data( 'slug', $args, [] );
            if ( ovabrw_array_exists( $slug ) && current_user_can( 'publish_posts' ) ) {
                // Custom taxonomies
				$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

                // New data
                $new_data = [];

                foreach ( $slug as $s ) {
                    if ( $s && array_key_exists( $s, $taxonomies ) ) {
                        $new_data[$s] = $taxonomies[$s];
                    }
                }

                if ( ovabrw_array_exists( $new_data ) ) {
                    update_option( 'ovabrw_custom_taxonomy', $new_data );
                }
            }

            // After sort custom taxonomy
            do_action( OVABRW_PREFIX.'after_sort_custom_taxonomies', $args, $this );
		}

		/**
		 * instance
		 */
		public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
	}

    // init class
	new OVABRW_Admin_Taxonomies();
}