<?php
//global $sl_dir, $sl_base, $sl_path, $sl_uploads_path, $sl_uploads_base, $text_domain;
$sl_siteurl=get_option('siteurl'); $sl_blog_charset=get_option('blog_charset'); $sl_admin_email=get_option('admin_email');
$sl_site_name=get_option('blogname');
$sl_dir=dirname(plugin_basename(__FILE__)); //plugin absolute server directory name
$sl_pub_dir=$sl_dir."/sl-pub";
$sl_inc_dir=$sl_dir."/sl-inc";
$sl_admin_dir=$sl_dir."/sl-admin";
$sl_base=plugins_url('', __FILE__); //URL to plugin directory
$sl_path=substr(plugin_dir_path(__FILE__), 0, -1); //absolute server path to plugin directory; substr() to remove trailing slash
$sl_uploads=wp_upload_dir();
$sl_uploads_base=$sl_uploads['baseurl']."/sl-uploads"; //URL to store locator uploads directory
$sl_upload_base=$sl_uploads_base; //added 's' to end for consistency. maintains for older versions.
$sl_uploads_path=$sl_uploads['basedir']."/sl-uploads"; //absolute server path to store locator uploads directory
$sl_upload_path=$sl_uploads_path; //added 's' to end for consistency. maintains for older versions.
$top_nav_base="/".substr($_SERVER["PHP_SELF"],1)."?page=";
$admin_nav_base=$sl_siteurl."/wp-admin/admin.php?page="; //die($admin_nav_base); 
$text_domain="store-locator";
$view_link="| <a href='".$admin_nav_base.$sl_admin_dir."/pages/locations.php'>".__("Manage Locations", "store-locator")."</a> <script>setTimeout(function(){jQuery('.sl_admin_success').fadeOut('slow');}, 6000);</script>";
$web_domain=str_replace("www.","",$_SERVER['HTTP_HOST']);

define('SL_SITEURL', $sl_siteurl); define('SL_BLOG_CHARSET', $sl_blog_charset); define('SL_ADMIN_EMAIL', $sl_admin_email); define('SL_SITE_NAME', $sl_site_name);
define('SL_DIR', $sl_dir);
	define('SL_PUB_DIR', $sl_dir);
		define('SL_CSS_DIR', SL_PUB_DIR."/css");
		define('SL_ICONS_DIR', SL_PUB_DIR."/icons");
		define('SL_JS_DIR', SL_PUB_DIR."/js");
		//All '_ORIGINAL' dir/base/paths are no longer or rarely used. Copied to sl-uploads.
		define('SL_IMAGES_DIR_ORIGINAL', SL_PUB_DIR."/images");
	define('SL_INC_DIR', $sl_inc_dir);
		define('SL_ACTIONS_DIR', SL_INC_DIR."/actions");
		define('SL_INCLUDES_DIR', SL_INC_DIR."/includes");
	define('SL_ADMIN_DIR', $sl_admin_dir);
		define('SL_INFO_DIR', SL_ADMIN_DIR."/info");
		define('SL_PAGES_DIR', SL_ADMIN_DIR."/pages");
		//All '_ORIGINAL' dir/base/paths are no longer or rarely used. Copied to sl-uploads.
		define('SL_ADDONS_DIR_ORIGINAL', SL_ADMIN_DIR."/addons");
		define('SL_LANGUAGES_DIR_ORIGINAL', SL_ADMIN_DIR."/languages");
		define('SL_THEMES_DIR_ORIGINAL', SL_ADMIN_DIR."/themes");
define('SL_BASE', $sl_base);
	define('SL_PUB_BASE', SL_BASE);
		define('SL_CSS_BASE', SL_PUB_BASE."/css");
		define('SL_ICONS_BASE', SL_PUB_BASE."/icons");
		define('SL_JS_BASE', SL_PUB_BASE."/js");
		//All '_ORIGINAL' dir/base/paths are no longer or rarely used. Copied to sl-uploads.
		define('SL_IMAGES_BASE_ORIGINAL', SL_PUB_BASE."/images");
	define('SL_INC_BASE', SL_BASE."/sl-inc");
		define('SL_ACTIONS_BASE', SL_INC_BASE."/actions");
		define('SL_INCLUDES_BASE', SL_INC_BASE."/includes");
	define('SL_ADMIN_BASE', SL_BASE."/sl-admin");
		define('SL_INFO_BASE', SL_ADMIN_BASE."/info");
		define('SL_PAGES_BASE', SL_ADMIN_BASE."/pages");
		//All '_ORIGINAL' dir/base/paths are no longer or rarely used. Copied to sl-uploads.
		define('SL_ADDONS_BASE_ORIGINAL', SL_ADMIN_BASE."/addons");
		define('SL_LANGUAGES_BASE_ORIGINAL', SL_ADMIN_BASE."/languages");
		define('SL_THEMES_BASE_ORIGINAL', SL_ADMIN_BASE."/themes");
define('SL_PATH', $sl_path);
	define('SL_PUB_PATH', SL_PATH);
		define('SL_CSS_PATH', SL_PUB_PATH."/css");
		define('SL_ICONS_PATH', SL_PUB_PATH."/icons");
		define('SL_JS_PATH', SL_PUB_PATH."/js");
		//All '_ORIGINAL' dir/base/paths are no longer or rarely used. Copied to sl-uploads.
		define('SL_IMAGES_PATH_ORIGINAL', SL_PUB_PATH."/images");
	define('SL_INC_PATH', SL_PATH."/sl-inc");
		define('SL_ACTIONS_PATH', SL_INC_PATH."/actions");
		define('SL_INCLUDES_PATH', SL_INC_PATH."/includes");
	define('SL_ADMIN_PATH', SL_PATH."/sl-admin");
		define('SL_INFO_PATH', SL_ADMIN_PATH."/info");
		define('SL_PAGES_PATH', SL_ADMIN_PATH."/pages");
		//All '_ORIGINAL' dir/base/paths are no longer or rarely used. Copied to sl-uploads.
		define('SL_ADDONS_PATH_ORIGINAL', SL_ADMIN_PATH."/addons");
		define('SL_LANGUAGES_PATH_ORIGINAL', SL_ADMIN_PATH."/languages");
		define('SL_THEMES_PATH_ORIGINAL', SL_ADMIN_PATH."/themes");
define('SL_UPLOADS_BASE', $sl_uploads_base);
define('SL_UPLOADS_PATH', $sl_uploads_path);
define('SL_TOP_NAV_BASE', $top_nav_base);
define('SL_ADMIN_NAV_BASE', $admin_nav_base);
define('SL_TEXT_DOMAIN', $text_domain);
define('SL_VIEW_LINK', $view_link);
define('SL_WEB_DOMAIN', $web_domain);

define('SL_ADDONS_BASE', SL_UPLOADS_BASE."/addons");
define('SL_CACHE_BASE', SL_UPLOADS_BASE."/cache");
define('SL_CUSTOM_CSS_BASE', SL_UPLOADS_BASE."/custom-css");
define('SL_CUSTOM_ICONS_BASE', SL_UPLOADS_BASE."/custom-icons");
define('SL_IMAGES_BASE', SL_UPLOADS_BASE."/images");
define('SL_LANGUAGES_BASE', SL_UPLOADS_BASE."/languages");
define('SL_THEMES_BASE', SL_UPLOADS_BASE."/themes");

define('SL_ADDONS_PATH', SL_UPLOADS_PATH."/addons");
define('SL_CACHE_PATH', SL_UPLOADS_PATH."/cache");
define('SL_CUSTOM_CSS_PATH', SL_UPLOADS_PATH."/custom-css");
define('SL_CUSTOM_ICONS_PATH', SL_UPLOADS_PATH."/custom-icons");
define('SL_IMAGES_PATH', SL_UPLOADS_PATH."/images");
define('SL_LANGUAGES_PATH', SL_UPLOADS_PATH."/languages");
define('SL_THEMES_PATH', SL_UPLOADS_PATH."/themes");

define('SL_INFORMATION_PAGE', SL_TOP_NAV_BASE.SL_PAGES_DIR."/information.php");
define('SL_MANAGE_LOCATIONS_PAGE', SL_TOP_NAV_BASE.SL_PAGES_DIR."/locations.php");
	define('SL_ADD_LOCATIONS_PAGE', SL_MANAGE_LOCATIONS_PAGE."&pg=add-locations");
define('SL_MAP_DESIGNER_PAGE', SL_TOP_NAV_BASE.SL_PAGES_DIR."/mapdesigner.php");
define('SL_MAPDESIGNER_PAGE', SL_MAP_DESIGNER_PAGE); //either/or works - for consistency

define('SL_PARENT_PAGE', SL_INFORMATION_PAGE); //Initial nav page
define('SL_PARENT_URL', preg_replace("@".preg_quote(SL_TOP_NAV_BASE)."@", "",SL_PARENT_PAGE)); //Initial nav page (w/o top-nav base)

$sl_aps=glob(SL_ADDONS_PATH.'/*addons-platform*', GLOB_NOSORT); //Addons Platform
if (!empty($sl_aps)){
	$sl_addons_platform_dir = basename(current($sl_aps));
	foreach ($sl_aps as $sl_ap_path) {
		if (file_exists($sl_ap_path.'/'.basename($sl_ap_path).'.php')) {
			$sl_addons_platform_dir = basename($sl_ap_path);
			break;
		} 
	}
	define('SL_ADDONS_PLATFORM_DIR', $sl_addons_platform_dir);
	define('SL_ADDONS_PLATFORM_PATH', SL_ADDONS_PATH.'/'.SL_ADDONS_PLATFORM_DIR );
	define('SL_ADDONS_PLATFORM_BASE', SL_ADDONS_BASE.'/'.SL_ADDONS_PLATFORM_DIR );
	define('SL_ADDONS_PLATFORM_FILE', SL_ADDONS_PLATFORM_PATH.'/'.SL_ADDONS_PLATFORM_DIR.'.php');
	//die("Exec. time: <br>dir: ".SL_ADDONS_PLATFORM_DIR."<br>path: ".SL_ADDONS_PLATFORM_PATH ."<br>base: ". SL_ADDONS_PLATFORM_BASE."<br>file: ".SL_ADDONS_PLATFORM_FILE);
}

?>