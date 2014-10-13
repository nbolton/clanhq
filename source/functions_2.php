<?php

// +------------------------------------------+ 
// | Clan-HQ.com CMS (Clan Management System) | 
// +------------------------------------------+ 

function last_action() {
	
	/* -----------------------------------------------
	| string : last_action()
	| Based on the $HTTP_REFERER, get the user's
	| last action and return it as string.
	+ --------------------------------------------- */
	
	global $HTTP_REFERER;
	
	// Return the "action" from the HTTP_REFERER string.
	$action = preg_replace("/.*[&|?]action=([a-z]*)&*.*/i", "$1", $HTTP_REFERER);
	
	// If PCRE pattern returns full address, no match was found! Presume default action.
	return (!stristr($action, "http://") ? strtolower($action) : "default");
}

function last_module() {
	
	/* -----------------------------------------------
	| string : last_module()
	| Based on the $HTTP_REFERER, get the user's
	| last used module and return it as string.
	+ --------------------------------------------- */
	
	global $HTTP_REFERER, $core_db;
	
	// Return the "action" from the HTTP_REFERER string.
	$module = preg_replace("/.*[&|?]mod=([a-z]*)&*.*/i", "$1", $HTTP_REFERER);
	
	$default = $core_db->lookup(
		"SELECT name FROM modules WHERE id = '{$cfg['default_mod']}'",
		"Get name of default module."
	);
	
	// If PCRE pattern returns full address, no match was found! Presume default module.
	return (!stristr($module, "http://") ? strtolower($module) : $default);
}

function fetch_clan_name($id, $tag = "", $name = "", $url = "") {
	
	/* -----------------------------------------------
	| Decides what a clan is called depending on what
	| info is given (Clan ID, Clan Name or Clan Tag).
	+ --------------------------------------------- */
	
	global $Conf, $core_db;
	
	if ($id) {
		
		$info = $core_db->query(
			"SELECT id, tag, display AS name, name AS prefix, domain AS url
			FROM clans WHERE id = '$id'",
			"Get clan info."
		);
		
		$info = $core_db->fetch_row($info);
		
		if (!$info['url']) {
			$info['url'] = "{$info['prefix']}." . $Conf['FreePostfix'];
		}
		
		return $info;
		
	} else {
		return array('id' => $id, 'tag' => $tag, 'name' => $name, 'url' => $url);
	}
}

function go_back($ext = "", $alt = "", $new_ref = "") {
	
	/* -----------------------------------------------
	| Sends user back to the last page they were on,
	| including a specified "ext" which will vary for
	| each module.
	+ --------------------------------------------- */
	
	if (isset($new_ref)) {
		$ref = $new_ref;
	} else {
		$ref = $_REQUEST['ref'];
	}
	
	// Make sure we have a ref.
	if (!$ref) {
		if ($alt) {
			$ref = $alt;
		} else {
			$ref = $_SERVER['HTTP_REFERER'];
		}
	}
	
	// First look for a '?'
	if (!strstr($ref, "?")) {
		// Can't have anything at the end, post-fix $ext...
		$ref = "?$ext";
		
	} else {
		// The ref is normal, check for ampersands.
		$ref .= "&$ext";
	}
	
	// Replace occurance of more than one & with one &.
	$search[0] = "/[&]{,2}/";
	$replace[0] = "&";
	
	// Remove instance of ?& and replace it with a ?.
	$search[1] = "/\?&/";
	$replace[1] = "?";
	
	// Remove anything before the ?
	$search[2] = "/(.*)\?(.*)/";
	$replace[2] = "./?$2";
	
	$ref = preg_replace($search, $replace, $ref);
	
	/*
		Now we're going to attempt to remove
		multiple occurances of patterns in a string.
	*/
	$ref = explode("&", $ref);
	$ref = array_unique($ref);
	$ref = implode("&", $ref);
	
	header("Location: $ref");
	exit;
}

function check_file($thisfile, $lower = "") {
	
	/* -----------------------------------------------
	| Checks file location on filesystem for the file,
	| if it exists, return it. If not, look for the
	| file but in lower case (optional).
	+ --------------------------------------------- */
	
	if (file_exists($thisfile)) return $thisfile;
	if (file_exists(strtolower($thisfile)) && $lower) return strtolower($thisfile);
}

function seek_file($name, $dir, $type = "", $alert = "", $extra_lines = "") {
	
	/* -----------------------------------------------
	| Returns a file location of a file if it exists!
	| If not, send an email to admin saying the file
	| dosen't exist!
	+ --------------------------------------------- */
	
	global $cfg, $core_db;
	
	if ($name) {
		
		if (!$type) {
			
			// If there isn't a type, look for just the filename.
			return check_file($dir . $name, TRUE);
			
		} elseif (!is_array($type)) {
			
			// Just check for the one supplied extension.
			return check_file($dir . $name . ".$type", TRUE);
			$email_ext = ".$type";
			
		} else {
			
			// Seek the file for each extension, and return first filename found!
			foreach($type as $thistype) {
				$thisfile = check_file($dir . $name . ".$thistype", TRUE);
				if ($thisfile) return $thisfile;
			}
			
			// Could have many diffrent extensions!
			$email_ext = ".*";
		}
		
		$name = strtolower($name);
		
		$missing_map_id = $core_db->lookup(
			"SELECT id FROM {$cfg['dblogs']}.missing_maps
			WHERE map = '$name'",
			"Look for map name in logs."
		);
		
		// Have we already been alerted?
		if (!$missing_map_id) {
			
			// If not, alert admin of new file.
			sendemail(
				$cfg['admin_email'],
				"Missing CMS Map Image!",
				"Map: $name".
				$extra_lines
			);
			
			// ... And add an entry to the database!
			$extra_lines = strip_tags(str_replace("\n", "", $extra_lines));
			$core_db->query("INSERT {$cfg['dblogs']}.missing_maps SET map = '$name', info = '$extra_lines'");
		}
	}
	
	// Ok, well you'll just have to have this! :)
	return "./themes/$cfg[theme_id]/images/map_not_found.gif";
}

function add_string_prefix($needle, $haystack) {
	
	/* -----------------------------------------------
	| Returns the haystack with the needle as a prefix
	| if the needle isn't already at the start of the
	| haystack.
	+ --------------------------------------------- */
	
	if ($haystack) {
		
		// Get the first group of char, and add a space char to the end.
		$haystack_array = explode(" ", $haystack);
		$haystack_array[0] .= " ";
		
		if ($haystack_array[0] != $needle) {
			
			// If we don't have the needle at the start, add it!
			return($needle . $haystack);
			
	} else {
			
			// Othwise, just return the origional string.
			return($haystack);
		}
	}
}

function check_sql_file($id) {
	
	/* -----------------------------------------------
	| Returns boolean for if this $id is a file or not
	+ --------------------------------------------- */
	
	global $files;
	return $files->exists($id);
}

function is_buddy($id) {
		
		/* -----------------------------------------------
		| Returns boolean for if this $id is a buddy or not
		+ --------------------------------------------- */
		
		global $core_db, $cfg;
		return $core_db->lookup(
			"SELECT buddy_id FROM buddy_list
			WHERE buddy_id = '$id'
			AND user_id = '{$cfg['user_id']}'",
			"Check if this ID is on current user's buddy list."
		);
}

function avg_load($return_int = "") {
		
		/* -----------------------------------------------
		| Returns the average load as text - not a number
		+ --------------------------------------------- */
		
		global $lang, $Conf;
		
		if ($Conf["operating_system"] != "linux") {
			return "n/a";
		}
		
		$avg_load = explode(" ", `cat /proc/loadavg`);
		
		// Just return the load!
		if ($return_int && isset($avg_load[1])) {
			return $avg_load[1];
		}
		
		if (isset($avg_load[1])) {
			if ($avg_load[1] < 0.2)			$text = "<span class=bodyGood><b>" . ucfirst($lang['very_low']) . "</span>";
			elseif ($avg_load[1] < 0.4)	$text = "<span class=bodyGood><b>" . ucfirst($lang['low']) . "</span>";
			elseif ($avg_load[1] < 0.6)	$text = "<span class=bodyWarning><b>" . ucfirst($lang['medium']) . "</span>";
			elseif ($avg_load[1] < 0.8)	$text = "<span class=bodyWarning><b>" . ucfirst($lang['high']) . "</span>";
			elseif ($avg_load[1] < 1)		$text = "<span class=bodyFatal><b>" . ucfirst($lang['very_high']) . "!</b></span>";
			elseif ($avg_load[1] >= 1)	$text = "<span class=bodyFatal><b>" . ucfirst($lang['extreme']) . "!</b></span>";
		} else {
			$text = "n/a";
		}
		
		return $text;
}

function set_msg_counts() {
		
		/* -----------------------------------------------
		| Count number of items in each of the messages
		| folders and set them on the page.
		+ --------------------------------------------- */
		
		global $t, $cfg, $core_db;
		
		$inbox = $core_db->lookup(
			"SELECT COUNT(*) FROM messages
			WHERE unread = '1' AND type = 'I'
			AND to_id = '{$cfg['user_id']}'",
			"Count new messages."
		);
		
		$sent = $core_db->lookup(
			"SELECT COUNT(*) FROM messages
			WHERE unread = '1' AND type = 'Se'
			AND to_id = '{$cfg['user_id']}'",
			"Count sent messages."
		);
		
		$deleted = $core_db->lookup(
			"SELECT COUNT(*) FROM messages
			WHERE unread = '1' AND type = 'R'
			AND to_id = '{$cfg['user_id']}'",
			"Count deleted messages."
		);
		
		$saved = $core_db->lookup(
			"SELECT COUNT(*) FROM messages
			WHERE unread = '1' AND type = 'Sa'
			AND to_id = '{$cfg['user_id']}'",
			"Count saved messages."
		);
		
		$t->set_var('msg_count_inbox', ($inbox ? " ($inbox)" : ""));
		$t->set_var('msg_count_sent', ($sent ? " ($sent)" : ""));
		$t->set_var('msg_count_deleted', ($deleted ? " ($deleted)" : ""));
		$t->set_var('msg_count_saved', ($saved ? " ($saved)" : ""));
}

function format_quote($id, $quote) {
	
	/* -----------------------------------------------
	| str : format_quote(id:int, quote:string)
	| Alias for get_quote()
	+ --------------------------------------------- */
	
	return get_quote($id, $quote);
}

function get_quote($id, $quote = "") {
	
	/* -----------------------------------------------
	| str : get_quote(id:int [, quote:string])
	| Return formatted quote for the selected user.
	+ --------------------------------------------- */
	
	global $cfg, $core_db;
	
	if (!$quote) {
		// Get the stuff from the DB.
		$quote = $core_db->lookup(
			"SELECT quote
			FROM users
			WHERE id = '$id'",
			"Get user's quote."
		);
	}
	
	if ($quote) {
		
		// Format the quote.
		$quote = cmscode(strip_tags($quote));
		
		$style = $core_db->lookup(
			"SELECT quote_style
			FROM users
			WHERE id = '$id'",
			"Get user's quote style."
		);
		
		// Does the user want our quote style?
		if (($style & 1) && $quote) {
			return "<i>\"$quote\"</i>";
		}
		
		// Otherwise, leave it alone!
		return $quote;
		
	} else { return ""; }
}

function getmicrotime() {
		
		/* -----------------------------------------------
		| Get microtime in seconds.
		+ --------------------------------------------- */
		
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
}

function PageLoadTime() {
		
		/* -----------------------------------------------
		| Show how long it took in seconds to parse the page
		+ --------------------------------------------- */
		
		global $cfg;
		$time_start = $cfg['process_start'];
		$time_end = getmicrotime();
		$time = $time_end - $time_start;
		$times = explode(".",$time);
		$timeone = $times[0];
		$timetwo = $times[1];
		$timetwo = substr($timetwo,0,3);
		$time = $timeone.".".$timetwo;
		return $time;
}

function set_admin_links($var, $links, $extra_inputs = "", $parent = "") {
	
	/* -----------------------------------------------
	|		Sets list of 'Admin Links' to shortcut from 
	| front-end public to back-end admin.
	| $var:		(char) Admin links variable array
	| $links:		(array) Links options (linkName; link(; confirmBoolean(; customMsg)))
	+ --------------------------------------------- */
	
	global $t, $lang, $cfg, $REQUEST_URI, $preview, $print, $row;
	
	if ($cfg['admin'] && $cfg['show_admin_links'] && $links && !$preview && !$print) {
		$result = "<table border=0 cellspacing=0 cellpadding=0><tr><td class=bodyText>[ <b>$lang[admin]</b> ]</td>";
		foreach($links as $link) {
			$link = explode("; ", $link);
			if (isset($link[2])) {
				$msg = ( $link[3] ? $lang[$link[3]] : $lang['confirmLink'] );
				$confirm = " onClick=\"return confirmForm('$msg')\"";
			} else {
				$confirm = "";
			}
			
			if (isset($_POST['type_id'])) {
				$type_id = $_POST['type_id'];
			} elseif (isset($_GET['type_id'])) {
				$type_id = $_GET['type_id'];
			} else {
				$type_id = "";
			}
			
			$result .= 
				"<td width=5></td><form method=post action=$link[1]><td>
				<input type=hidden name=ref value={this_ref}>
				<input type=hidden name=type_id value=$type_id>
				$extra_inputs
				<input class=formButton type=submit value=\"$link[0]\"$confirm></td></form>";
		}
		$result .= "</tr></table>";
		$t->set_var($var, $result);
	} else {
		$t->kill_block($var, "", $parent);
	}
}

function sql_date($day, $month, $year, $hour = "", $minute = "", $second = "") {
	
	/* -----------------------------------------------
	| Treats day/month/year ints and checks for dumbass american mistakes.
	+ --------------------------------------------- */
	
	$day = ($day + 0);
	$month = ($month + 0);
	$year = ($year + 0);
	
	if (($second < 0) || ($minute > 60)) { $second = 1; }
	if (($minute < 0) || ($minute > 60)) { $minute = 1; }
	if (($hour < 0) || ($hour > 24)) { $hour = 1; } elseif ($hour == 24) { $hour = 0; $day++; }
	if (($day < 1) || ($day > 31)) { $day = 1; }
	if ($month < 1) { $month = 1; }
	if ($year < 1) { $year = 1; }
	
	if ($month > 12) {
		$t_day = $day;
		$day = $month;
		$month = $t_day;
	} else {
		$day = $day;
		$month = $month;
	}
	
	$date = "$year-$month-$day";
	
	if ($hour) {
		$date .= " $hour";
		
		if ($minute) {
			$date .= ":$minute";
			
			if ($second) {
				$date .= ":$second";
			} else {
				$date .= ":00";
			}
		} else {
			$date .= ":00";
		}
	}
	
	return $date;
}

function sendemail($email, $subject, $message, $html = FALSE) {
	
	/* -----------------------------------------------
	| Sends a simple email
	+ --------------------------------------------- */
	
	if ($html) { $html = "\r\nContent-type: text/html"; }
	
	global $cfg;
	mail ($email, $subject, $message, "From: {$cfg['support_email']} \r\nReply-To: {$cfg['support_email']} $html");
}

function substr_adv($string, $limit, $link = FALSE) {
	
	/* -----------------------------------------------
	| Shortens a string and adds "..." at the end to 
	| indicate there is more text; also has the option
	| of adding a link at the end.
	+ --------------------------------------------- */
	
	if (strlen($string) > $limit) {
		$string = substr(stripslashes($string), 0, $limit);
		if (strlen($string) == $limit) {
			$string = chop($string) . '...';
			if ($link) $string .= " <a href=$link>More</a>";
		}
	}
	return ($string);
}