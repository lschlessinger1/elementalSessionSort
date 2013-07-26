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
			.scrollup{
				width:40px;
				height:40px;
				opacity:0.3;
				position:fixed;
				bottom:50px;
				right:100px;
				display:none;
				text-indent:-9999px;
				background: url('icon_top.png') no-repeat;
			}
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
				 var numGroupsVal = $("input#num_groups").val();
				 var ageVal = parseInt($('#ageSlider').slider("option", "value"));
				 var skillLevelVal = parseInt($('#skillLevelSlider').slider("option", "value"));
				 var interestsVal = parseInt($('#interestsLevelSlider').slider("option", "value"));
				 var traitsVal = parseInt($('#traitsSlider').slider("option", "value"));
				 var genderVal = parseInt($('#genderSlider').slider("option", "value"));
				 var daysOfMembershipVal = parseInt($('#daysOfMemberrshipSlider').slider("option", "value"));
				 var distanceVal = parseInt($('#distanceSlider').slider("option", "value"));
				 var comparisonWeights = {
					num_groups : numGroupsVal,
					age : ageVal,
					skill_level : skillLevelVal,
					interests : interestsVal,
					traits : traitsVal,
					gender : genderVal,
					days_of_membership : daysOfMembershipVal,
					distance : distanceVal
				 };
				 $.post("make_groups.php", comparisonWeights, function(result){
					$("#result").html(result);
					$(function(){
					$('#makeNewGroups').button();
						$('#makeNewGroups').on('click',function () {
							  location.reload();	
						});
					});
				  });
				$(".hide").hide("slow");
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
			
			/* Sliders */
			
			$(function() {
				$( "#skillLevelSlider" ).slider({
					value:1,
					min: 1,
					max: 10,
					animate: true,
					step: 1,
					slide: function( event, ui ) {
							$( "#skillLevelVal" ).val( ui.value );
					}
				});
				$( "#skillLevelVal" ).val($( "#skillLevelSlider" ).slider( "value" ));
			});
			$(function() {
				$( "#ageSlider" ).slider({
					value:1,
					min: 1,
					max: 10,
					animate: true,
					step: 1,
					slide: function( event, ui ) {
							$( "#ageVal" ).val( ui.value );
					}
				});
				$( "#ageVal" ).val($( "#ageSlider" ).slider( "value" ));
			});
			$(function() {
				$( "#interestsSlider" ).slider({
					value:1,
					min: 1,
					max: 10,
					animate: true,
					step: 1,
					slide: function( event, ui ) {
							$( "#interestsVal" ).val( ui.value );
					}
				});
				$( "#interestsVal" ).val($( "#interestsSlider" ).slider( "value" ));
			});
			$(function() {
				$( "#traitsSlider" ).slider({
					value:1,
					min: 1,
					max: 10,
					animate: true,
					step: 1,
					slide: function( event, ui ) {
							$( "#traitsVal" ).val( ui.value );
					}
				});
				$( "#traitsVal" ).val($( "#traitsSlider" ).slider( "value" ));
			});
			$(function() {
				$( "#genderSlider" ).slider({
					value:1,
					min: 1,
					max: 10,
					animate: true,
					step: 1,
					slide: function( event, ui ) {
							$( "#genderVal" ).val( ui.value );
					}
				});
				$( "#genderVal" ).val($( "#genderSlider" ).slider( "value" ));
			});
			$(function() {
				$( "#daysOfMemberrshipSlider" ).slider({
					value:1,
					min: 1,
					max: 10,
					animate: true,
					step: 1,
					slide: function( event, ui ) {
							$( "#daysOfMemberrshipVal" ).val( ui.value );
					}
				});
				$( "#daysOfMemberrshipVal" ).val($( "#daysOfMemberrshipSlider" ).slider( "value" ));
			});
			$(function() {
				$( "#distanceSlider" ).slider({
					value:1,
					min: 1,
					max: 10,
					animate: true,
					step: 1,
					slide: function( event, ui ) {
							$( "#distanceVal" ).val( ui.value );
					}
				});
				$( "#distanceVal" ).val($( "#distanceSlider" ).slider( "value" ));
			});
			$(window).scroll(function(){
				if ($(this).scrollTop() > 100) {
					$('.scrollup').fadeIn();
				} else {
					$('.scrollup').fadeOut();
				}
			}); 
			$('.scrollup').click(function(){
				$("html, body").animate({ scrollTop: 0 }, 600);
				return false;
			});
			$(function() {
				$( "#tabs" ).tabs();
			});
		});
		</script>
	</head>
	<body>
	<div id="content">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-3">Groups</a></li>
				<li><a href="#tabs-2">Session</a></li>
				<li><a href="#tabs-1">Create a kid</a></li>
			</ul>
			<div id="tabs-3">
				<?php include 'new_group.html'; ?>
			</div>
			<div id="tabs-1">
				<?php include 'inputs.html'; ?>
			</div>
			<div id="tabs-2">
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
			</div>
		</div>
			<?php mysql_close($connection); ?>
		</div>
			<a href="#" class="scrollup">Scroll</a>
	</body>
</html>