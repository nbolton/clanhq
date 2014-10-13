<?php

switch ($type) {
	
	case "results":
		
		$match = $core_db->query(
			"SELECT fixtures.vs_id, fixtures.vs_tag, fixtures.vs_name,
			fixtures.match_type, match_types.name AS match_type_name
			FROM fixtures
			LEFT JOIN match_types ON match_types.id = fixtures.match_type
			WHERE fixtures.id = '$id'",
			"Get clan name and match type."
		);
		
		$match = $core_db->fetch_row($match);
		$clan_inf = fetch_clan_name($match['vs_id'], $match['vs_tag'], $match['vs_name']);
		
		switch (last_action()) {
			
			default:
				$nav->navs[] = array(
					"<a href='?mod=$type'>{$lang[$type]}</a>",
					"<a href='?mod=$type&action=details&id=$id'>{$lang['details']}</a> ".
					"({$clan_inf['tag']} - {$match['match_type_name']})",
					$lang['create_report']
				);
			break;
			
			case "admin";
				$nav->navs[] = array(
					"<a href='?mod=$type'>{$lang[$type]}</a>",
					"<a href='?mod=$type&action=admin'>{$lang['admin']}</a>",
					"<a href='?mod=reports&action=admin&id=$id&type=$type'>{$lang[$mm->module]}</a> ".
					"({$clan_inf['tag']} - {$match['match_type_name']})",
					$lang['create']
				);
			break;
		}
	break;
}

?>