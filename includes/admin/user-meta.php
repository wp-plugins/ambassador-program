<?php
/**
 * User Meta Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Adds the form fields to the user profile page
 * @param  [type] $user [description]
 * @return [type]       [description]
 */
function ambprog_ambassador_information_fields( $user ) { 
    if ( !current_user_can( 'manage_options', $user->ID ) )
        return false;
    ?>

    <h3>Ambassador Information</h3>

    <table class="form-table">

        <tr>
            <th><label for="ambprog_ambassador_commission">Commision Rate</label></th>

            <td>
                <input type="number" max="100" name="ambprog_ambassador_commission" id="ambprog_ambassador_commission" value="<?php echo esc_attr( get_the_author_meta( 'ambprog_ambassador_commission', $user->ID ) ); ?>" class="regular-text" />%<br />
                <span class="description">Please enter the commission rate for this ambassador.</span>
            </td>
        </tr>

    </table>
<?php }
add_action( 'show_user_profile', 'ambprog_ambassador_information_fields' );
add_action( 'edit_user_profile', 'ambprog_ambassador_information_fields' );


/**
 * Update user meta when the user saves their profile
 * 
 * @param  [type] $user_id [description]
 * @return [type]          [description]
 */
function ambprog_ambassador_information_save_fields( $user_id ) {

    if ( !current_user_can( 'manage_options', $user_id ) )
        return false;

    /* Copy and paste this line for additional fields. Make sure to change 'ambprog_ambassador_commission' to the field ID. */
    update_user_meta( $user_id, 'ambprog_ambassador_commission', $_POST['ambprog_ambassador_commission'] );
}
add_action( 'personal_options_update', 'ambprog_ambassador_information_save_fields' );
add_action( 'edit_user_profile_update', 'ambprog_ambassador_information_save_fields' );




