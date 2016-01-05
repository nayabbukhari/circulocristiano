<?php
function xml_out($buff) {
	preg_match("@<markers>.*<\/markers>@s", $buff, $the_xml);
	//$the_xml[0]=preg_replace("@\n@","",$the_xml[0]);
	return $the_xml[0];
}
if (empty($_GET['debug'])) {
	ob_start("xml_out");
}
header("Content-type: text/xml");
include("sl-inc/includes/sl-env.php");

// Opens a connection to a MySQL server
$connection=mysql_connect ($host, $username, $password);
if (!$connection) { die('Not connected : ' . mysql_error()); }

// Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
mysql_query("SET NAMES utf8");
if (!$db_selected) { die ('Can\'t use db : ' . mysql_error());}

//Removing any vars never intended for $_GET
$sl_ap_xml = array("sl_custom_fields", "sl_xml_columns");
foreach ($sl_ap_xml as $value){ if (!empty($_GET[$value])){ unset($_GET[$value]); } }

$sl_custom_fields = (!empty($sl_xml_columns))? ", ".implode(", ", $sl_xml_columns) : "" ;

if (!empty($_GET)) { $_sl = $_GET; unset($_GET['mode']); unset($_GET['lat']); unset($_GET["lng"]); unset($_GET["radius"]); unset($_GET["edit"]);}
$_GET=array_filter($_GET); //removing any empty $_GET items that may disrupt query

$sl_param_where_clause="";
$sl_param_order_clause="";
if (function_exists("do_sl_hook")){ do_sl_hook("sl_xml_query"); }

$num_initial_displayed=(trim($sl_vars['num_initial_displayed'])!="" && preg_match("@^[0-9]+$@", $sl_vars['num_initial_displayed']))? $sl_vars['num_initial_displayed'] : "25";

if (!empty($_sl['mode']) && $_sl['mode']=='gen') {
	// Get parameters from URL
	$center_lat = $_sl['lat'];
	$center_lng = $_sl['lng'];
	$radius = $_sl['radius'];
	
	$multiplier=3959;
	$multiplier=($sl_vars['distance_unit']=="km")? ($multiplier*1.609344) : $multiplier;

	// Select all the rows in the markers table
	$query = sprintf(
	"SELECT sl_address, sl_address2, sl_store, sl_city, sl_state, sl_zip, sl_latitude, sl_longitude, sl_description, sl_url, sl_hours, sl_phone, sl_fax, sl_email, sl_image, sl_tags".
	" $sl_custom_fields,".
	" ( $multiplier * acos( cos( radians('%s') ) * cos( radians( sl_latitude ) ) * cos( radians( sl_longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( sl_latitude ) ) ) ) AS sl_distance".
	" FROM ".SL_TABLE.
	" WHERE sl_store<>'' AND sl_longitude<>'' AND sl_latitude<>''".
	" $sl_param_where_clause".
	" HAVING sl_distance < '%s' ORDER BY sl_distance LIMIT %d",
	esc_sql($center_lat),
	esc_sql($center_lng),
	esc_sql($center_lat),
	esc_sql($radius),
	esc_sql($num_initial_displayed)); //die($query);
} else {
	// Select all the rows in the markers table
	$query =  sprintf(
	"SELECT sl_address, sl_address2, sl_store, sl_city, sl_state, sl_zip, sl_latitude, sl_longitude, sl_description, sl_url, sl_hours, sl_phone, sl_fax, sl_email, sl_image, sl_tags".
	" $sl_custom_fields".
	" FROM ".SL_TABLE.
	" WHERE sl_store<>'' AND sl_longitude<>'' AND sl_latitude<>''".
	" $sl_param_where_clause".
	" $sl_param_order_clause".
	" LIMIT %d",
	esc_sql($num_initial_displayed)); //die($query);
}

//die($query);
$result = mysql_query($query);
if (!$result) { die('Invalid query: ' . mysql_error()); }

// Start XML file, echo parent node
echo "<markers>\n";
// Iterate through the rows, printing XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  $addr2=(trim($row['sl_address2'])!="")? " ".parseToXML($row['sl_address2']) : "" ;
  $row['sl_distance']=(!empty($row['sl_distance']))? $row['sl_distance'] : "" ;
  $row['sl_url']=(!url_test($row['sl_url']) && trim($row['sl_url'])!="")? "http://".$row['sl_url'] : $row['sl_url'] ;
  // ADD TO XML DOCUMENT NODE
  echo '<marker ';
  echo 'name="' . parseToXML($row['sl_store']) . '" ';
  echo 'address="' . parseToXML($row['sl_address']) .$addr2. ', '. parseToXML($row['sl_city']). ', ' .parseToXML($row['sl_state']).' ' .parseToXML($row['sl_zip']).'" ';
  echo 'street="' . parseToXML($row['sl_address']) . '" ';  //should've been sl_street in DB
  echo 'street2="' . parseToXML($row['sl_address2']) . '" '; //should've been sl_street2 in DB
  echo 'city="' . parseToXML($row['sl_city']). '" ';
  echo 'state="' . parseToXML($row['sl_state']). '" ';
  echo 'zip="' . parseToXML($row['sl_zip']). '" ';
  echo 'lat="' . $row['sl_latitude'] . '" ';
  echo 'lng="' . $row['sl_longitude'] . '" ';
  echo 'distance="' . $row['sl_distance'] . '" ';
  echo 'description="' . parseToXML($row['sl_description']) . '" ';
  echo 'url="' . parseToXML($row['sl_url']) . '" ';
  echo 'hours="' . parseToXML($row['sl_hours']) . '" ';
  echo 'phone="' . parseToXML($row['sl_phone']) . '" ';
  echo 'fax="' . parseToXML($row['sl_fax']) . '" ';
  echo 'email="' . parseToXML($row['sl_email']) . '" ';
  echo 'image="' . parseToXML($row['sl_image']) . '" ';
  echo 'tags="' . parseToXML($row['sl_tags']) . '" ';
  if (!empty($sl_xml_columns)){ 
  $alrdy_used=array('name', 'address', 'street', 'street2', 'city', 'state', 'zip', 'lat', 'lng', 'distance', 'description', 'url', 'hours', 'phone', 'fax', 'email', 'image', 'tags');
  	foreach($sl_xml_columns as $key=>$value) {
  		if (!in_array($value, $alrdy_used)) { //can't have duplicate property names in xml
	  		$row[$value]=(!isset($row[$value]))? "" : $row[$value] ;
  			 echo "$value=\"" . parseToXML($row[$value]) . "\" ";
  			 $alrdy_used[]=$value;
  		}
  	}
  }
  echo "/>\n";
}

// End XML file
echo "</markers>\n";
if (empty($_GET['debug'])) {
	ob_end_flush();
}

//var_dump($_GET);
//print_r($sl_xml_columns); die();
//die($query);
//var_dump($sl_param_where_clause); die;
?>