<?php

	if (!empty($_GET['delete'])) {
		//If delete link is clicked
		if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], "delete-location_".$_GET['delete'])){
			$wpdb->query($wpdb->prepare("DELETE FROM ".SL_TABLE." WHERE sl_id='%d'", $_GET['delete'])); 
			sl_process_tags("", "delete", $_GET['delete']); 
		} /*elseif (empty($_GET['q'])) {
			print "<div class='sl_admin_warning'>Security Check doesn't validate for deleting this location, make sure to delete by clicking the 'Delete' link next to a specific location.</div>";
		}*/
	}
	if (!empty($_POST) && !empty($_GET['edit']) && $_POST['act']!="delete") {
		$field_value_str=""; 
		foreach ($_POST as $key=>$value) {
			if (preg_match("@\-$_GET[edit]@", $key)) {
				$key=str_replace("-$_GET[edit]", "", $key); // stripping off number at the end (giving problems when constructing address string below)
				if ($key=="sl_tags") {
					//print "before: $value <br><br>";
					$value=sl_prepare_tag_string($value);
					//print "after: $value \r\n"; die();
				}
				
				if (is_array($value)){
					$value=serialize($value); //for arrays being submitted
					$field_value_str.=$key."='$value',";
				} else {
					$field_value_str.=$key."=".$wpdb->prepare("%s", trim(comma(stripslashes($value)))).", "; 
				}
				$_POST["$key"]=$value; 
			}
		}
		
		$field_value_str=substr($field_value_str, 0, strlen($field_value_str)-2);
		$edit=$_GET['edit']; extract($_POST);
		$the_address="$sl_address, $sl_address2, $sl_city, $sl_state $sl_zip";
		
		if (empty($_POST['no_geocode']) || $_POST['no_geocode']!=1) { //no_geocode sent by addons that manually edit the the coordinates. Prevents sl_do_geocoding() from overwriting the manual edit.
			$old_address=$wpdb->get_results("SELECT * FROM ".SL_TABLE." WHERE sl_id='".esc_sql($_GET['edit'])."'", ARRAY_A); 
		}
		//die("UPDATE ".SL_TABLE." SET $field_value_str WHERE sl_id='%d'");
		
		//$wpdb->query($wpdb->prepare("UPDATE ".SL_TABLE." SET $field_value_str WHERE sl_id='%d'", $_GET['edit'])); 
		$wpdb->query($wpdb->prepare("UPDATE ".SL_TABLE." SET ".str_replace("%", "%%", $field_value_str)." WHERE sl_id='%d'", $_GET['edit']));  //Thank you WP user @kostofffan; fixes 'empty query' bug when user is trying to update location with a '%' sign in it
		
		if(!empty($_POST['sl_tags'])){sl_process_tags($_POST['sl_tags'], "insert", $_GET['edit']);}
		
		if ((empty($_POST['sl_longitude']) || $_POST['sl_longitude']==$old_address[0]['sl_longitude']) && (empty($_POST['sl_latitude']) || $_POST['sl_latitude']==$old_address[0]['sl_latitude'])) {
			if ($the_address!=$old_address[0]['sl_address']." ".$old_address[0]['sl_address2'].", ".$old_address[0]['sl_city'].", ".$old_address[0]['sl_state']." ".$old_address[0]['sl_zip'] || ($old_address[0]['sl_latitude']==="" || $old_address[0]['sl_longitude']==="")) {
				sl_do_geocoding($the_address,$_GET['edit']);
				if (!empty($GLOBALS['sdg_reply']) && $GLOBALS['sdg_reply'] == "1st_attempt") {
					//added - v3.73, 7/10/15 - refresh page here only if successful on first geocoding attempt; 2nd attempt refreshing handled in sl_do_geocoding()
					print "<script>location.replace('".str_replace("&edit=$_GET[edit]", "", $_SERVER['REQUEST_URI'])."');</script>";
				}
			} else {
				//added - v3.73, 7/10/15 - refresh page if nothing about address changes
				print "<script>location.replace('".str_replace("&edit=$_GET[edit]", "", $_SERVER['REQUEST_URI'])."');</script>";
			}
		}
		//commented out - v3.73, 7/10/15 - in order to allow time to view geocoding status message when updating single location
		//print "<script>location.replace('".str_replace("&edit=$_GET[edit]", "", $_SERVER['REQUEST_URI'])."');</script>";
	}
	
	if (!empty($_POST['act']) && !empty($_POST['sl_id']) && $_POST['act']=="delete") {
		//If bulk delete is used
		if (!empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], "manage-locations_bulk")){
			include(SL_ACTIONS_PATH."/deleteLocations.php");
		} else {
			print "<div class='sl_admin_warning'>Security check doesn't validate for bulk deletion of locations.</div>";
		}
	}
	if (!empty($_POST['act']) && !empty($_POST['sl_id']) && preg_match("@tag@", $_POST['act'])) {
		//if bulk tagging is used
		include(SL_ACTIONS_PATH."/tagLocations.php");
	}
	if (!empty($_POST['act']) && ($_POST['act']=='add_multi' || $_POST['act']=='remove_multi')) {
		//if bulk updating is used
		include(SL_ADDONS_PATH."/multiple-field-updater/multiLocationUpdate.php");
	}
	if (!empty($_POST['act']) && $_POST['act']=="locationsPerPage") {
		//If change in locations per page
		$sl_vars['admin_locations_per_page']=$_POST['sl_admin_locations_per_page'];
		sl_data('sl_vars', 'update', $sl_vars);
		extract($_POST);
	}
	if (!empty($_POST['act']) && $_POST['act']=="regeocode" && file_exists(SL_ADDONS_PATH."/csv-xml-importer-exporter/reGeo.php")) {
		include(SL_ADDONS_PATH."/csv-xml-importer-exporter/reGeo.php");
	}
	if (!empty($_GET['changeView']) && $_GET['changeView']==1) {
		if ($sl_vars['location_table_view']=="Normal") {
			$sl_vars['location_table_view']='Expanded';
			sl_data('sl_vars', 'update', $sl_vars);
			//$tabViewText="Expanded";
		} else {
			$sl_vars['location_table_view']='Normal';
			sl_data('sl_vars', 'update', $sl_vars);
			//$tabViewText="Normal";
		}
		print "<script>location.replace('".str_replace("&changeView=1", "", $_SERVER['REQUEST_URI'])."');</script>";
	}
	if (!empty($_GET['changeUpdater']) && $_GET['changeUpdater']==1) {
		if (sl_data('sl_location_updater_type')=="Tagging") {
			sl_data('sl_location_updater_type', 'update', 'Multiple Fields');
			//$updaterTypeText="Multiple Fields";
		} else {
			sl_data('sl_location_updater_type', 'update', 'Tagging');
			//$updaterTypeText="Tagging";
		}
		$_SERVER['REQUEST_URI']=str_replace("&changeUpdater=1", "", $_SERVER['REQUEST_URI']);
		print "<script>location.replace('$_SERVER[REQUEST_URI]');</script>";
	}
	
?>