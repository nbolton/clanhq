<?php

/*
	Define what items can go into
	the db for each type of settings...
*/

$private_vars = array(
	'theme_id', 'default_mod', 'lang', 'select_logo', 'select_server', 'messages_popup',
	'logo_align', 'show_headlines', 'show_site_stats', 'show_poll', 'show_next_fixture',
	'show_last_result', 'show_match_stats', 'show_buddy_list', 'show_server_watch', 'show_messages',
	'avatar_news', 'avatar_comments', 'avatar_reports', 'diskspace_sizetype', 'bandwidth_sizetype',
	'left_panels_list', 'right_panels_list'
);

$global_vars = array(
	'theme_id', 'default_mod', 'lang', 'scoring', 'select_logo', 'select_server',
	'logo_align', 'show_headlines', 'show_site_stats', 'show_poll', 'show_next_fixture',
	'show_last_result', 'show_match_stats', 'show_buddy_list', 'show_server_watch', 'show_messages',
	'avatar_news', 'avatar_comments', 'avatar_reports', 'left_panels_list', 'right_panels_list'
);

switch ($mm->action) {
	
	/*
	+ ------------------------------------------------
	| Admin DEFAULT
	+ ------------------------------------------------
	| Shows shortcuts to diffrent module admins.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "default":
		
		$nav->navs[] = $lang["settings"];
		
		if ($cfg['admin']) {
			// CMS has detected that this user is an admin.
			$t->set_file('subTemplate');
			$t->mparse('Output', 'subTemplate');
		} else {
			// Otherwise, just send them to their settings.
			header("Location:http:?mod=settings&action=private");
		}
		
	break;
	
	/* -----------------------------------------------
	| Settings PRIVATE
	+ ------------------------------------------------
	| Lets user change all of their private settings
	| such as Theme, Language, etc.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "private":
		
		$nav->navs[] = array(
			"<a href='?mod=settings'>{$lang['settings']}</a>",
			$lang['private'],
		);
		
		if(!$core_db->lookup("SELECT id FROM settings_private WHERE user_id = '{$cfg['user_id']}'")) {
			
			foreach($private_vars as $var) {
				$sql_vars[$i++] = "$var = '$cfg[$var]'";
			}
			
			// Turns array into string.
			$sql_vars = implode(", \n", $sql_vars);
			
			$core_db->query(
				"INSERT settings_private SET
				user_id = '{$cfg['user_id']}',
				clan_id = '{$cfg['clan_id']}',
				$sql_vars",
				"make private settings"
			);
		}
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$t->set_msg_var();
		
		$t->set_file('panels_javascript', '../../../js/panels.js');
		$t->set_block('panels_javascript');
		
		$t->set_block('subTemplate', "PanelSettings", "PSBlock");
		foreach (array('left', 'right') as $side) {
			foreach ($cfg["{$side}_panels_list"] as $panel) {
				$panels[] = "\"{$panel}\"";
				$t->set_var("panel_lang", "{lang_{$panel}}");
				$t->set_var("panel_select", "{select_{$panel}}");
				$t->set_var("panel", $panel);
				$t->parse("PSBlock", "PanelSettings", TRUE);
			}
			$t->set_var("{$side}_panels_array", implode(', ', $panels));
			unset($panels);
		}
		
		$result = $core_db->query(
			"SELECT * FROM settings_private WHERE user_id = {$cfg['user_id']}",
			"get private settings"
		);
		
		$row = $core_db->fetch_row($result);
		
		$set_row['select_theme'] = sqllistbox('input[theme_id]', "SELECT id, name from themes WHERE clan_id = 0 OR clan_id = {$cfg['clan_id']}", $row['theme_id']);
		$set_row['select_home'] = sqllistbox('input[default_mod]', "select id, name from modules", $row['default_mod'], FALSE, FALSE, FALSE, TRUE);
		
		$sidebar_modes = array(1 => "+ ".$lang['maximize'], 2 => "- ".$lang['minimize'], 0 => "X ".$lang['close']);
		$set_row['select_latest_news'] = arraylistbox('input[show_headlines]', $sidebar_modes, $row['show_headlines']);
		$set_row['select_site_stats'] = arraylistbox('input[show_site_stats]', $sidebar_modes, $row['show_site_stats']);
		$set_row['select_latest_poll'] = arraylistbox('input[show_poll]', $sidebar_modes, $row['show_poll']);
		$set_row['select_next_fixture'] = arraylistbox('input[show_next_fixture]', $sidebar_modes, $row['show_next_fixture']);
		$set_row['select_last_result'] = arraylistbox('input[show_last_result]', $sidebar_modes, $row['show_last_result']);
		$set_row['select_match_stats'] = arraylistbox('input[show_match_stats]', $sidebar_modes, $row['show_match_stats']);
		$set_row['select_buddy_list'] = arraylistbox('input[show_buddy_list]', $sidebar_modes, $row['show_buddy_list']);
		$set_row['select_server_watch'] = arraylistbox('input[show_server_watch]', $sidebar_modes, $row['show_server_watch']);
		$set_row['select_messages'] = arraylistbox('input[show_messages]', $sidebar_modes, $row['show_messages']);
		$set_row['select_main_menu'] = arraylistbox('', array("+ " . $lang['maximize']), '');
		
		$set_row['select_language'] = arraylistbox('input[lang]', $cfg['languages'], $row['lang']);
		$set_row['select_avatar_news'] = yn('input[avatar_news]', $row['avatar_news'], 'radio');
		$set_row['select_avatar_comments'] = yn('input[avatar_comments]', $row['avatar_comments'], 'radio');
		$set_row['select_avatar_reports'] = yn('input[avatar_reports]', $row['avatar_reports'], 'radio');
		
		if ($cfg['admin']) {
			$sizetypes = array('KB' => "KiloBytes (KB)", 'MB' => "MegaBytes (MB)", 'GB' => "GigaBytes (GB)");
			$set_row['select_diskspace_sizetype'] = arraylistbox('input[diskspace_sizetype]', $sizetypes, $row['diskspace_sizetype']);
			$set_row['select_bandwidth_sizetype'] = arraylistbox('input[bandwidth_sizetype]', $sizetypes, $row['bandwidth_sizetype']);
		} else {
			$set_row['admin_settings'] = "";
			$set_row['admin_menu'] = "";
		}
		
		$popuptypes = array(0 => $lang['disabled'], 1 => $lang['one_message'], 2 => $lang['all_messages']);
		$set_row['select_messages_popup'] = arraylistbox('input[messages_popup]', $popuptypes, $row['messages_popup']);
		
		$result = $core_db->query(
			"SELECT id, ip, port, type
			FROM servers
			WHERE clan_id = {$cfg['clan_id']}
			ORDER BY priority ASC",
			"get servers"
		);
		
		if ($core_db->get_num_rows()) {
			while ($server_row = $core_db->fetch_row($result)) {
				$server_row['address'] = "$server_row[ip]:$server_row[port]";
				$server_row['qstat_type'] = $core_db->lookup("select type from clan_types where id = $server_row[type]");
				$server = new phpQStat($server_row['qstat_type'], $server_row['address'], true, true);
				$servers_list[$server_row['id']] = $server->server_name;
			}
		}
		
		$set_row['select_server_menu'] = arraylistbox('input[select_server]', $servers_list, $row['select_server'], TRUE);
		
		$align = array('left' => $lang['left'], 'right' => $lang['right'], 'center' => $lang['center']);
		$set_row['select_logo_align'] = arraylistbox('input[logo_align]', $align, $row['logo_align']);
		
		$files_db = new DBDriver;
		$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
		
		$set_row['select_logo_menu'] = $files_db->sqllistbox('input[select_logo]',
			"select id, filename from files where clan_id = {$cfg['clan_id']} and cat_id = 4", $row['select_logo'], TRUE, FALSE, 20);
		
		$t->set_array($set_row, "", "", "");
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/* -----------------------------------------------
	| Settings GLOBAL
	+ ------------------------------------------------
	| Lets admins change all of their site's global
	| settings such as Theme, Language, etc.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "global":
		
		$nav->navs[] = array(
			"<a href='?mod=settings'>{$lang['settings']}</a>",
			$lang['global'],
		);
		
		if(!$core_db->lookup("SELECT id FROM settings_global WHERE clan_id = {$cfg['clan_id']}")) {
			
			$i = 0;
			foreach($global_vars as $var) {
				$sql_vars[$i++] = "$var = '$cfg[$var]'";
			}
			
			// Turns array into string.
			$sql_vars = implode(", \n", $sql_vars);
			
			$core_db->query(
				"INSERT settings_private SET
				clan_id = '{$cfg['clan_id']}',
				$sql_vars",
				"make global settings"
			);
		}
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$t->set_msg_var();
		
		$t->set_file('panels_javascript', '../../../js/panels.js');
		$t->set_block('panels_javascript');
		
		$t->set_block('subTemplate', "PanelSettings", "PSBlock");
		foreach (array('left', 'right') as $side) {
			foreach ($cfg["{$side}_panels_list"] as $panel) {
				$panels[] = "\"{$panel}\"";
				$t->set_var("panel_lang", "{lang_{$panel}}");
				$t->set_var("panel_select", "{select_{$panel}}");
				$t->set_var("panel", $panel);
				$t->parse("PSBlock", "PanelSettings", TRUE);
			}
			$t->set_var("{$side}_panels_array", implode(', ', $panels));
			unset($panels);
		}
		
		$result = $core_db->query(
			"SELECT * FROM settings_global WHERE clan_id = {$cfg['clan_id']}",
			"get private settings"
		);
		$row = $core_db->fetch_row($result);
		
		$set_row['select_theme'] = sqllistbox('input[theme_id]', "SELECT id, name from themes WHERE clan_id = 0 OR clan_id = {$cfg['clan_id']}", $row['theme_id']);
		$set_row['select_home'] = sqllistbox('input[default_mod]', "select id, name from modules", $row['default_mod'], FALSE, FALSE, FALSE, TRUE);
		
		$sidebar_modes = array(1 => "+ ".$lang['maximize'], 2 => "- ".$lang['minimize'], 0 => "X ".$lang['close']);
		$set_row['select_latest_news'] = arraylistbox('input[show_headlines]', $sidebar_modes, $row['show_headlines']);
		$set_row['select_site_stats'] = arraylistbox('input[show_site_stats]', $sidebar_modes, $row['show_site_stats']);
		$set_row['select_latest_poll'] = arraylistbox('input[show_poll]', $sidebar_modes, $row['show_poll']);
		$set_row['select_next_fixture'] = arraylistbox('input[show_next_fixture]', $sidebar_modes, $row['show_next_fixture']);
		$set_row['select_last_result'] = arraylistbox('input[show_last_result]', $sidebar_modes, $row['show_last_result']);
		$set_row['select_match_stats'] = arraylistbox('input[show_match_stats]', $sidebar_modes, $row['show_match_stats']);
		$set_row['select_buddy_list'] = arraylistbox('input[show_buddy_list]', $sidebar_modes, $row['show_buddy_list']);
		$set_row['select_server_watch'] = arraylistbox('input[show_server_watch]', $sidebar_modes, $row['show_server_watch']);
		$set_row['select_messages'] = arraylistbox('input[show_messages]', $sidebar_modes, $row['show_messages']);
		$set_row['select_main_menu'] = arraylistbox('', array("+ " . $lang['maximize']), '');
		
		$set_row['select_language'] = arraylistbox('input[lang]', $cfg['languages'], $row['lang']);
		$set_row['select_avatar_news'] = yn('input[avatar_news]', $row['avatar_news'], 'radio');
		$set_row['select_avatar_comments'] = yn('input[avatar_comments]', $row['avatar_comments'], 'radio');
		$set_row['select_avatar_reports'] = yn('input[avatar_reports]', $row['avatar_reports'], 'radio');
		
		$result = $core_db->query(
			"SELECT id, ip, port, type
			FROM servers
			WHERE clan_id = {$cfg['clan_id']}
			ORDER BY priority ASC",
			"get servers"
		);
		
		if ($core_db->get_num_rows()) {
			while ($server_row = $core_db->fetch_row($result)) {
				$server_row['address'] = "$server_row[ip]:$server_row[port]";
				$server_row['qstat_type'] = $core_db->lookup("select type from clan_types where id = $server_row[type]");
				$server = new phpQStat($server_row['qstat_type'], $server_row['address'], true, true);
				$servers_list[$server_row['id']] = $server->server_name;
			}
		}
		$set_row['select_server_menu'] = arraylistbox('input[select_server]', $servers_list, $row['select_server'], TRUE);
		
		$files_db = new DBDriver;
		$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
		
		$set_row['select_logo_menu'] = $files_db->sqllistbox('input[select_logo]',
			"SELECT id, filename FROM files WHERE clan_id = {$cfg['clan_id']} AND cat_id = 4", $row['select_logo'], TRUE, FALSE, 20);
		
		$result = $core_db->query(
			"SELECT display, tag, clan_type, info,
			DATE_FORMAT(create_date, '%d') as birth_day,
			DATE_FORMAT(create_date, '%m') as birth_month,
			DATE_FORMAT(create_date, '%Y') as birth_year
			FROM clans WHERE id = {$cfg['clan_id']}",
			"get clan info"
		);
		
		$row_clan = $core_db->fetch_row($result);
		
		$set_row['birth_day'] = $row_clan['birth_day'];
		$set_row['birth_month'] = $row_clan['birth_month'];
		$set_row['birth_year'] = $row_clan['birth_year'];
		$set_row['clan_info'] = cmscode($row_clan['info'], 'cmscode');
		
		$set_row['select_clan_name'] = $row_clan['display'];
		$set_row['select_clan_tag'] = $row_clan['tag'];
		$set_row['select_clan_type'] = sqllistbox('input[clan_type]', "SELECT id, description from clan_types WHERE type != 'gps'", $cfg['clan_type']);
		
		$scoring = array('individual' => $lang['individual'], 'total' => $lang['total']);
		$set_row['select_scoring'] = arraylistbox('input[scoring]', $scoring, $row['scoring']);
		
		$align = array('left' => $lang['left'], 'right' => $lang['right'], 'center' => $lang['center']);
		$set_row['select_logo_align'] = arraylistbox('input[logo_align]', $align, $row['logo_align']);
		
		$t->set_array($set_row, "", "", "");
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/* -----------------------------------------------
	| Settings UPDATE_PRIVATE
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "update_private":
		
		if (!$do) $do = "multi";
		
		if (!$core_db->lookup("select id from settings_private where user_id = {$cfg['user_id']}")) {
			
			foreach($private_vars as $var) {
				$sql_vars[$i++] = "$var = '{$cfg[$var]}'";
			}
			
			// Turns array into string.
			$sql_vars = implode(", \n", $sql_vars);
			
			$core_db->query(
				"INSERT settings_private SET
				user_id = '{$cfg['user_id']}',
				clan_id = '{$cfg['clan_id']}',
				$sql_vars",
				"make private settings"
			);
		}
		
		switch($do) {
			
			case "single":
				if ($input['sidebar']) {
					
					if ($input['minimize']) {
						
						$core_db->query(
							"update settings_private set
							$input[sidebar] = 2
							where user_id = {$cfg['user_id']} and clan_id = {$cfg['clan_id']}"
						, ("minimize sidebar"));
						
						header("Location:http:$ref");
						
					} elseif ($input['maximize']) {
						
						$core_db->query(
							"update settings_private set
							$input[sidebar] = 1
							where user_id = {$cfg['user_id']} and clan_id = {$cfg['clan_id']}"
						, ("close sidebar"));
						
						header("Location:http:$ref");
						
					} elseif ($input['close']) {
						
						$core_db->query(
							"update settings_private set
							$input[sidebar] = 0
							where user_id = {$cfg['user_id']} and clan_id = {$cfg['clan_id']}"
						, ("close sidebar"));
						
						header("Location:http:$ref");
						
					} else {
						
						header("Location:http:$ref");
					}
				}
				
			break;
			
			case "multi":
				
				foreach (array('left', 'right') as $side) {
					$i = 1;
					foreach ($_REQUEST['panel_orders_' . $side] as $panel) {
						$panels[$side][$i++] = $panel;
					}
					$input[$side . '_panels_list'] = addslashes(serialize($panels[$side]));
				}
				
				foreach($private_vars as $var) {
					$sql_vars[$i++] = "$var = '$input[$var]'";
				}
				
				// Turns array into string.
				$sql_vars = implode(", \n", $sql_vars);
				
				$core_db->query(
					"UPDATE settings_private SET
					$sql_vars
					WHERE user_id = {$cfg['user_id']}
					AND clan_id = {$cfg['clan_id']}",
					"update private settings"
				);
				
				add_log("Updated Private Settings (ID: {$cfg['user_id']})");
				
				header("Location:http:?mod=settings&action=private&msg=u");
				
			break;
		}
		
	break;
	
	/* -----------------------------------------------
	| Settings UPDATE_GLOBAL
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "update_global":
		
		foreach (array('left', 'right') as $side) {
			$i = 1;
			foreach ($_REQUEST['panel_orders_' . $side] as $panel) {
				$panels[$side][$i++] = $panel;
			}
			$input[$side . '_panels_list'] = addslashes(serialize($panels[$side]));
		}
		
		foreach ($global_vars as $var) {
			$sql_vars[] = "$var = '{$input[$var]}'";
		}
		
		// Turns array into string.
		$sql_vars = implode(", \n", $sql_vars);
		
		if (!$core_db->lookup("select id from settings_global where clan_id = {$cfg['clan_id']}")) {
			
			$core_db->query(
				"INSERT settings_global SET
				clan_id = '{$cfg['clan_id']}',
				$sql_vars",
				"make global settings"
			);
		}
		
		$core_db->query(
			"UPDATE settings_global SET
			$sql_vars
			WHERE clan_id = '{$cfg['clan_id']}'",
			"update global settings"
		);
		
		add_log("Updated Global Settings (ID: {$cfg['clan_id']})");
		
		$create_date = sql_date($input['birth_day'], $input['birth_month'], $input['birth_year']);
		
		$core_db->query(
			"UPDATE clans SET
			display = '$input[clan_name]',
			clan_type = '$input[clan_type]',
			tag = '$input[clan_tag]',
			info = '$input[info]',
			create_date = '$create_date'
			WHERE id = '{$cfg['clan_id']}'",
			"update clan info"
		);
		
		add_log("Updated Clan Settings (ID: {$cfg['clan_id']})");
		
		// If user wants these settings applied to them aswell...
		if ($apply_to_me) {
			
			// Start again.
			$sql_vars = "";
			
			// Use the GLOBAL config array to avoid blanks being filled in.
			foreach($global_vars as $var) {
				if (!in_array($var, array('scoring'))) {
					$sql_vars[$i++] = "$var = '$input[$var]'";
				}
			}
			
			// Turns array into string.
			$sql_vars = implode(", \n", $sql_vars);
			
			$core_db->query(
				"UPDATE settings_private SET
				$sql_vars
				WHERE user_id = {$cfg['user_id']}
				AND clan_id = {$cfg['clan_id']}",
				"update private settings"
			);
			
			add_log("Updated Private Settings (ID: {$cfg['user_id']})");
		}
		
		header("Location:http:?mod=settings&action=global&msg=u");
		
	break;
}