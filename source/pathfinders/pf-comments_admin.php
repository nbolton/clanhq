<?php

switch ($type) {
	
	case "news":
		
		$news = $core_db->query(
			"SELECT title, body
			FROM news
			WHERE id = '$id'",
			"Get news title and body."
		);
		
		$news = $core_db->fetch_row($news);
		
		$title = ($news['title'] ? substr_adv($news['title'], 20) : substr_adv($news['body'], 20));
		if (!$title) { $title = $lang['na']; }
		
		$nav->navs[] = array(
			"<a href='?mod=$type'>{$lang[$type]}</a>",
			"<a href='?mod=$type&action=admin'>{$lang['admin']}</a>",
			"{$lang['comments']} ($title)",
		);
		
	break;
	
	case "fixtures":
		
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
		
		$nav->navs[] = array(
			"<a href='?mod=$type'>{$lang[$type]}</a>",
			"<a href='?mod=$type&action=admin'>{$lang['admin']}</a>",
			"{$lang['comments']} ({$clan_inf['tag']} - {$match['match_type_name']})",
		);
		
	break;
	
	case "reports":
		
		switch ($origin) {
			
			case "results":
				
				$match = $core_db->query(
					"SELECT fixtures.vs_id, fixtures.vs_tag, fixtures.vs_name,
					fixtures.match_type, match_types.name AS match_type_name
					FROM fixtures
					LEFT JOIN match_types ON match_types.id = fixtures.match_type
					WHERE fixtures.id = '$origin_id'",
					"Get clan name and match type."
				);
				
				$match = $core_db->fetch_row($match);
				$clan_inf = fetch_clan_name($match['vs_id'], $match['vs_tag'], $match['vs_name']);
				
				$nav->navs[] = array(
					"<a href='?mod=$origin'>{$lang[$origin]}</a>",
					"<a hreg='?mod=$origin&action=admin'>{$lang['admin']}</a>",
					"<a href='?mod=reports&action=admin&id=$origin_id&type=$origin'>{$lang[$type]}</a>".
					" ({$clan_inf['tag']} - {$match['match_type_name']})"
				);
				
			break;
		}
		
		$report_title = $core_db->query(
			"SELECT title, body
			FROM reports
			WHERE id = '$id'",
			"Get report body and title."
		);
		
		$report_title = $core_db->fetch_row($report_title);
		if ($report_title['title']) {
			$report_title = substr_adv($report_title['title'], 20);
		} elseif ($report_title['body']) {
			$report_title = substr_adv($report_title['body'], 20);
		} else {
			$report_title = $lang['na'];
		}
		
		$nav->navs[] = array(
			"{$lang['comments']} ($report_title)"
		);
		
	break;
}

?>