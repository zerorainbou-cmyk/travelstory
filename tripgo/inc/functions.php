<?php if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Get current ID of post/page, etc
 */
if ( !function_exists( 'tripgo_get_current_id' ) ) {
	function tripgo_get_current_id() {
        // Current page
	    $current_page_id = '';
	    
	    if ( class_exists( 'woocommerce' ) ) {
	        if ( is_shop() ) {
	            $current_page_id = get_option( 'woocommerce_shop_page_id' );
	        } elseif ( is_cart() ) {
	            $current_page_id = get_option( 'woocommerce_cart_page_id' );
	        } elseif ( is_checkout() ) {
	            $current_page_id = get_option( 'woocommerce_checkout_page_id' );
	        } elseif ( is_account_page() ) {
	            $current_page_id = get_option( 'woocommerce_myaccount_page_id' );
	        } elseif ( is_view_order_page() ) {
	            $current_page_id = get_option( 'woocommerce_view_order_page_id' );
	        }
	    }

	    if ( '' === $current_page_id ) {
	        if ( is_home () && is_front_page () ) {
	            $current_page_id = '';
	        } elseif ( is_home () ) {
	            $current_page_id = get_option( 'page_for_posts' );
	        } elseif ( is_search () || is_category () || is_tag () || is_tax () || is_archive() ) {
	            $current_page_id = '';
	        } elseif ( !is_404 () ) {
	           $current_page_id = get_the_id();
	        } 
	    }

	    return apply_filters( 'tripgo_get_current_id', $current_page_id );
	}
}

/**
 * is elementor active
 */
if ( !function_exists( 'tripgo_is_elementor_active' ) ) {
    function tripgo_is_elementor_active() {
        return did_action( 'elementor/loaded' );
    }
}

/**
 * is woo active
 */
if ( !function_exists( 'tripgo_is_woo_active' ) ) {
    function tripgo_is_woo_active() {
        return class_exists( 'woocommerce' );    
    }
}

/**
 * is blog archive
 */
if ( !function_exists( 'tripgo_is_blog_archive' ) ) {
    function tripgo_is_blog_archive() {
        return ( is_home() && is_front_page() ) || is_archive() || is_category() || is_tag() || is_home();
    }
}

/**
 * is woo archive
 */
if ( !function_exists( 'tripgo_is_woo_archive' ) ) {
    function tripgo_is_woo_archive() {
        return is_post_type_archive( 'product' ) || is_tax( 'product_cat' ) || is_tax( 'product_tag' );
    }
}

/**
 * Get ID from Slug of Header Footer Builder - Post Type
 */
if ( !function_exists( 'tripgo_get_id_by_slug' ) ) {
    function tripgo_get_id_by_slug( $page_slug ) {
        // Get page ID
        $page = get_page_by_path( $page_slug, OBJECT, 'ova_framework_hf_el' );
        if ( $page ) {
            return apply_filters( 'tripgo_get_id_by_slug', $page->ID, $page_slug );
        } else {
            return null;
        }
    }
}

/**
 * Google Font sanitization
 */
if ( !function_exists( 'tripgo_google_font_sanitization' ) ) {
    function tripgo_google_font_sanitization( $font ) {
        // Get fonts
        $default_fonts = json_decode( $font, true );
        if ( tripgo_array_exists( $default_fonts ) ) {
            foreach ( $default_fonts as $k => $value ) {
                $default_fonts[$k] = sanitize_text_field( $value );
            }
            $font = json_encode( $default_fonts );
        } else {
            $font = json_encode( sanitize_text_field( $default_fonts ) );
        }

        return apply_filters( 'tripgo_google_font_sanitization', $font );
    }
}

/**
 * Default Primary Font in Customize
 */
if ( !function_exists( 'tripgo_default_primary_font' ) ) {
    function tripgo_default_primary_font() {
        return apply_filters( 'tripgo_default_primary_font', json_encode([
            'font'          => 'HK Grotesk',
            'regularweight' => '300,400,500,600,700,800,900',
            'category'      => 'serif'
        ]));
    }
}

/**
 * Woo sidebar
 */
if ( !function_exists( 'tripgo_woo_sidebar' ) ) {
    function tripgo_woo_sidebar() {
        if ( class_exists( 'woocommerce' ) && is_product() ) {
            return apply_filters( 'tripgo_woo_sidebar', get_theme_mod( 'woo_product_layout', 'woo_layout_1c' ) );
        } else {
            return apply_filters( 'tripgo_woo_sidebar', get_theme_mod( 'woo_archive_layout', 'woo_layout_1c' ) );
        }
    }
}

/**
 * Blog show media
 */
if ( !function_exists( 'tripgo_blog_show_media' ) ) {
    function tripgo_blog_show_media() {
        return apply_filters( 'tripgo_blog_show_media', sanitize_text_field( tripgo_get_meta_data( 'show_media', $_GET, get_theme_mod( 'blog_archive_show_media', 'yes' ) ) ) );
    }
}

/**
 * Blog show title
 */
if ( !function_exists( 'tripgo_blog_show_title' ) ) {
    function tripgo_blog_show_title() {
        return apply_filters( 'tripgo_blog_show_title', sanitize_text_field( tripgo_get_meta_data( 'show_title', $_GET, get_theme_mod( 'blog_archive_show_title', 'yes' ) ) ) );
    }
}

/**
 * Blog show date
 */
if ( !function_exists( 'tripgo_blog_show_date' ) ) {
    function tripgo_blog_show_date() {
        return apply_filters( 'tripgo_blog_show_date', sanitize_text_field( ovabrw_get_meta_data( 'show_date', $_GET, get_theme_mod( 'blog_archive_show_date', 'yes' ) ) ) );
    }
}

/**
 * Blog show category
 */
if ( !function_exists( 'tripgo_blog_show_cat' ) ) {
    function tripgo_blog_show_cat() {
        return apply_filters( 'tripgo_blog_show_cat', sanitize_text_field( ovabrw_get_meta_data( 'show_cat', $_GET, get_theme_mod( 'blog_archive_show_cat', 'yes' ) ) ) );
    }
}

/**
 * Blog show author
 */
if ( !function_exists( 'tripgo_blog_show_author' ) ) {
    function tripgo_blog_show_author(){
        return apply_filters( 'tripgo_blog_show_author', sanitize_text_field( ovabrw_get_meta_data( 'show_author', $_GET, get_theme_mod( 'blog_archive_show_author', 'yes' ) ) ) );
    }
}

/**
 * Blog show comment
 */
if ( !function_exists( 'tripgo_blog_show_comment' ) ) {
    function tripgo_blog_show_comment() {
        return apply_filters( 'tripgo_blog_show_comment', sanitize_text_field( ovabrw_get_meta_data( 'show_comment', $_GET, get_theme_mod( 'blog_archive_show_comment', 'yes' ) ) ) );
    }
}

/**
 * Blog show excerpt
 */
if ( !function_exists( 'tripgo_blog_show_excerpt' ) ) {
    function tripgo_blog_show_excerpt() {
        return apply_filters( 'tripgo_blog_show_excerpt', sanitize_text_field( ovabrw_get_meta_data( 'show_excerpt', $_GET, get_theme_mod( 'blog_archive_show_excerpt', 'yes' ) ) ) );
    }
}

/**
 * Show readmore button
 */
if ( !function_exists( 'tripgo_blog_show_readmore' ) ) {
    function tripgo_blog_show_readmore() {
        return apply_filters( 'tripgo_blog_show_readmore', sanitize_text_field( tripgo_get_meta_data( 'show_readmore', $_GET, get_theme_mod( 'blog_archive_show_readmore', 'yes' ) ) ) );
    }
}

/**
 * Post show media
 */
if ( !function_exists( 'tripgo_post_show_media' ) ) {
    function tripgo_post_show_media() {
        return apply_filters( 'tripgo_post_show_media', sanitize_text_field( tripgo_get_meta_data( 'show_media', $_GET, get_theme_mod( 'blog_single_show_media', 'yes' ) ) ) );
    }
}

/**
 * Post show title
 */
if ( !function_exists( 'tripgo_post_show_title' ) ) {
    function tripgo_post_show_title() {
        return apply_filters( 'tripgo_post_show_title', sanitize_text_field( tripgo_get_meta_data( 'show_title', $_GET, get_theme_mod( 'blog_single_show_title', 'yes' ) ) ) );
    }
}

/**
 * Post show date
 */
if ( !function_exists( 'tripgo_post_show_date' ) ) {
    function tripgo_post_show_date() {
        return apply_filters( 'tripgo_post_show_date', sanitize_text_field( tripgo_get_meta_data( 'show_date', $_GET, get_theme_mod( 'blog_single_show_date', 'yes' ) ) ) );
    }
}

/**
 * Post show category
 */
if ( !function_exists( 'tripgo_post_show_cat' ) ) {
    function tripgo_post_show_cat() {
        return apply_filters( 'tripgo_post_show_cat', sanitize_text_field( tripgo_get_meta_data( 'show_cat', $_GET, get_theme_mod( 'blog_single_show_cat', 'yes' ) ) ) );
    }
}

/**
 * Post show author
 */
if ( !function_exists( 'tripgo_post_show_author' ) ) {
    function tripgo_post_show_author() {
        return apply_filters( 'tripgo_post_show_author', sanitize_text_field( tripgo_get_meta_data( 'show_author', $_GET, get_theme_mod( 'blog_single_show_author', 'yes' ) ) ) );
    }
}

/**
 * Post show comment
 */
if ( !function_exists( 'tripgo_post_show_comment' ) ) {
    function tripgo_post_show_comment() {
        return apply_filters( 'tripgo_post_show_comment', sanitize_text_field( tripgo_get_meta_data( 'show_comment', $_GET, get_theme_mod( 'blog_single_show_comment', 'yes' ) ) ) );
    }
}

/**
 * Post show content
 */
if ( !function_exists( 'tripgo_post_show_content' ) ) {
    function tripgo_post_show_content() {
        return apply_filters( 'tripgo_post_show_content', sanitize_text_field( tripgo_get_meta_data( 'show_content', $_GET, get_theme_mod( 'blog_single_show_content', 'yes' ) ) ) );
    }
}

/**
 * Post show tag
 */
if ( !function_exists( 'tripgo_post_show_tag' ) ) {
    function tripgo_post_show_tag() {
        return apply_filters( 'tripgo_post_show_tag', sanitize_text_field( tripgo_get_meta_data( 'show_tag', $_GET, get_theme_mod( 'blog_single_show_tag', 'yes' ) ) ) );
    }
}

/**
 * Post show share social icon
 */
if ( !function_exists( 'tripgo_post_show_share_social_icon' ) ) {
    function tripgo_post_show_share_social_icon() {
        return apply_filters( 'tripgo_post_show_share_social_icon', sanitize_text_field( tripgo_get_meta_data( 'show_share_social_icon', $_GET, get_theme_mod( 'blog_single_show_share_social_icon', 'yes' ) ) ) );
    }
}

/**
 * Post show next & prev button
 */
if ( !function_exists( 'tripgo_post_show_next_prev_post' ) ) {
    function tripgo_post_show_next_prev_post() {
        return apply_filters( 'tripgo_post_show_next_prev_post', sanitize_text_field( tripgo_get_meta_data( 'show_next_prev_post', $_GET, get_theme_mod( 'blog_single_show_next_prev_post', 'yes' ) ) ) );
    }
}

/**
 * Post show leave a reply
 */
if ( !function_exists( 'tripgo_post_show_leave_a_reply' ) ) {
    function tripgo_post_show_leave_a_reply() {
        return apply_filters( 'tripgo_post_show_leave_a_reply', sanitize_text_field( tripgo_get_meta_data( 'show_leave_a_reply', $_GET, get_theme_mod( 'blog_single_show_leave_a_reply', 'yes' ) ) ) );
    }
}

/**
 * Get Gallery ids Product
 */
if ( !function_exists( 'tripgo_get_gallery_ids' ) ) {
    function tripgo_get_gallery_ids( $product_id ) {
        // Get product
        $product = wc_get_product( $product_id );
        if ( $product ) {
            $arr_image_ids = [];

            // Get product image id
            $product_image_id = $product->get_image_id();
            if ( $product_image_id ) {
                array_push( $arr_image_ids, $product_image_id );
            }

            // Get product gallery ids
            $product_gallery_ids = $product->get_gallery_image_ids();
            if ( tripgo_array_exists( $product_gallery_ids ) ) {
                $arr_image_ids = array_merge( $arr_image_ids, $product_gallery_ids );
            }

            return apply_filters( 'tripgo_get_gallery_ids', $arr_image_ids, $product_id );
        }

        return false;
    }
}

/**
 * Get product price
 */
if ( !function_exists( 'tripgo_get_price_product' ) ) {
    function tripgo_get_price_product( $product_id ) {
        // Get product
        $product = wc_get_product( $product_id );
        if ( !$product ) {
            return apply_filters( 'tripgo_get_price_product', [
                'regular_price' => 0,
                'sale_price'    => 0
            ], $product_id );
        }

        // init
        $regular_price = $sale_price = 0;

        if ( $product->is_on_sale() && $product->get_sale_price() ) {
            $regular_price  = $product->get_sale_price();
            $sale_price     = $product->get_regular_price();
        } else {
            $regular_price = $product->get_regular_price();
        }

        return apply_filters( 'tripgo_get_price_product', [
            'regular_price' => $regular_price,
            'sale_price'    => $sale_price
        ], $product_id );
    }
}

/**
 * Get Price - Multi Currency
 */
if ( !function_exists( 'ovabrw_wc_price' ) ) {
    function ovabrw_wc_price( $price = null, $args = [], $convert = true ) {
        $new_price = $price;
        if ( !$price ) $new_price = 0;

        // Get currency
        $current_currency = tripgo_get_meta_data( 'currency', $args );

        // CURCY - Multi Currency for WooCommerce
        // WooCommerce Multilingual & Multicurrency
        if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
            $new_price = wmc_get_price( $price, $current_currency );
        } elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
            if ( $convert ) {
                // WPML multi currency
                global $woocommerce_wpml;

                if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
                    if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

                    $multi_currency     = $woocommerce_wpml->get_multi_currency();
                    $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
                    $WMCP               = new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
                    $new_price          = $WMCP->convert_price_amount( $price, $current_currency );
                }
            }
        } else {
            // nothing
        }
        
        return apply_filters( 'ovabrw_wc_price', wc_price( $new_price, $args ), $price, $args, $convert );
    }
}

/**
 * Convert Price - Multi Currency
 */
if ( !function_exists( 'ovabrw_convert_price' ) ) {
    function ovabrw_convert_price( $price = null, $args = [], $convert = true ) {
        $new_price = $price;
        if ( ! $price ) $new_price = 0;

        // Get currency
        $current_currency = tripgo_get_meta_data( 'currency', $args );

        // CURCY - Multi Currency for WooCommerce
        // WooCommerce Multilingual & Multicurrency
        if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
            $new_price = wmc_get_price( $price, $current_currency );
        } elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
            if ( $convert ) {
                // WPML multi currency
                global $woocommerce_wpml;

                if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
                    if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

                    $multi_currency     = $woocommerce_wpml->get_multi_currency();
                    $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
                    $WMCP               = new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
                    $new_price          = $WMCP->convert_price_amount( $price, $current_currency );
                }
            }
        } else {
            // nothing
        }
        
        return apply_filters( 'ovabrw_convert_price', $new_price, $price, $args, $convert );
    }
}

/**
 * Convert Price in Admin - Multi Currency
 */
if ( !function_exists( 'ovabrw_convert_price_in_admin' ) ) {
    function ovabrw_convert_price_in_admin( $price = null, $currency_code = '' ) {
        $new_price = $price;
        if ( !$price ) $new_price = 0;

        if ( is_admin() && ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) ) {
            $setting = '';
            
            if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
                $setting = WOOMULTI_CURRENCY_F_Data::get_ins();
            }

            if ( is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
                $setting = WOOMULTI_CURRENCY_Data::get_ins();
            }

            if ( ! empty( $setting ) && is_object( $setting ) ) {
                /*Check currency*/
                $selected_currencies = $setting->get_list_currencies();
                $current_currency    = $setting->get_current_currency();

                if ( ! $currency_code || $currency_code === $current_currency ) {
                    return $new_price;
                }

                if ( $new_price ) {
                    if ( $currency_code && isset( $selected_currencies[ $currency_code ] ) ) {
                        $new_price = $price * (float) $selected_currencies[ $currency_code ]['rate'];
                    } else {
                        $new_price = $price * (float) $selected_currencies[ $current_currency ]['rate'];
                    }
                }
            }
        }

        return apply_filters( 'ovabrw_convert_price_in_admin', $new_price, $price, $currency_code );
    }
}

/**
 * Conver number to hours
 */
if ( !function_exists( 'ovabrw_convert_number_to_hours' ) ) {
    function ovabrw_convert_number_to_hours( $number = '' ) {
        if ( !$number ) return false;
        $hours = floor( (float)$number );

        return apply_filters( 'ovabrw_convert_number_to_hours', absint( $hours ), $number );
    }
}

/**
 * Conver number to minutes
 */
if ( !function_exists( 'ovabrw_convert_number_to_minutes' ) ) {
    function ovabrw_convert_number_to_minutes( $number = '' ) {
        if ( ! $number ) return false;

        $hours      = floor( (float)$number );
        $minutes    = round( ( $number - $hours ) * 60 );

        return apply_filters( 'ovabrw_convert_number_to_minutes', absint( $minutes ), $number );
    }
}

/**
 * Check array exists
 */
if ( !function_exists( 'tripgo_array_exists' ) ) {
    function tripgo_array_exists( $arr ) {
        if ( !empty( $arr ) && is_array( $arr ) ) {
            return true;
        }

        return false;
    }
}

/**
 * Get post meta
 */
if ( !function_exists( 'tripgo_get_post_meta' ) ) {
    function tripgo_get_post_meta( $id = null, $name = '', $default = false ) {
        $value = '';

        if ( $id && $name ) {
            $value = get_post_meta( $id, 'ovabrw_'.$name, true );

            if ( empty( $value ) && $default !== false ) {
                $value = $default;
            }
        }

        return apply_filters( 'tripgo_get_post_meta', $value, $id, $name, $default );
    }
}

/**
 * Get meta from data
 */
if ( !function_exists( 'tripgo_get_meta_data' ) ) {
    function tripgo_get_meta_data( $key = '', $args = [], $default = false ) {
        $value = '';

        // Check $args
        if ( empty( $args ) || !is_array( $args ) ) $args = [];

        // Get value by key
        if ( $key !== '' && isset( $args[$key] ) && '' !== $args[$key] ) {
            $value = $args[$key];
        }

        // Set default
        if ( !$value && false !== $default ) {
            $value = $default;
        }

        return apply_filters( 'tripgo_get_meta_data', $value, $key, $args, $default );
    }
}

/**
 * Random unique id
 */
if ( !function_exists( 'tripgo_unique_id' ) ) {
    function tripgo_unique_id( $id = '' ) {
        $unique_id = 'tripgo_'.$id . '_' . time() . '_' . mt_rand();

        return apply_filters( 'tripgo_unique_id', $unique_id, $id );
    }
}

/**
 * Output the text input
 */
if ( !function_exists( 'tripgo_text_input' ) ) {
    function tripgo_text_input( $args = [] ) {
        $args['type']           = tripgo_get_meta_data( 'type', $args, 'text' );
        $args['id']             = tripgo_get_meta_data( 'id', $args );
        $args['class']          = tripgo_get_meta_data( 'class', $args );
        $args['name']           = tripgo_get_meta_data( 'name', $args );
        $args['value']          = tripgo_get_meta_data( 'value', $args );
        $args['default']        = tripgo_get_meta_data( 'default', $args );
        $args['placeholder']    = tripgo_get_meta_data( 'placeholder', $args );
        $args['description']    = tripgo_get_meta_data( 'description', $args );
        $args['required']       = tripgo_get_meta_data( 'required', $args );
        $args['readonly']       = tripgo_get_meta_data( 'readonly', $args );
        $args['checked']        = tripgo_get_meta_data( 'checked', $args );
        $args['disabled']       = tripgo_get_meta_data( 'disabled', $args );
        $args['attrs']          = tripgo_get_meta_data( 'attrs', $args );

        // Set value
        if ( ! $args['value'] && $args['default'] ) {
            $args['value'] = $args['default'];
        }

        // Data type
        $data_type = tripgo_get_meta_data( 'data_type', $args );
        switch ( $data_type ) {
            case 'timepicker':
                // Add class
                $args['class'] .= ' ovabrw-timepicker';

                // Get time format
                $time_format = function_exists( 'ovabrw_get_time_format' ) ? ovabrw_get_time_format() : 'H:i';

                // Set value
                $args['value'] = strtotime( $args['value'] ) ? gmdate( $time_format, strtotime( $args['value'] ) ) : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = function_exists( 'ovabrw_get_time_format_placeholder' ) ? ovabrw_get_time_format_placeholder() : esc_html__( 'H:i', 'tripgo' );
                }
                break;
            case 'datepicker':
                // Add class
                $args['class'] .= ' ovabrw-datepicker';

                // Get date format
                $date_format = function_exists( 'ovabrw_get_date_format' ) ? ovabrw_get_date_format() : 'd-m-Y';

                // Set value
                $args['value']  = strtotime( $args['value'] ) ? gmdate( $date_format, strtotime( $args['value'] ) ) : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = function_exists( 'ovabrw_get_placeholder_date' ) ? ovabrw_get_placeholder_date() : esc_html__( 'DD-MM-YYYY', 'tripgo' );
                }
                break;
            case 'datepicker-field':
                // Add class
                $args['class'] .= ' ovabrw-datepicker-field';

                // Get date format
                $date_format = function_exists( 'ovabrw_get_date_format' ) ? ovabrw_get_date_format() : 'd-m-Y';

                // Set value
                $args['value']  = strtotime( $args['value'] ) ? gmdate( $date_format, strtotime( $args['value'] ) ) : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = function_exists( 'ovabrw_get_placeholder_date' ) ? ovabrw_get_placeholder_date() : esc_html__( 'DD-MM-YYYY', 'tripgo' );
                }
                break;
            case 'datepicker-start':
                // Add class
                $args['class'] .= ' ovabrw-datepicker-start';

                // Get date format
                $date_format = function_exists( 'ovabrw_get_date_format' ) ? ovabrw_get_date_format() : 'd-m-Y';

                // Set value
                $args['value']  = strtotime( $args['value'] ) ? gmdate( $date_format, strtotime( $args['value'] ) ) : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = function_exists( 'ovabrw_get_placeholder_date' ) ? ovabrw_get_placeholder_date() : esc_html__( 'DD-MM-YYYY', 'tripgo' );
                }
                break;
            case 'datepicker-end':
                // Add class
                $args['class'] .= ' ovabrw-datepicker-end';

                // Get date format
                $date_format = function_exists( 'ovabrw_get_date_format' ) ? ovabrw_get_date_format() : 'd-m-Y';

                // Set value
                $args['value']  = strtotime( $args['value'] ) ? gmdate( $date_format, strtotime( $args['value'] ) ) : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = function_exists( 'ovabrw_get_placeholder_date' ) ? ovabrw_get_placeholder_date() : esc_html__( 'DD-MM-YYYY', 'tripgo' );
                }
                break;
            case 'datetimepicker':
                // Add class
                $args['class'] .= ' ovabrw-datetimepicker';

                // Get date time format
                $datetime_format = function_exists( 'ovabrw_get_datetime_format' ) ? ovabrw_get_datetime_format() : 'd-m-Y H:i';

                // Set value
                $args['value'] = strtotime( $args['value'] ) ? gmdate( $datetime_format, strtotime( $args['value'] ) ) : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = function_exists( 'ovabrw_get_datetime_format_placeholder' ) ? ovabrw_get_datetime_format_placeholder() : esc_html__( 'DD-MM-YYYY H:i', 'tripgo' );
                }
                break;
            case 'datetimepicker-start':
                // Add class
                $args['class'] .= ' ovabrw-datetimepicker-start';

                // Get date time format
                $datetime_format = function_exists( 'ovabrw_get_datetime_format' ) ? ovabrw_get_datetime_format() : 'd-m-Y H:i';

                // Set value
                $args['value'] = strtotime( $args['value'] ) ? gmdate( $datetime_format, strtotime( $args['value'] ) ) : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = function_exists( 'ovabrw_get_datetime_format_placeholder' ) ? ovabrw_get_datetime_format_placeholder() : esc_html__( 'DD-MM-YYYY H:i', 'tripgo' );
                }
                break;
            case 'datetimepicker-end':
                // Add class
                $args['class'] .= ' ovabrw-datetimepicker-end';

                // Get date time format
                $datetime_format = function_exists( 'ovabrw_get_datetime_format' ) ? ovabrw_get_datetime_format() : 'd-m-Y H:i';

                // Set value
                $args['value'] = strtotime( $args['value'] ) ? gmdate( $datetime_format, strtotime( $args['value'] ) ) : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = function_exists( 'ovabrw_get_datetime_format_placeholder' ) ? ovabrw_get_datetime_format_placeholder() : esc_html__( 'DD-MM-YYYY H:i', 'tripgo' );
                }
                break;
            case 'number':
                // Set value
                $args['value'] = $args['value'] ? (int)$args['value'] : '';

                // Set placeholder
                if ( !$args['placeholder'] ) {
                    $args['placeholder'] = esc_html__( 'number', 'ova-brw' );
                }
            default:
                break;
        }

        // Custom attribute handling
        $attrs = [];

        if ( tripgo_array_exists( $args['attrs'] ) ) {
            foreach ( $args['attrs'] as $attr => $value ) {
                if ( !$value && $value !== 0 ) continue;
                $attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
            }
        }

        // Required
        if ( $args['required'] ) {
            $args['class'] .= ' ovabrw-input-required';
        }

        // Checked
        if ( $args['checked'] ) {
            $attrs[] = 'checked';
        }

        // Disabled
        if ( $args['disabled'] ) {
            $attrs[] = 'disabled';
        }

        // Read only
        if ( $args['readonly'] ) {
            $attrs[] = 'readonly';
        }

        // Input name
        $name = $args['name'];

        // Item key
        $key = tripgo_get_meta_data( 'key', $args );
        if ( $key ) {
            $name = $args['name'].'['.esc_attr( $key ).']';
        }

        do_action( 'tripgo_before_text_input', $args );

        if ( $args['id'] ) {
            echo '<input type="'.esc_attr( $args['type'] ).'" id="'.esc_attr( $args['id'] ).'" class="'.esc_attr( $args['class'] ).'" name="'.esc_attr( $name ).'" value="'.esc_attr( $args['value'] ).'" placeholder="'.esc_attr( $args['placeholder'] ).'" '.wp_kses_post( implode( ' ', $attrs ) ).' />';
        } else {
            echo '<input type="'.esc_attr( $args['type'] ).'" class="'.esc_attr( $args['class'] ).'" name="'.esc_attr( $name ).'" value="'.esc_attr( $args['value'] ).'" placeholder="'.esc_attr( $args['placeholder'] ).'" '.wp_kses_post( implode( ' ', $attrs ) ).' />';
        }

        // Description
        if ( $args['description'] ) {
            echo '<span class="description">'.wp_kses_post( $args['description'] ).'</span>';
        }

        do_action( 'tripgo_after_text_input', $args );
    }
}

/**
 * Recursive array replace \\
 */
if ( !function_exists( 'tripgo_recursive_replace' ) ) {
    function tripgo_recursive_replace( $find, $replace, $array ) {
        if ( !is_array( $array ) ) {
            return str_replace( $find, $replace, $array );
        }

        foreach ( $array as $key => $value ) {
            $array[$key] = tripgo_recursive_replace( $find, $replace, $value );
        }

        return apply_filters( 'tripgo_recursive_replace', $array, $find, $replace );
    }
}



//추가 20260324
// 1. 달력 HTML 생성 전담 함수 (wpautop 붕괴 방지 + 우커머스 기본 할인 스케줄 완벽 적용)
// 1. 달력 HTML 생성 전담 함수 (원화 표시 및 서체 디자인 수정 버전)
function tripgo_build_calendar_html( $product_id, $cal_ym ) {
    $product = wc_get_product( $product_id );
    if ( ! $product ) return '';

    // 1. 타임슬롯 상태 확인
    $ts_meta = get_post_meta( $product_id, 'ovabrw_enable_time_slot', true );
    $is_time_slot_enabled = ( $ts_meta === 'yes' || $ts_meta === 'on' );

    // 2. 기본 가격 설정
    $base_price = (float) $product->get_regular_price();
    if ( ! $base_price ) $base_price = (float) $product->get_price();
    
    // 3. 데이터 가져오기 (배열 형태 대응을 위해 true/false 혼용 확인 필요)
    $adult_prices = get_post_meta( $product_id, 'ovabrw_schedule_adult_price', true );
    
    // Special Time(특가/출발일) 데이터
    $st_pickup_dates  = get_post_meta( $product_id, 'ovabrw_st_pickup_date', true );
    $st_adult_prices  = get_post_meta( $product_id, 'ovabrw_st_adult_price', true );
    
    // 1번 상품(기간설정/Fixed Dates) 데이터 추가 확인
    $fixed_checkins   = get_post_meta( $product_id, 'ovabrw_check_in_fixed', true );

    $days_map = array(0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday');
    $timestamp = strtotime( $cal_ym . '-01' );
    if ( ! $timestamp ) $timestamp = time();

    $month = date( 'm', $timestamp );
    $year  = date( 'Y', $timestamp );
    $prev_ym = date( 'Y-m', strtotime( '-1 month', $timestamp ) );
    $next_ym = date( 'Y-m', strtotime( '+1 month', $timestamp ) );
    $days_in_month = date( 't', $timestamp );
    $first_day     = date( 'w', mktime( 0, 0, 0, $month, 1, $year ) );

    // CSS 스타일 (디자인 유지)
    $html = '<style>
        .tripgo-grid-calendar { display: grid !important; grid-template-columns: repeat(7, 1fr) !important; border-top: 1px solid #eee !important; border-left: 1px solid #eee !important; background: #fff !important; width: 100% !important; }
        .tripgo-grid-calendar > div { border-right: 1px solid #eee !important; border-bottom: 1px solid #eee !important; padding: 12px 2px !important; box-sizing: border-box; }
        .tripgo-grid-calendar .cal-header { background-color: #fcfcfc !important; font-weight: bold !important; font-size: 13px !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 40px !important; }
        .tripgo-grid-calendar .cal-cell { min-height: 65px !important; display: flex !important; flex-direction: column !important; justify-content: center !important; align-items: center !important; }
        .cal-day-num { font-size: 1.2em !important; font-weight: 700 !important; font-family: "Noto Sans KR", sans-serif !important; margin-bottom: 4px; }
        .cal-price-text { font-size: 0.85em !important; font-weight: 500 !important; color: #ff5722 !important; font-family: Arial, sans-serif !important; }
    </style>';

    $html .= '<div class="custom-ova-calendar-inner">';
    $html .= '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">';
    $html .= '<a href="#" class="cal-nav-btn" data-ym="' . $prev_ym . '" style="text-decoration: none; padding: 6px 12px; background: #fff; border: 1px solid #ddd; border-radius: 4px; color: #333; font-size: 12px;">이전달</a>';
    $html .= '<h4 style="margin: 0; font-weight: bold; font-size: 16px;">' . $year . '년 ' . $month . '월</h4>';
    $html .= '<a href="#" class="cal-nav-btn" data-ym="' . $next_ym . '" style="text-decoration: none; padding: 6px 12px; background: #fff; border: 1px solid #ddd; border-radius: 4px; color: #333; font-size: 12px;">다음달</a>';
    $html .= '</div>';

    $html .= '<div class="tripgo-grid-calendar">';
    $html .= '<div class="cal-header" style="color:#d9534f;">일</div><div class="cal-header">월</div><div class="cal-header">화</div><div class="cal-header">수</div><div class="cal-header">목</div><div class="cal-header">금</div><div class="cal-header" style="color:#0275d8;">토</div>';

    for ( $i = 0; $i < $first_day; $i++ ) {
        $html .= '<div class="cal-cell empty-cell"></div>';
    }

    $current_day = $first_day;
    for ( $day = 1; $day <= $days_in_month; $day++ ) {
        if ( $current_day == 7 ) $current_day = 0;
        
        $day_color = '#333';
        if ( $current_day == 0 ) $day_color = '#d9534f'; 
        if ( $current_day == 6 ) $day_color = '#0275d8'; 

        $current_date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $current_ts       = strtotime( $current_date_str );
        
        $today_price = 0;
        $show_price = false;

        // --- 가격 노출 판단 로직 ---

        // A. 타임슬롯이 켜진 경우: 모든 날짜에 가격 표시 (2번 상품 대응)
        if ( $is_time_slot_enabled ) {
            $day_key = $days_map[$current_day];
            $today_price = $base_price; 
            if ( ! empty( $adult_prices ) && is_array( $adult_prices ) && isset( $adult_prices[ $day_key ][0] ) && $adult_prices[ $day_key ][0] !== '' ) {
                $today_price = (float) $adult_prices[ $day_key ][0];
            }
            $show_price = true;
        }

        // B. Special Time 확인 (이미지 2, 4번 대응)
        if ( ! empty( $st_pickup_dates ) && is_array( $st_pickup_dates ) ) {
            foreach ( $st_pickup_dates as $index => $st_val ) {
                $st_date_only = trim( explode(' ', $st_val)[0] );
                $st_ts = strtotime( str_replace('/', '-', $st_date_only) );
                
                if ( $current_ts == $st_ts ) {
                    if ( isset( $st_adult_prices[$index] ) && $st_adult_prices[$index] !== '' ) {
                        $today_price = (float) $st_adult_prices[$index];
                        $show_price = true; 
                    }
                    break; 
                }
            }
        }

        // C. Fixed Dates(기간설정) 확인 (1번 상품 대응)
        if ( ! $show_price && ! empty( $fixed_checkins ) && is_array( $fixed_checkins ) ) {
            foreach ( $fixed_checkins as $f_date ) {
                $f_date_only = trim( explode(' ', $f_date)[0] );
                $f_ts = strtotime( str_replace('/', '-', $f_date_only) );
                
                if ( $current_ts == $f_ts ) {
                    $today_price = $base_price; // 기간설정은 기본 성인요금 사용
                    $show_price = true;
                    break;
                }
            }
        }

        $html .= '<div class="cal-cell">';
        $html .= '<span class="cal-day-num" style="color:' . $day_color . ';">' . $day . '</span>';
        
        if ( $show_price && $today_price > 0 ) {
            $html .= '<span class="cal-price-text">' . number_format($today_price) . '원</span>';
        }
        
        $html .= '</div>';
        $current_day++;
    }

    while ( $current_day < 7 ) {
        $html .= '<div class="cal-cell empty-cell"></div>';
        $current_day++;
    }

    $html .= '</div></div>'; 
    return str_replace( array( "\r", "\n", "\t" ), '', $html ); 
}

// 2. 워드프레스 AJAX 요청을 처리하는 함수
add_action( 'wp_ajax_tripgo_load_calendar', 'tripgo_ajax_calendar_handler' );
add_action( 'wp_ajax_nopriv_tripgo_load_calendar', 'tripgo_ajax_calendar_handler' );

function tripgo_ajax_calendar_handler() {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $cal_ym     = isset($_POST['cal_ym']) ? sanitize_text_field($_POST['cal_ym']) : date('Y-m');
    
    if ( $product_id ) {
        echo tripgo_build_calendar_html( $product_id, $cal_ym );
    }
    wp_die(); 
}

// 3. 화면에 출력하는 달력 숏코드 및 자바스크립트 함수
add_shortcode( 'travel_price_calendar', 'tripgo_ajax_calendar_shortcode' );

function tripgo_ajax_calendar_shortcode() {
    global $product;

    if ( ! function_exists('is_product') || ! is_product() || ! is_a( $product, 'WC_Product' ) ) {
        return '';
    }

    $product_id = $product->get_id();
    $current_ym = date('Y-m');
    
    $initial_html = tripgo_build_calendar_html( $product_id, $current_ym );

    ob_start(); 
    ?>
    <div id="tripgo-ajax-calendar-wrapper" data-pid="<?php echo $product_id; ?>" style="margin: 20px 0; border: 1px solid #e5e5e5; padding: 15px; border-radius: 8px; background-color: #fff; position: relative;">
        
        <?php echo $initial_html; ?>
        
        <div id="tripgo-cal-overlay" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); border-radius: 8px; z-index: 10; align-items: center; justify-content: center;">
            <span style="font-weight:bold; color:#555; font-size: 0.8em;">로딩중...</span>
        </div>

    </div>

    <script>
    jQuery(document).ready(function($) {
        $(document).on('click', '.cal-nav-btn', function(e) {
            e.preventDefault(); 
            
            var ym = $(this).data('ym');
            var pid = $('#tripgo-ajax-calendar-wrapper').data('pid');
            
            $('#tripgo-cal-overlay').css('display', 'flex');
            
            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: {
                    action: 'tripgo_load_calendar',
                    product_id: pid,
                    cal_ym: ym
                },
                success: function(response) {
                    $('#tripgo-ajax-calendar-wrapper .custom-ova-calendar-inner').replaceWith(response);
                    $('#tripgo-cal-overlay').hide();
                },
                error: function() {
                    alert('달력을 불러오는 중 오류가 발생했습니다.');
                    $('#tripgo-cal-overlay').hide();
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean(); 
}

// =========================================================================
// 1. 유의사항 탭 추가 (입력창을 에디터 형식으로 변경)
// =========================================================================

// 관리자 화면: 기존 텍스트박스를 상세설명과 같은 wp_editor로 변경합니다.
add_action( 'woocommerce_product_options_general_product_data', 'tripgo_add_precautions_field' );
function tripgo_add_precautions_field() {
    global $post;
    
    // 저장된 값을 가져옵니다.
    $value = get_post_meta( $post->ID, '_product_precautions', true );
    
    echo '<div class="options_group" style="padding: 10px 20px 20px; border-top: 1px solid #eee;">';
    echo '<p style="font-weight: bold; margin-bottom: 10px; font-size: 14px; color: #333;">유의사항·환불사항</p>';
    
    // 워드프레스 기본 에디터를 호출하여 입력창을 생성합니다.
    wp_editor( 
        htmlspecialchars_decode( $value ), 
        'product_precautions_editor', // 고유 ID (알파벳 권장)
        array(
            'textarea_name' => '_product_precautions', // DB 저장 시 사용할 이름
            'media_buttons' => true, // 사진 추가 버튼 표시
            'textarea_rows' => 12,   // 에디터 높이 조정
            'tinymce'       => true, // 비주얼 탭 활성화
            'quicktags'     => true  // 텍스트(코드) 탭 활성화
        ) 
    );
    echo '</div>';
}

// 저장 로직: HTML 태그가 깨지지 않도록 wp_kses_post를 사용해 저장합니다.
add_action( 'woocommerce_process_product_meta', 'tripgo_save_precautions_field' );
function tripgo_save_precautions_field( $post_id ) {
    if ( isset( $_POST['_product_precautions'] ) ) {
        update_post_meta( $post_id, '_product_precautions', wp_kses_post( $_POST['_product_precautions'] ) );
    }
}

// 사용자 화면: 엘리멘터 어디든 넣으면 자동으로 '탭'으로 변신하는 숏코드
add_shortcode( 'tripgo_precautions', 'tripgo_precautions_shortcode_content' );
function tripgo_precautions_shortcode_content() {
    $product_id = get_the_ID();
    if ( ! $product_id ) return '';
    
    $precautions = get_post_meta( $product_id, '_product_precautions', true );
    if ( empty( $precautions ) ) return '';
    
    ob_start();
    ?>
    <div id="tour-precautions-content" style="display: none; padding: 0px 15px 30px 15px; margin-top: 0px;">
        <h2 style="font-weight: 700;    font-size: 42px !important;    line-height: 54px;    color: #000000;">유의사항·환불사항</h2>
        <div style="line-height: 1.8; color: #555;">
            <?php echo wpautop( $precautions ); ?>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // 'Explore' 제목 강제 변경
        $('.elementor-widget-heading h2, .elementor-heading-title').filter(function() {
            return $(this).text().trim() === 'Explore';
        }).text('관련상품');
        
        var $tabsContainer = $('.ova-tabs-product');
        var $customTabContent = $('#tour-precautions-content');

        if ( $tabsContainer.length > 0 && $customTabContent.length > 0 ) {
            // 점을 강제로 없앰 (list-style: none)
            if ( $tabsContainer.find('.tabs .item[data-id="#tour-precautions-content"]').length === 0 ) {
                $tabsContainer.find('.tabs').append('<li class="item" data-id="#tour-precautions-content" style="list-style: none;">유의사항·환불사항</li>');
            }

            $tabsContainer.append($customTabContent);

            $tabsContainer.on('click', '.tabs .item', function() {
                var targetId = $(this).data('id');

                $tabsContainer.find('.tabs .item').removeClass('active');
                $(this).addClass('active');

                if ( targetId === '#tour-precautions-content' ) {
                    $tabsContainer.find('#tour-description, #tour-included-excluded, #tour-plan, #ova-tour-map, #ova-tour-review').hide();
                    $customTabContent.fadeIn(200);
                } else {
                    $customTabContent.hide();
                }
            });
        } else {
            $customTabContent.show();
        }
    });
    </script>
    <?php
    return ob_get_clean();
}


// =========================================================================
// 2. 사용자 화면 번역 (탭, 예약폼, 관련상품) & 버튼 텍스트 변경
// =========================================================================

// 사용자 화면 영문 텍스트 국문 강제 번역
add_filter( 'gettext', 'tripgo_custom_all_translations', 999, 3 );
function tripgo_custom_all_translations( $translated_text, $text, $domain ) {
    switch ( trim( $text ) ) {
        case 'Description': return '상세설명';
        case 'Included/Excluded':
        case 'Included / Excluded': return '포함/불포함';
        case 'Tour Plan': return '여행일정';
        case 'Tour Map':
        case 'Map': return '투어 맵';
        case 'Reviews': return '리뷰';
        case 'Booking Form':
        case 'Booking form': return '예약하기';
        case 'Request Form':
        case 'Request form': return '문의하기';
        case 'Check in':
        case 'Check-in':
        case 'Check In': return '출발일';
        case 'Check out':
        case 'Check-out':
        case 'Check Out': return '도착일';
        case 'Guests':
        case 'Guest': return '인원';
        case 'Available': return '예약 가능 인원'; 
        case 'Total': return '총금액';
        case 'Booking Now':
        case 'Booking Now': return '예약하기';
        case 'Full Payment':
        case 'Full Payment': return '완납';
        case 'Pay Deposit':
        case 'Pay Deposit': return '예약금';

        case 'Read More':
        case 'Read More': return '더보기';
        case 'Read more':
        case 'Read more': return '더보기';

        case 'Name':
        case 'Name': return '성함';
        case 'Email':
        case 'Email': return '이메일';
        case 'Phone':
        case 'Phone': return '전화번호';
        case 'Address':
        case 'Address': return '주소';

        case 'Your name':
        case 'Your name': return '성함을 입력하세요.';
        case 'Your email':
        case 'Your email': return '이메일을 입력하세요.';
        case 'Your phone':
        case 'Your phone': return '전화번호을 입력하세요.';
        case 'Your address':
        case 'Your address': return '주소를 입력하세요.';

        case 'Extra Services':
        case 'Extra Services': return '추가서비스';
        case 'Send Now':
        case 'Send Now': return '보내기';
        case 'Extra Information':
        case 'Extra Information': return '기타 하고싶은 말씀';

        case 'This field is required.':
        case 'This field is required.': return '필수 입력';

        case 'featured':
        case 'featured': return '추천';


            
        // 💡 일단 제목과 버튼 모두 '관련상품'으로 번역해 둡니다.
        case 'Explore': return '관련상품'; 
    }

    
    // 혹시 단어 뒤에 콜론(:)이나 공백이 붙어있는 경우를 대비한 추가 방어 로직
    if ( trim( $text ) === 'Available:' ) return '예약 가능 인원:';
    if ( trim( $text ) === 'Total:' ) return '총금액:';
    
    return $translated_text;
}

// 2. 화면 출력 시 버튼(링크)만 찾아내어 '더보기'로 바꿔치기 하는 자바스크립트 (목록화면 대응형)
add_action( 'wp_footer', 'tripgo_change_button_text_only', 99 );
function tripgo_change_button_text_only() {
    if ( is_admin() ) return; 
    ?>
    <script>
    jQuery(document).ready(function($) {
        // 버튼 텍스트를 변경하는 핵심 함수
        function changeExploreToMore() {
            $('a, .btn, .button, .ovabrw-view-detail').each(function() {
                // 텍스트가 '관련상품'이거나 영문 'Explore'인 경우 모두 '더보기'로 변경
                var btnText = $(this).text().trim();
                if ( btnText === '관련상품' || btnText === 'Explore' ) {
                    $(this).text('더보기');
                }
            });
        }

        // 1. 페이지 로드 직후 실행
        changeExploreToMore();

        // 2. 목록 화면의 동적 변화(필터, 정렬, 로딩 등)를 감시하여 실행 (MutationObserver)
        var observer = new MutationObserver(function(mutations) {
            changeExploreToMore();
        });

        // 문서 전체의 변화를 감시하도록 설정
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // 3. 브라우저 창 크기가 변하거나 스크롤 시 혹시 놓친 버튼이 있다면 다시 실행
        $(window).on('scroll resize', function() {
            changeExploreToMore();
        });
    });
    </script>
    <?php
}


// =========================================================================
// 3. 관리자 화면 전용 번역 (설정 메뉴 & 상품 편집 화면)
// =========================================================================

// 관리자 화면 강제 번역 (사용자 화면 영향 X)
add_filter( 'gettext', 'tripgo_admin_product_tabs_translation', 999, 3 );
function tripgo_admin_product_tabs_translation( $translated_text, $text, $domain ) {
    
    // 안전장치: '관리자 화면'이 아니면 원본 그대로 출력하고 종료
    if ( ! is_admin() ) {
        return $translated_text;
    }

    switch ( trim( $text ) ) {
        // --- 상품 편집: 좌측 탭 메뉴 ---
        case 'Duration':
            return '비용 (월~일) time slot을 on하면 요일별 비용설정이 가능합니다. 첫째 성인가만 반영됩니다.';
        case 'Deposit':
            return '선불'; 
        case 'Service':
            return '서비스';
        case 'Unavailable time(UT)':
        case 'Unavailable Time(UT)':
        case 'Unavailable time (UT)':
            return '이용불가기간';
        case 'Place, Map':
        case 'Place, map':
        case 'Place':
            return '장소, 지도';
        case 'Advanced options':
        case 'Advanced Options':
            return '고급옵션';

        case 'Categories':
            return '카테고리'; 
        case 'Custom taxonomies':
            return '추가카테고리'; 

        // --- 상품 편집: Resources 탭 ---
        case 'Resources':
            return '리소스 (추가 옵션)';
        case 'Name':
            return '이름';
        case 'Price/Unit':
            return '단위당 가격 (단가)';
        case 'Duration Type':
            return '기간 유형';
        case 'Total ID':
            return '총 수량(ID)';
        case 'Max Options':
            return '최대 옵션 수';
        case 'Adult price':
        case 'Adult Price':
            return '성인요금';
        case 'Child price':
        case 'Child Price':
            return '청소년요금';
        case 'Baby price':
        case 'Baby Price':
            return '영유아요금';

        // --- 상품 편집: Price by Date 탭 ---
        case 'Price by Date':
        case 'Price By Date':
            return '날짜별 가격';
        case 'Start Date':
            return '시작일';
        case 'End Date':
            return '종료일';
        case 'Price':
            return '가격';

        // --- 플러그인 전역 설정(Settings) 메뉴 ---
        case 'General':
            return '일반';
        case 'Product details':
        case 'Product Details':
            return '상품 상세';
        case 'Enquiry form':
        case 'Enquiry Form':
            return '문의 양식';
        case 'Guest Information':
        case 'Guest information':
            return '예약자 정보';
        case 'reCAPTCHA':
            return '자동등록방지 (reCAPTCHA)';
        case 'Cancellation policy':
        case 'Cancellation Policy':
            return '취소/환불 규정';
        case 'Reminder':
            return '리마인더 (알림)';
        case 'Manage bookings':
        case 'Manage Bookings':
            return '예약 관리';
        case 'Date format':
        case 'Date Format':
            return '날짜 형식';
        case 'Time format':
        case 'Time Format':
            return '시간 형식';
        case 'Step time':
        case 'Step Time':
            return '시간 간격';
        case 'Calendar language':
        case 'Calendar Language':
            return '달력 언어';
        case 'Disable weekdays':
        case 'Disable Weekdays':
            return '특정 요일 비활성화';
        case 'The first day of the week':
            return '주의 시작일';
        case 'Show custom taxonomy':
        case 'Show Custom Taxonomy':
            return '커스텀 분류 표시';
        case 'Google Maps':
        case 'Google maps':
            return '구글 맵';
        case 'Default latitude':
        case 'Default Latitude':
            return '기본 위도';
        case 'Default longitude':
        case 'Default Longitude':
            return '기본 경도';

        // --- 세부 설정 메뉴 ---
        case 'Product templates':
        case 'Product Templates':
            return '상품 템플릿';
        case 'Show booking form':
        case 'Show Booking Form':
            return '예약 양식 표시';
        case 'Show request form':
        case 'Show Request Form':
            return '요청 양식 표시';
        case 'Show enquiry form':
        case 'Show Enquiry Form':
            return '문의 양식 표시';
        case 'Book before X hours today':
            return '당일 예약 마감 시간 (X시간 전)';
        case 'Show check-out field':
        case 'Show Check-out Field':
            return '도착일(체크아웃) 필드 표시';
        case 'Show quantity':
        case 'Show Quantity':
            return '수량 표시';
        case 'Show number of tours available':
            return '예약 가능 투어 수 표시';
        case 'Show amount of insurance':
        case 'Show Amount of Insurance':
            return '보험료 표시';
        case 'Apply tax for insurance amount':
            return '보험료에 세금 적용';
        case 'Insurance amount will be paid once':
            return '보험료 1회 부과';
        case 'Show resources and services in Cart, Checkout, Order detail':
            return '장바구니, 결제, 주문 상세에 리소스 및 서비스 표시';
        case 'Terms and conditions':
        case 'Terms and Conditions':
            return '이용약관';
        case 'Enable':
            return '활성화 (사용)';
        case 'Thank page':
        case 'Thank Page':
            return '예약 완료 페이지';
        case 'Error page':
        case 'Error Page':
            return '오류 페이지';
        case 'Show phone':
        case 'Show Phone':
            return '전화번호 표시';
        case 'Show address':
        case 'Show Address':
            return '주소 표시';
        case 'Show check-out date':
        case 'Show Check-out Date':
            return '도착일(체크아웃) 표시';
        case 'Show custom checkout fields':
            return '사용자 정의 결제 필드 표시';
        case 'Show extra services':
        case 'Show Extra Services':
            return '추가 서비스 표시';
        case 'Show service':
        case 'Show Service':
            return '서비스 표시';
        case 'Show extra info':
        case 'Show Extra Info':
            return '추가 정보 표시';
        case 'Show total':
        case 'Show Total':
            return '총액 표시';
        
        // --- 이메일 및 주문 설정 ---
        case 'Email settings':
        case 'Email Settings':
            return '이메일 설정';
        case 'Subject':
            return '이메일 제목';
        case 'From name':
        case 'From Name':
            return '발신자 이름';
        case 'Send from email':
        case 'Send From Email':
            return '발신자 이메일';
        case 'Cc':
            return '참조 (Cc)';
        case 'Email content':
        case 'Email Content':
            return '이메일 내용';
        case 'Order':
            return '주문';
        case 'Allows creating new orders':
            return '새 주문 생성 허용';
        case 'Order status':
        case 'Order Status':
            return '주문 상태';
        case 'Collect customer information':
            return '고객 정보 수집';
        case 'Remaining amount':
            return '잔금 (남은 금액)';
        case 'Send order detail to customer':
            return '고객에게 주문 내역 발송';
        case 'Automatically create order details for remaining amount':
            return '잔금 결제용 주문 내역 자동 생성';
        case 'X days before the customer\'s Check-in date, the order detail for Remaining Amount will be automatically created and sent to the customer\'s email (If the order detail has not been created manually).':
        case "X days before the customer's Check-in date, the order detail for Remaining Amount will be automatically created and sent to the customer's email (If the order detail has not been created manually).":
            return '고객의 체크인(출발일) X일 전에 잔금 결제용 주문 내역이 자동으로 생성되어 이메일로 발송됩니다 (수동으로 생성하지 않은 경우).';
        case 'X day before pick-up date':
            return '픽업(출발일) X일 전';
        case 'Check periodically every X seconds for creating a remaining invoice':
            return '잔금 청구서 생성을 위해 X초마다 주기적으로 확인';
        case 'Minimum time required before canceling (hours)':
            return '취소 가능한 최소 시간 (시간 단위)';
        case 'Cancellation is accepted if the total order is less than x amount':
            return '총 주문 금액이 X 이하일 경우 취소 허용';
        case 'Send a recurring email every X seconds after the initial one.':
            return '최초 발송 후 X초마다 반복 이메일 발송';
    }

    return $translated_text;
}


add_filter( 'woocommerce_account_menu_items', 'rename_orders_endpoint', 999 );

function rename_orders_endpoint( $items ) {
    // 'orders' 항목의 이름을 '주문내역'으로 변경합니다.
    if ( isset( $items['orders'] ) ) {
        $items['orders'] = '주문내역';
    }
    return $items;
}

add_action('template_redirect', 'custom_login_redirect');
function custom_login_redirect() {
    // 사용자가 로그인이 안 되어 있고, 내 계정 페이지에 접근할 때
    if ( !is_user_logged_in() && is_account_page() ) {
        wp_redirect( home_url( '/login/' ) );
        exit;
    }
}

add_action('template_redirect', 'custom_register_redirect');
function custom_register_redirect() {
    // URL에 register가 포함되어 있거나, 우커머스 회원가입 페이지인 경우
    if ( !is_user_logged_in() && isset($_GET['action']) && $_GET['action'] == 'register' ) {
        wp_redirect( home_url( '/register/' ) );
        exit;
    }
}

add_filter( 'woocommerce_product_tabs', 'woo_remove_reviews_tab', 98 );
function woo_remove_reviews_tab( $tabs ) {
    if ( isset( $tabs['reviews'] ) ) {
        unset( $tabs['reviews'] );
    }
    return $tabs;
}

