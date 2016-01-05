<h4><?php _e('Recently Viewed Posts','alimir'); ?></h4>
<?php
	$viewed_posts = get_user_meta($current_user->ID, 'alimir_viewed_posts', true);
	if (is_array($viewed_posts) && sizeof($viewed_posts)>0) :
		echo '<ul class="links">';
		$viewed_posts = array_reverse($viewed_posts);
		foreach ($viewed_posts as $viewed) :
			$viewed_post = get_post($viewed);
			if ($viewed_post) echo '<li><a href="'.get_permalink($viewed).'">'.$viewed_post->post_title.'</a></li>';
		endforeach;
		echo '</ul>';
	else :
		_e('You have not viewed any posts recently.', 'alimir');	
	endif;
?>