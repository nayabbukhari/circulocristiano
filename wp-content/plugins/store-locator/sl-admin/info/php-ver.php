<?php 

$is_included_pv=(basename(__FILE__) != basename($_SERVER['SCRIPT_FILENAME']) )? true : false;

if ($is_included_pv) {
	print "Current PHP Version is ".phpversion(); 
}
?>