<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
	<td width="32%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name('special_price[]'),
			'data_type' 	=> 'price',
			'placeholder' 	=> 10.00
		]); ?>
	</td>
	<td width="33%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'specialStartDateUniqueID',
			'class' 	=> 'ovabrw-input-required start-date',
			'name' 		=> $this->get_meta_name( 'special_startdate[]' ),
			'data_type' => 'datetimepicker'
		]); ?>
    </td>
    <td width="33%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'specialEndDateUniqueID',
			'class' 	=> 'ovabrw-input-required end-date',
			'name' 		=> $this->get_meta_name( 'special_enddate[]' ),
			'data_type' => 'datetimepicker'
		]); ?>
    </td>
    <td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-appointment-remove-special-time" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>