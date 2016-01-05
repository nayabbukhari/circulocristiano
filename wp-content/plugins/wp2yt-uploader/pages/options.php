<?php 
	// enqueue jQuery dialog, for warning alerts
	wp_enqueue_script('jquery-ui-dialog');
?>
<style>
.media-upload-form div.error, .wrap div.error, .wrap div.updated {
	margin: auto !important;
}
</style>
	
<script type="text/javascript">
/* 
* Options Page Script 
* Handles save functions
*/
jQuery(document).ready(function () {
	
	/*
	* Clear our response message upon
	* saving the options
	*/
	function clear_update_option_message() {
		window.clearTimeout(timeoutHandle);
		var timeoutHandle = setTimeout(function(){
										jQuery('#yt4wp-status').fadeOut('fast');
									},6000);
	};

	// ajax save the WordPress Plugin Options Page
	// YouTube Settings Page
    jQuery('#yt4wp-youtube-form').submit(function (e) {	      
		
            jQuery('#yt4wp-status').slideUp('fast');
			var oauth2_id = jQuery( '#yt4wp-oauth2-key' ).val();
			var oauth2_secret = jQuery( '#yt4wp-oauth2-secret' ).val();
			var api_key = jQuery( '#yt4wp-api-key' ).val();
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yt_plus_settings_form',
						form_action: 'update_options',
						form_data: jQuery('#yt4wp-youtube-form').serialize()
					},
					dataType: 'json',
					success: function (MAILCHIMP) {
						if (MAILCHIMP == '1') {	
							jQuery('#yt4wp-status').html('<div class=updated><p><span class="dashicons dashicons-yes" style="line-height:1;"></span><?php _e('Options saved successfully.', 'youtube-for-wordpress'); ?></p></div>');
							jQuery('#yt4wp-status').slideDown('fast');
							clear_update_option_message();
						} else {
							jQuery('#yt4wp-status').html("<div class=error><p><span class='dashicons dashicons-no-alt' style='line-height:1;'></span><?php _e("No changes have been made.", "yt-plus-translation-text-domain"); ?></p></div>");
							jQuery('#yt4wp-status').slideDown('fast');
							clear_update_option_message();
							console.log(MAILCHIMP);
						}
					},
					error : function(MAILCHIMP2) {
						console.log(MAILCHIMP2.responseText);
					}
				});
       e.preventDefault();
    });
	
		
	// ajax save the YouTube for WordPress License Options
	// License Options Page
    jQuery('#yt4wp-youtube-form-license-options').submit(function (e) {	   
        // Make sure the api key exists
            jQuery('#yt4wp-status').slideUp('fast');
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yt_plus_settings_form',
						form_action: 'update_license_options',
						form_data: jQuery('#yt4wp-youtube-form-license-options').serialize()
					},
					dataType: 'json',
					success: function (response) {
						if (response == '1') {	
							jQuery('#yt4wp-status').html('<div class=updated><p><?php _e('Options saved successfully.', 'youtube-for-wordpress'); ?></p></div>');
							jQuery('#yt4wp-status').slideDown('fast');
							clear_update_option_message()
						} else {
							jQuery('#yt4wp-status').html("<div class=error><p><span class='dashicons dashicons-no-alt' style='line-height:1;'></span><?php _e("No changes have been made.", "yt-plus-translation-text-domain"); ?></p></div>");
							jQuery('#yt4wp-status').slideDown('fast');
							clear_update_option_message()
							console.log(response);
						}
					},
					error : function(error_response) {
						console.log(error_response.responseText);
					}
				});
       e.preventDefault();
    });
	
	/*******************	Reset Plugin Ajax Request ****************************/	
	
	jQuery('#yt4wp-mc-reset-plugin-settings').click(function(e) {
		jQuery("<div id='yt_plus_reset_plugin_settings' style='height:auto !important;'><div class='yt4wp-mc-icon-yt4wp-mc-warning yt4wp-mc-reset-warning-icon'></div><p style='float:left;width:80%;margin-top:5px;'><?php _e("Are you sure you want to reset YouTube for WordPress back to default? This cannot be undone.", "yt-plus-translation-text-domain" ); ?></p></div>").dialog({
		 dialogClass: 'ui-dialog-yt-plus',
		 title : "Reset YouTube for WordPress Settings?",
		 width: 400,
		 height: 200,
		 resizable: false,
		 buttons : {
			"Yes" : function() {
				 jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yt_plus_settings_form',
						form_action: 'yt_plus_reset_plugin_settings'
					},
					dataType: 'json',
					success: function () {
						jQuery( "#yt_plus_reset_plugin_settings" ).html('<div class="dashicons dashicons-yes yt4wp-mc-success-icon"></div><p style="float:left;width:80%;"><?php _e("YouTube for WordPress settings have successfully been reset", "yt-plus-translation-text-domain" ); ?></p><span class="yt4wp-mc-reset-plugin-settings-preloader-container"><img class="yt4wp-mc-reset-plugin-settings-preloader" src="<?php echo admin_url().'images/wpspin_light.gif'; ?>" alt="preloader" /></span>');
						jQuery( "#yt_plus_reset_plugin_settings" ).next().hide();
						jQuery( "#yt_plus_reset_plugin_settings" ).prev().text("Success!");
						setTimeout(function() {	
							location.reload();
						}, 2000);
					},
					error: function() {
						alert('Error resetting plugin settings. If the error persists, uninstall and reinstall the plugin to reset your options.');
					}
				});
			},
			"Cancel" : function() {
			  jQuery(this).dialog("close");
			}
		  },
		  modal: true,
		  resizable: false
		});
		e.preventDefault();
	});
		
	/* Revoke Permissions From the User */
	jQuery('#yt4wp-mc-logout-revoke-permissions').click(function(e) {
		jQuery("<div id='yt_plus_revoke_permissions' style='height:auto !important;'><div class='yt4wp-mc-icon-yt4wp-mc-warning yt4wp-mc-reset-warning-icon'></div><p style='float:left;width:80%;margin-top:5px;'><?php _e("Are you sure you want to revoke permissions? This cannot be undone and you will need to re-authenticate to regain access.", "yt-plus-translation-text-domain" ); ?></p></div>").dialog({
		 dialogClass: 'ui-dialog-yt-plus',
		 title : "Revoke YouTube for WordPress Permissons?",
		 width: 400,
		 height: 200,
		 resizable: false,
		 buttons : {
			"Yes" : function() {
				 jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'yt_plus_settings_form',
						form_action: 'yt_plus_revoke_user_permissions',
						access_token: '<?php echo $this->optionVal['yt4wp-oauth2-key']; ?>'
					},
					dataType: 'json',
					success: function () {
						jQuery( "#yt_plus_revoke_permissions" ).html('<div class="dashicons dashicons-yes yt4wp-mc-success-icon"></div><p style="float:left;width:80%;"><?php _e("YouTube for WordPress permissions have successfully been revoked. You can now re-authenticate.", "yt-plus-translation-text-domain" ); ?></p><span class="yt4wp-mc-reset-plugin-settings-preloader-container"></span>');
						jQuery('#yt4wp-mc-logout-revoke-permissions').attr('disabled','disabled');
						jQuery( "#yt_plus_revoke_permissions" ).next().find('button:first').remove();
						jQuery( "#yt_plus_revoke_permissions" ).next().find('button:last').find('span').text('Close');
					},
					error: function() {
						jQuery('body.youtube_page_youtube-for-wordpress-settings').find('#yt4wp-youtube-form').before('<span id="response_message" class="yt4wp-error-alert" style="display:none;width:100%;"><p>Error revoking permissions. If the error persists, please contact the YouTube for WordPress support team.</p></span>');
						jQuery(this).dialog("close");
						jQuery('.yt4wp-error-alert').slideDown();
						window.scrollTo(0, 0);
					}
				});
			},
			"Cancel" : function() {
			  jQuery(this).dialog("close");
			}
		  },
		  modal: true,
		  resizable: false
		});
		e.preventDefault();
	});
	
	/* 
	* Clear our Error Log 
	*
	* since v2.1.2
	*/
	jQuery( 'body' ).on( 'click' , '.clear-yt4wp-error-log' , function() {
		
		jQuery( '#yt4wp-error-log-table' ).fadeTo( 'fast' , .5 );
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'yt_plus_settings_form',
				form_action: 'clear_yt4wp_error_log'
			},
			dataType: 'json',
			success: function (response) {
				setTimeout(function() {	
					jQuery( '#yt4wp-error-log-table' ).fadeOut( 'fast' , function() {
						jQuery( '.clear-yt4wp-error-log' ).attr( 'disabled' , 'disabled' );
						setTimeout(function() {
							jQuery( '.yt4wp-error-log-table-row' ).html( '<em>no errors logged</em>' );
						}, 250 );
					});
				}, 1000 );
			},
			error : function(error_response) {
				alert( 'There was an error with your request. Unable to clear the erorr log!' );
				console.log(error_response.responseText);
				jQuery( '#yt4wp-error-log-table' ).fadeTo( 'fast' , 1 );
			}
		});
	});
	
	/* Toggle Visibility of OAUTH2 ID/Client Secret and API Keys (for viewing/troubleshooting) */
	jQuery( 'body' ).on( 'click' , '.view-obfuscated-text' , function() {
		if( jQuery( this ).prev().attr( 'type' ) == 'password' ) {
			jQuery( this ).prev().removeAttr( 'type' );
			jQuery( this ).prev().attr( 'type' , 'text' );
		} else {
			jQuery( this ).prev().removeAttr( 'type' );
			jQuery( this ).prev().attr( 'type' , 'password' );
		}
	});
	
	/* Update Error log count on blur */
	jQuery( 'body' ).on( 'blur' , '.yt4wp-limit-error-log' , function() {
		
		jQuery( '.yt4wp-limit-error-log' ).after( '<img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" class="yt4wp-update-limit-preloader" style="margin-top:6px;">' );
		// ajax update count
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'yt_plus_settings_form',
				form_action: 'update_error_log_count_option',
				new_count: jQuery( this ).val()
			},
			dataType: 'json',
			success: function (response) {
				jQuery( '.yt4wp-limit-error-log' ).val( response );
				jQuery( '.yt4wp-update-limit-preloader' ).fadeOut( 'fast' , function() {
					jQuery( this ).replaceWith( '<span class="dashicons dashicons-yes yt4wp-update-limit-success-check" style="color:#7EBF5B;" style="margin-top:8px;"></span>' );
					setTimeout(function() {
						jQuery( '.yt4wp-update-limit-success-check' ).fadeOut( 'fast' , function() {
							jQuery( this ).remove();
						});
					}, 2000);
				});
				console.log(response);
			},
			error : function(error_response) {
				console.log(error_response.responseText);
			}
		});
	});
	
	/* Toggle the text of the Auto Background Update checkbox */
	jQuery( 'body' ).on( 'click' , '#yt4wp-auto-background-updates' , function() {
		if( jQuery( this ).is( ':checked' ) ) {
			jQuery( '.yt4wp-auto-background-updates-description' ).text( ' Yes, keep YouTube for WordPress up to date at all times.' );
		} else {
			jQuery( '.yt4wp-auto-background-updates-description' ).text( ' No, I will keep YouTube for WordPress up to date myself.' );
		}
	});
		
});

</script>

<!-- get and store our api key option -->
<?php
	$api_key_option = get_option( 'api_validation' );
	$wordPress_version = get_bloginfo( 'version' );
	
	// get the SQL and PHP versions 
	$php_version = phpversion();
	
	// set up the options for our WYSIWYG editors
	// for the optin messages
	$single_optin_message_parameters = array(
		'teeny' => true,
		'textarea_rows' => 15,
		'tabindex' => 1,
		'textarea_name' => 'single-optin-message',
		'drag_drop_upload' => true
	);
	
	$double_optin_message_parameters = array(
		'teeny' => true,
		'textarea_rows' => 15,
		'tabindex' => 1,
		'textarea_name' => 'double-optin-message',
		'drag_drop_upload' => true
	);
	
	// used to dictate the active tab
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'youtube_settings';
?>
<div class="wrap">

<?php 
	// Display the Help/Contact Banner
	$this->yt_plus_contact_support_banner(); 
?>

<h2 id="yt4wp-page-header">
	<div id="yt4wp-icon" class="icon32"></div><?php _e('YouTube for WordPress','youtube-for-wordpress'); ?>
</h2>

<!-- tabs -->
<h2 class="nav-tab-wrapper">
    <a href="?page=youtube-for-wordpress-settings&tab=youtube_settings" class="nav-tab <?php echo $active_tab == 'youtube_settings' ? 'nav-tab-active' : ''; ?>"><?php _e('YouTube Settings','youtube-for-wordpress'); ?></a>
	<a href="?page=youtube-for-wordpress-settings&tab=debug_settings" class="nav-tab <?php echo $active_tab == 'debug_settings' ? 'nav-tab-active' : ''; ?>" ><?php _e('Debug Settings','youtube-for-wordpress'); ?></a>
	<a href="?page=youtube-for-wordpress-settings&tab=license_settings" class="nav-tab <?php echo $active_tab == 'license_settings' ? 'nav-tab-active' : ''; ?>" ><?php _e('Support License','youtube-for-wordpress'); ?></a>
	<?php do_action( 'youtube_for_wordpress_addon_settings_tabs' , $active_tab ); ?>
</h2>
	
	<div class="yt4wp-status" id="yt4wp-status"></div>
	
<?php if ( $active_tab == 'youtube_settings' ) { ?>
	
	<br />
	
	<h2><?php _e('YouTube for WordPress Settings','youtube-for-wordpress'); ?></h2>

	<!-- WordPress version number and SSL error checking -->
	<!-- check WordPress version num. and display an error if its outdated -->
	<?php if ( $wordPress_version < '3.9' ) { ?>
		<div class="error">
			<h3><div class="dashicons dashicons-no yt_plus_error_x"></div><?php _e( 'WordPress Version Number Error', 'youtube-for-wordpress' ); ?></h3>
			<p><?php _e( 'We\'re sorry, but it looks like your using an outdated version of WordPress. You won\'t be able to access the tinyMCE button to insert forms into pages and posts unless you update to 3.9 or later.', 'youtube-for-wordpress' ); ?></p>
		</div>
	<?php } 
	
		
	// check if the user is on localhost
	// if so, they need to enable SSL on localhost
	if ( $this->yt4wp_is_user_localhost() ) {
	?>
		<div class="update-nag" style="margin-bottom:2.5em;width:97.5% !important;">
			<span class="yt4wp-mc-icon-notice"><h3><?php _e( 'LocalHost Detected :', 'youtube-for-wordpress' ); ?></h3></span>
			<p><?php _e( 'It looks like your using YouTube for WordPress on localhost.', 'youtube-for-wordpress' ); ?></p>
			<p><?php _e( 'You will want to setup a <strong>global API key</strong> and set your <strong>redirect URI</strong> as ', 'youtube-for-wordpress' ); ?><?php echo '<em>"' . admin_url() . 'admin.php?page=youtube-for-wordpress"</em>'; ?></p>
		</div>
	<?php }

	// Check the Users PHP Version Numbers 
	if ( $php_version < '5.3' ) {
	?>
		<div class="update-nag" style="margin-bottom:2.5em;width:97.5% !important;">
			<span class="yt4wp-mc-icon-notice"><h3><?php _e( 'Outdated Version of PHP :', 'youtube-for-wordpress' ); ?></h3></span>
			<p><?php _e( 'It looks like your site is running an outdated version of PHP. YouTube for WordPress requires a minimum of PHP 5.3.', 'youtube-for-wordpress' ); ?></p>
			<p><?php _e( 'Your site is currently running PHP v.', 'youtube-for-wordpress' ); echo $php_version; ?></p>
		</div>
	<?php 
	}  
	?>
	
	<form method="post" name="yt4wp-youtube-form" id="yt4wp-youtube-form">
		<table class="form-table yt4wp-admin-form">
			
			<a style="margin-left:0;" href="http://yt4wp.com/support/documentation/setup/setup-google-project/?utm_source=yt4wp-settings-page&utm_medium=button&utm_campaign=yt4wp-settings-page" target="_blank" class="settings-setup-link"><?php _e('Setup Guide','youtube-for-wordpress'); ?></a>	
			<a href="https://console.developers.google.com" target="_blank" class="settings-setup-link"><?php _e('Google Developer Console','youtube-for-wordpress'); ?></a>
			
			<tbody>				
				<!-- YouTube OAUTH2 Key Field -->
				<tr valign="top">
					<th scope="row"><label for="yt4wp-oauth2-key"><?php _e('Google OAUTH2 Client ID','youtube-for-wordpress'); ?></label></th>
					<td><input name="yt4wp-oauth2-key" type="password" autofill="off" id="yt4wp-oauth2-key" value="<?php echo $this->optionVal['yt4wp-oauth2-key']; ?>" class="regular-text" /><span class="dashicons dashicons-visibility view-obfuscated-text"></span></span>
					</td>
				</tr>
				<!-- YouTube OAUTH2 Secret Key Field -->
				<tr valign="top">
					<th scope="row"><label for="yt4wp-oauth2-secret"><?php _e('Google OAUTH2 Client Secret','youtube-for-wordpress'); ?></label></th>
					<td><input name="yt4wp-oauth2-secret" type="password" autofill="off" id="yt4wp-oauth2-secret" value="<?php echo $this->optionVal['yt4wp-oauth2-secret']; ?>" class="regular-text" /><span class="dashicons dashicons-visibility view-obfuscated-text"></span></span>
					</td>
				</tr>
				<!-- YouTube OAUTH2 Description -->
				<tr>
					<td></td>
					<td class="yt4wp-settings-description">
						<?php _e('Please enter your Google OAUTH2 Keys above.','youtube-for-wordpress'); ?>
					</td>
				</tr>
				<!-- YouTube API Key Field -->
				<tr valign="top">
					<th scope="row"><label for="yt4wp-api-key"><?php _e('Google Public API Key','youtube-for-wordpress'); ?></label></th>
					<td><input name="yt4wp-api-key" type="password" id="yt4wp-api-key" autofill="off" value="<?php echo $this->optionVal['yt4wp-api-key']; ?>" class="regular-text" /><span class="dashicons dashicons-visibility view-obfuscated-text"></span></span>
					</td>
				</tr>
				<!-- YouTube API Key Description -->
				<tr>
					<td></td>
					<td class="yt4wp-settings-description">
						<?php _e('Please enter your Google API Key above.','youtube-for-wordpress'); ?><br />
					</td>
				</tr>
				
				<?php if ( get_option( 'yt4wp_user_refresh_token' ) != '' ) { ?>
				
					<!-- YouTube Region Field -->
					<tr valign="top">
						<th scope="row"><label for="yt4wp-region"><?php _e('Select Your Region','youtube-for-wordpress'); ?></label></th>
						<td><?php $this->generateRegionDropdown(); ?></span>
						</td>
					</tr>
					<!-- YouTube Region Field Description -->
					<tr>
						<td></td>
						<td class="yt4wp-settings-description">
							<?php _e('Your region will determine some data returned from the API. For example, when searching, YouTube will return videos from your country first.','youtube-for-wordpress'); ?><br />
						</td>
					</tr>
					<tr>
						<td></td>
						<td class="yt4wp-settings-description">
							<?php _e('Note : Use Wordlwide if you are in the United States.','youtube-for-wordpress'); ?><br />
						</td>
					</tr>
					<!-- YouTube Language Field -->
					<tr valign="top">
						<th scope="row"><label for="yt4wp-language"><?php _e('Select Your Language','youtube-for-wordpress'); ?></label></th>
						<td><?php $this->generateLanguageDropdown(); ?></span>
						</td>
					</tr>
					<!-- YouTube Language Field Description -->
					<tr>
						<td></td>
						<td class="yt4wp-settings-description">
							<?php _e('This will determine the language of some data returned from the YouTube API (ie: category names).','youtube-for-wordpress'); ?><br />
						</td>
					</tr>
				
				<?php } ?>
				
				<!-- YouTube Embed Player Options -->
				<tr valign="top">
					<th scope="row"><label for="yt4wp-embed-player-style"><?php _e('YouTube Player Style','youtube-for-wordpress'); ?></label></th>
					<td>
						<select name="yt4wp-embed-player-style" id="yt4wp-embed-player-style" class="regular-text" style="width:300px;">
							<option value="yt-default"<?php echo ($this->optionVal['yt4wp-embed-player-style'] === 'yt-default' ? ' selected' : ''); ?>><?php _e('YouTube Default','youtube-for-wordpress'); ?></option>
							<option value="wp-mediaelement"<?php echo ($this->optionVal['yt4wp-embed-player-style'] === 'wp-mediaelement' ? ' selected' : ''); ?>><?php _e('Media Element Player','youtube-for-wordpress'); ?></option>
						</select>
					</td>
				</tr>
				<!-- YouTube API Key Description -->
				<tr>
					<td></td>
					<td class="yt4wp-settings-description">
						<?php _e('Select your video style above.','youtube-for-wordpress'); ?><br />
					</td>
				</tr>		
				<!-- YouTube Statistics Inclusion Setting -->
				<tr valign="top">
					<th scope="row"><label for="yt4wp-embed-player-style"><?php _e('Display Video Statistics','youtube-for-wordpress'); ?></label></th>
					<td>
						<select name="yt4wp-include-stat-count-in-query" id="yt4wp-include-stat-count-in-query" class="regular-text" style="width:300px;">
							<option value="stat-count-enabled"<?php echo ($this->optionVal['yt4wp-include-stat-count-in-query'] === 'stat-count-enabled' ? ' selected' : ''); ?>><?php _e('Enable Video Stats','youtube-for-wordpress'); ?></option>
							<option value="stat-count-disabled"<?php echo ($this->optionVal['yt4wp-include-stat-count-in-query'] === 'stat-count-disabled' ? ' selected' : ''); ?>><?php _e('Disable Video Stats','youtube-for-wordpress'); ?></option>
						</select>
					</td>
				</tr>
				<!-- YouTube Statistics Inclusion Setting Description -->
				<tr>
					<td></td>
					<td class="yt4wp-settings-description">
						<?php _e('Select if you would like the video statistics to be displayed below videos. Video stats include view count, dislikes, likes and favorites.','youtube-for-wordpress'); ?><br />
						<em style="display:block;margin-top:1em;"><?php _e('Note : including stats will significantly increase page load times in the dashboard.','youtube-for-wordpress'); ?></em>
					</td>
				</tr>	
				<!-- YouTube for WordPress automatic Plugin Updates -->
				<tr valign="top">
					<th scope="row"><label for="yt4wp-embed-player-style"><?php _e('Background Updates','youtube-for-wordpress'); ?></label></th>
					<td>
						<label for="yt4wp-auto-background-updates"><input type="checkbox" name="yt4wp-auto-background-updates" id="yt4wp-auto-background-updates" class="yt4wp-auto-background-updates" value="1" <?php checked( $this->optionVal['yt4wp-auto-background-updates'] , '1' ); ?>><?php if ( $this->optionVal['yt4wp-auto-background-updates'] == '1' ) { ?><span class="yt4wp-auto-background-updates-description"> <?php _e( 'Yes, keep YouTube for WordPress up to date at all times.' , 'youtube-for-wordpress' ); ?></span><?php } else { ?><span class="yt4wp-auto-background-updates-description"> <?php _e( 'No, I will keep YouTube for WordPress up to date myself.' , 'youtube-for-wordpress' ); ?></span><?php } ?></label>
					</td>
				</tr>
				<!-- YouTube Statistics Inclusion Setting Description -->
				<tr>
					<td></td>
					<td class="yt4wp-settings-description">
						<?php _e('Enable this setting if you would like <strong>YouTube for WordPress</strong> to be automatically updated. This will ensure you always have the most up to date version of the plugin.','youtube-for-wordpress'); ?><br />
					</td>
				</tr>	
				<!-- submit button -->
				<tr>
					<td></td>
					<td><input type="submit" name="submit" id="submit" class="purchase-add-on-button" value="Save Settings" style="width:150px;height:33px;"><input type="submit" name="yt4wp-mc-reset-plugin-settings" id="yt4wp-mc-reset-plugin-settings" class="reset-plugin-settings-button" value="Reset Plugin"><input style="float:right;" type="submit" name="yt4wp-mc-logout-revoke-permissions" id="yt4wp-mc-logout-revoke-permissions" class="yt4wp-mc-button-revoke yt4wp-revoke-permissions revoke-permissions-button" <?php if( get_option( 'yt4wp_user_refresh_token' ) == '' ) { echo 'disabled=disabled'; } ?> value="Revoke Permissions"></td>
				</tr>	
			
			</tbody>
		</table>
	</form>
	
		<?php 
			/** Display the upsell banner **/
			$this->yt_plus_display_upsell_banner();	
		?>

	<?php } else if ( $active_tab == 'debug_settings' ) { ?>
		
		<br />
		
		<h2><?php _e('Debug Settings','youtube-for-wordpress'); ?></h2>
		
		<form method="post" name="yt4wp-youtube-form" id="yt4wp-youtube-form-debug-options" onsubmit="return false;">
			<table class="form-table yt4wp-admin-form">
				<tbody>
				<table class="form-table yt4wp-admin-form">
					<tbody>
						<!-- Plugin Info -->
						<h3><?php _e('Plugin Information','youtube-for-wordpress'); ?></h3>
						<!-- Issues? Contact Us. -->
						<p>
							<?php _e('If you experience any issues with YouTube for WordPress, please ','youtube-for-wordpress'); ?> <a href="http://www.yt4wp.com/support/?utm_source=yt4wp-debug-settings&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank"><?php _e('submit a new ticket','youtube-for-wordpress'); ?></a> <?php _e('with the YouTube for WordPress support team','youtube-for-wordpress'); ?>. <?php _e('You will need to be a support license holder to receive any level of support. Please include the information below and any eror messages or error numbers to help us troubleshoot your problem.','youtube-for-wordpress'); ?>
						</p>
						<!-- User Debug Section -->
						<!-- Plugin Version, Browser Version etc. -->
						<tr valign="top">
							<th scope="row"><label><?php _e('Plugin Version','youtube-for-wordpress'); ?></label></th>
							<td><?php echo YT4WP_VERSION_CURRENT; ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Wordpress Version','youtube-for-wordpress'); ?></label></th>
							<td>
							<?php 
								$wordpress_version = get_bloginfo( 'version' );
								if ( $wordpress_version < '3.9' ) {
									echo '<div class="dashicons dashicons-no-alt" style="margin-right:2em;color:rgb(205, 90, 90);"></div>' . $wordpress_version;
								} else {
									echo '<div class="dashicons dashicons-yes" style="margin-right:2em;color:#7EBF5B;"></div>' . $wordpress_version;
								}							
							?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('PHP Version','youtube-for-wordpress'); ?></label></th>
							<td>
								<?php	
								if ( $php_version < '5.3' ) {
									echo '<div class="dashicons dashicons-no-alt" style="margin-right:2em;color:rgb(205, 90, 90);"></div>' . $php_version ;
								} else {
									echo '<div class="dashicons dashicons-yes" style="margin-right:2em;color:#7EBF5B;"></div>' . $php_version;
								}
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Browser Information','youtube-for-wordpress'); ?></label></th>
							<td>
								<?php
								$theBrowser = $this->getBrowser();
								echo $theBrowser['name'].' '.$theBrowser['version'].' on '.$theBrowser['platform'];					
								?>
							</td>
						</tr>
						<!-- check contents of our error log -->
						<?php $error_file_contents = $this->yt4wp_generate_error_log_table(); ?>
						<tr valign="top">
						
							<th scope="row"><label style="display:block;width:100%;margin-bottom:.5em;"><?php _e('Error Log','youtube-for-wordpress'); ?></label><a href="#" onclick="return false;" class="button-secondary clear-yt4wp-error-log" <?php if ( !$error_file_contents ) { ?> disabled="disabled" <?php } ?>><?php _e( 'clear log' , 'youtube-for-wordpress' ); ?></a><p style="font-weight:400;">Increase/Decrease number of errors to keep at one time</p><input type="number" max="20" min="5" name="yt4wp-limit-error-log" class="yt4wp-limit-error-log" value="<?php echo isset( $this->optionVal['yt4wp-limit-error-log-count'] ) ? $this->optionVal['yt4wp-limit-error-log-count'] : '5'; ?>" style="display:block;width:75px;"></th>
							<td class="yt4wp-error-log-table-row">
								
								<?php 
									if ( $error_file_contents ) {
										?>
										<!-- error log table -->
											<table cellspacing='0' id='yt4wp-error-log-table'> <!-- cellspacing='0' is important, must stay -->
												<!-- Table Header -->
												<thead>
													<tr>
														<th><?php _e( "Error Message" , "yt-plus-translation-text-domain" ); ?></th>
														<th><?php _e( "Error Number" , "yt-plus-translation-text-domain" ); ?></th>
														<th><?php _e( "Time" , "yt-plus-translation-text-domain" ); ?></th>
													</tr>
												</thead>
												<!-- Table Header -->

												<!-- Table Body -->
												<tbody>
													<?php 
														// dump the contents of the error log
														print_r( $error_file_contents );
													?>
												</tbody>
												<!-- Table Body -->
												
											</table>
										<?php
									} else {
										echo '<em>' . __( 'no errors logged' , 'youtube-for-wordpress' ) . '</em>';
									}	
								?>
							</td>
						</tr>
					</tbody>
				</table>
				
				</tbody>
			</table>
		</form>

			<?php 
				/** Display the upsell banner **/
				$this->yt_plus_display_upsell_banner();	
			?>

	<?php } else if ( $active_tab == 'license_settings' ) { ?>
		
			<br />
		
			<h2><?php _e('Support License','youtube-for-wordpress'); ?></h2>
						
			<form method="post" name="yt4wp-youtube-form" id="yt4wp-youtube-form-license-options">
								
				<table class="form-table yt4wp-admin-form" style="margin-bottom:4em;">
					<tbody>				
						<!-- YouTube for WordPress License Key Field -->
						<tr valign="top">
							<th scope="row"><label for="yt4wp-license-key"><?php _e('Support License Key','youtube-for-wordpress'); ?></label></th>
							<td><input name="yt4wp-license-key" disabled="disabled" placeholder="Coming Soon" type="text" id="yt4wp-license-key" value="<?php echo $this->optionVal['yt4wp-license-key']; ?>" class="regular-text" style="width:100%;min-width:500px;" /></span>
							</td>
						</tr>
						<!-- YouTube for WordPress License Description -->
						<tr>
							<td></td>
							<td class="yt4wp-settings-description">
								<?php _e('The license key is used for access to automatic upgrades and support. Please see the' , 'youtube-for-wordpress'); ?> <a href="http://www.yt4wp.com/buy-yt4wp/?utm_source=ytwp-support-license-settings&utm_medium=text-link&utm_campaign=get-support" target="_blank"><?php _e( 'support' , 'youtube-for-wordpress'); ?></a> <?php _e( 'page for further details about purchasing a support license.','youtube-for-wordpress'); ?>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" name="submit" id="submit" class="purchase-add-on-button" value="Save Settings" style="width:150px;height:33px;"></td>
						</tr>
					</tbody>
				</table>
			
			</form>
			
			
			
			<?php 
				/** Display the upsell banner **/
				$this->yt_plus_display_upsell_banner();	
			?>
	
	<?php }

	do_action( 'youtube_plus_addon_settings_page' , $active_tab ); ?>
	
</div>