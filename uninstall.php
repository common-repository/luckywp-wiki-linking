<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('lwpwl_dbVersion');

delete_option('lwpwl_general');

global $wpdb;
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'lwpwl_item');
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'lwpwl_post');
