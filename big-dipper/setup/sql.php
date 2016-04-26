<?php
/**
 * Author: Vitaly Kukin
 * Date: 23.04.2016
 * Time: 20:58
 */

function bidi_sql_list() {

    global $wpdb;

    $charset_collate = !empty($wpdb->charset) ? "DEFAULT CHARACTER SET $wpdb->charset" : "DEFAULT CHARACTER SET utf8mb4";

    return array(

        "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bidi_posts (
            id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) unsigned NOT NULL,
            seo_title VARCHAR(255) DEFAULT NULL,
            seo_keywords VARCHAR(255) DEFAULT NULL,
            seo_description TEXT DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY (`post_id`)
	    ) ENGINE=InnoDB {$charset_collate};",
    );
}