<?php

$panel_uc = "SiteStats";
	$panel_lc = "site_stats";
	$prefix = "ss_";
	$handle_name =  $panel_uc."Template";
	$panel_template = "panel_".$panel_lc.".ihtml";
	$block_hanle	= $panel_uc."RowBlock";
	$block_name	= $panel_uc[0]."RBlock";
	
	if ($cfg['show_'.$panel_lc] & 1) {
		
		$t->set_file($handle_name, $panel_template);
		$t->set_block($handle_name, $block_hanle, $block_name);
		
		$userlevel_desc = $cfg["userlevels"][$cfg["userlevel"]];
		$login_info = "{$lang['loggedinas']} <b>{$cfg['username']}</b>. {$lang['lastlogin']} {$cfg['lastlogin']}.<br>{$lang['youruserlevelis']} <b>$userlevel_desc</b>.";
		$row['login_info'] = (($cfg['userlevel'] == 1) ? $lang['notloggedin'] : $login_info);
		
		$users_online = $core_db->lookup(
			"SELECT COUNT(*)
			FROM users
			WHERE clan_id = {$cfg['clan_id']}
			AND unix_timestamp(lastaction) > " . (time()-$cfg['online_since']) . "
			AND logged_in = 'Y' and id != {$cfg['user_id']}
			AND id != $cfg[pub_id]
			ORDER BY lastaction DESC LIMIT 5",
			"select stats"
		);
		
		$row['active_users'] = ($users_online ? $users_online : $lang['None']);
		$row['total_users'] = $core_db->lookup("SELECT COUNT(*) FROM users WHERE clan_id = {$cfg['clan_id']} AND id != $cfg[pub_id]", 'site_stats: get # of members');
		
		$t->set_array($row, $prefix);
		$t->parse($block_name, $block_hanle);
		
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