<?php 

if( !function_exists( 'ju_plugin_activated_check' ) ) {
    function ju_plugin_activated_check( $plugin_file_name ) {
        include_once( ABSPATH . 'wp-admin/includes/pluing.php' ); 
        return is_plugin_active( $plugin_file_name ); 
    }
}