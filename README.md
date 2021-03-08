# Apache configuration for secure and performant Wordpress sites

Apache configuration for secure and performant Wordpress sites. Some of these rules may not work with your Wordpress installation, so test settings before deploying.

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
Prevent access to important files in the root folder. Attackers can use the information in these files to gain important information about your Wordpress installation and server settings.
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
`wp-includes` folder contains core files for your Wordpress installation. These files are never needed by the users, so they should not have access to them.
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

    # X-Content-Type-Options
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

</IfModule>
```

## Block bad bots

Block spambots (Updated 12.10.2020)
```apache
# Block spambots (Updated 12.10.2020)
RewriteEngine On
RewriteBase /
RewriteCond %{HTTP_USER_AGENT} ^(aesop_com_spiderman|alexibot|backweb|bandit|batchftp|bigfoot) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(black.?hole|blackwidow|blowfish|botalot|buddy|builtbottough|bullseye) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(cheesebot|cherrypicker|chinaclaw|collector|copier|copyrightcheck) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(cosmos|crescent|curl|custo|da|diibot|disco|dittospyder|dragonfly) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(drip|easydl|ebingbong|ecatch|eirgrabber|emailcollector|emailsiphon) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(emailwolf|erocrawler|exabot|eyenetie|filehound|flashget|flunky) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(frontpage|getright|getweb|go.?zilla|go-ahead-got-it|gotit|grabnet) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(grafula|harvest|hloader|hmview|httplib|httrack|humanlinks|ilsebot) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(infonavirobot|infotekies|intelliseek|interget|iria|jennybot|jetcar) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(joc|justview|jyxobot|kenjin|keyword|larbin|leechftp|lexibot|lftp|libweb) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(likse|linkscan|linkwalker|lnspiderguy|lwp|magnet|mag-net|markwatch) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(mata.?hari|memo|microsoft.?url|midown.?tool|miixpc|mirror|missigua) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(mister.?pix|moget|mozilla.?newt|nameprotect|navroad|backdoorbot|nearsite) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(net.?vampire|netants|netcraft|netmechanic|netspider|nextgensearchbot) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(attach|nicerspro|nimblecrawler|npbot|octopus|offline.?explorer) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(offline.?navigator|openfind|outfoxbot|pagegrabber|papa|pavuk) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(pcbrowser|php.?version.?tracker|pockey|propowerbot|prowebwalker) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(psbot|pump|queryn|recorder|realdownload|reaper|reget|true_robot) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(repomonkey|rma|internetseer|sitesnagger|siphon|slysearch|smartdownload) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(snake|snapbot|snoopy|sogou|spacebison|spankbot|spanner|sqworm|superbot) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(superhttp|surfbot|asterias|suzuran|szukacz|takeout|teleport) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(telesoft|the.?intraformant|thenomad|tighttwatbot|titan|urldispatcher) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(turingos|turnitinbot|urly.?warning|vacuum|vci|voideye|whacker) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^(libwww-perl|widow|wisenutbot|wwwoffle|xaldon|xenu|zeus|zyborg|anonymouse) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^web(zip|emaile|enhancer|fetch|go.?is|auto|bandit|clip|copier|master|reaper|sauger|site.?quester|whack) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^.*(craftbot|download|extract|stripper|sucker|ninja|clshttp|webspider|leacher|collector|grabber|webpictures).*$ [NC]
RewriteRule . - [F,L]
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
Add these options to your `.htaccess` only if the functionality they provide is not required by your Wordpress site.

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

### Prevent image hotlinking
```apache
# Prevent image hotlinking
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?your-domain-here.com [NC]
    RewriteRule \.(jpg|jpeg|png|gif|webp)$ – [NC,F,L]
</IfModule>
```

### Prevent resources hotlinking
```apache
# Prevent resources hotlinking
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?your-domain-here.com [NC]
    RewriteRule \.(js|css)$ – [NC,F,L]
</IfModule>
```
