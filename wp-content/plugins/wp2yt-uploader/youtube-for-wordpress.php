<?php
/*
#_________________________________________________ PLUGIN
Plugin Name: YouTube for WordPress
Plugin URI: http://www.yt4wp.com
Description: Upload videos, browse your account, and insert them in to posts without having to leave your blog! Now built with YouTube API v3 and lots of love
Version: 2.0.4.5
Author: YouTubeforWordPress, Evan Herman
Author URI: http://www.yt4wp.com
License: GPL2

#_________________________________________________ LICENSE
Copyright 2012-14 YouTube for WordPress (email : Info@yt4wp.com)

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

#_________________________________________________ CONSTANTS

/** Configuration **/
if( !defined( 'YT4WP_DEBUG') ) define( 'YT4WP_DEBUG' , false );
if( !defined( 'YT4WP_VERSION_CURRENT') ) define( 'YT4WP_VERSION_CURRENT',	'2.0.4.5' );
if( !defined( 'YT4WP_REQ_PHP') ) define( 'YT4WP_REQ_PHP', '5.3' );
if( !defined( 'YT4WP_AUTHOR' ) ) define( 'YT4WP_AUTHOR', 'Evan Herman' );
if( !defined( 'YT4WP_SITE' ) ) define(' YT4WP_SITE', site_url() . '/' );
if( !defined( 'YT4WP_PREFIX' ) ) define( 'YT4WP_PREFIX' , 'YT4WP_' );
if( !defined( 'YT4WP_PATH' ) ) define( 'YT4WP_PATH' , plugin_dir_path( __FILE__ ) );
if( !defined( 'YT4WP_URL' ) ) define( 'YT4WP_URL' , plugins_url('wp2yt-uploader/') );
if( !defined( 'YT4WP_URL_WP' ) ) define( 'YT4WP_URL_WP' , get_bloginfo('url') );
if( !defined( 'YT4WP_URL_WP_ADM' ) ) define('YT4WP_URL_WP_ADM' , YT4WP_URL_WP . '/wp-admin/' );
/** Database Tables **/
if( !defined( 'YT4WP_OPTION' ) ) define( 'YT4WP_OPTION' , YT4WP_PREFIX . 'storage' );
// Conditional check for SSL enabled site
if( !defined( 'YT4WP_URL_WP_AJAX' ) ) {
   if ( is_ssl() ) {
		define( 'YT4WP_URL_WP_AJAX' , admin_url( 'admin-ajax.php' , 'https' ) );
	} else {
		define( 'YT4WP_URL_WP_AJAX' , admin_url( 'admin-ajax.php' , 'http' ) );
	}
}

if( !defined( 'YT4WP_URL_CURRENT' ) ) define( 'YT4WP_URL_CURRENT' , $_SERVER['REQUEST_URI'] );

/** Localization **/
// include translated files
function yt4wp_plugin_init() {
	   load_plugin_textdomain('youtube-for-wordpress-translation', false, dirname(plugin_basename(__FILE__)) . '/languages'); 
}
add_action('init', 'yt4wp_plugin_init');

/** Initial Configuration **/
if(YT4WP_DEBUG) error_reporting(E_ALL ^ E_NOTICE);

/** Include Required Plugin Files **/
require_once YT4WP_PATH.'classes/class.youtube-for-wordpress.php';
require_once YT4WP_PATH.'lib/lib.ajax.php';

/** Require YouTube API Files */
require_once YT4WP_PATH.'inc/Google/Client.php';
/* Include our YouTube Erorr Base File */
require_once YT4WP_PATH.'inc/error_templates/base.php';

/** Initialize the plugin's base class **/
$YT4WPBase	= new YT4WPBase();

/** Activation Hooks **/
register_activation_hook(__FILE__,		array(&$YT4WPBase, 'activate'));
register_deactivation_hook(__FILE__,	array(&$YT4WPBase, 'deactivate'));
register_uninstall_hook(__FILE__,		array('YT4WPBase', 'uninstall'));