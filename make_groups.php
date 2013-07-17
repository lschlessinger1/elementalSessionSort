<?php
/* TO DO: */
/*Make static comparisons (comparisons that won't change ex: location)
either have less weight or have an element of randomness to promote new groups */
$server = "localhost";
$db_username = "lou_schlessinger";
$db_password = "admin000";
$connection = mysql_connect($server, $db_username, $db_password) or die('connection error: '.mysql_error());
$db = mysql_select_db("ethree_test", $connection) or die('database selection error: '.mysql_error());

$kids_query = mysql_query('SELECT * FROM kids') or die('couldn\'t get kids '.mysql_error());

$kids  = [];
while ($row = mysql_fetch_array($kids_query)) {
	array_push($kids, new Kid($row['id'], $row['name'], $row['skill_level'],
	$row['age'], $row['interests_passions'], $row['characteristics'], $row['gender'],
	$row['days_of_membership'], $row['lat'], $row['lon']));
}
$session = new Session($kids);

class Kid {
	private $id;
	private $name;
	private $skill_level;
	private $age;
	private $interests_passions;
	private $characteristics;
	private $gender;
	private $days_of_membership;
	private $lat;
	private $lon;
	
	public function __construct($id, $name, $skill_level, $age, $interests_passions, $characteristics, $gender, $days_of_membership, $lat, $lon){
		$this->id = $id;
		$this->name = $name;
		$this->skill_level = $skill_level;
		$this->age = $age;
		$this->interests_passions = $interests_passions;
		$this->characteristics = $characteristics;
		$this->gender = $gender;
		$this->days_of_membership = $days_of_membership;
		$this->lat = $lat;
		$this->lon = $lon;
	}
	/** BEGIN Getter and Setter methods**/
	public function get_id(){
		return $this->id;
	}
	public function set_id($id){
		$this->id = $id;
	}
	public function get_name(){
		return $this->name;
	}
	public function set_name($name){
		$this->name = $name;
	}
	public function get_skill_level(){
		return $this->skill_level;
	}
	public function set_skill_level($skill_level){
		$this->skill_level = $skill_level;
	}
	public function get_age(){
		return $this->age;
	}
	public function set_age($age){
		$this->age = $age;
	}
	public function get_interests_passions(){
		return $this->interests_passions;
	}
	public function set_interests_passions($interests_passions){
		$this->interests_passions = $interests_passions;
	}
	public function get_characteristics(){
		return $this->characteristics;
	}
	public function set_characteristics($characteristics){
		$this->characteristics = $characteristics;
	}
	public function get_gender(){
		return $this->gender;
	}
	public function set_gender($gender){
		$this->gender = $gender;
	}
	public function get_days_of_membership(){
		return $this->days_of_membership;
	}
	public function set_days_of_membership($days_of_membership){
		$this->days_of_membership = $days_of_membership;
	}
	public function get_lat(){
		return $this->lat;
	}
	public function set_lat($lat){
		$this->lat = $lat;
	}
	public function get_lon(){
		return $this->lon;
	}
	public function set_lon($lon){
		$this->lon = $lon;
	}
	/** END Getter and Setter methods**/
}
class Session {
	private $kids; // disorganized array of Kid objects
	private $match_points_arr = []; // $kid_a.id => [kid_b.id => matchpoints between kid_a and kid_b]
	private $session = []; // organized array of Kid objects
	private $num_groups = 4; // for now... -- maybe put in constructor?
	private $group_quorum = 10; //How many kids are enough to just return one group?
	public function __construct($kids){
		$this->kids = $kids;
		$this->run();
	}
	public function run(){
		$num_kids = count($this->kids);
		if($this->group_quorum < $num_kids){
			for($i=0; $i < $num_kids; $i++){
				for($j=0; $j<$i; $j++){
					$push_me = array($this->kids[$i]->get_id() => $this->kids[$j]->get_id() ,'match_points' => $this->get_match_points($this->kids[$i], $this->kids[$j])); // compare Kid_a with kid_b 
					array_push($this->match_points_arr, $push_me);
				}
			}
		}
		//$final_groups = $this->check_groups($this->create_groups()); 
		$this->prettify( $this->create_groups() ); // will be $final_groups
	}
	public function prettify($the_groups){
		$c = 1;
		foreach($the_groups as $g){
			echo '<h3 style="text-align:center; margin-bottom: -10px;">Group # '.$c.'</h3>';
			echo '<table cellpadding="1" cellspacing="1" style="border: 1px solid red; margin:0 auto;">';
			echo '<tbody>';
			echo '<thead>
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
				</thead>';
			echo '<br />';
			foreach($g as $kid_id){
				$kid = mysql_query('SELECT * FROM kids WHERE id = '.$kid_id);
					while ($row = mysql_fetch_array($kid)) {
						print ("<tr>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['name'] . ' (<em>' . $row['id'] . '</em>)');
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['skill_level']);
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['interests_passions']);
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['characteristics']);
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['gender']);
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['age']);
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['days_of_membership']);
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['lat']);
						print ("</td>");
						print ("<td>");
						print ($row['lon']);
						print ("</td>");
						print ("</tr>");
					}
				}
				echo '</tbody>';
				echo '</table>';
				$c++; //    :)
			}
	}
	public function get_match_points($kid_a, $kid_b) {
		$match_points = 0;
		$match_points += rand(1,10); // to promote new groups
		$skill_level_points = $this->compare_skill_level($kid_a, $kid_b);
		$age_points = $this->compare_age($kid_a, $kid_b);
		$interests_passions_points = $this->compare_interests_passions($kid_a, $kid_b);
		$characteristics = $this->compare_characteristics($kid_a, $kid_b);
		$gender_points = $this->compare_gender($kid_a, $kid_b);
		$days_of_membership_points = $this->compare_days_of_membership($kid_a, $kid_b);
		$location_points = $this->compare_location($kid_a, $kid_b);
		// any other criteria I need?
		$match_points += $skill_level_points + $age_points + $interests_passions_points
		+ $characteristics + $gender_points + $days_of_membership_points + $location_points;
		return intval($match_points);
	}
	public function create_groups(){
		// perform some other checks such as balancing boys and girls.
		// how can I find scenario with most match points?
		$num_groups = $this->num_groups;
		$num_kids = count($this->kids);
		//$kids_per_group = $this->make_groups($num_groups, $num_kids);
		
		$tmp = array(); 
		foreach($this->match_points_arr as &$kid) {
			$tmp[] = &$kid['match_points']; 
		}
		array_multisort($tmp, SORT_DESC, $this->match_points_arr); 
		return $this->get_best_groups(); // multi-dimensional array of groups with kids
	}
	public function get_best_groups(){ 
		$return_me = [];
		$num_kid_pairs = 0;
		for($i=0; $i<$this->num_groups; $i++){
			array_push($return_me, array());
		}
		foreach($this->match_points_arr as &$kid_pair) {
			$kid_a = key($kid_pair);
			$kid_b = $kid_pair[$kid_a];
			if(!$this->in_array_r($kid_a, $return_me) && !$this->in_array_r($kid_b, $return_me) && !$this->in_array_r($return_me,$kid_pair)){
				//push kid pair
				array_push($return_me[$num_kid_pairs % $this->num_groups], $kid_a);
				array_push($return_me[$num_kid_pairs % $this->num_groups], $kid_b);
				$num_kid_pairs++;
			}
		}
		return $return_me;
	}
	public function in_array_r($needle, $haystack, $strict = false) { //recursive in_array()
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}
	public function compare_skill_level($kid_a, $kid_b){ // either 1, 2, or 3
		// should this be linear? 
		$skill_level_points = 0;
		$skill_level_a = $kid_a->get_skill_level();
		$skill_level_b = $kid_b->get_skill_level();
		$diff = abs($skill_level_a - $skill_level_b); // so if they are same level - no match points
		$skill_level_points += $diff;
		return $skill_level_points;
	}
	public function compare_age($kid_a, $kid_b){
		$age_points = 0;
		$age_a = $kid_a->get_age();
		$age_b = $kid_b->get_age();
		$diff = abs($age_a - $age_b); // so if they are same level - no match points
		// maybe find difference in age 
		// but does it matter if kid_a.age is 4 and kid_b.age is 7
		// versus kid_a.age is 14 and kid_age.age is 17 ????
		// are age points linear?
		$age_points += $diff;
		return $age_points;
	}
	public function compare_interests_passions($kid_a, $kid_b){ // what will be format of interests/passions?
		$interests_passions_points = 0;
		$interests_passions_a = explode(', ', $kid_a->get_interests_passions()); 
		$interests_passions_b = explode(', ', $kid_b->get_interests_passions());
		// look for synonyms
		// for now return a number based on if the numbers match
		$common_interests_passions = count(array_intersect($interests_passions_a, $interests_passions_b));
		$interests_passions_points += $common_interests_passions;
		/* in future */
		// go through each kid and see how common it is for a kid to have certain interests/passions 
		return $interests_passions_points;
	}
	public function compare_characteristics($kid_a, $kid_b){
		$characteristics_points = 0;
		$characteristics_a = explode(', ', $kid_a->get_characteristics()); // does this work????
		$characteristics_b = explode(', ', $kid_b->get_characteristics());
		// for now return a number based on if the numbers match
		$common_characteristics = count(array_intersect($characteristics_a, $characteristics_b));
		$characteristics_points += $common_characteristics;
		/* in future */
		// look for synonyms
		// go through each kid and see how common it is for a kid to have certain characteristics 
		return $characteristics_points;
	}
	public function compare_gender($kid_a, $kid_b){
		$gender_points = 0;
		$gender_a = $kid_a->get_gender();
		$gender_b = $kid_b->get_gender();
		if($gender_a !== $gender_b){
			$gender_points += 5;
		}
		// if else if different return constant
		return $gender_points;
	}
	public function compare_days_of_membership($kid_a, $kid_b){
		$days_of_membership_points = 0;
		$days_of_membership_a = intval($kid_a->get_days_of_membership());
		$days_of_membership_b = intval($kid_b->get_days_of_membership());
		$diff = abs($days_of_membership_a - $days_of_membership_b);
		//default value if under 100 days?
		if($days_of_membership_a < 100 && $days_of_membership_b < 100){ // then they are considered N00B5
			$days_of_membership_points += 3;
		} elseif($diff >= 100){
			$days_of_membership_points += 3;
		}
		return $days_of_membership_points;
	}
	public function compare_location($kid_a, $kid_b){
		// logarithmic
		$location_points = 0;
		$lat_a = $kid_a->get_lat();
		$lon_a = $kid_a->get_lon();
		$lat_b = $kid_b->get_lat();
		$lon_b = $kid_b->get_lon();
		$distance = $this->haversine_great_circle_distance($lat_a, $lon_a, $lat_b, $lon_b, $earthRadius = 6371); // in kilometres. 6,371,000 for meters
		if($distance == 0){
			return 0;
		} elseif($distance < 10){ //if within 10km
			return 1;
		} elseif($distance >= 10){
			$location_points += round(log($distance));
		}
		return $location_points;
	}
	public function haversine_great_circle_distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371) {
		// convert from degrees to radians
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;
		if($latDelta == 0 && $lonDelta == 0){
			return 0; // then they are at the same address, siblings
		}
		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
		return $angle * $earthRadius;
	}
	# Check and make new groups if necessary 
	public function check_groups($final_group_arr){
		$tmp = $final_group_arr;
		$ok = $this->check_all($tmp);
		$num_checks = 3; // make this an attribute and every check make the attr++ 
		if($ok){
			return $tmp;
		} elseif(!$ok){
			for($i=0; $i<$this->factorial($num_checks); $i++){
				if($this->check_all($tmp)){
					break;
				}
			}
		}
		// check the checks! - see if one check makes another group imbalanced
		//check gender balance
		//check age (make sure it's not fifteen 15yr olds and one 4yr old)
		// ABC, ACB, BAC, BCA, CBA, CAB
		//while there is an imbalance, check every possible combination of checks, then break
		//while(check_all($tmp) == true){} 
		return $tmp;
	}
	public function factorial($n){
		if($n < 2){
			return 1;
		} else {
			return $n * factorial($n-1);
		}
	}
	public function check_gender($final_group_arr){ // returns true if ok
		//define what imbalance is worth fixing and if it's possible
		$tmp = $final_group_arr;
		$fixable = false; // if its possible to fix (if an imbalance exists but its not possible to fix )
		$worth_fixing = false; // check if there is an imbalance
		//(ex: worth_fixing == false if there is a good balance of male/females)
		//get num of each gender
		$total_num_males = 0;
		$total_num_females = 0;
		$gender_group_nums = array();
		// $num_other = 0; ?? maybe later?
		foreach($tmp as $grp){
			$group_num_males = 0;
			$group_num_females = 0;
			foreach($grp as $kid_id){
				$gender = $this->get_kid_by_id($kid_id)->get_gender();
				if($gender == 'male'){
					$group_num_males++;
					$total_num_males++;
				} elseif($gender == 'female'){
					$group_num_females++;
					$total_num_females++;
				}
			}
			array_push($gender_group_nums, array('males' => $group_num_males, 'females' => $group_num_females));
		}
		foreach($gender_group_nums as $genders){
			$males = intval($genders['males']);
			$females = intval($genders['females']);
			$ratio = (double) $males / $females;
			// if males:females --> 3:1 or 1:3 
			if($ratio > 3 || $ratio < (1/3)){ // worth fixing
				$worth_fixing == true;
				//$total_num_males
				//$total_num_females
				//if()
			}
		}
		
		if($fixable && $worth_fixing){
			return false;
		} else {
			return true; // for now
		}
	}
	public function check_age($final_group_arr){ // returns true if ok
		//define what imbalance is worth fixing and if it's possible
		$tmp = $final_group_arr;
		//check other checks
		return true; // for now
	}
	public function check_skill_level($final_group_arr){ // returns true if ok
		//define what imbalance is worth fixing and if it's possible
		$tmp = $final_group_arr;
		//check other checks
		return true; // for now
	}
	public function make_best_gender_groups($final_group_arr){ // returns new groups
		$tmp = $final_group_arr;
		return $tmp;
	}
	public function make_best_age_groups($final_group_arr){ // returns new groups
		$tmp = $final_group_arr;
		return $tmp;
	}
	public function make_best_skill_level_groups($final_group_arr){ // returns new groups
		$tmp = $final_group_arr;
		return $tmp;
	}
	public function check_all($final_group_arr){ // returns true if ok	
		$tmp = $final_group_arr;
		if($this->check_gender($tmp) && $this->check_age($tmp) && $this->check_skill_level($tmp) ){
			return true;
		} else {
			return false;
		}
	}
	public function get_kid_by_id($kid_id){
		foreach($this->kids as $kid_obj){
			if($kid_obj->get_id() == $kid_id){
				return $kid_obj;
			}
		}
	}
}
?>