<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dflforum' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '?M?>`s)3y3Ui?WqLCURBYpEe(2YOLZ(B-Y5Hp}0}k8O+b:kUjg!NAE,e~|%T^A1/' );
define( 'SECURE_AUTH_KEY',  'FyOWxGIhH@eoX[^ 3~EJn-)[o8` B|L@hZ;u20mN 6!@w|T~7l(Pao&hA7 6Bs7_' );
define( 'LOGGED_IN_KEY',    '}jfuWiJ?IB?EfXs;i2$UQ4ksN6sfG(xBgpHNFx?)=0fh_q8}Gvu+n*TJp,I].Dsd' );
define( 'NONCE_KEY',        ';&#P=lT%41y>vi2jiqU~t@0zt?QuKCx50=5RW?DZ4+}D Af_Xuw%bg5ZW-hDRo*@' );
define( 'AUTH_SALT',        '#aHxbWoLh<G)0ej(` G_j+:x7Nc1h<;l!hb@ `v9ohu[fnNBy<VS<,gu)tE&/Kp{' );
define( 'SECURE_AUTH_SALT', 'x`l&&r.4E?Pr@Ud S+(6`Fl;cmquL6@2J!wDg/ vH2L=eZu4}u$i~$w,=lk8N6vJ' );
define( 'LOGGED_IN_SALT',   'G]B_qGj&&qCWm}apz!w5v~.o;05+:`d,7CzEd)3#[vMm;5m#^}Ou36!:Iip@,LH|' );
define( 'NONCE_SALT',       '1YlK:m[R7COur=b@z][yF3pyrkh]:/PDhIMQM`IQ5FZp)BWbnHFuoepUS[82,A(>' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
