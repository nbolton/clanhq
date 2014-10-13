<?php

switch (last_action()) {
	
	default:
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang[$mm->module]}</a>",
			"<a href='?mod=$mm->module&action=details&id=$id'>{$lang['details']}</a> ".
			"({$row['clan_tag']} - {$row['match_type_name']})",
			$lang['edit'],
		);
	break;
	
	case "admin":
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang[$mm->module]}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			"{$lang['edit']} ({$row['clan_name']} - {$row['match_type_name']})",
		);
	break;
}

?>