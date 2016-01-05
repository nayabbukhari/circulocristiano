<?php
//
// Template for reloading a single video, after updating
// via the edit video button
//
/**************************************************************/
					
			$htmlBody = '';
		
			// set up the dialog drawer
			$dialog_drawer = '<section class="dialog_message_drawer"></section>';
			  
			 foreach ($playlistResponse['items'] as $playlist) {
				
				// setup video count (number of videos in the playlist)
				$number_of_videos = $playlist['contentDetails']['itemCount'];
				
				// add a video count to our stat container for playlists
				$stat_container = '<span class="yt-plus-stats-container number_of_videos_in_playlist">'.$number_of_videos.' videos</span>';				
							
				// grab the privacy settings
				$privacy_settings = $playlist['modelData']['status']['privacyStatus'];
				
				if ( $privacy_settings == 'private' ) { // private
					$privacy_setting_icon = '<div class="dashicons dashicons-lock"></div>';
				} else if ( $privacy_settings == 'public' ) { // public
					$privacy_setting_icon = '<div class="dashicons dashicons-admin-site"></div>';
				} else { // unlisted
					$privacy_setting_icon = '<span class="yt-plus-unlisted-icon"><div class="dashicons dashicons-no-alt"></div><div class="dashicons dashicons-admin-site"></div></span>';
				}
				
				// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
				if($playlist['modelData']['snippet']['description']) {	
					// trim the description
					// if there are more than 400 characters
					if(strlen($playlist['modelData']['snippet']['description']) > 325) {
						$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlist['modelData']['snippet']['description'], 0, 400).'...'; 
					} else {
						$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlist['modelData']['snippet']['description']; 
					}
				} else {
					$video_description = ''; 
				}	
				$htmlBody .= sprintf('<input type="hidden" class="playlist_id" value="%s"><a class="youtube-plus-video-preview-btn youtube-plus-view-playlist" href="#" onclick="return false;"><section class="yt-plus-outside-hidden"><img class="youtube-plus-video-thumbnail" src="%s"></a><section class="drawer"> %s </section></section> %s <h3><span class="playlist_title">%s</span></h3> <p class="youtube-plus-video-description">%s</p>',
					  $playlist['id'], $playlist['modelData']['snippet']['thumbnails']['high']['url'], apply_filters( 'yt4wp_playlist_buttons', $this->yt4wp_get_playlist_buttons($screen_base) ) , $stat_container, $playlist['modelData']['snippet']['title'], $playlist['modelData']['snippet']['description'] );
			  }
			  
			  // end single playlist template file