<?php

switch($mm->action) {
	
	case "default": redirect("&action=admin"); break;
	
	/*
	+ ------------------------------------------------
	| Comments ADMIN
	+ ------------------------------------------------
	| Shows list of comments according to an type_id
	| which can belong to several diffrent modules.
	+ ------------------------------------------------
	| Added: v3.7 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		$mm->ifempty($id, $HTTP_REFERER);
		$srt->set_array( array('comments', 'create_id; author', 'create_date; date', 'userlevel'), 'create_date', 'asc', 0, $cfg['browsers_limit']);
		
		$module_id = $core_db->lookup("SELECT id FROM modules WHERE name = '$type'");
		
		$result = $core_db->query(
			"SELECT comments.id, comments.create_id, comments.clan_id,
			comments.item_id, comments.comments, comments.userlevel,
			DATE_FORMAT(comments.create_date,'{$cfg['sql_date']['alt']}') as date
			FROM comments
			LEFT JOIN modules ON modules.name = '$type'
			WHERE comments.userlevel <= '{$cfg['userlevel']}'
			AND comments.clan_id = '{$cfg['clan_id']}'
			AND comments.item_id = '$id'
			AND comments.module_id = modules.id".
			$srt->sql(),
			"Get news comments."
		);
		
		// Used for multi tier modules (i.e. Reports)...
		if ($origin) {
			$cfg['origin_url'] = "&origin=$origin&origin_id=$origin_id";
			$cfg['origin_id'] = $origin_id;
			$cfg['origin'] = $origin;
		}
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->page_index(
				"select COUNT(*) from comments
				where userlevel <= $cfg[userlevel]
				and clan_id = {$cfg['clan_id']}
				and item_id = $id
				and module_id = $module_id"
			);
			
			$cfg['type_id'] = $id;
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
			while ($row = $core_db->fetch_row($result)) {
				$row['comments'] = substr_adv(strip_tags($row['comments']), 60, false);
				$row['userlevel'] = $core_db->lookup("select info from userlevels where id = $row[userlevel]");
				$row['creator'] = $core_db->lookup("select name from users where id = $row[create_id]");
				if($option) $row['option'] = $option;
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_comments_id', true, false, false, true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Comments CREATE
	+ ------------------------------------------------
	| Added: v3.7 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		$mm->ifempty($type, $HTTP_REFERER);
		$mm->ifempty($id, $HTTP_REFERER);
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$row['input[s_userlevel]'] = sqllistbox('input[s_userlevel]', "select id, info from userlevels");
		$row['select_avatar'] = yn('input[avatar]', $cfg['avatar_comments'], 'radio');
		$row['type'] = $type;
		$row['id'] = $id;
		
		include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
		
		if ($cfg['user_id'] == $cfg['pub_id']) {
			$t->set_block('subTemplate', 'hide_pub');
			$t->set_var('hide_pub', "");
		} else {
			// Hide the name field for members.
			$t->set_block('subTemplate', 'hide_name');
			$t->set_var('hide_name', "");
		}
		
		$t->set_array($row, "", "", "");
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Comments EDIT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		$mm->ifempty($id, $HTTP_REFERER);
		
		$result = $core_db->query(
			"SELECT id, comments, userlevel,
			item_id, avatar, name, create_id,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') AS date
			FROM comments
			WHERE id = '$id'
			AND clan_id = '{$cfg['clan_id']}'",
			"Edit news comment."
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			
			$row = cmscode($core_db->fetch_row($result), 'cmscode');
			
			$row['input[s_userlevel]'] = sqllistbox('input[s_userlevel]', "select id, info from userlevels", $row['userlevel'], false);
			$row['select_avatar'] = yn('input[avatar]', $row['avatar'], 'radio');
			$comments_nav = ($row['comments'] ? substr_adv($row['comments'], 20) : $lang['na']);
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
			if ($row['create_id'] == $cfg['pub_id']) {
				// Hide the name value for public.
				$row['hide_member'] = "";
				$row['hide_avatar'] = "";
			} else {
				// Hide the name field for members.
				$row['hide_name'] = "";
				$row['name'] = $core_db->lookup("SELECT name FROM users WHERE id = $row[create_id]", "get member name");
			}
			
			$t->set_array($row, "", "", "");
			
		} else {
			// Only show the JavaScript "Go Back!" link!
			$t->set_msg('no_comments_id', 1, 0, 0, 1);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Comments INSERT
	+ ------------------------------------------------
	| Added: v3.7 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "insert":
		
		$mm->ifempty($type, $HTTP_REFERER);
		$mm->ifempty($id, $HTTP_REFERER);
		$s_userlevel = $input['s_userlevel'] + 0;
		$comments = cmscode($input['comments'], 'cmscode');
		$avatar = ($input['avatar']);
		$module_id = $core_db->lookup("select id from modules where name = '$type'");
		if ($cfg['user_id'] == $cfg['pub_id']) $name = $input['name'];
		
		$core_db->query(
			"insert comments set
			comments = '$comments',
			name = '$name',
			userlevel = $s_userlevel,
			item_id = $id,
			avatar = '$avatar',
			module_id = $module_id,
			clan_id = {$cfg['clan_id']},
			create_id = {$cfg['user_id']},
			edit_id = {$cfg['user_id']},
			create_date = now(),
			edit_date = now()"
		, ("insert"));
		
		$comment_id = mysql_insert_id();
		add_log("Inserted Comment (ID: $comment_id)");
		go_back("msg=$mm->module_id;i", "?mod=comments&action=admin&type=$type&id=$comment_id#c$comment_id");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Comments UPDATE
	+ ------------------------------------------------
	| Added: v3.7 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "update":
		
		$mm->ifempty($id, $HTTP_REFERER);
		
		$s_userlevel = $input['s_userlevel'] + 0;
		$comments = cmscode($input['comments'], 'cmscode');
		$avatar = ($input['avatar']);
		$name = ($input['name']);
		
		$core_db->query(
			"update comments set
			comments = '$comments',
			name = '$name',
			avatar = '$avatar',
			userlevel = $s_userlevel,
			edit_date = now(),
			edit_id = {$cfg['user_id']}
			where id = $id and
			clan_id = {$cfg['clan_id']}"
		, ("update cmment"));
		
		add_log("Updated Comment (ID: $id)");
		go_back("msg=$mm->module_id;u", "?mod=comments&action=admin&type=$type&id=$id#c$id");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Comments DELETE
	+ ------------------------------------------------
	| Added: v3.7 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "delete":
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach($item as $id) {
				$id = $id + 0;
				$core_db->query(
					"DELETE FROM comments WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete comments."
				);
				add_log("Deleted Comment (ID: $id)");
				$i++;
			}
			
			// Deleted one, or several?
			if ($i == 1) {
				$msg = "ds";
			} elseif ($i > 1) {
				$msg = "d";
			}
		}
		
		go_back("msg=$mm->module_id;$msg", "?mod=comments&action=admin&type=$type&id=$id");
		
	break;
}