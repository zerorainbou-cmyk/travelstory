<?php defined( 'ABSPATH' ) || exit; ?>
<tr>
	<td width="16%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'number',
			'class' 	=> 'ovabrw-input-required ovabrw-special-discount-from',
			'name' 		=> $this->get_meta_name( 'special_discount[index][from][]' ),
			'attrs' 	=> [ 'min' => 0 ],
			'data_type' => 'number'
		]); ?>
	</td>
	<td width="16%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'number',
			'class' 	=> 'ovabrw-input-required ovabrw-special-discount-to',
			'name' 		=> $this->get_meta_name( 'special_discount[index][to][]' ),
			'attrs' 	=> [ 'min' => 0 ],
			'data_type' => 'number'
		]); ?>
	</td>
	<?php foreach ( $guest_options as $guest ): ?>
		<td width="<?php echo esc_attr( $guest_dsc_width ).'%'; ?>" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'class' 	=> 'ovabrw-special-discount-price',
				'name' 		=> $this->get_meta_name( 'special_discount[index]['.$guest['name'].'_price][]' ),
				'data_type' => 'price',
				'attrs' 	=> [
					'data-name' => $this->get_meta_name( 'special_discount[index]['.$guest['name'].'_price][]' )
				]
			]); ?>
		</td>
	<?php endforeach; ?>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-special-discount" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>