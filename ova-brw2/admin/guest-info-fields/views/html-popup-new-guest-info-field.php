<?php defined( 'ABSPATH' ) || exit(); ?>

<form action="" method="post" id="ovabrw-popup-guest-info-form" enctype="multipart/form-data">
	<?php ovabrw_wp_text_input([
		'type' 	=> 'hidden',
		'name' 	=> 'ovabrw-guest-info-action',
		'value'	=> $action
	]); ?>
	<button class="popup-guest-info-close">X</button>
	<table width="100%">
		<tbody>
			<?php add_action( OVABRW_PREFIX.'popup_add_guest_info_before', $action, $type, $name ); ?>
			<tr class="type">
				<td class="label ovabrw-required">
					<?php esc_html_e( 'Select type', 'ova-brw' ); ?>
				</td>
				<td>
					<select name="type" id="type" class="ovabrw-input-required">
						<?php foreach ( $this->get_types() as $t => $v ): ?>
							<option value="<?php echo esc_attr( $t ); ?>"<?php ovabrw_selected( $t, $type ); ?>>
								<?php echo esc_html( $v ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<?php if ( 'tel' == $type ): ?>
				<tr class="label">
					<td class="label">
						<?php esc_html_e( 'Pattern', 'ova-brw' ); ?>
					</td>
					<td>
						<?php ovabrw_wp_text_input([
							'name' 			=> 'pattern',
							'placeholder' 	=> esc_html__( '[0-9]{3}-[0-9]{3}-[0-9]{4}', 'ova-brw' ),
							'attrs' 		=> [
								'autocomplete' => 'off'
							]
						]); ?>
						<span>
							<em>
								<strong>
									<?php esc_html_e( 'Example:', 'ova-brw' ); ?>
								</strong>
								<?php esc_html_e( '[0-9]{3}-[0-9]{3}-[0-9]{4}', 'ova-brw' ); ?>
							</em>
						</span>
					</td>
				</tr>
			<?php endif;

			// input file
			if ( 'file' == $type ): ?>
				<tr class="label">
					<td class="label">
						<?php esc_html_e( 'Allowed file types', 'ova-brw' ); ?>
					</td>
					<td>
						<?php ovabrw_wp_text_input([
							'class' 		=> 'ovabrw-input-required',
							'name' 			=> 'accept',
							'value' 		=> '.jpg, .jpeg, .png, .pdf, .doc, .docx',
							'placeholder' 	=> esc_html__( '.jpg, .jpeg, .png, .pdf, .doc, .docx', 'ova-brw' ),
							'attrs' 		=> [
								'autocomplete' => 'off'
							]
						]); ?>
						<span>
							<em>
								<strong>
									<?php esc_html_e( 'Example:', 'ova-brw' ); ?>
								</strong>
								<?php esc_html_e( '.jpg, .jpeg, .png, .pdf, .doc, .docx', 'ova-brw' ); ?>
							</em>
						</span>
						<br>
						<span>
							<em>
								<strong>
									<?php esc_html_e( 'Images:', 'ova-brw' ); ?>
								</strong>
								<?php esc_html_e( '.jpg, .jpeg, .png, .gif, .ico, .webp', 'ova-brw' ); ?>
							</em>
						</span>
						<br>
						<span>
							<em>
								<strong>
									<?php esc_html_e( 'Documents:', 'ova-brw' ); ?>
								</strong>
								<?php esc_html_e( '.pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx, .PSD, .XML', 'ova-brw' ); ?>
							</em>
						</span>
						<br>
						<span>
							<em>
								<strong>
									<?php esc_html_e( 'Audio:', 'ova-brw' ); ?>
								</strong>
								<?php esc_html_e( '.mp3, .m4a, .ogg, .wav', 'ova-brw' ); ?>
							</em>
						</span>
						<br>
						<span>
							<em>
								<strong>
									<?php esc_html_e( 'Video:', 'ova-brw' ); ?>
								</strong>
								<?php esc_html_e( '.mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2', 'ova-brw' ); ?>
							</em>
						</span>
					</td>
				</tr>
				<tr class="label">
					<td class="label">
						<?php esc_html_e( 'Max size', 'ova-brw' ); ?>
					</td>
					<td>
						<?php ovabrw_wp_text_input([
							'name' 			=> 'max_size',
							'placeholder' 	=> 10,
							'attrs' 		=> [
								'autocomplete' => 'off'
							]
						]); ?>
						<span>
							<em>
								<?php esc_html_e( 'Unit: MB (megabyte)', 'ova-brw' ); ?>
							</em>
						</span>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="name">
				<td class="label ovabrw-required">
					<?php esc_html_e( 'Name', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'class' 		=> 'ovabrw-input-required',
						'name' 			=> 'name',
						'value' 		=> $name,
						'placeholder' 	=> esc_html__( 'enter name', 'ova-brw' ),
						'attrs' 		=> [
							'autocomplete' => 'off'
						]
					]); ?>
					<span>
						<em>
							<?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
						</em>
					</span>
				</td>
			</tr>
			<tr class="label">
				<td class="label ovabrw-required">
					<?php esc_html_e( 'Label', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'class' 		=> 'ovabrw-input-required',
						'name' 			=> 'label',
						'placeholder' 	=> esc_html__( 'enter label', 'ova-brw' ),
						'attrs' 		=> [
							'autocomplete' => 'off'
						]
					]); ?>
				</td>
			</tr>
			<tr class="label">
				<td class="label">
					<?php esc_html_e( 'Description', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_textarea([
						'name' 			=> 'description',
						'placeholder' 	=> esc_html__( 'enter your text here...', 'ova-brw' )
					]); ?>
				</td>
			</tr>
			<?php if ( !in_array( $type, ['radio', 'checkbox', 'file'] ) ): ?>
				<tr class="label">
					<td class="label">
						<?php esc_html_e( 'Placeholder', 'ova-brw' ); ?>
					</td>
					<td>
						<?php ovabrw_wp_text_input([
							'name' 			=> 'placeholder',
							'placeholder' 	=> esc_html__( 'enter placeholder', 'ova-brw' ),
							'attrs' 		=> [
								'autocomplete' => 'off'
							]
						]); ?>
					</td>
				</tr>
			<?php endif;

			// Radio, checkbox, select
			if ( in_array( $type, ['radio', 'checkbox', 'select'] ) ): ?>
				<tr class="radio-options ovabrw-guest-info-options">
					<td class="label">
						<?php esc_html_e( 'Options(*)', 'ova-brw' ); ?>
					</td>
					<td>
						<table class="widefat" width="100%">
							<thead>
								<tr>
									<th class="ovabrw-required" width="30%">
										<?php esc_html_e( 'Option ID', 'ova-brw' ); ?>
									</th>
									<th class="ovabrw-required" width="30%">
										<?php esc_html_e( 'Name', 'ova-brw' ); ?>
									</th>
									<th width="30%">
										<?php esc_html_e( 'Max Quantity', 'ova-brw' ); ?>
									</th>
									<th width="10%">
										<?php esc_html_e( 'Actions', 'ova-brw' ); ?>
									</th>
								</tr>
							</thead>
							<tbody class="ovabrw-guest-info-option-sortable">
								<tr>
									<td>
										<?php ovabrw_wp_text_input([
											'class' 		=> 'ovabrw-input-required',
											'name' 			=> 'option_ids[]',
											'value' 		=> 'option_1',
											'placeholder' 	=> esc_html__( 'text', 'ova-brw' ),
											'attrs' 		=> [
												'autocomplete' => 'off'
											]
										]); ?>
									</td>
									<td>
										<?php ovabrw_wp_text_input([
											'class' 		=> 'ovabrw-input-required',
											'name' 			=> 'option_names[]',
											'placeholder' 	=> esc_html__( 'text', 'ova-brw' ),
											'attrs' 		=> [
												'autocomplete' => 'off'
											]
										]); ?>
									</td>
									<td>
										<?php ovabrw_wp_text_input([
											'type' 			=> 'number',
											'name' 			=> 'option_qtys[]',
											'placeholder' 	=> esc_html__( 'number', 'ova-brw' ),
											'attrs' 		=> [
												'min' 			=> 0,
												'autocomplete' 	=> 'off'
											]
										]); ?>
									</td>
									<td>
										<span class="btn btn-add-new" title="<?php esc_attr_e( 'Add', 'ova-brw' ); ?>">+</span>
										<span class="btn btn-add-remove" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">x</span>
										<span class="dashicons dashicons-menu-alt3" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="4">
										<button class="button ovabrw-guest-info-add-option">
											<?php esc_html_e( 'Add option', 'ova-brw' ); ?>
										</button>
									</th>
								</tr>
							</tfoot>
						</table>
						<span>
							<em>
								<strong>
									<?php esc_html_e( 'Option ID:', 'ova-brw' ); ?>
								</strong>
								<?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
							</em>
						</span>
						<br>
						<span>
							<em>
								<strong>
									<?php esc_html_e( 'Max Quantity:', 'ova-brw' ); ?>
								</strong>
								<?php esc_html_e( 'Maximum quantity selectable', 'ova-brw' ); ?>
							</em>
						</span>
					</td>
				</tr>
			<?php endif; // END

			if ( 'file' != $type ): ?>
				<tr class="default">
					<td class="label">
						<?php esc_html_e( 'Default', 'ova-brw' ); ?>
					</td>
					<td>
						<?php if ( 'date' == $type ) {
							// Get datepicker
							$datepicker = ovabrw_admin_datepicker_options();

							// Min year
							$datepicker['AmpPlugin']['dropdown']['minYear'] = apply_filters( OVABRW_PREFIX.'guest_info_min_year', gmdate('Y') - 125 );

							// Max year
							$datepicker['AmpPlugin']['dropdown']['maxYear'] = apply_filters( OVABRW_PREFIX.'guest_info_max_year', gmdate('Y') + 3 );

							// Min date
							$datepicker['LockPlugin']['minDate'] = apply_filters( OVABRW_PREFIX.'guest_info_min_date', '' );

							// Max date
							$datepicker['LockPlugin']['maxDate'] = apply_filters( OVABRW_PREFIX.'guest_info_max_date', '' );

							ovabrw_wp_text_input([
								'id' 		=> ovabrw_unique_id( 'guest_info_date' ),
								'name' 		=> 'default',
								'data_type' => 'datepicker'
							]);

							// Datepicker
							ovabrw_wp_text_input([
								'type' 	=> 'hidden',
								'name' 	=> 'ovabrw-datepicker-options',
								'value' => wp_json_encode( $datepicker )
							]);
						} elseif ( 'number' == $type ) {
							ovabrw_wp_text_input([
								'type' 			=> 'number',
								'name' 			=> 'default',
								'placeholder' 	=> 10,
								'attrs' 		=> [
									'step' 			=> 'any',
									'autocomplete' 	=> 'off'
								]
							]);
						} elseif ( 'tel' == $type ) {
							ovabrw_wp_text_input([
								'type' 			=> 'tel',
								'name' 			=> 'default',
								'placeholder' 	=> esc_html__( 'enter phone number', 'ova-brw' ),
								'attrs' 		=> [
									'autocomplete' => 'off'
								]
							]);
						} elseif ( 'email' == $type ) {
							ovabrw_wp_text_input([
								'type' 			=> 'email',
								'name' 			=> 'default',
								'placeholder' 	=> esc_html__( 'your_email@gmail.com', 'ova-brw' ),
								'attrs' 		=> [
									'autocomplete' => 'off'
								]
							]);
						} else {
							ovabrw_wp_text_input([
								'name' 			=> 'default',
								'placeholder' 	=> esc_html__( 'enter text value', 'ova-brw' ),
								'attrs' 		=> [
									'autocomplete' => 'off'
								]
							]);
						} ?>
					</td>
				</tr>
			<?php endif;

			// Date
			if ( 'date' == $type ): ?>
				<tr class="min">
					<td class="label">
						<?php esc_html_e( 'Min date', 'ova-brw' ); ?>
					</td>
					<td>
						<?php ovabrw_wp_text_input([
							'id' 		=> ovabrw_unique_id( 'guest_info_min_date' ),
							'name' 		=> 'min',
							'data_type' => 'datepicker'
						]); ?>
					</td>
				</tr>
				<tr class="max">
					<td class="label">
						<?php esc_html_e( 'Max date', 'ova-brw' ); ?>
					</td>
					<td>
						<?php ovabrw_wp_text_input([
							'id' 		=> ovabrw_unique_id( 'guest_info_max_date' ),
							'name' 		=> 'max',
							'data_type' => 'datepicker'
						]); ?>
					</td>
				</tr>
			<?php endif;

			// Number
			if ( 'number' == $type ): ?>
				<tr class="min">
					<td class="label">
						<?php esc_html_e( 'Min', 'ova-brw' ); ?>
					</td>
					<td>
						<?php ovabrw_wp_text_input([
							'type' 			=> 'number',
							'name' 			=> 'min',
							'placeholder' 	=> 1,
							'attrs' 		=> [
								'step' 			=> 'any',
								'autocomplete' 	=> 'off'
							]
						]); ?>
					</td>
				</tr>
				<tr class="max">
					<td class="label">
						<?php esc_html_e( 'Max', 'ova-brw' ); ?>
					</td>
					<td>
						<?php ovabrw_wp_text_input([
							'type' 			=> 'number',
							'name' 			=> 'max',
							'placeholder' 	=> 100000,
							'attrs' 		=> [
								'step' 			=> 'any',
								'autocomplete' 	=> 'off'
							]
						]); ?>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="class">
				<td class="label">
					<?php esc_html_e( 'Class', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'name' 			=> 'class',
						'placeholder' 	=> esc_html__( 'enter class name', 'ova-brw' ),
						'attrs' 		=> [
							'autocomplete' => 'off'
						]
					]); ?>
				</td>
			</tr>
			<?php add_action( OVABRW_PREFIX.'popup_add_guest_info_after', $action, $type, $name ); ?>
			<tr class="status">
				<td class="label"></td>
				<td>
					<label>
						<?php ovabrw_wp_text_input([
							'type' 	=> 'checkbox',
							'name' 	=> 'required',
							'value' => 'on'
						]);

						esc_html_e( 'Required', 'ova-brw' ); ?>
					</label>
					<label>
						<?php ovabrw_wp_text_input([
							'type' 		=> 'checkbox',
							'name' 		=> 'enable',
							'value' 	=> 'on',
							'checked' 	=> true
						]);

						esc_html_e( 'Enable', 'ova-brw' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="ovabrw-guest-info-submit">
		<button type="submit" class="button button-primary">
			<?php esc_html_e( 'Save', 'ova-brw' ); ?>
		</button>
		<div class="ovabrw-loading">
			<span class="dashicons dashicons-update-alt"></span>
		</div>
	</div>
</form>