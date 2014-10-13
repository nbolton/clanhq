<?php

switch($mm->action) {
	
	case "default": redirect("&action=browse"); break;
	
	/*
	+ ------------------------------------------------
	| Trophies BROWSE
	+ ------------------------------------------------
	| Added: v4.0 Delta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "browse":
		
		$result = $core_db->query(
			"SELECT id, image_id, title, info,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') AS date
			FROM trophies WHERE clan_id = '{$cfg['clan_id']}'
			ORDER BY priority ASC",
			"Get list of trophies."
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			
			$nav->navs[] = $lang["trophies"];
			
			while ($row = cmscode($core_db->fetch_row($result))) {
				
				if ($row['image_id']) {
					$row['image'] = "<img src='?mod=files&action=getimage&id={$row['image_id']}' border=0>";
				} else {
					$row['image'] = $lang['no_image_selected'];
				}
				
				$t->set_array($row);
				
				set_admin_links('admin_links',
					array(
						"{$lang['edit']}; ?mod=$mm->module&action=edit&id={$row['id']}",
						"{$lang['delete']}; ?mod=$mm->module&action=delete&id={$row['id']}; true; delete_item_q"
					)
				);
				
				$t->parse('RBlock', 'RowBlock', TRUE);
			}
		} else {
			$t->set_msg('no_trophies', TRUE);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Trophies ADMIN
	+ ------------------------------------------------
	| Shows all servers as list with admin features.
	+ ------------------------------------------------
	| Added: v4.0 Delta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		$srt->set_array(
			array('title', 'priority', 'create_id; creator', 'create_date; created', 'info'),
			'priority', 'asc', 0, $cfg['browsers_limit']
		);
		
		$result = $core_db->query(
			"SELECT id, image_id, info, create_id, priority, title,
			DATE_FORMAT(create_date,'{$cfg['sql_date']['full']}') as created
			FROM trophies WHERE clan_id = {$cfg['clan_id']}" . $srt->sql(),
			"Get list of trophies."
		);
		
		if ($core_db->get_num_rows($result)) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			$nav->page_index("SELECT COUNT(*) FROM trophies WHERE clan_id = {$cfg['clan_id']}");
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['trophies']}</a>",
				$lang['admin']
			);
			
			$i = 1;
			
			while ($row = $core_db->fetch_row($result)) {
				
				// So user can tab through the fields easier
				$row['tabindex'] = $i++;
				
				// Well we don't expect the info to be tiny...
				$row['info'] = substr_adv(cmscode($row['info']), 20, FALSE);
				
				if ($row['create_id']) {
					$row['creator'] = $core_db->lookup("SELECT name FROM users WHERE id = '{$row['create_id']}'");
				}
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', TRUE);
			}
		} else {
			$t->set_msg('no_trophies', TRUE, TRUE);
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Trophies CREATE
	+ ------------------------------------------------
	| Added: v4.0 Delta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang['trophies']}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			$lang['create']
		);
		
		$usg->check_limit('servers');
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		
		$files_db = new DBDriver;
		$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
		
		$row['select_image'] = $files_db->sqllistbox(
			'input[image_id]', "SELECT id, filename FROM files WHERE
			clan_id = {$cfg['clan_id']} AND cat_id = 9", "", TRUE
		);
		
		$t->set_array($row, "", "", "");
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Trophies EDIT
	+ ------------------------------------------------
	| Added: v4.0 Delta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		$mm->ifempty($id, "?mod=$mm->module&action=admin");
		
		$result = $core_db->query(
			"SELECT id, title, image_id, info
			FROM trophies
			WHERE id = '$id'",
			"Get details for edit."
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			$row = $core_db->fetch_row();
			
			$row['title'] = cmscode($row['title'], 'cmscode');
			$row['info'] = cmscode($row['info'], 'cmscode');
			
			$files_db = new DBDriver;
			$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
			
			$row['select_image'] = $files_db->sqllistbox(
				'input[image_id]', "SELECT id, filename FROM files WHERE
				clan_id = {$cfg['clan_id']} AND cat_id = 9", $row['image_id'], TRUE
			);
			
			$t->set_array($row, "", "", "");
			
			include "source/pathfinders/pf-{$mm->module}_{$mm->action}.php";
			
		} else {
			$t->set_msg('no_trophies_id');
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Trophies INSERT
	+ ------------------------------------------------
	| Added: v4.0 Delta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "insert":
		
		$title = cmscode($input['title'], 'cmscode');
		$info = cmscode($input['info'], 'cmscode');
		$image_id = $input['image_id'] + 0;
		$priority = $core_db->lookup("SELECT COUNT(*) FROM trophies WHERE clan_id = '{$cfg['clan_id']}'") + 1;
		
		$core_db->query(
			"INSERT trophies SET
			title 			=	'$title',
			image_id		=	'$image_id',
			info				=	'$info',
			priority 		= '$priority',
			clan_id 		=	'{$cfg['clan_id']}',
			create_id 	=	'{$cfg['user_id']}',
			edit_id 		=	'{$cfg['user_id']}',
			create_date	=	now(),
			edit_date 	=	now()",
			"Insert info into db."
		);
		
		$id = $core_db->get_insert_id();
		
		if ($files->insert('upload_image', 9)) {
			$core_db->query(
				"UPDATE trophies SET
				image_id = '$files->file_id'
				WHERE id = '$id'
				AND clan_id = '{$cfg['clan_id']}'",
				"Changed image_id for trophie."
			);
		}
		
		add_log("Inserted Trophie (ID: $id)");
		go_back("msg=i", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Trophies UPDATE
	+ ------------------------------------------------
	| Added: v4.0 Delta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "update":
		
		$title = cmscode($input['title'], 'cmscode');
		$info = cmscode($input['info'], 'cmscode');
		$image_id = $input['image_id'] + 0;
		
		$core_db->query(
			"UPDATE trophies SET
			title 		=	'$title',
			info			=	'$info',
			image_id	=	'$image_id',
			edit_id 	=	'{$cfg['user_id']}',
			edit_date =	now()
			WHERE id 	=	'$id'
			AND clan_id	=	'{$cfg['clan_id']}'",
			"Update existing info."
		 );
		
		if ($files->insert('upload_image', 9)) {
			$core_db->query(
				"UPDATE trophies SET
				image_id = '$files->file_id'
				WHERE id = '$id'
				AND clan_id = '{$cfg['clan_id']}'",
				"Changed image_id for trophie."
			);
		}
		
		add_log("Updated Trophie (ID: $id)");
		go_back("msg=u", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Trophies DELETE
	+ ------------------------------------------------
	| Added: v4.0 Delta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "delete":
		
		if($item_order) {
			foreach($item_order as $id => $value) {
				$id = $id + 0;
				$value = $value + 0;
				$core_db->query(
					"UPDATE trophies SET priority = '$value'
					WHERE id = '$id'
					AND clan_id = {$cfg['clan_id']}",
					"Update the priority for each item."
				);
				add_log("Updated Trophie (ID: $key)");
			}
		}
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach($item as $key => $id) {
				$id = $id + 0;
				$core_db->query(
					"DELETE FROM trophies
					WHERE id = '$id'
					AND clan_id = {$cfg['clan_id']}",
					"Delete checked items."
				);
				add_log("Deleted Trophie (ID: $id)");
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