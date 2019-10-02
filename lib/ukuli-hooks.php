<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Remove deleted posts from database.
 */
add_action( 'delete_post', 'ukuli_chinavasion_delete_post' );
function ukuli_chinavasion_delete_post($postid) { 
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'ukuli_chinavasion_api';
    $post_type = get_post_type($postid);
    if($post_type == "product") {
        $ukuli_chinavasion_dropshipping = get_post_meta($postid, 'ukuli_chinavasion_dropshipping', true );
	    if($ukuli_chinavasion_dropshipping == "yes") {
            $wpdb->delete($table_name, array('post_id' => $postid));
        }
    }
}
