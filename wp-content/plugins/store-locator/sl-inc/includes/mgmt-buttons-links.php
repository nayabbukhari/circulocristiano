<?php

print "<table width='100%' cellpadding='5px' cellspacing='0' style='border:solid silver 1px' id='mgmt_bar' class='widefat'>
<thead><tr>
<th style='/*background-color:#000;*/ width:10%; vertical-align:middle; font-family:inherit; font-size:12px;'><input class='button-primary' type='button' value='".__("Delete", "store-locator")."' onclick=\"if(confirm('".__("Delete", "store-locator")." -- ".__("You sure", "store-locator")."?')){LF=document.forms['locationForm'];LF.act.value='delete';LF.submit();}else{return false;}\"></th>";
$extra=(!empty($extra))? $extra : "" ;

if (function_exists("addto_sl_hook")) {addto_sl_hook('sl_mgmt_bar_links', 'export_links', '', '', 'csv-xml-importer-exporter');} 
$mgmt_bgcolor=((!function_exists("addto_sl_hook") && file_exists(SL_ADDONS_PATH."/csv-xml-importer-exporter/export-links.php")) || (!empty($sl_hooks['sl_mgmt_bar_links'])) )? "/*background-color:#e6e6e6; background-image:none;*/ border-left: solid #ccc 1px; border-right: solid #ccc 1px;" : "";
print "<th style='width:30%; text-align:center; color:black; font-family:inherit; font-size:12px; {$mgmt_bgcolor} /**/' class='youhave'>";
function export_links() {
		global $sl_uploads_path, $web_domain, $extra, $sl_base, $sl_uploads_base, $text_domain;
		if (file_exists(SL_ADDONS_PATH."/csv-xml-importer-exporter/export-links.php")) {
			$sl_real_base=$sl_base; $sl_base=$sl_uploads_base;
			include(SL_ADDONS_PATH."/csv-xml-importer-exporter/export-links.php");
			$sl_base=$sl_real_base;
		}
		
} 
if (!function_exists("addto_sl_hook")) {export_links();}
if (function_exists("do_sl_hook")) { do_sl_hook('sl_mgmt_bar_links', 'select-right');  }

print "</th>";
print "<th style='/*background-color:#000;*/ width:50%; text-align:right; /*color:white;*/ vertical-align:middle; font-family:inherit; font-size:12px;'>";

  function multi_updater() {
	global $sl_uploads_path, $text_domain, $web_domain;
	if (file_exists(SL_ADDONS_PATH."/multiple-field-updater/multiple-field-update-form.php") && (sl_data('sl_location_updater_type')=="Multiple Fields" || function_exists("do_sl_hook"))) {
		include(SL_ADDONS_PATH."/multiple-field-updater/multiple-field-update-form.php");
	}
  }
	
	function tagger() {
		print "<!--strong-->".__("Tags", "store-locator")."<!--/strong-->&nbsp;<input name='sl_tags' style='margin: 1px; padding:3px; line-height:15px;'>&nbsp;<input class='button-primary' type='button' value='".__("Add Tag", "store-locator")."' onclick=\"LF=document.forms['locationForm'];LF.act.value='add_tag';LF.submit();\">&nbsp;<input class='button-primary' type='button' value='".__("Remove Tag", "store-locator")."' onclick=\"if(confirm('".__("Remove Tag", "store-locator")." -- ".__("You sure", "store-locator")."?')){LF=document.forms['locationForm'];LF.act.value='remove_tag';LF.submit();}else{return false;}\">";
	}
  
	if (function_exists("addto_sl_hook")) {
	    if (is_dir(SL_ADDONS_PATH."/multiple-field-updater/")){
		addto_sl_hook('sl_mgmt_bar_form', 'multi_updater', '', '', 'multiple-field-updater');
	    }
	    addto_sl_hook('sl_mgmt_bar_form', 'tagger');
	} elseif (!function_exists("addto_sl_hook")) {
		if (file_exists(SL_ADDONS_PATH."/multiple-field-updater/multiple-field-update-form.php") && sl_data('sl_location_updater_type')=="Multiple Fields") {
			multi_updater();
		} else {
			 tagger();
		}
	}

	if (function_exists("do_sl_hook")) {do_sl_hook('sl_mgmt_bar_form', 'select');
  }
print "</th></tr></thead></table>
";

?>