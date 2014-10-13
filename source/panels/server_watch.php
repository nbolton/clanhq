<?php

$panel_uc = "ServerWatch";
	$panel_lc = "server_watch";
	$prefix = "sw_";
	$handle_name =  $panel_uc."Template";
	$panel_template = "panel_".$panel_lc.".ihtml";
	$block_hanle	= $panel_uc."RowBlock";
	$block_name	= $panel_uc[0].$panel_uc[1]."RBlock";
	
	if ($cfg['show_'.$panel_lc] & 1) {
		
		$t->set_file($handle_name, $panel_template);
		$t->set_block($handle_name, $block_hanle, $block_name);
		
		$result = $core_db->query(
			"SELECT id, ip, port, type
			FROM servers
			WHERE clan_id = {$cfg['clan_id']}
			AND id = '$cfg[select_server]'
			ORDER BY priority ASC",
			"get servers"
		);
		
		if ($core_db->get_num_rows()) {
			
			$row = $core_db->fetch_row();
			if ($cfg['user_id'] == $cfg['pub_id']) $row['buddies_on'] = "";
			$t->set_array($row, $prefix);
			$t->parse($block_name, $block_hanle);
			
		} else {
			// No server was found matching the selected server in the config array!
			
			if ($cfg['user_id'] != $cfg['pub_id']) {
				// For members, simply attach a link so they can correct it them selfs.
				$lang['server_not_found'] .= "<br><img height=5 width=0><br>[ <a href=?mod=settings&action=private><b>{lang_change}</b></a> ]";
				
			} else {
				// Otherwise, presume they are public and ask for them to contact the clan.
				$lang['server_not_found'] .= "<br><img height=7 width=0><br>{lang_server_fix_contact}";
			}
			$t->set_var($block_name, $lang['server_not_found']);
		}
		
		$t->parse($block_name, $handle_name);
		
		$t->kill_block($panel_lc.'_maximize_button', FALSE, $parent);
		$t->kill_block($panel_lc.'_maximize_block', FALSE, $parent);
		$return_code = 2;
		
	} elseif (($cfg['show_'.$panel_lc] & 2) && ($cfg['user_id'] != $cfg['pub_id'])) {
		
		$t->kill_block($panel_lc.'_minimize_button', FALSE, $parent);
		$t->kill_block($panel_lc.'_minimize_block', FALSE, $parent);
		$return_code = 1;
		
	} else {
		
		$t->kill_block($panel_lc, FALSE, $parent);
		$return_code = 0;
	}
	if ($cfg['user_id'] == $cfg['pub_id']) $t->kill_block($panel_lc.'_modes', FALSE, $parent);

?>