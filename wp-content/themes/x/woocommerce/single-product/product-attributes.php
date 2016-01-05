<?php

// =============================================================================
// WOOCOMMERCE/SINGLE-PRODUCT/PRODUCT-ATTRIBUTES.PHP
// -----------------------------------------------------------------------------
// @version 2.1.3
// =============================================================================

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$has_row    = false;
$alt        = 1;
$attributes = $product->get_attributes();

ob_start();

?>

<table class="shop_attributes">

  <thead>
    <tr class="shop_attributes_thead">
      <th><?php _e( 'Attribute', '__x__' ) ?></th>
      <th><?php _e( 'Information', '__x__' ) ?></th>
    </tr>
  </thead>

  <?php if ( $product->enable_dimensions_display() ) : ?>

    <?php if ( $product->has_weight() ) : $has_row = true; ?>
      <tr class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
        <td><?php _e( 'Weight', '__x__' ) ?></td>
        <td class="product_weight"><?php echo $product->get_weight() . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ); ?></td>
      </tr>
    <?php endif; ?>

    <?php if ( $product->has_dimensions() ) : $has_row = true; ?>
      <tr class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
        <td><?php _e( 'Dimensions', '__x__' ) ?></td>
        <td class="product_dimensions"><?php echo $product->get_dimensions(); ?></td>
      </tr>
    <?php endif; ?>

  <?php endif; ?>

  <?php foreach ( $attributes as $attribute ) :
    if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
      continue;
    } else {
      $has_row = true;
    }
    ?>
    <tr class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
      <td><?php echo wc_attribute_label( $attribute['name'] ); ?></td>
      <td><?php
        if ( $attribute['is_taxonomy'] ) {

          $values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
          echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

        } else {

          // Convert pipes to commas and display values
          $values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
          echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

        }
      ?></td>
    </tr>
  <?php endforeach; ?>
  
</table>

<?php

if ( $has_row ) {
  echo ob_get_clean();
} else {
  ob_end_clean();
}