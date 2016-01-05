<?php
/**
 * Plugin Name:       X - Splash by Perceptive.io
 * Description:       Turns the top section into a splash page. Drag 'Splash' element onto the first section and set a background image for the page and the background color for the other sections.
 * Version:           1.0.0
 * Author:            Matthew Shelley
 * Author URI:        https://perceptive.io/
 * Text Domain:       perceptive
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/*

Add Cornerstone element

*/
add_action( 'cornerstone_load_elements', 'cornerstone_perceptive_splash' );
function cornerstone_perceptive_splash() {
  require('perceptive_cs_splash.php');
  cornerstone_add_element( 'Cornerstone_Splash' );
}

/*
  
Add the shortcode handler  
    
*/
function perceptive_x_splash_shortcode() {
    
    wp_enqueue_script( 'x_splash_element', plugins_url('/js/perceptive_x_splash.min.js', __FILE__ ), array('jquery'), null, true );
    return '<div style="display:none" id="x_splash_marker"></div>';
        
}
add_shortcode( 'x-splash-perceptive', 'perceptive_x_splash_shortcode' );