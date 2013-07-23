<?php
/* TO DO: */
/*Make static comparisons (comparisons that won't change ex: location)
either have less weight or have an element of randomness to promote new groups 
Check when dividing that the value is defined (make sure to check if it equals 0)
*/
$server = "localhost";
$db_username = "lou_schlessinger";
$db_password = "admin000";
$connection = mysql_connect($server, $db_username, $db_password) or die('connection error: '.mysql_error());
$db = mysql_select_db("ethree_test", $connection) or die('database selection error: '.mysql_error());
$kids_query = mysql_query('SELECT * FROM kids') or die('couldn\'t get kids '.mysql_error());
ini_set('max_execution_time', 300); // set max_execution_time to 5 minutes (300 seconds)
// time the script
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime;

$kids  = [];
$num_groups = $_POST['num_groups'];
if (isset($_POST["num_groups"]) && !empty($_POST["num_groups"])) {
    $num_groups = mysql_real_escape_string(htmlentities(trim($num_groups))); 
} else {  
    $num_groups = 3;
}
while ($row = mysql_fetch_array($kids_query)) {
	array_push($kids, new Kid($row['id'], $row['name'], $row['skill_level'],
	$row['age'], $row['interests'], $row['traits'], $row['gender'],
	$row['days_of_membership'], $row['lat'], $row['lon']));
}

$elemental_session  = new ElementalSession($kids, $num_groups);

class Kid {
	private $id;
	private $name;
	private $skill_level;
	private $age;
	private $interests;
	private $traits;
	private $gender;
	private $days_of_membership;
	private $lat;
	private $lon;
	
	public function __construct($id, $name, $skill_level, $age, $interests, $traits, $gender, $days_of_membership, $lat, $lon){
		$this->id = $id;
		$this->name = $name;
		$this->skill_level = $skill_level;
		$this->age = $age;
		$this->interests = $interests;
		$this->traits = $traits;
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
	public function get_interests(){
		return $this->interests;
	}
	public function set_interests($interests){
		$this->interests = $interests;
	}
	public function get_traits(){
		return $this->traits;
	}
	public function set_traits($traits){
		$this->traits = $traits;
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
class ElementalSession {
	private $kids; // disorganized array of Kid objects
	private $match_points_arr = []; // array of ($kid_a.id => kid_b.id, [match_points] => num_points)
	private $session = []; // organized array of Kid objects
	private $num_groups; // for now... - maybe put in constructor?
	private $group_quorum = 10; //How many kids are enough to just return one group?
	private $check_perms = []; // array of permutations of the checks
	
	/* initialize the kids and run program */
	public function __construct($kids, $num_groups){
		$this->kids = $kids;
		$this->num_groups = $num_groups;
		$this->run();
	}
	
	/* begin program */
	public function run(){
		$num_kids = count($this->kids);
		if($this->group_quorum < $num_kids && $this->num_groups > 1){
			for($i=0; $i < $num_kids; $i++){
				for($j=0; $j<$i; $j++){
					$push_me = array($this->kids[$i]->get_id() => $this->kids[$j]->get_id() ,'match_points' => $this->get_match_points($this->kids[$i], $this->kids[$j])); // compare Kid_a with kid_b 
					array_push($this->match_points_arr, $push_me);
				}
			}
			//$final_groups = $this->check_groups($this->create_groups()); 
			$this->prettify( $this->create_groups() ); // will be: $this->prettify( $final_groups )
		} elseif($this->group_quorum > $num_kids || $this->num_groups == 1) {
			$this->prettify( $this->create_groups() );
		}
	}
	
	/* pretty print the results */
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
						<th>Interests</th>
						<th>Traits</th>
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
						if($row['skill_level'] == '1'){
							print ("<td style='border-right: 1px solid gray;'>");
							print ($row['skill_level']);
						} elseif($row['skill_level'] == '2'){
							print ("<td style='border-right: 1px solid gray; background: #B0B0B0 ; color: white;'>");
							print ($row['skill_level']);
						} elseif($row['skill_level'] == '3'){
							print ("<td style='border-right: 1px solid gray; background: #484848; color: white;'>");
							print ($row['skill_level']);
						}
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['interests']);
						print ("</td>");
						print ("<td style='border-right: 1px solid gray;'>");
						print ($row['traits']);
						print ("</td>");
						if($row['gender'] == 'male'){
							print ("<td style='border-right: 1px solid gray; background: blue; color: white;'>");
							print ($row['gender']);
						} elseif($row['gender'] == 'female'){
							print ("<td style='border-right: 1px solid gray; background: pink; color: white;'>");
							print ($row['gender']);
						} 
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
				$c++; // :)
			}
	}
	
	/* gets match points for each kid pair */
	public function get_match_points($kid_a, $kid_b) {
		$match_points = 0;
		$match_points += rand(1,10); // to promote new groups
		$skill_level_points = $this->compare_skill_level($kid_a, $kid_b);
		$age_points = $this->compare_age($kid_a, $kid_b);
		$interests_points = $this->compare_interests($kid_a, $kid_b);
		$traits = $this->compare_traits($kid_a, $kid_b);
		$gender_points = $this->compare_gender($kid_a, $kid_b);
		$days_of_membership_points = $this->compare_days_of_membership($kid_a, $kid_b);
		$location_points = $this->compare_location($kid_a, $kid_b);
		$match_points += $skill_level_points + $age_points + $interests_points
		+ $traits + $gender_points + $days_of_membership_points + $location_points;
		return intval($match_points);
	}
	
	/* makes match point array, sorts it (high to low) and returns the best possible groups */
	public function create_groups(){
		$tmp = array(); 
		foreach($this->match_points_arr as &$kid) {
			$tmp[] = &$kid['match_points']; 
		}
		array_multisort($tmp, SORT_DESC, $this->match_points_arr); 
		return $this->get_best_groups();
	}
	
	/* gets the best groups based on match points -- multi-dimensional array of groups with kids */
	public function get_best_groups(){ 
		$return_me = [];
		$num_kids = 0;
		for($i=0; $i<$this->num_groups; $i++){
			array_push($return_me, array());
		}
		foreach($this->match_points_arr as $kid_pair) {
			$kid_a = key($kid_pair);
			$kid_b = $kid_pair[$kid_a];
			if(!$this->in_array_r($kid_a, $return_me) && !$this->in_array_r($kid_b, $return_me)){
				// first iteration add the best kid pairs
				$group_index = $num_kids/2 % $this->num_groups;
				if($num_kids/2 < $this->num_groups){
					array_push($return_me[$group_index], $kid_a);
					array_push($return_me[$group_index], $kid_b);
					$num_kids+=2;
				}
			}
		}
		for($i=0; $i<floor((count($this->kids)/$this->num_groups)-1); $i++){
			$j = 0;
			foreach($return_me as $group){
				$grp_kid_ids = [];
				foreach($group as $kid){
					array_push($grp_kid_ids, $kid);
				}
				$next_best_kid = $this->find_best_match($grp_kid_ids, $return_me);
				if(!empty($grp_kid_ids) && !empty($next_best_kid)){
					array_push($return_me[$j % $this->num_groups], $next_best_kid);
					$num_kids+=1;
					$j++;
				} 
			}
		}
		return $return_me;
	}
	
	/* given a group, get the kid with highest match points with the group */
	public function find_best_match($group, $all_groups){
		$tmp = $group;
		$best = [];
		$possibles = [];
		
		foreach($tmp as $kid_in_grp){
			$kid_matches = [];
			foreach($this->match_points_arr as $kid){
				$kid_a = key($kid);
				$kid_b = $kid[$kid_a];
				if($kid_a == $kid_in_grp){
					array_push($kid_matches, $kid);
				} elseif($kid_b == $kid_in_grp){
					array_push($kid_matches, $kid);
				}
			}
			array_push($best, $kid_matches);
		}
		foreach($best as $kid){
			foreach($kid as $possible_match){
				$kid_a = key($possible_match);
				$kid_b = $possible_match[$kid_a];
				$top_kid = [];
				if((!$this->in_array_r($kid_a, $all_groups) || !$this->in_array_r($kid_b, $all_groups)) && !empty($possible_match)){
					array_push($top_kid, $possible_match);
					break;
				}
			}
			if(!empty($top_kid)){
				array_push($possibles, $top_kid);
			}
		}
		//get last kid in possibles
		if(!empty($possibles)){
			usort($possibles, function (array $a, array $b){ return $a[0]["match_points"] - $b[0]["match_points"]; });
			$end = end($possibles)[0];
			$kid_1 = key($end);
			$kid_2 = $end[$kid_1];
			if(!in_array($kid_1, $tmp)){
				return $kid_1;
			} elseif(!in_array($kid_2, $tmp)){
				return $kid_2;
			}
		}
	}

	/* recursive in_array() */
	public function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}
	
	/* return match points for a kid pair based on their skill levels */
	public function compare_skill_level($kid_a, $kid_b){ // either 1, 2, or 3
		$skill_level_points = 0;
		$skill_level_a = $kid_a->get_skill_level();
		$skill_level_b = $kid_b->get_skill_level();
		$diff = abs($skill_level_a - $skill_level_b); // so if they are same level - no match points
		$skill_level_points += $diff;
		return $skill_level_points;
	}
		
	/* return match points for a kid pair based on their ages */
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
		
	/* return match points for a kid pair based on their interests/passions */
	public function compare_interests($kid_a, $kid_b){
		$interests_points = 0;
		$interests_a = explode(', ', $kid_a->get_interests()); 
		$interests_b = explode(', ', $kid_b->get_interests());
		// look for synonyms
		// for now return a number based on if the numbers match
		$common_interests = count(array_intersect($interests_a, $interests_b));
		$interests_points += $common_interests;
		/* in future */
		// go through each kid and see how common it is for a kid to have certain interests/passions 
		return $interests_points;
	}
	
	/* return match points for a kid pair based on their traits */
	public function compare_traits($kid_a, $kid_b){
		$traits_points = 0;
		$traits_a = explode(', ', $kid_a->get_traits());
		$traits_b = explode(', ', $kid_b->get_traits());
		// for now return a number based on if the numbers match
		$common_traits = count(array_intersect($traits_a, $traits_b));
		$traits_points += $common_traits;
		/* in future */
		// look for synonyms
		// go through each kid and see how common it is for a kid to have certain traits 
		return $traits_points;
	}
	
	/* return match points for a kid pair based on their gender */
	public function compare_gender($kid_a, $kid_b){
		$gender_points = 0;
		$gender_a = $kid_a->get_gender();
		$gender_b = $kid_b->get_gender();
		if($gender_a !== $gender_b){
			$gender_points += 5;
		}
		return $gender_points;
	}
	
	/* return match points for a kid pair based on how long they have been members */
	public function compare_days_of_membership($kid_a, $kid_b){
		$days_of_membership_points = 0;
		$days_of_membership_a = intval($kid_a->get_days_of_membership());
		$days_of_membership_b = intval($kid_b->get_days_of_membership());
		$diff = abs($days_of_membership_a - $days_of_membership_b);
		if($days_of_membership_a < 100 && $days_of_membership_b < 100){ // then they are considered N00B5
			$days_of_membership_points += 3;
		} elseif($diff >= 100){
			$days_of_membership_points += 3;
		}
		return $days_of_membership_points;
	}
	
	/* return match points for a kid pair based on where they live */
	public function compare_location($kid_a, $kid_b){
		$location_points = 0;
		$lat_a = $kid_a->get_lat();
		$lon_a = $kid_a->get_lon();
		$lat_b = $kid_b->get_lat();
		$lon_b = $kid_b->get_lon();
		$distance = $this->haversine_great_circle_distance($lat_a, $lon_a, $lat_b, $lon_b, $earthRadius = 6371);  
		if($distance == 0){
			return 0;
		} elseif($distance < 10){ //if within 10km
			return 1;
		} elseif($distance >= 10){
			$location_points += round(log($distance));
		}
		return $location_points;
	}
	
	/* return distance in metric units,if $earthRadius = 6371, answer will be in kilometres. 6,371,000 for meters */
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
	
	/* check and make new groups if necessary */
	public function check_groups($final_group_arr){
		$tmp = $final_group_arr;
		$ok = $this->check_all($tmp);
		if($ok){
			return $tmp;
		} elseif(!$ok){
			$checks_to_run = array('optimize_gender','optimize_age','optimize_skill_level');//
			$this->pc_permute($checks_to_run);
			$checks = $this->check_perms;
			foreach($checks as $permutation){
				foreach($permutation as $func){
					if($this->check_all($tmp)){
						break;
					} else {
						call_user_func(array($this,$func),$tmp);
					}
				}
			}
			return $tmp;
		}
	}
	
	/*check every permutation of optimizations, set check_perms*/
	public function pc_permute($items, $perms = array()) {
		if (empty($items)) { 
			array_push($this->check_perms, $perms);
		} else {
			for ($i = count($items) - 1; $i >= 0; --$i) {
				 $newitems = $items;
				 $newperms = $perms;
				 list($foo) = array_splice($newitems, $i, 1);
				 array_unshift($newperms, $foo);
				 $this->pc_permute($newitems, $newperms);
			 }
		}
	}
	
	/* returns true if OK (no imbalance) */
	public function check_gender($final_group_arr){ 
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
		$num_groups = count($gender_group_nums);
		foreach($gender_group_nums as $genders){
			$males = intval($genders['males']);
			$females = intval($genders['females']);
			$ratio = (double) $males / $females;
			// if males:females --> 3:1 or 1:3 
			if($ratio > 3 || $ratio < (1/3)){ // worth fixing if > 75% boys or girls
				$worth_fixing == true;
				// see if it's possible to fix
				// if putting another gender wouldn't mess up the 3:1 ratio in another group
				$best_num_males = $total_num_males / $num_groups;
				$best_num_females = $total_num_females / $num_groups;
				$male_deviation = abs(100*(($best_num_males - $males) / ($males))); // percent difference from ideal #
				$female_deviation = abs(100*(($best_num_females - $females) / ($females))); // percent difference from ideal #
				$pct = 75; // if it's 75% different
				if($male_deviation > $pct || $female_deviation > $pct ){
					$fixable == true;
				}
			}
		}
		if($fixable && $worth_fixing){
			return false;
		} else {
			return true; 
		}
	}
	
	/* returns true if OK (age distribution isn't a problem) */
	public function check_age($final_group_arr){ // returns true if ok
		//define what imbalance is worth fixing and if it's possible
		$tmp = $final_group_arr;
		$fixable = false; // if its possible to fix (if an imbalance exists but its not possible to fix )
		$worth_fixing = false;
		/* I'm not sure what boss man wants for age distribution within groups... so not worth making now.*/
		return true; // for now...
	}
	
	/* returns true if OK (no imbalance of skill levels) */
	public function check_skill_level($final_group_arr){ 
		//define what imbalance is worth fixing and if it's possible
		$tmp = $final_group_arr;
		$fixable = false; // if its possible to fix (if an imbalance exists but its not possible to fix )
		$worth_fixing = false;
		//(ex: worth_fixing == false if there is a good balance of male/females)
		//get num of each level
		$total_num_first = 0; // discovery
		$total_num_second = 0; // exploration
		$total_num_third = 0; // launch
		$skill_level_group_nums = array();
		foreach($tmp as $grp){
			$group_num_first = 0;
			$group_num_second = 0;
			$group_num_third = 0;
			foreach($grp as $kid_id){
				$skill_level = $this->get_kid_by_id($kid_id)->get_skill_level();
				if($skill_level == '1'){
					$group_num_first++;
					$total_num_first++;
				} elseif($skill_level == '2'){
					$group_num_second++;
					$total_num_second++;
				} elseif($skill_level == '3'){
					$group_num_third++;
					$total_num_third++;
				}
			}
			array_push($skill_level_group_nums, array('firsts' => $group_num_first, 'seconds' => $group_num_second, 'thirds' =>$group_num_third));
		}
		$num_groups = count($skill_level_group_nums);
		foreach($skill_level_group_nums as $skill_levels){
			$firsts = intval($skill_levels['firsts']);
			$seconds = intval($skill_levels['seconds']);
			$thirds = intval($skill_levels['thirds']);
			$total = $firsts + $seconds + $thirds;
			$pct_first = (double) 100*($firsts / $total); 
			$pct_second = (double) 100*($seconds / $total);
			$pct_third = (double) 100*($thirds / $total);
			// if any skill level has more than 75% of a group
			if($pct_first > 70 || $pct_second > 70 || $pct_third > 70){ // worth fixing if > 75% 1s or 2s or 3s 
				$worth_fixing == true;
				// see if it's possible to fix
				//best num per group
				$best_num_first = $total_num_first / $num_groups; 
				$best_num_second = $total_num_second / $num_groups; 
				$best_num_third = $total_num_third / $num_groups; 
				// make sure not to divide by 0
				$first_deviation = abs(100*(($best_num_first - $firsts) / ($firsts))); // percent difference from ideal #
				$second_deviation = abs(100*(($best_num_second - $seconds) / ($seconds))); // percent difference from ideal #
				$third_deviation = abs(100*(($best_num_third - $thirds) / ($thirds))); // percent difference from ideal #
				$pct = 75; // if it's 75% different from ideal
				if($male_deviation > $pct || $female_deviation > $pct || $third_deviation > $pct){
					$fixable == true;
				}
			}
		}
		if($fixable && $worth_fixing){
			return false;
		} else {
			return true; 
		}
	}
	
	/* returns new groups optimized by gender distribution */
	public function optimize_gender($final_group_arr){
		$tmp = $final_group_arr;
		return $tmp;
	}
	
	/* returns new groups optimized by age distribution */
	public function optimize_age($final_group_arr){ 
		$tmp = $final_group_arr;
		return $tmp;
	}
	
	/* returns new groups optimized by skill level distribution */
	public function optimize_skill_level($final_group_arr){ 
		$tmp = $final_group_arr;
		return $tmp;
	}
	
	/* returns true if all checks are OK */
	public function check_all($final_group_arr){
		$tmp = $final_group_arr;
		if($this->check_gender($tmp) && $this->check_age($tmp) && $this->check_skill_level($tmp) ){
			return true;
		} else {
			return false;
		}
	}
	
	/* returns kid object by id */
	public function get_kid_by_id($kid_id){
		foreach($this->kids as $kid_obj){
			if($kid_obj->get_id() == $kid_id){
				return $kid_obj;
			}
		}
	}
}

$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$endtime = $mtime; 
$totaltime = ($endtime - $starttime); 
echo "Total time: ".round($totaltime, 2)." seconds"; 
?>