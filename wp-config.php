<?php





/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u442300711_dGLQ8' );

/** Database username */
define( 'DB_USER', 'u442300711_BKRPO' );

/** Database password */
define( 'DB_PASSWORD', 'jt7U1DKLFi' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'cj=J/$8j9#j%CKJv8>UgTuk|kjH<3,DXkNle{)<&lx| Z^Axe>;[SM/A0@5ghwBs' );
define( 'SECURE_AUTH_KEY',   'l<Z[!8Vj:![0*)DKlNp:fI6[!+9u+#v3m0[f/>)aDg,lGVb>}P+*s|z6G5|p[Nwr' );
define( 'LOGGED_IN_KEY',     '($Q<@74wK4fN`Um]?Pse=3|6No{ ?qVGN0CaE[M_oiHh1}e_#VtuzTp,Sg*Ze.~S' );
define( 'NONCE_KEY',         'L+FrPpkv#r`{+4lH?<k;t}7TEucS-H]hyinj|{e0Y4VJ~B=RMi|iMUTH:v2a(g]v' );
define( 'AUTH_SALT',         '~0sOX@;%YKJj1Vd+h{7iv:p+6PSoy#@sLx:Nxe5%u7AX1V5vOF~`J2C5x_*gZ1SW' );
define( 'SECURE_AUTH_SALT',  'DF~/^53<JFz4nYS~8.$eoX&sIRMj*O=;Wpl8|aiyZ-li&P[,L9[[$m!`VJ5>X>Nx' );
define( 'LOGGED_IN_SALT',    '67/(: OEjdD4>>UKC+`pX-#)CM(R/#<a<kc0+sxC!d/fVl@`r4%S$R]Xb]3e:,Tv' );
define( 'NONCE_SALT',        '8DA/3-@p!?U-ReoRmq,:*_t1wbOus{LCqa9NfI&M|liu(WAQem)#@Rz[ C(`ZGWy' );
define( 'WP_CACHE_KEY_SALT', '.@WG]S/M6Tk -}WVz,^Lrc[%}swK<kgrD^^|s.2%TC6Hz@IX+bA]D#M:@{IW(8?b' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
