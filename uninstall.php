<?php
//IF PLUGIN IS DELETED WE DO NOT WANT METADATA TO STAY IN DATABASE
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
// thanks juliobox for your report. In fact, we do not want to make a request per comment so we'd rather use $wpdb
global $wpdb;
$wpdb->query( "DELETE FROM $wpdb->commentmeta WHERE meta_key = 'twitAccount'" );
$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 'jm_ttc_twitter'" );

