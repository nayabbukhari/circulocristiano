<?php
/**
 * Add CampaignMonitor section
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'campaignmonitor'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_campaignmonitor_section');
}

function seed_cspv4_campaignmonitor_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('CampaignMonitor', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to Campaign Monitor options. Save your change after you enter your api key to load your client. Then save again after you select a client to load you list. <a href="http://support.seedprod.com/article/26-collecting-emails-with-campaign-monitor" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'campaignmonitor_api_key',
                    'type'      => 'text',
                    'title'     => __( "API Key", 'seedprod' ),
                    'subtitle'  => __('Get your <a target="_blank" href="http://help.campaignmonitor.com/topic.aspx?t=206">API key</a>', 'seedprod'),
                ),
                array(
                    'id'        => 'campaignmonitor_clientid',
                    'type'      => 'select',
                    'title'     => __( "Client", 'seedprod' ),
                    'options'   => cspv4_get_campaignmonitor_clients()
                ),
                array(
                    'id'        => 'campaignmonitor_listid',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_campaignmonitor_lists()
                ),
                array(
                    'id'        => 'refresh_campaignmonitor',
                    'type'      => 'checkbox',
                    'title'     => __( "Refresh Campaign Monitor Lists", 'seedprod' ),
                    'subtitle'  => __('Check and Save changes to have the lists refreshed above.', 'seedprod'),
                ),

        	)
    );

    return $sections;
}



/**
 *  Get List from CampaignMonitor
 */
function cspv4_get_campaignmonitor_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
  $lists = array();
        if($o['emaillist'] == 'campaignmonitor' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list' )){
        $lists = maybe_unserialize(get_transient('seed_cspv4_campaignmonitor_lists'));
        if(empty($lists)){

            if (class_exists('CS_REST_Clients') ) {
                //trigger_error("Duplicate: Another Campaign Moniter client library is already in scope.", E_USER_WARNING);
            }
            else {
                require_once SEED_CSPV4_PLUGIN_PATH.'extentions/campaignmonitor/campaign_monitor/csrest_clients.php';
            }


            if(!isset($apikey) && isset($campaignmonitor_api_key)){
                $apikey = $campaignmonitor_api_key;
            }
            if(!isset($clientid) && isset($campaignmonitor_clientid)){
                $clientid = $campaignmonitor_clientid;
            }

            if(empty($apikey) || empty($clientid)){
                return array();
            }

            $api = new CS_REST_Clients($clientid, $apikey);

            $response = $api->get_lists();

            if($response->was_successful()){
                foreach($response->response as $k => $v){
                    $lists[$v->ListID] = $v->Name;
                }
                if(!empty($lists)){
                   set_transient('seed_cspv4_campaignmonitor_lists',serialize( $lists ),86400);
                }
            }else{
                $lists['false'] = __("Unable to load Campaign Monitor lists", 'seedprod');
            }

        }}
        return $lists;
}

    /**
     *  Get List from Campaign Monitor
     */
    function cspv4_get_campaignmonitor_clients($apikey=null){
        global $seed_cspv4;
        extract($seed_cspv4);
        $o = $seed_cspv4;
        $clients = array();
        if($o['emaillist'] == 'campaignmonitor' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_campaingmonitor_client' )){
        $clients = maybe_unserialize(get_transient('seed_cspv4_campaignmonitor_clients'));
        if(empty($clients)){
            //var_dump('miss');
            if (class_exists('CS_REST_General') ) {
                //trigger_error("Duplicate: Another Campaign Moniter client library is already in scope.", E_USER_WARNING);
            }
            else {
                require_once SEED_CSPV4_PLUGIN_PATH.'extentions/campaignmonitor/campaign_monitor/csrest_general.php';
            }


            if(!isset($apikey) && isset($campaignmonitor_api_key)){
                $apikey = $campaignmonitor_api_key;
            }

            if(empty($apikey)){
                return array();
            }

            $api = new CS_REST_General($apikey);

            $response = $api->get_clients();

            if($response->was_successful()) {
                foreach($response->response as $k => $v){
                    $clients[$v->ClientID] = $v->Name;
                }
                if(!empty($clients)){
                   set_transient('seed_cspv4_campaignmonitor_clients',serialize( $clients ),86400);
                }
            }else{
                $clients['false'] = __("Unable to load Campaign Monitor clients", 'seedprod');
            }

        }}
        return $clients;
    }


/**
 *  Subscribe CampaignMonitor
 */
add_action('seed_cspv4_emaillist_campaignmonitor', 'seed_cspv4_emaillist_campaignmonitor_add_subscriber');

function seed_cspv4_emaillist_campaignmonitor_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
    require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );
                if (class_exists('CS_REST_Subscribers') ) {
                    //trigger_error("Duplicate: Another Campaign Moniter client library is already in scope.", E_USER_WARNING);
                }
                else {
                    require_once SEED_CSPV4_PLUGIN_PATH.'extentions/campaignmonitor/campaign_monitor/csrest_subscribers.php';
                }

                // If tracking enabled
                if(!empty($enable_reflink)){
                    seed_cspv4_emaillist_database_add_subscriber();
                }

                $apikey = $campaignmonitor_api_key;
                $listid = $campaignmonitor_listid;

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

                $api = new CS_REST_Subscribers($listid, $apikey);

                $response = $api->add(array(
                    'EmailAddress' => $email,
                    'Name' => $fname.' '.$lname,
                    // 'CustomFields' => array(
                    //     array(
                    //         'Key' => 'Field Key',
                    //         'Value' => 'Field Value'
                    //     )
                    // ),
                    'Resubscribe' => true
                ));
                //var_dump($name);
                //var_dump($response);

                if($response->was_successful()){
                    if(empty($seed_cspv4_post_result['status']))
                            $seed_cspv4_post_result['status'] ='200';
                }else{
                    $seed_cspv4_post_result['status'] = '500';
                    $seed_cspv4_post_result['status'] = $txt_api_error_msg;
                    $seed_cspv4_post_result['status'] = 'alert-danger';
                };
}


// Hook into save

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_refresh_campaignmonitor_lists' );

function seed_csvp4_refresh_campaignmonitor_lists($value){
    if(!empty($value['refresh_campaignmonitor']) && $value['refresh_campaignmonitor'] == '1'){
        //Clear cache
        delete_transient('seed_cspv4_campaignmonitor_lists');
        delete_transient('seed_cspv4_campaignmonitor_clients');
        cspv4_get_campaignmonitor_clients();
        cspv4_get_campaignmonitor_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('refresh_campaignmonitor', 0);
    }

}
