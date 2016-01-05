<div id="browse_user_content" class="top_level_container_youtube_plus_pro">
<?php $this->changeQueriedPlaylist($selected_list='',$clicked_button='',$screen_base=''); ?>
</div>

<?php 
	// create our edit video form
	$this->generateEditVideoForm();
	// create our edit playlist form
	$this->generateEditPlaylistForm();
?>