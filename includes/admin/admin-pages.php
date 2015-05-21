<?php
/**
 * Admin Pages
 *
 * @package     Fifty Framework Ambassador Program
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;




/**
 * Creates the admin menu pages under Donately and assigns them their global variables
 *
 * @since  0.1
 * @author Bryan Monzon
 * @global  $ambprog_settings_page
 * @return void
 */
function ambprog_add_menu_page() {
    global $ambprog_settings_page, $ambprog_projects_page;

    $ambprog_settings_page = add_submenu_page( 'edit.php?post_type=ambprog_projects', __( 'Settings', 'ambprog' ), __( 'Settings', 'ambprog'), 'edit_pages', 'ambprog-settings', 'ambprog_settings_page' );
    
}
add_action( 'admin_menu', 'ambprog_add_menu_page', 11 );
