<?php defined( 'ABSPATH' ) || exit();

// Get custom checkout fields
$cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

// Get field
$field = ovabrw_get_meta_data( $name, $cckf );

?>

<form action="" method="post" id="ovabrw-popup-cckf-form" enctype="multipart/form-data">
    <?php ovabrw_wp_text_input([
        'type'  => 'hidden',
        'name'  => 'ovabrw-cckf-action',
        'value' => $action
    ]); ?>
    <button class="popup-cckf-close">X</button>
    <table width="100%">
        <tbody>
            <?php add_action( OVABRW_PREFIX.'before_popup_add_cckf', $action, $type, $name ); ?>
            <tr class="type">
                <td class="label ovabrw-required">
                    <?php esc_html_e( 'Select type', 'ova-brw' ); ?>
                </td>
                <td>
                    <div class="ovabrw-input-type">
                        <select name="type" class="ovabrw-input-required">
                            <?php foreach ( $this->get_types() as $t => $v ): ?>
                                <option value="<?php echo esc_attr( $t ); ?>"<?php ovabrw_selected( $t, $type ); ?>>
                                    <?php echo esc_html( $v ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="ovabrw-loading">
                            <span class="dashicons dashicons-update-alt"></span>
                        </div>
                    </div>
                    <?php if ( 'file' === $type ): ?>
                        <span>
                            <em>
                                <?php esc_html_e( 'Formats: .jpg, .jpeg, .png, .pdf, .doc', 'ova-brw' ); ?>
                            </em>
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ( 'file' === $type ): ?>
                <tr class="max-size">
                    <td class="label ovabrw-required">
                        <?php esc_html_e( 'Max size', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'name'  => 'max_file_size',
                            'value' => ovabrw_get_meta_data( 'max_file_size', $field )
                        ]); ?>
                        <span>
                            <em>
                                <?php esc_html_e( 'Default: 20MB', 'ova-brw' ); ?>
                            </em>
                        </span>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ( 'radio' === $type ):
                // Get option values
                $opt_values = ovabrw_get_meta_data( 'ova_values', $field, [] );

                // Get option prices
                $opt_prices = ovabrw_get_meta_data( 'ova_prices', $field );

                // Get option min quantity
                $opt_min_qtys = ovabrw_get_meta_data( 'ova_min_qtys', $field );

                // Get option max quantity
                $opt_max_qtys = ovabrw_get_meta_data( 'ova_qtys', $field );
            ?>
                <tr class="ovabrw-cckf-options">
                    <td class="label ovabrw-required">
                        <?php esc_html_e( 'Options', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <table class="widefat" width="100%">
                            <thead>
                                <tr>
                                    <th class="ovabrw-required" width="30%">
                                        <?php esc_html_e( 'Value', 'ova-brw' ); ?>
                                    </th>
                                    <th width="30%">
                                        <?php esc_html_e( 'Price', 'ova-brw' ); ?>
                                    </th>
                                    <th width="15%">
                                        <?php esc_html_e( 'Min quantity', 'ova-brw' ); ?>
                                    </th>
                                    <th width="15%">
                                        <?php esc_html_e( 'Max quantity', 'ova-brw' ); ?>
                                    </th>
                                    <th width="10%">
                                        <?php esc_html_e( 'Actions', 'ova-brw' ); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="ovabrw-cckf-option-sortable">
                                <?php foreach ( $opt_values as $i => $val ):
                                    // Price
                                    $price = ovabrw_get_meta_data( $i, $opt_prices );

                                    // Min qty
                                    $min_qty = ovabrw_get_meta_data( $i, $opt_min_qtys );

                                    // Max qty
                                    $max_qty = ovabrw_get_meta_data( $i, $opt_max_qtys );
                                ?>
                                    <tr>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'class'         => 'ovabrw-input-required',
                                                'name'          => 'ova_values[]',
                                                'value'         => $val,
                                                'placeholder'   => esc_html__( 'text', 'ova-brw' ),
                                                'attrs'         => [
                                                    'autocomplete' => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'name'          => 'ova_prices[]',
                                                'value'         => '' != $price ? (float)$price : '',
                                                'placeholder'   => esc_html__( 'price', 'ova-brw' ),
                                                'attrs'         => [
                                                    'autocomplete' => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'type'          => 'number',
                                                'name'          => 'ova_min_qtys[]',
                                                'value'         => '' != $min_qty ? (int)$min_qty : '',
                                                'placeholder'   => esc_html__( 'number', 'ova-brw' ),
                                                'attrs'         => [
                                                    'min'           => 0,
                                                    'autocomplete'  => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'type'          => 'number',
                                                'name'          => 'ova_qtys[]',
                                                'value'         => '' != $max_qty ? (int)$max_qty : '',
                                                'placeholder'   => esc_html__( 'number', 'ova-brw' ),
                                                'attrs'         => [
                                                    'min'           => 0,
                                                    'autocomplete'  => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <span class="btn btn-add-new" title="<?php esc_attr_e( 'Add', 'ova-brw' ); ?>">+</span>
                                            <span class="btn btn-add-remove" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">x</span>
                                            <span class="dashicons dashicons-menu-alt3" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">
                                        <button class="button ovabrw-cckf-add-option">
                                            <?php esc_html_e( 'Add option', 'ova-brw' ); ?>
                                        </button>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'Value:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
                            </em>
                        </span>
                        <br>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'Min quantity:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Minimum quantity per booking', 'ova-brw' ); ?>
                            </em>
                        </span>
                        <br>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'Max quantity:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Maximum quantity per booking', 'ova-brw' ); ?>
                            </em>
                        </span>
                    </td>
                </tr>
            <?php elseif ( 'checkbox' === $type ):
                // Get option ids
                $opt_ids = ovabrw_get_meta_data( 'ova_checkbox_key', $field, [] );

                // Get option values
                $opt_values = ovabrw_get_meta_data( 'ova_checkbox_text', $field );

                // Get option prices
                $opt_prices = ovabrw_get_meta_data( 'ova_checkbox_price', $field );

                // Get option min quantity
                $opt_min_qtys = ovabrw_get_meta_data( 'ova_checkbox_min_qty', $field );

                // Get option max quantity
                $opt_max_qtys = ovabrw_get_meta_data( 'ova_checkbox_qty', $field );
            ?>
                <tr class="ovabrw-cckf-options">
                    <td class="label ovabrw-required">
                        <?php esc_html_e( 'Options', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <table class="widefat" width="100%">
                            <thead>
                                <tr>
                                    <th class="ovabrw-required" width="20%">
                                        <?php esc_html_e( 'ID', 'ova-brw' ); ?>
                                    </th>
                                    <th class="ovabrw-required" width="20%">
                                        <?php esc_html_e( 'Value', 'ova-brw' ); ?>
                                    </th>
                                    <th width="20%">
                                        <?php esc_html_e( 'Price', 'ova-brw' ); ?>
                                    </th>
                                    <th width="15%">
                                        <?php esc_html_e( 'Min quantity', 'ova-brw' ); ?>
                                    </th>
                                    <th width="15%">
                                        <?php esc_html_e( 'Max quantity', 'ova-brw' ); ?>
                                    </th>
                                    <th width="10%">
                                        <?php esc_html_e( 'Actions', 'ova-brw' ); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="ovabrw-cckf-option-sortable">
                                <?php foreach ( $opt_ids as $i => $opt_id ):
                                    // Get option value
                                    $opt_val = ovabrw_get_meta_data( $i, $opt_values );

                                    // Get option price
                                    $opt_price = ovabrw_get_meta_data( $i, $opt_prices );

                                    // Get option min quantity
                                    $opt_min_qty = ovabrw_get_meta_data( $i, $opt_min_qtys );

                                    // Get option max quantity
                                    $opt_max_qty = ovabrw_get_meta_data( $i, $opt_max_qtys );
                                ?>
                                    <tr>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'class'         => 'ovabrw-input-required',
                                                'name'          => 'ova_checkbox_key[]',
                                                'value'         => $opt_id,
                                                'placeholder'   => esc_html__( 'text', 'ova-brw' ),
                                                'attrs'         => [
                                                    'autocomplete' => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'class'         => 'ovabrw-input-required',
                                                'name'          => 'ova_checkbox_text[]',
                                                'value'         => $opt_val,
                                                'placeholder'   => esc_html__( 'text', 'ova-brw' ),
                                                'attrs'         => [
                                                    'autocomplete' => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'name'          => 'ova_checkbox_price[]',
                                                'value'         => '' != $opt_price ? (float)$opt_price : '',
                                                'placeholder'   => esc_html__( 'price', 'ova-brw' ),
                                                'attrs'         => [
                                                    'autocomplete' => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'type'          => 'number',
                                                'name'          => 'ova_checkbox_min_qty[]',
                                                'value'         => '' != $opt_min_qty ? (int)$opt_min_qty : '',
                                                'placeholder'   => esc_html__( 'number', 'ova-brw' ),
                                                'attrs'         => [
                                                    'min'           => 0,
                                                    'autocomplete'  => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'type'          => 'number',
                                                'name'          => 'ova_checkbox_qty[]',
                                                'value'         => '' != $opt_max_qty ? (int)$opt_max_qty : '',
                                                'placeholder'   => esc_html__( 'number', 'ova-brw' ),
                                                'attrs'         => [
                                                    'min'           => 0,
                                                    'autocomplete'  => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <span class="btn btn-add-new" title="<?php esc_attr_e( 'Add', 'ova-brw' ); ?>">+</span>
                                            <span class="btn btn-add-remove" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">x</span>
                                            <span class="dashicons dashicons-menu-alt3" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">
                                        <button class="button ovabrw-cckf-add-option">
                                            <?php esc_html_e( 'Add option', 'ova-brw' ); ?>
                                        </button>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'ID:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
                            </em>
                        </span>
                        <br>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'Min quantity:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Minimum quantity per booking', 'ova-brw' ); ?>
                            </em>
                        </span>
                        <br>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'Max quantity:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Maximum quantity per booking', 'ova-brw' ); ?>
                            </em>
                        </span>
                    </td>
                </tr>
            <?php elseif ( 'select' === $type ):
                // Get option ids
                $opt_ids = ovabrw_get_meta_data( 'ova_options_key', $field, [] );

                // Get option values
                $opt_values = ovabrw_get_meta_data( 'ova_options_text', $field );

                // Get option prices
                $opt_prices = ovabrw_get_meta_data( 'ova_options_price', $field );

                // Get option min quantity
                $opt_min_qtys = ovabrw_get_meta_data( 'ova_options_min_qty', $field );

                // Get option max quantity
                $opt_max_qtys = ovabrw_get_meta_data( 'ova_options_qty', $field );
            ?>
                <tr class="ovabrw-cckf-options">
                    <td class="label ovabrw-required">
                        <?php esc_html_e( 'Options', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <table class="widefat" width="100%">
                            <thead>
                                <tr>
                                    <th class="ovabrw-required" width="20%">
                                        <?php esc_html_e( 'ID', 'ova-brw' ); ?>
                                    </th>
                                    <th class="ovabrw-required" width="20%">
                                        <?php esc_html_e( 'Value', 'ova-brw' ); ?>
                                    </th>
                                    <th width="20%">
                                        <?php esc_html_e( 'Price', 'ova-brw' ); ?>
                                    </th>
                                    <th width="15%">
                                        <?php esc_html_e( 'Min quantity', 'ova-brw' ); ?>
                                    </th>
                                    <th width="15%">
                                        <?php esc_html_e( 'Max quantity', 'ova-brw' ); ?>
                                    </th>
                                    <th width="10%">
                                        <?php esc_html_e( 'Actions', 'ova-brw' ); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="ovabrw-cckf-option-sortable">
                                <?php foreach ( $opt_ids as $i => $opt_id ):
                                    // Get option value
                                    $opt_val = ovabrw_get_meta_data( $i, $opt_values );

                                    // Get option price
                                    $opt_price = ovabrw_get_meta_data( $i, $opt_prices );

                                    // Get option min quantity
                                    $opt_min_qty = ovabrw_get_meta_data( $i, $opt_min_qtys );

                                    // Get option max quantity
                                    $opt_max_qty = ovabrw_get_meta_data( $i, $opt_max_qtys );
                                ?>
                                    <tr>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'class'         => 'ovabrw-input-required',
                                                'name'          => 'ova_options_key[]',
                                                'value'         => $opt_id,
                                                'placeholder'   => esc_html__( 'text', 'ova-brw' ),
                                                'attrs'         => [
                                                    'autocomplete' => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'class'         => 'ovabrw-input-required',
                                                'name'          => 'ova_options_text[]',
                                                'value'         => $opt_val,
                                                'placeholder'   => esc_html__( 'text', 'ova-brw' ),
                                                'attrs'         => [
                                                    'autocomplete' => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'name'          => 'ova_options_price[]',
                                                'value'         => '' != $opt_price ? (float)$opt_price : '',
                                                'placeholder'   => esc_html__( 'price', 'ova-brw' ),
                                                'attrs'         => [
                                                    'autocomplete' => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'type'          => 'number',
                                                'name'          => 'ova_options_min_qty[]',
                                                'value'         => '' != $opt_min_qty ? (int)$opt_min_qty : '',
                                                'placeholder'   => esc_html__( 'number', 'ova-brw' ),
                                                'attrs'         => [
                                                    'min'           => 0,
                                                    'autocomplete'  => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <?php ovabrw_wp_text_input([
                                                'type'          => 'number',
                                                'name'          => 'ova_options_qty[]',
                                                'value'         => '' != $opt_max_qty ? (int)$opt_max_qty : '',
                                                'placeholder'   => esc_html__( 'number', 'ova-brw' ),
                                                'attrs'         => [
                                                    'min'           => 0,
                                                    'autocomplete'  => 'off'
                                                ]
                                            ]); ?>
                                        </td>
                                        <td>
                                            <span class="btn btn-add-new" title="<?php esc_attr_e( 'Add', 'ova-brw' ); ?>">+</span>
                                            <span class="btn btn-add-remove" title="<?php esc_attr_e( 'Remove', 'ova-brw' ); ?>">x</span>
                                            <span class="dashicons dashicons-menu-alt3" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">
                                        <button class="button ovabrw-cckf-add-option">
                                            <?php esc_html_e( 'Add option', 'ova-brw' ); ?>
                                        </button>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'ID:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
                            </em>
                        </span>
                        <br>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'Min quantity:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Minimum quantity per booking', 'ova-brw' ); ?>
                            </em>
                        </span>
                        <br>
                        <span>
                            <em>
                                <strong>
                                    <?php esc_html_e( 'Max quantity:', 'ova-brw' ); ?>
                                </strong>
                                <?php esc_html_e( 'Maximum quantity per booking', 'ova-brw' ); ?>
                            </em>
                        </span>
                    </td>
                </tr>
            <?php endif; ?>
            <tr class="name">
                <td class="label ovabrw-required">
                    <?php esc_html_e( 'Name', 'ova-brw' ); ?>
                </td>
                <td>
                    <?php ovabrw_wp_text_input([
                        'class'         => 'ovabrw-input-required',
                        'name'          => 'name',
                        'value'         => $name,
                        'placeholder'   => esc_html__( 'enter name', 'ova-brw' ),
                        'attrs'         => [
                            'autocomplete' => 'off'
                        ]
                    ]); ?>
                    <?php ovabrw_wp_text_input([
                        'type'          => 'hidden',
                        'name'          => 'old_name',
                        'value'         => $name
                    ]); ?>
                    <span>
                        <em>
                            <?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
                        </em>
                    </span>
                </td>
            </tr>
            <tr class="label">
                <td class="label ovabrw-required">
                    <?php esc_html_e( 'Label', 'ova-brw' ); ?>
                </td>
                <td>
                    <?php ovabrw_wp_text_input([
                        'class'         => 'ovabrw-input-required',
                        'name'          => 'label',
                        'value'         => ovabrw_get_meta_data( 'label', $field ),
                        'placeholder'   => esc_html__( 'enter label', 'ova-brw' ),
                        'attrs'         => [
                            'autocomplete' => 'off'
                        ]
                    ]); ?>
                </td>
            </tr>
            <tr class="description">
                <td class="label">
                    <?php esc_html_e( 'Description', 'ova-brw' ); ?>
                </td>
                <td>
                    <?php ovabrw_wp_textarea([
                        'name'          => 'description',
                        'value'         => ovabrw_get_meta_data( 'description', $field ),
                        'placeholder'   => esc_html__( 'enter your text here...', 'ova-brw' )
                    ]); ?>
                </td>
            </tr>
            <?php if ( !in_array( $type, ['radio', 'checkbox', 'select', 'file', 'price'] ) ): ?>
                <tr class="placeholder">
                    <td class="label">
                        <?php esc_html_e( 'Placeholder', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'name'          => 'placeholder',
                            'value'         => ovabrw_get_meta_data( 'placeholder', $field ),
                            'placeholder'   => esc_html__( 'enter placeholder', 'ova-brw' ),
                            'attrs'         => [
                                'autocomplete' => 'off'
                            ]
                        ]); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ( 'file' != $type ): ?>
                <tr class="default">
                    <td class="label">
                        <?php esc_html_e( 'Default', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php if ( 'number' === $type ) {
                            ovabrw_wp_text_input([
                                'type'          => 'number',
                                'name'          => 'default',
                                'value'         => '' != ovabrw_get_meta_data( 'default', $field ) ? (int)ovabrw_get_meta_data( 'default', $field ) : '',
                                'placeholder'   => esc_html__( 'enter number', 'ova-brw' ),
                                'attrs'         => [
                                    'autocomplete' => 'off'
                                ]
                            ]);
                        } elseif ( 'tel' === $type ) {
                            ovabrw_wp_text_input([
                                'type'          => 'tel',
                                'name'          => 'default',
                                'value'         => ovabrw_get_meta_data( 'default', $field ),
                                'placeholder'   => esc_html__( 'enter phone number', 'ova-brw' ),
                                'attrs'         => [
                                    'autocomplete' => 'off'
                                ]
                            ]);
                        } elseif ( 'email' === $type ) {
                            ovabrw_wp_text_input([
                                'type'          => 'email',
                                'name'          => 'default',
                                'value'         => ovabrw_get_meta_data( 'default', $field ),
                                'placeholder'   => esc_html__( 'your_email@gmail.com', 'ova-brw' ),
                                'attrs'         => [
                                    'autocomplete' => 'off'
                                ]
                            ]);
                        } elseif ( 'date' === $type ) {
                            ovabrw_wp_text_input([
                                'id'        => ovabrw_unique_id( 'cckf_default_date' ),
                                'class'     => 'ovabrw-datepicker',
                                'name'      => 'default_date',
                                'value'     => ovabrw_get_meta_data( 'default_date', $field ),
                                'data_type' => 'datepicker'
                            ]);
                        } elseif ( 'price' === $type ) {
                            ovabrw_wp_text_input([
                                'name'          => 'default',
                                'value'         => '' != ovabrw_get_meta_data( 'default', $field ) ? (float)ovabrw_get_meta_data( 'default', $field ) : '',
                                'placeholder'   => esc_html__( 'price', 'ova-brw' ),
                                'attrs'         => [
                                    'autocomplete' => 'off'
                                ]
                            ]);
                        } else {
                            ovabrw_wp_text_input([
                                'name'          => 'default',
                                'value'         => ovabrw_get_meta_data( 'default', $field ),
                                'placeholder'   => esc_html__( 'enter text', 'ova-brw' ),
                                'attrs'         => [
                                    'autocomplete' => 'off'
                                ]
                            ]);
                        } ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ( 'number' === $type ): ?>
                <tr class="min">
                    <td class="label">
                        <?php esc_html_e( 'Min', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'type'          => 'number',
                            'name'          => 'min',
                            'value'         => '' != ovabrw_get_meta_data( 'min', $field ) ? (int)ovabrw_get_meta_data( 'min', $field ) : '',
                            'placeholder'   => esc_html__( 'enter number', 'ova-brw' ),
                            'attrs'         => [
                                'autocomplete' => 'off'
                            ]
                        ]); ?>
                    </td>
                </tr>
                <tr class="max">
                    <td class="label">
                        <?php esc_html_e( 'Max', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'type'          => 'number',
                            'name'          => 'max',
                            'value'         => '' != ovabrw_get_meta_data( 'max', $field ) ? (int)ovabrw_get_meta_data( 'max', $field ) : '',
                            'placeholder'   => esc_html__( 'enter number', 'ova-brw' ),
                            'attrs'         => [
                                'autocomplete' => 'off'
                            ]
                        ]); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ( 'date' === $type ): ?>
                <tr class="min">
                    <td class="label">
                        <?php esc_html_e( 'Min', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'id'        => ovabrw_unique_id( 'cckf_min_date' ),
                            'class'     => 'ovabrw-datepicker',
                            'name'      => 'min_date',
                            'value'     => ovabrw_get_meta_data( 'min_date', $field ),
                            'data_type' => 'datepicker'
                        ]); ?>
                    </td>
                </tr>
                <tr class="max">
                    <td class="label">
                        <?php esc_html_e( 'Max', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'id'        => ovabrw_unique_id( 'cckf_max_date' ),
                            'class'     => 'ovabrw-datepicker',
                            'name'      => 'max_date',
                            'value'     => ovabrw_get_meta_data( 'max_date', $field ),
                            'data_type' => 'datepicker'
                        ]); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ( 'price' === $type ): ?>
                <tr class="min-price">
                    <td class="label ovabrw-required">
                        <?php esc_html_e( 'Min', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'class'         => 'ovabrw-input-required',
                            'name'          => 'min_price',
                            'value'         => (float)ovabrw_get_meta_data( 'min_price', $field ),
                            'placeholder'   => esc_html__( 'price', 'ova-brw' ),
                            'attrs'         => [
                                'autocomplete' => 'off'
                            ]
                        ]); ?>
                    </td>
                </tr>
                <tr class="max-price">
                    <td class="label ovabrw-required">
                        <?php esc_html_e( 'Max', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'class'         => 'ovabrw-input-required',
                            'name'          => 'max_price',
                            'value'         => (float)ovabrw_get_meta_data( 'max_price', $field ),
                            'placeholder'   => esc_html__( 'price', 'ova-brw' ),
                            'attrs'         => [
                                'autocomplete' => 'off'
                            ]
                        ]); ?>
                    </td>
                </tr>
                <tr class="step-price">
                    <td class="label ovabrw-required">
                        <?php esc_html_e( 'Step', 'ova-brw' ); ?>
                    </td>
                    <td>
                        <?php ovabrw_wp_text_input([
                            'class'         => 'ovabrw-input-required',
                            'name'          => 'step',
                            'value'         => (float)ovabrw_get_meta_data( 'step', $field ),
                            'placeholder'   => esc_html__( 'number', 'ova-brw' ),
                            'attrs'         => [
                                'autocomplete' => 'off'
                            ]
                        ]); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr class="class">
                <td class="label">
                    <?php esc_html_e( 'Class', 'ova-brw' ); ?>
                </td>
                <td>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'class',
                        'value'         => ovabrw_get_meta_data( 'class', $field ),
                        'placeholder'   => esc_html__( 'enter class name', 'ova-brw' ),
                        'attrs'         => [
                            'autocomplete' => 'off'
                        ]
                    ]); ?>
                </td>
            </tr>
            <?php add_action( OVABRW_PREFIX.'after_popup_add_cckf', $action, $type, $name ); ?>
            <tr class="status">
                <td class="label"></td>
                <td>
                    <label>
                        <?php ovabrw_wp_text_input([
                            'type'      => 'checkbox',
                            'name'      => 'required',
                            'value'     => 'on',
                            'checked'   => 'on' == ovabrw_get_meta_data( 'required', $field ) ? true : false
                        ]);

                        esc_html_e( 'Required', 'ova-brw' ); ?>
                    </label>
                    <label>
                        <?php ovabrw_wp_text_input([
                            'type'      => 'checkbox',
                            'name'      => 'enabled',
                            'value'     => 'on',
                            'checked'   => 'on' == ovabrw_get_meta_data( 'enabled', $field ) ? true : false
                        ]);

                        esc_html_e( 'Enabled', 'ova-brw' ); ?>
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="ovabrw-cckf-submit">
        <button type="submit" class="button button-primary">
            <?php esc_html_e( 'Save', 'ova-brw' ); ?>
        </button>
        <div class="ovabrw-loading">
            <span class="dashicons dashicons-update-alt"></span>
        </div>
    </div>
</form>