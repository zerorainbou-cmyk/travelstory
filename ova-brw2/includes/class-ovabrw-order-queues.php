<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Class OVABRW_Order_Queues
 */
if ( !class_exists( 'OVABRW_Order_Queues' ) ) {

	class OVABRW_Order_Queues {

		// Options
		private const STATUS_OPTION    = 'ovabrw_order_sync_status';
    	private const COMPLETED_OPTION = 'ovabrw_order_sync_completed';

    	// Action scheduler
	 	const ACTION_INIT  = 'ovabrw_order_sync_init';
    	const ACTION_BATCH = 'ovabrw_order_sync_batch';
    	const ACTION_GROUP = 'ovabrw_order_sync_once';

    	// Number of orders synchronized each time
    	const BATCH_SIZE = 50;

		/**
		 * The single instance of the class.
		 *
		 * @var OVABRW_Order_Queues
		 * @since 1.0
		 */
		protected static $_instance = null;

		// Order ids
		protected ?array $order_ids = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Ajax order sync
			add_action( 'wp_ajax_ovabrw_order_sync_start',  [ $this, 'ajax_start_sync' ] );
	        add_action( 'wp_ajax_ovabrw_order_sync_status', [ $this, 'ajax_get_status' ] );

	        // Action scheduler
	        add_action( self::ACTION_INIT,  [ $this, 'action_init_batches' ] );
	        add_action( self::ACTION_BATCH, [ $this, 'action_process_batch' ], 10, 1 );

	        // Cron update order_queues table
	        add_action( 'init', [ $this, 'register_cron' ] );

	        // Clear cron
	        register_deactivation_hook( __FILE__, [ $this, 'clear_cron' ] );

	        // Cron schedules
			add_filter( 'cron_schedules', [ $this, 'cron_schedules' ] );

			// Cron order queues
			add_action( 'ovabrw_order_queues_event', [ $this, 'order_queues_event' ] );

			// Update order status
            add_action( 'woocommerce_order_status_changed', [ $this, 'update_order_status' ], 10, 4 );

            // Trash order - HPOS
            add_action( 'woocommerce_trash_order', [ $this, 'trash_order_hpos' ] );

            // Untrash order - HPOS
            add_action( 'woocommerce_untrash_order', [ $this, 'untrash_order_hpos' ], 10, 2 );

            // Delete order - HPOS
            add_action( 'woocommerce_delete_order', [ $this, 'delete_order_hpos' ] );

            // Trashed order (legacy)
            add_action( 'trashed_post', [ $this, 'trashed_order_legacy' ] );

            // Untrashed order (legacy)
            add_action( 'untrashed_post', [ $this, 'untrashed_order_legacy' ], 10, 2 );

            // Deleted order (legacy)
            add_action( 'deleted_post', [ $this, 'deleted_order_legacy' ] );
		}

		/**
		 * Log
		 */
		private function log( $message ) {
		    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		        error_log( ( is_array( $message ) || is_object( $message ) ? print_r( $message, true ) : $message ) );
		    }
		}

		/**
		 * Get table name
		 */
		public function get_table_name() {
			global $wpdb;
			return $wpdb->prefix . OVABRW_PREFIX . 'order_queues';
		}

		/**
		 * Table exists
		 */
		public function table_exists() {
			global $wpdb;

			// Get table name
			$table_name = esc_sql( $this->get_table_name() );

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
			$exists = ( $wpdb->get_var(
			    $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name )
			) === $table_name );
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			return $exists;
		}

		/**
		 * Create table
		 */
		public function create_table() {
			// Check table exists
			if ( $this->table_exists() ) return;

			global $wpdb;

			// Table name
			$table_name = esc_sql( $this->get_table_name() );

			// Get charset collate
    		$charset_collate = $wpdb->get_charset_collate();

    		// sql
    		$sql = "CREATE TABLE $table_name (
		        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		        order_id BIGINT(20) UNSIGNED NOT NULL,
		        status VARCHAR(50) NOT NULL,
		        item_id BIGINT(20) UNSIGNED NOT NULL,
		        product_id BIGINT(20) UNSIGNED NOT NULL,
		        pickup_date BIGINT(20) UNSIGNED NOT NULL,
		        dropoff_date BIGINT(20) UNSIGNED NOT NULL,
		        quantity INT(11) NOT NULL DEFAULT 1,
		        vehicle_id VARCHAR(255) DEFAULT NULL,
		        PRIMARY KEY (id),
		        KEY order_id (order_id),
		        KEY item_id (item_id),
		        KEY product_id (product_id),
		        KEY pickup_date (pickup_date),
		        KEY dropoff_date (dropoff_date),
		        KEY vehicle_id (vehicle_id)
		    ) $charset_collate;";

		    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		    dbDelta($sql);
		}

		/**
		 * Drop table
		 */
		public function drop_table() {
			global $wpdb;

			// Table name
			$table_name = esc_sql( $this->get_table_name() );

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$exists = $wpdb->get_var( $wpdb->prepare(
			    "SHOW TABLES LIKE %s",
			    $table_name
			));

			// Drop table
			if ( $exists ) {
	            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
	            $wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );
	        }
	        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}

		/**
		 * Insert order
		 */
		public function insert_order( $order ) {
			if ( !$order || !is_object( $order ) ) return false;

			// Get order id
			$order_id = $order->get_id();
			if ( !$order_id ) return false;

			global $wpdb;

			// Get order status
			$status = 'wc-' . $order->get_status();

			// Get table name
			$table_name = esc_sql( $this->get_table_name() );

			// Get items
            $items = $order->get_items();
            if ( !ovabrw_array_exists( $items ) ) return false;

            // Loop items
            foreach ( $items as $item_id => $item ) {
            	// Get item product id
                $product_id = $item->get_product_id();

                // Get rental product
                $rental_product = OVABRW()->rental->get_rental_product( $product_id );
                if ( !$rental_product ) continue;

            	// Pick-up date
            	$pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date_real' ) );
            	if ( !$pickup_date ) continue;

            	// Drop-off date
            	$dropoff_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date_real' ) );
            	if ( !$dropoff_date || $dropoff_date < current_time( 'timestamp' ) ) continue;

            	// is tour
            	if ( 'tour' === $rental_product->get_type() ) {
            		// Get quantity
					$quantity = (int)$item->get_meta( 'ovabrw_numberof_guests' );
					if ( !$quantity ) $quantity = 1;
            	} else {
            		// Get quantity
					$quantity = (int)$item->get_meta( 'ovabrw_number_vehicle' );
					if ( !$quantity ) $quantity = 1;
            	}

				// Get vehicle id
				$vehicle_id = $item->get_meta( 'id_vehicle' );
				if ( !$vehicle_id ) $vehicle_id = '';

                // Get order queue id
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->insert(
				    $table_name,
				    [
				    	'order_id' 		=> $order_id,
				        'status'   		=> $status,
				        'item_id' 		=> $item_id,
				        'product_id' 	=> $product_id,
				        'pickup_date' 	=> $pickup_date,
				        'dropoff_date' 	=> $dropoff_date,
				        'quantity' 		=> $quantity,
				        'vehicle_id' 	=> $vehicle_id
				    ],
				    [ '%d', '%s', '%d', '%d', '%d', '%d', '%d', '%s' ]
				);
				// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
            } // END loop
		}

		/**
		 * Detele order
		 */
		public function delete_order( int $order_id ) {
			if ( !$order_id || !$this->table_exists() ) return;

			global $wpdb;

			// Get table name
			$table_name = esc_sql( $this->get_table_name() );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->delete(
			    $table_name,
			    [ 'id' => $order_id ],
			    [ '%d' ]
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}

		/**
	     * Get order ids
	     */
	    protected function get_order_ids() {
	    	if ( $this->order_ids !== null ) {
	            return $this->order_ids;
	        }

	    	// Get order status
	    	$order_status = ovabrw_get_order_status();

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
                        AND oim.meta_value >= %d
                        AND o.status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_pickoff_date_strtotime',
                    	current_time( 'timestamp' )
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
                        AND oitem_meta.meta_value >= %d
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_pickoff_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	}

        	$this->order_ids = apply_filters( OVABRW_PREFIX.'order_sync_order_ids', $order_ids );

        	return $this->order_ids;
	    }

	    /**
	     * Get orders count
	     *
	     * @return int
	     */
	    protected function get_orders_count(): int {
	        return apply_filters( OVABRW_PREFIX . 'order_sync_orders_count', count( $this->get_order_ids() ) );
	    }

	    /**
	     * Get order queues
	     */
	    public function get_order_queues() {
	    	// Check table exists
			if ( !$this->table_exists() ) return false;

	    	// WordPress Database
			global $wpdb;

			// Get order queue
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$order_queues = $wpdb->get_results( "
			    SELECT id, pickup_date, dropoff_date
			    FROM {$wpdb->prefix}ovabrw_order_queues
			", ARRAY_A );
        	// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			return apply_filters( OVABRW_PREFIX.'get_order_queues', $order_queues );
	    }

		/**
		 * Order sync completed
		 */
		public function is_completed(): bool {
	        return (bool) get_option( self::COMPLETED_OPTION, false );
	    }

	    /**
	     * Sync status
	     */
	    public function get_status(): array {
	        return (array)get_option( self::STATUS_OPTION, [
	            'total'     => 0,
	            'processed' => 0,
	            'running'   => false,
	            'done'      => false,
	        ]);
	    }

	    /**
	     * Update sync status
	     */
	    private function update_status( array $status ): void {
	        update_option( self::STATUS_OPTION, $status, false );
	    }

	    /**
	     * Reset sync status
	     */
	    private function reset_status( int $total ): void {
	        $this->update_status( [
	            'total'     => $total,
	            'processed' => 0,
	            'running'   => true,
	            'done'      => false
	        ]);
	    }

	    /**
	     * Mark completed
	     */
	    private function mark_completed(): void {
	        update_option( self::COMPLETED_OPTION, true, false );
	    }

	    /**
	     * Ajax start sync
	     */
	    public function ajax_start_sync() {
	    	check_admin_referer( 'ovabrw-security-ajax', 'security' );

	    	// Check permission
	        if ( !current_user_can( 'manage_options' ) ) {
	            wp_send_json_error( esc_html__( 'Permission denied', 'ova-brw' ) );
	        }

	        // Check sync completed
	        if ( $this->is_completed() ) {
	            wp_send_json_error( esc_html__( 'Orders have already been synced.', 'ova-brw' ) );
	        }

	        // Check async action exists
	        if ( !function_exists( 'as_enqueue_async_action' ) ) {
	            wp_send_json_error( esc_html__( 'Action Scheduler is not available.', 'ova-brw' ) );
	        }

	        // Prevent double start
	        $status = $this->get_status();
	        if ( !empty( $status['running'] ) ) {
	            wp_send_json_success();
	        }

	        // Create order queues table
	        $this->create_table();

	        // Get orders count
	        $total = $this->get_orders_count();
	        $this->reset_status( (int)$total );

	        as_enqueue_async_action(
	            self::ACTION_INIT,
	            [],
	            self::ACTION_GROUP
	        );

	        wp_send_json_success();
	    }

	    /**
	     * Ajax get status
	     */
	    public function ajax_get_status() {
	    	check_admin_referer( 'ovabrw-security-ajax', 'security' );
	        wp_send_json( $this->get_status() );
	    }

	    /**
	     * Action init batches
	     */
	    public function action_init_batches() {
	        if ( $this->is_completed() ) return;

	        if ( ovabrw_array_exists( $this->get_order_ids() ) ) {
	        	foreach ( array_chunk( $this->get_order_ids(), self::BATCH_SIZE ) as $batch ) {
		            as_enqueue_async_action(
		                self::ACTION_BATCH,
		                [ 'order_ids' => $batch ],
		                self::ACTION_GROUP
		            );
		        }

		        if ( function_exists( 'as_run_queue' ) ) {
			        as_run_queue();
			    }
	        } else {
	        	// Create oreder queues table
	        	$this->create_table();

				// Order sync completed
				$this->mark_completed();

				// Get status
				$status = $this->get_status();
				$status['running'] 	= false;
				$status['done'] 	= true;
				$this->update_status( $status );
	        } // END if
	    }

	    /**
	     * Action process batch
	     */
	    public function action_process_batch( array $args ) {
	        if ( $this->is_completed() ) return;

		    // Get sync status
		    $status = $this->get_status();
	        if ( empty( $status['running'] ) ) return;
    
		    // Get order ids
		    $order_ids = isset( $args['order_ids'] ) ? $args['order_ids'] : $args;
		    if ( empty( $order_ids ) ) return;

		    // Insert order
		    foreach ( $order_ids as $order_id ) {
		        $this->insert_order( wc_get_order( $order_id ) );
		        $status['processed']++;
		    }

		    // Update status
		    if ( $status['processed'] >= $status['total'] ) {
		        $status['running'] = false;
		        $status['done']    = true;
		        $this->mark_completed();
		    }
		    $this->update_status( $status );
	    }

	    /**
	     * Register cron
	     */
	    public function register_cron() {
	    	if ( !wp_next_scheduled( 'ovabrw_order_queues_event' ) ) {
			    wp_schedule_event( time(), OVABRW_PREFIX.'order_queues_recurrence', 'ovabrw_order_queues_event' );
			}
	    }

	    /**
	     * Clear cron
	     */
	    public function clear_cron() {
			wp_clear_scheduled_hook( 'ovabrw_order_queues_event' );
		}

		/**
		 * Cron schedules
		 */
		public function cron_schedules( $schedules ) {
			$interval = apply_filters( OVABRW_PREFIX.'order_queues_recurrence', DAY_IN_SECONDS );

			$schedules['ovabrw_order_queues_recurrence'] = [
				'interval' 	=> $interval,
				/* Translators: %s Interval. */
		        'display' 	=> sprintf( esc_html__( 'Every %s seconds', 'ova-brw' ), $interval )
			];

			return $schedules;
		}

		/**
		 * Order queues event
		 */
		public function order_queues_event() {
			// Get order queues
			$order_queues = $this->get_order_queues();

			// Before order queues event
			do_action( OVABRW_PREFIX.'before_order_queues_event', $order_queues );

			// Loop
			if ( ovabrw_array_exists( $order_queues ) ) {
        		foreach ( $order_queues as $order ) {
        			// Get order id
        			$order_id = (int)ovabrw_get_meta_data( 'id', $order );

        			// Get pick-up date
        			$pickup_date = (int)ovabrw_get_meta_data( 'pickup_date', $order );
        			if ( !$pickup_date ) {
						$this->delete_order( $order_id );
						continue;
					}

        			// Get drop-off date
        			$dropoff_date = (int)ovabrw_get_meta_data( 'dropoff_date', $order );
        			if ( !$dropoff_date || $dropoff_date < current_time( 'timestamp' ) ) {
						$this->delete_order( $order_id );
						continue;
					}
        		}
			}

			// After order queues event
			do_action( OVABRW_PREFIX.'after_order_queues_event', $order_queues );
		}

		/**
		 * Update order status
		 */
		public function update_order_status( $order_id, $old_status, $new_status, $order ) {
			if ( !$this->table_exists() ) return;

			global $wpdb;

			// Get table name
			$table_name = esc_sql( $this->get_table_name() );

			// Get order queue ids
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
			$order_queue_ids = $wpdb->get_col(
			    $wpdb->prepare(
			        "SELECT id FROM {$table_name} WHERE order_id = %d",
			        $order_id
			    )
			);

			if ( ovabrw_array_exists( $order_queue_ids ) ) {
				$wpdb->update(
		            $table_name,
		            [ 'status' => 'wc-' . $new_status ],
		            [ 'order_id' => $order_id ],
		            [ '%s' ],
		            [ '%d' ]
		        );
			}
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}

		/**
		 * Update order queue status
		 */
		public function update_order_queue_status( $id, $new_status ) {
			if ( !$id || !$new_status ) return;

			// Check table exists
			if ( !$this->table_exists() ) return;

			global $wpdb;

			// Get table name
			$table_name = esc_sql( $this->get_table_name() );

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->update(
	            $table_name,
	            [ 'status' => $new_status ],
	            [ 'order_id' => $id ],
	            [ '%s' ],
	            [ '%d' ]
	        );
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}

		/**
		 * Delete order queue by order id
		 */
		public function delete_order_queue_by_order_id( $order_id ) {
			if ( !$order_id || !$this->table_exists() ) return;
			
			global $wpdb;

			// Get table name
			$table_name = esc_sql( $this->get_table_name() );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->delete(
			    $table_name,
			    [ 'order_id' => $order_id ],
			    [ '%d' ]
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}

		/**
		 * Trash order - HPOS
		 */
		public function trash_order_hpos( $order_id ) {
			if ( !$order_id || !OVABRW()->options->custom_orders_table_usage() ) return;

			// Update order queue status: trash
			$this->update_order_queue_status( $order_id, 'trash' );
		}

		/**
		 * Untrash order - HPOS
		 */
		public function untrash_order_hpos( $order_id, $previous_status ) {
			if ( !$order_id || !OVABRW()->options->custom_orders_table_usage() ) return;

			// Update order queue status: $previous_status
			$this->update_order_queue_status( $order_id, $previous_status );
		}

		/**
		 * Delete order - HPOS
		 */
		public function delete_order_hpos( $order_id ) {
			if ( !$order_id || !OVABRW()->options->custom_orders_table_usage() ) return;

			// Delete order queue
			$this->delete_order_queue_by_order_id( $order_id );
		}

		/**
		 * Tracked order (legacy)
		 */
		public function trashed_order_legacy( $post_id ) {
			if ( get_post_type( $post_id ) !== 'shop_order' || OVABRW()->options->custom_orders_table_usage() ) return;
			
			// Update order queue status: trash
			$this->update_order_queue_status( $post_id, 'trash' );
		}

		/**
		 * Untrashed order (legacy)
		 */
		public function untrashed_order_legacy( $post_id, $previous_status ) {
			if ( get_post_type( $post_id ) !== 'shop_order' || OVABRW()->options->custom_orders_table_usage() ) return;
			
			// Update order queue status: $previous_status
			$this->update_order_queue_status( $post_id, $previous_status );
		}

		/**
		 * Deleted order (legacy)
		 */
		public function deleted_order_legacy( $post_id ) {
			if ( get_post_type( $post_id ) !== 'shop_order' || OVABRW()->options->custom_orders_table_usage() ) return;

			// Delete order queue
			$this->delete_order_queue_by_order_id( $post_id );
		}

		/**
		 * instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}

	// init class
	new OVABRW_Order_Queues();
}