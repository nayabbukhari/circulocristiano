<?php
/*
Plugin Name: Ajax BootModal Login
Plugin URI: http://wordpress.org/plugins/ajax-bootmodal-login
Description: Ajax BootModal Login is a WordPress plugin that is powered by bootstrap and ajax for better login, registration or lost password and display the user profile with beautiful shape. It is an easy to use WordPress plugin and can be configured quickly.
Version: 1.4.3
Author: Ali Mirzaei
Author URI: http://alimir.ir
License: GPLv2 or later
*/
session_start();
ob_start();

load_plugin_textdomain( 'alimir', false, dirname( plugin_basename( __FILE__ ) ) .'/lang/' );

function alimir_bootmodal_options() {
	add_option('option_bs3patch', '0', '', 'yes');
	add_option('modal_theme', '1', '', 'yes');
	add_option('option_checkbox', '0', '', 'yes');
	add_option('option_usermodal', '0', '', 'yes');
	add_option('can_register_option', '0', '', 'yes');
	add_option('lable_setting', '0', '', 'yes');
	add_option('button_text', __('Login','alimir'), '', 'yes');
	add_option('button_text2', __('Profile','alimir'), '', 'yes');
	add_option('enable_login_captcha', '1', '', 'yes');
	add_option('enable_register_captcha', '1', '', 'yes');
	add_option('enable_lostpass_captcha', '1', '', 'yes');
	add_option('captcha_font', '1', '', 'yes');
	add_option('default_buttons', '0', '', 'yes');
	add_option('default_sizes', '0', '', 'yes');
	add_option('remove_btn_block', '0', '', 'yes');
}
register_activation_hook(__FILE__, 'alimir_bootmodal_options');

function alimir_bootmodal_unset_options() {
	delete_option('option_bs3patch');
	delete_option('modal_theme');
	delete_option('option_checkbox');
	delete_option('option_usermodal');
	delete_option('can_register_option');
	delete_option('lable_setting');
	delete_option('button_text');
	delete_option('button_text2');
	delete_option('enable_login_captcha');
	delete_option('enable_register_captcha');
	delete_option('enable_lostpass_captcha');
	delete_option('captcha_font');
	delete_option('default_buttons');
	delete_option('default_sizes');
	delete_option('remove_btn_block');
}
register_uninstall_hook(__FILE__, 'alimir_bootmodal_unset_options');

//admin setting
if(is_admin()):
include( plugin_dir_path( __FILE__ ) . 'inc/settings.php');
endif;

//functions
include( plugin_dir_path( __FILE__ ) . 'inc/functions.php');

//scripts
include( plugin_dir_path( __FILE__ ) . 'inc/scripts.php');

//shortcode
include( plugin_dir_path( __FILE__ ) . 'inc/shortcode.php');

//ajax authenticate
include( plugin_dir_path( __FILE__ ) . 'inc/authenticate.php');

//Modal box
include( plugin_dir_path( __FILE__ ) . 'inc/modal-box.php');

//widget
include( plugin_dir_path( __FILE__ ) . 'inc/widget.php');

?>