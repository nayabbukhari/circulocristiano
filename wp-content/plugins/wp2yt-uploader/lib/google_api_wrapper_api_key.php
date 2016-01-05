<?php 
 // Call set_include_path() as needed to point to your client library.
	require_once YT4WP_PATH.'inc/Google/Client.php';
	require_once YT4WP_PATH.'inc/Google/Service/YouTube.php';

  /*
   * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
   * Google Developers Console <https://console.developers.google.com/>
   * Please ensure that you have enabled the YouTube Data API for your project.
   */
  $API_KEY = $this->optionVal['yt4wp-api-key'];

  $client = new Google_Client();
  $client->setDeveloperKey($API_KEY);

  // Define an object that will be used to make all API requests.
  $youtube = new Google_Service_YouTube($client);