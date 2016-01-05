jQuery(document).ready(function() {
		
		/* Masonry the Results */
		jQuery('#masonry-container').masonry({
		  itemSelector: '.youtube-plus-video-single-list-item',
		  // options...
		  isAnimated: true,
		  animationOptions: {
			duration: 250,
			easing: 'linear',
			queue: false
		  }
		});		

		
		/* Insert Click */
		jQuery('#browse_user_content').delegate('.insert_video_button' , 'click' , function() {
			var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
			// close the thickbox
			tb_remove();
			// insert the video into the active editor
			window.send_to_editor('[youtube-plus-video video_id="'+video_id+'"]');
		});
		
		/* Insert Playlist Click */
		jQuery('#browse_user_content').delegate('.insert_playlist_button' , 'click' , function() {
			var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
			// close the thickbox
			tb_remove();
			// insert the video into the active editor
			 window.send_to_editor('[youtube-plus-playlist playlist_id="'+playlist_id+'"]');
		});
		
		
		/**********************************************************************************************/
		
		/** Animate drawr on thumbnail hover **/
		jQuery('#browse_user_content_container').undelegate( '.youtube-plus-video-thumbnail' , 'mouseenter' ).delegate( '.youtube-plus-video-thumbnail' , 'mouseenter' , function() { 
			jQuery(this).parents('.yt-plus-outside-hidden').find('.drawer').addClass('move');
		});
		
		jQuery('#browse_user_content_container').undelegate( '.youtube-plus-video-thumbnail' , 'mouseleave' ).delegate( '.youtube-plus-video-thumbnail' , 'mouseleave' , function() {
			jQuery(this).parents('.yt-plus-outside-hidden').find('.drawer').removeClass('move');
		});
			
			// add+remove Class from the drawer when a user
			// hovers on it
			jQuery('#browse_user_content_container').undelegate( '.drawer' , 'mouseenter' ).delegate( '.drawer' , 'mouseenter' , function() {
				jQuery(this).addClass('move');
				jQuery(this).parents('.yt-plus-outside-hidden').find('.youtube-plus-video-thumbnail').stop().fadeTo('fast',.75);
			});
			
			jQuery('#browse_user_content_container').undelegate( '.drawer' , 'mouseleave' ).delegate( '.drawer' , 'mouseleave' , function() {
				jQuery(this).removeClass('move');
			});
		
		// end drawer animation
		
		
		/* Sub Navigation */
		jQuery('#browse_user_content_container').delegate('.sub-nav-button' , 'click' , function() {
			
			jQuery('.sub-nav-button-active').removeClass('sub-nav-button-active');
			
			/* Disable the buttons to prevent users from spamming buttons */
			jQuery('.sub-nav-button').attr( 'disabled' , 'disabled' );
			jQuery('#search_my_playlists_form').remove();
			jQuery(this).addClass('sub-nav-button-active');
			var selected_list = jQuery(this).attr('alt');
			var clicked_button = jQuery(this).text();
			var screen_base = localized_data.screen_base;
			var preloader_url = localized_data.preloader_url;
			jQuery('#browse_user_content_container').find('ul:last').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
			jQuery('#browse_user_content_container').find('h3').text('');
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
						jQuery('#browse_user_content_container').html(response);
						jQuery('.sub-nav-button').removeAttr( 'disabled' );
					},
					error : function(error_response) {
						jQuery('#browse_user_content_container').html('<strong>Error : '+error_response+'</strong>');
						console.log(error_response);
					}
				});
			return false;
		});
		
		/* Retreive Playlist Items */
		/* Insert Click */
		jQuery('#browse_user_content_container').delegate('.youtube-plus-view-playlist' , 'click' , function() {
		
			var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
			var playlist_title = jQuery(this).parents('.youtube-plus-video-single-list-item').find('h3').text();
			var screen_base = localized_data.screen_base;
			var preloader_url = localized_data.preloader_url;
			
			console.log('Playlist ID : '+playlist_id+'<br /> Playlist title : '+playlist_title+'<br /> Screen Base : '+screen_base+'<br /> Preloader URL : '+preloader_url);
			
			jQuery('#browse_user_content_container').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
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
						jQuery('#browse_user_content_container').html(response);
						jQuery('#browse_user_content_container').prepend('<input type="hidden" class="playlist_id" value="'+playlist_id+'">');
					},
					error : function(error_response) {
						jQuery('#browse_user_content_container').html('<strong>Error : '+error_response+'</strong>');
					}
				});
			 	
			 // alert(playlist_id);
			return false;
		});
		
		
		/* Back to Playlists */
		jQuery('#browse_user_content_container').delegate('.youtube-plus-back-to-playlists' , 'click' , function() {
			var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
			var playlist_title = jQuery(this).parents('.youtube-plus-video-single-list-item').find('h3').val();
			var screen_base = localized_data.screen_base;
			var preloader_url = localized_data.preloader_url;
			jQuery('#browse_user_content_container').find('ul').not(':first-child').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
			jQuery('#browse_user_content_container').find('h3').text('');
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
						jQuery('#browse_user_content_container').html(response);
					},
					error : function(error_response) {
						jQuery('#browse_user_content_container').html('<strong>Error : '+error_response+'</strong>');
						console.log(error_response);
					}
				});
			 // alert(playlist_id);
			return false;
		});
						
		
		/************************************************************/
		
		// delete an existing video button click
		// jQuery ui dialog functions
		jQuery('#browse_user_content_container').undelegate( '.delete_video_button' , 'click' ).delegate( '.delete_video_button' , 'click' , function() {
						
			var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
			var video_title = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.youtube-plus-video-title-container').text();
			var parent_element = jQuery(this).parents('.youtube-plus-video-single-list-item');
						
			jQuery('<div></div>').appendTo('body')
			.html('<div><h6 style="font-family:sans-serif;font-size:13px;">Are you sure you want to delete '+video_title+'?</h6></div>')
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
	// update an existing video button click
	// jQuery ui dialog functions
	//
		jQuery('#browse_user_content_container').undelegate( '.edit_video_button' , 'click' ).delegate( '.edit_video_button' , 'click' , function() {						
			var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();	
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
							alert('error retreiving video data');
						}
					});			
			return false;
		});
		
		//
		// our new video form settings
		//		
		
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
				
				jQuery(edit_video_form).fadeTo( 'fast' , .6 ).prepend('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" style="position:absolute;left:50%;top:50%;">');
				
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
							jQuery('.youTube_api_key_preloader').remove();
							
							// fade down our container
							jQuery('#browse_user_content_container').find('input.video_id[value="'+video_id+'"]').parents('.youtube-plus-video-single-list-item').fadeTo( 'fast' , .4 );
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
										jQuery('#browse_user_content_container').find('input.video_id[value="'+video_id+'"]').parents('.youtube-plus-video-single-list-item').html(response);
										jQuery('#browse_user_content_container').find('input.video_id[value="'+video_id+'"]').parents('.youtube-plus-video-single-list-item').fadeTo( 'fast' , 1 );
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
		
		
		// our dialog settings
		edit_existing_video_dialog = jQuery( "#edit-video-dialog-form" ).dialog({
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
		
		edit_video_form = edit_existing_video_dialog.find( "form" ).on( "submit", function( event ) {
		  event.preventDefault();
		  addUser();
		});
		
		
		/************************************************************/
		// update an existing playlist button click
		// jQuery ui dialog functions
		
		jQuery('#browse_user_content_container').undelegate( '.edit_playlist_button' , 'click' ).delegate( '.edit_playlist_button' , 'click' , function() {
						
			var playlist_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.playlist_id').val();
			var screen_base = localized_data.screen_base;
			
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
			// open the modal
			
			// populate the fields with the response...
			
			//simple right?
			
			
			return false;
		});
		
		//
		// our new playlist form settings
		//		
		
		// when user hits update -- run this function to update the selected playlist
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
				
				jQuery(edit_existing_playlist_dialog).fadeTo( 'fast' , .6 ).prepend('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" style="position:absolute;left:50%;top:50%;">');
				
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
							jQuery('.youTube_api_key_preloader').remove();
							
							// fade down our container
							jQuery('#browse_user_content_container').find('input.playlist_id[value="'+playlist_id+'"]').parents('.youtube-plus-video-single-list-item').fadeTo( 'fast' , .4 );
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
										jQuery('#browse_user_content_container').find('input.playlist_id[value="'+playlist_id+'"]').parents('.youtube-plus-video-single-list-item').html(response);
										jQuery('#browse_user_content_container').find('input.playlist_id[value="'+playlist_id+'"]').parents('.youtube-plus-video-single-list-item').fadeTo( 'fast' , 1 );
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
		edit_existing_playlist_dialog = jQuery( "#edit-playlist-dialog-form" ).dialog({
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
		  addUser();
		});

		// hide the main area content when a thumbnail is clicked,
			// so we can watch the video in the modal,
			// this prevents the glitch of appending the modal below the 
			// browsed content
			jQuery('.ytplus-page-main').undelegate( '.youtube-plus-video-preview-btn' , 'click' ).delegate( '.youtube-plus-video-preview-btn' , 'click' , function() {
				
				var video_id = jQuery(this).parents('.youtube-plus-video-single-list-item').find('.video_id').val();
				
				var the_title = jQuery('#TB_title').html();
				jQuery('#TB_title').remove();
				jQuery('#TB_ajaxContent').fadeOut( 'fast' , function() {
					jQuery('#TB_window').prepend('<input type="hidden" id="video_id" value="'+video_id+'"><a href="#" class="yt_plus_insert_video button-secondary" title="Insert Video" onclick="yt_plus_insert_video_to_editor(this);" style="position:absolute;top:0;left:0;margin-top:6em;margin-left:4em;"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a><a href="#" class="yt_plus_modal_back_to_videos button-secondary" title="Close" onclick="yt_plus_modal_back_to_playlist();" style="position:absolute;top:0;right:0;margin-top:6em;margin-right:4em;"><div class="dashicons dashicons-dismiss" style="line-height:1.3"></div></a>');
					jQuery('#TB_window').css('background','#1B1B1B');
					jQuery('#TB_window').prepend(the_title);
				});
				
			});
			
		/************************************************************/
		
		// Delete a video from 'Watch Later' Playlist
		// jQuery ui dialog functions
		jQuery('#browse_user_content_container').undelegate( '.remove_video_from_watch_later' , 'click' ).delegate( '.remove_video_from_watch_later' , 'click' , function() {
						
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
			
			
		/************************************************************/
		
		// Clear the entire watch later playlist
		jQuery('#browse_user_content_container').undelegate( '.yt-plus-clear-entire-playlist' , 'click' ).delegate( '.yt-plus-clear-entire-playlist' , 'click' , function() {			
									
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
			
		/******************************************************************/
				
		/* Add to Watch Later List Function */
		jQuery('#browse_user_content_container').undelegate( '.add_to_watch_later' , 'click' ).delegate( '.add_to_watch_later' , 'click' , function() {
			var ajaxurl = localized_data.admin_ajax_url;
			var preloader_url = localized_data.preloader_url;
			var watch_later_list_id = jQuery('#browse_user_content_container').find('#profile_sub_navigation').find('.sub-nav-button:last').attr('alt');
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
		
		
		/******************************************************************/
				
		/* Search user videos */
		jQuery('#browse_user_content_container').on( 'submit' , '#yt-plus-upload-search' , function() {
			return false;
		});
		// delay 1 second after last key is typed
		var timer;
		jQuery('#browse_user_content_container').on( 'keyup' , '#yt-plus-upload-search' , function() {
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
							},
							error: function(response) {
								console.log(response);
							}
						});
			}, 1000 );
			return false;
		});
	
			
			// timer to masonry the browse tab when on
			// page.php 
			var myInterval = setInterval(function() {
				if ( jQuery('#masonry-container').is(':visible') ) {

						/* Masonry the Results */
						jQuery('#masonry-container').masonry({
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
					
					// clear interval to prevent
					// function from continuously
					// firing
					clearInterval(myInterval);
					jQuery(window).resize(function() { jQuery('#masonry-container').masonry('reloadItems'); });
				}
				
			}, 25 );
						
	}); // end document ready
	
		// Insert the video into the editor , directly from the modal
		function yt_plus_insert_video_to_editor(clicked_element) {
				var video_id = jQuery(clicked_element).prev().val();
				// close the thickbox
				tb_remove();
				// insert the video into the active editor
				window.send_to_editor('[youtube-plus-video video_id="'+video_id+'"]');
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