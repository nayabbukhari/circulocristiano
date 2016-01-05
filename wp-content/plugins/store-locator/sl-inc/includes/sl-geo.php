<?php
include('sl-env.php');
if (empty($_GET['sl_id']) || empty($_GET['lat']) || empty($_GET['lng']) || empty($_GET['_wpnonce'])) {
	die('Missing parameters');
} elseif ( !wp_verify_nonce($_GET['_wpnonce'], 'second-pass-geo_'.$_GET['sl_id']) ) {
	die('Security check');
} else {
	$wpdb->query("UPDATE ".SL_DB_PREFIX."store_locator SET sl_latitude = '".esc_sql($_GET['lat'])."', sl_longitude = '".esc_sql($_GET['lng'])."' WHERE sl_id = '".esc_sql($_GET['sl_id'])."' ");
	print "Successful Update via second-pass geocoding";
}
?>