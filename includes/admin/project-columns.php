<?php
/**
 * Project Columns
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add new columns to projects post type
 * @param  [type] $columns [description]
 * @return [type]          [description]
 */
function ambprog_projects_columns( $columns ) {

    $columns = array(
        'cb'     => '<input type="checkbox" />',
        'title'  => __( 'Projects' ),
        'cost'   => __( 'Project Cost' ),
        'status' => __( 'Project Status' ),
        'date'   => __( 'Date Submitted' )
    );

    return $columns;
}
add_filter( 'manage_edit-ambprog_projects_columns', 'ambprog_projects_columns' ) ;






/**
 * Pull in the post meta for the projects
 * 
 * @param  [type] $column  [description]
 * @param  [type] $post_id [description]
 * @return [type]          [description]
 */
function manage_ambprog_projects_columns( $column, $post_id ) {
    global $post;

    switch( $column ) {

        /* If displaying the 'duration' column. */
        case 'cost' :

            /* Get the post meta. */
            $project_cost = get_post_meta( $post_id, 'ambprog_project_cost', true );

            /* If no duration is found, output a default message. */
            if ( empty( $project_cost) )
                echo __( 'Unknown' );

            /* If there is a duration, append 'minutes' to the text string. */
            else
                printf( __( '%s' ), '$ ' . number_format( $project_cost ) );

            break;

        /* If displaying the 'genre' column. */
        case 'status' :

            /* Get the genres for the post. */
            $project_status = get_post_meta( $post_id, 'ambprog_project_status', true );

            /* If no duration is found, output a default message. */
            if ( empty( $project_status) )
                echo __( '0%' );

            /* If there is a duration, append 'minutes' to the text string. */
            else
                echo __( number_format( $project_status ) . '%' );

            break;

            break;

        /* Just break out of the switch statement for everything else. */
        default :
            break;
    }
}
add_action( 'manage_ambprog_projects_posts_custom_column', 'manage_ambprog_projects_columns', 10, 2 );