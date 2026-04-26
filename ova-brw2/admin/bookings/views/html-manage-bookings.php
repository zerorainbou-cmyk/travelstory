<?php if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'OVABRW_Admin_Booking_List' ) ) return;

// Datepicker option
$datetimepicker = ovabrw_admin_datetimepicker_options();
$datetimepicker['datepicker']['AmpPlugin']['dropdown']['minYear'] = gmdate("Y") - 75;
$datetimepicker['datepicker']['AmpPlugin']['dropdown']['maxYear'] = gmdate("Y") + 75;
$datetimepicker['datepicker']['LockPlugin']['minDate'] = null;
$datetimepicker['datepicker']['LockPlugin']['maxDate'] = null;

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

// Get gg api key
$api_key = ovabrw_get_setting( 'google_key_map' );

// Get client id
$client_id = ovabrw_get_setting( 'gcal_client_id' );

// Get booking manage
$booking_manage = new OVABRW_Admin_Booking_List();
$booking_manage->prepare_items();

// Get rental product ids
$product_ids = OVABRW()->options->get_rental_product_ids();

// Get locations
$all_locations = OVABRW()->options->get_location_ids();

// Get vehicle ids
$vehicle_ids = OVABRW()->options->get_vehicle_ids();

// Order ID
$order_id = sanitize_text_field( ovabrw_get_meta_data( 'order_id', $_GET ) );

// Custom name
$customer_name = sanitize_text_field( ovabrw_get_meta_data( 'customer_name', $_GET ) );

// Product ID
$product_id = sanitize_text_field( ovabrw_get_meta_data( 'product_id', $_GET ) );

// Vehicle ID
$vehicle_id = sanitize_text_field( ovabrw_get_meta_data( 'vehicle_id', $_GET ) );

// Search by
$search_by = sanitize_text_field( ovabrw_get_meta_data( 'search_by', $_GET ) );

// From date
$from_date = sanitize_text_field( ovabrw_get_meta_data( 'from_date', $_GET ) );

// To date
$to_date = sanitize_text_field( ovabrw_get_meta_data( 'to_date', $_GET ) );

// Pick-up location
$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $_GET ) );

// Drop-off location
$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $_GET ) );

// Order status
$order_status = sanitize_text_field( ovabrw_get_meta_data( 'order_status', $_GET ) );

?>
<div class="wrap">
    <form id="booking-filter" method="GET" action="<?php echo esc_url( get_admin_url( null, 'admin.php?page=ovabrw-manage-bookings' ) ); ?>">
        <h2 class="wp-heading-inline">
            <?php esc_html_e( 'Manage Bookings', 'ova-brw' ); ?>
            <button type="button" class="page-title-action ovabrw-sync-cal">
                <?php esc_html_e( 'Add to Calendar', 'ova-brw' ); ?>
            </button>
        </h2>
        <div class="booking_filter">
            <?php if ( get_option( 'admin_manage_order_show_id', 1 ) ): // Show order id ?>
                <div class="form-field">
                    <?php ovabrw_wp_text_input([
                        'name'          => 'order_id',
                        'value'         => $order_id,
                        'placeholder'   => esc_html__( 'Order ID', 'ova-brw' )
                    ]); ?>
                </div>
            <?php endif;

            // Show customer name
            if ( get_option( 'admin_manage_order_show_customer', 2 ) ): ?>
                <div class="form-field">
                    <?php ovabrw_wp_text_input([
                        'name'          => 'customer_name',
                        'value'         => $customer_name,
                        'placeholder'   => esc_html__( 'Customer name', 'ova-brw' )
                    ]); ?>
                </div>
            <?php endif;

            // Show filter by dates
            if ( get_option( 'admin_manage_order_show_time', 3 ) ): ?>
                <div class="form-field">
                    <select name="search_by">
                        <option value="">
                            <?php esc_html_e( '-- Search by --', 'ova-brw' ); ?>
                        </option>
                        <option value="from_date"<?php selected( $search_by, 'from_date' ); ?>>
                            <?php esc_html_e( 'From date', 'ova-brw' ); ?>
                        </option>
                        <option value="to_date"<?php selected( $search_by, 'to_date' ); ?>>
                            <?php esc_html_e( 'To date', 'ova-brw' ); ?>
                        </option>
                    </select>
                </div>
                <div class="form-field">
                    <?php ovabrw_wp_text_input([
                        'id'            => ovabrw_unique_id( 'from_date' ),
                        'class'         => 'start-date',
                        'name'          => 'from_date',
                        'value'         => $from_date,
                        'placeholder'   => esc_html__( 'From date', 'ova-brw' ),
                        'data_type'     => 'datetimepicker',
                        'attrs'         => [
                            'data-date' => strtotime( $from_date ) ? gmdate( $date_format, strtotime( $from_date ) ) : '',
                            'data-time' => strtotime( $from_date ) ? gmdate( $time_format, strtotime( $from_date ) ) : ''
                        ]
                    ]); ?>
                </div>
                <div class="form-field">
                    <?php ovabrw_wp_text_input([
                        'id'            => ovabrw_unique_id( 'to_date' ),
                        'class'         => 'end-date',
                        'name'          => 'to_date',
                        'value'         => $to_date,
                        'placeholder'   => esc_html__( 'To date', 'ova-brw' ),
                        'data_type'     => 'datetimepicker',
                        'attrs'         => [
                            'data-date' => strtotime( $to_date ) ? gmdate( $date_format, strtotime( $to_date ) ) : '',
                            'data-time' => strtotime( $to_date ) ? gmdate( $time_format, strtotime( $to_date ) ) : ''
                        ]
                    ]); ?>
                </div>
            <?php endif;

            // Show vehicle
            if ( get_option( 'admin_manage_order_show_vehicle', 7 ) ): ?>
                <div class="form-field">
                    <select name="vehicle_id">
                        <option value="">
                            <?php esc_html_e( '-- Vehicle --', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $vehicle_ids ) ):
                            foreach ( $vehicle_ids as $post_id ):
                                $v_id = ovabrw_get_post_meta( $post_id, 'id_vehicle' );
                            ?>
                                <option value="<?php echo esc_attr( $v_id ); ?>"<?php ovabrw_selected( $v_id, $vehicle_id ); ?>>
                                    <?php echo get_the_title( $post_id ); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            <?php endif;

            // Show locations
            if ( get_option( 'admin_manage_order_show_location', 4 ) ): ?>
                <div class="form-field">
                    <select name="pickup_location">
                        <option value="">
                            <?php esc_html_e( '-- Pick-up location --', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $all_locations ) ):
                            foreach ( $all_locations as $location_id ): ?>
                                <option value="<?php echo esc_attr( get_the_title( $location_id ) ); ?>"<?php ovabrw_selected( get_the_title( $location_id ), $pickup_location ); ?>>
                                    <?php echo esc_html( get_the_title( $location_id ) ); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
                <div class="form-field">
                    <select name="dropoff_location">
                        <option value="">
                            <?php esc_html_e( '-- Drop-off location --', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $all_locations ) ):
                            foreach ( $all_locations as $location_id ): ?>
                                <option value="<?php echo esc_attr( get_the_title( $location_id ) ); ?>"<?php ovabrw_selected( get_the_title( $location_id ), $dropoff_location ); ?>>
                                    <?php echo esc_html( get_the_title( $location_id ) ); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            <?php endif;

            // Show product
            if ( get_option( 'admin_manage_order_show_product', 8 ) ): ?>
                <div class="form-field">
                    <select name="product_id">
                        <option value="">
                            <?php esc_html_e( '-- Choose product --', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $product_ids ) ):
                            foreach ( $product_ids as $pid ): ?>
                                <option value="<?php echo esc_attr( $pid ); ?>"<?php ovabrw_selected( $pid, $product_id ); ?>>
                                    <?php echo get_the_title( $pid ); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            <?php endif;

            // Show order status
            if ( get_option( 'admin_manage_order_show_order_status', 9 ) ): ?>
                <div class="form-field">
                    <select name="order_status">
                        <option value="">
                            <?php esc_html_e( '-- Order status --', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-completed"<?php ovabrw_selected( $order_status, 'wc-completed' ); ?>>
                            <?php esc_html_e( 'Completed', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-processing"<?php ovabrw_selected( $order_status, 'wc-processing' ); ?>>
                            <?php esc_html_e( 'Processing', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-pending"<?php ovabrw_selected( $order_status, 'wc-pending' ); ?>>
                            <?php esc_html_e( 'Pending payment', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-on-hold"<?php ovabrw_selected( $order_status, 'wc-on-hold' ); ?>>
                            <?php esc_html_e( 'On hold', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-cancelled"<?php ovabrw_selected( $order_status, 'wc-cancelled' ); ?>>
                            <?php esc_html_e( 'Cancel', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-closed"<?php ovabrw_selected( $order_status, 'wc-closed' ); ?>>
                            <?php esc_html_e( 'Closed', 'ova-brw' ); ?>
                        </option>
                    </select>
                </div>
            <?php endif; ?>
            <div class="form-field">
                <button type="submit" class="button">
                    <?php esc_html_e( 'Filter', 'ova-brw' ); ?>
                </button>
            </div>
        </div>
        <?php ovabrw_wp_text_input([
        	'type' 	=> 'hidden',
        	'name' 	=> 'page',
        	'value' => 'ovabrw-manage-bookings'
        ]); ?>
        <?php $booking_manage->display(); ?>
    </form>
    <div class="ovabrw-sync-cal-wrap">
        <div class="ovabrw-sync-cal-container">
            <span class="sync-cal-close dashicons dashicons-no-alt"></span>
            <h3 class="sync-label"><?php esc_html_e( 'Select pickup date range for bookings', 'ova-brw' ); ?></h3>
            <div class="ovabrw-sync-cal-filter">
                <form action="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="form-field">
                        <?php ovabrw_wp_text_input([
                            'id'            => ovabrw_unique_id( 'from_date' ),
                            'class'         => 'start-date ovabrw-input-required',
                            'name'          => 'from_date',
                            'placeholder'   => esc_html__( 'From date', 'ova-brw' ),
                            'data_type'     => 'datetimepicker'
                        ]); ?>
                    </div>
                    <div class="form-field">
                        <?php ovabrw_wp_text_input([
                            'id'            => ovabrw_unique_id( 'to_date' ),
                            'class'         => 'end-date ovabrw-input-required',
                            'name'          => 'to_date',
                            'value'         => $to_date,
                            'placeholder'   => esc_html__( 'To date', 'ova-brw' ),
                            'data_type'     => 'datetimepicker'
                        ]); ?>
                    </div>
                </form>
            </div>
            <div class="ovabrw-sync-cal-progress">
                <div class="progress-bg">
                    <div class="progress-fill"></div>
                </div>
                <div class="progress-results">
                    <span class="progress-percent">0%</span>
                    <span class="progress-stats">
                        <span class="synced">0</span>
                        <span>/</span>
                        <span class="total-events">0</span>
                        <span><?php esc_html_e( ' orders', 'ova-brw' ); ?></span>
                    </span>
                </div>
                <div class="sync-log">
                    <p><?php esc_html_e( 'Synchronizing...', 'ova-brw' ) ?></p>
                </div>
            </div>
            <div class="ovabrw-sync-cal-log"></div>
            <div class="ovabrw-sync-cal-btn">
                <?php if ( $api_key && $client_id ): ?>
                    <button type="button" class="button ovabrw-sync-gcal">
                        <?php esc_html_e( 'Sync Google Calendar', 'ova-brw' ); ?>
                    </button>
                <?php endif; ?>
                <button type="button" class="button ovabrw-download-ical">
                    <?php esc_html_e( 'Download Ical(.ics)', 'ova-brw' ); ?>
                    <span class="dashicons dashicons-update-alt"></span>
                </button>
            </div>
        </div>
    </div>
    <div class="ovabrw-backbone-modal ovabrw-order-preview">
        <div class="ovabrw-backbone-modal-content">
            <section class="ovabrw-backbone-modal-main" role="main">
                <header class="ovabrw-backbone-modal-header">
                    <mark class="order-status"><span></span></mark>
                    <h1 class="order-number">
                        <?php esc_html_e( 'Order #', 'ova-brw' ); ?>
                        <span></span>
                    </h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text">
                            <?php esc_html_e( 'Close modal panel', 'ova-brw' ); ?>
                        </span>
                    </button>
                </header>
                <article>
                    <?php do_action( OVABRW_PREFIX.'admin_order_preview_start' ); ?>
                    <div class="ovabrw-order-preview-addresses">
                        <div class="ovabrw-order-preview-address">
                            <h2>
                                <?php esc_html_e( 'Billing details', 'ova-brw' ); ?>
                            </h2>
                            <div class="ovabrw-formatted-billing-address"></div>
                            <div class="ovabrw-billing-email">
                                <strong>
                                    <?php esc_html_e( 'Email', 'ova-brw' ); ?>
                                </strong>
                                <a href="#"></a>
                            </div>
                            <div class="ovabrw-billing-phone">
                                <strong>
                                    <?php esc_html_e( 'Phone', 'ova-brw' ); ?>
                                </strong>
                                <a href="#"></a>
                            </div>
                            <div class="ovabrw-payment-via">
                                <strong>
                                    <?php esc_html_e( 'Payment via', 'ova-brw' ); ?>
                                </strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="ovabrw-needs-shipping">
                            <div class="ovabrw-order-preview-address">
                                <h2>
                                    <?php esc_html_e( 'Shipping details', 'ova-brw' ); ?>
                                </h2>
                                <div class="ovabrw-formatted-shipping-address"></div>
                                <div class="ovabrw-ship-to-billing">
                                    <a href="#"></a>
                                </div>
                                <div class="ovabrw-shipping-phone">
                                    <strong>
                                        <?php esc_html_e( 'Phone', 'ova-brw' ); ?>
                                    </strong>
                                    <a href="#"></a>
                                </div>
                                <div class="ovabrw-shipping-via">
                                    <strong>
                                        <?php esc_html_e( 'Shipping method', 'ova-brw' ); ?>
                                    </strong>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                        <div class="ovabrw-order-preview-note">
                            <strong>
                                <?php esc_html_e( 'Note', 'ova-brw' ); ?>
                            </strong>
                            <span></span>
                        </div>
                    </div>
                    <div class="ovabrw-item-html"></div>
                    <div class="ovabrw-order-totals"></div>
                    <?php do_action( OVABRW_PREFIX.'admin_order_preview_end' ); ?>
                </article>
                <footer>
                    <div class="inner">
                        <div class="ovabrw-footer-actions"></div>
                        <a class="button button-primary button-large ovabrw-edit-order" aria-label="<?php esc_attr_e( 'Edit this order', 'ova-brw' ); ?>" href="#" data-edit-order-url="<?php echo esc_url( admin_url( 'admin.php?page=wc-orders&action=edit' ) ) . '&id=[order_id]'; ?>">
                            <?php esc_html_e( 'Edit', 'ova-brw' ); ?>
                        </a>
                    </div>
                </footer>
            </section>
        </div>
    </div>
    <div class="ovabrw-backbone-modal-backdrop"></div>
    <input
        type="hidden"
        name="ovabrw-datetimepicker-options"
        value="<?php echo esc_attr( wp_json_encode( $datetimepicker ) ); ?>"
    />
</div>