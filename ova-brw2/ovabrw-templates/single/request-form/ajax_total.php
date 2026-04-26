<?php if ( !defined( 'ABSPATH' ) ) exit();

if ( 'yes' === ovabrw_get_setting( 'request_booking_show_total', 'no' ) ): ?>
<div class="ajax_show_total">
	<div class="ajax_loading"></div>
	<div class="show_ajax_content">
		<?php if ( 'yes' === ovabrw_get_setting( 'request_booking_show_price_details', 'no' ) ): ?>
			<div class="ovabrw-price-details">
				<div class="ovabrw-location-prices"></div>
				<div class="ovabrw-pickup-location-surcharge"></div>
				<div class="ovabrw-dropoff-location-surcharge"></div>
				<div class="ovabrw-extra-time-prices"></div>
				<div class="ovabrw-guest-prices"></div>
				<div class="ovabrw-cckf-prices"></div>
				<div class="ovabrw-resource-prices"></div>
				<div class="ovabrw-service-prices"></div>
				<div class="ovabrw-subtotal"></div>
			</div>
		<?php endif; ?>
		
		<span class="ovabrw-total-amount"></span>
		<?php if ( 'yes' === ovabrw_get_setting( 'request_booking_show_insurance_amount', 'no' ) ): ?>
			<div class="ovabrw-insurance-amount">
				<span class="show-amount-insurance"></span>
			</div>
		<?php endif; ?>
		<?php if ( 'yes' === ovabrw_get_setting( 'request_booking_show_availables_vehicle', 'yes' ) ): ?>
			<span class="ovabrw-items-available">
				<?php esc_html_e( 'Items available:', 'ova-brw' ); ?>
				<span class="number-available"></span>
			</span>
		<?php endif; ?>
	</div>
	<div class="ajax-show-error"></div>
</div>
<?php endif; ?>
