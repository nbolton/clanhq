<?

include("admin.php");

switch($action) {
	
	case "logs":
		
		echo("Incomplete<br>\n");
		
	break;
	
	case "find_user":
		
  	echo(
			"<form method=post action=stats.php?action=show_user>\n".
			"User ID: <input type=text name=id size=5><p>\n".
  		"Username: <input type=text name=name size=20><p>\n".
			"<input type=submit value=Go>\n".
			"</form>"
		);
		
	break;
	
	case "show_user":
		
		$id = $id + 0;
		$name = addslashes($name);
		
  	$result = $core_db->query(
			"select id, name, clan_id from $cfg[dbname].users where id = $id" . ($name ? " or name like '%$name%'" : ""),
			"get"
		);
		
		if($core_db->get_num_rows($result)) {
			if($name) echo("Seach \"$name\" found...<p>");
			while ($row = $core_db->fetch_row($result)) {
				echo("$row[id]: <a href=stats.php?action=user_info&id=$row[id]>$row[name]</a>");
				$clan_name = $core_db->lookup("select name from $cfg[dbname].clans where id = $row[clan_id]");
				if (!$clan_name) $clan_name = "n/a";
				echo(" ($clan_name)<br>\n");
			}
		} else {
			echo("Couldn't find users.");
		}
		
	break;
	
	case "user_info":
		
		$id = $id + 0;
		
  	$result = $core_db->query(
			"select id, name, clan_id, access, create_id, edit_id,
			DATE_FORMAT(create_date,'%H:%i %d/%m/%y') as create_date,
			DATE_FORMAT(edit_date,'%H:%i %d/%m/%y') as edit_date
			from $cfg[dbname].users where id = $id",
			"get"
		);
		
		if($core_db->get_num_rows($result)) {
			while ($row = $core_db->fetch_row($result)) {
				echo("<b>Username:</b> $row[name]<br>\n");
				echo("<b>Password:</b> <a href=security.php?action=password&id=$row[id]>Reset</a><br>\n");
				$clan = $core_db->lookup("select name from $cfg[dbname].clans where id = $row[clan_id]");
				echo("<b>Clan:</b> <a href=http://$clan.clan-hq.net target=_blank>$clan.clan-hq.net</a><p>\n");
				$create_name = $core_db->lookup("select name from $cfg[dbname].users where id = $row[create_id]");
				if (!$create_name) $create_name = "n/a";
				echo("<b>Created:</b> $row[create_date] ($create_name)<br>\n");
				$edit_name = $core_db->lookup("select name from $cfg[dbname].users where id = $row[edit_id]");
				if (!$edit_name) $edit_name = "n/a";
				echo("<b>Edited:</b> $row[edit_date] ($edit_name)<p>\n");
				echo("<b>Access:</b> $row[access]<p>\n");
			}
		} else {
			echo("Error: Couln't get user!");
		}
		
	break;
}

echo("<p><a href=javascript:history.back()>Go back</a> | ");
echo("<a href=/{$cfg['basedir']}/>Main menu</a>");