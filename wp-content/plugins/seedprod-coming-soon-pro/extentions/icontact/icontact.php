<?php
// * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
/**
 *  Add iContact section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'icontact'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_icontact_section');
}

function seed_cspv4_icontact_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('iContact', 'seedprod'),
        'desc' => __('<p class="description"><a href="https://app.icontact.com/icp/core/externallogin?sAppId=puD4TZWs2kKlKZLZZgD7IAUiqPSYPIvd" target="_blank">Authorize the App</a> and define the app password, then enter that information below. Save your username and password to load your list. <br><a href="http://support.seedprod.com/article/73-collecting-emails-with-icontact" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'icontact_username',
                    'type'      => 'text',
                    'title'     => __( "Username", 'seedprod' ),
                    'subtitle'  => __('Enter your username.', 'seedprod'),
                ),

                array(
                    'id'        => 'icontact_password',
                    'type'      => 'password',
                    'title'     => __( "Password", 'seedprod' ),
                    'subtitle'  => __('Enter your password.', 'seedprod'),
                ),
                array(
                    'id'        => 'icontact_listid',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_icontact_lists()
                ),
                array(
                    'id'        => 'refresh_icontact',
                    'type'      => 'checkbox',
                    'title'     => __( "Refresh iContact Lists", 'seedprod' ),
                    'subtitle'  => __('Check and Save changes to have the lists refreshed above.', 'seedprod'),
                ),


        	)
    );

    return $sections;
}



/**
 *  Get List from iContact
 */
function cspv4_get_icontact_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
    $lists = array();
    if($o['emaillist'] == 'icontact' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list' )){
    $lists = maybe_unserialize(get_transient('seed_cspv4_icontact_lists'));
    //$lists = false;
    if(empty($lists)){
        require_once SEED_CSPV4_PLUGIN_PATH.'extentions/icontact/seed_cspv4_iContactApi.php';


        if(!isset($pass) && isset($icontact_password)){
            $pass = $icontact_password;
        }

        if(!isset($username) && isset($icontact_username)){
            $username = $icontact_username;
        }

        if(!empty($pass) && !empty($username)){
            seed_cspv4_iContactApi::getInstance()->setConfig(array(
                'appId'       => 'puD4TZWs2kKlKZLZZgD7IAUiqPSYPIvd',
                'apiPassword' => $pass,
                'apiUsername' => $username
            ));

            $oiContact = seed_cspv4_iContactApi::getInstance();


            $response = $oiContact->getLists();


        }

        if (empty($response)){
            $lists['false'] = __("No lists Found", 'seedprod');
            return $lists;
        } else {

            foreach ($response as $k => $v){
                $lists[$v->listId] = $v->name;
            }
            if(!empty($lists)){
               set_transient('seed_cspv4_icontact_lists',serialize( $lists ),86400);
            }
        }
    }}
    return $lists;
}


/**
 *  Subscribe iContact
 */
add_action('seed_cspv4_emaillist_icontact', 'seed_cspv4_emaillist_icontact_add_subscriber');

function seed_cspv4_emaillist_icontact_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
    require_once SEED_CSPV4_PLUGIN_PATH.'extentions/icontact/seed_cspv4_iContactApi.php';
    require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );

    // If tracking enabled
    if(!empty($enable_reflink)){
        seed_cspv4_emaillist_database_add_subscriber();
    }

    $pass = $icontact_password;
    $username = $icontact_username;
    $listId = $icontact_listid;

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


    seed_cspv4_iContactApi::getInstance()->setConfig(array(
        'appId'       => 'puD4TZWs2kKlKZLZZgD7IAUiqPSYPIvd',
        'apiPassword' => $pass,
        'apiUsername' => $username
    ));

    $oiContact = seed_cspv4_iContactApi::getInstance();



    $user = array(
        'email' => $email,
        'firstName' => $fname,
        'lastName' => $lname,
        'add_list' => $listId);

    $contact = $oiContact->addContact($email, $sStatus = 'normal', $sPrefix = null, $sFirstName = $fname, $sLastName = $lname);
    $response = $oiContact->subscribeContactToList($contact->contactId, $listId, $sStatus = 'normal');

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

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_refresh_icontact_lists' );

function seed_csvp4_refresh_icontact_lists($value){
    if(!empty($value['refresh_icontact']) && $value['refresh_icontact'] == '1'){
        //Clear cache
        delete_transient('seed_cspv4_icontact_lists');
        cspv4_get_icontact_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('refresh_icontact', 0);
    }

}
