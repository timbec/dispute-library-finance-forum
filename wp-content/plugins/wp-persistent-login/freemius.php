<?php

// Create a helper function for easy SDK access.
function persistent_login()
{
    global  $persistent_login ;
    
    if ( !isset( $persistent_login ) ) {
        // Include Freemius SDK.
        require_once plugin_dir_path( __FILE__ ) . '/inc/freemius/start.php';
        $persistent_login = fs_dynamic_init( array(
            'id'             => '1917',
            'slug'           => 'wp-persistent-login',
            'type'           => 'plugin',
            'public_key'     => 'pk_2f0822b0db5884898e4f60e4b1d48',
            'is_premium'     => false,
            'has_addons'     => false,
            'has_paid_plans' => true,
            'trial'          => array(
            'days'               => 7,
            'is_require_payment' => true,
        ),
            'menu'           => array(
            'slug'    => 'wp-persistent-login',
            'contact' => true,
            'support' => false,
            'parent'  => array(
            'slug' => 'options-general.php',
        ),
        ),
            'is_live'        => true,
        ) );
    }
    
    return $persistent_login;
}

// Init Freemius.
persistent_login();
// Signal that SDK was initiated.
do_action( 'persistent_login_loaded' );
// new user
function persistent_login_custom_connect_message(
    $message,
    $user_first_name,
    $plugin_title,
    $user_login,
    $site_link,
    $freemius_link
)
{
    return sprintf(
        __( 'Hey %1$s' ) . ',<br>' . __( 'Never miss an important update! Click \'Allow\' for security upadtes, update notifications, and non-sensitive diagnostic tracking with freemius to help make our plugin better.', 'persistent-login' ),
        $user_first_name,
        '<b>' . $plugin_title . '</b>',
        '<b>' . $user_login . '</b>',
        $site_link,
        $freemius_link
    );
}

persistent_login()->add_filter(
    'connect_message',
    'persistent_login_custom_connect_message',
    10,
    6
);