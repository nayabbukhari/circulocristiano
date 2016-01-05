<?php
/*
This page gets included from lib.ajax and then processes
the post. This page should never get called by itself.
*/
if(!empty($_POST)
&& isset($_POST['form_action']))
	{	
	switch($_POST['form_action'])
		{
			
/** YouTube for WordPress Ajax Requests **/

default:
	echo '-1';
	break;

/*
* Update YouTube Settings
	- oauth2 key, oauth2 secret, api key etc.
*/
case 'update_options':
	$action	= $YT4WPBase->updateOptions($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
	
/*
* Update YouTube License Options
*/	
case 'update_license_options':
	$action	= $YT4WPBase->updateLicenseOptions($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
	
/*
* Update YouTube Debug Options
*/	
case 'update_error_log_count_option':
	$action	= $YT4WPBase->updateErrorLogCountOption( $_POST['new_count'] );
	die();
	break;

/*
* Reset YouTube Plus Plugins Back to Default
*/		
case 'yt_plus_reset_plugin_settings':
	$validate_action = $YT4WPBase->yt_plus_resetPluginSettings($_POST);
	break;	
	
// Search YouTube	
case 'youtube_plus_search_youtube':
	$get_subscribers = $YT4WPBase->searchYouTube($_POST['search_term'],$_POST['max_results'],$_POST['search_type'],$_POST['sort_results_by'],$_POST['advanced_search_checkbox'],$_POST['upload_date_timeframe'],$_POST['screen_base']);
	break;
	
// Search User Uploads	
case 'youtube_plus_search_user_uploads':
	$get_subscribers = $YT4WPBase->searchUserUploads($_POST['search_term'],$_POST['channel_id'],$_POST['screen_base']);
	break;
	
case 'change_queried_playlist':
	$get_subscribers = $YT4WPBase->changeQueriedPlaylist($_POST['selected_list'],$_POST['clicked_button'],$_POST['screen_base']);
	break;	
	
case 'get_users_playlist_items':
	$get_playlist_items = $YT4WPBase->getUsersPlaylistItems($_POST['playlist_id'],$_POST['playlist_title'],$_POST['screen_base'],$_POST['current_tab']);
	break;

case 'get_subsctiptions_playlist_items':
	$get_playlist_items = $YT4WPBase->getSubscriptionPlaylistItems($_POST['playlist_id'],$_POST['playlist_title'],$_POST['screen_base']);
	break;
	
case 'back_to_playlists':
	$get_playlist_items = $YT4WPBase->backToPlaylists($_POST['screen_base']);
	break;	
	
case 'get_subscription_videos':
	$get_playlist_items = $YT4WPBase->getSubscriptionVideos($_POST['channel_id'],$_POST['clicked_subscription'],$_POST['user_name'],$_POST['screen_base'],$_POST['current_tab']);
	break;
	
case 'get_subscription_playlists':
	$get_playlist_items = $YT4WPBase->getSubscriptionPlaylists($_POST['clicked_subscription'],$_POST['channel_id'],$_POST['screen_base']);
	break;
	
case 'reload_subscriptions':
	$get_playlist_items = $YT4WPBase->reloadSubscriptions($_POST['screen_base']);
	break;		
	
case 'get_channel_playlists':
	$get_playlist_items = $YT4WPBase->getChannelPlaylists($_POST['channel_id'],$_POST['clicked_subscription'],$_POST['user_name'],$_POST['screen_base']);
	break;	
	
case 'create_new_playlist':
	$get_playlist_items = $YT4WPBase->createNewPlaylist($_POST['screen_base'],$_POST['new_playlist_title'],$_POST['new_playlist_description']);
	break;	
	
case 'yt_plus_edit_video':
	$get_playlist_items = $YT4WPBase->editExistingVideo($_POST['screen_base'],$_POST['video_title'],$_POST['video_description'],$_POST['video_id'],$_POST['video_tags'],$_POST['video_category'],$_POST['video_privacy']);
	break;	
	
case 'get_users_videos':
	$get_playlist_items = $YT4WPBase->getUsersLikesAndFavoritesVideos($_POST['playlist_id'],$_POST['playlist_title'],$_POST['screen_base']);
	break;	
	
case 'youtube_plus_paginate_search_youtube':
	$get_playlist_items = $YT4WPBase->paginate_youtube_search($_POST['page_token'],$_POST['search_term'],$_POST['max_results'],$_POST['search_type'],$_POST['sort_results_by'],$_POST['advanced_search_checkbox'],$_POST['upload_date_timeframe'],$_POST['screen_base']);
	break;	
	
case 'youtube_plus_ajax_enqueue_all_scripts':
	$ajax_enqueue_scripts = $YT4WPBase->ajaxEnqueueScripts();
	break;	
	
case 'youtube_plus_paginate_browse_youtube':
	$paginate_youtube_browse = $YT4WPBase->paginate_youtube_browse($_POST['page_token'],$_POST['screen_base'],$_POST['current_tab'],$_POST['playlist_id']);
	break;	
	
case 'youtube_plus_paginate_subscriptions_youtube':
	$paginate_youtube_browse = $YT4WPBase->paginate_youtube_subscriptions($_POST['page_token'],$_POST['screen_base']);
	break;	
	
case 'youtube_plus_paginate_search_youtube_frontend':
	$get_playlist_items = $YT4WPBase->paginate_youtube_search_frontend($_POST['page_token'],$_POST['search_term'],$_POST['max_results'],$_POST['screen_base']);
	break;
	
case 'upload_content_to_YouTube':
	$upload_new_video = $YT4WPBase->newUploadToYouTube($_POST['video_location'],$_POST['video_titlte'],$_POST['video_description'],$_POST['screen_base']);
	die();
	break;		

case 'edit_video_button_click':
	$create_video_post = $YT4WPBase->getSelectedVideoResponse($_POST['video_id']);
	break;	
	
case 'delete_video_button_click':
	$create_video_post = $YT4WPBase->deleteUserVideo($_POST['video_id']);
	break;
	
case 'delete_video_from_playlist_button_click':
	$delete_video_from_playlist = $YT4WPBase->deleteVideoFromPlaylist($_POST['playlistItem_id']);
	break;	
	
case 'delete_selected_playlist':
	$delete_selected_playlist = $YT4WPBase->deleteSelectedPlaylist($_POST['playlist_id']);
	break;	
	
case 'edit_playlist_button_click':
	$create_video_post = $YT4WPBase->getSelectedPlaylistResponse($_POST['playlist_id']);
	break;		
	
case 'yt_plus_update_playlist':
	$delete_selected_playlist = $YT4WPBase->editExistingPlaylist($_POST['screen_base'],$_POST['playlist_title'],$_POST['playlist_description'],$_POST['playlist_id'],$_POST['playlist_tags'],$_POST['playlist_privacy']);
	break;	
	
case 'yt_plus_get_users_channels_and_playlists_dropdown':
	$users_channel_playlist_dropdown = $YT4WPBase->getUsersPlaylistAndChannelsDropdown($_POST['query_param'],$_POST['playlist_id']);
	break;	
	
case 'youtube_plus_add_video_to_watch_later':
	$add_to_watch_later_list = $YT4WPBase->addVideoToWatchLaterList($_POST['watch_later_list_id'],$_POST['video_id']);
	break;	
	
case 'youtube_plus_add_channel_subscription':
	$add_subscription = $YT4WPBase->addChannelToSubscriptions($_POST['channel_id']);
	break;	
	
case 'youtube_plus_remove_channel_subscription':
	$remove_subscription = $YT4WPBase->removeChannelSubscription($_POST['channel_id']);
	break;	
	
case 'youtube_plus_update_video_container':
	$update_single_video_container = $YT4WPBase->updateVideoContainer($_POST['video_id'],$_POST['screen_base']);
	break;	
	
case 'youtube_plus_update_playlist_container':
	$update_single_playlist_container = $YT4WPBase->updatePlaylistContainer($_POST['playlist_id'],$_POST['screen_base']);
	break;	
	
case 'get_YouTube_Plus_Main':
	$youtube_plus_main = $YT4WPBase->getYouTubePlusMain($_POST['screen_base']);
	break;	
		
/* Log the user out so they can switch accounts properly */
case 'logout_and_revoke_access_token':
	$revoke_uesr_access_token = $YT4WPBase->logOutAndRevokeAccessToken($_POST['access_token']);
		break;
		
/* Log the user out so they can switch accounts properly */
case 'load_tab_content':
	$loadTabDataAjax = $YT4WPBase->loadTabDataAjax($_POST['clicked_tab_no_hash']);
		break;
		
/* Log the user out so they can switch accounts properly */
case 'yt_plus_revoke_user_permissions':
	$loadTabDataAjax = $YT4WPBase->logOutAndRevokeAccessToken($_POST['access_token']);
		break;
		
/* Delete the contents of our error log */
case 'clear_yt4wp_error_log':
	$clear_yt4wp_error_log = $YT4WPBase->clear_yt4wp_error_log();
		break;
			
		}
	}
?>