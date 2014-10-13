<?php

class Comments {
	
	/* -----------------------------------------------
	| The comments class sets up everything needed to
	| show comments in the right places in diffrent
	| modules.
	+ --------------------------------------------- */
	
	var $this_id			= 0;
	var $num_comments	=	0;
	var $preview			=	0;
	
	function Comments($id = 0) {
		
		/* -----------------------------------------------
		| Sets all the needed variables at start of sript.
		+ --------------------------------------------- */
		
		global $preview;
		
	 	$this->this_id = $id + 0;
	 	$this->set_COUNT();
	 	$this->preview = $preview;
	}
	
	function set_comments($id = 0) {
		
		/* -----------------------------------------------
		| Sets all comments in one block - can be used
		| for several diffrent modules.
		 + --------------------------------------------- */
		
		global $mm, $t, $core_db, $cfg, $lang, $type, $type_id;
		
		if (!$id) {
			$id =	$this->this_id;
		}
		
		$t->set_block('subTemplate', 'ComBlock', 'CBlock');
		
		if(!$this->preview && $t->check_privs(9, 'browse') && $this->num_comments) {
			
			$result = $core_db->query(
				"SELECT id, create_id, clan_id, item_id, comments, avatar, name,
				DATE_FORMAT(create_date, '{$cfg['sql_date']['full']}') as date
				FROM comments
				WHERE userlevel <= $cfg[userlevel]
				AND clan_id = {$cfg['clan_id']}
				AND item_id = $id
				AND module_id = $mm->module_id
				ORDER BY create_date ASC",
				"Get comments for this module."
			);
			
			while ($row = $core_db->fetch_row($result)) {
				
				// Don't allow users to use html.
				$row['name'] = strip_tags($row['name']);
				$row['comments'] = strip_tags(stripslashes($row['comments']));
				
				$row = cmscode($row);
				$row['c_id'] = $row['id'];
				if (!$row['name']) {
					$row['name'] = $core_db->lookup(
						"SELECT name FROM users WHERE id = '{$row['create_id']}'",
						"Find username from ID."
					);
				}
				$do_link = ($row['create_id'] != $cfg['pub_id']);
				
				if ($row['name']) {
					if ($do_link) {
						$author = "<a href=?mod=members&action=details&id={$row['create_id']}>{$row['name']}</a>";
					} else {
						$author = $row['name'];
					}
				} else {
					$author = $lang['unknown_author'];
				}
				
				$row['c_info'] = "[ $lang[created_by] <b>$author</b> - <b>$row[date]</b> ]";
				
				$avatar_id = $core_db->lookup(
					"SELECT avatar_id FROM users WHERE id = '{$row['create_id']}'",
					"Get avatar_id for the user who created the comment."
				);
				
				$row['avatar_id'] = ((($row['avatar'] == 'Y') && check_sql_file($avatar_id)) ? $avatar_id : "");
				$row['quote'] = get_quote($row['create_id']);
				$t->set_array($row);
				
				set_admin_links('comment_admin_links',
					array(
						"$lang[edit]; ?mod=comments&action=edit&type=$cfg[mod]&type_id=$id&id=$row[id]&origin=$type&origin_id=$type_id",
						"$lang[delete]; ?mod=comments&action=delete&type=$cfg[mod]&id={$row['id']}; true; delete_item_q"
					)
				);
				$t->parse('CBlock', 'ComBlock', true);
			}
		} else {
			// Don't display anything!
			$t->set_var('CBlock');
		}
	}
	
	function set_COUNT($id = 0, $module = 0) {
		
		/* -----------------------------------------------
		| Sets the number of comments for this item where
		| an ID match and module match is found.
		 + --------------------------------------------- */
		 
		global $core_db, $mm, $cfg;
		
		if (!$id) {
			$id =	$this->this_id;
		}
		
		if (!$module) {
			$module = $mm->module_id;
		}
		
		$this->num_comments = $core_db->lookup(
			"SELECT COUNT(*) FROM comments
			WHERE item_id = '$id'
			AND module_id = '$module'",
			"Get the number of comments for a certain element."
		);
	}
}