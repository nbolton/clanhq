<?php

switch($mm->action) {
	
	case "default": redirect("&action=browse"); break;
	
	/*
	+ ------------------------------------------------
	| Poll BROWSE
	+ ------------------------------------------------
	| Shows all polls as list.
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "browse":
		
		$srt->set_array( array('title', 'info', 'create_id; author', 'create_date; date'), 'create_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT id, title, info, create_id,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['short']}') as date
			FROM polls_data
			WHERE userlevel <= '{$cfg['userlevel']}'
			AND clan_id = '{$cfg['clan_id']}'".
			$srt->sql(),
			"Get poll list."
		);
		
		if ($core_db->get_num_rows()) {
			
			$nav->navs[] = $lang["polls"];
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->page_index(
				"SELECT COUNT(*) FROM polls_data
				WHERE userlevel <= '{$cfg['userlevel']}'
				AND clan_id = '{$cfg['clan_id']}'"
			);
			
			while ($row = $core_db->fetch_row($result)) {
				$row['author'] = $core_db->lookup("SELECT name FROM users WHERE id = '{$row['create_id']}'");
				$row['author'] = ($row['author'] ? "<a href=?mod=members&action=details&id={$row['create_id']}>{$row['author']}</a>" : "");
				$row['title'] = substr_adv(cmscode($row['title']), 25);
				$row['info'] = substr_adv(cmscode($row['info']), 30, "?mod=polls&action=details&id={$row['id']}");
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_polls', true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Poll DETAILS
	+ ------------------------------------------------
	| Shows specific details for one poll.
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "details":
		
		(!$id ? header("Location:http:?mod=polls") : ($id = $id + 0));
		
		$result = $core_db->query(
			"SELECT id, title, info, DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') as date,
			(unix_timestamp(now()) - unix_timestamp(create_date)) as timerunning
			FROM polls_data
			WHERE userlevel <= '{$cfg['userlevel']}'
			AND clan_id = '{$cfg['clan_id']}'
			AND id = '$id'",
			"Get poll details."
		);
		
		if ($core_db->get_num_rows($result)) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			
			$row = $core_db->fetch_row($result);
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['polls']}</a>",
				"{$lang['details']} ({$row['title']})"
			);
			
			$row['title'] = cmscode($row['title']);
			$row['info'] = cmscode($row['info']);
			
			$allow_pub = $core_db->lookup("select allow_pub from polls_data where id = $id");
			$lang['poll_details'] .= '<br><i>' . (($allow_pub == 'Y') ? $lang['polls_pub_enabled'] : $lang['polls_pub_disabled']) . '</i>';
			
			$t->set_var('poll_details', $lang['poll_details']);
			$running_days = $row['timerunning'] / 86400;
			$time_left = round($row['timerunning'] / 86400);
			$time_type = ($time_left == 1) ? 'day' : 'days';
			if ($row['timerunning'] < 86400) {
				
				$time_left = round($row['timerunning'] / 3600);
				$time_type = ($time_left == 1) ? 'hour' : 'hours';
				if ($row['timerunning'] < 3600) {
					
					$time_left = round($row['timerunning'] / 60);
					$time_type = ($time_left == 1) ? 'min' : 'mins';
					if ($row['timerunning'] < 60) {
						
						$time_left = round($row['timerunning']);
						$time_type = ($time_left == 1) ? 'sec' : 'secs';
					}
				}
			}
			$row['timerunning'] = "$time_left $lang[$time_type]";
			
			$t->set_array($row, FALSE, "subTemplate");
			
			set_admin_links('admin_links',
				array(
					"$lang[edit]; ?mod=$mm->module&action=edit&id={$row['id']}",
					"$lang[delete]; ?mod=$mm->module&action=delete&id={$row['id']}; true; delete_item_q"
				), FALSE, "subTemplate"
			);
			
			$poll_id = $row['id'];
			
			$votecount_result = $core_db->query(
				"SELECT votes
				FROM polls_options
				WHERE poll_id = '$poll_id'
				AND clan_id = '{$cfg['clan_id']}'",
				"Get poll votes for adding total."
			);
			
			$total_votes = 0;
			if ($core_db->get_num_rows($votecount_result)) {
				while ($row = $core_db->fetch_row($votecount_result)) {
					$total_votes += $row['votes'];
				}
			}
			
			if ($running_days < 1) { $running_days = 1; }
			$votes_per_day = round($total_votes / $running_days, 0); // vote per hour figure stays below total votes
			$votes_per_day = (($votes_per_day > $total_votes) ? $total_votes : $votes_per_day);
			$t->set_var('votes_per_day', "$votes_per_day " . (($votes_per_day == 1) ? strtolower($lang['vote']) : strtolower($lang['votes'])));
			$t->set_var('total_votes', "$total_votes " . (($total_votes == 1) ? $lang['vote'] : $lang['votes']));
			
			$opt_results = $core_db->query(
				"SELECT id as opt_id, opt, votes
				FROM polls_options
				WHERE poll_id = '$poll_id'
				AND clan_id = '{$cfg['clan_id']}'
				ORDER BY votes DESC",
				"get poll options"
			);
			
			if ($core_db->get_num_rows($opt_results)) {
				
				// Make sure there is no division by 0.
				if (!$total_votes) $total_votes = 1;
				
				while ($row = $core_db->fetch_row($opt_results)) {
					
					// Work out the percentage and stuff.
					$percentage = $row['votes'] / $total_votes * 100;
					$row['opt_bar_width'] = round($percentage, 0) * 3;
					$row['opt_percent'] = round($percentage, 2) . '%';
					$row['votes'] = "$row[votes] " . ( ($row['votes'] == 1) ? $lang['vote'] : $lang['votes'] );
					$t->set_array($row);
					$t->parse('RBlock', 'RowBlock', true);
				}
			} else {
				$t->parse('RBlock', 'RowBlock', true);
				$t->set_var('RBlock', "<tr><td class=bodyText>$lang[no_poll_options]</td></tr>");
			}
			
			$users_voted = $core_db->query(
				"SELECT user_id
				FROM polls_log
				WHERE poll_id = '$poll_id'
				AND clan_id = '{$cfg['clan_id']}'
				ORDER BY date ASC",
				"Get poll options."
			);
			
			if ($core_db->get_num_rows($users_voted)) {
				$i = 1;
				while($row = $core_db->fetch_row($users_voted)) {
					
					// Get the name of the user...
					$name = $core_db->lookup("SELECT name FROM users WHERE id = '{$row['user_id']}'");
					
					// If there's no name in the db, the user dosen't exist.
					if (!$name) { $name = $lang['ex_member']; }
					
					// If user isn't public... otherwise, increment the public votes.
					($row['user_id'] != $cfg['pub_id']) ? ($who_voted[] = $name) : ($pub_votes = $i++);
				}
				
				if($pub_votes) {
					// Select the clan's name for their public account.
					$pub_name = $core_db->lookup("SELECT name FROM users WHERE id = '{$cfg['pub_id']}'");
					
					// Plural or singular?
					$votes = ($pub_votes == 1 ? $lang['lc_vote'] : $lang['lc_votes']);
					
					$who_voted[] = "$pub_name ($pub_votes $votes)";
				}
				
				$t->set_var('who_voted', implode(", ", $who_voted) . ".");
			} else {
				$t->set_var('who_voted', $lang['poll_nobody_voted']);
				$t->set_var('poll_details', $lang['no_poll_details']);
			}
			
			if (!$t->check_privs(9, 'create') || $preview) $t->kill_block($var, 'post_comment', "subTemplate");
			$cmts->set_comments($row['id']);
			
		} else {
			$t->set_msg('no_news_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Poll VOTE
	+ ------------------------------------------------
	| Votes for an option and sends user back to page.
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "vote":
		
		$opt = $option + 0;
		$poll = polls_vote_control($id);
		
		if (!$poll['has_voted'] && $poll['allow_vote'] && !$spr && $opt) {
			
			$core_db->query(
				"UPDATE polls_options
				SET votes = (votes + 1)
				WHERE id = '$opt'
				AND poll_id = '$id'
				AND clan_id = '{$cfg['clan_id']}'",
				"Increment vote count."
			);
			
			$core_db->query(
				"INSERT polls_log SET
				poll_id = '$id',
				opt_id = '$opt',
				user_id = '{$cfg['user_id']}',
				clan_id = '{$cfg['clan_id']}',
				ip = '$REMOTE_ADDR',
				date = now()",
				"Log vote"
			);
			
			setcookie("poll-$id", $id , time() + 3600 * 12 * 365 , '/'); // Set cookie so user can vote more than once
			add_log("Voted on Poll (ID: $id) option (ID: $opt)");
			go_back();
			
		} else {
			add_log("Attempt failed: Vote on Poll (ID: $id) option (ID: $opt)");
			go_back();
		}
		
	break;
	
	/*
	+ ------------------------------------------------
	| Poll ADMIN
	+ ------------------------------------------------
	| Shows list of polls with admin features.
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		$srt->set_array( array('title; title', 'create_id; creator', 'userlevel', 'create_date; date'), 'create_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT id, title, info, create_id, userlevel,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') AS date
			FROM polls_data
			WHERE userlevel <= '{$cfg['userlevel']}'
			and clan_id = '{$cfg['clan_id']}'".
			$srt->sql(),
			"Get polls."
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->page_index("select COUNT(*) from polls_data
			where userlevel <= $cfg[userlevel]
			and clan_id = {$cfg['clan_id']}");
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['polls']}</a>",
				$lang['admin']
			);
			
			$i = 0;
			while ($row = $core_db->fetch_row($result)) {
				$row['levelinfo'] = $core_db->lookup("select info from userlevels where id = $row[userlevel]");
				$row['title'] = substr_adv($row['title'], 25, false);
				$row['alt_color'] = ( ($i++ % 2) ? 'class=altBgColor' : false );
				if ($row['create_id']) { $row['creator'] = $core_db->lookup("select name from users where id = $row[create_id]"); }
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_polls', true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Poll CREATE
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang['polls']}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			$lang['create']
		);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate');
		
		$t->set_var('s_userlevel', sqllistbox('input[s_userlevel]', "SELECT id, info FROM userlevels"));
		$t->set_var('allow_pub', yn('input[allow_pub]', 'Y', 'radio'));
		$t->set_var('enabled', yn('input[enabled]', 'Y', 'radio'));
		
		$t->set_file('RowController', '../../../source/templates/row_ctrl_block.ihtml');
		$t->set_block('RowController');
		
		$t->set_var('delete_parameters', "'{row_id}', '{lang_option_delete_warn}'");
		$t->set_var(array('row_id' => "%rowID%", 'row_count' => 2, 'field_name' => 'option'));
		$t->set_var(array('row_label' => '{lang_option}', 'field_width' => 30));
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Poll EDIT
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		$id = $_GET['id'] + 0;
		$mm->ifempty($id);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate');
		
		$result = $core_db->query(
			"SELECT id, title, info, enabled,
			allow_pub, userlevel, create_id, edit_id,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') AS date
			FROM polls_data
			WHERE id = '$id'
			AND clan_id = '{$cfg['clan_id']}'",
			"Edit poll."
		);
		
		$i = 0;
		
		if ($core_db->get_num_rows()) {
			
			$row = $core_db->fetch_row($result);
			$row = cmscode($row, 'cmscode');
			$t->set_var($row);
			
			$t->set_var('title', stripslashes($row['title']));
			$t->set_var('select_userlevel', sqllistbox('select_userlevel', "SELECT id, info FROM userlevels", $row['userlevel']));
			$t->set_var('allow_pub', yn('allow_pub', $row['allow_pub'], 'radio'));
			$t->set_var('enabled', yn('enabled', $row['enabled'], 'radio'));
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
			$options_result = $core_db->query(
				"SELECT opt AS field_value
				FROM polls_options
				WHERE poll_id = '$id'
				ORDER BY opt_num ASC",
				"Get poll options."
			);
			
			$t->set_file('RowController', '../../../source/templates/row_ctrl_block.ihtml');
			$t->set_block('RowController', 'LayerRow', 'LayerBlock');
			
			if ($core_db->get_num_rows($options_result)) {
				
				$i = 1;
				while ($row = $core_db->fetch_row($options_result)) {
					
					$row['delete_parameters'] = "'{row_id}', '{lang_restore_options_tip}'";
					$row['row_id'] = $i++;
					
					$t->set_var($row);
					$t->parse('OptionsBlock', 'LayerRow', TRUE);
				}
			}
			
			$t->set_var('delete_parameters', "'%rowID%', '{lang_restore_options_tip}'");
			$t->set_var(array('field_name' => 'option',  'field_value' => '', 'field_width' => 30));
			$t->set_var(array('row_id' => '%rowID%', 'row_label' => '{lang_option}', 'form_name' => "poll_edit"));
			$t->parse('LayerBlock', 'LayerRow');
			
			$t->set_var('row_count', $core_db->get_num_rows($row));
			
		} else {
			$t->set_msg('no_polls_id');
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Poll INSERT
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "insert":
		
		$title = ($input['title']);
		$info = str_replace("\n","<br>", str_replace("  ","&nbsp;&nbsp;", ($input['info']) ) );
		$s_userlevel = $input['s_userlevel'] + 0;
		$allow_pub = ($input['allow_pub']);
		$enabled = ($input['enabled']);
		
		if($enabled == 'Y') {
			$core_db->query(
				"UPDATE polls_data
				SET enabled = 'N'
				WHERE clan_id = '{$cfg['clan_id']}'
				and is_enabled = 'Y'",
				"Disable all other polls."
			);
		}
		
		$core_db->query(
			"INSERT polls_data SET
			title = '$title',
			info = '$info',
			enabled = '$enabled',
			allow_pub = '$allow_pub',
			userlevel = '$s_userlevel',
			clan_id = {$cfg['clan_id']},
			create_id = {$cfg['user_id']},
			edit_id = {$cfg['user_id']},
			create_date = now(),
			edit_date = now()",
			"Insert poll."
		);
		
		$id = $core_db->get_insert_id();
		$opt_num = 1;
		
		foreach($_POST['option'] as $k => $v) {
			
			$core_db->query(
				"INSERT polls_options SET
				poll_id = '$id',
				opt_num = '" . $i++ . "',
				clan_id = '{$cfg['clan_id']}',
				opt = '$v'",
				"Insert poll option."
			);
		}
		
		add_log("Inserted Poll (ID: $id)");
		go_back("msg=i", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Poll UPDATE
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "update":
		
		$id = $_POST['id'] + 0;
		$mm->ifempty($id);
		
		$JSRowsChanged = FALSE;
		if (isset($_POST['js_rows_changed'])) {
			if ($_POST['js_rows_changed'] == "1") {
				$JSRowsChanged = TRUE;
			}
		}
		
		$_POST['info'] = str_replace("\n","<br>", str_replace("  ","&nbsp;&nbsp;", $_POST['info']));
		
		$s_userlevel = "";
		if (isset($_POST['s_userlevel'])) {
			$s_userlevel = $_POST['s_userlevel'] + 0;
		}
		
		if ($enabled == 'Y') {
			$core_db->query(
				"UPDATE polls_data
				SET enabled = 'N'
				WHERE clan_id = '{$cfg['clan_id']}'
				and is_enabled = 'Y'",
				"Disable all other polls."
			);
		}
		
		if (!$JSRowsChanged) {
			
			$core_db->query(
				"UPDATE polls_data SET
				title = '$title',
				info = '$info',
				enabled = '$enabled',
				allow_pub = '$allow_pub',
				userlevel = '$s_userlevel',
				clan_id = {$cfg['clan_id']},
				create_id = {$cfg['user_id']},
				edit_id = {$cfg['user_id']},
				create_date = now(),
				edit_date = now()
				WHERE id = '$id'",
				"Update poll."
			);
			
		} else {
			
			$core_db->query(
				"DELETE FROM polls_data
				WHERE clan_id = '{$cfg['clan_id']}'
				AND id = '$id'",
				"Delete entire poll so people can re-vote."
			);
			
			$core_db->query(
				"DELETE FROM polls_log
				WHERE clan_id = '{$cfg['clan_id']}'
				AND poll_id = '$id'",
				"Delete all votes that have been cast."
			);
			
			$core_db->query(
				"DELETE FROM polls_options
				WHERE clan_id = '{$cfg['clan_id']}'
				AND poll_id = '$id'",
				"Delete all options to be replaced."
			);
			
			add_log("Removed Poll (ID: $id)");
			
			$core_db->query(
				"INSERT polls_data SET
				title = '$title',
				info = '$info',
				enabled = '$enabled',
				allow_pub = '$allow_pub',
				userlevel = '$s_userlevel',
				clan_id = {$cfg['clan_id']},
				create_id = {$cfg['user_id']},
				edit_id = {$cfg['user_id']},
				create_date = now(),
				edit_date = now()",
				"Insert poll."
			);
			
			$id = $core_db->get_insert_id();
			$i = 0;
			
			foreach($_POST['option'] as $k => $v) {
				if ($k != "%rowID%" && $v) {
					$core_db->query(
						"INSERT polls_options SET
						poll_id = '$id',
						opt_num = '" . $i++ . "',
						clan_id = '{$cfg['clan_id']}',
						opt = '$v'",
						"Insert poll option."
					);
				}
			}
		}
		
		add_log("Updated Poll (ID: $id)");
		go_back("msg=u", "?mod=$mm->module&action=admin", preg_replace("/id=(\d)*/", "id=$id", $_SERVER['HTTP_REFERER']));
		
	break;
	
	/*
	+ ------------------------------------------------
	| Poll DELETE
	+ ------------------------------------------------
	| Added: v3.6 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "delete":
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach($item as $key => $id) {
				$id = $id + 0;
				$core_db->query(
					"DELETE FROM polls_data
					WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete poll."
				);
				$core_db->query(
					"DELETE FROM polls_options
					WHERE poll_id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete poll options."
				);
				add_log("Deleted Poll (ID: $id)");
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