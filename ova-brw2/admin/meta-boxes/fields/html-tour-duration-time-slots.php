<?php defined( 'ABSPATH' ) || exit;

do_action( OVABRW_PREFIX.'before_tour_product_options_duration_time_slots', $this ); ?>

<div id="ovabrw-duration-timeslots" class="ovabrw-duration-type">
	<?php
	// Get guest options
	$guest_options = $this->get_guest_options();

	// Width for guest price column
	$guest_width = round( 48 / count( $guest_options ), wc_get_price_decimals() );

	// Number colspan
	$colspan = 6 + count( $guest_options );

	// Daily
	$daily_args = [
		'monday' 	=> esc_html__( 'Monday', 'ova-brw' ),
		'tuesday'	=> esc_html__( 'Tuesday', 'ova-brw' ),
		'wednesday' => esc_html__( 'Wednesday', 'ova-brw' ),
		'thursday' 	=> esc_html__( 'Thursday', 'ova-brw' ),
		'friday'	=> esc_html__( 'Friday', 'ova-brw' ),
		'saturday'	=> esc_html__( 'Saturday', 'ova-brw' ),
		'sunday'	=> esc_html__( 'Sunday', 'ova-brw' )
	];

	// Get time slots data
	$daily_labels 		= $this->get_meta_value( 'tour_timeslots_label' );
	$daily_start_times 	= $this->get_meta_value( 'tour_timeslots_start' );
	$daily_end_times 	= $this->get_meta_value( 'tour_timeslots_end' );
	$daily_max_guests 	= $this->get_meta_value( 'tour_timeslots_max_guests' );

	// Get daily price
	foreach ( $guest_options as $guest ) {
		// Get discount guest prices
		$daily_price = 'daily_'.$guest['name'].'_price';

		// Initialize a variable with the name stored in $var_price
		${$daily_price} = $this->get_meta_value('tour_timeslots_'.$guest['name'].'_price' );
	}
	
	?>
	<div class="ovabrw-table">
		<?php foreach ( $daily_args as $daily => $daily_label ):
			$labels 		= ovabrw_get_meta_data( $daily, $daily_labels, [] );
			$start_times 	= ovabrw_get_meta_data( $daily, $daily_start_times, [] );
			$end_times 		= ovabrw_get_meta_data( $daily, $daily_end_times, [] );
			$max_guests 	= ovabrw_get_meta_data( $daily, $daily_max_guests, [] );

			// Get guest price
			foreach ( $guest_options as $guest ) {
				// Get discount guest prices
				$daily_price 	= 'daily_'.$guest['name'].'_price';
				$var_price 		= $guest['name'].'_price';

				// Initialize a variable with the name stored in $daily_price
				${$var_price} = isset( ${$daily_price} ) ? ovabrw_get_meta_data( $daily, ${$daily_price}, [] ) : [];
			}
		?>
		<div id="ovabrw-every-<?php echo esc_attr( $daily ); ?>" class="ovabrw-daily">
			<h3 class="ovabrw-daily-label">
				<span><?php echo esc_html( $daily_label ); ?></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
				<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
			</h3>
			<div class="ovabrw-daily-content">
				<table class="widefat">
					<thead>
						<th>
							<?php esc_html_e( 'Label (option)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Start', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'End', 'ova-brw' ); ?>
						</th>
						<?php foreach ( $guest_options as $guest ): ?>
							<th class="ovabrw-required">
								<?php printf( esc_html__( '%s price (%s)', 'ova-brw' ), esc_html( $guest['label'] ), esc_html( get_woocommerce_currency_symbol() ) ); ?>
								<?php echo wc_help_tip( sprintf( esc_html__( 'Price per %s', 'ova-brw' ), $guest['label'] ) ); ?>
							</th>
						<?php endforeach; ?>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Max guests', 'ova-brw' ); ?>
							<?php echo wc_help_tip( esc_html__( 'Maximum Number of Guests', 'ova-brw' ) ); ?>
						</th>
						<th></th>
						<th></th>
					</thead>
					<tbody class="ovabrw-sortable">
					<?php if ( ovabrw_array_exists( $start_times ) ):
						foreach ( $start_times as $k => $start_time ):
							$label 		= ovabrw_get_meta_data( $k, $labels );
							$end_time 	= ovabrw_get_meta_data( $k, $end_times );
							$max_guest 	= (int)ovabrw_get_meta_data( $k, $max_guests );
					?>
						<tr>
							<td width="18%">
								<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'timeslot-label',
									'name' 			=> $this->get_meta_name( 'tour_timeslots_label['.$daily.'][]' ),
									'value' 		=> $label,
									'placeholder' 	=> esc_html__( '...', 'ova-brw' )
								]); ?>
							</td>
							<td width="11%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'class' 	=> 'ovabrw-input-required start-time',
									'name' 		=> $this->get_meta_name( 'tour_timeslots_start['.$daily.'][]' ),
									'value' 	=> $start_time,
									'data_type' => 'timestamp'
								]); ?>
							</td>
							<td width="11%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'class' 	=> 'ovabrw-input-required end-time',
									'name' 		=> $this->get_meta_name( 'tour_timeslots_end['.$daily.'][]' ),
									'value' 	=> $end_time,
									'data_type' => 'timestamp'
								]); ?>
							</td>
							<?php foreach ( $guest_options as $guest ):
								$var_price 		= $guest['name'].'_price';
								$guest_price 	= isset( ${$var_price} ) ? ovabrw_get_meta_data( $k, ${$var_price} ) : '';
							?>
								<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
									<?php ovabrw_wp_text_input([
										'type' 		=> 'text',
										'class' 	=> 'ovabrw-timeslots-guest-price',
										'name' 		=> $this->get_meta_name( 'tour_timeslots_'.$guest['name'].'_price['.$daily.'][]' ),
										'value' 	=> $guest_price,
										'data_type' => 'price'
									]); ?>
								</td>
							<?php endforeach; ?>
							<td width="10%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'number',
									'class' 	=> 'ovabrw-timeslots-max-guests',
									'name' 		=> $this->get_meta_name( 'tour_timeslots_max_guests['.$daily.'][]' ),
									'value' 	=> $max_guest,
									'attrs' 	=> [
										'data-min' => 0
									],
									'data_type' => 'number'
								]); ?>
							</td>
							<td width="1%" class="ovabrw-sort-icon">
								<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
							</td>
							<td width="1%">
								<button class="button ovabrw-remove-time-slot" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
							</td>
						</tr>
					<?php endforeach;
					endif; ?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="<?php echo esc_attr( $colspan ); ?>">
								<button class="button ovabrw-add-timeslot">
									<?php esc_html_e( 'Add time slot', 'ova-brw' ); ?>
								</button>
							</th>
						</tr>
					</tfoot>
					<?php ovabrw_wp_text_input([
						'type' 	=> 'hidden',
						'name' 	=> 'ovabrw-day-of-week',
						'value' => $daily
					]); ?>
					<input type="hidden" name="ovabrw-day-of-week" value="<?php echo esc_attr( $daily ); ?>">
				</table>
			</div>
		</div>
		<?php endforeach; ?>
		<input
			type="hidden"
			name="ovabrw-time-slots-row"
			data-row="
			<?php
				$template = OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-tour-duration-time-slots-field.php';
				ob_start();
				include( $template );
				echo esc_attr( ob_get_clean() );
			?>"
		/>
	</div>
	<?php do_action( OVABRW_PREFIX.'tour_product_options_duration_time_slots', $this ); ?>
</div>