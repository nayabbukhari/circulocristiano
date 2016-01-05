<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/CLASS-THEME-UPDATER.PHP
// -----------------------------------------------------------------------------
// The theme updater.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Theme Updater
// =============================================================================

// Theme Updater
// =============================================================================

class X_Theme_Updater {

  //
  // Setup hooks.
  //

  public function __construct() {

    add_action( 'init', array( $this, 'init' ) );

    if ( empty( $_GET['action'] ) || ! in_array( $_GET['action'], array( 'do-core-reinstall', 'do-core-upgrade' ), true ) ) {
      add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_themes' ) );
    }

    if ( ! is_multisite() ) {
      add_filter( 'wp_prepare_themes_for_js', array( $this, 'customize_theme_update_html' ) );
    }

  }


  //
  // Filter the update transient and supply new version if one is detected.
  //

  public function pre_set_site_transient_update_themes( $data ) {

    $theme = $this->get_theme_meta();


    //
    // Only check once.
    //

    if ( ! empty( $theme ) && ! empty( $data->checked ) ) {

      $remote = X_Update_API::get_x_theme();
      
      $update = array(
        'new_version' => ( $remote['download_url'] == NULL ) ? ( $remote['latest_version'] . '<br/>' . X_Update_API::get_validation_html_theme_updates() ) : $remote['latest_version'],
        'url'         => 'http://theme.co/changelog/?iframe=true',
        'package'     => $remote['download_url'],
      );

      $remote_is_newer = ( 1 === version_compare( $remote['latest_version'], $theme->local_version ) );

      if ( $remote_is_newer ) {
        $data->response[ $theme->stylesheet ] = $update;
      }

    }

    return $data;

  }


  //
  // Get array of all themes in multisite.
  //
  // The wp_get_themes() function does not seem to work under network
  // activation in the same way as in a single install.
  //

  private function multisite_get_themes() {

    $themes     = array();
    $theme_dirs = scandir( get_theme_root() );
    $theme_dirs = array_diff( $theme_dirs, array( '.', '..', '.DS_Store' ) );

    foreach ( (array) $theme_dirs as $theme_dir ) {
      $themes[] = wp_get_theme( $theme_dir );
    }

    return $themes;

  }


  //
  // Get the meta data from the style.css headers.
  //

  protected function get_theme_meta() {

    $themes = wp_get_themes();

    if ( is_multisite() ) {
      $themes = $this->multisite_get_themes();
    }

    $x_theme = array();

    foreach ( (array) $themes as $theme ) {

      $x_theme['name']      = $theme->get( 'Name' );
      $x_theme['theme_uri'] = $theme->get( 'ThemeURI' );

      if ( $x_theme['name'] == 'X' && $x_theme['theme_uri'] == 'http://theme.co/x/' ) {

        $x_theme['stylesheet']              = $theme->stylesheet;
        $x_theme['name']                    = $theme->get( 'Name' );
        $x_theme['theme_uri']               = $theme->get( 'ThemeURI' );
        $x_theme['author']                  = $theme->get( 'Author' );
        $x_theme['local_version']           = $theme->get( 'Version' );
        $x_theme['sections']['description'] = $theme->get( 'Description' );
        $x_theme['local_path']              = get_theme_root() . '/' . $theme->stylesheet;

      }

    }

    return (object) $x_theme;

  }


  //
  // Customize the update HTML for the theme.
  //

  public function customize_theme_update_html( $prepared_themes ) {

    $theme = $this->get_theme_meta();

    if ( isset( $prepared_themes[$theme->stylesheet] ) ) {

      $update = $prepared_themes[$theme->stylesheet]['update'];
      $update = preg_replace( '/(details)[^(or)]*?<em>.*?<\/em>/', '', $update );

      $prepared_themes[$theme->stylesheet]['update'] = $update;

    }

    return $prepared_themes;

  }

}