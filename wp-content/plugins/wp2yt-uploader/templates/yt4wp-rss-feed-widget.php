<?php
//
//	RSS Widget Template
//  RSS feed widget template
//

// Creating the widget 

class yt_plus_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'yt_plus_widget', 

			// Widget name will appear in UI
			__('YouTube for WordPress : RSS Feed', 'youtube-for-wordpress'), 

			// Widget description
			array( 'description' => __( 'Display latest content from a YouTube user or channel.', 'youtube-for-wordpress' ), ) 
		);
	}

	
	/** Custom Widget Class Functions **/

	// build our authenticated users Widget dropdown
	// to make it very easy for the user to select an RSS feed
	// from a subscription or one of their own playlists
	function buildWidgetRSSDropDown($instance) {
	
		// set our default instance arguments
		$instance = wp_parse_args( (array) $instance, array( 'select_user' => '1234~~Recent Uploads~~user_channel' , 'randomize_feed' => 0 ) );
	
	
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var user_subscription_feed_dropdown = jQuery('#user_subscription_feeds');
				jQuery('.widget-inside').undelegate( ".feed_selector" , 'change' ).delegate( ".feed_selector" , 'change' , function() {
				  var selected_feed = jQuery(this.options[this.selectedIndex]).closest('optgroup').prop('class');
				  if ( selected_feed == 'user_playlist' || selected_feed == 'user_channels' ) {
					jQuery(this).parents('.widget-content').find('#user_subscription_feeds').fadeOut('fast');
				  } else if ( selected_feed == 'user_subscription' ) {
					// get the feed to display text
					jQuery('.widget-content').find('#user_subscription_feeds').find('select').find('option').each(function() {
						var dropdown_text = jQuery('.widget-content').find('#user_selection').find('select option:selected').text().replace( 'Your Uploads' , '' );
						var the_dropdown_text = jQuery(this).val();
						jQuery(this).html(dropdown_text+' '+'<span style="text-transform:capitalize;">'+the_dropdown_text+'</span>');
					});
					
					jQuery(this).parents('.widget-content').find('#user_subscription_feeds').fadeIn('fast');
				  }
				});
			});
		</script>
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

	// check if yt plus session is started
	
	/*
	 * You can acquire an OAuth 2.0 client ID and client secret from the
	 * Google Developers Console <https://console.developers.google.com/>
	 * For more information about using OAuth 2.0 to access Google APIs, please see:
	 * <https://developers.google.com/youtube/v3/guides/authentication>
	 * Please ensure that you have enabled the YouTube Data API for your project.
	 */
	$YT4WPBase	= new YT4WPBase();
	$OAUTH2_CLIENT_ID = $YT4WPBase->optionVal['yt4wp-oauth2-key'];
	$OAUTH2_CLIENT_SECRET = $YT4WPBase->optionVal['yt4wp-oauth2-secret'];

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
		
			$client->refreshToken( get_option( 'yt4wp_user_refresh_token' ) );
			$_SESSION['token'] = $client->getAccessToken();
			
		} else {
		
		// this runs only the first time the user ever installs the plugin
		 echo '<div class="error"><p>'.__("Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</p></div>'; 
		 
		// If the user hasn't authorized the app, initiate the OAuth flow
	  $state = mt_rand();
	  $client->setState($state);
	  $_SESSION['state'] = $state;
		$authUrl = $client->createAuthUrl();
  $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
	
	}
	
}

			// Check to ensure that the access token was successfully acquired.
			if ( $_SESSION['token'] != '' ) {
				
				 try {
					// Call the channels.list method to retrieve information about the
					// currently authenticated user's channel.
					$subscriptionsResponse = $youtube->subscriptions->listSubscriptions('snippet', array(
					  'mine' => 'true',
					  'maxResults' => 50
					));

					// list my playlists here as well
					$userPlaylists = $youtube->playlists->listPlaylists('snippet', array(
						'mine' =>  'true',
						// maybe set up pagination here, for users
						// who have more than 50 vids
						'maxResults' => 50
					 ));
					 
					 // list my channels here as well
						// ( includes favorites, watch history, likes, recent uploads etc. )
					
					 $userChannels = $youtube->channels->listChannels('contentDetails', array(
						  'mine' => 'true'
						));
					 						
					// Testing returned data from YouTube API
					// subscription list...
						// to do---
						// get all video for given subscription
					// print_r($subscriptionsResponse);
					
					// define our htmlBody variable
					$htmlBody = '';
					
					$htmlBody .= '<select style="width:100%;" id="'.$this->get_field_id( 'select_user' ).'" class="feed_selector" name="'.$this->get_field_name( 'select_user' ).'">';
					
						// list user channels
						 // likes, favorites, uploads etc.
						 // set up the user channel dropdown
						if ( $userChannels ) {	
								
							$htmlBody .= '<optgroup label="Your Channels" class="user_channels">';
		
								foreach( $userChannels['items'] as $channel ) {
									
									// Extract the unique playlist ID that identifies the list of videos
									  // uploaded to the channel, and then call the playlistItems.list method
									  // to retrieve that list.
									if ( isset( $channel['contentDetails']['relatedPlaylists']['uploads'] ) && $instance["select_user"] == $channel['contentDetails']['relatedPlaylists']['uploads'].'~~Recent Uploads~~user_channel' ) { 
										$uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];
											$htmlBody .= '<option value="'.$uploadsListId.'~~Recent Uploads~~user_channel" selected="selected">Your Uploads</option>';
									} else {
										$uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];
											$htmlBody .= '<option value="'.$uploadsListId.'~~Recent Uploads~~user_channel">Your Uploads</option>';
									}
									
									if ( isset( $channel['contentDetails']['relatedPlaylists']['likes'] ) && $instance["select_user"] == $channel['contentDetails']['relatedPlaylists']['likes'].'~~Likes~~user_channel' ) {	
										$likesListId = $channel['contentDetails']['relatedPlaylists']['likes'];
											$htmlBody .= '<option value="'.$likesListId.'~~Likes~~user_channel" selected="selected">Your Likes</option>';
									} else {
										$likesListId = $channel['contentDetails']['relatedPlaylists']['likes'];
											$htmlBody .= '<option value="'.$likesListId.'~~Likes~~user_channel" >Your Likes</option>';
									}	
									
									if ( isset( $channel['contentDetails']['relatedPlaylists']['favorites'] ) && $instance["select_user"] == $channel['contentDetails']['relatedPlaylists']['favorites'].'~~Favorites~~user_channel' ) {	
										$favoritesListId = $channel['contentDetails']['relatedPlaylists']['favorites'];
											$htmlBody .= '<option value="'.$favoritesListId.'~~Favorites~~user_channel" selected="selected">Your Favorites</option>';
									} else {
										$favoritesListId = $channel['contentDetails']['relatedPlaylists']['favorites'];
											$htmlBody .= '<option value="'.$favoritesListId.'~~Favorites~~user_channel">Your Favorites</option>';
									}	

								}
							
							$htmlBody .= '</optgroup>';
							
						}
						// end the user channels dropdown
						
						// print_r($userPlaylists);

						// set up the user playlists dropdown
						$htmlBody .= '<optgroup label="Your Playlists" class="user_playlist">';
						
							// list user playlists, if any are found
							if ( $userPlaylists['modelData']['pageInfo']['totalResults'] > 0 ) {	

								// print_r($userPlaylists);

								foreach( $userPlaylists as $playlist ) {		
									if ( $playlist['modelData']['snippet']['title'] != 'Favorites' ) {
										if (  isset( $instance["select_user"] ) && $instance["select_user"] == $playlist['id'].'~~'.$playlist['modelData']['snippet']['title'].'~~user_playlist' ) {
											$htmlBody .= '<option value="'.$playlist['id'].'~~'.$playlist['modelData']['snippet']['title'].'~~user_playlist" selected="selected">'.ucwords($playlist['modelData']['snippet']['title']).'</option>';
										} else {
											$htmlBody .= '<option value="'.$playlist['id'].'~~'.$playlist['modelData']['snippet']['title'].'~~user_playlist">'.ucwords($playlist['modelData']['snippet']['title']).'</option>';
										}
									}
								}
								
							} else {
							
								$htmlBody .= '<option value="no_playlists_found" disabled="disabled">No Playlists Found</option>';
							
							}
							
						$htmlBody .= '</optgroup>';
						// end user playlists dropdown	
						
						// set up the subscriptions dropdown
						$htmlBody .= '<optgroup label="Your Subscriptions" class="user_subscription">';
								
								// list users subscriptions
								if ( $subscriptionsResponse['modelData']['pageInfo']['totalResults'] > 0 ) {	
									
									foreach( $subscriptionsResponse as $subscription ) {
										if ( isset( $instance["select_user"] ) && $instance["select_user"] == $subscription["modelData"]['snippet']["resourceId"]["channelId"].'~~'.$subscription['modelData']['snippet']['title'] ) {
											$htmlBody .= '<option value="'.$subscription["modelData"]['snippet']["resourceId"]["channelId"].'~~'.$subscription['modelData']['snippet']['title'].'" selected="selected">'.ucwords($subscription['modelData']['snippet']['title']).'</option>';
										} else {
											$htmlBody .= '<option value="'.$subscription["modelData"]['snippet']["resourceId"]["channelId"].'~~'.$subscription['modelData']['snippet']['title'].'">'.ucwords($subscription['modelData']['snippet']['title']).'</option>';
										}
									}									
								
								} else {
								
									$htmlBody .= '<option value="no_subscritions" disabled="disabled">No Subscriptions Found</option>';
					
								}
						
						$htmlBody .= '</optgroup>';
						// end subscriptions dropdown					
					
					$htmlBody .= '</select>';
					// end dropdown all together 
					
				} catch (Google_ServiceException $e) {
					  $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
					  htmlspecialchars($e->getMessage()));
				} catch (Google_Exception $e) {
					$htmlBody .= sprintf('<p>A client error occurred: <code>%s</code></p>',
					htmlspecialchars($e->getMessage()));
				}

				echo $htmlBody;
					  
			}
	}
	
	
		// Creating widget front-end
		// This is where the action happens
		public function widget( $args, $instance ) {
			
			// print_r($instance);
			
			echo $args['before_widget'];
			
			// Get RSS Feed(s)
			include_once( ABSPATH . WPINC . '/class-simplepie.php' );
						
			// Let's simplify this with the shorter syntax. http://simplepie.org/wiki/reference/simplepie/start
			
			// need to dynamically update this
			if ( $instance["selected_feed"] && $instance["select_user"] ) {
								
				$feed_type = $instance["selected_feed"];
				
				$select_user_explosion = explode( '~~' , $instance["select_user"] );
				
				if ( count( $select_user_explosion ) == 3 ) {
					
					$selected_user = $select_user_explosion[0];
					$user_channel = $select_user_explosion[1];
					$user_playlist = 'user_playlist';
					$feed_url = 'http://gdata.youtube.com/feeds/api/playlists/'.$selected_user.'/';
					
					if ( isset( $instance['rss_feed_title'] ) && $instance['rss_feed_title'] != '' ) {
						$dynamic_widget_title = apply_filters( 'widget_title', ucfirst($instance['rss_feed_title']) ); 
					} else {
						if ( $select_user_explosion[2] == 'user_channel' ) {
							$dynamic_widget_title = apply_filters( 'widget_title', 'My ' . ucfirst($user_channel) );
						} else {
							$dynamic_widget_title = apply_filters( 'widget_title', 'My ' . ucfirst($user_channel) );
						}
					}
					
					
				} else {
					$selected_user = $select_user_explosion[0];
					$user_channel = $select_user_explosion[1];
					$feed_url = 'http://gdata.youtube.com/feeds/api/users/'.$selected_user.'/'.$feed_type.'/';
				
					if ( isset( $instance['rss_feed_title'] ) && $instance['rss_feed_title'] != '' ) {
						$dynamic_widget_title = apply_filters( 'widget_title', ucfirst($instance['rss_feed_title']) );
					} else {
						$dynamic_widget_title = apply_filters( 'widget_title', ucfirst($feed_type) . __( ' from ', 'my-text-domain' ) ) . $user_channel;
					}
					
				}

				
			}
			
			// echo 'the queried feed url is : '.$feed_url;			
			
			// need to update the title field with a drop down of possible username
				// channels
				// playlists
				// etc...
			$feed = new SimplePie();
			
			// set the URL where to pull our feed from
			// $feed->set_feed_url($instance['title']);
			$feed->set_feed_url($feed_url);
			
			// set the feed cache duration (in seconds)
			$feed->set_cache_duration(30);
			
			// set our cache location to the temp_cache folder in /lib
			$feed->set_cache_location( YT4WP_PATH . '/lib/temp_cache' ); 
			 
			// initialize the feed 
			$feed->init(); 
			 
			// Make sure the content is being served out to the browser properly.
			$feed->handle_content_type();
						 
			if ( $feed->get_items() ) {
				?>
				<!-- initialize fancybox  -->
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery(".fancybox").on("click", function(){
							jQuery.fancybox({
							  href: this.href,
							  type: jQuery(this).data("type")
							}); // fancybox
							return false;  
						}); // on
					}); // ready
				</script>
				<div class="yt4wp-rss-feed widget masonry-brick">
					<h3 class="widget-title"><?php echo $dynamic_widget_title; ?></h3>
				<?php
				// set the limit to the widget option
				// 1-50 (min/max)
					// randomize our array here -- !
				// if randomize is set to 'Yes'
				if ( $instance['randomize_feed'] == 1 ) {
					$feed_array = $feed->get_items( 0 , $instance["feed_limit"]);
					shuffle($feed_array);
				} else { // else , set to 'No' randomize
					$feed_array = $feed->get_items( 0 , $instance["feed_limit"] );
				}
				foreach ($feed_array as $item)
					{
						// As long as an enclosure exists...
						if ($enclosure = $item->get_enclosure())
						{
							?>
							<a class="fancybox" data-type="iframe" href="<?php echo str_replace( '&amp;feature=youtube_gdata' , '' , str_replace( 'http://www.youtube.com/watch?v=' , 'http://www.youtube.com/embed/' , $item->get_permalink() ) ).'?autoplay=1'; ?>" title="<?php echo $item->get_title(); ?>">
									<img src=<?php echo $enclosure->get_thumbnail(); ?> alt="<?php echo $item->get_title(); ?>" style="max-width:100%;" />
									<h3 style="margin-top:0px;"><?php echo $item->get_title(); ?></h3>
								</a>
							<?php
						}
						
					}
				
			?></div><?php	
				// if no content is found when querying the RSS feed
			} else {
				?>
					<h3 class="widget-title">No content found in <?php echo $dynamic_widget_title; ?>.</h3>
				<?php
			}
			
			echo $args['after_widget'];
				
		}
			
		// Widget Backend 
		 function form($instance) {    			
		 				
				$instance = wp_parse_args( (array) $instance, array( 'select_user' => '1234~~Recent Uploads~~user_channel' , 'randomize_feed' => 0 ) );
					
				// check and store values for each field
					//title
					if ( isset( $instance[ 'title' ] ) ) {
						$title = $instance[ 'title' ];
					} else {
						$title = __( 'Sign Up For Our Newsletter', 'youtube-for-wordpress' );
					}
					
					// submit button text
					if ( isset( $instance[ 'submit_button_text' ] ) ) {
						$submit_button_text = $instance[ 'submit_button_text' ];
					} else {
						$submit_button_text = __( 'Sign Me Up', 'youtube-for-wordpress' );
					}
			 ?>
				<p>
					Fill in the following information below to display your or another users feed:
				</p>
				
				<?php if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) { ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'rss_feed_title' ); ?>"><?php _e('Widget Title :','youtube-for-wordpress'); ?> <br />
							<input class="widefat" id="<?php echo $this->get_field_id( 'rss_feed_title' ); ?>" name="<?php echo $this->get_field_name( 'rss_feed_title' ); ?>"  value="<?php if( !isset($instance["rss_feed_title"]) || $instance["rss_feed_title"] == '' ) { echo ''; } elseif ( $instance["rss_feed_title"] > 50 ) { echo '50'; } else { echo $instance["rss_feed_title"]; } ?>" name="rss_feed_title" >
					</label>
				</p>
				
				<p id="user_selection">
					<label for="<?php echo $this->get_field_id( 'select_user' ); ?>"><?php _e('Select Feed :','youtube-for-wordpress'); ?> <br />
						<?php $this->buildWidgetRSSDropDown($instance); ?>
					</label>
				</p>
				
				<p>
					<label for="<?php echo $this->get_field_id( 'feed_limit' ); ?>"><?php _e('Feed Limit :','youtube-for-wordpress'); ?> <br />
						<input class="widefat" id="<?php echo $this->get_field_id( 'feed_limit' ); ?>" name="<?php echo $this->get_field_name( 'feed_limit' ); ?>"  value="<?php if( !isset($instance["feed_limit"]) || $instance["feed_limit"] == '' ) { echo '5'; } elseif ( $instance["feed_limit"] > 50 ) { echo '50'; } else { echo $instance["feed_limit"]; } ?>" type="number" min="1" max="50">
					</label>
				</p>
				
				<!-- toggle the feed that gets displayed on the front end of the site -->				
					<p id="user_subscription_feeds" <?php if ( isset( $instance["select_user"] ) && count( explode( '~~' , $instance["select_user"] ) ) == 3 ) { echo 'style="display:none;"'; } ?>>
						<?php
							$explode_selected_user = explode( '~~' , $instance["select_user"] );
							$selected_user = $explode_selected_user[1];
						?>
						<label for="<?php echo $this->get_field_id( 'selected_feed' ); ?>"><?php _e('Feed To Display :','youtube-for-wordpress'); ?> <br />
							<select id="<?php echo $this->get_field_id('selected_feed'); ?>" name="<?php echo $this->get_field_name('selected_feed'); ?>" type="text" style="width:100%;">
								<!-- construct selectable options based on imported lists -->
								<option value="uploads" <?php if ( isset( $instance["selected_feed"] ) ) { selected($instance["selected_feed"], 'uploads' ); } ?>><?php echo $selected_user; ?> Uploads</option>
								<option value="favorites" <?php if ( isset( $instance["selected_feed"] ) ) { selected($instance["selected_feed"], 'favorites' ); } ?>><?php echo $selected_user; ?> Favorites</option>
							</select>
						</label>
					</p>
				
				<!-- toggle the this setting to randomize the feed -->
				<p id="randomize_rss_feed">
					<label for="<?php echo $this->get_field_id( 'randomize_feed' ); ?>"><?php _e('Randomize Feed? :','youtube-for-wordpress'); ?> <br />
						<select id="<?php echo $this->get_field_id('randomize_feed'); ?>" name="<?php echo $this->get_field_name('randomize_feed'); ?>" type="text" style="width:100%;">
							<!-- construct selectable options based on imported lists -->
							<option value="1" <?php if ( isset( $instance["randomize_feed"] ) ) { selected($instance["randomize_feed"], '1' ); } ?>>Yes</option>
							<option value="0" <?php if ( isset( $instance["randomize_feed"] ) ) { selected($instance["randomize_feed"], '0' ); } ?>>No</option>
						</select>
					</label>
				</p>
			<?php } else {
				echo '<div class="yt-plus-error"><h4 style="margin:1em 0 -.5em 0;"><div class="dashicons dashicons-dismiss" style="line-height:.8;margin-right:.25em;"></div>Error</h4><p>You must completely fill out the <a href="' . admin_url('/admin.php?page=youtube-for-wordpress') .'">settings</a>, and authorize the application before using this widget.</p></div>';
			} 
		 }

		// Updating widget replacing old instances with new
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['rss_feed_title'] = $new_instance['rss_feed_title'];
			$instance['select_user'] = $new_instance['select_user'];
			$instance['selected_feed'] = strip_tags( $new_instance['selected_feed'] );
			$instance['feed_limit'] = strip_tags( $new_instance['feed_limit'] );
			$instance['randomize_feed'] = strip_tags( $new_instance['randomize_feed'] );
			return $instance;
		}
	
} // Class yt4wp_MC_widget ends here

// Register and load the widget
function youtube_plus_load_widget() {
	register_widget( 'yt_plus_widget' );
}
add_action( 'widgets_init', 'youtube_plus_load_widget' );