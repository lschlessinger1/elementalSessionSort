<?php
$server = "localhost";
$db_username = "lou_schlessinger";
$db_password = "admin000";
$connection = mysql_connect($server, $db_username, $db_password) or die(mysql_error());
$db = mysql_select_db("ethree_test", $connection) or die(mysql_error());

$name = mysql_real_escape_string(htmlentities(trim($_POST['new_name']))); // any string
$gender = mysql_real_escape_string(htmlentities(trim($_POST['new_gender']))); // "male" or "female"
$skill_level = mysql_real_escape_string(htmlentities(trim($_POST['new_skill_level']))); //1, 2 or 3
$age = mysql_real_escape_string(htmlentities(trim($_POST['new_age']))); //positive int
$member_since = mysql_real_escape_string(htmlentities(trim($_POST['new_member_since']))); //yyyy-mm-dd
$now = time(); 
$my_date = strtotime($member_since);
$datediff = $now - $my_date;
$days_of_membership = floor($datediff/(60*60*24)); // >= 1
$interests_passions = ''; // 1 - 10
$characteristics = ''; // 1 - 10
foreach($_POST['new_interests_passions'] as $item){
	$interests_passions .= $item . ', ';
}
foreach($_POST['new_characteristics'] as $item){
	$characteristics .= $item . ', ';
}

$address_line_1 = mysql_real_escape_string(htmlentities(trim($_POST['new_address_line_1'])));
$address_line_2 = mysql_real_escape_string(htmlentities(trim($_POST['new_address_line_2'])));
$city = mysql_real_escape_string(htmlentities(trim($_POST['new_city'])));
$state_province_region = mysql_real_escape_string(htmlentities(trim($_POST['new_state_province_region'])));
$postal_code = mysql_real_escape_string(htmlentities(trim($_POST['new_postal_code'])));
$country = mysql_real_escape_string(htmlentities(trim($_POST['new_country'])));

$address = $address_line_1.' '.$address_line_2.', '.$city.', '.$state_province_region.' '.$postal_code. ' '.$country;
$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false";
$get = file_get_contents($url);
$lat = json_decode($get)->results[0]->geometry->location->lat;
$lon = json_decode($get)->results[0]->geometry->location->lng;

mysql_query("INSERT INTO kids (skill_level, interests_passions, characteristics, name, gender, age, days_of_membership, lat, lon)
 VALUES ('".$skill_level."', '".substr($interests_passions,0,-2)."', '".substr($characteristics,0,-2)."', '".$name."', '".$gender."', '".$age."', '".$days_of_membership."', '".$lat."', '".$lon."')") or die ("Could not perform query... ".mysql_error());
mysql_close($connection);
header("Location:home.php");
exit;
?>