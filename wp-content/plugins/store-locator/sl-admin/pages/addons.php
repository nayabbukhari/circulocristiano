<?php
//include("variables.sl.php");
include_once(SL_INCLUDES_PATH."/top-nav.php");
?>
<div class='wrap'>
<?php 
if (empty($_GET['pg'])) {
	if (function_exists("do_sl_addons_settings")){ do_sl_addons_settings(); }
} elseif (!empty($_GET['pg'])) {
	if (function_exists("do_sl_addons_{$_GET['pg']}")){ call_user_func("do_sl_addons_{$_GET['pg']}"); }
}

?>
</div>
<?php include(SL_INCLUDES_PATH."/sl-footer.php"); ?>