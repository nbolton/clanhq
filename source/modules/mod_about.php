<?php

switch ($mm->action) {
	
	/*
	+ ------------------------------------------------
	| About DEFAULT
	+ ------------------------------------------------
	| Shows info about the clan.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "default":
		
		$core_db->query(
			"SELECT display, tag, info,
			DATE_FORMAT(create_date, '{$cfg['sql_date']['extended']}') AS create_date
			FROM clans WHERE id = '{$cfg['clan_id']}'",
			"Get clan details."
		);
		
		if ($core_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			$nav->navs[] = $lang['about'];
			$row = $core_db->fetch_row();
			$row['info'] = cmscode($row['info']);
			$t->set_array($row);
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
}