<?php

	/*
	|| #################################################################### ||
	|| #                             ArrowChat                            # ||
	|| # ---------------------------------------------------------------- # ||
	|| #    Copyright ©2010-2012 ArrowSuites LLC. All Rights Reserved.    # ||
	|| # This file may not be redistributed in whole or significant part. # ||
	|| # ---------------- ARROWCHAT IS NOT FREE SOFTWARE ---------------- # ||
	|| #   http://www.arrowchat.com | http://www.arrowchat.com/license/   # ||
	|| #################################################################### ||
	*/
	
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	// ########################## INCLUDE BACK-END ###########################
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');

	// ########################## GET POST DATA ###########################
	$room_id = $_GET['rid'];

	if (strlen($room_id) == 9) 
	{
		$room_id = "0".$room_id;
	}

	$username = get_username($userid);

	// ############################ START HTML ############################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html> 
<head> 
	<title><?php echo $language[200]; ?></title> 
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
 
	<style> 
	html, body, div, span, applet, object, iframe,
	h1, h2, h3, h4, h5, h6, p, blockquote, pre,
	a, abbr, acronym, address, big, cite, code,
	del, dfn, em, font, img, ins, kbd, q, s, samp,
	small, strike, strong, sub, sup, tt, var,
	dl, dt, dd, ol, ul, li,
	fieldset, form, label, legend,
	table, caption, tbody, tfoot, thead, tr, th, td {
		margin: 0;
		padding: 0;
		border: 0;
		outline: 0;
		font-weight: inherit;
		font-style: inherit;
		font-size: 100%;
		font-family: inherit;
		vertical-align: baseline;
		text-align: center;
	}
	 
	html {
	  height: 100%;
	  width: 100%;
	  overflow: hidden; /* Hides scrollbar in IE */
	}
	 
	body {
	  height: 100%;
	  width: 100%;
	  margin: 0;
	  padding: 0;
	}
	 
	#video_chat {
		position: fixed;
		top: 0px;
		left: 0px;
		bottom: 0px;
		min-width: 300px;
		width: 100%;
		background: #fff;
	} 
	iframe{
	    position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		height: 100%;
		width: 100%;
	}
	</style>  
</head> 
<body> 
	<div id="video_chat">
		<script type='text/javascript'> 
		var tinychat = { room: "arrowchat{<?php echo $room_id; ?>}", join: "auto", api: "none", change: "none", nick: "{<?php echo $username; ?>}", colorbk: "0xffffff"};
		</script> 
		<script src="https://tinychat.com/js/embed.js"></script> 
		<div id="client"></div> 
	</div>
</body>
</html>