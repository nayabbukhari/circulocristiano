jQuery(document).ready(function() {
		
		/* Insert Click */
		jQuery('#search_results').undelegate( '.insert_video_button' , 'click' ).delegate('.insert_video_button' , 'click' , function() {
			var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
			// close the thickbox
			tb_remove();
			// insert the video into the active editor
			window.send_to_editor('[youtube-plus-video video_id="'+video_id+'"]');
		});
		
		/* Insert Playlist Click */
		jQuery('#search_results').undelegate( '.insert_playlist_button' , 'click' ).delegate('.insert_playlist_button' , 'click' , function() {
			var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
			// close the thickbox
			tb_remove();
			// insert the video into the active editor
			window.send_to_editor('[youtube-plus-playlist playlist_id="'+playlist_id+'"]');
		});

		jQuery('#search_results').delegate( '.sub-nav-button ' , 'click' , function() {
			if ( jQuery(this).attr('disabled') ) {
				return;
			} else {
				jQuery('.sub-nav-button').removeClass('sub-nav-button-active');
				jQuery(this).addClass('sub-nav-button-active');
			}	
		})
	
		/**********************************************************************************************/
		/** Animate drawr on thumbnail hover **/
		jQuery('#search_results').undelegate( '.youtube-plus-video-thumbnail' , 'mouseenter' ).delegate( '.youtube-plus-video-thumbnail' , 'mouseenter' , function() {
			jQuery(this).parents('.yt-plus-outside-hidden').find('.drawer').addClass('move');
		});
		jQuery('#search_results').undelegate( '.youtube-plus-video-thumbnail' , 'mouseleave' ).delegate( '.youtube-plus-video-thumbnail' , 'mouseleave' , function() {
			jQuery(this).parents('.yt-plus-outside-hidden').find('.drawer').removeClass('move');
		});
			// add+remove Class from the drawer when a user
			// hovers on it
			jQuery('#search_results').undelegate( '.drawer' , 'mouseenter' ).delegate( '.drawer' , 'mouseenter' , function() {
				jQuery(this).addClass('move');
				jQuery(this).parents('.yt-plus-outside-hidden').find('.youtube-plus-video-thumbnail').stop().fadeTo('fast',.75);
			});
			jQuery('#search_results').undelegate( '.drawer' , 'mouseleave' ).delegate( '.drawer' , 'mouseleave' , function() {
				jQuery(this).removeClass('move');
				jQuery(this).parents('.yt-plus-outside-hidden').find('.youtube-plus-video-thumbnail').stop().fadeTo('fast',1);
			});
		
		
		/* Search Submit Function */
		jQuery('#youtube_plus_search_form').submit(function() {	
			var ajaxurl = localized_data.admin_ajax_url;
			var preloader_url = localized_data.preloader_url;
			jQuery('#search_results').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
			var search_term = jQuery('#youtube_plus_search_form').find('#yt-plus-upload-search').val();
			var search_type = jQuery('#search_type_dropdown').val();
			var sort_results_by = jQuery('#order_results_by_dropdown').val();
			var advanced_search_checkbox = jQuery('#advanced_search').prop( "checked" );
			var upload_date_timeframe = jQuery('#upload_date_timeframe').val();
			var max_results = jQuery('#maxResults').val();

				var screen_base = localized_data.screen_base;
				jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'youtube_plus_search_youtube',
								search_term: search_term,
								max_results: max_results,
								search_type: search_type,
								sort_results_by: sort_results_by,
								advanced_search_checkbox: advanced_search_checkbox,
								upload_date_timeframe: upload_date_timeframe,
								screen_base: screen_base
							},
							dataType: 'html',
							success: function(response) {
								jQuery('#search_results').html(response);
							}
						});			
				return false;
		});
		
		/* Add to Watch Later List Function */
		jQuery('#search_results').undelegate( '.add_to_watch_later' , 'click' ).delegate( '.add_to_watch_later' , 'click' , function() {
			var ajaxurl = localized_data.admin_ajax_url;
			var preloader_url = localized_data.preloader_url;
			var watch_later_list_id = jQuery('#youtube_plus_search_form').find('#watchLaterListID').val();
			var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
			var selected_video = jQuery(this).parents('.youtube-plus-video-single-list-item');
			// jQuery('#search_results').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
				jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'youtube_plus_add_video_to_watch_later',
								watch_later_list_id: watch_later_list_id,
								video_id: video_id
							},
							dataType: 'html',
							success: function(response) {
								if ( response.indexOf("Error") > -1 ) {
									var response_html = '<span class="add_video_to_watch_later_error"><div class="dashicons dashicons-no-alt" style="line-height:1.3"></div>'+response+'</span>';
								} else {	
									var response_html = '<span class="add_video_to_watch_later_success"><div class="dashicons dashicons-yes" style="line-height:1.3"></div>'+response+'</span>';
								}
								// slide in our drawer
								selected_video.find( '.dialog_message_drawer' ).html(response_html).addClass('move');	
									// hide the drawer
									setTimeout(function() {
										selected_video.find( '.dialog_message_drawer' ).removeClass('move');
									},4500);
									// log the response
									console.log(response);
							},
							error: function(error_response) {
								console.log(error_response);
							}
						});			
				return false;
		});
		
		/* Insert Click */
		jQuery('#search_results').undelegate('.insert_video_button' , 'click').delegate('.insert_video_button' , 'click' , function() {
			var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
			// close the thickbox
			tb_remove();
			// insert the video into the active editor
			window.send_to_editor('[youtube-plus-video video_id="'+video_id+'"]');
		});
		
		/* Pagination Click */
		jQuery('#search_results').undelegate( '.pagination_page_search' , 'click' ).delegate( '.pagination_page_search' , 'click' , function() {
			var ajaxurl = localized_data.admin_ajax_url;
			var page_token = jQuery(this).attr('alt');
			var preloader_url = localized_data.preloader_url;
			jQuery('#search_results').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
			var search_term = jQuery('#youtube_plus_search_form').find('#yt-plus-upload-search').val();
			var search_type = jQuery('#search_type_dropdown').val();
			var sort_results_by = jQuery('#order_results_by_dropdown').val();
			var max_results = jQuery('#maxResults').val();
			var screen_base = localized_data.screen_base;
			var advanced_search_checkbox = jQuery('#advanced_search').prop( "checked" );
			var upload_date_timeframe = jQuery('#upload_date_timeframe').val();
			
				jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'youtube_plus_paginate_search_youtube',
								page_token: page_token,
								search_term: search_term,
								max_results: max_results,
								search_type: search_type,
								sort_results_by: sort_results_by,
								advanced_search_checkbox: advanced_search_checkbox,
								upload_date_timeframe: upload_date_timeframe,
								screen_base: screen_base
							},
							dataType: 'html',
							success: function(response) {
								jQuery('#search_results').html(response);
							}
						});
			return false;
		});
		 
		/*******************************************************************************/
			/**
			*	Channel search AJAX Function
			*   when user clicks on a channel thumbnail
			**/
			jQuery('#search_results').undelegate( '.view-subscription-videos-btn' , 'click' ).delegate( '.view-subscription-videos-btn' , 'click' , function() {
							
					var channel_id = jQuery(this).attr('alt');
					var clicked_subscription = jQuery(this).attr('alt');
					var user_name = jQuery(this).attr('title');
					
					var screen_base = localized_data.screen_base;
					var preloader_url = localized_data.preloader_url;
					
					jQuery('#search_results').find('ul').not(':first-child').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('#search_results').find('h3').remove();
					jQuery('.pagination_page_search').remove();
					jQuery('.pagination_page_search_disabled ').remove();
					
					/* Ajax Request To Get New Playlist */
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'get_channel_playlists',
								channel_id: channel_id,
								clicked_subscription: clicked_subscription,
								user_name: user_name,
								screen_base: screen_base
							},
							dataType: 'html',
							success: function (response) {
								jQuery('#search_results').html(response);
							},
							error : function(error_response) {
								jQuery('#search_results').html('<strong>Error : '+error_response+'</strong>');
								console.log(error_response);
							}
						});
					return false;	
			});
			
			/*******************************************************************************/
			/**
			*	Subscribe/Unsubscribe Function
			**/
			jQuery('#search_results').undelegate( '.yt-subscribe-btn' , 'click' ).delegate( '.yt-subscribe-btn' , 'click' , function() {		
									
				var ajaxurl = localized_data.admin_ajax_url;
				var channel_id = jQuery('#channel_id').val();
				var what_to_do = jQuery(this).attr('title');
				var button = jQuery(this);
						
				// jQuery('#search_results').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					if ( what_to_do == 'subscribe' ) {
						jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yt_plus_settings_form',
										form_action: 'youtube_plus_add_channel_subscription',
										channel_id: channel_id
									},
									dataType: 'html',
									success: function(response) {
										button.before('<div type="submit" class="yt-subscribe-btn" title="unsubscribe" alt="'+response+'"><div class="dashicons dashicons-no"></div> Un-Subscribe</div>');
										button.remove();
									},
									error: function(error_response) {
										console.log(error_response);
									}
								});	
					} else { // unsubscribe ajax
					var channel_id = jQuery(this).attr('alt');
						jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yt_plus_settings_form',
										form_action: 'youtube_plus_remove_channel_subscription',
										channel_id: channel_id
									},
									dataType: 'html',
									success: function(response) {
										button.before('<div type="submit" class="yt-subscribe-btn" title="subscribe"><div class="dashicons dashicons-yes"></div> Subscribe</div>');
										button.remove();
									},
									error: function(error_response) {
										console.log(error_response);
									}
								});	
					}
				return false;
		});
			
			/*******************************************************************************/
			/**
			*	View a Channels Playlist
			**/
			jQuery('#search_results').undelegate( '.view-subscription-playlists-btn' , 'click' ).delegate( '.view-subscription-playlists-btn' , 'click' , function() {
						
					// alert('view subscriptions playlists button clicked');			
								
					var channel_id = jQuery(this).attr('alt');			
					var clicked_subscription = jQuery(this).attr('title');
					
					var screen_base = localized_data.screen_base;
					var preloader_url = localized_data.preloader_url;
					
					console.log(channel_id);
					console.log(clicked_subscription);
					
					jQuery('#subscription_content_div').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('#browse_page_title').find('h3').text('');
					
					/* Ajax Request To Get New Playlist */
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'get_subscription_playlists',
								channel_id: channel_id,
								clicked_subscription: clicked_subscription,
								screen_base: screen_base
							},
							dataType: 'html',
							success: function (response) {
								jQuery('#subscription_content_div').html(response);
							},
							error : function(error_response) {
								jQuery('#subscription_content_div').html('<strong>Error : '+error_response+'</strong>');
								console.log(error_response);
							}
						});
					return false;					
			});
			
			/*******************************************************************************/
			/**
			*	View a channels likes
			**/
			// Subscription Likes Button Click
			jQuery('#search_results').undelegate( '.youtube-plus-subscription-likes-playlist' , 'click' ).delegate('.youtube-plus-subscription-likes-playlist' , 'click' , function() {
					
				var playlist_id = jQuery(this).attr('alt');
				var playlist_title = jQuery(this).attr('title');
				var screen_base = localized_data.screen_base;
				var preloader_url = localized_data.preloader_url;
				
					// console.log(playlist_id);
					// console.log(playlist_title);
				
				jQuery('#subscription_content_div').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
				/* Ajax Request To Get New Playlist */
				jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'yt_plus_settings_form',
							form_action: 'get_users_videos',
							playlist_id: playlist_id,
							playlist_title: playlist_title,
							screen_base: screen_base
						},
						dataType: 'html',
						success: function (response) {
							jQuery('#subscription_content_div').html(response);
						},
						error : function(error_response) {
							jQuery('#subscription_content_div').html('<strong>Error : '+error_response+'</strong>');
						}
					});
					
				 // alert(playlist_id);
				return false;
			});
			
			/*******************************************************************************/
			/**
			*	View a channels uploads playlist
			
			jQuery('#search_results').undelegate( '.youtube-plus-subscription-uploads-playlist' , 'click' ).delegate( '.youtube-plus-subscription-uploads-playlist' , 'click' , function() {
						
				// // alert('view subscriptions uploads-playlist button clicked');				
							
				var channel_id = jQuery(this).attr('alt');					
				var clicked_subscription = jQuery(this).attr('alt');			
				var user_name = jQuery(this).attr('title');
						
				var screen_base = localized_data.screen_base;		
				var preloader_url = localized_data.preloader_url;
				
				jQuery('#subscription_content_div').find('ul').not(':first-child').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
				jQuery('#subscription_content_div').find('h3').text('');
				
				// Ajax Request To Get New Playlist
				jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'yt_plus_settings_form',
							form_action: 'get_channel_playlists',
							channel_id: channel_id,
							clicked_subscription: clicked_subscription,
							user_name: user_name,
							screen_base: screen_base
						},
						dataType: 'html',
						success: function (response) {
							jQuery('#subscription_content_div').html(response).find('#profile_sub_navigation').remove();
							console.log(response);
						},
						error : function(error_response) {
							jQuery('#subscription_content_div').html('<strong>Error : '+error_response+'</strong>');
							console.log(error_response);
						}
					});
				return false;	
			});
			**/
			
			/*******************************************************************************/
			/**
			*	View a entire channels playlist items
			**/
			/* Retreive Playlist Items */
				jQuery('#search_results').undelegate( '.youtube-plus-view-playlist' , 'click' ).delegate('.youtube-plus-view-playlist' , 'click' , function() {
				
					var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
					var playlist_title = jQuery(this).parents('.youtube-plus-video-single-list-item').find('h3').text().replace('insert','').replace('view playlist','');
					var screen_base = localized_data.screen_base;
					var preloader_url = localized_data.preloader_url;
					
					jQuery('#search_results').find('ul').not(':first-child').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('#search_results').find('h3').text('');
					jQuery('.pagination_page_search').remove();
					
					/* Ajax Request To Get New Playlist */
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'get_users_playlist_items',
								playlist_id: playlist_id,
								playlist_title: playlist_title,
								screen_base: screen_base
							},
							dataType: 'html',
							success: function (response) {
								jQuery('#search_results').html(response);
							},
							error : function(error_response) {
								jQuery('#search_results').html('<strong>Error : '+error_response+'</strong>');
							}
						});
						
					 // alert(playlist_id);
					return false;
				});
				
				
			/*******************************************************************************/
			/**
			*	Advanced Search Toggle
			**/ 
				jQuery('#youtube_plus_search_form').undelegate( '#advanced_search' , 'click' ).delegate( '#advanced_search' , 'click' , function() {
				
					if(jQuery('#advanced_search').attr('checked')) {
						jQuery("#advanced_search_container").stop().slideDown();
						jQuery('.advanced_search_label').find('.dashicons-admin-generic').css('color','rgba(238, 54, 55, 0.5)');
					} else {
						jQuery("#advanced_search_container").stop().slideUp();
						jQuery('.advanced_search_label').find('.dashicons-admin-generic').css('color','#5c6671');
					}
					
				});
				
			/*******************************************************************************/
			/**
			*	Search Type Check
			*  removed video count from the order by filters when playlists is not selected
			**/ 
				jQuery('#search_type_dropdown').change(function() {
					var new_value = jQuery(this).val();
					var sort_by_val = jQuery('#order_results_by_dropdown').val();
					
					if ( new_value == 'video' ) {
						jQuery('#order_results_by_dropdown').find('option[value="videoCount"]').hide();
					} else {
						jQuery('#order_results_by_dropdown').find('option').each(function() {
							jQuery(this).show();
						});
					}
					
					if ( new_value != 'video' ) {
						jQuery('#upload_date_timeframe').find('option[value="all_time"]').attr('selected','selected');
						jQuery('.upload_date_filter').fadeOut( 'fast' );
					} else {
						jQuery('#upload_date_timeframe').find('option[value="all_time"]').attr('selected','selected');
						jQuery('.upload_date_filter').fadeIn( 'fast' );
					}
					
				});
				
	});