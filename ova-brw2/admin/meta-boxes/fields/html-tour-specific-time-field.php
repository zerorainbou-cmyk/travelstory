<?php defined( 'ABSPATH' ) || exit; ?>
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
							<?php esc_html_e( 'End date', 'ova-brw' ); ?>
						</th>
					</thead>
					<tbody>
						<tr>
							<td width="50%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'id' 		=> 'specificFromUniqueID',
									'class' 	=> 'ovabrw-input-required start-date',
									'name' 		=> $this->get_meta_name( 'specific_from[]' ),
									'data_type' => 'datepicker-no-year',
								]); ?>
							</td>
							<td width="50%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'id' 		=> 'specificToUniqueID',
									'class' 	=> 'ovabrw-input-required end-date',
									'name' 		=> $this->get_meta_name( 'specific_to[]' ),
									'data_type' => 'datepicker-no-year'
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
		<?php foreach ( $daily_args as $daily => $daily_label ): ?>
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
						<tbody class="ovabrw-sortable"></tbody>
						<tfoot>
							<tr>
								<th colspan="<?php echo esc_attr( $colspan ); ?>">
									<button class="button ovabrw-add-specific-timeslot">
										<?php esc_html_e( 'Add time slot', 'ova-brw' ); ?>
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