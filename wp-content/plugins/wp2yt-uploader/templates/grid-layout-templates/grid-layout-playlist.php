<?php

// Front end layout
// for our Playlists grid 
// @since v2.0

// enqueue masonry for layouts
wp_enqueue_script( 'masonry' , array('jquery') );

// include the required php files - containers api key
include_once YT4WP_PATH.'lib/google_api_wrapper_api_key.php';

		// include object buffer
		ob_start();

			try {
				// run the YouTube API request
				$playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('status,snippet', array(
					'playlistId' =>  $playlist_id,
					// To Do
					// set up pagination per 50 results
					'maxResults' => 50
				 ));
				 
				  if ( $playlistItemsResponse['items'] ) {
			
				// Run another YouTube API Request to grab the Playlist title
				$playlistData = $youtube->playlists->listPlaylists('snippet', array(
					'id' =>  $playlist_id,
					'maxResults' => 1
				 ));

				// display our playlist title here
				echo '<h4>Showing results for "' . stripslashes( $playlistData['modelData']['items'][0]['snippet']['title'] ) . '" Playlist</h4>';
			}
			
				echo '<ul id="masonry-container">';
					
					
					
						foreach ( $playlistItemsResponse['items'] as $playlistItem ) {
							
							// grab the privacy settings
							$privacy_setting = $playlistItem['modelData']['status']['privacyStatus'];
							
							// setup the description
							if($playlistItem['modelData']['snippet']['description']) {
								// trim the description
								// if there are more than 400 characters
								if(strlen($playlistItem['modelData']['snippet']['description']) > 325) {
									$video_description = '<b class="yt4wp-video-description" style="text-decoration:underline;">Description</b> <br />' . substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
								} else {
									$video_description = '<b class="yt4wp-video-description" style="text-decoration:underline;">Description</b> <br />' . $playlistItem['modelData']['snippet']['description']; 
								}
							} else {
								$video_description = ''; 
							}
						
							if ( $privacy_setting == 'public' && $playlistItem['modelData']['snippet']['title'] != 'Deleted video' ) {
								?>
								<li class="yt4wp-video-single-list-item">
									<input type="hidden" class="video_id" value="<?php echo $playlistItem['snippet']['resourceId']['videoId']; ?>">
										<a class="fancybox" data-type="iframe" href="http://www.youtube.com/embed/<?php echo $playlistItem['snippet']['resourceId']['videoId']; ?>?autoplay=1" title="<?php echo $playlistItem['snippet']['title']; ?>">	
										<img class="yt4wp-video-thumbnail" src="<?php echo $playlistItem['snippet']['thumbnails']['high']['url'];?>"></a>
										<h3 class="yt4wp-video-title-container"><?php echo $playlistItem['snippet']['title']; ?></h3> 
									<span class="yt4wp-video-description-container"><?php echo $video_description; ?></span>
									<p>&nbsp;</p> <!-- spacer -->
								</li>	
								<?php
							}
						
					}
				echo '</ul>';
				
				
				
				?>
				<!-- 
				Initialize the masonry script 
					- run it after images have fully loaded to prevent layout issues
				-->
				<script type="text/javascript">
					 jQuery(document).ready(function() {		
							/* Masonry the Results */
							jQuery('#masonry-container').masonry().imagesLoaded( function() {
								jQuery('#masonry-container').masonry({
								  itemSelector: '.yt4wp-video-single-list-item',
								  gutter: 35,
								 // options...
								  isAnimated: true,
								  animationOptions: {
									duration: 350,
									easing: 'linear',
									queue: false
								  }
							});
						});

													
						jQuery(window).resize(function() { jQuery('#masonry-container').masonry('reloadItems'); });
					
					
						<!-- initialize fancybox  -->
						jQuery(".fancybox").on("click", function(){
							jQuery.fancybox({
								href: this.href,
								type: jQuery(this).data("type")
							}); // fancybox
							return false;  
						}); // on
					

					
					});
				</script>
		
		<?php	
		
			} catch (Exception $e) {
												
				echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> Something went wrong while retreiving the playlist. Double check the playlist id is correct.</span>';
					// display an error for the admin only
					if( current_user_can( 'manage_options' ) ) {
						echo '<p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #'.$e->getCode().'</p>';
					}
				/* Write the error to our error log */
				$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						 
			 }
			 
	// return the contents of our search grid
	return ob_get_clean(); 
?>