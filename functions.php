<?php

/**
 * Removes x-powered-by header which is set by PHP
 */
header_remove('x-powered-by');

/**
 * Remove X-Pingback header
 */
add_filter('pings_open', function() {
    return false;
});

/**
 * Disables xmlrpc.php
 * Disable only if your site does not require use of xmlrpc
 */
add_filter('xmlrpc_enabled', function() {
    return false;
});

/**
 * Disables REST API completely for non-logged in users
 * Use this option only if your site does not require use of REST API
 */
// add_filter('rest_authentication_errors', function($result) {
//    return (is_user_logged_in()) ? $result : new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
// });

/**
 * Disables Wordpress default REST API endpoints.
 * Use this option if your plugins require use of REST API, but would still like to disable core endpoints.
 */
add_filter('rest_endpoints', function($endpoints) {
    // If user is logged in, allow all endpoints
    if(is_user_logged_in()) {
        return $endpoints;
    }
    foreach($endpoints as $route => $endpoint) {
        if(stripos($route, '/wp/') === 0) {
            unset($endpoints[ $route ]);
        }
    }
    return $endpoints;
});

/**
 * Disable plugins auto-update email notifications
 */
add_filter( 'auto_plugin_update_send_email', function() {
    return false;
});

/**
 * Disable themes auto-update email notifications
 */
add_filter( 'auto_theme_update_send_email', function() {
    return false;
});

/**
 * Removes unnecesary information from <head> tag
 */
add_action('init', function() {
    // Remove post and comment feed link
    remove_action( 'wp_head', 'feed_links', 2 );

    // Remove post category links
	remove_action('wp_head', 'feed_links_extra', 3);

    // Remove link to the Really Simple Discovery service endpoint
	remove_action('wp_head', 'rsd_link');

    // Remove the link to the Windows Live Writer manifest file
	remove_action('wp_head', 'wlwmanifest_link');

    // Remove the XHTML generator that is generated on the wp_head hook, WP version
	remove_action('wp_head', 'wp_generator');

    // Remove start link
	remove_action('wp_head', 'start_post_rel_link');

    // Remove index link
	remove_action('wp_head', 'index_rel_link');

    // Remove previous link
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);

    // Remove relational links for the posts adjacent to the current post
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

    // Remove relational links for the posts adjacent to the current post
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove REST API links
    remove_action('wp_head', 'rest_output_link_wp_head');

    // Remove Link header for REST API
    remove_action('template_redirect', 'rest_output_link_header', 11, 0 );

    // Remove Link header for shortlink
    remove_action('template_redirect', 'wp_shortlink_header', 11, 0 );

});

/**
 * List of feeds to disable
*/
$feeds = [
    'do_feed',
    'do_feed_rdf',
    'do_feed_rss',
    'do_feed_rss2',
    'do_feed_atom',
    'do_feed_rss2_comments',
    'do_feed_atom_comments',
];

foreach($feeds as $feed) {
    add_action($feed, function() {
        wp_die('Feed has been disabled.');
    }, 1);
}

/**
 * Remove wp-embed.js file from loading
 */
add_action( 'wp_footer', function() {
    wp_deregister_script('wp-embed');
});

/**
 * Enable WebP image type upload
 */
add_filter('mime_types', function($existing_mimes) {
    $existing_mimes['webp'] = 'image/webp';
    return $existing_mimes;
});

/**
 * Display WebP thumbnail
 */
add_filter('file_is_displayable_image', function($result, $path) {
    return ($result) ? $result : (empty(@getimagesize($path)) || !in_array(@getimagesize($path)[2], [IMAGETYPE_WEBP]));
}, 10, 2);

?>
