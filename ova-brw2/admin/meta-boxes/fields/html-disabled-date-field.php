<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
	<?php if ( $this->is_type( 'hotel' ) || $this->is_type( 'tour' ) ): ?>
		<td width="49%">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'id' 		=> 'disabledFromUniqueID',
				'class' 	=> 'ovabrw-input-required start-date',
				'name' 		=> $this->get_meta_name( 'untime_startdate[]' ),
				'data_type' => 'datepicker'
			]); ?>
	    </td>
	    <td width="49%">
	    	<?php ovabrw_wp_text_input([
	    		'type' 		=> 'text',
				'id' 		=> 'disabledToUniqueID',
				'class' 	=> 'ovabrw-input-required end-date',
				'name' 		=> $this->get_meta_name( 'untime_enddate[]' ),
				'data_type' => 'datepicker'
	    	]); ?>
	    </td>
	<?php else: ?>
		<td width="49%">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'id' 		=> 'disabledFromUniqueID',
				'class' 	=> 'ovabrw-input-required start-date',
				'name' 		=> $this->get_meta_name( 'untime_startdate[]' ),
				'data_type' => 'datetimepicker'
			]); ?>
	    </td>
	    <td width="49.5%">
	    	<?php ovabrw_wp_text_input([
	    		'type' 		=> 'text',
				'id' 		=> 'disabledToUniqueID',
				'class' 	=> 'ovabrw-input-required end-date',
				'name' 		=> $this->get_meta_name( 'untime_enddate[]' ),
				'data_type' => 'datetimepicker'
	    	]); ?>
	    </td>
	<?php endif; ?>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-disabled-date" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>