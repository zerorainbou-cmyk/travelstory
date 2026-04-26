<?php if ( !defined( 'ABSPATH' ) ) exit();

// Disable Week Days
$disable_weekdays = $this->get_meta_value( 'product_disable_week_day' );

if ( ovabrw_array_exists( $disable_weekdays ) ) {
	$disable_weekdays = str_replace( '0', '7', $disable_weekdays );
	$disable_weekdays = array_map( 'trim', $disable_weekdays );
} else {
	$disable_weekdays = '' != $disable_weekdays ? explode( ',', $disable_weekdays ) : [];
}

?>

<div class="ovabrw_rent_time_min_wrap">
	<?php if ( $this->is_type( 'day' ) ) {
		// Rent Day min
		woocommerce_wp_text_input([
			'id'            => $this->get_meta_name( 'rent_day_min' ),
		    'class'         => 'short ',
		    'label'         => esc_html__( 'Minimum booking days', 'ova-brw' ),
		    'placeholder'   => esc_html__( '1', 'ova-brw' ),
		    'desc_tip'      => 'true',
		    'value' 		=> $this->get_meta_value( 'rent_day_min' ),
		    'data_type' 	=> 'price'
		]);

		// Rent Day max
		woocommerce_wp_text_input([
			'id'            => $this->get_meta_name('rent_day_max'),
		    'class'         => 'short ',
		    'label'         => esc_html__( 'Maximum booking days', 'ova-brw' ),
		    'placeholder'   => esc_html__( '1', 'ova-brw' ),
		    'desc_tip'      => 'true',
		    'value' 		=> $this->get_meta_value('rent_day_max'),
		    'data_type' 	=> 'price'
		]);
	} elseif ( $this->is_type( 'hotel' ) ) {
		// Rent hotel min
		woocommerce_wp_text_input([
			'id'            => $this->get_meta_name( 'rent_day_min' ),
		    'class'         => 'short ',
		    'label'         => esc_html__( 'Minimum booking nights', 'ova-brw' ),
		    'placeholder'   => esc_html__( '1', 'ova-brw' ),
		    'desc_tip'      => 'true',
		    'value' 		=> $this->get_meta_value( 'rent_day_min' ),
		    'data_type' 	=> 'price'
		]);

		// Rent hotel max
		woocommerce_wp_text_input([
			'id'            => $this->get_meta_name( 'rent_day_max' ),
		    'class'         => 'short ',
		    'label'         => esc_html__( 'Maximum booking nights', 'ova-brw' ),
		    'placeholder'   => esc_html__( '1', 'ova-brw' ),
		    'desc_tip'      => 'true',
		    'value' 		=> $this->get_meta_value( 'rent_day_max' ),
		    'data_type' 	=> 'price'
		]);
	} elseif ( $this->is_type( 'hour' ) || $this->is_type( 'mixed' ) ) {
		// Rent Hour min
		woocommerce_wp_text_input([
			'id'            => $this->get_meta_name( 'rent_hour_min' ),
		    'class'         => 'short ',
		    'label'         => esc_html__( 'Minimum booking hours', 'ova-brw' ),
		    'placeholder'   => esc_html__( '1', 'ova-brw' ),
		    'desc_tip'      => 'true',
		    'value' 		=> $this->get_meta_value( 'rent_hour_min' ),
		    'data_type' 	=> 'price'
		]);

		// Rent Hour max
		woocommerce_wp_text_input([
			'id'            => $this->get_meta_name( 'rent_hour_max' ),
		    'class'         => 'short ',
		    'label'         => esc_html__( 'Maximum booking hours', 'ova-brw' ),
		    'placeholder'   => esc_html__( '1', 'ova-brw' ),
		    'desc_tip'      => 'true',
		    'value' 		=> $this->get_meta_value( 'rent_hour_max' ),
		    'data_type' 	=> 'price'
		]);
	}

	// Time between 2 leases
	if ( $this->is_type( 'day' ) || $this->is_type( 'transportation' ) ) {
		woocommerce_wp_text_input([
			'id' 			=> $this->get_meta_name( 'prepare_vehicle_day' ),
			'class' 		=> 'short',
			'label' 		=> esc_html__( 'Preparation days', 'ova-brw' ),
			'desc_tip' 		=> 'true',
			'description' 	=> esc_html__( 'Car delivered (rental ends): 01/01/2024, set 1 day to prepare → 02/01/2024 blocked, Next available date becomes 03/01/2024', 'ova-brw' ),
			'placeholder' 	=> '0',
			'value' 		=> $this->get_meta_value( 'prepare_vehicle_day' ),
			'data_type' 	=> 'price'
		]);
	} elseif ( $this->is_type( 'hour' ) || $this->is_type( 'mixed' ) || $this->is_type( 'period_time' ) || $this->is_type( 'taxi' ) ) {
		woocommerce_wp_text_input([
			'id' 			=> $this->get_meta_name( 'prepare_vehicle' ),
			'class' 		=> 'short',
			'label' 		=> esc_html__( 'Preparation time (minutes)', 'ova-brw' ),
			'desc_tip' 		=> 'true',
			'description' 	=> esc_html__( 'Rental ends: 09:00 AM, Preparation time: 60 minutes (1 hour) → Next booking can start: 10:00 AM', 'ova-brw' ),
			'placeholder' 	=> '60',
			'value' 		=> $this->get_meta_value( 'prepare_vehicle' ),
			'data_type' 	=> 'price'
		]);
	}

	woocommerce_wp_text_input([
		'id' 			=> $this->get_meta_name( 'preparation_time' ),
		'class' 		=> 'short',
		'label' 		=> esc_html__( 'Minimum advance booking days', 'ova-brw' ),
		'desc_tip' 		=> 'true',
		'description' 	=> esc_html__( 'Book in advance X days from the current date', 'ova-brw' ),
		'placeholder' 	=> esc_html__( 'number of days', 'ova-brw' ),
		'value' 		=> $this->get_meta_value( 'preparation_time' ),
		'custom_attributes' => [
			'min' => 0
		],
		'data_type' 	=> 'price'
	]);

	// Disable weekdays
	woocommerce_wp_radio([
		'id' 		=> $this->get_meta_name( 'choose_disable_weekdays' ),
		'value' 	=> $this->get_meta_value( 'choose_disable_weekdays', 'global' ),
		'label' 	=> esc_html__( 'Disable weekdays', 'ova-brw' ),
		'options' 	=> [
			'global' 	=> esc_html__( 'Global setting', 'ova-brw' ),
			'local' 	=> esc_html__( 'Local', 'ova-brw' ),
			'none' 		=> esc_html__( 'None', 'ova-brw' )
		]
	]); ?>
	<p class="form-field ovabrw_product_disable_week_day_field ovabrw-required">
		<label for="ovabrw_product_disable_week_day">
			<?php esc_html_e( 'Select day of the week', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_wp_select_input([
			'id' 		=> $this->get_meta_name( 'product_disable_week_day' ),
			'class' 	=> 'ovabrw-select2',
			'name' 		=> $this->get_meta_name( 'product_disable_week_day[]' ),
			'value' 	=> $disable_weekdays,
			'options' 	=> [
				'1' => esc_html__( 'Monday', 'ova-brw' ),
				'2' => esc_html__( 'Tuesday', 'ova-brw' ),
				'3' => esc_html__( 'Wednesday', 'ova-brw' ),
				'4' => esc_html__( 'Thursday', 'ova-brw' ),
				'5' => esc_html__( 'Friday', 'ova-brw' ),
				'6' => esc_html__( 'Saturday', 'ova-brw' ),
				'7' => esc_html__( 'Sunday', 'ova-brw' )
			],
			'multiple' 	=> true
		]); ?>
	</p>
</div>