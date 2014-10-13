<?php

switch($mm->action) {
	
	/*
	+ ------------------------------------------------
	| Home DEFAULT
	+ ------------------------------------------------
	| Only shows news.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "default":
		
		$result = $core_db->query(
			"SELECT news.id, news.title, news.body, news.edit_note, news.create_id, news.edit_id, news.avatar,
			DATE_FORMAT(news.create_date,'{$cfg['sql_date']['full']}') as date,
			DATE_FORMAT(news.edit_date,'{$cfg['sql_date']['full']}') as edit_date,
			users.name as author, users.avatar_id
			FROM news LEFT JOIN users ON users.id = news.create_id
			WHERE news.userlevel <= '{$cfg['userlevel']}'
			AND news.clan_id = {$cfg['clan_id']}
			ORDER BY news.create_date DESC LIMIT 5",
			"Fetch list of all recent news."
		);
		
		if ($core_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'NewsBlock', 'NBlock');
			$t->set_msg_var('news');
			$nav->navs[] = $lang["home"];
			
			while ($row = cmscode($core_db->fetch_row($result))) {
				
				// Cant't select the edit name in the inital sql query,
				// because rows from users already matched with news.create_id...
				$row['edit_name'] = $core_db->lookup("SELECT name FROM users WHERE id = '{$row['edit_id']}'");
				
				// Start off by saying no edit.
				$row['edit'] = " ";
				
				// If the edit date is different or the user isn't the same and edit_note is enabled...
				if ((($row['edit_date'] != $row['date']) || ($row['edit_id'] != $row['create_id'])) && ($row['edit_note'] == 'Y')) {
					$editor_link = "<a href=?mod=members&action=details&id={$row['edit_id']}>{$row['edit_name']}</a>";
					$row['edit'] = " - [ {$lang['news_edit_by']} <b>$editor_link</b> - <b>{$row['edit_date']}</b> ]";
				}
				
				$row['quote'] = get_quote($row['create_id']);
				
				$avatar_exists = "";
				if ($row['avatar'] == 'Y') {
					$avatar_exists = check_sql_file($row['avatar_id']);
				}
				
				echo "<!-- {$row['id']}: ". $avatar_exists . " -->\n";
				
				if (!$avatar_exists && $row['avatar_id']) {
					$core_db->query(
						"UPDATE news SET avatar = 'N' WHERE id = '{$row['id']}'",
						"Disable avatar for this news post!"
					);
				}
				
				// Check that the user wants an avatar if so, look for the avatar, if it exists, give the id...
				$row['avatar_id'] = ((($row['avatar'] == 'Y') && $avatar_exists) ? $row['avatar_id'] : "");
				
				if ($t->check_privs(9, 'browse')) {
					$cmts->set_count($row['id'], 2);
					$row['comment'] = " - [ <a href=?mod=news&action=details&id={$row['id']}>{$lang['comments']}</a> ($cmts->num_comments) ]";
				}
				
				if ($row['author']) {
					// For previews, open link in new window.
					$row['author'] = "<a href='?mod=members&action=details&id={$row['create_id']}'>{$row['author']}</a>";
					
				} else {
					// The user has been deleted!
					$row['author'] = $lang['unknown_author'];
				}
				
				if (!isset($edit)) { $edit = ""; }
				if (!isset($comments)) { $comments = ""; }
				
				$row['info'] = "[ {$lang['created_by']} <b>{$row['author']}</b> ] - [ {$lang['news_posted']} <b>$row[date]</b> ]$edit$comments";
				
				set_admin_links('news_admin_links',
					array(
						"$lang[edit]; ?mod=news&action=edit&id={$row['id']}",
						"$lang[delete]; ?mod=news&action=delete&id={$row['id']}; true; delete_item_q"
					)
				);
				
				$t->set_array($row);
				
				$t->parse('NBlock', 'NewsBlock', true);
			}
		} else {
			$t->set_msg('no_home', true, false, true, true);
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
}