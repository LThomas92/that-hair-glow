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
define( 'DB_NAME', 'thathairglow' );

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
define( 'AUTH_KEY',         '}o9g3:pP!}Y?=9ICKj^Y<|~:cUX~4mumwH3{Wa?,cD1OY#q!m9VaRz7MW(GopPAo' );
define( 'SECURE_AUTH_KEY',  '_>w}GdJ)yOZ/icX# u+i;5LX.^{pQ]mlhZJU<#dM:3E2~P=J9(U R(5iTIU/y}ye' );
define( 'LOGGED_IN_KEY',    'xd3hNXJ>pDhfbGt@&opij?{pS+qa|d@+(W(=KQ}8|97$yg(T<^lG::AE]RM8F: i' );
define( 'NONCE_KEY',        '1CvEp74h:!_/H,=4O9+EMx6Zl@y5fxY*l+pHNjzeo^E3T.=SLn!WXe0el(Jj))en' );
define( 'AUTH_SALT',        '{jF/}*6qXV+S+o(=r1ul//1N5Y] 3A0`.fZW/#v_wyz$^<fzIXFw?X(cS8W9QhGt' );
define( 'SECURE_AUTH_SALT', ';f&z$X[E[cOtJwW<+rG9:%R,/Tz[-xCDE*Q+z9]sShyqX:%rbL>NrID]Pl%A02p_' );
define( 'LOGGED_IN_SALT',   'aBNVUiGjAfep+W;k9s`y2w?TSf{<YpNCAxFsgKQMH~4Bm97KC_W2:}#OBb=j64M6' );
define( 'NONCE_SALT',       'O2YNX?5BBmVCNvYMszf;JA]CJpSJq_4kW_b5LDd2c2%s8:QAU,KsAt3dowyhxt1p' );

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
