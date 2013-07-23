<!DOCTYPE html>
<?php
	$server = "localhost";
	$db_username = "lou_schlessinger";
	$db_password = "admin000";
	$connection = mysql_connect($server, $db_username, $db_password) or die(mysql_error());
	$db = mysql_select_db("ethree_test", $connection) or die(mysql_error());
	
	$kids_query = mysql_query('SELECT * FROM kids') or die('couldn\'t get kids'.mysql_error());

	$counter = mysql_query('SELECT COUNT(*) AS id FROM kids');
	$num = mysql_fetch_array($counter);
	$count = $num['id'];
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
			#progressbar .ui-progressbar-value { background-color: #ccc; }
		</style>
		<script>
		$(document).ready(function() {
			$(function() {
				$( "input[type=submit], a, button" ).button().click(function( event ) {
					//event.preventDefault();
				});
			});
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
			$('#show').click(function(){
				 var txt = $("input#num_groups").val();
				 $.post("make_groups.php",{num_groups:txt},function(result){
					$("#result").html(result);
				  });
			    $('.hide').hide();
			    $(function() {
					$( "#progressbar" ).progressbar({
						value: false
				    });
					progressbar = $( "#progressbar" ),
					progressbarValue = progressbar.find( ".ui-progressbar-value" );
					progressbarValue.css({
					  "background": '#' + Math.floor( Math.random() * 16777215 ).toString( 16 )
					});
				});
				return false;
			});
			$(function() {
				$( "#num_groups" ).spinner({
					spin: function( event, ui ) {
						if ( ui.value > <?php echo $count; ?> ) {
							$( this ).spinner( "value", 1 );
							return false;
						} else if ( ui.value < 1 ) {
							$( this ).spinner( "value", <?php echo $count; ?> );
							return false;
						}
					},
					max: <?php echo $count; ?>,
					min: 1
				});
			});
			$(function() {
				$( "#age" ).spinner({
					spin: function( event, ui ) {
						if ( ui.value > 100 ) {
							$( this ).spinner( "value", 1 );
							return false;
						} else if ( ui.value < 1 ) {
							$( this ).spinner( "value", 100 );
							return false;
						}
					},
					max: 100,
					min: 1
				});
			});
			$(function() {
				$( ".format" ).buttonset();
			});
		});
  </script>
  <style>
  
  </style>
</head>
<body>
		</script>
		<?php include 'inputs.html'; ?>
	</head>
	<body>
	<div id="content">
			<table cellpadding='1' cellspacing='1' id='resultTable' style="border: 1px solid black; margin: 0 auto;">
				<thead>
					<tr>
						<th>Name (<em>id</em>)</th>
						<th>Skill level </th>
						<th>Interests</th>
						<th>Traits</th>
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
					print ($row['interests']);
					print ("</td>");
					print ("<td>");
					print ($row['traits']);
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
			<div id="result"  style="text-align: center; margin-top: 5px;">
			<div class="hide">
				<a id="show" href="#">Create Groups</a>
				<label for="num_groups">Number of Groups: </label>
				<input id="num_groups" name="num_groups">
			</div>
			<div id="progressbar"></div>
			</div>
			<?php mysql_close($connection); ?>
			</div>
	</body>
</html>