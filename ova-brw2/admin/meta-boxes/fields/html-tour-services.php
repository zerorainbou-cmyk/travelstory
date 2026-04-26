<?php defined( 'ABSPATH' ) || exit;

// Show tour product options extra services
if ( !apply_filters( OVABRW_PREFIX.'show_tour_product_options_extra_services', true ) ) return;

// Before tour product options extra services
do_action( OVABRW_PREFIX.'before_tour_product_options_extra_services', $this );

// Get extra services data
$service_ids = $this->get_meta_value( 'extra_service_id' );

// Get guest options
$guest_options = $this->get_guest_options();

// Width for guest price column
$guest_width = round( 42 / count( $guest_options ), wc_get_price_decimals() );

// Number colspan
$colspan = 6 + count( $guest_options );

?>

<div id="ovabrw-options-extra-services" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Services', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_tour_services_content', $this ); ?>
		<div class="ovabrw-extra-services-wrap">
			<div class="ovabrw-extra-services-content ovabrw-sortable-extra-services">
			<?php if ( ovabrw_array_exists( $service_ids ) ):
				$service_label 			= $this->get_meta_value( 'extra_service_label' );
				$service_required 		= $this->get_meta_value( 'extra_service_required' );
				$service_display 		= $this->get_meta_value( 'extra_service_display' );
				$service_guests 		= $this->get_meta_value( 'extra_service_guests' );
				$service_description 	= $this->get_meta_value( 'extra_service_description' );

				// Options
				$option_ids 			= $this->get_meta_value( 'extra_service_option_id' );
				$option_names 			= $this->get_meta_value( 'extra_service_option_name' );
				$option_guests 			= $this->get_meta_value( 'extra_service_option_guest' );
				$option_types 			= $this->get_meta_value( 'extra_service_option_type' );

				// Option guest prices
				foreach ( $guest_options as $guest ) {
					// Get discount guest prices
					$var_price = $guest['name'].'_price';

					// Initialize a variable with the name stored in $var_price
					${$var_price} = $this->get_meta_value('extra_service_option_'.$guest['name'].'_price' );
				}
				// End

				foreach ( $service_ids as $k => $id ):
					$label 				= ovabrw_get_meta_data( $k, $service_label );
					$required 			= ovabrw_get_meta_data( $k, $service_required );
					$display 			= ovabrw_get_meta_data( $k, $service_display );
					$choose_guests 		= ovabrw_get_meta_data( $k, $service_guests );
					$description 		= ovabrw_get_meta_data( $k, $service_description );
					$opt_ids 			= ovabrw_get_meta_data( $k, $option_ids );
					$opt_names 			= ovabrw_get_meta_data( $k, $option_names );
					$opt_guests 		= ovabrw_get_meta_data( $k, $option_guests );
					$opt_types 			= ovabrw_get_meta_data( $k, $option_types );
			?>
				<div class="ovabrw-extra-service-item">
					<div class="ovabrw-services-head">
						<div class="ovabrw-services-head-left">
							<div class="ovabrw-service-heading">
								<span class="ovabrw-required">
									<?php esc_html_e( 'ID', 'ova-brw' ); ?>
								</span>
								<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-required ovabrw-extra-service-id',
									'name' 			=> $this->get_meta_name( 'extra_service_id[]' ),
									'value' 		=> $id,
									'placeholder' 	=> esc_html__( 'unique ID', 'ova-brw' ),
									'attrs' 		=> [ 'autocomplete' => 'off' ]
								]); ?>
							</div>
							<div class="ovabrw-service-heading">
								<span class="ovabrw-required">
									<?php esc_html_e( 'Label', 'ova-brw' ); ?>
								</span>
								<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name( 'extra_service_label[]' ),
									'value' 		=> $label,
									'placeholder' 	=> esc_html__( 'label', 'ova-brw' ),
									'attrs' 		=> [ 'autocomplete' => 'off' ]
								]); ?>
							</div>
							<div class="ovabrw-service-heading">
								<span>
									<?php esc_html_e( 'Required', 'ova-brw' ); ?>
								</span>
								<?php ovabrw_wp_select_input([
									'name' 		=> $this->get_meta_name( 'extra_service_required[]' ),
									'value' 	=> $required,
									'options' 	=> [
										'1' 	=> esc_html__( 'Yes', 'ova-brw' ),
										'' 		=> esc_html__( 'No', 'ova-brw' )
									]
								]); ?>
							</div>
							<div class="ovabrw-service-heading">
								<span>
									<?php esc_html_e( 'Display', 'ova-brw' ); ?>
								</span>
								<?php ovabrw_wp_select_input([
									'name' 		=> $this->get_meta_name( 'extra_service_display[]' ),
									'value' 	=> $display,
									'options' 	=> [
										'dropdown' => esc_html__( 'Dropdown', 'ova-brw' ),
										'checkbox' => esc_html__( 'Checkbox', 'ova-brw' )
									]
								]); ?>
							</div>
							<div class="ovabrw-service-heading">
								<span>
									<?php esc_html_e( 'Choose number of guests', 'ova-brw' ); ?>
								</span>
								<div class="ovabrw-service-guests">
									<?php ovabrw_wp_select_input([
										'name' 		=> $this->get_meta_name( 'extra_service_guests[]' ),
										'value' 	=> $choose_guests,
										'options' 	=> [
											'manual' 	=> esc_html__( 'Manual', 'ova-brw' ),
											'auto' 		=> esc_html__( 'Automatic', 'ova-brw' )
										]
									]);

									echo wc_help_tip( esc_html__( 'The customer can either manually choose the number of guests for each option or automatically use the number of guests previously chosen.', 'ova-brw' ) ); ?>
								</div>
							</div>
							<div class="ovabrw-service-heading ovabrw-service-description">
								<span>
									<?php esc_html_e( 'Description', 'ova-brw' ); ?>
								</span>
								<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-service-description',
									'name' 			=> $this->get_meta_name( 'extra_service_description[]' ),
									'value' 		=> $description,
									'placeholder' 	=> esc_html__( 'description', 'ova-brw' ),
									'attrs' 		=> [ 'autocomplete' => 'off' ]
								]); ?>
							</div>
						</div>
						<div class="ovabrw-services-head-right">
							<span class="ovabrw-sort-icon">
								<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
							</span>
							<span>
								<button class="button ovabrw-remove-extra-service" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
							</span>
						</div>
					</div>
					<div class="ovabrw-table">
						<table class="widefat">
							<thead>
								<th class="ovabrw-required">
									<?php esc_html_e( 'Option ID', 'ova-brw' ); ?>
								</th>
								<th class="ovabrw-required">
									<?php esc_html_e( 'Option name', 'ova-brw' ); ?>
								</th>
								<?php foreach ( $guest_options as $guest ):
									$var_price = $guest['name'].'_price';

									// Get discount guest prices
									$var_opt_price = 'opt_'.$guest['name'].'_price';

									// Initialize a variable with the name stored in $var_opt_price
									${$var_opt_price} = isset( ${$var_price} ) ? ovabrw_get_meta_data( $k, ${$var_price} ) : [];
								?>
									<th>
										<?php printf( esc_html__( '%s price (%s)', 'ova-brw' ), esc_html( $guest['label'] ), esc_html( get_woocommerce_currency_symbol() ) ); ?>
										<?php echo wc_help_tip( sprintf( esc_html__( 'Price per %s', 'ova-brw' ), $guest['label'] ) ); ?>
									</th>
								<?php endforeach; ?>
								<th class="ovabrw-service-quatity">
									<?php esc_html_e( 'Max quantity', 'ova-brw' ); ?>
									<?php echo wc_help_tip( esc_html__( 'Maximum quantity', 'ova-brw' ) ); ?>
								</th>
								<th class="ovabrw-required">
									<?php esc_html_e( 'Applicable', 'ova-brw' ); ?>
								</th>
								<th></th>
								<th></th>
							</thead>
							<tbody class="ovabrw-sortable ovabrw-service-options">
							<?php if ( ovabrw_array_exists( $opt_ids ) ):
								foreach ( $opt_ids as $i => $opt_id ):
									$name 			= ovabrw_get_meta_data( $i, $opt_names );
									$numberof_guest = ovabrw_get_meta_data( $i, $opt_guests );
									$type 			= ovabrw_get_meta_data( $i, $opt_types );
							?>
								<tr>
									<td width="14%">
										<?php ovabrw_wp_text_input([
											'type' 			=> 'text',
											'class' 		=> 'ovabrw-input-required ovabrw-service-option-id',
											'name' 			=> $this->get_meta_name( 'extra_service_option_id['.$k.'][]' ),
											'value' 		=> $opt_id,
											'placeholder' 	=> esc_html__( 'unique ID', 'ova-brw' ),
											'attrs' 		=> [ 'autocomplete' => 'off' ]
										]); ?>
									</td>
									<td width="18%">
										<?php ovabrw_wp_text_input([
											'type' 			=> 'text',
											'class' 		=> 'ovabrw-input-required ovabrw-service-option-name',
											'name' 			=> $this->get_meta_name( 'extra_service_option_name['.$k.'][]' ),
											'value' 		=> $name,
											'placeholder' 	=> esc_html__( 'name', 'ova-brw' ),
											'attrs' 		=> [ 'autocomplete' => 'off' ]
										]); ?>
									</td>
									<?php foreach ( $guest_options as $guest ):
										$var_opt_price 	= 'opt_'.$guest['name'].'_price';
										$guest_price 	= isset( ${$var_opt_price} ) ? ovabrw_get_meta_data( $i, ${$var_opt_price} ) : '';
									?>
										<td width="<?php echo esc_attr( $guest_width ).'%'; ?>" class="ovabrw-input-price">
											<?php ovabrw_wp_text_input([
												'type' 		=> 'text',
												'class' 	=> 'ovabrw-service-option-price',
												'name' 		=> $this->get_meta_name( 'extra_service_option_'.$guest['name'].'_price['.$k.'][]' ),
												'value' 	=> $guest_price,
												'data_type' => 'price',
												'attrs' 	=> [
													'data-name' => $this->get_meta_name( 'extra_service_option_'.$guest['name'].'_price[index][]' )
												]
											]); ?>
										</td>
									<?php endforeach; ?>
									<td width="10%">
										<?php ovabrw_wp_text_input([
											'type' 			=> 'number',
											'class' 		=> 'ovabrw-service-option-guest',
											'name' 			=> $this->get_meta_name( 'extra_service_option_guest['.$k.'][]' ),
											'value' 		=> $numberof_guest,
											'placeholder' 	=> 1
										]); ?>
									</td>
									<td width="14%">
										<?php ovabrw_wp_select_input([
											'class' 	=> 'ovabrw-service-option-type',
											'name' 		=> $this->get_meta_name( 'extra_service_option_type['.$k.'][]' ),
											'value' 	=> $type,
											'options' 	=> [
												'person' 	=> esc_html__( '/guest', 'ova-brw' ),
												'order' 	=> esc_html__( '/order', 'ova-brw' )
											]
										]); ?>
									</td>
									<td width="1%" class="ovabrw-sort-icon">
										<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
									</td>
									<td width="1%">
										<button class="button ovabrw-remove-extra-service-option" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
									</td>
								</tr>
							<?php endforeach; endif; ?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="<?php echo esc_attr( $colspan ); ?>">
										<button class="button ovabrw-add-extra-service-option">
											<?php esc_html_e( 'Add new option', 'ova-brw' ); ?>
										</button>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			<?php endforeach; endif; ?>
			</div>
			<div class="ovabrw-services-btn">
				<button class="button ovabrw-add-extra-service">
					<?php esc_html_e( 'Add service', 'ova-brw' ); ?>
				</button>
			</div>
			<input
				type="hidden"
				name="ovabrw-service-row"
				data-row="
				<?php
					$template = OVABRW_PLUGIN_ADMIN.'meta-boxes/fields/html-tour-service-field.php';
					ob_start();
					include( $template );
					echo esc_attr( ob_get_clean() );
				?>"
			/>
			<input
				type="hidden"
				name="ovabrw-service-option-row"
				data-row="
				<?php
					$template = OVABRW_PLUGIN_ADMIN.'meta-boxes/fields/html-tour-service-option.php';
					ob_start();
					include( $template );
					echo esc_attr( ob_get_clean() );
				?>"
			/>
		</div>
		<?php do_action( OVABRW_PREFIX.'tour_product_options_extra_services', $this ); ?>
		<?php do_action( $this->prefix.'after_tour_services_content', $this ); ?>
	</div>
</div>