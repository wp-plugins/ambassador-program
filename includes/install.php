<?php
/**
 * Install Function
 *
 * @package     AMBPROG
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2014, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 *
 * Runs on plugin install by setting up the post types, custom taxonomies,
 * flushing rewrite rules to initiate the new 'ambprog' slug and also
 * creates the plugin and populates the settings fields for those plugin
 * pages. After successful install, the user is redirected to the AMBPROG Welcome
 * screen.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @global $wpdb
 * @global $ambprog_settings
 * @global $wp_version
 * @return void
 */
function ambprog_install() {
    global $wpdb, $ambprog_settings, $wp_version;

    // Setup the Ambassador Program Custom Post Type
    setup_ambprog_post_types();

    // Setup the Download Taxonomies    

    // Clear the permalinks
    flush_rewrite_rules();

    // Add Upgraded From Option
    $current_version = get_option( 'ambprog_version' );
    if ( $current_version ) {
        update_option( 'ambprog_version_upgraded_from', $current_version );
    }


    // Setup some default options
    $options = array();

    // Checks if the purchase page option exists
    if ( ! ambprog_get_option( 'dashboard_page', false ) ) {
      // Checkout Page
        $dashboard = wp_insert_post(
            array(
                'post_title'     => __( 'Dashboard', 'edd' ),
                'post_content'   => '[referral_dashboard]',
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_type'      => 'page',
                'comment_status' => 'closed'
            )
        );

        // Purchase Confirmation (Success) Page
        $create = wp_insert_post(
            array(
                'post_title'     => __( 'Submit Project', 'edd' ),
                'post_content'   => __( '[submit_project_form]', 'edd' ),
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_parent'    => $dashboard,
                'post_type'      => 'page',
                'comment_status' => 'closed'
            )
        );

        // Failed Purchase Page
        $register = wp_insert_post(
            array(
                'post_title'     => __( 'Register', 'edd' ),
                'post_content'   => __( '[register_form]', 'edd' ),
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_type'      => 'page',
                'comment_status' => 'closed'
            )
        );

        // Purchase History (History) Page
        $login = wp_insert_post(
            array(
                'post_title'     => __( 'Login', 'edd' ),
                'post_content'   => '[login_form]',
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_type'      => 'page',
                'comment_status' => 'closed'
            )
        );

        // Store our page IDs
        $options['dashboard_page']      = $dashboard;
        $options['submit_project_page'] = $create;
        $options['register_page']       = $register;
        $options['login_page']          = $login;
    }

    update_option( 'ambprog_settings', array_merge( $ambprog_settings, $options ) );
    update_option( 'ambprog_version', AMBPROG_VERSION );



    // Bail if activating from network, or bulk
    if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
        return;
    }

    // Add the transient to redirect
    set_transient( '_ambprog_activation_redirect', true, 30 );
}
register_activation_hook( AMBPROG_PLUGIN_FILE, 'ambprog_install' );

/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the
 * ambprog_after_install hook.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */
function ambprog_after_install() {

    if ( ! is_admin() ) {
        return;
    }

    $activation_pages = get_transient( '_ambprog_activation_pages' );

    // Exit if not in admin or the transient doesn't exist
    if ( false === $activation_pages ) {
        return;
    }

    // Delete the transient
    delete_transient( '_ambprog_activation_pages' );

    do_action( 'ambprog_after_install', $activation_pages );
}
add_action( 'admin_init', 'ambprog_after_install' );