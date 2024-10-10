# Apache configuration for secure and performant WordPress sites

Apache configuration for secure and performant WordPress sites. Some of these rules may not work with your WordPress installation, so test settings before deploying.

## Apache and PHP settings

### Hide PHP errors
Hide any errors from showing. Errors can be used by attackers to gain information about our system.
```apache
# Hide any errors from showing
php_flag display_errors Off
```

### Disable directory browsing
Disable directory browsing
```apache
# Disable directory browsing
Options All -Indexes
```

### Disable server signature
Disables the server signature
```apache
# Disables the server signature
ServerSignature Off
```

Set default charset
```apache
# Set default charset
AddDefaultCharset UTF-8
```
## Deny access

### Deny access to important core files
Prevent access to important files in the root folder. Attackers can use the information in these files to gain important information about your WordPress installation and server settings.
```apache
# Prevent access to important files
<FilesMatch "^.*(readme.html|debug.log|error_log|wp-config\.php|php.ini|\.[hH][tT][aApP].*)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

### Deny access to login page
Preventing unknown computers from accessing your login page, can easily prevent brute force attacks on your website.
```apache
# Disable login access from all except your IP
<FilesMatch "wp-login.php">
    Order Deny,Allow
    Deny from all
    Allow from xxx.xxx.xxx.xxx
</FilesMatch>
```

### Force encrypted connection
```apache
# Force encrypted connection
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### Blocks some XSS attacks
```apache
# Blocks some XSS attacks
<IfModule mod_rewrite.c>
    RewriteCond %{QUERY_STRING} (\|%3E) [NC,OR]
    RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
    RewriteRule .* index.php [F,L]
</IfModule>
```

### Hide `wp-includes` folder
`wp-includes` folder contains core files for your WordPress installation. These files are never needed by the users, so they should not have access to them.
```apache
# Blocks all wp-includes folders and files
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^wp-admin/includes/ - [F,L]
    RewriteRule !^wp-includes/ - [S=3]
    RewriteRule ^wp-includes/[^/]+\.php$ - [F,L]
    RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F,L]
    RewriteRule ^wp-includes/theme-compat/ - [F,L]
</IfModule>
```

Restricts access to PHP files from plugin and theme directories
```apache
# Restricts access to PHP files from plugin and theme directories
#RewriteCond %{REQUEST_URI} !^/wp-content/plugins/file-to-exclude\.php
#RewriteCond %{REQUEST_URI} !^/wp-content/plugins/directory-to-exclude/
RewriteRule wp-content/plugins/(.*\.php)$ - [R=404,L]
#RewriteCond %{REQUEST_URI} !^/wp-content/themes/file-to-exclude\.php
#RewriteCond %{REQUEST_URI} !^/wp-content/themes/directory-to-exclude/
RewriteRule wp-content/themes/(.*\.php)$ - [R=404,L]
```

## Security and performance headers

```apache
# Security and performance headers

<IfModule mod_headers.c>
    # X-Frame-Options
	Header set X-Frame-Options "SAMEORIGIN"

    # Prevents browsers from interpreting files as a different MIME type, reducing the risk of security vulnerabilities like MIME-sniffing attacks
    Header set X-Content-Type-Options "nosniff"

    # Strict-Transport-Security
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains"

    # Content-Security-Policy
    Header set Content-Security-Policy "default-src * data:; script-src https: 'unsafe-inline' 'unsafe-eval'; style-src https: 'unsafe-inline'"

    # Hide X-Powered-By header
    Header unset X-Powered-By

    # The 'Referrer Policy' header controls what information is passed on to the next site whenever a link is clicked on your site.
    Header set Referrer-Policy "no-referrer-when-downgrade"

    # Prevents hotlinking of Adobe resources
    Header set X-Permitted-Cross-Domain-Policies "none"

    # Disables the ETag Header
    Header unset ETag

    # Set site features
    Header set Feature-Policy "camera 'none'; fullscreen 'self'; geolocation *; microphone 'none'"

    # Set permision policy header
    Header set Permissions-Policy "geolocation=(*), microphone=(), camera=(), fullscreen=(self)"
    
    # Enables Cross-Site Scripting (XSS) filtering in browsers, providing basic protection against XSS attacks
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

## Block bad bots

Block spambots (Updated 8.8.2024)
```apache
# Block spambots (Updated 8.8.2024)
# https://perishablepress.com/4g-ultimate-user-agent-blacklist/
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{HTTP_USER_AGENT} ^$|\<|\>|\'|\%|\_iRc|\_Works|\@\$x|\<\?|\$x0e|\+select\+|\+union\+|1\,\1\,1\,|2icommerce|3GSE|4all|59\.64\.153\.|88\.0\.106\.|98|85\.17\.|A\_Browser|ABAC|Abont|abot|Accept|Access|Accoo|AceFTP|Acme|ActiveTouristBot|Address|Adopt|adress|adressendeutschland|ADSARobot|agent|ah\-ha|Ahead|AESOP\_com\_SpiderMan|aipbot|Alarm|Albert|Alek|Alexibot|Alligator|AllSubmitter|alma|almaden|ALot|Alpha|aktuelles|Akregat|Amfi|amzn\_assoc|Anal|Anarchie|andit|Anon|AnotherBot|Ansearch|AnswerBus|antivirx|Apexoo|appie|Aqua_Products|Arachmo|archive|arian|ASPSe|ASSORT|aster|Atari|ATHENS|AtHome|Atlocal|Atomic_Email_Hunter|Atomz|Atrop|^attach|attrib|autoemailspider|autohttp|axod|batch|b2w|Back|BackDoorBot|BackStreet|BackWeb|Badass|Baid|Bali|Bandit|Baidu|Barry|BasicHTTP|BatchFTP|bdfetch|beat|Become|Beij|BenchMark|berts|bew|big.brother|Bigfoot|Bilgi|Bison|Bitacle|Biz360|Black|Black.Hole|BlackWidow|bladder.fusion|Blaiz|Blog.Checker|Blogl|BlogPeople|Blogshares.Spiders|Bloodhound|Blow|bmclient|Board|BOI|boitho|Bond|Bookmark.search.tool|boris|Bost|Boston.Project|BotRightHere|Bot.mailto:craftbot@yahoo.com|BotALot|botpaidtoclick|botw|brandwatch|BravoBrian|Brok|Bropwers|Broth|browseabit|BrowseX|Browsezilla|Bruin|bsalsa|Buddy|Build|Built|Bulls|bumblebee|Bunny|Busca|Busi|Buy|bwh3|c\-spider|CafeK|Cafi|camel|Cand|captu|Catch|cd34|Ceg|CFNetwork|cgichk|Cha0s|Chang|chaos|Char|char\(32\,35\)|charlotte|CheeseBot|Chek|CherryPicker|chill|ChinaClaw|CICC|Cisco|Cita|Clam|Claw|Click.Bot|clipping|clshttp|Clush|COAST|ColdFusion|Coll|Comb|commentreader|Compan|contact|Control|contype|Conc|Conv|Copernic|Copi|Copy|Coral|Corn|core-project|cosmos|costa|cr4nk|crank|craft|Crap|Crawler0|Crazy|Cres|cs\-CZ|cuill|Curl|Custo|Cute|CSHttp|Cyber|cyberalert|^DA$|daoBot|DARK|Data|Daten|Daum|dcbot|dcs|Deep|DepS|Detect|Deweb|Diam|Digger|Digimarc|digout4uagent|DIIbot|Dillo|Ding|DISC|discobot|Disp|Ditto|DLC|DnloadMage|DotBot|Doubanbot|Download|Download.Demon|Download.Devil|Download.Wonder|Downloader|drag|DreamPassport|Drec|Drip|dsdl|dsok|DSurf|DTAAgent|DTS|Dual|dumb|DynaWeb|e\-collector|eag|earn|EARTHCOM|EasyDL|ebin|EBM-APPLE|EBrowse|eCatch|echo|ecollector|Edco|edgeio|efp\@gmx\.net|EirGrabber|email|Email.Extractor|EmailCollector|EmailSearch|EmailSiphon|EmailWolf|Emer|empas|Enfi|Enhan|Enterprise\_Search|envolk|erck|EroCr|ESurf|Eval|Evil|Evere|EWH|Exabot|Exact|EXPLOITER|Expre|Extra|ExtractorPro|EyeN|FairAd|Fake|FANG|FAST|fastlwspider|FavOrg|Favorites.Sweeper|Faxo|FDM\_1|FDSE|fetch|FEZhead|Filan|FileHound|find|Firebat|Firefox.2\.0|Firs|Flam|Flash|FlickBot|Flip|fluffy|flunky|focus|Foob|Fooky|Forex|Forum|ForV|Fost|Foto|Foun|Franklin.Locator|freefind|FreshDownload|FrontPage|FSurf|Fuck|Fuer|futile|Fyber|Gais|GalaxyBot|Galbot|Gamespy\_Arcade|GbPl|Gener|geni|Geona|Get|gigabaz|Gira|Ginxbot|gluc|glx.?v|gnome|Go.Zilla|Goldfire|Google.Wireless.Transcoder|Googlebot\-Image|Got\-It|GOFORIT|gonzo|GornKer|GoSearch|^gotit$|gozilla|grab|Grabber|GrabNet|Grub|Grup|Graf|Green.Research|grub|grub\-client|gsa\-cra|GSearch|GT\:\:WWW|GuideBot|guruji|gvfs|Gyps|hack|haha|hailo|Harv|Hatena|Hax|Head|Helm|herit|hgre|hhjhj\@yahoo|Hippo|hloader|HMView|holm|holy|HomePageSearch|HooWWWer|HouxouCrawler|HMSE|HPPrint|htdig|HTTPConnect|httpdown|http.generic|HTTPGet|httplib|HTTPRetriever|HTTrack|human|Huron|hverify|Hybrid|Hyper|ia\_archiver|iaskspi|IBM\_Planetwide|iCCra|ichiro|ID\-Search|IDA|IDBot|IEAuto|IEMPT|iexplore\.exe|iGetter|Ilse|Iltrov|Image|Image.Stripper|Image.Sucker|imagefetch|iimds\_monitor|Incutio|IncyWincy|Indexer|Industry.Program|Indy|InetURL|informant|InfoNav|InfoTekies|Ingelin|Innerpr|Inspect|InstallShield.DigitalWizard|Insuran\.|Intellig|Intelliseek|InterGET|Internet.Ninja|Internet.x|Internet\_Explorer|InternetLinkagent|InternetSeer.com|Intraf|IP2|Ipsel|Iria|IRLbot|Iron33|Irvine|ISC\_Sys|iSilo|ISRCCrawler|ISSpi|IUPUI.Research.Bot|Jady|Jaka|Jam|^Java|java\/|Java\(tm\)|JBH.agent|Jenny|JetB|JetC|jeteye|jiro|JoBo|JOC|jupit|Just|Jyx|Kapere|kash|Kazo|KBee|Kenjin|Kernel|Keywo|KFSW|KKma|Know|kosmix|KRAE|KRetrieve|Krug|ksibot|ksoap|Kum|KWebGet|Lachesis|lanshan|Lapo|larbin|leacher|leech|LeechFTP|LeechGet|leipzig\.de|Lets|Lexi|lftp|Libby|libcrawl|libcurl|libfetch|libghttp|libWeb|libwhisker|libwww|libwww\-FM|libwww\-perl|LightningDownload|likse|Linc|Link|Link.Sleuth|LinkextractorPro|Linkie|LINKS.ARoMATIZED|LinkScan|linktiger|LinkWalker|Lint|List|lmcrawler|LMQ|LNSpiderguy|loader|LocalcomBot|Locu|London|lone|looksmart|loop|Lork|LTH\_|lwp\-request|LWP|lwp-request|lwp-trivial|Mac.Finder|Macintosh\;.I\;.PPC|Mac\_F|magi|Mag\-Net|Magnet|Magp|Mail.Sweeper|main|majest|Mam|Mana|MarcoPolo|mark.blonin|MarkWatch|MaSagool|Mass|Mass.Downloader|Mata|mavi|McBot|Mecha|MCspider|mediapartners|^Memo|MEGAUPLOAD|MetaProducts.Download.Express|Metaspin|Mete|Microsoft.Data.Access|Microsoft.URL|Microsoft\_Internet\_Explorer|MIDo|MIIx|miner|Mira|MIRE|Mirror|Miss|Missauga|Missigua.Locator|Missouri.College.Browse|Mist|Mizz|MJ12|mkdb|mlbot|MLM|MMMoCrawl|MnoG|moge|Moje|Monster|Monza.Browser|Mooz|Moreoverbot|MOT\-MPx220|mothra\/netscan|mouse|MovableType|Mozdex|Mozi\!|^Mozilla$|Mozilla\/1\.22|Mozilla\/22|^Mozilla\/3\.0.\(compatible|Mozilla\/3\.Mozilla\/2\.01|Mozilla\/4\.0\(compatible|Mozilla\/4\.08|Mozilla\/4\.61.\(Macintosh|Mozilla\/5\.0|Mozilla\/7\.0|Mozilla\/8|Mozilla\/9|Mozilla\:|Mozilla\/Firefox|^Mozilla.*Indy|^Mozilla.*NEWT|^Mozilla*MSIECrawler|Mp3Bot|MPF|MRA|MS.FrontPage|MS.?Search|MSFrontPage|MSIE\_6\.0|MSIE6|MSIECrawler|msnbot\-media|msnbot\-Products|MSNPTC|MSProxy|MSRBOT|multithreaddb|musc|MVAC|MWM|My\_age|MyApp|MyDog|MyEng|MyFamilyBot|MyGetRight|MyIE2|mysearch|myurl|NAG|NAMEPROTECT|NASA.Search|nationaldirectory|Naver|Navr|Near|NetAnts|netattache|Netcach|NetCarta|Netcraft|NetCrawl|NetMech|netprospector|NetResearchServer|NetSp|Net.Vampire|netX|NetZ|Neut|newLISP|NewsGatorInbox|NEWT|NEWT.ActiveX|Next|^NG|NICE|nikto|Nimb|Ninja|Ninte|NIPGCrawler|Noga|nogo|Noko|Nomad|Norb|noxtrumbot|NPbot|NuSe|Nutch|Nutex|NWSp|Obje|Ocel|Octo|ODI3|oegp|Offline|Offline.Explorer|Offline.Navigator|OK.Mozilla|omg|Omni|Onfo|onyx|OpaL|OpenBot|Openf|OpenTextSiteCrawler|OpenU|Orac|OrangeBot|Orbit|Oreg|osis|Outf|Owl|P3P|PackRat|PageGrabber|PagmIEDownload|pansci|Papa|Pars|Patw|pavu|Pb2Pb|pcBrow|PEAR|PEER|PECL|pepe|Perl|PerMan|PersonaPilot|Persuader|petit|PHP|PHP.vers|PHPot|Phras|PicaLo|Piff|Pige|pigs|^Ping|Pingd|PingALink|Pipe|Plag|Plant|playstarmusic|Pluck|Pockey|POE\-Com|Poirot|Pomp|Port.Huron|Post|powerset|Preload|press|Privoxy|Probe|Program.Shareware|Progressive.Download|ProPowerBot|prospector|Provider.Protocol.Discover|ProWebWalker|Prowl|Proxy|Prozilla|psbot|PSurf|psycheclone|^puf$|Pulse|Pump|PushSite|PussyCat|PuxaRapido|PycURL|Pyth|PyQ|QuepasaCreep|Query|Quest|QRVA|Qweer|radian|Radiation|Rambler|RAMP|RealDownload|Reap|Recorder|RedCarpet|RedKernel|ReGet|relevantnoise|replacer|Repo|requ|Rese|Retrieve|Rip|Rix|RMA|Roboz|Rogue|Rover|RPT\-HTTP|Rsync|RTG30|.ru\)|ruby|Rufus|Salt|Sample|SAPO|Sauger|savvy|SBIder|SBP|SCAgent|scan|SCEJ\_|Sched|Schizo|Schlong|Schmo|Scout|Scooter|Scorp|ScoutOut|SCrawl|screen|script|SearchExpress|searchhippo|Searchme|searchpreview|searchterms|Second.Street.Research|Security.Kol|Seekbot|Seeker|Sega|Sensis|Sept|Serious|Sezn|Shai|Share|Sharp|Shaz|shell|shelo|Sherl|Shim|Shiretoko|ShopWiki|SickleBot|Simple|Siph|sitecheck|SiteCrawler|SiteSnagger|Site.Sniper|SiteSucker|sitevigil|SiteX|Sleip|Slide|Slurpy.Verifier|Sly|Smag|SmartDownload|Smurf|sna\-|snag|Snake|Snapbot|Snip|Snoop|So\-net|SocSci|sogou|Sohu|solr|sootle|Soso|SpaceBison|Spad|Span|spanner|Speed|Spegla|Sphere|Sphider|spider|SpiderBot|SpiderEngine|SpiderView|Spin|sproose|Spurl|Spyder|Squi|SQ.Webscanner|sqwid|Sqworm|SSM\_Ag|Stack|Stamina|stamp|Stanford|Statbot|State|Steel|Strateg|Stress|Strip|studybot|Style|subot|Suck|Sume|sun4m|Sunrise|SuperBot|SuperBro|Supervi|Surf4Me|SuperHTTP|Surfbot|SurfWalker|Susi|suza|suzu|Sweep|sygol|syncrisis|Systems|Szukacz|Tagger|Tagyu|tAke|Talkro|TALWinHttpClient|tamu|Tandem|Tarantula|tarspider|tBot|TCF|Tcs\/1|TeamSoft|Tecomi|Teleport|Telesoft|Templeton|Tencent|Terrawiz|Test|TexNut|trivial|Turnitin|The.Intraformant|TheNomad|Thomas|TightTwatBot|Timely|Titan|TMCrawler|TMhtload|toCrawl|Todobr|Tongco|topic|Torrent|Track|translate|Traveler|TREEVIEW|True|Tunnel|turing|Turnitin|TutorGig|TV33\_Mercator|Twat|Tweak|Twice|Twisted.PageGetter|Tygo|ubee|UCmore|UdmSearch|UIowaCrawler|Ultraseek|UMBC|unf|UniversalFeedParser|unknown|UPG1|UtilMind|URLBase|URL.Control|URL\_Spider\_Pro|urldispatcher|URLGetFile|urllib|URLSpiderPro|URLy|User\-Agent|UserAgent|USyd|Vacuum|vagabo|Valet|Valid|Vamp|vayala|VB\_|VCI|VERI\~LI|verif|versus|via|Viewer|virtual|visibilitygap|Visual|vobsub|Void|VoilaBot|voyager|vspider|VSyn|w\:PACBHO60|w0000t|W3C|w3m|w3search|walhello|Walker|Wand|WAOL|WAPT|Watch|Wavefire|wbdbot|Weather|web.by.mail|Web.Data.Extractor|Web.Downloader|Web.Ima|Web.Mole|Web.Sucker|Web2Mal|Web2WAP|WebaltBot|WebAuto|WebBandit|Webbot|WebCapture|WebCat|webcraft\@bea|Webclip|webcollage|WebCollector|WebCopier|WebCopy|WebCor|webcrawl|WebDat|WebDav|webdevil|webdownloader|Webdup|WebEMail|WebEMailExtrac|WebEnhancer|WebFetch|WebGo|WebHook|Webinator|WebInd|webitpr|WebFilter|WebFountain|WebLea|Webmaster|WebmasterWorldForumBot|WebMin|WebMirror|webmole|webpic|WebPin|WebPix|WebReaper|WebRipper|WebRobot|WebSauger|WebSite|Website.eXtractor|Website.Quester|WebSnake|webspider|Webster|WebStripper|websucker|WebTre|WebVac|webwalk|WebWasher|WebWeasel|WebWhacker|WebZIP|Wells|WEP\_S|WEP.Search.00|WeRelateBot|wget|Whack|Whacker|whiz|WhosTalking|Widow|Win67|window.location|Windows.95\;|Windows.95\)|Windows.98\;|Windows.98\)|Winodws|Wildsoft.Surfer|WinHT|winhttp|WinHttpRequest|WinHTTrack|Winnie.Poh|wire|WISEbot|wisenutbot|wish|Wizz|WordP|Works|world|WUMPUS|Wweb|WWWC|WWWOFFLE|WWW\-Collector|WWW.Mechanize|www.ranks.nl|wwwster|^x$|X12R1|x\-Tractor|Xaldon|Xenu|XGET|xirq|Y\!OASIS|Y\!Tunnel|yacy|YaDirectBot|Yahoo\-MMAudVid|YahooSeeker|YahooYSMcm|Yamm|Yand|yang|Yeti|Yoono|yori|Yotta|YTunnel|Zade|zagre|ZBot|Zeal|ZeBot|zerx|Zeus|ZIPCode|Zixy|zmao|Zyborg [NC]
RewriteRule ^(.*)$ - [F,L]
</IfModule>
```

## Compression and cache

### File compression
```apache
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
  AddOutputFilterByType DEFLATE application/x-font
  AddOutputFilterByType DEFLATE application/x-font-opentype
  AddOutputFilterByType DEFLATE application/x-font-otf
  AddOutputFilterByType DEFLATE application/x-font-truetype
  AddOutputFilterByType DEFLATE application/x-font-ttf
  AddOutputFilterByType DEFLATE application/x-javascript
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE font/opentype
  AddOutputFilterByType DEFLATE font/otf
  AddOutputFilterByType DEFLATE font/ttf
  AddOutputFilterByType DEFLATE image/svg+xml
  AddOutputFilterByType DEFLATE image/x-icon
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/javascript
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/xml
</IfModule>
```

### Browser cache

```apache
# Enable browser caching
<IfModule mod_expires.c>
  ExpiresActive On

 # Images
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType image/x-icon "access plus 1 year"

  # Video
  ExpiresByType video/webm "access plus 1 year"
  ExpiresByType video/mp4 "access plus 1 year"
  ExpiresByType video/mpeg "access plus 1 year"

  # Fonts
  ExpiresByType font/ttf "access plus 1 year"
  ExpiresByType font/otf "access plus 1 year"
  ExpiresByType font/woff "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
  ExpiresByType application/font-woff "access plus 1 year"

  # CSS, JavaScript
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType text/javascript "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"

  # Others
  ExpiresByType application/pdf "access plus 1 month"
  ExpiresByType image/vnd.microsoft.icon "access plus 1 year"
</IfModule>
```

## Optional settings
Add these options to your `.htaccess` only if the functionality they provide is not required by your WordPress site.

### Disable author pages
Disabling author pages prevents bots and attackers from gaining registered user information (like usernames), which can be used in attacks.
```apache
# Disable author pages
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_URI}  ^/$
    RewriteCond %{QUERY_STRING} ^/?author=([0-9]*) [NC]
    RewriteRule ^(.*)$ http://%{HTTP_HOST}/? [L,R=301,NC]
</IfModule>
```

### Block xmlrpc.php requests
Block WordPress xmlrpc.php requests
```apache
# Block WordPress xmlrpc.php requests
<Files "xmlrpc.php">
    Order Deny,Allow
    Deny from all
</Files>
```

### Prevent image hot-linking
```apache
# Prevent image hotlinking
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?your-domain-here.com [NC]
    RewriteRule \.(jpg|jpeg|png|gif|webp)$ – [NC,F,L]
</IfModule>
```

### Prevent resources hot-linking
```apache
# Prevent resources hotlinking
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?your-domain-here.com [NC]
    RewriteRule \.(js|css)$ – [NC,F,L]
</IfModule>
```
