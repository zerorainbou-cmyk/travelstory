<?php defined( 'ABSPATH' ) || exit;

// Period data
$period_checkin = $this->get_meta_value( 'period_start' );

// Get guest options
$guest_options = $this->get_guest_options();

// Width for guest price column
$guest_width = round( 42 / count( $guest_options ), wc_get_price_decimals() );

// Number colspan
$colspan = 6 + count( $guest_options );

do_action( OVABRW_PREFIX.'before_tour_product_options_duration_period', $this ); ?>

<div id="ovabrw-duration-period" class="ovabrw-duration-type">
	<div class="ovabrw-table">
		<table class="widefat">
			<thead>
				<th>
					<?php esc_html_e( 'Label (option)', 'ova-brw' ); ?>
				</th>
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
					${$var_price} = $this->get_meta_value('period_'.$guest['name'].'_price' );
				?>
					<th class="ovabrw-required">
						<?php printf( esc_html__( '%s price (%s)', 'ova-brw' ), esc_html( $guest['label'] ), esc_html( get_woocommerce_currency_symbol() ) ); ?>
						<?php echo wc_help_tip( sprintf( esc_html__( 'Price per %s', 'ova-brw' ), $guest['label'] ) ); ?>
					</th>
				<?php endforeach; ?>
				<th class="ovabrw-required">
					<?php esc_html_e( 'Max guests', 'ova-brw' ); ?>
					<?php echo wc_help_tip( esc_html__( 'Maximum number of guests', 'ova-brw' ) ); ?>
				</th>
				<th></th>
				<th></th>
			</thead>
			<tbody class="ovabrw-sortable">
			<?php if ( ovabrw_array_exists( $period_checkin ) ):
				$period_labels 	= $this->get_meta_value('period_label' );
				$period_end 	= $this->get_meta_value('period_end' );
				$max_guests 	= $this->get_meta_value('period_max_guests' );

				foreach ( $period_checkin as $k => $start_date ):
					$label 		= ovabrw_get_meta_data( $k, $period_labels );
					$end_date 	= ovabrw_get_meta_data( $k, $period_end );
					$max_guest 	= (int)ovabrw_get_meta_data( $k, $max_guests );
			?>
				<tr>
					<td width="16%">
						<?php ovabrw_wp_text_input([
							'type' 			=> 'text',
							'class' 		=> 'period-label',
							'name' 			=> $this->get_meta_name( 'period_label[]' ),
							'value' 		=> $label,
							'placeholder' 	=> esc_html__( '...', 'ova-brw' )
						]); ?>
					</td>
					<td width="15%">
						<?php ovabrw_wp_text_input([
							'type' 		=> 'text',
							'id' 		=> ovabrw_unique_id( 'period_start' ),
							'class' 	=> 'ovabrw-input-required start-date',
							'name' 		=> $this->get_meta_name( 'period_start[]' ),
							'value' 	=> $start_date,
							'data_type' => 'datepicker'
						]); ?>
					</td>
					<td width="15%">
						<?php ovabrw_wp_text_input([
							'type' 		=> 'text',
							'id' 		=> ovabrw_unique_id( 'period_end' ),
							'class' 	=> 'ovabrw-input-required end-date',
							'name' 		=> $this->get_meta_name( 'period_end[]' ),
							'value' 	=> $end_date,
							'data_type' => 'datepicker'
						]); ?>
					</td>
					<?php foreach ( $guest_options as $guest ):
						$var_price 		= $guest['name'].'_price';
						$guest_price 	= isset( ${$var_price} ) ? ovabrw_get_meta_data( $k, ${$var_price} ) : '';
					?>
						<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
							<?php ovabrw_wp_text_input([
								'type' 		=> 'text',
								'class' 	=> 'ovabrw-period-guest-price',
								'name' 		=> $this->get_meta_name( 'period_'.$guest['name'].'_price[]' ),
								'value' 	=> $guest_price,
								'data_type' => 'price'
							]); ?>
						</td>
					<?php endforeach; ?>
					<td width="10%">
						<?php ovabrw_wp_text_input([
							'type' 			=> 'number',
							'class' 		=> 'ovabrw-period-max-guests',
							'name' 			=> $this->get_meta_name( 'period_max_guests[]' ),
							'value' 		=> $max_guest,
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
			<?php endforeach;
			endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="<?php echo esc_attr( $colspan ); ?>">
						<button class="button ovabrw-add-period">
							<?php esc_html_e( 'Add date range', 'ova-brw' ); ?>
						</button>
					</th>
				</tr>
			</tfoot>
		</table>
		<input
			type="hidden"
			name="ovabrw-period-row"
			data-row="
			<?php
				$template = OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-duration-period-field.php';
				ob_start();
				include( $template );
				echo esc_attr( ob_get_clean() );
			?>"
		/>
	</div>
	<?php do_action( OVABRW_PREFIX.'tour_product_options_duration_period', $this ); ?>
</div>