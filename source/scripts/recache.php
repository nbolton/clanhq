<?php

echo "\n\n";

include "../global_conf.php";
include "../mysql_driver.php";

$files_db = new DBDriver;
$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], "rackshack.clan-hq.com");

$files_db->query("SELECT id, DATA FROM files WHERE cat_id = '4' OR cat_id = '7'");

while ($row = $files_db->fetch_row()) {
	echo "Writing to ../../cache/file{$row['id']}... ";
	$handle = fopen("../../cache/file{$row['id']}", 'w');
	fwrite($handle, $row['DATA']);
	fclose($handle);
	echo "Done!\n";
}

?>