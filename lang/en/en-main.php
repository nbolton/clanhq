<?php

// +--------------------------------------------+ 
// | Clan-HQ.com CMS (Clan Management System)		| 
// | Version 3 - Development by r3n							| 
// | lang-en.php // English (British)						| 
// +--------------------------------------------+ 

if (!isset($input['email'])) {
	$input['email'] = "";
}

$lang = array(
	
	'lang-en'	=>	"English",
	'lang-nl'	=>	"Dutch",
	
	/* -------------------------------------------------------------- +
	| LONGER/MISC INFO
	+ -------------------------------------------------------------- */
	'local_server_time'			=>	"The local server-time is <b>{$cfg['server_time']}</b> on <b>{$cfg['server_date']}</b>.",
	'sectionunderconst'			=>	"Section under construction.",
	'badpass'								=>	"Sorry, your Username or Password appears to be invalid, please try again.",
	'emaildosentexist'			=>	"Sorry, the email address ({$input['email']}) does not exist on our user database. ".
															"Please check you have entered it correctly and try again!",
	'passemailsent'					=>	"Congratulations, your password has been successfully <strong>reset</strong> and sent ".
															"to <b>{$input['email']}</b>. ".
															"Please note that we could not send your old password due to security reasons.<p><a href=?mod=security>Back to login!</a>",
	'welcometoadmin'				=>	"Welcome to the <b>Admin Home</b>!<br>This feature will allow you to overview all the deferent modules on your website. ".
															"<br>Click on the name of a module to use it, or click help for help with that module.",
	'nofilemirrors'					=>	"Sorry, there are no mirrors for this file at the moment. Please check back tomorrow.",
	'noupcomingmatches'			=>	"No upcoming matches.",
	'email_pass_info'				=>	"If you have forgotten your password, you can use this feature to email your password back to yourself. ".
															"<b>Use this feature at your own risk!</b>",
	'access_denied_info'		=> 	"Sorry, you do not have permission to use the <b>{denied_action}</b> action in the  <b>{denied_module}</b> module. ".
															"<br>Please contact one of the site administrators for more information.",
	'upload_screenshot'			=>	"Upload Screenshot",
	
	'fix_info_1'						=>	/* No. of slots. */ "slots for this match have been forfilled.",
	'fix_info_reg'					=>	"Register to play!",
	'fix_info_unreg'				=>	"Cancel your slot",
	'fix_slots_regd'				=>	"Players registered: ", /* No. of players. */
	'fix_slots_taken'				=>	"All slots for this match are taken.",
	
	/* Post created for a new site by admin. */
	'admin_default_welcome_title' =>	"Welcome to CMS!",
	'admin_default_welcome_body'	=> "Hello <b>{username}</b>!<p><b>Welcome</b> to your new Clan Management System (CMS)...<p>".
																		"On behalf of the Clan-HQ team, I would like to congratulate you ".
																		"as clan number <b>{num_clan}</b> to your new CMS!<p>".
																		"Before we proceed, you will need to log-in, click ".
																		"<a href=?mod=security&action=login>here</a> to login now!",
	'admin_default_howto_title'		=>	"How to use your CMS!",
	'admin_default_howto_body'		=>	"To begin you should try posting some news; click on <b>Select</b>".
																		" to the left hand side of your screen (below Administration) ".
																		"and scroll down to <b>Post News</b>. Once there, follow the ".
																		"on-screen instructions for further assistance.<p>".
																		"Other items on your site are created in almost the exact same ".
																		"way (over time you will discover a wide range ".
																		"of shortcuts for simple tasks just like posting news). CMS also ".
																		"allows you to edit, delete and view items in an admin layout... ".
																		"Try exploring the Administration menu you just used (top left) ".
																		"for more ways to customise your CMS.<p>".
																		"If any of these instructions seem a bit too complicated or don\'t ".
																		"make sense, feel free to email me (".
																		"<a href=mailto:{support_email}>{support_email}</a>) or /msg me on ".
																		"IRC ({irc_nick} in {irc_chan} on {irc_net}).".
																		"<p>Enjoy!<br>Nick \"r3n\" Bolton<br>Senior Developer<br>".
																		"<a href=http://www.clan-hq.com/ target=_blank>Clan-HQ.com</a>",
	
	'latest_files'								=>	"Latest Files",
	'select_category'							=>	"Please select a category",
	'showing_category'						=>	"Showing category",
	'no_files_in_cat'							=>	"Sorry, there are no files in this category!",
	
	'this_is_a_map_preview'				=>	"<b>This is a screenshot of the map!</b><br>".
																		"For a screenshot of the scores (if it has been uploaded), ".
																		"click <b>screenshot</b> next to the map name!",
	'click_now_to_read_message' 	=>	"Click <b>now</b> to read this messsage!",
	'click_now_to_send_message'		=>	"Click <b>now</b> to send a message!",
	
	'recipients_info'							=>	"To select<br>multiple, hold<br><b>ctrl</b> while<br>clicking.",
	'carbon_copy_info'						=>	"Use if you<br>want to sent<br>a copy (<b>CC</b>) of<br>this message.<br>",
	'must_add_buddys_first'				=>	"It seems you haven't added any buddies, click ".
																		"<a href=javascript:; onClick=\"openBrWindow('?mod=buddy&action=search','', ".
																		"'scrollbars=yes,resizable=yes,width=400,height=400')\">here</a> to add some now! ".
																		"<br><b>Important!</b> After adding a buddy, you must press <b>F5</b> to refresh this window!",
	
	'buddy_members_info'					=>	"Go to your members page and add buddies from there.",
	'buddy_search_link_info'			=>	"Search the CMS directory for memebrs of other clans!",
	
	'account_default_info'				=>	"This module allows you to track how much <b>diskspace</b> you have ".
																		"(used by uploading files) and how much of your allocated <b>bandwidth</b>".
																		" (cpacity of data downloaded from your CMS) you have used up this month.",
	
	'please_wait_loading'					=>	"Please Wait, Loading...",
	'server_timed_out'						=>	"The server has timed out, please try again!",
	'server_down'									=>	"The server has gone down, try again soon.",
	'no_server_selected'					=>	"No server could be loaded, please check the server's properties.",
	
	'create_new_item'							=>	"Create new item!",
	'module_administration'				=>	"Module administration",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | EMPTY TABLE MESSAGES																						|
	// + -------------------------------------------------------------- +
	'back_to_module_home'	=>	"Click <a href=?mod={mod}>here</a> to go back to {mod_name}.",
	
	'no_home'							=>	"There is no content for your home page. Please create some <a href=?mod=news>news</a>.",
	'no_results'					=>	"There are no results in our database.",
	'no_fixtures'					=>	"There are no fixtures in our database.",
	'no_members'					=>	"There are no members in our database.",
	'no_news'							=>	"There is no news in our database.",
	'no_files'						=>	"There are no files in our database.",
	'no_servers'					=>	"There are no servers in our database.",
	'no_polls'						=>	"There are no polls in our database.",
	'no_messages'					=>	"Your Inbox is empty.",
	'no_messages_saved'		=>	"There are no messages in your Saved Items folder.",
	'no_messages_sent'		=>	"There are no messages in your Sent Items folder.",
	'no_messages_recycle'	=>	"There are no messages in your Recycle Bin.",
	'no_trophies'					=>	"There are no trophies in our database.",
	
	'no_results_id'				=>	"There are no results matching the ID you have specified.",
	'no_fixtures_id'			=>	"There are no fixtures matching the ID you have specified.",
	'no_members_id'				=>	"There are no members matching the ID you have specified.",
	'no_news_id'					=>	"There is no news matching the ID you have specified.",
	'no_files_id'					=>	"There are no files matching the ID you have specified.",
	'no_servers_id'				=>	"There are no servers matching the ID you have specified.",
	'no_polls_id'					=>	"There are no polls matching the ID you have specified.",
	'no_comments_id'			=>	"There are no comments matching the ID you have specified.",
	'no_reports_id'				=>	"There are no reports matching the ID you have specified.",
	'no_messages_id'			=>	"There are no messages matching the ID you have specified.",
	'no_trophie_id'				=>	"There are no trophies matching the ID you have specified.",
	
	'no_poll_options'			=>	"There are no options for this poll!",
	'no_poll_sb'					=>	"There are no active polls in our database.",
	'poll_whovoted'				=>	"Who Voted?",
	'poll_nobody_voted'		=>	"Nobody.",
	'poll_details'				=>	"This poll has been running for about {timerunning} and was started on {date}.<br>".
														"So far, there has been {total_votes} (an average of approximately {votes_per_day} per day).",
	'no_poll_details'			=>	"No poll details could be generated.",
	'no_poll_votes'				=>	"Nobody has voted!",
	'polls_pub_enabled'		=>	"Public voting for this poll has been enabled.",
	'polls_pub_disabled'	=>	"Public voting for this poll has been disabled.",
	'no_last_result'			=>	"There are no results in our database.",
	'no_buddy_list'				=>	"To add a buddy, go to the <a href=?mod=members>members</a> page then click <b>A</b> ".
														"next to the member you want to add. Alternatively, you can click <b>Search</b> below.",
	'no_stats_generated'	=>	"No match stats have been generated because we have no results.",
	'nonews'							=>	"No news in database.",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | TIPS																														|
	// + -------------------------------------------------------------- +
	'notloggedin'							=>	"Not Logged In!<br>Click <a href=?mod=security&action=login>here</a> to login now.",
	'loggedinas'							=>	"Logged in as", /* Username */
	'lastlogin'								=>	"Last login", /* Date */
	'otheractiveusers'				=>	"Others online", /* Number */
	'totalactiveusers'				=>	"Total members online", /* Number */
	'membersinclan'						=>	"Members in clan", /* Number */
	'youruserlevelis'					=>	"Your userlevel is", /* Userlevel */
	'print_page'							=>	"Print view",
	'page_proccessed_in'			=>	"Page processed in", /* Number (seconds) */
	'no_buddies_in_server'		=>	"No buddies on this server.",
	'found_in_server'					=>	"Found in server", /* List of names */
	'server_not_found'				=>	"The selected server could not be found!",
	'server_fix_contact'			=>	"Please contact us so we can fix this problem!",
	'server_add_info'					=>	"<br>\nTo select a server, go to your <a href=?mod=settings&action=private>private settings</a>.",
	'buddy_search_info'				=>	"This tool allows you to search through the whole CMS clan directory, ".
																"then add members from other clans to your buddy list.",
	'buddy_list_info'					=>	"Here are the results for your search, click 'A' next to that member to ".
																"add them to your buddy list. After you have close this window, you must ".
																"refresh your buddy list to show the new buddies.",
	
	'you_searched_for' 				=>	"You searched for", /* Search string */
	'no_results_from_search'	=>	"Sorry, no results could be found based on your search query! ".
																"<a href=javascript:history.back()>Try again!</a>",
	'members_in_server'				=>	"Clan members in server", /* Number */
	'mark_as_unread'					=>	"Mark as Unread",
	'mark_as_read'						=>	"Mark as Read",
	'your_message_here'				=>	"Your message here",
	'confirm_send_message'		=>	'Are you sure you would like to send this message?\nClick OK to continue.',
	'save_copy_of_message'		=>	"Save a copy of this message in my Sent Items folder!",
	'origional_message'				=>	"Origional Message",
	'copy_to_saved'						=>	"Copy to saved",
	'move_to_saved'						=>	"Move to saved",
	'add_to_buddy_list'				=>	"Add to buddy list!",
	'no_new_messages'					=>	"No new messages.",
	'leave_empty_for_default'	=>	"Leave this box empty for no change.",
	'sort_by_frags'						=>	"Sort by Frags",
	'sort_by_buddies'					=>	"Sort by Buddies",
	'select_a_flag'						=>	"Select a flag...",
	'no_image_selected'				=>	"No image selected!",
	'passwords_not_match'			=>	"Passwords do not match!",
	
	'diskspace_used'					=>	"Diskspace Used",
	'diskspace_left'					=>	"Diskspace Left",
	'diskspace_limit'					=>	"Diskspace Limit",
	'bandwidth_used'					=>	"Bandwidth Used",
	'bandwidth_left'					=>	"Bandwidth Left",
	'bandwidth_limit'					=>	"Bandwidth Limit",
	
	'admin_settings_info'			=>	"Admin Settings",
	'display_diskspace_in'		=>	"Display Diskspace in..." /* GB, MB or KB */,
	'display_bandwidth_in'		=>	"Display Bandwidth in..." /* GB, MB or KB */,
	'opponent_details'				=>	"Opponent Details",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | MISC WORDS																											|
	// + -------------------------------------------------------------- +
	'name'						=>	'Name',
	'realname'				=>	'Real Name',
	'tag'							=>	'Tag',
	'url'							=>	'URL',
	'rank'						=>	'Rank',
	'age'							=>	'Age',
	'info'						=>	'Info',
	'email'						=>	'Email',
	'msn'							=>	'MSN',
	'aim'							=>	'AIM',
	'yahoo'						=>	'Yahoo',
	'icq'							=>	'ICQ',
	'username'				=>	'Username',
	'password'				=>	'Password',
	'newpassword'			=>	'New Password',
	'forgotpass'			=>	'Forgotten Password!',
	'online'					=>	'Online',
	'offline'					=>	'Offline',
	'goback'					=>	'Go back!',
	'status'					=>	'Status',
	'change'					=>	'Change',
	'joindate'				=>	'Join Date',
	'createdate'			=>	'Create Date',
	'underayear'			=>	'Under a year',
	'year'						=>	'year',
	'years'						=>	'years',
	'over'						=>	'over',
	'clanage'					=>	'Clan Age',
	'warning'					=>	'Warning!',
	'license'					=>	'License',
	'filesize'				=>	'Filesize',
	'filename'				=>	'Filename',
	'date'						=>	'Date',
	'time'						=>	'Time',
	'filerequires'		=>	'Requires',
	'downloads'				=>	'Downloads',
	'dls'							=>	'DLs',
	'lastdl'					=>	'Last Download',
	'uploaded'				=>	'Uploaded',
	'rating'					=>	'Our Rating',
	'outofunit'				=>	'out of',
	'sincetime'				=>	'since',
	'details'					=>	'Details',
	'moretext'				=>	'More',
	'downloadsing'		=>	'download',
	'downloadplur'		=>	'downloads',
	'havingbeensing'	=>	'there has been',
	'havingbeenplur'	=>	'there have been',
	'hits'						=>	'hits',
	'news_edit_by'		=>	'Edited by',
	'news_posted'			=>	'Posted',
	'posted'					=>	'Posted',
	'created_by'			=>	'By',
	'none'						=>	'None',
	'na'							=>	'n/a',
	'born'						=>	'Born',
	'ago'							=>	'ago',
	'osi'							=>	'Online Status Indicator',
	'vsclan'					=>	'Vs. Clan',
	'matchtype'				=>	'Match Type',
	'type'						=>	'Type',
	'maps'						=>	'Maps',
	'timeleft'				=>	'Time Left',
	'days'						=>	'Days',
	'hours'						=>	'Hours',
	'mins'						=>	'Mins',
	'secs'						=>	'Secs',
	'day'							=>	'Day',
	'hour'						=>	'Hour',
	'min'							=>	'Min',
	'sec'							=>	'Sec',
	'scores'					=>	'Scores',
	'outcomes'				=>	'Outcomes',
	'address'					=>	'Address',
	'title'						=>	'Title',
	'creator'					=>	'Creator',
	'userlevel'				=>	'Userlevel',
	'body'						=>	'Body',
	'lastaction'			=>	'Last Action',
	'order'						=>	'Order',
	'password'				=>	'Password',
	'active_logins'		=>	'Active Logins',
	'reset_password'	=>	'Reset Password',
	'match_date'			=>	'Match Date',
	'match_time'			=>	'Match Time',
	'map'							=>	'Map',
	'appearance'			=>	'Appearance',
	'personal'				=>	'Personal',
	'information'			=>	'Information',
	'expired'					=>	'Expired',
	'pending'					=>	'Pending',
	'score'						=>	'Score',
	'ip'							=>	'IP Address',
	'port'						=>	'Port',
	'created'					=>	'Created',
	'edited'					=>	'Edited',
	'edit'						=>	'Edit',
	'uploader'				=>	'Uploader',
	'select_new_file'	=>	'Select New File',
	'category'				=>	'Category',
	'attention'				=>	'Attention',
	'delete'					=>	'Delete',
	'for'							=>	'for',
	'outcome'					=>	'Outcome',
	'us'							=>	'Us',
	'enemy'						=>	'Enemy',
	'clan'						=>	'Clan',
	'draw'						=>	'Draw',
	'lose'						=>	'Lose',
	'win'							=>	'Win',
	'report'					=>	'Report',
	'postcomment'			=>	'Post comment!',
	'next_match'			=>	'Next match',
	'match_info'			=>	"Match Info",
	'played'					=>	"Played",
	'won'							=>	"Won",
	'drew'						=>	"Drew",
	'lost'						=>	"Lost",
	'neither'					=>	"Neither",
	'screenshot'			=>	"Screenshot",
	'timeleft'				=>	"Timeleft",
	'yes'							=>	"Yes",
	'no'							=>	"No",
	'none'						=>	"None",
	'author'					=>	"Author",
	'votes'						=>	"Votes",
	'vote'						=>	"Vote",
	'lc_votes'				=>	"votes",
	'lc_vote'					=>	"vote",
	'view'						=>	"View",
	'email_address'		=>	"Email Address",
	'send_password'		=>	"Send Password",
	'none'						=>	"none",
	'option'					=>	"Option",
	'admin'						=>	"Admin",
	'settings'				=>	"Settings",
	'members'					=>	"Members",
	'fixtures'				=>	"Fixtures",
	'results'					=>	"Results",
	'servers'					=>	"Servers",
	'files'						=>	"Files",
	'polls'						=>	"Polls",
	'help'						=>	"Help",
	'created'					=>	"Created",
	'reports'					=>	"Reports",
	'error'						=>	"Error!",
	'select'					=>	"Select",
	'profile'					=>	"Profile",
	'login'						=>	"Login",
	'logout'					=>	"Logout",
	'ex_member'				=>	"Ex-Member",
	'member'					=>	"Member",
	'ID'							=>	"ID",
	'caccess'					=>	"Custom Access",
	'None'						=>	"None",
	'availability'		=>	"Availability",
	'position'				=>	"Position",
	'duration'				=>	"Duration",
	'server'					=>	"Server",
	'preview'					=>	"Preview",
	'private'					=>	"Private",
	'miscelanious'		=>	"Miscelanious",
	'theme'						=>	"Theme",
	'language'				=>	"Language",
	'show_logo'				=>	"Show Logo",
	'browse_limit'		=>	"Browsers Limit",
	'sidebars'				=>	"Sidebars",
	'admin_menu'			=>	"Admin Menu",
	'latest_news'			=>	"Latest News",
	'site_stats'			=>	"Site Stats",
	'poll'						=>	"Poll",
	'upcoming_match'	=>	"Upcoming Match",
	'latest_result'		=>	"Latest Result",
	'match_stats'			=>	"Match Stats",
	'global'					=>	"Global",
	'select_logo'			=>	"Select Logo",
	'privileges'			=>	"Privileges",
	'update_privs'		=>	"Update Privs.",
	'default'					=>	"Default",
	'insert'					=>	"Insert",
	'update'					=>	"Update",
	'update_multi'		=>	"Update Multi",
	'addmap'					=>	"Add Map",
	'delmap'					=>	"Delete Map",
	'addopt'					=>	"Add Option",
	'delopt'					=>	"Delete Option",
	'register'				=>	"Register",
	'unregister'			=>	"Unregister",
	'slots'						=>	"Slots",
	'getimage'				=>	"Get Image",
	'download'				=>	"Download",
	'auth'						=>	"Authenticate",
	'denied'					=>	"Denied",
	'unknown'					=>	"Unknown",
	'visitor'					=>	"Visitor",
	'account'					=>	"Account",
	'clan_name'				=>	"Clan Name",
	'clan_tag'				=>	"Clan Tag",
	'clan_info'				=>	"Clan Info",
	'activity'				=>	"Activity",
	'active'					=>	"Active",
	'semi_active'			=>	"Semi-Active",
	'inactive'				=>	"Inactive",
	'location'				=>	"Location",
	'class'						=>	"Class",
	'edit_profile'		=>	"Edit Profile",
	'update_profile'	=>	"Update Profile",
	'view_profile'		=>	"View Profile",
	'avatar'					=>	"Avatar",
	'logo'						=>	"Logo",
	'choose'					=>	"Choose",
	'upload'					=>	"Upload",
	'update_private'	=>	"Update Private",
	'update_global'		=>	"Update Global",
	'matches'					=>	"Matches",
	'streaks'					=>	"Streaks",
	'winning'					=>	"Winning",
	'drawing'					=>	"Drawing",
	'losing'					=>	"Losing",
	'high_score'			=>	"High Score",
	'go'							=>	"Go",
	'scoring'					=>	"Scoring",
	'total'						=>	"Total",
	'individual'			=>	"Individual",
	'more'						=>	"More",
	'opponent'				=>	"Opponent",
	'quote'						=>	"Quote",
	'open'						=>	"open",
	'viewing'					=>	"viewing",
	'minimize'				=>	"Minimize",
	'maximize'				=>	"Maximize",
	'close'						=>	"Close",
	'buddy_list'			=>	"Buddy List",
	'nobody'					=>	"Nobody",
	'delete_buddy'		=>	"Delete Buddy",
	'add_buddy'				=>	"Add Buddy",
	'seconds'					=>	"seconds",
	'load'						=>	"Load",
	'very_low'				=>	"very low",
	'low'							=>	"low",
	'medium'					=>	"medium",
	'high'						=>	"high",
	'very_high'				=>	"very high",
	'extreme'					=>	"extreme",
	'players'					=>	"Players",
	'priority'				=>	"Priority",
	'ping'						=>	"Ping",
	'buddies'					=>	"Buddies",
	'platform'				=>	"Platform",
	'player'					=>	"Player",
	'frags'						=>	"Frags",
	'server_watch'		=>	"Server Watch",
	'buddies_on'			=>	"Buddies On",
	'add_more'				=>	"Add More",
	'refresh'					=>	"Refresh",
	'game'						=>	"Game",
	'avatar_defaults'	=>	"Avatar Defaults",
	'search'					=>	"Search",
	'list'						=>	"List",
	'add'							=>	"Add",
	'buddy'						=>	"Buddy",
	'refine_search'		=>	"Refine Search",
	'new_search'			=>	"New Search",
	'nothing'					=>	"Nothing",
	'plain'						=>	"Plain",
	'style'						=>	"Style",
	'wonid'						=>	"WONID",
	'messages'				=>	"Messages",
	'view_all'				=>	"View All",
	'organize'				=>	"Organize",
	'main_menu'				=>	"Main Menu",
	'inbox'						=>	"Inbox",
	'sent_items'			=>	"Sent Items",
	'recycle_bin'			=>	"Recycle Bin",
	'saved_items'			=>	"Saved Items",
	'read'						=> 	"Read",
	'saved'						=>	"Saved",
	'sent'						=>	"Sent",
	'deleted'					=>	"Deleted",
	'send'						=>	"Send",
	'from'						=>	"From",
	'received'				=>	"Received",
	'visit'						=>	"Visit",
	'reply'						=>	"Reply",
	'compose'					=>	"Compose",
	'recipients'			=>	"Recipients",
	'Open'						=>	"Open",
	'save'						=>	"Save",
	'carbon_copy'			=>	"Carbon Copy",
	'forward'					=>	"Forward",
	'last_action'			=>	"Last Action",
	'never'						=>	"Never",
	'wins'						=>	"Wins",
	'draws'						=>	"Draws",
	'losses'					=>	"Losses",
	'nationality'			=>	"Nationality",
	'choose'					=>	"Choose",
	'view_flags'			=>	"View Flags",
	'retry'						=>	"Retry",
	'disabled'				=>	"Disabled",
	'one_message'			=>	"One Message",
	'all_messages'		=>	"All Messages",
	'messages_popup'	=>	"Messages Popup",
	'views'						=>	"Views",
	'left'						=>	"Left",
	'right'						=>	"Right",
	'center'					=>	"Center",
	'trophies'				=>	"Trophies",
	'image'						=>	"Image",
	'website'					=>	"Website",
	'day'							=>	"Day",
	'month'						=>	"Month",
	'year'						=>	"Year",
	'hour'						=>	"Hour",
	'minute'					=>	"Minute",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | GLOBAL ADMIN																										|
	// + -------------------------------------------------------------- +
	'select_to_delete'	=>	"Select the items you want to delete, then click the <b>delete</b> button below!",
	'select_to_update'	=>	"Select or modify the items you wish to update or delete, ".
													"then click the <b>Update Items</b> button below!",
	'check_to_delete'		=>	"Select to <b>delete</b> upon update!",
	'players_needed'		=>	"Players Needed",
	'match_type'				=>	"Match Type",
	'comments'					=>	"Comments",
	'public_voting'			=>	"Public voting?",
	'enabled'						=>	"Enabled?",
	'arrow_delete_tip'	=>	"Select to <b>delete</b> upon update!",
	'click_ok_continue'	=>	"Click OK to continue.",
	'del_items_bttn'		=>	"Delete Items",
	'save_changes_bttn'	=>	"Save Changes",
	'undo_changes_bttn'	=>	"Undo Changes",
	'update_items_bttn'	=>	"Update Items",
	'delete_items_bttn'	=>	"Delete Items",
	'delete_items_q'		=>	"Delete the selected items?",
	'save_changes_q'		=>	'Are you sure you want\nto Save Changes?',
	'undo_changes_q'		=>	'Are you sure you want\nto Undo Changes?',
	'update_items_q'		=>	'Update item order and delete selected items?\nWarning, this action '.
													'cannot be undone!\nClick OK to continue.',
	'confirmLink'				=>	'Are you sure?',
	'click_ok_to_cont'	=>	'\nClick OK to continue.',
	'delete_items_q'		=>	'Are you sure you want to delete the selected items?\nWarning, this '.
													'action cannot be undone!\nClick OK to continue.',
	'delete_item_q'			=>	'Are you sure you want to delete this item?\nWarning, this action cannot be undone!\n'.
													'Click OK to continue.',
	'recycle_items_q'		=>	'Are you sure you want to move the selected items to your Recyle Bin?\nClick OK to continue.',
	'news_comment_info'	=>	"Welcome to the News Commenting utility.<br>To make a comment about this post, ".
													"fill in your comment then click Save Changes.",
	'who_can_view'			=>	"Minimum required userlevel to view your comment.<br><b>recommended: Public.</b>",
	'admin_create_item' =>	"To create an item for this module, click <a href=?mod={mod}&action=create>here</a>.",
	'retype_to_confirm'	=>	"Re-type to confirm",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | NEWS ADMIN																											|
	// + -------------------------------------------------------------- +
	'news_admin_info'			=>	"Welcome to the News Admin!<br>To <b>delete</b> news posts, check the box next to ".
														"that post then click the <b>delete</b> button at the bottom of the page. ".
														"To <b>edit</b> a news post, click edit to the right hand side of that post. ".
														"To <b>create</b> a new post, click <b>Post News</b> below.",
	'news_list_com_info'	=>	"Welcome to the News Comments Admin!<br>To <b>delete</b> comments, check the box ".
														"next to that comment then click the <b>delete</b> button at the bottom of the page. ".
														"To <b>edit</b> a news post, click edit to the right hand side of that comment. ".
														"To <b>create</b> a new comment, click <strong>Create Comment</strong>, or ".
														"<strong>Post Comment</strong> benieth the post (<a href=?mod=news&action=details&id={news_id}>here</a>).",
	'news_create_info'		=>	"Welcome to the Post News utility!<br>Fill in the following fields then click <b>Save Changes</b> to submit the post.",
	'news_edit_info'			=>	"Welcome to the Edit News utility!<br>Modify the following fields then click <b>Save Changes</b> to submit the modifications.",
	'news_list_empty'			=>	"You currently have no news! To post some news click ".
														"<a href=?mod=news&action=create>here</a>, or click <b>Post News</b> on your <b>Admin Menu</b>.",
	'editby_note'					=>	"Show edit note?",
	'create_news_bttn'		=>	"Post News",
	
	'news_i'			=>	"The news post has been successfully created!",
	'news_u'			=>	"The news post has been successfully updated!",
	'news_ds'			=>	"The news post has been successfully deleted!",
	'news_d'			=>	"The news posts have been successfully deleted!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | MEMBERS ADMIN																									|
	// + -------------------------------------------------------------- +
	'members_admin_info'					=>	"Welcome to the Members Admin!<br>To <b>delete</b> members, check the box next to that member then click the <b>delete</b> ".
																		"button at the bottom of the page. To <b>edit</b> a member, click edit to the right hand side of that member. ".
																		"To <b>create</b> a member, click <b>Add Member</b> below.",
	'members_create_info'					=>	"Welcome to the Add Members utility!<br>Fill in the following fields then click <b>Save Changes</b> to add the member.",
	'members_edit_info'						=>	"Welcome to the Edit Members utility!<br>Modify the following fields then click <b>Save Changes</b> to make the modifications.",
	'members_edit_profile_info'		=>	"Welcome to the Edit Profile utility!<br>Modify the following fields then click <b>Save Changes</b> to make the modifications.",
	'members_privileges_info'			=>	"<b>Advanced Security Settings</b><br>".
																		"<b>Warning!</b> Please do not alter these settings unless you are sure of what you are doing, ".
																		"as they could cause problems for the member if incorrectly modified.",
	'members_view_flags_info'			=>	"Please select the flag that you want to appear on your profile.",
	'overwrite_pass'							=>	"Warning: This will overwrite the old password with the one you have entered!",
	'enter_no_pass'								=>	"Entering no password will leave the old one in tact.",
	'create_member_bttn'					=>	"Add Member",
	
	'members_i'			=>	"The member has been successfully created!",
	'members_u'			=>	"The member has been successfully updated!",
	'members_ds'		=>	"The member has been successfully deleted!",
	'members_um'		=>	"The members have been successfully updated!",
	'members_cds'		=>	"You cannot delete your account or the public account!",
	'members_d'			=>	"The members have been successfully deleted!",
	'members_pu'		=>	"Your profile has been successfully updated!",
	'members_mx'		=>	"A member with the same name already exists, click <a href=javascript:history.back()>here</a> and try again with a different name.",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | fixtures ADMIN																									|
	// + -------------------------------------------------------------- +
	'fixtures_admin_info'		=>	"Welcome to the fixtures Admin!<br>To <b>delete</b> fixtures, check the box next to that fixture then click the <b>delete</b> ".
															"button at the bottom of the page. To <b>edit</b> a fixture, click edit to the right hand side of that fixture. ".
															"To <b>create</b> a fixture, click <b>Create fixture</b> below.",
	'fixtures_create_info'	=>	"Welcome to the Add fixtures utility!<br>Fill in the following fields then click <b>Save Changes</b> to add the fixture.",
	'fixtures_edit_info'		=>	"Welcome to the Edit fixtures utility!<br>Modify the following fields then click <b>Save Changes</b> to make the modifications.",
	'click_to_addmap_1'			=>	"You have not added any maps! Click <a href=?mod=fixtures&action=addmap&id=$id>here</a> to add one.",
	'click_to_addmap_2'			=>	"Click <a href=?mod=fixtures&action=addmap&id=$id>here</a> to add another map.",
	'create_fixture_bttn'		=>	"Create Fixture",
	
	'fixtures_i'		=>	"The fixture has been successfully created!",
	'fixtures_u'		=>	"The fixture has been successfully updated!",
	'fixture_ds'		=>	"The fixture has been successfully deleted!",
	'fixtures_d'		=>	"The fixtures have been successfully deleted!",
	'fixtures_r'		=>	"You are now registered to play in this match!",
	'fixtures_ar'		=>	"You are already registered to play in this match!",
	'fixtures_ur'		=>	"You have un-registered your slot for this match!",
	'fixtures_cr'		=>	"All slots taken for this match!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | RESULTS ADMIN																									|
	// + -------------------------------------------------------------- +
	'results_admin_info'		=>	"Welcome to the Results Admin!<br>To <b>delete</b> results, check the box next to that result then click the <b>delete</b> ".
															"button at the bottom of the page. To <b>edit</b> a result, click edit to the right hand side of that result. ".
															"To <b>create</b> a result, click <b>Create Result</b> below.",
	'results_create_info'		=>	"Welcome to the Create Results utility!<br>Fill in the following fields then click <b>Save Changes</b> to add the result.",
	'results_edit_info'			=>	"Welcome to the Edit Results utility!<br>Modify the following fields then click <b>Save Changes</b> to make the modifications.",
	'results_not_complete'	=>	"Results with this icon have not been completed (they are not shown on the public results page).",
	'click_to_addmap_1_r'		=>	"You have not added any maps! Click <a href=?mod=fixtures&action=addmap&id=$id&o=results>here</a> to add one.",
	'click_to_addmap_2_r'		=>	"Click <a href=?mod=fixtures&action=addmap&id=$id&o=results>here</a> to add another map.",
	'create_result_bttn'		=>	"Create Result",
	
	'results_i'			=>	"The result has been successfully created!",
	'results_u'			=>	"The result has been successfully updated!",
	'results_ds'		=>	"The result has been successfully deleted!",
	'results_d'			=>	"The results have been successfully deleted!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | SERVERS ADMIN																									|
	// + -------------------------------------------------------------- +
	'servers_admin_info'		=>	"Welcome to the Server List Admin!<br>To <b>delete</b> a server, check the box next to that server then click the <b>delete</b> ".
															"button at the bottom of the page. To <b>edit</b> a server, click edit to the right hand side of that server. ".
															"To <b>create</b> a new server entry, click <b>Add Server</b> below.",
	'servers_create_info'		=>	"Welcome to the Create Server utility!<br>Fill in the following fields then click <b>Save Changes</b> to submit the server.",
	'servers_edit_info'			=>	"Welcome to the Edit Server utility!<br>Modify the following fields then click <b>Save Changes</b> to submit the modifications.",
	'create_server_bttn'		=>	"Add Server",
	
	'servers_i'			=>	"The server has been successfully created!",
	'servers_u'			=>	"The server has been successfully updated!",
	'servers_ds'		=>	"The server has been successfully deleted!",
	'servers_d'			=>	"The servers have been successfully deleted!",
	'servers_um'		=>	"The servers have been successfully updated!",
	'servers_lr'		=>	"Sorry, you have reached your limit for items in this module!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | FILES ADMIN													 												  |
	// + -------------------------------------------------------------- +
	'files_admin_info'		=>	"Welcome to the Files Admin!<br>To <b>delete</b> a file, check the box next to that file then click the <b>delete</b> ".
														"button at the bottom of the page. To <b>edit</b> a file, click edit to the right hand side of that file. ".
														"To <b>upload</b> a new file, click <b>Upload File</b> below.",
	'files_create_info'		=>	"Welcome to the Upload File utility!<br>Fill in the following fields then click <b>Save Changes</b> to submit the file.",
	'files_edit_info'			=>	"Welcome to the Edit File utility!<br>Modify the following fields then click <b>Save Changes</b> to submit the modifications.",
	'upload_file'					=>	"Upload File",
	'select_file'					=>	"Select File",
	'rename_file'					=>	"Rename File",
	'rename_file_note'		=>	"Use if you wish to <b>change</b> the filename!",
	'no_file_uploaded'		=>	"Warning! No file has been uploaded, please go back and choose a file to upload.",
	'no_file_found'				=>	"The specified file could not be found! Please contact your site administrator.",
	'create_file_bttn'		=>	"Upload File",
	
	'files_i'				=>	"The file has been successfully created!",
	'files_u'				=>	"The file has been successfully updated!",
	'fles_ds'				=>	"The file has been successfully deleted!",
	'files_d'				=>	"The files have been successfully deleted!",
	'files_e'				=>	"An error occured while uploading the file! This could be because the server is too busy or the file is far too large.",
	'files_dslr'		=>	"You have reached your disk space limit, try deleting some of your old files then try again.",
	'files_fslr'		=>	"The filesize of this file excceeds your limit, you should consider upgrading your account.",
	'files_bwlr'		=>	"This file could not be downloaded due to insufficient bandwidth.",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | POLLS ADMIN																				 						|
	// + -------------------------------------------------------------- +
	'polls_admin_info'			=>	"Welcome to the Poll Admin!<br>To <b>delete</b> polls, check the box next to that post then click the <b>delete</b> ".
															"button at the bottom of the page. To <b>edit</b> a poll, click edit to the right hand side of that poll. ".
															"To <b>create</b> a new poll, click <b>Create Poll</b> below.",
	'polls_create_info'			=>	"Welcome to the Create Poll utility!<br>Fill in the following fields then click <b>Save Changes</b> to submit the poll.",
	'polls_edit_info'				=>	"Welcome to the Edit Poll utility!<br>Modify the following fields then click <b>Save Changes</b> to submit the modifications.",
	'polls_addoptlater'			=>	"To add more options, save then open this poll back up with the edit tool.",
	'click_to_addopt_1_r'		=>	"You have not added any options! Click <a href=?mod=polls&action=addopt&id=$id>here</a> to add one.",
	'click_to_addopt_2_r'		=>	"Click <a href=?mod=polls&action=addopt&id=$id>here</a> to add another option.",
	'poll_sidebar_info'			=>	"Options with 0 votes have been hidden.",
	'create_polls_bttn'			=>	"Create Poll",
	
	'polls_i'			=>	"The poll has been successfully created!",
	'polls_u'			=>	"The poll has been successfully updated!",
	'poll_ds'			=>	"The poll has been successfully deleted!",
	'polls_d'			=>	"The polls have been successfully deleted!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | COMMENTS ADMIN																			 						|
	// + -------------------------------------------------------------- +
	'comments_admin_info'			=>	"Welcome to the Comments Admin!<br>To <b>delete</b> comments, check the box next to that post then click the <b>delete</b> ".
																"button at the bottom of the page. To <b>edit</b> a comment, click edit to the right hand side of that comment. ".
																"To <b>create</b> a new comment, click <b>Add Comment</b> below.",
	'comments_create_info'		=>	"Welcome to the Create Comments utility! <br>Fill in the following fields then click <b>Save Changes</b> to add the comment.",
	'comments_edit_info'			=>	"Welcome to the Edit Comments utility!",
	'create_comments_bttn'		=>	"Add Comment",
	
	'comments_i'			=>	"The comment has been successfully created!",
	'comments_u'			=>	"The comment has been successfully updated!",
	'comments_ds'			=>	"The comment has been successfully deleted!",
	'comments_d'			=>	"The comments have been successfully deleted!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | REPORTS ADMIN																			 						|
	// + -------------------------------------------------------------- +
	'reports_admin_info'		=>	"Welcome to the Reports Admin!<br>To <b>delete</b> reports, check the box next to that item then click the <b>delete</b> ".
															"button at the bottom of the page. To <b>edit</b> a report, click edit to the right hand side of that report. ".
															"To <b>create</b> a new report, click <b>Add Report</b> below.",
	'reports_create_info'		=>	"Welcome to the Create Report utility! <br>Fill in the following fields then click <b>Save Changes</b> to add the report.",
	'reports_edit_info'			=>	"Welcome to the Edit Report utility!",
	'create_reports_bttn'		=>	"Add Report",
	
	'reports_i'			=>	"The report has been successfully created!",
	'reports_u'			=>	"The report has been successfully updated!",
	'reports_ds'		=>	"The report has been successfully deleted!",
	'reports_d'			=>	"The reports have been successfully deleted!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | MESSAGES																						 						|
	// + -------------------------------------------------------------- +
	'message_name_prefix_info'	=>	"*This member isn't on your buddys list! Click the <b>A</b> symbol (to the left) to add them now.",
	'messages_s'		=>	"The message has been successfully sent!",
	'messages_d'		=>	"The messages have been moved to your Recycle Bin!",
	'messages_ds'		=>	"The message has been moved to your Recycle Bin!",
	'messages_pd'		=>	"The messages have been <b>permanently</b> deleted!",
	'messages_pds'	=>	"The message has been <b>permanently</b> deleted!",
	'messages_mm'		=>	"The message has been moved to your Saved Items folder!",
	'messages_mc'		=>	"The message has been copied to your Saved Items folder!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | TROPHIES ADMIN																									|
	// + -------------------------------------------------------------- +
	'trophies_admin_info'		=>	"Welcome to the Trophies Admin!<br>To <b>delete</b> a trophie, check the box next to that server then click the <b>delete</b> ".
															"button at the bottom of the page. To <b>edit</b> a trophie, click edit to the right hand side of that trophie. ".
															"To <b>create</b> a new trophie entry, click <b>Add Trophie</b> below.",
	'trophies_create_info'	=>	"Welcome to the Create Trophie utility!<br>Fill in the following fields then click <b>Save Changes</b> to submit the trophie.",
	'trophies_edit_info'		=>	"Welcome to the Edit Trophie utility!<br>Modify the following fields then click <b>Save Changes</b> to submit the modifications.",
	'create_trophie_bttn'		=>	"Add Trophie",
	
	'trophies_i'		=>	"The trophie has been successfully created!",
	'trophies_u'		=>	"The trophie has been successfully updated!",
	'trophies_ds'		=>	"The trophie has been successfully deleted!",
	'trophies_d'		=>	"The trophies have been successfully deleted!",
	'trophies_um'		=>	"The trophies have been successfully updated!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | MODULES & ACTIONS																							|
	// + -------------------------------------------------------------- +
	'home' 					=>	'Home',
	'news' 					=>	'News',
	'members' 			=>	'Members',
	'results' 			=>	'Results',
	'fixtures' 			=>	'Fixtures',
	'servers' 			=>	'Servers',
	'files' 				=>	'Files',
	'polls' 				=>	'Polls',
	'comments' 			=>	'Comments',
	'reports' 			=>	'Reports',
	'settings' 			=>	'Settings',
	'security' 			=>	'Security',
	'about' 				=>	'About',
	'help' 					=>	'Help',
	'admin' 				=>	'Admin',
	'browse'				=>	'Browse',
	'profile'				=>	'Profile',
	'details'				=>	'Details',
	'admin'					=>	'Admin',
	'create'				=>	'Create',
	'edit'					=>	'Edit',
	'login'					=>	'Login',
	'forpass'				=>	'Forgot Password',
	'user_settings'	=>	'User Settings',
	'post_news'			=>	'Post News',
	'add_member'		=>	'Add Member',
	'add_result'		=>	'Add Result',
	'add_fixture'		=>	'Add Fixture',
	'add_server'		=>	'Add Server',
	'upload_file'		=>	'Upload File',
	'create_poll'		=>	'Create Poll',
	'add_trophie'		=>	'Add Trophie',
	'edit_comment'	=>	"Edit Comment",
	'create_report'	=>	"Create Report",
	// + -------------------------------------------------------------- +
	
	/* Added 4.0.5 */
	'unknown_author'			=>	"Unknown Author",
	
	/* Shifted from results & fixtures */
	'clan_select_help'		=>	"Please browse through the list below, if your opponent is not<br>".
														"in this list you can enter the details in manualy!",
	'custom_clan_help'		=>	"Alternatively you can enter custom details in the three boxes given.",
	'match_time_help'			=>	"Note: 24-Hour format.",
	
	/* Added 4.3.8 */
	'Custom'							=>	"Custom",
	'enable_score'				=>	"Enable Score!",
	'add_score_tip'				=>	"Click <a href='javascript:;' onClick=\"generateScoresTable(1)\">here</a> to add another score!",
	
	'delete_score'				=>	"Delete Score!",
	'score_delete_warn'		=>	'This score will be permenantly \ndeleted! Are you sure?',
	'restore_scores_tip'	=>	'You are about to remove this score.\n'.
														'If the score was origionaly part of \n'.
														'this result, you can restore it by \n'.
														'clicking the Undo Changes button at \n'.
														'the bottom of the page BEFORE saving!',
	
	/* Added 4.5.1 */
	'add_map_tip'					=>	"Click <a href='javascript:;' onClick=\"generateRows('tableBase', 'tableRow', 1)\">here</a> to add another map!",
	'delete_map'					=>	"Delete Map!",
	'map_delete_warn'			=>	'This map will be permenantly \ndeleted! Are you sure?',
	'restore_maps_tip'		=>	'You are about to remove this map.\n'.
														'If the map was origionaly part of \n'.
														'this fixture, you can restore it by \n'.
														'clicking the Undo Changes button at \n'.
														'the bottom of the page BEFORE saving!',
	
	/* Added 4.5.3 */
	'add_option_tip'			=>	"Click <a href='javascript:;' onClick=\"generateRows('tableBase', 'tableRow', 1)\">here</a> to add another option!",
	'delete_option'				=>	"Delete Option!",
	'option_delete_warn'	=>	'This option will be permenantly \ndeleted! Are you sure?',
	'restore_options_tip'	=>	'You are about to remove this option.\n'.
														'If the option was origionaly part of \n'.
														'this fixture, you can restore it by \n'.
														'clicking the Undo Changes button at \n'.
														'the bottom of the page BEFORE saving!',
	
	/* Added 4.6.5 */
	'latest_poll'					=>	"Latest Poll",
	'last_result'					=>	"Last Result",
	'next_fixture'				=>	"Next Fixture",
	'panels'							=>	"Panels",
);