<?php
//include("variables.sl.php");
include_once(SL_INCLUDES_PATH."/top-nav.php");
?>
<div class='wrap'>
<?php 

if (empty($_POST)) {sl_move_upload_directories();}
if (!empty($_POST['sl_map_type'])) { //shouldn't just be "$_POST"; use an index that should always have a value - 12/9/14
    //$sl_vars=sl_data('sl_vars');
    
 // sl_initialize_variables();
    include(SL_INCLUDES_PATH."/mapdesigner-options.php");
    sl_md_save($sl_mdo);
    unset($sl_mdo);  //needs to be unset here in order for the latest updated values to show up when md options are included a 2nd time below for display
 
 sl_initialize_variables(); //needs to be after unset($sl_mdo), otherwise values set to blank in MapDesigner won't reset to their default value until a 2nd page load - 4/17/18  1:!7a - v3.48
 
	print "<div class='sl_admin_success' >".__("Successful Update", "store-locator")." $view_link</div> <!--meta http-equiv='refresh' content='0'-->";
}

$api_key_field=(empty($sl_vars['api_key']))? "<input name='sl_api_key' size='30' style='font-size:10px'>" : "<input name='sl_api_key' value='$sl_vars[api_key]' size='60' style='font-size:10px'>";
include(SL_INCLUDES_PATH."/countries-languages.php");
$your_location_select="<!--tr><td--><select name='map_region' style='font-size:11px'><optgroup label='".__("Select Your Location", "store-locator")."'><!--/td><td-->";
foreach ($tld as $key=>$value) {
	$selected=($sl_vars['map_region']==$value)?" selected " : "";
	$your_location_select.="<option value='$key:{$the_domain[$key]}:$value' $selected>$key</option>\n";
}
$your_location_select.="</optgroup></select><!--/td></tr-->";
$map_lang_select="<!--tr><td--><select name='sl_map_language' style='font-size:11px'><optgroup label='".__("Select Map Language", "store-locator")."'><!--/td><td-->";

ksort($map_lang);
foreach ($map_lang as $key=>$value) {
	$selected=($sl_vars['map_language']==$value)? " selected='selected'" : "";
	$map_lang_select.= "<option value='$value' $selected>".ucwords(strtolower($key))."</option>\n";
}
$map_lang_select.= "</optgroup></select><!--/td></tr-->";
$update_button="<input type='submit' value='".__("Update", "store-locator")."' class='button-primary'>";

print "<form method='post' name='mapdesigner_form'><table class='widefat' id='mapdesigner_table'><thead><tr><th colspan='2'>".__("MapDesigner", "store-locator")." <div style='float:right'><small>".__("API Key", "store-locator")." (<a rel='sl_pop' href='#api-key-info'>?</a>): </small>
<div id='api-key-info' style='display:none'><h3 style='margin-top:0px'>".__("Google Maps", "store-locator")." ".__("API Key", "store-locator")."</h3>".__("Google Maps API V3 actually doesn't require an API Key, however, if needed (it appears that high usage requires a key)", "store-locator").", <a target='_blank' href='https://developers.google.com/maps/documentation/javascript/tutorial#api_key'>".__("get your key here", "store-locator")."</a></div> {$api_key_field}&nbsp;{$your_location_select}&nbsp;{$map_lang_select}&nbsp;&nbsp;<input type='submit' value='".__("Update", "store-locator")."' class='button-primary' style=''><div></th><!--td><".__("Designer", "store-locator")."--></td--></tr></thead>";

include(SL_INCLUDES_PATH."/mapdesigner-options.php");

print "<tr><td colspan='1' width='45%' class='left_side' style='vertical-align:top'><h2>".__("Defaults", "store-locator")."</h2>";

sl_md_display($sl_mdo, 'defaults', 1);

print "</td>";

//WPML Registration Integration
if (function_exists('icl_register_string')) {

	#### MapDesigner labels 
	$GLOBALS['input_zone_type'] = "labels";
	$labels_arr = array_filter($sl_mdo, "filter_sl_mdo");
	unset($GLOBALS['input_zone_type']);
	//var_dump($labels_arr); die();
	
	foreach ($labels_arr as $value) {
		$the_field = $value['field_name'];
		$varname = "sl_".$the_field;
		
		icl_register_string(SL_DIR, $value['label'], $sl_vars[$the_field]);
	}
	
	### Search button states
	icl_register_string(SL_DIR, 'Search Button Filename', "search_button.png");
	icl_register_string(SL_DIR, 'Search Button Filename (Down State)', "search_button_down.png");
	icl_register_string(SL_DIR, 'Search Button Filename (Over State)', "search_button_over.png");
}

print "<!--tr--><td colspan='1' width='50%' style='vertical-align:top'><h2>".__("Design", "store-locator")."</h2>
$icon_notification_msg";

sl_md_display($sl_mdo, 'design', 1, "right_side");

print "</td></tr>
<tr><td colspan='1' class='left_side' style='vertical-align:top; border-bottom:0px'><h2>".__("Dimensions", "store-locator")."</h2>";

sl_md_display($sl_mdo, 'dimensions', 1);

print "</td><!--/tr>
<tr--><td colspan='1' style='vertical-align:top; border-bottom:0px'><h2>".__("Labels", "store-locator")."</h2>";

sl_md_display($sl_mdo, 'labels', 2, "right_side");

print "</td></tr>
<tr><td colspan='2'>$update_button</td></tr></table></form>";

?>
</div>
<?php include(SL_INCLUDES_PATH."/sl-footer.php"); ?>