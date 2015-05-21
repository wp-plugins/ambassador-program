<?php
/**
 * Shortcodes
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Shortcode that displays the registration form
 * 
 * @return [type] [description]
 */
function ambprog_registration_form( $atts, $content = null ) {
 
    // only show the registration form to non-logged-in members
    if( !is_user_logged_in() ) {
 
        global $ambprog_load_css;

        extract( 
            shortcode_atts( 
                array(
                'redirect' => null,
                ),
                $atts, 'register_form' )
        );
 
        // set this to true so the CSS is loaded
        $ambprog_load_css = true;
 
        // check to make sure user registration is enabled
        $registration_enabled = get_option('users_can_register');
 
        // only show the registration form if allowed
        if( $registration_enabled ) {
            $output = ambprog_registration_form_fields( $redirect );
        } else {
            $output = __('User registration is not enabled');
        }
        return $output;
    }else{
        return '<p>You are logged in already and registered already.</p>';
    }
}
add_shortcode('register_form', 'ambprog_registration_form');


/**
 * Shortcode that displays the user login form
 * 
 * @return [type] [description]
 */
function ambprog_login_form( $atts, $content = null ) {
 
    if(!is_user_logged_in()) {
 
        global $ambprog_load_css;

        extract( 
            shortcode_atts( 
                array(
                'redirect' => null,
                ),
                $atts, 'login_form' )
        );
        

 
        // set this to true so the CSS is loaded
        $ambprog_load_css = true;
 
        $output = ambprog_login_form_fields( $redirect );
    } else {
        // could show some logged in user info here
        $output = '<p>You\'re already logged in!</p>';
    }
    return $output;
}
add_shortcode('login_form', 'ambprog_login_form');



/**
 * Create Project Form
 * 
 * @return [type] [description]
 */
function ambprog_create_project_form( $atts, $content = null ) {


    if(is_user_logged_in()) {

        global $current_user, $ambprog_load_css;

        $ambprog_load_css = true;

        // print_r( $current_user );
        ob_start(); 
        if( isset( $_GET['post'] ) ) {
            switch( $_GET['post'] ) {
                case 'successful':
                    echo '<p class="zilla-alert green">' . __('Project created', 'ambprog') . '</p>';
                    break;
                case 'failed' :
                    echo '<p class="zilla-alert red">' . __('Please fill in all the info', 'ambprog') . '</p>';
                    break;
            }
        }
        ?>
        <form id="ambprog_create_project" action="" method="POST">
            <fieldset>
                <p>
                    <label for="organization_name">Organization Name</label><br>
                    <input name="organization_name" id="organization_name" type="text"/>    
                </p>
                
                <p>
                    <label for="organization_description">Organization Description</label><br>
                    <textarea name="organization_description" id="organization_description"></textarea>
                </p>

                <p>
                    <label for="contanct_name">Contact Name</label><br>
                    <input name="contanct_name" id="contanct_name" type="text"/>    
                </p>
                
                <p>
                    <label for="contact_email">Contact Email</label><br>
                    <input name="contact_email" id="contact_email" type="email"  />    
                </p>
                
                <p>
                    <label for="current_url">Current URL</label><br>
                    <input name="current_url" id="current_url" type="url"  />    
                </p>
                
                <p>
                    <?php wp_nonce_field('projects_nonce', 'projects_nonce_field'); ?>
                    <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
                    <input type="submit" name="project_submit" value="<?php _e('Submit Project', 'ambprog'); ?>"/>    
                </p>
                
            </fieldset>
        </form>
        <?php 
        $output = ob_get_clean();
    }else{
        $output = '<p>You must be logged into submit a project.</p>';
    }

    return $output;
}
add_shortcode('submit_project_form', 'ambprog_create_project_form');


function ambprog_dashboard( $atts, $content = null ) {

    if( is_user_logged_in() ) {

        global $post, $current_user, $ambprog_load_dashboard_css;

        $ambprog_load_dashboard_css = true;

        ob_start();

        $profile_args = array(
            'post_status' => array( 'publish', 'pending' ),
            'post_type'   => 'ambprog_projects',
            'nopaging'    => true,
            'author'      => $current_user->ID

        );
        $profile_query = new WP_Query( $profile_args ); 

        $user_meta               = get_userdata( $current_user->ID );
        $register_date           = $user_meta->user_registered;
        $register_date_formatted = date( 'F j, Y', strtotime( $register_date ) );


        if( $profile_query->have_posts() ) : ?>
        <div class="ambprog_wrap">
            <div class="ambprog_stats_wrap">
                <div class="ambprog_total_referrals ambrprog_stat">
                    Total referrals: <br>
                    <span class="ambprog_stat_number"><?php print_r( ambprog_get_project_count( $current_user->ID ) ); ?></span>
                </div>
                <div class="ambprog_total_commission ambrprog_stat">
                    Total earned: <br>
                    <span class="ambprog_stat_number">$<?php echo ambprog_get_commission_totals( $current_user->ID ); ?></span>
                    <span class="ambprog_register_date">since <?php echo $register_date_formatted; ?></span>
                </div>
                <a href="/dashboard/submit-project" class="ambprog-button black large square" style="float:right;">+ New Project</a>
            </div>
        
            <table class="ambprog_dashboard">
                <thead>
                    <th>Referral/Org Name</th>
                    <th>Date Submitted</th>
                    <th>Project Cost</th>
                    <th>Project Commission</th>
                    <th>Last Note</th>
                    <th>Progress</th>
                </thead>

                <tbody>
                    <?php while( $profile_query->have_posts() ) : $profile_query->the_post(); ?>
                    <?php 
                        $project_status = get_post_meta( get_the_ID(), 'ambprog_project_status', true );
                        $project_cost = get_post_meta( get_the_ID(), 'ambprog_project_cost', true );

                        $project_status = isset($project_status) ? $project_status : 0;
                    ?>
                    <tr class="<?php echo get_post_status( get_the_ID() ); ?> ambprog-project ambprog-project-<?php the_ID();  ?>">
                        <td>
                            <?php if( get_post_status( ) == 'pending' ) : ?>
                            
                            <span class="pending-badge">pending approval</span>
                        <?php else : ?>
                            <?php the_title(); ?>
                        <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $now = time(); // Current time in seconds since Epoch
                            $post_created = strtotime( $post->post_date );  // post's creation date in seconds since Epoch, so we're comparing apples to apples
                            $one_day_in_seconds = 24*60*60;
                            if ( ( $now - $post_created ) < $one_day_in_seconds ) {
                                echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago';
                            } else {
                                the_time( 'j F Y' );
                            }
                             ?>
                        </td>
                        <td>
                            <?php if( $project_cost ) : echo '$' . number_format( $project_cost ); endif; ?>
                        </td>
                        <td>
                            <?php echo ambprog_get_commission_amount( $post->ID ); ?>
                        </td>
                        <td>
                            <?php
                            $args = array(
                                'status' => 'approve',
                                'number' => '1',
                                'post_id' => get_the_ID(), // use post_id, not post_ID
                            );
                            $comments = get_comments( $args );
                            if( $comments ) :
                                foreach($comments as $comment) :
                                    echo $comment->comment_content;
                                endforeach;
                            else : ?>
                            <?php if( get_post_status( ) == 'pending' ) : ?>Referral Received <?php else : ?>In process <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="lead-progress-wrapper">
                                <span class="lead-progress-inner" style="width: <?php echo $project_status; ?>%"></span>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
            <p>You have not submitted any projects/referrals</p>
        <?php endif; wp_reset_query(); ?>

        <?php $output = ob_get_clean();
    }else{
        $output = '<p>Log in to view your dashboard';
    }
    return $output;
}
add_shortcode('referral_dashboard', 'ambprog_dashboard');