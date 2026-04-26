<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-service-item">
	<div class="ovabrw-service-head">
		<div class="ovabrw-service-head-left">
			<div class="ovabrw-service-heading">
				<span class="ovabrw-required">
					<?php esc_html_e( 'Label', 'ova-brw' ); ?>
				</span>
				<?php ovabrw_wp_text_input([
					'type' 	=> 'text',
					'class' => 'ovabrw-input-required',
					'name' 	=> $this->get_meta_name( 'label_service[]' ),
					'value' => '[serviceName]'
				]); ?>
			</div>
			<div class="ovabrw-service-heading">
				<span class="ovabrw-required">
					<?php esc_html_e( 'Required', 'ova-brw' ); ?>
				</span>
				<?php ovabrw_wp_select_input([
					'class' 	=> 'ovabrw-input-required',
					'name' 		=> $this->get_meta_name( 'service_required[]' ),
					'value' 	=> 'no',
					'options' 	=> [
						'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
						'no' 	=> esc_html__( 'No', 'ova-brw' )
					]
				]); ?>
			</div>
		</div>
		<div class="ovabrw-service-head-right">
			<span class="ovabrw-sort-icon">
				<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
			</span>
			<span>
				<button class="button ovabrw-remove-service-group" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
			</span>
		</div>
	</div>
	<table class="widefat">
		<thead>
			<tr>
				<th class="ovabrw-required">
					<?php esc_html_e( 'Unique ID', 'ova-brw' ); ?>
				</th>
				<th class="ovabrw-required">
					<?php esc_html_e( 'Name', 'ova-brw' ); ?>
				</th>
				<th class="ovabrw-required">
					<?php esc_html_e( 'Price', 'ova-brw' ); ?>
				</th>
				<th><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></th>
				<th class="ovabrw-required">
					<?php esc_html_e( 'Applicable', 'ova-brw' ); ?>
				</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody class="ovabrw-sortable"></tbody>
		<tfoot>
			<tr>
				<th colspan="7">
					<button class="button ovabrw-add-service-group" >
						<?php esc_html_e( 'Add option', 'ova-brw' ); ?>
					</button>
				</th>
			</tr>
		</tfoot>
	</table>
</div>