<?php

function buddy_block($buddies_status, $handle, $name, $prefix, $block_handle) {
	
	global $core_db, $cfg, $lang, $t;
	
	$t->set_block($block_handle, $handle, $name);
	
	if ($buddies_status) {
		
		foreach($buddies_status as $id) {
			
			$row['buddy_id'] = $id;
			$row['buddy'] = substr_adv($core_db->lookup("SELECT name FROM users WHERE id = '$id'", "get buddy's name"), 12);
			$row['clan_id']	= $core_db->lookup("SELECT clan_id FROM users WHERE id = '$id'", "get buddy's clan_id");
			
			if ($row['clan_id'] != $cfg['clan_id']) {
				$clan_prefix = $core_db->lookup("SELECT name FROM clans WHERE id = '{$row['clan_id']}'");
				$row['external_profile'] = "http://$clan_prefix.clan-hq.com/";
				$row['new_window'] = "target=_blank";
			} else {
				$row['external_profile'] = " ";
				$row['new_window'] = " ";
			}
			
			$t->set_array($row, $prefix);
			$t->parse($name, $handle, true);
		}
	} else {
		$t->set_var($name . '_subst', $lang['nobody']);
		$t->set_var($name);
	}
}

$panel_uc = "BuddyList";
	$panel_lc = "buddy_list";
	$prefix = "bl_";
	$handle_name =  $panel_uc."Template";
	$panel_template = "panel_".$panel_lc.".ihtml";
	$block_handle	= $panel_uc."RowBlock";
	$block_name	= $panel_uc[0]."RBlock";
	
	if (($cfg['user_id'] != $cfg['pub_id']) && ($cfg['show_'.$panel_lc] & 1)) {
		
		$result = $core_db->query(
			"SELECT buddy_list.id, buddy_list.buddy_id,
			users.name as buddy, users.clan_id as clan_id, users.avatar_id,
			DATE_FORMAT(users.lastaction, '{$cfg['sql_date']['full']}') as lastaction,
			unix_timestamp(users.lastaction) as unix_lastaction, users.logged_in,
			clans.display as clan_name, clans.tag as clan_tag
			FROM buddy_list
			LEFT JOIN users ON users.id = buddy_list.buddy_id
			LEFT JOIN clans ON clans.id = clan_id
			WHERE buddy_list.user_id = {$cfg['user_id']}
			AND buddy_list.buddy_id != {$cfg['pub_id']}
			ORDER BY users.name ASC",
			"Get buddy list."
		);
		
		$t->set_file($handle_name, $panel_template);
		
		if ($core_db->get_num_rows($result)) {
			
			$t->set_block($handle_name, 'BuddyOverlibBlock', 'BOBlock');
			
			while($row = $core_db->fetch_row($result)) {
				
				$row['lastaction'] = ($row['lastaction'] == "00:00 00/00/00" ? $lang['never'] : $row['lastaction']);
				$row['avatar'] =	check_sql_file($row['avatar_id']);
				
				$t->set_array($row, 'blo_');
				$t->parse('BOBlock', 'BuddyOverlibBlock', TRUE);
				
				if ($row['unix_lastaction'] > (time() - $cfg['online_since']) && ($row['logged_in'] == 'Y')) {
					$buddies_online[] = $row['buddy_id'];
				} else {
					$buddies_offline[] = $row['buddy_id'];
				}
			}
		}
		
		$t->set_block($handle_name, $block_handle, $block_name);
		
		if (!isset($buddies_online)) { $buddies_online = array(); }
		if (!isset($buddies_offline)) { $buddies_offline = array(); }
		
		// Set the two on and offline blocks based on above info!
		buddy_block($buddies_online, 'BuddyOnline', 'BOnline', $prefix, $block_handle);
		buddy_block($buddies_offline, 'BuddyOffline', 'BOffline', $prefix, $block_handle);
		
		$t->parse($block_name, $block_handle, true);
		
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