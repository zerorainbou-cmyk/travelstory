<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get latitude
$latitude = $this->get_meta_value( 'latitude' );
if ( !$latitude ) $latitude = ovabrw_get_setting( 'latitude_map_default', 39.177972 );

// Get longitude
$longitude = $this->get_meta_value( 'longitude' );
if ( !$longitude ) $longitude = ovabrw_get_setting( 'longitude_map_default', -100.36375 );

?>

<div id="ovabrw-options-place" class="ovabrw-advanced-settings" style="border: none;">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Place', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<?php do_action( $this->prefix.'before_map_content', $this ); ?>
		<div class="ovabrw-form-field ovabrw-map">
			<?php
				// Address
				woocommerce_wp_text_input([
					'id' 				=> 'pac-input',
					'class' 			=> 'controls',
					'label'				=> '',
					'placeholder'		=> esc_html__( 'Enter a venue', 'ova-brw' ),
					'type' 				=> 'text',
					'value' 			=> $this->get_meta_value( 'address' ),
					'custom_attributes' => [
						'autocomplete' 	=> 'off',
						'autocorrect'	=> 'off',
						'autocapitalize'=> 'none'
					]
				]);
			?>
			<div id="admin_show_map" data-zoom="<?php echo esc_attr( ovabrw_get_setting( 'google_map_zoom', 17 ) ); ?>"></div>
			<div id="map_info">
			<?php
				woocommerce_wp_text_input([
					'type' 				=> 'text',
					'id' 				=> $this->get_meta_name( 'address' ),
					'class' 			=> 'address',
					'label' 			=> esc_html__( 'Address', 'ova-brw' ),
					'value' 			=> $this->get_meta_value( 'address' ),
					'custom_attributes' => [
						'autocomplete' 	=> 'nope',
						'autocorrect'	=> 'off',
						'autocapitalize'=> 'none'
					]
				]);
				woocommerce_wp_text_input([
					'type' 				=> 'text',
					'id' 				=> $this->get_meta_name( 'latitude' ),
					'class' 			=> 'latitude',
					'label' 			=> esc_html__( 'Latitude', 'ova-brw' ),
					'value' 			=> $latitude,
					'custom_attributes' => [
						'autocomplete' 	=> 'nope',
						'autocorrect'	=> 'off',
						'autocapitalize'=> 'none'
					]
				]);
				woocommerce_wp_text_input([
					'type' 				=> 'text',
					'id' 				=> $this->get_meta_name( 'longitude' ),
					'class' 			=> 'longitude',
					'label' 			=> esc_html__( 'Longitude', 'ova-brw' ),
					'value' 			=> $longitude,
					'custom_attributes' => [
						'autocomplete' 	=> 'nope',
						'autocorrect'	=> 'off',
						'autocapitalize'=> 'none'
					]
				]);
			?>
			</div>
		</div>
		<?php do_action( $this->prefix.'after_map_content', $this ); ?>
	</div>
</div>