<?php
/*
	Addons page, display add-ons for purchase, linking to our site
*/

	// used to dictate the active tab
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'yt-plus-add_ons';
	
?>

<style>
.cta:nth-child(2) {
	display: none !important;
}
.wrap #yt_plus_review_this_plugin_container {
	top: 3.25em;
}
#yt4wp-icon {
	height: 47px;
	width: 47px;
}
</style>

<div class="wrap ytplus-page-about">
	
		<?php 
			// Display the Help/Contact Banner
			$this->yt_plus_contact_support_banner(); 
		?>
			
	<h2 id="yt4wp-page-header" style="margin-bottom:0;">
		<div id="yt4wp-icon" class="icon32"></div>
		<?php _e('YouTube for WordPress - Add Ons','youtube-for-wordpress-translation'); ?>
	</h2>
	
	<h2 class="nav-tab-wrapper" style="margin-top:0;">
		<a href="?page=youtube-for-wordpress-add-ons" class="nav-tab <?php echo $active_tab == 'yt-plus-add_ons' ? 'nav-tab-active' : ''; ?>"><?php _e('Add Ons','youtube-for-wordpress-translation'); ?></a>
		<a href="?page=youtube-for-wordpress-themes" disabled="disabled" onclick="return false;" class="yt-plus_themes_tab nav-tab <?php echo $active_tab == 'yt-plus-themes' ? 'nav-tab-active' : ''; ?>" ><?php _e('Themes (coming soon)','youtube-for-wordpress-translation'); ?></a>
	</h2>
	
	<p class="about-text">
		<?php printf( __( 'Purchase premium add ons to extend the functionality of what YouTube for WordPress can do out of the box.', 'youtube-for-wordpress-translation' ), $this->optionVal['version'] ); ?>
	</p>
		
	<div id="content_wrap" class="col-md-9">
		
		<div id="add_on_div" class="add_on_div col-md-4">
			<h2>User Uploads<span class="user_favorite" title="Popular Item"><div class="dashicons dashicons-awards" title="Popular Item"></div></span></h2>
			<p class="addon-description">Allow users to upload content to your account and monitor what gets accepted and what doesn't. Easily add upload forms through shortcodes and widgets. Features email notifications, an admin management interface and seammless front end integration.</p>
			<input type="submit" class="purchase-add-on-button" value="coming soon">
		</div>
		
		<div id="add_on_div" class="add_on_div col-md-4">
			<h2>YouTube Analytics</h2>
			<p class="addon-description">Mointor the success of your YouTube videos with important statistics such as view count, likes, favorites and much more. Statistics are viewable through interactive charts and printable reports.</p>
			<input type="submit" class="purchase-add-on-button" value="coming soon">
		</div>
		
		<div id="add_on_div" class="add_on_div col-md-4">
			<h2>Player Customizer</h2>
			<p class="addon-description">Customize the embedded player to perfectly match the look and feel of your site. Add your logo, a watermark, change colors and more.</p>
			<input type="submit" class="purchase-add-on-button" value="coming soon">
		</div>
		
		<div id="add_on_div" class="add_on_div col-md-4">
			<h2>Post Creation<span class="user_favorite" title="Popular Item"><div class="dashicons dashicons-awards" title="Popular Item"></div></span></h2>
			<p class="addon-description">Create beautiful posts from YouTube videos with the click of a button.</p>
			<input type="submit" class="purchase-add-on-button" value="coming soon">
		</div>
		
		<!--
		<div id="add_on_div" class="add_on_div col-md-4">
			<span>Add On Image Here</span>
			<h2>PayPal Payments</h2>
			<p class="addon-description">Accept payments from users before allowing them to upload content to the site.</p>
			<input type="submit" class="purchase-add-on-button" value="coming soon">
		</div>
		-->
		
		<div id="add_on_div" class="add_on_div col-md-4">
			<h2>Playlist Builder<span class="user_favorite" title="Popular Item"><div class="dashicons dashicons-awards" title="Popular Item"></div></span></h2>
			<p class="addon-description">Create, build and re-arrange playlists on the fly and then insert them into posts or pages with ease.</p>
			<input type="submit" class="purchase-add-on-button" value="coming soon">
		</div>
		
		<div id="add_on_div" class="add_on_div col-md-4">
			<h2>Mass Import</h2>
			<p class="addon-description">Import and create seperate posts for each video on a channel or in a playlist at the click of a button.</p>
			<input type="submit" class="purchase-add-on-button" value="coming soon">
		</div>
		
		<div id="add_on_div" class="add_on_div col-md-4">
			<h2>Automatic Account Monitoring</h2>
			<p class="addon-description">Monitor your favorite YouTube users, and everytime they upload a video your site will automagically create a post for the video in the background...even while you sleep! The ultimate automation tool for video blogs.</p>
			<input type="submit" class="purchase-add-on-button" value="coming soon">
		</div>
		
				
	</div>
	
		
	<?php 
		/** Display the upsell banner **/
		$this->yt_plus_display_upsell_banner();	
	?>
	
	
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
	
	
	<?php 
	/*
		Cross domain get Add-ons here!
		
		$url = 'http://www.yt4wp.com/wp-json/posts/1';
		$get_it = wp_remote_get( $url );
		print_r($get_it);
	*/
	?>
	
</div>