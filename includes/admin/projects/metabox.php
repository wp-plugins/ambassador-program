<?php
/**
 * Metabox Functions
 *
 * @package     Ambassador Program
 * @subpackage  Admin/Classes
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Register all the meta boxes for the Download custom post type
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */
function ambprog_add_meta_box() {

    $post_types = apply_filters( 'ambprog_metabox_post_types' , array( 'ambprog_projects' ) );

    foreach ( $post_types as $post_type ) {

        /** Class Configuration */
        add_meta_box( 'projectinfo', 'Project Information',  'ambprog_render_meta_box', $post_type, 'normal', 'high' );

        
    }
}
add_action( 'add_meta_boxes', 'ambprog_add_meta_box' );


/**
 * Sabe post meta when the save_post action is called
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param int $post_id Download (Post) ID
 * @global array $post All the data of the the current post
 * @return void
 */
function ambprog_meta_box_save( $post_id ) {
    global $post, $ambprog_settings;

    if ( ! isset( $_POST['ambprog_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ambprog_meta_box_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) )
        return $post_id;

    if ( isset( $post->post_type ) && $post->post_type == 'revision' )
        return $post_id;



    // The default fields that get saved
    $fields = apply_filters( 'ambprog_metabox_fields_save', array(
            'ambprog_project_cost',
            'ambprog_project_status',
            'ambprog_project_commission_rate'
        )
    );


    foreach ( $fields as $field ) {
        if ( ! empty( $_POST[ $field ] ) ) {
            $new = apply_filters( 'etm_metabox_save_' . $field, $_POST[ $field ] );
            update_post_meta( $post_id, $field, $new );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'ambprog_meta_box_save' );





/** Class Configuration *****************************************************************/

/**
 * Class Metabox
 *
 * Extensions (as well as the core plugin) can add items to the main download
 * configuration metabox via the `ambprog_meta_box_fields` action.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */
function ambprog_render_meta_box() {
    global $post, $ambprog_settings;

    do_action( 'ambprog_meta_box_fields', $post->ID );
    wp_nonce_field( basename( __FILE__ ), 'ambprog_meta_box_nonce' );
}



/**
 * Render the fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param  [type] $post [description]
 * @return [type]       [description]
 */
function ambprog_render_fields( $post )
{
    global $post, $ambprog_settings; 

    /*$postmeta_check = get_post_meta($post->ID);
    echo '<pre>';
    var_dump($postmeta_check);
    echo '</pre>';*/
    $ambprog_project_cost            = get_post_meta( $post->ID, 'ambprog_project_cost', true);
    $ambprog_project_status          = get_post_meta( $post->ID, 'ambprog_project_status', true);
    $ambprog_project_commission_rate = get_post_meta( $post->ID, 'ambprog_project_commission_rate', true);
    echo 'comm: '. ambprog_get_commission(false);
    
    ?>
    
    <div id="project_info_wrap">
        <p>
            <label for="ambprog_project_cost">
                Estimated Project Cost:<br>
                $<input type="number" name="ambprog_project_cost" value="<?php echo $ambprog_project_cost; ?>">
            </label>
        </p>

        <p>
            <label for="ambprog_project_status">
                Project Status:<br>
                <input type="number" name="ambprog_project_status" onchange="handleChange(this);" value="<?php echo $ambprog_project_status; ?>" max="100">%
            </label>
        </p>

        <p>
            <label for="ambprog_project_commission_rate">
                Project Commission Rate:<br>
                <input type="number" name="ambprog_project_commission_rate" onchange="handleChange(this);" value="<?php echo $ambprog_project_commission_rate; ?>" max="100">% <br>
                <em class="hint">This will override the user commission rate and the global default rate.</em>
            </label>
        </p>
    </div>
    
    <?php

}
add_action( 'ambprog_meta_box_fields', 'ambprog_render_fields', 10 );

