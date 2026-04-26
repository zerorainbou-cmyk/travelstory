<?php defined( 'ABSPATH' ) || exit();

// Get custom checkout fields
$product_cckf = $this->product->get_cckf();

if ( ovabrw_array_exists( $product_cckf ) ): ?>
	<div class="rental_item ovabrw-custom_ckf">
		<?php foreach ( $product_cckf as $name => $fields ):
	        if ( !ovabrw_get_meta_data( 'enabled', $fields ) ) continue;

	        $type           = ovabrw_get_meta_data( 'type', $fields );
	        $label          = ovabrw_get_meta_data( 'label', $fields );
	        $description 	= ovabrw_get_meta_data( 'description', $fields );
	        $default 		= ovabrw_get_meta_data( 'default', $fields );
	        $placeholder 	= ovabrw_get_meta_data( 'placeholder', $fields );
	        $class 			= ovabrw_get_meta_data( 'class', $fields );
	        $required 		= ovabrw_get_meta_data( 'required', $fields );
	        $max_size 		= ovabrw_get_meta_data( 'max_file_size', $fields );
	        $min 			= ovabrw_get_meta_data( 'min', $fields );
	        $max 			= ovabrw_get_meta_data( 'max', $fields );
	        $min_price 		= ovabrw_get_meta_data( 'min_price', $fields );
	        $max_price 		= ovabrw_get_meta_data( 'max_price', $fields );
	        $step 			= ovabrw_get_meta_data( 'step', $fields );
	        $default_date 	= ovabrw_get_meta_data( 'default_date', $fields );
	        $min_date 		= ovabrw_get_meta_data( 'min_date', $fields );
	        $max_date 		= ovabrw_get_meta_data( 'max_date', $fields );
	        $wrapper_class 	= 'ovabrw-ccfk-'.esc_attr( $type );

	        // Options
	        $options    = [];
	        $quantities = [];
	        $min_quantities = [];

	        // Radio
	        if ( 'radio' === $type ) {
	        	$opt_ids = ovabrw_get_meta_data( 'ova_values', $fields );
	        	$opt_qty = ovabrw_get_meta_data( 'ova_qtys', $fields );
	        	$opt_min = ovabrw_get_meta_data( 'ova_min_qtys', $fields );

	        	if ( ovabrw_array_exists( $opt_ids ) ) {
	        		foreach ( $opt_ids as $k => $opt_id ) {
	        			// Add option ID
	                	$options[$opt_id] = $opt_id;

	                	// Add option min quantity
	                	$min_qty = ovabrw_get_meta_data( $k, $opt_min );
	                	if ( '' != $min_qty ) $min_quantities[$opt_id] = (int)$min_qty;

	                	// Add option quantity
	                	$qty = ovabrw_get_meta_data( $k, $opt_qty );
	                	if ( '' != $qty ) $quantities[$opt_id] = (int)$qty;
	        		}
	        	}
	        }

	        // Checkbox
	        if ( 'checkbox' === $type ) {
	        	$opt_ids    = ovabrw_get_meta_data( 'ova_checkbox_key', $fields );
	        	$opt_names  = ovabrw_get_meta_data( 'ova_checkbox_text', $fields );
	        	$opt_qty    = ovabrw_get_meta_data( 'ova_checkbox_qty', $fields );
	        	$opt_min    = ovabrw_get_meta_data( 'ova_checkbox_min_qty', $fields );

	        	if ( ovabrw_array_exists( $opt_ids ) ) {
	        		foreach ( $opt_ids as $k => $opt_id ) {
	        			// Option name
	        			$opt_name = ovabrw_get_meta_data( $k, $opt_names );

	        			// Add option ID
	                	$options[$opt_id] = $opt_name;

	                	// Add option min quantity
	                	$min_qty = ovabrw_get_meta_data( $k, $opt_min );
	                	if ( '' != $min_qty ) $min_quantities[$opt_id] = (int)$min_qty;

	                	// Add option quantity
	                	$qty = ovabrw_get_meta_data( $k, $opt_qty );
	                	if ( '' != $qty ) $quantities[$opt_id] = (int)$qty;
	        		}
	        	}
	        }

	        // Select
	        if ( 'select' === $type ) {
	        	// Placeholder
	        	$placeholder = sprintf( esc_html__( 'Select %s', 'ova-brw' ), $label );

	        	// Options
	        	$opt_ids    = ovabrw_get_meta_data( 'ova_options_key', $fields );
	        	$opt_names  = ovabrw_get_meta_data( 'ova_options_text', $fields );
	        	$opt_qty    = ovabrw_get_meta_data( 'ova_options_qty', $fields );
	        	$opt_min    = ovabrw_get_meta_data( 'ova_options_min_qty', $fields );

	        	if ( ovabrw_array_exists( $opt_ids ) ) {
	        		foreach ( $opt_ids as $k => $opt_id ) {
	        			// Option name
	        			$opt_name = ovabrw_get_meta_data( $k, $opt_names );

	        			// Add option ID
	                	$options[$opt_id] = $opt_name;

	                	// Add option min quantity
	                	$min_qty = ovabrw_get_meta_data( $k, $opt_min );
	                	if ( '' != $min_qty ) $min_quantities[$opt_id] = (int)$min_qty;

	                	// Add option quantity
	                	$qty = ovabrw_get_meta_data( $k, $opt_qty );
	                	if ( '' != $qty ) $quantities[$opt_id] = (int)$qty;
	        		}
	        	}
	        }

	        // Get field id
        	$field_id = ovabrw_unique_id( $name.'_'.$this->get_id() );
		?>
			<div class="ovabrw-ckf <?php echo esc_attr( $wrapper_class ); ?>">
				<div class="ovabrw-label">
					<?php echo esc_html( $label ); ?>
					<?php if ( $description ): ?>
		                <span class="ovabrw-description" aria-label="<?php echo esc_attr( $description ); ?>">
		                    <i class="brwicon2-question"></i>
		                </span>
		            <?php endif; ?>
				</div>
				<?php if ( 'textarea' === $type ) {
					ovabrw_textarea_input([
						'id' 			=> $field_id,
						'class' 		=> $class,
						'name' 			=> $name,
						'key' 			=> 'ovabrw-item-key',
						'default' 		=> $default,
						'placeholder' 	=> $placeholder,
						'required' 		=> $required,
						'attrs' 		=> [
							'rows' => 5
						]
					]);
				} elseif ( 'select' === $type ) {
					ovabrw_select_input([
						'id' 			=> $field_id,
						'class'         => $class,
						'name'          => $name,
						'key'           => 'ovabrw-item-key',
						'default'       => $default,
						'placeholder'   => $placeholder,
						'options'       => $options,
						'min_quantities'=> $min_quantities,
						'quantities'    => $quantities,
						'required'      => $required
					]);
				} elseif ( 'radio' === $type ) {
					ovabrw_radio_input([
						'class'         => $class,
						'name'          => $name,
						'key'           => 'ovabrw-item-key',
						'default'       => $default,
						'placeholder'   => $placeholder,
						'options'       => $options,
						'min_quantities'=> $min_quantities,
						'quantities'    => $quantities,
						'required'      => $required
					]);
				} elseif ( 'checkbox' === $type ) {
					ovabrw_checkbox_input([
						'class'         => $class,
						'name'          => $name,
						'key'           => 'ovabrw-item-key',
						'default'       => $default,
						'placeholder'   => $placeholder,
						'options'       => $options,
						'min_quantities'=> $min_quantities,
						'quantities'    => $quantities,
						'required'      => $required
					]);
				} elseif ( 'file' === $type ) {
					ovabrw_wp_file_input([
						'type' 		=> 'text',
						'id' 		=> $field_id,
						'class' 	=> $class,
						'name' 		=> $name,
						'key' 		=> 'ovabrw-item-key',
						'default' 	=> $default,
						'required' 	=> $required,
	                    'attrs'         => [
	                        'accept'    		=> '.jpg, .jpeg, .png, .pdf, .doc, .docx',
	                        'max-size'  		=> $max_size,
	                        'data-title' 		=> esc_html__( 'Select or Upload a File', 'ova-brw' ),
	                        'data-button-text' 	=> esc_html__( 'Use this file', 'ova-brw' )
	                    ]
	                ]);
				} elseif ( 'date' === $type ) {
					// Date format
	                $date_format = OVABRW()->options->get_date_format();

	                // Default date
	                $default_date = strtotime( $default_date ) ? gmdate( $date_format, strtotime( $default_date ) ) : '';

	                // Min date
	                $min_date = strtotime( $min_date ) ? gmdate( $date_format, strtotime( $min_date ) ) : '';

	                // Max date
	                $max_date = strtotime( $max_date ) ? gmdate( $date_format, strtotime( $max_date ) ) : '';

	                // Min year
	                $min_year = '';
	                if ( strtotime( $min_date ) ) $min_year = gmdate( 'Y', strtotime( $min_date ) );

	                // Max year
	                $max_year = '';
	                if ( strtotime( $max_date ) ) $max_year = gmdate( 'Y', strtotime( $max_date ) );

					ovabrw_text_input([
						'type' 			=> 'text',
				        'id' 			=> $field_id,
				        'class' 		=> $class,
				        'name' 			=> $name,
				        'key' 			=> 'ovabrw-item-key',
				        'value' 		=> $default_date,
				        'placeholder' 	=> $placeholder,
				        'required' 		=> $required,
				        'data_type' 	=> 'datepicker-field',
				        'attrs' 		=> [
				        	'data-min-date' => $min_date,
				        	'data-max-date' => $max_date,
				        	'data-min-year' => $min_year,
				        	'data-max-year' => $max_year
				        ]
					]);
				} elseif ( 'number' === $type ) {
					ovabrw_text_input([
						'type' 			=> $type,
						'id' 			=> $field_id,
				        'class' 		=> $class,
				        'name' 			=> $name,
				        'key' 			=> 'ovabrw-item-key',
				        'value' 		=> $default,
				        'placeholder' 	=> $placeholder,
				        'required' 		=> $required,
				        'data_type' 	=> 'number',
				        'attrs' 		=> [
				        	'min' => $min,
				        	'max' => $max
				        ]
					]);
				} elseif ( 'price' === $type ) {
				    // Get currency
				    $currency = ovabrw_get_meta_data( 'currency', $args );
				    
				    // Price values
				    $min_value      = $min_price ? $min_price : 0;
				    $max_value      = $max_price ? $max_price : 1000;
				    $step_value     = $step ? $step : 0.01;
				    $default_value  = $default;
				    
				    // Convert prices currency
				    $min_value      = ovabrw_convert_price( $min_value, [ 'currency' => $currency ] );
				    $max_value      = ovabrw_convert_price( $max_value, [ 'currency' => $currency ] );
				    $default_value  = ovabrw_convert_price( $default_value, [ 'currency' => $currency ] );
				    
				    // Get currency info
				    $currency_symbol 	= get_woocommerce_currency_symbol( $currency );
				    $currency_pos    	= get_option( 'woocommerce_currency_pos', 'left' );
				    $thousand_separator = wc_get_price_thousand_separator();
				    $decimal_separator 	= wc_get_price_decimal_separator();
				    $price_decimals 	= wc_get_price_decimals();

				    ?>
				    <div class="ovabrw-price-slider-wrapper">
				        <div class="ovabrw-price-slider"
				            data-min="<?php echo esc_attr( $min_value ); ?>" 
				            data-max="<?php echo esc_attr( $max_value ); ?>" 
				            data-step="<?php echo esc_attr( $step_value ); ?>"
				            data-value="<?php echo esc_attr( $default_value ); ?>"
				            data-currency-symbol="<?php echo esc_attr( $currency_symbol ); ?>"
				            data-currency-position="<?php echo esc_attr( $currency_pos ); ?>"
				            data-thousand-separator="<?php echo esc_attr( $thousand_separator ); ?>"
				            data-decimal-separator="<?php echo esc_attr( $decimal_separator ); ?>"
				            data-price-decimals="<?php echo esc_attr( $price_decimals ); ?>">
				        </div>

				        <?php
				        ovabrw_text_input([
				            'type'          => 'hidden',
				            'id' 			=> $field_id,
				            'class'         => $class,
				            'name'          => $name,
				            'key'           => 'ovabrw-item-key',
				            'value'         => $default_value,
				            'required'      => $required,
				            'data_type'     => 'price',
				            'attrs'         => [
				                'step' => $step_value,
				                'min_price' => $min_value,
				                'max_price' => $max_value
			                ]
				        ]);
				        ?>
				    </div>
				    <?php
				} else {
					ovabrw_text_input([
						'type' 			=> $type,
						'id' 			=> $field_id,
				        'class' 		=> $class,
				        'name' 			=> $name,
				        'key' 			=> 'ovabrw-item-key',
				        'value' 		=> $default,
				        'placeholder' 	=> $placeholder,
				        'required' 		=> $required
					]);
				} ?>
			</div>
		<?php endforeach; ?>
		<?php ovabrw_text_input([
			'type' 	=> 'hidden',
	        'name' 	=> 'ovabrw-cckf-data',
	        'key' 	=> 'ovabrw-item-key',
	        'value' => json_encode( $product_cckf )
		]); ?>
	</div>
<?php endif;