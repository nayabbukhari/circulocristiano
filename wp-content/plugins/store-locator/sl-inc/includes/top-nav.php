<?php
$sl_top_nav_hash[]='information';
$sl_top_nav_links[SL_INFORMATION_PAGE]=__("News & Upgrades", "store-locator");

$sl_top_nav_hash[]='locations';
$sl_top_nav_links[SL_MANAGE_LOCATIONS_PAGE]=__("Locations", "store-locator");
	$sl_top_nav_sub_links['locations'][__("Manage", "store-locator")] = SL_MANAGE_LOCATIONS_PAGE;
	$sl_top_nav_sub_links['locations'][__("Add", "store-locator")] = SL_ADD_LOCATIONS_PAGE;

$sl_top_nav_hash[]='mapdesigner'; 
$sl_top_nav_links[SL_MAP_DESIGNER_PAGE]=__("MapDesigner", "store-locator");

if (function_exists("do_sl_hook")){
	do_sl_hook("sl_top_nav_links", "", array(&$sl_top_nav_hash, &$sl_top_nav_links, &$sl_top_nav_sub_links));
}
//do_sl_top_nav();
//function do_sl_top_nav(){
//	global $sl_top_nav_links, $sl_top_nav_sub_links, $sl_top_nav_hash, $sl_version;
	
print "<br>";
$style_var = "";
if (!empty($_POST['sl_thanks'])) {
	$sl_vars['thanks'] = $_POST['sl_thanks'];
	$sl_vars['thanks_time_length'] = $_POST['sl_thanks_time_length'];
	$sl_vars['thanks_num_locs'] = $_POST['sl_thanks_num_locs'];
	unset($_POST);
}
$sl_thanks = (!empty($sl_vars['thanks']))? $sl_vars['thanks'] : "";
print <<<EOQ
<ul class="tablist">\n
EOQ;
$ctr=0; $tsn_links_js="<script>\nvar tsn_link_arr = [];"; $tsn_links_output="";
$tm_st = ((time() - strtotime($sl_vars["start"]))/60/60/24>=7); //time since start
$nm_locs = ($wpdb->get_var("SELECT COUNT(sl_id) from ".SL_DB_PREFIX."store_locator WHERE sl_latitude<>'' AND sl_latitude <>0 AND sl_latitude IS NOT NULL")>=50); //num. of locations sucessfully added to db
foreach ($sl_top_nav_links as $key=>$value) {
	$current_var=(preg_match("@$_GET[page]@",$key))? "current_top_link" : "" ;
	if (preg_match("@$_GET[page]@",$key)){
		//$style_var .= "sl_top_nav_init(tsn_link_arr);\n";
	}
	//top-level nav
	print "<li class=\"top_nav_li $sl_top_nav_hash[$ctr]\" id=\"$current_var\"><a href=\"$key\"  id='__$sl_top_nav_hash[$ctr]' class='top_nav_a' style=''>$value</a></li>\n";

	$tsn_links_js.="tsn_link_arr['$sl_top_nav_hash[$ctr]']='';";

	if (!empty($sl_top_nav_sub_links[$sl_top_nav_hash[$ctr]])) {
		$cur = ""; $ctr2=0;
		foreach ($sl_top_nav_sub_links[$sl_top_nav_hash[$ctr]] as $key2=>$value2) {
			if (preg_match("@$sl_top_nav_hash[$ctr]@", $_SERVER['REQUEST_URI'])) {
				if (empty($_GET['pg']) && !preg_match("@&pg@", $value2)) {
					$cur = "current_sub_link";
				} elseif (!empty($_GET['pg']) && preg_match("@$_GET[pg]@", $value2)) {
					$cur = "current_sub_link";
				}  else {
					$cur = "";
				}
				$tsn_links_output.="<a href='$value2' class='$cur'>$key2</a>";
			}
			//sub-nav
			$tsn_links_js .= "tsn_link_arr['$sl_top_nav_hash[$ctr]']+=\"<a href='$value2' class='top_nav_sub_a $cur' id='$sl_top_nav_hash[$ctr]_$ctr2' onmouseover='level3_links(this, &quot;__$sl_top_nav_hash[$ctr]&quot;, &quot;show&quot;)' onmouseout='level3_links(this, &quot;__$sl_top_nav_hash[$ctr]&quot;, &quot;hide&quot;)'>$key2</a>\";";
				if (!empty($sl_top_nav_sub2_links[$value2])) {
					$tsn_links_js.= "tsn_link_arr['$sl_top_nav_hash[$ctr]_$ctr2']='';";
					foreach ($sl_top_nav_sub2_links[$value2] as $level3_title => $level3_url) {
						//3rd-level nav
						$tsn_links_js.= "tsn_link_arr['{$sl_top_nav_hash[$ctr]}_{$ctr2}']+=\"<a href='$level3_url' class='' id=''>$level3_title</a>\"; " ;
					}
				}
			$ctr2++;
		}
	}
	$ctr++;
}

$show_my_views_button = (($sl_thanks!="false" && $sl_thanks!="true")); //2nd part of clause shows it to longer time users who haven't rated. For now, will show to newere users //|| ($sl_thanks=="false" && empty($sl_vars['thanks_num_locs']) && empty($sl_vars['thanks_time_length']) ));
$thnks_ct = (!empty($sl_vars['thanks_count']))? $sl_vars['thanks_count'] : 0 ;
if (empty($thnks_ct) || $thnks_ct<=20){
	if (!empty($thnks_ct)){ $sl_vars['thanks_count'] = ($thnks_ct+1); } else { $sl_vars['thanks_count'] = 1; }
} elseif ($thnks_ct>=21 && $show_my_views_button) { //$sl_thanks!="false" && $sl_thanks!="true") {
	$sl_vars['thanks_count'] = ($thnks_ct+1);
	$style_var .= ($tm_st && $nm_locs && $show_my_views_button)? "jQuery('#thanks_button').click();\njQuery('.pp_close').css('visibility', 'hidden');\n" : "" ;
}
$tsn_links_js.="jQuery(document).ready(function(){ {$style_var} });\n";
$tsn_links_js.="</script>";

#Update plugin link creation
$sl_vars['sl_latest_version_check_time'] = (empty($sl_vars['sl_latest_version_check_time']))? date("Y-m-d H:i:s") : $sl_vars['sl_latest_version_check_time'];
if (empty($sl_vars['sl_latest_version']) || (time() - strtotime($sl_vars['sl_latest_version_check_time']))/60>=(60*12)){ //12-hr db caching of version info
	/*if (!function_exists("plugins_api")) {
		$plugin_install_url = ABSPATH."wp-admin/includes/plugin-install.php"; //die($plugin_install_url);
		include_once($plugin_install_url);
	}
	$sl_api = plugins_api('plugin_information', array('slug' => 'store-locator', 'fields' => array('sections' => false) ) ); */
	/*need 'true' if trying to include changelog info in future*/
	$sl_api = @sl_remote_data(array(
			'host' => 'api.wordpress.org',
			'url' => '/plugins/info/1.0/store-locator',
			'ua' => 'none'), 'serial');
	//var_dump($sl_api); die();
	$sl_latest_version = @$sl_api->version; //$sl_version="2.6";
	//$sl_latest_changelog = $sl_api->sections['changelog']; //var_dump($sl_latest_changelog); die();
	//preg_match_all("@<ul>(.*)</ul>@", $sl_latest_changelog, $sl_version_matches); var_dump($sl_version_matches); die();
	
	$sl_vars['sl_latest_version_check_time'] = date("Y-m-d H:i:s");
	$sl_vars['sl_latest_version'] = $sl_latest_version;
} else {
	$sl_latest_version = $sl_vars['sl_latest_version'];
}

if (strnatcmp($sl_latest_version, $sl_version) > 0) { 
	$sl_plugin = SL_DIR . "/store-locator.php";
	$sl_update_link = admin_url('update.php?action=upgrade-plugin&plugin=' . $sl_plugin);
	$sl_update_link_nonce = wp_nonce_url($sl_update_link, 'upgrade-plugin_' . $sl_plugin);
	$sl_update_msg = "&nbsp;&gt;&nbsp;<a href='$sl_update_link_nonce' style='color:#900; font-weight:bold;' onclick='confirmClick(\"".__("You will now be updating to Store Locator", "store-locator")." v$sl_latest_version, ".__("click OK or Confirm to continue", "store-locator").".\", this.href); return false;'>".__("Update to", "store-locator")." $sl_latest_version</a>";
} else {
	$sl_update_msg = "";
}

#Notice for Addons Platform update
if ( defined("SL_ADDONS_PLATFORM_DIR") ) {
	$sl_vars['sl_latest_ap_check_time'] = (empty($sl_vars['sl_latest_ap_check_time']))? date("Y-m-d H:i:s") : $sl_vars['sl_latest_ap_check_time'];

	if ( (empty($sl_vars['sl_latest_ap_version']) || (time() - strtotime($sl_vars['sl_latest_ap_check_time']))/60>=(60*12)) ) { //12-hr db caching of version info
		$ap_update = sl_remote_data(array(
			'pagetype' => 'ap',
			'dir' => SL_ADDONS_PLATFORM_DIR, 
			'key' => sl_data('sl_license_' . SL_ADDONS_PLATFORM_DIR)
		));
		$ap_latest_version = (!empty($ap_update[0]))? preg_replace("@\.zip|".SL_ADDONS_PLATFORM_DIR."\.@", "", $ap_update[0]['filename']) : 0;
		//var_dump($ap_update); die();
		
		$sl_vars['sl_latest_ap_check_time'] = date("Y-m-d H:i:s");
		$sl_vars['sl_latest_ap_version'] = $ap_latest_version;
	} else {
		$ap_latest_version = $sl_vars['sl_latest_ap_version'];
	}

	$ap_readme = SL_ADDONS_PLATFORM_PATH."/readme.txt"; 
	if (file_exists($ap_readme)) {
		$rm_txt = file_get_contents($ap_readme);
		preg_match("/\n[ ]*stable tag:[ ]?([^\n]+)(\n)?/i", $rm_txt, $cv); //var_dump($rm_txt); var_dump($cv);
		$ap_version = (!empty($cv[1]))? trim($cv[1]) : "1.0" ;
	} else {$ap_version = "1.0";}
	
	$ap_title = ucwords(str_replace("-", " ", SL_ADDONS_PLATFORM_DIR));
	$ap_update_msg = ucwords(str_replace("-", " ", SL_ADDONS_PLATFORM_DIR))." Version $ap_latest_version is available";
	$ap_update = (strnatcmp($ap_latest_version, $ap_version) > 0)? "&nbsp;|&nbsp;<a href='#' style='color:#900; font-weight: bold;' onclick='alert(\"$ap_title v$ap_latest_version ".__("is available for download -- you are currently using v$ap_version. \\n\\n\\t1) Please re-use the download link from the email receipt sent to you for your $ap_title purchase[[comma-here]] \\n\\n\\t2) Extract the zip file to your computer[[comma-here]] then \\n\\n\\t3) Upload the &apos;".SL_ADDONS_PLATFORM_DIR."&apos; folder to &apos;".SL_ADDONS_PATH."&apos; on your website using FTP for the latest $ap_title functionality", "store-locator").".\"); return false;' title='$ap_update_msg'>Get AP v{$ap_latest_version}</a>" : "" ;
} else { $ap_update = ""; }



print "<span style='padding-left:10px; color:gray; position:relative; top:10px; font-size:11px; cursor:help;' title='".__("Store Locator Version", "store-locator")." $sl_version ".__("and", "store-locator")." ".__("PHP Version", "store-locator")." ".phpversion()."'>SL v$sl_version{$sl_update_msg}&nbsp;|&nbsp;PHP v".phpversion()."{$ap_update}</span></ul>\n";
//if (preg_match("@addon-settings@", $_GET['page'])){
	print "<div class='top_sub_nav' id='top_sub_nav'><div id='inner_div' style='display:inline; height:inherit;'>$tsn_links_js{$tsn_links_output}  </div><div id='level3_nav' style='display:none;' ></div>";
	if (function_exists("do_sl_hook")) { do_sl_hook("sl_nav_buttons_right"); }
	if ($show_my_views_button) { //$sl_thanks!="false" && $sl_thanks!="true") {
		//&& $tm_st
		print "<input rel='sl_pop' type='button' class='button-primary' onclick='return false;' id='thanks_button' href='".SL_INCLUDES_BASE."/thank-you.php?ajax=true' style='margin-right:10px; font-weight:bold; margin:5px; background:green; float:right;' value='".__("My Views", "store-locator")."'/>";
	}
	print "</div>";
//}

include(SL_INCLUDES_PATH."/admin-notices.php");
if (function_exists("do_sl_hook")){ do_sl_hook("sl_admin_notices"); }



if (!empty($_POST) && function_exists("do_sl_hook")){ do_sl_hook("sl_admin_form_post"); /*print "<br>";*/ }
if (function_exists("do_sl_hook")) { do_sl_hook("sl_admin_data"); } 
?>
<div id="slideout">
	<div id="clickme"><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b style='font-family:georgia; position:relative; top:-10px;'><?php print __("Dashboard", "store-locator"); ?></b>
	</div>
	<div id="slidecontent">
	   <div id='slidecontainer'>
		<div style='width:50%; float:left'><?php sl_module("thanks", __("My Views", "store-locator")."", "240px");  ?></div>
		<div style='width:50%; float:right'><?php sl_module("readme", __("Information & ReadMe Instructions", "store-locator"), "240px");  ?></div>
		<div style='width:50%; float:left'><?php sl_module("news", __("Latest News", "store-locator"), "270px"); ?></div>
		<div style='width:50%; float:left'><?php sl_module("keys", __("Activation Keys", "store-locator")."<img style='float:right; opacity:0; height:20px;' id='module-keys' src='".SL_IMAGES_BASE_ORIGINAL."/loading.gif'>", "270px"); ?></div>
	   </div>
	</div>
</div>
<div id='validation_status' style='display:none;'><h3 style='margin-top:0px'>Validation Status</h3><div style='width:90%'></div></div>
<?php  sl_data('sl_vars', 'update', $sl_vars); ?>