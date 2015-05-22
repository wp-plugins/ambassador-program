<?php 
/**
 * Plugin Name: Ambassador Program
 * Plugin URI: http://bryanmonzon.com/
 * Description: Build a basic ambassador program for your company or organizations
 * Version: 1.1
 * Author: Bryan Monzon
 * Author URI: http://bryanmonzon.com
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AMBPROG' ) ) :


/**
 * Main AMBPROG Class
 *
 * @since 1.0 */
final class AMBPROG {

  /**
   * @var AMBPROG Instance
   * @since 1.0
   */
  private static $instance;


  /**
   * AMBPROG Instance / Constructor
   *
   * Insures only one instance of AMBPROG exists in memory at any one
   * time & prevents needing to define globals all over the place. 
   * Inspired by and credit to AMBPROG.
   *
   * @since 1.0
   * @static
   * @uses AMBPROG::setup_globals() Setup the globals needed
   * @uses AMBPROG::includes() Include the required files
   * @uses AMBPROG::setup_actions() Setup the hooks and actions
   * @see AMBPROG()
   * @return void
   */
  public static function instance() {
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AMBPROG ) ) {
      self::$instance = new AMBPROG;
      self::$instance->setup_constants();
      self::$instance->includes();
      // self::$instance->load_textdomain();
      // use @examples from public vars defined above upon implementation
    }
    return self::$instance;
  }



  /**
   * Setup plugin constants
   * @access private
   * @since 1.0 
   * @return void
   */
  private function setup_constants() {
    // Plugin version
    if ( ! defined( 'AMBPROG_VERSION' ) )
      define( 'AMBPROG_VERSION', '1.1' );

    // Plugin Folder Path
    if ( ! defined( 'AMBPROG_PLUGIN_DIR' ) )
      define( 'AMBPROG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

    // Plugin Folder URL
    if ( ! defined( 'AMBPROG_PLUGIN_URL' ) )
      define( 'AMBPROG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

    // Plugin Root File
    if ( ! defined( 'AMBPROG_PLUGIN_FILE' ) )
      define( 'AMBPROG_PLUGIN_FILE', __FILE__ );

    if ( ! defined( 'AMBPROG_DEBUG' ) )
      define ( 'AMBPROG_DEBUG', true );
  }



  /**
   * Include required files
   * @access private
   * @since 1.0
   * @return void
   */
  private function includes() {
    global $ambprog_settings, $wp_version;

    require_once AMBPROG_PLUGIN_DIR . '/includes/admin/settings/register-settings.php';
    $ambprog_settings = ambprog_get_settings();

    // Required Plugin Files
    require_once AMBPROG_PLUGIN_DIR . '/includes/commission-functions.php';
    require_once AMBPROG_PLUGIN_DIR . '/includes/functions.php';
    require_once AMBPROG_PLUGIN_DIR . '/includes/misc.php';
    require_once AMBPROG_PLUGIN_DIR . '/includes/project-functions.php';
    require_once AMBPROG_PLUGIN_DIR . '/includes/posttypes.php';
    require_once AMBPROG_PLUGIN_DIR . '/includes/registration.php';
    require_once AMBPROG_PLUGIN_DIR . '/includes/scripts.php';
    require_once AMBPROG_PLUGIN_DIR . '/includes/shortcodes.php';

    if( is_admin() ){
        //Admin Required Plugin Files
        require_once AMBPROG_PLUGIN_DIR . '/includes/admin/admin-pages.php';
        require_once AMBPROG_PLUGIN_DIR . '/includes/admin/admin-notices.php';
        require_once AMBPROG_PLUGIN_DIR . '/includes/admin/projects/metabox.php';
        require_once AMBPROG_PLUGIN_DIR . '/includes/admin/project-columns.php';
        require_once AMBPROG_PLUGIN_DIR . '/includes/admin/settings/display-settings.php';
        require_once AMBPROG_PLUGIN_DIR . '/includes/admin/user-meta.php';

    }

    require_once AMBPROG_PLUGIN_DIR . '/includes/install.php';


  }

} /* end AMBPROG class */
endif; // End if class_exists check


/**
 * Main function for returning AMBPROG Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sqcash = AMBPROG(); ?>
 *
 * @since 1.0
 * @return object The one true AMBPROG Instance
 */
function AMBPROG() {
  return AMBPROG::instance();
}


/**
 * Initiate
 * Run the AMBPROG() function, which runs the instance of the AMBPROG class.
 */
AMBPROG();



/**
 * Debugging
 * @since 1.0
 */
if ( AMBPROG_DEBUG ) {
  ini_set('display_errors','On');
  error_reporting(E_ALL);
}


