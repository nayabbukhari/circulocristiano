<?php
/**
 * Plugin Name:       X - Starfield by Perceptive.io
 * Description:       Creates a starfield effect on the background.
 * Version:           1.0.2
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
add_action( 'cornerstone_load_elements', 'cornerstone_perceptive_starfield' );
function cornerstone_perceptive_starfield() {
  require('perceptive_cs_starfield.php');
  cornerstone_add_element( 'Cornerstone_Starfield' );
}

/*
  
Add the shortcode handler  
    
*/
function perceptive_x_starfield_shortcode() {
    
    wp_enqueue_style( 'x_starfield_element', plugins_url('/css/perceptive_x_starfield.css', __FILE__ ) );
    wp_enqueue_script( 'x_starfield_element', plugins_url('/js/perceptive_x_starfield.js', __FILE__ ), array('jquery'), null, true );
     
    $html = '<div style="display:none" id="x_starfield_marker"></div><div id="stars"></div><div id="stars2"></div><div id="stars3"></div> ';
    
    return $html;
        
}
add_shortcode( 'x-starfield-perceptive', 'perceptive_x_starfield_shortcode' );