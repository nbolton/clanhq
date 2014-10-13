<?php

/*
	Author: Nick "r3n" Bolton
	This script is designed to clean out all the
	unwanted data in the cms_core database. This
	data is also known as garbage, hence making 
	this a garbage collection script.
*/

// Tidy up in Linux console...
echo "\n";

// Include all nessecary libries.
include "../global_conf.php";
include "../functions_1.php";
include "../functions_2.php";
include "../mysql_driver.php";

// Stop driver sending email incase of DB failure.
$cfg['dev_mode'] = TRUE;

echo "Connecting to Database... ";
// Connect to the DB using default values from CMS.
$core_db = new DBDriver;
$core_db->connect($cfg['dbname'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbhost']);
echo "Done!\n";

echo "Getting list of valid clans... ";
$valid_clans_result = $core_db->query("SELECT id FROM clans");

// Retrieve array of valid clans.
while ($clan = $core_db->fetch_row($valid_clans_result)) {
	$valid_clans[] = $clan['id'];
}
echo "Done!\n";

echo "Getting list of invalid users... ";
// Now we need to find out who are invalid users.
$invalid_users_result = $core_db->query(
	"SELECT id FROM users WHERE clan_id != " . implode(" AND clan_id != ", $valid_clans)
);

// Retrieve array of invalid users.
while ($user = $core_db->fetch_row($invalid_users_result)) {
	$invalid_users[] = $user['id'];
}
echo "Done!\n";

$invalid_users[] = 43243;

// Tables with records that are identified by valid clans...
$gc_tables = array(
	'comments', 'fix_registry', 'users',
	'fixtures', 'news', 'polls_data',
	'polls_log', 'polls_options',
	'reports', 'scores', 'servers',
	'sessions', 'settings_global',
	'settings_private', 'trophies'
);

foreach ($gc_tables as $table) {
	echo "Deleting dead records from {$table}... ";
	$core_db->query("DELETE FROM {$table} WHERE clan_id != " . implode(" AND clan_id != ", $valid_clans));
	echo "Done!\n";
}

echo "Deleting dead records from buddy_list... ";
$core_db->query("DELETE FROM buddy_list WHERE buddy_id = " . implode(" OR buddy_id = ", $invalid_users));
echo "Done!\n";

echo "Deleting dead records from messages... ";
$core_db->query("DELETE FROM messages WHERE to_id = " . implode(" OR to_id = ", $invalid_users) . " AND (type != 'Se')");
echo "Done!\n";

echo "Deleting empty records from comments... ";
$core_db->query("DELETE FROM comments WHERE comments = ''");
echo "Done!\n";

echo "\n";

?>