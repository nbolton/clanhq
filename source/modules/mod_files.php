<?php

if ($mm->action != 'getimage') {
	$files_db = new DBDriver;
	$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
}

switch($mm->action) {
	
	case "default": redirect("&action=browse"); break;
	
	/*
	+ ------------------------------------------------
	| Files BROWSE
	+ ------------------------------------------------
	| Shows a list of files that the clan own.
	| Will change shortcut between "DOWNLOAD" and
	| "GETIMAGE" depending on file-type.
	| Images open in new window, other files download
	| as normal.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	| Updated: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "browse":
		
		$t->set_file('subTemplate');
		
		$srt->set_array( array('filename', 'filesize', 'upload_date; uploaded', 'download_date; lastdl', 'count; dls'), 'upload_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $files_db->query(
			"SELECT id, user_id, clan_id, cat_id,
			download_id, filesize, count, info, filename, type,
			DATE_FORMAT(upload_date, '{$cfg['sql_date']['alt']}') as u_date,
			DATE_FORMAT(download_date, '{$cfg['sql_date']['alt']}') as d_date
			FROM files WHERE clan_id = '{$cfg['clan_id']}'
			".($cat ? "and cat_id = $cat" : "")."
			AND userlevel <= '{$cfg['userlevel']}'".
			$srt->sql(),
			"Get file list."
		);
		
		$total_file_count = $files_db->lookup(
			"SELECT COUNT(*) FROM files
			WHERE clan_id = '{$cfg['clan_id']}'
			AND userlevel <= '{$cfg['userlevel']}'",
			"Get total file count."
		);
		
		// First, check there are actualy any files before doing anything!
		if ($total_file_count) {
			
			$t->set_block('subTemplate', 'FileCatsBlock', 'FCBlock');
			
			// Set "Latest files" at top of cats list
			if(!$cat) {
				$row['name'] = "<b>{$lang['latest_files']}</b>";
				$row['folder_image'] = "icon_folder_open.gif";
				$row['open'] = " ({$lang['viewing']})";
				$row['files_shown'] = "{$lang['latest_files']}...";
				$row['file_count'] = " ($total_file_count)";
			} else {
				$row['folder_image'] = "icon_folder_closed.gif";
				$row['name'] = $lang['latest_files'];
				$row['open'] = " ";
				$row['file_count'] = " ($total_file_count)";
			}
			
			$t->set_array($row);
			$t->parse("FCBlock", 'FileCatsBlock', TRUE);
			
			// Make a list of all the categorys
			$cats_result = $core_db->query(
				"SELECT id, name, info FROM file_cats",
				"Get file categories list."
			);
			
			while ($row = $core_db->fetch_row($cats_result)) {
				
				$file_count = $core_db->lookup(
					"SELECT file_count FROM file_counts
					WHERE cat_id = '{$row['id']}'
					AND clan_id = '{$cfg['clan_id']}'
					AND userlevel = '{$cfg['userlevel']}'",
					"Count number of files for this category."
				);
				
				if ($file_count) {
					if ($cat == $row['id']) {
						$row['name'] = "<b>$row[name]</b>";
						$row['folder_image'] = "icon_folder_open.gif";
						$row['open'] = " ($lang[viewing])";
						$row['files_shown'] = "$lang[showing_category]: $row[name]...";
						$category = $row['name'];
					} else {
						$row['folder_image'] = "icon_folder_closed.gif";
						$row['open'] = " ";
					}
					$row['file_count'] = $file_count;
					$row['file_count'] = " ($row[file_count])";
					$t->set_array($row);
					$t->parse("FCBlock", 'FileCatsBlock', TRUE);
				}
			}
			
			if ($cat) {
				$file_count = $core_db->lookup(
					"SELECT file_count FROM file_counts
					WHERE cat_id = '$cat'
					AND clan_id = '{$cfg['clan_id']}'
					AND userlevel = '{$cfg['userlevel']}'",
					"Count number of files for the selected category."
				);
				
				$nav->navs[] = array("<a href='?mod=$mm->module'>{$lang['files']}</a>", $category);
			} else {
				$file_count = $total_file_count;
				$nav->navs[] = array("<a href='?mod=$mm->module'>{$lang['files']}</a>", $lang['latest_files']);
			}
			
			if ($files_db->get_num_rows($result) && $file_count) {
				// Start creating files list
				$t->set_block('subTemplate', 'RowBlock', 'RBlock');
				$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
				
				$nav->page_index(
					"SELECT file_count FROM file_counts
					WHERE clan_id = '{$cfg['clan_id']}'
					".($cat ? "and cat_id = $cat" : "")."
					AND userlevel = '{$cfg['userlevel']}'"
				);
				
				while ($row = $core_db->fetch_row($result)) {
					$row['info'] = substr_adv($row['info'], 35, false);
					$row['lastdl_name'] = $core_db->lookup("select name from users where id = $row[download_id]");
					$row['upload_name'] = $core_db->lookup("select name from users where id = $row[user_id]");
					$row['lastdl'] = ( ($row['lastdl_name']) ? "$row[lastdl_name] ($row[d_date])" : false );
					$row['uploaded'] = ( ($row['upload_name']) ? "$row[upload_name] ($row[u_date])" : false );
					
					
					if ($row['filesize'] > 1024 * 1024) {
						$row['filesize'] = round( ($row['filesize'] / 1024 / 1024), 1) . 'MB';
					} else {
						$row['filesize'] = round( ($row['filesize'] / 1024), 1) . 'KB';
					}
					
					if (stristr($row['type'], 'image')) {
						$row['target'] = "target=_blank";
						$row['action_type'] = $lang['view'];
						$row['link'] = "?mod=files&action=getimage&id={$row['id']}";
					} else {
						$row['target'] = " ";
						$row['action_type'] = $lang['download'];
						$row['link'] = "?mod=files&action=download&id={$row['id']}";
					}
					
					$t->set_array($row);
					$t->parse('RBlock', 'RowBlock', true);
				}
			} else {
				
				$t->set_block("subTemplate", "FilesBlock", "FBlock");
				$t->set_var("FBlock", $lang['no_files_in_cat']);
			}
		} else {
			$t->set_msg('no_files', true);
		}
		
		echo "<!-- FILE_DB QUERIES: $queries -->\n\n\n\n";
		
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Files ADMIN
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "admin":
		
		$srt->set_array( array('filename', 'user_id; uploader', 'upload_date; date', 'filesize', 'cat_id; category'), 'upload_date', 'desc', 0, $cfg['browsers_limit']);
		
		$result = $files_db->query(
			"SELECT id, filename, user_id, cat_id, filesize, count, type,
			DATE_FORMAT(upload_date, '{$cfg['sql_date']['alt']}') as date,
			DATE_FORMAT(download_date, '{$cfg['sql_date']['alt']}') as download_date
			FROM files
			WHERE clan_id = {$cfg['clan_id']}".
			$srt->sql(),
			"Get."
		);
		
		$i = 0;
		
		if ($files_db->get_num_rows()) {
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'RowBlock', 'RBlock');
			$t->set_msg_var();
			$srt->set_var('subTemplate', 'SortsBlock', 'StBlock');
			
			$nav->page_index(
				"select COUNT(*) from $cfg[dbfiles].files
				where clan_id = {$cfg['clan_id']}",
				"", $files_db
			);
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['files']}</a>",
				$lang['admin']
			);
			
			while ($row = $files_db->fetch_row($result)) {
				$row['uploader'] = $core_db->lookup("select name from users where id = $row[user_id]");
				$row['category'] = $core_db->lookup("select name from file_cats where id = $row[cat_id]");
				$row['link'] = "?mod=files&action=download&id={$row['id']}";
				$row['target'] = ( (($row['type'] == 'image/pjpeg') || ($row['type'] == 'image/gif')) ? "target=_blank" : " " );
				
				$row['size'] = round( ($row['filesize'] / 1024), 1) . 'kB';
				if ($row['filesize'] > 1024 * 1024) {
					$row['size'] = round( ($row['filesize'] / 1024 / 1024), 1) . 'MB';
				}
				
				$t->set_array($row);
				$t->parse('RBlock', 'RowBlock', true);
			}
		} else {
			$t->set_msg('no_files', true, true);
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Files CREATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "create":
		
		$nav->navs[] = array(
			"<a href='?mod=$mm->module'>{$lang['files']}</a>",
			"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
			$lang['create']
		);
		
		$t->set_file('subTemplate');
		$t->set_block('subTemplate', 'subTemplate');
		
		$row['select_userlevel'] = sqllistbox('input[select_userlevel]', "select id, info from userlevels");
		$row['select_category'] = sqllistbox('input[select_category]', "select id, name from file_cats");
		
		$t->set_array($row, "", "", "");
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Files EDIT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "edit":
		
		$mm->ifempty($id, "?mod=news&action=admin");
		
		$result = $files_db->query(
			"SELECT id, info, cat_id, filename, userlevel
			FROM files
			WHERE id = $id",
			"edit"
		);
		
		if ($files_db->get_num_rows()) {
			
			$t->set_file('subTemplate');
			$t->set_block('subTemplate', 'subTemplate');
			
			$row = cmscode($files_db->fetch_row($result), 'cmscode');
			
			$row['select_userlevel'] = sqllistbox('input[select_userlevel]', "select id, info from userlevels", $row['userlevel'], false);
			$row['select_category'] = sqllistbox('input[select_category]', "select id, name from file_cats", $row['cat_id'], false);
			
			$t->set_array($row, "", "", "");
			
			$nav->navs[] = array(
				"<a href='?mod=$mm->module'>{$lang['news']}</a>",
				"<a href='?mod=$mm->module&action=admin'>{$lang['admin']}</a>",
				"{$lang['edit']} ({$row['filename']})",
			);
			
		} else {
			$t->set_msg('no_files_id');
		}
		$t->mparse('Output', 'subTemplate');
		
	break;
	
	/*
	+ ------------------------------------------------
	| Files INSERT
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "insert":
		
		// Check the limitations
		$filesize = ''.$HTTP_POST_FILES['uploadfile']['size'];
		$usg->check_limit('filesize', $filesize, 1, "", "?mod=files&action=admin");
		$usg->check_limit('diskspace', $filesize, 1, "", "?mod=files&action=admin");
		
		$tempfile = $HTTP_POST_FILES['uploadfile']['tmp_name'];
		$filename = $HTTP_POST_FILES['uploadfile']['name'];
		$filetype = $HTTP_POST_FILES['uploadfile']['type'];
		
		$cat_id = $input['select_category'] + 0;
		$userlevel = $input['select_userlevel'] + 0;
		
		// Has file arrived at server ok?
		if (file_exists($tempfile)) {
			
			if ($input['new_filename']) { $filename = $input['new_filename']; }
			
			$info = str_replace("\n","<br>", str_replace("  ","&nbsp;&nbsp;", ($input['info']) ) );
			$data = addslashes(fread(fopen($tempfile, "r"), $filesize));
			$files->set_cache($id, $cat_id, $data);
			
			$files_db->query(
				"INSERT files SET
				info		=	'$info',
				data		=	'$data',
				filename	=	'$filename',
				filesize	=	'$filesize',
				type		=	'$filetype',
				userlevel	=	'$userlevel',
				cat_id 		=	'$cat_id',
				clan_id 	=	{$cfg['clan_id']},
				user_id	 	=	{$cfg['user_id']},
				edit_id 	=	{$cfg['user_id']},
				upload_date	=	now(),
				edit_date 	=	now()",
				"insert/upload"
			);
			
			if ($data) { $files->index($cat_id, $userlevel); }
			
			$id = mysql_insert_id();
			add_log("Uploaded File (ID: $id)");
			
			go_back("msg=i", "?mod=$mm->module&action=admin");
			
		} else {
			go_back("msg=e", "?mod=$mm->module&action=admin");
		}
		
	break;
	
	/*
	+ ------------------------------------------------
	| Files UPDATE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "update":
		
		// Check the limitations
		$filesize = ($HTTP_POST_FILES['uploadfile']['size'] + 0);
		$usg->check_limit('filesize', $filesize, 1, "", "?mod=files&action=admin");
		$usg->check_limit('diskspace', $filesize, 1, "", "?mod=files&action=admin");
		
		$tempfile = ($HTTP_POST_FILES['uploadfile']['tmp_name']);
		$filename = ($HTTP_POST_FILES['uploadfile']['name']);
		$filetype = ($HTTP_POST_FILES['uploadfile']['type']);
		$userlevel = $input['select_userlevel'] + 0;
		$cat_id = $input['select_category'] + 0;
		
		$info = str_replace("\n","<br>", str_replace("  ","&nbsp;&nbsp;", ($input['info']) ) );
		
		// Has file arrived at server ok?
		if (file_exists($tempfile)) {
			
			$data = fread(fopen($tempfile, "r"), $filesize);
			$sql_data = "data = '{$data}',";
			$sql_filetype = "type = '$filetype',";
			$sql_filesize = "filesize = '$filesize',";
			$sql_filename = "filename = '$filename',";
		}
		
		if ($input['new_filename']) {
			$sql_filename = "filename = '$input[new_filename]',";
		}
		
		$files_db->query(
			"UPDATE files SET
			$sql_data
			$sql_filename
			$sql_filetype
			$sql_filesize
			info = '$info',
			userlevel = '$userlevel',
			cat_id = '$cat_id',
			edit_id = '{$cfg['user_id']}',
			edit_date = now()
			WHERE id = '$id'
			AND clan_id = '{$cfg['clan_id']}'",
			"update/upload"
		);
		
		if ($data) { $files->index($cat_id, $userlevel); }
		
		add_log("Updated File (ID: $id)");
		go_back("msg=u", "?mod=$mm->module&action=admin");
		
	break;
	
	/*
	+ ------------------------------------------------
	| Files DOWNLOAD
	+ ------------------------------------------------
	| Increments counter, and retrieves data from
	| database and updates the last download info.
	| Will show in browser window if filetype is
	| image, and will download as normal if else.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	| Updated: v3.8 Alpha Mod (r3n)
	+ ------------------------------------------------
	*/
	
	case "download":
		
		if (!$cfg['fileserver']) {
			$clan_id_sql = "AND clan_id = '{$cfg['clan_id']}'";
		}
		
		$result = $files_db->query(
			"SELECT id, data, type,
			filename, filesize, clan_id
			FROM files
			WHERE id = $id
			$clan_id_sql",
			"Download."
		);
		
		if ($files_db->get_num_rows()) {
			
			$data = $files_db->fetch_row($result);
			
			$files_db->query(
				"UPDATE files SET
				count = (count + 1),
				download_id = '{$cfg['user_id']}',
				download_date = now()
				WHERE id = '{$data['id']}'",
				"Update counter."
			);
			
			add_log("Downloaded File (ID: {$data['id']})");
			
			// Is it an image?
			if (strstr($data['type'], 'image')) {
				header("Location:http:?mod=files&action=getimage&id=$id");
			} else {
				$type = $data['type'];
				$file = $data['data'];
				$filename = $data['filename'];
				$filesize = $data['filesize'];
				$usg->use_bandwidth($filesize, $data['clan_id']);
				header ("Content-type: $type");
				header ("Content-length: $filesize");
				header ("Content-disposition: attachment; filename=$filename");
				echo $file;
			}
			
		} else {
			header ("Location: http:?mod=files&nofile=1");
		}
		
	break;
	
	/*
	+ ------------------------------------------------
	| Files GETIMAGE
	+ ------------------------------------------------
	| Gets an image from the DB, does not increment
	| download counter, as it is used for getting the
	| title image.
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "getimage":
		
		$files_db = new DBDriver;
		$files_db->connect($cfg['dbfiles'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbfiles_host']);
		
		$result = $files_db->query(
			"SELECT id, data, type, filesize, clan_id
			FROM files WHERE id = '$id'
			AND clan_id = '{$cfg['clan_id']}'",
			"get image"
		);
		
		if ($files_db->get_num_rows()) {
			$data = $files_db->fetch_row($result);
			
			// Is it an image?
			if (strstr($data['type'], 'image')) {
				$file = $data['data'];
				$usg->use_bandwidth($data['filesize'], $data['clan_id']);
				echo $file;
				
			} else {
				header ("Location:http:?mod=files&action=download&id=$id");
				exit;
			}
			
		} else {
			header ("Location:http:?mod=files&nofile=1");
			exit;
		}
		
	break;
	
	/*
	+ ------------------------------------------------
	| Files DELETE
	+ ------------------------------------------------
	| Added: v3.0 Beta (r3n)
	+ ------------------------------------------------
	*/
	
	case "delete":
		
		if ($item || $id) {
			
			if (!$item) { $item = array($id); }
			
			foreach ($item as $key => $id) {
				$id = $id + 0;
				
				$file_inf = $files_db->query(
					"SELECT cat_id, userlevel FROM files WHERE id = '$id'",
					"Get file info to update the index."
				);
				$file_inf = $files_db->fetch_row();
				$files->index($file_inf['cat_id'], $file_inf['userlevel']);
				
				$files_db->query(
					"DELETE FROM files
					WHERE id = '$id'
					AND clan_id = '{$cfg['clan_id']}'",
					"Delete files."
				);
				
				if (file_exists("./cache/file{id}")) { unlink("./cache/file{id}"); }
				add_log("Deleted File (ID: $id)");
				$i++;
			}
			
			// Deleted one, or several?
			if ($i == 1) {
				$msg = "ds";
			} elseif ($i > 1) {
				$msg = "d";
			}
		}
		
		go_back("msg=$msg", "?mod=$mm->module&action=admin");
		
	break;
}