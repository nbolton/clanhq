<?

include("admin.php");

switch($action) {
	
	case "create":
		
		echo(
			"<p><b>$error</b>\n".
			"<form method=post action=message.php?action=send>\n".
  		"\tTitle...<br><input type=text name=title size=30><br>".
			"\tMessage...<br><textarea name=body rows=10 cols=50></textarea><br>\n".
			"\t<br><input type=submit value=Go>\n".
			"</form>"
		);
		
	break;
	
	case "send":
		
		echo("<b>Title...</b><br>".nl2br($title)."<p>\n");
		echo("<b>Message...</b><br>".nl2br($body)."<p>\n");
		
		$notice = "[b]Important:[/b] Do not reply to this message, it will not be answered; please email [email]admin@clan-hq.com[/email] instead.";
		
		$title = addslashes($title);
		$body = cmscode(addslashes($notice . "\n\n" . $body . "\n\n" . $notice), 'cmscode');
		
		$result = $core_db->query(
			"SELECT id FROM users
			WHERE clan_id != 0
			AND name != 'Public'
			AND name != 'Visitor'"
		);
		
		if ($core_db->get_num_rows($result)) {
			
			while ($row = $core_db->fetch_row($result)) {
				
				$i++;
				
				$core_db->query(
					"INSERT messages SET
					title = '$title',
					body = '$body',
					to_id = $row[id],
					from_id = {$cfg['user_id']},
					clan_id = {$cfg['clan_id']},
					unread = '1',
					type = 'I',
					send_date = now()"
				);
			}
		}
		
		echo("<b>Message sent to $i users!</b>");
		
	break;
}

echo("<p><a href=javascript:history.back()>Go back</a> | ");
echo("<a href=/{$cfg['basedir']}/>Main menu</a>");