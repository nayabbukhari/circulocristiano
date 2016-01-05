<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/PAGE-PRODUCT-VALIDATION.PHP
// -----------------------------------------------------------------------------
// Addons product validation page output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Page Output
//   02. Validation
// =============================================================================

// Page Output
// =============================================================================

function x_addons_page_product_validation() { ?>

  <div class="wrap x-addons-product-validation">

    <header class="x-addons-header">
      <h2>Product Validation</h2>
      <?php if ( x_is_validated() ) : ?>
        <p>Yay! Automatic updates are up and running. You're also welcome to <a href="<?php echo x_addons_get_link_extensions(); ?>">browse and install Extensions</a>.</p>
      <?php else : ?>
        <p>Enter your API key to validate your purchase, receive automatic updates, and unlock Extension.</p>
      <?php endif; ?>
    </header>

    <div class="x-addons-postbox product-validation">
      <?php x_addons_product_validation(); ?>
      <?php $name = x_addons_get_api_key_option_name(); ?>
      <?php $key  = get_option( $name ); ?>
      <div class="inside">
        <form method="post" enctype="multipart/form-data">
          <?php wp_nonce_field( 'x-addons-product-validation' ); ?>
          <input name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="large-text<?php echo ( x_is_validated() ) ? ' x-input-success' : ''; ?>" type="text" value="<?php echo ( ! empty( $key ) ) ? $key : ''; ?>">
          <input name="x_addons_product_validation_submitted" type="hidden" value="submitted">
          <p class="submit">
            <input type="submit" name="validate" class="button button-primary" value="<?php echo ( x_is_validated() ) ? 'Update API Key' : 'Submit API Key'; ?>">
          </p>
        </form>
      </div>
    </div>
    <p class="x-product-validation-info">Visit the <a href="<?php echo x_addons_get_link_home(); ?>">Addons Home</a> to learn how to find your API key. <span class="dashicons dashicons-admin-network"></span></p>

  </div>

<?php }



// Validation
// =============================================================================

function x_addons_product_validation() {

  if ( isset( $_POST['validate'] ) && check_admin_referer( 'x-addons-product-validation' ) ) {
    if ( strip_tags( $_POST['x_addons_product_validation_submitted'] ) == 'submitted' ) {

      //
      // If $input is set and an empty string, delete the option and provide a
      // message to confirm that the key has been removed.
      //
      // Else, check the value returned by $response['code'] and provide an
      // appropriate action and message.
      //
      // $response['code'] == 1 - Success
      // $response['code'] == 2 - General Error
      // $response['code'] == 3 - Invalid API Key
      // $response['code'] == 4 - Connection Error
      //

      $name  = x_addons_get_api_key_option_name();
      $input = strip_tags( $_POST[$name] );

      if ( isset( $input ) && $input == '' ) {
        delete_option( $name );
        echo '<div class="updated"><p>API key removed successfully!</p></div>';
      } else {
        $response = X_Update_API::validate_key( $input );
        if ( $response['code'] == 2 || $response['code'] == 3 || $response['code'] == 4 ) {
          delete_option( $name );
          echo '<div class="error"><p>' . $response['message'] . '</p></div>';
          if ( isset( $_GET['x-verbose'] ) && $_GET['x-verbose'] == 1 ) {
            x_dump( X_Update_API::get_errors(), 350, 'var_dump' );
          }
        } else {
          update_option( $name, $input );
          echo '<div class="updated"><p>' . $response['message'] . '</p></div>';
        }
      }

      delete_site_option( 'x_addon_list_cache' );

    }
  }

}