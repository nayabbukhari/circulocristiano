<?php
					
	// include the required php files - containers api key
	include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';

	if ( get_option( 'yt4wp_user_refresh_token' ) == '' ) {
		?><style>.yt4wp-error-alert:before { padding:0 !important; line-height: 1 !important; padding-right: 5px !important; }</style><?php
		wp_die( '<span id="response_message" class="yt4wp-error-alert"><strong>Woah Woah Woah...</strong> It looks like you haven\'t authenticated yet. You\'ll first need to authenticate yourself before you can go any further.</span>');
	}
	
// Check to ensure that the access token was successfully acquired.
if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
	
  try {
   
	$htmlBody = '';
  
    // Call the channels.list method to retrieve information about the
    // currently authenticated user's channel.
    $subscriptionsResponse = $youtube->subscriptions->listSubscriptions('snippet,id', array(
      'mine' => 'true',
	  'maxResults' => 50,
	  'order' => 'alphabetical'
    ));
	
		if ( count( $subscriptionsResponse['items'] ) > 0 ) {	

			$htmlBody .= '<h3>Your Subscriptions</h3>';
			
			$htmlBody .= $this->yt4wp_pagination( $subscriptionsResponse , 'browse' );
			// end set up pagination
			
			$htmlBody .= '<ul id="masonry-container">';
			
			// Testing returned data from YouTube API
			// subscription list...
				// to do---
					// get all video for given subscription
			// print_r($subscriptionsResponse);
			
			
			// add an empty stat container to unify styles throughout plugin
			$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
				
			  foreach ($subscriptionsResponse['items'] as $subscriptionsItem) {	
				// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
				if($subscriptionsItem['modelData']['snippet']['description']) {
					// trim the description
					// if there are more than 400 characters
					if(strlen($subscriptionsItem['modelData']['snippet']['description']) > 325) {
						$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($subscriptionsItem['modelData']['snippet']['description'], 0, 400).'...'; 
					} else {
						$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$subscriptionsItem['modelData']['snippet']['description']; 
					}
				} else {
					$video_description = ''; 
				}
				$htmlBody .= sprintf('<li class="youtube-plus-video-single-list-item subscribtions"><div class="dashicons dashicons-dismiss yt-plus-unsubscribe-to-channel"></div><input type="hidden" class="subscription_id" value="%s"><input type="hidden" class="channel_id" value="%s"><a class="view-subscription-videos-btn" href="#" alt="%s" title="%s" target="_blank"><img class="youtube-plus-video-thumbnail subscription" src="%s"></a> %s <h3>%s</h3> %s </li>',
					  $subscriptionsItem['id'], $subscriptionsItem['snippet']['resourceId']['channelId'], $subscriptionsItem['snippet']['resourceId']['channelId'], $subscriptionsItem['modelData']['snippet']['title'], $subscriptionsItem['snippet']['thumbnails']['high']['url'], $stat_container, $subscriptionsItem['modelData']['snippet']['title'], $video_description );
			  }
			  $htmlBody .= '</ul>';
		
		} else {
			$error_message = '<h3>Your Subscriptions</h3><ul id="masonry-container" style="position: relative; height: auto;"><span class="no_content_found_error">You have not subscribed to any channels yet.</span></ul>';
			$htmlBody .= $error_message;  
		}
	
  } catch (Google_ServiceException $e) {
	$htmlBody = '';
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
	$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
  } catch (Google_Exception $e) {
	$htmlBody = '';
    $htmlBody .= sprintf('<p>A client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
	// write the error to the error log
	$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
  }

  $_SESSION['token'] = $client->getAccessToken();
} else {
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $authUrl = $client->createAuthUrl();
  $htmlBody = '';
  // this runs only the first time the user ever installs the plugin
	 echo '<div class="error" style="margin-top:2em;">
			<h3>'.__("Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
			<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
			</div>'; 
}

?>

<!doctype html>
	  <body>	  
		<div id="subscribtions_box">
			<?php echo $htmlBody; ?>
		</div>
	  </body>