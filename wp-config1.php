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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'vannavy' );

/** MySQL database username */
define( 'DB_USER', 'vannavy' );

/** MySQL database password */
define( 'DB_PASSWORD', 'vannavy123@' );

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
define( 'AUTH_KEY',         'ub/MR(4aN031k7{KRI+3q|r(y81zS=/*9R1H:TT*90aWfn0?YcVS8@I3sB83H1jG' );
define( 'SECURE_AUTH_KEY',  '?i}S+h?GJq#o}VZ&6Tms6t^rZ,;e, W/yP<xBDJ}1/FqT7^sBCxeWBOG;|3&r8v5' );
define( 'LOGGED_IN_KEY',    '[]1h)?(Kuui*Y*5.1aA(8qq<`T-.;!P@:5i!5/1Wzqg,cwzAA&W*84iLv2l!Aw:J' );
define( 'NONCE_KEY',        'C M6~S6zk#rVr>/d59{>&GW~8YwgI0kd{}vSrF?9e()7S@>XWk<XvDuVr8Umqn~/' );
define( 'AUTH_SALT',        ',ibB8Nb//&lHK^o0fg3Vig1{B81kUtI_f(+-k)Q<yG;ABQ8:z2E7WMce{PsLo;TZ' );
define( 'SECURE_AUTH_SALT', '<p0:]8:;2qDBdZ,[$-<kz$,iKt4e#lR{M-tAs$*X~`?wt[fw7/*1]Os#`Q Ca]A`' );
define( 'LOGGED_IN_SALT',   '<JMknTb?7~YWde>)vC`]hnz0@yG{7b[xv2iS*v+{wtdO;?MJt[G0|mt&H8b)I>ig' );
define( 'NONCE_SALT',       '6lpv(tP@GIefyx<m?_Xt2~JZ+p^Wx<o$ru.=0dc|!)o_*9E|#+5-;j<Pf;kG+XH]' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
