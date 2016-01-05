<?php

print "<div style='/*overflow:scroll; height:200px;*/ padding:1px;'>";

$lic_str="";
if (is_dir(SL_ADDONS_PATH)) {
	$a_lic_arr=array();
	$g1[]="csv-xml-importer-exporter"; $g1[]="db-importer"; $g1[]="point-click-add"; $g1[]="multiple-field-updater";
	//$plat[]="addons-platform.php";
	$ao_dir=opendir(SL_ADDONS_PATH); $ctr=0;
	print "<table width='95%' border='0'><tr>"; 
	while (false !== ($a_lic=readdir($ao_dir))) {
		if (in_array($a_lic, $g1) || (preg_match("/^addons\-platform/", $a_lic) && is_dir(SL_ADDONS_PATH."/".$a_lic)) ) {
			$a_lic=(preg_match("/^addons\-platform/", $a_lic))? str_replace(".php","",$a_lic) : $a_lic ;

			$style="style='border:red; background-color:salmon'";
			if (sl_data('sl_activation_'.$a_lic)!="") {
				$a_lic_arr["sl_activation_".$a_lic]=sl_data('sl_activation_'.$a_lic);
				$style="style='border:green; background-color:LightGreen'";
			}
			if (sl_data('sl_license_'.$a_lic)!="") {
				$a_lic_arr["sl_license_".$a_lic]=sl_data('sl_license_'.$a_lic);
			
			}
			$a_lic_arr["sl_license_".$a_lic]=(!empty($a_lic_arr["sl_license_".$a_lic]))? $a_lic_arr["sl_license_".$a_lic] : "";
			$a_lic_arr["sl_activation_".$a_lic]=(!empty($a_lic_arr["sl_activation_".$a_lic]))? $a_lic_arr["sl_activation_".$a_lic] : "";
			$lic_str.="<td><div class='sl_admin_success' $style><b>".ucwords(str_replace("-", " ", $a_lic))."</b>";
			$lic_str.="</div>
<table style='border:none'>
<tr>
<td>".__("Key", "store-locator").":&nbsp;&nbsp;</td><!--/tr>
<tr--><td><input name='sl_license_".$a_lic."' value='".$a_lic_arr["sl_license_".$a_lic]."' size='12' style='vertical-align:middle'>
<input name='sl_activation_".$a_lic."' value='".$a_lic_arr["sl_activation_".$a_lic]."' type='hidden'></td></tr>
</table>
</td>";

			if ($ctr%2==1) {$lic_str.="</tr><tr>";}
			$ctr++; 
		}
	}
	print "</table>";
}

print "<form name='licenseForm' id='licenseForm'><table style='border:none'><tr>$lic_str</tr></table><br>
<input type='hidden' name='validate_addons' value='1'>
</form>
<input class='button-primary' type='button' value='".__("Activate", "store-locator")."' onclick=\"showLoadImg('show', 'module-keys');validate_addons(document.getElementById('licenseForm')); return false;\">&nbsp;&nbsp;".__("Looking for more addons & themes", "store-locator")."? <a href='http://www.viadat.com/products-page/' target='_blank'>".__("They're all right here", "store-locator")."</a><br><br>
<a rel='sl_pop' href='#validation_status' id='validation_status_link' style='display:none'></a>
</div>";

?>