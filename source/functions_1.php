<?php

/* Safely returns the value of a $_REQUEST key as string. */
function request_string($key) {
	
	if (isset($_REQUEST[$key])) {
		return $_REQUEST[$key];
	}
	
	return "";
}

/* Safely returns the value of a $_REQUEST key as int. */
function request_int($key) {
	
	if (isset($_REQUEST[$key])) {
		return $_REQUEST[$key] + 0;
	}
	
	return 0;
}

/* Safely returns the value of a $_REQUEST key as array. */
function request_array($key) {
	
	if (isset($_REQUEST[$key])) {
		$value = $_REQUEST[$key];
		if (is_array($value)) {
			return $value;
		}
	}
	
	return array();
}

/* Safely returns the value of a $_REQUEST key as boolean. */
function request_bool($key) {
	
	if (isset($_REQUEST[$key])) {
		$value = $_REQUEST[$key];
		if ($value == 1) {
			return true;
		}
	}
	
	return false;
}


function regex_check_image($url = "") {
	
	/* -----------------------------------------------
	| Checks, and builds the <img> html.
	+ --------------------------------------------- */
	
	if (!$url) return;
	
	$url = trim($url);
	
	// Is it a legitimate image?
	if (!preg_match("/^(http|https|ftp):\/\//i", $url)) {
		// Presume it's internal and force the url as such...
		return "<img src='http:$url' border='0'>";
	}
	
	// If we are still here...
	return "<img src='$url' border='0'>";
}

function regex_font_attr($IN) {
	
	/* -----------------------------------------------
	| Returns a string for an /e regexp based on the input
	+ --------------------------------------------- */
	
	if (!is_array($IN)) return "";
	
	// Trim out stoopid 1337 stuff
	// [color=black;font-size:500pt;border:orange 50in solid;]hehe[/color]
	
	if ( preg_match( "/;/", $IN['1'] ) ) {
		
		$attr = explode( ";", $IN['1'] );
		$IN['1'] = $attr[0];
	}
	
	if ($IN['s'] == 'size') {
		
		$IN['1'] = $IN['1'] + 7;
		
		if ($IN['1'] > 30) {
			$IN['1'] = 30;
		}
		
		return "<span style='font-size:".$IN['1']."pt;line-height:100%'>".$IN['2']."</span>";
		
	} else if ($IN['s'] == 'col') {
		return "<span style='color:".$IN['1']."'>".$IN['2']."</span>";
		
	} else if ($IN['s'] == 'font') {
		return "<span style='font-family:".$IN['1']."'>".$IN['2']."</span>";
	}
}

function regex_build_url($url=array()) {
	
	/* -----------------------------------------------
	| Checks, and builds the a href html
	+ --------------------------------------------- */
	
	$skip_it = 0;
	
	// Make sure the last character isn't punctuation.. if it is, remove it and add it to the
	// end array
	if ( preg_match( "/([\.,\?]|&#33;)$/", $url['html'], $match) ) {
		$url['end'] .= $match[1];
		$url['html'] = preg_replace( "/([\.,\?]|&#33;)$/", "", $url['html'] );
		$url['show'] = preg_replace( "/([\.,\?]|&#33;)$/", "", $url['show'] );
	}
	
	// Make sure it's not being used in a closing code/quote/html or sql block
	
	if (preg_match( "/\[\/(html|quote|code|sql)/i", $url['html']) ) {
		return $url['html'];
	}
	
	// clean up the ampersands
	$url['html'] = preg_replace( "/&amp;/" , "&" , $url['html'] );
	
	// Make sure we don't have a JS link
	$url['html'] = preg_replace( "/javascript:/i", "java script&#58; ", $url['html'] );
	
	// Do we have http:// at the front?
	
	if ((!preg_match("#^(http|news|https|ftp|aim|irc)://#", $url['html']) && ($url['html']{0} != "?")) ) {
		$url['html'] = 'http://'.$url['html'];
	}
	
	if (preg_match("#^irc://#", $url['html'] ) ) {
		$same_window = 1;
	}
	
	//-------------------------
	// Tidy up the viewable URL
	//-------------------------
	
	if (preg_match( "/^<img src/i", $url['show'] )) $skip_it = 1;
	
	$url['show'] = preg_replace( "/&amp;/" , "&" , $url['show'] );
	$url['show'] = preg_replace( "/javascript:/i", "javascript&#58; ", $url['show'] );
	
	if (strlen($url['show']) < 55) $skip_it = 1;
	
	// Make sure it's a "proper" url
	
	if (!preg_match( "/^(http|ftp|https|news):\/\//i", $url['show'] )) $skip_it = 1;
	
	$show = $url['show'];
	
	if ($skip_it != 1) {
		$stripped = preg_replace( "#^(http|ftp|https|news)://(\S+)$#i", "\\2", $url['show'] );
		$uri_type = preg_replace( "#^(http|ftp|https|news)://(\S+)$#i", "\\1", $url['show'] );
		
		$show = $uri_type.'://'.substr( $stripped , 0, 35 ).'...'.substr( $stripped , -15		 );
	}
	
	return $url['st'] . "<a href='".$url['html']."'". ($same_window ? "" : " target='_blank'") .">".$show."</a>" . $url['end'];
}

function cmscode($txt, $convert = 'html') {
	
	/* -----------------------------------------------
	| Converts CMSCode into HTML and vice-versa.
	+ --------------------------------------------- */
	
	$cmscode = array(
	// For CMSCode failsafe system to prevent user from screwing their site up!
	// CMS	=>	HTML
	'		'		=>	'&nbsp;&nbsp;',
	"\n\n"	=>	'<p>',
	"\n"		=>	'<br>',
	'[b]'		=>	'<b>',
	'[/b]'	=>	'</b>',
	'[i]'		=>	'<i>',
	'[/i]'	=>	'</i>',
	'[u]'		=>	'<u>',
	'[/u]'	=>	'</u>',
	'[s]'		=>	'<s>',
	'[/s]'	=>	'</s>',
	);
	
	switch($convert) {
		
		case "html":
			
			// Lists
			$txt = preg_replace("#\[list\]#i", "<ul>", $txt);
			$txt = preg_replace("#\[\*\]#", "<li>", $txt);
			$txt = preg_replace("#\[/list\]#i", "</ul>", $txt);
			
			// Images
			$txt = preg_replace("#\[img\](.+?)\[/img\]#ie", "regex_check_image('\\1')", $txt);
			
			// New line for single line windows
			$txt = preg_replace("#\[br\]#i", "<br>", $txt);
			
			// Easy stuff (bold, underline, italic, etc)
			$txt = preg_replace("#\[b\](.+?)\[/b\]#is", "<b>\\1</b>", $txt);
			$txt = preg_replace("#\[i\](.+?)\[/i\]#is", "<i>\\1</i>", $txt);
			$txt = preg_replace("#\[u\](.+?)\[/u\]#is", "<u>\\1</u>", $txt);
			$txt = preg_replace("#\[s\](.+?)\[/s\]#is", "<s>\\1</s>", $txt);
			
			// (c) (r) and (tm)
			$txt = preg_replace("#\(c\)#i", "&copy;", $txt);
			$txt = preg_replace("#\(tm\)#i", "&#153;", $txt);
			$txt = preg_replace("#\(r\)#i", "&reg;", $txt);
			
			// font size, colour and font style
			// [font=courier]Text here[/font]		[size=6]Text here[/size]		[color=red]Text here[/color]
			
			$txt = preg_replace("#\[size=([^\]]+)\](.+?)\[/size\]#ies", "regex_font_attr(array('s'=>'size','1'=>'\\1','2'=>'\\2'))", $txt);
			$txt = preg_replace("#\[font=([^\]]+)\](.*?)\[/font\]#ies", "regex_font_attr(array('s'=>'font','1'=>'\\1','2'=>'\\2'))", $txt);
			$txt = preg_replace("#\[color=([^\]]+)\](.+?)\[/color\]#ies", "regex_font_attr(array('s'=>'col' ,'1'=>'\\1','2'=>'\\2'))", $txt);
			
			// email tags
			// [email]matt@index.com[/email]		 [email=matt@index.com]Email me[/email]
			$txt = preg_replace("#\[email\](\S+?)\[/email\]#i", "<a href='mailto:\\1'>\\1</a>", $txt);
			$txt = preg_replace("#\[email\s*=\s*\&quot\;([\.\w\-]+\@[\.\w\-]+\.[\.\w\-]+)\s*\&quot\;\s*\](.*?)\[\/email\]#i", "<a href='mailto:\\1'>\\2</a>", $txt);
			$txt = preg_replace("#\[email\s*=\s*([\.\w\-]+\@[\.\w\-]+\.[\w\-]+)\s*\](.*?)\[\/email\]#i", "<a href='mailto:\\1'>\\2</a>", $txt);
			
			// url tags
			// [url]http://www.index.com[/url]		 [url=http://www.index.com]cms![/url]
			$txt = preg_replace("#\[url\](\S+?)\[/url\]#ie", "regex_build_url(array('html' => '\\1', 'show' => '\\1'))", $txt);
			$txt = preg_replace("#\[url\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/url\]#ie", "regex_build_url(array('html' => '\\1', 'show' => '\\2'))", $txt);
			$txt = preg_replace("#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#ie", "regex_build_url(array('html' => '\\1', 'show' => '\\2'))", $txt);
			
			// link tags (same as url - but called link instead)
			// [link]http://www.index.com[/link]		 [link=http://www.index.com]cms![/link]
			$txt = preg_replace("#\[link\](\S+?)\[/link\]#ie", "regex_build_url(array('html' => '\\1', 'show' => '\\1'))", $txt);
			$txt = preg_replace("#\[link\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/link\]#ie", "regex_build_url(array('html' => '\\1', 'show' => '\\2'))", $txt);
			$txt = preg_replace("#\[link\s*=\s*(\S+?)\s*\](.*?)\[\/link\]#ie", "regex_build_url(array('html' => '\\1', 'show' => '\\2'))", $txt);
			
			$txt = str_replace("\n", "<br>", $txt);
			
		break;
		
		case "cmscode":
			// CMSCODE > HTML
			foreach($cmscode as $code => $html) {
				$txt = str_replace($html, $code, $txt);
			}
			
			$i = 0;
			
			if(!is_array($txt)) {
				$txt = htmlspecialchars($txt);
			} else {
				reset($txt);
				foreach($txt as $k => $v) {
					$txt[$i++] = htmlspecialchars($v);
				}
			}
			
		break;
	}
	return($txt);
}

function htmlsafe($value) {
		
		/* -----------------------------------------------
		| Stops user from inputting html
		+ --------------------------------------------- */
		
		$array = array(
	"\""	=>	'&quot;',
	"<"		=>	'&lt;',
	">"		=>	'&gt;',
	);
	
	foreach($array as $char => $html) {
		$value = str_replace($char, $html, $value);
	}
}

function debug_info() {
	
	/* -----------------------------------------------
	| Shows a huge table with all the keys/values from 
	| the $cfg array.
	+ --------------------------------------------- */
	
	global $cfg, $core_db;
	
	print("<table border=1 cellspacing=0 cellpadding=2><tr>
		<td bgcolor=#999999 colspan=2><font face=Arial, Helvetica, sans-serif size=2><b>Debug info for $cfg[site_name]</b></font></td>
	</tr>");
	
	foreach ($cfg as $key => $value) {
		print("<tr>
		<td bgcolor=#DDDDDD><font face=Arial, Helvetica, sans-serif size=2><b>$key</b></font></td>
		<td bgcolor=#DDDDDD><font face=Arial, Helvetica, sans-serif size=2>$value</font></td>
		</tr>");
	}
	
	print("</table><p>");
}

function set_prefs($query) {
	
	/* -----------------------------------------------
	| If $type has a preference for something,
	| modify it; if not, leave it alone 
	+ --------------------------------------------- */
	
	global $cfg, $core_db;
	$result = $core_db->query($query);
	if ($core_db->get_num_rows()) {
		foreach($core_db->fetch_row($result) as $key => $value) {
			$cfg[$key] = $value;
		}
	}
	
	foreach (array('left', 'right') as $side) {
		if (isset($cfg[$side . '_panels_list'])) {
			if (!is_array($cfg[$side . '_panels_list'])) {
				$cfg[$side . '_panels_list'] = unserialize($cfg[$side . '_panels_list']);
			}
		}
	}
	
	return $cfg;
}

function admin_links($page, $id) {
	
	/* -----------------------------------------------
	| Set admin links - so admins can jump back and
	| forward from front end to back end.
	+ --------------------------------------------- */
	
	global $t, $color, $cfg;
	
	if ($cfg['admin']) {
		
		$t->set_var("admin_links[$page]",
		"[ <b><a href=$page.php?action=edit&referer=$page&id=$id>Edit</a></b> ] "
		."[ <b><a href=$page.php?action=delete&referer=$page&id=$id>Delete</a></b> ]");
	}
}

function add_log($info, $log_table = "action_logs", $parse_time = "") {
	
	/* -----------------------------------------------
	| Adds a record to the log in the db.
	+ --------------------------------------------- */
	
	global $t, $color, $cfg, $REMOTE_ADDR, $HTTP_HOST, $REQUEST_URI, $core_db;
	
	if (!$cfg['user_id']) { $cfg['user_id'] = 1; }
	if (!$cfg['clan_id']) { $cfg['clan_id'] = 0; }
	
	switch ($log_table) {
		
		case "action_logs":
			$core_db->query(
		"INSERT INTO $cfg[dblogs].action_logs SET
		user_id = {$cfg['user_id']},
		user_ip = '$REMOTE_ADDR',
		clan_id = {$cfg['clan_id']},
		time = now(),
		url = '$REQUEST_URI',
		action = '$info'",
		"add entry to log"
			);
		break;
		
		case "download_logs":
			$core_db->query(
		"INSERT INTO $cfg[dblogs].download_logs SET
		user_id = {$cfg['user_id']},
		user_ip = '$REMOTE_ADDR',
		clan_id = {$cfg['clan_id']},
		time = now(),
		url = '$REQUEST_URI',
		file_id = '$info'",
		"add entry to log"
			);
		break;
		
		case "load_logs":
			$core_db->query(
		"INSERT INTO $cfg[dblogs].load_logs SET
		user_id = {$cfg['user_id']},
		user_ip = '$REMOTE_ADDR',
		clan_id = {$cfg['clan_id']},
		time = now(),
		url = '$REQUEST_URI',
		load_avg = '$info',
		parse_time = '$parse_time'",
		"add entry to log"
			);
		break;
	}
}

function sqllistradio($field, $query, $default, $none) {
	
	/* -----------------------------------------------
	| Creates a list of radio buttons returned from a sql statement
	+ --------------------------------------------- */
	
	global $t, $core_db;
	
	$result = $core_db->query($query, "sqllistradio");
	
	if ($default)
	{
		while ($row = $core_db->fetch_row($result))
		{
			if ($row[1] == $default){	// default match found
		$listcode .= "<input type=radio name=$field value=$row[1] checked><img alt='$row[2]' src=$imagedir$row[1].gif>&nbsp;&nbsp;&nbsp;";
			} else {
		$listcode .= "<input type=radio name=$field value=$row[1]><img alt='$row[2]' src=$imagedir$row[1].gif>&nbsp;&nbsp;&nbsp;";
			}
		}
		if ($none==true){
		$listcode .= "<input type=radio name=$field value=0> Other&nbsp;&nbsp;&nbsp;";
		}
	}
	return $t->set_var($field,$listcode);
}

function yn($field, $default, $type) {
	
	/* -----------------------------------------------
	| Creates 2 radio buttons, yes or no.
	+ --------------------------------------------- */
	
	global $t, $lang;
	
	$radio = "";
	$list = "";
	
	$yn = array (
		'Y'	=> array('Y', $lang['yes']),
		'N'	=> array('N', $lang['no']),
	);
	
	foreach($yn as $row) {
		if ($default == $row[0]) { // default match found
			$radio .= "<input type=radio name=$field value=$row[0] checked>$row[1]&nbsp;&nbsp;";
			$list .= "<option value='$row[0]' selected>$row[1]</option>"; 
		} else {
			$radio .= "<input type=radio name=$field value=$row[0]>$row[1]&nbsp;&nbsp;";
			$list .= "<option value='$row[0]'>$row[1]</option>";
		}
	}
	
	if ($type == 'radio') return $radio;
	if ($type == 'listbox') return $list;
}

function checkbox($field, $value) {
	
	/* -----------------------------------------------
	| Makes a checkbox, and checks it if value exists
	+ --------------------------------------------- */
	
	global $t, $lang;
	
	if ($value) { // default match found
		$checkbox = "<input type=checkbox name=$field value=1 checked>";
	} else {
		$checkbox = "<input type=checkbox name=$field value=1>";
	}
	
	return ($checkbox);
}

function sqllistbox($field, $query, $default = "", $none = "", $part = "", $charlimit = "", $use_lang = "") {
	
	/* -----------------------------------------------
	| Creates a list of items returned from a sql statement
	+ --------------------------------------------- */
	
	global $t, $lang, $core_db;
	if(!$part) $listcode = "<select name=\"$field\" class=formDropdown>";
	$result = $core_db->query($query, "sqllistbox");
	if ($none) { $listcode .= "<option value=''>None</option>"; }
	while ($row = mysql_fetch_array($result)) {
		if($charlimit) $row[1] = substr_adv($row[1], $charlimit);
		if($use_lang) $row[1] = $lang[$row[1]];
		if ($row[0] == $default)	// default match found
			$listcode .= "<option value='$row[0]' selected>$row[1]</option>";
		else
			$listcode .= "<option value='$row[0]'>$row[1]</option>";
	}
	
	if(!$part) $listcode .= "</select>";
	
	return ($listcode);
}

function arraylistbox($field, $array, $default = "", $none = "") {
	
	/* -----------------------------------------------
	| Creates a list of items from $array
	+ --------------------------------------------- */
	
	global $t;
	$listcode = "<select name=\"$field\" class=formDropdown>";
	if ($none) { $listcode .= "<option value=''>None</option>"; }
	
	if ($array) {
		foreach($array as $key => $value) {
			if ($default == $key) { // default match found
		$listcode .= "<option value='$key' selected>$value</option>";
			} else {
		$listcode .= "<option value='$key'>$value</option>";
			}
		}
	}
	
	$listcode .= "</select>";
	
	return ($listcode);
}

function monthlistbox($field, $default = "") {
	
	/* -----------------------------------------------
	| Generates a listbox with all the months of the year in.
	+ --------------------------------------------- */
	
	global $t;
	$months = array('January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$listcode = "<select name=\"$field\" class=formDropdown>";
	$i = 1; // $i starts off as 1, not 0
	
	foreach ($months as $row) {
		if ($default == $i) { // default match found
			$listcode .= "<option value='" . $i++ . "' selected>$row</option>";
		} else {
			$listcode .= "<option value='" . $i++ . "'>$row</option>";
		}
	}
	
	$listcode .= "</select>";
	
	return $listcode;
}

function numlistbox($field, $start, $stop, $default) {
	
	/* -----------------------------------------------
	| Creates a list of items within a number range
	+ --------------------------------------------- */
	
	global $t;
	$listcode = "<select name=\"$field\" class=formDropdown>";
	
	for ($i=$start;$i<=$stop;$i=$i+1) { // generate minute list box items
		
		if ($i == $default) { // default match found
			$listcode = $listcode . "<option value='$i' selected>$i</option>";
		} else {
			$listcode = $listcode . "<option value='$i'>$i</option>";
		}
	}
	$listcode .= "</select>";
	
	return ($t->set_var($field,$listcode));
}

function leading_zero($int, $count) {
	
	$int_len = strlen($int);
	
	// Is there more char needed? If not, do this...
	if ($int_len < $count) {
		$int_left = $count - $int_len;
		
		// Count how many zeros to add.
		for ($i = 0; $i < $int_left; $i++) {
			$add_int .= "0";
		}
		
		// Return the treated string.
		return $add_int . $int;
	}
	
	// Otherwise just leave it alone :)
	return $int;
}

function redirect($ext_str) {
	global $REQUEST_URI;
	header("Location:http:" . (strchr($_SERVER['REQUEST_URI'], "?") ? "" : "?") . $_SERVER['REQUEST_URI'] . $ext_str);
}

function polls_vote_control($id = false) { // Added v3.6 Beta
	
	// Controls if poll is shown, and if users can vote
	
	global $cfg, $REMOTE_ADDR, $_COOKIE, $core_db;
	
	$this_poll = $core_db->lookup(
		"select id from polls_data
		where userlevel <= $cfg[userlevel]
		and clan_id = '{$cfg['clan_id']}'
		and is_enabled = 'Y'
		order by create_date desc limit 1",
		'get this poll id'
	);
	
	$poll['this'] = ($id ? $id : $this_poll);
	
	if ($poll['this']) {
		
		if ($cfg['user_id'] == $cfg['pub_id']) {
			// If the user is pub, are they allowed to vote and are there any options?
			$allow_pub = ($core_db->lookup("select allow_pub from polls_data where id = $poll[this]", 'find out if allow pub') == 'Y') ? true : false;
			$are_options = $core_db->lookup("select id from polls_options where poll_id = $poll[this]", 'find out if option exists');
			
			// Do we have their IP or do they have our cookie?
			$got_ip = ( $cfg['poll_use_ip'] ? $core_db->lookup("select id from polls_log where ip = '$REMOTE_ADDR' and poll_id = $poll[this]", 'vote_polls_control: check ip in log') : false);
			
			if (isset($_COOKIE["poll-$this_poll"])) {
				$got_cookie = $_COOKIE["poll-$this_poll"];
			} else {
				$got_cookie = "";
			}
			
			$poll['has_voted'] = ($got_ip || $got_cookie) ? true : false;
			$poll['allow_vote'] = ($allow_pub && $are_options) ? true : false;
			
		} else {
			
			// Are there any options for the poll?
			$are_options = $core_db->lookup("select id from polls_options where poll_id = $poll[this]", 'vote_polls_control: check for options');
			
			// If user is a member, have they voted before?
			$got_id = $core_db->lookup("select id from polls_log where poll_id = $poll[this] and user_id = {$cfg['user_id']}", 'vote_polls_control: if member');
			
			// Look for an id where we have IP, this poll_id and their user_id
			$found_ip = $core_db->lookup(
				"select id from polls_log
				where ip = '$REMOTE_ADDR'
				and poll_id = $poll[this]
				and user_id = {$cfg['user_id']}",
				'vote_polls_control: check log for ip 2'
			);
			
			// Do we have their IP or do they have our cookie?
			$got_ip = ( $cfg['poll_use_ip'] ? $found_ip : false);
			
			if (isset($_COOKIE["poll-$this_poll"])) {
				$got_cookie = $_COOKIE["poll-$this_poll"];
			} else {
				$got_cookie = FALSE;
			}
			
			$poll['has_voted'] = ($got_ip || $got_cookie || $got_id) ? true : false;
			$poll['allow_vote'] = ($are_options ? true : false);
		}
	}
	
	return($poll);
}

?>