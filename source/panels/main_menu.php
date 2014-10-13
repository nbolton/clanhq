<?php

if ($cfg['show_pub_menu']) {
	
	$result = $core_db->query(
		"SELECT id, mod_id, action, spacer, custom, extra, admin
		FROM menus WHERE userlevel <= '{$cfg['userlevel']}'
		ORDER BY menu_ord ASC",
		"Select all items for main menu."
	);
	
	$i = 1;
	$menu = array();
	if (mysql_num_rows($result)) {
		
		while ($row = mysql_fetch_array($result)){
		
			$row['display'] = "";
			$row['mod'] = $cfg["modules"][$row["mod_id"]];
			$row['link'] = "?"
				.( $row['mod'] ? "mod=$row[mod]" : "")
				.( $row['action'] ? "&action=$row[action]" : "")
				.( $row['extra'] ? "&$row[extra]" : "");
			
			if ($row['custom']) {
				if (isset($lang[$row['custom']])) {
					$row['display'] = $lang[$row['custom']];
				}
			} else {
				if (isset($lang[$row['mod']])) {
					$row['display'] = $lang[$row['mod']];
				}
			}
			
			$hide_login = ((($row['mod'] == 'security') && ($row['action'] == 'login') && ($cfg['user_id'] != $cfg['pub_id']) ) ? true : "");
			$hide_logout = ((($row['mod'] == 'security') && ($row['action'] == 'logout') && ($cfg['user_id'] == $cfg['pub_id']) ) ? true : "");
			$hide_profile = ((($row['mod'] == 'members') && ($row['action'] == 'profile') && ($cfg['user_id'] == $cfg['pub_id']) ) ? true : "");
			
			
			if ($row['spacer']) {
		 	  $menu[] = "<tr><td height=10></td></tr>";
			} elseif ($hide_login || $hide_profile || $hide_logout) {
				$menu[] = "";
			} else {
				$menu[] = "<tr><td class=titleText><a href={$row['link']}>{$row['display']}</a></td>";
   			if ($cfg['admin'] && $row['admin']) {
					$alt_new = "{$row['display']} > {lang_create_new_item}";
					$alt_admin = "{$row['display']} > {lang_module_administration}";
					$menu[] = 
						"<td align=right width=1>".
						"<a href=?mod={$row['mod']}&action=create><img src=themes/{$cfg['theme_id']}/images/icon_new.gif alt='$alt_new' border=0></a>".
						"</td><td align=right width=1>".
						"<a href=?mod={$row['mod']}&action=admin><img src=themes/{$cfg['theme_id']}/images/icon_admin.gif alt='$alt_admin' border=0></a>".
						"</td></tr>";
				}
  		}
		}
	}
	$t->set_var('main_menu', implode($menu, "\n"));
}

?>