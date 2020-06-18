<?php

/**
 *   Plugin Name: WordPress Persistent Login
 *   Plugin URI: https://lukeseager.com/plugins/wp-persistent-login/
 *   Description: Keep users logged into your website securely.
 *   Author: Luke Seager
 *   Author URI:  https://lukeseager.com/
 *   Version: 1.3.12
 *   
 */
/*   
    Copyright 2018 Luke Seager  (email : info@lukeseager.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !function_exists( 'persistent_login' ) ) {
    /* ------------------------------------------------------------------------ *
     * Load freemius
     * ------------------------------------------------------------------------ */
    include_once plugin_dir_path( __FILE__ ) . 'freemius.php';
    require plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';
    /* ------------------------------------------------------------------------ *
     * Setup - include plugin and get global vars
     * ------------------------------------------------------------------------ */
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    // plugin variables
    global  $tableRef ;
    $tableRef = 'persistent_logins';
    // set current database version
    global  $persistent_login_db_version ;
    $persistent_login_db_version = '1.3.12';
    // set global plugin path
    global  $pluginPath ;
    $pluginPath = plugin_dir_path( __FILE__ );
    /* ------------------------------------------------------------------------ *
     * Install persistent login
     * ------------------------------------------------------------------------ */
    function persistent_login_install()
    {
        // add db version for future reference
        global  $persistent_login_db_version ;
        update_option( 'persistent_login_db_version', $persistent_login_db_version );
        // setup CRON to check how many users are logged in
        // Use wp_next_scheduled to check if the event is already scheduled
        $timestamp = wp_next_scheduled( 'persistent_login_user_count' );
        // If $timestamp == false schedule daily backups since it hasn't been done previously
        if ( $timestamp == false ) {
            // Schedule the event for right now, then to repeat daily using the hook 'persistent_login_user_count'
            wp_schedule_event( time(), 'twicedaily', 'persistent_login_user_count' );
        }
        // set detaults for permissions - all roles are available for persistent login by default
        // free options
        if ( !get_option( 'persistent_login_options' ) ) {
            $defaultOptions = array(
                'duplicateSessions' => '0',
            );
        }
    }
    
    register_activation_hook( __FILE__, 'persistent_login_install' );
    /* ------------------------------------------------------------------------ *
     * END install persistent login
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Update DB function
     * ------------------------------------------------------------------------ */
    function persistent_login_update_db_check()
    {
        // check db version
        global  $persistent_login_db_version ;
        $current_persistent_login_db_version = get_option( 'persistent_login_db_version' );
        // test db version number against plugin
        if ( $current_persistent_login_db_version !== $persistent_login_db_version ) {
            // if different, run the update function
            $persistent_login_db_update = persistent_login_update_db( $current_persistent_login_db_version );
        }
    }
    
    add_action( 'plugins_loaded', 'persistent_login_update_db_check' );
    // run update of database
    function persistent_login_update_db( $persistent_login_db_version )
    {
        // multi-device support
        
        if ( $persistent_login_db_version === '1.1.3' ) {
            // load required global vars
            global  $wpdb ;
            global  $tableRef ;
            // set table name
            $table = $wpdb->prefix . $tableRef;
            // fetch charset for db
            $charset_collate = $wpdb->get_charset_collate();
            // run query
            $table_update = $wpdb->query( "\n\t\t\t\t\t\t\t\tALTER TABLE {$table} \n\t\t\t\t\t\t\t\tADD `ip` INT(11) NOT NULL AFTER `login_key`,\n\t\t\t\t\t\t\t\tADD `user_agent` varchar(255) NOT NULL AFTER `ip`\n\t\t\t\t\t\t\t" );
            // update db version option
            update_option( 'persistent_login_db_version', '1.1.3' );
            $persistent_login_db_version = '1.1.3';
        }
        
        // 1.1.3 update
        // timestamps
        
        if ( $persistent_login_db_version === '1.1.3' ) {
            // load required global vars
            global  $wpdb ;
            global  $tableRef ;
            // set table name
            $table = $wpdb->prefix . $tableRef;
            // fetch charset for db
            $charset_collate = $wpdb->get_charset_collate();
            // run query
            $table_update = $wpdb->query( "\n\t\t\t\t\t\t\t\tALTER TABLE {$table} \n\t\t\t\t\t\t\t\tADD `timestamp` CHAR(19) NOT NULL AFTER `user_agent`\n\t\t\t\t\t\t\t" );
            // update db version option
            update_option( 'persistent_login_db_version', '1.2.3' );
            $persistent_login_db_version = '1.2.3';
        }
        
        // 1.2.3 update
        // remove db, no longer needed
        
        if ( $persistent_login_db_version === '1.2.3' ) {
            // remove all existing logins
            global  $wpdb ;
            global  $tableRef ;
            $table = $wpdb->prefix . $tableRef;
            // drop the table, we don't need it anymore!
            $sql = "DROP TABLE IF EXISTS {$table};";
            $drop = $wpdb->query( $sql );
            
            if ( $drop ) {
                // update db version option
                update_option( 'persistent_login_db_version', '1.3.0' );
                $persistent_login_db_version = '1.3.0';
                return true;
            } else {
                return false;
            }
        
        }
        
        // 1.3.0 update
        // fixing options in options table
        
        if ( $persistent_login_db_version === '1.3.0' ) {
            // fetching the current settings, which we don't need any more!
            $current_settings = get_option( 'persistent_login_options_user_access' );
            
            if ( $current_settings ) {
                // now delete the old free option, not needed anymore
                delete_option( 'persistent_login_options_user_access' );
                // update db version option
                update_option( 'persistent_login_db_version', '1.3.10' );
                $persistent_login_db_version = '1.3.10';
                return true;
            }
        
        }
        
        // 1.3.10 update
        
        if ( $persistent_login_db_version === '1.3.10' ) {
            // Use wp_next_scheduled to check if the event is already scheduled
            $timestamp = wp_next_scheduled( 'persistent_login_user_count' );
            // If $timestamp == false schedule daily backups since it hasn't been done previously
            if ( $timestamp == false ) {
                // Schedule the event for right now, then to repeat daily using the hook 'persistent_login_user_count'
                wp_schedule_event( time(), 'twicedaily', 'persistent_login_user_count' );
            }
            // update db version option
            update_option( 'persistent_login_db_version', '1.3.12' );
            $persistent_login_db_version = '1.3.12';
            return true;
        }
        
        // 1.3.12 update
    }
    
    /* ------------------------------------------------------------------------ *
     * END Update DB persistent login
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Uninstall persistent login
     * ------------------------------------------------------------------------ */
    // setup uninstall cleanup
    function persistent_login_uninstall_cleanup()
    {
        // remove database options
        $options = array( 'persistent_login_db_version', 'persistent_login_options', 'persistent_login_user_count' );
        foreach ( $options as $option ) {
            delete_option( $option );
        }
        // unschedule cron event
        wp_clear_scheduled_hook( 'persistent_login_user_count' );
    }
    
    persistent_login()->add_action( 'after_uninstall', 'persistent_login_uninstall_cleanup' );
    /* ------------------------------------------------------------------------ *
     * END uninstall persistent login
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Downgrade data after trial
     * ------------------------------------------------------------------------ */
    
    if ( persistent_login()->is_trial_utilized() && persistent_login()->is_free_plan() ) {
        function persistent_login_downgrade_settings()
        {
            // delete premium options from db
            delete_option( 'persistent_login_options_premium' );
        }
        
        add_action( 'admin_init', 'persistent_login_downgrade_settings' );
    }
    
    /* ------------------------------------------------------------------------ *
     * END Downgrade data after trial
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Usage Stats CRON job
     * ------------------------------------------------------------------------ */
    // Hook our function , persistent_login_update_user_count(), into the action wi_create_daily_backup
    add_action( 'persistent_login_user_count', 'persistent_login_update_user_count' );
    function persistent_login_update_user_count()
    {
        // setup variables for chunking
        $chunk_size = 1;
        $offset = 0;
        $found_users = 1;
        // get the ball rolling
        $loggedInUsers = array();
        $result = [];
        $roles = [];
        // add the current roles to the outputted roles array, start user count at 0.
        
        if ( isset( $persistent_login_premium_options ) ) {
            $allowed_roles = $persistent_login_premium_options['roles'];
            foreach ( $allowed_roles as $role ) {
                $roles[$role] = 0;
            }
        } else {
            $allowed_roles = array();
            global  $wp_roles ;
            foreach ( $wp_roles->roles as $key => $value ) {
                $roles[$key] = 0;
            }
        }
        
        // chunk the results
        while ( $found_users > 0 ) {
            $loggedInUsers = array();
            $args = array(
                'role__in' => $allowed_roles,
                'fields'   => array( 'ID' ),
                'number'   => $chunk_size,
                'offset'   => $chunk_size * $offset,
            );
            $allUsers = get_users( $args );
            // loop through the chunk we've got, check if these users have sessions
            foreach ( $allUsers as $user ) {
                $wp_session_token = WP_Session_Tokens::get_instance( $user->ID );
                $sessions = count( $wp_session_token->get_all() );
                if ( $sessions ) {
                    array_push( $loggedInUsers, $user->ID );
                }
            }
            if ( $loggedInUsers ) {
                
                if ( !empty($allowed_roles) ) {
                    foreach ( $allowed_roles as $role ) {
                        // get all unique users for each role
                        $args = array(
                            'role'    => $role,
                            'include' => $loggedInUsers,
                            'fields'  => array( 'ID' ),
                        );
                        $users = count( get_users( $args ) );
                        $roles[$role] = $roles[$role] + $users;
                    }
                } else {
                    // get all roles
                    global  $wp_roles ;
                    foreach ( $wp_roles->roles as $key => $value ) {
                        // get all unique users for each role
                        $args = array(
                            'role'    => $key,
                            'include' => $loggedInUsers,
                            'fields'  => array( 'ID' ),
                        );
                        $users = count( get_users( $args ) );
                        $roles[$key] = $roles[$key] + $users;
                    }
                }
            
            }
            // increase the array size
            $offset += 1;
            $found_users = count( $allUsers );
        }
        // update option
        update_option( 'persistent_login_user_count', $roles );
    }
    
    /* ------------------------------------------------------------------------ *
     * END Usage Stats CRON job
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Usage Stats
     * ------------------------------------------------------------------------ */
    // get the current device count
    function persistent_login_getDeviceCount()
    {
        $deviceCount = 0;
        
        if ( isset( $persistent_login_premium_options ) ) {
            $roles = $persistent_login_premium_options['roles'];
        } else {
            $roles = array();
        }
        
        $args = array(
            'role__in' => $roles,
            'fields'   => array( 'ID' ),
        );
        $allUsers = get_users( $args );
        foreach ( $allUsers as $user ) {
            $wp_session_token = WP_Session_Tokens::get_instance( $user->ID );
            $sessions = $wp_session_token->get_all();
            if ( $sessions ) {
                foreach ( $sessions as $session ) {
                    $deviceCount++;
                }
            }
        }
        return $deviceCount;
    }
    
    // get the current user count
    function persistent_login_getUserCount()
    {
        $user_count = 0;
        $roles = get_option( 'persistent_login_user_count' );
        if ( isset( $roles ) && !empty($roles) ) {
            foreach ( $roles as $role ) {
                $user_count += $role;
            }
        }
        return $user_count;
    }
    
    // get the current roles breakdown
    function persistent_login_getRolesBreakdown()
    {
        $user_count = get_option( 'persistent_login_user_count' );
        return $user_count;
    }
    
    /* ------------------------------------------------------------------------ *
     * Plugins admin page
     * ------------------------------------------------------------------------ */
    // add settings button to plugins page at the front of links
    function persistent_login_add_settings_link( $links )
    {
        $settings_link = '<a href="options-general.php?page=wp-persistent-login">' . __( 'Settings' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
    
    $plugin = plugin_basename( __FILE__ );
    add_filter( "plugin_action_links_{$plugin}", 'persistent_login_add_settings_link' );
    /* ------------------------------------------------------------------------ *
     * END plugins admin page
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Dashboard - At a glance
     * ------------------------------------------------------------------------ */
    function persistent_login_dashboard_stats()
    {
        // check if we should show the dashboard stats or not
        $hideDashboardStats = get_option( 'persistent_login_dashboard_stats' );
        if ( !$hideDashboardStats ) {
            
            if ( $hideDashboardStats === '0' ) {
                $users = persistent_login_getUserCount();
                
                if ( $users === 1 ) {
                    $users = $users . ' user';
                    $plural = ' is';
                } else {
                    $users = $users . ' users';
                    $plural = ' are';
                }
                
                
                if ( persistent_login()->is_not_paying() ) {
                    $button = '<a href="' . persistent_login()->get_upgrade_url() . '" class="button button-primary">
				    	View Upgrade Options
				    </a>';
                    $title = ' - Free Forever Plan';
                } else {
                    $button = '';
                    $title = ' - Premium Plan';
                }
                
                echo  sprintf(
                    '<hr/><h3><strong>WordPress Persistent Login %s</strong></h3>
		      		<p><strong>%s</strong> %s logged into your website.</p>
		      		<p>
		      			<a href="' . admin_url( '/options-general.php?page=wp-persistent-login' ) . '" class="button">Manage Settings</a>
		      			&nbsp; %s
		      		</p><hr/>',
                    $title,
                    $users,
                    $plural,
                    $button
                ) ;
            }
        
        }
    }
    
    add_action( 'activity_box_end', 'persistent_login_dashboard_stats' );
    /* ------------------------------------------------------------------------ *
     * END Dashboard - At a glance 
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Options page
     * ------------------------------------------------------------------------ */
    // load up options.php to create options page
    if ( is_admin() ) {
        include plugin_dir_path( __FILE__ ) . '/inc/options.php';
    }
    /* ------------------------------------------------------------------------ *
     * END options page
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * wp admin user profile page
     * ------------------------------------------------------------------------ */
    // load up options.php to create options page
    if ( is_admin() ) {
        include plugin_dir_path( __FILE__ ) . '/inc/profiles.php';
    }
    /* ------------------------------------------------------------------------ *
     * wp admin user profile page
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Update cookie expiration time
     * ------------------------------------------------------------------------ */
    function persistent_login_set_login_expiration( $expiration, $user_id, $remember )
    {
        
        if ( $remember ) {
            // default expiration to 1 year
            $expiration = strtotime( '1 year', 0 );
            $user = get_user_by( 'id', $user_id );
        }
        
        // return the expiration time
        return $expiration;
    }
    
    add_filter(
        'auth_cookie_expiration',
        'persistent_login_set_login_expiration',
        10,
        3
    );
    /* ------------------------------------------------------------------------ *
     * END Update cookie expiration time
     * ------------------------------------------------------------------------ */
    /* --------------------------------------------------------------------------------------- *
     * Check remember me by default, add cookie to test if user submitted with remember me.
     * --------------------------------------------------------------------------------------- */
    // function to add scripts to improve login page
    function persistent_login_rememberme_checked()
    {
        echo  '<script>' ;
        echo  "\n\t\t\t\t\tdocument.addEventListener('DOMContentLoaded', function(event) {\n\t\t\t\t\t\t\n\t\t\t\t\t\t// check remember me by default\n\t\t\t\t\t\tvar forms = document.querySelectorAll('form'); \t\t\t\t\t\t\n\t\t\t\t\t\tif (forms) {\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t// look out for inputs named rememberme\n\t\t\t\t\t\t\t\tvar rememberArray = [];\n\t\t\t\t\t\t\t\tvar rememberMe = document.getElementsByName('rememberme');\n\t\t\t\t\t\t\t\tif( rememberMe.length ) {\n\t\t\t\t\t\t\t\t\trememberArray.push(rememberMe);\n\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t// look out for inputs named remember\n\t\t\t\t\t\t\t\tvar remember = document.getElementsByName('remember');\n\t\t\t\t\t\t\t\tif( remember.length ) {\n\t\t\t\t\t\t\t\t\trememberArray.push(remember);\n\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t// if there are remember me inputs\n\t\t\t\t\t\t\tif( rememberArray.length ) { \t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t// 'check' the inputs so they're active\t\t\n\t\t\t\t\t\t\t\t\tfor (i = 0; i < rememberArray.length; i++) {\n\t\t\t\t\t\t\t\t\t\tfor (x = 0; x < rememberArray[i].length; x++) {\n\t\t\t\t\t\t\t\t\t\t  rememberArray[i][x].checked = true;\n\t\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t}\n\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t// test for Ultimate Member Plugin forms\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t// find the UM checkboxes\n\t\t\t\t\t\t\t\tvar UmCheckboxIcon = document.querySelectorAll('.um-icon-android-checkbox-outline-blank');\n\t\t\t\t\t\t\t\tvar UmCheckboxLabel = document.querySelectorAll('.um-field-checkbox');\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\tif( UmCheckboxIcon.length && UmCheckboxLabel.length ) {\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t// loop through UM checkboxes\n\t\t\t\t\t\t\t\t\tfor (i = 0; i < UmCheckboxLabel.length; i++) {\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t// find the UM input element\n\t\t\t\t\t\t\t\t\t\tvar UMCheckboxElement = UmCheckboxLabel[i].children;\n\t\t\t\t\t\t\t\t\t\tvar UMCheckboxElementName = UMCheckboxElement[0].getAttribute('name');\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t// check if UM input element is remember me box\n\t\t\t\t\t\t\t\t\t\tif( UMCheckboxElementName === 'remember' || UMCheckboxElementName === 'rememberme' ) {\n\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t// activate the UM checkbox if it is a remember me box\n\t\t\t\t\t\t\t\t\t\t\tUmCheckboxLabel[i].classList.add('active');\n\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t// swap out UM classes to show the active state\n\t\t\t\t\t\t\t\t\t\t\tUmCheckboxIcon[i].classList.add('um-icon-android-checkbox-outline');\n\t\t\t\t\t\t\t\t\t\t\tUmCheckboxIcon[i].classList.remove('um-icon-android-checkbox-outline-blank');\n\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t} // endif\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t} // end for\n\t\n\t\t\t\t\t\t\t\t} // endif UM\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t// test for AR Member\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\tvar ArmRememberMeCheckboxContainer = document.querySelectorAll('.arm_form_input_container_rememberme');\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\tif( ArmRememberMeCheckboxContainer.length ) {\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\tfor (i = 0; i < ArmRememberMeCheckboxContainer.length; i++) {\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\tvar ArmRememberMeCheckbox = ArmRememberMeCheckboxContainer[i].querySelectorAll('md-checkbox');\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\tif( ArmRememberMeCheckbox.length ) {\n\t\t\t\t\t\t\t\t\t\t\t// loop through AR Member checkboxes\n\t\t\t\t\t\t\t\t\t\t\tfor (x = 0; x < ArmRememberMeCheckbox.length; x++) {\n\t\t\t\t\t\t\t\t\t\t\t\tif( ArmRememberMeCheckbox[x].classList.contains('ng-empty') ) {\n\t\t\t\t\t\t\t\t\t\t\t\t\tArmRememberMeCheckbox[x].click();\n\t\t\t\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t} // end if AR Member\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t\t} // endif forms\n\t\t\t\t\t\t\n\t\t\t\t\t});\n\t\t\n\t\t\t\t\t" ;
        echo  '</script>' ;
    }
    
    add_action( 'wp_footer', 'persistent_login_rememberme_checked' );
    function persistent_login_check_remember_me()
    {
        add_filter( 'login_footer', 'persistent_login_rememberme_checked' );
    }
    
    add_action( 'init', 'persistent_login_check_remember_me' );
    /* ------------------------------------------------------------------------ *
     * Update auth cookie
     * ------------------------------------------------------------------------ */
    // update auth cookie with new login time, expiry time & IP address
    function persistent_login_update_auth_cookie( $cookieElements, $user )
    {
        
        if ( $user ) {
            
            if ( persistent_login()->can_use_premium_code() ) {
                // check if user has premium options
                $persistent_login_premium_options = get_option( 'persistent_login_options_premium' );
                if ( isset( $persistent_login_premium_options ) && !empty($persistent_login_premium_options['roles']) ) {
                    $persistent_login_roles = $persistent_login_premium_options['roles'];
                }
            }
            
            
            if ( !isset( $persistent_login_roles ) ) {
                global  $wp_roles ;
                $persistent_login_roles = array();
                foreach ( $wp_roles->roles as $key => $value ) {
                    array_push( $persistent_login_roles, $key );
                }
            }
            
            
            if ( $persistent_login_roles ) {
                
                if ( is_array( $user->roles ) ) {
                    $role_check = array_intersect( $user->roles, $persistent_login_roles );
                } else {
                    $role_check = in_array( $user->roles, $persistent_login_roles );
                }
                
                
                if ( $role_check === true ) {
                    
                    if ( isset( $persistent_login_premium_options ) && isset( $persistent_login_premium_options['cookieTime'] ) ) {
                        $expiration = $persistent_login_premium_options['cookieTime'];
                    } else {
                        $expiration = strtotime( '1 year', 0 );
                        // 1 year default
                    }
                    
                    // get the session verifier from the token
                    $sessionToken = $cookieElements['token'];
                    
                    if ( function_exists( 'hash' ) ) {
                        $verifier = hash( 'sha256', $sessionToken );
                    } else {
                        $verifier = sha1( $sessionToken );
                    }
                    
                    // update the login time, expires time
                    $sessions = get_user_meta( $user->ID, 'session_tokens', true );
                    $sessions[$verifier]['login'] = time();
                    $sessions[$verifier]['expiration'] = time() + $expiration;
                    $sessions[$verifier]['ip'] = $_SERVER["REMOTE_ADDR"];
                    // update the token with new data
                    $wp_session_token = WP_Session_Tokens::get_instance( $user->ID );
                    $wp_session_token->update( $sessionToken, $sessions[$verifier] );
                    // apply filter for allowing duplicate sessions, default false
                    $currentOptions = get_option( 'persistent_login_options' );
                    $allowDuplicateSessions = $currentOptions['duplicateSessions'];
                    // remove any exact matches to this session
                    foreach ( $sessions as $key => $session ) {
                        if ( $key !== $verifier ) {
                            if ( $allowDuplicateSessions === '0' ) {
                                // if we're on the same user agent and same IP, we're probably on the same device
                                // delete the duplicate session
                                
                                if ( $session['ip'] === $sessions[$verifier]['ip'] && $session['ua'] === $sessions[$verifier]['ua'] ) {
                                    $updateSession = new Persistent_Login_Manage_Sessions( $user->ID );
                                    $updateSession->persistent_login_update_session( $key );
                                }
                            
                            }
                        }
                        // if key is different to identifier
                    }
                    // set users local cookie again - checks if they should be remembered
                    $rememberUserCheck = get_user_meta( $user->ID, 'persistent_login_remember_me', true );
                    
                    if ( $rememberUserCheck === 'true' ) {
                        // if the user should be remembered, reset the cookie so the cookie time is reset
                        wp_set_auth_cookie(
                            $user->ID,
                            true,
                            is_ssl(),
                            $sessionToken
                        );
                    } else {
                        // if the users doen't want to be remembered, don't re-set the cookie
                    }
                
                }
                
                // end if roles match the user roles
            }
            
            // endif persistent login roles
        }
        
        // endif user
    }
    
    add_action(
        'auth_cookie_valid',
        'persistent_login_update_auth_cookie',
        10,
        2
    );
    /* ------------------------------------------------------------------------ *
     * END Update auth cookie
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Test if user checked remember me
     * ------------------------------------------------------------------------ */
    function persistent_login_remember_me_check( $secure_cookie, $credentials )
    {
        $user = get_user_by( 'login', $credentials['user_login'] );
        if ( $user && $credentials['remember'] === true ) {
            update_user_meta( $user->ID, 'persistent_login_remember_me', 'true' );
        }
        if ( $user && $credentials['remember'] === false ) {
            delete_user_meta( $user->ID, 'persistent_login_remember_me', 'true' );
        }
        return $secure_cookie;
    }
    
    add_filter(
        'secure_signon_cookie',
        'persistent_login_remember_me_check',
        10,
        2
    );
    /* ------------------------------------------------------------------------ *
     * END Test if user checked remember me
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Extend WP_User_Meta_Session_Tokens class
     * ------------------------------------------------------------------------ */
    // make protected methods publicly accessible
    class Persistent_Login_Manage_Sessions extends WP_User_Meta_Session_Tokens
    {
        // rebuild constructor
        public function __construct( $user_id )
        {
            $this->user_id = $user_id;
        }
        
        // allow us to update sessions by verifier instead of unhashed token
        public function persistent_login_update_session( $verifier, $session = null )
        {
            $this->update_session( $verifier, $session );
        }
        
        // allow us to get a session by verifier instead of unhashed token
        public function persistent_login_get_session( $verifier )
        {
            $this->get_session( $verifier );
        }
    
    }
    /* ------------------------------------------------------------------------ *
     * END Extend WP_User_Meta_Session_Tokens class
     * ------------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ *
     * Tidy up after user logout
     * ------------------------------------------------------------------------ */
    function persistent_login_manage_logout()
    {
        delete_user_meta( get_current_user_id(), 'persistent_login_remember_me', 'true' );
    }
    
    add_action( 'clear_auth_cookie', 'persistent_login_manage_logout' );
}

// end if persistent login exists