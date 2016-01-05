<?php $htmlBody = ''; 

if ( get_option( 'yt4wp_user_refresh_token' ) == '' ) {
	?><style>.yt4wp-error-alert:before { padding:0 !important; line-height: 1 !important; padding-right: 5px !important; }</style><?php
	wp_die( '<span id="response_message" class="yt4wp-error-alert"><strong>Woah Woah Woah...</strong> It looks like you haven\'t authenticated yet. You\'ll first need to authenticate yourself before you can go any further.</span>');
}

?>
<style>#search_youtube .edit_video_button, #search_youtube .youtube-plus-delete-video-button { display: none !important; }</style>
<form id="youtube_plus_search_form" method="GET">
  <input type="hidden" id="page" name="page" value="youtube-plus-pro">
  <!-- get and store our watch later list ID -->
  <input type="hidden" id="watchLaterListID" name="watchLaterListID" value="<?php echo $this->getUserWatchLaterListId(); ?>">
  <div class="searchContainer">
   <input type="search" id="yt-plus-upload-search" name="q" placeholder="Enter Search Term (Leave blank to search all of YouTube)"  <?php if ( isset($_GET['q']) ) { ?> value="<?php echo $_GET['q']; ?>"<?php } ?> autocomplete="off"><label class="yt-plus-search-form-label" for="yt-plus-upload-search">Search</label>
  </div>
  <section id="advanced_search_container">
	  <div class="advanced_search_setting">
		<div class="dashicons dashicons-info advanced_search_info" title="Select how many results you would like to display per page (max 50)"></div>Max Results: <input type="number" id="maxResults" name="maxResults" min="1" max="50" step="1" <?php if ( isset($_GET['maxResults']) ) { ?> value="<?php echo $_GET['maxResults']; ?>"<?php } else { ?> value="25" <?php } ?>>
	  </div>
	  <div class="advanced_search_setting">
		<div class="dashicons dashicons-info advanced_search_info" title="Select what you would like to search"></div>Search Type: 
		<select id="search_type_dropdown" name="search_type_dropdown">
			<option value="video" name="search_type_dropdown" <?php if ( isset( $_GET['search_type_dropdown'] ) && $_GET['search_type_dropdown'] == 'video' ) echo 'selected="selected"'; ?>>Videos</option>
			<option value="channel" name="search_type_dropdown" <?php if ( isset( $_GET['search_type_dropdown'] ) && $_GET['search_type_dropdown'] == 'channel' ) echo 'selected="selected"'; ?>>Channels</option>
			<option value="playlist" name="search_type_dropdown" <?php if ( isset( $_GET['search_type_dropdown'] ) && $_GET['search_type_dropdown'] == 'playlist' ) echo 'selected="selected"'; ?>>Playlists</option>
		</select>
	  </div>
	  <div class="advanced_search_setting">
		<div class="dashicons dashicons-info advanced_search_info" title="Select how you would like to sort your results"></div>Sort By:
		<select id="order_results_by_dropdown" name="order_results_by_dropdown">
			<option value="relevance" name="order_results_by_dropdown" <?php if ( isset( $_GET['order_results_by_dropdown'] ) && $_GET['order_results_by_dropdown'] == 'relevance' ) echo 'selected="selected"'; ?>>Relevance</option>
			<option value="rating" name="order_results_by_dropdown" <?php if ( isset( $_GET['order_results_by_dropdown'] ) && $_GET['order_results_by_dropdown'] == 'rating' ) echo 'selected="selected"'; ?>>Rating</option>
			<option value="viewCount" name="order_results_by_dropdown" <?php if ( isset( $_GET['order_results_by_dropdown'] ) && $_GET['order_results_by_dropdown'] == 'viewCount' ) echo 'selected="selected"'; ?>>View Count</option>
			<option value="videoCount" name="order_results_by_dropdown" <?php if ( isset( $_GET['order_results_by_dropdown'] ) && $_GET['order_results_by_dropdown'] == 'videoCount' ) echo 'selected="selected"'; ?> style="display:none;">Video Count</option>			
			<option value="date" name="order_results_by_dropdown" <?php if ( isset( $_GET['order_results_by_dropdown'] ) && $_GET['order_results_by_dropdown'] == 'date' ) echo 'selected="selected"'; ?>>Date</option>
			<option value="title" name="order_results_by_dropdown" <?php if ( isset( $_GET['order_results_by_dropdown'] ) && $_GET['order_results_by_dropdown'] == 'title' ) echo 'selected="selected"'; ?>>Title</option>
		</select>
	  </div>
	  <div class="advanced_search_setting upload_date_filter">
		<div class="dashicons dashicons-info advanced_search_info" title="Narrow down your search results by time frame"></div>Time Frame:
		<select id="upload_date_timeframe" name="upload_date_timeframe">
			<option value="all_time" name="order_results_by_dropdown" <?php if ( isset( $_GET['upload_date_timeframe'] ) && $_GET['upload_date_timeframe'] == 'all_time' ) echo 'selected="selected"'; ?>>All Time</option>
			<option value="past_hour" name="order_results_by_dropdown" <?php if ( isset( $_GET['upload_date_timeframe'] ) && $_GET['upload_date_timeframe'] == 'past_hour' ) echo 'selected="selected"'; ?>>Last Hour</option>
			<option value="past_day" name="order_results_by_dropdown" <?php if ( isset( $_GET['upload_date_timeframe'] ) && $_GET['upload_date_timeframe'] == 'past_day' ) echo 'selected="selected"'; ?>>Today</option>
			<option value="past_week" name="order_results_by_dropdown" <?php if ( isset( $_GET['upload_date_timeframe'] ) && $_GET['upload_date_timeframe'] == 'past_week' ) echo 'selected="selected"'; ?>>This Week</option>
			<option value="past_month" name="order_results_by_dropdown" <?php if ( isset( $_GET['upload_date_timeframe'] ) && $_GET['upload_date_timeframe'] == 'past_month' ) echo 'selected="selected"'; ?> style="display:none;">This Month</option>			
			<option value="past_year" name="order_results_by_dropdown" <?php if ( isset( $_GET['upload_date_timeframe'] ) && $_GET['upload_date_timeframe'] == 'past_year' ) echo 'selected="selected"'; ?>>This Year</option>
		</select>
	  </div>
	</section>
	<label for="advanced_search" class="advanced_search_label"><input type="checkbox" name="advanced_search" id="advanced_search" value="advanced_search"><div class="dashicons dashicons-admin-generic"></div> Advanced Search</label><br />
  <input type="submit" value="Search" class="button-primary">
  <!-- watch later list ID -->
  <input type="hidden" id="watch_later_list_id" value="<?php echo $this->getUserWatchLaterListId(); ?>">
  </form>

<hr />


	
<div id="search_results">
	<span style="display:block;width:100%;margin-top:6.5em;text-align:center;"><em>Type something in the search box above to get started...</em></span>
</div>