<?php

switch($mm->action) {
	
	/*
	+ ------------------------------------------------
	| Help DEFAULT
	+ ------------------------------------------------
	| Shows help in POPUP window.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "default":
		
		$nav->navs[] = $lang["help"];
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$t->set_var('info', $lang['sectionunderconst']);
		
		$t->mparse('Output', 'subTemplate', "popup.ihtml");
		
	break;
}