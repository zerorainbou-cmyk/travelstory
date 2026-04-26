<?php defined( 'ABSPATH' ) || exit; ?>
<tr>
	<td width="19%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'number',
			'class' 	=> 'ovabrw-input-required',
			'name' 		=> $this->get_meta_name( 'discount_from[]' ),
			'attrs' 	=> [
				'data-min' => 0
			],
			'data_type' => 'number'
		]); ?>
	</td>
	<td width="19%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'number',
			'class' 	=> 'ovabrw-input-required',
			'name' 		=> $this->get_meta_name( 'discount_to[]' ),
			'attrs' 	=> [
				'data-min' => 0
			],
			'data_type' => 'number'
		]); ?>
	</td>
	<?php foreach ( $guest_options as $guest ): ?>
		<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'name' 		=> $this->get_meta_name( 'discount_'.$guest['name'].'_price[]' ),
				'data_type' => 'price'
			]); ?>
		</td>
	<?php endforeach; ?>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-discount" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>