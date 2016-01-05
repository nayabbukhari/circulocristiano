<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-SINGLE.PHP
// -----------------------------------------------------------------------------
// Single post output for Integrity.
// =============================================================================

 /**
 * Desc: Sermon post template
 * author: ajay3085006
 * dated: 25 Dec 2015
 * updated: 25 Dec 2015
 */

?>

<?php get_header(); ?>
  
  <div class="x-container max width offset">
    <div class="<?php //x_main_content_class(); ?>x-main  left " role="main">

      <?php while ( have_posts() ) : the_post(); ?>
        <?php x_get_view( 'integrity', 'content', get_post_format() ); ?>
        <?php x_get_view( 'global', '_comments-template' ); ?>
      <?php endwhile; ?>

    </div>

  <aside class="<?php //x_sidebar_class(); ?> x-sidebar  right" role="complementary">
    <?php if ( get_option( 'ups_sidebars' ) != array() ) : ?>
      <?php do_action('sermon_profile'); ?>
	  <?php dynamic_sidebar( apply_filters( 'ups_sidebar', 'sidebar-main' ) ); ?>
	  
    <?php else : ?>
      <?php dynamic_sidebar( 'sidebar-main' ); ?>
    <?php endif; ?>
  </aside>

  </div>

<?php get_footer(); //echo  x_sidebar_class();?>
