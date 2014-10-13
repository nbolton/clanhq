<?php

switch($mm->action) {
	
	/*
	+ ------------------------------------------------
	| Account DEFAULT
	+ ------------------------------------------------
	| Shows bandidth and usage statistics for this
	| clan's account.
	+ ------------------------------------------------
	| Added: v3.9 Beta Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "default":
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		$nav->navs[] = $lang['account_information'];
		
		// What are we going to measure diskspace in?
		switch ($cfg['diskspace_sizetype']) {
			case 'KB'; $div = 1; break;
			case 'MB'; $div = 1024; break;
			case 'GB'; $div = 1048576; break;
		}
		$diskspace = $usg->check_limit('diskspace', "", 2);
		$row['diskspace_used'] = round($diskspace[0] / $div, 2);
		$row['diskspace_left'] = round($diskspace[1] / $div, 2);
		$row['diskspace_limit'] = round($diskspace[2] / $div, 2);
		$row['diskspace_used_percent'] = round($diskspace[0] / $diskspace[2] * 100, 2);
		$row['diskspace_left_percent'] = round($diskspace[1] / $diskspace[2] * 100, 2);
		
		// What are we going to measure bandwidth in?
		switch ($cfg['bandwidth_sizetype']) {
			case 'KB'; $div = 1; break;
			case 'MB'; $div = 1024; break;
			case 'GB'; $div = 1048576; break;
		}
		$bandwidth = $usg->check_limit('bandwidth', "", 2);
		$row['bandwidth_used'] = round(($bandwidth[0] / $div), 2);
		$row['bandwidth_left'] = round(($bandwidth[1] / $div), 2);
		$row['bandwidth_limit'] = round(($bandwidth[2] / $div), 2);
		$row['bandwidth_used_percent'] = round($bandwidth[0] / $bandwidth[2] * 100, 2);
		$row['bandwidth_left_percent'] = round($bandwidth[1] / $bandwidth[2] * 100, 2);
		
		$t->set_array($row);
		$t->mparse('Output', 'subTemplate');
		
	break;
}