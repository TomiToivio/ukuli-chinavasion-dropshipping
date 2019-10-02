<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Create database tables for plugin.
 */
function ukuli_chinavasion_database_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ukuli_chinavasion_api';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	    $charset_collate = $wpdb->get_charset_collate();
	    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id varchar(250),
        api_id varchar(250),
	    PRIMARY KEY  (id)
	    ) $charset_collate;";
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql );
	}
    $table_name = $wpdb->prefix . 'ukuli_chinavasion_log';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	    $charset_collate = $wpdb->get_charset_collate();
	    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
	    id mediumint(9) NOT NULL AUTO_INCREMENT,
	    event varchar(140) NOT NULL,
	    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	    PRIMARY KEY  (id)
	    ) $charset_collate;";
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql );
	}
}
add_action( 'wp', 'ukuli_chinavasion_database_install' );

/**
 * Drop database tables for plugin on uninstall.
 */
function ukuli_chinavasion_remove_database() {
     global $wpdb;
     $tables = array("ukuli_chinavasion_log","ukuli_chinavasion_api");
     foreach ($tables as $table) {
        $table_name = $wpdb->prefix . $table;
        $sql = "DROP TABLE IF EXISTS $table_name;";
        $wpdb->query($sql);
     }
}    
