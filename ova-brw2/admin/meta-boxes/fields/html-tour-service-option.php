<?php defined( 'ABSPATH' ) || exit; ?>
<tr>
	<td width="14%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-service-option-id',
			'name' 			=> $this->get_meta_name( 'extra_service_option_id[index][]' ),
			'value' 		=> '[option-id]',
			'placeholder' 	=> esc_html__( 'unique ID', 'ova-brw' ),
			'attrs' 		=> [ 'autocomplete' => 'off' ]
		]); ?>
	</td>
	<td width="18%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-service-option-name',
			'name' 			=> $this->get_meta_name( 'extra_service_option_name[index][]' ),
			'placeholder' 	=> esc_html__( 'name', 'ova-brw' ),
			'attrs' 		=> [ 'autocomplete' => 'off' ]
		]); ?>
	</td>
	<?php foreach ( $guest_options as $guest ): ?>
		<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'class' 	=> 'ovabrw-service-option-price',
				'name' 		=> $this->get_meta_name( 'extra_service_option_'.$guest['name'].'_price[index][]' ),
				'data_type' => 'price',
				'attrs' 	=> [
					'data-name' => $this->get_meta_name( 'extra_service_option_'.$guest['name'].'_price[index][]' )
				]
			]); ?>
		</td>
	<?php endforeach; ?>
	<td width="10%">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'number',
			'class' 		=> 'ovabrw-service-option-guest',
			'name' 			=> $this->get_meta_name( 'extra_service_option_guest[index][]' ),
			'placeholder' 	=> 1
		]); ?>
	</td>
	<td width="14%">
		<?php ovabrw_wp_select_input([
			'class' 	=> 'ovabrw-service-option-type',
			'name' 		=> $this->get_meta_name( 'extra_service_option_type[index][]' ),
			'options' 	=> [
				'person' 	=> esc_html__( '/guest', 'ova-brw' ),
				'order' 	=> esc_html__( '/order', 'ova-brw' )
			]
		]); ?>
	</td>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-extra-service-option" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>