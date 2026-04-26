<?php defined( 'ABSPATH' ) || exit; ?>
<tr>
	<td width="16%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'period-label',
			'name' 			=> $this->get_meta_name( 'period_label[]' ),
			'placeholder' 	=> esc_html__( '...', 'ova-brw' )
		]); ?>
	</td>
	<td width="15%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'startUniqueID',
			'class' 	=> 'ovabrw-input-required start-date',
			'name' 		=> $this->get_meta_name( 'period_start[]' ),
			'data_type' => 'datepicker'
		]); ?>
	</td>
	<td width="15%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'endUniqueID',
			'class' 	=> 'ovabrw-input-required end-date',
			'name' 		=> $this->get_meta_name( 'period_end[]' ),
			'data_type' => 'datepicker'
		]); ?>
	</td>
	<?php foreach ( $guest_options as $guest ): ?>
		<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'class' 	=> 'ovabrw-input-required ovabrw-period-guest-price',
				'name' 		=> $this->get_meta_name( 'period_'.$guest['name'].'_price[]' ),
				'data_type' => 'price'
			]); ?>
		</td>
	<?php endforeach; ?>
	<td width="10%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'number',
			'class' 		=> 'ovabrw-input-required ovabrw-period-max-guests',
			'name' 			=> $this->get_meta_name( 'period_max_guests[]' ),
			'value' 		=> 1,
			'placeholder' 	=> 1,
			'attrs' 		=> [
				'data-min' => 0
			],
			'data_type' 	=> 'number'
		]); ?>
	</td>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-period" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>