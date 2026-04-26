<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get locations
$locations = OVABRW()->options->get_locations();

?>
<tr>
	<td width="49%">
		<?php ovabrw_wp_select_input([
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'dropoff_location_surcharge[]' ),
			'placeholder' 	=> esc_html__( 'Select location', 'ova-brw' ),
			'options' 		=> $locations
		]); ?>
    </td>
    <td width="49%" class="ovabrw-input-price">
    	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-price',
			'name' 			=> $this->get_meta_name( 'dropoff_surcharge_price[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '10'
		]); ?>
    </td>
    <td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-location-surcharge" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>