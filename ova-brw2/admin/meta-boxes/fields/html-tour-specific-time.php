<?php defined( 'ABSPATH' ) || exit;

// Show tour product options date ranges
if ( !apply_filters( OVABRW_PREFIX.'show_tour_product_options_specific_time', true ) ) return;

// Before tour product options date ranges
do_action( OVABRW_PREFIX.'before_tour_product_options_specific_time', $this );

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

// Specific time
$specific_from 	= $this->get_meta_value( 'specific_from' );
$specific_to 	= $this->get_meta_value( 'specific_to' );

?>
<div id="ovabrw-options-specific-time" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Specific time period', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-specific-time-wrap">
			<div class="ovabrw-specific-time-content ovabrw-sortable-specific-time">
				<?php if ( ovabrw_array_exists( $specific_from ) && ovabrw_array_exists( $specific_to ) ):
					// Get time slots data
					$specific_labels 		= $this->get_meta_value( 'specific_label' );
					$specific_start 		= $this->get_meta_value( 'specific_start' );
					$specific_end 			= $this->get_meta_value( 'specific_end' );
					$specific_max_guests 	= $this->get_meta_value( 'specific_max_guests' );

					// Get daily price
					foreach ( $guest_options as $guest ) {
						// Get discount guest prices
						$specific_price = 'specific_'.$guest['name'].'_price';

						// Initialize a variable with the name stored in $var_price
						${$specific_price} = $this->get_meta_value('specific_'.$guest['name'].'_price' );
					}

					foreach ( $specific_from as $k => $from_date ):
						// To date
						$to_date = ovabrw_get_meta_data( $k, $specific_to );
				?>
					<div class="ovabrw-specific-time-item">
						<div class="ovabrw-specific-time-head">
							<div class="ovabrw-specific-time-head-left">
								<div class="ovabrw-table">
									<table class="widefat">
										<thead>
											<th class="ovabrw-required">
												<?php esc_html_e( 'From date', 'ova-brw' ); ?>
											</th>
											<th class="ovabrw-required">
												<?php esc_html_e( 'To date', 'ova-brw' ); ?>
											</th>
										</thead>
										<tbody>
											<tr>
												<td width="50%">
													<?php ovabrw_wp_text_input([
														'type' 		=> 'text',
														'id' 		=> ovabrw_unique_id( 'specific_from' ),
														'class' 	=> 'ovabrw-input-required start-date',
														'name' 		=> $this->get_meta_name( 'specific_from[]' ),
														'value' 	=> $from_date,
														'data_type' => 'datepicker-no-year',
														'attrs' 	=> [
															'data-date' => $from_date ? gmdate( OVABRW()->options->get_date_format_no_year(), $from_date ) : ''
														]
													]); ?>
												</td>
												<td width="50%">
													<?php ovabrw_wp_text_input([
														'type' 		=> 'text',
														'id' 		=> ovabrw_unique_id( 'specific_to' ),
														'class' 	=> 'ovabrw-input-required end-date',
														'name' 		=> $this->get_meta_name( 'specific_to[]' ),
														'value' 	=> $to_date,
														'data_type' => 'datepicker-no-year',
														'attrs' 	=> [
															'data-date' => $to_date ? gmdate( OVABRW()->options->get_date_format_no_year(), $to_date ) : ''
														]
													]); ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="ovabrw-specific-time-head-right">
								<span class="ovabrw-sort-icon">
									<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
								</span>
								<span>
									<button class="button ovabrw-remove-specific-time" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
								</span>
							</div>
						</div>
						<div class="ovabrw-table">
						<?php foreach ( $daily_args as $daily => $daily_label ):
							// Labels
							$labels = isset( $specific_labels[$k][$daily] ) ? $specific_labels[$k][$daily] : '';

							// Start times
							$start_times = isset( $specific_start[$k][$daily] ) ? $specific_start[$k][$daily] : '';

							// End times
							$end_times = isset( $specific_end[$k][$daily] ) ? $specific_end[$k][$daily] : '';

							// Max guests
							$max_guests = isset( $specific_max_guests[$k][$daily] ) ? $specific_max_guests[$k][$daily] : '';
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
										<?php if ( ovabrw_array_exists( $start_times ) && ovabrw_array_exists( $end_times ) ):
											foreach ( $start_times as $i => $start_time ):
												$end_time 	= ovabrw_get_meta_data( $i, $end_times );
												$label 		= ovabrw_get_meta_data( $i, $labels );
												$max_guest 	= (int)ovabrw_get_meta_data( $i, $max_guests );
										?>
											<tr>
												<td width="18%">
													<?php ovabrw_wp_text_input([
														'type' 			=> 'text',
														'class' 		=> 'timeslot-label',
														'name' 			=> $this->get_meta_name( 'specific_label['.$k.']['.$daily.'][]' ),
														'value' 		=> $label,
														'placeholder' 	=> esc_html__( '...', 'ova-brw' ),
														'attrs' 		=> [
															'data-name' => $this->get_meta_name( 'specific_label[index]['.$daily.'][]' )
														]
													]); ?>
												</td>
												<td width="11%">
													<?php ovabrw_wp_text_input([
														'type' 		=> 'text',
														'class' 	=> 'ovabrw-input-required start-time',
														'name' 		=> $this->get_meta_name( 'specific_start['.$k.']['.$daily.'][]' ),
														'value' 	=> $start_time,
														'data_type' => 'timestamp',
														'attrs' 	=> [
															'data-name' => $this->get_meta_name( 'specific_start[index]['.$daily.'][]' )
														]
													]); ?>
												</td>
												<td width="11%">
													<?php ovabrw_wp_text_input([
														'type' 		=> 'text',
														'class' 	=> 'ovabrw-input-required end-time',
														'name' 		=> $this->get_meta_name( 'specific_end['.$k.']['.$daily.'][]' ),
														'value' 	=> $end_time,
														'data_type' => 'timestamp',
														'attrs' 	=> [
															'data-name' => $this->get_meta_name( 'specific_end[index]['.$daily.'][]' )
														]
													]); ?>
												</td>
												<?php foreach ( $guest_options as $guest ):
													$var_price 		= 'specific_'.$guest['name'].'_price';
													$guest_price 	= isset( ${$var_price}[$k][$daily][$i] ) ? ${$var_price}[$k][$daily][$i] : '';
												?>
													<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
														<?php ovabrw_wp_text_input([
															'type' 		=> 'text',
															'class' 	=> 'ovabrw-input-required ovabrw-specific-option-price',
															'name' 		=> $this->get_meta_name( 'specific_'.$guest['name'].'_price['.$k.']['.$daily.'][]' ),
															'value' 	=> $guest_price,
															'data_type' => 'price',
															'attrs' 	=> [
																'data-name' => $this->get_meta_name( 'specific_'.$guest['name'].'_price[index]['.$daily.'][]' )
															]
														]); ?>
													</td>
												<?php endforeach; ?>
												<td width="10%">
													<?php ovabrw_wp_text_input([
														'type' 		=> 'number',
														'class' 	=> 'ovabrw-input-required ovabrw-specific-max-guests',
														'name' 		=> $this->get_meta_name( 'specific_max_guests['.$k.']['.$daily.'][]' ),
														'value' 	=> $max_guest,
														'attrs' 	=> [ 'min' => 0 ],
														'data_type' => 'number',
														'attrs' 	=> [
															'data-name' => $this->get_meta_name( 'specific_max_guests[index]['.$daily.'][]' )
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
										<?php endforeach;
										endif; ?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="<?php echo esc_attr( $colspan ); ?>">
													<button class="button ovabrw-add-specific-timeslot">
														<?php esc_html_e( 'Add new Time Slot', 'ova-brw' ); ?>
													</button>
												</th>
											</tr>
										</tfoot>
										<input type="hidden" name="ovabrw-specific-dayofweek" value="<?php echo esc_attr( $daily ); ?>">
									</table>
								</div>
							</div>
						<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach;
				endif; ?>
			</div>
			<div class="ovabrw-specific-time-btn">
				<button class="button ovabrw-add-specific-time">
					<?php esc_html_e( 'Add specific time', 'ova-brw' ); ?>
				</button>
			</div>
			<input
				type="hidden"
				name="ovabrw-specific-field"
				data-row="
				<?php
					$template = OVABRW_PLUGIN_ADMIN.'meta-boxes/fields/html-tour-specific-time-field.php';
					ob_start();
					include( $template );
					echo esc_attr( ob_get_clean() );
				?>"
			/>
			<input
				type="hidden"
				name="ovabrw-specific-timeslot"
				data-row="
				<?php
					$template = OVABRW_PLUGIN_ADMIN.'meta-boxes/fields/html-tour-specific-timeslot.php';
					ob_start();
					include( $template );
					echo esc_attr( ob_get_clean() );
				?>"
			/>
		</div>
		<?php do_action( OVABRW_PREFIX.'tour_product_options_specific_time', $this ); ?>
	</div>
</div>