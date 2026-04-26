<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get import calendar links
$import_calendar_links = $this->get_meta_value( 'import_calendar_links' );

?>

<div id="options-sync-calendar" class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label"><?php esc_html_e( 'Sync order', 'ova-brw' ); ?></h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_sync_calendar_content', $this ); ?>
		<p><?php esc_html_e( 'Sync orders arcoss multiple platforms.', 'ova-brw' ); ?></p>
		<p class="form-field ovabrw-export-calendar-link">
			<label for="ovabrw-export-calendar-link">
				<?php esc_html_e( 'Export calendar link', 'ova-brw' ); ?>
			</label>
			<input
				type="text"
				id="ovabrw-export-calendar-link"
				class="short"
				name="ovabrw_export_calendar_link"
				value="<?php echo esc_url( OVABRW()->options->get_ical_url( $this->get_id() ) ); ?>"
				readonly
			/>
			<span class="button ovabrw-copy-link"><?php esc_html_e( 'Copy', 'ova-brw' ); ?></span>
			<span class="description">
				<?php esc_html_e( 'Add this link to the multiple platforms.', 'ova-brw' ); ?>
			</span>
		</p>
		<?php if ( 'yes' === ovabrw_get_option( 'enable_sync_calendar', 'no' ) ): ?>
			<p class="form-field ovabrw-import-calendar-link">
				<label for="">
					<?php esc_html_e( 'Import calendar link', 'ova-brw' ); ?>
				</label>
				<span class="calendar-links">
					<?php if ( ovabrw_array_exists( $import_calendar_links ) ):
						foreach ( $import_calendar_links as $url ): ?>
							<span class="item">
								<input
									type="text"
									class="short"
									name="ovabrw_import_calendar_links[]"
									value="<?php echo esc_url( $url ); ?>"
								/>
								<span class="button ovabrw-add-calendar-link" title="<?php esc_attr_e( 'Add link', 'ova-brw' ); ?>">
									<span class="dashicons dashicons-plus-alt2"></span>
								</span>
								<span class="button ovabrw-remove-calendar-link" title="<?php esc_attr_e( 'Remove link', 'ova-brw' ); ?>">
									<span class="dashicons dashicons-minus"></span>
								</span>
								<span class="button ovabrw-update-calendar-link" title="<?php esc_attr_e( 'Update', 'ova-brw' ); ?>">
									<span class="dashicons dashicons-update-alt"></span>
								</span>
							</span>
						<?php endforeach;
					else: ?>
						<span class="item">
							<input
								type="text"
								class="short"
								name="ovabrw_import_calendar_links[]"
								value=""
							/>
							<span class="button ovabrw-add-calendar-link" title="<?php esc_attr_e( 'Add link', 'ova-brw' ); ?>">
								<span class="dashicons dashicons-plus-alt2"></span>
							</span>
							<span class="button ovabrw-remove-calendar-link" title="<?php esc_attr_e( 'Remove link', 'ova-brw' ); ?>">
								<span class="dashicons dashicons-minus"></span>
							</span>
							<span class="button ovabrw-update-calendar-link" title="<?php esc_attr_e( 'Update', 'ova-brw' ); ?>">
								<span class="dashicons dashicons-update-alt"></span>
							</span>
						</span>
					<?php endif; ?>
				</span>
				<span class="description">
					<?php esc_html_e( 'Get a link ending in .ics from the other platform and add it above.', 'ova-brw' ); ?>
				</span>
			</p>
		<?php endif; ?>
		<?php do_action( $this->prefix.'after_sync_calendar_content', $this ); ?>
	</div>
</div>