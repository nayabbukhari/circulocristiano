<table cellpadding='3px' class='tablenav' style='float:right'><tr><td valign=bottom style='padding-left:0px; float:left;' class='tablenav-pages'>
<?php
$pos=0;
if ($start<0 || $start==="" || !isset($start) || empty($start)) {$start=0;}
if ($num_per_page<0 || $num_per_page==="") {$num_per_page=10;}
$prev=$start-$num_per_page;
$next=$start+$num_per_page;
if (preg_match("@&start=$start@",$_SERVER['QUERY_STRING'])) {
	$prev_page=str_replace("&start=$start","&start=$prev",$_SERVER['QUERY_STRING']);
	$next_page=str_replace("&start=$start","&start=$next",$_SERVER['QUERY_STRING']); //echo($next_page);
}
else {
	$prev_page=$_SERVER['QUERY_STRING']."&start=$prev";
	$next_page=$_SERVER['QUERY_STRING']."&start=$next";
}
if ($numMembers2>$num_per_page) {
	//print "  | ";

if ((($start/$num_per_page)+1)-3<1) {
	$beginning_link=1;
}
else {
	$beginning_link=(($start/$num_per_page)+1)-3;
}
if ((($start/$num_per_page)+1)+4>(($numMembers2/$num_per_page)+1)) {
	$end_link=(($numMembers2/$num_per_page)+1);
}
else {
	$end_link=(($start/$num_per_page)+1)+4;
}
$pos=($beginning_link-1)*$num_per_page;
	for ($k=$beginning_link; $k<$end_link; $k++) {
		if (preg_match("@&start=$start@",$_SERVER['QUERY_STRING'])) {
			$curr_page=str_replace("&start=$start","&start=$pos",$_SERVER['QUERY_STRING']);
		}
		else {
			$curr_page=$_SERVER['QUERY_STRING']."&start=$pos";
		}
		if (($start-($k-1)*$num_per_page)<0 || ($start-($k-1)*$num_per_page)>=$num_per_page) {
			print "<a class='' href=\"{$_SERVER['PHP_SELF']}?$curr_page\" rel='nofollow'>";
		}
		print $k;
		if (($start-($k-1)*$num_per_page)<0 || ($start-($k-1)*$num_per_page)>=$num_per_page) {
			print "</a>";
		}
		$pos=$pos+$num_per_page;
		print "&nbsp;&nbsp;";
	}
}
$cleared=(!empty($_GET['q']))? str_replace("q=$_GET[q]", "", $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI'] ;
$extra_text=(!empty($_GET['q']))? __("for your search of", "store-locator")." <strong>\"$_GET[q]\"</strong>&nbsp;|&nbsp;<a href='$cleared'>".__("Clear&nbsp;Results", "store-locator")."</a>" : "" ;
?>
</td>
<td align='center' valign='middle' style=''><div class='' style='padding:5px; font-weight:normal'>
<?php 

	$end_num=($numMembers2<($start+$num_per_page))? $numMembers2 : ($start+$num_per_page) ;
	print "<nobr>".__("Results", "store-locator")." <strong>".($start+1)." - ".$end_num."</strong>"; 
	//if (!empty($_GET['u']) && !preg_match("@doSearch@", $_GET['u'])) {
		print " ($numMembers2 ".__("total", "store-locator").") ".$extra_text; 
	//}
	print "</nobr>";

?>
</div>
</td>
<td align=right valign=bottom style='padding-right:0px;' class='tablenav-pages'>
<!--table><tr><td width=75><img src='/images/spacer.gif' height=1 width=75 alt='' border=0--><nobr>
<?php 
if (($start-$num_per_page)>=0) { ?>
<a class='' href="<?php print "{$_SERVER['PHP_SELF']}?$prev_page"; ?>" rel='nofollow'>&laquo;&nbsp;<?php print __("Previous", "store-locator")."&nbsp;$num_per_page"; ?></a>
<?php } 
if (($start-$num_per_page)>=0 && ($start+$num_per_page)<$numMembers2) { ?>
<!--&nbsp;&nbsp;|&nbsp;-->
<?php } ?>
<!--/td>
<td width='85px' valign=bottom><img src='/images/spacer.gif' height=1 width=45 alt='' border=0-->
<?php 
if (($start+$num_per_page)<$numMembers2) { ?>
<a class='' href="<?php print "{$_SERVER['PHP_SELF']}?$next_page"; ?>" rel='nofollow'><?php print __("Next", "store-locator")."&nbsp;$num_per_page"; ?>&nbsp;&raquo;</a><br>
<?php } ?>
</nobr>
<!--/td></tr></table-->
</td>
</tr>
</table>