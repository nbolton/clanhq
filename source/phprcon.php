<?php 
/********************************************************************
* Half-Life Query                                                   *
* http://hlquery.sourceforge.net/                                   *
* Released under the GNU GENERAL PUBLIC LICENSE                     *
* Copyright 2002 Steve Hutson                                       *
* Steve@material-things.com                                         *
********************************************************************/

// rcon.php - Send rcons to the server.

function SendRCON($ServerIP, $ServerPort, $ServerCommand, $Password)
{
  // Open a connection to the server 
  $HLServer = fsockopen("udp://".$ServerIP, $ServerPort, $errno, $errstr); 

  // Create the variable of the command to send to the server. 
  $ChallengeCommand = "\377\377\377\377challenge rcon\0"; 

  //Send the request to the server. 
  fputs($HLServer, $ChallengeCommand); 
  
  // Remove the junk headers returned
  $keyunk = fread($HLServer,19); 

  // Create a variable with the challenge number returned.
  $do = true;
  while ($do == true) 
  { 
    $str = fread($HLServer,1); 
    if ($str == "\n") 
    { 
      $do = false; 
    } 
    else 
    { 
      $ChallengeNumber .= $str; 
    } 
  } 
  
  // Create the variable of the requested command.
  $RCONCommand = "\377\377\377\377rcon $ChallengeNumber $Password $ServerCommand\0";

  //Send the requested command to the server.
  fputs($HLServer, $RCONCommand); 

  // Remove the junk headers.
  $keyunk =  fread($HLServer,6); 

  // Get the number of unread bytes to return all info.
  $status = socket_get_status($HLServer);

  // Read the rest of the unread bytes into a variable
  $ServerReply = fread($HLServer,$status["unread_bytes"]);

  // Replace line breaks with HTML breaks
  $ServerReply = str_replace("\n", "<br>", $ServerReply);

  //Close the connection 
  fclose($HLServer); 

// Return the data from the function.
return $ServerReply;
}
?>