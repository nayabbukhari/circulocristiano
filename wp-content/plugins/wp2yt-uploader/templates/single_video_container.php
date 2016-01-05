<?php
//
// Template for reloading a single video, after updating
// via the edit video button
//
/**************************************************************/

			$htmlBody = '';
		
			// set up the dialog drawer
			$dialog_drawer = '<section class="dialog_message_drawer"></section>';
			  
			foreach ($videoResponse['items'] as $playlistItem) {	
												
				// check if the setting is set, so we don't run unecessary API requests
				if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
			
					// loop to get video statistics...
						$video_statistics = $youtube->videos->listVideos('statistics', array(
							'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
						 ));
						
						// print_r($video_statistics);
						// build the video stats array
						foreach( $video_statistics as $stat ) {
							// $comment_count = $stat['statistics']['commentCount']; // not used but could be in future releases...
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
				
				// grab the privacy settings
				$privacy_settings = $playlistItem['modelData']['status']['privacyStatus'];
				
				if ( $privacy_settings == 'private' ) { // private
					$privacy_setting_icon = '<div class="dashicons dashicons-lock"></div>';
				} else if ( $privacy_settings == 'public' ) { // public
					$privacy_setting_icon = '<div class="dashicons dashicons-admin-site"></div>';
				} else { // unlisted
					$privacy_setting_icon = '<span class="yt-plus-unlisted-icon"><div class="dashicons dashicons-no-alt"></div><div class="dashicons dashicons-admin-site"></div></span>';
				}
				
				// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
				if($playlistItem['modelData']['snippet']['description']) {	
					// trim the description
					// if there are more than 400 characters
					if(strlen($playlistItem['modelData']['snippet']['description']) > 325) {
						$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
					} else {
						$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
					}
				} else {
					$video_description = ''; 
				}	
				$htmlBody .= sprintf('<input type="hidden" class="full_video_description" value="%s"><input type="hidden" class="video_privacy_status" value="%s"><input type="hidden" class="video_id" value="%s"><a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/%s?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank"><section class="yt-plus-outside-hidden">%s<img class="youtube-plus-video-thumbnail" src="%s"></a><section class="drawer">%s</section></section> %s <h3><span class="youtube-plus-video-title-container">%s</span> </h3> <span class="youtube-plus-video-description-container"> %s </span>',
					$playlistItem['modelData']['snippet']['description'],$playlistItem['modelData']['status']['privacyStatus'], $playlistItem['id'], $playlistItem['id'], $dialog_drawer, $playlistItem['modelData']['snippet']['thumbnails']['high']['url'], apply_filters( 'youtube_plus_browse_buttons', $this->yt4wp_get_browse_buttons($screen_base) ) , $stat_container, $playlistItem['modelData']['snippet']['title'], $video_description );
			  }
			  
			  // end single video template file