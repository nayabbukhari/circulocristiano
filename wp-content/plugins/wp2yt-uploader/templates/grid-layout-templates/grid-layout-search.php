<?php

// Front end layout
// for our Search grid 
// @since v2.0

// enqueue masonry for layouts
wp_enqueue_script( 'masonry' , array('jquery') );

// include the required php files - containers api key
include_once YT4WP_PATH.'lib/google_api_wrapper_api_key.php';
	
// include object buffer
ob_start();

$searchResponse = $youtube->search->listSearch('id,snippet', array(
		  'q' => stripslashes( $search_term ),
		  'maxResults' => '25',
		  'order' => 'relevance',
		  'regionCode' => $this->optionVal['yt4wp-region'],
		  'type' => 'video'
		));

// display our page title + search term
echo '<h4>Showing results for "' . stripslashes( $search_term ) . '"</h4>';
	
		echo '<ul id="masonry-container">';
			
			foreach ($searchResponse['items'] as $searchResult) {
			
				// setup the description
				if($searchResult['modelData']['snippet']['description']) {
					// trim the description
					// if there are more than 400 characters
					if(strlen($searchResult['modelData']['snippet']['description']) > 325) {
						$video_description = '<b class="yt4wp-video-description" style="text-decoration:underline;">Description</b> <br />' . substr($searchResult['modelData']['snippet']['description'], 0, 400).'...'; 
					} else {
						$video_description = '<b class="yt4wp-video-description" style="text-decoration:underline;">Description</b> <br />' . $searchResult['modelData']['snippet']['description']; 
					}
				} else {
					$video_description = ''; 
				}
			
				?>
				<li class="yt4wp-video-single-list-item">
					<input type="hidden" class="video_id" value="<?php echo $searchResult['id']['videoId']; ?>">
						<a class="fancybox" data-type="iframe" href="http://www.youtube.com/embed/<?php echo $searchResult['id']['videoId']; ?>?autoplay=1" title="<?php echo $searchResult['snippet']['title']; ?>">	
						<img class="yt4wp-video-thumbnail" src="<?php echo $searchResult['snippet']['thumbnails']['high']['url'];?>"></a>
						<h3 class="yt4wp-video-title-container"><?php echo $searchResult['snippet']['title']; ?></h3> 
					<span class="yt4wp-video-description-container"><?php echo $video_description; ?></span>
					<p>&nbsp;</p> <!-- spacer -->
				</li>	
				<?php
			}
			
		echo '</ul>';
		
?>
<!-- 
Initialize the masonry script 
	- run it after images have fully loaded to prevent layout issues
-->
<script type="text/javascript">
	 jQuery(document).ready(function() {		
			/* Masonry the Results */
			jQuery('#masonry-container').masonry().imagesLoaded( function() {
				jQuery('#masonry-container').masonry({
				  itemSelector: '.yt4wp-video-single-list-item',
				  gutter: 35,
				 // options...
				  isAnimated: true,
				  animationOptions: {
					duration: 350,
					easing: 'linear',
					queue: false
				  }
			});
		});
								
		jQuery(window).resize(function() { jQuery('#masonry-container').masonry('reloadItems'); });
	
		<!-- initialize fancybox  -->
		jQuery(".fancybox").on("click", function(){
			jQuery.fancybox({
				href: this.href,
				type: jQuery(this).data("type")
			}); // fancybox
			return false;  
		}); // on
	
	});
</script>
		

<?php 
	
	// return the contents of our search grid
	return ob_get_clean(); 
	
?>