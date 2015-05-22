<?php
/**
 * Miscellaneus Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Return a 404 when someone tries to access the
 * project directly
 * 
 * @return [type] [description]
 */
function ambprog_404_project_single() {
  global $post;
  if ( is_singular( 'ambprog_projects' ) ) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
  }
}
add_action( 'wp', 'ambprog_404_project_single' );

/**
 * Get all of the registered roles
 * 
 * @return [type] [description]
 */
function ambprog_get_roles() {
    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);

    return $editable_roles;
}

