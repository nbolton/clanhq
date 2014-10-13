<?

include("admin.php");

echo("<b>Clan-HQ CMS Admin Tools</b><p>\n");

echo("&gt; <a href=create.php?action=clan>Create Clan</a><br>\n");
echo("&gt; <a href=security.php?action=find_user>Reset Password</a><br>\n");
//echo("&gt; <a href=stats.php?action=logs>Browse Logs</a><br>\n");
echo("&gt; <a href=stats.php?action=find_user>Find users (stats)</a><br>\n");
echo("&gt; <a href=message.php?action=create>Global Message</a><br>\n");
echo("&gt; <a href=search.php?action=map>Search for map</a><br>\n");
echo "<br>";
echo("&gt; <a href=security.php?action=update_privs>Update Privileges</a><br>\n");
echo("&gt; <a href=security.php?action=reset_privs>Reset Privileges</a><br>\n");
echo("<p>\n");

echo("Logged in as: <b>$cfg[username]</b><br>\n");
echo("Connected to DB: <b>$cfg[dbname]</b><br>\n");

$avg_load = `cat /proc/loadavg`;
echo("Load averages: $avg_load<br>\n");

echo("Load status in the last 5 mins: ");
$avg_load = explode(" ", $avg_load);
if ($avg_load[1] < 0.2)			echo "<b>Very Low</b>";
elseif ($avg_load[1] < 0.4)	echo "<b>Low</b>";
elseif ($avg_load[1] < 0.6)	echo "<b>Medium</b>";
elseif ($avg_load[1] < 0.8)	echo "<b>High</b>";
elseif ($avg_load[1] < 1)		echo "<b>Very High!</b>";
elseif ($avg_load[1] >= 1)	echo "<b>Extreme!</b>";
echo("<br>\n");