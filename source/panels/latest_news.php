<?php

$panel_uc = "Headlines";
	$panel_lc = "headlines";
	$prefix = "hl_";
	$handle_name =  $panel_uc."Template";
	$panel_template = "panel_".$panel_lc.".ihtml";
	$block_hanle	= $panel_uc."RowBlock";
	$block_name	= $panel_uc[0]."RBlock";
	
	if ($cfg['show_'.$panel_lc] & 1) {
		
		$t->set_file($handle_name, $panel_template);
		$t->set_block($handle_name, $block_hanle, $block_name);
		
		$result = $core_db->query(
			"SELECT id, title, body, create_id, edit_id,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['alt']}') as date,
			DATE_FORMAT(edit_date,'{$cfg['sql_date']['alt']}') as edit_date
			FROM news
			WHERE userlevel <= $cfg[userlevel]
			AND clan_id = {$cfg['clan_id']}
			ORDER BY create_date DESC LIMIT $cfg[headline_limit]",
			"headlines"
		);
		
		if ($core_db->get_num_rows($result)) {
			while ($row = $core_db->fetch_row($result)) {
				$row['author'] = $core_db->lookup("SELECT name FROM users WHERE id = $row[create_id]", 'headlines: get name of author');
				if (!$row['title']) $row['title'] = $row['body'];
				$row['alt'] = substr_adv(strip_tags($row['title']), 100, false);
				$row['title'] = substr_adv(strip_tags($row['title']), 15, false);
				$t->set_array($row, $prefix);
				$t->parse($block_name, $block_hanle, true);
			}
		} else {
			$t->set_var($block_name, $lang['nonews']);
		}
		$t->parse($block_name, $handle_name);
		
		$t->kill_block($panel_lc.'_maximize_button', FALSE, $parent);
		$t->kill_block($panel_lc.'_maximize_block', FALSE, $parent);
		$return_code = 2;
		
	} elseif (($cfg['show_'.$panel_lc] & 2) && ($cfg['user_id'] != $cfg['pub_id'])) {
		
		$t->kill_block($panel_lc.'_minimize_button', FALSE, $parent);
		$t->kill_block($panel_lc.'_minimize_block', FALSE, $parent);
		
	} else {
		
		$t->kill_block($panel_lc, FALSE, $parent);
	}
	if ($cfg['user_id'] == $cfg['pub_id']) $t->kill_block($panel_lc.'_modes', FALSE, $parent);

?>