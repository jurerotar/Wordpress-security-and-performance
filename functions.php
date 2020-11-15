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
 */
add_filter('xmlrpc_enabled', function() {
    return false;
});

/**
 * Disables REST API for non-logged in users
 */
add_filter('rest_authentication_errors', function($result) {
    return (is_user_logged_in()) ? $result : new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
});

/**
 * Removes unnecesary information from <head> tag
 */
add_action('init', function() {
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');

    // Remove wordpress version
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'start_post_rel_link');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'rest_output_link_wp_head');

    // Remove Link header for REST API
    remove_action('template_redirect', 'rest_output_link_header', 11, 0 );

    // Remove Link header for shortlink
    remove_action('template_redirect', 'wp_shortlink_header', 11, 0 );

});

/**
 * Disable Wordpress RSS feed
 */
function disableFeed():void {
    wp_die('Feed has been disabled.');
}

add_action('do_feed', 'disableFeed', 1);
add_action('do_feed_rdf', 'disableFeed', 1);
add_action('do_feed_rss', 'disableFeed', 1);
add_action('do_feed_rss2', 'disableFeed', 1);
add_action('do_feed_atom', 'disableFeed', 1);
add_action('do_feed_rss2_comments', 'disableFeed', 1);
add_action('do_feed_atom_comments', 'disableFeed', 1);

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

?>
