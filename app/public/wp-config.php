<?php
# Database Configuration
// define( 'DB_NAME', 'wp_mrcornelius' );
// define( 'DB_USER', 'mrcornelius' );
// define( 'DB_PASSWORD', 'rKbost3m4uVXJS9Y' );
// define( 'DB_HOST', '127.0.0.1' );
// define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'mrc_';

define( 'DB_NAME', 'local' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );
define('DB_HOST', 'localhost');
define('WP_DEBUG', true);
define('WP_AUTO_UPDATE_CORE', false );

define('WP_SITEURL', 'http://local.mr.com/');
define('WP_HOME', 'http://local.mr.com/');

# Security Salts, Keys, Etc
define('AUTH_KEY',         '~oIS3RkAjNXO{Ytt(Z#[L+|xPJc u!|Gus^g:EFoG%^a4j_G[F|~+_+c^VtA~S8f');
define('SECURE_AUTH_KEY',  '+=a4TfEbnCZM_mis.{JxmVjZ).a 4eoF2`^JkL<tH_&gpa^o|2*OG3.!XN/Os0tz');
define('LOGGED_IN_KEY',    ' 7k4+%)+[L{ @6fx<!LlyFv@!eV-)hPnBU0_)ju/+dr*.|OBuD1(+>GTxfisD&O[');
define('NONCE_KEY',        '}e0Y]-`D(<^`iB9CeNkbCm1X_7KI|YE5Yb*3:>8RG,-S &&%aqE+CTQcfYI9^Lc*');
define('AUTH_SALT',        'w 6@Nd)zrGJJXCJH,E/V"t{lf=jzdw[bF1o?HgGBm(8|_*yMC~K%r9IMGqKk[lKB');
define('SECURE_AUTH_SALT', '0?|v`v^6v$qj%A+n6~kzW<|5I+})o.L_0VTyc2 XziB%ZciZRq,uLB/4HtbeX!AS');
define('LOGGED_IN_SALT',   'pvwjjj^Ji:0Hu[[p[a{*GMLN~*{"^vtE:>|~iIw617UMoioBhkiy}qw`={lRgE)e');
define('NONCE_SALT',       'Wax4TRzVUAB83:vYAd`5,q*ye5BP!-")[Y:/R5qtI`)V;tBb<e8-::$;hEZ_;6,u');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'mrcornelius' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', '2aebe0b8f0a672377c2e7eb9b2a5f808c4ef3193' );

define( 'WPE_FOOTER_HTML', "" );

define( 'WPE_CLUSTER_ID', '100333' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', true );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'mrcornelius.com', 1 => 'www.mrcornelius.com', 2 => 'mrcornelius.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-100333', );

$wpe_special_ips=array ( 0 => '104.196.2.43', );

$wpe_ec_servers=array ( );

$wpe_largefs=array ( );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( 'default' =>  array ( 0 => 'unix:///tmp/memcached.sock', ), );
define('WPLANG','');

# WP Engine ID


# WP Engine Settings






# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');

$_wpe_preamble_path = null; if(false){}
