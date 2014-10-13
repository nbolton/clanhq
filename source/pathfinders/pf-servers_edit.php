<?php

$name = substr_adv($row['name'], 40);

switch (last_action()) {
	
	default:
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang[$mm->module]}</a>",
			"<a href='?mod=$mm->module&action=details&id=$id'>{$lang['details']}</a> ($name)",
			$lang['edit'],
		);
	break;
	
	case "admin":
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang[$mm->module]}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			"{$lang['edit']} ($name)"
		);
	break;
}

?>