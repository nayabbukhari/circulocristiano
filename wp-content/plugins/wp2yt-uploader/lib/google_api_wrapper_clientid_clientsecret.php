<?php
// Call set_include_path() as needed to point to your client library.
	require_once YT4WP_PATH.'inc/Google/Client.php';
	require_once YT4WP_PATH.'inc/Google/Service/YouTube.php';

	/* 
	* Check if session has started 
	* On callback, it tries to restart the session
	* Throwing an error
	*/
	if(!isset($_SESSION)) { 
			session_start();
		}
	
	/*
	 * You can acquire an OAuth 2.0 client ID and client secret from the
	 * Google Developers Console <https://console.developers.google.com/>
	 * For more information about using OAuth 2.0 to access Google APIs, please see:
	 * <https://developers.google.com/youtube/v3/guides/authentication>
	 * Please ensure that you have enabled the YouTube Data API for your project.
	 */
	$OAUTH2_CLIENT_ID = $this->optionVal['yt4wp-oauth2-key'];
	$OAUTH2_CLIENT_SECRET = $this->optionVal['yt4wp-oauth2-secret'];

	$client = new Google_Client();
	$client->setClientId($OAUTH2_CLIENT_ID);
	$client->setClientSecret($OAUTH2_CLIENT_SECRET);
	$client->setScopes('https://www.googleapis.com/auth/youtube');
	$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?page=youtube-for-wordpress', FILTER_SANITIZE_URL);
	$client->setRedirectUri($redirect);

	// Define an object that will be used to make all API requests.
	$youtube = new Google_Service_YouTube($client);

	if ( isset( $screen_base ) && $screen_base == '' ) {
		$screen_base = get_current_screen()->base;
	}

	if (isset($_GET['code'])) {
	
	   if (strval($_SESSION['state']) !== strval($_GET['state'])) {
		// die('The session state did not match.');
	  }

	  $client->authenticate($_GET['code']);
	  
	  $_SESSION['token'] = $client->getAccessToken();
	  
		$token_array = explode( "," ,  str_replace( "}" , "" , str_replace( "{" , "" , str_replace( "\"" , "" ,  $_SESSION['token'] ) ) ) );
		
		// empty exploded token data array
		$exploded_token_array = array();
		
		// loop over to build our array of token data
		foreach ( $token_array as $token_data ) {
			$exploded_token_array[] = explode( ":" , $token_data );
		}
		
		// remove our \ in the refresh token, which throws errors
		// when sending to YouTube APi
		$refresh_token = str_replace( "\\" , "" , $exploded_token_array[3][1] );
			
		// update the refresh token option with the new refresh token	
		update_option( 'yt4wp_user_refresh_token' , $refresh_token );
		
		// set the access token
		$client->setAccessToken($_SESSION['token']);

		// set our headers
		  $client->authenticate($_GET['code']);
		  $_SESSION['token'] = $client->getAccessToken();
		  header('Location: ' . $redirect);
	  
	}

	if (isset($_SESSION['token'])) {
	  $client->setAccessToken($_SESSION['token']);
	}
	
	
	if ($client->isAccessTokenExpired()) {
		// check if the user has ANY data stored in the token session
		// if they do, but the token has expired, we simply call a refresh
		// on the refresh token to retreive new access tokens
		//
		// we do this to avoid re-authorizing every time you use
		// the plugin -- this is all done behind the scenes ;)
		if ( get_option( 'yt4wp_user_refresh_token' ) != '' ) {
		
			try {
				$client->refreshToken( get_option( 'yt4wp_user_refresh_token' ) );
				$_SESSION['token'] = $client->getAccessToken();
			} catch( Exception $e ) {
				echo '<span id="response_message" class="yt4wp-error-alert special" style="margin-top:0;"><p><strong>Oh No!</strong> ' . $e->getMessage() . '. Double check that your client keys are correct.<p>If the error persits please <a href="http://www.yt4wp.com/support" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';
				$errors = true;
				/* Write the error to our error log */
				$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
			}
			
		} else {
		
			 $htmlBody = '';
			// If the user hasn't authorized the app, initiate the OAuth flow
			  $state = mt_rand();
			  $client->setState($state);
			  $_SESSION['state'] = $state;
				$authUrl = $client->createAuthUrl();
				$htmlBody .= '<div class="error" style="margin-top:2em;">
				<h3>'.__("Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
				<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
				</div>'; 
	
		}
	
	}