<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
/**
 *  Add GetResponse section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'getresponse'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_getresponse_section');
}

function seed_cspv4_getresponse_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('GetResponse', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to Get Response options. Save after you enter your api key to load your list. <a href="http://support.seedprod.com/article/82-collecting-emails-with-get-response" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'getresponse_api_key',
                    'type'      => 'text',
                    'title'     => __( "API Key", 'seedprod' ),
                    'subtitle'  => __('Enter your API Key. <a target="_blank" href="https://app.getresponse.com/my_account.html" target="_blank">Get your API key</a>', 'seedprod'),
                ),
                array(
                    'id'        => 'getresponse_listid',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_getresponse_lists()
                ),
                array(
                    'id'        => 'refresh_getresponse',
                    'type'      => 'checkbox',
                    'title'     => __( "Refresh Get Response Lists", 'seedprod' ),
                    'subtitle'  => __('Check and Save changes to have the lists refreshed above.', 'seedprod'),
                ),

        	)
    );

    return $sections;
}



/**
 *  Get List from GetResponse
 */
function cspv4_get_getresponse_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
        $lists = array();
        if($o['emaillist'] == 'getresponse' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list' )){
        $lists = maybe_unserialize(get_transient('seed_cspv4_getresponse_lists'));
        if(empty($lists)){
            require_once SEED_CSPV4_PLUGIN_PATH.'extentions/getresponse/seed_cspv4_GetResponseAPI.class.php';

            if(!isset($apikey) && isset($getresponse_api_key)){
                $apikey = $getresponse_api_key;
            }

            if(empty($apikey)){
                return array();
            }

            $api = new seed_cspv4_GetResponse($apikey);

            $response = (array)$api->getCampaigns();

            if (empty($response)){
                $lists['false'] = __("No lists Found", 'seedprod');
                return $lists;
            } else {

                foreach ($response as $k => $v){
                    $lists[$k] = $v->name;
                }
                if(!empty($lists)){
                   set_transient('seed_cspv4_getresponse_lists',serialize( $lists ),86400);
                }
            }
        }}
        return $lists;

}


/**
 *  Subscribe GetResponse
 */
add_action('seed_cspv4_emaillist_getresponse', 'seed_cspv4_emaillist_getresponse_add_subscriber');

function seed_cspv4_emaillist_getresponse_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
                require_once SEED_CSPV4_PLUGIN_PATH.'extentions/getresponse/seed_cspv4_GetResponseAPI.class.php';
                require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );

                // If tracking enabled
                if(!empty($enable_reflink)){
                    seed_cspv4_emaillist_database_add_subscriber();
                }

                $apikey = $getresponse_api_key;
                $api = new seed_cspv4_GetResponse($apikey);
                $listId = $getresponse_listid;

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

                $response = $api->addContact( $listId,$fullname,$email);
                //var_dump( $response);
                if(empty($response)){
                    $seed_cspv4_post_result['status'] = '500';
                    $seed_cspv4_post_result['status'] = $txt_api_error_msg;
                    $seed_cspv4_post_result['status'] = 'alert-danger';
                }else {
                    // if(!empty($enable_reflink)){
                    //     seed_cspv4_emaillist_database_add_subscriber();
                    // }
                    if(empty($seed_cspv4_post_result['status']))
                        $seed_cspv4_post_result['status'] ='200';
                }
}

// Hook into save

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_refresh_getresponse_lists' );

function seed_csvp4_refresh_getresponse_lists($value){
    if(!empty($value['refresh_getresponse']) && $value['refresh_getresponse'] == '1'){
        //Clear cache
        delete_transient('seed_cspv4_getresponse_lists');
        cspv4_get_getresponse_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('refresh_getresponse', 0);
    }

}
