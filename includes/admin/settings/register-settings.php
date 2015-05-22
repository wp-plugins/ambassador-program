<?php
/**
 * Register Settings
 *
 * @package     Fifty Framework Ambassador Program
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return mixed
 */
function ambprog_get_option( $key = '', $default = false ) {
    global $ambprog_settings;
    return isset( $ambprog_settings[ $key ] ) ? $ambprog_settings[ $key ] : $default;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array AMBPROG settings
 */
function ambprog_get_settings() {

    $settings = get_option( 'ambprog_settings' );
    if( empty( $settings ) ) {

        // Update old settings with new single option

        $general_settings = is_array( get_option( 'ambprog_settings_general' ) )    ? get_option( 'ambprog_settings_general' )      : array();


        $settings = array_merge( $general_settings );

        update_option( 'ambprog_settings', $settings );
    }
    return apply_filters( 'ambprog_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
*/
function ambprog_register_settings() {

    if ( false == get_option( 'ambprog_settings' ) ) {
        add_option( 'ambprog_settings' );
    }

    foreach( ambprog_get_registered_settings() as $tab => $settings ) {

        add_settings_section(
            'ambprog_settings_' . $tab,
            __return_null(),
            '__return_false',
            'ambprog_settings_' . $tab
        );

        foreach ( $settings as $option ) {
            add_settings_field(
                'ambprog_settings[' . $option['id'] . ']',
                $option['name'],
                function_exists( 'ambprog_' . $option['type'] . '_callback' ) ? 'ambprog_' . $option['type'] . '_callback' : 'ambprog_missing_callback',
                'ambprog_settings_' . $tab,
                'ambprog_settings_' . $tab,
                array(
                    'id'      => $option['id'],
                    'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
                    'name'    => $option['name'],
                    'section' => $tab,
                    'size'    => isset( $option['size'] ) ? $option['size'] : null,
                    'options' => isset( $option['options'] ) ? $option['options'] : '',
                    'std'     => isset( $option['std'] ) ? $option['std'] : ''
                )
            );
        }

    }

    // Creates our settings in the options table
    register_setting( 'ambprog_settings', 'ambprog_settings', 'ambprog_settings_sanitize' );

}
add_action('admin_init', 'ambprog_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array
*/
function ambprog_get_registered_settings() {

    $pages = get_pages();
    $pages_options = array( 0 => '' ); // Blank option
    if ( $pages ) {
        foreach ( $pages as $page ) {
            $pages_options[ $page->ID ] = $page->post_title;
        }
    }

    /**
     * 'Whitelisted' AMBPROG settings, filters are provided for each settings
     * section to allow extensions and other plugins to add their own settings
     */
    $ambprog_settings = array(
        /** General Settings */
        'general' => apply_filters( 'ambprog_settings_general',
            array(
                'general_settings' => array(
                    'id'   => 'general_settings',
                    'name' => '<strong>' . __( 'General Settings', 'ambprog' ) . '</strong>',
                    'desc' => '',
                    'type' => 'header'
                ),
                'default_commission' => array(
                    'id'   => 'default_commission',
                    'name' => __( 'Default Commission Rate', 'ambprog' ),
                    'desc' => __( '%. Set the default commission rate'  , 'ambprog' ),
                    'type' => 'text',
                    'size' => 'small',
                    'std'  => ''
                ),
                'dashboard_page' => array(
                    'id'          => 'dashboard_page',
                    'name'        => __( 'Dashboard Page', 'ambprog' ),
                    'desc'        => __( 'This is the referrer\'s dashboard. The [referral_dashboard] short code must be on this page.', 'ambprog' ),
                    'type'        => 'select',
                    'options'     => ambprog_get_pages(),
                    'placeholder' => __( 'Select a page', 'ambprog' )
                ),
                'submit_project_page' => array(
                    'id'          => 'submit_project_page',
                    'name'        => __( 'Submit Project Page', 'ambprog' ),
                    'desc'        => __( 'This is where your referrer\'s can submit projects. The [submit_project_form] short code must be on this page.', 'ambprog' ),
                    'type'        => 'select',
                    'options'     => ambprog_get_pages(),
                    'placeholder' => __( 'Select a page', 'ambprog' )
                ),
                'register_page' => array(
                    'id'          => 'register_page',
                    'name'        => __( 'Register Page', 'ambprog' ),
                    'desc'        => __( 'This is where your referrer\'s can register. The [register_form] short code must be on this page.', 'ambprog' ),
                    'type'        => 'select',
                    'options'     => ambprog_get_pages(),
                    'placeholder' => __( 'Select a page', 'ambprog' )
                ),
                'login_page' => array(
                    'id'          => 'login_page',
                    'name'        => __( 'Login Page', 'ambprog' ),
                    'desc'        => __( 'This is where your referrer\'s can login. The [login_form] short code must be on this page.', 'ambprog' ),
                    'type'        => 'select',
                    'options'     => ambprog_get_pages(),
                    'placeholder' => __( 'Select a page', 'ambprog' )
                ),
            )
        ),
        
    );

    return $ambprog_settings;
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function ambprog_header_callback( $args ) {
    $html = '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
    echo $html;
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_checkbox_callback( $args ) {
    global $ambprog_settings;

    $checked = isset($ambprog_settings[$args['id']]) ? checked(1, $ambprog_settings[$args['id']], false) : '';
    $html = '<input type="checkbox" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_multicheck_callback( $args ) {
    global $ambprog_settings;

    foreach( $args['options'] as $key => $option ):
        if( isset( $ambprog_settings[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
        echo '<input name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
        echo '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;
    echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_radio_callback( $args ) {
    global $ambprog_settings;

    foreach ( $args['options'] as $key => $option ) :
        $checked = false;

        if ( isset( $ambprog_settings[ $args['id'] ] ) && $ambprog_settings[ $args['id'] ] == $key )
            $checked = true;
        elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $ambprog_settings[ $args['id'] ] ) )
            $checked = true;

        echo '<input name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
        echo '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;

    echo '<p class="description">' . $args['desc'] . '</p>';
}



/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_text_callback( $args ) {
    global $ambprog_settings;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * AMBPROG Hidden Text Field Callback
 *
 * Renders text fields (Hidden, for necessary values in ambprog_settings in the wp_options table)
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 * @todo refactor it is not needed entirely
 */
function ambprog_hidden_callback( $args ) {
    global $ambprog_settings;

    $hidden = isset($args['hidden']) ? $args['hidden'] : false;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="hidden" class="' . $size . '-text" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['std'] . '</label>';

    echo $html;
}




/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_textarea_callback( $args ) {
    global $ambprog_settings;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<textarea class="large-text" cols="50" rows="5" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_password_callback( $args ) {
    global $ambprog_settings;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="password" class="' . $size . '-text" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function ambprog_missing_callback($args) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'ambprog' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_select_callback($args) {
    global $ambprog_settings;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $name ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_color_select_callback( $args ) {
    global $ambprog_settings;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $color ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @global $wp_version WordPress Version
 */
function ambprog_rich_editor_callback( $args ) {
    global $ambprog_settings, $wp_version;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
        $html = wp_editor( stripslashes( $value ), 'ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']', array( 'textarea_name' => 'ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']' ) );
    } else {
        $html = '<textarea class="large-text" rows="10" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    }

    $html .= '<br/><label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_upload_callback( $args ) {
    global $ambprog_settings;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text ambprog_upload_field" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<span>&nbsp;<input type="button" class="ambprog_settings_upload_button button-secondary" value="' . __( 'Upload File', 'ambprog' ) . '"/></span>';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_color_callback( $args ) {
    global $ambprog_settings;

    if ( isset( $ambprog_settings[ $args['id'] ] ) )
        $value = $ambprog_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $default = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="ambprog-color-picker" id="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
    $html .= '<label for="ambprog_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}



/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function ambprog_hook_callback( $args ) {
    do_action( 'ambprog_' . $args['id'] );


    
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function ambprog_settings_sanitize( $input = array() ) {

    global $ambprog_settings;

    parse_str( $_POST['_wp_http_referer'], $referrer );

    $output    = array();
    $settings  = ambprog_get_registered_settings();
    $tab       = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
    $post_data = isset( $_POST[ 'ambprog_settings_' . $tab ] ) ? $_POST[ 'ambprog_settings_' . $tab ] : array();

    $input = apply_filters( 'ambprog_settings_' . $tab . '_sanitize', $post_data );

    // Loop through each setting being saved and pass it through a sanitization filter
    foreach( $input as $key => $value ) {

        // Get the setting type (checkbox, select, etc)
        $type = isset( $settings[ $key ][ 'type' ] ) ? $settings[ $key ][ 'type' ] : false;

        if( $type ) {
            // Field type specific filter
            $output[ $key ] = apply_filters( 'ambprog_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $output[ $key ] = apply_filters( 'ambprog_settings_sanitize', $value, $key );
    }


    // Loop through the whitelist and unset any that are empty for the tab being saved
    if( ! empty( $settings[ $tab ] ) ) {
        foreach( $settings[ $tab ] as $key => $value ) {

            // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $_POST[ 'ambprog_settings_' . $tab ][ $key ] ) ) {
                unset( $ambprog_settings[ $key ] );
            }

        }
    }

    // Merge our new settings with the existing
    $output = array_merge( $ambprog_settings, $output );

    // @TODO: Get Notices Working in the backend.
    add_settings_error( 'ambprog-notices', '', __( 'Settings Updated', 'ambprog' ), 'updated' );

    return $output;

}

/**
 * Sanitize text fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function ambprog_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'ambprog_settings_sanitize_text', 'ambprog_sanitize_text_field' );

/**
 * Retrieve settings tabs
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function ambprog_get_settings_tabs() {

    $settings = ambprog_get_registered_settings();

    $tabs            = array();
    $tabs['general'] = __( 'General', 'ambprog' );

    return apply_filters( 'ambprog_settings_tabs', $tabs );
}

/**
 * Retrieve a list of all published pages
 *
 * On large sites this can be expensive, so only load if on the settings page or $force is set to true
 *
 * @since 1.9.5
 * @param bool $force Force the pages to be loaded even if not on settings
 * @return array $pages_options An array of the pages
 */
function ambprog_get_pages( $force = false ) {

    $pages_options = array( '' => '' ); // Blank option

    if( ( ! isset( $_GET['page'] ) || 'ambprog-settings' != $_GET['page'] ) && ! $force ) {
        return $pages_options;
    }

    $pages = get_pages();
    if ( $pages ) {
        foreach ( $pages as $page ) {
            $pages_options[ $page->ID ] = $page->post_title;
        }
    }

    return $pages_options;
}