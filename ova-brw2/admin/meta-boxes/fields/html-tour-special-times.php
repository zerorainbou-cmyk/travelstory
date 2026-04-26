<?php defined( 'ABSPATH' ) || exit;

// Show tour product options special times
if ( !apply_filters( OVABRW_PREFIX.'show_tour_product_options_special_times', true ) ) return;

// Before tour product options special times
do_action( OVABRW_PREFIX.'before_tour_product_options_special_times', $this );

// Special data
$special_from = $this->get_meta_value( 'special_from' );

// Get guest options
$guest_options = $this->get_guest_options();

// Width for guest price column
$guest_width = round( 24 / count( $guest_options ), wc_get_price_decimals() );

// Discount width for guest price column
$guest_dsc_width = round( 66 / count( $guest_options ), wc_get_price_decimals() );

// Number colspan
$colspan = 5 + count( $guest_options );

// Number discount colspan
$dsc_colspan = 4 + count( $guest_options );

?>
<br/>
<br/>
<div class="ovabrw-table">
	<span class="ovabrw-note">
		<?php esc_html_e( 'Seasonal Discounts (Price Priority - No. 1)', 'ova-brw' ); ?>
	</span>
	<table class="widefat">
		<thead>
			<th class="ovabrw-required">
				<?php esc_html_e( 'Start date', 'ova-brw' ); ?>
			</th>
			<th class="ovabrw-required">
				<?php esc_html_e( 'End date', 'ova-brw' ); ?>
			</th>
			<?php foreach ( $guest_options as $guest ):
				// Get discount guest prices
				$var_price = $guest['name'].'_price';

				// Initialize a variable with the name stored in $var_price
				${$var_price} = $this->get_meta_value('special_'.$guest['name'].'_price' );
			?>
				<th>
					<?php printf( esc_html__( '%s price (%s)', 'ova-brw' ), esc_html( $guest['label'] ), esc_html( get_woocommerce_currency_symbol() ) ); ?>
					<?php echo wc_help_tip( sprintf( esc_html__( 'Price per %s', 'ova-brw' ), $guest['label'] ) ); ?>
				</th>
			<?php endforeach; ?>
			<th>
				<?php esc_html_e( 'Base discounts for seasons', 'ova-brw' ); ?>
				<?php echo wc_help_tip( esc_html__( 'Discount based on total number of guests', 'ova-brw' ) ); ?>
			</th>
			<th></th>
			<th></th>
		</thead>
		<tbody class="ovabrw-special-time-item ovabrw-sortable-specials">
		<?php if ( ovabrw_array_exists( $special_from ) ):
			$special_to = $this->get_meta_value( 'special_to' );
			$discounts 	= $this->get_meta_value( 'special_discount' );

			foreach ( $special_from as $k => $from_date ):
				$to_date 	= ovabrw_get_meta_data( $k, $special_to );
				$discount 	= ovabrw_get_meta_data( $k, $discounts );
		?>
			<tr>
				<td width="13%">
					<?php ovabrw_wp_text_input([
						'type' 		=> 'text',
						'id' 		=> ovabrw_unique_id( 'special_from' ),
						'class' 	=> 'ovabrw-input-required start-date',
						'name' 		=> $this->get_meta_name( 'special_from[]' ),
						'value' 	=> $from_date,
						'data_type' => 'datepicker-no-year',
						'attrs' 	=> [
							'data-date' => $from_date ? gmdate( OVABRW()->options->get_date_format_no_year(), $from_date ) : ''
						]
					]); ?>
				</td>
				<td width="13%">
					<?php ovabrw_wp_text_input([
						'type' 		=> 'text',
						'id' 		=> ovabrw_unique_id( 'special_to' ),
						'class' 	=> 'ovabrw-input-required end-date',
						'name' 		=> $this->get_meta_name( 'special_to[]' ),
						'value' 	=> $to_date,
						'data_type' => 'datepicker-no-year',
						'attrs' 	=> [
							'data-date' => $to_date ? gmdate( OVABRW()->options->get_date_format_no_year(), $to_date ) : ''
						]
					]); ?>
				</td>
				<?php foreach ( $guest_options as $guest ):
					$var_price 		= $guest['name'].'_price';
					$guest_price 	= isset( ${$var_price} ) ? ovabrw_get_meta_data( $k, ${$var_price} ) : '';
				?>
					<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
						<?php ovabrw_wp_text_input([
							'type' 		=> 'text',
							'name' 		=> $this->get_meta_name( 'special_'.$guest['name'].'_price[]' ),
							'value' 	=> $guest_price,
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
							<?php foreach ( $guest_options as $guest ):
								// Get discount guest prices
								$var_dsc_price = 'dsc_'.$guest['name'].'_price';

								// Initialize a variable with the name stored in $var_price
								${$var_dsc_price} = ovabrw_get_meta_data( $guest['name'].'_price', $discount );
							?>
								<th>
									<?php printf( esc_html__( '%s price (%s)', 'ova-brw' ), esc_html( $guest['label'] ), esc_html( get_woocommerce_currency_symbol() ) ); ?>
									<?php echo wc_help_tip( sprintf( esc_html__( 'Price per %s', 'ova-brw' ), $guest['label'] ) ); ?>
								</th>
							<?php endforeach; ?>
							<th></th>
							<th></th>
						</thead>
						<tbody class="ovabrw-sortable">
						<?php if ( ovabrw_array_exists( $discount ) ):
							$dsc_from 	= ovabrw_get_meta_data( 'from', $discount );
							$dsc_to 	= ovabrw_get_meta_data( 'to', $discount );

							if ( ovabrw_array_exists( $dsc_from ) ):
								foreach ( $dsc_from as $i => $from ):
									$to = ovabrw_get_meta_data( $i, $dsc_to );
						?>
							<tr>
								<td width="16%">
									<?php ovabrw_wp_text_input([
										'type' 		=> 'number',
										'class' 	=> 'ovabrw-input-required ovabrw-special-discount-from',
										'name' 		=> $this->get_meta_name( 'special_discount['.$k.'][from][]' ),
										'value' 	=> $from,
										'attrs' 	=> [ 'min' => 0 ],
										'data_type' => 'number'
									]); ?>
								</td>
								<td width="16%">
									<?php ovabrw_wp_text_input([
										'type' 		=> 'number',
										'class' 	=> 'ovabrw-input-required ovabrw-special-discount-to',
										'name' 		=> $this->get_meta_name( 'special_discount['.$k.'][to][]' ),
										'value' 	=> $to,
										'attrs' 	=> [ 'min' => 0 ],
										'data_type' => 'number'
									]); ?>
								</td>
								<?php foreach ( $guest_options as $guest ):
									$var_dsc_price 		= 'dsc_'.$guest['name'].'_price';
									$dsc_guest_price 	= isset( ${$var_dsc_price} ) ? ovabrw_get_meta_data( $i, ${$var_dsc_price} ) : '';
								?>
									<td width="<?php echo esc_attr( $guest_dsc_width ).'%'; ?>" class="ovabrw-input-price">
										<?php ovabrw_wp_text_input([
											'type' 		=> 'text',
											'class' 	=> 'ovabrw-special-discount-price',
											'name' 		=> $this->get_meta_name( 'special_discount['.$k.']['.$guest['name'].'_price][]' ),
											'value' 	=> $dsc_guest_price,
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
						<?php endforeach; endif; endif; ?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="<?php echo esc_attr( $dsc_colspan ); ?>">
									<button class="button ovabrw-add-special-discount">
										<?php esc_html_e( 'Add discount', 'ova-brw' ); ?>
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
		<?php endforeach; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="<?php echo esc_attr( $colspan ); ?>">
					<button class="button ovabrw-tour-add-special-time">
						<?php esc_html_e( 'Add seasonal discount', 'ova-brw' ); ?>
					</button>
				</th>
			</tr>
		</tfoot>
	</table>
	<input
		type="hidden"
		name="ovabrw-special-times-row"
		data-row="
		<?php
			$template = OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-special-time-field.php';
			ob_start();
			include( $template );
			echo esc_attr( ob_get_clean() );
		?>"
	/>
	<input
		type="hidden"
		name="ovabrw-special-discount-row"
		data-row="
		<?php
			$template = OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-special-time-discount.php';
			ob_start();
			include( $template );
			echo esc_attr( ob_get_clean() );
		?>"
	/>
</div>
<?php do_action( OVABRW_PREFIX . 'tour_product_options_special_times', $this ); ?>