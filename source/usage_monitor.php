<?php

class ResourceUsage {
	
	function check_limit($type, $size = "", $mode = 1, $clan_id = "", $ref = "") {
		
		/* -----------------------------------------------
		| Checks limit according to type (space, bw, etc)
		+ --------------------------------------------- */
		
		global $cfg, $core_db;
		
		if (!$clan_id) { $clan_id = $cfg['clan_id']; }
		
		// If we are on the fileserver, the account type wont have been set!
		if ($cfg['fileserver']) {
			$cfg['account'] = $core_db->lookup(
				"SELECT account FROM clans WHERE id = '$clan_id'",
				'Get clan account type (fileserver).'
			);
		}
		
		// Limits are stored in the db as MB, need to return them as Bytes.
		$limit = ($core_db->lookup("SELECT $type FROM acct_specs WHERE id = '{$cfg['account']}'", "Get '$type' max.") * 1048576);
		
		switch($type) {
			
			case "servers":
				$current = $core_db->lookup("SELECT COUNT(*) FROM servers WHERE clan_id = '$clan_id'", "Count number of servers.");
				if ($limit == $current) $msg = 'lr';
			break;
			
			case "diskspace":
				
				// First get the limit in KB
				$limit = $limit / 1024;
				
				$files_db = new DBDriver;
				$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
				$result = $files_db->query("SELECT filesize FROM files WHERE clan_id = '$clan_id'", "Get list of filesizes.");
				$used = 0;
				while ($row = $files_db->fetch_row($result)) {
					$used += $row['filesize'];
				}
				
				// Get the used value in KB
				$used = $used / 1024;
				
				// How much space is there left in KB?
				$left = ($limit - $used);
				
				if (($used > $limit) && ($mode == 1)) {
					$msg = 'dslr';
				} elseif ($mode == 2) {
					return array($used, $left, $limit);
				}
				
			break;
			
			case "filesize":
				
				if (($size > $limit) && ($mode == 1)) {
					$msg = 'fslr';
				} elseif ($mode == 2) {
					return $limit;
				}
				
			break;
			
			case "bandwidth":
				
				// Get the limit value in KB
				$limit = $limit / 1024;
				
				// This value is in KB
				$bw_used = $core_db->lookup("select bw_used from clans where id = '$clan_id'");
				
				$left = $limit - $bw_used;
				if (($bw_used > $limit) && ($mode == 1)) {
					$msg = 'bwlr';
				} elseif ($mode == 2) {
					return array($bw_used, $left, $limit);
				}
			break;
		}
		
		if (isset($msg) && ($mode == 1)) go_back("msg=$msg", $ref);
	}
	
	function use_bandwidth($filesize, $clan_id = "") {
		
		global $cfg, $core_db;
		
		if (!$clan_id) { $clan_id = $cfg['clan_id']; }
		
		// Check they are not over their limit, if not, how much do they have?
		$bandwidth = $this->check_limit('bandwidth', "", 2, $clan_id);
		
		// DB Stores in KB, get Byte equiv!
		$bandwidth = ($bandwidth[0] * 1024);
		
		// Add on how much they have used... (KB)
		$used = round($bandwidth + $filesize);
		
		// Store BW usage no lower than in KB.
		$used = round($used / 1024);
		
		// Update the current value in the DB.
		$core_db->query(
			"UPDATE clans
			SET bw_used = '$used'
			WHERE id = '$clan_id'",
			"update bw usage"
		);
	}
}