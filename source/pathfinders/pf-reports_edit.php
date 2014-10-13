<?php

switch ($type) {
	
	case "results":
		
		$match = $core_db->query(
			"SELECT fixtures.vs_id, fixtures.vs_tag, fixtures.vs_name,
			fixtures.match_type, match_types.name AS match_type_name
			FROM fixtures
			LEFT JOIN match_types ON match_types.id = fixtures.match_type
			WHERE fixtures.id = '$type_id'",
			"Get clan name and match type."
		);
		
		$match = $core_db->fetch_row($match);
		$clan_inf = fetch_clan_name($match['vs_id'], $match['vs_tag'], $match['vs_name']);
		$title = ($row['title'] ? substr_adv($row['title'], 20) : substr_adv($row['body'], 20));
		if (!$title) { $title = $lang['na']; }
		
		switch (last_action()) {
			
			default:
				$nav->navs[] = array(
					"<a href='?mod=$type'>{$lang[$type]}</a>",
					"<a href='?mod=$type&action=details&id=$type_id'>{$lang['details']}</a> ".
					"({$clan_inf['tag']} - {$match['match_type_name']})",
					"<a href='?mod=reports&action=details&type=$type&id=$id&type_id=$type_id'>{$lang['report']}</a> ($title)",
					$lang['edit']
				);
			break;
			
			case "admin";
				$nav->navs[] = array(
					"<a href='?mod=$type'>{$lang[$type]}</a>",
					"<a href='?mod=$type&action=admin'>{$lang['admin']}</a>",
					"<a href='?mod=reports&action=admin&id=$type_id&type=$type'>{$lang[$mm->module]}</a> ".
					"({$clan_inf['tag']} - {$match['match_type_name']})",
					"{$lang['edit']} ($title)"
				);
			break;
		}
		
	break;
}

?>