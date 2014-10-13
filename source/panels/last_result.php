<?php

$panel_uc = "LastResult";
	$panel_lc = "last_result";
	$prefix = "lr_";
	$handle_name =  $panel_uc."Template";
	$panel_template = "panel_".$panel_lc.".ihtml";
	$block_hanle	= $panel_uc."RowBlock";
	$block_name	= $panel_uc[0]."RBlock";
	
	global $t, $color, $cfg, $lang, $core_db;
	
	if ($cfg['show_'.$panel_lc] & 1) {
		
		$t->set_file($handle_name, $panel_template);
		$t->set_block($handle_name, $block_hanle, $block_name);
		
		$result = $core_db->query(
			"SELECT id, vs_id, vs_name, vs_tag, match_type,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['short']}') AS date
			FROM fixtures
			WHERE clan_id = {$cfg['clan_id']}
			AND (unix_timestamp(match_date) < unix_timestamp(now()))
			and is_enabled = 'Y'
			ORDER BY match_date DESC LIMIT 1",
			"Get latest fixture."
		);
		
		$row = $core_db->fetch_row($result);
		
		if ($core_db->get_num_rows($result)) {
			
			// Make a new results stats object.
			$rs = new ResultsStats;
			$rs->match_outcome($row['id']);
			
			$row['tag'] = $core_db->lookup(
				"SELECT tag FROM clans
				WHERE id = '{$row['vs_id']}'",
				"Get tag for last_result function."
			);
			
			$row['type'] = $core_db->lookup(
				"SELECT name FROM match_types
				WHERE (clan_id = '0' OR clan_id = '{$cfg['clan_id']}')
				AND id = '{$row['match_type']}'",
				"Get the name of the match type"
			);
			
			$row['tag'] = ( !$row['vs_id'] ? $row['vs_tag'] :  $row['tag']);
			$row['outcome'] = $lang[$rs->outcome];
			
			$t->set_array($row, $prefix);
			$t->parse($block_name, $block_hanle, true);
			
		} else {
			$t->set_var($block_name, $lang['no_last_result']);
		}
		$t->parse('Output', $handle_name);
		
		$t->kill_block($panel_lc.'_maximize_button', FALSE, $parent);
		$t->kill_block($panel_lc.'_maximize_block', FALSE, $parent);
		$return_code = 2;
		
	} elseif (($cfg['show_last_result'] & 2) && ($cfg['user_id'] != $cfg['pub_id'])) {
		
		$t->kill_block($panel_lc.'_minimize_button', FALSE, $parent);
		$t->kill_block($panel_lc.'_minimize_block', FALSE, $parent);
		$return_code = 1;
		
	} else {
		$t->kill_block($panel_lc, FALSE, $parent);
		$return_code = 0;
	}
	if ($cfg['user_id'] == $cfg['pub_id']) $t->kill_block($panel_lc.'_modes', FALSE, $parent);
	
	?>