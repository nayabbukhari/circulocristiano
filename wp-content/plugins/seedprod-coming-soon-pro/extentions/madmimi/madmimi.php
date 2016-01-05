<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add MadMimi section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'madmimi'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_madmimi_section');
}

function seed_cspv4_madmimi_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('MadMimi', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to Mad Mimi options. Save after you enter your api key and username to load your list. <a href="http://support.seedprod.com/article/75-collecting-emails-with-mad-mimi" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'madmimi_api_key',
                    'type'      => 'text',
                    'title'     => __( "API Key", 'seedprod' ),
                    'subtitle'  => __('Enter your API Key. <a target="_blank" href="https://madmimi.com/user/edit?set_api&account_info_tabs=account_info_personal" target="_blank">Get your API key</a>', 'seedprod'),
                ),

                array(
                    'id'        => 'madmimi_username',
                    'type'      => 'text',
                    'title'     => __( "Username or Email", 'seedprod' ),
                    'subtitle'  => __('Enter your username or email.', 'seedprod'),
                ),
                array(
                    'id'        => 'madmimi_listid',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_madmimi_lists()
                ),
                array(
                    'id'        => 'refresh_madmimi',
                    'type'      => 'checkbox',
                    'title'     => __( "Refresh Mad Mimi Lists", 'seedprod' ),
                    'subtitle'  => __('Check and Save changes to have the lists refreshed above.', 'seedprod'),
                ),


        	)
    );

    return $sections;
}



/**
 *  Get List from MadMimi
 */
function cspv4_get_madmimi_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
        $lists = array();
        if($o['emaillist'] == 'madmimi' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list' )){
        $lists = maybe_unserialize(get_transient('seed_cspv4_madmimi_lists'));
        //$lists = false;
        if(empty($lists)){
            //var_dump('MadMimi Miss');
            require_once SEED_CSPV4_PLUGIN_PATH.'extentions/madmimi/seed_cspv4_MadMimi.class.php';

            if(!isset($apikey) && isset($madmimi_api_key)){
                $apikey = $madmimi_api_key;
            }

            if(!isset($username) && isset($madmimi_username)){
                $username = $madmimi_username;
            }

            if(!empty($apikey) && !empty($username)){
                $api = new seed_cspv4_MadMimi($username,$apikey);

                $response = $api->Lists();

                if($response == 'Unable to authenticate'){
                    $lists['false'] = __("Unable to authenticate", 'seedprod');
                    return $lists;
                }
            }

            if (empty($response)){
                $lists['false'] = __("No lists Found", 'seedprod');
                return $lists;
            } else {
                $response = json_decode($response);
                foreach ($response as $k => $v){
                    $lists[$v->name] = $v->name;
                }
                if(!empty($lists)){
                   set_transient('seed_cspv4_madmimi_lists',serialize( $lists ),86400);
                }
            }
        }}
        return $lists;
}


/**
 *  Subscribe MadMimi
 */
add_action('seed_cspv4_emaillist_madmimi', 'seed_cspv4_emaillist_madmimi_add_subscriber');

function seed_cspv4_emaillist_madmimi_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
    require_once SEED_CSPV4_PLUGIN_PATH.'extentions/madmimi/seed_cspv4_MadMimi.class.php';
    require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );

    // If tracking enabled
    if(!empty($enable_reflink)){
        seed_cspv4_emaillist_database_add_subscriber();
    }

    $apikey = $madmimi_api_key;
    $username = $madmimi_username;
    $api = new seed_cspv4_MadMimi($username,$apikey);
    $listId = $madmimi_listid;

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

    $user = array(
        'email' => $email,
        'firstName' => $fname,
        'lastName' => $lname,
        'add_list' => $listId);

    $response = $api->AddUser($user);
    //var_dump();

    if(empty($response)){
        $seed_cspv4_post_result['status'] = '500';
    }else {
        // if(!empty($enable_reflink)){
        //     seed_cspv4_emaillist_database_add_subscriber();
        // }
        if(empty($seed_cspv4_post_result['status']))
            $seed_cspv4_post_result['status'] ='200';
    }
}

// Hook into save

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_refresh_madmimi_lists' );

function seed_csvp4_refresh_madmimi_lists($value){
    if(!empty($value['refresh_madmimi']) && $value['refresh_madmimi'] == '1'){
        //Clear cache
        delete_transient('seed_cspv4_madmimi_lists');
        cspv4_get_madmimi_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('refresh_madmimi', 0);
    }

}
