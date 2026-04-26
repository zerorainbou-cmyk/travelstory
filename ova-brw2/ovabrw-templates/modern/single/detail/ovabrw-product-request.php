<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Loading reCAPTCHA
OVABRW()->options->loading_recaptcha();

?>

<form
	class="form ovabrw-form"
	id="request_booking"
	action="<?php echo home_url('/'); ?>"
	method="post"
	enctype="multipart/form-data"
	data-run_ajax="<?php esc_attr_e( apply_filters( OVABRW_PREFIX.'request_booking_form_run_ajax', 'true' ) ); ?>"
	autocomplete="off">
	<div class="ovabrw-product-fields">
		<?php ovabrw_get_template('modern/single/detail/request-form/request-fields.php'); ?>
		<?php ovabrw_get_template('modern/single/detail/request-form/request-custom-checkout-fields.php'); ?>
	</div>
	<?php ovabrw_get_template('modern/single/detail/booking-form/booking-extra-services.php', [ 'form' => 'request' ]); ?>
	<?php ovabrw_get_template('modern/single/detail/request-form/request-resources.php'); ?>
	<?php ovabrw_get_template('modern/single/detail/request-form/request-services.php'); ?>
	<?php if ( 'yes' === ovabrw_get_setting( 'request_booking_form_show_extra_info', 'yes' ) ): ?>
		<div class="ovabrw-request-extra">
			<div class="ovabrw-label">
				<?php esc_html_e( 'Extra Information', 'ova-brw' ); ?>
			</div>
			<textarea name="extra"></textarea>
		</div>
	<?php endif; ?>
	<?php ovabrw_get_template('modern/single/detail/request-form/request-show-total.php'); ?>
	<?php ovabrw_get_template('modern/single/detail/request-form/request-submit.php'); ?>
</form>