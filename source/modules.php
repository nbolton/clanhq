<?php

class ModuleManagement {
  
	var $action = "";
	var $module = "";
	var $debug	=	FALSE;
	
  function validate($default_mod_id, $default_action) {
    
    global $core_db, $cfg;
		
		if (isset($_REQUEST['action'])) {
			$this->action = $_REQUEST['action'];
		}
		
		if (isset($_REQUEST['mod'])) {
			$this->module = $_REQUEST['mod'];
		}
		
		if (!$this->action) {
			$this->action = $default_action;
		}
		
		if (!$this->module) {
		
			// Assume default ID and get the name.
			$this->module_id = $default_mod_id;
			$this->module = $cfg["modules"][$this->module_id];
			
		} else {
			// Find the key for the matching value.
			$this->module_id = array_search($this->module, $cfg["modules"]); 
		}
		
		if ($this->module_id != 0) {
			$this->valid_actions = $cfg["actions"][$this->module_id];
			
		} else {
			$this->halt("Module '" . $this->module . "' does not exist!",
	  		"Non-existant Module", TRUE);
		}
    
    if (!file_exists("./source/modules/mod_" . $this->module . ".php")) {
		$this->halt("Module '" . $this->module . "' does not exist!",
	  		"Non-existant Module", TRUE);
    }
		
    if (!in_array($this->action, $this->valid_actions)){
      $this->halt("Action '$this->action' is bad!", "Bad Action", TRUE);
    }
		
    if (isset($_REQUEST['type']) && !file_exists("./source/modules/mod_{$_REQUEST['type']}.php")) {
      $this->halt("Module '{$_REQUEST['type']}' does not exist!", "Non-existant Module", TRUE);
    }
		
	  if (!$cfg['fileserver']) {
			$this->template = "{$this->module}_{$this->action}.ihtml";
    	$this->check_privs();
		}
  }
  
  function halt($msg, $title = "", $return_link = FALSE, $no_output = FALSE, $no_email = FALSE) {
    global $cfg;
		
		$ignore = "/" . implode("|", array(
			"DOCUMENT_ROOT", "SCRIPT_FILENAME", "GATEWAY_INTERFACE", "PATH",
			"SCRIPT_NAME", "SERVER_", "PATH_TRANSLATED", "PHP_SELF", "argc",
			"QUERY_STRING", "REQUEST_URI"
		)) . "/";
		
		/* Clickable link to test the URL. */
		if ($_SERVER['HTTP_HOST'] && $_SERVER['REQUEST_URI']) {
			$_SERVER['REQUEST_URL'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} elseif ($_SERVER['REQUEST_URI']) {
			$_SERVER['REQUEST_URL'] = $_SERVER['REQUEST_URI'];
		}
		
		/* Clickable link to find more info on the user. */
		if ($_SERVER['CMS_USER_ID'] = $cfg['user_id']) {
			$_SERVER['CMS_USER_INFO'] = "http://admin.clan-hq.com/stats.php?action=user_info&id={$_SERVER['CMS_USER_ID']}";
		}
		
		reset($_SERVER);
		ksort($_SERVER);
		
		foreach($_SERVER as $k => $v) {
			if ($v) {
				if (!is_array($v) && !preg_match($ignore, $k)) {
					$svars[$k] = str_replace(array("\n", "\r"), "", "$k => $v");
				}
			}
		}
		
		// Only send email if not in dev mode, and HTTP_USER_AGENT is not a crawler!
		if ((!$cfg['dev_mode'] && !preg_match("/(" . implode("|", $cfg['crawlers']) . ")/i", $_SERVER['HTTP_USER_AGENT'])) && !$no_email) {
			sendemail(
				$cfg['admin_email'],
				"CMS Error!" . ($title ? " ($title)" : ""),
				strip_tags($msg) . " \n\n".
				"======= CLIENT/SERVER INFO ======= \n".
				strip_tags(implode("    \n", $svars))
			);
		}
		
		if ($return_link) {
			$return_link = "<br>Return to <a href='http://{$_SERVER['HTTP_HOST']}'>{$_SERVER['HTTP_HOST']}</a>.";
		}
		
		if ($no_output) { return FALSE; }
    
		die(
			"<style><!-- {$cfg['error_style']} --></style>".
			"<div align='center' class='text'><a href='http://www.clan-hq.com/cms'>".
			"<img src='./images/system/clan_hq_badge.gif' border='0'></a>".
      "<br><br><b>$msg $return_link</b><br><br>For more information on CMS, please visit ".
			"<a href='http://www.clan-hq.com/cms'>www.clan-hq.com/cms</a>.<br>".
			"<br>If you have any questions, please email ".
			"<a href=mailto:{$cfg['support_email']}>{$cfg['support_email']}</a>.<br>".
      "<br>Thank You<br>The <b>Clan-HQ</b> Team".
      "<br><a href=mailto:{$cfg['support_email']}>{$cfg['support_email']}</a><br>".
			"<br><i>Clan-HQ :: Made For Gamers, By Gamers!</i></div>"
    );
  }
  
  function check_privs_specific($this_module_id, $this_action) {
    
    global $cfg, $core_db;
	
	if (isset($cfg["actions"][$this_module_id])) {
		$valid_actions = $cfg["actions"][$this_module_id];
		
	} else {
		return false;
	}
	
	
	$action_has_access  = false;
	
    foreach(explode('|', $cfg['access_protocol']) as $access) {
      $access = explode(';', $access);
      $mod_id = $access[0];
      if (isset($access[1])) $privs = $access[1];
      if ($mod_id == $this_module_id) {
        $i = -1;
		
        foreach($valid_actions as $valid_action) {
          $i++;
          if($this_action == $valid_action) {
            $action_has_access = ($privs[$i] == 'x' ? true : false);
          }
        }
        $mod_has_access[$mod_id] = true;
      } else {
        $mod_has_access[$mod_id] = false;
      }
    }
	
	if ($mod_has_access[$this_module_id] && $action_has_access) {
		return true;
	}
	
	return false;
  }
  
  function check_privs() {
    
    global $cfg, $core_db, $t;
	
		if ($cfg['clan_id']) {
   		if (!strstr($cfg['access_protocol'], '|') || !$this->check_privs_specific(12, 'denied')) {
    	  $this->halt(
					"Fatal Error: Your security privelages are unusable! <br>\n <br>  \n".
					"This requires immediate attention, please contact <br>  \n".
					"a member of the Clan-HQ team for assistance.",
					"Invalid security privelages"
				);
    	}
    	
			foreach(explode('|', $cfg['access_protocol']) as $access) {
    	  $access = explode(';', $access);
    	  $mod_id = $access[0];
    	  if (isset($access[1])) $privs = $access[1];
				
      	if ($this->debug) echo("MOD_ID: *$mod_id* // PRIVS: *$privs* <br>\n");
      	
      	if($mod_id == $this->module_id) {
        	$i = -1;
        	foreach($this->valid_actions as $valid_action) {
          	$i++;
          	if($this->action == $valid_action) {
            	$action_has_access = ($privs[$i] == 'x' ? true : false);
          	}
        	}
        	$mod_has_access[$mod_id] = true;
     		} else {
        	$mod_has_access[$mod_id] = false;
      	}
    	}
    	
    	if (!$mod_has_access[$this->module_id] || !$action_has_access) {
      	header("Location:http:?mod=security&action=denied&id=$this->module_id&msg=$this->action");
    	}
  	}
	}
  
  function filter($data, $type) {
    global $cfg, $lang;
    switch($type) {
      case 'int':
        $data ? $data = ($data + 0) : header("location:http:?mod=$this->module");
      break;
    }
  }
  
  function ifempty($data, $location = FALSE, $type = "http:") {
    if(!$location) $location = "?";
		if(!stristr($data, "http")) { $location = $type . $location; }
    if(!$data) {
			header("Location:$location");
			exit;
		}
  }
}