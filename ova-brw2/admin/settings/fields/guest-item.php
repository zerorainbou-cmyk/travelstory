<?php if ( !defined( 'ABSPATH' ) ) exit;

// Guest information
$guest_info = ovabrw_get_option( 'guest_info' );

?>
<div class="ovabrw-guest-item">
	<table class="form-table">
		<tbody>
			<tr class="ovabrw-guest-label ovabrw-required">
				<th scope="row" class="titledesc">
					<label>
						<?php esc_html_e( 'Label', 'ova-brw' ); ?>
						<?php echo wc_help_tip( esc_html__( 'The label of the guest.', 'ova-brw' ) ); ?>
					</label>
				</th>
				<td class="forminp forminp-text">
					<input
						type="text"
						name=""
						class="guest-option guest-label"
						data-name="ovabrw_guest_options[ovabrw-index][label]"
						autocomplete="off"
						required
					/>
				</td>
			</tr>
			<tr class="ovabrw-guest-name ovabrw-required">
				<th scope="row" class="titledesc">
					<label>
						<?php esc_html_e( 'Name', 'ova-brw' ); ?>
						<?php echo wc_help_tip( esc_html__( 'This name field will be used to set up the price for guests in each product.', 'ova-brw' ) ); ?>
					</label>
				</th>
				<td class="forminp forminp-text">
					<input
						type="text"
						name="ovabrw_guest_options[ovabrw-index][name]"
						class="guest-option guest-name"
						placeholder="<?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>"
						data-name="ovabrw_guest_options[ovabrw-index][name]"
						autocomplete="off"
						required
					/>
				</td>
			</tr>
			<tr class="ovabrw-guest-description">
				<th scope="row" class="titledesc">
					<label>
						<?php esc_html_e( 'Description', 'ova-brw' ); ?>
						<?php echo wc_help_tip( esc_html__( 'The description of the guest.', 'ova-brw' ) ); ?>
					</label>
				</th>
				<td class="forminp forminp-textarea">
					<textarea name="ovabrw_guest_options[ovabrw-index][desc]" class="guest-option guest-description" rows="3" data-name="ovabrw_guest_options[ovabrw-index][desc]"></textarea>
				</td>
			</tr>
			<tr class="ovabrw-guest-required-price">
				<th scope="row" class="titledesc">
					<label>
						<?php esc_html_e( 'Mandatory price field', 'ova-brw' ); ?>
						<?php echo wc_help_tip( esc_html__( 'When you add a new product, you must enter the price for guests.', 'ova-brw' ) ); ?>
					</label>
				</th>
				<td class="forminp forminp-text">
					<input
						type="checkbox"
						name="ovabrw_guest_options[ovabrw-index][required_price]"
						class="guest-option guest-required-price"
						value="yes"
						data-name="ovabrw_guest_options[ovabrw-index][required_price]"
					/>
				</td>
			</tr>
			<tr class="ovabrw-guest-show-price">
				<th scope="row" class="titledesc">
					<label>
						<?php esc_html_e( 'Show price beside', 'ova-brw' ); ?>
						<?php echo wc_help_tip( esc_html__( 'Show price beside the guest label.', 'ova-brw' ) ); ?>
					</label>
				</th>
				<td class="forminp forminp-text">
					<input
						type="checkbox"
						name="ovabrw_guest_options[ovabrw-index][show_price]"
						class="guest-option guest-show-price"
						value="yes"
						data-name="ovabrw_guest_options[ovabrw-index][show_price]"
					/>
				</td>
			</tr>
			<?php if ( 'yes' == $guest_info ):
				// Guest fields
				$guest_fields = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'guest_fields', [] ) );
			?>
				<tr class="ovabrw-guest-info">
					<th scope="row" class="titledesc">
						<label>
							<?php esc_html_e( 'Information fields', 'ova-brw' ); ?>
							<?php echo wc_help_tip( esc_html__( 'The specific information fields for each guest in the booking or enquiry forms.', 'ova-brw' ) ); ?>
						</label>
					</th>
					<td class="forminp forminp-multiselect">
						<select name="ovabrw_guest_options[ovabrw-index][info_fields][]" class="guest-option ovabrw-select2" data-placeholder="<?php esc_attr_e( 'Select fields...', 'ova-brw' ); ?>" data-name="ovabrw_guest_options[ovabrw-index][info_fields][]" multiple>
							<?php foreach ( $guest_fields as $name => $fields ): ?>
								<option value="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $fields['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endif;

			do_action( OVABRW_PREFIX.'admin_field_guest_option_item' ); ?>
		</tbody>
	</table>
	<span class="ovabrw-sort-guest-item dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
	<span class="ovabrw-remove-guest-item dashicons dashicons-no-alt" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>"></span>
</div>