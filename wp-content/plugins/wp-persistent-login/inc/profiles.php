<?php

// show sessions on your own profile
add_action( 'show_user_profile', 'persistent_login_admin_show_user_sessions' );
/* ------------------------------------------------------------------------ *
 * Adding session info to wp admin user profile
 * ------------------------------------------------------------------------ */
function persistent_login_admin_show_user_sessions( $user )
{
    echo  persistent_login_admin_render_sessions() ;
}

function persistent_login_admin_render_sessions()
{
    // setup vars
    global  $wpdb ;
    global  $tableRef ;
    // grab the user
    
    if ( isset( $_GET['user_id'] ) ) {
        $user = get_userdata( $_GET['user_id'] );
        $buttonText = 'Update User';
    } else {
        $user = wp_get_current_user();
        $buttonText = 'Update Profile';
    }
    
    // find this session token & create verifier
    $sessionToken = wp_get_session_token();
    
    if ( function_exists( 'hash' ) ) {
        $verifier = hash( 'sha256', $sessionToken );
    } else {
        $verifier = sha1( $sessionToken );
    }
    
    $output = '';
    // start outputting table
    $output .= '<table class="form-table persistent-login-table persistent-login-manage-sessions">';
    $output .= '<h2 id="sessions" style="margin: 2rem 0 0;">Sessions - WP Persistent Login</h2>';
    $output .= '<p class="description">Select the sessions you want to end, and click <strong>' . $buttonText . '</strong></p>';
    $output .= '<tr class="persistent-login-table-header-row" style="border-bottom: 1px solid #dfdfdf;">';
    $output .= '<th width="50%" style="padding-left: 10px;">Session Details</th>';
    $output .= '<th width="25%" style="padding-left: 10px;">Last Active</th>';
    $output .= '<th width="25%" style="padding-left: 10px; text-align: right;">Manage</th>';
    $output .= '</tr>';
    // get all of the users sessions & sort them by login time
    $sessions = get_user_meta( $user->ID, 'session_tokens', true );
    
    if ( is_array( $sessions ) ) {
        $loginTimes = array_column( $sessions, 'login' );
        array_multisort( $loginTimes, SORT_DESC, $sessions );
        // loop through all of the sessions & output information
        foreach ( $sessions as $key => $session ) {
            // calculate time since login
            $seconds_ago = time() - $session['login'];
            
            if ( $seconds_ago >= 31536000 ) {
                $loginTime = intval( $seconds_ago / 31536000 ) . " years ago";
            } elseif ( $seconds_ago >= 2419200 ) {
                $loginTime = intval( $seconds_ago / 2419200 ) . " months ago";
            } elseif ( $seconds_ago >= 86400 ) {
                $loginTime = intval( $seconds_ago / 86400 ) . " days ago";
            } elseif ( $seconds_ago >= 3600 ) {
                $loginTime = intval( $seconds_ago / 3600 ) . " hours ago";
            } elseif ( $seconds_ago >= 120 ) {
                $loginTime = intval( $seconds_ago / 60 ) . " mins ago";
            } elseif ( $seconds_ago >= 60 ) {
                $loginTime = intval( $seconds_ago / 60 ) . " min ago";
            } else {
                $loginTime = "Active now";
            }
            
            // identify user agent
            $device = new WhichBrowser\Parser( $session['ua'] );
            // output the row
            $output .= '<tr style="border-bottom: 1px solid #dfdfdf;">';
            // device & IP
            $output .= '<td width="50%">';
            $output .= ucfirst( $device->device->type ) . ' - ';
            $output .= $device->toString();
            $output .= '<br/>';
            $output .= '<small>';
            $output .= 'IP address: ' . $session['ip'];
            $output .= '&nbsp;&nbsp;<a href="http://ip-api.com/' . $session['ip'] . '" target="_blank">Find Location<span class="dashicons dashicons-external" style="font-size: 0.7rem;"></span></a>';
            $output .= '</small>';
            $output .= '</td>';
            // Login time
            $output .= '<td width="25%">';
            $output .= $loginTime;
            if ( $key === $verifier ) {
                $output .= ' <small class="meta"><strong>(this device)</strong></small>';
            }
            $output .= '</td>';
            // End Session
            $output .= '<td width="25%">
													<label title="End Session" style="cursor: pointer;padding: 3px 10px 4px; height: auto;" class="button right persistent-login-end-session-link">
														<input type="checkbox" name="endSessions[]" value="' . $key . '" style="margin: 0 5px 0 0;" title="End Session" />
														End Session
													</label>
											</td>';
            // END: End Session
            $output .= '</tr>';
        }
    }
    
    $output .= '</table>';
    $output .= '<style>
				
					.form-table.persistent-login-table { margin-left: 210px; width: calc(100% - 220px); }
				
					@media all and ( max-width: 782px ) {
						
						.persistent-login-end-session-link { float: none !important; }
						.form-table.persistent-login-table tr { padding: 1rem 0 !important; display: block; } 
						.form-table.persistent-login-table { width: 100%; margin-left: 0; }
						
					}
				</style>';
    // return the output
    return $output;
}

/* ------------------------------------------------------------------------ *
 * Saving session info
 * ------------------------------------------------------------------------ */
add_action( 'personal_options_update', 'persistent_login_admin_save_user_sessions' );
add_action( 'edit_user_profile_update', 'persistent_login_admin_save_user_sessions' );
function persistent_login_admin_save_user_sessions( $user_id )
{
    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    // remove session if requested
    
    if ( isset( $_POST['endSessions'] ) ) {
        // setup vars
        $tokens = $_POST['endSessions'];
        foreach ( $tokens as $token ) {
            // remove that session
            $updateSession = new Persistent_Login_Manage_Sessions( $user_id );
            $updateSession->persistent_login_update_session( $token );
        }
    }

}
