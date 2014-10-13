<?php

$panel_uc = "NextFixture";
	$panel_lc = "next_fixture";
	$prefix = "nf_";
	$handle_name =  $panel_uc."Template";
	$panel_template = "panel_".$panel_lc.".ihtml";
	$block_hanle	= $panel_uc."RowBlock";
	$block_name	= $panel_uc[0]."RBlock";
	
	if ($cfg['show_'.$panel_lc] & 1) {
		
		$t->set_file($handle_name, $panel_template);
		$t->set_block($handle_name, $block_hanle, $block_name);
		
		$result = $core_db->query(
			"SELECT id, vs_id, vs_name, vs_tag, match_type,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['short']}') as date,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['time']}') as time,
			(unix_timestamp(match_date) - unix_timestamp(now())) as timeleft
			FROM fixtures
			WHERE clan_id = {$cfg['clan_id']}
			AND (unix_timestamp(match_date) > unix_timestamp(now()))
			ORDER BY match_date ASC LIMIT 1",
			"get latest fixture"
		);
		
		$row = $core_db->fetch_row($result);
		
		if ($core_db->get_num_rows($result)) {
			
			$row['tag'] = $core_db->lookup(
				"SELECT tag FROM clans
				WHERE id = '{$row['vs_id']}'",
				"Get the opposing clan's tag."
			);
			
			$row['tag'] = (!$row['vs_id'] ? $row['vs_tag'] : $row['tag']);
			
			$row['type'] = $core_db->lookup(
				"SELECT name FROM match_types
				WHERE (clan_id = 0 OR clan_id = '{$cfg['clan_id']}')
				AND id = '{$row['match_type']}'",
				"Get the name of the match type."
			);
			
			$time_left = round($row['timeleft'] / 86400);
			$time_type = ($time_left == 1) ? 'day' : 'days';
			if ($row['timeleft'] < 86400) {
				
				$time_left = round($row['timeleft'] / 3600);
				$time_type = ($time_left == 1) ? 'hour' : 'hours';
				if ($row['timeleft'] < 3600) {
					
					$time_left = round($row['timeleft'] / 60);
					$time_type = ($time_left == 1) ? 'min' : 'mins';
					if ($row['timeleft'] < 60) {
						
						$time_left = round($row['timeleft']);
						$time_type = ($time_left == 1) ? 'sec' : 'secs';
					}
				}
			}
			
			$row['time_type'] = $lang[$time_type];
			$row['time_left'] = $time_left;
			
			$t->set_array($row, $prefix);
			$t->parse($block_name, $block_hanle, true);
			
		} else {
			$t->set_var($block_name, $lang['noupcomingmatches']);
		}
		
		$t->parse('Output', $handle_name);
		
		$t->kill_block($panel_lc.'_maximize_button', FALSE, $parent);
		$t->kill_block($panel_lc.'_maximize_block', FALSE, $parent);
		$return_code = 2;
		
	} elseif (($cfg['show_'.$panel_lc] & 2) && ($cfg['user_id'] != $cfg['pub_id'])) {
		
		$t->kill_block($panel_lc.'_minimize_button', FALSE, $parent);
		$t->kill_block($panel_lc.'_minimize_block', FALSE, $parent);
		$return_code = 1;
		
	} else {
		$t->kill_block($panel_lc, FALSE, $parent);
		$return_code = 0;
	}
	if ($cfg['user_id'] == $cfg['pub_id']) $t->kill_block($panel_lc.'_modes', FALSE, $parent);
	
	?>