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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '4fUGW18/jPZSWwl9vOEMZeVgoBLyxTByCjoyHlTKzRorOgAdA5W7pKd5PqENE3H99BsYs6BdX6390108NNo9/g==');
define('SECURE_AUTH_KEY',  'Y1KKleiEVFidnuBNpix0vXYjLGClzrZ6hAtnSenIGjE3YgqWJeY7n+uwPASHjdGi3+HWiaOF1ghhUxugc/t3lw==');
define('LOGGED_IN_KEY',    'tLoCp0PdGGcvMWLFSjI3qZgrXAEIcjkcwxx+V3vudHayvCg0vY/aUgB/qVoZtmuGMznyD8WMuVWUJAxGU5c+xg==');
define('NONCE_KEY',        'VWOd63uaDIA0oESRDfK/D2/NyzSQK699kfWSEIpGR4QO/onJyHtH6loodgHRU2hR82btb16Uz8FtHm5wAMN2Gw==');
define('AUTH_SALT',        'ybvvS6r6ZBjpbaMv7ddG1Y2XW5J+T/2xmEMmJxtDx5f32qbobRmmsYohuTikmyXmo7UC+h3OepnEym9ToFmkjA==');
define('SECURE_AUTH_SALT', '8dDn1BlWSlbVbhLCH0M74Ssfpe6NQhCSGsKHyW8KtkHK5W91nh7NPrGL/sbrM+oHngN1t7njJXvMvoZW+ogJNg==');
define('LOGGED_IN_SALT',   'g7GiwhArQqRW0voPXjjXyX8EHL8JOsPUwORhT1Jj+trBsRWY4g3N7XSDIPSTbSGzsds4KaqyBggqz/xo9SbTbA==');
define('NONCE_SALT',       'r/J9g4j8txPqiwdZ/M/CdOJUbJzmH3mQjXWt0jopq0RNFmvmi9XQfVzUkWxZf8bg0HD8xTnqNbpA16iLMMmJWg==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';





/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}

/* Inserted by Local by Flywheel. Fixes $is_nginx global for rewrites. */
if (strpos($_SERVER['SERVER_SOFTWARE'], 'Flywheel/') !== false) {
	$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.10.1';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
