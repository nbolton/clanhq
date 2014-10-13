<?php

switch($mm->action) {
	
	case "default": redirect("&action=browse"); break;
	
	/* -----------------------------------------------
	| Buddy ADD
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "add":
		
		if ($id != $cfg['pub_id']) {
			// Don't add public!
			
			$core_db->query(
				"INSERT buddy_list SET
				user_id = '{$cfg['user_id']}',
				buddy_id = '$id'",
				"Add new buddy."
			);
		}
		go_back();
		
	break;
	
	/* -----------------------------------------------
	| Buddy DELETE
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "delete":
		
		$core_db->query(
			"DELETE FROM buddy_list WHERE
			user_id = '{$cfg['user_id']}'
			AND buddy_id = '$id'",
			"Delete existing buddy."
		);
		go_back();
		
	break;
	
	/* -----------------------------------------------
	| Buddy SEARCH
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "search":
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$t->set_msg_var();
		
		if (isset($_GET['from_msg'])) {
			$t->set_var("javascript", "<script>alert('{$lang['new_recipient_help']}');</script>");
		}
		
		$t->mparse('Output', 'subTemplate', "popup.ihtml");
		
	break;
	
	/* -----------------------------------------------
	| Buddy LIST
	+ ------------------------------------------------
	| Displays results from search query
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ --------------------------------------------- */
	
	case "list":
		
		global $input;
		
		$srt->set_array( array('name', 'clan_id; clan'), '', '', 0, $cfg['browsers_limit']);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'RowBlock', 'RBlock');
		$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
		
		if (!isset($input['query'])) { $input['query'] = ""; }
		if (!isset($input['query_refine'])) { $input['query_refine'] = ""; }
		
		$name = ltrim(chop($input['query']));
		$refine = ltrim(chop($input['query_refine']));
		$t->set_var('query', stripslashes(stripslashes($name . ($refine ? ", $refine" : ""))));
		if (!$name) {
			$t->set_var('query', $lang['nothing']);
		}
		$t->set_var('refine', $refine);
		if ($refine) {
			$t->set_block('subTemplate', 'RefineBlock', 'RefBlock');
			$t->set_var('RefBlock', " <a href=javascript:history.back()>Go Back!</a><br>&nbsp;");
		}
		
		if($name) {
			
			$rules = "name != 'Public' and name like '%$name%'" . ($refine ? " and name like '%$refine%'" : "");
			
			$nav->page_index(
				"select COUNT(*) from users where $rules",
				"&input[query]=$name&input[query_refine]=$refine"
			);
			
			$result = $core_db->query(
				"select id, name, clan_id from users where $rules" . $srt->sql(),
				"get name list"
			);
		}
		
		if($core_db->get_num_rows() && $name) {
			while ($row = $core_db->fetch_row($result)) {
				
				$row['clan_tag'] = $core_db->lookup("select tag from clans where id = $row[clan_id]");
				$row['clan_name'] = substr_adv($core_db->lookup("select display from clans where id = $row[clan_id]"), 25);
				
				$is_buddy = ($core_db->lookup("select id from buddy_list where buddy_id = $row[id] and user_id = {$cfg['user_id']}"));
				if (!$is_buddy) {
					$row['buddy_title'] = $lang['add_buddy'] . " ($row[name])";
					$row['buddy'] = 
						"</form><form method=post action=?mod=buddy&action=add>".
						"<input type=hidden name=id value=$row[id]><input type=hidden name=ref value=\"{this_ref}&input[query]=$name&input[query_refine]=$refine\">".
						"<input type=image border=0 src=themes/{theme_id}/images/icon_add.gif alt=\"$row[buddy_title]\" width=10 height=8>\n";
				} else {
					$row['buddy_title'] = $lang['delete_buddy'] . " ($row[name])";
					$row['buddy'] = 
						"</form><form method=post action=?mod=buddy&action=delete>".
						"<input type=hidden name=id value=$row[id]><input type=hidden name=ref value=\"{this_ref}&input[query]=$name&input[query_refine]=$refine\">".
						"<input type=image border=0 src=themes/{theme_id}/images/icon_delete.gif alt=\"$row[buddy_title]\" width=9 height=8>\n";
				}
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_block('subTemplate', 'ResultsBlock', 'RBlock');
			$t->set_var('RBlock', $lang['no_results_from_search']);
		}
		
		$t->mparse('Output', 'subTemplate', "popup.ihtml");
		
	break;
}