<?php
/*
Plugin Name: SeedProd Drip
Plugin URI: http://www.seedprod.com
Description: SeedProd Drip Add On
Version:  1.0.0
Author: SeedProd
Author URI: http://www.seedprod.com
TextDomain: seedprod
License: GPLv2
Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
*/

define( 'SEED_CSPV4_DRIP_VERSION', '1.0.0' ); // Plugin Version Number. Recommend you use Semantic Versioning http://semver.org/


/**
 *  Add drip to the menu
 */
add_filter( 'seed_cspv4_providers', 'seed_cspv4_drip_providers' );

function seed_cspv4_drip_providers( $v ) {
    $v['drip'] = 'Drip';

    return $v;
}



/**
 *  Add drip section to admin
 */
$seed_cspv4 = get_option( 'seed_cspv4' );
if ( $seed_cspv4['emaillist'] == 'drip' ) {
    add_filter( 'seedredux/options/seed_cspv4/sections', 'seed_cspv4_drip_section' );
}

function seed_cspv4_drip_section( $sections ) {

    global $seed_cspv4;
    //var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title'  => __( 'Drip', 'seedprod' ),
        'desc'   => __( '<p class="description">Configure saving subscribers to Drip options.</p>', 'seedprod' ),
        'icon'   => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id'       => 'drip_api_key',
                'type'     => 'text',
                'title'    => __( "API Token", 'seedprod' ),
                'subtitle' => __( 'Enter your API Token.', 'seedprod' ),
            ),
            array(
                'id'       => 'drip_account_id',
                'type'     => 'text',
                'title'    => __( "Account ID", 'seedprod' ),
                'subtitle' => __( 'Enter your Account ID and Save to load your lists.', 'seedprod' ),
            ),
            array(
                'id'      => 'drip_listid',
                'type'    => 'select',
                'title'   => __( "Campaign", 'seedprod' ),
                'options' => cspv4_get_drip_lists()
            ),
            array(
                'id'       => 'refresh_drip',
                'type'     => 'checkbox',
                'title'    => __( "Refresh Drip Lists", 'seedprod' ),
                'subtitle' => __( 'Check and Save changes to have the lists refreshed above.', 'seedprod' ),
            ),
            array(
                'id'       => 'drip_double_optin',
                'type'     => 'checkbox',
                'title'    => __( "Enable Double Optin", 'seedprod' ),
                'subtitle' => __( 'Check to enable double optin.', 'seedprod' ),
                'default'  => false,
            ),


        )
    );

    return $sections;
}


/**
 *  Get List from MailChimp
 */
function cspv4_get_drip_lists( $apikey = null ) {
    global $seed_cspv4;
    extract( $seed_cspv4 );
    $o     = $seed_cspv4;
    $lists = array();
    if ( $o['emaillist'] == 'drip' || ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_GET['action'] ) && $_GET['action'] == 'seed_cspv4_refresh_list' ) ) {
        $lists = maybe_unserialize( get_transient( 'seed_cspv4_drip_lists' ) );
        if ( empty( $lists ) ) {

            if ( ! isset( $apikey ) && isset( $drip_api_key ) ) {
                $apikey = $drip_api_key;
            }

            if ( empty( $apikey ) ) {
                return array();
            }

            $args = array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode( $apikey . ':' . '' )
                )
            );
            $url  = "https://api.getdrip.com/v2/$drip_account_id/campaigns";

            $response = wp_remote_get( $url, $args );
            $body     = json_decode( wp_remote_retrieve_body( $response ) );

            if ( ! empty( $body->campaigns ) ) {
                foreach ( $body->campaigns as $k => $v ) {
                    $lists[ $v->id ] = $v->name;
                }
                if ( ! empty( $lists ) ) {
                    set_transient( 'seed_cspv4_drip_lists', serialize( $lists ), 86400 );
                }
            }
        }
    }

    return $lists;
}


/**
 *  Subscribe MailChimp
 */
add_action( 'seed_cspv4_emaillist_drip', 'seed_cspv4_emaillist_drip_add_subscriber' );

function seed_cspv4_emaillist_drip_add_subscriber( $args ) {
    global $seed_cspv4, $seed_cspv4_post_result;
    extract( $seed_cspv4 );
    require_once( SEED_CSPV4_PLUGIN_PATH . 'lib/nameparse.php' );

    // If tracking enabled
    if ( ! empty( $enable_reflink ) ) {
        seed_cspv4_emaillist_database_add_subscriber();
    }

    $apikey = $drip_api_key;
    //$drip_account_id;
    $listId = $drip_listid;


    $name = '';
    if ( ! empty( $_REQUEST['name'] ) ) {
        $name = $_REQUEST['name'];
    }
    $email         = $_REQUEST['email'];
    $fname         = '';
    $lname         = '';
    $custom_fields = array();
    $tags          = array();

    if ( ! empty( $name ) ) {
        $name          = seed_cspv4_parse_name( $name );
        $fname         = $name['first'];
        $lname         = $name['last'];
        $custom_fields = array( 'first_name' => $fname, 'last_name' => $lname );
    }

    $postData = array(
        'subscribers' => array(
            array(
                'email' => $email,
                'custom_fields' => $custom_fields,
                'tags'=> $tags,
            )
        )
    );


    $postData = json_encode( $postData );

    $args = array(
        'body'    => $postData,
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode( $apikey . ':' . '' ),
            'Content-Type'  => 'application/vnd.api+json'
        )
    );
    $url  = "https://api.getdrip.com/v2/$drip_account_id/subscribers";

    $response = wp_remote_post( $url, $args );


    $body1 = json_decode( wp_remote_retrieve_body( $response ) );


    if(!empty($drip_double_optin)){
        $drip_double_optin = true;
    }else{
        $drip_double_optin = false;
    }


    $postData = array(
        'subscribers' => array(
            array(
                'email'        => $email,
                'double_optin' => $drip_double_optin,
            )
        )
    );

    $postData = json_encode( $postData );


    $args = array(
        'body'    => $postData,
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode( $apikey . ':' . '' ),
            'Content-Type'  => 'application/vnd.api+json'
        )
    );
    $url  = "https://api.getdrip.com/v2/$drip_account_id/campaigns/$listId/subscribers";

    $response = wp_remote_post( $url, $args );
    //var_dump( $response );
    $body2 = json_decode( wp_remote_retrieve_body( $response ) );
    //var_dump( $body2 );

    if ( $seed_cspv4_post_result['status'] == '600' ) {
        $seed_cspv4_post_result['status'] = '200';
    }

    if ( empty( $seed_cspv4_post_result['status'] ) ) {
        $seed_cspv4_post_result['status'] = '200';
    }

}

// Hook into save

add_action( 'seedredux/options/seed_cspv4/saved', 'seed_csvp4_refresh_drip_lists' );

function seed_csvp4_refresh_drip_lists( $value ) {
    if ( ! empty( $value['refresh_drip'] ) && $value['refresh_drip'] == '1' ) {
        //Clear cache
        delete_transient( 'seed_cspv4_drip_lists' );
        cspv4_get_drip_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set( 'refresh_drip', 0 );
    }

}
