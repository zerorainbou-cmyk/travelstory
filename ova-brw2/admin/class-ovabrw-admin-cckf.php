<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Custom Checkout Fields class.
 */
if ( !class_exists( 'OVABRW_Admin_CCKF' ) ) {

	class OVABRW_Admin_CCKF {

		/**
		 * instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add sub-menu
            add_action('admin_menu', [ $this, 'add_submenu' ] );
		}

		/**
		 * Add sub-menu: Custom checkout fields
		 */
		public function add_submenu() {
			add_submenu_page(
                'ovabrw-settings',
                esc_html__( 'Custom checkout fields', 'ova-brw' ),
                esc_html__( 'Custom checkout fields', 'ova-brw' ),
                apply_filters( OVABRW_PREFIX.'submenu_cckf_capability', 'edit_posts' ),
                'ovabrw-custom-checkout-field',
                [ $this, 'view_custom_checkout_fields' ],
                6
            );
		}

		/**
		 * View custom checkout fields
		 */
		public function view_custom_checkout_fields() {
			// Before cckf HTML
            do_action( OVABRW_PREFIX.'before_cckf_html', $this );

			include( OVABRW_PLUGIN_ADMIN.'custom-checkout-fields/views/html-custom-checkout-fields.php' );

			// After cckf HTML
            do_action( OVABRW_PREFIX.'after_cckf_html', $this );
		}

		/**
         * Popup custom checkout field
         */
		public function popup_custom_checkout_field( $action = 'new', $type = 'text', $name = '' ) {
            // Before popup cckf HTML
            do_action( OVABRW_PREFIX.'before_popup_cckf_html', $this );

            if ( 'edit' == $action ) {
                include( OVABRW_PLUGIN_ADMIN.'custom-checkout-fields/views/html-popup-edit-custom-checkout-field.php' );
            } elseif ( 'new' == $action ) {
                include( OVABRW_PLUGIN_ADMIN.'custom-checkout-fields/views/html-popup-new-custom-checkout-field.php' );
            }

            // After popup cckf HTML
            do_action( OVABRW_PREFIX.'after_popup_cckf_html', $this );
        }

        /**
         * custom checkout type field
         */
        public function get_types() {
            $types = [
                'text'      => esc_html__( 'Text', 'ova-brw' ),
                'number'    => esc_html__( 'Number', 'ova-brw' ),
                'tel'       => esc_html__( 'Tel', 'ova-brw' ),
                'email'     => esc_html__( 'Email', 'ova-brw' ),
                'password'  => esc_html__( 'Password', 'ova-brw' ),
                'textarea'  => esc_html__( 'Textarea', 'ova-brw' ),
                'radio'     => esc_html__( 'Radio', 'ova-brw' ),
                'checkbox'  => esc_html__( 'Checkbox', 'ova-brw' ),
                'select'    => esc_html__( 'Select', 'ova-brw' ),
                'date'      => esc_html__( 'Date', 'ova-brw' ),
                'file'      => esc_html__( 'File', 'ova-brw' ),
                'price' 	=> esc_html__( 'Price', 'ova-brw' )
            ];

            return apply_filters( OVABRW_PREFIX.'cckf_get_types', $types );
        }

        /**
         * Popup custom checkout field
         */
		public function ovabrw_popup_custom_checkout_field( $action = 'new', $type = 'text', $name = '' ) {
            // Before popup cckf HTML
            do_action( OVABRW_PREFIX.'before_popup_cckf_html', $this );

            if ( 'edit' == $action ) {
                include( OVABRW_PLUGIN_ADMIN.'custom-checkout-fields/views/html-popup-edit-custom-checkout-field.php' );
            } elseif ( 'new' == $action ) {
                include( OVABRW_PLUGIN_ADMIN.'custom-checkout-fields/views/html-popup-new-custom-checkout-field.php' );
            }

            // After popup cckf HTML
            do_action( OVABRW_PREFIX.'after_popup_cckf_html', $this );
        }

        /**
         * Add new field
         */
        public function add( $post = [] ) {
            // Before add cckf
            do_action( OVABRW_PREFIX.'before_add_cckf', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can( 'publish_posts' ) ) {
                // Get custom checkout fields
                $cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

                // Get name
                $name = sanitize_text_field( sanitize_title( ovabrw_get_meta_data( 'name', $post ) ) );
                if ( $name ) {
                    $cckf[$name] = [
                        'type'          => ovabrw_get_meta_data( 'type', $post ),
                        'label'         => ovabrw_get_meta_data( 'label', $post ),
                        'description'   => ovabrw_get_meta_data( 'description', $post ),
                        'class'         => ovabrw_get_meta_data( 'class', $post ),
                        'required'      => ovabrw_get_meta_data( 'required', $post ),
                        'enabled'       => ovabrw_get_meta_data( 'enabled', $post )
                    ];

                    // Max file size
                    if ( (float)ovabrw_get_meta_data( 'max_file_size', $post ) ) {
                        $cckf[$name]['max_file_size'] = (float)$post['max_file_size'];
                    }

                    // Placeholder
                    if ( ovabrw_get_meta_data( 'placeholder', $post ) ) {
                        $cckf[$name]['placeholder'] = $post['placeholder'];
                    }

                    // Default
                    if ( ovabrw_get_meta_data( 'default', $post ) ) {
                        $cckf[$name]['default'] = $post['default'];
                    }

                    // Default date
                    if ( ovabrw_get_meta_data( 'default_date', $post ) ) {
                        $cckf[$name]['default_date'] = $post['default_date'];
                    }

                    // Min number
                    if ( ovabrw_get_meta_data( 'min', $post ) ) {
                        $cckf[$name]['min'] = $post['min'];
                    }

                    // Max number
                    if ( ovabrw_get_meta_data( 'max', $post ) ) {
                        $cckf[$name]['max'] = $post['max'];
                    }

                    // Min date
                    if ( ovabrw_get_meta_data( 'min_date', $post ) ) {
                        $cckf[$name]['min_date'] = $post['min_date'];
                    }

                    // Max date
                    if ( ovabrw_get_meta_data( 'max_date', $post ) ) {
                        $cckf[$name]['max_date'] = $post['max_date'];
                    }

                    // Min price
                    if ( ovabrw_get_meta_data( 'min_price', $post ) ) {
                        $cckf[$name]['min_price'] = $post['min_price'];
                    }

                    // Max price
                    if ( ovabrw_get_meta_data( 'max_price', $post ) ) {
                        $cckf[$name]['max_price'] = $post['max_price'];
                    }

                    // Step price
                    if ( ovabrw_get_meta_data( 'step', $post ) ) {
                        $cckf[$name]['step'] = $post['step'];
                    }

                    // Radio
                    if ( ovabrw_get_meta_data( 'ova_values', $post ) ) {
                        $cckf[$name]['ova_values'] = $post['ova_values'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_prices', $post ) ) {
                        $cckf[$name]['ova_prices'] = ovabrw_recursive_array_price( $post['ova_prices'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_min_qtys', $post ) ) {
                        $cckf[$name]['ova_min_qtys'] = $post['ova_min_qtys'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_qtys', $post ) ) {
                        $cckf[$name]['ova_qtys'] = $post['ova_qtys'];
                    }

                    // Checkbox
                    if ( ovabrw_get_meta_data( 'ova_checkbox_key', $post ) ) {
                        $cckf[$name]['ova_checkbox_key'] = ovabrw_sanitize_title( $post['ova_checkbox_key'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_checkbox_text', $post ) ) {
                        $cckf[$name]['ova_checkbox_text'] = $post['ova_checkbox_text'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_checkbox_price', $post ) ) {
                        $cckf[$name]['ova_checkbox_price'] = ovabrw_recursive_array_price( $post['ova_checkbox_price'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_checkbox_min_qty', $post ) ) {
                        $cckf[$name]['ova_checkbox_min_qty'] = $post['ova_checkbox_min_qty'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_checkbox_qty', $post ) ) {
                        $cckf[$name]['ova_checkbox_qty'] = $post['ova_checkbox_qty'];
                    }

                    // Select
                    if ( ovabrw_get_meta_data( 'ova_options_key', $post ) ) {
                        $cckf[$name]['ova_options_key'] = ovabrw_sanitize_title( $post['ova_options_key'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_options_text', $post ) ) {
                        $cckf[$name]['ova_options_text'] = $post['ova_options_text'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_options_price', $post ) ) {
                        $cckf[$name]['ova_options_price'] = ovabrw_recursive_array_price( $post['ova_options_price'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_options_min_qty', $post ) ) {
                        $cckf[$name]['ova_options_min_qty'] = $post['ova_options_min_qty'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_options_qty', $post ) ) {
                        $cckf[$name]['ova_options_qty'] = $post['ova_options_qty'];
                    }
                }

                // Update cckf
                update_option( 'ovabrw_booking_form', $cckf );
            }

            // After add cckf
            do_action( OVABRW_PREFIX.'after_add_cckf', $post, $this );
        }

        /**
         * Edit field
         */
        public function edit( $post = [] ) {
            // Before edit cckf
            do_action( OVABRW_PREFIX.'before_edit_cckf', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Get custom checkout fields
                $cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

                // Get name
                $new_name = sanitize_text_field( sanitize_title( ovabrw_get_meta_data( 'name', $post ) ) );
                $old_name = sanitize_text_field( ovabrw_get_meta_data( 'old_name', $post ) );

                if ( $new_name && $old_name ) {
                    $data = [
                        'type'          => ovabrw_get_meta_data( 'type', $post ),
                        'label'         => ovabrw_get_meta_data( 'label', $post ),
                        'description'   => ovabrw_get_meta_data( 'description', $post ),
                        'class'         => ovabrw_get_meta_data( 'class', $post ),
                        'required'      => ovabrw_get_meta_data( 'required', $post ),
                        'enabled'       => ovabrw_get_meta_data( 'enabled', $post )
                    ];

                    // Max file size
                    if ( (float)ovabrw_get_meta_data( 'max_file_size', $post ) ) {
                        $data['max_file_size'] = (float)$post['max_file_size'];
                    }

                    // Placeholder
                    if ( ovabrw_get_meta_data( 'placeholder', $post ) ) {
                        $data['placeholder'] = $post['placeholder'];
                    }

                    // Default
                    if ( ovabrw_get_meta_data( 'default', $post ) ) {
                        $data['default'] = $post['default'];
                    }

                    // Default date
                    if ( ovabrw_get_meta_data( 'default_date', $post ) ) {
                        $data['default_date'] = $post['default_date'];
                    }

                    // Min number
                    if ( ovabrw_get_meta_data( 'min', $post ) ) {
                        $data['min'] = $post['min'];
                    }

                    // Max number
                    if ( ovabrw_get_meta_data( 'max', $post ) ) {
                        $data['max'] = $post['max'];
                    }

                    // Min date
                    if ( ovabrw_get_meta_data( 'min_date', $post ) ) {
                        $data['min_date'] = $post['min_date'];
                    }

                    // Max date
                    if ( ovabrw_get_meta_data( 'max_date', $post ) ) {
                        $data['max_date'] = $post['max_date'];
                    }

                    // Min price
                    if ( ovabrw_get_meta_data( 'min_price', $post ) ) {
                        $data['min_price'] = $post['min_price'];
                    }

                    // Max price
                    if ( ovabrw_get_meta_data( 'max_price', $post ) ) {
                        $data['max_price'] = $post['max_price'];
                    }

                    // Step price
                    if ( ovabrw_get_meta_data( 'step', $post ) ) {
                        $data['step'] = $post['step'];
                    }

                    // Radio
                    if ( ovabrw_get_meta_data( 'ova_values', $post ) ) {
                        $data['ova_values'] = $post['ova_values'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_prices', $post ) ) {
                        $data['ova_prices'] = ovabrw_recursive_array_price( $post['ova_prices'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_min_qtys', $post ) ) {
                        $data['ova_min_qtys'] = $post['ova_min_qtys'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_qtys', $post ) ) {
                        $data['ova_qtys'] = $post['ova_qtys'];
                    }

                    // Checkbox
                    if ( ovabrw_get_meta_data( 'ova_checkbox_key', $post ) ) {
                        $data['ova_checkbox_key'] = ovabrw_sanitize_title( $post['ova_checkbox_key'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_checkbox_text', $post ) ) {
                        $data['ova_checkbox_text'] = $post['ova_checkbox_text'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_checkbox_price', $post ) ) {
                        $data['ova_checkbox_price'] = ovabrw_recursive_array_price( $post['ova_checkbox_price'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_checkbox_min_qty', $post ) ) {
                        $data['ova_checkbox_min_qty'] = $post['ova_checkbox_min_qty'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_checkbox_qty', $post ) ) {
                        $data['ova_checkbox_qty'] = $post['ova_checkbox_qty'];
                    }

                    // Select
                    if ( ovabrw_get_meta_data( 'ova_options_key', $post ) ) {
                        $data['ova_options_key'] = ovabrw_sanitize_title( $post['ova_options_key'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_options_text', $post ) ) {
                        $data['ova_options_text'] = $post['ova_options_text'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_options_price', $post ) ) {
                        $data['ova_options_price'] = ovabrw_recursive_array_price( $post['ova_options_price'] );
                    }
                    if ( ovabrw_get_meta_data( 'ova_options_min_qty', $post ) ) {
                        $data['ova_options_min_qty'] = $post['ova_options_min_qty'];
                    }
                    if ( ovabrw_get_meta_data( 'ova_options_qty', $post ) ) {
                        $data['ova_options_qty'] = $post['ova_options_qty'];
                    }

                    // Change name
                    if ( $new_name != $old_name ) {
                        // Update new name
                        $cckf = array_map( function ( $key, $value ) use ( $old_name, $new_name ) {
                            if ( $key === $old_name ) {
                                return [$new_name => $value];
                            }

                            return [$key => $value];
                        }, array_keys( $cckf ), $cckf );

                        // Merge
                        $cckf = call_user_func_array( 'array_merge', $cckf );

                        // Add new name
                        $cckf[$new_name] = $data;
                    } else {
                        $cckf[$old_name] = $data;
                    }
                }

                // Update cckf
                update_option( 'ovabrw_booking_form', $cckf );
            }

            // After edit cckf
            do_action( OVABRW_PREFIX.'after_edit_cckf', $post, $this );
        }

        /**
         * Delete field
         */
        public function delete( $post = [] ) {
            // Before delete cckf
            do_action( OVABRW_PREFIX.'before_delete_cckf', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Get custom checkout fields
                $cckf   = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );
                $fields = ovabrw_get_meta_data( 'fields', $post );

                if ( ovabrw_array_exists( $fields ) ) {
                    foreach ( $fields as $name ) {
                        // remove field
                        if ( isset( $cckf[$name] ) ) unset($cckf[$name]);
                    }
                }

                // Update cckf
                update_option( 'ovabrw_booking_form', $cckf );
            }

            // After delete cckf
            do_action( OVABRW_PREFIX.'after_delete_cckf', $post, $this );
        }

        /**
         * Required field
         */
        public function required( $post = [] ) {
        	// Before required cckf
            do_action( OVABRW_PREFIX.'before_required_cckf', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Get custom checkout fields
                $cckf 	= ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );
                $fields = ovabrw_get_meta_data( 'fields', $post );

                if ( ovabrw_array_exists( $fields ) ) {
                    foreach ( $fields as $name ) {
                        // update field
                        if ( isset( $cckf[$name]['required'] ) ) {
                            $cckf[$name]['required'] = 'on';
                        }
                    }
                }

                update_option( 'ovabrw_booking_form', $cckf );
            }

            // After required cckf
            do_action( OVABRW_PREFIX.'after_required_cckf', $post, $this );
        }

        /**
         * Optional field
         */
        public function optional( $post = [] ) {
        	// Before optional cckf
            do_action( OVABRW_PREFIX.'before_optional_cckf', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Get custom checkout fields
                $cckf 	= ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );
                $fields = ovabrw_get_meta_data( 'fields', $post );

                if ( ovabrw_array_exists( $fields ) ) {
                    foreach ( $fields as $name ) {
                        // update field
                        if ( isset( $cckf[$name]['required'] ) ) {
                            $cckf[$name]['required'] = '';
                        }
                    }
                }

                update_option( 'ovabrw_booking_form', $cckf );
            }

            // After optional cckf
            do_action( OVABRW_PREFIX.'after_optional_cckf', $post, $this );
        }

        /**
         * Enable field
         */
        public function enable( $post = [] ) {
        	// Before enable cckf
            do_action( OVABRW_PREFIX.'before_enable_cckf', $post, $this );

			if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Get custom checkout fields
                $cckf 	= ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );
                $fields = ovabrw_get_meta_data( 'fields', $post );

                if ( ovabrw_array_exists( $fields ) ) {
                    foreach ( $fields as $name ) {
                        // update field
                        if ( isset( $cckf[$name]['enabled'] ) ) {
                            $cckf[$name]['enabled'] = 'on';
                        }
                    }
                }

                update_option( 'ovabrw_booking_form', $cckf );
            }

            // After enable cckf
            do_action( OVABRW_PREFIX.'after_enable_cckf', $post, $this );
        }

        /**
         * Disable field
         */
        public function disable( $post = [] ) {
        	// Before disable cckf
            do_action( OVABRW_PREFIX.'before_disable_cckf', $post, $this );

			if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Get custom checkout fields
                $cckf 	= ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );
                $fields = ovabrw_get_meta_data( 'fields', $post );

                if ( ovabrw_array_exists( $fields ) ) {
                    foreach ( $fields as $name ) {
                        // disable field
                        if ( isset( $cckf[$name]['enabled'] ) ) {
                            $cckf[$name]['enabled'] = '';
                        }
                    }
                }

                update_option( 'ovabrw_booking_form', $cckf );
            }

            // After disable cckf
            do_action( OVABRW_PREFIX.'after_disable_cckf', $post, $this );
        }

        /**
         * Save field
         */
        public function save( $post = [] ) {
        	// Before save cckf
            do_action( OVABRW_PREFIX.'before_save_cckf', $post, $this );

            if ( ovabrw_array_exists( $post ) && current_user_can('publish_posts') ) {
                // Get action
                $action = ovabrw_get_meta_data( 'action', $post );

                if ( 'new' == $action ) {
                    $this->add( $post );
                } elseif ( 'edit' == $action ) {
                    $this->edit( $post );
                }
            }

            // After save cckf
            do_action( OVABRW_PREFIX.'after_save_cckf', $post, $this );
        }

        /**
         * Sort
         */
        public function sort( $args = [] ) {
        	// Before sort cckf
            do_action( OVABRW_PREFIX.'before_sort_cckf', $args, $this );

            // Get fields
			$fields = ovabrw_get_meta_data( 'fields', $args, [] );

            if ( ovabrw_array_exists( $fields ) && current_user_can( 'publish_posts' ) ) {
                // Get custom checkout fields
                $cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

                // New data
                $new_data = [];

                // Loop
                foreach ( $fields as $name ) {
                    if ( $name && array_key_exists( $name, $cckf ) ) {
                        $new_data[$name] = $cckf[$name];
                    }
                }

                // Update cckf
                if ( ovabrw_array_exists( $new_data ) ) {
                    update_option( 'ovabrw_booking_form', $new_data );
                }
            }

            // After sort cckf
            do_action( OVABRW_PREFIX.'after_sort_cckf', $args, $this );
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
	new OVABRW_Admin_CCKF();
}