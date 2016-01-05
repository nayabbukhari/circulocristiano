<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/CLASS-UPDATE-API.PHP
// -----------------------------------------------------------------------------
// The update API for X and related plugins.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Update API
// =============================================================================

// Update API
// =============================================================================

class X_Update_API {

  //
  // Holds a copy of itself so it can be referenced by the class name.
  //

  private static $instance, $theme_updater, $plugin_updater, $errors;


  //
  // The update URL base.
  //

  private static $base_url = 'https://community.theme.co/api-v1/';


  //
  // Adds a reference of this object to $instance and adds hooks.
  //

  public function __construct() {

    self::$instance = $this;

    add_action( 'init', array( $this, 'init' ) );
    add_action( 'upgrader_pre_download', array( $this, 'upgrader_screen_message' ), 10, 3 );

    if ( defined('THEMECO_PRERELEASES') && THEMECO_PRERELEASES ) {
    	add_filter( 'x_update_product_slug', array( $this, 'enable_pre_release_updates' ) );
    	add_filter( 'x_update_response_data', array( $this, 'filter_pre_release_data' ) );
    }
  }


  //
  // This class setup instantiates the theme and plugin updaters based on
  // WordPress permissions.
  //

  public function init() {

    if ( current_user_can( 'update_plugins' ) ) {
      $plugin_updater = new X_Plugin_Updater;
    }

    if ( current_user_can( 'update_themes' ) ) {
      $theme_updater = new X_Theme_Updater;
    }

  }

  public function enable_pre_release_updates( $slug ) {
  	if ( 'x-the-theme' == $slug || 'cornerstone' == $slug ) {
  		$slug .= '-edge';
  	}
  	return $slug;
  }

  public function filter_pre_release_data( $data ) {

  	if ( isset( $data['slug'] ) && strpos( $data['slug'], '-edge' ) !== false ) {
  		$data['slug'] = str_replace('-edge', '', $data['slug']);

  	}

  	if ( isset( $data['products'] ) ) {
  		foreach ($data['products'] as $key => $value) {
  			if ( isset( $value['slug'] ) && strpos( $value['slug'], '-edge' ) !== false ) {
  				$value['slug'] = str_replace( '-edge', '', $value['slug']);
		  		$data['products'][$value['slug']] = $value;
		  		unset($data['products'][$key]);
		  	}
  		}
  	}

  	return $data;
  }

  //
  // Request information from the remote update API. The $args input is an
  // array of parameters to send or override.
  //

  public static function remote_request( $args ) {

    $name    = x_addons_get_api_key_option_name();
    $api_key = esc_attr( get_option( $name ) );

    if ( $api_key == '' )
      $api_key = 'unverified';

    $args = wp_parse_args( $args, array(
      'action'   => 'autoupdates',
      'api-key'  => $api_key,
      'siteurl'  => preg_replace( '#(https?:)?//#','', esc_attr( untrailingslashit( network_home_url() ) ) ),
      'xversion' => X_VERSION
    ) );

    if ( isset( $args['product'] ) )
    	$args['product'] = apply_filters( 'x_update_product_slug', $args['product'] );

    if ( isset( $args['products'] ) ) {
    	foreach ($args['products'] as $key => $slug ) {
    		$args['products'][$key] = apply_filters( 'x_update_product_slug', $slug );
    	}

    	$args['products'] = base64_encode( serialize( $args['products'] ) );
    }

    $request_url = self::$base_url . trailingslashit( $args['action'] ) . trailingslashit( $args['api-key'] );

    unset($args['action']);
    unset($args['api-key']);

    $uri = add_query_arg( $args, $request_url );

    $request = wp_remote_get( $uri, array( 'timeout' => 15 ) );
    $connection_error = array( 'code' => 4, 'message' => __( 'Could not establish connection. For assistance, please start by reviewing our article on troubleshooting <a href="https://community.theme.co/kb/connection-issues/">connection issues.</a>', '__x__' ) );

    if ( is_wp_error( $request ) || $request['response']['code'] != 200 ) {
      self::store_error( $request );
      return $connection_error;
    }

    $data = apply_filters( 'x_update_response_data', json_decode( $request['body'], true ) );

    if ( ! isset( $data['code'] ) ) {
      return $connection_error;
    }

    //
    // Key was good but is now invalid (revoked).
    //

    if ( $api_key != '' && $data['code'] == 3 ) {
      delete_option( $name );
      delete_site_option( 'x_addon_list_cache' );
    }

    return $data;

  }

  public static function list_addons() {
    return self::remote_request( array( 'action' => 'listaddons' ) );
  }


  //
  // Override the API key so we can test one specifically.
  //

  public static function validate_key( $key ) {
    return self::remote_request( array( 'api-key' => esc_attr( $key ), 'product' => 'x-the-theme' ) );
  }


  //
  // Retrieve remote product.
  //

  public static function get_product( $slug ) {
    return self::remote_request( array( 'product' => $slug ) );
  }

  public static function get_products( $slugs ) {
    return self::remote_request( array( 'products' => $slugs ) );
  }


  //
  // Shortcut to retrieve X remote data.
  //

  public static function get_x_theme() {
    return self::get_product( 'x-the-theme' );
  }


  //
  // Shortcut to retrieve X - Shortcodes data.
  //

  public static function get_x_shortcodes() {
    return self::get_product( 'x-shortcodes' );
  }


  //
  // Links to the validation page (output when an update is available and if a
  // user has not yet validated their purchase).
  //

  public static function get_validation_html_theme_main() {
    return sprintf( __( '<a href="%s">Validate X to enable automatic updates</a>', '__x__' ), x_addons_get_link_home() );
  }

  public static function get_validation_html_theme_updates() {
    return sprintf( __( '<a href="%s">Validate X to enable automatic updates</a>', '__x__' ), x_addons_get_link_home() );
  }

  public static function get_validation_html_theme_update_error() {
    return sprintf( __( 'X is not validated. <a href="%s">Validate X to enable automatic updates</a>', '__x__' ), x_addons_get_link_home() );
  }

  public static function get_validation_html_plugin_main() {
    return sprintf( __( '<a href="%s">Validate X to enable automatic updates</a>.', '__x__' ), x_addons_get_link_home() );
  }

  public static function get_validation_html_plugin_updates() {
    return sprintf( __( '<a href="%s">Validate X to enable automatic updates (go to "Addons" &gt; "Home" to learn more.)</a>', '__x__' ), x_addons_get_link_home() );
  }

  public static function get_validation_html_plugin_update_error() {
    return sprintf( __( 'X is not validated. <a href="%s">Validate X to enable automatic updates.</a>', '__x__' ), x_addons_get_link_home() );
  }


  //
  // Cache addons list in a transient.
  //

  public static function get_cached_addons() {

    if ( false === ( $addons = get_site_option( 'x_addon_list_cache', false ) ) ) {

      $request = self::list_addons();

      $error = array( 'error' => true, 'message' => __( 'Could not retrieve extensions list. For assistance, please start by reviewing our article on troubleshooting <a href="https://community.theme.co/kb/connection-issues/">connection issues.</a>', '__x__' ) );

      $addons = ( isset( $request['addons'] ) ) ? $request['addons'] : $error;

      update_site_option( 'x_addon_list_cache', $addons );

    }

    return $addons;

  }


  //
  // Upgrader screen message.
  //

  public function upgrader_screen_message( $false, $package, $upgrader ) {

    if ( null === $package ) {
      if ( isset( $upgrader->skin->plugin_info['X Plugin'] ) ) {

        return new WP_Error( 'x_not_valid', self::get_validation_html_plugin_update_error() );

      } else if ( isset( $upgrader->skin->theme_info['Name'] ) && 'X' == $upgrader->skin->theme_info['Name'] ) {

        return new WP_Error( 'x_not_valid', self::get_validation_html_theme_update_error()  );

      }
    }

    return $false;

  }


  //
  // Save connection errors.
  //

  public static function store_error( $wp_error ) {

    if ( ! isset( self::$errors ) ) {
      self::$errors = array();
    }

    array_push( self::$errors, (array) $wp_error );

  }


  //
  // Return any saved errors.
  //

  public static function get_errors() {

    return isset( self::$errors ) ? self::$errors : array();

  }

}

new X_Update_API;