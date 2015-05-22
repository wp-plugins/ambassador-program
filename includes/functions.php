<?php
/**
 * Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * 
 * @param  integer $post_id [description]
 * @return [type]           [description]
 */
function ambprog_get_commission_amount( $post_id = 0 ) {
    global $post;

    $post_id = isset( $post_id ) ? $post_id : $post->ID;

    $rate = ambprog_get_commission();
    $cost = get_post_meta( $post_id, 'ambprog_project_cost', true );

    $commission = ($cost * $rate);

    return '$' . number_format( $commission, 2, '.', ',' );

}


/**
 * Get and sum the totals from the projects.
 * @param  integer $user_id [description]
 * @return [type]           [description]
 */
function ambprog_get_commission_totals( $user_id = 0 ) {
    
    global $current_user;
    
    $user_id = ( $user_id > 0 ) ? $user_id : $current_user->ID;

    $project_args = array(
        'post_status' => 'publish',
        'post_type'   => 'ambprog_projects',
        'nopaging'    => true,
        'author'      => $user_id
    );
    $projects = get_posts( $project_args );
    $total = array();
    foreach( $projects as $project ){
        $total[] = get_post_meta( $project->ID, 'ambprog_project_cost', true );
    }

    $total = array_sum( $total );
    $total = $total * ambprog_get_commission();
    
    return number_format( $total, 2, '.', ',' );
}


/**
 * Returns the number of projects for a specific user
 * 
 * @param  integer $user_id [description]
 * @return [type]           [description]
 */
function ambprog_get_project_count( $user_id = 0)
{
    global $current_user;
    
    $user_id = ( $user_id > 0 ) ? $user_id : $current_user->ID;

    $project_args = array(
        'post_status' => array( 'pending', 'publish'),
        'post_type'   => 'ambprog_projects',
        'nopaging'    => true,
        'author'      => $user_id
    );
    $projects = new WP_Query( $project_args );

    return $projects->post_count;

}






