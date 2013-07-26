# == Schema Information
#
# Table name: elemental_sessions
#
#  id            :integer          not null, primary key
#  scheduled_for :datetime         default(2013-06-13 19:27:35 UTC)
#  capacity      :integer          default(60)
#  duration      :integer          default(75)
#  created_at    :datetime         not null
#  updated_at    :datetime         not null
#  membership_id :integer
#

class ElementalSession < ActiveRecord::Base
  attr_accessible :capacity, :duration, :scheduled_for, :membership_id
  default_scope order: 'elemental_sessions.scheduled_for ASC'
  belongs_to :membership
  has_many :elemental_session_bookings
  has_many :kids, :through => :elemental_session_bookings
  has_many :activity_updates

  validates_presence_of :membership_id

  def available_slots
  	self.capacity - self.elemental_session_bookings.count
  end

  def short_identifier
    self.membership.name + " "+ self.scheduled_for.strftime("%m/%d/%Y")
  end

  # THE SECRET SAUCE LOU STYLE________________________________________________

  def sort_kids(num_groups)
    kids = self.elemental_session_bookings.map(&:kid_id)
    total_kids = kids.count
    group_quorum = 10
    num_groups = 4
    categories = ["age"]
    match_points_arr = []
	
	if group_quorum < total_kids
		(0...kids.count).each do |i|
			(0...i).each do |j|
				 match_points_arr.push({:kids[i]=> kids[j], :match_points => get_match_points(categories, kids[i], kids[j]) })
			end
		end
		return get_best_groups(num_groups, match_points_arr.sort_by { |hash| hash[:match_points].reverse! })
	end
  end

  def get_match_points(categories, kid_a, kid_b)
    match_points = 0
    for category in categories
      match_points += compare(category, kid_a, kid_b) || 0
    end
    return match_points
  end

  def get_best_groups(num_groups, match_points_arr)
    groups = []
    num_kids = 0

    0.upto(num_groups-1).each do |i|
      groups[i] = []
    end

    match_points_arr.each do |pair|
        kid_a = pair[:kid_a]
        kid_b = pair[:kid_b]
		if !in_arrays?(kid_a, kid_b, groups)
			# first iteration add the best kid pairs
			group_index = (num_kids / 2) % num_groups
			if num_kids/2 < num_groups
				groups[group_index] << kid_a
				groups[group_index] << kid_b
				num_kids += 2
			end
		end
    end
	
	for i in 0..(num_kids/num_groups - 1).floor
		j = 0
		groups.each { |group| 
			grp_kid_ids = []
			group.each { |kid|
				grp_kid_ids << kid
			}
			next_best_kid = find_best_match(grp_kid_ids, groups);
			if !grp_kid_ids.empty? & !next_best_kid.empty?
				array_push(groups[j % num_groups], next_best_kid);
				num_kids+=1;
				j+=1;
			end 
		}
	end
    return groups
  end
  
	def find_best_match(group, groups)
		tmp = group
		best = []
		possibles = []
		tmp.each { |kid_in_grp| 
			kid_matches = []
			match_points_arr.each { |kid| 
				kid_a = kid[:kid_a]
				kid_b = kid[:kid_b]
				if kid_a == kid
					kid_matches << kid
				end
				elsif
					kid_matches << kid
				end
			}
			best << kid_matches
		}
		best.each do |possibles|
			possibles.each do |pair|
				pair.each do |key, val|
					next if key == 'match_points'
					kid_a = key
					kid_b = val
					top_kid = [];
					if !in_arrays?(kid_a, kid_b, groups) & !pair.empty?
						top_kid << pair
						break;
					end
				end
			end
			if !top_kid.empty?
				possibles << top_kid
			end
		end
		if !possibles.empty?
			#wasn't sure about this part
			possibles.sort_by { |hash| hash[:match_points].reverse! }
			pair = possibles.first
			pair.each do |key, val|
				next if key == 'match_points'
				kid_1 = key
				kid_2 = val
				if !tmp.include? kid_1
					return kid_1;
				elsif !tmp.include? kid_2
					return kid_2;
				end
			end
		end
	end
	
  def in_arrays?(kid_a, kid_b, groups)
    t = false
    for group in groups
      if (group & [kid_a, kid_b]).present?
        t = true
      end
    end
    return t
  end
  
  def haversine_great_circle_distance(latitudeFrom, longitudeFrom, latitudeTo, longitudeTo) 
	earthRadius = 6371
	# convert from degrees to radians
	latFrom = (latitudeFrom * PI) / 180
	lonFrom = (longitudeFrom * PI) / 180
	latTo = (latitudeTo * PI) / 180
	lonTo = (longitudeTo * PI) / 180
	latDelta = latTo - latFrom
	lonDelta = lonTo - lonFrom
	if latDelta == 0 & lonDelta == 0
		return 0 # then they are at the same address, siblings
	end
	angle = 2 * Math.asin(Math.sqrt(Math.sin(latDelta / 2)**2 +
	Math.cos(latFrom) * Math.cos(latTo) *(Math.sin(lonDelta / 2)**2)))
	return angle * earthRadius
  end
  
  def compare(category, kid_a, kid_b)
    case category
    when "skill_level"
      return (kid_a.level - kid_b.level).abs
    when "age"
      return (kid_a.age - kid_b.age).abs
    when "interests"
      return (kid_a.interest_list & kid_b.interest_list).count
    when "traits"
      return (kid_a.trait_list & kid_b.trait_list).count
    when "gender"
      return 5 if kid_a.gender != kid_b.gender
    when "tenure"
      # IMPLEMENT THIS
    when "location"
      distance = haversine_great_circle_distance(kid_a.geo[0], kid_a.geo[1], kid_b.geo[0], kid_b.geo[1])
      case distance
      when distance == 0
        return 0
      when distance < 10
        return 1
      when distance >= 10
        return Math.round(Math.log(distance))
      end
    end
  end
end