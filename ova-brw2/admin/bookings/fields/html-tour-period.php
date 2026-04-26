<?php defined( 'ABSPATH' ) || exit;

// Check is tour
if ( 'tour' != $this->get_type() ) return;
if ( !$this->is_period_time() ) return;

// Get period id
$period_id = ovabrw_unique_id( 'booking_period' );

?>

<div class="rental_item ovabrw-tour-period-field">
    <label for="<?php echo esc_attr( $period_id ); ?>">
        <?php esc_html_e( 'Select period', 'ova-brw' ); ?>
    </label>
    <select
        id="<?php echo esc_attr( $period_id ); ?>"
        clas="ovabrw-input-required"
        name="ovabrw_period[ovabrw-item-key]"
        data-no-time="<?php esc_attr_e( 'There are no time periods available', 'ova-brw' ); ?>">
        <option value="">
            <?php esc_html_e( 'Select ...', 'ova-brw' ); ?>
        </option>
    </select>
    <span class="ovabrw-loader-period">
        <i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
    </span>
</div>