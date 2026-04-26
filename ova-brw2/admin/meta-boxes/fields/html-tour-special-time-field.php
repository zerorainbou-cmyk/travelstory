<?php defined( 'ABSPATH' ) || exit; ?>
<tr>
	<td width="13%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'frominUniqueID',
			'class' 	=> 'ovabrw-input-required start-date',
			'name' 		=> $this->get_meta_name( 'special_from[]' ),
			'data_type' => 'datepicker-no-year'
		]); ?>
	</td>
	<td width="13%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'toUniqueID',
			'class' 	=> 'ovabrw-input-required end-date',
			'name' 		=> $this->get_meta_name( 'special_to[]' ),
			'data_type' => 'datepicker-no-year'
		]); ?>
	</td>
	<?php foreach ( $guest_options as $guest ): ?>
		<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'name' 		=> $this->get_meta_name( 'special_'.$guest['name'].'_price[]' ),
				'data_type' => 'price'
			]); ?>
		</td>
	<?php endforeach; ?>
	<td width="48%" class="ovabrw-special-discounts">
		<table class="widefat">
			<thead>
				<th class="ovabrw-required">
					<?php esc_html_e( 'From (No. of guests)', 'ova-brw' ); ?>
				</th>
				<th class="ovabrw-required">
					<?php esc_html_e( 'To (No. of guests)', 'ova-brw' ); ?>
				</th>
				<?php foreach ( $guest_options as $guest ): ?>
					<th>
						<?php printf( esc_html__( '%s price (%s)', 'ova-brw' ), esc_html( $guest['label'] ), esc_html( get_woocommerce_currency_symbol() ) ); ?>
						<?php echo wc_help_tip( sprintf( esc_html__( 'Price per %s', 'ova-brw' ), $guest['label'] ) ); ?>
					</th>
				<?php endforeach; ?>
				<th></th>
				<th></th>
			</thead>
			<tbody class="ovabrw-sortable"></tbody>
			<tfoot>
				<tr>
					<th colspan="<?php echo esc_attr( $dsc_colspan ); ?>">
						<button class="button ovabrw-add-special-discount">
							<?php esc_html_e( 'Add new discount', 'ova-brw' ); ?>
						</button>
					</th>
				</tr>
			</tfoot>
		</table>
	</td>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-tour-remove-special-time" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
	</td>
</tr>