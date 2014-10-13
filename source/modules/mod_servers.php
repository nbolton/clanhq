<?php

switch($mm->action) {
	
	case "default": redirect("&action=browse"); break;
	
	/*
	+ ------------------------------------------------
	| Servers BROWSE
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "browse":
		
		$result = $core_db->query(
			"SELECT id, ip, port, type, info
			FROM servers WHERE clan_id = {$cfg['clan_id']}
			order by priority asc",
			"Get list of servers."
		);
		
		if ($core_db->get_num_rows()) {
			
			$nav->navs[] = $lang["servers"];
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			
			while ($row = $core_db->fetch_row($result)) {
				
				// Make a valid address for phpQStat
				$row['address'] = "$row[ip]:$row[port]";
				
				// Get the QStat game type from the db
				$row['qstat_type'] = $core_db->lookup("SELECT type FROM clan_types where id = $row[type]", "Get QStat type.");
				$server = new phpQStat($row['qstat_type'], $row['address'], true, true);
				
				$row['name'] = addslashes($server->server_name);
				$row['type'] = $core_db->lookup("select description from clan_types where id = $row[type]", "Get the name of the game.");
				$row['ping'] = $server->server_responce_time;
				if ($row['ping']) $row['ping'] .= 'ms';
				
				if (isset($server->rules->value[5])) {
					$row['platform'] = ucfirst($server->rules->value[5]);
				} else {
					$row['platform'] = "";
				}
				
				// How many players do we have?
				if ($server->server_num_players_max) {
					$row['players'] = $server->server_num_players . "/" . $server->server_num_players_max;
				} else {
					$row['players'] = "";
				}
				
				// Yey! A pretty pic of the OS
				$platform_img = "";
				switch($row['platform']) {
					case "Windows": $platform_img = "icon_windows.gif"; break;
					case "Linux": $platform_img = "icon_linux.gif"; break;
					case 0: $row['platform'] = ""; break;
				}
				if ($platform_img) $row['platform'] .= " &nbsp;<img src=themes/{theme_id}/images/$platform_img>";
				
				$row['map'] = $server->server_map;
				
				// Check the maps folder to see if we have the map!
				$row['map_image'] = seek_file($row['map'], "./images/maps/", array("jpg", "gif"), $row['ping'], "\nGame: {$row['type']}");
				
				$buddy_result = $core_db->query(
					"SELECT id, buddy_id
					FROM buddy_list
					WHERE user_id = {$cfg['user_id']}",
					"Get buddy list."
				);
				
				$buddys_on = "";
				$buddy_list = "";
				$i = 0;
				
				if ($core_db->get_num_rows($buddy_result)) {
					
					while ($buddy_row = $core_db->fetch_row($buddy_result)) {
						// Make all the buddy names.
						$buddy_list[$i++] = $core_db->lookup("select name from users where id = $buddy_row[buddy_id]", "Get name of buddy.");
					}
					
					for ($i = 0 ; $i < $server->players->count ; $i++) {
						$player = $server->players->field0[$i];
						
						if ($buddy_list) {
							foreach($buddy_list as $buddy) {
								
								// Hello, are you my friend? :)
								if (stristr($player, $buddy) && $buddy) {
									if (@!in_array($player, $buddys_on)) $buddys_on[] = $player;
								}
							}
						}
					}
				}
				
				if ($buddys_on) {
					
					$tag = $core_db->lookup("SELECT tag FROM clans where id = {$cfg['clan_id']}", "Get clan's tag.");
					
					foreach ($buddys_on as $k => $v) {
						$buddys_on[$k] = trim(str_replace($tag, "", $v));
					}
					
					$row['buddy_list'] = implode(", ", $buddys_on);
				} else {
					$row['buddy_list'] = $lang['no_buddies_in_server'];
				}
				
				// Well, we cant have buddys with no buddy list, can we? :)
				if ($cfg['user_id'] == $cfg['pub_id']) $row['buddy_list'] = "";
				
				$t->set_array($row);
				
				set_admin_links('admin_links',
					array(
						"$lang[edit]; ?mod=servers&action=edit&id={$row['id']}",
						"$lang[delete]; ?mod=servers&action=delete&id={$row['id']}; true; delete_item_q"
					)
				);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_servers', true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Servers DETAILS
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "details":
		
		$result = $core_db->query(
			"select id, ip, port, type, info
			from servers
			where clan_id = {$cfg['clan_id']}
			and id = $id",
			"Get list of servers."
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'ServerBlock');
			$t->set_msg_var();
			
			$row = $core_db->fetch_row($result);
			
			// Make a valid address for phpQStat
			$row['address'] = "$row[ip]:$row[port]";
			
			// Get the QStat game type from the db
			$row['qstat_type'] = $core_db->lookup("SELECT type FROM clan_types WHERE id = '{$row['type']}'", "Get QStat type.");
			$server = new phpQStat($row['qstat_type'], $row['address'], true, true);
			
			$row['name'] = $server->server_name;
			$row['type'] = $core_db->lookup("SELECT description FROM clan_types WHERE id = '{$row['type']}'", "Get the name of the game.");
			$row['ping'] = $server->server_responce_time;
			if ($row['ping']) $row['ping'] .= 'ms';
			$row['platform'] = ucfirst($server->rules->value[5]);
			$row['info'] = cmscode($row['info'], 'html');
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['servers']}</a>",
				"{$lang['details']} (" . substr_adv($row['name'], 40) . ")"
			);
			
			// How many players do we have?
			if ($server->server_num_players_max) {
				$row['players'] = $server->server_num_players . "/" . $server->server_num_players_max;
			} else {
				$row['players'] = "";
			}
			
			// Yey! A pretty pic of the OS
			switch($row['platform']) {
				default: $row['platform'] = ""; break;
				case "Windows": $platform_img = "icon_windows.gif"; break;
				case "Linux": $platform_img = "icon_linux.gif"; break;
			}
			if ($platform_img) $row['platform'] .= " &nbsp;<img src=themes/{theme_id}/images/$platform_img>";
			
			$row['map'] = $server->server_map;
			
			// Check the maps folder to see if we have the map!
			$row['map_image'] = seek_file($row['map'], "./images/maps/", array("jpg", "gif"), $row['ping'], "\nGame: {$row['type']}");
			
			$buddy_result = $core_db->query(
				"SELECT id, buddy_id
				FROM buddy_list
				WHERE user_id = {$cfg['user_id']}",
				"Get buddy list."
			);
			
			$buddys_on = "";
			$buddy_list = "";
			$row['members_on'] = 0;
			$tag = $core_db->lookup("SELECT tag FROM clans where id = '{$cfg['clan_id']}'", "Get clan's tag.");
			
			while ($buddy_row = $core_db->fetch_row($buddy_result)) {
				// Make all the buddy names.
				$buddy_list[$i++] = $core_db->lookup("SELECT name FROM users WHERE id = '{$buddy_row['buddy_id']}'", "Get name of buddy.");
			}
			
			if ($core_db->get_num_rows($buddy_result)) {
				
				for ($i = 0; $i < $server->players->count; $i++) {
					
					$player = $server->players->field0[$i];
					
					// I can't count... can you do it for me? :)
					if (strstr($player, $tag)) $row['members_on']++;
					
					if ($buddy_list) {
						foreach($buddy_list as $buddy) {
							
							// Hello, are you my friend? :)
							if (stristr($player, $buddy) && $buddy) {
								if (@!in_array($player, $buddys_on)) $buddys_on[] = $player;
							}
						}
					}
				}
			} else {
				// Don't look for buddies, but still look for clan members :)
				for ($i = 0; $i < $server->players->count; $i++) {
					
					$player = $server->players->field0[$i];
					
					// I can't count... can you do it for me? :)
					if (strstr($player, $tag)) $row['members_on']++;
				}
			}
			
			// If there is a buddy list, clean it up!
			if ($buddys_on) {
				
				foreach ($buddys_on as $k => $v) {
					$buddys_on[$k] = trim(str_replace($tag, "", $v));
				}
				$row['buddy_list'] = implode(", ", $buddys_on);
				
			} else {
				$row['buddy_list'] = $lang['no_buddies_in_server'];
			}
			
			// Well, we cant have buddies without buddy list, can we? :)
			if ($cfg['user_id'] == $cfg['pub_id']) $row['buddy_list'] = "";
			
			$t->set_array($row);
			
			set_admin_links('admin_links',
				array(
					"$lang[edit]; ?mod=servers&action=edit&id={$row['id']}",
					"$lang[delete]; ?mod=servers&action=delete&id={$row['id']}; true; delete_item_q"
				)
			);
			
			if ($row['players'] > 0) {
				
				$t->set_block('ServerBlock', 'PlayersBlock', 'PBlock');
				
				for ($i = 0 ; $i < $server->players->count ; $i++) {
					
					$player = htmlspecialchars($server->players->field0[$i]);
					$frags = $server->players->field1[$i];
					
					$highlight = "";
					if ($buddy_list) {
						foreach($buddy_list as $buddy) {
							if (stristr($player, $buddy)) {
								// Hey, I know you!!
								$player = "<b>$player</b>";
								$highlight = "class=backgroundHighlight";
								// Now stop searching and go onto the next.
								break;
							}
						}
					}
					
					$t->set_var('player', $player);
					$t->set_var('highlight', $highlight);
					$t->set_var('frags', $frags);
					$t->parse('PBlock', 'PlayersBlock', true);
				}
				
			} else {
				
				$t->set_block('ServerBlock', 'HidePlayersBlock', 'HPBlock');
				$t->set_var('HPBlock');
			}
			
			$t->parse('ServerBlock', 'ServerBlock');
			
		} else {
			$t->set_msg('no_servers', true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Servers ADMIN
	+ ------------------------------------------------
	| Shows all servers as list with admin features.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		$srt->set_array( array('ip; address', 'priority; order', 'create_id; creator', 'create_date; created', 'info'), 'priority', 'asc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT id, ip, create_id, edit_id, port, info, priority,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') as created,
			DATE_FORMAT(edit_date,'{$cfg['sql_date']['full']}') as edited
			FROM servers
			WHERE clan_id = {$cfg['clan_id']}" . $srt->sql(),
			"get news"
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->page_index("select COUNT(*) from servers where clan_id = {$cfg['clan_id']}");
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['servers']}</a>",
				$lang['admin']
			);
			
			$i = 1;
			
			while ($row = $core_db->fetch_row($result)) {
				
				// So user can tab through the fields easier
				$row['tabindex'] = $i++;
				
				// Hmm, what's your port?
				$row['ip'] .= ":$row[port]";
				
				// Well we don't expect the info to be tiny...
				$row['info'] = substr_adv($row['info'], 20, false);
				
				if ($row['create_id']) { $row['creator'] = $core_db->lookup("select name from users where id = $row[create_id]"); }
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_servers', true, true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Servers CREATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang['servers']}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			$lang['create']
		);
		
		$usg->check_limit('servers');
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$row['server_type'] = sqllistbox('input[type]', "select id, description from clan_types", $cfg['clan_type']);
		$t->set_array($row, "", "", "");
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Servers EDIT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		$mm->ifempty($id, "?mod=$mm->module&action=admin");
		
		$result = $core_db->query(
			"SELECT id, ip, port, info, type
			FROM servers
			WHERE id = '$id'",
			"Edit."
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			$row = $core_db->fetch_row($result);
			$row = cmscode($row, 'cmscode');
			
			$row['ip'] = "{$row['ip']}:{$row['port']}";
			$row['qstat_type'] = $core_db->lookup("select type from clan_types where id = '{$row['type']}'");
			$server = new phpQStat($row['qstat_type'], $row['ip'], true, true);
			$row['name'] = $server->server_name;
			
			$row['server_type'] = sqllistbox('input[type]', "select id, description from clan_types", $row['type']);
			$t->set_array($row, "", "", "");
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
		} else {
			$t->set_msg('no_servers_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Servers INSERT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "insert":
		
		$usg->check_limit('servers');
		
		// Always seportate IP from port.
		$address = explode(":", $input['ip']);
		
		if (!$input['port']) {
			// If the user hasn't specified a port, then use the one post-fixed on the end of the address.
			$ip = $address[0];
			$port = $address[1] + 0;
			
		} else {
			// However if a port was given, make sure a 0 is added.
			$ip = $address[0];
			$port = $input['port'] + 0;
		}
		
		// If there's STILL no port...
		if (!$port) {
			$port = $core_db->lookup("SELECT port FROM clan_types WHERE id = '{$cfg['clan_type']}'", "Get port.");
		}
		
		$type = $input['type'] + 0;
		$info = cmscode($input['info'], 'cmscode');
		$priority = $core_db->lookup("select COUNT(*) from servers where clan_id = {$cfg['clan_id']}") + 1;
		
		$core_db->query(
			"INSERT servers SET
			ip 			=	'$ip',
			port		=	'$port',
			type		=	'$type',
			info 		=	'$info',
			priority = '$priority',
			clan_id 	=	'{$cfg['clan_id']}',
			create_id 	=	'{$cfg['user_id']}',
			edit_id 	=	'{$cfg['user_id']}',
			create_date	=	now(),
			edit_date 	=	now()",
			"insert"
		);
		
		$id = mysql_insert_id();
		add_log("Inserted Server (ID: $id)");
		go_back("msg=i", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Servers UPDATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "update":
		
		// Always seportate IP from port.
		$address = explode(":", $input['ip']);
		
		if (!$input['port']) {
			// If the user hasn't specified a port, then use the one post-fixed on the end of the address.
			$address = explode(":", $input['ip']);
			$ip = $address[0];
			$port = $address[1] + 0;
			
		} else {
			// However if a port was given, make sure a 0 is added.
			$ip = $address[0];
			$port = $input['port'] + 0;
		}
		
		// If there's STILL no port...
		if (!$port) {
			$port = $core_db->lookup("SELECT port FROM clan_types WHERE id = '{$cfg['clan_type']}'", "Get port.");
		}
		
		$type = $input['type'] + 0;
		$info = cmscode($input['info'], 'cmscode');
		
		$core_db->query(
			"UPDATE servers SET
			ip 			=	'$ip',
			port		=	'$port',
			type		=	'$type',
			info 		=	'$info',
			edit_id		=	'{$cfg['user_id']}',
			edit_date	=	now()
			WHERE id =	'$id'
			AND clan_id = '{$cfg['clan_id']}'",
			"update"
		);
		
		add_log("Updated Server (ID: $id)");
		go_back("msg=u", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Servers DELETE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "delete":
		
		if ($item_order) {
			foreach($item_order as $id => $value) {
				$id = $id + 0;
				$value = $value + 0;
				$core_db->query(
					"UPDATE servers SET priority = '$value'
					WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Update list of servers."
				);
				add_log("Updated Server (ID: $key)");
			}
			$msg = "um";
			unset($id);
		}
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach($item as $key => $id) {
				$id = $id + 0;
				$core_db->query(
					"DELETE FROM servers WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete servers."
				);
				add_log("Deleted Server (ID: $id)");
				$i++;
			}
			
			// Deleted one, or several?
			if ($i == 1) {
				$msg = "ds";
			} elseif ($i > 1) {
				$msg = "d";
			}
		}
		
		go_back("msg=$msg", "?mod=$mm->module&action=admin");
		
	break;
}