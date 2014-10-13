<?php

$title = ($row['title'] ? substr_adv($row['title'], 20) : substr_adv($row['body'], 20));

switch (last_action()) {
	
	default:
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang[$mm->module]}</a>",
			"<a href='?mod=$mm->module&action=details&id=$id'>{$lang['details']}</a> ($title)",
			$lang['edit'],
		);
	break;
	
	case "admin":
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang[$mm->module]}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			"{$lang['edit']} ($title)",
		);
	break;
}

?>