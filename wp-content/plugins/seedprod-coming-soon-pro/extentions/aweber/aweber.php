<?php
/**
 *  Add Aweber section
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'aweber'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_aweber_section');
}

function seed_cspv4_aweber_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('Aweber', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to Aweber options. <a target="_blank" href="http://support.seedprod.com/article/34-collecting-emails-with-aweber">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'aweber_authorization_code',
                    'type'      => 'text',
                    'title'     => __( "Authorization Code", 'seedprod' ),
                    'desc'     => __( "Paste in the Authorization Code you received when authorizing the app and click <strong>Save</strong>.", 'seedprod' ),
                    'subtitle'  => __('<a href="https://auth.aweber.com/1.0/oauth/authorize_app/a662998e" target="_blank">Authorize App</a> &larr; Click the link to get you Authorization Code.', 'seedprod'),
                ),
                array(
                    'id'        => 'aweber_listid',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_aweber_lists()
                ),
                array(
                    'id'        => 'refresh_aweber',
                    'type'      => 'checkbox',
                    'title'     => __( "Refresh Aweber Lists", 'seedprod' ),
                    'subtitle'  => __('Check and Save changes to have the lists refreshed above. ', 'seedprod'),
                ),

        	)
    );

    return $sections;
}



/**
 *  Get List from Aweber
 */
function cspv4_get_aweber_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
        $lists = array();
        if($o['emaillist'] == 'aweber' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list' )){
        $lists = maybe_unserialize(get_transient('seed_cspv4_aweber_lists'));
        if(empty($lists)){
            //var_dump('hit');
            require_once SEED_CSPV4_PLUGIN_PATH.'extentions/aweber/aweber_api/aweber_api.php';

            $authorization_code = $aweber_authorization_code;
            if(empty($seed_cspv4_aweber_auth) && !empty($authorization_code)){
                try {
                    $auth = AWeberAPI::getDataFromAweberID($authorization_code);
                    list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $auth;

                    update_option('seed_cspv4_aweber_auth', array('consumer_key'=>$consumerKey,'consumer_secret'=>$consumerSecret,'access_key'=>$accessKey,'access_secret'=>$accessSecret));
                    //echo '200';
                }
                catch(AWeberAPIException $exc) {
                    //echo $exc;
                }
            }else{
                update_option('seed_cspv4_aweber_auth','');
            }


            $aweber_auth = get_option('seed_cspv4_aweber_auth');
            if(!empty($aweber_auth)){
                extract($aweber_auth);
                $consumerKey = $consumer_key;
                $consumerSecret = $consumer_secret;
            }

            if(empty($consumerKey) || empty($consumerSecret)){
                return array();
            }

            try{
                $aweber = new AWeberAPI($consumerKey, $consumerSecret);
                $account = $aweber->getAccount($access_key, $access_secret);
            } catch (Exception $e) {}

            foreach($account->lists as $list) {
                $lists[$list->id] = $list->name;
            }

            if(!empty($lists)){
                set_transient('seed_cspv4_aweber_lists',serialize( $lists ),86400);
            } else{
                $lists['false'] = __("Unable to load Aweber lists", 'seedprod');
            }

        }
        }
        return $lists;
}

/**
 *  Callback for Aweber Authorization
 */
function cspv4_aweber_auth(){
    if(check_ajax_referer('seed_cspv4_aweber_auth')){
        require_once SEED_CSPV4_PLUGIN_PATH.'extentions/aweber/aweber_api/aweber_api.php';
        $authorization_code = urldecode($_GET['auth_code']);
        try {
            $auth = AWeberAPI::getDataFromAweberID($authorization_code);
            list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $auth;

            update_option('seed_cspv4_aweber_auth', array('consumer_key'=>$consumerKey,'consumer_secret'=>$consumerSecret,'access_key'=>$accessKey,'access_secret'=>$accessSecret));
            echo '200';
        }
        catch(AWeberAPIException $exc) {
            echo '500';
        }
        exit;
    }
}


/**
 *  Subscribe Aweber
 */
add_action('seed_cspv4_emaillist_aweber', 'seed_cspv4_emaillist_aweber_add_subscriber');

function seed_cspv4_emaillist_aweber_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
        require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );
        require_once SEED_CSPV4_PLUGIN_PATH.'extentions/aweber/aweber_api/aweber_api.php';

                // If tracking enabled
                if(!empty($enable_reflink)){
                    seed_cspv4_emaillist_database_add_subscriber();
                }

                $aweber_auth = get_option('seed_cspv4_aweber_auth');
                extract($aweber_auth);

                if(!empty($consumer_key)){
                $consumerKey = $consumer_key;
                $consumerSecret = $consumer_secret;
                $aweber = new AWeberAPI($consumerKey, $consumerSecret);

                $list_id = $aweber_listid;

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
                }

                try {
                    $account = $aweber->getAccount($access_key, $access_secret);
                    $account_id     = $account->id;
                    $listURL = "/accounts/{$account_id}/lists/{$list_id}";
                    $list = $account->loadFromUrl($listURL);

                    # create a subscriber
                    $params = array(
                        'email' => $email,
                        'name' => $fullname,
                        'ip_address' => seed_cspv4_get_ip(),
                        // 'ad_tracking' => 'coming_soon_pro',
                        // 'last_followup_message_number_sent' => 1,
                        // 'misc_notes' => 'my cool app',
                        // 'name' => 'John Doe',
                        // 'custom_fields' => array(
                        //     'Car' => 'Ferrari 599 GTB Fiorano',
                        //     'Color' => 'Red',
                        // ),
                    );
                    $subscribers = $list->subscribers;
                    $new_subscriber = $subscribers->create($params);

                    # success!
                    //$this->add_subscriber($email,$fname,$lname);
                    if(empty($seed_cspv4_post_result['status']))
                            $seed_cspv4_post_result['status'] ='200';

                } catch(AWeberAPIException $exc) {
                    if($exc->status == '400'){
                        $seed_cspv4_post_result['status'] = '600';
                        $seed_cspv4_post_result['msg'] =$txt_already_subscribed_msg;
                        $seed_cspv4_post_result['msg_class'] = 'alert-danger';
                    }else{
                        $seed_cspv4_post_result['status'] = '500';
                    }
                    // var_dump($exc);
                    // print "<h3>AWeberAPIException:</h3>";
                    // print " <li> Type: $exc->type              <br>";
                    // print " <li> Msg : $exc->message           <br>";
                    // print " <li> Docs: $exc->documentation_url <br>";
                    // print "<hr>";
                }
}


// Hook into save

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_refresh_aweber_lists' );

function seed_csvp4_refresh_aweber_lists($value){
    if(!empty($value['refresh_aweber']) && $value['refresh_aweber'] == '1'){
        //Clear cache
        delete_transient('seed_cspv4_aweber_lists');
        cspv4_get_aweber_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('refresh_aweber', 0);
    }

}
