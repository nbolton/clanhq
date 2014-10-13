<?php

class result_controller {
	
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
					vs_id				= '{$_POST['clan_id']}',
					vs_tag			= '{$_POST['tag']}',
					vs_name			= '{$_POST['name']}',
					vs_url			= '{$_POST['url']}',
					match_date 	= '{$_POST['date']}',
					match_type 	= '{$_POST['type_id']}',
					clan_id 		= '{$cfg['clan_id']}',
					create_id		= '{$cfg['user_id']}',
					edit_id			= '{$cfg['user_id']}',
					create_date = now(),
					edit_date 	= now(),
					enabled			= 'Y'",
					"Insert match details."
				);
				
				$result_id = $core_db->get_insert_id();
				
			break;
			
			case "update":
				
				$core_db->query(
					"UPDATE fixtures SET
					vs_id				= '{$_POST['clan_id']}',
					vs_tag			= '{$_POST['tag']}',
					vs_name			= '{$_POST['name']}',
					vs_url			= '{$_POST['url']}',
					match_date 	= '{$_POST['date']}',
					match_type 	= '{$_POST['type_id']}',
					edit_id			= '{$cfg['user_id']}',
					edit_date 	= now(),
					enabled			= 'Y'
					WHERE id		= '{$_POST['id']}'
					AND clan_id	=	'{$cfg['clan_id']}'",
					"Insert match details."
				);
				
				$result_id = $_POST['id'];
				
				$core_db->query(
					"DELETE FROM scores WHERE fix_id = '$result_id' AND clan_id = '{$cfg['clan_id']}'",
					"Delete all existing scores that belong to this fixture."
				);
				
			break;
		}
		
		if ($_POST['map_name']) {
			foreach ($_POST['map_name'] as $k => $v) {
				if (($k += 0) && $v) {
					$core_db->query(
						"INSERT scores SET
						map = '$v',
						clan_id	= '{$cfg['clan_id']}',
						item_id	= '$k',
						fix_id = '$result_id'",
						"Insert new map."
					);
				}
			}
			
			if ($_POST['self_score']) {
				foreach ($_POST['self_score'] as $k => $v) {
					if (($v += 0) && ($k += 0)) {
						$core_db->query(
							"UPDATE scores SET h_score = '$v'
							WHERE clan_id = '{$cfg['clan_id']}'
							AND item_id = '$k'
							AND fix_id = '$result_id'",
							"Update the map with the self score."
						);
					}
				}
			}
			
			if ($_POST['enemy_score']) {
				foreach ($_POST['enemy_score'] as $k => $v) {
					if (($v += 0) && ($k += 0)) {
						$core_db->query(
							"UPDATE scores SET e_score = '$v'
							WHERE clan_id = '{$cfg['clan_id']}'
							AND item_id = '$k'
							AND fix_id = '$result_id'",
							"Update the map with the enemy score."
						);
					}
				}
			}
			
			if ($_POST['screenshot']) {
				foreach ($_POST['screenshot'] as $k => $v) {
					if (($v += 0) && ($k += 0)) {
						$core_db->query(
							"UPDATE scores SET ss_id = '$v'
							WHERE clan_id = '{$cfg['clan_id']}'
							AND item_id = '$k'
							AND fix_id = '$result_id'",
							"Update the map with an existing screenshot."
						);
					}
				}
			}
			
			if (count($_FILES['screenshot_upload']['name'])) {
				for ($k = 1; $k <= count($_FILES['screenshot_upload']['name']); $k++) {
					
					$tempfile = $_FILES["screenshot_upload"]['tmp_name'][$k];
					$filename = $_FILES["screenshot_upload"]['name'][$k];
					$filetype = $_FILES["screenshot_upload"]['type'][$k];
					$filesize = $_FILES["screenshot_upload"]['size'][$k] + 0;
					
					if (file_exists($tempfile)) {
						
						$files_db = new DBDriver;
						$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
						
						$files_db->query(
							"INSERT files SET
							data			=	'" . addslashes(fread(fopen($tempfile, "r"), $filesize)) . "',
							filename	=	'$filename',
							filesize	=	'$filesize',
							type			=	'$filetype',
							clan_id 	=	'{$cfg['clan_id']}',
							user_id	 	=	'{$cfg['user_id']}',
							edit_id 	=	'{$cfg['user_id']}',
							userlevel	=	1,
							cat_id 		=	6,
							upload_date	=	now(),
							edit_date 	=	now()",
							"Upload screenshot."
						);
						
						$file_id = $files_db->get_insert_id();
						add_log("Uploaded File (ID: $file_id)");
						
						$core_db->query (
							"UPDATE scores SET ss_id = '$file_id'
							WHERE clan_id = '{$cfg['clan_id']}'
							AND item_id = '$k'
							AND fix_id = '$result_id'",
							"Update the map with a new screenshot."
						);
					}
				}
			}
		}
		
		switch ($method) {
			
			case "insert":
				add_log("Inserted Result (ID: $result_id)");
				go_back("msg=i", "?mod=$mm->module&action=admin");
			break;
			
			case "update";
				add_log("Updated Result (ID: $result_id)");
				go_back("msg=u", "?mod=$mm->module&action=admin");
			break;
		}
	}
}

switch($mm->action) {
	
	case "default": redirect("&action=browse"); break;
	
	/*
	+ ------------------------------------------------
	| Results BROWSE
	+ ------------------------------------------------
	| Shows items from fixtures table, which have
	| "expired" as a fixture. Fixtures need to
	| be enabled (enabled = 'Y') : set in admin.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "browse":
		
		$srt->set_array( array('vs_tag; opponent', 'match_type; type', 'match_date; date'), 'match_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"select id, vs_id, vs_name, vs_tag, vs_url, match_type,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['full']}') as date
			from fixtures
			where clan_id = {$cfg['clan_id']}
			and (unix_timestamp(match_date) < unix_timestamp(now()))
			and is_enabled = 'Y'" . $srt->sql(),
			"get results from fixtures table"
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->navs[] = $lang["results"];
			
			$nav->page_index(
				"SELECT COUNT(*) FROM fixtures
				WHERE clan_id = {$cfg['clan_id']}
				AND (unix_timestamp(match_date) < unix_timestamp(now()))
				and is_enabled = 'Y'"
			);
			
			// Make a new results stats object.
			$rs = new ResultsStats;
			
			while ($row = $core_db->fetch_row($result)) {
				
				$rs->match_outcome($row['id']);
				
				$clan_inf = fetch_clan_name($row['vs_id'], $row['vs_tag'], $row['vs_name'], $row['vs_url']);
				
				if (!$clan_inf['id']) {
					// Non-CMS Clan...
					if ($clan_inf['url']) {
						$row['tag'] = "<a href='http://{$clan_inf['url']}' target=_blank>{$clan_inf['tag']}</a>";
					} else {
						$row['tag'] = $clan_inf['tag'];
					}
					$row['clan'] = $clan_inf['name'];
					
				} else {
					// Clan is CMS...
					$vs_tag = $clan_inf['tag'];
					$vs_name = $clan_inf['name'];
					$vs_prefix = $clan_inf['prefix'];
					$row['tag'] = "<a href='http://{$clan_inf['url']}' target='_blank'>{$clan_inf['tag']}</a>";
					$row['clan'] = $clan_inf['name'];
				}
				
				$row['match_type'] = $core_db->lookup("select name from match_types where (clan_id = 0 or clan_id = {$cfg['clan_id']}) and id = $row[match_type]");
				$row['outcome'] = $lang[$rs->outcome];
				$row['us_tag'] = $core_db->lookup("select tag from clans where id = {$cfg['clan_id']}");
				$row['enemy_tag'] = ( $row['vs_id'] ? $core_db->lookup("select tag from clans where id = $row[vs_id]") : $row['vs_tag'] );
				
				switch ($rs->outcome) {
					default: $row['score_style'] = "bodyText"; break;
					case "win": $row['score_style'] = "backgroundWin"; break;
					case "draw": $row['score_style'] = "backgroundDraw"; break;
					case "lose": $row['score_style'] = "backgroundLose"; break;
				}
				
				$row['scores'] = $rs->match_scores($row['id']);
				
				$num_reports = $core_db->lookup("SELECT COUNT(*) FROM reports WHERE item_id = $row[id]", "count number of reports");
				$row['num_reports'] = ( $num_reports ? " ($num_reports)" : "<!-- no reports -->");
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_results', true);
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
		
		$result = $core_db->query (
			"SELECT fixtures.id, fixtures.vs_id, fixtures.vs_name,
			fixtures.vs_tag, fixtures.vs_url, fixtures.match_type, fixtures.create_id,
			DATE_FORMAT(fixtures.match_date, '{$cfg['sql_date']['short']}') AS date,
			DATE_FORMAT(fixtures.match_date, '{$cfg['sql_date']['time']}') AS time,
			match_types.name AS match_type_name
			FROM fixtures
			LEFT JOIN match_types ON match_types.id = fixtures.match_type
			WHERE fixtures.clan_id = '{$cfg['clan_id']}'
			AND (unix_timestamp(fixtures.match_date) < unix_timestamp(now()))
			AND fixtures.enabled = 'Y'
			AND fixtures.id = $id",
			"Get details."
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'ResultsBlock');
			$row = cmscode($core_db->fetch_row($result), 'html');
			$t->set_msg_var();
			
			// Make a new results stats object.
			$rs = new ResultsStats;
			$outcome = $rs->match_outcome($row['id']);
			$scores = $rs->match_scores($row['id']);
			
			// Used later on in comments module!
			$cfg['type'] = $mm->module;
			$cfg['origin_id'] = $id;
			
			$clan = fetch_clan_name($row['vs_id'], $row['vs_tag'], $row['vs_name'], $row['vs_url']);
			$row['clan'] = $clan['name'];
			if ($clan['url']) {
				$row['tag'] = "<a href=http://'{$row['vs_url']}' target='_blank'>{$clan['tag']}</a>";
			} else {
				$row['tag'] = $clan['tag'];
			}
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['results']}</a>",
				"{$lang['details']} ({$clan['tag']} - {$row['match_type_name']})"
			);
			
			set_admin_links('admin_links',
				array(
					"$lang[edit]; ?mod=results&action=edit&id={$row['id']}",
					"$lang[delete]; ?mod=results&action=delete&id={$row['id']}; true; delete_item_q",
					"$lang[report]; ?mod=reports&action=create&id=$row[id]&type=$mm->module"
				)
			);
			
			$scores_result = $core_db->query(
				"select id, h_score, e_score, map, ss_id from scores where fix_id = $row[id] order by item_id asc",
				"get list of scores"
			);
			
			if ($core_db->get_num_rows($scores_result)) {
				$i = 0;
				while ($scores_row = $core_db->fetch_row($scores_result)) {
					$i++;
					
					$files_db = new DBDriver;
					$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
					
					if ($scores_row['ss_id'] && $files_db->lookup("select id from files where id = '$scores_row[ss_id]'")) {
						$screenshot = "- <a href=?mod=files&action=getimage&id=$scores_row[ss_id] target=_blank>Screenshot</a>";
					} else {
						$screenshot = "";
					}
					
					$scores_row['map_image'] = seek_file($scores_row['map'], "./images/maps/", array("jpg", "gif"), 1, "\nGame: {$cfg['clan_type_desc']}");
					
					$scores_row['map_image'] = 
						"<table border=1 cellspacing=0 cellpadding=5 class=tableDefault>".
						"<tr><td bordercolor=#000000 class=bodyText width=212>".
						"{lang_this_is_a_map_preview}".
						"<br><br><img src=$scores_row[map_image]>".
						"</td></tr></table>";
					
					$cfg['preloads'] .= $scores_row['map_image'];
					$scores_row['map'] = "<span onmouseover=\"return overlib('$scores_row[map_image]');\" onmouseout=\"return nd();\">$scores_row[map]</span>";
					
					$outcome = $rs->map_outcome[$row['id']][$i];
					$scores = $rs->map_score[$scores_row['id']]['h'] . ":" . $rs->map_score[$scores_row['id']]['e'];
					$row['scores'] .= "<tr><td class=titleText width=50>{lang_map} $i</td><td width=10></td>
					<td class=bodyText>$scores_row[map] - $scores - $lang[$outcome] $screenshot</td></tr>";
				}
			} else {
				$row['scores'] = " ";
			}
			
			$results_id = $row['id'];
			
			$row['match_type'] = $core_db->lookup("select name from match_types where (clan_id = 0 or clan_id = {$cfg['clan_id']}) and id = $row[match_type]");
			$create_name = "<a href=?mod=members&action=details&id=$row[create_id]>" . $core_db->lookup("select name from users where id = $row[create_id]") . "</a>";
			
			$t->set_array($row);
			
			$t->parse('ResultsBlock', 'ResultsBlock');
			
			$result = $core_db->query(
				"SELECT reports.id, reports.body, reports.create_id,
				reports.edit_id, reports.edit_note, reports.title,
				DATE_FORMAT(reports.create_date,'{$cfg['sql_date']['full']}') AS date,
				DATE_FORMAT(reports.edit_date,'{$cfg['sql_date']['full']}') AS edit_date,
				reports.avatar, users.avatar_id, users.name AS author
				FROM reports
				LEFT JOIN users ON users.id = reports.create_id
				WHERE reports.item_id = '{$row['id']}'
				AND reports.module_id = '$mm->module_id'
				AND reports.clan_id = '{$cfg['clan_id']}'
				ORDER BY reports.create_date ASC",
				"Get list of scores."
			);
			
			if ($core_db->get_num_rows()) {
				
				$t->set_block('subTemplate', 'ReportsBlock', 'RepBlock');
				
				while ($row = cmscode($core_db->fetch_row($result))) {
					
					$cmts->set_count($row['id'], 10);
					$num_comments = $cmts->num_comments;
					
					$updatename = "<a href=?mod=members&action=details&id=$row[edit_id]>" . $core_db->lookup("SELECT name FROM users WHERE id = '{$row['edit_id']}'") . '</a>';
					if ((($row['edit_date'] != $row['date']) || ($row['edit_id'] != $row['create_id']) ) && ($row['edit_note'] == 'Y')) {
						$row['edit'] = " - [ $lang[news_edit_by] <b>$updatename</b> - <b>$row[edit_date]</b> ]";
					} else {
						$row['edit'] = " ";
					}
					
					if ($t->check_privs(9, 'browse')) {
						$row['comment'] = " - [ <a href=?mod=reports&action=details&type=results&id=$row[id]&type_id=$id>Comments</a> ($num_comments) ]";
					}
					
					if ($row['author']) {
						// For previews, open link in new window.
						if ($preview) { $target = "target='_blank'"; }
						$row['author'] = "<a href='?mod=members&action=details&id={$row['create_id']}' $target>{$row['author']}</a>";
						
					} else {
						// The user has been deleted!
						$row['author'] = $lang['unknown_author'];
					}
					
					$avatar_id = $core_db->lookup("SELECT avatar_id FROM users WHERE id = $row[create_id]", "get avatar id");
					$row['avatar_id'] = ((($row['avatar'] == 'Y') && check_sql_file($avatar_id)) ? $avatar_id : "");
					$row['quote'] = get_quote($row['create_id']);
					
					$t->set_array($row);
					
					set_admin_links('reports_admin_links',
						array(
							"$lang[edit]; ?mod=reports&action=edit&id=$row[id]&type=$mm->module&type_id=$results_id",
							"$lang[delete]; ?mod=reports&action=delete&id=$row[id]&type=$mm->module; true; delete_item_q"
						)
					);
					
					$t->parse('RepBlock', 'ReportsBlock', true);
				}
			} else {
				$t->kill_block('AllReports', "", 'subTemplate');
			}
		} else {
			$t->set_msg('no_results_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Results ADMIN
	+ ------------------------------------------------
	| Shows results admin, results not yet completed
	| are marked with an "X".
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		$srt->set_array( array('vs_tag; tag', 'vs_name; clan', '', 'match_date; date', 'time'), 'match_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"select id, vs_id, vs_name, vs_tag, match_type, enabled,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['short']}') as date,
			DATE_FORMAT(match_date, '{$cfg['sql_date']['time']}') as time
			from fixtures
			where clan_id = {$cfg['clan_id']}
			and (unix_timestamp(match_date) <= unix_timestamp(now()))" . $srt->sql()
		, ("get fixtures"));
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'ReportLinks');
			$t->set_msg_var();
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$nav->page_index(
				"select COUNT(*) from fixtures
				where clan_id = {$cfg['clan_id']}
				and (unix_timestamp(match_date) <= unix_timestamp(now()))"
			);
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['results']}</a>",
				$lang['admin']
			);
			
			while ($row = $core_db->fetch_row($result)) {
				$row['vstag'] = false;
				if ($row['vs_id'] == 0) {
					$row['vstag'] = $row['vs_tag'];
					$row['vsclan'] = $row['vs_name'];
				} else {
					$row['vstag'] = $core_db->lookup("select tag from clans where id = $row[vs_id]");
					$row['vsclan'] = $core_db->lookup("select display from clans where id  = $row[vs_id]");
				}
				$row['hide_imark'] = ($row['enabled'] == 'Y') ? "<!--" : "<!-- Incomplete -->";
				$row['num_reports'] = $core_db->lookup("select COUNT(*) from reports where item_id = $row[id] and module_id = $mm->module_id");
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_results', true, true);
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	case "create":
		
		/* --------------------------------------------- +
		| Results > CREATE
		+ ---------------------------------------------- +
		| - Looks-up a list of Clan Names & IDs from the
		|   'clans' table so the user can use a clan who
		|		also uses CMS.
		| - Looks-up a list of Match Types either assigned
		|   to that clan, or specific to their game type.
		| - Selects a list of existing screenshots incase
		|		the user uploaded the screenshot beforehand.
		| - Sets the default date in the past to reduce
		|		risk of user creating a result in the future.
		+ ---------------------------------------------- +
		| Introduced: 3.0 Beta
	 	| Modified: 4.3.8 (Monday 15 Dec 2003)
		+ --------------------------------------------- */
		
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang['results']}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			$lang['create']
		);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate');
		
		$t->set_file('SelectClanBlock', '../../../source/templates/select_clan_block.ihtml');
		$t->set_block('SelectClanBlock');
		
		$t->set_file('ScoreController', '../../../source/templates/score_ctrl_block.ihtml');
		$t->set_block('ScoreController');
		
		$t->set_var('segment_count', 2);
		$t->set_var('score_count', 1);
		$t->set_var('delete_parameters', "'{score_id}', 'disable'");
		$t->set_var('score_id', "%scoreID%");
		
		$t->set_var('self_tag', $core_db->lookup(
			"SELECT tag FROM clans WHERE id = '{$cfg['clan_id']}'"
		));
		
		$t->set_var('clan_id_select', $core_db->sqllistbox(
			'clan_id', "SELECT id, display FROM clans WHERE id != '{$cfg['clan_id']}'",
			"", "{lang_Custom}...", "", "", "", " onChange='checkShowCustom()'"
		));
		
		$t->set_var('type_id_select', $core_db->sqllistbox(
			'type_id',
			"SELECT id, name FROM match_types
			WHERE (clan_id = '{$cfg['clan_id']}' OR clan_id = 0)
			AND (game_id = '{$cfg['clan_type']}' OR game_id = 0)"
		));
		
		$files_db = new DBDriver;
		$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
		
		$t->set_var('screenshot', $files_db->sqllistbox(
			'screenshot[%scoreID%]',
			"SELECT id, filename FROM files
			WHERE clan_id = '{$cfg['clan_id']}'
			AND cat_id = '6'", "", TRUE, "", 20
		));
		
		$t->set_var('day', date('d'));
		$t->set_var('month', monthlistbox('month', date('n')));
		$t->set_var('year', date('Y'));
		$t->set_var('hour', leading_zero(floor(date('H')) - 1, 2));
		$t->set_var('minute', "00");
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	case "edit":
		
		/* --------------------------------------------- +
		| Results > EDIT
		+ ---------------------------------------------- +
		| - Brings back all information on the result
		|   which maches the ID specified.
		| - List of scores is constructed from a template
		|   used in the CREATE action.
		| - The scores table can be appended and modified
		|   as nessecary by the JavaScript handler on the
		|   returned page.
		+ ---------------------------------------------- +
		| Introduced: 3.0 Beta
	 	| Modified: 4.4.0 (Monday 15 Dec 2003)
		+ --------------------------------------------- */
		
		$id = $_GET['id'] + 0;
		$mm->ifempty($id);
		
		$result = $core_db->query(
			"SELECT fixtures.id, fixtures.vs_id, fixtures.vs_name AS name,
			fixtures.vs_tag AS tag, fixtures.vs_url AS url, fixtures.match_date,
			fixtures.match_type AS type, match_types.name AS match_type_name,
			DATE_FORMAT(fixtures.match_date, '%d') as day,
			DATE_FORMAT(fixtures.match_date, '%m') as month,
			DATE_FORMAT(fixtures.match_date, '%Y') as year,
			DATE_FORMAT(fixtures.match_date, '%H') as hour,
			DATE_FORMAT(fixtures.match_date, '%i') as minute,
			clans.display AS clan_name, clans.tag AS clan_tag
			FROM fixtures
			LEFT JOIN match_types ON match_types.id = fixtures.match_type
			LEFT JOIN clans ON clans.id = fixtures.vs_id
			WHERE fixtures.id = '{$_GET['id']}'
			AND fixtures.clan_id = '{$cfg['clan_id']}'",
			"Load current values for the result."
		);
		
		if ($core_db->get_num_rows($result)) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate');
			
			$t->set_file('SelectClanBlock', '../../../source/templates/select_clan_block.ihtml');
			$t->set_block('SelectClanBlock');
			
			$row = cmscode($core_db->fetch_row($result), 'cmscode');
			
			$scores = $core_db->query(
				"SELECT map AS map_name, h_score AS self_score,
				e_score AS enemy_score, ss_id AS screenshot
				FROM scores WHERE fix_id = '{$row['id']}'
				ORDER BY item_id ASC",
				"Get maps."
			);
			
			if (!$row['vs_id']) {
				$row['clan_name'] = $row['name'];
				$row['clan_tag'] = $row['tag'];
			}
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
			$t->set_var($row);
			
			$t->set_file('ScoreController', $_SERVER['DOCUMENT_ROOT'] . '/source/templates/score_ctrl_block.ihtml');
			$t->set_block('ScoreController', 'ScoresRow', 'ScoresBlockTemp');
			
			if ($core_db->get_num_rows($scores)) {
				
				$i = 1;
				
				$files_db = new DBDriver;
				$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
				
				while ($scores_row = $core_db->fetch_row($scores)) {
					
					$scores_row['screenshot'] = $files_db->sqllistbox(
						"screenshot[{score_id}]",
						"SELECT id, filename FROM files
						WHERE clan_id = '{$cfg['clan_id']}'
						AND cat_id = '6'", "", TRUE, "", 20
					);
					
					$scores_row['delete_parameters'] = "'{score_id}', 'disable', '{lang_restore_scores_tip}'";
					$scores_row['score_id'] = $i++;
					
					$t->set_var($scores_row);
					$t->parse('ScoresBlock', 'ScoresRow', TRUE);
				}
			}
			
			$t->set_var(array('score_id' => '%scoreID%', 'map_name' => '', 'self_score' => '', 'enemy_score' => ''));
			$t->set_var('delete_parameters', "'%scoreID%', 'disable', '{lang_restore_scores_tip}'");
			$t->set_var('screenshot', $files_db->sqllistbox(
				'screenshot[%scoreID%]',
				"SELECT id, filename FROM files
				WHERE clan_id = '{$cfg['clan_id']}'
				AND cat_id = '6'", $row['screenshot'], TRUE, "", 20
			));
			
			$t->parse('ScoresBlockTemp', 'ScoresRow');
			
			$t->set_var('score_count', 2);
			$t->set_var('segment_count', $core_db->get_num_rows($scores));
			
			$t->set_var('self_tag', $core_db->lookup(
				"SELECT tag FROM clans WHERE id = '{$cfg['clan_id']}'"
			));
			
			$t->set_var('enemy_tag', ($row['vs_id'] ? $core_db->lookup("SELECT tag FROM clans WHERE id = '{$row['vs_id']}'") : $row['vs_tag']));
			
			$t->set_var('screenshot_select', $files_db->sqllistbox(
				'screenshot_select', "SELECT id, filename FROM files
				WHERE clan_id = '{$cfg['clan_id']}'
				AND cat_id = '6'", $row['ss_id'], true
			));
			
			$t->set_var('type_id_select', sqllistbox(
				'type_id', "SELECT id, name FROM match_types
				WHERE (clan_id = '{$cfg['clan_id']}' OR clan_id = 0)
				AND (game_id = '{$cfg['clan_type']}' OR game_id = 0)",
				$row['type_id']
			));
			
			$t->set_var('clan_id_select', $core_db->sqllistbox(
				'clan_id', "SELECT id, display FROM clans WHERE id != '{$cfg['clan_id']}'",
				$row['vs_id'], "{lang_Custom}...", "", "", "", " onChange='checkShowCustom()'"
			));
			
			$t->set_var('month', monthlistbox('month', $row['month']));
			
		} else {
			$t->set_msg('no_results_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	case "insert":
		
		$result = new result_controller;
		$result->modify('insert');
		
	break;
	
	case "update":
		
		$result = new result_controller;
		$result->modify('update');
		
	break;
	
	case "delete":
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach($item as $key => $id) {
				$id = $id + 0;
				$core_db->query(
					"DELETE FROM fixtures
					WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete result."
				);
				$core_db->query(
					"DELETE FROM scores
					WHERE fix_id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete scores & maps."
				);
				add_log("Delete Result (ID: $id)");
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