<?php

class fixture_ctrl {
	
	function modify($method) {
		
		global $core_db, $mm, $cfg;
		
		if ($method == "update") {
			$mm->ifempty($_POST['id'], "?mod=$mm->module");
		}
		
		$int = array(
			'clan_id'	=>	$_POST['clan_id'],
			'type_id'	=>	$_POST['type_id'],
			'day'			=>	$_POST['day'],
			'month'		=>	$_POST['month'],
			'year'		=>	$_POST['year'],
			'hour'		=>	$_POST['hour'],
			'minute'	=>	$_POST['minute']
		);
		
		foreach($int as $k => $v) { $int[$k] += 0; }
		$_POST = array_merge($_POST, $int);
		
		if ((!$_POST['url'] || !$_POST['name']) && $_POST['tag']) {
			
			if (!$_POST['url']) { $getvars[] = "vs_url AS url"; }
			if (!$_POST['name']) { $getvars[] = "vs_name AS name"; }
			
			$core_db->query(
				"SELECT " . implode(", ", $getvars) . " FROM fixtures
				WHERE vs_tag LIKE '{$_POST['tag']}'",
				"Lookup clan name and URL."
			);
			
			if ($core_db->get_num_rows()) {
				$_POST = array_merge($_POST, $core_db->fetch_row());
			}
		}
		
		$_POST['date'] = sql_date($_POST['day'], $_POST['month'], $_POST['year'], $_POST['hour'], $_POST['minute']);
		
		switch ($method) {
			case "insert":
				
				$core_db->query(
					"INSERT fixtures SET
					vs_id						= '{$_POST['clan_id']}',
					vs_tag					= '{$_POST['tag']}',
					vs_name					= '{$_POST['name']}',
					vs_url					= '{$_POST['url']}',
					match_date 			= '{$_POST['date']}',
					match_type 			= '{$_POST['type_id']}',
					players_needed	= '{$_POST['players']}',
					server					=	'{$_POST['server']}',
					clan_id 				= '{$cfg['clan_id']}',
					create_id				= '{$cfg['user_id']}',
					edit_id					= '{$cfg['user_id']}',
					create_date			= now(),
					edit_date 			= now()",
					"Insert match details."
				);
				
				$fixture_id = $core_db->get_insert_id();
				
			break;
			
			case "update":
				
				$core_db->query(
					"UPDATE fixtures SET
					vs_id						= '{$_POST['clan_id']}',
					vs_tag					= '{$_POST['tag']}',
					vs_name					= '{$_POST['name']}',
					vs_url					= '{$_POST['url']}',
					match_date 			= '{$_POST['date']}',
					match_type 			= '{$_POST['type_id']}',
					players_needed	= '{$_POST['players']}',
					server					=	'{$_POST['server']}',
					edit_id					= '{$cfg['user_id']}',
					edit_date 			= now()
					WHERE id				= '{$_POST['id']}'
					AND clan_id			=	'{$cfg['clan_id']}'",
					"Insert match details."
				);
				
				$fixture_id = $_POST['id'];
				
				$core_db->query(
					"DELETE FROM scores WHERE fix_id = '$fixture_id' AND clan_id = '{$cfg['clan_id']}'",
					"Delete all existing scores that belong to this fixture."
				);
				
			break;
		}
		
		if ($_POST['map_name']) {
			
			foreach ($_POST['map_name'] as $k => $v) {
				if (($k += 0) && ($v = trim($v))) {
					$core_db->query(
						"INSERT scores SET
						map = '$v',
						clan_id	= '{$cfg['clan_id']}',
						item_id	= '$k',
						fix_id = '$fixture_id'",
						"Insert new map."
					);
				}
			}
		}
		
		switch ($method) {
			
			case "insert":
				add_log("Inserted Fixture (ID: $fixture_id)");
				go_back("msg=i", "?mod=$mm->module&action=admin");
			break;
			
			case "update";
				add_log("Updated Fixture (ID: $fixture_id)");
				go_back("msg=u", "?mod=$mm->module&action=admin");
			break;
		}
	}
}

switch($mm->action) {
	
	case "default": redirect("&action=browse"); break;
	
	/*
	+ ------------------------------------------------
	| Fixtures BROWSE
	+ ------------------------------------------------
	| Shows items from fixtures table where date is
	| before now.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "browse":
		
		$t->set_file('subTemplate');
		
		$srt->set_array( array('vs_tag; opponent', 'match_type; type', 'date', 'time', 'timeleft'), 'timeleft', 'asc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"select id, vs_id, vs_name, vs_tag, vs_url, match_type,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['short']}') as date,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['time']}') as time,
			(unix_timestamp(match_date) - unix_timestamp(now())) as timeleft
			from fixtures
			where clan_id = {$cfg['clan_id']}
			and (unix_timestamp(match_date) > unix_timestamp(now()))".
			$srt->sql(),
			"get fixtures"
		);
		
		if ($core_db->get_num_rows()) {
			
			$nav->navs[] = $lang["fixtures"];
			
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			
			$nav->page_index("select COUNT(*) from fixtures
				where clan_id = {$cfg['clan_id']}
				and (unix_timestamp(match_date) > unix_timestamp(now()))");
			
			while ($row = $core_db->fetch_row($result)) {
				
				$clan = fetch_clan_name($row['vs_id'], $row['vs_tag'], $row['vs_name'], $row['vs_url']);
				$row['clan'] = $clan['name'];
				if ($clan['url']) {
					$row['tag'] = "<a href=http://'{$row['vs_url']}' target='_blank'>{$clan['tag']}</a>";
				} else {
					$row['tag'] = $clan['tag'];
				}
				
				$row['match_type'] = $core_db->lookup("SELECT name FROM match_types WHERE (clan_id = 0 OR clan_id = {$cfg['clan_id']}) AND id = $row[match_type]", "match type");
				
				$maps_result = $core_db->query("SELECT map FROM scores WHERE fix_id = $row[id] AND map != '' order by item_id asc", "get list of maps");
				if ($core_db->get_num_rows($maps_result)) {
					$maps_list = array();
					//Reset for each item
					while ($maps_row = $core_db->fetch_row($maps_result)) {
						if (!in_array($maps_row['map'], $maps_list)) $maps_list[] = $maps_row['map'];
					}
					$row['maps'] = implode(", ", $maps_list);
				} else {
					$row['maps'] = "";
				}
				
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
				$row['timeleft'] = "$time_left $lang[$time_type]";
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_fixtures', true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Results DETAILS
	+ ------------------------------------------------
	| Shows all the info given for that match.
	+ ------------------------------------------------
	| Added: v3.4 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "details":
		
		$mm->ifempty($id, "?mod=$mm->module");
		
		$result = $core_db->query(
			"SELECT fixtures.id, fixtures.vs_id, fixtures.vs_name,
			fixtures.vs_tag, fixtures.match_type, fixtures.create_id,
			fixtures.players_needed, fixtures.server, fixtures.vs_url,
			(unix_timestamp(match_date) - unix_timestamp(now())) AS timeleft,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['short']}') AS date,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['time']}') AS time,
			match_types.name AS match_type_name
			FROM fixtures
			LEFT JOIN match_types ON match_types.id = fixtures.match_type
			WHERE fixtures.clan_id = '{$cfg['clan_id']}'
			AND (unix_timestamp(fixtures.match_date) > unix_timestamp(now()))
			AND fixtures.id = $id",
			"Get details."
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'FixturesBlock');
			$row = cmscode($core_db->fetch_row($result), 'html');
			$t->set_msg_var();
			
			set_admin_links('admin_links',
				array(
					"$lang[edit]; ?mod=$mm->module&action=edit&id={$row['id']}",
					"$lang[delete]; ?mod=$mm->module&action=delete&id={$row['id']}; true; delete_item_q",
				)
			);
			
			$clan = fetch_clan_name($row['vs_id'], $row['vs_tag'], $row['vs_name'], $row['vs_url']);
			$row['clan'] = $clan['name'];
			if ($clan['url']) {
				$row['tag'] = "<a href=http://'{$row['vs_url']}' target='_blank'>{$clan['tag']}</a>";
			} else {
				$row['tag'] = $clan['tag'];
			}
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['fixtures']}</a>",
				"{$lang['details']} ({$clan_inf['tag']} - {$row['match_type_name']})"
			);
			
			$maps_result = $core_db->query("SELECT map FROM scores WHERE fix_id = $row[id] AND map != '' order by item_id asc", "get array of maps");
			if ($core_db->get_num_rows($maps_result)) {
				
				// Reset for each item.
				$maps_list = "";
				
				while ($value = $core_db->fetch_row($maps_result)) {
					
					$map_name = $value['map'];
					$map_image = seek_file($map_name, "./images/maps/", array("jpg", "gif"), 1, "\nGame: {$cfg['clan_type_desc']}");
					
					$map_image =
						"<table border=1 cellspacing=0 cellpadding=5 class=tableDefault>".
						"<tr><td bordercolor=#000000>".
						"<img src=$map_image>".
						"</td></tr></table>";
					
					$cfg['preloads'] .= $map_image;
					$maps_list .= "<span onmouseover=\"return overlib('$map_image');\" onmouseout=\"return nd();\">$map_name</span>, ";
				}
				$maps_list .= '%void_comma%';
				$row['maps'] = str_replace(', %void_comma%', '', $maps_list);
				
			} else {
				$row['maps'] = '';
			}
			
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
			
			$server = explode(':', $row['server']);
			$server_id = $core_db->lookup(
				"SELECT id FROM servers
				WHERE clan_id = {$cfg['clan_id']}
				AND ip = '" . addslashes($server[0]) . "'
				AND port = '" . addslashes($server[1]) . "'"
			);
			if ($server_id) $row['server'] .= " (<a href=?mod=servers&action=details&id=$server_id>{$lang['details']}</a>)";
			
			$row['availability'] = sqllistbox(FALSE, "SELECT id, name FROM fix_slot_opts WHERE type = 'A'", FALSE, TRUE, TRUE);
			$row['position'] = sqllistbox(FALSE, "SELECT id, name FROM fix_slot_opts WHERE type = 'P'", FALSE, TRUE, TRUE);
			$row['duration'] = sqllistbox(FALSE, "SELECT id, name FROM fix_slot_opts WHERE type = 'D'", FALSE, TRUE, TRUE);
			$row['post_comment'] = ($t->check_privs(9, 'create') && !$preview) ? TRUE : FALSE;
			$row['timeleft'] = "$time_left $lang[$time_type]";
			$row['slots_used'] = $core_db->lookup("SELECT COUNT(*) FROM fix_registry WHERE clan_id = {$cfg['clan_id']} AND fix_id = $row[id]");
			$row['match_type'] = $core_db->lookup("SELECT name FROM match_types WHERE (clan_id = 0 OR clan_id = {$cfg['clan_id']}) AND id = $row[match_type]");
			
			// *************************************************************** //
			// Controls which buttons are shown for registering slots in matches.
			// *************************************************************** //
			$t->set_block('FixturesBlock', 'HideExInfo');
			$t->set_block('HideExInfo', 'HideReg');
			$t->set_block('HideExInfo', 'HideUnReg');
			$t->set_block('HideExInfo', 'SlotsTaken');
			
			$has_registered = $core_db->lookup("SELECT id FROM fix_registry WHERE user_id = {$cfg['user_id']} AND clan_id = {$cfg['clan_id']} AND fix_id = $id", "has reg'd?");
			$slots_used = $core_db->lookup("SELECT COUNT(*) FROM fix_registry WHERE clan_id = {$cfg['clan_id']} AND fix_id = $row[id]", "slots used");
			$slots_full = ($slots_used >= $row['players_needed'] ? TRUE : FALSE);
			
			if($has_registered || $slots_full) $row['HideReg'] = "<!-- Reg is hidden. -->";
			if(!$has_registered) $row['HideUnReg'] = "<!-- UnReg is hidden. -->";
			if(!$slots_full) $row['SlotsTaken'] = "<!-- SlotsTaken is hidden. -->";
			if(!$cfg['pub_fix_e_info'] && ($cfg['userlevel'] == 1)) $row['HideExInfo'] = "<!-- ExInfo is hidden. -->";
			// *************************************************************** //
			
			$slots_result = $core_db->query("SELECT user_id FROM fix_registry WHERE clan_id = {$cfg['clan_id']} AND fix_id = $id order by id", "slots result");
			if ($core_db->get_num_rows($slots_result)) {
				$slots_list = false;
				// Reset for each item
				while ($value = $core_db->fetch_row($slots_result)) {
					$name = $core_db->lookup("SELECT name FROM users WHERE id = $value[user_id]", "name of registered player");
					if($name == $cfg['username']) {
						$slots_list .= "<b>$name</b>, ";
					} else {
						
						// If there's no name in the db, the user dosen't exist.
						if (!$name) { $name = $lang['ex_member']; }
						
						$slots_list .= "$name, ";
					}
				}
				$slots_list .= '%void_comma%';
				$row['slots'] = str_replace(', %void_comma%', '', $slots_list);
			} else {
				$row['slots'] = "<b>{$lang['None']}</b>";
				echo $t->kill_block("details_link", FALSE, "HideExInfo");
			}
			
			$t->set_array($row);
			
			$t->parse('FixturesBlock', 'FixturesBlock');
			
			if (!$t->check_privs(9, 'create') || $preview) $t->kill_block($var, 'post_comment', "subTemplate");
			
			if ($cmts->num_comments) {
				$cmts->set_comments($row['id']);
			} else {
				$t->kill_block('AllComments', "", 'subTemplate');
			}
			
		} else {
			$t->set_msg('no_fixtures_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Fixtures ADMIN
	+ ------------------------------------------------
	| Same as BROWSE, but with admin features.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		$srt->set_array( array('vs_tag; tag', 'vs_name; clan', '', 'match_date; date', 'time'), 'match_date', 'asc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"select id, vs_id, vs_name, vs_tag, match_type,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['short']}') as date,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['time']}') as time
			from fixtures
			where clan_id = {$cfg['clan_id']}
			and (unix_timestamp(match_date) > unix_timestamp(now()))" . $srt->sql()
		, ("get fixtures") );
		
		$i = 0;
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->page_index("select COUNT(*) from fixtures
				where clan_id = {$cfg['clan_id']}
				and (unix_timestamp(match_date) > unix_timestamp(now()))");
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['fixtures']}</a>",
				$lang['admin']
			);
			
			while ($row = $core_db->fetch_row($result)) {
				if ($row['vs_id'] == 0) {
					$row['vstag'] = $row['vs_tag'];
					$row['vsclan'] = $row['vs_name'];
				} else {
					$vs_tag = $core_db->lookup("select tag from clans where id = $row[vs_id]");
					$vs_name = $core_db->lookup("select display from clans where id  = $row[vs_id]");
					if ($vs_tag) { $row['vstag'] = $vs_tag; }
					$row['vsclan'] = $vs_name;
				}
				
				$cmts->set_count($row['id']);
				$row['num_comments'] = $cmts->num_comments;
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_fixtures', true, true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Fixtures CREATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang['fixtures']}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			$lang['create']
		);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate');
		
		$t->set_file('SelectClanBlock', '../../../source/templates/select_clan_block.ihtml');
		$t->set_block('SelectClanBlock');
		
		$t->set_var('clan_id_select', $core_db->sqllistbox(
			'clan_id', "SELECT id, display FROM clans WHERE id != '{$cfg['clan_id']}'",
			"", "{lang_Custom}...", "", "", "", " onChange='checkShowCustom()'"
		));
		
		$t->set_var('type_id_select', sqllistbox(
			'type_id', "SELECT id, name FROM match_types
			WHERE (clan_id = '{$cfg['clan_id']}' OR clan_id = 0)
			AND (game_id = '{$cfg['clan_type']}' OR game_id = 0)"
		));
		
		$t->set_var('day', date('d'));
		$t->set_var('month', monthlistbox('month', date('n')));
		$t->set_var('year', date('Y'));
		$t->set_var('hour', leading_zero(ceil(date('H')) + 1, 2));
		$t->set_var('minute', "00");
		
		$t->set_file('RowController', '../../../source/templates/row_ctrl_block.ihtml');
		$t->set_block('RowController');
		
		$t->set_var('delete_parameters', "'{row_id}', '{lang_map_delete_warn}'");
		$t->set_var(array('row_id' => "%rowID%", 'row_count' => 2, 'field_name' => 'map_name'));
		$t->set_var(array('row_label' => '{lang_map}', 'field_width' => 15));
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Fixtures EDIT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		/* --------------------------------------------- +
		| Fixtures > EDIT
		+ ---------------------------------------------- +
		| - Brings back all information on the fixture
		|   which maches the ID specified.
		| - List of maps is constructed from a template
		|   used in the CREATE action.
		| - The maps table can be appended and modified
		|   as nessecary by the JavaScript handler on the
		|   returned page.
		+ ---------------------------------------------- +
		| Introduced: 3.0 Beta
	 	| Modified: 4.5.3 (Thursday 18 Dec 2003)
		+ --------------------------------------------- */
		
		$id = $_GET['id'] + 0;
		$mm->ifempty($id);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate');
		
		$result = $core_db->query(
			"SELECT fixtures.id, fixtures.vs_id, fixtures.vs_name AS name,
			fixtures.vs_tag AS tag, fixtures.vs_url AS url, fixtures.server,
			fixtures.players_needed AS players, fixtures.match_type,
			DATE_FORMAT(fixtures.match_date, '%d') as day,
			DATE_FORMAT(fixtures.match_date, '%m') as month,
			DATE_FORMAT(fixtures.match_date, '%Y') as year,
			DATE_FORMAT(fixtures.match_date, '%H') as hour,
			DATE_FORMAT(fixtures.match_date, '%i') as minute,
			clans.display AS clan_name, match_types.name AS match_type_name
			FROM fixtures
			LEFT JOIN match_types ON match_types.id = fixtures.match_type
			LEFT JOIN clans ON clans.id = fixtures.vs_id
			WHERE fixtures.id = $id
			AND fixtures.clan_id = '{$cfg['clan_id']}'",
			"Pull fixture details for editing."
		);
		
		$i = 0;
		
		if ($core_db->get_num_rows()) {
			
			$row = $core_db->fetch_row($result);
			$row = cmscode($row, 'cmscode');
			
			$maps = $core_db->query(
				"SELECT map AS field_value FROM scores WHERE
				fix_id = '{$row['id']}' ORDER BY item_id ASC",
				"Get list of maps."
			);
			
			if (!$row['clan_name']) {
				$row['clan_name'] = $row['name'];
				$row['clan_tag'] = $row['tag'];
			}
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
			$t->set_var($row);
		
			$t->set_file('SelectClanBlock', '../../../source/templates/select_clan_block.ihtml');
			$t->set_block('SelectClanBlock');
			
			$t->set_file('RowController', '../../../source/templates/row_ctrl_block.ihtml');
			$t->set_block('RowController', 'LayerRow', 'LayerBlock');
			
			if ($core_db->get_num_rows($maps)) {
				
				$i = 1;
				
				while ($maps_row = $core_db->fetch_row($maps)) {
					
					$maps_row['delete_parameters'] = "'{row_id}', '{lang_restore_maps_tip}'";
					$maps_row['row_id'] = $i++;
					
					$t->set_var($maps_row);
					$t->parse('MapsBlock', 'LayerRow', TRUE);
				}
			}
			
			$t->set_var('delete_parameters', "'%rowID%', '{lang_restore_maps_tip}'");
			$t->set_var(array('field_name' => 'map_name',  'field_value' => '', 'field_width' => 15));
			$t->set_var(array('row_id' => '%rowID%', 'row_label' => '{lang_map}'));
			$t->parse('LayerBlock', 'LayerRow');
			
			$t->set_var('row_count', $core_db->get_num_rows($scores));
			
			$t->set_var('type_id_select', $core_db->sqllistbox(
				'type_id', "SELECT id, name FROM match_types
				WHERE (clan_id = '{$cfg['clan_id']}' OR clan_id = 0)
				AND (game_id = '{$cfg['clan_type']}' OR game_id = 0)",
				$row['match_type']
			));
			
			$t->set_var('clan_id_select', $core_db->sqllistbox(
				'clan_id', "SELECT id, display FROM clans WHERE id != '{$cfg['clan_id']}'",
				$row['vs_id'], "{lang_Custom}...", "", "", "", " onChange='checkShowCustom()'"
			));
			
			$t->set_var('month', monthlistbox('month', $row['month']));
			
		} else {
			$t->set_msg('no_fixtures_details');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	case "insert":
		
		$result = new fixture_ctrl;
		$result->modify('insert');
		
	break;
	
	case "update":
		
		$result = new fixture_ctrl;
		$result->modify('update');
		
	break;
	
	/* -----------------------------------------------
	| Fixtures REGISTER
	+ ------------------------------------------------
	| Registers a user for playing in a match with
	| Availability, Position, etc.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "register";
		
		$mm->ifempty($id, "?mod=$mm->module");
		
		$players_needed = $core_db->lookup("select players_needed from fixtures where id = $id and clan_id = {$cfg['clan_id']}");
		$slots_used = $core_db->lookup("select COUNT(*) from fix_registry where clan_id = {$cfg['clan_id']} and fix_id = $id");
		
		if($slots_used < $players_needed) {
			
			if(!$core_db->lookup("select id from fix_registry where user_id = {$cfg['user_id']} and clan_id = {$cfg['clan_id']} and fix_id = $id", "fixtures: check for slot")
				&& ($cfg['user_id'] != $cfg['pub_id'])) {
				
				$availability = $input['availability'] + 0;
				$position = $input['position'] + 0;
				$duration = $input['duration'] + 0;
				
				$core_db->query (
					"insert fix_registry set
					user_id = {$cfg['user_id']},
					clan_id = {$cfg['clan_id']},
					fix_id = $id,
					availability = '$availability',
					position = '$position',
					duration = $duration"
				, ("register to play"));
				
				add_log("Registered for Fixture (ID: $id)");
				go_back("msg=r");
				
			} else {
				add_log("Already Registered for Fixture (ID: $id)");
				go_back("msg=ar");
			}
		} else {
			add_log("No slots left for Fixture (ID: $id)");
			go_back("msg=cr");
		}
		
	break;
	
	/* -----------------------------------------------
	| Fixtures UNREGISTER
	+ ------------------------------------------------
	| Cancells user's registration slot for a match.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "unregister";
		
		$mm->ifempty($id, "?mod=$mm->module");
		
		$core_db->query (
			"delete from fix_registry where user_id = {$cfg['user_id']} and clan_id = {$cfg['clan_id']} and fix_id = $id"
		, ("cancel user reg slot"));
		
		add_log("Unregistered for Fixture (ID: $id)");
		go_back("msg=ur");
		
	break;
	
	/* -----------------------------------------------
	| Fixtures SLOTS
	+ ------------------------------------------------
	| Shows detailed info about what slots are taken.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "slots":
		
		$mm->ifempty($id, "?mod=help");
		
		$t->set_file('subTemplate');
		
		$srt->set_array( array('user_id; name', 'availability', 'position', 'duration',), 'availability', 'asc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT user_id, availability, position, duration
			FROM fix_registry
			WHERE fix_id = '$id'
			AND clan_id = '{$cfg['clan_id']}'".
			$srt->sql(),
			"Get registered slots."
		);
		
		if ($core_db->get_num_rows()) {
			
			$fix_info = $core_db->query(
				"SELECT fixtures.vs_id, fixtures.vs_tag,
				fixtures.vs_name, match_types.name AS match_type
				FROM fixtures
				LEFT JOIN match_types ON match_types.id = fixtures.match_type
				WHERE fixtures.id = '$id'",
				"Lookup clan info for PathFinder"
			);
			
			$fix_info = $core_db->fetch_row($fix_info);
			$clan = fetch_clan_name($fix_info['vs_id'], $fix_info['vs_tag']);
			
			$nav->navs = array(
				$cfg['site_name'], $lang[$mm->module],
				"{$lang['details']} ({$clan['tag']} - {$fix_info['match_type']})",
				$lang['slots']
			);
			
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			
			$nav->page_index("select COUNT(*) from fix_registry where
				fix_id = $id and clan_id = {$cfg['clan_id']}");
			
			while ($row = $core_db->fetch_row($result)) {
				$row['name'] = $core_db->lookup("select name from users where id = $row[user_id]", "fixtures: slots: get slot user");
				$row['availability'] = $core_db->lookup("select name from fix_slot_opts where id = $row[availability]", "fixtures: slots: availability");
				$row['position'] = $core_db->lookup("select name from fix_slot_opts where id = $row[position]", "fixtures: slots: position");
				$row['duration'] = $core_db->lookup("select name from fix_slot_opts where id = $row[duration]", "fixtures: slots: duration");
				$row['fix_id'] = $id;
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_fixtures_id', true);
		}
		$t->mparse('Output', 'subTemplate', "popup.ihtml");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Fixtures DELETE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "delete":
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach($item as $key => $id) {
				$id = $id + 0;
				$core_db->query(
					"DELETE FROM fixtures WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete fixture."
				);
				$core_db->query(
					"DELETE FROM scores WHERE fix_id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete scores & maps."
				);
				add_log("Deleted Fixture (ID: $id)");
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