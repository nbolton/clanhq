<?

class DBDriver {
	
	var $obj = array(
		"Username"				=> "",
		"Password"				=> "",
		"Database"				=> "",
		"Hostname"				=> "localhost",
		"Portnum"					=> "",
		"persistent"			=> "0",
		"cached_queries"	=> array(),
		'debug'						=> 0,
	);
	
	var $query_id				= "";
	var $connection_id	= "";
	var $query_count		= 0;
	var $record_row			= array();
	var $return_die			= 0;
	var $error					= "";
	var $query_string		= "";
	var $query_info		="";
	
	var $ErrorString		=	"";
	var $ErrorNum				=	0;
	var $ErrorDate			=	"";
	
	/*========================================================================*/
	// Connect to the database                 
	/*========================================================================*/  
	
	function Connect($Database, $Username, $Password, $Hostname = '') {
		
		$DBConfig = array(
			'Username'	=>	$Username,
			'Database'	=>	$Database,
			'Password'	=>	$Password,
			'Hostname'	=>	$Hostname,
		);
		
		if (is_array($DBConfig)) {
			foreach($DBConfig as $ConfKey => $ConfValue) {
				$this->obj[$ConfKey] = $ConfValue;
			}
		}
		
		$this->connection_id = @mysql_connect(
			$this->obj['Hostname'],
			$this->obj['Username'],
			$this->obj['Password']
		);
		
		if (!$this->connection_id) {
			$this->FatalError("Connect MySQL.", "");
		}
		
		if (!mysql_select_db($this->obj['Database'], $this->connection_id)) {
			$this->FatalError("Select Database.", "");
		}
	}
	
	
	/*========================================================================*/
	// Process a query
	/*========================================================================*/
	
	function Query($Query, $Info = 'n/a') {
		
		//--------------------------------------
		// Change the table prefix if needed
		//--------------------------------------
		
		$this->query_info = $Info;
		$this->query_string = $Query;
		$this->query_id = mysql_query($Query, $this->connection_id);
		
		if (!$this->query_id) {
			$this->FatalError();
		}
		
		$this->query_count++;
		$this->obj['cached_queries'][] = $Query;
		return $this->query_id;
	}
	
	
	/*========================================================================*/
	// Get a value from a row which matches mysql query
	/*========================================================================*/
	
	function Lookup($Query, $Info = 'n/a') {
		$this->query($Query, "Lookup: $Info");
		if ($Row = $this->fetch_row()) {
			foreach($Row as $Key => $Value) {
				return $Value;
				break;
			}
		}
	}
	
	
	/*========================================================================*/
	// Fetch a row based on the last query
	/*========================================================================*/
	
	function FetchRow($QueryID = '') {
		return $this->fetch_row($QueryID);
	}
	
	function fetch_row($query_id = "", $get_ords = "") {
		
		if ($query_id == "") {
			$query_id = $this->query_id;
		}
		
		if (!$get_ords) {
			$this->record_row = mysql_fetch_assoc($query_id);
		} else {
			$this->record_row = mysql_fetch_row($query_id);
		}
		
		return $this->record_row;
	}
	
	/*========================================================================*/
	// Fetch the number of rows affected by the last query
	/*========================================================================*/
	
	function get_affected_rows() {
		return mysql_affected_rows($this->connection_id);
	}
	
	/*========================================================================*/
	// Fetch the number of rows in a result set
	/*========================================================================*/
	
	function GetNumRows($QueryID = '') {
		return $this->get_num_rows($QueryID);
	}
	
	function get_num_rows($query_id = "") {
		
		if ($query_id == "") {
			$query_id = $this->query_id;
		}
		
		if ($query_id) return @mysql_num_rows($query_id);
	}
	
	/*========================================================================*/
	// Fetch the last insert id from an sql autoincrement
	/*========================================================================*/
	
	function get_insert_id() {
		return mysql_insert_id($this->connection_id);
	}
	
	/*========================================================================*/
	// Return the amount of queries used
	/*========================================================================*/
	
	function get_query_cnt() {
		return $this->query_count;
	}
	
	/*========================================================================*/
	// Free the result set from mySQLs memory
	/*========================================================================*/
	
	function free_result($query_id = "") {
		
		if ($query_id == "") {
			$query_id = $this->query_id;
		}
		
		@mysql_free_result($query_id);
	}
	
	/*========================================================================*/
	// Shut down the database
	/*========================================================================*/
	
	function close_db() { 
		return mysql_close($this->connection_id);
	}
	
	/*========================================================================*/
	// Return an array of tables
	/*========================================================================*/
	
	function get_table_names() {
		
		$result = mysql_list_tables($this->obj['sql_database']);
		$num_tables = @mysql_numrows($result);
		
		for ($i = 0; $i < $num_tables; $i++) {
			$tables[] = mysql_tablename($result, $i);
		}
		
		mysql_free_result($result);
		
		return $tables;
	}
	
	/*========================================================================*/
	// Return an array of fields
	/*========================================================================*/
	
	function get_result_fields($query_id = "") {
		
		if ($query_id == "") {
			$query_id = $this->query_id;
		}
		
		while ($field = mysql_fetch_field($query_id)) {
			$Fields[] = $field;
		}
		
		return $Fields;
	}
	
	/*========================================================================*/
	// Basic error handler
	/*========================================================================*/
	
	function FatalError($Query = '', $Info = '') {
		return $this->fatal_error($Query, $Info);
	}
	
	function fatal_error($Query = '', $Info = '') {
		global $Conf, $mm;
		
		// Removes retarded info from newer versions of mySQL.
		$this->ErrorString = str_replace(".  Check the manual that corresponds to your ".
			"MySQL server version for the right syntax to use", "", mysql_error());
		$this->ErrorNum = mysql_errno();
		$this->ErrorDate = date("l dS of F Y h:i:s A");
		
		// Build up the error info...
		$DebugInfo = "";
		if ($this->query_string) $DebugInfo .= "<b>Query String:</b> $this->query_string <br>\n";
		if ($this->query_info) $DebugInfo .= "<b>Query Info:</b> $this->query_info <br>\n";
		
		$DebugInfo .= "<b>Error String:</b> $this->ErrorString <br>\n";
		$DebugInfo .= "<b>Error Code:</b> $this->ErrorNum <br>\n";
		$DebugInfo .= "<b>Error Date:</b> $this->ErrorDate <br>\n";
		
		$ReturnInfo = "<p><b>There appears to be an error with our database.</b></p>\n";
		
		if ($Conf['DevServer'] != $_SERVER['HTTP_HOST']) {
			
			Mail(
				$Conf['AdminEmail'], "MySQL Error!",
				'<style>' . Implode(File("css/mysql.css"), '') .  '</style>' . $DebugInfo,
				"From: Clan-HQ <{$Conf['AdminEmail']}> \r\n".
				"Content-Type: text/html"
			);
			
			$ReturnInfo .= "<p>We have been alerted of this error, and will attempt to fix it asap.<br>\n";
			$ReturnInfo .= "In the meantime, for further information click <a href='mailto:{$Conf['AdminEmail']}'>here</a> to email us.</p>\n";
			$ReturnInfo .= "<p>We apologise for any inconvenience.</p>\n";
			
		} else {
			$ReturnInfo .= "<p>$DebugInfo</p>\n";
		}
		
		Die("<title>Clan-HQ MySQL Error</title>\n<link href='css/mysql.css' ".
			"rel='stylesheet' type='text/css'>{$ReturnInfo}");
	}
	
	/*========================================================================*/
	// Create an array from a multidimensional array returning formatted
	// strings ready to use in an INSERT query, saves having to manually format
	// the (INSERT INTO table) ('field', 'field', 'field') VALUES ('val', 'val')
	/*========================================================================*/
	
	function compile_db_insert_string($data) {
		
		$field_names  = "";
		$field_values = "";
		
		foreach ($data as $k => $v) {
			$v = preg_replace( "/'/", "\\'", $v );
			//$v = preg_replace( "/#/", "\\#", $v );
			$field_names  .= "$k,";
			$field_values .= "'$v',";
		}
		
		$field_names  = preg_replace( "/,$/" , "" , $field_names  );
		$field_values = preg_replace( "/,$/" , "" , $field_values );
		
		return array(
			'FIELD_NAMES'  => $field_names,
			'FIELD_VALUES' => $field_values,
		);
	}
	
	/*========================================================================*/
	// Create an array from a multidimensional array returning a formatted
	// string ready to use in an UPDATE query, saves having to manually format
	// the FIELD='val', FIELD='val', FIELD='val'
	/*========================================================================*/
	
	function compile_db_update_string($data) {
		
		$return_string = "";
		
		foreach ($data as $k => $v) {
			$v = preg_replace( "/'/", "\\'", $v );
			$return_string .= $k . "='".$v."',";
		}
		
		$return_string = preg_replace( "/,$/" , "" , $return_string );
		
		return $return_string;
	}
	
	function sqllistbox($field, $query, $default = "", $custom = "", $part = "", $charlimit = "", $use_lang = "", $params = "") {
		
		/* -----------------------------------------------
		| Creates a list of items returned from a sql statement
		+ --------------------------------------------- */
		
		if (strlen($custom) == 1) {
			// Presume "None"...
			$custom = "{lang_None}";
		}
		
		global $t, $lang;
		if(!$part) $listcode = "<select name=\"$field\" class=formDropdown %disabled%" . ($params ? " $params" : "") . ">";
		$result = $this->query($query, "sqllistbox");
		if ($custom) { $listcode .= "<option value=''>$custom</option>"; }
		while ($row = mysql_fetch_array($result)) {
			if($charlimit) $row[1] = substr_adv($row[1], $charlimit);
			if($use_lang) $row[1] = $lang[$row[1]];
			if ($row[0] == $default)	// default match found
				$listcode .= "<option value='$row[0]' selected>$row[1]</option>";
			else
				$listcode .= "<option value='$row[0]'>$row[1]</option>";
		}
		
		if(!$part) $listcode .= "</select>";
		
		return ($listcode);
	}
	
	
	function sqllistradio($field, $query, $default, $none) {
		
		/* -----------------------------------------------
		| Creates a list of radio buttons returned from a sql statement
		+ --------------------------------------------- */
		
		global $t, $core_db;
		
		$result = $this->query($query, "sqllistradio");
		
		if ($default) {
			while ($row = $this->fetch_row($result)) {
				if ($row[1] == $default) {
					$listcode .= "<input type=radio name=$field value=$row[1] checked><img alt='$row[2]' src=$imagedir$row[1].gif>&nbsp;&nbsp;&nbsp;";
				} else {
					$listcode .= "<input type=radio name=$field value=$row[1]><img alt='$row[2]' src=$imagedir$row[1].gif>&nbsp;&nbsp;&nbsp;";
				}
			}
			
			if ($none == TRUE) {
				$listcode .= "<input type=radio name=$field value=0> Other&nbsp;&nbsp;&nbsp;";
			}
		}
		return $t->set_var($field,$listcode);
	}
}