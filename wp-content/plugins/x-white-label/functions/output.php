<?php

// =============================================================================
// FUNCTIONS/OUTPUT.PHP
// -----------------------------------------------------------------------------
// Plugin output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Addons Home Heading
//   02. Addons Home Subheading
//   03. Addons Home Content
//   04. Output
// =============================================================================

// Addons Home Heading
// =============================================================================

function x_addons_home_heading() {

  require( X_WHITE_LABEL_PATH . '/functions/options.php' );

  return $x_white_label_addons_home_heading;

}



// Addons Home Subheading
// =============================================================================

function x_addons_home_subheading() {

  require( X_WHITE_LABEL_PATH . '/functions/options.php' );

  return $x_white_label_addons_home_subheading;

}



// Addons Home Content
// =============================================================================

function x_addons_home_content() {

  require( X_WHITE_LABEL_PATH . '/functions/options.php' );

  return $x_white_label_addons_home_content;

}



// Output
// =============================================================================

require( X_WHITE_LABEL_PATH . '/functions/options.php' );

if ( isset( $x_white_label_enable ) && $x_white_label_enable == 1 ) {

  add_filter( 'login_headerurl', 'get_home_url' );
  add_filter( 'login_headertitle', 'get_bloginfo' );

  if ( $x_white_label_addons_home_heading != '' ) {
    add_filter( 'x_addons_home_heading', 'x_addons_home_heading' );
  }

  if ( $x_white_label_addons_home_subheading != '' ) {
    add_filter( 'x_addons_home_subheading', 'x_addons_home_subheading' );
  }

  if ( $x_white_label_addons_home_content != '' ) {
    add_filter( 'x_addons_home_content', 'x_addons_home_content' );
  }

}