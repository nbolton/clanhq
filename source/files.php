<?php

class FileManagement {
	
	var $file_id = "";
	
	function insert($key, $cat_id, $userlevel = 1) {
		
		global $HTTP_POST_FILES, $cfg, $input, $id, $core_db, $file;
		
		$tempfile = $HTTP_POST_FILES[$key]['tmp_name'];
		$filename = $HTTP_POST_FILES[$key]['name'];
		$filetype = $HTTP_POST_FILES[$key]['type'];
		$filesize = $HTTP_POST_FILES[$key]['size'];
		
		if (file_exists($tempfile)) {
			
			$data = addslashes(fread(fopen($tempfile, "r"), $filesize));
			$this->set_cache($id, $cat_id, $data);
			
			$files_db = new DBDriver;
			$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
			
			$files_db->query(
				"INSERT files SET
				data			=	'$data',
				filename	=	'$filename',
				filesize	=	'$filesize',
				type			=	'$filetype',
				userlevel	=	'1',
				cat_id 		=	'$cat_id',
				clan_id 	=	'{$cfg['clan_id']}',
				user_id	 	=	'$id',
				edit_id 	=	'{$cfg['user_id']}',
				upload_date	=	now(),
				edit_date 	=	now()",
				"insert/upload ss"
			);
			
			if ($data) { $this->index($cat_id, $userlevel); }
			
			$this->file_id = $files_db->get_insert_id();
			add_log("Uploaded File (ID: $this->file_id)");
			
			return true;
		}
		return false;
	}
	
	function index($cat_id, $userlevel) {
		
		global $cfg, $core_db;
		
		$files_db = new DBDriver;
		$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
		
		/* First count how many files there are for this combination. */
		$file_count = $files_db->lookup(
			"SELECT COUNT(id) FROM files
			WHERE cat_id = '$cat_id'
			AND userlevel <= '$userlevel'
			AND clan_id = '{$cfg['clan_id']}'",
			"Count number of files for this category."
		);
		
		/* Now, check to see if this has already been indexed. */
		$index_count = $core_db->lookup(
			"SELECT COUNT(id) FROM file_counts
			WHERE cat_id = '$cat_id'
			AND userlevel <= '$userlevel'
			AND clan_id = '{$cfg['clan_id']}'",
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
				AND clan_id = '{$cfg['clan_id']}'",
				"Update existing index."
			);
			
		} else {
			// Otherwise create a new one.
			$core_db->query(
				"INSERT file_counts
				SET file_count = '$file_count',
				cat_id = '$cat_id',
				userlevel = '$userlevel',
				clan_id = '{$cfg['clan_id']}',
				updated = NOW()",
				"Create a new index record."
			);
		}
		
		$core_db->query(
			"DELETE FROM file_counts WHERE file_count = 0",
			"Delete all unusable indexes."
		);
		
		return TRUE;
	}
	
	function exists($id) {
		
		/* -----------------------------------------------
		| string : exists($id : integer)
		| Checks if a file exists in the cache dir.
		+ --------------------------------------------- */
		
		global $cfg;
		
		if (file_exists("./cache/file{$id}") && $id) {
			return "./cache/file{$id}";
			
		} elseif ($id) {
			
			$files_db = new DBDriver;
			$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
			$file_id = $files_db->lookup(
				"SELECT id FROM files
				WHERE id = '$id'
				AND clan_id = '{$cfg['clan_id']}'",
				"Check a file exists."
			);
			
			if ($file_id) {
				return $file_id;
				
			} else {
				return false;
			}
		}
	}
	
	function set_cache($id, $cat_id, $data) {
		
		global $core_db;
		
		$core_db->query(
			"SELECT id FROM file_cats WHERE cached = '1'",
			"Fetch array of valid categories."
		);
		
		$valid_cat_ids = $core_db->fetch_row();
		
		/* Only cache the file if it's in a valid category. */
		if (in_array($cat_id, $valid_cat_ids)) {
			$handle = fopen("./cache/file{$id}", "w");
			fwrite($handle, $data);
			fclose($handle);
		}
		
		return TRUE;
	}
}