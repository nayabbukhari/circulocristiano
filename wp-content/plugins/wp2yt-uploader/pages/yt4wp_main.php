<?php

// todo --
	// implement tagging (nice styled)
	// when user types tag and hits comma
	// should auto create tag, etc. for sending to youtube

// used to dictate the active tab
$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'youtube_plus_upload';

// get the current screen base name
$screen_base = get_current_screen()->base;

?>

<div class="wrap ytplus-page-main">
	<?php 
		// Display the Help/Contact Banner
		$this->yt_plus_contact_support_banner(); 
	?>
	
	<h2 id="yt4wp-page-header">
		<div id="yt4wp-icon" class="icon32"></div>
		<?php _e('YouTube for WordPress','youtube-for-wordpress-translation'); ?>
	</h2>
	
	
	<style>
		.price-row {
			top:0; 
			max-width:
		}
		.price-head {
			padding-bottom: 0;
			padding-top:2px;
		}
		.price-detail, .price-content {
			display:none;
		}
		.price-value {
			font-size: 20px;
		}
		.price-row .dashicons {
			height: auto !important;
			width: auto !important;
			font-size: 2em !important;	
		}
	</style>
		
	<?php
		// if we are on the YouTube+ Pro Main Page
		// menu item
		if ( $screen_base == 'toplevel_page_youtube-for-wordpress' ) {
			$refresh_token = get_option( 'yt4wp_user_refresh_token' );
	?>
		<!-- tabs -->
		<!-- add hook here for new menu items -->
		<h2 class="nav-tab-wrapper yt-plus-translation-text-domain-nav-tab-wrapper">
			<a href="?page=youtube-for-wordpress&tab=youtube_plus_upload" class="nav-tab <?php echo $active_tab == 'youtube_plus_upload' ? 'nav-tab-active' : ''; ?>"><?php _e('Upload','youtube-for-wordpress-translation'); ?></a>
			<a class="nav-tab <?php echo $active_tab == 'youtube_plus_browse' ? 'nav-tab-active' : ''; ?>" <?php if($refresh_token == '' && !isset($_GET['code'])) { ?>style="opacity:.45;" disabled="disabled;" onclick="return false;" href="#"<?php } else { ?> href="?page=youtube-for-wordpress&tab=youtube_plus_browse" <?php } ?>><?php _e('Browse','youtube-for-wordpress-translation'); ?></a>
			<a class="nav-tab <?php echo $active_tab == 'youtube_plus_search' ? 'nav-tab-active' : ''; ?>" <?php if($refresh_token == '' && !isset($_GET['code']) ) { ?>style="opacity:.45;" disabled="disabled;" onclick="return false;" href="#"<?php } else { ?> href="?page=youtube-for-wordpress&tab=youtube_plus_search"  <?php } ?>><?php _e('Search','youtube-for-wordpress-translation'); ?></a>
			<a class="nav-tab <?php echo $active_tab == 'youtube_subscriptions' ? 'nav-tab-active' : ''; ?>" <?php if($refresh_token == '' && !isset($_GET['code']) ) { ?>style="opacity:.45;" disabled="disabled;" onclick="return false;" href="#"<?php } else { ?> href="?page=youtube-for-wordpress&tab=youtube_subscriptions" <?php } ?>><?php _e('Subscriptions','youtube-for-wordpress-translation'); ?></a>
		</h2>
	<?php
		
		// Debug
			
			if ( $active_tab == 'youtube_plus_upload' ) { 
				// Upload Content
				include YT4WP_PATH . 'inc/upload_content.php'; 
			} 
			
			if ( $active_tab == 'youtube_plus_browse' ) { 			
				// Browse Authenticated User Uploads
				include YT4WP_PATH . 'inc/browse_user_content.php'; 
			}
			
			if ( $active_tab == 'youtube_plus_search' ) { 
				// Search YouTube Content
				include YT4WP_PATH . 'inc/search_youtube.php'; 
			}
			
			if ( $active_tab == 'youtube_subscriptions' ) { 
				// Search YouTube Content
				include YT4WP_PATH . 'inc/youtube_subscriptions.php'; 
			}
	
	
		} else {
		// this is the modal
		// on the add new post or page
		
		// create our edit video form
		$this->generateEditVideoForm();
		// create our edit playlist form
		$this->generateEditPlaylistForm();
	?>
	
		<script type="text/javascript">
		/* Fake our Ajax inside Modals */
		jQuery(document).ready(function() {
		
			/* 
				Interval to re-style the thickbox for our plugin only 
				- may be better alternatives for this
			*/
				var i = 0;
				var timed_thickbox_check = setInterval(function() {	
					if ( jQuery( '.ytplus-page-main' ).is( ':visible' ) && jQuery( '.yt4wp_tb_window_style' ).length == 0 ) {
						 jQuery('#TB_ajaxContent').prepend("<div class='yt4wp_tb_window_style'><style>#TB_window {"+
								"width: 80% !important;"+
								"margin-left:-38% !important;"+
								"margin-top: -1.2em !important;"+
							"}"+
							"#TB_ajaxContent {"+
								"width: 100% !important;"+
								"height:95.5% !important;"+
								"padding: 0 !important;"+
							"}"+
							"#TB_ajaxContent .ytplus-page-main {"+
								"padding: 15px;"+
							"}</style></div>");
							i = 1;
					} else if ( jQuery( '.ytplus-page-main' ).length == 0 && jQuery( '.yt4wp_tb_window_style' ).length == 1 ) {
						jQuery( '.yt4wp_tb_window_style' ).remove();
						i = 0;
					}
				}, 15);

			
			jQuery('.nav-tab').on( 'click' , function(event) {
					
				var clicked_button = jQuery(this).text();
					
				if ( jQuery(this).hasClass( 'disabled-tab' ) ) {
				
					event.preventDefault();
					
				} else {
													
					// add disabled class to prevent duplicate clicks
					jQuery( '.nav-tab' ).each(function() {
						jQuery(this).addClass('disabled-tab');
					});
					
					jQuery( '.hidden_content' ).each(function() {
						if ( jQuery(this).attr( 'id' ) != 'upload_content' ) {
							jQuery(this).html('');
						}
					});
					
					var clicked_tab = '#'+jQuery(this).attr('alt');
					var preloader_url = '<?php echo admin_url("/images/wpspin_light.gif"); ?>';
					jQuery('.nav-tab').removeClass('nav-tab-active');
					jQuery(this).addClass('nav-tab-active');
					jQuery('.hidden_content').hide();
					
					if ( jQuery(this).attr('alt') != 'upload_content' ) {	
						jQuery(clicked_tab).html('<img alt="youtube-plus-preloader" class="youTube_api_key_preloader" src="'+preloader_url+'">').show();
					} else {
						jQuery( '#upload_content' ).before('<img alt="youtube-plus-preloader" class="youTube_api_key_preloader" src="'+preloader_url+'">');
					}
				}
								
			});

		});	
		
		</script>
		<!-- tabs -->
		<h2 class="nav-tab-wrapper">
			<a href="#" onclick="return false;" class="nav-tab nav-tab-active" alt="upload_content"><?php _e('Upload','youtube-for-wordpress-translation'); ?></a>
			<a href="#" onclick="return false;" class="nav-tab nav-tab" alt="browse_user_content"><?php _e('Browse','youtube-for-wordpress-translation'); ?></a>
			<a href="#" onclick="return false;" class="nav-tab nav-tab" alt="search_youtube"><?php _e('Search','youtube-for-wordpress-translation'); ?></a>
			<a href="#" onclick="return false;" class="nav-tab nav-tab" alt="youtube_subscriptions"><?php _e('Subscriptions','youtube-for-wordpress-translation'); ?></a>
		</h2>
		
		<div id="upload_content" class="hidden_content"><?php
			include YT4WP_PATH . 'inc/upload_content.php'; 
		?></div>
		
		<div id="browse_user_content" style="display:none;" class="hidden_content"></div>
		
		<div id="search_youtube" style="display:none;" class="hidden_content"></div>
		
		<div id="youtube_subscriptions" style="display:none;" class="hidden_content"></div>
		
	<?php
		}
	?>	
	
</div>