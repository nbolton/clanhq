<?

include("admin.php");

switch($action) {
	
	case "map":
		
  	echo(
			"<form method=post action=search.php?action=seek_map>\n".
			"Map name: <input type=text name=map size=20><p>\n".
			"<input type=submit value=Go>\n".
			"</form>"
		);
		
	break;
	
	case "seek_map":
		
		echo("Searching... ");
		
		$dir = "images/maps/";
		if (is_dir($dir)) {
			echo("<font color=#00CC00><b>Found</b></font> dir!");
		} else {
			echo("<font color=#FF0000><b>Couldn't find</b></font> dir!");
		}
		
		echo("<br>");
		
		$maps = explode(" // ", $map);
		$map = implode("; ", $maps);
		$maps = explode("; ", $map);
		
		foreach($maps as $map) {
			
			// Get rid of whitespace.
			$map = trim($map);
			
			$foundmap = seek_file($map, $dir, array('jpg', 'gif'));
			
			if(!strstr($foundmap, "map_not_found")) {
				echo("<font color=#00CC00><b>Found</b></font> <b>$map</b> in <b>$dir</b>!");
			} else {
				echo("<font color=#FF0000><b>Couldn't find</b></font> <b>$map.*</b> in <b>$dir</b>!");
			}
			
			echo("<br>\n");
		}
		
	break;
}

echo("<p><a href=javascript:history.back()>Go back</a> | ");
echo("<a href=/{$cfg['basedir']}/>Main menu</a>");