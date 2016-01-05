// =============================================================================
// JS/SRC/SITE/INC/X-BODY-WOOCOMMERCE.JS
// -----------------------------------------------------------------------------
// Includes all additional WooCommerce functionality tied into the theme.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. WooCommerce Functionality
// =============================================================================

// WooCommerce Functionality
// =============================================================================

jQuery(document).ready(function($) {

  var $notification = $('.x-cart-notification');

  if ( $notification.length > 0 ) {

    $('.add_to_cart_button.product_type_simple').on('click', function(e) {
      $notification.addClass('bring-forward appear loading');
    });

    $('body').on('added_to_cart', function(e, fragments, cart_hash) {
      $notification.removeClass('loading').addClass('added');
      setTimeout(function() {
        $notification.removeClass('appear');
        setTimeout(function() {
          $notification.removeClass('added bring-forward');
        }, 650);
      }, 1000);
    });

  }

});