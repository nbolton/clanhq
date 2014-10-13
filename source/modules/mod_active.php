<?php
// Sets meta to refresh page, making the refresh interval just below the 'online_since' var
$next_refresh = $cfg['online_since'] - 5;
echo("<meta http-equiv=refresh content=$next_refresh>\n");
// Confirm script has worked
echo("Status updated. Next refresh in $next_refresh seconds.");