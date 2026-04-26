<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field manager-show-dropoff-date">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Drop-off date', 'ova-brw' ); ?>
	</strong>
	<?php
		if ( $this->is_type( 'transportation' ) ) {
			woocommerce_wp_radio([
				'id' 			=> $this->get_meta_name( 'dropoff_date_by_setting' ),
				'value' 		=> $this->get_meta_value( 'dropoff_date_by_setting', 'no' ),
				'options'		=> [
					'yes' 	=> esc_html__( 'Global setting', 'ova-brw' ),
					'no' 	=> esc_html__( 'No', 'ova-brw' )
				],
				'label' 		=> esc_html__( 'Show', 'ova-brw' ),
				'desc_tip' 		=> true,
				'description'	=> esc_html__( 'Global setting: Go to WooCommerce >> Settings >> Booking & Rental >> Product Details', 'ova-brw' )
			]);
		} else {
			woocommerce_wp_radio([
				'id' 			=> $this->get_meta_name( 'show_pickoff_date_product' ),
				'value' 		=> $this->get_meta_value( 'show_pickoff_date_product', 'in_setting' ),
				'options'		=> [
					'in_setting' 	=> esc_html__( 'Global setting', 'ova-brw' ),
					'yes' 			=> esc_html__( 'Yes', 'ova-brw' ),
					'no'			=> esc_html__( 'No', 'ova-brw' )
				],
				'label' 		=> esc_html__( 'Show', 'ova-brw' ),
				'desc_tip' 		=> true,
				'description'	=> esc_html__( 'Global setting: Go to WooCommerce >> Settings >> Booking & Rental >> Product Details', 'ova-brw' )
			]);
		}

		// Has time group
		$has_time_group = [ 'day', 'hour', 'mixed', 'transportation' ];

		if ( in_array( $this->get_type(), $has_time_group ) ):
			woocommerce_wp_radio([
				'id' 			=> $this->get_meta_name( 'manage_time_book_end' ),
				'value' 		=> $this->get_meta_value( 'manage_time_book_end', 'in_setting' ),
				'options'		=> [
					'in_setting' 	=> esc_html__( 'Global setting', 'ova-brw' ),
					'new_time'		=> esc_html__( 'Local', 'ova-brw' ),
					'everyday'		=> esc_html__( 'Everyday', 'ova-brw' ),
					'no'			=> esc_html__( 'No', 'ova-brw' )
				],
				'label' 		=> esc_html__( 'Show the group of time', 'ova-brw' ),
				'desc_tip' 		=> true,
				'description' 	=> esc_html__( 'Global setting: WooCommerce >> Settings >> Booking & Rental >> General Tab', 'ova-brw' )
			]);

		   	woocommerce_wp_textarea_input([
		   		'id' 			=> $this->get_meta_name( 'product_time_to_book_end' ),
		   		'wrapper_class' => 'ovabrw-required',
		        'placeholder' 	=> esc_html__( '07:00, 07:30, 13:00, 18:00', 'ova-brw' ),
		        'label' 		=> esc_html__('New time', 'ova-brw'),
		        'value' 		=> $this->get_meta_value( 'product_time_to_book_end' ),
		        'desc_tip' 		=> true,
		        'description' 	=> esc_html__( 'Insert time format: 24hour. Ex. 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00', 'ova-brw' )
		   	]);

			woocommerce_wp_radio([
				'id' 			=> $this->get_meta_name( 'manage_default_hour_end' ),
				'value' 		=> $this->get_meta_value( 'manage_default_hour_end', 'in_setting' ),
				'options'		=> [
					'in_setting' 	=> esc_html__( 'Global setting', 'ova-brw' ),
					'new_time' 		=> esc_html__( 'Local', 'ova-brw' )
				],
				'label' 		=> esc_html__( 'Default Time', 'ova-brw' ),
				'desc_tip' 		=> true,
				'description' 	=> esc_html__( 'Global setting: WooCommerce >> Settings >> Booking & Rental >> General Tab', 'ova-brw' )
			]);

		   	woocommerce_wp_text_input([
		   		'id' 			=> $this->get_meta_name( 'product_default_hour_end' ),
		   		'wrapper_class' => 'ovabrw-required',
		   		'class' 		=> 'ovabrw-timepicker',
		        'placeholder' 	=> esc_html__( '07:00', 'ova-brw' ),
		        'label' 		=> esc_html__( 'Select time', 'ova-brw' ),
		        'value' 		=> $this->get_meta_value( 'product_default_hour_end' ),
		        'desc_tip' 		=> true,
		        'description' 	=> esc_html__( 'Insert time format 24hour. Example: 09:00', 'ova-brw' )
		   	]);

			?>

			<div class="ovabrw-daily-time">
			<?php
				// Get daily drop-off time step
				$daily_dropoff_time_step = $this->get_meta_value( 'daily_dropoff_time_step', 30 );

				// Get daily drop-off times
				$daily_dropoff_times = $this->get_meta_value( 'daily_dropoff_times' );

				// Time step
				woocommerce_wp_text_input([
					'type' 			=> 'number',
					'id' 			=> $this->get_meta_name( 'daily_dropoff_time_step' ),
					'wrapper_class' => 'ovabrw-required',
					'value' 		=> $daily_dropoff_time_step,
					'label' 		=> esc_html__( 'Time step', 'ova-brw' ),
					'placeholder' 	=> 30,
					'desc_tip' 		=> true,
					'description' 	=> esc_html__( 'Set 30 minutes as the default time slot step, the working hours will be divided by a grid of 30 minutes: 07:30, 08:00, 08:30, ...', 'ova-brw' )
				]);

				// Days of week
				$days_of_week = [
					'monday' 	=> esc_html__( 'Monday', 'ova-brw' ),
					'tuesday' 	=> esc_html__( 'Tuesday', 'ova-brw' ),
					'wednesday' => esc_html__( 'Wednesday', 'ova-brw' ),
					'thursday' 	=> esc_html__( 'Thursday', 'ova-brw' ),
					'friday' 	=> esc_html__( 'Friday', 'ova-brw' ),
					'saturday' 	=> esc_html__( 'Saturday', 'ova-brw' ),
					'sunday' 	=> esc_html__( 'Sunday', 'ova-brw' )
				];

				?>
				<div class="daily-grid">
					<?php foreach ( $days_of_week as $dayofweek => $label ):
						// Get start time
						$start_time = isset( $daily_dropoff_times[$dayofweek]['start'] ) ? $daily_dropoff_times[$dayofweek]['start'] : '';

						// Get end time
						$end_time = isset( $daily_dropoff_times[$dayofweek]['end'] ) ? $daily_dropoff_times[$dayofweek]['end'] : '';
					?>
					<div class="daily-item">
						<div class="dayofweek ovabrw-required">
							<?php echo esc_html( $label ); ?>
						</div>
						<div class="time">
							<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'class' 		=> 'ovabrw-daily-timepicker start-time',
								'name' 			=> $this->get_meta_name( 'daily_dropoff_times['.$dayofweek.'][start]' ),
								'value' 		=> $start_time,
								'placeholder' 	=> '06:00'
							]); ?>
							<span class="separator">-</span>
							<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'class' 		=> 'ovabrw-daily-timepicker end-time',
								'name' 			=> $this->get_meta_name( 'daily_dropoff_times['.$dayofweek.'][end]' ),
								'value' 		=> $end_time,
								'placeholder' 	=> '18:00'
							]); ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; // END group time

		woocommerce_wp_radio([
			'id' 			=> $this->get_meta_name( 'label_dropoff_date_product' ),
			'value' 		=> $this->get_meta_value( 'label_dropoff_date_product', 'category' ),
			'options'		=> [
				'category' 	=> esc_html__( 'Category setting', 'ova-brw' ),
				'new' 		=> esc_html__( 'Local', 'ova-brw' )
			],
			'label'       	=> esc_html__( 'Rename "Drop-off Date" title by', 'ova-brw' ),
			'desc_tip' 		=> true,
			'description' 	=> esc_html__( '- Category setting: Get title per Category.', 'ova-brw' ),
		]);

		woocommerce_wp_text_input([
			'id' 			=> $this->get_meta_name( 'new_dropoff_date_product' ),
			'wrapper_class' => 'ovabrw-required',
	        'placeholder' 	=> esc_html__( 'New title', 'ova-brw' ),
	        'label' 		=> esc_html__( 'New title', 'ova-brw' ),
	        'value' 		=> $this->get_meta_value( 'new_dropoff_date_product' ),
	        'desc_tip' 		=> true,
	        'description' 	=> esc_html__( 'Ex. check-out date, return date', 'ova-brw' )
		]);
	?>
</div>