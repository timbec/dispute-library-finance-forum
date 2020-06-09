<?php
/**
 * Custom subscriptions for bbPress, replies by e-mail
 *
 */

/*
Plugin Name: bbPress Post Via Mail
Description: Reply to posts, comments, bbPress topics by e-mail. Send notifications about new topics and replies as well.
Version: 1.2.8
Author: Unicornis, parts by Ryan McCue
Author URI: https://postviamail.unicornis.pl/
*/

if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	echo '<p>pvm requires PHP 5.3 or newer.</p>';
	exit;
}

register_activation_hook( __FILE__, 'pvm_activation' );
register_deactivation_hook( __FILE__, 'pvm_deactivation' );

add_action( 'plugins_loaded', 'pvm_load' );

function pvm_load() {
	define( 'PVM_PATH', __DIR__ );
	define( 'PVM_PLUGIN', plugin_basename( __FILE__ ) );
	spl_autoload_register( 'pvm_autoload' );
    load_plugin_textdomain( 'pvm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    pvm::bootstrap();
}

/**
 * Register cron event on activation
 */
function pvm_activation() {
	wp_schedule_event( time(), 'pvm_minutely', 'bb_pvm_check_inbox' );
}

/**
 * Clear cron event on deactivation
 */
function pvm_deactivation() {
	wp_clear_scheduled_hook( 'pvm_check_inbox' );
}




function pvm_autoload($class) {
	if ( strpos( $class, 'EmailReplyParser' ) === 0 ) {
		$filename = str_replace( array( '_', '\\' ), '/', $class );
		$filename = PVM_PATH . '/vendor/EmailReplyParser/src/' . $filename . '.php';
		if ( file_exists( $filename ) ) {
			require_once( $filename );
		}
		return;
	}
	if ( strpos( $class, 'pvm' ) !== 0 ) {
		return;
	}

	$filename = str_replace( array( '_', '\\' ), '/', $class );
	$filename = PVM_PATH . '/library/' . $filename . '.php';
	if ( file_exists( $filename ) ) {
		require_once( $filename );
	}
}
