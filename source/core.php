<?php

 /*---------------------------------------+ 
 | Clan-HQ CMS (Clan Management System)	  | 
 | Started: March 03 2000                 | 
 | Author: Nick Bolton                    | 
 | See source/global_conf.php for ver.    | 
 +----------------------------------------+ 
 | index.php - used to load configs,      | 
 | classes, functions, the requested      |
 | module and the language file.          |
 +---------------------------------------*/ 

$id = 0;
if (isset($_REQUEST["id"])) {
	$id += $_REQUEST["id"];
}

$input = array();
if (isset($_REQUEST["input"])) {
	$input = $_REQUEST["input"];
}

// Load session.
session_start();

// Load configuration files.
include "./source/global_conf.php";

// Load functions.
include "./source/functions_1.php";
include "./source/functions_2.php";

// Load MySQL Driver.
include "./source/mysql_driver.php";

// Hello DB!
$core_db = new DBDriver;
$core_db->connect($cfg['dbname'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbhost']);

/*
	If the site needs to go down for maintainance, the
	presense of a file called "lockfile" can cause CMS
	to return a message which is found in the lockfile.
*/
if (isset($bypass)) {
	if ($bypass != $cfg['bypass_pw']) {
		if ($message = $core_db->lookup("SELECT lockinfo FROM locktable WHERE effective = '1'", "Get DB lockfile.")) {
			die(nl2br("<font face='Verdana' size='2'>$message</font>"));
			
		} elseif ($message = @implode("", @file("lockfile"))) {
			die(nl2br("<font face='Verdana' size='2'>$message</font>"));
		}
	}
}

/*
	We need this to stop invalid modules/actions
	from being loaded before we start.
*/
include "./source/modules.php";
$mm = new ModuleManagement;

// Load needed classes.
include "./source/security.php";
include "./source/results_stats.php";
include "./source/browser.php";
include "./source/navigation.php";
include "./source/phpqstat.php";
include "./source/templates.php";
include "./source/usage_monitor.php";
include "./source/comments.php";
include "./source/files.php";

// Start the process timer.
$cfg['process_start'] = getmicrotime();

// Define the classess we're going to use.
$usg		=	new ResourceUsage;
$files	= new FileManagement;
$t			= new Template("./themes/{$cfg['theme_id']}/templates");
$srt		= new BrowserSort;
$nav		= new navigation("<a href='./'>{$cfg['site_name']}</a>");
$cmts		=	new Comments($id);

// Load required langauage file.
include "./lang/{$cfg['lang']}/{$cfg['lang']}-main.php";

// Include the module-specific language file.
$mod_lang = array();
@include "lang/{$cfg['lang']}/{$cfg['lang']}-". $cfg["mod"] .".php";

// Mege the module specific and common lang files...
$lang = array_merge($lang, $mod_lang);

// Individual theme configuration...
include "./themes/{$cfg['theme_id']}/config.php";

/* Fill in the theme config gaps that were overriden! */
foreach ($theme_cfg as $k => $v) {
	if (!$cfg[$k]) { $cfg[$k] = $v; }
}

// And away we go!!!
include "./source/modules/mod_$mm->module.php";

?>
