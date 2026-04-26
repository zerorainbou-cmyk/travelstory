<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get special start
$special_start = $product->get_meta_value( 'special_from' );
if ( !ovabrw_array_exists( $special_start ) ) return;

// Get special end
$special_end = $product->get_meta_value( 'special_to' );

// Get special discount
$special_discount = $product->get_meta_value( 'special_discount' );

// Get guest options
$guest_options = $product->get_guests();

?>
<div class="ovabrw-product-special-time">
	<div class="ovabrw-label">
		<?php esc_html_e( 'Seasonal Discounts', 'ova-brw' ); ?>
	</div>
	<table class="ovabrw-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Start', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'End', 'ova-brw' ); ?></th>
				<?php foreach ( $guest_options as $guest ): ?>
					<th><?php echo esc_html( $guest['label'] ); ?></th>
				<?php endforeach; ?>
				<?php if ( ovabrw_array_exists( $special_discount ) ): ?>
					<th><?php esc_html_e( 'Discount', 'ova-brw' ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $special_start as $i => $start ):
				if ( !$start ) continue;
				$start = date_i18n( 'M j', $start );

				// Get end date
				$end = ovabrw_get_meta_data( $i, $special_end );
				if ( !$end ) continue;
				$end = date_i18n( 'M j', $end );

				// Discounts
				$discounts = ovabrw_get_meta_data( $i, $special_discount );
			?>
				<tr>
					<td><?php echo esc_html( $start ); ?></td>
					<td><?php echo esc_html( $end ); ?></td>
					<?php foreach ( $guest_options as $guest ):
						// Get discount prices
						$discount_prices = $product->get_meta_value( 'special_'.$guest['name'].'_price' );

						// Get guest price
						$guest_price = ovabrw_get_meta_data( $i, $discount_prices );
					?>
						<td><?php echo ovabrw_wc_price( $guest_price ); ?></td>
					<?php endforeach;

					// Special discount
					if ( ovabrw_array_exists( $special_discount ) ): ?>
						<td>
							<?php if ( ovabrw_array_exists( $discounts ) ):
								$dsc_from 	= ovabrw_get_meta_data( 'from', $discounts );
								$dsc_to 	= ovabrw_get_meta_data( 'to', $discounts );
							?>
								<a href="#" class="ovabrw_open_popup">
									<?php esc_html_e( 'View', 'ova-brw' ); ?>
								</a>
								<div class="popup">
									<div class="popup-inner">
										<div class="price_table">
											<div class="time_discount">
												<span>
													<?php esc_html_e( 'Time: ', 'ova-brw' ); ?>
												</span>
												<span class="start-time">
													<?php echo esc_html( $start ); ?>
												</span>
												<span class="seperate">-</span>
												<span class="end-time">
													<?php echo esc_html( $end ); ?>
												</span>
											</div>
											<?php if ( ovabrw_array_exists( $dsc_from ) ): ?>
												<table class="ovabrw-table">
													<thead>
														<tr>
															<th>
																<?php esc_html_e( 'From - To (No. of guests)', 'ova-brw' ); ?>
															</th>
															<?php foreach ( $guest_options as $guest ): ?>
																<th><?php echo esc_html( $guest['label'] ); ?></th>
															<?php endforeach; ?>
														</tr>
													</thead>
													<tbody>
													<?php foreach ( $dsc_from as $dsc_i => $from ):
														$to = ovabrw_get_meta_data( $dsc_i, $dsc_to );
													?>
														<tr>
															<td>
																<span>
																	<?php echo esc_html( $from ); ?>
																</span>
																<span>-</span>
																<span>
																	<?php echo esc_html( $to ); ?>
																</span>
															</td>
															<?php foreach ( $guest_options as $guest ):
																// Get discount prices
																$dsc_prices = ovabrw_get_meta_data( $guest['name'].'_price', $discounts );

																// Get guest price
																$guest_price = ovabrw_get_meta_data( $dsc_i, $dsc_prices );
															?>
																<td><?php echo ovabrw_wc_price( $guest_price ); ?></td>
															<?php endforeach; ?>
														</tr>
													<?php endforeach; ?>
													</tbody>
												</table>
											<?php endif; ?>
										</div>
										<div class="close_discount">
											<a class="popup-close-2" href="#">
												<?php esc_html_e( 'Close', 'ova-brw' ); ?>
											</a>
										</div>
										<a class="popup-close" href="#">x</a>
									</div>
								</div>
							<?php endif; ?>
						</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>