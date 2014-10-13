<?php

switch($mm->action) {
	
	case "default":
		
		if (($cfg['user_id'] != $cfg['pub_id'])) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			
			$result = $core_db->query(
				"SELECT id, clan_id, title, body, from_id, unread, popped,
				DATE_FORMAT(send_date,'{$cfg['sql_date']['full']}') as date
				FROM messages
				WHERE to_id = {$cfg['user_id']}
				AND type = 'I'
				order by send_date desc limit $cfg[messages_limit]",
				"get unread messages"
			);
			
			// Need to refresh this window every minute.
			echo "<meta http-equiv=refresh content=60>\n";
			
			if ($core_db->get_num_rows()) {
				
				if ($cfg['show_messages']) {
					$vars = "   parent.msg_table.innerHTML = parent.msg_cached_table\n";
				}
				$i = 0;
				while ($row = $core_db->fetch_row($result)) {
					
					if (($id != $row['id']) && ($cfg['show_messages'] & 1)) {
						
						$i++;
						$row['from'] = $core_db->lookup("SELECT name FROM users WHERE id = $row[from_id]", "get name of sender");
						$row['clan'] = $core_db->lookup("SELECT tag FROM clans WHERE id = $row[clan_id]", "get clan's tag");
						$row['clan'] .= " " . $core_db->lookup("SELECT display FROM clans WHERE id = $row[clan_id]", "get name of clan");
						$row['icon'] = "<img src=\\\"themes/$cfg[theme_id]/images/" . ($row['unread'] ? 'msg_unread.gif' : 'msg_read.gif') .'\" border=0>';
						
						$row['details'] = 
							"<table border='1' cellspacing='0' cellpadding='5' class='tableDefault'>".
							"<tr><td bordercolor='#000000' class='bodyText'>".
							"<b>{$lang['title']}:</b> " . ($row['title'] ? $row['title'] : $lang['na']) . "<br>".
							"<b>{$lang['from']}:</b> {$row['from']}<br>".
							"<b>{$lang['clan']}:</b> {$row['clan']}<br>".
							"<b>{$lang['received']}:</b> {$row['date']}<br>".
							"{$lang['click_now_to_read_message']}</td></tr></table>";
						
						// Only return the first line of message!
						$row['body'] = explode("\n", $row['body']);
						$row['body'] = ($row['body'][0] ? $row['body'][0] : $lang['na']);
						
						// Do we have a title? If not, use the first bit of the body...
						if (!$row['title']) $row['title'] = $row['body'];
						
						// Treat the title so it can go in a JavaScript var.
						$row['title'] = substr_adv(strip_tags($row['title']), 13, "");
						$row['title'] = str_replace(chr(13).chr(10), "", $row['title']);
						$row['title'] = str_replace(chr(13), "", $row['title']);
						
						// Does overlib still work?
						/* <span onMouseOver=\"return overlib('".
							$row["details"]."')\" onMouseOut=\"return nd()\" """.
							""".""."onClick=\"return nd()\"> </span>*/
						
						$link = addslashes("<a href='?mod=messages&id='".
							$row['id']."' target='messages' onClick=\"openBrWindow('".
							"?mod=messages&action=read&id=". $row['id'] ."','','scrollbars=yes,".
							"resizable=yes,width=650,height=520')\">%variable%</a>");
						
						$title = str_replace("%variable%", $row['title'], $link);
						$icon = str_replace("%variable%", $row['icon'], $link);
						
						$vars .=
							"   // Set vars for message #$i...\n".
							"   parent.msg_space_$i.height = \"3\";\n".
							"   parent.msg_title_$i.innerHTML = \"$title\";\n".
							"   parent.msg_icon_$i.innerHTML = \"$icon\";\n   \n";
					}
				}
				
				// Now, get the list of unread messages in REVERSE ORDER.
				$result = $core_db->query(
					"SELECT id, clan_id, title, body, from_id, unread, popped,
					DATE_FORMAT(send_date,'{$cfg['sql_date']['full']}') as date
					FROM messages
					WHERE to_id = {$cfg['user_id']}
					AND type = 'I'
					AND unread = '1'
					order by send_date ASC limit $cfg[messages_limit]",
					"get unread messages"
				);
				
				$popups = "";
				if ($core_db->get_num_rows()) {
					
					while ($row = $core_db->fetch_row($result)) {
						
						if ($id != $row['id']) {
							
							// Has this message not already been popped?
							if (!$row['popped']) {
								switch ($cfg['messages_popup']) {
									
									// User want's only the LAST message to be shown.
									case 1:
										// Can't be popped more than once!
										if(!$has_popped) {
											$core_db->query("UPDATE messages SET popped = 1 where id = $row[id]");
											$popups .= "   openBrWindow('?mod=messages&action=read&id=$row[id]','','scrollbars=yes,resizable=yes,width=650,height=510');\n";
											$has_popped = 1;
										}
									break;
									
									// Popup ALL awaiting messages.
									case 2:
										$core_db->query("UPDATE messages SET popped = 1 where id = $row[id]");
										$popups .= "   openBrWindow('?mod=messages&action=read&id=$row[id]','','scrollbars=yes,resizable=yes,width=650,height=510');\n";
									break;
								}
							}
						}
					}
					$t->set_var('popups', $popups);
				}
				
				$t->set_var('vars', $vars);
				$t->set_var('popups', $popups);
				
			} elseif ($cfg['show_messages'] & 1) {
				$t->set_var('vars', "  parent.msg_table.innerHTML = \"{$lang['no_new_messages']}\";\n");
			}
			
			$t->parse('Output', 'subTemplate');
			$t->p('Output');
		}
		
	break;
	
	/* -----------------------------------------------
	| Messages INBOX
	+ ------------------------------------------------
	| Shows all read and unread messages in inbox.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "inbox":
		
		$srt->set_array( array('unread', '', 'from_id; from', 'title', 'send_date; date'), 'send_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT id, clan_id, title, from_id, unread,
			DATE_FORMAT(send_date,'{$cfg['sql_date']['full']}') as date
			FROM messages
			WHERE to_id = {$cfg['user_id']} AND type = 'I'" . $srt->sql(),
			"get unread messages"
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$nav->page_index("SELECT COUNT(*) FROM messages WHERE to_id = {$cfg['user_id']} AND type = 'I'");
			while ($row = $core_db->fetch_row($result)) {
				
				$row['from'] = $core_db->lookup("SELECT name FROM users WHERE id = $row[from_id]", "get name of user");
				$row['icon'] = "<img src=\"themes/{theme_id}/images/" . ($row['unread'] ? 'msg_unread.gif' : 'msg_read.gif') . "\" border=0>";
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_messages', 1, 1, 1, 1);
		}
		
		$t->mparse('Output', 'subTemplate', "messages_master.ihtml");
		
	break;
	
	/* -----------------------------------------------
	| Messages SENT
	+ ------------------------------------------------
	| Shows Sent Items.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "sent":
		
		$srt->set_array( array('unread', '', 'from_id; from', 'title', 'send_date; date'), 'send_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT id, clan_id, title, from_id, unread,
			DATE_FORMAT(send_date,'{$cfg['sql_date']['full']}') as date
			FROM messages
			WHERE to_id = {$cfg['user_id']} AND type = 'Se'" . $srt->sql(),
			"get unread messages"
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$nav->page_index("SELECT COUNT(*) FROM messages WHERE to_id = {$cfg['user_id']} AND type = 'Se'");
			while ($row = $core_db->fetch_row($result)) {
				
				$row['from'] = $core_db->lookup("SELECT name FROM users WHERE id = $row[from_id]", "get name of user");
				$row['icon'] = "<img src=\"themes/{theme_id}/images/" . ($row['unread'] ? 'msg_unread.gif' : 'msg_read.gif') . "\" border=0>";
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_messages_sent', 1, 1, 1, 1);
		}
		
		$t->mparse('Output', 'subTemplate', "messages_master.ihtml");
		
	break;
	
	/* -----------------------------------------------
	| Messages SAVED
	+ ------------------------------------------------
	| Shows Saved Items.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "saved":
		
		$srt->set_array( array('unread', '', 'from_id; from', 'title', 'send_date; date'), 'send_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT id, clan_id, title, from_id, unread,
			DATE_FORMAT(send_date,'{$cfg['sql_date']['full']}') as date
			FROM messages
			WHERE to_id = {$cfg['user_id']} AND type = 'Sa'" . $srt->sql(),
			"get unread messages"
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$nav->page_index("SELECT COUNT(*) FROM messages WHERE to_id = {$cfg['user_id']} AND type = 'Sa'");
			while ($row = $core_db->fetch_row($result)) {
				
				$row['from'] = $core_db->lookup("SELECT name FROM users WHERE id = $row[from_id]", "get name of user");
				$row['icon'] = "<img src=\"themes/{theme_id}/images/" . ($row['unread'] ? 'msg_unread.gif' : 'msg_read.gif') . "\" border=0>";
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_messages_saved', 1, 1, 1, 1);
		}
		
		$t->mparse('Output', 'subTemplate', "messages_master.ihtml");
		
	break;
	
	/* -----------------------------------------------
	| Messages DELETED
	+ ------------------------------------------------
	| Shows Recycle Bin.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "deleted":
		
		$srt->set_array( array('unread', '', 'from_id; from', 'title', 'send_date; date'), 'send_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $core_db->query(
			"SELECT id, clan_id, title, from_id, unread,
			DATE_FORMAT(send_date,'{$cfg['sql_date']['full']}') as date
			FROM messages
			WHERE to_id = {$cfg['user_id']} AND type = 'R'" . $srt->sql(),
			"get unread messages"
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$nav->page_index("SELECT COUNT(*) FROM messages WHERE to_id = {$cfg['user_id']} AND type = 'R'");
			while ($row = $core_db->fetch_row($result)) {
				
				$row['from'] = $core_db->lookup("SELECT name FROM users WHERE id = $row[from_id]", "get name of user");
				$row['icon'] = "<img src=\"themes/{theme_id}/images/" . ($row['unread'] ? 'msg_unread.gif' : 'msg_read.gif') . "\" border=0>";
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_messages_recycle', 1, 1, 1, 1);
		}
		
		$t->mparse('Output', 'subTemplate', "messages_master.ihtml");
		
	break;
	
	/* -----------------------------------------------
	| Messages COMPOSE
	+ ------------------------------------------------
	| Returns form for user to compose a new message.
	| If there is an ID given, then presume that it's
	| a reply, and show the origional message.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "compose":
		
		global $lang;
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		
		// Get a list of buddies...
		$buddys = $core_db->query(
			"SELECT buddy_list.id, buddy_list.buddy_id
			FROM buddy_list
			LEFT JOIN users ON users.id = buddy_list.buddy_id
			WHERE buddy_list.user_id = ". $cfg['user_id'] ."
			AND buddy_list.buddy_id != ". $cfg['pub_id'] ."
			ORDER BY users.clan_id ASC",
			"get buddy list"
		);
		
		// Start off by hiding the prefix, so it can be overriden later!
		$prefix_hide = 1;
		$name_prefix = "";
		$name = "";
		$forward = request_bool("forward");
		$rec_id = 0;
		$recipients_list = "";
		
		// Don't select any names if we're just forwarding!
		if (!$forward) {
			
			$buddy_id = request_int("buddy_id");
			
			if ($buddy_id != 0) {
				$rec_id = $buddy_id;
			}
			
			if ($rec_id == 0) {
				$rec_id = $core_db->lookup(
					"SELECT `from_id` FROM `messages` WHERE `id` = '$id'",
					"Get recipient id.");
			}
			
			// We want to add the reply to recipient to the top.
			if ($rec_id != 0) {
				
				// Is this guy a buddy? If not, add a * infront of his name!
				if (!is_buddy($rec_id)) {
					$name_prefix .= '*';
					$prefix_hide = "";
					$t->set_var('buddy_add',
						"<input type=hidden name=id value=$rec_id><input type=hidden name=ref value='{this_ref}'>".
						"<input alt='{lang_add_to_buddy_list}' type=image border=0 src=themes/{theme_id}/images/icon_add.gif width=10 height=8>\n"
					);
				}
				
				if (!isset($name_prefix)) { $name_prefix = ""; }
				$name = $name_prefix . $core_db->lookup(
					"SELECT name FROM users WHERE id = $rec_id");
			}
		}
		
		$name_prefix = "";
		$recipients_list = "<option value='new'>{$lang['new_recipient']}</option>\n";
		$cc_list = $recipients_list;
					
		if ($rec_id != 0) {
			$recipients_list .= "<option value=" . $rec_id .
			" selected>". $name ."</option>\n";
		}
		
		// Compile them into a string of options.
		while ($row = $core_db->fetch_row($buddys)) {
			
			$name_prefix = "";
			
			// Mark the member from another clan if not from this clan.
			if (!$core_db->lookup("SELECT id FROM users WHERE id = $row[buddy_id] AND clan_id = '{$cfg['clan_id']}'")) {
				$user_clan = $core_db->lookup("SELECT clan_id FROM users WHERE id = '{$row['buddy_id']}'");
				$clan_tag = $core_db->lookup("SELECT tag FROM clans WHERE id = '$user_clan'");
				$name_prefix .= $clan_tag . " ";
			}
			
			$row['name'] = substr_adv($name_prefix . $core_db->lookup("SELECT name FROM users WHERE id = $row[buddy_id]"), 20);
			$option = "<option value=$row[buddy_id]>{$row['name']} &nbsp;</option>\n";
			$cc_list .= $option;
			
			// Don't add the buddy's name twice!
			if ($rec_id != $row['buddy_id']) $recipients_list .= $option;
		}
		
		$buddy_search = str_replace(
			"'?mod=buddy&action=search'",
			"'?mod=buddy&action=search&from_msg=1'",
			$cfg['buddy_search_window_function']
		);
		
		$popup_event_handler = "onChange=\"if(this.value == 'new') $buddy_search\"";
		
		if (!isset($row['recipients_list'])) { $row['recipients_list'] = ""; }
		if ($recipients_list) {
			// Show the buddys list!
			$row['recipients_list'] .= "<select class='formMultilist' name='send_to[]' size='6' multiple $popup_event_handler>$recipients_list</select>";
			$row['carbon_copy_list'] = "<select class='formMultilist' name='cc_to[]' size='6' multiple $popup_event_handler>$cc_list</select>";
		} else {
			// HAHA! You have no friends :p
			$row['lang_recipients_info'] = "{lang_must_add_buddys_first}";
			$row['lang_carbon_copy'] = " ";
			$row['lang_carbon_copy_info'] = " ";
		}
		
		// Is this a reply? If so, is the given ID authentic?
		if ($core_db->lookup("SELECT id FROM messages WHERE id = '$id'", "Check if message is a reply.")) {
			
			$from_id = $core_db->lookup("SELECT from_id FROM messages WHERE id = $id");
			$from = $core_db->lookup("SELECT name FROM users WHERE id = $from_id");
			$date = $core_db->lookup("SELECT DATE_FORMAT(send_date,'{$cfg['sql_date']['full']}') as date FROM messages WHERE id = $id");
			$message = $core_db->lookup("SELECT body FROM messages WHERE id = $id");
			$title = $core_db->lookup("SELECT title FROM messages WHERE id = $id");
			
			// So user can return back to the last message.
			$row['reply_ref'] = "?mod=messages&action=read&id=$id";
			
			// Only set $row['title'] if $title exists!
			if ($title) {
				
				// Add RE: or FW:? Hmm... Decisions, decisions...
				if (isset($forward)) {
					$row['title'] = add_string_prefix("FW: ", $title);
				} else {
					$row['title'] = add_string_prefix("RE: ", $title);
				}
			}
			
			// This bit goes in the 'message' text box...
			$row['body'] = 
				"\n\n\n\n---- {lang_origional_message} ----".
				"\n{lang_from}: $from".
				"\n{lang_date}: $date".
				"\n\n$message";
		}
		
		// Do we want to hide the * prefix info?
		if ($prefix_hide) $row['prefix_info'] = "";
		
		$t->set_array($row);
		
		$t->mparse('Output', 'subTemplate', "messages_master.ihtml");
		
	break;
	
	/* -----------------------------------------------
	| Messages SEND
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "send":
		
		$title = request_string("title");
		$body = request_string("body");
		$send_to = request_array("send_to");
		$cc_to = request_array("cc_to");
		$user_id = request_int("user_id");
		$save = request_bool("save");
		
		if ($send_to) {
			
			// Send a message to each of the recipients!
			foreach ($send_to as $user_id) {
				
				$core_db->query(
					"INSERT messages SET
					to_id = $user_id,
					from_id = {$cfg['user_id']},
					clan_id = {$cfg['clan_id']},
					send_date = now(),
					body = '$body',
					title = '$title',
					unread = '1',
					type = 'I'",
					"send the message!"
				);
				
				$id = mysql_insert_id();
				add_log("Sent Message (ID: $id)");
			}
		}
		
		if ($cc_to) {
			
			// Sent the CC's
			foreach ($cc_to as $user_id) {
				
				$title = add_string_prefix("CC: ", $title);
				
				$core_db->query(
					"INSERT messages SET
					to_id = $user_id,
					from_id = {$cfg['user_id']},
					clan_id = {$cfg['clan_id']},
					send_date = now(),
					body = '$body',
					title = '$title',
					unread = '1',
					type = 'I'",
					"cc the message!"
				);
				
				$id = mysql_insert_id();
				add_log("Sent Message (CC) (ID: $id)");
			}
		}
		
		// Do we want to save a copy?
		if ($save) {
			$core_db->query(
				"INSERT messages SET
				to_id = '{$cfg['user_id']}',
				from_id = '{$cfg['user_id']}',
				clan_id = '{$cfg['clan_id']}',
				send_date = now(),
				body = '$body',
				title = '$title',
				unread = '1',
				type = 'Se'",
				"save the message!"
			);
		}
		
		go_back("msg=s", "?mod=messages&action=inbox");
		
	break;
	
	/* -----------------------------------------------
	| Messages READ
	+ ------------------------------------------------
	| Shows detailed information on a message.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "read":
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$t->set_msg_var();
		
		if ($core_db->lookup("SELECT id FROM messages WHERE id = $id AND type = 'Sa'")) {
			$t->set_block("RowBlock", "message_save");
			$t->set_var("message_save");
		}
		
		// Do we want it set the message as read or unread?
		$unread = (isset($unread) ? 1 : 0);
		$core_db->query("UPDATE messages SET unread = '$unread' WHERE id = $id", "update read/unread status");
		
		$result = $core_db->query(
			"SELECT id, clan_id, title, body, from_id, unread,
			DATE_FORMAT(send_date,'{$cfg['sql_date']['full']}') as date
			FROM messages
			WHERE id = $id",
			"get unread messages"
		);
		
		if ($core_db->get_num_rows($result)) {
			
			$row = cmscode($core_db->fetch_row($result));
			$row['from'] = $core_db->lookup("SELECT name FROM users WHERE id = $row[from_id]", "get name of user");
			
			$ClanInfo = fetch_clan_name($row['clan_id']);
			$row['clan'] = $ClanInfo['tag'] . " " . $ClanInfo['name'];
			$row['clan_url'] = $ClanInfo['url'];
			
			// Hmm, shall I set a link for mark as read... or unread?
			if ($row['unread'] == 1) {
				$row['mark_as'] = "<img src='themes/{theme_id}/images/msg_unread.gif'> <a href='?mod=messages&action=read&id=$row[id]'>{lang_mark_as_read}</a>";
			} else {
				$row['mark_as'] = "<img src='themes/{theme_id}/images/msg_read.gif'> <a href='?mod=messages&action=read&id=$row[id]&unread=1'>{lang_mark_as_unread}</a>";
			}
			
			$t->set_array($row);
		}
		$t->mparse('Output', 'subTemplate', "messages_master.ihtml");
		
	break;
	
	/* -----------------------------------------------
	| Messages SAVE
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "save":
		
		if ($move) {
			// Just move it to the Saved Items folder :)
			$core_db->query("UPDATE messages SET type = 'Sa' WHERE id = $id AND to_id = {$cfg['user_id']}", "move to saved folder");
			
			// Now, go to the Saved Items folder...
			header ("Location:http:?mod=messages&action=saved&msg=mm");
			
		} else {
			// Hmm, gonna have to make a copy...
			
			$result = $core_db->query(
				"SELECT * FROM messages WHERE id = $id AND to_id = {$cfg['user_id']}",
				"get message info"
			);
			
			if ($core_db->get_num_rows()) {
				// Does the message actualy exist?
				
				/*
					MUST addslashes, GPC_magic_quotes
					dosen't come into effect here!
				*/
				$row = addslashes($core_db->fetch_row($result));
				
				$core_db->query(
					"INSERT messages SET
					to_id = '{$row['to_id']}',
					from_id = '{$row['from_id']}',
					clan_id = '{$row['clan_id']}',
					send_date = '{$row['send_date']}',
					body = '{$row['body']}',
					title = '{$row['title']}',
					unread = '{$row['unread']}',
					type = 'Sa'",
					"Save the message."
				);
			}
			// Go back to the message!
			header ("Location:http:?mod=messages&action=read&id=$id&msg=mc");
		}
		
	break;
	
	/* -----------------------------------------------
	| Messages DELETE
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "delete":
		
		$items_deleted = 0;
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach($item as $key => $id) {
				$id = $id + 0;
				if ($id) {
					if ($perm_delete) {
						// Going... Going... Gone!
						$core_db->query(
							"DELETE FROM messages
							WHERE id = '$id'
							AND to_id = '{$cfg['user_id']}'",
							"Permenantly delete messages."
						);
					} else {
						// Just sent it to the Recycle Bin :)
						$core_db->query(
							"UPDATE messages SET type = 'R'
							WHERE id = '$id'
							AND to_id = '{$cfg['user_id']}'",
							"Send to recycle bin."
						);
					}
					$items_deleted++;
				}
			}
		}
		
		// Did I actualy delete anything?
		if ($items_deleted) {
			if ($perm_delete) {
				$msg = (($items_deleted == 1) ? 'pds' : 'pd');
			} else {
				$msg = (($items_deleted == 1) ? 'ds' : 'd');
			}
		}
		
		go_back("msg=$msg", "?mod=messages&action=inbox");
		
	break;
}