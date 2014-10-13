<?php

switch($mm->action) {
	
	/*
	+ ------------------------------------------------
	| Account DEFAULT
	+ ------------------------------------------------
	| Shows bandidth and usage statistics for this
	| clan's account.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "default":
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'ServerWatchRowBlock', 'SWRBlock');
		
		$result = $core_db->query(
			"SELECT id, ip, port, type
			FROM servers
			WHERE clan_id = {$cfg['clan_id']}
			AND id = '$cfg[select_server]'
			ORDER BY priority ASC",
			"get servers"
		);
		
		if ($core_db->get_num_rows() && ($cfg['show_server_watch'] & 1)) {
			$row = $core_db->fetch_row($result);
			
			// Need to refresh this window every minute.
			echo("<meta http-equiv=refresh content=60>\n");
			
			$row['address'] = "$row[ip]:$row[port]";
			$row['qstat_type'] = $core_db->lookup("select type from clan_types where id = $row[type]");
			$server = new phpQStat($row['qstat_type'], $row['address'], true, true);
			$row['type'] = $core_db->lookup("select description from clan_types where id = $row[type]", "Get the name of the game.");
			$row['name'] = $server->server_name;
			
			$row['ping'] = $server->server_responce_time;
			if ($row['ping']) $row['ping'] .= 'ms';
			$row['platform'] = ucfirst($server->rules->value[5]);
			
			if ($server->server_num_players_max) {
				$row['players'] = $server->server_num_players . "/" . $server->server_num_players_max;
			} else {
				$row['players'] = "";
			}
			
			$row['map'] = $server->server_map;
			
			// Get a valid file location!
			$row['map_image'] = seek_file($row['map'], "./images/maps/", array("jpg", "gif"), "", "\nGame: {$row['type']}");
			
			$buddy_result = $core_db->query(
				"SELECT id, buddy_id
				FROM buddy_list
				WHERE user_id = {$cfg['user_id']}",
				"get buddy list"
			);
			
			if ($core_db->get_num_rows($buddy_result)) {
				while ($buddy_row = $core_db->fetch_row($buddy_result)) {
					// Get the name of each buddy!
					$buddy_list[] = $core_db->lookup("select name from users where id = $buddy_row[buddy_id]");
				}
				for ($i = 0 ; $i < $server->players->count ; $i++) {
					$player = $server->players->field0[$i];
					
					if ($buddy_list) {
						foreach($buddy_list as $buddy) {
							
							// Hello, are you my friend? :)
							if (stristr($player, $buddy) && $buddy) {
								if (@!in_array($player, $buddys_on)) {
									$buddys_on[] = $player;
									$row['buddies_on']++;
								}
							}
						}
					}
				}
			}
			if ($row['buddies_on']) {
				$row['buddies_on'] = "<b class=colourGood>$row[buddies_on]</b>";
			} else {
				$row['buddies_on'] = $lang['None'];
			}
			
			if ($cfg['user_id'] == $cfg['pub_id']) $row['buddies_on'] = "";
			
			$row['details'] = "<a href=?mod=servers&action=details&id=$row[id]>$lang[details]</a>";
			$row['map'] = substr_adv($row['map'], 12);
			
			foreach(array('name', 'address', 'map', 'buddies_on', 'players', 'ping', 'details') as $var) {
				if ($row[$var]) $row['vars'] .= "  parent.sw_$var.innerHTML = \"{$row[$var]}\";\n";
			}
			
			$row['vars'] .= "  parent.sw_map_img.style.backgroundImage = \"url({$row['map_image']})\";\n";
			
			$spacer = "<br><img src='' height=5 width=0>";
			$retry = "$spacer<br>[ <b><a href='?mod=server_watch' target='server_watch'>{$lang['retry']}</a></b> ]";
			$row['timeout'] = $lang['server_timed_out'] . $retry;
			$row['server_down'] = $lang['server_down'] . $retry;
			$edit = "$spacer<br>[ <b><a href='?mod=servers&action=edit&id={$row['id']}'>{$lang['edit']}</a></b> ]";
			$row['no_server'] = $lang['no_server_selected'] . ($t->check_privs(6, 'edit') ? $edit : "");
			
			$t->set_array($row);
			$t->parse('SWRBlock', 'ServerWatchRowBlock');
		} else {
			// Don't show any JavaScript + don't update!
			$t->set_var('SWRBlock');
		}
		
		$t->parse('Output', 'subTemplate');
		$t->p('Output');
		
	break;
}