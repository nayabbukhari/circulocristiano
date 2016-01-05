<?php
/**
 * Plugin Name: Give - Authorize.net Gateway
 * Plugin URL: http://givewp.com/addins/authorize-net
 * Description: Give add-on gateway for Authorize.net
 * Version: 1.1
 * Author: WordImpress
 * Author URI: http://wordimpress.com
 * Contributors: dlocc, webdevmattcrom, pippinwilliamson, mordauk
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


final class Give_Authorize {

	/** Singleton *************************************************************/

	/**
	 * @var Give_Authorize The one true Give_Authorize
	 * @since 1.0
	 */
	private static $instance;


	public $id = 'give-authorize';
	public $basename;

	// Setup objects for each class
	public $admin_form;

	/**
	 * Main Give_Authorize Instance
	 *
	 * Insures that only one instance of Give_Authorize exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @staticvar array $instance
	 * @return The one true Give_Authorize
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Give_Authorize ) ) {
			self::$instance = new Give_Authorize;
			self::$instance->define_globals();
			self::$instance->includes();


			//Class Instances
			self::$instance->payments = new Give_Authorize_Payments();


			//Admin only
			if ( is_admin() ) {


			}


		}

		return self::$instance;
	}

	/**
	 * Defines all the globally used constants
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_globals() {

		if ( ! defined( 'GIVE_AUTHORIZE_PLUGIN_FILE' ) ) {
			define( 'GIVE_AUTHORIZE_PLUGIN_FILE', __FILE__ );
		}
		if ( ! defined( 'GIVE_AUTHORIZE_PLUGIN_DIR' ) ) {
			define( 'GIVE_AUTHORIZE_PLUGIN_DIR', dirname( GIVE_AUTHORIZE_PLUGIN_FILE ) );
		}
		if ( ! defined( 'GIVE_AUTHORIZE_PLUGIN_URL' ) ) {
			define( 'GIVE_AUTHORIZE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
		if ( ! defined( 'GIVE_AUTHORIZE_VERSION' ) ) {
			define( 'GIVE_AUTHORIZE_VERSION', '1.1' );
		}

		//Authorize-specific credentials
		add_action( 'plugins_loaded', array( $this, 'give_add_authorize_net_licensing' ) );

	}

	/**
	 * Authorize.net Licensing
	 */
	public function add_authorize_net_licensing() {
		if ( class_exists( 'Give_License' ) && is_admin() ) {
			$license = new Give_License( __FILE__, 'Authorize.net Gateway', GIVE_AUTHORIZE_VERSION, 'WordImpress' );
		}
	}

	/**
	 * Include all files
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {
		self::includes_general();
		self::includes_admin();
	}

	/**
	 * Load general files
	 *
	 * @return void
	 */
	private function includes_general() {
		$files = array(
			'class-authorize-payments.php',
		);

		foreach ( $files as $file ) {
			require( sprintf( '%s/includes/%s', untrailingslashit( GIVE_AUTHORIZE_PLUGIN_DIR ), $file ) );
		}
	}

	/**
	 * Load admin files
	 *
	 * @return void
	 */
	private function includes_admin() {
		if ( is_admin() ) {
			$files = array(
				'give-authorize-settings.php',
			);

			foreach ( $files as $file ) {
				require( sprintf( '%s/includes/admin/%s', untrailingslashit( GIVE_AUTHORIZE_PLUGIN_DIR ), $file ) );
			}
		}
	}

}

/**
 * The main function responsible for returning the one true Give_Authorize
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @since 1.1
 * @return object The one true Give_Form_Fields_Manager Instance
 */

function Give_Authorize() {

	//Check for Give
	if ( ! class_exists( 'Give' ) ) {
		return false;
	}

	return Give_Authorize::instance();
}

add_action( 'plugins_loaded', 'Give_Authorize' );


/**
 * Give Authorize.net Activation Banner
 *
 * @description: Includes and initializes the activation banner class; only runs in WP admin
 * @hook       admin_init
 */
function give_authorize_activation_banner() {

	//Check to see if Give is activated, if it isn't deactivate and show a banner
	if ( is_admin() && current_user_can( 'activate_plugins' ) && ! is_plugin_active( 'give/give.php' ) ) {
		add_action( 'admin_notices', 'give_ffm_child_plugin_notice' );

		//Don't let this plugin activate
		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		return false;

	}


	//Check for activation banner inclusion
	if ( ! class_exists( 'Give_Addon_Activation_Banner' ) && file_exists( GIVE_PLUGIN_DIR . 'includes/admin/class-addon-activation-banner.php' ) ) {
		include GIVE_PLUGIN_DIR . 'includes/admin/class-addon-activation-banner.php';
	}

	//Only runs on admin
	$args = array(
		'file'              => __FILE__,
		//Directory path to the main plugin file
		'name'              => __( 'Authorize.net Gateway', 'give-stripe' ),
		//name of the Add-on
		'version'           => GIVE_AUTHORIZE_VERSION,
		//The most current version
		'documentation_url' => 'https://givewp.com/documentation/add-ons/authorize-net-gateway/',
		'support_url'       => 'https://givewp.com/support/',
		//Location of Add-on settings page, leave blank to hide
		'testing'           => false,
		//Never leave as "true" in production!!!
	);

	new Give_Addon_Activation_Banner( $args );

	return false;

}

add_action( 'admin_init', 'give_authorize_activation_banner' );

/**
 * Notice for No Core Activation
 */
function give_authorize_child_plugin_notice() {

	echo '<div class="error"><p>' . __( '<strong>Activation Error:</strong> We noticed Give is not active. Please activate Give in order to use the Authorize.net Gateway.', 'give-authorize' ) . '</p></div>';
}


// registers the gateway
function give_register_authorize_gateway( $gateways ) {
	// Format: ID => Name
	$gateways['authorize'] = array(
		'admin_label'    => __( 'Authorize.net', 'give' ),
		'checkout_label' => __( 'Credit Card', 'give' )
	);

	return $gateways;
}

add_filter( 'give_payment_gateways', 'give_register_authorize_gateway' );
