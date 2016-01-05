<?php 
if (function_exists("file_get_contents") && 1==2) {
	$main=file_get_contents(SL_PATH."/store-locator.php");
	$head_comment=token_get_all($main); 
	$hc=$head_comment[1][1];
	$hc=preg_replace("/\r\n([^:]+:)/", "\r\n<b>\\1</b>", $hc);
	$hc=do_hyperlink($hc, "'_blank'", "protocol");
	print nl2br(preg_replace("@((\r\n)?\*/|/\*(\r\n))?@", "", $hc))."<br><br>";
} else {
	print "<b>Plugin Name:</b> Store Locator<br>
	<b>Plugin URI:</b> <a href='http://www.viadat.com/store-locator/'>http://www.viadat.com/store-locator/</a><br>
	<b>Version:</b> ".SL_VERSION."<br>
	<b>Author:</b> Viadat Creations<br>
	<b>Author URI:</b> <a href='http://www.viadat.com'>http://www.viadat.com</a><br><br>";
}

?>
<strong>View:&nbsp;<a href='#' id='readme_button'>Instructions</a>&nbsp;|&nbsp;<a href="#server_caps" rel="sl_pop" id='server_caps_button'>Server Capabilities</a>&nbsp;|&nbsp;<a href="#shortcode_params" rel="sl_pop" id='shortcode_params_button'>Shortcode Params</a></strong>
<table id="" style="display: none;"><tr><td><div id='server_caps'>
<strong>Your Server Capabilities:</strong><br>
<table cellpadding='4px' width='100%' class='sl_code code'>
<tr><td valign='top'><b>PHP:</b></td><td><?php include(SL_INFO_PATH."/php-ver.php"); ?></td></tr>
<tr><td valign='top'><b>Zip:</b></td><td><?php include(SL_INFO_PATH."/zip-info.php"); ?></td></tr>
<tr><td valign='top'><b>Extensions:</b></td><td><pre style='line-height:10px; font-size:11px; padding-top:0px; margin-top:0px;'><?php print_r(get_loaded_extensions()); ?></pre></td></tr>
</table></div>
</td></tr></table>
<table id="" style="display: none;"><tr><td>
<div id='shortcode_params'>
<?php
global $sl_all_columns;
if (!empty($sl_all_columns) && $vals=implode(', ', $sl_all_columns)) {
	print "<b>Available Shortcode Parameters:</b><br><br><div class='sl_code code'>".$vals;
} else {
	print "<b>Available Shortcode Parameters:</b><br><br><div class='sl_code code'>sl_id, sl_store, sl_address, sl_address2, sl_city, sl_state, sl_zip, sl_latitude, sl_longitude, sl_tags, sl_description, sl_url, sl_hours, sl_phone, sl_fax, sl_email, sl_image, sl_private, sl_neat_title";
}
print "</div><br><b>Example Usage:</b><br><br><div class='sl_code code'>[STORE-LOCATOR sl_city='Washington' not_sl_zip='20001']</div>
(Shows all locations in the city of 'Washington' without a zip code of '20001')<br><br>
<b>Shortcode Parameters Are Useful For:</b><br><br>
- Creating multiple Store Locator maps on your website<br>
- Modifying the layout of your store locator using a theme template<br>
- Automatically creating unique pages for each store in your Store Locator's database<br>
<br>
<b>Requirements:</b><br><br>
<a href='http://www.viadat.com/products-page/' target='_blank'>LotsOfLocales&trade; Addons Platform</a>, then install the following addons:<br>
- Multiple Mapper<br>
- Advanced Theme Manager<br>
- Location Pages";
?>
</div>
</td></tr></table>