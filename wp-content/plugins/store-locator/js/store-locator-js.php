<?php
function js_out($buff) {
	preg_match("@\/\*sl\-dyn\-js\-start\*\/.*\/\*sl\-dyn\-js\-end\*\/@s", $buff, $the_js);
	$the_js[0]=preg_replace("@<script([^>]*)?src=('|\")?([A-Za-z0-9\.\ \_\:\/-]*)('|\")?([^>]*)?>(\r)?(\n)?@s", "jQuery.getScript(\"\\3\");\n", $the_js[0]);
	$the_js[0]=preg_replace("@<\/script>(\r)?(\n)?@s", "", $the_js[0]);
	$the_js[0]=preg_replace("@<script[^>]*>(\r)?(\n)?@s", "", $the_js[0]);
	$the_js[0]=preg_replace("@\/\*[^(\*\/)]*\*\/@s", "", $the_js[0]);
	//$the_js[0]=preg_replace("@[^http(s)?:]\/\/[^(\r|\n)]*@s", "", $the_js[0]);
	//$the_js[0]=preg_replace("@\r@s","",$the_js[0]);
	//$the_js[0]=preg_replace("@\n@s","",$the_js[0]);
	//$the_js[0]=preg_replace("@\t@s","",$the_js[0]);
	//$the_js[0]=base64_encode("<script>".$the_js[0]."</script>");
	//return "document.write(decode64('".$the_js[0]."'));";
	return $the_js[0];
}
ob_start("js_out");
header("Content-type: text/javascript");
include("../sl-inc/includes/sl-env.php");
print "/*sl-dyn-js-start*/";
sl_dyn_js();
print "/*sl-dyn-js-end*/";
ob_end_flush();
//var_dump($_GET);
?>