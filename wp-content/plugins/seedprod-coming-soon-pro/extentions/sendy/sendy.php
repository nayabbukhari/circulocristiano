<?php
/**
 *  Add Sendy section
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'sendy'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_sendy_section');
}

function seed_cspv4_sendy_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('Sendy', 'seedprod'),
        'desc' => __('<p class="description">Store emails in your Sendy app. <a href="http://support.seedprod.com/article/76-collecting-emails-with-sendy" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'sendy_url',
                    'type'      => 'text',
                    'title'     => __( "Sendy Url", 'seedprod' ),
                    'subtitle'  => __('The url to where your Sendy is installed. Example: http://your_sendy_installation', 'seedprod'),
                ),
                array(
                    'id'        => 'sendy_list_id',
                    'type'      => 'text',
                    'title'     => __( "List ID", 'seedprod' ),
                    'subtitle'   => __('The list id you want to subscribe a user to. This encrypted & hashed id can be found under View all lists section named ID in Sendy','seedprod'),
                ),


        	)
    );

    return $sections;
}


/**
 *  Get List from Sendy
 */
function cspv4_get_sendy_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;

    $lists = array();
    if($o['emaillist'] == 'sendy' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list')){
        $lists = unserialize(get_transient('seed_cspv4_sendy_lists'));
        if($lists === false){
            //var_dump('SENDY MISS');
            require_once SEED_CSPV4_PLUGIN_PATH.'extentions/sendy/seed_cspv4_MCAPI.class.php';

            if(!isset($apikey) && isset($sendy_api_key)){
                $apikey = $sendy_api_key;
            }

            if(empty($apikey)){
                return array();
            }

            $api = new seed_cspv4_MCAPI($apikey);

            $response = $api->lists();

            if ($response['total'] === 0){
                $lists['false'] = __("No lists Found", 'seedprod');
                return $lists;
            }
            if ($api->errorCode){
                $lists['false'] = __("Unable to load Sendy lists, check your API Key.", 'seedprod');
            } else {

                foreach ($response['data'] as $k => $v){
                    $lists[$v['id']] = $v['name'];
                }
                if(!empty($lists)){
                   set_transient('seed_cspv4_sendy_lists',serialize( $lists ),86400);
                }
            }
        }
    }
    return $lists;
}


/**
 *  Subscribe Sendy
 */
add_action('seed_cspv4_emaillist_sendy', 'seed_cspv4_emaillist_sendy_add_subscriber');

function seed_cspv4_emaillist_sendy_add_subscriber($args){
    require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );

    //Get global and settings
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);

    // If tracking enabled
    if(!empty($enable_reflink)){
        seed_cspv4_emaillist_database_add_subscriber();
    }

    // Set vars
    $url = $sendy_url;
    $list = $sendy_list_id;
    $name = '';
    if(!empty($_REQUEST['name'])){
        $name = $_REQUEST['name'];
    }
    $email = $_REQUEST['email'];
    $fname = '';
    $lname = '';

    if(!empty($name)){
        $name = seed_cspv4_parse_name($name);
        $fname = $name['first'];
        $lname = $name['last'];
    }

    $fullname = $fname.' '.$lname;

    // Make Request
    $args = array('timeout' => 45,'body' => array('name'=>$fullname,'email'=>$email,'list'=> $list,'boolean'=>'true'));
    if(!empty($url) && !empty($list)){
        $r = wp_remote_post( trailingslashit($url).'subscribe', $args );
    }

    // Return results
    if ( is_wp_error( $r ) ) {
        $seed_cspv4_post_result['status'] = '500';
    }else{
        // if(!empty($enable_reflink)){
        //     seed_cspv4_emaillist_database_add_subscriber();
        // }
        $body = wp_remote_retrieve_body($r);
        //var_dump($body);
        //die();
        if($body){
            $seed_cspv4_post_result['status'] = '200';
        }else{
            $seed_cspv4_post_result['status'] = $body;
        }

    }
}
