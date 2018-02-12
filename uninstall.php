<?php

defined( 'WP_UNINSTALL_PLUGIN' ) or exit;

// https://wordpress.stackexchange.com/a/236515/103670
function delete_options_with_prefix( $prefix ) {
    
    global $wpdb;
    
    $plugin_options = $wpdb->get_results( 
            "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'ihaf_insert_header_marale_%'" );

    foreach( $plugin_options as $option ) {
        delete_option( $option->option_name );
    }
}

delete_options_with_prefix( 'ihaf_insert_header_marale_' );
delete_options_with_prefix( 'ihaf_insert_footer_marale_' );