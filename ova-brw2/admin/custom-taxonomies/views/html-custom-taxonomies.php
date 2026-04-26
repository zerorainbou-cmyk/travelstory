<?php if ( !defined( 'ABSPATH' ) ) exit();

// Custom taxonomies
$taxonomies = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

?>

<div class="wrap ovabrw-custom-taxonomies">
	<h2><?php esc_html_e( 'Custom Taxonomies', 'ova-brw' ); ?></h2>
	<div class="heading">
		<div class="ovabrw-taxonomy-action">
			<button type="button" class="button" name="ovabrw-taxonomy-action" value="add" title="<?php esc_attr_e( 'Add taxonomy', 'ova-brw' ); ?>">
				<?php esc_html_e( 'Add taxonomy', 'ova-brw' ); ?>
			</button>
			<button type="submit" class="button" name="ovabrw-taxonomy-action" value="enable" title="<?php esc_attr_e( 'Enable', 'ova-brw' ); ?>">
				<?php esc_html_e( 'Enable', 'ova-brw' ); ?>
			</button>
			<button type="submit" class="button" name="ovabrw-taxonomy-action" value="disable" title="<?php esc_attr_e( 'Disable', 'ova-brw' ); ?>">
				<?php esc_html_e( 'Disable', 'ova-brw' ); ?>
			</button>
			<button type="submit" class="button" name="ovabrw-taxonomy-action" value="show" title="<?php esc_attr_e( 'Show in listing', 'ova-brw' ); ?>">
				<?php esc_html_e( 'Show in listing', 'ova-brw' ); ?>
			</button>
			<button type="submit" class="button" name="ovabrw-taxonomy-action" value="hide" title="<?php esc_attr_e( 'Hide in listing', 'ova-brw' ); ?>">
				<?php esc_html_e( 'Hide in listing', 'ova-brw' ); ?>
			</button>
			<button type="submit" class="button" name="ovabrw-taxonomy-action" value="delete" title="<?php esc_attr_e( 'Delete', 'ova-brw' ); ?>">
				<?php esc_html_e( 'Delete', 'ova-brw' ); ?>
			</button>
			<div class="ovabrw-loading">
				<span class="dashicons dashicons-update-alt"></span>
			</div>
		</div>
	</div>
	<div class="content">
		<?php if ( ovabrw_array_exists( $taxonomies ) ): ?>
			<table class="widefat fixed">
				<thead>
					<tr>
						<td class="check-column">
							<input type="checkbox" class="ovabrw-check-all">
						</td>
						<th class="slug">
							<?php esc_html_e( 'Slug', 'ova-brw' ); ?>
						</th>
						<th class="name">
							<?php esc_html_e( 'Name', 'ova-brw' ); ?>
						</th>
						<th class="singular_name">
							<?php esc_html_e( 'Singular name', 'ova-brw' ); ?>
						</th>
						<th class="label">
							<?php esc_html_e( 'Label', 'ova-brw' ); ?>
						</th>
						<th class="manage">
							<?php esc_html_e( 'Manage', 'ova-brw' ); ?>
						</th>
						<th class="enable">
							<?php esc_html_e( 'Enable', 'ova-brw' ); ?>
						</th>
						<th class="status">
	                        <?php esc_html_e( 'Show in Listing', 'ova-brw' ); ?>
	                    </th> 
						<th class="actions">
							<?php esc_html_e( 'Actions', 'ova-brw' ); ?>
						</th>
					</tr>
				</thead>
				<tbody class="ovabrw-taxonomy-sortable">
					<?php foreach ( $taxonomies as $slug => $tax ):
						// Get terms
						$terms = get_terms([
                        	'taxonomy' 		=> $slug,
                            'hide_empty' 	=> false
                        ]);
					?>
						<?php if ( 'on' === $tax['enabled'] ): ?>
							<tr>
						<?php else: ?>
							<tr class="disabled">
						<?php endif; ?>
							<th class="ovabrw-check-column">
								<?php ovabrw_wp_text_input([
									'type' 	=> 'checkbox',
									'name' 	=> 'slug[]',
									'value' => $slug
								]); ?>
							</th>
							<td><?php echo esc_html( $slug ); ?></td>
							<td>
								<?php echo esc_html( $tax['name'] ); ?>
							</td>
							<td><?php echo esc_html( $tax['singular_name'] ); ?></td>
							<td><?php echo esc_html( $tax['label_frontend'] ); ?></td>
							<td>
								<a href="<?php echo esc_url( admin_url( 'edit-tags.php?post_type=product&taxonomy='.sanitize_file_name( $slug ) ) ); ?>" title="<?php esc_html_e( 'Add/Update value of taxonomy', 'ova-brw' ); ?>">
	                                <i class="dashicons dashicons-category"></i>
	                                (<?php echo !is_wp_error( $terms ) ? count( $terms ) : 0; ?>)    
	                            </a>
							</td>
							<td class="enable">
								<?php if ( 'on' === $tax['enabled'] ): ?>
									<span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>"></span>
								<?php else: ?>
									<span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>" style="display: none;"></span>
								<?php endif; ?>
							</td>
							<td class="show-listing">
								<?php if ( 'on' === $tax['show_listing'] ): ?>
									<span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>"></span>
								<?php else: ?>
									<span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>" style="display: none;"></span>
								<?php endif; ?>
							</td>
							<td>
								<div class="actions">
									<span class="dashicons dashicons-edit" title="<?php esc_attr_e( 'Edit', 'ova-brw' ); ?>"></span>
									<span>|</span>
									<?php if ( $tax['enabled'] ): ?>
										<span class="dashicons dashicons-visibility active" title="<?php esc_attr_e( 'Disable', 'ova-brw' ); ?>"></span>
										<span class="dashicons dashicons-hidden" title="<?php esc_attr_e( 'Enable', 'ova-brw' ); ?>"></span>
									<?php else: ?>
										<span class="dashicons dashicons-visibility" title="<?php esc_attr_e( 'Disable', 'ova-brw' ); ?>"></span>
										<span class="dashicons dashicons-hidden active" title="<?php esc_attr_e( 'Enable', 'ova-brw' ); ?>"></span>
									<?php endif; ?>
									<span>|</span>
									<span class="dashicons dashicons-trash" title="<?php esc_attr_e( 'Delete', 'ova-brw' ); ?>"></span>
									<span>|</span>
									<span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
									<div class="ovabrw-loading">
										<span class="dashicons dashicons-update-alt"></span>
									</div>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td class="check-column">
							<input type="checkbox" class="ovabrw-check-all">
						</td>
						<th class="slug">
							<?php esc_html_e( 'Slug', 'ova-brw' ); ?>
						</th>
						<th class="name">
							<?php esc_html_e( 'Name', 'ova-brw' ); ?>
						</th>
						<th class="singular_name">
							<?php esc_html_e( 'Singular name', 'ova-brw' ); ?>
						</th>
						<th class="label">
							<?php esc_html_e( 'Label', 'ova-brw' ); ?>
						</th>
						<th class="manage">
							<?php esc_html_e( 'Manage', 'ova-brw' ); ?>
						</th>
						<th class="enable">
							<?php esc_html_e( 'Enable', 'ova-brw' ); ?>
						</th>
						<th class="status">
	                        <?php esc_html_e( 'Show in Listing', 'ova-brw' ); ?>
	                    </th> 
						<th class="actions">
							<?php esc_html_e( 'Actions', 'ova-brw' ); ?>
						</th>
					</tr>
				</tfoot>
			</table>
		<?php endif; ?>
	</div>
	<div class="ovabrw-popup-taxonomies">
		<div class="ovabrw-popup-taxonomy-field"></div>
	</div>
</div>