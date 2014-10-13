<?php

$panel_uc = "Messages";
	$panel_lc = "messages";
	$prefix = "msg_";
	$handle_name =  $panel_uc."Template";
	$panel_template = "panel_".$panel_lc.".ihtml";
	$block_hanle	= $panel_uc."RowBlock";
	$block_name	= $panel_uc[0]."RBlock";
	
	if (($cfg['user_id'] != $cfg['pub_id']) && ($cfg['show_'.$panel_lc] & 1)) {
		
		$t->set_file($handle_name, $panel_template);
		$t->set_block($handle_name, $block_hanle, $block_name);
		
		for ($i = 1; $i <= $cfg['messages_limit']; $i++) {
			$row['id'] = $i;
			$t->set_array($row, $prefix);
			$t->parse($block_name, $block_hanle, true);
		}
		$t->parse('Output', $handle_name);
		
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