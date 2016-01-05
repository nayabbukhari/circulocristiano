<?php

// =============================================================================
// WOOCOMMERCE/CHECKOUT/THANKYOU.PHP
// -----------------------------------------------------------------------------
// @version 2.2.0
// =============================================================================

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( $order ) : ?>

  <?php if ( $order->has_status( 'failed' ) ) : ?>

    <p><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', '__x__' ); ?></p>

    <p><?php
      if ( is_user_logged_in() )
        _e( 'Please attempt your purchase again or go to your account page.', '__x__' );
      else
        _e( 'Please attempt your purchase again.', '__x__' );
    ?></p>

    <p>
      <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', '__x__' ) ?></a>
      <?php if ( is_user_logged_in() ) : ?>
      <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="button pay"><?php _e( 'My Account', '__x__' ); ?></a>
      <?php endif; ?>
    </p>

  <?php else : ?>

    <p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', '__x__' ), $order ); ?></p>

    <ul class="order_details x-alert x-alert-info x-alert-block">
      <li class="order">
        <?php _e( 'Order:', '__x__' ); ?>
        <strong><?php echo $order->get_order_number(); ?></strong>
      </li>
      <li class="date">
        <?php _e( 'Date:', '__x__' ); ?>
        <strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
      </li>
      <li class="total">
        <?php _e( 'Total:', '__x__' ); ?>
        <strong><?php echo $order->get_formatted_order_total(); ?></strong>
      </li>
      <?php if ( $order->payment_method_title ) : ?>
      <li class="method">
        <?php _e( 'Payment method:', '__x__' ); ?>
        <strong><?php echo $order->payment_method_title; ?></strong>
      </li>
      <?php endif; ?>
    </ul>
    <div class="clear"></div>

  <?php endif; ?>

  <?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
  <?php do_action( 'woocommerce_thankyou', $order->id ); ?>

<?php else : ?>

  <p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', '__x__' ), null ); ?></p>

<?php endif; ?>