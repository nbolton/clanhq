<?php

class Template {
	var $classname			= "Template";
	var $debug					= FALSE;
	var	$kb_debug				=	FALSE;
	var $kb_prefix			=	"Kill";
	var $root						= FALSE;
	var	$this_block			=	FALSE;
	var $file						= array();
	var $varkeys				= array();
	var $varvals				= array();
	var $halt_on_error	= "yes";
	var $last_error		 	= "";
	var $block_cache		= array();
	var $default_template		=	"master.ihtml";
	
	/* "remove"	=> remove undefined variables
	 * "comment" => replace undefined variables with comments
	 * "keep"		=> keep undefined variables
	 */
	var $unknowns			 = "remove";
	
	/***************************************************************************/
	/* public: Constructor.
	 * root:		 template directory.
	 * unknowns: how to handle unknown variables.
	 */
	function Template($root = ".", $unknowns = "remove") {
		$this->set_root($root);
		$this->set_unknowns($unknowns);
	}
	
	/* public: setroot(pathname $root)
	 * root:	 new template directory.
	 */	
	function set_root($root) {
		if (!is_dir($root)) {
			$this->halt("set_root: $root is not a directory.");
			return false;
		}
		
		$this->root = $root;
		return true;
	}
	
	/* public: set_unknowns(enum $unknowns)
	 * unknowns: "remove", "comment", "keep"
	 *
	 */
	function set_unknowns($unknowns = "keep") {
		$this->unknowns = $unknowns;
	}
	
	/* public: set_file(array $filelist)
	 * filelist: array of handle, filename pairs.
	 *
	 * public: set_file(string $handle, string $filename)
	 * handle: handle for a filename,
	 * filename: name of template file
	 */
	function set_file($handle, $filename = "") {
		
		// Do we want to use the default template set by ModuleManagement?
		if (!$filename) {
			global $mm;
			$filename = $mm->template;
		}
		
		if (!is_array($handle)) {
			$this->file[$handle] = $this->filename($filename);
		} else {
			reset($handle);
			while (list($h, $f) = each($handle)) {
				$this->file[$h] = $this->filename($f);
			}
		}
	}
	
	/* public: set_block(string $parent, string $handle, string $name = "")
	 * extract the template $handle from $parent, 
	 * place variable {$name} instead.
	 */
	function set_block($parent, $handle = "", $name = "", $ignore_block = "") {
		
		// For standalone files.
		if (!$handle) { $handle = $parent; }
		
		// Clear the cache from the last parent.
		$this->clear_cache();
		
		if (!$ignore_block) $this->this_block = $handle;
		
		if (!$this->loadfile($parent)) {
			$this->halt("subst: unable to load $parent.");
			return false;
		}
		
		if ($name == "")
			$name = $handle;
		
		$str = $this->get_var($parent);
		$reg = "/<!--\s+BEGIN $handle\s+-->(.*)\n\s*<!--\s+END $handle\s+-->/sm";
		preg_match_all($reg, $str, $m);
		$str = preg_replace($reg, "{" . "$name}", $str);
		$this->set_var($handle, (isset($m[1][0]) ? $m[1][0] : FALSE));
		$this->set_var($parent, $str);
	}
	
	/* public: set_var(array $values)
	 * values: array of variable name, value pairs.
	 *
	 * public: set_var(string $varname, string $value)
	 * varname: name of a variable that is to be defined
	 * value:	 value of that variable
	 */
	function set_var($varname, $value = "") {
		if (!is_array($varname)) {
			if (!empty($varname))
				if ($this->debug) print "scalar: set *$varname* to *$value*<br>\n";
				$this->varkeys[$varname] = "/".$this->varname($varname)."/";
				$this->varvals[$varname] = $value;
		} else {
			reset($varname);
			while (list($k, $v) = each($varname)) {
				if (!empty($k))
					if ($this->debug) print "array: set *$k* to *$v*<br>\n";
					$this->varkeys[$k] = "/".$this->varname($k)."/";
					$this->varvals[$k] = $v;
			}
		}
	}
	
	/* public: subst(string $handle)
	 * handle: handle of template where variables are to be substituted.
	 */
	function subst($handle) {
		if (!$this->loadfile($handle)) {
			$this->halt("subst: unable to load $handle.");
			return false;
		}
		
		$str = $this->get_var($handle);
		$str = @preg_replace($this->varkeys, $this->varvals, $str);
		return $str;
	}
	
	/* public: psubst(string $handle)
	 * handle: handle of template where variables are to be substituted.
	 */
	function psubst($handle) {
		print $this->subst($handle);
		
		return false;
	}
	
	/* public: parse(string $target, string $handle, boolean append)
	 * public: parse(string $target, array	$handle, boolean append)
	 * target: handle of variable to generate
	 * handle: handle of template to substitute
	 * append: append to target handle
	 */
	function parse($target, $handle, $append = false) {
		if (!is_array($handle)) {
			$str = $this->subst($handle);
			if ($append) {
				$this->set_var($target, $this->get_var($target) . $str);
			} else {
				$this->set_var($target, $str);
			}
		} else {
			reset($handle);
			while (list($i, $h) = each($handle)) {
				$str = $this->subst($h);
				$this->set_var($target, $str);
			}
		}
		
		return $str;
	}
	
	function pparse($target, $handle, $append = false) {
		print $this->parse($target, $handle, $append);
		return false;
	}
	
	/* public: get_vars()
	 */
	function get_vars() {
		reset($this->varkeys);
		while (list($k, $v) = each($this->varkeys)) {
			$result[$k] = $this->varvals[$k];
		}
		
		return $result;
	}
	
	/* public: get_var(string varname)
	 * varname: name of variable.
	 *
	 * public: get_var(array varname)
	 * varname: array of variable names
	 */
	function get_var($varname) {
		if (!is_array($varname)) {
			if (isset($this->varvals[$varname])) {
				return $this->varvals[$varname];
			} else {
				return FALSE;
			}
		} else {
			reset($varname);
			while (list($k, $v) = each($varname)) {
				$result[$k] = $this->varvals[$k];
			}
			
			return $result;
		}
	}
	
	/* public: get_undefined($handle)
	 * handle: handle of a template.
	 */
	function get_undefined($handle) {
		if (!$this->loadfile($handle)) {
			$this->halt("get_undefined: unable to load $handle.");
			return false;
		}
		
		preg_match_all("/\{([^}]+)\}/", $this->get_var($handle), $m);
		$m = $m[1];
		if (!is_array($m))
			return false;
		
		reset($m);
		while (list($k, $v) = each($m)) {
			if (!isset($this->varkeys[$v]))
				$result[$v] = $v;
		}
		
		if (COUNT($result))
			return $result;
		else
			return false;
	}
	
	/* public: finish(string $str)
	 * str: string to finish.
	 */
	function finish($str) {
		global $lang, $core_db, $cfg;
		
		if (avg_load(TRUE) > $cfg['log_load_point']) {
			$core_db->connect($cfg['dblogs'], $cfg['dbuser'], $cfg['dbpass'], $cfg['dbhost']);
			add_log(avg_load(TRUE), "load_logs", PageLoadTime());
			$core_db->close_db();
		}
		
		$str = str_replace("{load_time}", "$lang[page_proccessed_in] <b>" . PageLoadTime() . "</b> $lang[seconds].", $str);
		
		switch ($this->unknowns) {
			case "keep":
			break;
			
			case "remove":
				$str = preg_replace('/{[^ \t\r\n}]+}/', "", $str);
			break;
			
			case "comment":
				$str = preg_replace('/{([^ \t\r\n}]+)}/', "<!-- Template $handle: Variable \\1 undefined -->", $str);
			break;
		}
		
		return $str;
	}
	
	/* public: p(string $varname)
	 * varname: name of variable to print.
	 */
	function p($varname) {
		print $this->finish($this->get_var($varname));
	}
	
	function get($varname) {
		return $this->finish($this->get_var($varname));
	}
		
	/***************************************************************************/
	/* private: filename($filename)
	 * filename: name to be completed.
	 */
	function filename($filename) {
		if (substr($filename, 0, 1) != "/") {
			$filename = $this->root."/".$filename;
		}
		
		if (!file_exists($filename))
			$this->halt("filename: file $filename does not exist.");
		
		return $filename;
	}
	
	/* private: varname($varname)
	 * varname: name of a replacement variable to be protected.
	 */
	function varname($varname) {
		return preg_quote("{".$varname."}");
	}
	
	/* private: loadfile(string $handle)
	 * handle:	load file defined by handle, if it is not loaded yet.
	 */
	function loadfile($handle) {
		if (isset($this->varkeys[$handle]) and !empty($this->varvals[$handle]))
			return true;
		
		if (!isset($this->file[$handle])) {
			$this->halt("loadfile: $handle is not a valid handle.");
			return false;
		}
		$filename = $this->file[$handle];
		
		$str = implode("", @file($filename));
		if (empty($str)) {
			$this->halt("loadfile: While loading $handle, $filename does not exist or is empty.");
			return false;
		}
		
		$this->set_var($handle, $str);
		
		return true;
	}
	
	/***************************************************************************/
	/* public: halt(string $msg)
	 * msg:		error message to show.
	 */
	function halt($msg) {
		$this->last_error = $msg;
		
		if ($this->halt_on_error != "no")
			$this->haltmsg($msg);
		
		if ($this->halt_on_error == "yes")
			die("<b>Halted.</b>");
		
		return false;
	}
	
	/* public, override: haltmsg($msg)
	 * msg: error message to show.
	 */
	function haltmsg($msg) {
		print("<b>CMS Error:</b> $msg<br>\n");
	}
	
	/*
	* public: cache_block(string $block)
	* stores last killed block
	*/
	function clear_cache() {
		$this->block_cache = "";
	}
	
	/*
	* public: cache_block(string $block)
	* stores last killed block
	*/
	function cache_block($block, $handle) {
		if($block){
			$this->block_cache[$handle] = $block;
		}
	}
	
	/*
	* public: restore_block(string $block)
	* restores a block
	*/
	function restore_block($handle) {
		$kb_handle = $this->kb_prefix . $handle;
		
		if (isset($this->block_cache[$handle])) {
			$block = "<!-- BEGIN $handle -->" . $this->block_cache[$handle] . "<!-- END $handle -->";
		} else {
			$block = "";
		}
		
		$this->set_var($kb_handle, $block);
	}
	
	/* public: kill_block(string $parent, string $handle)
	 * extract the template $handle from $parent, 
	 * place variable {$name} instead.
	 */
	function kill_block($handle, $prefix = FALSE, $parent = FALSE, $has_value = FALSE) {
		
		if (!$parent) $parent = $this->this_block;
		if ($prefix) $handle = $prefix . $handle;
		
		if (!$this->loadfile($parent)) {
			$this->halt("subst: unable to load $parent.");
			return false;
		}
		
		if ($this->kb_debug) echo("PARENT: *$parent* // HANDLE: *$handle*<br>\n");
		
		$str = $this->get_var($parent);
		$reg = "/<!--\s+BEGIN $handle\s+-->(.*)\n\s*<!--\s+END $handle\s+-->/sm";
		preg_match_all($reg, $str, $m);
		 $str = preg_replace($reg, "{". $this->kb_prefix . "$handle}", $str);
		
		if (isset($m[1][0])) {
			$this->cache_block($m[1][0], $handle);
			 $this->set_var($handle, $m[1][0]);
		} else {
		 $this->set_var($this->kb_prefix . $handle, FALSE);
		}
		$this->set_var($parent, $str);
	}
	
	/*
	*
	*/
	function set_array($row, $prefix = "", $parent = "", $na = TRUE, $striptags = "", $sripslashes = TRUE) {
		global $lang, $cfg, $ref, $type, $id, $print;
		
		if ($row) {
			
			foreach($row as $key => $value) {
				$restore_key = $prefix . $key;
				if(!strstr($restore_key, "lang_")) { // make sure not a lang array
					$this->restore_block($restore_key);
				}
			}
			
			foreach($row as $key => $value) {
				if ($sripslashes) $value = stripslashes($value);
				if ($striptags) $value = strip_tags($value);
				if (!$value && ($value != '0')) {
					if ($na) $value = $lang['na'];
					$this->kill_block($key, $prefix, $parent);
				}
				if ($value == "00:00 00/00/00" && $value != '0') $value = 'Never';
				$this->set_var($prefix . $key, $value);
			}
		}
	}
	
	/*
	*
	*/
	function set_msg($msg, $hide_backto = false, $show_create = false, $hide_goback = false, $hide_create = false, $template = "subTemplate") {
		global $lang, $cfg, $id, $type, $nav;
		$this->set_file($template, "msg.ihtml");
		$this->set_block($template, $template);
		$this->set_msg_var();
		$this->set_var('msg', $lang[$msg]);
		$this->set_var('id', $id);
		$this->set_var('type', $type);
		if($hide_backto) $this->set_var('hide_backto', '<!--');
		if(!$cfg['admin'] || $hide_create) $this->set_var('hide_create_link', '<!--');
		$nav->navs[] = $lang["error"];
	}
	
	/*
	*
	*/
	function set_msg_var($override_mod = FALSE, $message_var = 'message'){
		global $cfg, $lang, $core_db;
		
		if (isset($_REQUEST['msg'])) {
			$msg = $_REQUEST['msg'];
		} else {
			$msg = "";
		}
		
		$msg_array = $cfg['valid_message_postifxes'];
		
		// Override the module?
		if (strstr($msg, ";")) {
			
			$override_id = preg_replace("/([0-9]);.*/", "$1", $msg) + 0;
			$override_mod = $cfg["modules"][$override_id];
			$msg = preg_replace("/.*;(.*)/", "$1", $msg);
		}
		
		$mod = ($override_mod ? $override_mod : $cfg['mod']);
		
		if ($msg) {
			foreach($msg_array as $value) {
				$value = explode('; ', $value);
				if (($msg == $value[0]) && isset($lang[$mod . "_{$value[0]}"])) { // match found
					$message = $lang[$mod . "_{$value[0]}"];
					if (!isset($value[1])) $value[1] = FALSE;
					$class = (!$value[1] ? 'bodyGood' : $value[1]);
					$hide_msg = false;
					break; // exit loop
				} else {
					$hide_msg = true;
				}
			}
			if (!isset($class)) { $class = ""; }
			if (!isset($message)) { $message = ""; }
			$this->set_var($message_var, "<span class=$class>" . ($value[1] ? "<b>{$lang['warning']}</b> " : false) . "$message</span>");
		} else {
			$hide_msg = true;
		}
		
		if ($hide_msg) $this->kill_block($message_var, FALSE, "subTemplate");
	}
	
	/* DEPRECATED. Use ModuleManagement->check_privs_specific(). */
	function check_privs($this_module_id, $this_action) {
		
		global $mm;
		return $mm->check_privs_specific($this_module_id, $this_action);
	}
	
	/*
	*
	*/
	function mparse($target, $handle, $template = ""){
		
		global
			$cfg, $p, $r, $e, $ref, $cat, $id, $lang, $HTTP_HOST, $HTTP_REFERER, $REQUEST_URI,
			$preview, $print, $id, $type_id, $type, $ext_id, $core_db, $nav, $mm, $files;
		
		if (!$template) $template = $this->default_template;
		
		$this->parse($target, $handle);
		
		$cfg['id'] = $id;
		
		if (isset($preview) || isset($popup)) $template = "popup.ihtml";
		
		$this->set_file('masterTemplate', $template);
		
		/*
			Only do this for the "master" template!
			"help" templates dont need them.
		*/
		if ($template == $this->default_template) {
			include "./source/panels.php";
			$panels = new PanelControler('masterTemplate');
		}
		
		if ($template == "messages_master.ihtml") {
			// I am using the templates system.
			set_msg_counts();
		}
		
		if (isset($cfg['select_logo'])) {
			$cfg['logo_id'] = $files->exists($cfg['select_logo']);
		}
		
		if ($cfg['select_logo'] && $cfg['logo_id']) {
			// Ah, I see you have a logo!
			$cfg['clan_logo'] = "<div align={$cfg['logo_align']}><a href=?>".
				"<img src=?mod=files&action=getimage&id={$cfg['select_logo']} ".
				"border=0 alt=\"{$cfg['site_name']}\"></a></div>";
	    }
		
		$search[0] = "/[&|?]msg=[0-9]*;*[a-z]*/";		// Remove message init.
		$search[1] = "/[&|?]#[a-z]+[0-9]+/";	// Remove anchor ref.
		$replace[0] = "";
		$replace[1] = "";
		$cfg['last_ref'] = preg_replace($search, $replace, $HTTP_REFERER . (!strstr($HTTP_REFERER, "?") ? "?" : ""));
		$cfg['this_ref'] = preg_replace($search, $replace, $REQUEST_URI . (!strstr($REQUEST_URI, "?") ? "?" : ""));
		
		/*
			Now we're going to attempt to remove
			multiple occurances of patterns in a string.
		*/
		$cfg['last_ref'] = explode("&", $cfg['last_ref']);
		$cfg['last_ref'] = array_unique($cfg['last_ref']);
		$cfg['last_ref'] = implode("&", $cfg['last_ref']);
		$cfg['this_ref'] = explode("&", $cfg['this_ref']);
		$cfg['this_ref'] = array_unique($cfg['this_ref']);
		$cfg['this_ref'] = implode("&", $cfg['this_ref']);
		
		if ($cfg['debug']) debug_info();
		$cfg['navigation'] = $nav->nav_links();
		$cfg['avg_load'] = avg_load();
		
		/*
			Retrieve the second element of the array recursively,
			as this will be the current module.
		*/
		if (isset($nav->navs[1])) {
			if (is_array($nav->navs[1])) {
				if (is_array($nav->navs[1][0])) {
					// Shouldn't ever go any further than this...
					$cfg['mod_name'] = $nav->navs[1][0][0];
				} else {
					$cfg['mod_name'] = $nav->navs[1][0];
				}
			} else {
				$cfg['mod_name'] = $nav->navs[1];
			}
		} else {
			$cfg['mod_name'] = "";
		}
		
		// Now, based on what we have, decide what to use...
		if ($cfg['mod_name'] && ($cfg['mod_name'] != $lang['error'])) {
			$cfg['mod_name'] = stripslashes(strip_tags($cfg['mod_name']));
		} else {
			$cfg['mod_name'] = $lang[$mm->module];
		}
		
		// Must have security privs, otherwise server gets spammed!
		if ($mm->check_privs_specific(17, 'default')) $cfg['active_iframe'] = "\n<iframe src=?mod=active name=active width=0 height=0></iframe>";
		if ($mm->check_privs_specific(19, 'default')) $cfg['server_watch_iframe'] = "\n<iframe src=?mod=server_watch name=server_watch width=0 height=0></iframe>";
		if ($mm->check_privs_specific(20, 'default')) $cfg['messages_iframe'] = "\n<iframe src=?mod=messages name=messages width=0 height=0></iframe>";
		
		// Goodbye DB!
		$core_db->close_db();
		
		// You speaka english?
		$this->set_array($lang, 'lang_', TRUE, FALSE, FALSE, FALSE);
		
		// Set configuration vars without sensitive info!
		$clean_cfg = $cfg;
		foreach($cfg['sensitive'] as $remove) { unset($clean_cfg[$remove]); }
		$this->set_var($clean_cfg);
		
		// Go gadget go!
		$this->parse($target, 'masterTemplate');
		$this->p($target);
	}
}