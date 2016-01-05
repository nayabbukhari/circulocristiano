<?php
/*
Plugin Name: Cornerstone Modal
Plugin URI:  http://onlinemastery.co.uk/
Description: A modal popup box for Cornerstone
Version:     0.2
Author:      OnlineMastery
Author URI:  http://onlinemastery.co.uk/
Author Email: info@onlinemastery.co.uk
Text Domain: __x__
*/


// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;



/*
 * => Enqueue Scripts
 * ---------------------------------------------------------------------------*/

function cornerstone_modal_scripts() {
	wp_enqueue_script( 'remodal', plugins_url('/assets/js/remodal.min.js', __FILE__ ), array(), null, true );
	wp_enqueue_style( 'remodal', plugins_url('/assets/css/remodal.css', __FILE__ ), array(), '1.0' );
	wp_enqueue_style( 'remodal-theme', plugins_url('/assets/css/remodal-default-theme.css', __FILE__ ), array('remodal'), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'cornerstone_modal_scripts', 100 );


// shortcodes
require_once('includes/cornerstone-modal-shortcodes.php');


/*
 * => ADD ELEMENTS TO CORNERSTONE
 * ---------------------------------------------------------------------------*/

add_action( 'cornerstone_load_elements', 'bw_cornerstone_horiz_scroll' );
function bw_cornerstone_horiz_scroll() {
	require_once( 'includes/cornerstone-modal-element.php' );
  cornerstone_add_element( 'Cornerstone_Modal' );
}