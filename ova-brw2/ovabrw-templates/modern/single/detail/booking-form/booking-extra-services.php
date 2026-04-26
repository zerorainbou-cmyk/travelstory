<?php if ( !defined( 'ABSPATH' ) ) exit();

// Show extra services
$form = ovabrw_get_meta_data( 'form', $args, 'booking' );
if ( 'booking' === $form && 'yes' != ovabrw_get_setting( 'booking_form_show_extra_service', 'yes' ) ) return;
if ( 'request' === $form && 'yes' != ovabrw_get_setting( 'request_booking_form_show_service', 'yes' ) ) return;

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Hidden
$hidden_services = [];

// Get service ids
$service_ids = $product->get_meta_value( 'extra_service_id' );
if ( !ovabrw_array_exists( $service_ids ) ) return;

// Guest options
$guest_options = $product->get_guests();

// Get service labels
$service_labels = $product->get_meta_value( 'extra_service_label' );

// Get service required
$service_required = $product->get_meta_value( 'extra_service_required' );

// Get service display
$service_display = $product->get_meta_value( 'extra_service_display' );

// Get service guests
$service_guests = $product->get_meta_value( 'extra_service_guests' );

// Get service description
$service_desc = $product->get_meta_value( 'extra_service_description' );

// Get option ids
$option_ids = $product->get_meta_value( 'extra_service_option_id' );

// Get option names
$option_names = $product->get_meta_value( 'extra_service_option_name' );

// Option prices
foreach ( $guest_options as $guest ) {
	$var_price 		= $guest['name'].'_prices';
	${$var_price} 	= $product->get_meta_value( 'extra_service_option_'.$guest['name'].'_price' );
}

// Get option qtys
$option_qtys = $product->get_meta_value( 'extra_service_option_guest' );

// Get option applicable
$option_applicable = $product->get_meta_value( 'extra_service_option_type' );

// Loop
foreach ( $service_ids as $k => $id ):
	$label 			= ovabrw_get_meta_data( $k, $service_labels );
    $required 		= ovabrw_get_meta_data( $k, $service_required );
    $display 		= ovabrw_get_meta_data( $k, $service_display );
    $choose_guests 	= ovabrw_get_meta_data( $k, $service_guests );
    $desc    		= ovabrw_get_meta_data( $k, $service_desc );
    $opt_ids 		= ovabrw_get_meta_data( $k, $option_ids, [] );
    $opt_names 		= ovabrw_get_meta_data( $k, $option_names, [] );
    $opt_qtys 		= ovabrw_get_meta_data( $k, $option_qtys, [] );
    $opt_applicable = ovabrw_get_meta_data( $k, $option_applicable, [] );

    // Required class
    $required_class = '';
    if ( $required ) $required_class .= ' ovabrw-required';

    // Check option ids exists
    if ( !ovabrw_array_exists( $opt_ids ) ) continue;

    // Hidden
    $hidden_services[$id] = $display;

    // Option prices
    $option_prices = [];

    // Option max guests
    $max_guests = [];

    // Get field id
    $field_id = ovabrw_unique_id( $id.'_'.$product->get_id() );
?>
	<div class="ovabrw-extra-services">
		<div class="ovabrw-label <?php echo esc_attr( $required_class ); ?>">
			<?php echo esc_html( $label ); ?>
			<?php if ( $desc ): ?>
				<span class="ovabrw-description" aria-label="<?php echo esc_attr( $desc ); ?>">
					<i class="brwicon2-question" aria-hidden="true"></i>
				</span>
			<?php endif; ?>
		</div>
		<?php if ( 'dropdown' === $display ): ?>
			<div class="ovabrw-service-select">
				<select name="<?php echo esc_attr( $id ); ?>" class="<?php echo $required ? 'ovabrw-input-required' : ''; ?><?php echo 'auto' === $choose_guests ? ' auto-guests' : ''; ?>">
					<option value="">
						<?php echo wp_kses_post( apply_filters( OVABRW_PREFIX.'extra_service_dropdown_placehodel', sprintf( esc_html__( 'Select %s', 'ova-brw' ), esc_html( $label ) ), $product ) ); ?>
					</option>
					<?php foreach ( $opt_ids as $i => $opt_id ):
						// Get option name
						$opt_name = ovabrw_get_meta_data( $i, $opt_names );

						// Get option quantity
                        $opt_qty = ovabrw_get_meta_data( $i, $opt_qtys );

                        // Get option applicable
                        $applicable = ovabrw_get_meta_data( $i, $opt_applicable );

                        // Guest prices
                    	$max_guests[$opt_id] = $opt_qty;

                    	foreach ( $guest_options as $guest ) {
                        	$var_price		= $guest['name'].'_prices';
                        	$guest_price 	= isset( ${$var_price}[$k][$i] ) ? ${$var_price}[$k][$i] : '';

                        	if ( '' !== $guest_price ) {
                        		$guest['price'] 			= (float)$guest_price;
                        		$guest['type'] 				= $applicable;
                        		$option_prices[$opt_id][] 	= $guest;
                        	}
                        }
					?>
						<option value="<?php echo esc_attr( $opt_id ); ?>">
							<?php echo esc_html( $opt_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<?php if ( 'auto' != $choose_guests ): ?>
					<div class="ovabrw-service-guest">
						<i class="brwicon2-group" aria-hidden="true"></i>
					</div>
					<?php if ( ovabrw_array_exists( $option_prices ) ):
						foreach ( $option_prices as $opt_id => $guest_data ):
							$opt_max_guest = ovabrw_get_meta_data( $opt_id, $max_guests );
					?>
						<div class="ovabrw-service-guestspicker" data-option="<?php echo esc_attr( $opt_id ); ?>">
							<?php $flag = 0; foreach ( $guest_data as $guest ):
								$opt_guest_name = $id.'_guests['.$opt_id.']['.$guest['name'].']';
								$opt_guest_type = 'person' === $guest['type'] ? esc_html__( ' /guest', 'ova-brw' ) : esc_html__( ' /order', 'ova-brw' );
								
								$numberof_guest = 0;
								if ( 0 === $flag ) $numberof_guest = 1;
								$flag++;
							?>
								<div class="guests-item">
			                        <div class="guests-info">
			                            <div class="guests-label">
			                                <h3 class="ovabrw-label">
			                                    <?php echo esc_html( ovabrw_get_meta_data( 'label', $guest ) ); ?>
			                                    <?php if ( ovabrw_get_meta_data( 'desc', $guest ) ): ?>
			                                        <span class="ovabrw-description" aria-label="<?php echo esc_attr( $guest['desc'] ); ?>">
			                                            <i class="brwicon2-question" aria-hidden="true"></i>
			                                        </span>
			                                    <?php endif; ?>
			                                </h3>
			                            </div>
			                            <?php if ( apply_filters( OVABRW_PREFIX.'extra_service_show_guest_price', true, $product ) ): ?>
			                                <div class="guests-price <?php echo esc_attr( $guest['name'] ); ?>-price">
			                                	<?php echo wp_kses_post( ovabrw_wc_price( $guest['price'] ).$opt_guest_type ); ?>
			                                </div>
			                            <?php endif; ?>
			                        </div>
			                        <div class="guests-action">
			                            <div class="guests-icon guests-minus">
			                                <i class="brwicon2-minus-sign" aria-hidden="true"></i>
			                            </div>
			                            <span class="guests-number numberof-<?php echo esc_attr( $guest['name'] ); ?>">
			                                <?php echo esc_html( $numberof_guest ); ?>
			                            </span>
			                            <?php ovabrw_text_input([
			                            	'type'  => 'hidden',
			                                'class' => 'service-guests-input',
			                                'name'  => $opt_guest_name,
			                                'value' => $numberof_guest,
			                                'attrs' => [
			                                	'data-name'     => $guest['name'],
			                                    'data-label'    => $guest['label']
			                                ]
			                            ]); ?>
			                            <div class="guests-icon guests-plus">
			                                <i class="brwicon2-plus-sign" aria-hidden="true"></i>
			                            </div>
			                        </div>
			                    </div>
							<?php endforeach; ?>
							<input
								type="hidden"
								name="<?php echo esc_attr( $id.'_max_guests['.$opt_id.']' ); ?>"
								class="ovabrw-option-max-guests"
								value="<?php echo esc_attr( $opt_max_guest ); ?>"
							/>
						</div>
					<?php endforeach; // END loop
					endif; // END option prices
				endif; // END choose guests ?>
			</div>
		<?php elseif ( 'checkbox' === $display ): ?>
			<div class="ovabrw-service-checkbox<?php echo $required ? ' ovabrw-input-required' : ''; ?>">
				<?php foreach ( $opt_ids as $i => $opt_id ):
					// Get option name
					$opt_name = ovabrw_get_meta_data( $i, $opt_names );

					// Get option quantity
                    $opt_qty = ovabrw_get_meta_data( $i, $opt_qtys );

                    // Get option applicable
                    $applicable = ovabrw_get_meta_data( $i, $opt_applicable );

                    // Get option class
                    $opt_class = ovabrw_unique_id( $opt_id );
				?>
					<div class="service-item">
						<label for="<?php echo esc_attr( $opt_class ); ?>">
							<?php echo wp_kses_post( $opt_name ); ?>
							<input
								type="checkbox"
								id="<?php echo esc_attr( $opt_class ); ?>"
								class="<?php echo 'auto' === $choose_guests ? 'auto-guests' : ''; ?>"
								name="<?php echo esc_attr( $id ).'[]'; ?>"
								value="<?php echo esc_attr( $opt_id ); ?>"
							/>
							<span class="checkmark"></span>
						</label>
						<?php if ( 'auto' != $choose_guests ): ?>
							<div class="ovabrw-service-guest" data-option="">
								<i class="brwicon2-group" aria-hidden="true"></i>
							</div>
							<div class="ovabrw-service-guestspicker <?php echo esc_attr( $opt_class ); ?>" data-class="<?php echo esc_attr( $opt_class ); ?>">
								<?php $flag = 0; foreach ( $guest_options as $guest ):
									$var_price		= $guest['name'].'_prices';
	                        		$guest_price 	= isset( ${$var_price}[$k][$i] ) ? ${$var_price}[$k][$i] : '';

	                        		// Class
	                        		$opt_class = '';
	                        		if ( '' === $guest_price ) $opt_class = 'ovabrw-hidden';

	                        		$opt_guest_name = $id.'_guests['.$opt_id.']['.$guest['name'].']';
									$opt_guest_type = 'person' === $applicable ? esc_html__( ' /guest', 'ova-brw' ) : esc_html__( ' /order', 'ova-brw' );

	                        		$numberof_guest = 0;
									if ( 0 === $flag ) $numberof_guest = 1;
									$flag++;
								?>
									<div class="guests-item <?php echo esc_attr( $opt_class ); ?>">
				                        <div class="guests-info">
				                            <div class="guests-label">
				                                <h3 class="ovabrw-label">
				                                    <?php echo esc_html( ovabrw_get_meta_data( 'label', $guest ) ); ?>
				                                    <?php if ( ovabrw_get_meta_data( 'desc', $guest ) ): ?>
				                                        <span class="ovabrw-description" aria-label="<?php echo esc_attr( $guest['desc'] ); ?>">
				                                            <i class="brwicon2-question" aria-hidden="true"></i>
				                                        </span>
				                                    <?php endif; ?>
				                                </h3>
				                            </div>
				                            <?php if ( apply_filters( OVABRW_PREFIX.'extra_service_show_guest_price', true, $product ) ): ?>
				                                <div class="guests-price <?php echo esc_attr( $guest['name'] ); ?>-price">
				                                	<?php echo wp_kses_post( ovabrw_wc_price( $guest_price ).$opt_guest_type ); ?>
				                                </div>
				                            <?php endif; ?>
				                        </div>
				                        <div class="guests-action">
				                            <div class="guests-icon guests-minus">
				                                <i class="brwicon2-minus-sign" aria-hidden="true"></i>
				                            </div>
				                            <span class="guests-number numberof-<?php echo esc_attr( $guest['name'] ); ?>">
				                                <?php echo esc_html( $numberof_guest ); ?>
				                            </span>
				                            <?php ovabrw_text_input([
				                            	'type'  => 'hidden',
				                                'class' => 'service-guests-input',
				                                'name'  => $opt_guest_name,
				                                'value' => $numberof_guest,
				                                'attrs' => [
				                                	'data-name'     => $guest['name'],
				                                    'data-label'    => $guest['label']
				                                ]
				                            ]); ?>
				                            <div class="guests-icon guests-plus">
				                                <i class="brwicon2-plus-sign" aria-hidden="true"></i>
				                            </div>
				                        </div>
				                    </div>
								<?php endforeach; ?>
								<input
									type="hidden"
									name="<?php echo esc_attr( $id.'_max_guests['.$opt_id.']' ); ?>"
									class="ovabrw-option-max-guests"
									value="<?php echo esc_attr( $opt_max_guest ); ?>"
								/>
							</div>
						<?php endif; // END choose guests ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
<?php endforeach; // END loop extra services

if ( ovabrw_array_exists( $hidden_services ) ): ?>
	<input
		type="hidden"
		name="ovabrw-extra-services"
		value="<?php echo esc_attr( wp_json_encode( $hidden_services ) ); ?>"
	/>
<?php endif; ?>