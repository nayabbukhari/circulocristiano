O��V<?php exit; ?>a:6:{s:10:"last_error";s:0:"";s:10:"last_query";s:417:"SELECT   wptz_posts.ID FROM wptz_posts  INNER JOIN wptz_postmeta ON ( wptz_posts.ID = wptz_postmeta.post_id ) WHERE 1=1  AND ( 
  ( wptz_postmeta.meta_key = 'membership_id' AND CAST(wptz_postmeta.meta_value AS CHAR) = '51' )
) AND wptz_posts.post_type = 'ms_relationship-n' AND ((wptz_posts.post_status <> 'trash' AND wptz_posts.post_status <> 'auto-draft')) GROUP BY wptz_posts.ID ORDER BY wptz_posts.post_date DESC ";s:11:"last_result";a:0:{}s:8:"col_info";a:1:{i:0;O:8:"stdClass":13:{s:4:"name";s:2:"ID";s:5:"table";s:10:"wptz_posts";s:3:"def";s:0:"";s:10:"max_length";i:0;s:8:"not_null";i:1;s:11:"primary_key";i:0;s:12:"multiple_key";i:0;s:10:"unique_key";i:0;s:7:"numeric";i:1;s:4:"blob";i:0;s:4:"type";s:3:"int";s:8:"unsigned";i:1;s:8:"zerofill";i:0;}}s:8:"num_rows";i:0;s:10:"return_val";i:0;}