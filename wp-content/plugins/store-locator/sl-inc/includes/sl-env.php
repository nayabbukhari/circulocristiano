<?php
if (!defined("DB_USER")){
	function sl_load_config($path, $lvl, $file="wp-config.php"){
		if ($lvl>30){die('reached 30 levels of search'); /*loop limit*/} else {$lvl++;}
		return file_exists($path."/".$file)? $path."/".$file : call_user_func(__FUNCTION__, "../".$path, $lvl);
	}
	include(sl_load_config(".", 0));
	$username=DB_USER;
	$password=DB_PASSWORD;
	$database=DB_NAME;
	$host=DB_HOST;
}

?>