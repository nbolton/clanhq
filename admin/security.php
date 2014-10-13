<?

include("admin.php");

$confirm_actions = array(
	"update_privs", "reset_privs"
);

if (!$confirm && in_array($action, $confirm_actions)) {
	die("Are you sure? <a href='$REQUEST_URI&confirm=1'>Yes</a> | <a href='javascript:history.back()'>No</a>");
}

switch($action) {
	
	case "update_privs":
		
		echo("Updating... ");
		
		$result = $core_db->query(
			"select id, access
			from $cfg[dbname].users",
			"get all members"
		);
		
		if($core_db->get_num_rows($result)) {
			
			while ($row = $core_db->fetch_row($result)) {
				
				$i = 0;
				
				foreach(explode('|', $row['access']) as $access) {
					$access = explode(';', $access);
					$mod_id = $access[0];
					$privs = $access[1];
					$valid_actions = explode(', ', $core_db->lookup("select actions from $cfg[dbname].modules where id = '$mod_id'"));
					$i = -1;
					foreach($valid_actions as $action) {
						$i++;
						$input[$mod_id.'|'.$action] = ($privs[$i] == 'x' ? 'x' : FALSE);
					}
				}
				
				// Build Access Structure array from access model.
				foreach(explode('|', $cfg['access_model']) as $access) {
					$access = explode(';', $access);
					$mod_id = $access[0];
					$privs = $access[1];
					$valid_actions = explode(', ', $core_db->lookup("select actions from $cfg[dbname].modules where id = $mod_id"));
					foreach($valid_actions as $action) {
						// If default access is 'x', override.
						if($privs[$i] == 'x') $input[$mod_id.'|'.$action] = 'x';
						$access_structure[$mod_id][$action] = ($input[$mod_id.'|'.$action] ? 'x' : '-');
					}
				}
				
				// Build Access Protocol String from Access Structure Array
				$access_protocol = FALSE;
				foreach($access_structure as $access_element) {
					$i++;
					$access_protocol .= "$i;";
					foreach($access_element as $action => $access) {
						$access_protocol .= $access;
					}
					$access_protocol .= "|";
				}
				
				$core_db->query(
					"update $cfg[dbname].users set
					access = '$access_protocol'
					where id = $row[id]",
					"update privs"
				);
			}
			$users_updated = $core_db->get_num_rows($result);
			echo("Done! Users updated: $users_updated");
		} else {
			echo("Failed! Couldn't get rows.");
		}
		
	break;
	
	case "reset_privs":
		
		echo("Resetting... ");
		
		$result = $core_db->query(
			"SELECT id, userlevel FROM users",
			"get all members"
		);
		
		if($core_db->get_num_rows($result)) {
			while ($row = $core_db->fetch_row($result)) {
				switch ($row['userlevel']) {
					default: $access = $cfg['public_access']; break;
					case 2: $access = $cfg['member_access']; break;
					case 3: $access = $cfg['admin_access']; break;
				}
				$core_db->query("UPDATE users SET access = '$access' WHERE id = $row[id]");
			}
			$users_updated = $core_db->get_num_rows($result);
			echo("Done! Users updated: $users_updated");
		} else {
			echo("Failed! Couldn't get rows.");
		}
		
	break;
	
	case "find_user":
		
  	echo(
			"<form method=post action=security.php?action=show_user>\n".
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
			"select id, name, clan_id from $cfg[dbname].users where id = $id" . ($name ? " or name like '%$name%'" : FALSE),
			"get"
		);
		
		if($core_db->get_num_rows($result)) {
			if($name) echo("Seach \"$name\" found...<p>");
			while ($row = $core_db->fetch_row($result)) {
				echo("$row[id]: <a href=security.php?action=password&id=$row[id]>$row[name]</a>");
				echo(" (" . $core_db->lookup("select name from $cfg[dbname].clans where id = $row[clan_id]") . ")<br>\n");
			}
		} else {
			echo("Couldn't find users.");
		}
		
	break;
	
	case "password":
		
		$id = $id + 0;
		$username = $core_db->lookup("select name from $cfg[dbname].users where id = $id");
		
		echo(
			"<form method=post action=security.php?action=password>\n".
			"\tEnter new password for <b>$username</b> (ID: $id)...<p>\n".
			"\t<input type=password name=password size=20> ".
			"\t<input type=submit value=Go>\n".
			"\t<input type=hidden name=id value='$id'>\n".
			"</form>"
		);
		
		if($password) {
			
			$password = md5($password);
			
			$core_db->query(
				"update $cfg[dbname].users set password = '$password' where id = $id",
				"reset pass");
			
			echo("<b>New password has been set!</b>");
		}
		
	break;
}

echo("<p><a href=javascript:history.back()>Go back</a> | ");
echo("<a href=/{$cfg['basedir']}/>Main menu</a>");