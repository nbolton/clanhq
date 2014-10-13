<?php

$poll = polls_vote_control();
	$t->clear_cache();
	
	if ($cfg['show_poll'] & 1) {
		
		if (!isset($poll['has_voted'])) { $poll['has_voted'] = ""; }
		if (!isset($_REQUEST['spr'])) { $_REQUEST['spr'] = ""; }
		if (!isset($poll['allow_vote'])) { $poll['allow_vote'] = ""; }
		
		if (!$poll['has_voted'] && !$_REQUEST['spr'] && $poll['allow_vote'] && $t->check_privs(8, 'vote')) {
			
			// If the user has never voted, show them the options.
			$t->set_file('pollTemplate', 'polls_options.ihtml');
			$t->set_block('pollTemplate', 'PollRowBlock', 'PRBlock');
			
			$cfg['userlevel'] = $cfg['userlevel'] + 0;
			$cfg['clan_id'] = $cfg['clan_id'] + 0;
			
			$result = $core_db->query(
				"select id, title, info
				from polls_data
				where userlevel <= $cfg[userlevel]
				and clan_id = {$cfg['clan_id']}
				and is_enabled = 'Y'
				order by create_date desc limit 1"
			,("get latest poll") );
			
			if ($core_db->get_num_rows($result)) {
				
				$row = $core_db->fetch_row($result);
				
				$row['title'] = cmscode($row['title']);
				$row['info'] = cmscode($row['info']);
				
				$t->set_array($row, 'poll_', "pollTemplate");
				
				$result = $core_db->query(
					"select id as opt_id, opt
					from polls_options
					where poll_id = $row[id]
					and clan_id = {$cfg['clan_id']}
					order by opt_num asc"
				,("get poll options") );
				
				if (mysql_num_rows($result)) {
					while ($row = mysql_fetch_array($result)) {
						$t->set_array($row, 'poll_');
						$t->parse('PRBlock', 'PollRowBlock', true);
					}
				} else {
					$t->set_var('poll_opt', $lang['no_polls_options']);
					$t->parse('PRBlock', 'PollRowBlock', true);
				}
			} else {
				$t->set_block('pollTemplate', 'PollShowBlock', 'PSBlock');
				$t->set_var('PSBlock', $lang['no_poll_sb']);
			}
			
			$t->parse('Output', 'pollTemplate');
			
		} elseif ($t->check_privs(8, 'results')) {
			
			// If the user has voted before, show the results
			
			$t->set_file('pollTemplate', 'polls_results.ihtml');
			$t->set_block('pollTemplate', 'PollRowBlock', 'PRBlock');
			
			$cfg['userlevel'] = $cfg['userlevel'] + 0;
			$cfg['clan_id'] = $cfg['clan_id'] + 0;
			
			$result = $core_db->query(
				"select id, title, info
				from polls_data
				where userlevel <= $cfg[userlevel]
				and clan_id = {$cfg['clan_id']}
				and is_enabled = 'Y'
				order by create_date desc limit 1"
			,("get latest poll") );
			
			if ($core_db->get_num_rows($result)) {
				
				$row = $core_db->fetch_row($result);
				
				$row['title'] = cmscode($row['title']);
				$row['info'] = cmscode($row['info']);
				
				$poll_id = $row['id'];
				$row['vote_link'] = (($poll['has_voted'] || !$poll['allow_vote']) ? FALSE : TRUE );
				if(!$core_db->lookup("select id from polls_options where poll_id = $poll_id and clan_id = {$cfg['clan_id']} and votes = 0")) $row['info_block'] = FALSE;
				$t->set_array($row, 'poll_', "pollTemplate");
				
				$result = $core_db->query(
					"select votes
					from polls_options
					where poll_id = $poll_id
					and clan_id = {$cfg['clan_id']}"
				) or die ( mysql_error_page("get poll votes for adding total") );
				
				if (!isset($total_votes)) { $total_votes = 0; }
				if (mysql_num_rows($result)) {
					while ($row = mysql_fetch_array($result)) {
						$total_votes += $row['votes'];
					}
				}
				
				$result = $core_db->query(
					"select id as opt_id, opt, votes
					from polls_options
					where poll_id = $poll_id
					and clan_id = {$cfg['clan_id']}
					order by votes desc"
				,("get poll options") );
				
				$were_no_votes = FALSE;
				if (mysql_num_rows($result)) {
					if ($total_votes > 0) {
						while ($row = mysql_fetch_array($result)) {
							if($row['votes'] > 0) {
								$percentage = $row['votes'] / $total_votes * 100;
								$row['opt_bar_width'] = round($percentage, 0) / 2;
								$row['opt_percent'] = round($percentage, 2) . '%';
								$row['opt'] = stripslashes($row['opt']);
								
								$t->set_array($row, 'poll_');
								
								if ((strlen($row['opt']) <= 9) && ($row['opt_percent'] <= 20) ||
										(strlen($row['opt']) <= 7) && ($row['opt_percent'] <= 40) ||
										(strlen($row['opt']) <= 4) && ($row['opt_percent'] <= 60) ||
										(strlen($row['opt']) <= 2) && ($row['opt_percent'] <= 80)) {
									
									$row['opt_sm'] = $row['opt'] . '&nbsp;&nbsp;';
									$row['opt_big'] = false;
									
								} else {
									$row['opt_big'] = $row['opt'] . '...';
									$row['opt_sm'] = false;
								}
								$t->set_var('poll_opt_sm',$row['opt_sm']);
								$t->set_var('poll_opt_big',$row['opt_big']);
								
								$t->parse('PRBlock', 'PollRowBlock', true);
							}
						}
					} else {
						$t->parse('PRBlock', 'PollRowBlock', false);
						$t->set_var('PRBlock', "<tr><td class=bodyText>" . $lang['no_poll_votes'] . "</td></tr>");
						$were_no_votes = TRUE;
					}
				} else {
					$t->parse('PRBlock', 'PollRowBlock', false);
					if(!$were_no_votes) $t->set_var('PRBlock', "<tr><td class=bodyText>" . $lang['no_poll_options'] . "</td></tr>");
				}
			} else {
				$t->set_block('pollTemplate', 'PollShowBlock', 'PSBlock');
				$t->set_var('PSBlock', $lang['no_poll_sb']);
			}
			
			$t->parse('Output', 'pollTemplate');
			
		} else {
			$t->kill_block('poll_sidebar', FALSE, $parent);
		}
		
		$t->kill_block('poll_maximize_button', FALSE, $parent);
		$t->kill_block('poll_maximize_block', FALSE, $parent);
		$return_code = 2;
		
	} elseif (($cfg['show_poll'] & 2) && ($cfg['user_id'] != $cfg['pub_id'])) {
		
		$t->kill_block('poll_minimize_button', FALSE, $parent);
		$t->kill_block('poll_minimize_block', FALSE, $parent);
		$return_code = 1;
		
	} else {
		$t->kill_block('poll_sidebar', FALSE, $parent);
		$return_code = 0;
	}
	if ($cfg['user_id'] == $cfg['pub_id']) $t->kill_block('poll_modes', FALSE, $parent);

?>