O��V<?php exit; ?>a:6:{s:10:"last_error";s:0:"";s:10:"last_query";s:233:"
				SELECT ID
				FROM wptz_posts p
				INNER JOIN wptz_postmeta m_type ON m_type.post_id = p.ID
				WHERE
					p.post_type = 'ms_membership-n'
					AND m_type.meta_key = 'type'
					AND m_type.meta_value = 'searchindex'
			";s:11:"last_result";a:1:{i:0;O:8:"stdClass":1:{s:2:"ID";s:2:"67";}}s:8:"col_info";a:1:{i:0;O:8:"stdClass":13:{s:4:"name";s:2:"ID";s:5:"table";s:1:"p";s:3:"def";s:0:"";s:10:"max_length";i:2;s:8:"not_null";i:1;s:11:"primary_key";i:1;s:12:"multiple_key";i:0;s:10:"unique_key";i:0;s:7:"numeric";i:1;s:4:"blob";i:0;s:4:"type";s:3:"int";s:8:"unsigned";i:1;s:8:"zerofill";i:0;}}s:8:"num_rows";i:1;s:10:"return_val";i:1;}