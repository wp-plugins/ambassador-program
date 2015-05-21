<?php
/**
 * Admin Notices
 *
 * @package     FFW Ambassador Program
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2013, FIfty & Fifty
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin Messages
 *
 * @since  0.1
 * @author Bryan Monzon
 * @global $ambprog_settings Array of all the AMBPROG Options
 * @return void
 */
function ambprog_admin_messages() {
    global $ambprog_settings;

    settings_errors( 'ambprog-notices' );
}
add_action( 'admin_notices', 'ambprog_admin_messages' );


/**
 * Dismisses admin notices when Dismiss links are clicked
 *
 * @since 1.8
 * @return void
*/
function ambprog_dismiss_notices() {

    $notice = isset( $_GET['ambprog_notice'] ) ? $_GET['ambprog_notice'] : false;

    if( ! $notice )
        return; // No notice, so get out of here

    update_user_meta( get_current_user_id(), '_ambprog_' . $notice . '_dismissed', 1 );

    wp_redirect( remove_query_arg( array( 'ambprog_action', 'ambprog_notice' ) ) ); exit;

}
add_action( 'ambprog_dismiss_notices', 'ambprog_dismiss_notices' );
