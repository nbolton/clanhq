<?

include("admin.php");

$invalid_chars = array(
	'~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '=', '<', '>',
	',', '.', '/', '?', ':', ';', '"', "'", '[', ']', '{', '}', '|', "\\", '£',
);

switch($action) {
	
	case "clan":
		
		if(!$step) $step = 1;
		
		switch($step) {
			
			case 1:
				
				if ($invalid_chars) {
					
					echo("<b>Invalid Characters!</b><br>");
					
					foreach($invalid_chars as $char) {
						$char = htmlentities($char);
						echo("$char ");
					}
				}
				
				echo(
					"<p><b>$error</b>\n".
					"<form method=post action=create.php?action=clan&step=2>\n".
  				"\tEnter address prefix: <input type=text name=prefix size=5>\n".
					"\t<input type=hidden name=step value=2><input type=submit value=Go>\n".
					"</form>"
				);
				
			break;
			
			case 2:
				
				$prefix = addslashes($prefix);
				
				if (!$prefix)
					die("<b>Warning!</b> You have not entered a prefix! Click <b>Go back</b> to try again.<p><a href=javascript:history.back()>Go back</a>");
				
				if ($invalid_chars) {
					foreach($invalid_chars as $char) {
						if (strstr($prefix, $char)) {
							$char = htmlentities($char);
							die("<b>Warning!</b> Prefix contains invalid character! ($char) Click <b>Go back</b> to try again.<p><a href=javascript:history.back()>Go back</a>");
						}
					}
				}
				
				if($core_db->lookup("select id from $cfg[dbname].clans where name = '$prefix'"))
					die("<b>Warning!</b> Prefix already exists!  Click <b>Go back</b> to try again.<p><a href=javascript:history.back()>Go back</a>");
				
				echo(
					"<b>Prefix OK!</b>\n".
					"<p><b>$error</b>\n".
					"<form method=post action=create.php?action=clan>\n".
					"\t<b>Clan details</b><p>\n".
  				"\tName: <input type=text name=name size=20 value='$name'><p>\n".
					"\tTag: <input type=text name=tag size=5 value='$tag'><p>\n".
  				"\tAccount: " . sqllistbox("account", "select id, name from $cfg[dbname].acct_specs", $account) . "<p>\n".
  				"\tType: " . sqllistbox("clan_type", "select id, description from $cfg[dbname].clan_types", $account) . "<p>\n".
					"\t<b>Admin details</b><p>\n".
					"\tUsername: <input type=text name=username size=20 value='$username'><p>\n".
					"\tPassword: <input type=password name=password size=20><p>\n".
					"\tEmail: <input type=text name=email size=20 value='$email'><p>\n".
					"\t<input type=hidden name=step value=3>\n".
					"\t<input type=hidden name=prefix value='$prefix'>\n".
					"\t<input type=submit value=Go>\n".
					"</form>"
				);
				
			break;
			
			case 3:
				
				if(!$name || !$tag || !$username || !$password || !$email) {
					header(
						"Location:create.php?action=clan&step=2".
						"&error=Invalid%20name/tag/username/password!&name=$name".
						"&tag=$tag&prefix=$prefix&account=$account&clan_type=$clan_type".
						"&username=$username&email=$email"
					);
				}
				
				echo("Creating Clan... ");
				
				if(!$core_db->lookup("select id from $cfg[dbname].clans where name = '$prefix'")) {
					
					$prefix = addslashes($prefix);
					$name = addslashes($name);
					$tag = addslashes($tag);
					$account = addslashes($account);
					$clan_type = addslashes($clan_type);
					$core_db->query (
						"INSERT clans SET
						name = '$prefix',
						display = '$name',
						tag = '$tag',
						account = '$account',
						clan_type = '$clan_type',
						create_date = now(),
						joindate = now()",
						"insert clan"
					);
					$clan_id = mysql_insert_id();
					echo("<b>Done!</b><br>");
					
					echo("Creating Public User... ");
					$core_db->query (
						"INSERT users SET
						name = 'Public',
						userlevel = 1,
						clan_id = '$clan_id',
						create_id = 0,
						edit_id = 0,
						create_date = now(),
						edit_date = now(),
						logged_in = 'N',
						admin = 'N',
						access = '$cfg[public_access]'",
						"insert public"
					);
					$pub_id = mysql_insert_id();
					echo("<b>Done!</b><br>");
					
					echo("Updating Clan... ");
					$core_db->query (
						"UPDATE clans SET pub_id = $pub_id WHERE id = $clan_id",
						"update clan"
					);
					echo("<b>Done!</b><br>");
					
					echo("Creating Admin... ");
					$username = addslashes($username);
					$email = addslashes($email);
					$password = ( !$password ? md5(uniqid(microtime(),1)) : md5($password) );
					$core_db->query (
						"INSERT users SET
						name = '$username',
						password = '$password',
						userlevel = 3,
						rank_id = 5,
						email = '$email',
						clan_ord = 1,
						clan_id = $clan_id,
						create_id = 0,
						edit_id = 0,
						create_date = now(),
						edit_date = now(),
						logged_in = 'N',
						admin = 'Y',
						access = '$cfg[admin_access]'",
						"insert admin"
					);
					$user_id = mysql_insert_id();
					echo("<b>Done!</b><br>");
					
					echo("Creating Global Settings... ");
					
					// Are we a TFC clan? If so, set scroring to individual.
					switch($core_db->lookup("SELECT clan_type FROM clans WHERE id = $clan_id")) {
						case 18: $cfg['scoring'] = 'individual'; break;
					}
					
					$username = addslashes($username);
					$email = addslashes($email);
					$password = ( !$password ? md5(uniqid(microtime(),1)) : md5($password) );
					$core_db->query (
						"INSERT settings_global SET
						clan_id = '$clan_id',
						
						theme_id = '$cfg[theme_id]',
						default_mod = '$cfg[default_mod]',
						lang = '$cfg[lang]',
						scoring = '$cfg[scoring]',
						select_logo	=	'$cfg[select_logo]',
						select_server = '$cfg[select_server]',
						
						show_admin_menu = '$cfg[show_admin_menu]',
						show_headlines = '$cfg[show_headlines]',
						show_site_stats = '$cfg[show_site_stats]',
						show_poll = '$cfg[show_poll]',
						show_next_fixture = '$cfg[show_next_fixture]',
						show_last_result = '$cfg[show_last_result]',
						show_match_stats = '$cfg[show_match_stats]',
						show_buddy_list = '$cfg[show_buddy_list]',
						show_server_watch = '$cfg[show_server_watch]',
						show_messages	= '$cfg[show_messages]',
						
						avatar_news	=	'$cfg[avatar_news]',
						avatar_comments	=	'$cfg[avatar_comments]',
						avatar_reports	=	'$cfg[avatar_reports]'",
						"make global settings"
					);
					$user_id = mysql_insert_id();
					echo("<b>Done!</b><br>");
					
					echo("Finding clan number... ");
					$num_clan = $core_db->lookup("select COUNT(*) from $cfg[dbname].clans") + 1;
					$lang['admin_default_welcome_body'] = str_replace("{num_clan}", $num_clan, $lang['admin_default_welcome_body']);
					echo("<b>Done!</b><br>");
					
					echo("Creating welcome post... ");
					$datetime = date("Y/m/d H:i:10");
					$core_db->query (
						"INSERT news SET
						userlevel = 1,
						clan_id = $clan_id,
						create_id = 10,
						edit_id = 10,
						create_date = '$datetime',
						edit_date = '$datetime',
						title = '$lang[admin_default_welcome_title]', 
						body = '$lang[admin_default_welcome_body]',
						edit_note = 'N'",
						"insert welcome post"
					);
					echo("<b>Done!</b><br>");
					
					echo("Creating howto post... ");
					$datetime = date("Y/m/d H:i:50");
					$core_db->query (
						"INSERT news SET
						userlevel = 2,
						clan_id = $clan_id,
						create_id = 10,
						edit_id = 10,
						create_date = '$datetime',
						edit_date = '$datetime',
						title = '$lang[admin_default_howto_title]', 
						body = '$lang[admin_default_howto_body]',
						edit_note = 'N'",
						"insert howto post"
					);
					echo("<b>Done!</b><br>");
					
					echo("<br><b>Clan created successfully!</b>");
					echo("<br>URL: <b><a href=http://$prefix.clan-hq.net target=_blank>$prefix.clan-hq.net</a></b>");
					
				} else {
					
					// We have found the clan already...
					echo("<b>Clan already exists!</b>");
				}
				
			break;
		}
		
	break;
}

echo("<p><a href=javascript:history.back()>Go back</a> | ");
echo("<a href=/{$cfg['basedir']}/>Main menu</a>");