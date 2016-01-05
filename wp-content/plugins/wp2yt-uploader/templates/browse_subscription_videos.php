<?php			
			
// Call set_include_path() as needed to point to your client library.
require_once YT4WP_PATH.'inc/Google/Client.php';
require_once YT4WP_PATH.'inc/Google/Service/YouTube.php';

global $YT4WPBase;

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

if (isset($_GET['code'])) {
  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
    // die('The session state did not match.');
  }

  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: ' . $redirect);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}
		
	
// Check to ensure that the access token was successfully acquired.
if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {

  try {
			
			$subscriptionData = $youtube->channels->listChannels('snippet,contentDetails', array(
				'id' =>  $clicked_subscription,
				// maybe set up pagination here, for users
				// who have more than 50 vids
				'maxResults' => 50
			 ));
			 
			 // Check if the user is subscribed to this channel
			$is_user_subscribed = $youtube->subscriptions->listSubscriptions('id', array(
					'mine' =>  'true',
					// maybe set up pagination here, for users
					// who have more than 50 vids
					'forChannelId' => $channel_id,
					'maxResults' => 1
				 ));
				
				if ( $is_user_subscribed['items'] ) {
					$unsubscribe_id = $is_user_subscribed['items'][0]['id'];
					$subscribe_button = '<div class="yt-subscribe-btn" title="unsubscribe" alt="' . $unsubscribe_id . '"><div class="dashicons dashicons-no"></div> Un-Subscribe</div>';
				} else {
					$subscribe_button = '<div class="yt-subscribe-btn" title="subscribe"><div class="dashicons dashicons-yes"></div> Subscribe</div>';
				}
						

		 $htmlBody = '';
		 $htmlBody .= $subscribe_button;
		 $htmlBody .= '<input type="hidden" id="watch_later_list_id" value="'.$this->getUserWatchLaterListId().'">';
		 $htmlBody .= '<h3 id="browse_page_title">' . stripslashes( $user_name ) . ' Uploads</h3>' . $this->yt4wp_pagination( $subscriptionData , 'search' ) . '<ul id="masonry-container">';
		 // watch later list ID to allow for users to add videos to a watch later list
		 // add our dialog drawr for error message responses
		 $dialog_drawer = '<section class="dialog_message_drawer"></section>';
		// set the uploads playlist ID
		if ( isset( $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ) ) {
			$subscription_uploads_playlist_id = $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
		} else {
			$subscription_uploads_playlist_id = '';
		}
			
		// set the favorites playlist ID
		if ( isset( $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['likes'] ) ) {
			$subscription_likes_playlist_id = $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['likes'];		
		} else {
			$subscription_likes_playlist_id = '';
		}
		// set the favorites playlist ID
		if ( isset( $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['favorites'] ) ) {		
			$subscription_favorites_playlist_id = $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['favorites'];		
		} else {
			$subscription_favorites_playlist_id = '';
		}
		
		// $subscription_favorites_playlist_id = $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['favorites'];
		// print_r($subscriptionData);
		$subscriptionUploadData = $youtube->playlistItems->listPlaylistItems('snippet', array(
				'playlistId' =>  $subscription_uploads_playlist_id,
				// maybe set up pagination here, for users
				// who have more than 50 vids
				'maxResults' => 50
			 ));
			 
		if ( $screen_base == 'toplevel_page_youtube-for-wordpress' ) {
			$create_post_button = '<a class="button button-secondary create_video_post_button" disabled="disabled" title="Create Post From This Video (add on not installed)"><div class="dashicons dashicons-media-text" style="line-height:1.3"></div></a>';
		} else {
			$create_post_button = '';
		}
		$add_to_watch_later = '<a class="button button-secondary add_to_watch_later" title="Add to Watch Later"><div class="dashicons dashicons-clock" style="line-height:1.3"></div></a>';
		$send_to_playlist_button = '<a class="button button-secondary send_to_playlist_button" disabled="disabled" title="Add Video To Playlist (add on not installed)"><div class="dashicons dashicons-editor-ol" style="line-height:1.3"></div></a>';

		  foreach ($subscriptionUploadData['items'] as $subscriptionUploadVideo) {
				
				// check if the setting is set, so we don't run unecessary API requests
					if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
					
					// loop to get video statistics...
						$video_statistics = $youtube->videos->listVideos('statistics', array(
							'id' => $subscriptionUploadVideo['modelData']['snippet']['resourceId']['videoId']
						 ));
						
						// build the video stats array
						foreach( $video_statistics as $stat ) {
							// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
							$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
							$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
							$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
							$view_count = number_format( $stat['statistics']['viewCount'] );
							
							$stat_container = '<span class="yt-plus-stats-container">';
								$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
								$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
							$stat_container .= '</span>';	
						}
							
					} else { // if the setting is disabled
						// set the stat container to none
						$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
					}	
				
				// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
				if($subscriptionUploadVideo['modelData']['snippet']['description']) {
					// trim the description
					// if there are more than 400 characters
					if(strlen($subscriptionUploadVideo['modelData']['snippet']['description']) > 325) {
						$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($subscriptionUploadVideo['modelData']['snippet']['description'], 0, 400).'...'; 
					} else {
						$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$subscriptionUploadVideo['modelData']['snippet']['description']; 
					}
				} else {
					$video_description = ''; 
				}	
		  
								
				$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
					$htmlBody .= '<input type="hidden" class="video_id" value="' . $subscriptionUploadVideo['modelData']['snippet']['resourceId']['videoId'] . '">';
					$htmlBody .= '<section class="yt-plus-outside-hidden">';
						$htmlBody .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $subscriptionUploadVideo['modelData']['snippet']['resourceId']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">' . $dialog_drawer; 
						$htmlBody .= '<img class="youtube-plus-video-thumbnail" src="' . $subscriptionUploadVideo['modelData']['snippet']['thumbnails']['medium']['url'] . '"></a>';
						$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_likes_favs_history_buttons' , $this->yt4wp_get_likes_favs_history_buttons( $screen_base ) ) . '</section>';
					$htmlBody .= '</section>' . $stat_container; 
					$htmlBody .= '<h3>' . $subscriptionUploadVideo['modelData']['snippet']['title'] . '</h3>' . $video_description; 
				$htmlBody .= '</li>';
				
			 
			}
	 	  
      $htmlBody .= '</ul>';
    
  } catch (Google_ServiceException $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>A client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }

  $_SESSION['token'] = $client->getAccessToken();
} else {
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $authUrl = $client->createAuthUrl();
 // this runs only the first time the user ever installs the plugin
	 $htmlBody .= '<div class="error" style="margin-top:2em;">
			<h3>'.__("Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
			<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
			</div>'; 
}

?>

<!doctype html>

	  <body>		
		<!-- navigation items to change the playlist were pulling from -->
		<div id="profile_sub_navigation">
			<ul>
				<li><a href="#" class="button button-secondary sub-nav-button youtube-plus-back-to-subscriptions" title="Back to Subscriptions" <?php if ( $current_tab != 'Subscriptions' ) { ?> style="display:none;" <?php } ?>>Back to Subscriptions</a></li>
				<li><a href="#" class="button button-secondary sub-nav-button sub-nav-button-active youtube-plus-subscription-uploads-playlist" title="<?php echo esc_attr( stripslashes( $user_name ) ); ?>" alt="<?php echo $clicked_subscription; ?>"><?php echo stripslashes( $user_name ); ?>'s Uploads</a></li>
				<li><a href="#" class="button button-secondary sub-nav-button view-subscription-playlists-btn" alt="<?php echo $channel_id; ?>" title="<?php echo esc_attr( stripslashes( $user_name ) ); ?>"><?php echo stripslashes( $user_name ); ?>'s Playlists</a></li>
				<li><a href="#" class="button button-secondary sub-nav-button <?php if ($subscription_likes_playlist_id) { ?>youtube-plus-subscription-likes-playlist<?php } ?>" alt="<?php echo $subscription_likes_playlist_id; ?>" title="<?php echo esc_attr( stripslashes( $user_name ) ); ?> Likes" <?php if (!$subscription_likes_playlist_id) { echo 'disabled="disabled"'; } ?>><?php echo stripslashes( $user_name ); ?>'s Likes</a></li>
				<li><a href="#" class="button button-secondary <?php if ($subscription_favorites_playlist_id) { ?> sub-nav-button youtube-plus-subscription-favorites-playlist<?php } ?>" alt="<?php echo $subscription_favorites_playlist_id; ?>" title="<?php echo esc_attr( stripslashes( $user_name ) ); ?> Favorites"  <?php if (!$subscription_favorites_playlist_id) { echo 'disabled="disabled"'; } ?> onclick="return false;"><?php echo stripslashes( $user_name ); ?>'s Favorites</a></li>
			</ul>
			<input type="hidden" id="watch_later_list_id" value="<?php echo $this->getUserWatchLaterListId(); ?>">
			<input type="hidden" id="channel_id" value="<?php echo $channel_id; ?>">
		</div>
		
		<div id="subscription_content_div">
			<?php echo $htmlBody; ?>
		</div>
		
		
	  </body>