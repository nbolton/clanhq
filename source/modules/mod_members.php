<?php

switch($mm->action) {
	
	case "default": redirect("&action=browse"); break;
	
	/*
	+ ------------------------------------------------
	| Members BROWSE
	+ ------------------------------------------------
	| Shows users from the current clan; also shows
	| CMS online/offline status.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "browse":
		
		$result = $core_db->query(
			"SELECT id FROM users
			WHERE clan_id = '{$cfg['clan_id']}'
			AND id != $cfg[pub_id]",
			"Get members."
		);
		
		if ($core_db->get_num_rows()) {
			
			$nav->navs[] = $lang["members"];
			$t->set_file('subTemplate');
			
			function memblock($parent_handle, $child_handle, $child_name, $core_db_enum, $core_db_info) {
				
				global $t, $cfg, $lang, $core_db;
				
				$result = $core_db->query(
					"SELECT id, name, rank_id, flag_id
					FROM users
					WHERE clan_id = '{$cfg['clan_id']}'
					AND id != $cfg[pub_id]
					AND activity = '$core_db_enum'
					ORDER BY clan_ord ASC",
					$core_db_info
				);
				
				$t->set_block('subTemplate', $parent_handle);
				if ($core_db->get_num_rows()) {
					$t->set_block($parent_handle, $child_handle, $child_name);
					while ($row = $core_db->fetch_row($result)) {
						$online_time = (time() - $cfg['online_since']);
						$is_online = $core_db->lookup("select COUNT(*) from users where unix_timestamp(lastaction) > $online_time and logged_in = 'Y' and id = $row[id]");
						$row['status'] = "<span class=". ( $is_online ? "bodyGood>$lang[online]" : "bodyError>$lang[offline]") . "</span>";
						$row['rank'] = $core_db->lookup("select name from ranks where id = '$row[rank_id]'");
						if ($row['flag_id']) {
							$row['flag'] = "<img src=images/flags/$row[flag_id].gif>";
						} else {
							$row['flag'] = " ";
						}
						
						if ($cfg['user_id'] != $cfg['pub_id']) {
							
							$is_buddy = ($core_db->lookup("select id from buddy_list where buddy_id = $row[id] and user_id = {$cfg['user_id']}"));
							
							if (!$is_buddy) {
								$row['buddy_title'] = $lang['add_buddy'] . " ($row[name])";
								$row['buddy'] = "<a href=?mod=buddy&action=add&id=$row[id]><img src=themes/{theme_id}/images/icon_add.gif ".
									"width=10 height=8 border=0 alt=\"$row[buddy_title]\"></a>";
							} else {
								$row['buddy_title'] = $lang['delete_buddy'] . " ($row[name])";
								$row['buddy'] = "<a href=?mod=buddy&action=delete&id=$row[id]><img src=themes/{theme_id}/images/icon_delete.gif ".
									"width=9 height=8 border=0 alt=\"$row[buddy_title]\"></a>";
							}
							
						} else {
							$row['buddy'] = " ";
							$row['buddy_title'] = " ";
						}
						$t->set_array($row);
						$t->parse($child_name, $child_handle, TRUE);
					}
				} else {
					$t->set_var($parent_handle, FALSE);
				}
			}
			
			memblock('ActiveTable', 'ActiveBlock', 'ABlock', 'A', "get active members");
			memblock('SemiTable', 'SemiBlock', 'SBlock', 'S', "get sem-active members");
			memblock('InactiveTable', 'InactiveBlock', 'IBlock', 'I', "get inactive members");
			
		} else {
			$t->set_msg('no_members', TRUE);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members DETAILS
	+ ------------------------------------------------
	| Gets details of user and shows them in a profile
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "details":
		
		$mm->ifempty($id, "?mod=members");
		
		$result = $core_db->query(
			"SELECT users.id, users.name, users.real_name, users.rank_id, users.info, users.email,
			users.msn,users.aim, users.yahoo, users.icq, users.birth_date, users.activity,
			users.flag_id, users.location,users.location, users.class, users.avatar_id, ranks.name AS rank,
			users.logo_id, users.quote, DATE_FORMAT(users.birth_date, '{$cfg['sql_date']['alt']}') AS date_of_birth,
			(year(now()) - year(users.birth_date) - (if (dayofyear(users.birth_date)>dayofyear(now()),1,0))) AS user_age
			FROM users
			LEFT JOIN ranks ON ranks.id = users.rank_id
			WHERE users.id = '$id'
			AND users.clan_id = '{$cfg['clan_id']}'
			AND users.id != '{$cfg['pub_id']}'",
			"Get member details."
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			$t->set_msg_var();
			$row = cmscode($core_db->fetch_row($result), 'html');
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['members']}</a>",
				"{$lang['profile']} ({$row['name']})"
			);
			
			$row['age'] = ( ($row['birth_date'] == '0000-00-00') ? "" : "{$row['user_age']} ({$lang['born']} {$row['date_of_birth']})" );
			$row['email'] = ($row['email'] ? "<a href=mailto:{$row['email']}>{$row['email']}</a>" : "");
			
			if ($row['flag_id']) {
				$row['flag'] = "<img src=images/flags/{$row['flag_id']}.gif>";
			} else {
				$row['flag'] = "";
			}
			
			switch($row['activity']){
				case 'A': $row['activity'] = $lang['active']; break;
				case 'S': $row['activity'] = $lang['semi_active']; break;
				case 'I': $row['activity'] = $lang['inactive']; break;
			}
			
			$row['quote'] = get_quote($row['id']);
			
			// If thers no quote, and no flag, then delete the chunk.
			if (!$row['quote']) $row['quote'] = ($row['flag_id'] ? " " : "");
			
			$row['user_avatar'] = (check_sql_file($row['avatar_id']) ? "<img src=?mod=files&action=getimage&id={$row['avatar_id']} border=0>" : "");
			$row['user_logo'] = (check_sql_file($row['logo_id']) ? "<img src=?mod=files&action=getimage&id={$row['logo_id']} border=0>" : "");
			
			$online_time = (time() - $cfg['online_since']);
			$is_online = $core_db->lookup("SELECT COUNT(*) FROM users WHERE unix_timestamp(lastaction) > '$online_time' AND logged_in = 'Y' AND id = '{$row['id']}'");
			$row['status'] = ($is_online ? "<span class=bodyGood>$lang[online]</span>" : "<span class=bodyError>$lang[offline]</span>");
			
			$t->set_array($row);
			
			set_admin_links('admin_links',
				array(
					"{$lang['edit']}; ?mod=members&action=edit&id={$row['id']}",
					"{$lang['delete']}; ?mod=members&action=update_multi&id={$row['id']}; true; delete_item_q"
				)
			);
			
		} else {
			$t->set_msg('no_members_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members ADMIN
	+ ------------------------------------------------
	| Shows list of members - this is the only section
	| of CMS (at v3.0) which employs the "update order"
	| feature; the user can edit the number order of
	| each member then click update to apply the new order
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		global $cfg;
		
		$srt->set_array( array('name', 'clan_ord; order', 'userlevel', 'rank_id; rank', 'lastaction'), 'clan_ord', 'asc', 0, $cfg['browsers_limit'], "users");
		
		$result = $core_db->query(
			"SELECT users.id, users.name, users.userlevel, users.clan_ord, users.rank_id,
			DATE_FORMAT(users.lastaction,'{$cfg['sql_date']['full']}') AS f_lastaction,
			ranks.name AS rank_info
			FROM users
			LEFT JOIN ranks ON ranks.id = users.rank_id
			WHERE users.clan_id = '{$cfg['clan_id']}'
			AND users.id != '{$cfg['pub_id']}'".
			$srt->sql(),
			"Get members."
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$nav->page_index("SELECT COUNT(*) FROM users WHERE clan_id = '{$cfg['clan_id']}'");
			$i = 1;
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['members']}</a>",
				$lang['admin']
			);
			
			while ($row = $core_db->fetch_row($result)) {
				$row["userlevel_info"] = $cfg["userlevels"][$row["userlevel"]];
				$row['tabindex'] = $i++;
				$row['hide_profile'] = (($row['id'] == $cfg['pub_id']) ? "<!--" : " ");
				$t->set_array($row, 'm_');
				// prefix 'm_' avoids conflicts with security
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_members', true, true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members CREATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		global $cfg;
		
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang['members']}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			$lang['create']
		);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$row['s_userlevel'] = arraylistbox('input[userlevel]', $cfg["userlevels"], 2);
		$row['s_rank'] = sqllistbox('input[rank]', "select id, name from ranks");
		$activity = array('A' => $lang['active'], 'S' => $lang['semi_active'], 'I' => $lang['inactive']);
		$row['s_activity'] = arraylistbox('input[activity]', $activity, 'A');
		$row['select_quote_style'] = arraylistbox('input[quote_style]', array(1 => $lang['quote'], 2 => $lang['plain']), 1);
		
		$files_db = new DBDriver;
		$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
		
		$row['s_user_avatar'] = $files_db->sqllistbox(
			'input[avatar]', "select id, filename from files where
			cat_id = 7 and clan_id = {$cfg['clan_id']}", FALSE, TRUE
		);
		
		$row['s_user_logo'] = $files_db->sqllistbox(
			'input[logo]', "select id, filename from files where
			cat_id = 8 and clan_id = {$cfg['clan_id']}", FALSE, TRUE
		);
		
		$t->set_array($row, "", "", "");
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members EDIT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		$mm->ifempty($id, "?mod=members&action=admin");
		
		$result = $core_db->query(
			"select id, name, userlevel, rank_id, real_name, info, email, msn, aim, yahoo, wonid, flag_id,
			avatar_id, logo_id, icq, admin, access, activity, location, class, quote, quote_style, birth_date,
			DATE_FORMAT(birth_date, '%d') as birth_day,
			DATE_FORMAT(birth_date, '%m') as birth_month,
			DATE_FORMAT(birth_date, '%Y') as birth_year,
			DATE_FORMAT(lastlogin,'{$cfg['sql_date']['full']}') as f_lastlogin,
			DATE_FORMAT(lastaction,'{$cfg['sql_date']['full']}') as f_lastaction
			from users
			where id = $id
			and clan_id = {$cfg['clan_id']}"
		, ("edit") );
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			
			$row = cmscode($core_db->fetch_row($result), 'cmscode');
			$row['active_logins'] = $core_db->lookup("select COUNT(*) from sessions where user_id = '{$row['id']}'");
			$row['s_userlevel'] = arraylistbox('input[userlevel]', $cfg["userlevels"], $row['userlevel'], false);
			$row['s_rank'] = sqllistbox('input[rank]', "select id, name from ranks", $row['rank_id'], false);
			$activity = array('A' => $lang['active'], 'S' => $lang['semi_active'], 'I' => $lang['inactive']);
			$row['s_activity'] = arraylistbox('input[activity]', $activity, $row['activity']);
			$row['select_quote_style'] = arraylistbox('input[quote_style]', array(1 => $lang['quote'], 2 => $lang['plain']), $row['quote_style']);
			
			if ($row['birth_date'] == "0000-00-00") {
				$row['birth_day'] = "";
				$row['birth_month'] = "";
				$row['birth_year'] = "";
			}
			
			$files_db = new DBDriver;
			$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
			
			$row['s_user_avatar'] = $files_db->sqllistbox(
				'input[avatar]', "select id, filename from files where
				cat_id = 7 and clan_id = {$cfg['clan_id']}", $row['avatar_id'], true
			);
			
			$row['s_user_logo'] = $files_db->sqllistbox(
				'input[logo]', "select id, filename from files where
				cat_id = 8 and clan_id = {$cfg['clan_id']}", $row['logo_id'], true
			);
			
			$t->set_array($row, "", "", "");
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
		} else {
			$t->set_msg('no_members_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members EDIT_PROFILE
	+ ------------------------------------------------
	| Similar to EDIT, but only shows info for current
	| user!
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit_profile":
		
		$result = $core_db->query(
			"select id, name, real_name, info, email, msn, aim, yahoo, quote, wonid, flag_id,
			avatar_id, logo_id, icq, admin, activity, location, class, quote_style, birth_date,
			DATE_FORMAT(birth_date, '%d') as birth_day,
			DATE_FORMAT(birth_date, '%m') as birth_month,
			DATE_FORMAT(birth_date, '%Y') as birth_year,
			DATE_FORMAT(lastlogin,'{$cfg['sql_date']['full']}') as f_lastlogin,
			DATE_FORMAT(lastaction,'{$cfg['sql_date']['full']}') as f_lastaction
			from users
			where id = {$cfg['user_id']}
			and id != $cfg[pub_id]"
		, ("edit profile") );
		
		if ($core_db->get_num_rows()) {
			
			$nav->navs[] = $lang['edit_profile'];
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			$t->set_msg_var();
			$row = cmscode($core_db->fetch_row($result), 'cmscode');
			$row['active_logins'] = $core_db->lookup("select COUNT(*) from sessions where user_id = '{$row['id']}'");
			$activity = array('A' => $lang['active'], 'S' => $lang['semi_active'], 'I' => $lang['inactive']);
			$row['s_activity'] = arraylistbox('input[activity]', $activity, $row['activity']);
			
			if ($row['birth_date'] == "0000-00-00") {
				$row['birth_day'] = "";
				$row['birth_month'] = "";
				$row['birth_year'] = "";
			}
			
			$files_db = new DBDriver;
			$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
			
			$row['s_user_avatar'] = $files_db->sqllistbox(
				'input[avatar]', "select id, filename from files where cat_id = 7 and
				clan_id = {$cfg['clan_id']} and userlevel <= $cfg[userlevel]", $row['avatar_id'], true
			);
			
			$row['s_user_logo'] = $files_db->sqllistbox(
				'input[logo]', "select id, filename from files where cat_id = 8 and
				clan_id = {$cfg['clan_id']} and userlevel <= $cfg[userlevel]", $row['logo_id'], true
			);
			
			$row['select_quote_style'] = arraylistbox('input[quote_style]', array(1 => $lang['quote'], 2 => $lang['plain']), $row['quote_style']);
			
			$t->set_array($row, "", "", "");
			
		} else {
			$t->set_msg('no_members_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members VIEW_FLAGS
	+ ------------------------------------------------
	| Shows all flags as images in the /images/maps dir.
	+ ------------------------------------------------
	| Added: v4.0 Delta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "view_flags":
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		
		$nav->navs = $lang['select_a_flag'];
		
		$dir = 'images/flags';
		$flags = "";
		$i = 0;
		if ($handle = opendir($dir)) {
			$flags .= "<table cellpadding=5 width=100%>";
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file, array(".", "..", "index.php"))) {
					
					// Get numeric name of flag.
					$name = explode('.', $file);
					$name = $name[0];
					
					$i++;
					if ($i == 1) {
						$flags .= "<tr>";
					}
					$flags .= "<td align=center><a href=javascript:; onClick=\"SetFlag('$name')\"><img src='$dir/$file' border=0></a></td>";
					if ($i == 6) {
						$flags .= "</tr>";
						$i = 0;
					}
				}
			}
			$flags .= "</table>";
		}
		$t->set_var('flags', $flags);
		
		$t->mparse('Output', 'subTemplate', "popup.ihtml");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members INSERT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "insert":
		
		global $cfg;
		
		$input = request_array("input");
		$name = $input['name'];
		
		$exists = $core_db->lookup("SELECT id FROM users ".
			"WHERE name = '$name' AND clan_id = '{$cfg['clan_id']}'");
		
		// Check to see if this member already exists...
		if (!$exists) {
			
			$flag_id = request_int("flag_id");
			$password = request_string("password");
			
			$userlevel = $input['userlevel'] + 0;
			$rank = $input['rank'] + 0;
			$avatar = $input['avatar'] + 0;
			$logo = $input['logo'] + 0;
			$quote_style = $input['quote_style'] + 0;
			$wonid = $input['wonid'];
			$activity = ($input['activity']);
			$real_name = ($input['real_name']);
			$info = ($input['info']);
			$email = ($input['email']);
			$msn = ($input['msn']);
			$aim = ($input['aim']);
			$yahoo = ($input['yahoo']);
			$icq = ($input['icq']);
			$location = ($input['location']);
			$class = ($input['class']);
			$quote = ($input['quote']);
			$clan_ord = ($core_db->lookup("SELECT COUNT(*) FROM users WHERE clan_id = {$cfg['clan_id']}") + 1);
			$password_sql = (!$password ? md5(uniqid(microtime(),1)) : md5($password));
			$info = cmscode($input['info'], 'cmscode');
			
			if ($input['birth_day'] && $input['birth_month'] && $input['birth_year']) {
				$birth_date = sql_date($input['birth_day'], $input['birth_month'], $input['birth_year']);
			} else {
				$birth_date = "00/00/00 00:00";
			}
			
			switch ($userlevel) {
				case 1:
					$access = $cfg['public_access'];
					$admin = 'N';
				break;
				case 2:
					$access = $cfg['member_access'];
					$admin = 'N';
				break;
				case 3:
					$access = $cfg['admin_access'];
					$admin = 'Y';
				break;
			}
			
			$core_db->query(
				"INSERT users SET
				name = '$name',
				password = '$password_sql',
				userlevel = $userlevel,
				rank_id = $rank,
				avatar_id = $avatar,
				flag_id	=	'$flag_id',
				logo_id = $logo,
				birth_date = '$birth_date',
				real_name = '$real_name',
				activity = '$activity',
				location = '$location',
				class = '$class',
				quote	= '$quote',
				info = '$info',
				email = '$email',
				msn = '$msn',
				aim = '$aim',
				yahoo = '$yahoo',
				icq = '$icq',
				wonid = '$wonid',
				clan_ord = $clan_ord,
				clan_id = {$cfg['clan_id']},
				create_id = {$cfg['user_id']},
				edit_id = {$cfg['user_id']},
				create_date = now(),
				edit_date = now(),
				logged_in = 'N',
				admin = '$admin',
				access = '$access',
				quote_style = $quote_style",
				"insert"
			);
			
			$id = $core_db->get_insert_id();
			
			if ($files->insert('upload_avatar', 7)) {
				$core_db->query(
					"UPDATE users SET
					avatar_id = '$files->file_id'
					where id = '$id'
					and clan_id = '{$cfg['clan_id']}'",
					"Change avatar_id."
				);
			}
			
			if ($files->insert('upload_logo', 7)) {
				$core_db->query(
					"UPDATE users SET
					logo_id = '$files->file_id'
					where id = '$id'
					and clan_id = '{$cfg['clan_id']}'",
					"Change logo_id."
				);
			}
			
			add_log("Inserted Member (ID: $id)");
			go_back("msg=i", "?mod=$mm->module&action=admin");
			
		} else {
			// Return them to wherever, and say member exists.
			go_back("msg=mx", "?mod=$mm->module&action=admin");
		}
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members UPDATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "update":
		
		$mm->ifempty($id, "?mod=members&action=admin");
		
		$input = request_array("input");
		$flag_id = request_int("flag_id");
		$password = request_string("password");
		
		$flag_id = $flag_id + 0;
		$userlevel = $input['userlevel'] + 0;
		$rank = $input['rank'] + 0;
		$avatar = $input['avatar'] + 0;
		$logo = $input['logo'] + 0;
		$quote_style = $input['quote_style'] + 0;
		$wonid = $input['wonid'];
		$activity = ($input['activity']);
		$name = ($input['name']);
		$real_name = ($input['real_name']);
		$info = ($input['info']);
		$email = ($input['email']);
		$msn = ($input['msn']);
		$aim = ($input['aim']);
		$yahoo = ($input['yahoo']);
		$icq = ($input['icq']);
		$location = ($input['location']);
		$class = ($input['class']);
		$quote = ($input['quote']);
		$info = cmscode($input['info'], 'cmscode');
		$birth_date = sql_date($input['birth_day'], $input['birth_month'], $input['birth_year']);
		
		$password_sql = "";
		if ($password != "") {
			$password_sql = "password = md5('" . ($password) . "'),";
		}
		
		// Check to see if the user has chosen a diffrent userlevel
		$current_userlevel = $core_db->lookup("SELECT userlevel FROM users WHERE id = '$id'");
		
		$access = "";
		if ($current_userlevel != $userlevel) {
			// If so apply the new security settings!
			
			switch ($userlevel) {
				case 1: $access = $cfg['public_access']; break;
				case 2: $access = $cfg['member_access']; break;
				case 3: $access = $cfg['admin_access']; break;
			}
		}
		
		// Determin wether or not they are admin!
		switch ($userlevel) {
			case 1: $admin = 'N'; break;
			case 2: $admin = 'N'; break;
			case 3: $admin = 'Y'; break;
		}
		
		$access_sql = "";
		if ($access != "") {
			$access_sql = "access = '$access',";
		}
		
		$core_db->query(
			"update users set
			$password_sql
			name = '$name',
			userlevel = $userlevel,
			rank_id = $rank,
			avatar_id = $avatar,
			flag_id	=	'$flag_id',
			logo_id = $logo,
			birth_date = '$birth_date',
			real_name = '$real_name',
			activity = '$activity',
			location = '$location',
			class = '$class',
			quote	= '$quote',
			info = '$info',
			email = '$email',
			msn = '$msn',
			aim = '$aim',
			yahoo = '$yahoo',
			icq = '$icq',
			wonid = '$wonid',
			admin = '$admin',
			$access_sql
			edit_date = now(),
			edit_id = {$cfg['user_id']},
			quote_style = $quote_style
			where id = $id and clan_id = {$cfg['clan_id']}"
		, ("update"));
		
		if ($files->insert('upload_avatar', 7)) {
			$core_db->query(
				"UPDATE users SET
				avatar_id = '$files->file_id'
				where id = '$id'
				and clan_id = '{$cfg['clan_id']}'",
				"Change avatar_id."
			);
		}
		
		if ($files->insert('upload_logo', 7)) {
			$core_db->query(
				"UPDATE users SET
				logo_id = '$files->file_id'
				where id = '$id'
				and clan_id = '{$cfg['clan_id']}'",
				"Change logo_id."
			);
		}
		
		add_log("Updated Member (ID: $id)");
		go_back("msg=u", "?mod=$mm->module&action=admin");
		
	break;
	
	/* -----------------------------------------------
	| Members UPDATE_PROFILE
	+ ------------------------------------------------
	| Only updates where id = {$cfg['user_id']} - can't
	| change admin or userlevel privs etc.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "update_profile":
		
		$input = request_array("input");
		$flag_id = request_int("flag_id");
		
		$avatar = $input['avatar'] + 0;
		$logo = $input['logo'] + 0;
		$quote_style = $input['quote_style'] + 0;
		$wonid = $input['wonid'];
		$activity = ($input['activity']);
		$name = ($input['name']);
		$real_name = ($input['real_name']);
		$info = ($input['info']);
		$email = ($input['email']);
		$msn = ($input['msn']);
		$aim = ($input['aim']);
		$yahoo = ($input['yahoo']);
		$icq = ($input['icq']);
		$location = ($input['location']);
		$class = ($input['class']);
		$quote = ($input['quote']);
		$info = cmscode($input['info'], 'cmscode');
		$birth_date = sql_date($input['birth_day'], $input['birth_month'], $input['birth_year']);
		if ($password) { $password = "password = md5('" . ($password) . "'),"; }// Defaults
		
		$core_db->query(
			"update users set
			$password
			name = '$name',
			birth_date = '$birth_date',
			real_name = '$real_name',
			activity = '$activity',
			location = '$location',
			avatar_id = $avatar,
			flag_id	=	'$flag_id',
			logo_id = $logo,
			class = '$class',
			quote	= '$quote',
			info = '$info',
			email = '$email',
			msn = '$msn',
			aim = '$aim',
			yahoo = '$yahoo',
			icq = '$icq',
			wonid = '$wonid',
			edit_date = now(),
			edit_id = {$cfg['user_id']},
			quote_style = $quote_style
			where id = {$cfg['user_id']} and
			clan_id = {$cfg['clan_id']}
			and id != $cfg[pub_id]",
			"update profile"
		);
		
		$id = $cfg['user_id'];
		
		if ($files->insert('upload_avatar', 7)) {
			$core_db->query(
				"UPDATE users SET
				avatar_id = '$files->file_id'
				where id = '$id'
				and clan_id = '{$cfg['clan_id']}'",
				"Change avatar_id."
			);
		}
		
		if ($files->insert('upload_logo', 8)) {
			$core_db->query(
				"UPDATE users SET
				logo_id = '$files->file_id'
				where id = '$id'
				and clan_id = '{$cfg['clan_id']}'",
				"Change logo_id."
			);
		}
		
		add_log("Updated Profile (ID: $id)");
		go_back("msg=pu", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Members UPDATE_MULTI
	+ ------------------------------------------------
	| Deletes multiple items and updates order.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "update_multi":
		
		global $cfg;
		
		$clan_ord = request_array("clan_ord");
		$item = request_array("item");
		$id = request_int("item");
		
		if ($clan_ord) {
			foreach($clan_ord as $id => $value) {
				$id = $id + 0;
				$value = $value + 0;
				$core_db->query(
					"UPDATE users SET clan_ord = '$value'
					WHERE id = '$id' AND clan_id = '{$cfg['clan_id']}'",
					"Update list of members."
				);
				add_log("Updated Member (ID: $key)");
			}
			$msg = "um";
			unset($id);
		}
		
		if ($id != 0) {
			$item[] = $id;
		}
		
		$delete_self = array();
		if (count($item) > 0) {
			
			foreach($item as $key => $id) {
				// Check that the user they want to delete isn't themself or public!
				if (($id != $cfg['user_id']) && ($id != $cfg['pub_id'])) {
					$key = $key + 0;
					$id = $id + 0;
					$count++;
					
					$core_db->query(
						"DELETE FROM users WHERE id = '$id'
						AND clan_id = '{$cfg['clan_id']}'",
						"Delete member."
					);
					
					$core_db->query(
						"DELETE FROM sessions
						WHERE user_id = '$id'",
						"Destroying session."
					);
					
					$core_db->query(
						"DELETE FROM settings_private
						WHERE user_id = '$id'",
						"Clear private settings."
					);
					
					$core_db->query(
						"DELETE FROM buddy_list
						WHERE buddy_id = '$id'
						OR user_id = '$id'",
						"Clear buddy lists."
					);
					
					$delete_self[0] = FALSE;
					add_log("Deleted Member (ID: $id)");
					
				} else {
					$delete_self[$i++] = TRUE;
					add_log("Delete Self or Public - Failed (ID: $id)");
				}
			}
		}
		
		if (in_array(TRUE, $delete_self)) {
			$msg = "cds";
		} elseif ($count == 1) {
			$msg = "ds";
		} elseif ($count > 1) {
			$msg = "d";
		}
		
		go_back("msg=$msg", "?mod=$mm->module&action=admin");
		
	break;
}