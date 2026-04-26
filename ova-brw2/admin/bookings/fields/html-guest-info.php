<?php if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Variables used in this file.
 * @var object 	$product
 * @var int 	$product_id
 * @var string 	$guest_name
 * @var int   	$key 
 */

if ( $guest_name ):
	// Get guest fields
	$guest_fields = $product->get_guest_info_fields( $guest_name );
?>
	<div class="guest-info-item">
		<div class="guest-info-header">
            <h3 class="ovabrw-label">
            	<?php printf( esc_html__( 'Guest %s', 'ova-brw' ), esc_html( $key + 1 ) ); ?>
           	</h3>
            <i class="brwicon2-down-arrow" aria-hidden="true"></i>
        </div>
        <div class="guest-info-body">
        	<div class="guest-info-content">
    		<?php if ( ovabrw_array_exists( $guest_fields ) ):
    			foreach ( $guest_fields as $name => $fields ):
    				$enable = ovabrw_get_meta_data( 'enable', $fields );
        			if ( !$enable ) continue;

        			$type           = ovabrw_get_meta_data( 'type', $fields );
			        $label          = ovabrw_get_meta_data( 'label', $fields );
			        $accept         = ovabrw_get_meta_data( 'accept', $fields );
			        $max_size       = ovabrw_get_meta_data( 'max_size', $fields );
			        $pattern        = ovabrw_get_meta_data( 'pattern', $fields );
			        $description    = ovabrw_get_meta_data( 'description', $fields );
			        $placeholder    = ovabrw_get_meta_data( 'placeholder', $fields );
			        $default        = ovabrw_get_meta_data( 'default', $fields );
			        $min            = ovabrw_get_meta_data( 'min', $fields );
			        $max            = ovabrw_get_meta_data( 'max', $fields );
			        $class          = ovabrw_get_meta_data( 'class', $fields );
			        $required       = ovabrw_get_meta_data( 'required', $fields );

			        // Options
			        $options        = [];
			        $option_qtys    = [];
			        $opt_ids        = ovabrw_get_meta_data( 'option_ids', $fields );
			        $opt_names      = ovabrw_get_meta_data( 'option_names', $fields );
			        $opt_qtys       = ovabrw_get_meta_data( 'option_qtys', $fields );

			        if ( ovabrw_array_exists( $opt_ids ) && ovabrw_array_exists( $opt_names ) ) {
			            foreach ( $opt_ids as $index => $opt_id ) {
			                $opt_name   = (string)ovabrw_get_meta_data( $index, $opt_names );
			                $opt_qty    = ovabrw_get_meta_data( $index, $opt_qtys );

			                // Options
			                $options[$opt_id] = $opt_name;

			                // Option Qty
			                if ( $opt_qty !== '' ) $option_qtys[$opt_id] = (int) $opt_qty;
			            }
			        }

			        // Required class
			        $required_class = '';
			        if ( $required ) $required_class = 'ovabrw-required';

			        // Get field id
			        $field_id = ovabrw_unique_id( $name.'_'.$product_id );
    			?>
    			<div class="guest-info-field ovabrw-guest-info-<?php echo esc_attr( $type ); ?>">
    				<?php if ( in_array( $type, ['radio', 'checkbox'] ) ): ?>
    					<div class="ovabrw-label <?php echo esc_attr( $required_class ); ?>">
							<?php echo esc_html( $label ); ?>
			                <?php if ( $description ): ?>
			                    <span class="ovabrw-description" aria-label="<?php echo esc_attr( $description ); ?>">
			                        <i class="brwicon2-question"></i>
			                    </span>
			                <?php endif; ?>
						</div>
    				<?php else: ?>
    					<label for="<?php echo esc_attr( $field_id ); ?>" class="<?php echo esc_attr( $required_class ); ?>">
							<?php echo esc_html( $label ); ?>
			                <?php if ( $description ): ?>
			                    <span class="ovabrw-description" aria-label="<?php echo esc_attr( $description ); ?>">
			                        <i class="brwicon2-question"></i>
			                    </span>
			                <?php endif; ?>
						</label>
    				<?php endif;

    				// Textarea
    				if ( 'textarea' === $type ) {
						ovabrw_textarea_input([
							'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']' ),
		                    'default'       => $default,
		                    'placeholder'   => $placeholder,
		                    'required'      => $required
		                ]);
					} elseif ( 'number' === $type ) { // Number
						ovabrw_text_input([
		                    'type'          => 'number',
		                    'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']' ),
		                    'default'       => $default,
		                    'placeholder'   => $placeholder,
		                    'required'      => $required,
		                    'attrs'         => [
		                        'data-min' 	=> $min,
		                        'data-max' 	=> $max
		                    ]
		                ]);
					} elseif ( 'radio' === $type ) { // Radio
						ovabrw_radio_input([
							'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']' ),
		                    'name_qty' 		=> $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.'_qty]' ),
		                    'default'       => $default,
		                    'options'       => $options,
		                    'quantities' 	=> $option_qtys,
		                    'placeholder'   => $placeholder,
		                    'required'      => $required
		                ]);
					} elseif ( 'checkbox' === $type ) { // Checkbox
						ovabrw_checkbox_input([
							'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']' ),
		                    'name_qty' 		=> $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.'_qty]' ),
		                    'default'       => $default,
		                    'options'       => $options,
		                    'quantities' 	=> $option_qtys,
		                    'placeholder'   => $placeholder,
		                    'required'      => $required
		                ]);
					} elseif ( 'select' === $type ) { // Select
						ovabrw_select_input([
							'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']'),
		                    'name_qty' 		=> $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.'_qty]' ),
		                    'default'       => $default,
		                    'options'       => $options,
		                    'quantities' 	=> $option_qtys,
		                    'placeholder'   => $placeholder,
		                    'required'      => $required
		                ]);
					} elseif ( 'date' === $type ) { // Date
						// Date format
		                $date_format = OVABRW()->options->get_date_format();

		                // Min date
		                $min_date = strtotime( $min ) ? gmdate( $date_format, strtotime( $min ) ) : '';

		                // Max date
		                $max_date = strtotime( $max ) ? gmdate( $date_format, strtotime( $max ) ) : '';

		                // Min year
		                $min_year = '';
		                if ( strtotime( $min_date ) ) {
		                	$min_year = gmdate( 'Y', strtotime( $min_date ) );
		                }

		                // Max year
		                $max_year = '';
		                if ( strtotime( $max_date ) ) {
		                	$max_year = gmdate( 'Y', strtotime( $max_date ) );
		                }

		                ovabrw_text_input([
		                    'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']' ),
		                    'default'       => $default,
		                    'placeholder'   => $placeholder,
		                    'required'      => $required,
		                    'attrs'         => [
		                        'data-min-date' => $min_date,
		                        'data-max-date' => $max_date,
		                        'data-min-year' => $min_year,
		                        'data-max-year' => $max_year
		                    ],
		                    'data_type'     => 'datepicker-field'
		                ]);
					} elseif ( 'tel' === $type ) { // Tel
						ovabrw_text_input([
		                    'type'          => $type,
		                    'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']' ),
		                    'default'       => $default,
		                    'placeholder'   => $placeholder,
		                    'required'      => $required,
		                    'attrs'         => [
		                        'data-pattern' => $pattern
		                    ]
		                ]);
					} elseif ( 'file' === $type ) { // File
						ovabrw_wp_file_input([
							'type' 			=> 'text',
							'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']' ),
		                    'default'       => $default,
		                    'required'      => $required,
		                    'attrs'         => [
		                        'accept'    		=> $accept,
		                        'max-size'  		=> $max_size,
		                        'data-title' 		=> esc_html__( 'Select or Upload a File', 'ova-brw' ),
		                        'data-button-text' 	=> esc_html__( 'Use this file', 'ova-brw' )
		                    ]
		                ]);
					} else { // Text
						ovabrw_text_input([
		                    'type'          => $type,
		                    'id' 			=> $field_id,
		                    'class'         => $class,
		                    'name'          => $product->get_meta_key( $guest_name.'_info['.$meta_key.']['.$key.']['.$name.']' ),
		                    'default'       => $default,
		                    'placeholder'   => $placeholder,
		                    'required'      => $required
		                ]);
					} ?>
    			</div>
    		<?php endforeach;
    		endif; ?>
            </div>
        </div>
    </div>
<?php endif;