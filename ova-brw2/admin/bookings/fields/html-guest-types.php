<?php if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Variables used in this file.
 * @var object 	$product
 * @var int     $product_id
 * @var array   $guests 
 */

// Guests
if ( ovabrw_array_exists( $guests  ) ):
	// Open guest info form on page load
	$default_open = apply_filters( OVABRW_PREFIX.'open_guest_info_form_on_page_load', true );

	foreach ( $guests as $guest_name => $item ):
		// Label
		$label = ovabrw_get_meta_data( 'label', $item );

		// Number of guests
		$numberof_guests = (int)ovabrw_get_meta_data( 'number', $item );

		if ( !$numberof_guests ): ?>
			<div class="guest-info-item" data-guest-name="<?php echo esc_attr( $guest_name ); ?>" style="display: none;">
		<?php else: ?>
			<div class="guest-info-item <?php echo $default_open ? 'default-open' : ''; ?>" data-guest-name="<?php echo esc_attr( $guest_name ); ?>">
		<?php if ( $default_open ) $default_open = false;
		endif; ?>
			<div class="guest-info-header">
				<h3 class="ovabrw-label">
					<?php echo esc_html( $label ); ?>
				</h3>
				<i class="brwicon2-down-arrow" aria-hidden="true"></i>
			</div>
			<div class="guest-info-body">
				<div class="guest-info-total">
					<span class="text">
						<?php esc_html_e( 'Total guests:', 'ova-brw' ); ?>
					</span>
					<span class="number">
						<?php echo esc_html( $numberof_guests ); ?>
					</span>
				</div>
				<div class="guest-info-content">
					<div class="guest-info-accordion">
						<?php for ( $key = 0; $key < $numberof_guests; $key++ ) {
							include ( OVABRW_PLUGIN_ADMIN . 'bookings/fields/html-guest-info.php' );
						} ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach;
endif; ?>