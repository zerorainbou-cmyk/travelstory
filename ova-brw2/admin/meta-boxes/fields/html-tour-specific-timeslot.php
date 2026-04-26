<?php defined( 'ABSPATH' ) || exit; ?>
<tr>
	<td width="18%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'timeslot-label',
			'name' 			=> $this->get_meta_name( 'specific_label[index][dayOfWeek][]' ),
			'placeholder' 	=> esc_html__( '...', 'ova-brw' ),
			'attrs' 		=> [
				'data-name' => $this->get_meta_name( 'specific_label[index][dayOfWeek][]' )
			]
		]); ?>
	</td>
	<td width="11%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'class' 	=> 'ovabrw-input-required start-time',
			'name' 		=> $this->get_meta_name( 'specific_start[index][dayOfWeek][]' ),
			'data_type' => 'timestamp',
			'attrs' 	=> [
				'data-name' => $this->get_meta_name( 'specific_start[index][dayOfWeek][]' )
			]
		]); ?>
	</td>
	<td width="11%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'class' 	=> 'ovabrw-input-required end-time',
			'name' 		=> $this->get_meta_name( 'specific_end[index][dayOfWeek][]' ),
			'data_type' => 'timestamp',
			'attrs' 	=> [
				'data-name' => $this->get_meta_name( 'specific_end[index][dayOfWeek][]' )
			]
		]); ?>
	</td>
	<?php foreach ( $guest_options as $guest ): ?>
		<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'class' 	=> 'ovabrw-input-required ovabrw-specific-option-price',
				'name' 		=> $this->get_meta_name( 'specific_'.$guest['name'].'_price[index][dayOfWeek][]' ),
				'data_type' => 'price',
				'attrs' 	=> [
					'data-name' => $this->get_meta_name( 'specific_'.$guest['name'].'_price[index][dayOfWeek][]' )
				]
			]); ?>
		</td>
	<?php endforeach; ?>
	<td width="10%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'number',
			'class' 		=> 'ovabrw-input-required ovabrw-specific-max-guests',
			'name' 			=> $this->get_meta_name( 'specific_max_guests[index][dayOfWeek][]' ),
			'value' 		=> 1,
			'placeholder' 	=> 1,
			'attrs' 		=> [ 'min' => 0 ],
			'data_type' 	=> 'number',
			'attrs' 		=> [
				'data-name' => $this->get_meta_name( 'specific_max_guests[index][dayOfWeek][]' )
			]
		]); ?>
	</td>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-specific-timeslot" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>