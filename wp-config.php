<?php
// Prevents users from editing plugin and theme files via the WordPress admin, enhancing security.
define('DISALLOW_FILE_EDIT',true);

// Forces SSL for all admin sessions, ensuring encrypted communication and protecting login data.
define('FORCE_SSL_ADMIN', true);

// Disables debug mode to prevent errors and warnings from being displayed publicly, improving security.
define('WP_DEBUG', false);

// Ensures that debugging information is not displayed to site visitors, even when debugging is enabled.
define('WP_DEBUG_DISPLAY', false);

// Automatically updates WordPress core files for security and maintenance releases, reducing vulnerabilities.
define('WP_AUTO_UPDATE_CORE', true);

// This prevents unauthorized users from installing new plugins or themes from the WordPress admin area
define('DISALLOW_FILE_MODS', true);

// WordPress saves every revision of a post or page, which can cause database bloat.
// Limiting the number of revisions can help optimize performance
define('WP_POST_REVISIONS', 5); // Limits revisions to 5

// For additional security, you can move the wp-content directory to a custom location to obscure your site’s file structure
define('WP_CONTENT_DIR', dirname(__FILE__) . '/custom-content');
?>
