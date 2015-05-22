<?php 
/**
 * Commission Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Check if project has a commission rate
 * 
 * @param  int  $post_id optional
 * @return boolean return whether project has a commission rate
 */
function has_project_commission( $post_id = null ) {
    return (bool) get_project_commission( $post_id );
}

/**
 * Get the project commission rate.
 * 
 * @param  int $post_id optional
 * @return [type]          [description]
 */
function get_project_commission( $post_id = null ){
    $post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
    return get_post_meta( $post_id, 'ambprog_project_commission_rate', true );
}


/**
 * Check if user has a commission rate
 * 
 * @param  int  $post_id optional
 * @return boolean return whether user has a commission rate
 */
function has_ambassador_commission( $user_id = null ) {
    return (bool) get_ambassador_commission( $user_id );
}

/**
 * Get the user commission rate.
 * 
 * @param  int $post_id optional
 * @return [type]          [description]
 */
function get_ambassador_commission( $user_id = null ){
    global $current_user;
    $user_id = ( null === $user_id ) ? $current_user->ID : $user_id;
    return get_user_meta( $user_id, 'ambprog_ambassador_commission', true );
}


/**
 * Check if user has a commission rate
 * 
 * @param  int  $post_id optional
 * @return boolean return whether user has a commission rate
 */
function has_default_commission() {
    return (bool) get_default_commission();
}

/**
 * Get the user commission rate.
 * 
 * @param  int $post_id optional
 * @return [type]          [description]
 */
function get_default_commission(){
    global $ambprog_settings;
    
    if( !empty( $ambprog_settings['default_commission'] ) ) {
        return $ambprog_settings['default_commission'];
    }

    return false;
}


/**
 * Check first for project commission, then user/ambassador
 * commission, then finally global default commission.
 * 
 * @return [type] [description]
 */
function ambprog_get_commission( $format = true ){

    
    if( has_project_commission() ) {
        $commission = get_project_commission();
    }elseif( has_ambassador_commission() ){
        $commission = get_ambassador_commission();
    }else{
        $commission = get_default_commission();
    }

    return (int) ($format ) ? $commission / 100 : $commission;
    
}








