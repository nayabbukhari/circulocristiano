jQuery(document).ready(function() {

			/** Global Scripts test ajain **/
		/** Used Throughout Each Tab**/
		/************************************/
	
			/* initialize our tags */
			jQuery( 'input[name="video-tags"]').tagsInput();
			
			/** Find Masonry and Initialize It **/
			function find_and_initialize_masonry() {
				var find_masonry = setInterval(function() {
					if ( jQuery( '#masonry-container' ).find('.youtube-plus-video-single-list-item').is( ':visible' ) ) {
						var container_height =  jQuery( '#masonry-container' ).css('height');
						/* Masonry The Results */
						$masonry_container = jQuery('#masonry-container');
						$masonry_container.imagesLoaded( function() {
							$masonry_container.masonry({
								itemSelector: '.youtube-plus-video-single-list-item',
								columnWidth: '.youtube-plus-video-single-list-item',
								// options...
								isAnimated: true,
								animationOptions: {
									duration: 250,
									easing: 'linear',
									queue: false
								}
							});
						clearInterval(find_masonry);
						console.log('timer cleared');
						});
					}
					console.log('running');
				}, 800);
			}
	
	
			/* Sub Navigation */
			/*	ie; 'Uploads' , 'Playlists', 'Likes' , 'Favorites' , 'Watch History' , 'Watch Later */
				jQuery('body').on( 'click' , '.sub-nav-button' , function() {
	
					jQuery('.sub-nav-button-active').removeClass('sub-nav-button-active');
					jQuery('.pagination_page_browse').remove();
					jQuery('.pagination_page_browse_disabled').remove();
					jQuery('.yt4wp-video-count').remove();
					find_and_initialize_masonry();
					
					/* Disable the buttons to prevent users from spamming buttons */
					jQuery('.sub-nav-button').each(function() {
						if ( jQuery(this).attr('disabled') ) {
							jQuery(this).addClass('prev_disabled');
						}
						jQuery(this).attr('disabled','disabled');
					});
						
					jQuery('#search_my_playlists_form').remove();
					jQuery(this).addClass('sub-nav-button-active');
					var selected_list = jQuery(this).attr('alt');
					var clicked_button = jQuery(this).text();
					var screen_base = localized_data.screen_base;
					var preloader_url = localized_data.preloader_url;
					jQuery('#browse_user_content').find('ul:last').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('#browse_user_content').find('h3').text('');
					jQuery('#create_new_playlist_ul').remove();
					jQuery('#clear_watch_later_playlist').remove();
					/* Ajax Request To Get New Playlist */
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'change_queried_playlist',
								selected_list: selected_list,
								clicked_button: clicked_button,
								screen_base: screen_base
							},
							dataType: 'html',
							success: function (response) {
								jQuery('#browse_user_content').html(response);
								
								/* re-enable the buttons - but disable the previously disabled one */
								jQuery('.sub-nav-button').removeAttr( 'disabled' );
								jQuery('.sub-nav-button').each(function() {
									if ( jQuery(this).hasClass('prev_disabled') ) {
										jQuery(this).attr('disabled','disabled');
										jQuery(this).removeClass('prev_disabled');
									}
								});
								
							},
							error : function(error_response) {
								jQuery('#browse_user_content').html('<strong>Error : '+error_response+'</strong>');
								console.log(error_response);
							}
						});
					return false;
				});
	
			/** Animate drawr on thumbnail hover **/
			jQuery( 'body' ).on( 'mouseenter' , '.youtube-plus-video-thumbnail' , function() {
				jQuery(this).parents('.yt-plus-outside-hidden').find('.drawer').addClass('move');
			});
			jQuery( 'body' ).on( 'mouseleave', '.youtube-plus-video-thumbnail' , function() {
				jQuery(this).parents('.yt-plus-outside-hidden').find('.drawer').removeClass('move');
			});
				// add+remove Class from the drawer when a user
				// hovers on it
				jQuery( 'body' ).on( 'mouseenter' , '.drawer' , function() {
					jQuery(this).addClass('move');
					jQuery(this).parents('.yt-plus-outside-hidden').find('.youtube-plus-video-thumbnail').stop().fadeTo('fast',.75);
				});
				jQuery( 'body' ).on( 'mouseleave' , '.drawer' , function() {
					jQuery(this).removeClass('move');
				});
	
			/* 
			// Thumbnail Hover on all pages
			// ( Upload, Browse, Search, Subscriptions )
			*/
				jQuery('body').on( 'mouseenter' , '.youtube-plus-video-single-list-item' , function() {
					jQuery(this).stop().find('.youtube-plus-video-thumbnail').fadeTo( 'fast' , 0.75 );
					jQuery(this).find('.yt-plus-unsubscribe-to-channel').show();
				});
				
				jQuery('body').on(  'mouseleave' , '.youtube-plus-video-single-list-item' , function() {
					jQuery(this).stop().find('.youtube-plus-video-thumbnail').fadeTo( 'fast' ,1 );
					jQuery(this).find('.yt-plus-unsubscribe-to-channel').hide();
				});

			
		
			  /** 2.0 - Semi-Global Scripts **/
		/**	 Used on at least 2 tabs 		**/
		/*******************************************/
		
		/* Add Video to Watch Later List Function */
		jQuery('body').on( 'click' ,  '.add_to_watch_later' , function() {
						
			var ajaxurl = localized_data.admin_ajax_url;
			var preloader_url = localized_data.preloader_url;
			var watch_later_list_id = jQuery('body').find('#watch_later_list_id').val();
			var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
			var selected_video = jQuery(this).parents('.youtube-plus-video-single-list-item');
			
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
		
		
		/**
		*	View a entire channels playlist items
		**/
		/* Retreive Playlist Items */
				jQuery('body').on( 'click' , '.youtube-plus-view-playlist' , function() {
										
					find_and_initialize_masonry();	
					
					var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
					var playlist_title = jQuery(this).parents('.youtube-plus-video-single-list-item').find('h3').text().replace('insert','').replace('view playlist','');
					var screen_base = localized_data.screen_base;
					var preloader_url = localized_data.preloader_url;
					var current_tab = jQuery('.nav-tab-active').text();
					
					jQuery('body').find('.hidden_content:visible').find('ul#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('.hidden_content:visible').find('h3').text('');
					
					jQuery('body').find('.top_level_container_youtube_plus_pro').find('ul#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('.top_level_container_youtube_plus_pro').find('h3').text('');
					
					jQuery('#search_results').find('ul#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('#search_results').find('h3').text('');
					
					/* Remove Pagination */
					jQuery(' .pagination_page_search_disabled' ).remove();
					jQuery(' .pagination_page_search' ).remove();
					
					jQuery('.pagination_page_search').remove();
					if ( current_tab == 'Browse' || current_tab == 'Search' ) {
						/* ajax playlist item  when inside of the browse tab **/
						/* Ajax Request To Get New Playlist */
						jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'yt_plus_settings_form',
									form_action: 'get_users_playlist_items',
									playlist_id: playlist_id,
									playlist_title: playlist_title,
									screen_base: screen_base,
									current_tab: current_tab
								},
								dataType: 'html',
								success: function (response) {
									jQuery('.hidden_content:visible').find('#masonry-container').html(response);
									jQuery('.top_level_container_youtube_plus_pro').html(response);
									jQuery('#search_results').html(response);
								},
								error : function(error_response) {
									jQuery('.hidden_content:visible').find('#masonry-container').html('<strong>Error : '+error_response+'</strong>');
									jQuery('.top_level_container_youtube_plus_pro').html('<strong>Error : '+error_response+'</strong>');
									jQuery('#search_results').html('<strong>Error : '+error_response+'</strong>');
								}
							});
					} else {
						/* ajax playlist item  when in search or other tab */
						/* Ajax Request To Get New Playlist */
						jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'yt_plus_settings_form',
									form_action: 'get_subsctiptions_playlist_items',
									playlist_id: playlist_id,
									playlist_title: playlist_title,
									screen_base: screen_base
								},
								dataType: 'html',
								success: function (response) {
									jQuery('#subscription_content_div').find('#masonry-container').html(response);
								},
								error : function(error_response) {
									jQuery('#subscription_content_div').find('#masonry-container').html('<strong>Error : '+error_response+'</strong>');
								}
							});
					}
					 // alert(playlist_id);
					return false;
				});
				
			
		/* Insert Click */
			jQuery('body').on(  'click' , '.insert_video_button' , function() {
				var current_tab = jQuery( '.nav-tab-active' ).text();
				if ( current_tab == 'Upload' ) {
					var video_id = jQuery(this).parents('#video_success_response').find('.video_id').val();
				} else {
					var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
				}
				// close the thickbox
				tb_remove();
				// insert the video into the active editor
				window.send_to_editor('[yt4wp-video video_id="'+video_id+'"]');
			});
		
		/* Insert Playlist Click */
			jQuery('body').on( 'click' , '.insert_playlist_button' , function() {
				var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
				// close the thickbox
				tb_remove();
				// insert the video into the active editor
				window.send_to_editor('[yt4wp-playlist playlist_id="'+playlist_id+'"]');
			});
		
		
		
			/** Browse Tab **/
		/** Browse User Content **/
		/*******************************/		
		
		/* Back to Playlists */
		jQuery('body').on( 'click' , '.youtube-plus-back-to-playlists' , function() {
			
			var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
			var playlist_title = jQuery(this).parents('.youtube-plus-video-single-list-item').find('h3').val();
			var screen_base = localized_data.screen_base;
			var preloader_url = localized_data.preloader_url;
			
			
			jQuery('#browse_user_content').find('ul').not(':first-child').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					
			jQuery('#browse_user_content').find('h3').text('');
						
			jQuery('#profile_sub_navigation').html('');
			
			
			/* Ajax Request To Get New Playlist */
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yt_plus_settings_form',
						form_action: 'back_to_playlists',
						screen_base: screen_base
					},
					dataType: 'html',
					success: function (response) {
						jQuery('#browse_user_content').html(response);
						jQuery('.top_level_container_youtube_plus_pro').html(response);
						find_and_initialize_masonry();
					},
					error : function(error_response) {
						jQuery('#browse_user_content').html('<strong>Error : '+error_response+'</strong>');
						jQuery('.top_level_container_youtube_plus_pro').html('<strong>Error : '+error_response+'</strong>');
						console.log(error_response);
					}
				});
			 // alert(playlist_id);
			return false;
		});
						
		/* No user uploads, click 'Upload One' from edit.php or post-new.php */				
		jQuery('body').on( 'click' , '.yt4wp-no-uploaded-content' , function() { 
			jQuery('.nav-tab-wrapper').find('a[alt="upload_content"]').click();	
		});
		
		// delete an existing video button click
		// jQuery ui dialog functions
		jQuery('body').on( 'click' , '.delete_video_button' , function() {
						
			var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
			var video_title = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.youtube-plus-video-title-container').text();
			var parent_element = jQuery(this).parents('.youtube-plus-video-single-list-item');
						
			jQuery('<div></div>').appendTo('body')
			.html('<div><h6 style="font-family:sans-serif;font-size:13px;">Are you sure you want to delete "'+video_title+'" entirely from your account?</h6></div>')
			.dialog({
				modal: true,
				title: 'Delete Video',
				zIndex: 10000,
				autoOpen: true,
				width: 'auto',
				resizable: false,
				dialogClass: 'ui-dialog-yt-plus ui-dialog-yt-plus-delete-video',
				buttons: {
					Yes: function () {
						// ajax get the video list response
						jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yt_plus_settings_form',
										form_action: 'delete_video_button_click',
										video_id : video_id
									},
									dataType: 'json',
									success: function (response) {
										parent_element.css('background','rgb(255, 186, 186)');
										parent_element.fadeOut('fast',function() {
											jQuery('#masonry-container').masonry('remove',parent_element);
											/* update new video count */
											var video_count = jQuery('.yt4wp-video-count').text();
											var new_video_count = parseInt( video_count.replace( ' Videos' , '' ) ) - parseInt(1);
											jQuery('.yt4wp-video-count').text( new_video_count+' Videos' );
											jQuery('#masonry-container').masonry({
												itemSelector: '.youtube-plus-video-single-list-item'
											});
											console.log('video successfuly removed');
										});
										console.log('video successfuly deleted');
									},
									error : function(error_response) {
										alert('error deleting video');
										console.log(error_response);
									}
								});
						jQuery(this).dialog("close");
					},
					No: function () {
						jQuery(this).dialog("close");
					}
				},
				close: function (event, ui) {
					jQuery(this).remove();
				}
			});	
			
			return false;
		});
		
		
		//	
		// update an existing video button click (Edit)
		// jQuery ui dialog functions
		//
			jQuery('body').on( 'click' , '.edit_video_button' , function() {						
				var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();	
					
					// define and initialize our dialog
					var edit_existing_video_dialog = jQuery( "#edit-video-dialog-form" ).dialog({
					  autoOpen: false,
					  height: 'auto',
					  width: '50%',
					  modal: true,
					  resizable: false,
					  dialogClass: 'ui-dialog-yt-plus',
					  buttons: {
						"Save": edit_existing_video,
						Cancel: function() {
						  edit_existing_video_dialog.dialog( "close" );
						  // revert our input tag fields
						  edit_existing_video_dialog.find('.tagsinput').remove();
						  edit_existing_video_dialog.find('input[name="video_tags"]').attr('id','video_tags');
						  edit_existing_video_dialog.find('#video_tags').show();
						  // reset the form
						  edit_video_form[ 0 ].reset();
						}
					  },
					  close: function() {
						// revert our input tag fields
						 edit_existing_video_dialog.find('.tagsinput').remove();
						 edit_existing_video_dialog.find('input[name="video_tags"]').attr('id','video_tags');
						 edit_existing_video_dialog.find('#video_tags').show();
						edit_video_form[ 0 ].reset();
					  }
					});
					
					var edit_video_form = edit_existing_video_dialog.find( "form" ).on( "submit", function( event ) {
					  event.preventDefault();
					});
					
					
					// when user hits update -- run this function to update the selected video
					// runs after user hits 'Save'
					function edit_existing_video() {
					
							var screen_base = localized_data.screen_base;
							var video_title = edit_video_form.find('#video_title').val();
							var video_description = edit_video_form.find('#video_description').val();
							var image_url = edit_video_form.find('.youtube-plus-video-thumbnail').attr('src');
							var video_id = edit_video_form.find('#video_id').val();
							// reset the input tags id
							edit_existing_video_dialog.find('input[name="video_tags"]').attr('id','video_tags');
							var video_tags = edit_video_form.find('#video_tags').val();
							var video_category = edit_video_form.find('#video-category').val();
							var video_privacy = edit_video_form.find('input[name="video_privacy[]"]:checked').val();			
							var preloader_url = localized_data.preloader_url;
							
							jQuery(edit_video_form).fadeTo( 'fast' , .6 ).prepend('<img class="youTube_for_wordpress_update_video_preloader" src="'+preloader_url+'" alt="preloader" style="position:absolute;left:50%;top:50%;">');
							
							// disable all fields
							jQuery(edit_video_form).find('input').each(function() {
								jQuery(this).attr('disabled','disabled');
							});
							jQuery('textarea').attr('disabled','disabled');
							
							jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yt_plus_settings_form',
										form_action: 'yt_plus_edit_video',
										screen_base : screen_base,
										video_title : video_title,
										video_description : video_description,
										video_id : video_id,
										video_tags : video_tags,
										video_category : video_category,
										video_privacy : video_privacy
										// video_category : video_category
									},
									dataType: 'html',
									success: function (response) {
										// alert('success');
										edit_existing_video_dialog.dialog( "close" );
										
										// re-enable all fields
										jQuery(edit_video_form).fadeTo( 'fast' , 1 ).find('input').each(function() {
											jQuery(this).removeAttr('disabled','disabled');
										});
										jQuery('textarea').removeAttr('disabled','disabled');
										// remove the preloader
										jQuery('.youTube_for_wordpress_update_video_preloader').remove();
										
										// fade down our container
										jQuery('#browse_user_content').find('input.video_id[value="'+video_id+'"]').parents('.youtube-plus-video-single-list-item').fadeTo( 'fast' , .4 );
											// ajax to update the video container
											// with the new contents
											jQuery.ajax({
												type: 'POST',
												url: ajaxurl,
												data: {
													action: 'yt_plus_settings_form',
													form_action: 'youtube_plus_update_video_container',
													video_id : video_id,
													screen_base : screen_base
												},
												dataType: 'html',
												success: function (response) {
													// update the container with the new response
													jQuery('#browse_user_content').find('input.video_id[value="'+video_id+'"]').parents('.youtube-plus-video-single-list-item').html(response);
													jQuery('#browse_user_content').find('input.video_id[value="'+video_id+'"]').parents('.youtube-plus-video-single-list-item').fadeTo( 'fast' , 1 );
												},
												error : function(error_response) {
													alert('error saving video data');
													// console.log(error_response);
												}
											});
										console.log('Successfully updated "'+video_title+'"');
									},
									error : function(error_response) {
										alert('error saving video data');
										// console.log(error_response);
									}
								});
								
						}
					
				// ajax get the video list response
				jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'edit_video_button_click',
								video_id : video_id
							},
							dataType: 'json',
							success: function (response) {
								console.log(response);
								edit_existing_video_dialog.find('#video_id').val(video_id);
								edit_existing_video_dialog.find('#video_title').val(response.snippet.title);
								edit_existing_video_dialog.find('#video_description').val(response.snippet.description);
								edit_existing_video_dialog.find('#video_tags').val(response.snippet.tags);
								edit_existing_video_dialog.find('#video-category').find('option[value="'+response.snippet.categoryId+'"]').attr('selected','selected');
								edit_existing_video_dialog.find('input.'+response.status.privacyStatus).attr('checked','checked');
								// initialize the tags field first,
								edit_existing_video_dialog.find('#video_tags').tagsInput();
								edit_existing_video_dialog.dialog( "open" );
								// console.log(response);
							},
							error : function(error_response) {
								alert('error retreiving video data, please try again. If the error persists please contact the YouTube for WordPress support team.');
								console.log(error_response);
							}
						});			
				return false;
			});
									
		
		// update an existing playlist button click
		// jQuery ui dialog functions
		
		jQuery('body').on( 'click' , '.edit_playlist_button' , function() {
						
			var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
			var screen_base = localized_data.screen_base;
			
			
			// when user hits update -- run this function to update the selected play list
			// runs after user hits 'Save'
				function edit_existing_playlist() {
				
						var screen_base = localized_data.screen_base;	
						var playlist_title = edit_playlist_form.find('#playlist_title').val();
						var playlist_description = edit_playlist_form.find('#playlist_description').val();
						var image_url = edit_playlist_form.find('.youtube-plus-playlist-thumbnail').attr('src');
						var playlist_id = edit_playlist_form.find('#playlist_id').val();
						// reset the input tags id
						edit_playlist_form.find('input[name="playlist_tags"]').attr('id','playlist_tags');
						var playlist_tags = edit_playlist_form.find('#playlist_tags').val();				
						var playlist_privacy = edit_playlist_form.find('input[name="playlist_privacy[]"]:checked').val();								
						var preloader_url = localized_data.preloader_url;				
						jQuery(edit_existing_playlist_dialog).fadeTo( 'fast' , .6 ).prepend('<img class="youTube_for_wordpress_update_video_preloader" src="'+preloader_url+'" alt="preloader" style="position:absolute;left:50%;top:50%;">');
						
						// disable all fields
						jQuery(edit_existing_playlist_dialog).find('input').each(function() {
							jQuery(this).attr('disabled','disabled');
						});
						jQuery('textarea').attr('disabled','disabled');
						
						jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'yt_plus_settings_form',
									form_action: 'yt_plus_update_playlist',
									screen_base : screen_base,
									playlist_title : playlist_title,
									playlist_description : playlist_description,
									playlist_id : playlist_id,
									playlist_tags : playlist_tags,
									playlist_privacy : playlist_privacy
									// playlist_category : playlist_category
								},
								dataType: 'html',
								success: function (response) {
									// alert('success');
									edit_existing_playlist_dialog.dialog( "close" );
									// re-enable all fields
									jQuery(edit_existing_playlist_dialog).fadeTo( 'fast' , 1 ).find('input').each(function() {
										jQuery(this).removeAttr('disabled','disabled');
									});
									jQuery('textarea').removeAttr('disabled','disabled');
									// remove the preloader
									jQuery('.youTube_for_wordpress_update_video_preloader').remove();
									
									// fade down our container
									jQuery('#browse_user_content').find('input.playlist_id[value="'+playlist_id+'"]').parents('.youtube-plus-video-single-list-item').fadeTo( 'fast' , .4 );
										// ajax to update the video container
										// with the new contents
										jQuery.ajax({
											type: 'POST',
											url: ajaxurl,
											data: {
												action: 'yt_plus_settings_form',
												form_action: 'youtube_plus_update_playlist_container',
												playlist_id : playlist_id,
												screen_base : screen_base
											},
											dataType: 'html',
											success: function (response) {
												// update the container with the new response
												jQuery('#browse_user_content').find('input.playlist_id[value="'+playlist_id+'"]').parents('.youtube-plus-video-single-list-item').html(response);
												jQuery('#browse_user_content').find('input.playlist_id[value="'+playlist_id+'"]').parents('.youtube-plus-video-single-list-item').fadeTo( 'fast' , 1 );
											},
											error : function(error_response) {
												alert('error saving video data');
												// console.log(error_response);
											}
										});
									console.log(response);
								},
								error : function(error_response) {
									alert('error saving playlist data');
									// console.log(error_response);
								}
							});
							
					}
				
				
				// our dialog settings
				var edit_existing_playlist_dialog = jQuery( "#edit-playlist-dialog-form" ).dialog({
				  autoOpen: false,
				  height: 'auto',
				  width: '50%',
				  modal: true,
				  resizable: false,
				  dialogClass: 'ui-dialog-yt-plus',
				  buttons: {
					"Save": edit_existing_playlist,
					Cancel: function() {
					  edit_existing_playlist_dialog.dialog( "close" );
					   // revert our input tag fields
					  edit_existing_playlist_dialog.find('.tagsinput').remove();
					  edit_existing_playlist_dialog.find('input[name="playlist_tags"]').attr('id','playlist_tags');
					  edit_existing_playlist_dialog.find('#playlist_tags').show();
					  // reset the form
					  edit_playlist_form[ 0 ].reset();
					}
				  },
				  close: function() {
					// revert our input tag fields
					  edit_existing_playlist_dialog.find('.tagsinput').remove();
					  edit_existing_playlist_dialog.find('#playlist_tags').show();
					  // reset the form
					edit_playlist_form[ 0 ].reset();
				  }
				});
				
				edit_playlist_form = edit_existing_playlist_dialog.find( "form" ).on( "submit", function( event ) {
				  event.preventDefault();
				});
			
			
			
			// ajax get the video list response
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'yt_plus_settings_form',
							form_action: 'edit_playlist_button_click',
							playlist_id : playlist_id
						},
						dataType: 'json',
						success: function (response) {
							console.log(response);
							edit_existing_playlist_dialog.find('#playlist_id').val(playlist_id);
							edit_existing_playlist_dialog.find('#playlist_title').val(response.snippet.title);
							edit_existing_playlist_dialog.find('#playlist_description').val(response.snippet.description);
							edit_existing_playlist_dialog.find('#playlist_tags').val(response.snippet.tags);
							edit_existing_playlist_dialog.find('input.'+response.status.privacyStatus).attr('checked','checked');
							// initialize the tags field first,
							edit_existing_playlist_dialog.find('#playlist_tags').tagsInput();
							edit_existing_playlist_dialog.dialog( "open" );
							// console.log(response);
						},
						error : function(error_response) {
							alert('error retreiving playlist data');
						}
					});		
			
			return false;
			
		});
		
			
		/************************************************************/
		
		// modal video preview
		jQuery('body').on( 'click' , '.youtube-plus-video-preview-btn' , function() {
				
				var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
				
				var the_title = jQuery('#TB_title').html();
				jQuery('#TB_title').remove();
				jQuery('#TB_ajaxContent').fadeOut( 'fast' , function() {
					jQuery('#TB_window').prepend('<input type="hidden" id="video_id" value="'+video_id+'"><a href="#" class="yt_plus_insert_video button-secondary" title="Insert Video" onclick="yt_plus_insert_video_to_editor(this);" style="position:absolute;top:0;left:0;margin-top:6em;margin-left:4em;"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a><a href="#" class="yt_plus_modal_back_to_videos button-secondary" title="Close" onclick="yt_plus_modal_back_to_playlist();" style="position:absolute;top:0;right:0;margin-top:6em;margin-right:4em;"><div class="dashicons dashicons-dismiss" style="line-height:1.3"></div></a>');
					jQuery('#TB_window').css('background','#1B1B1B');
					jQuery('#TB_window').prepend(the_title);
				});
				
			});
		
		
		// Remove a video from 'Watch Later' Playlist	
		// jQuery ui dialog functions
			jQuery('body').on( 'click' , '.remove_video_from_watch_later' , function() {
							
				var playlistItem_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlistItem_id').val();
				var video_title = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.youtube-plus-video-title-container').text();
				var parent_element = jQuery(this).parents('.youtube-plus-video-single-list-item');
				
				jQuery('<div></div>').appendTo('body')
				.html('<div><h6 style="font-family:sans-serif;font-size:13px;">Are you sure you want to remove "'+video_title+'" from your "Watch Later" playlist?</h6></div>')
				.dialog({
					modal: true,
					title: 'Remove Video From "Watch Later" Playlist?',
					zIndex: 10000,
					autoOpen: true,
					width: 'auto',
					resizable: false,
					dialogClass: 'ui-dialog-yt-plus',
					buttons: {
						Yes: function () {
							// ajax get the video list response
							jQuery.ajax({
										type: 'POST',
										url: ajaxurl,
										data: {
											action: 'yt_plus_settings_form',
											form_action: 'delete_video_from_playlist_button_click',
											playlistItem_id : playlistItem_id
										},
										dataType: 'json',
										success: function (response) {
											parent_element.css('background','rgb(255, 186, 186)');
											parent_element.fadeOut('fast',function() {
												jQuery('#masonry-container').masonry('remove',parent_element);
												jQuery('#masonry-container').masonry({
													itemSelector: '.youtube-plus-video-single-list-item'
												});
												console.log('video successfuly removed');
											});
										},
										error : function(error_response) {
											alert('error deleting video');
											console.log(error_response);
										}
									});
							jQuery(this).dialog("close");
						},
						No: function () {
							jQuery(this).dialog("close");
						}
					},
					close: function (event, ui) {
						jQuery(this).remove();
					}
				});	
				
				return false;
				
			});	
			
			
		
		// Clear the entire watch later playlist
			jQuery('body').on( 'click' , '.yt-plus-clear-entire-playlist' , function() {			
										
				jQuery('<div></div>').appendTo('body')
				.html('<div><h6 style="font-family:sans-serif;font-size:13px;">Are you sure you want to remove all of the videos from your "Watch Later" playlist? This cannot be undone.</h6></div>')
				.dialog({
					modal: true,
					title: 'Empty "Watch Later" Playlist?',
					zIndex: 10000,
					autoOpen: true,
					width: 'auto',
					resizable: false,
					dialogClass: 'ui-dialog-yt-plus',
					buttons: {
						Yes: function () {
							// ajax get the video list response
							// loop over items here and delete one at a time						
							jQuery('#masonry-container').find('.youtube-plus-video-single-list-item').each(function() {
												
								var playlistItem_id = jQuery(this).find('.playlistItem_id').val();
								var parent_element = jQuery(this);
								
								jQuery.ajax({
											type: 'POST',
											url: ajaxurl,
											data: {
												action: 'yt_plus_settings_form',
												form_action: 'delete_video_from_playlist_button_click',
												playlistItem_id : playlistItem_id
											},
											dataType: 'json',
											success: function (response) {
												parent_element.css('background','rgb(255, 186, 186)');
												parent_element.fadeOut('fast',function() {
													jQuery('#masonry-container').masonry('remove',parent_element);
													jQuery('#masonry-container').masonry({
														itemSelector: '.youtube-plus-video-single-list-item'
													});
													console.log('video successfuly removed');
												});
											},
											error : function(error_response) {
												console.log(error_response);
											}
										});
							});
							
							jQuery(this).dialog("close");
						},
						No: function () {
							jQuery(this).dialog("close");
						}
					},
					close: function (event, ui) {
						jQuery(this).remove();
					}
				});	
				
				return false;
				
			});		
		
		
		/* Search user videos ( inside of uploads, searching via search bar ) */
		jQuery('body').on( 'submit' , '#search_my_playlists_form' , function() {
			return false;
		});
		// delay 1 second after last key is typed
		var timer;
		jQuery('body').on( 'keyup' , '#search_my_playlists_form #yt-plus-upload-search' , function() {
			var search_term = jQuery('#yt-plus-upload-search').val();
			var channel_id = jQuery('.sub-nav-button-active').attr('alt');
			var screen_base = localized_data.screen_base;
			var preloader_url = localized_data.preloader_url;
			clearInterval(timer);  //clear any interval on key up
			timer = setTimeout(function() { //then give it a second to see if the user is finished
				jQuery('#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
				// ajax search function here
				jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'youtube_plus_search_user_uploads',
								search_term: search_term,
								channel_id: channel_id,
								screen_base: screen_base
							},
							dataType: 'html',
							success: function(response) {
								jQuery('#masonry-container').html(response);
								find_and_initialize_masonry();
							},
							error: function(response) {
								console.log(response);
							}
						});
			}, 1000 );
			return false;
		});
	
					
		/* Search Submit Function */
		jQuery('body').on( 'submit', '#youtube_plus_search_form' , function() {
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
								find_and_initialize_masonry();
							}
						});			
				return false;
		});
			
			
		/* Pagination Click */
			
			/** Search Tab **/
			jQuery('body').on( 'click' , '.pagination_page_search' , function() {
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
									find_and_initialize_masonry();
								}
							});
				return false;
			});
		 
			/** Subscriptions Tab **/
			jQuery('body').on( 'click' , '.pagination_page_subscriptions' , function() {
				
				// set up necessary variables
				var ajaxurl = localized_data.admin_ajax_url;
				var page_token = jQuery(this).attr('alt');
				var preloader_url = localized_data.preloader_url;
				var screen_base = localized_data.screen_base;
				
				// remove pagination buttons
				jQuery( '.pagination_page_subscriptions_disabled' ).remove();
				jQuery( '.pagination_page_subscriptions' ).remove();
										
				// append the perloader
				jQuery('#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
							
					jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'yt_plus_settings_form',
									form_action: 'youtube_plus_paginate_subscriptions_youtube',
									page_token: page_token,
									screen_base: screen_base
								},
								dataType: 'html',
								success: function(response) {
									jQuery('#masonry-container').remove();
									jQuery( '#subscribtions_box' ).find( 'h3' ).remove();
									jQuery('#subscribtions_box').append(response);
									find_and_initialize_masonry();
								},
								error: function(response) {
									jQuery('#subscribtions_box').append(response);
								}
							});
				return false;
			});
			
			/** Browse Tab **/
			jQuery('body').on( 'click' , '.pagination_page_browse' , function() {
				
				// set up necessary variables
				var ajaxurl = localized_data.admin_ajax_url;
				var page_token = jQuery(this).attr('alt');
				var preloader_url = localized_data.preloader_url;
				var screen_base = localized_data.screen_base;
				var current_tab = jQuery( '.sub-nav-button-active' ).text();
				var playlist_id = jQuery( '.sub-nav-button-active' ).attr( 'alt' );
				
				// remove pagination buttons
				jQuery( '.pagination_page_browse_disabled' ).remove();
				jQuery( '.pagination_page_browse' ).remove();
										
				// append the perloader
				jQuery('#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
				
					jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'yt_plus_settings_form',
									form_action: 'youtube_plus_paginate_browse_youtube',
									page_token: page_token,
									screen_base: screen_base,
									current_tab: current_tab,
									playlist_id: playlist_id
								},
								dataType: 'html',
								success: function(response) {
									jQuery('#masonry-container').remove();
									jQuery( '#browse_user_content' ).find( 'h3' ).remove();
									jQuery('#profile_sub_navigation').append(response);
									find_and_initialize_masonry();
								}
							});
				return false;
			});
		 			
			/*******************************************************************************/
			/**
			*	Subscribe/Unsubscribe Function
			**/
			jQuery('body').on( 'click' , '.yt-subscribe-btn' , function() {		
									
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
			
			
			/**
			*	View a Channels Playlist
			* 	xxxx's Playlist  button from within a youtube channel
			**/
			jQuery('body').on( 'click' , '.view-subscription-playlists-btn' , function() {
											
					var channel_id = jQuery(this).attr('alt');			
					var clicked_subscription = jQuery(this).attr('title');
					
					var screen_base = localized_data.screen_base;
					var preloader_url = localized_data.preloader_url;
					
					console.log(channel_id);
					console.log(clicked_subscription);
					
					jQuery('#subscription_content_div').find('#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('#browse_page_title').remove();
					
					/* Remove Pagination */
					jQuery(' .pagination_page_search_disabled' ).remove();
					jQuery(' .pagination_page_search' ).remove();
					
					// Ajax Request To Get New Playlist 
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
								jQuery('#subscription_content_div').find('#masonry-container').remove();
								jQuery('#subscription_content_div').append(response);
								find_and_initialize_masonry();
							},
							error : function(error_response) {
								jQuery('#subscription_content_div').find('#masonry-container').html('<strong>Error : '+error_response+'</strong>');
								console.log(error_response);
							}
						});
					return false;					
			});
			
					
			/**
			*	View a channels likes
			* 	xxxx's Likes button from within a youtube channel
			**/
			// Subscription Likes Button Click
				jQuery('body').on( 'click' , '.youtube-plus-subscription-likes-playlist' , function() {
						
					var playlist_id = jQuery(this).attr('alt');
					var playlist_title = jQuery(this).attr('title');
					var screen_base = localized_data.screen_base;
					var preloader_url = localized_data.preloader_url;

					jQuery('#subscription_content_div').find('#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('#subscription_content_div').find('#browse_page_title').remove();
					
					/* Remove Pagination */
					jQuery(' .pagination_page_search_disabled' ).remove();
					jQuery(' .pagination_page_search' ).remove();
					
					// Ajax Request To Get New Playlist 
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
								jQuery('#subscription_content_div').find('#masonry-container').remove();
								jQuery('#subscription_content_div').append(response);
								find_and_initialize_masonry();
							},
							error : function(error_response) {
								jQuery('#subscription_content_div').find('#masonry-container').html('<strong>Error : '+error_response+'</strong>');
							}
						});
						
					 // alert(playlist_id);
					return false;
				});
			
			
			/**
			*	View a channels uploads playlist
			* 	xxxx's Uploads button from within a youtube channel
			**/
			jQuery('body').on( 'click' , '.youtube-plus-subscription-uploads-playlist' , function() {
						
				// // alert('view subscriptions uploads-playlist button clicked');				
							
				var channel_id = jQuery(this).attr('alt');					
				var clicked_subscription = jQuery(this).attr('alt');			
				var user_name = jQuery(this).attr('title');
						
				var screen_base = localized_data.screen_base;		
				var preloader_url = localized_data.preloader_url;
				
				jQuery('#subscription_content_div').find('ul').not(':first-child').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
				jQuery('#browse_page_title').remove();
				
				/* Remove Pagination */
				jQuery(' .pagination_page_search_disabled' ).remove();
				jQuery(' .pagination_page_search' ).remove();
					
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
							find_and_initialize_masonry();
						},
						error : function(error_response) {
							jQuery('#subscription_content_div').html('<strong>Error : '+error_response+'</strong>');
							console.log(error_response);
						}
					});
				return false;	
			});
				
			/**
			*	View a channels favorite playlist
			* 	xxxx's Favorites button from within a youtube channel
			**/	
			// Subscription Favorites Button Click
			jQuery('body').on( 'click' , '.youtube-plus-subscription-favorites-playlist' , function() {
										
				var playlist_id = jQuery(this).attr('alt');
				var playlist_title = jQuery(this).attr('title');
				var screen_base = localized_data.screen_base;		
				var preloader_url = localized_data.preloader_url;
								
				jQuery('#subscription_content_div').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
				/* Remove Pagination */
				jQuery(' .pagination_page_search_disabled' ).remove();
				jQuery(' .pagination_page_search' ).remove();
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
							find_and_initialize_masonry();
						},
						error : function(error_response) {
							jQuery('#subscription_content_div').html('<strong>Error : '+error_response+'</strong>');
						}
					});
					
				 // alert(playlist_id);
				return false;
			});
			
			/**
			*	Back to your subscriptions
			**/ 
			jQuery('body').on( 'click' , '.youtube-plus-back-to-subscriptions' ,  function() {
						
					// alert('back to subscriptions clicked');			
								
					var screen_base = localized_data.screen_base;		
					var preloader_url = localized_data.preloader_url;
					
					jQuery('#subscribtions_box').find('ul').not(':first-child').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
					jQuery('#subscribtions_box').find('h3').text('');
					jQuery('#subscribtions_box').find('#profile_sub_navigation').remove('');
					// remove the subscription button
					jQuery('.yt-subscribe-btn').remove();
					
					/* Ajax Request To Get New Playlist */
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'yt_plus_settings_form',
								form_action: 'reload_subscriptions',
								screen_base: screen_base
							},
							dataType: 'html',
							success: function (response) {
								jQuery('#subscribtions_box').html(response);
							},
							error : function(error_response) {
								jQuery('#subscribtions_box').html('<strong>Error : '+error_response+'</strong>');
								console.log(error_response);
							}
						});
					return false;	
			});	
			
			/**
			*	Advanced Search Toggle
			**/ 
				jQuery('body').on( 'click' , '#advanced_search' , function() {
				
					if(jQuery('#advanced_search').attr('checked')) {
						jQuery("#advanced_search_container").stop().slideDown();
						jQuery('.advanced_search_label').find('.dashicons-admin-generic').css('color','rgba(238, 54, 55, 0.5)').addClass( 'elastic-gear-spin' );
						setTimeout(function() {
							jQuery('.advanced_search_label').find('.dashicons-admin-generic').removeClass( 'elastic-gear-spin' );
						},1201);
					} else {
						jQuery("#advanced_search_container").stop().slideUp();
						jQuery('.advanced_search_label').find('.dashicons-admin-generic').css('color','#5c6671').addClass( 'elastic-gear-spin' );
						setTimeout(function() {
							jQuery('.advanced_search_label').find('.dashicons-admin-generic').removeClass( 'elastic-gear-spin' );
						},1201);
					}
					
				});
				
			
			/**
			*	Search Type Check
			*  removed video count from the order by filters when playlists is not selected etc.
			**/ 
				jQuery('body').on( 'change' , '#search_type_dropdown' , function() {
					
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
				
				
					/*	Upload Tab	*/
				/** File Upload Script **/
				/**************************/
				
				//
				// Advanced Settings toggle
				//
					jQuery('body').on( 'click' , '.video-upload-advanced-settings-toggle' , function() {
						jQuery('form#youtube-plus-pro-upload-form div.advanced-settings').slideToggle( 350 );
					});
				
				
				
				/** Subscription Page **/	
				/**
					*	Delete Video From Subscriptions (unsubscribe from a channel)
					**/
					jQuery('body').on(  'click' , '.yt-plus-unsubscribe-to-channel' , function() {
							
							// alert('view subscriptions uploads button clicked');			
							
							var subscription_id = jQuery(this).next().val();
							var clicked_subscription = jQuery(this).parents('.youtube-plus-video-single-list-item').find('h3').text();
							var clicked_x = jQuery(this);
							var screen_base = localized_data.screen_base;
							var preloader_url = localized_data.preloader_url;
							jQuery('<div></div>').appendTo('body')
							.html('<div><h6 style="font-family:sans-serif;font-size:13px;">Are you sure you want to unsubscribe from "'+clicked_subscription+'"?</h6></div>')
							.dialog({
								modal: true,
								title: 'Unsubscribe',
								zIndex: 10000,
								autoOpen: true,
								width: 'auto',
								resizable: false,
								dialogClass: 'ui-dialog-yt-plus',
								buttons: {
									Yes: function () {
										/* Ajax Request To Get New Playlist */
										jQuery.ajax({
											type: 'POST',
											url: ajaxurl,
											data: {
												action: 'yt_plus_settings_form',
												form_action: 'youtube_plus_remove_channel_subscription',
												channel_id: subscription_id
											},
											dataType: 'html',
											success: function(response) {
												clicked_x.parents('.youtube-plus-video-single-list-item').fadeOut('fast',function() {
													jQuery('#masonry-container').masonry( 'remove', jQuery(this) );
														jQuery('#masonry-container').masonry({
															itemSelector: '.youtube-plus-video-single-list-item'
														});
													console.log( 'successfully unsubscribed' );
												});
											},
											error: function(error_response) {
												console.log(error_response);
											}
										});	
										jQuery(this).dialog("close");
									},
									No: function () {
										jQuery(this).dialog("close");
									}
								},
								close: function (event, ui) {
									jQuery(this).remove();
								}
							});			
						return false;	
					});
				
		
				// view subscription video 
				// top level page youtube-plus-pro
					jQuery('body').on( 'click' , '.view-subscription-videos-btn' , function() {					
														
							var channel_id = jQuery(this).attr('alt');
							var clicked_subscription = jQuery(this).attr('alt');
							var user_name = jQuery(this).attr('title');

							var screen_base = localized_data.screen_base;
							var preloader_url = localized_data.preloader_url;
							var current_tab = jQuery('.nav-tab-active').text();
							
							jQuery('#subscribtions_box').find('ul#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
							jQuery('#subscribtions_box').find('h3').text('');
							
							jQuery('#search_results').find('ul#masonry-container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
							jQuery('#search_results').find('h3').text('');
							
							jQuery('.pagination_page_search').remove();
							jQuery('.pagination_page_search_disabled').remove();
							
							/* Ajax Request To Get New Playlist */
							jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'yt_plus_settings_form',
										form_action: 'get_subscription_videos',
										channel_id: channel_id,
										clicked_subscription: clicked_subscription,
										user_name: user_name,
										screen_base: screen_base,
										current_tab: current_tab
									},
									dataType: 'html',
									success: function (response) {
										jQuery('#subscribtions_box').html(response);
										jQuery('#search_results').html(response);
										find_and_initialize_masonry();
									},
									error : function(error_response) {
										jQuery('#subscribtions_box').html('<strong>Error : '+error_response+'</strong>');
										jQuery('#search_results').html('<strong>Error : '+error_response+'</strong>');
										console.log(error_response);
									}
								});
							return false;	
					});
					
				
				// find and initialize our masonry 
				// container on initial page load
				find_and_initialize_masonry();
				
}); // End Document Ready

	

		// Insert the video into the editor , directly from the modal
		function yt_plus_insert_video_to_editor(clicked_element) {
				var video_id = jQuery(clicked_element).prev().val();
				// close the thickbox
				tb_remove();
				// insert the video into the active editor
				window.send_to_editor('[yt4wp-video video_id="'+video_id+'"]');
			}
		
		// back to our playlist/video listings 
		// when inside the thickbox modal
		function yt_plus_modal_back_to_playlist() {
				jQuery('#TB_iframeContent').remove();
				jQuery('.yt_plus_modal_back_to_videos').remove();
				jQuery('.yt_plus_insert_video').remove();
				jQuery('#video_id').remove();
				jQuery('#TB_window').css('background','#FCFCFC');
				jQuery('#TB_ajaxContent').fadeIn( 'fast' , function() {
					jQuery(this).find('#TB_title').show();
				});	
			}
		