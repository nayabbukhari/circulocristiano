<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Plugin styles.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Output Login Styles
//   02. Enqueue Admin Styles
// =============================================================================

// Output Login Styles
// =============================================================================

function x_white_label_output_login_styles() {

  require( X_WHITE_LABEL_PATH . '/functions/options.php' );

  if ( isset( $x_white_label_enable ) && $x_white_label_enable == 1 ) {
    if ( $x_white_label_login_image != '' ) {

      $image  = getimagesize( $x_white_label_login_image );
      $width  = $image[0] . 'px';
      $height = $image[1] . 'px';
      $size   = $width . ' ' . $height;

      ?>

      <style id="x-white-label-login-css" type="text/css">

        body.login div#login h1 a {
          width: <?php echo $width; ?>;
          height: <?php echo $height; ?>;
          background-image: url(<?php echo $x_white_label_login_image; ?>);
          -webkit-background-size: <?php echo $size; ?>;
                  background-size: <?php echo $size; ?>;
        }

      </style>

    <?php }
  }

}

add_action( 'login_enqueue_scripts', 'x_white_label_output_login_styles' );



// Enqueue Admin Styles
// =============================================================================

function x_white_label_enqueue_admin_styles( $hook ) {

  if ( $hook == 'addons_page_x-extensions-white-label' ) {

    wp_enqueue_style( 'x-white-label-admin-css', X_WHITE_LABEL_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'x_white_label_enqueue_admin_styles' );