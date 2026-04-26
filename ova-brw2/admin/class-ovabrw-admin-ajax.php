<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Ajax class.
 */
if ( !class_exists( 'OVABRW_Admin_Ajax', false ) ) {

	class OVABRW_Admin_Ajax {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Define All Ajax function
			$arr_ajax = [
				'update_order_status',
				'get_custom_tax_in_cat',
				'change_rental_type',
				'create_booking_load_product_fields',
				'create_booking_render_guest_types',
				'create_booking_add_guest_info_item',
				'create_booking_change_country',
				'create_booking_calculate_total',
				'add_specification',
				'edit_specification',
				'delete_specification',
				'sort_specifications',
				'enable_specification',
				'disable_specification',
				'change_type_specification',
				'update_insurance',
				'booking_calendar_get_events',
				'booking_calendar_get_available_items',
				'user_autocomplete',
				'add_guest_info_field',
				'edit_guest_info_field',
				'required_guest_info_field',
				'optional_guest_info_field',
				'enable_guest_info_field',
				'disable_guest_info_field',
				'delete_guest_info_field',
				'save_guest_info_field',
				'sort_guest_info_fields',
				'change_type_guest_info_field',
				'sort_custom_taxonomies',
				'add_custom_taxonomy',
				'enable_custom_taxonomy',
				'disable_custom_taxonomy',
				'show_custom_taxonomy',
				'hide_custom_taxonomy',
				'delete_custom_taxonomy',
				'edit_custom_taxonomy',
				'save_custom_taxonomy',
				'sort_custom_checkout_fields',
				'add_custom_checkout_field',
				'edit_custom_checkout_field',
				'required_custom_checkout_field',
				'optional_custom_checkout_field',
				'enable_custom_checkout_field',
				'disable_custom_checkout_field',
				'delete_custom_checkout_field',
				'change_type_custom_checkout_field',
				'save_custom_checkout_field',
				'preview_booking',
				'sync_calendar'
			];

			foreach ( $arr_ajax as $name ) {
				add_action( 'wp_ajax_'.OVABRW_PREFIX.$name, [ $this, OVABRW_PREFIX.$name ] );
				add_action( 'wp_ajax_nopriv_'.OVABRW_PREFIX.$name, [ $this, OVABRW_PREFIX.$name ] );
			}
		}

		/**
		 * Update order status
		 */
		public function ovabrw_update_order_status() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$order_id 		= sanitize_text_field( ovabrw_get_meta_data( 'order_id', $_POST ) );
			$order_status 	= sanitize_text_field( ovabrw_get_meta_data( 'new_order_status', $_POST ) );

			if ( $order_id && $order_status ) {
				$order = wc_get_order( $order_id );

				if ( !current_user_can( apply_filters( OVABRW_PREFIX.'update_order_status' ,'publish_posts' ) ) ) {
					echo 'error_permission';	
				} elseif ( $order->update_status( $order_status ) ) {
					echo 'true';
				} else {
					echo 'false';
				}
			} else {
				echo 'false';
			}
			
			wp_die();
		}

		/**
		 * Get Custom Taxonomy choosed in Category
		 */
		public function ovabrw_get_custom_tax_in_cat() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$term_ids 	= ovabrw_get_meta_data( 'term_ids', $_POST );
			$taxonomies = [];
			
			if ( ovabrw_array_exists( $term_ids ) ) {
				foreach ( $term_ids as $term_id ) {
					$custom_tax = get_term_meta( $term_id, 'ovabrw_custom_tax', true );
					
					if ( $custom_tax ) {
						foreach ( $custom_tax as $slug ) {
							if ( $slug && !in_array( $slug, $taxonomies ) ) {
								array_push( $taxonomies, $slug );
							}
						}
					}
				}
			}
			
			echo implode(",", $taxonomies ); 
			wp_die();
		}

		/**
		 * Update custom checkout fields when sortable
		 */
		public function ovabrw_update_cckf() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			if ( ovabrw_array_exists( $fields ) ) {
				$new_fields = [];
				$cckf 		= ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

				foreach ( $fields as $field ) {
                    if ( !empty( $field ) && array_key_exists( $field, $cckf ) ) {
                        $new_fields[$field] = $cckf[$field];
                    }
                }

                if ( ovabrw_array_exists( $new_fields ) ) {
                    $cckf = $new_fields;
                }

                update_option( ovabrw_meta_key( 'booking_form' ), $cckf );
			}

			wp_die();
		}

		/**
		 * Change Rental Type
		 */
		public function ovabrw_change_rental_type() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $_POST );

			// Rental type
			$rental_type = ovabrw_get_meta_data( 'rental_type', $_POST );

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id, $rental_type );
			if ( $rental_product ) {
				ob_start();
				include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/views/html-rental-data-general.php' );
				echo ob_get_clean();
			}

			wp_die();
		}

		/**
		 * Create new booking: Load product fields
		 */
		public function ovabrw_create_booking_load_product_fields() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Product ID
			$product_id = trim( sanitize_text_field( ovabrw_get_meta_data( 'product_id', $_POST ) ) );
			if ( !$product_id ) wp_die();

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) wp_die();

			// Currency
			$currency = sanitize_text_field( ovabrw_get_meta_data( 'currency', $_POST ) );

			// Item key
			$key = wp_create_nonce( $product_id.current_time( 'timestamp' ) );

			echo wp_json_encode([
				'html' 	=> str_replace( 'ovabrw-item-key', $key, $rental_product->create_booking_view_meta_boxes([ 'currency' => $currency ]) ),
				'key' 	=> $key
			]);
			wp_die();

			wp_die();
		}

		/**
		 * Render guest types HTML
		 */
		public function ovabrw_create_booking_render_guest_types() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get product ID
			$product_id = absint( ovabrw_get_meta_data( 'product_id', $_POST ) );

			// Get product
			$product = wc_get_product( $product_id );
			if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) wp_die();

			// Guests
			$guests = ovabrw_get_meta_data( 'guests', $_POST );

			// Meta key
			$meta_key = ovabrw_get_meta_data( 'meta_key', $_POST );
			
			// Render HTML
			ob_start();

			// Get template
			include OVABRW_PLUGIN_ADMIN . 'bookings/fields/html-guest-types.php';

			$html = ob_get_contents();
			ob_end_clean();

			echo wp_json_encode([
				'html' => $html
			]);

			wp_die();
		}

		/**
		 * Create new booking - Add guest info item
		 */
		public function ovabrw_create_booking_add_guest_info_item() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Product ID
			$product_id = (int)ovabrw_get_meta_data( 'product_id', $_POST );
			if ( !$product_id ) wp_die();

			// Get product
			$product = wc_get_product( $product_id );
			if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) wp_die();

			// Meta key
			$meta_key = ovabrw_get_meta_data( 'meta_key', $_POST );
			if ( !$meta_key ) wp_die();

			// Guest name
			$guest_name = ovabrw_get_meta_data( 'guest_name', $_POST );
			if ( !$guest_name ) wp_die();

			// Guest key
			$key = (int)ovabrw_get_meta_data( 'guest_key', $_POST );
			$key -= 1;
			if ( $key < 0 ) wp_die();

			// Render HTML
			ob_start();

			// Get template
			include ( OVABRW_PLUGIN_ADMIN . 'bookings/fields/html-guest-info.php' );

			$html = ob_get_contents();
			ob_end_clean();

			echo wp_json_encode([
				'html' => $html
			]);

			wp_die();
		}

		/**
		 * Create booking change country
		 */
		public function ovabrw_create_booking_change_country() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Get country
			$country = ovabrw_get_meta_data( 'country', $_POST );
			if ( !$country ) wp_die();			

			$key = ovabrw_get_meta_data( 'key', $_POST, 'billing' );

			// Get data
			$data = ovabrw_get_meta_data( 'item', $_POST, [] );

			// Get country locale
			$address_field = WC()->countries->get_address_fields( $country, $key );

			// Get states
			$states = WC()->countries->get_states( $country );

			$label_state 	= esc_html__( 'State', 'ova-brw' );
			$required_state = false;

			// Postcode
			$label_postcode 	= esc_html__( 'ZIP Code', 'ova-brw' );
			$required_postcode 	= false;

			// Default billing state
			if (  'billing' === $key ) {
				// Default billing state
				$default_state = apply_filters( OVABRW_PREFIX.'create_booking_default_country', 'CA' );

				// Label state
				if ( isset( $address_field['billingstate']['label'] ) && $address_field['billingstate']['label'] ) {
					$label_state = $address_field['billingstate']['label'];
				}

				// Required state
				if ( isset( $address_field['billingstate']['required'] ) && $address_field['billingstate']['required'] ) {
					$required_state = true;
				}

				// Label postcode
				if ( isset( $address_field['billingpostcode']['label'] ) && $address_field['billingpostcode']['label'] ) {
					$label_postcode = $address_field['billingpostcode']['label'];
				}

				// Required postcode
				if ( isset( $address_field['billingpostcode']['required'] ) && $address_field['billingpostcode']['required'] ) {
					$required_postcode = true;
				}
			} elseif ( 'shipping' === $key ) {
				// Default shipping state
				$default_state = ovabrw_get_meta_data( 'state', $data, apply_filters( OVABRW_PREFIX.'create_booking_default_country', 'CA' ) );

				// Label state
				if ( isset( $address_field['shippingstate']['label'] ) && $address_field['shippingstate']['label'] ) {
					$label_state = $address_field['shippingstate']['label'];
				}

				// Required state
				if ( isset( $address_field['shippingstate']['required'] ) && $address_field['shippingstate']['required'] ) {
					$required_state = true;
				}

				// Label postcode
				if ( isset( $address_field['shippingpostcode']['label'] ) && $address_field['shippingpostcode']['label'] ) {
					$label_postcode = $address_field['shippingpostcode']['label'];
				}

				// Required postcode
				if ( isset( $address_field['shippingpostcode']['required'] ) && $address_field['shippingpostcode']['required'] ) {
					$required_postcode = true;
				}
			} else {
				wp_die();
			}

			// Label state
			if ( !$required_state ) {
				$label_state = sprintf( esc_html__( '%s (option)', 'ova-brw' ), $label_state );
			}

			// Label postcode
			if ( !$required_postcode ) {
				$label_postcode = sprintf( esc_html__( '%s (option)', 'ova-brw' ), $label_postcode );
			}

			ob_start();
			?>
				<div class="ovabrw-field">
					<label for="<?php echo esc_attr( $key.'-state' ); ?>" class="<?php echo $required_state ? 'ovabrw-required' : ''; ?>">
						<?php echo esc_html( $label_state ); ?>
					</label>
					<?php if ( ovabrw_array_exists( $states ) ): ?>
						<select name="<?php echo esc_attr( $key.'_state' ); ?>" id="<?php echo esc_attr( $key.'-state' ); ?>" class="<?php echo $required_state ? 'ovabrw-select2 ovabrw-input-required' : 'ovabrw-select2'; ?>" data-placeholder="<?php esc_attr_e( 'Select an option...', 'ova-brw' ); ?>" required>
							<option value="">
								<?php esc_html_e( 'Select an option...', 'ova-brw' ); ?>
							</option>
							<?php foreach ( $states as $skey => $svalue ): ?>
								<option value="<?php echo esc_attr( $skey ); ?>"<?php ovabrw_selected( $skey, $default_state ); ?>>
									<?php echo esc_html( $svalue ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php else: ?>
						<?php ovabrw_wp_text_input([
							'type' 	=> 'text',
							'id' 	=> esc_attr( $key.'-state' ),
							'class' => $required_state ? 'ovabrw-input-required' : '',
							'name' 	=> esc_attr( $key.'_state' ),
							'value' => $default_state,
							'attrs' => [ 'autocomplete' => 'off' ]
						]); ?>
					<?php endif; ?>
				</div>
				<div class="ovabrw-field">
					<label for="<?php echo esc_attr( $key.'-postcode' ); ?>" class="<?php echo $required_postcode ? 'ovabrw-required' : ''; ?>">
						<?php echo esc_html( $label_postcode ); ?>
					</label>
					<?php ovabrw_wp_text_input([
						'type' 	=> 'text',
						'id' 	=> esc_attr( $key.'-postcode' ),
						'class' => $required_postcode ? 'ovabrw-input-required' : '',
						'name' 	=> esc_attr( $key.'_postcode' ),
						'value' => ovabrw_get_meta_data( 'postcode', $data ),
						'attrs' => [ 'autocomplete' => 'off' ]
					]); ?>
				</div>
			<?php

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Create new booking: Calculate total
		 */
		public function ovabrw_create_booking_calculate_total() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Check post data
			if ( !ovabrw_array_exists( $_POST ) ) wp_die();

			// Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $_POST );
			if ( !$product_id ) wp_die();

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) wp_die();

			// Get results
			$results = $rental_product->create_booking_calculate_total( $_POST );
			if ( ovabrw_array_exists( $results ) ) {
				echo json_encode( $results );
			}

			wp_die();
		}

		/**
		 * Add new specification
		 */
		public function ovabrw_add_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			ob_start();

			// include html add new
			OVABRW_Admin_Specifications::instance()->popup_form_fields();
			$html = ob_get_contents();

			ob_clean();

			echo $html;

			wp_die();
		}

		/**
		 * Edit specification
		 */
		public function ovabrw_edit_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );
			$type = ovabrw_get_meta_data( 'type', $_POST );

			if ( !$name || !$type ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Admin_Specifications::instance()->popup_form_fields( 'edit', $type, $name );
			$html = ob_get_contents();

			ob_clean();

			echo $html;

			wp_die();
		}

		/**
		 * Delete specification
		 */
		public function ovabrw_delete_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );

			if ( !$name ) wp_die();

			$post['fields'] = [ $name ];

			OVABRW_Admin_Specifications::instance()->delete( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Sort specifications
		 */
		public function ovabrw_sort_specifications() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			OVABRW_Admin_Specifications::instance()->sort( $_POST ); wp_die();
		}

		/**
		 * Enable specification
		 */
		public function ovabrw_enable_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );

			if ( !$name ) wp_die();

			OVABRW_Admin_Specifications::instance()->enable([
				'fields' => [ $name ]
			]);

			echo 1;
			wp_die();
		}

		/**
		 * Disable specification
		 */
		public function ovabrw_disable_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );

			if ( !$name ) wp_die();

			OVABRW_Admin_Specifications::instance()->disable([
				'fields' => [ $name ]
			]);

			echo 1;
			wp_die();
		}

		/**
		 * Change type specification
		 */
		public function ovabrw_change_type_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Get type
			$type = ovabrw_get_meta_data( 'type', $_POST );

			if ( !$type ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Admin_Specifications::instance()->popup_form_fields( 'new', $type );
			$html = ob_get_contents();

			ob_clean();

			echo $html;

			wp_die();
		}

		/**
		 * Update insurance amount
		 */
		public function ovabrw_update_insurance() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$order_id 	= (int)ovabrw_get_meta_data( 'order_id', $_POST );
			$item_id 	= (int)ovabrw_get_meta_data( 'item_id', $_POST );
			$amount 	= floatval( ovabrw_get_meta_data( 'amount', $_POST ) );
			$tax 		= floatval( ovabrw_get_meta_data( 'tax', $_POST ) );

			if ( !$order_id || !$item_id || $amount < 0 || $tax < 0 ) wp_die();

			$order = wc_get_order( $order_id );
            if ( !$order ) wp_die();

			$item = WC_Order_Factory::get_order_item( absint( $item_id ) );
            if ( !$item ) wp_die();

            // Insurance key
           	$insurance_key = $order->get_meta( '_ova_insurance_key' );

            // Total insurance
            $order_insurance 		= floatval( $order->get_meta( '_ova_insurance_amount' ) );
            $order_insurance_tax 	= floatval( $order->get_meta( '_ova_insurance_tax' ) );

            // Item insurance
            $item_insurance = floatval( $item->get_meta( 'ovabrw_insurance_amount' ) );

            // Item insurance tax
            $item_insurance_tax = floatval( $item->get_meta( 'ovabrw_insurance_tax' ) );

            // Original order and item
            $original_order 	= $original_item = false;
            $original_item_id 	= $order->get_meta( '_ova_original_item_id' );

            if ( absint( $original_item_id ) ) {
            	$original_item = WC_Order_Factory::get_order_item( absint( $original_item_id ) );

            	if ( $original_item ) {
            		$original_order = $original_item->get_order();
            	}
            }

            // Get fees
            $fees = $order->get_fees();
            
            // Update order insurance amount
            if ( ! empty( $fees ) && is_array( $fees ) ) {
            	foreach ( $fees as $item_fee_id => $item_fee ) {
            		$fee_key = sanitize_title( $item_fee->get_name() );

            		if ( $fee_key === $insurance_key ) {
            			$order_insurance -= $item_insurance;
            			$order_insurance += $amount;

            			$order_insurance_tax -= $item_insurance_tax;
            			$order_insurance_tax += $tax;

            			if ( $order_insurance < 0 ) $order_insurance = 0;
            			if ( $order_insurance_tax < 0 ) $order_insurance_tax = 0;

            			// Update item fee
            			if ( wc_tax_enabled() ) {
                            $order_taxes = $order->get_taxes();
                            $tax_item_id = 0;

                            foreach ( $order_taxes as $tax_item ) {
                                $tax_item_id = $tax_item->get_rate_id();

                                if ( $tax_item_id ) break;
                            }

                            $item_fee->set_props(
								[
									'total'     => $order_insurance,
									'subtotal'  => $order_insurance,
									'total_tax' => $order_insurance_tax,
									'taxes'     => [
										'total' => [ $tax_item_id => $order_insurance_tax ]
									]
								]
							);

                            // Update original item
                            if ( $original_item ) {
                            	// Get original item remaining insurance amount
                            	$item_remaining_insurance = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance' ) );

                            	// Get original item remaining insurance tax amount
                            	$item_remaining_insurance_tax = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance_tax' ) );

                            	// Update original item meta data
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance', $order_insurance );
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance_tax', $order_insurance_tax );
                            	$original_item->save();

                            	// Update original order
	                            if ( $original_order ) {
	                            	// Get original order remaining insurance amount
	                            	$order_remaining_insurance = floatval( $original_order->get_meta( '_ova_remaining_insurance' ) );
	                            	$order_remaining_insurance -= $item_remaining_insurance;
	                            	$order_remaining_insurance += $order_insurance;

	                            	// Get original order remaining insurance tax amount
	                            	$order_remaining_insurance_tax = floatval( $original_order->get_meta( '_ova_remaining_insurance_tax' ) );
	                            	$order_remaining_insurance_tax -= $item_remaining_insurance_tax;
	                            	$order_remaining_insurance_tax += $order_insurance_tax;

	                            	// Update original order meta data
	                            	$original_order->update_meta_data( '_ova_remaining_insurance', $order_remaining_insurance );
	                            	$original_order->update_meta_data( '_ova_remaining_insurance_tax', $order_remaining_insurance_tax );
	                            	$original_order->save();
	                            }
                            }
            			} else {
            				$item_fee->set_props([
            					'total'     => $order_insurance,
								'subtotal'  => $order_insurance
            				]);

							// Update original order and item
                            if ( $original_item ) {
                            	// Get original item remaining insurance amount
                            	$item_remaining_insurance = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance' ) );

                            	// Update original item meta data
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance', $order_insurance );
                            	$original_item->save();

                            	// Update original order
	                            if ( $original_order ) {
	                            	// Get original order remaining insurance amount
	                            	$order_remaining_insurance = floatval( $original_order->get_meta( '_ova_remaining_insurance' ) );
	                            	$order_remaining_insurance -= $item_remaining_insurance;
	                            	$order_remaining_insurance += $order_insurance;

	                            	// Update original order meta data
	                            	$original_order->update_meta_data( '_ova_remaining_insurance', $order_remaining_insurance );
	                            	$original_order->save();
	                            }
                            }
            			}

            			$item_fee->set_amount( $order_insurance );
            			$item_fee->save();

            			// Update item insurance
        				$item->update_meta_data( 'ovabrw_insurance_amount', $amount );
        				$item->update_meta_data( 'ovabrw_insurance_tax', $tax );
        				$item->save();

        				// Update order insurance
        				$order->update_meta_data( '_ova_insurance_amount', $order_insurance );
        				$order->update_meta_data( '_ova_insurance_tax', $order_insurance_tax );
        				$order->update_taxes();
        				$order->calculate_totals( false );
            		}
            	}
            }

            wp_die();
		}

		/**
		 * Booking calendar filter by product ID
		 */
		public function ovabrw_booking_calendar_get_events() {
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Product ID
		    $product_id = (int)ovabrw_get_meta_data( 'product_id', $_POST );

		    // Current month
		    $current_month = ovabrw_get_meta_data( 'current_month', $_POST );

		    // Current year
		    $current_year = ovabrw_get_meta_data( 'current_year', $_POST );

		    // Get events
		    $events = OVABRW_Admin_Bookings::instance()->get_events( $product_id, $current_month, $current_year );

			echo json_encode([
				'events' => $events
			]);
			wp_die();
		}

		/**
		 * Booking calendar get available items
		 */
		public function ovabrw_booking_calendar_get_available_items() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Product ID
		    $product_id = (int)ovabrw_get_meta_data( 'product_id', $_POST );
		    if ( !$product_id ) wp_die();

		    // Get rental product
		    $rental_product = OVABRW()->rental->get_rental_product( $product_id );
		    if ( !$rental_product ) wp_die();

		    // Pick-up location
		    $pickup_location = ovabrw_get_meta_data( 'pickup_location', $_POST );

		    // Drop-off location
		    $dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $_POST );

		    // Pick-up date
		    $pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_POST ) );

		    // Drop-off date
		    $dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $_POST ) );

		    // Get items available
			$items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'checkout' );

			// Vehicles available
			if ( is_array( $items_available ) ) {
				echo sprintf( esc_html__( 'Items available: %s', 'ova-brw' ), implode( ', ', $items_available ) );
			} else {
				echo sprintf( esc_html__( 'Items available: %s', 'ova-brw' ), $items_available );
			}

			wp_die();
		}

		/**
		 * Search customers by name, phone, or email
		 */
		public function ovabrw_user_autocomplete() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get keyword
			$keyword = sanitize_text_field( ovabrw_get_meta_data( 'keyword', $_POST ) );
			if ( !$keyword ) wp_die();

			// Get meta key
			$meta_key = sanitize_text_field( ovabrw_get_meta_data( 'meta_key', $_POST ) );
			if ( !$meta_key ) wp_die();
			
			// Results
			$results = [];

			global $wpdb;

			// Meta query
			$meta_query = [
				[
					'key'     => $meta_key,
					'value'   => $keyword,
		 			'compare' => 'LIKE'
				]
			];

			// Search by first name or last name
			if ( in_array( $meta_key, [ 'billing_first_name', 'shipping_first_name' ] ) ) {
				$meta_query = [
					'relation' => 'OR',
					[
						'key'     => $meta_key,
						'value'   => $keyword,
			 			'compare' => 'LIKE'
					],
					[
						'key'     => 'first_name',
						'value'   => $keyword,
			 			'compare' => 'LIKE'
					]
				];
			} elseif ( in_array( $meta_key, [ 'billing_last_name', 'shipping_last_name' ] ) ) {
				$meta_query = [
					'relation' => 'OR',
					[
						'key'     => $meta_key,
						'value'   => $keyword,
			 			'compare' => 'LIKE'
					],
					[
						'key'     => 'last_name',
						'value'   => $keyword,
			 			'compare' => 'LIKE'
					]
				];
			} elseif ( in_array( $meta_key, [ 'billing_email', 'shipping_email' ] ) ) {
				$meta_query = [
					[
						'key'     => 'billing_email',
						'value'   => $keyword,
			 			'compare' => 'LIKE'
					]
				];
			} elseif ( in_array( $meta_key, [ 'billing_phone', 'shipping_phone' ] ) ) {
				$meta_query = [
					'relation' => 'OR',
					[
						'key'     => 'billing_phone',
						'value'   => $keyword,
			 			'compare' => 'LIKE'
					],
					[
						'key'     => 'shipping_phone',
						'value'   => $keyword,
			 			'compare' => 'LIKE'
					]
				];
			} // END if

			// Get user ids
			// phpcs:ignore
			$user_ids = get_users([
				'number' 			=> 20,
				'fields' 			=> 'ID',
				'meta_query' 		=> $meta_query // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			]);

			if ( ovabrw_array_exists( $user_ids ) ) {
				foreach ( $user_ids as $user_id ) {
					// Get first name
					$first_name = get_the_author_meta( 'first_name', $user_id );
					if ( !$first_name ) $first_name = get_the_author_meta( 'billing_first_name', $user_id );
					if ( !$first_name ) $first_name = get_the_author_meta( 'shipping_first_name', $user_id );

					// Get last name
					$last_name = get_the_author_meta( 'last_name', $user_id );
					if ( !$last_name ) $last_name = get_the_author_meta( 'billing_last_name', $user_id );
					if ( !$last_name ) $last_name = get_the_author_meta( 'shipping_last_name', $user_id );

					// Check first name & last name exists
					if ( !$first_name && !$last_name ) continue;

					// Label
					$label = implode( ' ', [ $first_name, $last_name ] );

					// Search by email
					if ( in_array( $meta_key, [ 'billing_email', 'shipping_email' ] ) ) {
						// Get user info
						$user_info = get_userdata( $user_id );
						if ( $user_info && is_object( $user_info ) ) {
							$label = $user_info->user_email;
						}
					}

					$results[] = [
						'label' 	=> $label,
						'billing' 	=> [
							'first_name' 	=> get_user_meta( $user_id, 'billing_first_name', true ) ?: $first_name,
							'last_name' 	=> get_user_meta( $user_id, 'billing_last_name', true ) ?: $last_name,
							'company' 		=> get_user_meta( $user_id, 'billing_company', true ),
							'country' 		=> get_user_meta( $user_id, 'billing_country', true ),
							'address_1' 	=> get_user_meta( $user_id, 'billing_address_1', true ),
							'address_2' 	=> get_user_meta( $user_id, 'billing_address_2', true ),
							'city' 			=> get_user_meta( $user_id, 'billing_city', true ),
							'state' 		=> get_user_meta( $user_id, 'billing_state', true ),
							'postcode' 		=> get_user_meta( $user_id, 'billing_postcode', true ),
							'phone' 		=> get_user_meta( $user_id, 'billing_phone', true ),
							'email' 		=> get_user_meta( $user_id, 'billing_email', true )
						],
						'shipping' 	=> [
							'first_name' 	=> get_user_meta( $user_id, 'shipping_first_name', true ) ?: $first_name,
							'last_name' 	=> get_user_meta( $user_id, 'shipping_last_name', true ) ?: $last_name,
							'company' 		=> get_user_meta( $user_id, 'shipping_company', true ),
							'country' 		=> get_user_meta( $user_id, 'shipping_country', true ),
							'address_1' 	=> get_user_meta( $user_id, 'shipping_address_1', true ),
							'address_2' 	=> get_user_meta( $user_id, 'shipping_address_2', true ),
							'city' 			=> get_user_meta( $user_id, 'shipping_city', true ),
							'state' 		=> get_user_meta( $user_id, 'shipping_state', true ),
							'postcode' 		=> get_user_meta( $user_id, 'shipping_postcode', true ),
							'phone' 		=> get_user_meta( $user_id, 'shipping_phone', true ),
							'email' 		=> get_user_meta( $user_id, 'shipping_email', true )
						]
					];
				}
			}

			// Response
			wp_send_json( $results );
		}

		/**
		 * Add new guest info field
		 */
		public function ovabrw_add_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			ob_start();

			// include html add new
			OVABRW_Guest_Info_Fields::instance()->popup_guest_info_field();

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Edit guest info field
		 */
		public function ovabrw_edit_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );
			$type = ovabrw_get_meta_data( 'type', $_POST );

			if ( !$name || !$type ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Guest_Info_Fields::instance()->popup_guest_info_field( 'edit', $type, $name );

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Required guest info field
		 */
		public function ovabrw_required_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Guest_Info_Fields::instance()->required( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Optional guest info field
		 */
		public function ovabrw_optional_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Guest_Info_Fields::instance()->optional( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Enable guest info field
		 */
		public function ovabrw_enable_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Guest_Info_Fields::instance()->enable( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Disable guest info field
		 */
		public function ovabrw_disable_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Guest_Info_Fields::instance()->disable( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Delete guest info field
		 */
		public function ovabrw_delete_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Guest_Info_Fields::instance()->delete( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Save guest info field
		 */
		public function ovabrw_save_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get data
			$data = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'data', $_POST ) );

			// Save field
			OVABRW_Guest_Info_Fields::instance()->save( $data );

			// Get action
			$action = ovabrw_get_meta_data( 'action', $data );
			if ( 'edit' === $action ) {
				echo esc_html( 'Updated', 'ova-brw' );
			} else {
				echo esc_html( 'Saved', 'ova-brw' );
			}

			wp_die();
		}

		/**
		 * Sort guest info fields
		 */
		public function ovabrw_sort_guest_info_fields() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			OVABRW_Guest_Info_Fields::instance()->sort( $_POST ); wp_die();
		}

		/**
		 * Change type guest info field
		 */
		public function ovabrw_change_type_guest_info_field() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$type = ovabrw_get_meta_data( 'type', $_POST );

			if ( !$type ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Guest_Info_Fields::instance()->popup_guest_info_field( 'new', $type );

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Sort custom taxonomies
		 */
		public function ovabrw_sort_custom_taxonomies() {
			// Verify
			check_admin_referer( 'ovabrw-security-ajax', 'security' );
			OVABRW_Admin_Taxonomies::instance()->sort( $_POST ); wp_die();
		}

		/**
		 * Add custom taxonomy
		 */
		public function ovabrw_add_custom_taxonomy() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			ob_start();

			// include html add new
			OVABRW_Admin_Taxonomies::instance()->ovabrw_popup_custom_taxonomy();

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Enable custom taxonomy
		 */
		public function ovabrw_enable_custom_taxonomy() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get slug
			$slug = ovabrw_get_meta_data( 'slug', $_POST );
			if ( !$slug ) wp_die();

			// Get post
			$post['slug'] = !ovabrw_array_exists( $slug ) ? [$slug] : $slug;
			OVABRW_Admin_Taxonomies::instance()->enabled( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Disable custom taxonomy
		 */
		public function ovabrw_disable_custom_taxonomy() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get slug
			$slug = ovabrw_get_meta_data( 'slug', $_POST );
			if ( !$slug ) wp_die();

			// Get post
			$post['slug'] = !ovabrw_array_exists( $slug ) ? [$slug] : $slug;
			OVABRW_Admin_Taxonomies::instance()->disable( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Show custom taxonomy in listing
		 */
		public function ovabrw_show_custom_taxonomy() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get slug
			$slug = ovabrw_get_meta_data( 'slug', $_POST );
			if ( !$slug ) wp_die();

			// Get post
			$post['slug'] = !ovabrw_array_exists( $slug ) ? [$slug] : $slug;
			OVABRW_Admin_Taxonomies::instance()->show( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Hide custom taxonomy in listing
		 */
		public function ovabrw_hide_custom_taxonomy() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get slug
			$slug = ovabrw_get_meta_data( 'slug', $_POST );
			if ( !$slug ) wp_die();

			// Get post
			$post['slug'] = !ovabrw_array_exists( $slug ) ? [$slug] : $slug;
			OVABRW_Admin_Taxonomies::instance()->hide( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Delete custom taxonomy
		 */
		public function ovabrw_delete_custom_taxonomy() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get slug
			$slug = ovabrw_get_meta_data( 'slug', $_POST );
			if ( !$slug ) wp_die();

			// Get post
			$post['slug'] = !ovabrw_array_exists( $slug ) ? [$slug] : $slug;
			OVABRW_Admin_Taxonomies::instance()->delete( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Edit custom tanonomy
		 */
		public function ovabrw_edit_custom_taxonomy() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get slug
			$slug = ovabrw_get_meta_data( 'slug', $_POST );
			if ( !$slug ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Admin_Taxonomies::instance()->ovabrw_popup_custom_taxonomy( 'edit', $slug );

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Save custom taxonomy
		 */
		public function ovabrw_save_custom_taxonomy() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get data
			$data = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'data', $_POST ) );

			// Save field
			OVABRW_Admin_Taxonomies::instance()->save( $data );

			// Get action
			$action = ovabrw_get_meta_data( 'action', $data );
			if ( 'edit' === $action ) {
				echo esc_html( 'Updated', 'ova-brw' );
			} else {
				echo esc_html( 'Saved', 'ova-brw' );
			}
			
			wp_die();
		}

		/**
		 * Sort cckf
		 */
		public function ovabrw_sort_custom_checkout_fields() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Sort
			OVABRW_Admin_CCKF::instance()->sort( $_POST ); wp_die();
		}

		/**
		 * Add cckf
		 */
		public function ovabrw_add_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			ob_start();

			// include html add new
			OVABRW_Admin_CCKF::instance()->ovabrw_popup_custom_checkout_field();

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Edit cckf
		 */
		public function ovabrw_edit_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get type
			$type = ovabrw_get_meta_data( 'type', $_POST );
			if ( !$type ) wp_die();

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );
			if ( !$name ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Admin_CCKF::instance()->ovabrw_popup_custom_checkout_field( 'edit', $type, $name );

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Required cckf
		 */
		public function ovabrw_required_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Admin_CCKF::instance()->required( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Optional cckf
		 */
		public function ovabrw_optional_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Admin_CCKF::instance()->optional( $post );
		}

		/**
		 * Enable cckf
		 */
		public function ovabrw_enable_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Admin_CCKF::instance()->enable( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Disable cckf
		 */
		public function ovabrw_disable_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Admin_CCKF::instance()->disable( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Delete cckf
		 */
		public function ovabrw_delete_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			// Get name
			$name = ovabrw_get_meta_data( 'name', $_POST );

			// Check
			if ( !$name && !ovabrw_array_exists( $fields ) ) wp_die();

			$post['fields'] = $name ? [$name] : $fields;

			OVABRW_Admin_CCKF::instance()->delete( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Change cckf type
		 */
		public function ovabrw_change_type_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get type
			$type = ovabrw_get_meta_data( 'type', $_POST );
			if ( !$type ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Admin_CCKF::instance()->ovabrw_popup_custom_checkout_field( 'new', $type );

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Save custom checkout fields
		 */
		public function ovabrw_save_custom_checkout_field() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get data
			$data = ovabrw_recursive_replace( '\\', '', ovabrw_get_meta_data( 'data', $_POST ) );

			// Save field
			OVABRW_Admin_CCKF::instance()->save( $data );

			// Get action
			$action = ovabrw_get_meta_data( 'action', $data );
			if ( 'edit' === $action ) {
				echo esc_html( 'Updated', 'ova-brw' );
			} else {
				echo esc_html( 'Saved', 'ova-brw' );
			}
			
			wp_die();
		}

		/**
		 * Preview booking
		 */
		public function ovabrw_preview_booking() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get order id
			$order_id = (int)ovabrw_get_meta_data( 'order_id', $_POST );

			// Check user can
			if ( !current_user_can( 'edit_shop_orders' ) || !$order_id ) {
				wp_die(-1);
			}

			// Get order
			$order = wc_get_order( $order_id );
			if ( $order ) {
				include_once WC_ABSPATH . 'includes/admin/list-tables/class-wc-admin-list-table-orders.php';

				// Get order data
				$order_data = WC_Admin_List_Table_Orders::order_preview_get_order_details( $order );
				wp_send_json_success( $order_data );
			}

			wp_die();
		}

		/**
		 * Sync calendar
		 */
		public function ovabrw_sync_calendar() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get product id
			$product_id = absint( ovabrw_get_meta_data( 'product_id', $_POST ) );
			if ( !$product_id ) {
				echo json_encode([
					'error' => esc_html__( 'Product does not exist.', 'ova-brw' )
				]);
				wp_die();
			}

			// Get ical link
			$ical_link = esc_url( ovabrw_get_meta_data( 'ical_link', $_POST ) );
			if ( !$ical_link ) {
				echo json_encode([
					'error' => esc_html__( 'iCal link does not exist.', 'ova-brw' )
				]);
				wp_die();
			} elseif ( !OVABRW()->options->is_ical_url( $ical_link ) ) {
				echo json_encode([
					'error' => esc_html__( 'iCal link is not in the correct format.', 'ova-brw' )
				]);
				wp_die();
			}

			// Sync calendar
			OVABRW()->options->sync_calendar_from_ical( $product_id, $ical_link );
			echo json_encode([
				'mesg' => esc_html__( 'Calendar import successful.', 'ova-brw' )
			]);
			wp_die();
		}
	}

	// init class
	new OVABRW_Admin_Ajax();
}