<?php
add_action('wp_ajax_yt_plus_settings_form', 'ytplus_ajaxActions');
// add_action('wp_ajax_nopriv_yt_plus_settings_form', 'ytplus_ajaxActions');

function ytplus_ajaxActions()
	{
		global $YT4WPBase;
		require_once YT4WP_PATH.'process/ajax.php';
		exit;
	}
		
?>