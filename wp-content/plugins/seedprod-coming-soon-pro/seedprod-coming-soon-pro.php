<?php
/*
Plugin Name: SeedProd Coming Soon Pro
Plugin URI: http://www.seedprod.com
Description: The Ultimate Coming Soon & Maintenance Mode Plugin
Version:  4.3.5
Author: SeedProd
Author URI: http://www.seedprod.com
TextDomain: seedprod
License: GPLv2
*/

/* Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod) */


/**
 * Default Constants
 */
define( 'SEED_CSPV4_SHORTNAME', 'seedprod' ); // Used to reference namespace functions.
define( 'SEED_CSPV4_FILE', 'seedprod-coming-soon-pro/seedprod-coming-soon-pro.php' ); // Used for settings link.
define( 'SEED_CSPV4_TEXTDOMAIN', 'seedprod' ); // i18
define( 'SEED_CSPV4_PLUGIN_NAME', __( 'Coming Soon Pro', 'seedprod' ) ); // Plugin Name shows up on the admin settings screen.
define( 'SEED_CSPV4_VERSION', '4.3.5' ); // Plugin Version Number. Recommend you use Semantic Versioning http://semver.org/
define( 'SEED_CSPV4_REQUIRED_WP_VERSION', '3.5' ); // Required Version of WordPress
define( 'SEED_CSPV4_PLUGIN_PATH', plugin_dir_path( __FILE__ ) ); // Example output: /Applications/MAMP/htdocs/wordpress/wp-content/plugins/seed_csp3/
define( 'SEED_CSPV4_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // Example output: http://localhost:8888/wordpress/wp-content/plugins/seed_csp3/
define( 'SEED_CSPV4_TABLENAME', 'csp3_subscribers' );
define( 'SEED_CSPV4_API_URL', 'http://api.sellwp.co/v2/update' );



/**
 * Load Translations
 */
function seed_cspv4_load_textdomain() {
    load_plugin_textdomain( 'seedprod', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'seed_cspv4_load_textdomain');



/**
 * Upon activation of the plugin, see if we are running the required version and deploy theme in defined.
 *
 */
function seed_cspv4_activation(){

    // Check the version
    if ( version_compare( get_bloginfo( 'version' ), SEED_CSPV4_REQUIRED_WP_VERSION, '<' ) ) {
        deactivate_plugins( __FILE__ );
        wp_die( sprintf( __( "WordPress %s and higher required. The plugin has now disabled itself. On a side note why are you running an old version :( Upgrade!", 'seedprod' ), SEED_CSPV4_REQUIRED_WP_VERSION ) );
    }
    // Disable Tracking
    $options = get_option('seedredux-framework-tracking');
    $options['allow_tracking'] = 'no';

}
register_activation_hook( __FILE__, 'seed_cspv4_activation' );

//add_action('activated_plugin', 'seed_cspv4_plugin_redirect');

function seed_cspv4_plugin_redirect( $plugin, $network_activation ) {
    if ( ! isset( $_GET['activate-multi'] ) && ( $plugin == SEED_CSPV4_FILE ) && ! $network_activation ) {
		wp_redirect( admin_url( 'options-general.php?page=seed_cspv4_options' ) );
		exit();
	}
}



/**
* API Updates
*/
add_action('init', 'seed_cspv4_sellwp_updater');
require_once( 'sellwp-updater.php' );
function seed_cspv4_sellwp_updater() {
    global $seed_cspv4;
    extract($seed_cspv4);

    $seed_cspv4_api_key = "";
    $seed_emaillist = "";
    $seed_admin_email = get_option( 'admin_email','' );


    if(defined('SEED_CSP_API_KEY')){
        $seed_cspv4_api_key = SEED_CSP_API_KEY;
    }
    if(!empty($seed_cspv4['api_key'])){
        $seed_cspv4_api_key = $seed_cspv4['api_key'];
    }
    if(!empty($seed_cspv4['emaillist'])){
        $seed_emaillist = $seed_cspv4['emaillist'];
    }

    $data = array();
    $data['emaillist'] = $seed_emaillist;
    $data['admin_email'] = $seed_admin_email;

    new SellWP_UpdaterV2(
        $seed_cspv4_api_key,
        SEED_CSPV4_VERSION,
        'seedprod-coming-soon-pro/seedprod-coming-soon-pro.php',
        null,
        $data
    );

}

/**
* ManageWP Updates
*/
//require_once( 'managewp-plugins-api.php' );



/**
 * Framework
 */
if ( !class_exists( 'SeedReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/SeedReduxCore/framework.php' ) ) {
    require_once( dirname( __FILE__ ) . '/SeedReduxCore/framework.php' );
}
if ( !isset( $seedredux_demo ) && file_exists( dirname( __FILE__ ) . '/includes/config.php' ) ) {
    require_once( dirname( __FILE__ ) . '/includes/config.php' );
}



/**
 * Load Required Files and Action
 */

 //Global
 require_once( 'includes/seedredux-overrides.php' );
 require_once( 'includes/class-cspv4.php' );
 require_once( 'includes/seed-cspv4-plugin-template-loader.php' );
 require_once( 'includes/template-tags.php' );
 require_once( 'includes/functions.php' );
 require_once( 'includes/import-v3.php' );
 add_action( 'plugins_loaded', array( 'SEED_CSPV4', 'get_instance' ) );
 add_action( 'wp_enqueue_scripts', 'seed_cspv4_scripts' );
 add_action( 'admin_enqueue_scripts', 'seed_cspv4_scripts' );

 seed_cspv4_extensions();

if( is_admin() ) {
//Admin Only


}else{
// Public Only

}



/**
 * Set options global
 */
if (!isset($GLOBALS['seed_cspv4'])) {
    $GLOBALS['seed_cspv4'] = get_option('seed_cspv4', array());
}
