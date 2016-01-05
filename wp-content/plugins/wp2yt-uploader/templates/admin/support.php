<style>
.cta:nth-child(2),.cta:nth-child(3) {
	display: none;
}
.support-sub-text {
	font-size: 16px;
}
.wrap #yt_plus_review_this_plugin_container {
	top: .75em;
}
</style>

	
<div class="wrap ytplus-page-about">

		<?php 
			// Display the Help/Contact Banner
			$this->yt_plus_contact_support_banner(); 
		?>
			
	<h2 id="yt4wp-page-header" style="margin-bottom:0;">
		<div id="yt4wp-icon" class="icon32"></div>
		<?php _e('YouTube for WordPress - Support','youtube-for-wordpress-translation'); ?>
	</h2>
	
	<hr />
		
	<div id="content_wrap" class="col-md-9" style="display:block;float:left;">
		
		<div style="float:left;width:75%;">
			<p class="support-sub-text">
				<?php _e( 'Thank you for installing and trying out YouTube for WordPress. Free users receive limited support. Please consider purchasing a support license to access our support ticketing system.', 'youtube-for-wordpress-translation' ); ?>
			</p>
			
			<p class="support-sub-text">
				<?php printf( __( "If you're running into issues, please reach out for support. We're working to make this plugin better, so your inqueries and comments are invaluable.", "youtube-for-wordpress-translation" ), $this->optionVal['version'] ); ?>
			</p>
			
			
				<p style="margin-top:2em;"><strong>E-mail - </strong><a href="mailto:support@yt4wp.com">Support@yt4wp.com</a></p>
				<p><strong>WordPress Support Forum - </strong><a href="https://wordpress.org/support/plugin/wp2yt-uploader" target="_blank">WordPress.org</a></p>
				<p style="opacity:.6;"><strong>Support Forum - </strong><a href="#" onclick="return false;">Coming Soon</a></p>
				<p>&nbsp;</p>
				<p><em>note : when receiving support, we will most likely ask for information from the <a href="<?php echo admin_url( 'admin.php?page=youtube-for-wordpress-settings&tab=debug_settings#yt4wp-youtube-form-debug-options' ); ?>" title="Debug Settings">debug settings page</a>.</em></p>
	
			
		</div>
		
		<?php 
			/** Display the upsell banner **/
			$this->yt_plus_display_upsell_banner();	
		?>
		
		
	</div>
		
	<hr style="display:block;width:100%;float:left;margin:2.5em 0;" /> 
	
	<span style="float:right">
		<p>
			<?php 
				$release_cycle_url = 'http://www.yt4wp.com/roadmap/';
				$release_notice_link = sprintf( __( 'Interested in release dates? Follow along with the <a href="%s">release cycle</a>.', 'youtube-for-wordpress-translation' ), esc_url( $release_cycle_url ) );
				echo $release_notice_link;
			?>
		</p>
	</span>

	
</div>