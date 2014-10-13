<?php

// CMS is not accessable via the server's IP address.
if (isset($_SERVER['SERVER_ADDR'])) {
	if (($_SERVER['HTTP_HOST'] == $_SERVER['SERVER_ADDR']) || !$_SERVER['HTTP_HOST']) {
		header("Location: http://{$cfg['cps_host']}");
		exit;
	}
}

if ($cfg['clan_name'] == "www") {
	// Feature allows users to prefix 'www.' infront of the URL for their CMS.
	
	$clan_name = $core_db->lookup(
		"SELECT name FROM clans
		WHERE domain = '{$_SERVER['HTTP_HOST']}'",
		"Find domain in clan DB."
	);
	
	if (!$cfg['clan_name'] = $clan_name) {
		// Domain does not belong to clan, check if it's a clan-hq domain...
		$host_name_segments = explode('.', $_SERVER['HTTP_HOST']);
		if (!in_array($host_name_segments[1], array("clanscape.net"))) {
			$cfg['clan_name'] = $host_name_segments[1];
		}
	}
	
} elseif (!in_array($_SERVER['HTTP_HOST'], $cfg['dev_servers'])) {
	// Make sure the user can only use the www. prefix for custom domains.
	if ($_SERVER['HTTP_HOST'] != $clan_inf['domain']) {
		if (!preg_match("/(" . implode("|", $cfg['system_hosts']) . ")/i", $_SERVER['HTTP_HOST'])) {
			$true_host = explode(".", $_SERVER['HTTP_HOST']);
			if ($true_host[0] == "www") { array_shift($true_host); }
			
			// If host isn't the same as the intended host, redirect.
			if ($_SERVER['HTTP_HOST'] != ("www." . implode(".", $true_host))) {
				header("Location:http://www." . implode(".", $true_host) . $_SERVER['REQUEST_URI']);
				exit;
			}
		}
	}
}

$alias = $core_db->lookup(
	"SELECT target FROM aliases
	WHERE alias = '{$cfg['clan_name']}'
	OR alias = '{$_SERVER['HTTP_HOST']}'",
	"Check to see if this is an alias for another account."
);

if ($alias) { $cfg['clan_name'] = $alias; }

$clan_inf_result = $core_db->query(
	"SELECT id AS clan_id, domain FROM clans
	WHERE name = '{$cfg['clan_name']}'",
	'Get clan ID and domain based on name.'
);

if ($core_db->get_num_rows($clan_inf_result)) {
	$cfg = array_merge($cfg, $core_db->fetch_row($clan_inf_result));
}

if ($cfg['domain_name'] && !$cfg['dev_mode']) {
	// Ok, so clan does have a domain name.
	
	if ($_SERVER['HTTP_HOST'] != $domain_name) {
		// Send them to their domain name.
		header("Location:http://{$domain_name}{$_SERVER['REQUEST_URI']}");
		exit;
	}
}

if ($cfg['fileserver']) {
	$cfg['clan_name'] = "FileServer";
	$cfg['clan_type'] = "FileServer";
	$cfg['clan_id'] = "1";
	$cfg['user_id'] = "1";
}

if (!isset($cfg['clan_id'])) {
	$cfg['clan_id'] = "";
}

// Check to see if the account still exists...
if (!$cfg['clan_id'] && !$cfg['fileserver']) {
	if ($cfg['clan_name']) {
		$removed = TRUE;
	} else {
		header("Location:http://" . $cfg['cps_host'] . "/cms");
		exit;
	}
}

if (!isset($removed)) {
	$banned = $core_db->lookup(
		"SELECT reason FROM banned
		WHERE ip = '{$_SERVER['REMOTE_ADDR']}'",
		"Search for banned IP address."
	);
	
	if (!isset($banned)) {
		$expired = $core_db->lookup(
			"SELECT display AS clan_name FROM clans
			WHERE expiry_date <= NOW()
			AND id = '{$cfg['clan_id']}'",
			"Check if account has expired."
		);
		
		if (!isset($expired)) {
			$disabled_result = $core_db->query(
				"SELECT display AS clan_name,
				disabled AS reason FROM clans
				WHERE id = '{$cfg['clan_id']}'
				AND disabled != ''",
				"Check if account is disabled."
			);
			
			if ($core_db->get_num_rows($disabled_result)) {
				$disabled = $core_db->fetch_row($disabled_result);
			}
		}
	}
}

if (isset($_GET['bypass'])) {
	$Bypass = $_GET['bypass'];
} else {
	$Bypass = "";
}

if ((isset($disabled) || isset($expired) || isset($banned) || isset($removed)) && ($Bypass != $cfg['bypass_pw'])) {
	
	if (isset($_SERVER['HTTP_REFERER'])) {
		$referer_explode = explode("?", $_SERVER['HTTP_REFERER']);
		if ($referer = array_shift($referer_explode)) {
			$already_alerted = $core_db->lookup(
				"SELECT COUNT(*) FROM {$cfg['dblogs']}.disabled_alerts
				WHERE referer = '$referer' AND
				clan_id = '{$cfg['clan_id']}'",
				"Check to see if we have already been alerted."
			);
			
			if (!$already_alerted) {
				$core_db->lookup(
					"INSERT {$cfg['dblogs']}.disabled_alerts
					SET referer = '$referer',
					clan_id = '{$cfg['clan_id']}'",
					"Insert new log for this referer."
				);
			}
		}
	}
	
	if (!isset($advert_img)) $advert_img = "";
	if (!isset($already_alerted)) $already_alerted = "";
	
	if (stristr($_SERVER['REQUEST_URI'], "action=getimage")) {
		$filename = "./images/system/clan_hq_badge.gif";
		$handle = fopen($filename, "r");
		$advert_img = fread($handle, filesize($filename));
	}
	
	if (isset($banned) && $banned) {
		$mm->halt(
			"Sorry, your IP address ({$_SERVER['REMOTE_ADDR']}) ".
			"has been banned from this service ($banned).",
			"User Banned", "", $advert_img, $already_alerted
		);
	}
	
	if (isset($disabled) && $disabled) {
		$mm->halt(
			"This CMS account ({$disabled['clan_name']}) has<br>   \nbeen disabled ".
			"due to the following reason...<br><br>\n\n{$disabled['reason']}",
			"Account Disabled", "", $advert_img, $already_alerted
		);
	}
	
	if (isset($expired) && $expired) {
		$mm->halt(
			"This CMS account ($expired) has<br>   \nexpired and is nolonger operational.".
			"<br><br>   \n\nTo reniew this site's subscription, please visit the blow link...",
			"Account Expired", "", $advert_img, $already_alerted
		);
	}
	
	if (isset($removed) && $removed) {
		$mm->halt(
			"Account *{$cfg['clan_name']}* does not exist!",
			"Non-existant Account", "", $advert_img, $already_alerted
		);
	}
	
	echo $advert_img;
	exit;
}

// Add clan info to configuration array.
$cfg = array_merge($cfg, $core_db->fetch_row($core_db->query(
	"SELECT clans.clan_type, clans.display AS site_name, clans.account,
	clans.pub_id, clan_types.description AS clan_type_desc
	FROM clans LEFT JOIN clan_types ON clan_types.id = clans.clan_type
	WHERE clans.id = '{$cfg['clan_id']}'",
	"Select various information for this clan."
)));

if (isset($_SESSION["id"])) {
	if ($cfg['sesh_id'] = $_SESSION["id"]) {
		
		$cfg['user_id'] = $core_db->lookup(
			"SELECT user_id FROM sessions
			WHERE sesh_id = '{$cfg['sesh_id']}'",
			'Find member which matches the session ID.'
		);
		
		if ($cfg['user_id']) {
			
			// Add user info to configuration array.
			$cfg = array_merge($cfg, $core_db->fetch_row($user_inf = $core_db->query(
				"SELECT DATE_FORMAT(users.lastlogin, '{$cfg['sql_date']['full']}') AS lastlogin,
				users.clan_id AS sesh_clan_id, users.name AS username, users.userlevel,
				sessions.user_ip, users.admin, users.access AS access_protocol
				FROM users LEFT JOIN sessions ON sessions.user_id = '{$cfg['user_id']}'
				WHERE users.id = '{$cfg['user_id']}' AND sessions.sesh_id = '{$cfg['sesh_id']}'",
				"Get various information on user."
			)));
			
			// For development site, clan name can be switched while logged in.
			if ($cfg['clan_id'] != $cfg['sesh_clan_id']) {
				setcookie("session");
				header("Location:http:{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
			}
			
			$core_db->query(
				"UPDATE users SET lastaction = now()
				WHERE id = {$cfg['user_id']}",
				"Set new last action for user."
			);
			
		} else {
			// If DB RECORD does NOT exist...
			$cfg['user_id'] = $cfg['pub_id'];
			$cfg['userlevel'] = 1;
		}
	} else {
		// If COOKIE doesnt have correct value...
		$cfg['user_id'] = $cfg['pub_id'];
		$cfg['userlevel'] = 1;
	}
} else {
	// If COOKIE does NOT exist...
	$cfg['user_id'] = $cfg['pub_id'];
	$cfg['userlevel'] = 1;
}

if ($cfg['user_id'] == $cfg['pub_id']) {

	$dbconf = array();
	
	$query = $user_inf = $core_db->query(
		"SELECT name AS username, access AS access_protocol
		FROM users WHERE users.id = '{$cfg['user_id']}'",
		"Get the custom name & access for the pub account."
	);
	if ($core_db->get_num_rows($query) > 0) {
		$dbconf = $core_db->fetch_row();
	}
	
	$cfg = array_merge($cfg, $dbconf);
}

if (isset($cfg['sesh_id'])) {
	$core_db->query(
		"UPDATE sessions SET lastaction = now()
		WHERE sesh_id = '{$cfg['sesh_id']}'",
		"Set user's last action in sessions table."
	);
}

$core_db->query(
	"UPDATE users SET lastaction = now(), logged_in = 'Y'
	WHERE id = '{$cfg['user_id']}'",
	"Update user's last action in users table."
);

$cfg = set_prefs("SELECT * FROM settings_global WHERE clan_id = '{$cfg['clan_id']}'");
$cfg = set_prefs("SELECT * FROM settings_private WHERE user_id = '{$cfg['user_id']}'");

// Redefine these variables, old values could be depreciated.
if ($cfg['admin'] == "N") { $cfg['admin'] == FALSE; }
if ($cfg['username'] == "Public") { $cfg['username'] = "Visitor"; }

$mm->validate($cfg['default_mod'], $cfg['default_action']);

if (isset($_REQUEST['type'])) {
	$cfg['type'] = $_REQUEST['type'];
} else {
	$cfg['type'] = "";
}

$cfg['extension_db'] = "";
$cfg['action_db'] = $cfg['type'];

/*
$cfg['extension_db'] = $core_db->lookup(
	"SELECT db FROM modules WHERE id = '$mm->module_id'",
	"Select default DB table for module."
);

if (!$cfg['action_db'] = $cfg['type']) {
	$cfg['action_db'] = $cfg['extension_db'];
}
*/

/* Ensure compatability with older code. */
if (isset($lang)) {
	$cfg['mod_name'] = $lang[$mm->module];
}

$cfg['mod'] = $mm->module;
$cfg['module'] = $mm->module;
$cfg['action'] = $mm->action;

if (!$cfg['action_db']) {	$cfg['action_db'] = $mm->module; }
if (!$cfg['extension_db']) { $cfg['extension_db'] = $mm->module; }

?>