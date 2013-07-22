<!DOCTYPE html>
<?php
	$server = "localhost";
	$db_username = "lou_schlessinger";
	$db_password = "admin000";
	$connection = mysql_connect($server, $db_username, $db_password) or die(mysql_error());
	$db = mysql_select_db("ethree_test", $connection) or die(mysql_error());
	
	$kids_query = mysql_query('SELECT * FROM kids') or die('couldn\'t get kids'.mysql_error());
?>
<html>
	<head>
		<title>Matching Kids</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="Home" />
		<link type="text/css" rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/> 	
		<script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript" charset="utf-8"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
		<style>
			#newKidForm { margin: 0 auto; }
		</style>
		<script>
		$(document).ready(function() {
			$(function() {
				$("#memberSince").datepicker({ //edit Member Since
					showAnim: "slide",
					dateFormat: "yy-mm-dd",
					constrainInput: true,
					changeYear: true,
					changeMonth: true,
					maxDate: "+0d"
				}); 
			});
		});
		</script>
		<?php include 'inputs.html'; ?>
	</head>
	<body>
	<div id="content">
			<table cellpadding='1' cellspacing='1' id='resultTable' style="border: 1px solid black; margin: 0 auto;">
				<thead>
					<tr>
						<th>Kid Name (<em>id</em>)</th>
						<th>Skill level </th>
						<th>Interests/Passions</th>
						<th>Characteristics</th>
						<th>Gender</th>
						<th>Age</th>
						<th>Days of Membership</th>
						<th>Lat</th>
						<th>Lng</th>
					</tr>
				</thead>
				<tbody>
			<?php
				while ($row = mysql_fetch_array($kids_query)) {
					print ("<tr style='border: 1px solid black;'>");
					print ("<td>");
					print ($row['name'] . ' (<em>' . $row['id'] . '</em>)');
					print ("</td>");
					print ("<td>");
					print ($row['skill_level']);
					print ("</td>");
					print ("<td>");
					print ($row['interests_passions']);
					print ("</td>");
					print ("<td>");
					print ($row['characteristics']);
					print ("</td>");
					print ("<td>");
					print ($row['gender']);
					print ("</td>");
					print ("<td>");
					print ($row['age']);
					print ("</td>");
					print ("<td>");
					print ($row['days_of_membership']);
					print ("</td>");
					print ("<td>");
					print ($row['lat']);
					print ("</td>");
					print ("<td>");
					print ($row['lon']);
					print ("</td>");
					print ("</tr>");
				}	
			?>
				</tbody>
			</table>
			<div id="result">
			<?php include_once 'make_groups.php'; ?>
			</div>
			<?php mysql_close($connection); ?>
			</div>
	</body>
</html>