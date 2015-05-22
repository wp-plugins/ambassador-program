<?php
/**
 * Registration Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns the registration fields
 * 
 * @return [type] [description]
 */
function ambprog_registration_form_fields( $redirect = null ) {

    if ( empty( $redirect ) ) {
        $redirect = home_url();
    }
 
    ob_start(); ?>  
 
        <?php 
        // show any error messages after form submission
        ambprog_show_error_messages(); ?>
 
        <form id="ambprog_registration_form" action="" method="POST">
            
            <fieldset>
                <p>
                    <label for="ambprog_user_Login"><?php _e('Username'); ?></label><br>
                    <input name="ambprog_user_login" id="ambprog_user_login" class="required" type="text"/>
                </p>
                <p>
                    <label for="ambprog_user_email"><?php _e('Email'); ?></label><br>
                    <input name="ambprog_user_email" id="ambprog_user_email" class="required" type="email"/>
                </p>
                <p>
                    <label for="ambprog_user_first"><?php _e('First Name'); ?></label><br>
                    <input name="ambprog_user_first" id="ambprog_user_first" type="text"/>
                </p>
                <p>
                    <label for="ambprog_user_last"><?php _e('Last Name'); ?></label><br>
                    <input name="ambprog_user_last" id="ambprog_user_last" type="text"/>
                </p>
                <p>
                    <label for="password"><?php _e('Password'); ?></label><br>
                    <input name="ambprog_user_pass" id="password" class="required" type="password"/>
                </p>
                <p>
                    <label for="password_again"><?php _e('Password Again'); ?></label><br>
                    <input name="ambprog_user_pass_confirm" id="password_again" class="required" type="password"/>
                </p>
                <p>
                    <input type="hidden" name="ambprog_register_nonce" value="<?php echo wp_create_nonce('ambprog-register-nonce'); ?>"/>
                    <input type="hidden" name="ambprog_redirect" value="<?php echo $redirect; ?>"/>
                    <input type="submit" value="<?php _e('Register Your Account'); ?>"/>
                </p>
            </fieldset>
        </form>
    <?php
    return ob_get_clean();
}


// login form fields
function ambprog_login_form_fields( $redirect = null ) {
    
    if ( empty( $redirect ) ) {
        $redirect = home_url();
    }

    ob_start(); ?>
        <h3 class="ambprog_header"><?php _e('Login'); ?></h3>
 
        <?php
        // show any error messages after form submission
        ambprog_show_error_messages(); ?>
 
        <form id="ambprog_login_form"  class="ambprog_form"action="" method="post">
            <fieldset>
                <p>
                    <label for="ambprog_user_Login">Username</label>
                    <input name="ambprog_user_login" id="ambprog_user_login" class="required" type="text"/>
                </p>
                <p>
                    <label for="ambprog_user_pass">Password</label>
                    <input name="ambprog_user_pass" id="ambprog_user_pass" class="required" type="password"/>
                </p>
                <p>
                    <input type="hidden" name="ambprog_login_nonce" value="<?php echo wp_create_nonce('ambprog-login-nonce'); ?>"/>
                    <input type="hidden" name="ambprog_login_redirect" value="<?php echo $redirect; ?>"/>
                    <input id="ambprog_login_submit" type="submit" value="Login"/>
                </p>
            </fieldset>
        </form>
    <?php
    return ob_get_clean();
}




/**
 * Process the login
 * 
 * @return [type] [description]
 */
function ambprog_login_member() {
 
    if( isset( $_POST['ambprog_user_login'] ) && 
        wp_verify_nonce( isset( $_POST['ambprog_login_nonce'] ) ? $_POST['ambprog_login_nonce'] : '', 'ambprog-login-nonce' ) ) {
        
        // echo '<pre>';
        // print_r( $_POST );
        // echo '</pre>';
        // wp_die();
        // this returns the user ID and other info from the user name
        $user = get_userdatabylogin( $_POST['ambprog_user_login'] );
 
        if(!$user) {
            // if the user name doesn't exist
            ambprog_errors()->add('empty_username', __('Invalid username'));
        }
 
        if(!isset($_POST['ambprog_user_pass']) || $_POST['ambprog_user_pass'] == '') {
            // if no password was entered
            ambprog_errors()->add('empty_password', __('Please enter a password'));
        }
 
        // check the user's login with their password
        if(!wp_check_password($_POST['ambprog_user_pass'], $user->user_pass, $user->ID)) {
            // if the password is incorrect for the specified user
            ambprog_errors()->add('empty_password', __('Incorrect password'));
        }
 
        // retrieve all error messages
        $errors = ambprog_errors()->get_error_messages();
 
        // only log the user in if there are no errors
        if(empty($errors)) {
 
            wp_setcookie( $_POST['ambprog_user_login'], $_POST['ambprog_user_pass'], true );
            wp_set_current_user( $user->ID, $_POST['ambprog_user_login'] );    
            do_action( 'wp_login', $_POST['ambprog_user_login'] );
 
            wp_redirect( $_POST['ambprog_login_redirect'] ); exit;
        }
    }
}
add_action('init', 'ambprog_login_member');




// register a new user
function ambprog_add_new_member() {
    if ( isset( $_POST["ambprog_user_login"] ) && 
        wp_verify_nonce( $_POST['ambprog_register_nonce'], 'ambprog-register-nonce' ) ) {

        $user_login     = $_POST["ambprog_user_login"];  
        $user_email     = $_POST["ambprog_user_email"];
        $user_first     = $_POST["ambprog_user_first"];
        $user_last      = $_POST["ambprog_user_last"];
        $user_pass      = $_POST["ambprog_user_pass"];
        $pass_confirm   = $_POST["ambprog_user_pass_confirm"];
 
 
        if(username_exists($user_login)) {
            // Username already registered
            ambprog_errors()->add('username_unavailable', __('Username already taken'));
        }
        if(!validate_username($user_login)) {
            // invalid username
            ambprog_errors()->add('username_invalid', __('Invalid username'));
        }
        if($user_login == '') {
            // empty username
            ambprog_errors()->add('username_empty', __('Please enter a username'));
        }
        if(!is_email($user_email)) {
            //invalid email
            ambprog_errors()->add('email_invalid', __('Invalid email'));
        }
        if(email_exists($user_email)) {
            //Email address already registered
            ambprog_errors()->add('email_used', __('Email already registered'));
        }
        if($user_pass == '') {
            // passwords do not match
            ambprog_errors()->add('password_empty', __('Please enter a password'));
        }
        if($user_pass != $pass_confirm) {
            // passwords do not match
            ambprog_errors()->add('password_mismatch', __('Passwords do not match'));
        }
 
        $errors = ambprog_errors()->get_error_messages();
 
        // only create the user in if there are no errors
        if(empty($errors)) {
 
            $new_user_id = wp_insert_user(array(
                    'user_login'        => $user_login,
                    'user_pass'         => $user_pass,
                    'user_email'        => $user_email,
                    'first_name'        => $user_first,
                    'last_name'         => $user_last,
                    'user_registered'   => date('Y-m-d H:i:s'),
                    'role'              => 'subscriber'
                )
            );
            if($new_user_id) {
                // send an email to the admin alerting them of the registration
                wp_new_user_notification( $new_user_id );
 
                // log the new user in
                wp_setcookie($user_login, $user_pass, true);
                wp_set_current_user( $new_user_id, $user_login );
                do_action( 'ambrog_after_register', $new_user_id );
                do_action('wp_login', $user_login);

                
 
                // send the newly created user to the home page after logging them in
                wp_redirect( $_POST['ambprog_redirect'] ); exit;
            }
 
        }
 
    }
}
add_action('init', 'ambprog_add_new_member');

function ambprog_after_register_test( $user_id )
{
    if( $user_id < 1 )
        return;


    $first_name = $_POST['ambprog_user_first'];
    $last_name  = $_POST['ambprog_user_last'];
    $full_name = $first_name . ' ' . $last_name;

    $profile_args = array(
        'post_type'   => 'ambprog_profiles',
        'post_author' => $user_id,
        'post_status' => 'pending',
        'post_name'   => $_POST['ambprog_user_login'],
        'post_title'  => $full_name
    );

    $post_id = wp_insert_post( $profile_args );

}
add_action( 'ambrog_after_register', 'ambprog_after_register_test' );

// used for tracking error messages
function ambprog_errors(){
    static $wp_error; // Will hold global variable safely
    return isset( $wp_error ) ? $wp_error : ( $wp_error = new WP_Error( null, null, null ) );
}


// displays error messages from form submissions
function ambprog_show_error_messages() {
    if( $codes = ambprog_errors()->get_error_codes() ) {
        echo '<div class="ambprog-alert red">';
            // Loop error codes and display errors
           foreach( $codes as $code ) {
                $message = ambprog_errors()->get_error_message( $code );
                echo '<span class="error"><strong>' . __( 'Error' ) . '</strong>: ' . $message . '</span><br/>';
            }
        echo '</div>';
    }   
}


/**
 * Load our CSS file if the global variable is present.
 * @return [type] [description]
 */
function ambprog_print_css() {
    global $ambprog_load_css, $ambprog_load_dashboard_css;
 
    // this variable is set to TRUE if the short code is used on a page/post
    if ( ! $ambprog_load_css )
        return; // this means that neither short code is present, so we get out of here
 
    wp_print_styles('ambprog-form-css');

    if( ! $ambprog_load_dashboard_css )
        return;

    wp_print_styles( 'ambprog-dashboard-css' );
}
add_action('wp_footer', 'ambprog_print_css');

/**
 * Load our CSS file if the global variable is present.
 * @return [type] [description]
 */
function ambprog_print_dashboard_css() {
    global $ambprog_load_dashboard_css;
 
    // this variable is set to TRUE if the short code is used on a page/post
    if( ! $ambprog_load_dashboard_css )
        return;

    wp_print_styles( 'ambprog-dashboard-css' );
}
add_action('wp_footer', 'ambprog_print_dashboard_css');