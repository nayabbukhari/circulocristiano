<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add Feedblitz section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'feedblitz'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_feedblitz_section');
}

function seed_cspv4_feedblitz_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('Feedblitz', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to Feedblitz options. Save after you enter your api key to load your list.</p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'feedblitz_api_key',
                    'type'      => 'text',
                    'title'     => __( "API Key", 'seedprod' ),
                    'subtitle'  => __('Enter your API Key. <a target="_blank" href="http://support.feedblitz.com/customer/portal/articles/874021-how-do-i-get-an-api-key-" target="_blank">Get your API key</a>', 'seedprod'),
                ),
                array(
                    'id'        => 'feedblitz_listid',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_feedblitz_lists()
                ),
                array(
                    'id'        => 'refresh_feedblitz',
                    'type'      => 'checkbox',
                    'title'     => __( "Refresh Feedblitz Lists", 'seedprod' ),
                    'subtitle'  => __('Check and Save changes to have the lists refreshed above.', 'seedprod'),
                ),


        	)
    );

    return $sections;
}



/**
 *  Get List from Feedblitz
 */
function cspv4_get_feedblitz_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
    $lists = array();
    if($o['emaillist'] == 'feedblitz' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list')){
    $lists = maybe_unserialize(get_transient('seed_cspv4_feedblitz_lists'));
    if(empty($lists)){
        //var_dump('miss');

        if(!isset($apikey) && isset($feedblitz_api_key)){
            $apikey = $feedblitz_api_key;
        }

        if(empty($apikey)){
            return array();
        }

        $url = 'https://www.feedblitz.com/f.api/syndications?summary=1&key='.$apikey;


        $response = wp_remote_get( $url );
        $xml_string = wp_remote_retrieve_body( $response , true );
        $xml = simplexml_load_string($xml_string);
		$json = json_encode($xml);
		$api = json_decode($json,TRUE);


        if ($api['syndications']['count'] == 0){
            $lists['false'] = __("No lists Found", 'seedprod');
            return $lists;
        }
        if (empty($api['syndications'])){
            $lists['false'] = __("Unable to load Feedblitz lists, check your API Key.", 'seedprod');
        } else {
        	if($api['syndications']['count'] == 1){
        		$lists[$api['syndications']['syndication']['id']] = $api['syndications']['syndication']['name'];
        	}else{
       			foreach ($api['syndications']['syndication'] as $k => $v){
                	$lists[$v['id']] = $v['name'];
            	}
        	}


            if(!empty($lists)){
               set_transient('seed_cspv4_feedblitz_lists',serialize( $lists ),86400);
            }
        }
    }}
    return $lists;
}


/**
 *  Subscribe Feedblitz
 */
add_action('seed_cspv4_emaillist_feedblitz', 'seed_cspv4_emaillist_feedblitz_add_subscriber');

function seed_cspv4_emaillist_feedblitz_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);

	        $apikey = $feedblitz_api_key;
	        $listId = $feedblitz_listid;

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

    

    		$url = "https://www.feedblitz.com/f/?SimpleApiSubscribe&email=$email&listid=$listId&key=$apikey";

	    	$response = wp_remote_get( $url );
	        $xml_string = wp_remote_retrieve_body( $response , true );
	        $xml = simplexml_load_string($xml_string);
			$json = json_encode($xml);
			$api = json_decode($json,TRUE);
 			

            if(!empty($api['subscriberid'])){
            	$seed_cspv4_post_result['status'] ='200';
            }else {
            	 $seed_cspv4_post_result['msg'] = '500';
                 $seed_cspv4_post_result['msg_class'] = 'alert-danger';
            }
}

// Hook into save

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_refresh_feedblitz_lists' );

function seed_csvp4_refresh_feedblitz_lists($value){
    if(!empty($value['refresh_feedblitz']) && $value['refresh_feedblitz'] == '1'){
        //Clear cache
        delete_transient('seed_cspv4_feedblitz_lists');
        cspv4_get_feedblitz_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('refresh_feedblitz', 0);
    }

}
