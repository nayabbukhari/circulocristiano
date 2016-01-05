<?php
if (empty($_GET['pg'])) {
	include(SL_PAGES_PATH."/news-upgrades.php");
} else {
	$the_page = SL_PAGES_PATH."/".$_GET['pg'].".php";
	if (file_exists($the_page)) {
		include($the_page);
	}
}
?>