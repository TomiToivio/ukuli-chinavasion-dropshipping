<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Hourly cron event activation.
 */
function ukuli_chinavasion_cron_activation() {
    if(!wp_next_scheduled('ukuli_hourly_chinavasion')) {
        wp_schedule_event(time(), 'hourly', 'ukuli_hourly_chinavasion');
    }
}
add_action( 'wp', 'ukuli_chinavasion_cron_activation' );

/**
 * Hourly cron event function.
 */
add_action('ukuli_hourly_chinavasion', 'ukuli_do_this_hourly_chinavasion');
function ukuli_do_this_hourly_chinavasion() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ukuli_chinavasion_api';
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    foreach($results as $result) {
        $postId = $result->post_id;
        $apiId = $result->api_id;
        $ukuli_chinavasion_dropshipping = get_post_meta($postId, 'ukuli_chinavasion_dropshipping', true );
	    if($ukuli_chinavasion_dropshipping == "yes") {
	       $lasttimestamp = get_post_meta( $postId, "ukuli_chinavasion_availability_timestamp", true);
	       $currenttimestamp = time ();
	       $timepassed = intval($currenttimestamp) - intval($lasttimestamp);
	       if($timepassed > 3600) { 
	   		    update_post_meta($postId, "ukuli_chinavasion_availability_timestamp", $currenttimestamp);
                $ukuliChinavasionAPI = new ukuliChinavasionAPI();
                $result = $ukuliChinavasionAPI->chinavasionCheckProductAvailability($postId);
           }
        }
    }
}
