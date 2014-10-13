<?php

if ($cfg['show_match_stats'] & 1) {
		
		$t->set_file('msTemplate', "match_stats.ihtml");
		$t->set_block('msTemplate', 'MatchStatsBlock', 'MSBlock');
		
		$result = $core_db->query(
			"SELECT id, vs_id
			FROM fixtures
			where is_enabled = 'Y'
			AND clan_id = {$cfg['clan_id']}
			AND (unix_timestamp(match_date) < unix_timestamp(now()))
			ORDER BY match_date ASC",
			"get results"
		);
		
		if (mysql_num_rows($result) ) {
			
			// Make a new results stats object.
			$rs = new ResultsStats;
			
			// Firstly, work out what the outcomes & scores are for this result.
			while ($row = $core_db->fetch_row($result)) {
				$rs->match_outcome($row['id']);
			}
			
			// Now, get an array of info to work out the highest score.
			$high_score = $rs->high_score();
			
			if ($high_score) {
				
				// Attempt to get the tag presuming the clan is a 'custom' one.
				$high_score_clan = $core_db->lookup(
					"SELECT vs_tag FROM fixtures
					WHERE id = '{$high_score['fix_id']}'",
					"Get the tag of the 'high_score_clan' from fixtures table."
				);
				
				if (!$high_score_clan) {
					/*
						If no ID was returned from the last query,
						then the clan's info must be elsewhere in
						the cms_core database...
					*/
					$high_score_clan = $core_db->lookup(
						"SELECT tag FROM clans
						WHERE id = '{$high_score['vs_id']}'",
						"Get the tag of the 'high_score_clan' from clans table."
					);
				}
				
				// Build up the info to be displayed in the stats.
				$row['high_score_info'] = "{$high_score['h_score']}:{$high_score['e_score']} vs $high_score_clan";
				
				// This is for the details link.
				$row['high_score_id'] = $high_score['fix_id'];
			}
			
			// Make the streaks integers incase they haven't been set.
			$row['winning_streaks'] = $rs->streak['win']['record'] + 0;
			$row['drawing_streaks'] = $rs->streak['draw']['record'] + 0;
			$row['losing_streaks'] = $rs->streak['lose']['record'] + 0;
			
			// Shift the data from the scores array to the row array for parsing.
			$row['matches_won'] = $rs->matches_won;
			$row['matches_drew'] = $rs->matches_drew;
			$row['matches_lost'] = $rs->matches_lost;
			$row['matches_unk'] = $rs->matches_unk;
			
			// Work out the percentages based on the scores given...
			// There will never be a division by 0 because IF statement checks num rows > 0.
			$row['matches_played'] = mysql_num_rows($result) - $row['matches_unknown'];
			$row['matches_won_percent'] = round($row['matches_won'] / $row['matches_played'] * 100, 2);
			$row['matches_lost_percent'] = round($row['matches_lost'] / $row['matches_played'] * 100, 2);
			$row['matches_drew_percent'] = round($row['matches_drew'] / $row['matches_played'] * 100, 2);
			$row['matches_unk_percent'] = round($row['matches_unk'] / $row['matches_played'] * 100, 2);
			
			$t->set_array($row, 'ms_');
			$t->parse('MSBlock', 'MatchStatsBlock', true);
			
		} else {
			$t->set_var('MSBlock', $lang['no_stats_generated']);
		}
		$t->kill_block('match_stats_maximize_button', FALSE, $parent);
		$t->kill_block('match_stats_maximize_block', FALSE, $parent);
		$return_code = 2;
		
	} elseif (($cfg['show_match_stats'] & 2) && ($cfg['user_id'] != $cfg['pub_id'])) {
		
		$t->kill_block('match_stats_minimize_button', FALSE, $parent);
		$t->kill_block('match_stats_minimize_block', FALSE, $parent);
		$return_code = 1;
		
	} else {
		$t->kill_block('match_stats', FALSE, $parent);
		$return_code = 0;
	}
	if ($cfg['user_id'] == $cfg['pub_id']) $t->kill_block('match_stats_modes', FALSE, $parent);
	
	?>