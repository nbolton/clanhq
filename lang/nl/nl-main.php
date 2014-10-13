<?php

// +--------------------------------------------+ 
// | Clan-HQ.com CMS (Clan Management System)	| 
// | Version 3 - Development by r3n		| 
// | lang-nl.php // Dutch (By Tom)	| 
// +--------------------------------------------+ 

$lang = array(
	
	'lang-en'	=>	"Engels",
	'lang-nl'	=>	"Nederlands",
	
	/* -------------------------------------------------------------- +
	| LONGER/MISC INFO
	+ -------------------------------------------------------------- */
	'local_server_time'			=>	"De lokale server-tijd is <b>{$cfg['server_time']}</b> op <b>{$cfg['server_date']}</b>.",
	'sectionunderconst'			=>	"Deze sectie is onder constructie.",
	'badpass'		=>	"Sorry, jouw Gebruikersnaam en Paswoord komen niet overeen of zijn ongeldig, probeer het nog eens.",
	'emaildosentexist'			=>	"Sorry, het E-Mail adres ({$input['email']}) komt niet overeen met de informatie uit onze database. ".
						"Kijk nog eens of u het wel goed heeft ingevuld, en probeer het nog eens.",
	'passemailsent'		=>	"Gefeliciteerd, je paswoord is succesvol verwijdert in onze database en er is een nieuwe opgestuurt".
						"naar <b>{$input['email']}</b>. ".
						"Let op! We kunnen uw wachtwoord niet opsturen vanwege beveiligings omstandigheden.<p><a href=?mod=security>Terug naar de login.</a>",
	'welcometoadmin'	=>	"Welkom in het <b>Admin Gedeelte</b>!<br>Hier kun je alle modules zien. ".
						"<br>Klik op de naam van een Module om het te gebruiken, of klik op help om hulp te krijgen.",
	'nofilemirrors'		=>	"Sorry, er zijn momenteel geen mirrors voor dit bestand. Kijk later nog eens.",
	'noupcomingmatches'			=>	"Geen opkomende Clanwars.",
	'email_pass_info'	=>	"Als je je Wachtwoord bent vergeten, kun je deze manier gebuiken om een mail te krijgen waarin je Paswoord staat. ".
						"<b>Gebruik deze manier op eigen risico!</b>",
	'access_denied_info'		=> 	"Sorry, maar je hebt geen toegang om deze <b>{denied_action}</b> actie te gebruiken in deze  <b>{denied_module}</b> Module. ".
						"<br>U kunt contact opnemene met een van de site Admins voor meer informatie.",
	'upload_screenshot'			=>	"Upload Screenshot",
	
	'fix_info_1'			=>	/* No. of slots. */ "aantal plaatsen zijn van te voren vastgesteld.",
	'fix_info_reg'		=>	"Je moet geregistreerd zijn om te kunnen spelen!",
	'fix_info_unreg'	=>	"Annuleer je plaats",
	'fix_slots_regd'	=>	"Geregistreerde spelers: ", /* No. of players. */
	'fix_slots_taken'	=>	"Alle plaatsen voor deze War zijn al ingenomen.",
	
	/* Post created for a new site by admin. */
	'admin_default_welcome_title' =>	"Welkom bij je CMS!",
	'admin_default_welcome_body'	=> "Hallo <b>{username}</b>!<p><b>Welkom</b> bij je nieuwe Clan Management Systeem(CMS)...<p>".
						"Namens het Clan-HQ team, gefeliciteerd met je nieuwe site ".
						"als clan nummer <b>{num_clan}</b> bij je nieuwe CMS!<p>".
						"Voordat we verder gaan, moet je even inloggen, klik ".
						"<a href=?mod=security&action=login>hier</a> om in te loggen.",
	'admin_default_howto_title'		=>	"Hoe gebruik ik mijn CMS!",
	'admin_default_howto_body'		=>	"Om te beginnen gaan we wat nieuws plaatsen; klik op <b>Select</b>".
						" aan de linkerkant van het scherm (onder Administratie) ".
						"en scroll naar beneden naar <b>Plaats Nieuws</b>. Eenmaal daar, volg de ".
						"on-screen instructies voor meer assistentie.<p>".
						"Andere items op je site zijn gemaakt op precies dezelfde ".
						"manier (na verloop van tijd merk je dat er ".
						"snelkoppelingen zijn om bijv. news te posten, dit zie je naast de modules staan). CMS laat ".
						"je ook editen, deleten en items bekijken in een Admin layout... ".
						"verken maar eens het Administratie menu aan de linkerbovenkant, ".
						"om meer te weten te komen over jouw CMS.<p>".
						"Als deze instructies een beetje te ingewikkeld zijn of als ".
						"ze nergens op slaan, kun je me altijd een E-Mail sturen (".
						"<a href=mailto:{support_email}>{support_email}</a>) of /msg me op ".
						"IRC ({irc_nick} in {irc_chan} op {irc_net}).".
						"<p>Veel plezier!<br>Nick \"r3n\" Bolton<br>Senior Developer<br>".
						"<a href=http://www.clan-hq.com/ target=_blank>Clan-HQ.com</a>",
	
	'latest_files'		=>	"Laatste bestanden",
	'select_category'				=>	"Kies alstublieft een categorie",
	'showing_category'			=>	"Categorie laten zien",
	'no_files_in_cat'				=>	"Sorry, er zijn geen bestanden in deze categorie!",
	
	'this_is_a_map_preview'	=>	"<b>Dit is een screenshot van de map!</b><br>".
						"Voor een screenshot van de scores (als dat is upgeload tenminste), ".
						"klik op <b>screenshot</b> naast de map naam!",
	'click_now_to_read_message' 	=>	"Klik <b>hier</b> om dit bericht te lezen!",
	'click_now_to_send_message'		=>	"Klik <b>hier</b> om een bericht te versturen!",
	
	'recipients_info'				=>	"Om meerdere te <br>selecteren, houd <br>dan<b>ctrl</b> in tijdens<br>het klikken.",
	'carbon_copy_info'			=>	"Als je wilt<br>kun je een<br>kopie (<b>CC</b>) sturen<br>van dit bericht.<br>",
	'must_add_buddys_first'	=>	"Het lijkt erop dat je geen buddies hebt, klik ".
						"<a href=javascript:; onClick=\"openBrWindow('?mod=buddy&action=search','', ".
						"."."."."."."."."."."'scrollbars=yes,resizable=yes,width=400,height=400')\">hier</a> om buddies toe te voegen! ".
						"<br><b>Belangrijk!</b> Nadat je een buddie hebt toegevoegd, moet je op <b>F5</b> drukken om deze pagina te verversen!",
	
	'buddy_members_info'		=>	"Ga naar je members pagina en voeg daar buddies toe.",
	'buddy_search_link_info'			=>	"Zoek door de CMS database voor members van andere clans!",
	
	'account_default_info'	=>	"Deze module laat je zien hoeveel<b>diskspace</b> je nog hebt ".
						"(gebruikt bij het uploaden van bestanden) en hoeveel <b>bandwidth</b>".
						" (capaciteit van date die wordt gedownload van je CMS) je hebt gebruikt deze maand.",
	
	'please_wait_loading'		=>	"Even wachten A.u.b., Laden...",
	'server_timed_out'			=>	"De server is gestopt met laden, probeer het nog eens!",
	'server_down'			=>	"De server is down, probeer het binnenkort nog eens.",
	'no_server_selected'		=>	"Er is geen server geladen, kijk de server eigenschappen eens na.",
	
	'create_new_item'				=>	"Creeer een nieuw item!",
	'module_administration'	=>	"Module administratie",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | EMPTY TABLE MESSAGES							|
	// + -------------------------------------------------------------- +
	'back_to_module_home'	=>	"Klik <a href=?mod={mod}>hier</a> om terug te gaan naar {mod_name}.",
	
	'no_home'				=>	"Er is geen content voor je home page. Maak alsjeblieft <a href=?mod=news>nieuws</a>.",
	'no_results'		=>	"Er zijn geen resultaten in onze database.",
	'no_fixtures'		=>	"Er zijn geen aankomende clanwars in onze database.",
	'no_members'		=>	"Er zijn geen members in onze database.",
	'no_news'				=>	"Er is momenteel geen nieuws in onze database.",
	'no_files'			=>	"Er zijn geen bestanden in onze database.",
	'no_servers'		=>	"Er zijn geen servers in onze database.",
	'no_polls'			=>	"Er zijn geen polls in onze database.",
	'no_messages'		=>	"Je Postvak IN is leeg.",
	'no_messages_saved'		=>	"Er zijn geen berichten in je bewaarde berichten folder.",
	'no_messages_sent'		=>	"Er zijn geen berichten in je gestuurde berichten folder.",
	'no_messages_recycle'	=>	"Er zijn geen berichten in je prullenbak.",
	'no_trophies'		=>	"Er zijn geen prijzen in de database.",
	
	'no_results_id'		=>	"Er zijn geen resultaten die overeenkomen met het nummer wat u heeft opgegeven.",
	'no_fixtures_id'	=>	"Er zijn geen geplande clanwars die overeenkomen met het nummer wat u heeft opgegeven.",
	'no_members_id'		=>	"Er zijn geen members die overeenkomen met het nummer wat u heeft opgegeven.",
	'no_news_id'			=>	"Er zijn geen nieuws items die overeenkomen met het nummer dat u heeft opgegeven.",
	'no_files_id'			=>	"Er zijn geen bestanden die overeenkomen met het nummer dat u heeft opgegeven.",
	'no_servers_id'		=>	"Er zijn geen servers die overeenkomen met het nummer dat u heeft opgegeven.",
	'no_polls_id'			=>	"Er zijn geen polls die overeenkomen met het nummer dat u heeft opgegeven.",
	'no_comments_id'	=>	"Er zijn geen commentaren die overeenkomen met het nummer dat u heeft opgegeven.",
	'no_reports_id'		=>	"Er zijn geen raporten die overeenkomen met het nummer dat u heeft opgegeven.",
	'no_messages_id'	=>	"Er zijn geen berichten die overeenkomen met het nummer dat u heeft opgegeven.",
	'no_trophie_id'		=>	"Er zijn geen prijzen die overeenkomen met het nummer dat u heeft opgegeven.",
	
	'no_poll_options'			=>	"Er zijn geen opties voor deze poll!",
	'no_poll_sb'		=>	"Geen actieve polls in de database.",
	'poll_whovoted'	=>	"Wie heeft er gestemd?",
	'poll_nobody_voted'		=>	"Niemand.",
	'poll_details'	=>	"Deze poll loopt al {timerunning} en is begonnen op {date}.<br>".
					"Tot nu toe is er {total_votes} (een gemiddelde van ongeveer {votes_per_day} per dag).",
	'no_poll_details'			=>	"Poll details konden niet gegereerd worden.",
	'no_poll_votes'	=>	"Niemand heeft nog gevote!",
	'polls_pub_enabled'		=>	"Publiekelijk voten voor deze poll is ingeschakeld.",
	'polls_pub_disabled'	=>	"Publiekelijk voten voor deze poll is uitgeschakeld.",
	'no_last_result'			=>	"Er zijn geen resultaten in onze database.",
	'no_buddy_list'	=>	"Om een vriend toe te voegen kun je naar de <a href=?mod=members>members</a> pagina en klik dan op <b>A</b> ".
					"naast de member die je wilt toevoegen. Je kan ook op <b>Zoeken</b> drukken.",
	'no_stats_generated'	=>	"Er zijn geen wedstrijden statistieken gegenereed omdat we die nog niet hebben gespeeld.",
	'nonews'				=>	"Geen nieuws in de database.",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | TIPS									|
	// + -------------------------------------------------------------- +
	'notloggedin'				=>	"U bent niet ingelogd!<br>Klik <a href=?mod=security&action=login>hier</a> om in te loggen.",
	'loggedinas'				=>	"Ingelogd als", /* Username */
	'lastlogin'		=>	"Laatst ingelogd op", /* Date */
	'otheractiveusers'	=>	"Anderen online", /* Number */
	'totalactiveusers'	=>	"Members online", /* Number */
	'membersinclan'			=>	"Members in de clan", /* Number */
	'youruserlevelis'		=>	"Jouw gebruikersniveau is", /* Userlevel */
	'print_page'				=>	"Print deze pagina",
	'page_proccessed_in'			=>	"Pagina gegenereerd in", /* Number (seconds) */
	'no_buddies_in_server'		=>	"Geen vrienden op deze server.",
	'found_in_server'		=>	"Gevonden op de server", /* List of names */
	'server_not_found'	=>	"De geselecteerde server kan niet worden gevonden!",
	'server_fix_contact'			=>	"Neem contact met ons op om dit probleem op te lossen!",
	'server_add_info'		=>	"<br>\nOm een server te selecteren, ga naar <a href=?mod=settings&action=private>prive instellingen</a>.",
	'buddy_search_info'	=>	"Deze tool maakt het mogelijk om door de hele CMS clan directory te gaan zoeken, ".
				"dan kun je members toevoegen van andere clans in je vriendenlijstje.",
	'buddy_list_info'		=>	"Hier zijn de resultaten van je zoekopdracht, klik op 'A' naast de member ".
				"om hem toe te voegen aan je vriendenlijstje. Als je het venster hebt gesloten, ".
				"moet je de pagina eerst verversen (refreshen).",
	
	'you_searched_for' 	=>	"Je zocht naar", /* Search string */
	'no_results_from_search'	=>	"Sorry, we hebben geen resultaten kunnen vinden op basis van uw zoekactie! ".
				"<a href=javascript:history.back()>Probeer het nog eens!</a>",
	'members_in_server'	=>	"Clan members in de server", /* Number */
	'mark_as_unread'		=>	"Markeer als ongelezen",
	'mark_as_read'			=>	"Markeer als Gelezen",
	'your_message_here'	=>	"Uw bericht hier",
	'confirm_send_message'		=>	'Weet je zeker dat je dit bericht wilt sturen??\nKlik op OK om verder te gaan.',
	'save_copy_of_message'		=>	"Bewaar een kopie van het bericht in je uitgezonden berichten vak!",
	'origional_message'	=>	"Origineel bericht",
	'copy_to_saved'			=>	"Kopieer naar bewaard",
	'move_to_saved'			=>	"Verplaats naar bewaard",
	'add_to_buddy_list'	=>	"Voeg toe aan vriendlijstje!",
	'no_new_messages'		=>	"U heeft geen nieuwe berichten.",
	'leave_empty_for_default'	=>	"Laat dit vak leeg als u niks wilt veranderen.",
	'sort_by_frags'			=>	"Sorteer met Frags",
	'sort_by_buddies'		=>	"Sorteer met vrienden",
	'select_a_flag'			=>	"Selecteer een vlag...",
	'no_image_selected'	=>	"Geen plaatje geselecteerd!",
	'passwords_not_match'			=>	"Paswoorden komen niet overeen!",
	
	'diskspace_used'		=>	"Schijfruimte gebruikt",
	'diskspace_left'		=>	"Schijfruimte over",
	'diskspace_limit'		=>	"Schijfruimte limiet",
	'bandwidth_used'		=>	"Bandbreedte gebruikt",
	'bandwidth_left'		=>	"Bandbreedte over",
	'bandwidth_limit'		=>	"Bandbreedte limiet",
	
	'admin_settings_info'			=>	"Admin instellingen",
	'display_diskspace_in'		=>	"Laat schijfruimte zien in..." /* GB, MB or KB */,
	'display_bandwidth_in'		=>	"Laat Bandbreedte zien in..." /* GB, MB or KB */,
	'opponent_details'	=>	"Details van de tegenstander",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | MISC WORDS									|
	// + -------------------------------------------------------------- +
	'name'			=>	'Nicknaam',
	'realname'	=>	'Echte naam',
	'tag'				=>	'Tag',
	'url'				=>	'URL',
	'rank'			=>	'Taak',
	'age'				=>	'Leeftijd',
	'info'			=>	'Informatie',
	'email'			=>	'E-Mail',
	'msn'				=>	'MSN',
	'aim'				=>	'AIM',
	'yahoo'			=>	'Yahoo',
	'icq'				=>	'ICQ',
	'username'	=>	'Gebruikersnaam',
	'password'	=>	'Paswoord',
	'newpassword'			=>	'Nieuw Paswoord',
	'forgotpass'			=>	'Paswoord vergeten!',
	'online'		=>	'Online',
	'offline'		=>	'Offline',
	'goback'		=>	'Ga terug!',
	'status'		=>	'Status',
	'change'		=>	'Verander',
	'joindate'	=>	'Join Datum',
	'createdate'			=>	'Maak Datum',
	'underayear'			=>	'Onder een jaar',
	'year'			=>	'Jaar',
	'years'			=>	'Jaren',
	'over'			=>	'over',
	'clanage'		=>	'Clan leeftijd',
	'warning'		=>	'Waarschuwing!',
	'license'		=>	'Licentie',
	'filesize'	=>	'Bestandgrootte',
	'filename'	=>	'Bestandnaam',
	'date'			=>	'Datum',
	'time'			=>	'Tijd',
	'filerequires'		=>	'Heeft nodig',
	'downloads'	=>	'Downloads',
	'dls'				=>	'DLs',
	'lastdl'		=>	'Laatste Download',
	'uploaded'	=>	'Uploaded',
	'rating'		=>	'Onze Rating',
	'outofunit'	=>	'op',
	'sincetime'	=>	'sinds',
	'details'		=>	'Details',
	'moretext'	=>	'Meer',
	'downloadsing'		=>	'download',
	'downloadplur'		=>	'downloads',
	'havingbeensing'	=>	'er zijn',
	'havingbeenplur'	=>	'er zijn',
	'hits'			=>	'hits',
	'news_edit_by'		=>	'Aangepast door',
	'news_posted'			=>	'Geplaatst',
	'posted'		=>	'Geplaatst',
	'created_by'			=>	'Door',
	'none'			=>	'Geen',
	'na'				=>	'Niet beschikbaar',
	'born'			=>	'Geboren',
	'ago'				=>	'geleden',
	'osi'				=>	'Online Status Indicator',
	'vsclan'		=>	'Tegen Clan',
	'matchtype'	=>	'Wedstrijd Type',
	'type'			=>	'Type',
	'maps'			=>	'Maps',
	'timeleft'	=>	'Tijd over',
	'days'			=>	'Dagen',
	'hours'			=>	'Uren',
	'mins'			=>	'Minuten',
	'secs'			=>	'Sekonden',
	'day'				=>	'dag',
	'hour'			=>	'uur',
	'min'				=>	'Minuut',
	'sec'				=>	'Sekonde',
	'scores'		=>	'Scores',
	'outcomes'	=>	'Uitkomsten',
	'address'		=>	'Adres',
	'title'			=>	'Titel',
	'creator'		=>	'Maker',
	'userlevel'	=>	'Gebruikersniveau',
	'body'			=>	'Inhoud',
	'lastaction'			=>	'Laatste actie',
	'order'			=>	'Bestel',
	'password'	=>	'Paswoord',
	'active_logins'		=>	'Actieve Logins',
	'reset_password'	=>	'Reset Paswoord',
	'match_date'			=>	'Wedstrijd Datum',
	'match_time'			=>	'Wedstrijd tijd',
	'map'				=>	'Map',
	'appearance'			=>	'Voorkomen',
	'personal'	=>	'Persoonlijk',
	'information'			=>	'Informatie',
	'expired'		=>	'Over datum',
	'pending'		=>	'Afwachtend',
	'score'			=>	'Score',
	'ip'				=>	'IP Adres',
	'port'			=>	'Port',
	'created'		=>	'Gecreeerd',
	'edited'		=>	'Bijgewerkt',
	'edit'			=>	'Bijwerken',
	'uploader'	=>	'Uploader',
	'select_new_file'	=>	'Selecteer nieuw bestand',
	'category'	=>	'Categorie',
	'attention'	=>	'Attentie',
	'delete'		=>	'Verwijderen',
	'for'				=>	'voor',
	'outcome'		=>	'Uitkomst',
	'us'				=>	'Wij',
	'enemy'			=>	'Vijand',
	'clan'			=>	'Clan',
	'draw'			=>	'Gelijkspel',
	'lose'			=>	'Verlies',
	'win'				=>	'Win',
	'report'		=>	'Report',
	'postcomment'			=>	'Plaats commentaar!',
	'next_match'			=>	'Volgende wedstrijd',
	'match_info'			=>	"Wedstrijd informatie",
	'played'		=>	"Gespeeld",
	'won'				=>	"Gewonnen",
	'drew'			=>	"Gelijkspel",
	'lost'			=>	"Verloren",
	'neither'		=>	"Geen van<br>allen",
	'screenshot'			=>	"Screenshot",
	'timeleft'	=>	"Timeleft",
	'yes'				=>	"Ja",
	'no'				=>	"Nee",
	'none'			=>	"Geen",
	'author'		=>	"Schrijver",
	'votes'			=>	"Stemmen",
	'vote'			=>	"Stem",
	'lc_votes'	=>	"Stemmen",
	'lc_vote'		=>	"Stem",
	'view'			=>	"Bekijk",
	'email_address'		=>	"E-Mail Adres",
	'send_password'		=>	"Stuur Paswoord",
	'none'			=>	"geen",
	'option'		=>	"Optie",
	'admin'			=>	"Admin",
	'settings'	=>	"Instellingen",
	'members'		=>	"Members",
	'fixtures'	=>	"Aankomende wedstrijden",
	'results'		=>	"Resultaten",
	'servers'		=>	"Servers",
	'files'			=>	"Bestanden",
	'polls'			=>	"Polls",
	'help'			=>	"Help",
	'created'		=>	"Gemaakt",
	'reports'		=>	"Reportages",
	'error'			=>	"Fout!",
	'select'		=>	"Selecteer",
	'profile'		=>	"Profiel",
	'login'			=>	"Login",
	'logout'		=>	"Logout",
	'ex_member'	=>	"Ex-Member",
	'member'		=>	"Member",
	'ID'				=>	"ID",
	'caccess'		=>	"Gewone toegang",
	'None'			=>	"Geen",
	'availability'		=>	"Beschikbaarheid",
	'position'	=>	"Positie",
	'duration'	=>	"Duur",
	'server'		=>	"Server",
	'preview'		=>	"Voorbeeld",
	'private'		=>	"Prive",
	'miscelanious'		=>	"Overig",
	'theme'			=>	"Thema",
	'language'	=>	"Taal",
	'show_logo'	=>	"Laat Logo zien",
	'browse_limit'		=>	"Browsers Limiet",
	'sidebars'	=>	"Sidebars",
	'admin_menu'			=>	"Admin Menu",
	'latest_news'			=>	"Laatste Nieuws",
	'site_stats'			=>	"Site Statistieken",
	'poll'			=>	"Poll",
	'upcoming_match'	=>	"Aankomende wedstrijd",
	'latest_result'		=>	"Laatste Resultaat",
	'match_stats'			=>	"Wedstrijd Stats",
	'global'		=>	"Globaal",
	'select_logo'			=>	"Selecteer Logo",
	'privileges'			=>	"Toegankelijkheden",
	'update_privs'		=>	"Update toegankelijkheden",
	'default'		=>	"Normaal",
	'insert'		=>	"Invoegen",
	'update'		=>	"Update",
	'update_multi'		=>	"Update Multi",
	'addmap'		=>	"Voeg map toe",
	'delmap'		=>	"Verwijder Map",
	'addopt'		=>	"Nog een optie",
	'delopt'		=>	"Verwijder Optie",
	'register'	=>	"Registreer",
	'unregister'			=>	"Account verwijderen",
	'slots'			=>	"Vrije plaatsen",
	'getimage'	=>	"Pak plaatje",
	'download'	=>	"Download",
	'auth'			=>	"Authenticatie",
	'denied'		=>	"Gewijgerd",
	'unknown'		=>	"Onbekend",
	'visitor'		=>	"Bezoeker",
	'account'		=>	"Account",
	'clan_name'	=>	"Clan Naam",
	'clan_tag'	=>	"Clan Tag",
	'clan_info'	=>	"Clan Informatie",
	'activity'	=>	"Aktiviteit",
	'active'		=>	"Aktief",
	'semi_active'			=>	"Semi-Aktief",
	'inactive'	=>	"Inaktief",
	'location'	=>	"Locatie",
	'class'			=>	"Klasse",
	'edit_profile'		=>	"Bewerk profiel",
	'update_profile'	=>	"Update Profiel",
	'view_profile'		=>	"Bekijk Profiel",
	'avatar'		=>	"Avatar",
	'logo'			=>	"Logo",
	'choose'		=>	"Kies",
	'upload'		=>	"Upload",
	'update_private'	=>	"Update Prive",
	'update_global'		=>	"Update Globaal",
	'matches'		=>	"Wedstrijden",
	'streaks'		=>	"Streaks",
	'winning'		=>	"Winnende",
	'drawing'		=>	"Gelijkspelende",
	'losing'		=>	"Verliezende",
	'high_score'			=>	"Hoogste Score",
	'go'				=>	"Ga",
	'scoring'		=>	"Scoren",
	'total'			=>	"Totaal",
	'individual'			=>	"Individueel",
	'more'			=>	"Meer",
	'opponent'	=>	"Vijand",
	'quote'			=>	"Citeer",
	'open'			=>	"open",
	'viewing'		=>	"Aan het bekijken",
	'minimize'	=>	"Minimaliseren",
	'maximize'	=>	"Maximaliseren",
	'close'			=>	"Sluiten",
	'buddy_list'			=>	"Vriendenlijst",
	'nobody'		=>	"Niemand",
	'delete_buddy'		=>	"Verwijder vriend",
	'add_buddy'	=>	"Voeg vriend toe",
	'seconds'		=>	"sekonden",
	'load'			=>	"Laden",
	'very_low'	=>	"Erg langzaam",
	'low'				=>	"Langzaam",
	'medium'		=>	"Normaal",
	'high'			=>	"Snel",
	'very_high'	=>	"Erg snel",
	'extreme'		=>	"Extreem",
	'players'		=>	"Spelers",
	'priority'	=>	"Voorrang",
	'ping'			=>	"Ping",
	'buddies'		=>	"Vrienden",
	'platform'	=>	"Platform",
	'player'		=>	"Speler",
	'frags'			=>	"Frags",
	'server_watch'		=>	"Server Watch",
	'buddies_on'			=>	"Vrienden Aan",
	'add_more'	=>	"Voeg meer toe",
	'refresh'		=>	"Verversen/Refresh",
	'game'			=>	"Spel",
	'avatar_defaults'	=>	"Avatar Normaal",
	'search'		=>	"Zoeken",
	'list'			=>	"Lijst",
	'add'				=>	"Toevoegen",
	'buddy'			=>	"Vriend",
	'refine_search'		=>	"Verfijn het zoeken",
	'new_search'			=>	"Nieuwe zoekopdracht",
	'nothing'		=>	"Niks",
	'plain'			=>	"Normaal",
	'style'			=>	"Stijl",
	'wonid'			=>	"WONID",
	'messages'	=>	"Berichten",
	'view_all'	=>	"Bekijk alles",
	'organize'	=>	"Organiseer",
	'main_menu'	=>	"Hoofd menu",
	'inbox'			=>	"Inbox",
	'sent_items'			=>	"Verzonden Items",
	'recycle_bin'			=>	"Prullenbak",
	'saved_items'			=>	"Bewaarde Items",
	'read'			=> 	"Gelezen",
	'saved'			=>	"Bewaard",
	'sent'			=>	"Verstuurd",
	'deleted'		=>	"Verwijderd",
	'send'			=>	"Verzonden",
	'from'			=>	"Van",
	'received'	=>	"Ontvangen",
	'visit'			=>	"Bezoek",
	'reply'			=>	"Beantwoorden",
	'compose'		=>	"Opstellen",
	'recipients'			=>	"Ontvangers",
	'Open'			=>	"Open",
	'save'			=>	"Opslaan",
	'carbon_copy'			=>	"Carbon Copy",
	'forward'		=>	"Forward",
	'last_action'			=>	"Laatste actie",
	'never'			=>	"Nooit",
	'wins'			=>	"Gewonnen",
	'draws'			=>	"Gelijkge-<br>speeld",
	'losses'		=>	"Verloren",
	'nationality'			=>	"Nationaliteit",
	'choose'		=>	"Kies",
	'view_flags'			=>	"Bekijk vlaggen",
	'retry'			=>	"Probeer nog eens",
	'disabled'	=>	"Uitgeschakeld",
	'one_message'			=>	"Een bericht",
	'all_messages'		=>	"Alle berichten",
	'messages_popup'	=>	"Berichten pop-up",
	'views'			=>	"Bekeken",
	'left'			=>	"Links",
	'right'			=>	"Rechts",
	'center'		=>	"Midden",
	'trophies'	=>	"Prijzen",
	'image'			=>	"Plaatje",
	'website'		=>	"Website",
	'day'				=>	"Dag",
	'month'			=>	"Maand",
	'year'			=>	"Jaar",
	'hour'			=>	"Uur",
	'minute'		=>	"Minuut",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | GLOBAL ADMIN								|
	// + -------------------------------------------------------------- +
	'select_to_delete'	=>	"Selecteer de items die je wilt verwijderen, klik dan op de <b>Verwijderen</b> knop onderaan!",
	'select_to_update'	=>	"Selecteer of verander items die je wilt updaten of verwijderen, ".
				"klik dan op de <b>Update Items</b> knop onderaan!",
	'check_to_delete'		=>	"Selecteer om te <b>verwijderen</b> wanneer je update!",
	'players_needed'		=>	"Spelers nodig",
	'match_type'	=>	"Wedstrijd type",
	'comments'		=>	"Commentaren",
	'public_voting'			=>	"Publiekelijk stemmen?",
	'enabled'			=>	"Ingeschakeld?",
	'arrow_delete_tip'	=>	"Selecteer om te <b>verwijderen</b> wanneer je update!",
	'click_ok_continue'	=>	"Klik op OK om verder te gaan.",
	'del_items_bttn'		=>	"Verwijder Items",
	'save_changes_bttn'	=>	"Sla veranderingen op",
	'undo_changes_bttn'	=>	"Maak veranderingen ongedaan",
	'update_items_bttn'	=>	"Update Items",
	'delete_items_bttn'	=>	"Verwijder Items",
	'delete_items_q'		=>	"Verwijder de geselecteerde items?",
	'save_changes_q'		=>	"Weet u zeker dat u de veranderingen op wilt slaan?",
	'undo_changes_q'		=>	"Weet u zeker dat u de veranderingen ongedaan wilt maken?",
	'update_items_q'		=>	'Update item sorteer en verwijder geselecteerde items?\nWaarschuwing, deze actie '.
				'kan niet ongedaan worden gemaakt!\nKlik op OK om door te gaan.',
	'confirmLink'	=>	'Weet u het zeker?',
	'click_ok_to_cont'	=>	'\nKlik op OK om door te gaan.',
	'delete_items_q'		=>	'Weet u zeker dat u de geselecteerde items wilt verwijderen?\nWaarschuwing, deze '.
				'actie kan niet ongedaan worden gemaakt!\nKlik op OK om door te gaan.',
	'delete_item_q'			=>	'Weet u zeker dat u deze item wilt verwijderen?\nWaarschuwing, deze actie kan niet ongedaan worden gemaakt!\n'.
				'Klik op OK om verder te gaan.',
	'recycle_items_q'		=>	'Weet u zeker dat u de geselecteerde items wilt verplaatsen naar de prullenbak?\nKlik op OK om verder te gaan.',
	'submit_form_br'		=>	"(Submit this form)",
	'reset_form_br'			=>	"(Reset this form)",
	'news_comment_info'	=>	"Welkom bij het Nieuws commentaar systeem.<br>Om commentaar te geven op de nieuws post, ".
				"moet je het lege vlak invullen en op save changes drukken.",
	'who_can_view'			=>	"Minimaal gebruikersniveau om je commentaar te bekijken.<br><b>aanbevolen: Public.</b>",
	'admin_create_item' =>	"Om een item voor de module te maken kun je <a href=?mod={mod}&action=create>hier</a> klikken.",
	'retype_to_confirm'	=>	"Type nog een keer voor de zekerheid",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | NEWS ADMIN									|
	// + -------------------------------------------------------------- +
	'news_admin_info'			=>	"Welkom bij de nieuws administratie!<br>om nieuws te <b>verwijderen</b> moet je de box aanvinken die ".
					"naast de post staat, en door dan op <b>verwijderen</b> te drukken aan de onderkant van de pagina. ".
					"Om een nieuws bericht te <b>bewerken</b> klik je op bewerken aan de rechterkant van die nieuws post. ".
					"Om nieuws te <b>maken</b> klik je op <b>Plaats nieuws</b> onderaan.",
	'news_list_com_info'	=>	"Welkom bij de nieuws commentaren administratie!<br>Om een commentaar te <b>verwijderen</b> Kun je het boxje aanvinken".
					"die naast het commentaar staat, en druk daarna op de <b>delete</b> knop aan de onderkant van de pagina. ".
					"Om een nieuws post te <b>bewerken</b> , klik aan de rechterkant van die post. ".
					"Om nieuw commentaar aan te <b>maken</b> , klik op <strong>Maak Commentaar</strong>, of ".
					"<strong>Plaats Commentaar</strong> onder de post (<a href=?mod=news&action=details&id={news_id}>hier</a>).",
	'news_create_info'		=>	"Welkom bij de nieuws administratie!<br>Vul de volgende velden in en klik op <b>Bewaar veranderingen</b> om de post te plaatsen.",
	'news_edit_info'			=>	"Welkom bij de nieuws bewerken administratie!<br>Verander de volgende velden en klik dan op <b>Bewaar veranderingen</b> om de wijzigingen op te slaan.",
	'news_list_empty'			=>	"U heeft momenteel geen nieuws! Om nieuws te posten klik ".
					"<a href=?mod=news&action=create>hier</a>, of klik <b>Plaats nieuws</b> in het <b>Administratie Menu</b>.",
	'editby_note'		=>	"Laat zien dat het nieuws is bewerkt?",
	'create_news_bttn'		=>	"Plaats Nieuws",
	
	'news_i'			=>	"De nieuws post is succesvol aangemaakt!",
	'news_u'			=>	"De nieuws post is succesvol geupdate!",
	'news_ds'			=>	"De nieuws post is succesvol verwijderd!",
	'news_d'			=>	"De nieuws posts zijn succesvol verwijderd!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | MEMBERS ADMIN							|
	// + -------------------------------------------------------------- +
	'members_admin_info'		=>	"Welkom bij de members administratie!<br>Om een member te <b>verwijderen</b> , vink dan de checkbox naast de member aan en klik op de<b>verwijderen</b> ".
						"knop aan de onderkant van de pagina. Om een member te <b>bewerken</b> , klik op bewerken aan de rechterkant van de member zijn naam. ".
						"Om een member aan te <b>maken</b> , klik <b>Voeg member toe</b> hieronder.",
	'members_create_info'		=>	"Welkom bij de administratie van members toevoegen!<br>Vul de volgende velden in en klik dan op <b>Bewaar veranderingen</b> om een member toe te voegen.",
	'members_edit_info'			=>	"Welkom bij de adminstratie van members bewerken!<br>Verander de volgende velden en klik dan op <b>Bewaar veranderingen</b> om de veranderingen op te slaan.",
	'members_edit_profile_info'		=>	"Welkom bij de bewerk profiel pagina!<br>Bewerk de volgende velden en klik dan op <b>Bewaar veranderingen</b> om de veranderingen op te slaan.",
	'members_privileges_info'			=>	"Geavanceerde beveiligings instellingen".
																		"Waarschuwing! Verander deze instellingen niet tenzij je zeker weet waar je mee bezig bent, ". 
																		"ze kunnen problemen veroorzaken voor de member als het niet correct is veranderd.",
	'members_view_flags_info'			=>	"Selecteer de vlag die je wilt laten zien op je profiel.",
	'overwrite_pass'				=>	"Waarschuwing: Dit zal het oude paswoord overschrijven met degene die je hebt ingevuld!",
	'enter_no_pass'		=>	"Als je geen paswoord invult zal het paswoord hetzelfde blijven.",
	'create_member_bttn'		=>	"Voeg member toe",
	
	'members_i'			=>	"De geselecteerde member is succesvol gecreeerd!",
	'members_u'			=>	"De geselecteerde member is succesvol geupdate!",
	'members_ds'		=>	"De geselecteerde member is succesvol verwijderd!",
	'members_um'		=>	"De geselecteerde members zijn succesvol geupdate!",
	'members_cds'		=>	"Je kan je account niet verwijderen!",
	'members_d'			=>	"De geselecteerde members zijn succesvol verwijderd!",
	'members_pu'		=>	"Je profiel is succesvol geupdate!",
	'members_mx'		=>	"Een member met dezelfde naam bestaat al, klik <a href=javascript:history.back()>hier</a> en probeer het nog een keer met een andere naam.",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | fixtures ADMIN							|
	// + -------------------------------------------------------------- +
	'fixtures_admin_info'		=>	"Welkom bij de aankomende wars administratie!<br>Om aankomende wars te <b>verwijderen</b> , vink de checkbox naast de aankomende war en druk op de <b>verwijderen</b> ".
						"knop aan de onderkant van de pagina. Om een aankomende war te <b>veranderen</b> , klik dan op bewerken aan de rechterkant van die aankomende war. ".
						"Om een nieuwe aankomende war aan te <b>maken</b> , kun je klikken op <b>Maak aankomende war</b> hieronder.",
	'fixtures_create_info'	=>	"Welkom bij de toevoegings administratie van aankomende wars!<br>Vul de volgende velden in en klik op <b>Bewaar veranderingen</b> om een nieuwe aankomende war toe te voegen.",
	'fixtures_edit_info'		=>	"Welkom bij de aankomende wars bewerken administratie!<br>Verander de volgende velden en klik dan op <b>Bewaar veranderingen</b> om de veranderingen op te slaan.",
	'click_to_addmap_1'			=>	"Je hebt nog geen mappen toegevoegd! Klik <a href=?mod=fixtures&action=addmap&id=$id>hier</a> om een map toe te voegen.",
	'click_to_addmap_2'			=>	"Klik <a href=?mod=fixtures&action=addmap&id=$id>hier</a> om nog een map toe te voegen.",
	'create_fixture_bttn'		=>	"Maak aankomende war aan",
	
	'fixtures_i'		=>	"De aankomende war is succesvol aangemaakt!",
	'fixtures_u'		=>	"De aankomende war is succesvol geupdate!",
	'fixture_ds'		=>	"De aankomende war is succesvol verwijderd!",
	'fixtures_d'		=>	"De aankomende wars zijn succesvol verwijderd!",
	'fixtures_r'		=>	"Je bent nu geregistreerd om in deze war mee te spelen!",
	'fixtures_ar'		=>	"Je bent al geregistreerd om in deze war mee te spelen!",
	'fixtures_ur'		=>	"Je hebt jezelf uitgeschreven voor deze war!",
	'fixtures_cr'		=>	"Je kan je niet meer registreren voor deze war, hij zit al vol!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | RESULTS ADMIN							|
	// + -------------------------------------------------------------- +
	'results_admin_info'		=>	"Welkom bij de resultaten administratie!<br>Om resultaten te <b>verwijderen</b> , vink dan de checkbox naast het resultaat aan, en klik dan op de <b>verwijder</b> ".
						"knop onderaan de pagina. Om een resultaat te <b>bewerken</b> , klik bewerken aan de rechterkant van dat resultaat. ".
						"Om een nieuw resultaat aan te <b>maken</b> , klik <b>Maak nieuw resultaat aan</b> hieronder.",
	'results_create_info'		=>	"Welkom bij de administratie maak resultaten aan!<br>Vul de volgende velden in en klik op <b>Bewaar veranderingen</b> om een resultaat toe te voegen.",
	'results_edit_info'			=>	"Welkom bij de administratie Bewerk resultaten!<br>Bewerk de volgende velden en klik dan op <b>Bewaar veranderingen</b> om de veranderingen op te slaan.",
	'results_not_complete'	=>	"Resultaten met dit icoon zijn niet af (ze zullen niet weergegeven worden).",
	'click_to_addmap_1_r'		=>	"Je hebt nog geen mappen toegevoegd! Klik <a href=?mod=fixtures&action=addmap&id=$id&o=results>hier</a> om er een toe te voegen.",
	'click_to_addmap_2_r'		=>	"Klik <a href=?mod=fixtures&action=addmap&id=$id&o=results>hier</a> om nog een map toe te voegen.",
	'create_result_bttn'		=>	"Maak resultaat aan",
	
	'results_i'			=>	"Het resultaat is succesvol aangemaakt!",
	'results_u'			=>	"Het resultaat is succesvol geupdate!",
	'results_ds'		=>	"Het resultaat is succesvol verwijderd!",
	'results_d'			=>	"De resultaten zijn succesvol verwijderd!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | SERVERS ADMIN							|
	// + -------------------------------------------------------------- +
	'servers_admin_info'		=>	"Welkom bij de server lijst administratie!<br>Om een server te <b>verwijderen</b> , vink dan de checkbox aan naast de server en klik op de <b>verwijderen</b> ".
						"knop aan de onderkant van de pagina. Om een server te <b>bewerken</b> , klik bewerken aan de rechterkant van die server. ".
						"Om een nieuwe server te <b>maken</b> , klik op <b>Voeg Server toe</b> hieronder.",
	'servers_create_info'		=>	"Welkom bij de server aanmaak administratie!<br>Vul de volgende velden en klik dan op <b>Bewaar veranderingen</b> om de server toe te voegen.",
	'servers_edit_info'			=>	"Welkom bij de server bewerkings administratie!<br>Verander de volgende velden en klik dan op <b>Bewaar veranderingen</b> to submit the modifications.",
	'create_server_bttn'		=>	"Voeg een server toe",
	
	'servers_i'			=>	"De server is succesvol aangemaakt!",
	'servers_u'			=>	"De server is succesvol geupdate!",
	'servers_ds'		=>	"De server is succesvol verwijderd!",
	'servers_d'			=>	"De server zijn succesvol verwijderd!",
	'servers_um'		=>	"De servers zijn succesvol geupdate!",
	'servers_lr'		=>	"Sorry, je hebt het limiet van servers bereikt in deze module!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | FILES ADMIN				 			  |
	// + -------------------------------------------------------------- +
	'files_admin_info'		=>	"Welkom bij de bestanden administratie!<br>Om een bestand te <b>verwijderen</b> , vink de checkbox aan naast het bestand en klik op de <b>verwijderen</b> ".
					"knop aan de onderkant van de pagina. Om een bestand te <b>bewerken</b> , klik op bewerken aan de rechterkant van dat bestand. ".
					"Om een nieuw bestand te <b>uploaden</b> , klik op <b>Upload Bestand</b> hieronder.",
	'files_create_info'		=>	"Welkom bij het uploaden van bestanden administratie!<br>Vul de volgende velden in en klik op <b>Bewaar veranderingen</b> om het bestand op te slaan.",
	'files_edit_info'			=>	"Welkom bij het bewerkings module voor bestanden!<br>Verander de volgende velden en klik op <b>Bewaar veranderingen</b> om de veranderingen op te slaan.",
	'upload_file'		=>	"Upload een bestand",
	'select_file'		=>	"Selecteer een bestand",
	'rename_file'		=>	"Hernoem een bestand",
	'rename_file_note'		=>	"Gebruik als je dat wilt <b>verander</b> de bestandsnaam!",
	'no_file_uploaded'		=>	"Waarschuwing! Er is geen bestand geupload, ga alstublieft terug en kies een bestand om te uploaden.",
	'no_file_found'	=>	"Het geselecteerde bestand kan niet gevonden worden! Neem a.u.b contact op met de website administrator.",
	'create_file_bttn'		=>	"Upload een bestand",
	
	'files_i'	=>	"Het bestand is succesvol gecreeerd!",
	'files_u'	=>	"Het bestand is succesvol geupdate!",
	'fles_ds'	=>	"Het bestand is succesvol verwijderd!",
	'files_d'	=>	"De bestanden zijn succesvol verwijderd!",
	'files_e'	=>	"Er is een fout opgetreden tijdens het uploaden van het bestand! Dit kan zijn omdat de server te druk is, of omdat het bestand te groot is.",
	'files_dslr'		=>	"Je hebt het maximum aan bestanden bereikt, probeer wat oude bestanden te verwijderen en probeer het nog eens.",
	'files_fslr'		=>	"Het bestand overschrijdt het limiet, probeer je account te upgraden.",
	'files_bwlr'		=>	"Dit bestand kan niet geupload worden, omdat er te weinig bandbreedte is.",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | POLLS ADMIN					 			|
	// + -------------------------------------------------------------- +
	'polls_admin_info'			=>	"Welkom bij de polls administratie!<br>Om een poll te <b>verwijderen</b> , vink de checkbox aan naast de betreffende post en klik op de <b>verwijderen</b> ".
						"knop onderaan de pagina. Om een poll te <b>bewerken</b> , klik op bewerken aan de rechterkant van die poll. ".
						"Om een nieuwe poll aan te <b>maken</b> , klik op <b>Maak Poll aan</b> hieronder.",
	'polls_create_info'			=>	"Welkom bij de aanmaakmodule van polls!<br>Vul de volgende velden in en klik op <b>Bewaar veranderingen</b> om de poll aan te maken.",
	'polls_edit_info'	=>	"Welkom bij de bewerkingsmodule van de polls!<br>Verander de volgende velden en klik op <b>Bewaar veranderingen</b> om de veranderingen op te slaan.",
	'polls_addoptlater'			=>	"Om meer opties toe te voegen, kun je deze poll opslaan, en daarna naar bewerken gaan.",
	'click_to_addopt_1_r'		=>	"Je hebt geen opties toegevoegd! Klik <a href=?mod=polls&action=addopt&id=$id>hier</a> om er een toe te voegen.",
	'click_to_addopt_2_r'		=>	"Klik <a href=?mod=polls&action=addopt&id=$id>hier</a> om nog een optie toe te voegen.",
	'poll_sidebar_info'			=>	"Opties die nog geen stemmen hebben zijn verstopt.",
	'create_polls_bttn'			=>	"Maak Poll aan",
	
	'polls_i'			=>	"De poll is succesvol aangemaakt!",
	'polls_u'			=>	"De poll is succesvol geupdate!",
	'poll_ds'			=>	"De poll is succesvol verwijderd!",
	'polls_d'			=>	"De polls zijn succesvol verwijderd!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | COMMENTS ADMIN							 			|
	// + -------------------------------------------------------------- +
	'comments_admin_info'			=>	"Welkom bij de commentaren administratie!<br>Om commentaar te <b>verwijderen</b> , vink de checkbox aan naast het commentaar en klik op de <b>verwijderen</b> ".
				"knop onderaan de pagina. Om commentaar te <b>bewerken</b> , klik op bewerken aan de rechterkant van het betreffende commentaar. ".
				"Om een nieuw commentaar aan te <b>maken</b> , klik op <b>Voeg Commentaar toe</b> onderaan.",
	'comments_create_info'		=>	"Welkom bij de aanmaakmodule voor commentaar! <br>Vul de volgende velden in en klik op <b>Bewaar veranderingen</b> om het commentaar toe te voegen.",
	'comments_edit_info'			=>	"Welkom bij de bewerkingsmodule voor commentaar!",
	'create_comments_bttn'		=>	"Voeg commentaar toe",
	
	'comments_i'			=>	"Het commentaar is succesvol aangemaakt!",
	'comments_u'			=>	"Het commentaar is succesvol bewerkt!",
	'comments_ds'			=>	"Het commentaar is succesvol verwijderd!",
	'comments_d'			=>	"De commentaren zijn succesvol verwijderd!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | REPORTS ADMIN							 			|
	// + -------------------------------------------------------------- +
	'reports_admin_info'		=>	"Welkom bij de reportages administratie!<br>Om een reportage te <b>verwijderen</b> , vink de checkbox aan naast de reportage en klik op de <b>verwijderen</b> ".
						"knop onderaan de pagina. Om een reportage te <b>bewerken</b> , klik op bewerken aan de rechterkant van die reportage. ".
						"Om een nieuwe reportage aan te <b>maken</b> , klik op <b>Voeg Reportage</b> onderaan.",
	'reports_create_info'		=>	"Welkom bij de aanmaakmodule voor reportages! <br>Vul de volgende velden in en klik op <b>Bewaar veranderingen</b> om de reportage toe te voegen.",
	'reports_edit_info'			=>	"Welkom bij de bewerkingsmodule voor reportages!",
	'create_reports_bttn'		=>	"Voeg reportage toe",
	
	'reports_i'			=>	"De reportage is succesvol aangemaakt!",
	'reports_u'			=>	"De reportage is succesvol geupdate!",
	'reports_ds'		=>	"De reportage is succesvol verwijderd!",
	'reports_d'			=>	"De reportages zijn succesvol verwijderd!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | MESSAGES							 			|
	// + -------------------------------------------------------------- +
	'message_name_prefix_info'	=>	"*Deze member staat niet op je vriendenlijst! Klik op het <b>A</b> symbool (aan de linkerkant) om ze nu toe te voegen.",
	'messages_s'		=>	"Het bericht is succesvol verstuurd!",
	'messages_d'		=>	"Het bericht is verplaatst naar de prullenbak!",
	'messages_ds'		=>	"Het bericht is verplaatst naar de prullenbak!",
	'messages_pd'		=>	"De berichten zijn <b>permanent</b> verwijderd!",
	'messages_pds'	=>	"Het bericht is <b>permanent</b> verwijderd!",
	'messages_mm'		=>	"Het bericht is verplaatst naar verzonden berichten!",
	'messages_mc'		=>	"Het bericht is gekopieerd naar de verzonden berichten!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | TROPHIES ADMIN							|
	// + -------------------------------------------------------------- +
	'trophies_admin_info'		=>	"Welkom bij de trofeeen administratie!<br>Om een trofee te <b>verwijderen</b> , vink de checkbox naast die trofee aan en klik op de <b>verwijderen</b> ".
						"knop aan de onderkant van de pagina. Om een trofee te <b>bewerken</b> , klik op bewerken aan de rechterkant van de trofee. ".
						"Om een nieuwe trofee aan te <b>maken</b> , klik op <b>Voeg Trofee toe</b> onderaan.",
	'trophies_create_info'	=>	"Welkom bij de aanmaakmodule voor trofees!<br>Vul de volgende velden in en klik op <b>Bewaar veranderingen</b> om de trofee toe te voegen.",
	'trophies_edit_info'		=>	"Welkom bij de bewerkingsmodule voor trofees!<br>Verander de volgende velden en klik dan op <b>Veranderingen opslaan</b> om de veranderingen op te slaan.",
	'create_trophie_bttn'		=>	"Voeg trofee toe",
	
	'trophies_i'		=>	"De trofee is succesvol aangemaakt!",
	'trophies_u'		=>	"De trofee is succesvol geupdate!",
	'trophies_d'		=>	"De trofee is succesvol verwijderd!",
	'trophies_ds'		=>	"De trofee is succesvol verwijderd!",
	'trophies_um'		=>	"De trofees zijn succesvol geupdate!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | SETTINGS ADMIN							 			|
	// + -------------------------------------------------------------- +
	'settings_default_info'		=>	"CMS heeft gezien dat je een <b>Website Administrator</b> bent, de instellingen module heeft twee modes...\n".
				"<ul><li><b>Algemeen</b><br><img width=0 height=5><br>Algemeen (ook bekend als 'publiekelijk') instellingen paneel laat je ".
				"de instellingen veranderen voor de hele website - deze staan bekend als de standaard instellingen. De standaard instellingen zijn ook voor ".
				"alle bezoekers; bijvoorbeeld, als je graag een server publiekelijk wilt laten zien, kun je dat hier doen.".
				"<br><img width=0 height=5><br>".
				"<b>Notitie:</b> Deze instellingen tellen niet voor prive instellingen (zie onderaan).<br><img width=0 height=5><br>".
				"<b>Klik <a href=?mod=settings&action=global>hier</a> om de globale instellingen te bewerken!</b><p>".
				"<li><b>Prive</b><br><img width=0 height=5><br>Het prive instellingen panel is gelijk aan de globale instellingen maar ".
				"het werkt alleen voor jou (als je ingelogged bent), dit betekent dat je andere servers kan zien dan andere gebruikers. ".
				"Dit bepaalt ook of er nieuwe berichten in een pop-up komen of niet!<br><img width=0 height=5><br>".
				"<b>Notitie:</b> Als dit eenmaal veranderd is, zullen de globale instellingen niet meer voor jou gelden.<br><img width=0 height=5><br>".
				"<b>Klik <a href=?mod=settings&action=private>hier</a> om de prive instellingen te veranderen!</b></ul>",
	'settings_private_info'		=>	"Hieronder staan de prive instellingen die gelden voor jou alleen. ".
				"Deze instellingen zullen de globale instellingen overhevelen.",
	'settings_global_info'		=>	"Deze instellingen zijn voor de hele website, maar, ".
				"ze kunnen alleen overheveld worden door prive instellingen. ".
				"Daarom raden we je aan om alleen de globale instellingen te gebruiken; ".
				"gebruik <a href=?mod=settings&action=private>Prive instellingen</a> om jouw instellingen te veranderen.",
	'settings_apply_to_me'		=>	"Bewaar deze instellingen voor mij!",
	// + -------------------------------------------------------------- +
	
	// + -------------------------------------------------------------- +
	// | MODULES & ACTIONS								|
	// + -------------------------------------------------------------- +
	'home' 		=>	'Home',
	'news' 		=>	'Nieuws',
	'members' 			=>	'Members',
	'results' 			=>	'Resultaten',
	'fixtures' 			=>	'Aankomende',
	'servers' 			=>	'Servers',
	'files' 	=>	'Bestanden',
	'polls' 	=>	'Polls',
	'comments' 			=>	'Commentaren',
	'reports' 			=>	'Reportages',
	'settings' 			=>	'Instellingen',
	'security' 			=>	'Beveiliging',
	'about' 	=>	'Over ons',
	'help' 		=>	'Help',
	'admin' 	=>	'Admin',
	'browse'	=>	'Bladeren',
	'profile'	=>	'Profiel',
	'details'	=>	'Details',
	'admin'		=>	'Admin',
	'create'	=>	'Maak',
	'edit'		=>	'Bewerk',
	'login'		=>	'Login',
	'forpass'	=>	'Paswoord vergeten',
	'user_settings'	=>	'Gebruiker instellingen',
	'post_news'			=>	'Plaats nieuws',
	'add_member'		=>	'Voeg Member toe',
	'add_result'		=>	'Voeg Resultaat toe',
	'add_fixture'		=>	'Voeg Aankomende war toe',
	'add_server'		=>	'Voeg Server toe',
	'upload_file'		=>	'Upload Bestand',
	'create_poll'		=>	'Maak Poll aan',
	'add_trophie'		=>	'Maak Trofee aan',
	'edit_comment'	=>	"Bewerk commentaar",
	'create_report'	=>	"Maak reportage aan",
	// + -------------------------------------------------------------- +
);
