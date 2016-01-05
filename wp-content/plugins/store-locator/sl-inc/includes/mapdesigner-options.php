<?php
//MapDesigner Options

###Data - Form Inputs###
$icon_str="";$icon2_str="";

$icon_dir=opendir(SL_ICONS_PATH);  //trailing slash removed after path constant - v3.78 2:27a
while (false !== $icon_dir && false !== ($an_icon=readdir($icon_dir))) {
	if (!preg_match("@^\.{1,2}.*$@", $an_icon) && !preg_match("@shadow@", $an_icon) && !preg_match("@\.db@", $an_icon)) {

		$icon_str.="<img style='height:25px; cursor:hand; cursor:pointer; border:solid white 2px; padding:2px' src='".SL_ICONS_BASE."/$an_icon' onclick='document.forms[\"mapdesigner_form\"].icon.value=this.src;document.getElementById(\"prev\").src=this.src;' onmouseover='style.borderColor=\"red\";' onmouseout='style.borderColor=\"white\";'>";
	}
}
if (is_dir(SL_CUSTOM_ICONS_PATH)) {
	$icon_upload_dir=opendir(SL_CUSTOM_ICONS_PATH);
	while (false !== $icon_upload_dir && false !== ($an_icon=readdir($icon_upload_dir))) {
		if (!preg_match("@^\.{1,2}.*$@", $an_icon) && !preg_match("@shadow@", $an_icon) && !preg_match("@\.db@", $an_icon)) {

			$icon_str.="<img style='height:25px; cursor:hand; cursor:pointer; border:solid white 2px; padding:2px' src='".SL_CUSTOM_ICONS_BASE."/$an_icon' onclick='document.forms[\"mapdesigner_form\"].icon.value=this.src;document.getElementById(\"prev\").src=this.src;' onmouseover='style.borderColor=\"red\";' onmouseout='style.borderColor=\"white\";'>";
		}
	}
}

$icon_dir=opendir(SL_ICONS_PATH); //trailing slash removed after path constant - v3.78 2:27a
while (false !== $icon_dir && false !== ($an_icon=readdir($icon_dir))) {
	if (!preg_match("@^\.{1,2}.*$@", $an_icon) && !preg_match("@shadow@", $an_icon) && !preg_match("@\.db@", $an_icon)) {

		$icon2_str.="<img style='height:25px; cursor:hand; cursor:pointer; border:solid white 2px; padding:2px' src='".SL_ICONS_BASE."/$an_icon' onclick='document.forms[\"mapdesigner_form\"].icon2.value=this.src;document.getElementById(\"prev2\").src=this.src;' onmouseover='style.borderColor=\"red\";' onmouseout='style.borderColor=\"white\";'>";
	}
}
if (is_dir(SL_CUSTOM_ICONS_PATH)) {
	$icon_upload_dir=opendir(SL_CUSTOM_ICONS_PATH);
	while (false !== $icon_upload_dir && false !== ($an_icon=readdir($icon_upload_dir))) {
		if (!preg_match("@^\.{1,2}.*$@", $an_icon) && !preg_match("@shadow@", $an_icon) && !preg_match("@\.db@", $an_icon)) {

			$icon2_str.="<img style='height:25px; cursor:hand; cursor:pointer; border:solid white 2px; padding:2px' src='".SL_CUSTOM_ICONS_BASE."/$an_icon' onclick='document.forms[\"mapDesigner\"].icon2.value=this.src;document.getElementById(\"prev2\").src=this.src;' onmouseover='style.borderColor=\"red\";' onmouseout='style.borderColor=\"white\";'>";
		}
	}
}

if (is_dir(SL_THEMES_PATH)) {
	$theme_dir=opendir(SL_THEMES_PATH); 
	$theme_str="";
	while (false !== $theme_dir && false !== ($a_theme=readdir($theme_dir))) {
		if (!preg_match("@^\.{1,2}.*$@", $a_theme) && !preg_match("@\.(php|txt|htm(l)?)@", $a_theme)) {

			$selected=($a_theme==$sl_vars['theme'])? " selected " : "";
			$theme_str.="<option value='$a_theme' $selected>$a_theme</option>\n";
		}
	}
}

$zl_arr=array();
for ($i=0; $i<=19; $i++) {
	$zl_arr[]=$i;
}

$zoom="<select name='zoom_level'>";
foreach ($zl_arr as $value) {
	$zoom.="<option value='$value' ";
	if ($sl_vars['zoom_level']==$value){ $zoom.=" selected ";}
	$zoom.=">$value</option>";
}
$zoom.="</select>";

$checked=($sl_vars['use_city_search']==1)? " checked " : "";
$checked2="";
//$checked2=($sl_vars['use_name_search']==1)? " checked " : "";
$checked3=($sl_vars['remove_credits']==1)? " checked " : "";
$checked4=($sl_vars['load_locations_default']==1)? " checked " : "";
$checked5=($sl_vars['map_overview_control']==1)? " checked " : "";
$checked6=($sl_vars['geolocate']==1)? " checked " : "";
$checked7=($sl_vars['load_results_with_locations_default']==1)? " checked " : "";

if (isset($sl_vars['scripts_load']) && $sl_vars['scripts_load']=='all'){
	$checked_all=" checked='checked' onclick='jQuery(\"#scripts_load_selective_tr\").fadeOut()' ";
	$checked_selective="onclick='jQuery(\"#scripts_load_selective_tr\").fadeIn()'";
	$hidden_selective_tr="style='display:none;'";
} else {
	$checked_all=" onclick='jQuery(\"#scripts_load_selective_tr\").fadeOut()' "; 
	$checked_selective=" checked='checked' onclick='jQuery(\"#scripts_load_selective_tr\").fadeIn()' ";
	$hidden_selective_tr="";
}
$checked_home=(isset($sl_vars['scripts_load_home']) && $sl_vars['scripts_load_home']==1)? " checked " : "";
$checked_archives_404=(isset($sl_vars['scripts_load_archives_404']) && $sl_vars['scripts_load_archives_404']==1)? " checked " : "";

$map_type["".__("Normal", "store-locator").""]="google.maps.MapTypeId.ROADMAP";
$map_type["".__("Normal + Terrain (Physical)", "store-locator").""]="google.maps.MapTypeId.TERRAIN";
$map_type["".__("Satellite", "store-locator").""]="google.maps.MapTypeId.SATELLITE";
$map_type["".__("Satellite + Labels (Hybrid)", "store-locator").""]="google.maps.MapTypeId.HYBRID";
$map_type_options="";

foreach($map_type as $key=>$value) {
	$selected2=($sl_vars['map_type']==$value)? " selected " : "";
	$map_type_options.="<option value='$value' $selected2>$key</option>\n";
}
$icon_notification_msg=((preg_match("@wordpress-store-locator-location-finder@", $sl_vars['icon']) && preg_match("@^store-locator@", $sl_dir)) || (preg_match("@wordpress-store-locator-location-finder@",$sl_vars['icon2']) && preg_match("@^store-locator@", $sl_dir)))? "<div class='sl_admin_success' style='background-color:LightYellow;color:red'><span style='color:red'>".__("You have switched from <strong>'wordpress-store-locator-location-finder'</strong> to <strong>'store-locator'</strong> --- great!<br>Now, please re-select your <b>'Home Icon'</b> and <b>'Destination Icon'</b> below, so that they show up properly on your store locator map.", "store-locator")."</span></div>" : "" ;


/*************** MapDesigner Options - Information & Usage **************/
/* 
== Description == 
Allows one to modify the '$sl_mdo' array via the 'sl_mapdesigner_options' hook in order to add options to the MapDesigner settings page

== Available Parameters in $sl_mdo array ==
= Required =
* field_name: 
name of the key stored in the $sl_vars array when saving the value
* default: 
initial value field is set to
* input_zone: 
the area of MapDesigner setting page where new option will appear (available values [as of v3.74] - "defaults", "labels", "dimensions", "design")
* output_zone: 
the area of Store Locator's functionality your option affects (available values [as of v3.74] - "sl_dyn_js", "sl_template", "sl_xml", "sl_head_scripts")
* label:
Description of option in MapDesigner settings
* input_template: 
HTML of the form element representing the new option

= Optional =
* field_type: 
only needed if your option is a 'checkbox'
* more_info: 
HTML in pop-up showing further details about the option, if needed
* more_info_label: 
1-word label of the link clicked to display HTML in 'more_info' (should be prefixed and unique label)
* row_id:
'id' value of the 'tr' HTML tag containing your new option
* hide_row:
logical condition under which you want the option(s) contained in the row labeled by 'row_id' to be hidden. If this evaluates to TRUE, then row will be hidden when option is first displayed. You can dynamically reveal the row using: {action}='jQuery("#{row_id}").fadeIn()'  in another form element in order to determine an action that reveals the row, where {action} can be 'onclick', 'onfocus', 'onmouseover', etc.
* stripslashes:
removes any slashes that are created in a text field if it contains apostrophes, quotation marks (available value: 1)
* numbers_only:
determines whether or not a new option's value can only contain numbers.  For example, and value of '24tgrwoi6f24l' will be changed to '24624' if 'numbers_only' is set to 1 (available values: 0, 1; available types: number or array [based on type of 'field_name'])
* colspan:
can be used if creating an informational row, where you fill in 'label' value and leave 'input_template' blank (available value: 2)

== Notes: Multiple values in grouped together ==
* If your new option has multiple values that you want to store in the $sl_vars array, then you can make 'field_name', 'default', and 'output_zone' entries in $sl_mdo into arrays in which field_name[0] has a default value of default[0] and an output zone of output_zone[0], field_name[1], default[1], & output_zone[1] are associated, and so on.
* If they are arrays, 'field_name', 'default', and 'output_zone' must always be the same length, with one exception -- 'output_zone' can be a single value if the output zone of each value in the 'field_name' array is the same.  So instead of "output_zone => ('sl_template', 'sl_template')", you can simply do "output_zone => 'sl_template' "
* Optional value 'numbers_only' follows the same rules as 'field_name' & 'default value'

== Notes: Using 'label' & 'input_template' values ==
* 'label' & 'input_template' values are guides in terms of formatting your new option, but are essentially just 2 containers in which you can place necessary labeling and HTML for form elements, so they can be interchanged, the label & HTML can all be in the 'label' value or 'input_template', etc -- helpful if grouping multiple values into one option
* However, the 'labels' input zone is unique to the other 3 input zones. The 'label' and 'input_template' values are displayed in columns of 3, with 'label' shown below 'input_template'.   The 'defaults', 'dimensions', and 'design' input zones are displayed in columns of 2, with the 'label' value displayed to the left of the 'input_template' value, thus grouping values into 1 option shouldn't be done if using an 'input_zone' of 'labels'

*/
/***************************************************/

###Defaults###
$sl_mdo[] = array("field_name" => "map_type", "default" => "google.maps.MapTypeId.ROADMAP", "input_zone" => "defaults", "output_zone" => "sl_dyn_js", "label" => __("Default Map Type", "store-locator"), "input_template" => "<select name='sl_map_type'>\n$map_type_options</select>");

$sl_mdo[] = array("field_name" => "num_initial_displayed", "default" => "500", "input_zone" => "defaults", "output_zone" => "sl_xml", "label" =>  __("Locations in Results", "store-locator"), "input_template" => "<input name='sl_num_initial_displayed' value='$sl_vars[num_initial_displayed]'>");

$sl_mdo[] = array("field_name" => "scripts_load", "default" => "selective", "input_zone" => "defaults", "output_zone" => "sl_head_scripts", "label" => __("JS & CSS Loading", "store-locator"), "input_template" => "<input name='sl_scripts_load' value='selective' type='radio' $checked_selective>Selective&nbsp;Loading&nbsp;&nbsp;<input name='sl_scripts_load' value='all' type='radio' $checked_all>All&nbsp;Pages", "more_info" => __("<h2 style='margin-top:0px'>JavaScript & Cascading Style Sheets Loading</h2><b>Selective Loading:</b><br>Attempts to detect where Store Locator JS & CSS scripts are needed and only loads them on those necessary pages. <br><br><b>All Pages:</b><br>Loads JS & CSS scripts on every page of your website.<br><br><div class='sl_code code'><b>Note:</b>&nbsp;\"Selective Loading\" will work for 99% of sites, however, if you experience map loading issues or missing CSS styling on your Store Locator or addon-generated pages, choose the \"All Pages\" option.</div>", "store-locator"), "more_info_label" => "info_js_css_load");

$sl_mdo[] = array("field_name" => array("scripts_load_home", "scripts_load_archives_404"), "default" => array("1", "1"), "field_type" =>"checkbox", "input_zone" => "defaults", "output_zone" => array("sl_head_scripts", "sl_head_scripts"), "label" => "", "input_template" => __("Also Load On", "store-locator")." .. <input name='sl_scripts_load_home' value='1' type='checkbox' {$checked_home}>&nbsp;".__("Home", "store-locator")."&nbsp;&nbsp;<input name='sl_scripts_load_archives_404' value='1' type='checkbox' {$checked_archives_404}>&nbsp;".__("Archives", "store-locator")." / 404", "row_id" => "scripts_load_selective_tr", "hide_row" => (isset($sl_vars['scripts_load']) && $sl_vars['scripts_load'] == "all") );

$sl_mdo[] = array("field_name" => array("use_city_search", "map_overview_control"), "default" => array("1", "0"), "field_type" =>"checkbox", "input_zone" => "defaults", "output_zone" => array("sl_template", "sl_dyn_js"), "label" => "<input name='sl_use_city_search' value='1' type='checkbox' $checked>&nbsp;".__("Search By City", "store-locator"), "input_template" => "<input name='sl_map_overview_control' value='1' type='checkbox' $checked5>&nbsp;".__("Show Map Inset Box", "store-locator"));

$sl_mdo[] = array("field_name" => array("geolocate", "load_locations_default", "load_results_with_locations_default"), "default" => array("0", "1", "1"), "field_type" => "checkbox", "input_zone" => "defaults", "output_zone" => array("sl_dyn_js", "sl_dyn_js", "sl_dyn_js"), "label" => "<input name='sl_geolocate' value='1' type='checkbox' $checked6>&nbsp;".__("Auto-Locate User", "store-locator"), "input_template" => "<input name='sl_load_locations_default' value='1' type='checkbox' $checked4>&nbsp;".__("Auto-Load Locations", "store-locator")."&nbsp;&nbsp;(<input name='sl_load_results_with_locations_default' value='1' type='checkbox' $checked7>&nbsp;&amp;&nbsp;".__("Listing", "store-locator")."&nbsp;(<a href='#info_load_results_default' rel='sl_pop'>?</a>)<div style='display:none;' id='info_load_results_default'>".__("<h2 style='margin-top:0px'>Search Results Listing By Default</h2>Determine whether or not both the map icons and the results listing show when loading locations by default. <br><Br>No results listings are shown even if this is checked, but 'Auto-Load Locations' is unchecked", "store-locator").".</div>)");

/*<!--tr><td>".__("Allow User Search By Name of Location?", "store-locator").":</td>
<td><input name='sl_use_name_search' value='1' type='checkbox' $checked2></td></tr-->
<!--/table-->*/
//$sl_vars['use_name_search']=($_POST['sl_use_name_search']==="")? 0 : $_POST['sl_use_name_search'];
###End Defaults###

###Labels###
$sl_mdo[] = array("field_name" => "search_label", "default" => "Address", "input_zone" => "labels", "output_zone" => "sl_template", "label" => __("Address Input", "store-locator"), "input_template" => "<input name='search_label' value=\"$sl_vars[search_label]\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "radius_label", "default" => "Radius", "input_zone" => "labels", "output_zone" => "sl_template", "label" => __("Radius Dropdown", "store-locator"), "input_template" => "<input name='sl_radius_label' value=\"$sl_vars[radius_label]\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "website_label", "default" => "Website", "input_zone" => "labels", "output_zone" => "sl_dyn_js", "label" => __("Website URL", "store-locator"), "input_template" => "<input name='sl_website_label' value=\"$sl_vars[website_label]\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "directions_label", "default" => "Directions", "input_zone" => "labels", "output_zone" => "sl_dyn_js", "label" => __("Directions URL", "store-locator"), "input_template" => "<input name='sl_directions_label' value=\"$sl_vars[directions_label]\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "map_link_label", "default" => "Map", "input_zone" => "labels", "output_zone" => "sl_dyn_js", "label" => __("Map Link URL", "store-locator"), "input_template" => "<input name='sl_map_link_label' value=\"$sl_vars[map_link_label]\" size='14'>", "stripslashes" => 1, "more_info"=>__("<h2 style='margin-top:0px'>Map Link Label</h2>This label shows for each location's Google Map link in the search results list if 'Auto-Load Locations' is on, but before a search is performed", "store-locator"), "more_info_label"=>"info_map_link_label"); //v3.88

$sl_mdo[] = array("field_name" => "instruction_message", "default" => "Enter Your Zip Code or Address Above.", "input_zone" => "labels", "output_zone" => "sl_template", "label" => __("Instruction to Users", "store-locator"), "input_template" => "<input name='sl_instruction_message' value=\"".$sl_vars['instruction_message']."\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "city_dropdown_label", "default" => "--Search By City--", "input_zone" => "labels", "output_zone" => "sl_template", "label" => __("City Dropdown", "store-locator"), "input_template" => "<input name='sl_city_dropdown_label' value=\"".$sl_vars['city_dropdown_label']."\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "location_not_found_message", "default" => "", "input_zone" => "labels", "output_zone" => "sl_dyn_js", "label" => __("Location Doesn't Exist", "store-locator"), "input_template" => "<input name='sl_location_not_found_message' value=\"".$sl_vars['location_not_found_message']."\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "no_results_found_message", "default" => "No Results Found", "input_zone" => "labels", "output_zone" => "sl_dyn_js", "label" => __("No Results Are Found", "store-locator"), "input_template" => "<input name='sl_no_results_found_message' value=\"".$sl_vars['no_results_found_message']."\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "hours_label", "default" => "Hours", "input_zone" => "labels", "output_zone" => "sl_dyn_js", "label" => __("Hours", "store-locator"), "input_template" => "<input name='sl_hours_label' value=\"".$sl_vars['hours_label']."\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "phone_label", "default" => "Phone", "input_zone" => "labels", "output_zone" => "sl_dyn_js", "label" => __("Phone", "store-locator"), "input_template" => "<input name='sl_phone_label' value=\"".$sl_vars['phone_label']."\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "fax_label", "default" => "Fax", "input_zone" => "labels", "output_zone" => "sl_dyn_js", "label" => __("Fax", "store-locator"), "input_template" => "<input name='sl_fax_label' value=\"".$sl_vars['fax_label']."\" size='14'>", "stripslashes" => 1);

$sl_mdo[] = array("field_name" => "email_label", "default" => "Email", "input_zone" => "labels", "output_zone" => "sl_dyn_js",  "label" => __("Email", "store-locator"), "input_template" => "<input name='sl_email_label' value=\"".$sl_vars['email_label']."\" size='14'>", "stripslashes" => 1);
###End Labels###

###Dimensions###
$sl_mdo[] = array("field_name" => "zoom_level", "default" => "4", "input_zone" => "dimensions", "output_zone" => "sl_dyn_js", "label" => "<nobr>".__("Zoom Level", "store-locator")."</nobr>", "input_template" => $zoom);

$sl_mdo[] = array("field_name" => array("height", "width", "height_units", "width_units"), "default" => array("350", "100", "px", "%"), "input_zone" => "dimensions", "output_zone" => "sl_template", "label" => "<nobr>".__("Map Dimensions (H x W)", "store-locator")."</nobr>", "input_template" => "<input name='height' value='$sl_vars[height]' size='3'>&nbsp;".sl_choose_units($sl_vars['height_units'], "height_units")." <span style='font-size:1.2em; vertical-align:middle'>X</span> <input name='width' value='$sl_vars[width]' size='3'>&nbsp;".sl_choose_units($sl_vars['width_units'], "width_units", ""), "numbers_only" => array(1, 1, 0, 0)
);

$the_distance_unit["".__("Km", "store-locator").""]="km";
$the_distance_unit["".__("Miles", "store-locator").""]="miles";
$radii_select = "";
foreach ($the_distance_unit as $key=>$value) {
	$selected = ($sl_vars['distance_unit']==$value)?" selected " : "";
	$radii_select .= "<option value='$value' $selected>$key</option>\n";
}
$sl_mdo[] = array("field_name" => array("distance_unit", "radii"),  "default" => array("miles", "1,5,10,25,(50),100,200,500"), "input_zone" => "dimensions", "output_zone" => array("sl_dyn_js", "sl_template"), "label" => "<nobr>".__("Radii Options", "store-locator")." (".__("in", "store-locator")." <select name='sl_distance_unit'>$radii_select</select>) </nobr>", "input_template" => "<input  name='radii' value='$sl_vars[radii]' size='25'><br><span style='font-size:80%'>(".__("Parentheses '( )' are for the default radius</span>", "store-locator").")");
###End Dimensions###

###Design###
$sl_mdo[] = array("field_name" => "theme", "default" =>"", "input_zone" => "design", "output_zone" => "sl_template", "label" => __("Choose Theme", "store-locator"), "input_template" => "<select name='theme' onchange=\"\"><option value=''>".__("No Theme Selected", "store-locator")."</option>$theme_str</select>&nbsp;&nbsp;&nbsp;<a href='http://www.viadat.com/products-page/store-locator-themes/' target='_blank'>".__("Get&nbsp;Themes", "store-locator")." &raquo;</a>");

$sl_mdo[] = array("field_name" => "remove_credits", "default" =>"0", "field_type" =>"checkbox", "input_zone" => "design", "output_zone" => "sl_template", "label" => __("Remove Credits", "store-locator"), "input_template" => "<input name='sl_remove_credits' value='1' type='checkbox' $checked3>");

$sl_mdo[] = array("field_name" => array("icon", "icon2"), "default" => array(SL_ICONS_BASE."/droplet_green.png", SL_ICONS_BASE."/droplet_red.png"), "input_zone" => "design", "output_zone" => array("sl_dyn_js", "sl_dyn_js"), "label" => "<input name='icon' size='20' value='$sl_vars[icon]' onchange=\"document.getElementById('prev').src=this.value\"><img id='prev' src='$sl_vars[icon]' align='top' rel='sl_pop' href='#home_icon' style='cursor:pointer;cursor:hand;height:60%;'> <br><a rel='sl_pop' href='#home_icon'><span style='font-size:80%'>".__("Choose", "store-locator")." ".__("Home Icon", "store-locator")."</span></a><div id='home_icon' style='display:none;'><h2 style='margin-top:0px'>".__("Choose", "store-locator")." ".__("Home Icon", "store-locator")."</h2>$icon_str</div>", "input_template" => "<input name='icon2' size='20' value='$sl_vars[icon2]' onchange=\"document.getElementById('prev2').src=this.value\"><img id='prev2' src='$sl_vars[icon2]' align='top' rel='sl_pop' href='#end_icon' style='cursor:pointer;cursor:hand;height:60%;'> <br><div id='end_icon' style='display:none;'><h2 style='margin-top:0px'>".__("Choose", "store-locator")." ".__("Destination Icon", "store-locator")."</h2>$icon2_str</div><a rel='sl_pop' href='#end_icon'><span style='font-size:80%'>".__("Choose", "store-locator")." ".__("Destination Icon", "store-locator")." </span></a>");


###End Design###

/*
$sl_mdo[] = array("input_zone" => "defaults", "label" => "Locations in Results", "input_template" => <<<EOQ
<input name='sl_num_initial_displayed' value='$sl_vars[num_initial_displayed]'>
EOQ
);*/

//if (function_exists("do_sl_hook") && defined("SL_AP_VERSION") && strnatcmp(SL_AP_VERSION, 1.4) > 0 ){
if (function_exists("do_sl_hook") && empty($_GET['via_platform'])) { //v3.75 - probably easiest way to prevent it from interfering with remote installs // v3.75.1 - don't forget "function_exists("do_sl_hook")"!
	do_sl_hook('sl_mapdesigner_options'); //, '', array(&$sl_mdo)); 
} //- removed v3.70 - reassess - add: defined('SL_IN_..MODE') checks to AP


$sl_mdo[] = array("input_zone" => "design", "label" => "<div class=''><b>".__("For more unique icons, visit", "store-locator")." <a href='http://code.google.com/p/google-maps-icons/' target='_blank'>Map Icons Collection</a>, <a href='https://www.iconfinder.com/search/?q=map&price=free' target='_blank'>Iconfinder</a>, & <a href='http://www.iconarchive.com/search?q=map' target='_blank'>IconArchive</a></b></div>", "input_template" => "", "colspan" => 2);
?>