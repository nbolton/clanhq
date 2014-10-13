<?php

echo "\n\n";

include "../global_conf.php";
include "../mysql_driver.php";

echo "\n\n";

echo "Connecting to fileserver...\n";
$files_db = new DBDriver;
$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], "rackshack.clan-hq.com");

echo "Connecting to localhost...\n";
$core_db = new DBDriver;
$core_db->connect($cfg['dbname'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbhost']);

$core_db->query(
	"DELETE FROM file_counts WHERE file_count = 0"
);

$clans_query = $core_db->query(
	"SELECT id FROM clans WHERE disabled = ''"
);

$i = 1;

while ($clans_row = $core_db->fetch_row($clans_query)) {
	
	$cats_query = $core_db->query(
		"SELECT id FROM file_cats"
	);
	
	while ($cats_row = $core_db->fetch_row($cats_query)) {
		
		$userlevels_query = $core_db->query(
			"SELECT id FROM userlevels"
		);
		
		while ($userlevels_row = $core_db->fetch_row($userlevels_query)) {
			
			echo "Creating new index... (" . $i++ . ") ";
			
			$cat_id = $cats_row['id'];
			$userlevel = $userlevels_row['id'];
			$clan_id = $clans_row['id'];
			
			// First count how many files there are for this combination. 
			$file_count = $files_db->lookup(
				"SELECT COUNT(*) FROM files
				WHERE cat_id = '$cat_id'
				AND userlevel <= '$userlevel'
				AND clan_id = '$clan_id'",
				"Count number of files for this category."
			);
			
			if ($file_count) {
				// Now, check to see if this has already been indexed. 
				$index_count = $core_db->lookup(
					"SELECT COUNT(*) FROM file_counts
					WHERE cat_id = '$cat_id'
					AND userlevel = '$userlevel'
					AND clan_id = '$clan_id'",
					"Check to see if this combination has been indexed."
				);
				
				if ($index_count) {
					// If it has, update the existing record.
					$core_db->query(
						"UPDATE file_counts
						SET file_count = '$file_count',
						updated = NOW()
						WHERE cat_id = '$cat_id'
						AND userlevel = '$userlevel'
						AND clan_id = '$clan_id'",
						"Update existing index."
					);
					
				} else {
					// Otherwise create a new one.
					$core_db->query(
						"INSERT file_counts
						SET file_count = '$file_count',
						cat_id = '$cat_id',
						userlevel = '$userlevel',
						clan_id = '$clan_id',
						updated = NOW()",
						"Create a new index record."
					);
				}
			}
			echo "Done!\n";
		}
	}
}

?>