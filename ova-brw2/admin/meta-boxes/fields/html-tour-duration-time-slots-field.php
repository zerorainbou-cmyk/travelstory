<?php defined( 'ABSPATH' ) || exit; ?>
<tr>
	<td width="18%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'timeslot-label',
			'name' 			=> $this->get_meta_name( 'tour_timeslots_label[dayOfWeek][]' ),
			'placeholder' 	=> esc_html__( '...', 'ova-brw' )
		]); ?>
	</td>
	<td width="11%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'class' 	=> 'ovabrw-input-required start-time',
			'name' 		=> $this->get_meta_name( 'tour_timeslots_start[dayOfWeek][]' ),
			'data_type' => 'timestamp'
		]); ?>
	</td>
	<td width="11%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'class' 	=> 'ovabrw-input-required end-time',
			'name' 		=> $this->get_meta_name( 'tour_timeslots_end[dayOfWeek][]' ),
			'data_type' => 'timestamp'
		]); ?>
	</td>
	<?php foreach ( $guest_options as $guest ): ?>
		<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'class' 	=> 'ovabrw-input-required ovabrw-timeslots-guest-price',
				'name' 		=> $this->get_meta_name( 'tour_timeslots_'.$guest['name'].'_price[dayOfWeek][]' ),
				'data_type' => 'price'
			]); ?>
		</td>
	<?php endforeach; ?>
	<td width="10%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'number',
			'class' 		=> 'ovabrw-input-required ovabrw-timeslots-max-guests',
			'name' 			=> $this->get_meta_name( 'tour_timeslots_max_guests[dayOfWeek][]' ),
			'value' 		=> 1,
			'placeholder' 	=> 1,
			'attrs' 		=> [
				'data-min' => 0
			],
			'data_type' 	=> 'number'
		]); ?>
	</td>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-time-slot">X</button>
	</td>
</tr>