<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
    <td width="32%" class="ovabrw-input-price">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'class'         => 'ovabrw-input-required',
            'name'          => $this->get_meta_name( 'discount_distance_from[]' ),
            'placeholder'   => esc_attr__( 'Number', 'ova-brw' ),
            'data_type'     => 'price'
        ]); ?>
    </td>
    <td width="33%" class="ovabrw-input-price">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'class'         => 'ovabrw-input-required',
            'name'          => $this->get_meta_name( 'discount_distance_to[]' ),
            'placeholder'   => esc_attr__( 'Number', 'ova-brw' ),
            'data_type'     => 'price'
        ]); ?>
    </td>
    <td width="33%" class="ovabrw-input-price">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'class'         => 'ovabrw-input-required',
            'name'          => $this->get_meta_name( 'discount_distance_price[]' ),
            'placeholder'   => esc_attr__( 'Price', 'ova-brw' ),
            'data_type'     => 'price'
        ]); ?>
    </td>
    <td width="1%" class="ovabrw-sort-icon">
        <span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
    </td>
    <td width="1%">
        <button class="button ovabrw-remove-discount-distance" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">X</button>
    </td>
</tr>