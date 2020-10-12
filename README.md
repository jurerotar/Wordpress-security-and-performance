# Wordpress enhanced security configuration

Htaccess and wp-config.php rules for Wordpress site security and performance

## htaccess

Hide any errors from showing. Errors can be used by attackers to gain information about our system.
`php_flag display_errors Off`

Disable directory browsing
`Options All -Indexes`

Disables the server signature
`ServerSignature Off`

Set default charset
`AddDefaultCharset UTF-8`

Prevent access to important files
```
<FilesMatch "^.*(readme.html|debug.log|error_log|wp-config\.php|php.ini|\.[hH][tT][aApP].*)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

Disable login access from all except your IP
```
<FilesMatch "wp-login.php">
    Order Deny,Allow
    Deny from all
    Allow from xxx.xxx.xxx.xxx
</FilesMatch>
```

Prevent page resources hotlinking
```
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?your-site-here.com [NC]
    RewriteRule \.(js|css|)$ – [NC,F,L]
</IfModule>
```

Force encrypted connection
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```




Blocks some XSS attacks
```
<IfModule mod_rewrite.c>
    RewriteCond %{QUERY_STRING} (\|%3E) [NC,OR]
    RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
    RewriteRule .* index.php [F,L]
</IfModule>
```

Blocks all wp-includes folders and files
```
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
```
#RewriteCond %{REQUEST_URI} !^/wp-content/plugins/file-to-exclude\.php
#RewriteCond %{REQUEST_URI} !^/wp-content/plugins/directory-to-exclude/
RewriteRule wp-content/plugins/(.*\.php)$ - [R=404,L]
#RewriteCond %{REQUEST_URI} !^/wp-content/themes/file-to-exclude\.php
#RewriteCond %{REQUEST_URI} !^/wp-content/themes/directory-to-exclude/
RewriteRule wp-content/themes/(.*\.php)$ - [R=404,L]
```

Security and performance headers
```
<IfModule mod_headers.c>
    X-Frame-Options
	Header set X-Frame-Options "SAMEORIGIN"

    X-XSS-Protection
    Header set X-XSS-Protection "1; mode=block"

    X-Content-Type-Options
	Header set X-Content-Type-Options "nosniff"

    Strict-Transport-Security
	Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains"

    Content-Security-Policy
	Header set Content-Security-Policy "default-src https:; font-src https: data:; img-src https: data:; script-src https:; style-src https:;"

    Hide X-Powered-By header
    Header unset X-Powered-By

    The 'Referrer Policy' header controls what information is passed on to the next site whenever a link is clicked on your site.
    Header set Referrer-Policy "no-referrer-when-downgrade"

    Prevents hotlinking of Adobe resources
    Header set X-Permitted-Cross-Domain-Policies "none"

    Disables the ETag Header
    Header unset ETag

    Set site features
    Header set Feature-Policy "camera 'none'; fullscreen 'self'; geolocation *; microphone 'none'"

    The Expect CT header policy instructs web browsers to either report or enforce Certificate Transparency requirements. This can stop miss-issued SSL certificates and can be set to either report mode or enforce mode.
    Header set Expect-CT: "enforce, max-age=31536000"
</IfModule>
```

Block spambots (Updated 12.10.2020)
```
RewriteCond %{HTTP:User-Agent} (?:Alexibot|Art-Online|asterias|BackDoorbot|Black.Hole|
BlackWidow|BlowFish|botALot|BuiltbotTough|Bullseye|BunnySlippers|Cegbfeieh|Cheesebot|
CherryPicker|ChinaClaw|CopyRightCheck|cosmos|Crescent|Custo|DISCo|DittoSpyder|DownloadsDemon|
eCatch|EirGrabber|EmailCollector|EmailSiphon|EmailWolf|EroCrawler|ExpresssWebPictures|ExtractorPro|
EyeNetIE|FlashGet|Foobot|FrontPage|GetRight|GetWeb!|Go-Ahead-Got-It|Go!Zilla|GrabNet|Grafula|
Harvest|hloader|HMView|httplib|HTTrack|humanlinks|ImagesStripper|ImagesSucker|IndysLibrary|
InfonaviRobot|InterGET|InternetsNinja|Jennybot|JetCar|JOCsWebsSpider|Kenjin.Spider|Keyword.Density|
larbin|LeechFTP|Lexibot|libWeb/clsHTTP|LinkextractorPro|LinkScan/8.1a.Unix|LinkWalker|lwp-trivial|
MasssDownloader|Mata.Hari|Microsoft.URL|MIDownstool|MIIxpc|Mister.PiX|MistersPiX|moget|
Mozilla/3.Mozilla/2.01|Mozilla.*NEWT|Navroad|NearSite|NetAnts|NetMechanic|NetSpider|NetsVampire|
NetZIP|NICErsPRO|NPbot|Octopus|Offline.Explorer|OfflinesExplorer|OfflinesNavigator|Openfind|
Pagerabber|PapasFoto|pavuk|pcBrowser|ProgramsSharewares1|ProPowerbot/2.14|ProWebWalker|ProWebWalker|
psbot/0.1|QueryN.Metasearch|ReGet|RepoMonkey|RMA|SiteSnagger|SlySearch|SmartDownload|Spankbot|spanner|
Superbot|SuperHTTP|Surfbot|suzuran|Szukacz/1.4|tAkeOut|Teleport|TeleportsPro|Telesoft|The.Intraformant|
TheNomad|TightTwatbot|Titan|toCrawl/UrlDispatcher|toCrawl/UrlDispatcher|True_Robot|turingos|
Turnitinbot/1.5|URLy.Warning|VCI|VoidEYE|WebAuto|WebBandit|WebCopier|WebEMailExtrac.*|WebEnhancer|
WebFetch|WebGosIS|Web.Image.Collector|WebsImagesCollector|WebLeacher|WebmasterWorldForumbot|
WebReaper|WebSauger|WebsiteseXtractor|Website.Quester|WebsitesQuester|Webster.Pro|WebStripper|
WebsSucker|WebWhacker|WebZip|Wget|Widow|[Ww]eb[Bb]andit|WWW-Collector-E|WWWOFFLE|
XaldonsWebSpider|Xenu's|Zeus) [NC]
RewriteRule .? - [F]
```

Add these options only if not required by your wordpress site

Disable author pages
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_URI}  ^/$
    RewriteCond %{QUERY_STRING} ^/?author=([0-9]*) [NC]
    RewriteRule ^(.*)$ http://%{HTTP_HOST}/? [L,R=301,NC]
</IfModule>
```

Block WordPress xmlrpc.php requests
```
<Files "xmlrpc.php">
    Order Deny,Allow
    Deny from all
</Files>
```

Prevent image hotlinking
```
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?your-domain-here.com [NC]
    RewriteRule \.(jpg|jpeg|png|gif|webp)$ – [NC,F,L]
</IfModule>
```
