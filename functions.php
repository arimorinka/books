<?php 
/**
* create and update db
**/
function books_info_insert_into_db() {
    global $wpdb;
    // creates books_info in database if not exists
    $table = $wpdb->prefix . "books_info"; 
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `post_id` text NOT NULL,
        `isbn` text NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`post_id`),
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
