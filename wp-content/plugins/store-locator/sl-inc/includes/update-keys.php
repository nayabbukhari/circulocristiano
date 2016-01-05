<?php
if (!empty($_GET['validate_addons']) ){//&& !empty($_POST)){
	include("sl-env.php");
	sl_update_keys($_GET);
}

function sl_update_keys($post) {
$_POST=$post;
$partner_mode = (!empty($_POST['val_mode']) && preg_match("@partner@", $_POST['val_mode']));
$val_page = ($partner_mode)? "partner_update" : "confirm_single_license";
$val_chk = ($partner_mode)? "partner_" :  "sl_license_";
foreach ($_POST as $key=>$value) {
	if (preg_match("@$val_chk@", $key) && trim($value)!="") {
		$value=trim($value);
		$val_url = "/sl_validate/{$val_page}.php?lic=". urlencode($value) ."&url=". urlencode($_SERVER['HTTP_HOST'] )."&dir=". urlencode(str_replace("sl_license_","",$key));
		$val_url .= ($partner_mode)? "&val_mode=".trim($_POST['val_mode']) : "";
		$val_url .= ($partner_mode)? "&dev_mode=".trim($_POST['dev_mode']) : "";
		$target = "http://www.viadat.com". $val_url;
  		//exit($target);
		$remote_access_fail = false;
		$useragent = 'LotsOfLocales Store Locator Plugin';
  		if (function_exists("curl_init")) {
    			ob_start();
    			$ch = curl_init();
    			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    			curl_setopt($ch, CURLOPT_URL,$target);
    			curl_exec($ch);
		    	$returned_value = ob_get_contents();
			//exit($returned_value);
   			ob_end_clean();
		} else {
	  		$request = '';
	  		$http_request  = "GET ". $val_url ." HTTP/1.0\r\n";
			$http_request .= "Host: viadat.com\r\n";
			$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . SL_BLOG_CHARSET . "\r\n";
			$http_request .= "Content-Length: " . strlen($request) . "\r\n";
			$http_request .= "User-Agent: $useragent\r\n";
			$http_request .= "\r\n";
			$http_request .= $request;
			$response = '';
			if (false != ( $fs = @fsockopen('viadat.com', 80, $errno, $errstr, 10) ) ) {
				fwrite($fs, $http_request);
				while ( !feof($fs) )
					$response .= fgets($fs, 1160); // One TCP-IP packet
				fclose($fs);
			}
			$returned_value = trim($response);
		}
	 	
	 	if (preg_match("@validated:@",$returned_value)) {
	 		$activ = ($partner_mode)? str_replace("_key","_activation", $key) : str_replace("sl_license_", "sl_activation_", $key);
			$enc1=explode(":", trim($returned_value));
			$enc=$enc1[1];
			$key_option=sl_data("$key");
			$activ_option=sl_data("$activ");
			if (empty($key_option)) {
				sl_data("$key", 'add', $value);
			} else {
				sl_data("$key", 'update', $value);
			}
			if (empty($activ_option)) {
				sl_data("$activ", 'add', $enc);
			} else {
				sl_data("$activ", 'update', $enc);
			}
			if (!$partner_mode) {	
				sl_data("sl-addon-status___".str_replace("sl_license_","",$key), "add", "on");
				global $view_link;
				print "<div class='sl_admin_success'><b>".ucwords(preg_replace("@(-|_)@", " ", str_replace("sl_license_", "", $key)))."</b> -- Successful validation using key '$value' </div><br>";
			} else {
				print "<div class='sl_admin_success'>Successful update</div><script>location.replace('".SL_ADDONS_PAGE."');</script><br>";
			}
		} elseif ($returned_value==="") {
			print "<div class='sl_admin_success' style='border-color:red; background-color:salmon'>Error: No response. Validation server may be down (or your internet connection), please try again later.</div><br>";
		} else {
			print "<div class='sl_admin_warning'>$returned_value</div><br>";
	  	}
  
  	}
}
	if (!empty($returned_value)){
		return $returned_value;
	}
}
?>