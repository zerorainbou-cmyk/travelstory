<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get feature labels
$feature_labels = $this->get_meta_value( 'features_label' );

?>

<div id="ovabrw-options-features" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label"><?php esc_html_e( 'Features', 'ova-brw' ); ?></h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_features_content', $this ); ?>
		<div class="ovabrw-table">
			<table class="widefat">
				<thead>
					<tr>
						<?php if ( apply_filters( OVABRW_PREFIX.'show_icon_features', true ) ): ?>
							<th><?php esc_html_e( 'Icon class', 'ova-brw' ); ?></th>
						<?php endif; ?>
						<th><?php esc_html_e( 'Label', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Description', 'ova-brw' ); ?></th>
						<th>
							<?php esc_html_e( 'Feature', 'ova-brw' ); ?>
							<?php echo wc_help_tip( esc_html__( 'Display field in Card Template (Listing Product)', 'ova-brw' ) ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Special', 'ova-brw' ); ?>
							<?php echo wc_help_tip( esc_html__( 'Display field at highlight position of Card Template and Product Detail', 'ova-brw' ) ); ?>
						</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody class="ovabrw-sortable">
					<?php if ( ovabrw_array_exists( $feature_labels ) ):
						$features_icons 	= $this->get_meta_value( 'features_icons' );
						$features_desc 		= $this->get_meta_value( 'features_desc' );
						$features_special 	= $this->get_meta_value( 'features_special' );
						$features_featured 	= $this->get_meta_value( 'features_featured' );

						foreach ( $feature_labels as $i => $label ): ?>
							<tr>
								<?php if ( apply_filters( OVABRW_PREFIX.'show_icon_features', true ) ): ?>
								    <td width="30%">
								    	<?php ovabrw_wp_text_input([
											'type' 			=> 'text',
											'name' 			=> $this->get_meta_name( 'features_icons[]' ),
											'value' 		=> ovabrw_get_meta_data( $i, $features_icons ),
											'placeholder' 	=> esc_html__( 'icon class', 'ova-brw' )
										]); ?>
								    </td>
							    <?php endif; ?>
							    <td width="20%">
							    	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'name' 			=> $this->get_meta_name( 'features_label[]' ),
										'value' 		=> $label,
										'placeholder' 	=> esc_html__( 'Label', 'ova-brw' )
									]); ?>
							    </td>
							    <td width="<?php echo apply_filters( OVABRW_PREFIX.'show_icon_features', 'true' ) ? esc_attr( '28%' ) : esc_attr( '58%' ); ?>">
							      	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'name' 			=> $this->get_meta_name( 'features_desc[]' ),
										'value' 		=> ovabrw_get_meta_data( $i, $features_desc ),
										'placeholder' 	=> esc_html__( 'Description', 'ova-brw' )
									]); ?>
							    </td>
							    <td width="10%">
							    	<?php ovabrw_wp_select_input([
							    		'name' 		=> $this->get_meta_name( 'features_special[]' ),
							    		'value' 	=> ovabrw_get_meta_data( $i, $features_special ),
							    		'options' 	=> [
							    			'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
							    			'no' 	=> esc_html__( 'No', 'ova-brw' )
							    		]
							    	]); ?>
							    </td>
							    <td width="10%">
							    	<?php ovabrw_wp_select_input([
							    		'name' 		=> $this->get_meta_name( 'features_featured[]' ),
							    		'value' 	=> ovabrw_get_meta_data( $i, $features_featured ),
							    		'options' 	=> [
							    			'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
							    			'no' 	=> esc_html__( 'No', 'ova-brw' )
							    		]
							    	]); ?>
							    </td>
						    	<td width="1%" class="ovabrw-sort-icon">
									<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
								</td>
								<td width="1%">
									<button class="button ovabrw-remove-feature" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
								</td>
							</tr>
					<?php endforeach;
					endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="7">
							<button class="button ovabrw-add-feature" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-feature-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add feature', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php do_action( $this->prefix.'after_features_content', $this ); ?>
	</div>
</div>