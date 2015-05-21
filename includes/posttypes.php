<?php
/**
 * Post Type Functions
 *
 * @package     AMBPROG
 * @subpackage  Functions
 * @copyright   Copyright (c) 2013, Fifty and Fifty
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the Ambassador Program custom post type
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */


function setup_ambprog_post_types() {
	global $ambprog_settings;

	$archives = defined( 'AMBPROG_PROJECTS_DISABLE_ARCHIVE' ) && AMBPROG_PROJECTS_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'AMBPROG_PROJECTS_SLUG' ) ? AMBPROG_PROJECTS_SLUG : 'projects';
	$rewrite  = defined( 'AMBPROG_PROJECTS_DISABLE_REWRITE' ) && AMBPROG_PROJECTS_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$ambprog_labels =  apply_filters( 'ambprog_labels', array(
		'name' 				=> '%2$s',
		'singular_name' 	=> '%1$s',
		'add_new' 			=> __( 'Add New', 'ambprog' ),
		'add_new_item' 		=> __( 'Add New %1$s', 'ambprog' ),
		'edit_item' 		=> __( 'Edit %1$s', 'ambprog' ),
		'new_item' 			=> __( 'New %1$s', 'ambprog' ),
		'all_items' 		=> __( 'All %2$s', 'ambprog' ),
		'view_item' 		=> __( 'View %1$s', 'ambprog' ),
		'search_items' 		=> __( 'Search %2$s', 'ambprog' ),
		'not_found' 		=> __( 'No %2$s found', 'ambprog' ),
		'not_found_in_trash'=> __( 'No %2$s found in Trash', 'ambprog' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( '%2$s', 'ambprog' )
	) );

	foreach ( $ambprog_labels as $key => $value ) {
	   $ambprog_labels[ $key ] = sprintf( $value, ambprog_get_label_singular(), ambprog_get_label_plural() );
	}

	$ambprog_args = array(
		'labels'              => $ambprog_labels,
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-list-view',
		'query_var'           => false,
		'exclude_from_search' => true,
		'rewrite'             => $rewrite,
		'map_meta_cap'        => true,
		'has_archive'         => $archives,
		'show_in_nav_menus'   => true,
		'hierarchical'        => false,
		'supports'            => apply_filters( 'ambprog_supports', array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ) ),
	);
	register_post_type( 'ambprog_projects', apply_filters( 'ambprog_post_type_args', $ambprog_args ) );



	
}
add_action( 'init', 'setup_ambprog_post_types', 1 );

/**
 * Get Default Labels
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array $defaults Default labels
 */
function ambprog_get_default_labels() {
	global $ambprog_settings;

	if( !empty( $ambprog_settings['ambprog_label_plural'] ) || !empty( $ambprog_settings['ambprog_label_singular'] ) ) {
	    $defaults = array(
	       'singular' => $ambprog_settings['ambprog_label_singular'],
	       'plural' => $ambprog_settings['ambprog_label_plural']
	    );
	 } else {
		$defaults = array(
		   'singular' => __( 'Project', 'ambprog' ),
		   'plural' => __( 'Projects', 'ambprog')
		);
	}
	
	return apply_filters( 'ambprog_default_name', $defaults );

}



/**
 * Get Singular Label
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return string $defaults['singular'] Singular label
 */
function ambprog_get_label_singular( $lowercase = false ) {
	$defaults = ambprog_get_default_labels();
	return ($lowercase) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return string $defaults['plural'] Plural label
 */
function ambprog_get_label_plural( $lowercase = false ) {
	$defaults = ambprog_get_default_labels();
	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}

/**
 * Change default "Enter title here" input
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param string $title Default title placeholder text
 * @return string $title New placeholder text
 */
function ambprog_change_default_title( $title ) {
     $screen = get_current_screen();

     if  ( 'ambprog_projects' == $screen->post_type ) {
     	$label = ambprog_get_label_singular();
        $title = sprintf( __( 'Enter %s title here', 'ambprog' ), $label );
     }

     return $title;
}
add_filter( 'enter_title_here', 'ambprog_change_default_title' );




/**
 * Updated Messages
 *
 * Returns an array of with all updated messages.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $messages Post updated message
 * @return array $messages New post updated messages
 */
function ambprog_updated_messages( $messages ) {
	global $post, $post_ID;

	$url1 = '<a href="' . get_permalink( $post_ID ) . '">';
	$url2 = ambprog_get_label_singular();
	$url3 = '</a>';

	$messages['ambprog_projects'] = array(
		1 => sprintf( __( '%2$s updated. %1$sView %2$s%3$s.', 'ambprog' ), $url1, $url2, $url3 ),
		4 => sprintf( __( '%2$s updated. %1$sView %2$s%3$s.', 'ambprog' ), $url1, $url2, $url3 ),
		6 => sprintf( __( '%2$s published. %1$sView %2$s%3$s.', 'ambprog' ), $url1, $url2, $url3 ),
		7 => sprintf( __( '%2$s saved. %1$sView %2$s%3$s.', 'ambprog' ), $url1, $url2, $url3 ),
		8 => sprintf( __( '%2$s submitted. %1$sView %2$s%3$s.', 'ambprog' ), $url1, $url2, $url3 )
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'ambprog_updated_messages' );
