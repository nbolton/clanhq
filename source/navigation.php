<?php

class navigation {
	
	var	$debug	= "";
	var $navs 	= array();
	
	/* ================================================== */
	// Sets the initial navigation link - usualy the site name.
	/* ================================================== */
	
	function navigation($initial_navs = "") {
		
		// Sets the initial nav links for this page...
		if (is_array($initial_navs)) {
			// So you want to set more than one link?
			foreach($initial_navs as $nav) {
				$this->navs[] = $nav;
			}
		} elseif ($nav = $initial_navs) {
			$this->navs[] = $nav;
		}
	}
	
	/* ================================================== */
	// Set a bunch of hierarchal links on the page so 
	// users can find their way around easier!
	/* ================================================== */
	
	function nav_links() {
		
		global $cfg, $lang, $core_db, $id, $type_id, $type;
		
		// If we're using the new system...
		if($this->navs) {
			if(is_array($this->navs)) {
				foreach($this->navs as $n1) {
					if (!is_array($n1)) {
						// For strings, just append the array.
						if ($n1) $navs[] = trim($n1);
					} else {
						// For arrays, do a verbose append on the array.
						foreach($n1 as $n2) {
							if ($n2) $navs[] = trim($n2);
						}
					}
				}
				$pathfinder = implode(" &raquo; ", $navs);
			} else {
				$pathfinder = trim($this->navs);
			}
		}
		
		$pathfinder .= "<p><br><br />\n";
		
		return ereg_replace("<br>|<p>*", '', $pathfinder);
	}
	
	/* ================================================== */
	// Make a cool little page index thingy so users can 
	// browse through huge lists with multiple pages!
	/* ================================================== */
	
	function page_index($mysql_count, $extra_ref = "", $use_db = "") {
		
		global $t, $cfg, $start, $core_db, $REQUEST_URI;
		
		if (!$use_db) {
			$use_db = $core_db;
		}
		
		$total_rows = $use_db->lookup($mysql_count, "page_index: mysql_count");
		
		$limit = $cfg['browsers_limit'];
		$end = $start + $limit;
		$start++;
		
		$rows_shown = (($end < $total_rows) ? $end : $total_rows);
		$nav_page_index_inf = "Showing items <b>$start</b> to <b>$rows_shown</b> (out of <b>$total_rows</b>).<br>";
		
		// Remove the existing var settings...
		$REQUEST_URI = preg_replace("/&start=[0-9]+/", "", $REQUEST_URI);
		
		if($total_rows > $cfg['browsers_limit']) {
			
			$back_link = ($start - $limit - 1) ;
			if($start > 1) $nav_page_index[] =	"<a href={this_ref}$extra_ref&start=$back_link>&laquo; Last $limit</a>";
			
			$pages = $total_rows / $limit;
			$pages = explode(".", $pages);
			if($pages[1] > 0) $pages[0]++;
			$pages = $pages[0];
			
			for($i = 1; $i <= $pages; $i++) {
				$link = (($i * $limit) - $limit);
				$nav_page_index[] =	(($link != ($start - 1)) ? ("<a href={this_ref}$extra_ref&start=$link>") : "<b>")."$i</a></b>";
			}
			
			$forward_link = ($start + $limit - 1);
			$next_num_rows = $total_rows - $rows_shown;
			if($next_num_rows > $limit) $next_num_rows = $limit;
			if($next_num_rows > 0) $nav_page_index[] =	"<a href={this_ref}$extra_ref&start=$forward_link>Next $next_num_rows &raquo;</a>";
		}
		
		if (!isset($nav_page_index)) {
			$nav_page_index = "";
		}
		
		$t->set_var('nav_page_index', $nav_page_index_inf . (is_array($nav_page_index) ? implode(" | ", $nav_page_index) : ""));
	}
	
	function set_results_nav($id_type = "") {
		
		/* -----------------------------------------------
		| Sets the title extension as opposing clan name
		| based onbased on if theres an ID, if not,
		| uses custom name and tag for clan.
		| Also shows what type of match was used.
		+ ------------------------------------------------
		| Added: v3.8 Alpha Mod (r3n)
		+ --------------------------------------------- */
		
		global $row, $cfg, $id, $type_id, $core_db;
		
		$fix_id = ($id_type == 'type_id') ? ($type_id + 0) : ($id + 0);
		
		$row['vs_id'] = $core_db->lookup("select vs_id from fixtures where id = '$fix_id' and clan_id = {$cfg['clan_id']}");
		$row['vs_tag'] = $core_db->lookup("select vs_tag from fixtures where id = '$fix_id' and clan_id = {$cfg['clan_id']}");
		$row['vs_name'] = $core_db->lookup("select vs_name from fixtures where id = '$fix_id' and clan_id = {$cfg['clan_id']}");
		if ($row['vs_id'] == 0) {
			if ($row['vs_name'] && $row['vs_tag']) { $row['tag'] = $row['vs_tag']; }
				$row['clan'] = $row['vs_name'];
		} else {
			$vs_tag = $core_db->lookup("select tag from clans where id = $row[vs_id]");
			$vs_name = $core_db->lookup("select display from clans where id  = $row[vs_id]");
			if ($vs_tag) { $row['tag'] = $vs_tag; }
			$row['clan'] = $vs_name;
		}
		
		$row['match_type'] = $core_db->lookup("select match_type from fixtures where id = $fix_id and clan_id = {$cfg['clan_id']}");
		$row['type'] = $core_db->lookup("select name from match_types where (clan_id = 0 or clan_id = {$cfg['clan_id']}) and id = '$row[match_type]'");
		return("$row[clan]" . ($row['type'] ? " ($row[type])" : FALSE));
	}
}