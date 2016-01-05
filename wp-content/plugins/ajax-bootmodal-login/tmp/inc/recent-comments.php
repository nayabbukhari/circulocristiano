<h4><?php _e('Recent Comments','alimir'); ?></h4>
<?php
	global $wpdb;
	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
		comment_post_ID, user_id, comment_approved, comment_date_gmt,
		comment_type, SUBSTRING(comment_content,1,30) AS com_excerpt
		FROM $wpdb->comments
		LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
		$wpdb->posts.ID)
		WHERE comment_approved = '1' AND comment_type = '' AND
		post_password = '' AND user_id = '".$current_user->ID."'
		ORDER BY comment_date_gmt DESC LIMIT 5";
	$comments = $wpdb->get_results($sql);
	if ($comments) :
		echo '<ul>';
		foreach ($comments as $comment) :
			echo '<li><a href="'.get_permalink($comment->ID).'#comment-'.$comment->comment_ID.'">&ldquo;'.strip_tags($comment->com_excerpt).'&hellip;&rdquo;</a></li>';
		endforeach;
		echo '</ul>';
	else :
		_e('You have not made any comment.', 'alimir');	
	endif;
?>