<?php
/************************************************************
*
*					ManageWP Premium Plugin  Integration
*
*************************************************************/



// mwp_premium_update_notification filter
//
// Hook to this filter to provide the new version of your plugin if available
//


add_filter('mwp_premium_update_notification', 'seed_cspv4_mwp_update_notification');

if( !function_exists('seed_cspv4_mwp_update_notification') ) {
	function seed_cspv4_mwp_update_notification( $premium_updates ){

		if( !function_exists( 'get_plugin_data' ) || !function_exists( 'is_plugin_active') ) // make sure we have the needed functions available
			if (!@include_once( ABSPATH.'wp-admin/includes/plugin.php'))
				return $premium_updates;

		if (!(is_plugin_active('worker/init.php') || is_plugin_active('managewp/init.php'))) // ManageWP client plugin needed
			return $premium_updates;

		global $seed_cspv4;
		$api_key = $seed_cspv4['api_key'];
		$updater = new SellWP_UpdaterV2();
		$response = $updater->getRemote_information(
				$api_key,
				SEED_CSPV4_VERSION,
				'seedprod-coming-soon-pro/seedprod-coming-soon-pro.php'
				);


		$cspv4 = get_plugin_data( SEED_CSPV4_PLUGIN_PATH.'seedprod-coming-soon-pro.php' );   // EDIT: if necessary edit the path to your main plugin file
		$cspv4['type'] = 'plugin';
		$cspv4['new_version'] = $response->new_version;  // EDIT: your plugin's new version
        $cspv4['old_version'] = $cspv4['Version'];
	//	error_log( print_r( $cspv4, true ) );



    if($cspv4['new_version'] === $cspv4['old_version'])//Check if there is a new version
			return $premium_updates;

		array_push($premium_updates, $cspv4);
		return $premium_updates;
	}
}



// mwp_premium_perform_update filter
//
// Hook to this filter to return either the URL to the new version
// or your callback function which will perform the update when called
//

add_filter('mwp_premium_perform_update', 'seed_cspv4_mwp_perform_update');

if( !function_exists('seed_cspv4_mwp_perform_update') ) {
	function seed_cspv4_mwp_perform_update( $update ){

		if( !function_exists( 'get_plugin_data' ) || !function_exists( 'is_plugin_active') )  // make sure we have the needed functions available
			if (!@include_once( ABSPATH.'wp-admin/includes/plugin.php'))
				return $update;

		if (!(is_plugin_active('worker/init.php') || is_plugin_active('managewp/init.php'))) // ManageWP client plugin needed
			return $update;

		global $seed_cspv4;
		$api_key = $seed_cspv4['api_key'];
		$updater = new SellWP_UpdaterV2();
		$response = $updater->getRemote_information(
				$api_key,
				SEED_CSPV4_VERSION,
				'seedprod-coming-soon-pro/seedprod-coming-soon-pro.php'
				);


		$cspv4= get_plugin_data(  SEED_CSPV4_PLUGIN_PATH.'seedprod-coming-soon-pro.php' );   // EDIT: if necessary edit the path to your main plugin file

		$cspv4['url'] = $response->download_link; 	// EDIT: provide URL to the archive file with the new version and we will use WordPress update mechanism
    $cspv4['type']= 'plugin';
    $cspv4['slug'] = 'seedprod-coming-soon-pro/seedprod-coming-soon-pro.php';
		//$my_addon['callback'] = 'my_update_callback'; 		// EDIT: OR alternately provide your own callback function for managing the update. Do not use both.

		array_push($update, $cspv4);

		return $update;
	}
}

// mwp_premium_update_check filter
//
// Hook to this filter to provide a function that checks for updates
//This hook is required only if you are using callback to check the  plugin version

//add_filter('mwp_premium_update_check', 'seed_cspv4_mwp_update_check');

if( !function_exists('seed_cspv4_mwp_update_check') ) {
	function seed_cspv4_mwp_update_check( $update ){

		if( !function_exists( 'get_plugin_data' ) || !function_exists( 'is_plugin_active') )   // make sure we have the needed functions available
			if (!@include_once( ABSPATH.'wp-admin/includes/plugin.php'))
				return $premium_updates;

		if (!(is_plugin_active('worker/init.php') || is_plugin_active('managewp/init.php'))) // ManageWP client plugin needed
			return $update;

		$my_addon = get_plugin_data(  __FILE__ );   //EDIT: if necessary edit the path to your main plugin file

		$my_addon['callback'] = 'my_update_callback'; // EDIT: provide your callback function which checks for your plugin updates
		//EDIT: If you provided path to zip file, don't use callback

		array_push($update, $my_addon);

		return $update;
	}
}
