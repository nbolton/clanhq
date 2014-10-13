<?
include("./source/mysql_driver.php");
include("./source/functions_1.php");
include("./source/functions_2.php");
include("./source/global_conf.php");
include("./lang/en/en-main.php");

$cfg['action'] = "None";
$cfg['mod'] = "Clanscape CMS Admin";
$cfg['username'] = $PHP_AUTH_USER;
$cfg['user_id'] = 10;
$cfg['clan_id'] = 2;
$cfg['basedir'] = "Clanscape CMS Admin";

$style = 
	"body {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		text-decoration: none
	}
	
	a {
		color: #0000FF;
		text-decoration: underline;
	}
	
	a:hover {
		text-decoration: none; 
		color: #0000FF
	}";

echo("<style type=\"text/css\"><!-- $style --></style>");

$cfg['error_style'] = $style;

// Hello DB!
$core_db = new DBDriver;
$core_db->connect($cfg['dbname'], $cfg['dbuser'], $cfg['dbpass']);
