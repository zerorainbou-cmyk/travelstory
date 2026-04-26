<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="ovabrw-form-field">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Guests', 'ova-brw' ); ?>
	</strong>
	<?php 
		woocommerce_wp_checkbox([
			'id' 			=> $this->get_meta_name( 'show_guests' ),
			'value' 		=> $this->get_meta_value( 'show_guests' ),
			'cbvalue' 		=> 'yes',
			'label' 		=> esc_html__( 'Show guests', 'ova-brw' ),
			'desc_tip' 		=> true,
			'description' 	=> esc_html__( 'Show the guests fields.', 'ova-brw' )
		]);
		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'guest_type' ),
			'wrapper_class' => 'ovabrw-hidden ovabrw-radios',
			'value' 		=> $this->get_meta_value( 'guest_type', 'all' ),
			'label' 		=> esc_html__( 'Types of guests', 'ova-brw' ),
			'desc_tip' 		=> true,
			'description' 	=> esc_html__( 'Choose specific guests. Note that changing this option may require you to re-enter the price for each guest type.', 'ova-brw' ),
			'options' 		=> [
				'all' 		=> esc_html__( 'All', 'ova-brw' ),
				'special' 	=> esc_html__( 'Local', 'ova-brw' )
			]
		]);

		// Get guest options
		$guest_options = OVABRW()->options->get_guest_options();

		// Get special guests
		$special_guests = $this->get_meta_value( 'special_guests', [] );
	?>
	<p class="form-field ovabrw_special_guests_field ovabrw-hidden ovabrw-required">
		<label for="ovabrw_special_guests">
			<?php esc_html_e( 'Select guests', 'ova-brw' ); ?>
		</label>
		<select
			name="<?php echo esc_attr( $this->get_meta_name( 'special_guests[]' ) ); ?>"
			id="ovabrw_special_guests"
			class="wc-enhanced-select-nostd"
			data-placeholder="<?php esc_html_e( 'Select guest...', 'ova-brw' ); ?>"
			multiple>
			<?php if ( ovabrw_array_exists( $guest_options ) ):
				foreach ( $guest_options as $guest ):
					// Get name
					$name = ovabrw_get_meta_data( 'name', $guest );

					// Get label
					$label = ovabrw_get_meta_data( 'label', $guest );
			?>
				<option value="<?php echo esc_attr( $name ); ?>"<?php ovabrw_selected( $name, $special_guests ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
				<?php endforeach;
			endif; ?>
		</select>
	</p>
	<?php if ( 'yes' === ovabrw_get_option( 'guest_info' ) ):
		// Guest info fields
		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'guest_info_fields' ),
			'value' 		=> $this->get_meta_value( 'guest_info_fields', 'all' ),
			'label' 		=> esc_html__( 'Guest info fields', 'ova-brw' ),
			'desc_tip' 		=> true,
			'description' 	=> esc_html__( 'Choose specific guest info fields.', 'ova-brw' ),
			'options' 		=> [
				'all' 	=> esc_html__( 'Global settings', 'ova-brw' ),
				'local' => esc_html__( 'Local', 'ova-brw' ),
				'none' 	=> esc_html__( 'None', 'ova-brw' )
			]
		]);

		// Get guest info fields
		$special_guest_fields 	= $this->get_meta_value( 'special_guest_fields' );
		$guest_info_fields 		= ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'guest_fields', [] ) );
	?>
	<p class="form-field ovabrw_special_guest_fields ovabrw-required">
		<label for="ovabrw_special_guest_fields">
			<?php esc_html_e( 'Select guest info fields', 'ova-brw' ); ?>
		</label>
		<select
			name="<?php echo esc_attr( $this->get_meta_name( 'special_guest_fields[]' ) ); ?>"
			id="ovabrw_special_guest_fields"
			class="wc-enhanced-select-nostd"
			data-placeholder="<?php esc_html_e( 'Select guest info field...', 'ova-brw' ); ?>"
			multiple>
			<?php if ( ovabrw_array_exists( $guest_info_fields ) ):
				foreach ( $guest_info_fields as $name => $field ):
					if ( !$field['enable'] ) continue;

					$label = ovabrw_get_meta_data( 'label', $field );
			?>
				<option value="<?php echo esc_attr( $name ); ?>"<?php ovabrw_selected( $name, $special_guest_fields ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; endif; ?>
		</select>
	</p>
	<?php endif; ?>
</div>