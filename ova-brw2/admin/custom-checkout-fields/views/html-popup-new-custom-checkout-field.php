<?php defined( 'ABSPATH' ) || exit(); ?>

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
                            'value' => '20'
                        ]); ?>
                        <span>
                            <em>
                                <?php esc_html_e( 'Default: 20MB', 'ova-brw' ); ?>
                            </em>
                        </span>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ( 'radio' === $type ): ?>
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
                                <tr>
                                    <td>
                                        <?php ovabrw_wp_text_input([
                                            'class'         => 'ovabrw-input-required',
                                            'name'          => 'ova_values[]',
                                            'value'         => 'option_1',
                                            'placeholder'   => esc_html__( 'text', 'ova-brw' ),
                                            'attrs'         => [
                                                'autocomplete' => 'off'
                                            ]
                                        ]); ?>
                                    </td>
                                    <td>
                                        <?php ovabrw_wp_text_input([
                                            'name'          => 'ova_prices[]',
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
            <?php elseif ( 'checkbox' === $type ): ?>
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
                                <tr>
                                    <td>
                                        <?php ovabrw_wp_text_input([
                                            'class'         => 'ovabrw-input-required',
                                            'name'          => 'ova_checkbox_key[]',
                                            'value'         => 'option_1',
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
                                            'value'         => 'Option 1',
                                            'placeholder'   => esc_html__( 'text', 'ova-brw' ),
                                            'attrs'         => [
                                                'autocomplete' => 'off'
                                            ]
                                        ]); ?>
                                    </td>
                                    <td>
                                        <?php ovabrw_wp_text_input([
                                            'name'          => 'ova_checkbox_price[]',
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
            <?php elseif ( 'select' === $type ): ?>
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
                                <tr>
                                    <td>
                                        <?php ovabrw_wp_text_input([
                                            'class'         => 'ovabrw-input-required',
                                            'name'          => 'ova_options_key[]',
                                            'value'         => 'option_1',
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
                                            'value'         => 'Option 1',
                                            'placeholder'   => esc_html__( 'text', 'ova-brw' ),
                                            'attrs'         => [
                                                'autocomplete' => 'off'
                                            ]
                                        ]); ?>
                                    </td>
                                    <td>
                                        <?php ovabrw_wp_text_input([
                                            'name'          => 'ova_options_price[]',
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
                                'placeholder'   => esc_html__( 'enter number', 'ova-brw' ),
                                'attrs'         => [
                                    'autocomplete' => 'off'
                                ]
                            ]);
                        } elseif ( 'tel' === $type ) {
                            ovabrw_wp_text_input([
                                'type'          => 'tel',
                                'name'          => 'default',
                                'placeholder'   => esc_html__( 'enter phone number', 'ova-brw' ),
                                'attrs'         => [
                                    'autocomplete' => 'off'
                                ]
                            ]);
                        } elseif ( 'email' === $type ) {
                            ovabrw_wp_text_input([
                                'type'          => 'email',
                                'name'          => 'default',
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
                                'data_type' => 'datepicker'
                            ]);
                        } elseif ( 'price' === $type ) {
                            ovabrw_wp_text_input([
                                'name'          => 'default',
                                'placeholder'   => esc_html__( 'price', 'ova-brw' ),
                                'attrs'         => [
                                    'autocomplete' => 'off'
                                ]
                            ]);
                        } else {
                            ovabrw_wp_text_input([
                                'name'          => 'default',
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
                            'value'         => 10,
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
                            'type'  => 'checkbox',
                            'name'  => 'required',
                            'value' => 'on'
                        ]);

                        esc_html_e( 'Required', 'ova-brw' ); ?>
                    </label>
                    <label>
                        <?php ovabrw_wp_text_input([
                            'type'      => 'checkbox',
                            'name'      => 'enabled',
                            'value'     => 'on',
                            'checked'   => true
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