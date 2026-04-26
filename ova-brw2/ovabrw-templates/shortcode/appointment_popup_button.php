<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get attributes
$product_id  = ovabrw_get_meta_data( 'product_id', $args );
$text_button = ovabrw_get_meta_data( 'text_button', $args );
$icon_button = ovabrw_get_meta_data( 'icon_button', $args );
$icon_align  = ovabrw_get_meta_data( 'icon_align', $args );

// Check if product is selected
if ( !$product_id ) return;

// Get product
$product = wc_get_product( $product_id );
if ( !$product || !$product->is_type( OVABRW_RENTAL ) || !$product->is_rental_type( 'appointment' ) ) {
	return;
}

$class_icon_align = $icon_align === 'after' ? 'icon-after' : 'icon-before';
?>

<button class="ovabrw-appointment-button <?php echo esc_attr( $class_icon_align ); ?>" 
    data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <span class="text-button">
        <?php echo esc_html( $text_button ); ?>    
    </span>
    <i class="<?php echo esc_attr( $icon_button ); ?>"></i>
    <span class="ovabrw-loader">
        <i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
    </span>
</button>
