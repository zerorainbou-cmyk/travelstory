<?php defined( 'ABSPATH' ) || exit;

// Show tour product options discounts
if ( !apply_filters( OVABRW_PREFIX.'show_tour_product_options_discounts', true ) ) return;

// Before tour product options discounts
do_action( OVABRW_PREFIX.'before_tour_product_options_discounts', $this );

// Discount from
$discount_from = $this->get_meta_value( 'discount_from' );

// Get guest options
$guest_options = $this->get_guest_options();

// Get first guest label
$first_guest_label = isset( $guest_options[0]['label'] ) ? $guest_options[0]['label'] : esc_html__( 'Adult', 'ova-brw' );

// Width for guest price column
$guest_width = round( 60 / count( $guest_options ), wc_get_price_decimals() );

// Number colspan
$colspan = 4 + count( $guest_options );

?>
<div id="ovabrw-options-discounts" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Discounts (Price Priority - No. 1,2)', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_tour_discounts_content', $this ); ?>
		<div class="ovabrw-table">
			<?php woocommerce_wp_radio([
				'id' 			=> $this->get_meta_name( 'discount_applicable' ),
				'value' 		=> $this->get_meta_value( 'discount_applicable', 'only' ),
				'label' 		=> esc_html__( 'Discount Based on ', 'ova-brw' ),
				'options' 		=> [
					'only' 	=> sprintf( esc_html__( 'Total %s', 'ova-brw' ), $first_guest_label ),
					'all' 	=> esc_html__( 'Total Guests', 'ova-brw' )
				]
			]); ?>
			<span class="ovabrw-note">
				<?php esc_html_e( 'Base Discounts (Price Priority - No. 2)', 'ova-brw' ); ?>
			</span>
			<table class="widefat">
				<thead>
					<th class="ovabrw-required ovabrw-discount-from" data-numberof-guests-text="<?php esc_attr_e( 'From (No. of guests)', 'ova-brw' ); ?>" data-numberof-adults-text="<?php echo sprintf( esc_attr__( 'From (No. of %s)', 'ova-brw' ), $first_guest_label ); ?>">
						<?php esc_html_e( 'From (No. of guests)', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required ovabrw-discount-to" data-numberof-guests-text="<?php esc_attr_e( 'To (No. of guests)', 'ova-brw' ); ?>" data-numberof-adults-text="<?php echo sprintf( esc_attr__( 'To (No. of %s)', 'ova-brw' ), $first_guest_label ); ?>">
						<?php esc_html_e( 'To (No. of guests)', 'ova-brw' ); ?>
					</th>
					<?php foreach ( $guest_options as $guest ):
						// Get discount guest prices
						$var_price = $guest['name'].'_price';

						// Initialize a variable with the name stored in $var_price
						${$var_price} = $this->get_meta_value('discount_'.$guest['name'].'_price' );
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
				<?php if ( ovabrw_array_exists( $discount_from ) ):
					// Discount to
					$discount_to = $this->get_meta_value( 'discount_to' );

					// Loop discount from
					foreach ( $discount_from as $k => $from ):
						$to = ovabrw_get_meta_data( $k, $discount_to );
				?>
					<tr>
						<td width="19%">
							<?php ovabrw_wp_text_input([
								'type' 		=> 'number',
								'class' 	=> 'ovabrw-input-required',
								'name' 		=> $this->get_meta_name( 'discount_from[]' ),
								'value' 	=> $from,
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
								'value' 	=> $to,
								'attrs' 	=> [
									'data-min' => 0
								],
								'data_type' => 'number'
							]); ?>
						</td>
						<?php foreach ( $guest_options as $guest ):
							$var_price 		= $guest['name'].'_price';
							$guest_price 	= isset( ${$var_price} ) ? ovabrw_get_meta_data( $k, ${$var_price} ) : '';
						?>
							<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'name' 		=> $this->get_meta_name( 'discount_'.$guest['name'].'_price[]' ),
									'value' 	=> $guest_price,
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
				<?php endforeach; endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="<?php echo esc_attr( $colspan ); ?>">
							<button class="button ovabrw-add-discount">
								<?php esc_html_e( 'Add discount', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
			<input
				type="hidden"
				name="ovabrw-discount-row"
				data-row="
				<?php
					$template = OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-discount-field.php';
					ob_start();
					include( $template );
					echo esc_attr( ob_get_clean() );
				?>"
			/>
		</div>
		<?php do_action( OVABRW_PREFIX.'tour_product_options_discounts', $this );

			// Special times options
			include OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-special-times.php';
		?>
		<?php do_action( $this->prefix.'after_tour_discounts_content', $this ); ?>
	</div>
</div>