jQuery(document).ready(function() {
	
	/** Masonry **/	
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
			
	jQuery(window).resize(function() { jQuery('#masonry-container').masonry('reloadItems'); });
	
		
	
	jQuery('#subscribtions_box').undelegate( '.view-subscription-videos-btn' , 'click' ).delegate( '.view-subscription-videos-btn' , 'click' , function() {
			
			// alert('view subscriptions uploads button clicked');			
			
			var channel_id = jQuery(this).attr('alt');
			var clicked_subscription = jQuery(this).attr('alt');
			var user_name = jQuery(this).attr('title');

			var screen_base = localized_data.screen_base;
			var preloader_url = localized_data.preloader_url;
			
			jQuery('#subscribtions_box').find('ul').not(':first-child').html('<img class="youTube_api_key_preloader" src="'+preloader_url+'" alt="preloader" >');
			jQuery('#subscribtions_box').find('h3').text('');
			
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
	
	
	/***************************************************************/
	/**
	*	Delete Video From Subscriptions (unsubscribe)
	**/
	jQuery('#subscribtions_box').undelegate( '.yt-plus-unsubscribe-to-channel' , 'click' ).delegate( '.yt-plus-unsubscribe-to-channel' , 'click' , function() {
			
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
	
	
});