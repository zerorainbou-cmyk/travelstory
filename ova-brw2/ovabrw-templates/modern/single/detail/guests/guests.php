<?php if ( !defined( 'ABSPATH' ) ) exit;

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get guest options
$guest_options = $product->get_guests();
if ( !ovabrw_array_exists( $guest_options ) ) return;

// Min guests
$min_guests = (int)$product->get_meta_value( 'min_guest' );

// Max guests
$max_guests = (int)$product->get_meta_value( 'max_guest' );

// Number of guests
$numberof_guests = 0;

// init guests
$guests = [];

// Loop guest options
foreach ( $guest_options as $guest_item ) {
	// Get guest price
    $price = (float)$product->get_meta_value( $guest_item['name'].'_price' );
    if ( $price ) $guest_item['price'] = $price;

	// Min
	$min = (int)$product->get_meta_value( 'min_'.$guest_item['name'] );
	$guest_item['min'] = $min;

	// Max
	$max = $product->get_meta_value( 'max_'.$guest_item['name'] );
	$guest_item['max'] = $max;

    // Value
    $value = (int)ovabrw_get_meta_data( $guest_item['name'], $_GET );
    if ( !$value ) $value = $min;

	// Number of guests
	$numberof_guests += $value;

	// Update guests
	array_push( $guests, $guest_item );
}

?>

<div class="rental_item ovabrw-guests-field full-width">
    <h3 class="ovabrw-label">
        <?php esc_html_e( 'Guests', 'ova-brw' ); ?>
    </h3>
    <div class="ovabrw-guests-wrap">
    	<div class="ovabrw-guestspicker">
    		<?php ovabrw_text_input([
                'type'          => 'number',
                'class'         => 'ovabrw-input-required',
                'name'          => $product->get_meta_key( 'numberof_guests' ),
                'value'         => ( $min_guests && !$numberof_guests ) ? $min_guests : $numberof_guests,
                'placeholder'   => esc_html__( 'number of guests', 'ova-brw' ),
                'readonly'      => true
            ]); ?>
            <span class="ovabrw-loader-guest">
                <i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
            </span>
    	</div>
    	<div class="ovabrw-guestspicker-content">
    		<?php foreach ( $guests as $k => $guest ):
    			// Get name
    			$name = ovabrw_get_meta_data( 'name', $guest );

    			// Get label
    			$label = ovabrw_get_meta_data( 'label', $guest );

    			// Get description
    			$desc = ovabrw_get_meta_data( 'desc', $guest );

    			// Show price
    			$show_price = 'yes' === ovabrw_get_meta_data( 'show_price', $guest ) ? true : false;

    			// Guest price
    			$price = (float)ovabrw_get_meta_data( 'price', $guest );

    			// Min
    			$min = ovabrw_get_meta_data( 'min', $guest );

    			// Max
    			$max = ovabrw_get_meta_data( 'max', $guest );

                // Value
                $value = (int)ovabrw_get_meta_data( $name, $_GET );
                if ( !$value ) $value = $min;
    		?>
    			<div class="guests-item">
                    <div class="guests-info">
                        <div class="guests-label">
                            <h3 class="ovabrw-label">
                                <?php echo esc_html( $label ); ?>
                                <?php if ( $desc ): ?>
                                	<span class="ovabrw-description" aria-label="<?php echo esc_attr( $desc ); ?>">
					                    <i class="brwicon2-question"></i>
					                </span>
                                <?php endif; ?>
                            </h3>
                        </div>
                        <?php if ( $show_price && $price ): ?>
                            <div class="guests-price <?php echo esc_attr( $name ); ?>-price">
                                <?php printf( esc_html__( '%s/guest', 'ova-brw' ), wp_kses_post( ovabrw_wc_price( $price ) ) ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="guests-action">
                    	<div class="guests-icon guests-minus">
	                        <span class="flaticon flaticon-substract" aria-hidden="true"></span>
	                    </div>
                        <?php if ( $min_guests && !$numberof_guests && !$k ): ?>
                            <span class="guests-number numberof-<?php echo esc_attr( $name ); ?>">
                                <?php echo esc_html( $min_guests ); ?>
                            </span>
                            <?php ovabrw_text_input([
                                'type'  => 'hidden',
                                'class' => 'guests-input',
                                'name'  => $product->get_meta_key( 'numberof_'.$name ),
                                'value' => $min_guests,
                                'attrs' => [
                                    'min'           => $min,
                                    'max'           => $max,
                                    'data-name'     => $name,
                                    'data-label'    => $label
                                ]
                            ]);
                        else: ?>
                            <span class="guests-number numberof-<?php echo esc_attr( $name ); ?>">
                                <?php echo esc_html( $value ); ?>
                            </span>
                            <?php ovabrw_text_input([
                                'type'  => 'hidden',
                                'class' => 'guests-input',
                                'name'  => $product->get_meta_key( 'numberof_'.$name ),
                                'value' => $value,
                                'attrs' => [
                                    'min'           => $min,
                                    'max'           => $max,
                                    'data-name'     => $name,
                                    'data-label'    => $label
                                ]
                            ]);
                        endif; ?>
                        <div class="guests-icon guests-plus">
	                        <span class="flaticon flaticon-add" aria-hidden="true"></span>
	                    </div>
                    </div>
                </div>
    		<?php endforeach; ?>
    	</div>
    	<?php if ( OVABRW()->options->guest_info_enabled( $product->get_id() ) ): ?>
    		<div class="ovabrw-guest-info">
                <div class="guest-info-heading">
                    <?php esc_html_e( 'Please enter guest information', 'ova-brw' ); ?>
                </div>
                <div class="guest-info-accordion"></div>
            </div>
    	<?php endif; ?>
    </div>
    <?php ovabrw_text_input([
        'type'  => 'hidden',
        'name'  => $product->get_meta_key( 'min_guests' ),
        'value' => $min_guests
    ]); ?>
    <?php ovabrw_text_input([
        'type'  => 'hidden',
        'name'  => $product->get_meta_key( 'max_guests' ),
        'value' => $max_guests
    ]); ?>
</div>