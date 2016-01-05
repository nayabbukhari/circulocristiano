<?php

/* 
YouTube for WordPress Main Upload Page
Maintained and Supported by http://yt4wp.com
*/

// load thickbox styles etc.
add_thickbox();

// Call set_include_path() as needed to point to your client library.
require_once YT4WP_PATH.'inc/Google/Client.php';
require_once YT4WP_PATH.'inc/Google/Service/YouTube.php';

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

// initialize the body, and a new Google Client class
$htmlBody = '';
$client = new Google_Client();

// Set the Scopes
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');

// store our screen_base variable when not created
if ( !isset( $screen_base ) ) {
	$screen_base = 'Front End';
} 

// checking if were on SSL,
// if we are, we need to set the http:// to https://
if ( is_ssl () ) {
	$redirect_prefix = 'https://';
} else {
	$redirect_prefix = 'http://';
}

// if we're on the dashboard, the redirect URL 
// should reflect the correct admin page
if ( is_admin() ) {
	$redirect = esc_url( $redirect_prefix . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?page=youtube-for-wordpress' );
// if were on the front end
// the redirect URL should
// be the current page
} else {
	$redirect = esc_url( $redirect_prefix . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] );
}

// Setup the redirect URL
$client->setRedirectUri($redirect);
// Set the access type to offline
$client->setAccessType('offline');

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);

// Check for a returned code after authenticating
if ( isset($_GET['code']) ) {
	
	// try and authenticate the returned code
	try {
	
		if ( $client->authenticate($_GET['code']) ) {
		
		  $_SESSION['token'] = $client->getAccessToken();
		  
		  $token_decode = json_decode($_SESSION['token']);
		  
			if ( isset( $token_decode->refresh_token ) ) { 
				?>
				<style>#initial_setup_message{margin-top:8em;}.nav-tab-wrapper{display:none;}</style>
				<script>
				jQuery(document).ready(function(){
					setTimeout(function() {
						jQuery( '.success_redirect_preloader' ).fadeIn();
					}, 1000 );
				});
				</script>
				<?php
				update_option( 'yt4wp_user_refresh_token' , $token_decode->refresh_token );
				$client->setAccessToken($_SESSION['token']);
				echo '<meta http-equiv="refresh" content="1.5;url=' . admin_url() . 'admin.php?page=youtube-for-wordpress" />';
				// display a nice success message, and redirect the user
				$message = '<div id="initial_setup_message" class="yt4wp-setup-alert"><div id="sucess_redirect_box"><span class="dashicons dashicons-yes" style="width:100%;color:#40ad6e;margin-bottom:.25em;font-size:1.75em;"></span>' . __( "Successfuly authenticated. Redirecting you now..." , "yt-plus-translation-text-domain" ) . '<img src="' . admin_url() . '/images/wpspin_light.gif" class="success_redirect_preloader"></div></div>';
				wp_die( $message );
				exit;
			} else {
				$htmlBody .= '<span class"yt4wp-error-alert">Oops, it looks like we ran into an error. Please refresh the page and try again.</span>';
			}
			
		}
	// catch any exceptions that may be thrown, and display it back to the user
	} catch ( Exception $e ) {
		// setup the error message
		$message = '<div id="response_message" class="yt4wp-error-alert">
					<h3>There was an error in your request</h3>
					<p>'.$e->getMessage().'.</p>
					<p>If you keep receiving this error, please <a href="http://www.yt4wp.com/support/submit-ticket/" target="_blank" title="Submit a support ticket">open a support ticket</a> with the YouTube for WordPress support team, and reference the following error number : Error #' . $e->getCode() . '</p>
					</div>';
		
		
		/* Write the error to our error log */
		$error_message = $e->getMessage();
		$error_code = $e->getCode();
		$this->writeErrorToErrorLog( $error_message , $error_code );
		
		// kill
		wp_die( $message );
	}
	
} 



// test expiration of the users token
// echo $client->isAccessTokenExpired($_SESSION['token']);
if ( $client->isAccessTokenExpired() ) {
	
	// check if the user has ANY data stored in the token session
	// if they do, but the token has expired, we simply call a refresh
	// on the refresh token to retreive new access tokens
	//
	// we do this to avoid re-authorizing every time you use
	// the plugin -- this is all done behind the scenes ;)
	if ( get_option( 'yt4wp_user_refresh_token' ) != '' && ( $this->optionVal['yt4wp-oauth2-key'] != '' && $this->optionVal['yt4wp-oauth2-secret'] != '' && $this->optionVal['yt4wp-api-key'] != '' ) ) {
		
		try {
			$client->refreshToken( get_option( 'yt4wp_user_refresh_token' ) );
			$_SESSION['token'] = $client->getAccessToken();
			$client->setAccessToken($client->getAccessToken());
		} catch (Exception $e) {
			$htmlBody .= sprintf("<span id='response_message' class='yt4wp-error-alert'><p><strong>Oh No!</strong> %s. Double check that you have entered your client ID and client secret keys correctly.<p>If the error persits please <a href='http://www.yt4wp.com/support' target='_blank' title='Submit a Ticket'>open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></span>",
				htmlspecialchars($e->getMessage()),$e->getCode());
			/* Write the error to our error log */
			$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
		}
		
	} else {

		if( get_current_screen()->base == 'toplevel_page_youtube-for-wordpress' ) {
			?>
				<script type="text/javascript">
					jQuery(document).ready(function() {	
						jQuery('.switch-yt-channels').click(function(e) {
							// store the url
							var auth_url = jQuery(this).attr('href');
							jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'yt_plus_settings_form',
									form_action: 'logout_and_revoke_access_token',
									access_token: '<?php echo $OAUTH2_CLIENT_ID; ?>'
								},
								dataType: 'json',
								success: function (response) {
									if ( null == auth_url ) {	
										location.reload();
									} else {
										//  direct the user to the auth screen
										window.location.replace(auth_url);		
									}
								},
								error : function(error_response) {
									console.log(error_response.responseText);
								}
							});
						e.preventDefualt();
						});
					});
				</script>
				<style>
					#initial_setup_message{margin-top:8em;padding:3em 0;}body.toplevel_page_youtube-for-wordpress .nav-tab-wrapper { display: none; }
				</style>
			<?php
			
				// this runs only the first time the user ever installs the plugin
				 echo '<div id="initial_setup_message" class="yt4wp-setup-alert">
						<h2>'.__("Welcome to YouTube for WordPress", "yt-plus-translation-text-domain") .'</h2>';
					
					if ( $this->optionVal['yt4wp-api-key'] == '' || $this->optionVal['yt4wp-oauth2-secret'] == '' || $this->optionVal['yt4wp-oauth2-key'] == '' ) {
						
						echo '<p>'.__( "It looks like you need to set things up before you can connect " , "yt-plus-translation-text-domain" ) . get_bloginfo("name" ) . __( " with your YouTube account." , "yt-plus-translation-text-domain" ) . '</p>';
						
						// three buttons here
						?>
						<a class="yt4wp-setup-button" href="<?php echo admin_url() . 'admin.php?page=youtube-for-wordpress-settings'; ?>">
							<span class="dashicons dashicons-admin-tools"></span><?php _e( 'Settings' , 'youtube-for-wordpress' ); ?>
						</a>
						<a class="yt4wp-setup-button" href="http://www.yt4wp.com/documentation/?utm_source=ytwp-upload-page&utm_medium=button&utm_campaign=yt4wp-upload-page" target="_blank">
							<span class="dashicons dashicons-book-alt"></span><?php _e( 'Documentation' , 'youtube-for-wordpress' ); ?>
						</a>
						<a class="yt4wp-setup-button" href="http://www.yt4wp.com/contact/?utm_source=ytwp-upload-page&utm_medium=button&utm_campaign=yt4wp-upload-page&contact-reason=I%20Need%20Support" target="_blank">
							<span class="dashicons dashicons-format-status"></span><?php _e( 'Support' , 'youtube-for-wordpress' ); ?>
						</a>
						<?php						
						echo '<p>&nbsp;</p>';
						echo "<p><em>" . __( "if this is your first time using the plugin, it's recommended you watch our " , "yt-plus-translation-text-domain") . "<a href='http://www.yt4wp.com/support/documentation/setup/setup-google-project/?utm_source=ytwp-upload-page&utm_medium=text-link&utm_campaign=yt4wp-upload-page' target='_blank' title='Setup Help'>" . __( "video walkthrough" , "yt-plus-translation-text-domain" ) . "</a>" . __( " on properly setting everything up." , "yt-plus-translation-text-domain" ) . "</em></p>";
					
					} else {
						
						$state = mt_rand();
						$client->setState($state);
						$_SESSION['state'] = $state;
						$authUrl = $client->createAuthUrl();
						
						echo '<p>' . __( "You need to authorize access using your Google account before you can continue" , "yt-plus-translation-text-domain" ) . '</p>';
						?>
						<!-- Authenticate Button -->
							<a href="<?php echo $authUrl; ?>" style="display:block;width:275px;margin:0 auto;margin-bottom:1em;">
								<input type="submit" class="purchase-add-on-button authenticate-google-account" value="<?php _e( 'Authorize Now' , 'youtube-for-wordpress' ); ?>">
							</a>
						<?php					
					}
					
				echo '</div>'; 
						
				// If the user hasn't authorized the app, initiate the OAuth flow
				/*
				echo '<p style="text-align:right; font-size:11px; font-style:italic;opacity:.8;">' . __("If you keep hitting this error, you may want to try and revoke permissions first. ", "yt-plus-translation-text-domain") . '</p>';
				echo '<p><a onclick="return false;" class="switch-yt-channels" style="font-size:13px;padding:4px 10px;float:right;display:block;position:relative; margin-top:0 !important;">Revoke Permissions</a></p>';
				*/
		}
	}
	
}

// Check to ensure that the access token was successfully acquired.
if ( @$_SESSION['token'] && !$client->isAccessTokenExpired()) {
	// $youtube->channels->list('mine',true);		
	$channelsResponse = $youtube->channels->listChannels('contentDetails', array(
		  'mine' => 'true'
		));
		
	$myPlaylistsResponse = $youtube->playlists->listPlaylists('snippet,status', array(
				'mine' =>  'true',
				// maybe set up pagination here, for users
				// who have more than 50 vids
				'maxResults' => 50
			 ));
			 
		$total_channel_count = count($channelsResponse);
		// print_r($channelsResponse);
	
		// if we're not on the admin side
		// we shouldn't display the Switch Channels button
		if ( $screen_base == 'toplevel_page_youtube-for-wordpress' ) { 
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {	
					jQuery('.switch-yt-channels').click(function(e) {
						// store the url
						var auth_url = jQuery(this).attr('href');
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'logout_and_revoke_access_token',
								access_token: '<?php echo $OAUTH2_CLIENT_ID; ?>'
							},
							dataType: 'json',
							success: function (response) {
								//  direct the user to the auth screen
								window.location.replace(auth_url);								
							},
							error : function(error_response) {
								console.log(error_response.responseText);
							}
						});
					e.preventDefualt();
					});
				});
			</script>	
			<a href="<?php echo $client->createAuthUrl(); ?>" onclick="return false;" class="switch-yt-channels"><div class="dashicons dashicons-migrate"></div> Switch Channels</a>
			
		<?php } ?>
		
		<h3>Content Upload</h3>
		<p>Upload content to your YouTube account. Drop a file into the dropzone below, or use the browse button to select a file.</p>
		<span class="yt-upload-info"><?php /* echo __( 'Maximum upload file size' , 'youtube-for-wordpress' ) . ': ' . esc_html( size_format( wp_max_upload_size() ) ); */ ?></span>
		<hr />
				
		<div id="upload_form_container">
			
			<div id="progress_container" style="display:none;width:200px;height:200px;">
				<li class="working" style="list-style:none;">	
					<input type="text" value="0" data-width="200" data-height="200" style="width: 104px; height: 66px; position: absolute; vertical-align: middle; margin-top: 66px; margin-left: -152px; border: 0px; font-weight: bold; font-style: normal; font-variant: normal; font-size: 40px; line-height: normal; font-family: 'Open Sans',sans-serif; text-align: center; padding: 0px; -webkit-appearance: none; background: none;" /><p></p><span></span>
				</li>
			</div>
			
			<form method="POST" enctype="multipart/form-data"  id="youtube-plus-pro-upload-form">
				  <input type="hidden" id="page" name="page" value="youtube-plus-pro">
				  <div id="video-title-div" style="display:none;">
					 <label for="video-title"><b>Title</b></label> <br />
					 <input type="text" id="video-title" name="video-title" placeholder="Enter Search Term"  <?php if ( isset($_GET['video-title']) ) { ?> value="<?php echo $_GET['video-title']; ?> " <?php } ?> required>
				  </div>
				  <div id="video-description-div" style="display:none;">
					 <label for="video-details"><b>Description</b></label><br />
					 <textarea id="video-details" name="video-details" placeholder="Video Details"  <?php if ( isset($_GET['video-details']) ) { ?> value="<?php echo $_GET['video-details']; ?> " <?php } ?>><?php if ( isset($_GET['video-details']) ) { echo $_GET['video-details']; } ?></textarea>
				  </div>
				  <div>
					 <div id="drop">
						Drop File Here To Begin
						<br />
						<a class="browse-files" onclick="return false;" href="#">Browse</a>
						<div style="font-size:14px; margin-top:2em;">
						 <!-- <label for="video-privacy-settings"><p><b>Privacy Setting</b></p></label> -->
						 <!-- <p class="video-upload-description">the privacy setting will dictate if your video is viewable by the public - default is public</p> -->
							<label style="margin-right:.5em;"><input type="radio" name="video-privacy-settings" value="public" checked>Public</label>
							<label style="margin-right:.5em;"><input type="radio" name="video-privacy-settings" value="private">Private</label>
							<label><input type="radio" name="video-privacy-settings" value="unlisted">Unlisted</label>
						</div>	
						<input type="file" name="videolocation" required><br>
					</div>
				  </div>
				  <br />
				  <a href="#" onclick="return false;" class="button-secondary video-upload-advanced-settings-toggle" style="display:none;">Advanced Settings</a>
				  <div class="advanced-settings" >
					  <div>
						 <label for="video-tags"><p><b>Video Tags</b></p></label>
						 <p class="video-upload-description">assign tags to your video. seperate each tag with a comma.</p>
							 <input type="text" name="video-tags">
					 </div>
					 <div>
						 <label for="video-category"><p><b>Video Category</b></p></label>
						 <p class="video-category">set the category for your video</p>
							<?php $this->generateVideoCategoryDropdown(); ?>
					 </div>
					
					 <?php 
					 /* Display playlist dropdown */
					 if( count($myPlaylistsResponse['items']) > 1 ) {
					 wp_enqueue_script('msdropdown.js',YT4WP_URL.'js/msDropDown.js', array('jquery'));
					 wp_enqueue_style('msdropdown.css',YT4WP_URL.'css/msDropDown.css');
					 ?>
					 <script>
					jQuery(document).ready(function() {
						jQuery('body').find('select[name="video-playlist-setting"]').msDropdown();
					});
					 </script>
					 <div>
						 <label for="video-playlist-setting"><p><b>Select Playlist</b></p></label>
						 <p class="video-upload-description">Specify which playlist you would like to upload this video too.</p>
						 <select name="video-playlist-setting" >
							<option title="<?php echo YT4WP_URL; ?>css/images/no-playlists-found.svg" value="" name="video-playlist-setting">None</option>
							<?php
								foreach ($myPlaylistsResponse['items'] as $myPlaylist) {
									switch( $myPlaylist['status']['privacyStatus'] ) {
										case "public":
											$privacy_icon_class = 'earth';
											break;
											
										case "private": 
											$privacy_icon_class = 'lock';
											break;
											
										case "unlisted":
											$privacy_icon_class = 'unlocked';
											break;
									}
									echo '<option title="' . YT4WP_URL . 'css/images/' . esc_attr( $privacy_icon_class ) . '.svg" value="' . $myPlaylist['id'] . '" name="video-playlist-setting">' . $myPlaylist['snippet']['title'] . '</option>';
								}
							 ?>
						 </select>
					 </div>
					 <?php } else { ?>
					 <div>
						 <label for="video-playlist-setting"><p><b>Select Playlist</b></p></label>
						 <p class="video-upload-description">Specify which playlist you would like to upload this video too.</p>
						 <select name="video-playlist-setting" >
							<option title="<?php echo YT4WP_URL; ?>css/images/no-playlists-found.svg" value="" name="video-playlist-setting">No Playlists Found</option>
						 </select>
					 </div>
					<?php } ?>
				</div>
				<p id="video-submission-button" style="display:none;"><input type="submit" value="Upload" class="button-primary"></p>
			</form>
			
		</div>
		
<?php
  // Check to make sure our video title is set
  if (  isset ($_POST['video-title']) && isset( $_FILES['videolocation']['tmp_name'] ) ) {
		
		global $_FILES;
		
		// load thickbox styles etc.
		add_thickbox();
		
		// submit the upload request
		  try{
		  		  
			// set video path
			$videoPath = $_FILES['videolocation']['tmp_name'];
			// set playlist ID
			$playlist_id = $_POST['video-playlist-setting'];
			// set video category
			$video_category = $_POST['video-category'];
			
			// Create a snippet with title, description, tags and category ID
			// Create an asset resource and set its snippet metadata and type.
			// This example sets the video's title, description, keyword tags, and
			// video category.
			$snippet = new Google_Service_YouTube_VideoSnippet();
			$snippet->setTitle($_POST['video-title']);
			$snippet->setDescription($_POST['video-details']);
			$snippet->setTags( explode( ',' , $_POST['video-tags'] ) );
			
			$playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
			
			// Numeric video category. See
			// https://developers.google.com/youtube/v3/docs/videoCategories/list 
			$snippet->setCategoryId($video_category);

			// Set the video's status.
			// Valid statuses are "public",
			// "private" and "unlisted".
			$status = new Google_Service_YouTube_VideoStatus();
			$status->privacyStatus = $_POST['video-privacy-settings'];
			
			// schedule the post at a given day and time! cool!
			// $status->publishAt = '2020-04-04T00:00:0.0Z';

			// Associate the snippet and status objects with a new video resource.
			$video = new Google_Service_YouTube_Video();
			$video->setSnippet($snippet);
			$video->setStatus($status);
			
			
			$channel = new  Google_Service_YouTube_VideoCategorySnippet();
			
			// Specify the size of each chunk of data, in bytes. Set a higher value for
			// reliable connection as fewer chunks lead to faster uploads. Set a lower
			// value for better recovery on less reliable connections.
			// $chunkSizeBytes = 1 * 1024 * 1024;
			$chunkSizeBytes = 2 * 1024 * 1024;
			
			// Setting the defer flag to true tells the client to return a request which can be called
			// with ->execute(); instead of making the API call immediately.
			$client->setDefer(true);
			
			// Create a request for the API's videos.insert method to create and upload the video.
			$insertRequest = $youtube->videos->insert("status,snippet", $video);

			// Create a MediaFileUpload object for resumable uploads.
			$media = new Google_Http_MediaFileUpload(
				$client,
				$insertRequest,
				'video/*',
				null,
				true,
				$chunkSizeBytes
			);
			$media->setFileSize(filesize($videoPath));


			// Read the media file and upload it chunk by chunk.
			$status = false;
			$handle = fopen($videoPath, "rb");
			while (!$status && !feof($handle)) {
			  $chunk = fread($handle, $chunkSizeBytes);
			  $status = $media->nextChunk($chunk);
			}

			fclose($handle);

			// If you want to make other calls after the file upload, set setDefer back to false
			$client->setDefer(false);

			if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
				$insert_button = '<a class="button button-secondary insert_video_button">insert</a>';
			} else {
				$insert_button = '';
			}
			
			// if the user selected to upload to a playlist
			// else we just skip it
			if ( $playlist_id != '' ) {
				$resourceId = new Google_Service_YouTube_ResourceId();
				$resourceId->setVideoId($status['id']);
				$resourceId->setKind('youtube#video');
				
				$playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
				$playlistItemSnippet->setTitle($_POST['video-title']);
				$playlistItemSnippet->setPlaylistId($playlist_id);
				$playlistItemSnippet->setResourceId($resourceId);
				
				$playlistItem = new Google_Service_YouTube_PlaylistItem();
				$playlistItem->setSnippet($playlistItemSnippet);
				
				$insertToPlaylistRequest = $youtube->playlistItems->insert(
					'snippet,contentDetails', 
					$playlistItem, 
					array()
				);
			}
			
			$htmlBody .= '<h3>Content Successfuly Uploaded</h3><p>Your new content should now be viewable from within the <a style="margin-top:-6px;" class="button-secondary" href="?page=youtube-for-wordpress&amp;tab=youtube_plus_browse">browse</a> tab, but may be unavailable until it completes processing.</p><br /><a href="#" class="upload_another_video button-secondary">Upload Another</a>';
				
				// print_r($status);

			$htmlBody .= '</ul>';
			
			// testing returned data from
			// YouTube API
			/******************************/
			// $htmlBody .= 'The Privacy Setting Is ...... '. $_POST['video-privacy-settings'];
			
		  } catch (Google_ServiceException $e) {
				$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p>An error has occurred: %s. Error #%s</p></span>',
					htmlspecialchars($e->getMessage(),$e->getCode()));	
		  } catch (Google_Exception $e) {	
				$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p>An error has occurred: %s. Error #%s</p></span>',
					htmlspecialchars($e->getMessage(),$e->getCode()));	
		  }
	// if $_FILES error returns 1
	} else if ( isset( $_FILES['videolocation']['error'] ) && $_FILES['videolocation']['error'] == 1 ) {	
		$htmlBody .= '<span id="response_message" class="yt4wp-error-alert">The file was too large, please try uploading a smaller file...</span>';
	}
  $_SESSION['token'] = $client->getAccessToken();
} else {

}

?>

<!doctype html>
<html>
<?php // if were on the top level youtube plus page (not in a modal)
if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
?>

<?php } // end ?>

<body>
<div id="upload_content_container">
  <?php echo $htmlBody; ?>
</div>
</body>
<div id="video_success_response" style="display:none;"></div>
</html>