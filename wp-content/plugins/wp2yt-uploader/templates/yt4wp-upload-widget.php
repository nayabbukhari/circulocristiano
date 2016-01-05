<?php

// Creating the widget 

class yt_plus_upload_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'yt_plus_upload_widget', 

			// Widget name will appear in UI
			__('YouTube Plus : User Upload Widget', 'youtube-for-wordpress'), 

			// Widget description
			array( 'description' => __( 'Allow visitors to upload content from a simple sidebar widget.', 'youtube-for-wordpress' ), ) 
		);
	}
	
		// Creating widget front-end
		// This is where the action happens
		public function widget( $args, $instance ) {
				
		}
			
		// Widget Backend 
		 function form($instance) {    					
				 $YT4WPBase	= new YT4WPBase();
				?>
				<h3 style="width:100%;text-align:center;">Add-On Coming Soon</h3>
				<p>
					Allow visitors to upload videos directly to your YouTube account, where you can then review the video before publishing it.
					You can limit video submissions to logged in users and easily build a user submitted playlist to display around your site easily.
					That and much more in the professional version of YouTube Plus.
				</p>
				<?php
				
				echo $YT4WPBase->yt_plus_display_upsell_banner();
			
		}
		
} // Class yt4wp_MC_widget ends here

// Register and load the widget
function youtube_plus_upload_widget() {
	register_widget( 'yt_plus_upload_widget' );
}
add_action( 'widgets_init', 'youtube_plus_upload_widget' );