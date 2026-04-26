<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get custom checkout fields
$cckf = ovabrw_recursive_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

?>

<div class="wrap ovabrw-cckf">
    <h2><?php esc_html_e( 'Custom Checkout Fields', 'ova-brw' ); ?></h2>
    <div class="heading">
        <div class="ovabrw-cckf-action">
            <button type="button" class="button" name="ovabrw-cckf-action" value="add" title="<?php esc_attr_e( 'Add field', 'ova-brw' ); ?>">
                <?php esc_html_e( 'Add field', 'ova-brw' ); ?>
            </button>
            <button type="submit" class="button" name="ovabrw-cckf-action" value="required" title="<?php esc_attr_e( 'Required', 'ova-brw' ); ?>">
                <?php esc_html_e( 'Required', 'ova-brw' ); ?>
            </button>
            <button type="submit" class="button" name="ovabrw-cckf-action" value="optional" title="<?php esc_attr_e( 'Optional', 'ova-brw' ); ?>">
                <?php esc_html_e( 'Optional', 'ova-brw' ); ?>
            </button>
            <button type="submit" class="button" name="ovabrw-cckf-action" value="enable" title="<?php esc_attr_e( 'Enable', 'ova-brw' ); ?>">
                <?php esc_html_e( 'Enable', 'ova-brw' ); ?>
            </button>
            <button type="submit" class="button" name="ovabrw-cckf-action" value="disable" title="<?php esc_attr_e( 'Disable', 'ova-brw' ); ?>">
                <?php esc_html_e( 'Disable', 'ova-brw' ); ?>
            </button>
            <button type="submit" class="button" name="ovabrw-cckf-action" value="delete" title="<?php esc_attr_e( 'Delete', 'ova-brw' ); ?>">
                <?php esc_html_e( 'Delete', 'ova-brw' ); ?>
            </button>
            <div class="ovabrw-loading">
                <span class="dashicons dashicons-update-alt"></span>
            </div>
        </div>
    </div>
    <div class="content">
    <?php if ( ovabrw_array_exists( $cckf ) ): ?>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <td class="check-column">
                        <?php ovabrw_wp_text_input([
                            'type'  => 'checkbox',
                            'class' => 'ovabrw-check-all',
                            'name'  => 'ovabrw-check-all'
                        ]); ?>
                    </td>
                    <th class="type">
                        <?php esc_html_e( 'Type', 'ova-brw' ); ?>
                    </th>
                    <th class="name">
                        <?php esc_html_e( 'Name', 'ova-brw' ); ?>
                    </th>
                    <th class="label">
                        <?php esc_html_e( 'Label', 'ova-brw' ); ?>
                    </th>
                    <th class="required">
                        <?php esc_html_e( 'Required', 'ova-brw' ); ?>
                    </th>
                    <th class="enabled">
                        <?php esc_html_e( 'Enabled', 'ova-brw' ); ?>
                    </th>
                    <th class="actions">
                        <?php esc_html_e( 'Actions', 'ova-brw' ); ?>
                    </th>
                </tr>
            </thead>
            <tbody class="ovabrw-cckf-sortable">
            <?php foreach ( $cckf as $name => $field ): ?>
                <?php if ( ovabrw_get_meta_data( 'enabled', $field ) ): ?>
                    <tr>
                <?php else: ?>
                    <tr class="disabled">
                <?php endif; ?>
                    <th class="ovabrw-check-column">
                        <?php ovabrw_wp_text_input([
                            'type'  => 'checkbox',
                            'name'  => 'fields[]',
                            'value' => $name
                        ]); ?>
                    </th>
                    <td>
                        <?php echo esc_html( ovabrw_get_meta_data( 'type', $field ) ); ?>
                        <?php ovabrw_wp_text_input([
                            'type'  => 'hidden',
                            'name'  => 'type',
                            'value' => ovabrw_get_meta_data( 'type', $field )
                        ]); ?>
                    </td>
                    <td><?php echo esc_html( $name ); ?></td>
                    <td><?php echo esc_html( ovabrw_get_meta_data( 'label', $field ) ); ?></td>
                    <td class="required">
                        <?php if ( ovabrw_get_meta_data( 'required', $field ) ): ?>
                            <span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>"></span>
                        <?php endif; ?>
                    </td>
                    <td class="enable">
                        <?php if ( ovabrw_get_meta_data( 'enabled', $field ) ): ?>
                            <span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>"></span>
                        <?php else: ?>
                            <span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>" style="display: none;"></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="actions">
                            <span class="dashicons dashicons-edit" title="<?php esc_attr_e( 'Edit', 'ova-brw' ); ?>"></span>
                            <span>|</span>
                            <?php if ( ovabrw_get_meta_data( 'enabled', $field ) ): ?>
                                <span class="dashicons dashicons-visibility active" title="<?php esc_attr_e( 'Disable', 'ova-brw' ); ?>"></span>
                                <span class="dashicons dashicons-hidden" title="<?php esc_attr_e( 'Enable', 'ova-brw' ); ?>"></span>
                            <?php else: ?>
                                <span class="dashicons dashicons-visibility" title="<?php esc_attr_e( 'Disable', 'ova-brw' ); ?>"></span>
                                <span class="dashicons dashicons-hidden active" title="<?php esc_attr_e( 'Enable', 'ova-brw' ); ?>"></span>
                            <?php endif; ?>
                            <span>|</span>
                            <span class="dashicons dashicons-trash" title="<?php esc_attr_e( 'Delete', 'ova-brw' ); ?>"></span>
                            <span>|</span>
                            <span class="dashicons dashicons-menu" title="<?php esc_attr_e( 'Grab & Drop', 'ova-brw' ); ?>"></span>
                            <div class="ovabrw-loading">
                                <span class="dashicons dashicons-update-alt"></span>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="check-column">
                        <?php ovabrw_wp_text_input([
                            'type'  => 'checkbox',
                            'class' => 'ovabrw-check-all',
                            'name'  => 'ovabrw-check-all'
                        ]); ?>
                    </td>
                    <th class="type">
                        <?php esc_html_e( 'Type', 'ova-brw' ); ?>
                    </th>
                    <th class="name">
                        <?php esc_html_e( 'Name', 'ova-brw' ); ?>
                    </th>
                    <th class="label">
                        <?php esc_html_e( 'Label', 'ova-brw' ); ?>
                    </th>
                    <th class="required">
                        <?php esc_html_e( 'Required', 'ova-brw' ); ?>
                    </th>
                    <th class="enabled">
                        <?php esc_html_e( 'Enabled', 'ova-brw' ); ?>
                    </th>
                    <th class="actions">
                        <?php esc_html_e( 'Actions', 'ova-brw' ); ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
    </div>
    <div class="ovabrw-popup-cckf">
        <div class="ovabrw-popup-cckf-field"></div>
    </div>
    <?php
        // Get datepicker
        $datepicker = ovabrw_admin_datepicker_options();

        // Min year
        $datepicker['AmpPlugin']['dropdown']['minYear'] = apply_filters( OVABRW_PREFIX.'cckf_min_year', gmdate('Y') - 125 );

        // Max year
        $datepicker['AmpPlugin']['dropdown']['maxYear'] = apply_filters( OVABRW_PREFIX.'cckf_max_year', gmdate('Y') + 3 );

        // Min date
        $datepicker['LockPlugin']['minDate'] = apply_filters( OVABRW_PREFIX.'cckf_min_date', '' );

        // Max date
        $datepicker['LockPlugin']['maxDate'] = apply_filters( OVABRW_PREFIX.'cckf_max_date', '' );

        // Render input hidden
        ovabrw_wp_text_input([
            'type'  => 'hidden',
            'name'  => 'ovabrw-datepicker-options',
            'value' => wp_json_encode( $datepicker )
        ]);
    ?>
</div>