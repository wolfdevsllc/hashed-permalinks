<?php
/**
 * Plugin Name: Hashed Permalinks
 * Plugin URI: https://wolfdevs.com/plugins/hashed-permalinks/
 * Description: This plugin will add a unique hash to the end of your post permalinks. Use %posthash% in your permalink structure to add the hash.
 * Version: 1.0
 * Author: WolfDevs
 * Author URI:  https://wolfdevs.com/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

function hashed_post_name($permalink, $post_id, $leavename) {
    if (strpos($permalink, '%posthash%') === FALSE) {
        return $permalink;
    }

    $post = get_post($post_id);
    if (!$post) {
        return $permalink;
    }

    // Hash the post title
    $hashed_post_title = hash('sha256', $post->post_title);

    // Shorten the hash to 6 characters
    $short_hash = substr($hashed_post_title, 0, 6);

    // Concatenate the post ID with the shortened hash
    $unique_hash = $post->ID . $short_hash;
    return str_replace('%posthash%', $unique_hash, $permalink);
}
add_filter('post_link', 'hashed_post_name', 10, 3);
add_filter('post_type_link', 'hashed_post_name', 10, 3);
add_filter('page_link', 'hashed_post_name', 10, 3); // Apply the function to pages as well

function custom_rewrite_tag() {
    add_rewrite_tag('%posthash%', '([^&]+)');
}
add_action('init', 'custom_rewrite_tag', 10, 0);

function custom_rewrite_rules() {
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'custom_rewrite_rules');
