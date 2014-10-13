<?php

switch($mm->action) {
	
	case "default": redirect("&action=login"); break;
	
	/*
	+ ------------------------------------------------
	| Security LOGIN
	+ ------------------------------------------------
	| Shows login screen.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
		case "login":
		
		$t->set_file('subTemplate');
		
		if (isset($_REQUEST['e'])) {
			if ($_REQUEST['e']) {
				// I'm sorry, we dont like you :)
				$nav->navs[] = array(
					"<a href='?mod=security&action=login'>{$lang['login']}</a>",
					$lang['retry']
				);
			}
		} else {
			$t->set_block("subTemplate", "bad_pass");
			$t->set_var("bad_pass");
			$nav->navs[] = $lang['login'];
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Security FORPASS
	+ ------------------------------------------------
	| Resets users password and emails it to the email
	| address specified.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "forpass":
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		
		$email = $input['email'];
		$go = $input['go'];
		
		if ($go && $email) {
			// If user has come from forgotten password form
			
			$email = ($email);
			$user_id = $core_db->lookup("SELECT id FROM users WHERE email = '$email' AND clan_id = {$cfg['clan_id']}");
			
			if ($user_id) {
				// If email address exists in database
				
				$password = substr(md5(uniqid(microtime(),1)), 0, 10);
				$username = $core_db->lookup("SELECT name FROM users WHERE id = '$user_id'");
				$clan = $core_db->lookup("SELECT name FROM clans WHERE id = '$user_id'");
				
				$core_db->query ("UPDATE users SET password = md5('$password') WHERE id = $user_id", "reset pass");
				
				sendemail(
					$email,
					"Your New CMS Password",
					"<style type=\"text/css\"><!-- $cfg[error_style] --></style>".
					"You have recived this email from the <strong>Forgotten Password</strong> feature on your clan's CMS ($HTTP_HOST).\n\n".
					"<br>Username: <b>$username</b>\n".
					"<br>Password: <b>$password</b>\n<br>(we reccomend you change this once logged in)\n".
					"<br>Login URL: <b><a href=http://$HTTP_HOST/?mod=security&action=login>http://$HTTP_HOST/?mod=security&action=login</a></b>".
					"<p>\n\nThankyou for using CMS. If you require further assistance, ".
					"pm r3n in #chq-cms on Quakenet or email r3n at <a href=mailto:r3n@clan-hq.com>r3n@clan-hq.com</a>"
				);
				
				$t->kill_block("bad_email", "", "subTemplate");
				
				add_log("Reset & Sent Password to $email.");
				
				$nav->navs[] = array(
					"<a href='?mod=security&action=forpass'>{$lang['forpass']}</a>",
					$lang['sent']
				);
				
			} else {
				
				$nav->navs[] = array(
					"<a href='?mod=security&action=forpass'>{$lang['forpass']}</a>",
					$lang['error']
				);
				
				$t->set_var(
					'site_error',
					"<table width=100% border=0 cellspacing=0 cellpadding=2><tr>
					<td width=1><img src=themes/$cfg[theme_id]/images/error-icon.gif></td>
					<td class=bodyText><b><img src=themes/$cfg[theme_id]/images/warning.gif><br>$lang[emaildosentexist]</td>
					</tr></table>"
				);
				
				$t->kill_block("passemailsent", "", "subTemplate");
			}
		} else {
			$nav->navs[] = $lang['forpass'];
			$t->kill_block("bad_email", "", "subTemplate");
			$t->kill_block("passemailsent", "", "subTemplate");
		}
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Security AUTH
	+ ------------------------------------------------
	| Authorises username against password, if good
	| sets session and brings user to HOME.
	| If bad, user is returned to the login screen
	| with an error message.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "auth":
		
		$input = $_REQUEST["input"];
		$username = ($input['username']);
		$password = substr(md5($input['password']), 0, 30);
		
		$result = $core_db->query("SELECT id, clan_id FROM users WHERE name = '$username' ".
			"AND password = '$password' and clan_id = {$cfg['clan_id']}");
		
		if ($core_db->get_num_rows()) {
			
			$array = $core_db->fetch_row($result);
			
			$cfg['sesh_id'] = md5(uniqid(microtime(),1));
			$cfg['user_id'] = $array['id'];
			$cfg['clan_id'] = $array['clan_id'];
			
			$core_db->query(
				"DELETE FROM sessions WHERE user_id = ". $cfg["user_id"],
				"Remove any existing sessions."
			);
			
			$core_db->query(
				"INSERT sessions SET
				sesh_id = '{$cfg['sesh_id']}',
				user_id = '{$cfg['user_id']}',
				clan_id = '{$cfg['clan_id']}',
				user_ip = '". $_SERVER["REMOTE_ADDR"] ."',
				lastaction = now(),
				login_date = now()"
			);
			
			$_SESSION["id"] = $cfg['sesh_id'];
			//setcookie("session", $cfg['sesh_id'] , time() + 3600 * 12 * 31 , '/');
			// setcookie(Name, Value, Expire, Path)
			
			$core_db->query("UPDATE users SET lastlogin = now(), ".
				"logged_in = 'Y' WHERE id = {$cfg['user_id']}");
			
			add_log('Logged In');
			
			header("Location: ./");
			
		} else {
			
			$cfg['user_id'] = $core_db->lookup("SELECT id FROM users WHERE name='$username'");
			add_log('Bad username/password!');
			header("location: ./?mod=security&e=1");
		}
	break;
	
	/*
	+ ------------------------------------------------
	| Security LOGOUT
	+ ------------------------------------------------
	| Log user out and destroy session in DB.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "logout":
		
		$core_db->query("UPDATE users SET logged_in = 'N' WHERE id = {$cfg['user_id']}");
		$core_db->query("DELETE FROM sessions WHERE sesh_id = '{$cfg['sesh_id']}'");
		add_log('Logged Out');
		
		header ("Location:http:?");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Security DENIED
	+ ------------------------------------------------
	| Brings user to an "Access Denied" page and shows
	| the denied module and action.
	+ ------------------------------------------------
	| Added: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "denied":
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		
		$t->set_var('access_denied_info', $lang['access_denied_info']);
		$t->set_var('denied_module', ucfirst($lang[$core_db->lookup("SELECT name FROM modules WHERE id = $id")]));
		$t->set_var('denied_action', ucfirst($lang[$msg]));
		
		$t->mparse('Output', 'subTemplate');
		
	break;
}