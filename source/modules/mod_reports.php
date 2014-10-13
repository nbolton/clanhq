<?php

switch($mm->action) {
	
	case "default": redirect("&action=admin"); break;
	
	/*
	+ ------------------------------------------------
	| Report DETAILS
	+ ------------------------------------------------
	| Shows detauls of report, including comments.
	+ ------------------------------------------------
	| Added: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "details":
		
		// Stops crackers from causing a MySQL error.
		$supported = array(
			"results"
		);
		
		// Check we have everything we need, and that it is valid...
		$mm->ifempty((!$id || !in_array($type, $supported) || !$type_id) ? FALSE : TRUE, "?");
		
		/*
			Since the reports module is a snap-on module
			this means it needs information from the module
			which has called it.
		*/
		switch($type) {
			
			case "results":
				
				// Set the variables for this module...
				$type_table = "fixtures";
				$type_select = array(
					"vs_id", "vs_tag", "match_type",
					"match_types.name AS match_type_name"
				);
				
				// Make it SQL valid.
				foreach($type_select as $k => $v) {
					
					// Check no table defined!
					if (!strstr($v, ".")) {
						$type_select[$k] = "$type_table.$v";
					}
				}
				
				$type_select = implode(", ", $type_select);
				$type_extra_joins = "LEFT JOIN match_types ON match_types.id = fixtures.match_type";
				
			break;
		}
		
		$result = $core_db->query(
			"SELECT $type_select, reports.id, reports.body, reports.create_id,
			reports.edit_id, reports.edit_note, reports.title,
			DATE_FORMAT(reports.create_date,'{$cfg['sql_date']['full']}') AS date, avatar,
			DATE_FORMAT(reports.edit_date,'{$cfg['sql_date']['full']}') AS edit_date
			FROM reports
			LEFT JOIN modules ON modules.name = '$type'
			LEFT JOIN $type_table ON $type_table.id = '$type_id'
			$type_extra_joins
			WHERE reports.userlevel <= '{$cfg['userlevel']}'
			AND reports.module_id = modules.id
			AND reports.clan_id = '{$cfg['clan_id']}'
			AND reports.id = '$id'",
			"Get report."
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'ReportsBlock');
			$t->set_msg_var();
			
			$row = cmscode($core_db->fetch_row($result), 'html');
			$row['edit_name'] = $core_db->lookup("select name from users where id = $row[edit_id]");
			if ((($row['edit_date'] != $row['date']) || ($row['edit_id'] != $row['create_id'])) && ($row['edit_note'] == 'Y')) {
				$row['edit'] = " - [ $lang[news_edit_by] <b><a href=?mod=members&action=details&id=$row[edit_id]>$row[edit_name]</a></b> - <b>$row[edit_date]</b> ]"; }
			
			switch($type) {
				case "results":
					$clan = fetch_clan_name($row['vs_id'], $row['vs_tag'], $row['vs_name']);
					$type_info = "{$clan['tag']} - {$row['match_type_name']}";
				break;
			}
			
			$report_title = ($row['title'] ? substr_adv($row['title'], 20) : substr_adv($row['body'], 20));
			
			$nav->navs[] = array(
				"<a href='?mod=$type'>{$lang[$type]}</a>",
				"<a href='?mod=$type&action=details&id=$type_id'>{$lang['details']}</a> ($type_info)",
				"{$lang['report']} ($report_title)"
			);
			
			// Used later on in comments module!
			$cfg['origin_id'] = $type_id;
			
			$row['post_comment'] = ($t->check_privs(9, 'create') && !$preview) ? TRUE : FALSE;
			$row['author'] = $core_db->lookup("select name from users where id = $row[create_id]");
			$avatar_id = $core_db->lookup("select avatar_id from users where id = $row[create_id]");
			$row['avatar_id'] = ((($row['avatar'] == 'Y') && check_sql_file($avatar_id)) ? $avatar_id : FALSE);
			$row['quote'] = get_quote($row['create_id']);
			
			$t->set_array($row);
			set_admin_links('admin_links',
				array(
					"$lang[edit]; ?mod=$mm->module&action=edit&id=$row[id]&type=$type&type_id=$type_id",
					"$lang[delete]; ?mod=$mm->module&action=delete&id={$row['id']}; true; delete_item_q"
				)
			);
			
			$t->parse('ReportsBlock', 'ReportsBlock');
			
			if (!$t->check_privs(9, 'create') || $preview) $t->kill_block($var, 'post_comment', "subTemplate");
			
			if ($cmts->num_comments) {
				$cmts->set_comments($row['id']);
			} else {
				$t->kill_block('AllComments', "", 'subTemplate');
			}
			
		} else {
			// Only show the JavaScript "Go Back!" link!
			$t->set_msg('no_reports_id', 1, 0, 0, 1);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| REPORTS ADMIN
	+ ------------------------------------------------
	| Shows list of reports with links to
	| corresponding comments.
	+ ------------------------------------------------
	| Added: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		$mm->ifempty($id, $HTTP_REFERER);
		$srt->set_array( array('title', 'create_id; author', 'create_date; date', 'userlevel'), 'create_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"select id, create_id, clan_id, item_id, userlevel, title,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') as date
			from reports
			where userlevel <= $cfg[userlevel]
			and clan_id = {$cfg['clan_id']}
			and item_id = $id" . $srt->sql()
		, ("get reports") );
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->page_index(
				"select COUNT(*) from reports
				where userlevel <= $cfg[userlevel]
				and clan_id = {$cfg['clan_id']}
				and item_id = $id"
			);
			
			// Needed for PathFinder
			$cfg['type_id'] = $id;
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
			while ($row = $core_db->fetch_row($result)) {
				$row['body'] = substr_adv(strip_tags($row['body']), 40, false);
				$row['userlevel'] = $core_db->lookup("select info from userlevels where id = $row[userlevel]");
				$row['creator'] = $core_db->lookup("select name from users where id = $row[create_id]");
				$row['num_comments'] = $core_db->lookup("select COUNT(*) from comments where item_id = '{$row['id']}' and module_id = $mm->module_id and clan_id = {$cfg['clan_id']}");
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_reports_id', true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| CREATE REPORT
	+ ------------------------------------------------
	| Added: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
		$mm->ifempty(($type && $id) ? TRUE : FALSE, $HTTP_REFERER);
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$row['s_userlevel'] = sqllistbox('input[s_userlevel]', "select id, info from userlevels");
		$row['select_avatar'] = yn('input[avatar]', $cfg['avatar_reports'], 'radio');
		$row['type'] = $type;
		$row['id'] = $id;
		$t->set_array($row, "", "", "");
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| EDIT REPORT
	+ ------------------------------------------------
	| Added: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		$mm->ifempty($id, $HTTP_REFERER);
		$type = ($type);
		
		$result = $core_db->query(
			"select id, body, userlevel, item_id, title, avatar,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') as date
			from reports
			where id = $id
			and clan_id = {$cfg['clan_id']}",
			"edit report"
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			$row = $core_db->fetch_row($result);
			$row = cmscode($row, 'cmscode');
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
			$row['s_userlevel'] = sqllistbox('input[s_userlevel]', "select id, info from userlevels", $row['userlevel']);
			$item_id = $core_db->lookup("select item_id from reports where id = $id");
			$row['select_avatar'] = yn('input[avatar]', $row['avatar'], 'radio');
			$t->set_array($row, "", "", "");
			
		} else {
			// Only show the JavaScript "Go Back!" link!
			$t->set_msg('no_reports_id', 1, 0, 0, 1);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| INSERT REPORT
	+ ------------------------------------------------
	| Added: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "insert":
		
		$mm->ifempty($type, $HTTP_REFERER);
		$mm->ifempty($id, $HTTP_REFERER);
		$type = ($type);
		$avatar = ($input['avatar']);
		$title = strip_tags(($input['title']));
		$s_userlevel = $input['s_userlevel'] + 0;
		$body = cmscode($input['body'], 'cmscode');
		$module_id = $core_db->lookup("select id from modules where name = '$type'");
		
		$core_db->query(
			"insert reports set
			body = '$body',
			title = '$title',
			userlevel = $s_userlevel,
			avatar = '$avatar',
			item_id = $id,
			module_id = $module_id,
			clan_id = {$cfg['clan_id']},
			create_id = {$cfg['user_id']},
			edit_id = {$cfg['user_id']},
			create_date = now(),
			edit_date = now()"
		, ("insert"));
		
		$report_id = mysql_insert_id();
		add_log("Inserted Report (ID: $report_id)");
		go_back("msg=$mm->module_id;i", "?mod=$mm->module&action=admin&type=$type&id=$id");
		
	break;
	
	/*
	+ ------------------------------------------------
	| UPDATE REPORT
	+ ------------------------------------------------
	| Added: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "update":
		
		$mm->ifempty($id, $HTTP_REFERER);
		
		$s_userlevel = $input['s_userlevel'] + 0;
		$body = cmscode($input['body'], 'cmscode');
		$title = strip_tags(($input['title']));
		$avatar = ($input['avatar']);
		
		$core_db->query(
			"update reports set
			body = '$body',
			title = '$title',
			avatar = '$avatar',
			userlevel = $s_userlevel,
			clan_id = {$cfg['clan_id']},
			edit_date = now(),
			edit_id = {$cfg['user_id']}
			where id = $id"
		, ("update report"));
		
		add_log("Updated Report (ID: $id)");
		go_back("msg=$mm->module_id;u", "?mod=$mm->module&action=admin&type=$type&id=$id");
		
	break;
	
	/*
	+ ------------------------------------------------
	| DELETE REPORT
	+ ------------------------------------------------
	| Added: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "delete":
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach($item as $key => $id) {
				$id = $id + 0;
				$core_db->query(
					"DELETE FROM reports
					WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete."
				);
				add_log("Deleted Report (ID: $id)");
				$i++;
			}
			
			// Deleted one, or several?
			if ($i == 1) {
				$msg = "ds";
			} elseif ($i > 1) {
				$msg = "d";
			}
		}
		
		go_back("msg=$mm->module_id;$msg", "?mod=$mm->module&action=admin&type=$type&id=$id");
		
	break;
}