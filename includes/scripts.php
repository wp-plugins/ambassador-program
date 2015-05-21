<?php
/**
 * Scripts
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Loads scripts needed for the admin area
 *
 * @since  0.1
 * @author Bryan Monzon
 */
function ambprog_load_admin_scripts( $hook ) 
{
    global $post,
    $ambprog_settings,
    $ambprog_settings_page,
    $wp_version;

    $js_dir  = AMBPROG_PLUGIN_URL . 'assets/js/';
    $css_dir = AMBPROG_PLUGIN_URL . 'assets/css/';

    wp_register_script( 'ambprog-admin-scripts', $js_dir . 'admin-scripts.js', array('jquery'), '1.0', true );

    wp_enqueue_script( 'ambprog-admin-scripts' );
    wp_localize_script( 'ambprog-admin-scripts', 'ambprog_vars', array(
        'new_media_ui'            => apply_filters( 'ambprog_use_35_media_ui', 1 ),
        ) 
    );

    if ( $hook == $ambprog_settings_page ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'colorbox', $css_dir . 'colorbox.css', array(), '1.3.20' );
        wp_enqueue_script( 'colorbox', $js_dir . 'jquery.colorbox-min.js', array( 'jquery' ), '1.3.20' );
        if( function_exists( 'wp_enqueue_media' ) && version_compare( $wp_version, '3.5', '>=' ) ) {
            //call for new media manager
            wp_enqueue_media();
        }
    }




}
add_action( 'admin_enqueue_scripts', 'ambprog_load_admin_scripts', 100 );


/**
 * Register and Enqueue Scripts/Style for the Frontend
 * @return [type] [description]
 */
function ambprog_load_scripts()
{
    global $post,
    $ambprog_settings;

    $js_dir  = AMBPROG_PLUGIN_URL . 'assets/js/';
    $css_dir = AMBPROG_PLUGIN_URL . 'assets/css/';

    wp_register_style('ambprog-form-css', $css_dir . 'forms.css');
    wp_register_style('ambprog-dashboard-css', $css_dir . 'dashboard.css');

    wp_register_style('ambprog-css', $css_dir . 'ambprog-styles.css');
    wp_enqueue_style( 'ambprog-css' );
}
add_action( 'wp_enqueue_scripts', 'ambprog_load_scripts' );