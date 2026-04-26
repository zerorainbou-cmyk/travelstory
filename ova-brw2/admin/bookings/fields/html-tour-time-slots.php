<?php defined( 'ABSPATH' ) || exit;

// Check is tour
if ( 'tour' != $this->get_type() ) return;
if ( !$this->is_timeslots() ) return;

?>

<div class="rental_item ovabrw-tour-timeslots-field">
    <label for="ovabrw-pickup-date">
        <?php esc_html_e( 'Select time', 'ova-brw' ); ?>
    </label>
    <div class="ovabrw-tour-timeslots ovabrw-input-required"></div>
</div>