<?php

switch($mm->action) {
	
	/*
	+ ------------------------------------------------
	| News DEFAULT
	+ ------------------------------------------------
	| Shows archive of news.
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	case "default":
	case "browse":
		
		$srt->set_array( array('title; title', 'author', 'create_date; date', 'views', 'comments_count; comments'), 'create_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT news.id, news.title, news.body, news.edit_note,
			news.create_id, news.edit_id, news.views, news.create_date,
			DATE_FORMAT(news.create_date,'{$cfg['sql_date']['short']}') AS date,
			COUNT(comments.id) AS comments_count, users.name AS author FROM news
			
			LEFT JOIN comments
			ON comments.item_id = news.id
			AND comments.module_id = '$mm->module_id'
			AND comments.clan_id = '{$cfg['clan_id']}'
			AND comments.userlevel <= '{$cfg['userlevel']}'
			
			LEFT JOIN users
			ON users.id = news.create_id
			
			WHERE news.userlevel <= '{$cfg['userlevel']}'
			AND news.clan_id = '{$cfg['clan_id']}'
			GROUP BY news.id" . $srt->sql(),
			"Get news"
		);
		
		if ($core_db->get_num_rows()) {
			
			$nav->navs[] = $lang["news"];
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->page_index("SELECT COUNT(*) FROM news WHERE news.userlevel <= '{$cfg['userlevel']}' AND news.clan_id = '{$cfg['clan_id']}'");
			
			while ($row = $core_db->fetch_row($result)) {
				$row['title'] = substr_adv($row['title'], 20);
				$cmts->set_count($row['id']);
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_news', true);
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| News DETAILS
	+ ------------------------------------------------
	| Shows a full view of the news including comments
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "details":
	
		$id = request_int("id");
		
		$result = $core_db->query(
			"SELECT news.id, news.title, news.body, news.edit_note,
			news.create_id, news.edit_id, news.avatar,
			DATE_FORMAT(news.create_date,'{$cfg['sql_date']['full']}') AS create_date,
			DATE_FORMAT(news.edit_date,'{$cfg['sql_date']['full']}') AS edit_date,
			users.name AS author, users.avatar_id, users.quote
			FROM news
			LEFT JOIN users ON users.id = news.create_id
			WHERE news.userlevel <= '{$cfg['userlevel']}'
			AND news.clan_id = '{$cfg['clan_id']}'
			AND news.id = '$id'",
			"Get news which matches ID."
		);
		
		if ($core_db->get_num_rows($result)) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'NewsBlock');
			$t->set_msg_var();
			
			$row = $core_db->fetch_row($result);
			
			$title = ($row['title'] ? substr_adv($row['title'], 20) : substr_adv($row['body'], 20));
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['news']}</a>",
				"{$lang['details']} ({$title})"
			);
			
			$row = cmscode($row, 'html');
			
			// Increment the view counter.
			$core_db->query(
				"UPDATE news SET views = views + 1
				WHERE id = {$row['id']}",
				"Increment view counter."
			);
			
			if (($row['edit_id'] != $row['create_id']) || ($row['edit_date'] != $row['create_date']) && ($row['edit_note'] == 'Y')) {
				
				$row['edit_name'] = $core_db->lookup(
					"SELECT name FROM users
					WHERE id = '{$row['edit_id']}'",
					"Lookup edit username."
				);
				
				// For previews, open link in new window.
				if ($preview) { $target = "target='_blank'"; }
				
				$row['edit'] = " - [ {$lang['news_edit_by']} <b>".
					"<a href=?mod=members&action=details&id={$row['edit_id']}>".
					"{$row['edit_name']}</a></b> - <b>{$row['edit_date']}</b> ]";
			}
			
			$row['avatar_id'] = ((($row['avatar'] == 'Y') && check_sql_file($row['avatar_id'])) ? $row['avatar_id'] : FALSE);
			$row['post_comment'] = ($t->check_privs(9, 'create') && !$preview) ? TRUE : FALSE;
			
			if ($row['author']) {
				if ($preview) {
					$target = "target='_blank'";
				} else {
					$target = "";
				}
				
				// For previews, open link in new window.
				$row['author'] = "<a href='?mod=members&action=details&id={$row['create_id']}' $target>{$row['author']}</a>";
				
			} else {
				// The user has been deleted!
				$row['author'] = $lang['unknown_author'];
			}
			
			$row['quote'] = format_quote($row['create_id'], $row['quote']);
			
			$t->set_array($row);
			
			set_admin_links('admin_links',
				array(
					"$lang[edit]; ?mod=news&action=edit&id={$row['id']}",
					"$lang[delete]; ?mod=news&action=delete&id={$row['id']}; true; delete_item_q"
				)
			);
			
			$t->parse('NewsBlock', 'NewsBlock');
			
			/*
				Don't show post comment button if in preview
				mode or if the user can't post comments anyway.
			*/
			if (!$t->check_privs(9, 'create') || $preview) {
				$var = ""; // No idea what $var is for.
				$t->kill_block($var, 'post_comment', "subTemplate");
			}
			
			if ($cmts->num_comments) {
				$cmts->set_comments($row['id']);
			} else {
				$t->kill_block('AllComments', "", 'subTemplate');
			}
			
		} else {
			$t->set_msg('no_news_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| News ADMIN
	+ ------------------------------------------------
	| Shows list of news with admin functions.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
	
		$srt->set_array( array('title; title', 'author', 'create_date; date', 'comments_count; comments'), 'create_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT news.id, news.title, news.body, news.create_id, news.userlevel, news.create_date,
			DATE_FORMAT(news.create_date,'{$cfg['sql_date']['short']}') AS date, COUNT(comments.id) AS comments_count,
			users.name AS author FROM news
			
			LEFT JOIN comments
			ON comments.item_id = news.id
			AND comments.module_id = '$mm->module_id'
			AND comments.clan_id = '{$cfg['clan_id']}'
			
			LEFT JOIN users
			ON users.id = news.create_id
			
			WHERE news.userlevel <= '{$cfg['userlevel']}'
			AND news.clan_id = '{$cfg['clan_id']}'
			GROUP BY news.id" . $srt->sql(),
			"Get news"
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$nav->page_index("select COUNT(*) from news where userlevel <= $cfg[userlevel] and clan_id = {$cfg['clan_id']}");
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['news']}</a>",
				$lang['admin']
			);
			
			while ($row = $core_db->fetch_row($result)) {
				
				if ($row['author']) {
					$row['author'] = "<a href='?mod=members&action=details&id={$row['create_id']}'>{$row['author']}</a>";
				}
				
				$row["levelinfo"] = $cfg["userlevels"][$row["userlevel"]];
				$row['title'] = substr_adv($row['title'], 25);
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_news', true, true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| News CREATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang['news']}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			$lang['create']
		);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		
		$row['select_userlevel'] = arraylistbox('input[userlevel]', $cfg["userlevels"]);
		$row['select_avatar'] = yn('input[avatar]', $cfg['avatar_news'], 'radio');
		
		$t->set_array($row, "", "", "");
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| News EDIT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		$mm->ifempty($id);
		
		$result = $core_db->query(
			"select id, title, body, create_id, userlevel, edit_note,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') as date, avatar
			from news
			where id = $id",
			"edit news"
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			$row = $core_db->fetch_row($result);
			$row = cmscode($row, 'cmscode');
			$row['select_userlevel'] = sqllistbox('input[userlevel]', "select id, info from userlevels", $row['userlevel']);
			$row['title'] = stripslashes($row['title']);
			$row['select_edit_note'] = yn('input[edit_note]', $row['edit_note'], 'radio');
			$row['select_avatar'] = yn('input[avatar]', $row['avatar'], 'radio');
			if ($row['create_id']) $row['creator'] = $core_db->lookup("select name from users where id = $row[create_id]");
			$t->set_array($row, "", "", "");
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
		} else {
			$t->set_msg('no_news_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| News INSERT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "insert":
		
		$userlevel = $input['userlevel'] + 0;
		$title = $input['title'];
		$avatar = $input['avatar'];
		$body = cmscode($input['body'], 'cmscode');
		
		$core_db->query(
			"insert news set
			userlevel = $userlevel,
			clan_id = {$cfg['clan_id']},
			create_id = {$cfg['user_id']},
			edit_id = {$cfg['user_id']},
			avatar = '$avatar',
			create_date = now(),
			edit_date = now(),
			title = '$title',
			body = '$body',
			edit_note = 'N'",
			"insert"
		);
		
		$id = mysql_insert_id();
		add_log("Inserted News (ID: $id)");
		go_back("msg=i", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| News UPDATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "update":
		
		$mm->ifempty($id, $HTTP_REFERER);
		$userlevel = $input['userlevel'] + 0;
		$title = $input['title'];
		$avatar = $input['avatar'];
		$edit_note = $input['edit_note'];
		$body = cmscode($input['body'], 'cmscode');
		
		$core_db->query(
			"update news set
			userlevel = $userlevel,
			clan_id = {$cfg['clan_id']},
			edit_id = {$cfg['user_id']},
			avatar = '$avatar',
			edit_date = now(),
			title = '$title',
			body = '$body',
			edit_note = '$edit_note',
			edit_date = now(),
			edit_id = {$cfg['user_id']}
			where id = $id and clan_id = {$cfg['clan_id']}"
		, ("update") );
		
		add_log("Updated News (ID: $id)");
		go_back("msg=u", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| News DELETE
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
					"DELETE FROM news WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'", 
					"Delete posts."
				);
				
				$core_db->query(
					"DELETE FROM comments WHERE item_id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete comments."
				);
				
				add_log("Deleted News (ID: $id)");
				add_log("Deleted News (ID: $id) - All Comments");
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