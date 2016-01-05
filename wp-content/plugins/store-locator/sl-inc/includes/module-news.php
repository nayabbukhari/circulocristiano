<?php

// include lastRSS library
include_once (SL_ACTIONS_PATH."/lastRSS.php");
// create lastRSS object
$rss = new lastRSS; 
// setup transparent cache
$rss->cache_dir = SL_CACHE_PATH; 
$rss->cache_time = 3600*2; //2 hrs

// load some RSS file
if ($rs = $rss->get('http://feeds2.feedburner.com/Viadat')) {
    //var_dump($rs);
    $c=1;
   // print "<ul style='margin-left:0px'>";
    foreach ($rs['items'] as $value) {
	preg_match_all("@[^ ]* @",$value['pubDate'], $date_parts);
	//var_dump($date_parts);
	if ($c<=0) {
		print "<li><A href=\"$value[link]\" target='_blank' class='home_rss' style='font-size:12px'>
		<b>$value[title]</b></a></li><b>$value[pubDate]</b><br>
		<!--br-->
		<spa	n class='home_rss'> ".
		str_replace("]]>","",str_replace("</p>", "", str_replace("<![CDATA[", "", nl2br(html_entity_decode($value['description']))))).
		"</span><br><br>";
	} else {
		if ($c<=4) {
			print "<li style='font-size:12px; color:black; position:relative; left:0px; list-style-type:none;'><h3>{$date_parts[0][2]} ".trim(preg_replace("@^0*@", "", $date_parts[0][1])).", {$date_parts[0][3]}</h3><A href=\"$value[link]\" target='_blank' class='home_rss' style='font-size:12px'>$value[title]</a> </li>";
		}
	}
	$c++;
    }	
   // print "</ul>";
}
?>