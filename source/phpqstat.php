<?php

/********************************************************
 phpQStat 0.01 BETA  2/25/2000
 
 Creator: Chris Weiss
 Description: PHP3 wrapper for QStat (http://www.activesw.com/people/steve/qstat.html)
 
 Prerequisites:  PHP3 and QStat2.3g.
 
 See included test.php3 file for example useage.
 
 Definitions:
 Class phpQStat:
  Parameters:
    $game = Game Type as defined in QStat Documentation (they could change so I won't list them here).
    $host = Hostname OR IP Address of server with optional port if not default port fo game.
    OPTIONAL: Use to speed up queries
    $do_rules = True/False. If omitted will assume True.  
    $do_players = True/False. If omitted will assume True.  
  
  Variables:
    $game_type = Game Type as returned by QStat.
    $server_address = IP Address of server.
    $server_name = Long Name of the server as defined by server admin
    $server_map = Current Map playing.
    $server_num_players = Current number of players in the game.
    $server_num_players_max = Maximum allow player in the game.
    $server_responce_time = Time in milliseconds(ms) that the took to return information.
          This will be time from the Web server, not the person viewing the page.
  
  Objects:
    $rules: Holds all the rules/variables that QStat returned.
    $rules->count = Total numbe of rules/variables returned.
    $rules->name[] = Array from 0 to $rules->count - 1 of rule names.
    $rules->value[] = Array from 0 to $rules->count - 1 of rule values.
    
    $players: Holds all connected players.
    $players->count = Number of players returned.  Should always be equal to $server_num_players.
    $players->fcount = Number of fields returned.  You will have use QStat on you servers to
                know what these fields are because evey game is different.
    $players->field0[] = Array from 0 to $players->count - 1 of the first field returned.
    $players->field1[] = Array from 0 to $players->count - 1 of the second field returned.
        :
        :
    $players->field20[] = Array from 0 to $players->count - 1 of the 21st field returned.
    
    NOTE:  phpQStat currently supports 21 fields even though QStat2.3g "appears" to return a maximum
            of 19 fields.  Refer to test.php3 for an example of how to get the correct fileds. 
  
********************************************************/
 
class phpQStat
{
    var $game_type = "";
    var $server_address;
    var $server_name;
    var $server_map;
    var $server_num_players;
    var $server_num_players_max;
    var $server_responce_time;
    var $rules;
    var $players;
    
    function phpQStat($game, $host, $do_rules = true, $do_players = true)
    {
    
    global $cfg;
    
    //run qstat
    $result = "";
    $param = $cfg['qstat_dir']; // Specified in MySQL_Conf
    if ($do_rules)
      $param .= "-R ";
    if ($do_players)
      $param .= "-P ";
    $param .= "-sort F ";
    $param .= "-".$game." ".$host." -raw ,,,";
    
		exec($param, $result);
    
    //get server info
    $info1 = explode( ",,,", (isset($result[0]) ? $result[0] : ""));
    $this->game_type = (isset($info1[0]) ? $info1[0] : "");
    $this->server_address = (isset($info1[1]) ? $info1[1] : "");
    $this->server_name = (isset($info1[2]) ? $info1[2] : "");
    $this->server_map = (isset($info1[3]) ? $info1[3] : "");
    $this->server_num_players = (isset($info1[5]) ? $info1[5] : "");
    $this->server_num_players_max = (isset($info1[4]) ? $info1[4] : "");
    $this->server_responce_time = (isset($info1[6]) ? $info1[6] : "");
    
    
    //get rules
    $info2 = explode( ",,,", (isset($result[1]) ? $result[1] : ""));
    
    $this->rules = new ruleclass;
    
    $count = 0;
    while ($count < COUNT($info2))
    {
      $temp = explode( "=", $info2[$count]);
      
      $this->rules->add_rule((isset($temp[0]) ? $temp[0] : ""), (isset($temp[1]) ? $temp[1] : ""));
      $count++;
    }

    //get players
    $this->players = new playerclass;
        
        $count = 2;
        while ($count < COUNT($result) - 1)
        {
            $this->players->add_player($result[$count]);
            $count++;
        }
    }
}

class ruleclass
{
  var $name;
  var $value;
  var $count = 0;
  
  function add_rule($rname, $rvalue)
  {
    $this->name[$this->count] = $rname;
    $this->value[$this->count] = $rvalue;
    $this->count++;
  }

}

class playerclass
{
  var $field0;
  var $field1;
  var $field2;
  var $field3;
  var $field4;
  var $field5;
  var $field6;
  var $field7;
  var $field8;
  var $field9;
  var $field10;
  var $field11;
  var $field12;
  var $field13;
  var $field14;
  var $field15;
  var $field16;
  var $field17;
  var $field18;
  var $field19;
  var $field20;
  var $count = 0;
  var $fcount = 0;

  function add_player($player)
  {
    $temp = explode( ",,,", $player);
    if (COUNT($temp) > $this->fcount)
      $this->fcount = COUNT($temp);
    
    switch ($this->fcount)
    {
      case 21:
        $this->field20[$this->count] = $temp[20];
      case 20:
        $this->field19[$this->count] = $temp[19];
      case 19:
        $this->field18[$this->count] = $temp[18];
      case 18:
        $this->field17[$this->count] = $temp[17];
      case 17:
        $this->field16[$this->count] = $temp[16];
      case 16:
        $this->field15[$this->count] = $temp[15];
      case 15:
        $this->field14[$this->count] = $temp[14];
      case 14:
        $this->field13[$this->count] = $temp[13];
      case 13:
        $this->field12[$this->count] = $temp[12];
      case 12:
        $this->field11[$this->count] = $temp[11];
      case 11:
        $this->field10[$this->count] = $temp[10];
      case 10:
        $this->field9[$this->count] = $temp[9];
      case 9:
        $this->field8[$this->count] = $temp[8];
      case 8:
        $this->field7[$this->count] = $temp[7];
      case 7:
        $this->field6[$this->count] = $temp[6];
      case 6:
        $this->field5[$this->count] = $temp[5];
      case 5:
        $this->field4[$this->count] = $temp[4];
      case 4:
        $this->field3[$this->count] = $temp[3];
      case 3:
        $this->field2[$this->count] = $temp[2];
      case 2:
        $this->field1[$this->count] = $temp[1];
      case 1:
        $this->field0[$this->count] = $temp[0];
    }
      $this->count++;
    }
}
?>
