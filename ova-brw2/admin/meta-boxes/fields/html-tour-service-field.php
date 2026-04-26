<?php defined( 'ABSPATH' ) || exit; ?>
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
					'value' 		=> '[service-id]',
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
					'placeholder' 	=> esc_html__( 'label', 'ova-brw' ),
					'attrs' 		=> [ 'autocomplete' => 'off' ]
				]); ?>
			</div>
			<div class="ovabrw-service-heading">
				<span><?php esc_html_e( 'Required', 'ova-brw' ); ?></span>
				<?php ovabrw_wp_select_input([
					'name' 		=> $this->get_meta_name( 'extra_service_required[]' ),
					'options' 	=> [
						'1' 	=> esc_html__( 'Yes', 'ova-brw' ),
						'' 		=> esc_html__( 'No', 'ova-brw' )
					]
				]); ?>
			</div>
			<div class="ovabrw-service-heading">
				<span><?php esc_html_e( 'Display', 'ova-brw' ); ?></span>
				<?php ovabrw_wp_select_input([
					'name' 		=> $this->get_meta_name( 'extra_service_display[]' ),
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
						'options' 	=> [
							'manual' 	=> esc_html__( 'Manual', 'ova-brw' ),
							'auto' 		=> esc_html__( 'Automatic', 'ova-brw' )
						]
					]);

					echo wc_help_tip( esc_html__( 'The customer can either manually choose the number of guests for each service or automatically apply the number of guests previously chosen.', 'ova-brw' ) ); ?>
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
				<?php foreach ( $guest_options as $guest ): ?>
					<th>
						<?php printf( esc_html__( '%s price (%s)', 'ova-brw' ), esc_html( $guest['label'] ), esc_html( get_woocommerce_currency_symbol() ) ); ?>
						<?php echo wc_help_tip( sprintf( esc_html__( 'Price per %s', 'ova-brw' ), $guest['label'] ) ); ?>
					</th>
				<?php endforeach; ?>
				<th class="ovabrw-service-quatity">
					<?php esc_html_e( 'Max quantity', 'ova-brw' ); ?>
					<?php echo wc_help_tip( esc_html__( 'Maximum quantity', 'ova-brw' ) ); ?>
				</th>
				<th><?php esc_html_e( 'Applicable', 'ova-brw' ); ?></th>
				<th></th>
				<th></th>
			</thead>
			<tbody class="ovabrw-sortable ovabrw-service-options"></tbody>
			<tfoot>
				<tr>
					<th colspan="<?php echo esc_attr( $colspan ); ?>">
						<button class="button ovabrw-add-extra-service-option">
							<?php esc_html_e( 'Add option', 'ova-brw' ); ?>
						</button>
					</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>