<?php

class ResultsStats {
	
	/* -----------------------------------------------
	| This class contains a group of functions which
	| decides the statistics from given results and
	| scores in the database.
	+ --------------------------------------------- */
	
	// Debug mode - true/false.
	var $debug				=		"";
	
	// The outcome of this result.
	var $outcome			=		"";
	
	// The total score for this result.
	var $total_score	=		array();
	
	// Outcomes/Scores for maps for each result.
	var $map_outcome	=		array();
	var $map_score		=		array();
	
	// Win/draw/loosing streaks for all results.
	var $streak				=		array();
	
	// Integers which store score records.
	var	$matches_won	=		0;
	var	$matches_drew	=		0;
	var	$matches_lost	=		0;
	var $matches_unk	=		0;
	
	function high_score() {
		
		/* -----------------------------------------------
		| Returns the scores and IDs from our best score!
		+ --------------------------------------------- */
		
		global $cfg, $core_db;
		
		// Start off by making our high score 0.
		$high_score['h_score'] = 0;
		
		// Eeek! The enemy is winning! Look for their smaller scores...
		$high_score['e_score'] = 99999999999999999;
		
		$result = $core_db->query(
			"SELECT id, vs_id
			FROM fixtures
			where is_enabled = 'Y'
			AND clan_id = {$cfg['clan_id']}
			AND (unix_timestamp(match_date) < unix_timestamp(now()))
			ORDER BY match_date ASC",
			"get results"
		);
		
		// Do we actualy have any results?
		if ($core_db->get_num_rows($result)) {
			
			while ($result_row = $core_db->fetch_row($result)) {
				
				// Get the scores for THIS result.
				$score_result = $core_db->query(
					"SELECT h_score, e_score
					FROM scores
					WHERE fix_id = $result_row[id]
					ORDER BY item_id ASC",
					"get list of scores for result"
				);
				
				if ($core_db->get_num_rows($score_result) ) {
					
					// Start counting again.
					unset($h_score_count);
					unset($e_score_count);
					
					while ($scores_row = $core_db->fetch_row($score_result) ) {
						
						// Count the scores...
						$h_score_count = ($h_score_count + $scores_row['h_score']);
						$e_score_count = ($e_score_count + $scores_row['e_score']);
					}
					
					// If the home score for this fixture is higher, push it up.
					if($h_score_count > $high_score['h_score']) {
						$high_score['h_score'] = $h_score_count;
						$high_score['e_score'] = $e_score_count;
						
						// If the enemy's score is smaller, push it down.
						if($e_score_count <= $high_score['e_score']) {
							
							// Now set the IDs.
							$high_score['fix_id'] = $result_row['id'];
							$high_score['vs_id'] = $result_row['vs_id'];
						}
					}
				}
			}
			
			// Return the high score array.
			return $high_score;
		}
		
		// Othwerwise, make sure high_score is set, but empty.
		return false;
	}
	
	function match_scores($id) {
		
		/* -----------------------------------------------
		| Returns the scores in a home:enemy format from
		| the given id of a result from the fixtures table.
		+ --------------------------------------------- */
		
		global $cfg, $core_db;
		
		$result = $core_db->query(
			"SELECT id, h_score, e_score
			FROM scores
			WHERE fix_id = $id
			ORDER BY item_id ASC",
			"get list of scores"
		);
		
		if ($core_db->get_num_rows($result) ) {
			
			while ($row = $core_db->fetch_row($result) ) {
				
				// Store the scores for each map.
				$this->map_score[$row['id']]['h'] = $row['h_score'];
				$this->map_score[$row['id']]['e'] = $row['e_score'];
				
				// Store the total scores for this fixture.
				$this->total_score[$id]['h'] = $this->total_score[$id]['h'] + $row['h_score'];
				$this->total_score[$id]['e'] = $this->total_score[$id]['e'] + $row['e_score'];
			}
			
			// Return the total map score.
			return ($this->total_score[$id]['h'] . ":" . $this->total_score[$id]['e']);
		}
		
		// Othwerwise, make sure scores is set, but empty.
		return (FALSE);
	}
	
	function store_streaks() {
		
		/* -----------------------------------------------
		| Used per result, decides wether to store or reset
		| this streak and if not, append this streak.
		+ --------------------------------------------- */
		
		$type = $this->outcome;
		$streak = $this->streak;
		
		// Increment the temp streak for this type of outcome.
		$streak[$type]['temp']++;
		
		// What possible types of outcome can we have?
		$types = array('win', 'draw', 'lose');
		
		// Now, store the result for each type of outcome!
		foreach($types as $thistype) {
			if($streak[$thistype]['temp'] > $streak[$thistype]['record']) {
				$streak[$thistype]['record'] = $streak[$thistype]['temp'];
			}
		}
		
		// Reset the other outcome's temp scores!
		foreach($types as $thistype) {
			if ($type != $thistype) $streak[$thistype]['temp'] = 0;
		}
		
		return $streak;
	}
	
	function match_outcome($id) {
		
		/* -----------------------------------------------
		| Figures out the outcome (win, lose, draw) based
		| on the game type and the scores for each map.
		| The $id is the ID of the current result.
		+ --------------------------------------------- */
		
		global $cfg, $lang, $core_db;
		
		$scores = $core_db->query(
			"SELECT id, h_score, e_score
			FROM scores
			WHERE fix_id = $id
			ORDER BY item_id asc",
			"get list of scores"
		);
		
		// First build up the totals...
		if ($core_db->get_num_rows()) {
			
			while ($row = $core_db->fetch_row($scores) ) {
				
				$h_score = $row['h_score'];
				$e_score = $row['e_score'];
				
				$i++;
				if ($h_score == $e_score) {
					$this->map_outcome[$id][$i] = 'draw';
				} elseif ($h_score > $e_score) {
					$this->map_outcome[$id][$i] = 'win';
				} elseif ($h_score < $e_score) {
					$this->map_outcome[$id][$i] = 'lose';
				}
				
				$h_total = $h_total + $h_score;
				$e_total = $e_total + $e_score;
			}
		}
		
		// We can ONLY have TWO maps to work out 'individual' scoring...
		$score_count = $core_db->lookup("SELECT COUNT(*) FROM scores WHERE fix_id = $id");
		if ($cfg['scoring'] == "individual") $cfg['scoring'] = (($score_count != 2) ? "total" : "individual");
		
		// Now figure out what the over-all results should be...
		switch($cfg['scoring']) {
			
			// Judges match outcomes depending on overall score frequency.
			case "total":
				
				// Must have at least one map to be valid
				if ($score_count) {
					
					if ($h_total > $e_total){
						$this->matches_won++;
						$this->outcome = 'win';
						$this->streak = $this->store_streaks();
					}
					
					if ($h_total == $e_total){
						$this->matches_drew++;
						$this->outcome = 'draw';
						$this->streak = $this->store_streaks();
					}
					
					if ($h_total < $e_total){
						$this->matches_lost++;
						$this->outcome = 'lose';
						$this->streak = $this->store_streaks();
					}
					
				} else {
					
					// Otherwise just get rid of the object!
					unset($this->outcome);
					
					// And increment unknowns!
					$this->matches_unk++;
				}
				
			break;
			
			// Judges match outcomes depending on individual map outcomes...
			// Only works for TWO maps; best used with TFC!
			case "individual":
				
				// Must have exactly 2 maps to be valid!
				if ($score_count == 2) {
					
					$o1 = $this->map_outcome[$id][1]; // Outcome for first map
					$o2 = $this->map_outcome[$id][2]; // Outcome for second map
					
					if (
						($o1 == 'win') && ($o2 == 'win')
						|| ($o1 == 'win') && ($o2 == 'draw')
						|| ($o1 == 'draw') && ($o2 == 'win')
					) {
						$this->matches_won++;
						$this->outcome = 'win';
						$this->streak = $this->store_streaks();
					}
					
					if (
						($o1 == 'lose') && ($o2 == 'win')
						|| ($o1 == 'win') && ($o2 == 'lose')
						|| ($o1 == 'draw') && ($o2 == 'draw')
					) {
						$this->matches_drew++;
						$this->outcome = 'draw';
						$this->streak = $this->store_streaks();
					}
					
					if (
						($o1 == 'lose') && ($o2 == 'lose')
						|| ($o1 == 'draw') && ($o2 == 'lose')
						|| ($o1 == 'lose') && ($o2 == 'draw')
					) {
						$this->matches_lost++;
						$this->outcome = 'lose';
						$this->streak = $this->store_streaks();
					}
					
				} else {
					
					// Otherwise just get rid of the object!
					unset($this->outcome);
					
					// And increment unknowns!
					$this->matches_unk++;
				}
				
			break;
		}
		
		return(true);
	}
}