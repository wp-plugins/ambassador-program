<?php
/**
 * Project Functions
 */


/**
 * Processes the project creation
 * @return [type] [description]
 */
function ambprog_process_post_creation() {

    if( isset( $_POST['projects_nonce_field'] ) && wp_verify_nonce( $_POST['projects_nonce_field'], 'projects_nonce') ) {
 
        if( strlen( trim( $_POST['organization_name'] ) ) < 1 || strlen( trim( $_POST['organization_description'] ) ) < 1 ) {
            $redirect = add_query_arg('post', 'failed', home_url( $_POST['_wp_http_referer'] ) );
        } else {        
            $project_info = array(
                'post_title' => esc_attr(strip_tags($_POST['organization_name'])),
                'post_type' => 'ambprog_projects',
                'post_content' => esc_attr(strip_tags($_POST['organization_description'])),
                'post_status' => 'pending',
                'post_author' => esc_attr( strip_tags( $_POST['user_id'] ) )
            );
            $project_id = wp_insert_post( $project_info );
 
            if( $project_id ) {
                // update_post_meta( $project_id, 'ecpt_postedby', esc_attr( strip_tags( $_POST['user_name'] ) ) );
                // update_post_meta( $project_id, 'ecpt_posteremail', esc_attr( strip_tags( $_POST['user_email'] ) ) );
                // update_post_meta( $project_id, 'ecpt_contactemail', esc_attr( strip_tags( $_POST['inquiry_email'] ) ) );
                $redirect = add_query_arg('post', 'successful', home_url( $_POST['_wp_http_referer'] ) );
            }
        }
        wp_redirect( esc_url( $redirect ) ); exit;
    }
}
add_action('init', 'ambprog_process_post_creation');