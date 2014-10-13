<?php

$hostnameseg = explode('.', $_SERVER['HTTP_HOST']);
$cfg = array(
	
	/* EXTREAMLY SENSITIVE! */
	'dbname'				=>	"chq1_cms_core",
	'dbfiles'				=>	"chq1_cms_core",
	'dblogs'				=>	"chq1_cms_core",
	'dbuser'				=>	"chq1",
	'dbpass'				=>	"+ZrxKtnusT5tNqPTut7Q",
	'dbhost'				=>	"localhost",
	'dbfiles_host'			=>	"localhost",
	'qstat_dir'				=>	"qstat ",
	'bypass_pw'				=>	"43few63gdd",
	'sensitive'				=>	array('dbname', 'dbfiles', 'dblogs', 'dbuser', 'dbpass', 'dbfiles_host', 'fileserver', 'qstat_dir', 'bypass_pw'),
	
	/* Less important info... */
	'clan_name'				=>	array_shift($hostnameseg),
	'footnote'			 	=>	"<b>Clan-HQ {version}</b><br>Copyright &copy; 2006<br>\n<a href='http://www.clan-hq.com' target='_blank'>Clan-HQ.com</a>",
	'debug'					=>	FALSE,
	'version'				=>	"6.0",
	'log_load_point'		=>  0.3,  // When do we want to log the parse time? (value is average CPU load)
	'server_time'			=>	date('g:ia'),
	'server_date'			=>	date('D d M Y'),
	'poll_use_ip'			=>	TRUE,	// Use IP tracking for poll.
	'dev_servers'			=>	array("bongo"),
	'cps_host'				=>	"www.clan-hq.com",
	'system_hosts' 			=>	array("clan-hq\.com",),
	'domain_name'			=>	FALSE,
	'fileserver'			=>	FALSE,
	'admin'					=>	FALSE,
	'lastlogin'				=>	FALSE,
	'select_logo'			=>	FALSE,
	'logo_id'				=>	FALSE,
	'user_id'				=>	0,
	'dev_mode'				=>	TRUE,
	'username'				=>	"",
	'access_protocol'		=>	"",
	'operating_system'		=>	"win32",
	
	/* Sizes displayed (KB, MB, GB) */
	'diskspace_sizetype'	=>	'MB',
	'bandwidth_sizetype'	=>	'GB',
	
	/* Contact info */
	'admin_email'			=>	'admin@clan-hq.com',
	'sales_email'			=>	'sales@clan-hq.com',
	'support_email'			=>	'support@clan-hq.com',
	
	/* Privileges settings
	 * Syntax: (module_id;[+|-]|)*
	 * + = Allow access
	 * - = Deny access
	 */
	'access_model'			=>	"1;-|2;-|3;-|4;-|5;-|6;-|7;-|8;-|9;-|10;-|11;-|12;-|13;-|14;-|15;-|16;-|17;-|18;-|19;-|20;-|21;-",
	'admin_access'			=>	"1;x|2;xxxxxxxxx|3;xxxxxxxxxxxxx|4;xxxxxxxxx|5;xxxxxxxxxxxxxx|6;xxxxxxxxx|7;xxxxxxxxxxx|8;xxxxxxxxxxxxx|9;xxxxxxxxx|10;xxxxxxxxx|11;xxxxx|12;xxxxxx|13;x|14;xxx|15;x|16;x|17;x|18;xxxx|19;x|20;xxxxxxxxxx|21;xxxxxxxxx|",
	'member_access'			=>	"1;x|2;xxx------|3;xxx-----x--x-|4;xxx------|5;xxx--------xxx|6;xxx------|7;xxx------xx|8;xxx--------xx|9;xxx-x-x--|10;xxx------|11;xx-x-|12;xxxxxx|13;x|14;xxx|15;-|16;-|17;x|18;xxxx|19;x|20;xxxxxxxxxx|21;xxx------|",
	'public_access'			=>	"1;x|2;xxx------|3;xxx----------|4;xxx------|5;xxx-----------|6;xxx------|7;xxx------xx|8;xxx--------xx|9;xxx-x-x--|10;xxx------|11;-----|12;xxxxxx|13;x|14;xxx|15;-|16;-|17;-|18;----|19;x|20;----------|21;xxx------|",
	
	// Used to reference valid actions.
	'modules' => array(
		1 => "home",
		2 => "news",
		3 => "members",
		11 => "buddy",
		12 => "security",
		13 => "about",
		20 => "messages"
	),
	
	// Use the 'modules' element to see what the IDs mean.
	'actions' => array(
		1	=> array("default"),
		2	=> array("default", "browse", "details", "admin", "create", "edit", "insert", "update", "delete"),
		3	=> array("default", "browse", "details", "admin", "create", "edit", "edit_profile", "view_flags", "insert", "update", "update_profile", "update_multi"),
		
		11	=> array("default", "add", "delete", "search", "list"),
		12	=> array("default", "login", "forpass", "auth", "logout", "denied"),
		13	=> array("default"),
		20	=> array("default", "inbox", "sent", "saved", "deleted", "compose", "send", "read", "save", "delete")
	),
	
	/* Userlevels */
	'userlevels' => array(
		1	=> "Public",
		2	=> "Member",
		3	=> "Admin"
	),
	
	/* Sidebar Settings
	 * 0: Close
	 * 1: Maximize
	 * 2: Minimize
	 */
	'show_pub_menu'			=>	1,
	'show_admin_menu'		=>	1,
	'show_admin_links'		=>	1,
	'show_headlines'		=>	1,
	'show_site_stats'		=>	1,
	'show_last_result'		=>	1,
	'show_next_fixture'		=>	1,
	'show_match_stats'		=>	1,
	'show_cms_banner'		=>	1,
	'show_poll'				=>	1,
	'show_buddy_list'		=>	1,
	'show_server_watch'		=>	1,
	'show_messages'			=>	1,
	
	/* Misc Settings */
	'theme_id'				=>	3,			// Default theme ID.
	'select_server'			=>	0,			// Default server ID.
	'default_mod'			=>	1,			// Default module ID.
	'default_action'		=>	'default',	// Default action name.
	'online_since'			=>	300,		// Timeout in seconds.
	'lang'					=>	'en',		// Default language file.
	'scoring'				=>	'total',	// Default type of scoring.
	'browsers_limit'		=>	30,			// Default max for browsers.
	'messages_limit'		=>	3,			// Number of messages in messages panel.
	'headline_limit'		=>	5,			// Number of items in headline panel.
	'messages_popup'		=>	1,			// Messages popup... (0 - off, 1 - one msg, 2 - all messages)
	'logo_align'			=> 'left',		// Align logo to this side.
	
	/* Avatar Y/N Defaults */
	'avatar_news'			=>	'Y',
	'avatar_comments'		=>	'Y',
	'avatar_reports'		=>	'Y',
	
	/* Valid Message Postfixes
	 * Stops public from screwing with message function.
	 * Syntax: array( message(; class) );
	 */
	'valid_message_postifxes' => array(
		'i', 'u', 'd', 'um', 'ds', 'r', 'pu', 'ur', 's', 'pd', 'pds', 'mc', 'mm',
		'mx; bodyError', 'ar; bodyError', 'cr; bodyError', 'cds; bodyError', 'e; bodyError', 
		'lr; bodyError', 'dslr; bodyError', 'fslr; bodyError', 'bwlr; bodyError'
	),
	
	'languages'		=> array(
		"en" => "{lang_lang-en}",
		"nl" => "{lang_lang-nl}"
	),
	
	'sql_date' =>	array(
		'extended' => '%W %d %b %Y',
		'full' => '%H:%i %a %e %b',
		'short' => '%a %e %b',
		'time' => '%H:%i',
		'alt' => '%d/%m/%y'
	),
	
	'buddy_search_window_function'	=>	"openBrWindow('?mod=buddy&action=search','','scrollbars=yes,resizable=yes,width=400,height=400')",
	
	'error_style' =>
		"body {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 11px;
			color: #000000;
			text-decoration: none
		}
		
		text {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 11px;
			color: #000000;
			text-decoration: none
		}
		
		.title {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 11px;
			color: #000000;
			text-decoration: none;
			font-weight: bold
		}
		
		a {
			color: #0000FF;
			text-decoration: underline;
		}
		
		a:hover {
			text-decoration: none; 
			color: #0000FF
		}",
		
		/* Search for these strings in $_SERVER['HTTP_USER_AGENT'] */
		'crawlers'	=>	array(
			"NPBot", "Slurp", "Girafabot",
			"Googlebot", "WISEnutbot",
			"Netcraft", "SurveyBot", "Teoma",
			"ia_archiver", "crawler"
		),
);

$Conf = array(
		'AdminEmail' => $cfg['admin_email'],
		'DevServer'	=>	"bongo",
		'FreePostfix'	=>	"clan-hq.com",
);

if ($_SERVER['HTTP_HOST'] == $Conf['DevServer']) {
	$Conf['clan_name'] = "dc";
}

// Must go at end.
$Conf = array_merge($cfg, $Conf);
$cfg = $Conf;

?>