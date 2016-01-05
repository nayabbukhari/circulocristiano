<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
/**
 *  Add InfusionSoft section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'infusionsoft'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_infusionsoft_section');
}

function seed_cspv4_infusionsoft_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('InfusionSoft', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to InfusionSoft options. <a href="http://support.seedprod.com/article/74-collecting-emails-with-infusionsoft" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'infusionsoft_app',
                    'type'      => 'text',
                    'title'     => __( "App Name", 'seedprod' ),
                    'subtitle'  => __('Enter your app name.', 'seedprod'),
                ),
                array(
                    'id'        => 'infusionsoft_api_key',
                    'type'      => 'text',
                    'title'     => __( "API Key", 'seedprod' ),
                    'subtitle'  => __('Enter your api key. Learn how to <a href="http://ug.infusionsoft.com/article/AA-00442/0/How-do-I-enable-the-Infusionsoft-API-and-generate-an-API-Key.html" target="_blank">generate your Infusionsoft api key</a>.', 'seedprod'),
                ),
                array(
                    'id'        => 'infusionsoft_tag_id',
                    'type'      => 'text',
                    'title'     => __( "Tag IDs", 'seedprod' ),
                    'subtitle'  => __('Enter the Tag IDs seperated by commas. Tag IDs can be founds in Infusionsoft: Menu -> CRM -> Settings -> Tags ', 'seedprod'),
                ),


        	)
    );

    return $sections;
}


/**
 *  Subscribe InfusionSoft
 */
add_action('seed_cspv4_emaillist_infusionsoft', 'seed_cspv4_emaillist_infusionsoft_add_subscriber');

function seed_cspv4_emaillist_infusionsoft_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
    require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );
    if(!class_exists('xmlrpc_client')){
        require_once SEED_CSPV4_PLUGIN_PATH.'extentions/infusionsoft/xmlrpc-2.0/lib/xmlrpc.inc';
    }

    // If tracking enabled
    if(!empty($enable_reflink)){
        seed_cspv4_emaillist_database_add_subscriber();
    }

    $app = $infusionsoft_app;
    $api_key = $infusionsoft_api_key;
    $tag_id = $infusionsoft_tag_id;

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


    ###Set our Infusionsoft application as the client###
    $client = new xmlrpc_client("https://".$app.".infusionsoft.com/api/xmlrpc");

    ###Return Raw PHP Types###
    $client->return_type = "phpvals";

    ###Dont bother with certificate verification###
    $client->setSSLVerifyPeer(FALSE);

    ###Our API Key###
    $key = $api_key;


    ###Build a Key-Value Array to store a contact###
    $contact = array(
            "FirstName" =>  $fname,
            "LastName" =>   $lname,
            "Email" =>      $email,
        );

    $optin_reason = 'Coming Soon Page';


    ###Set up the call###
    $call = new xmlrpcmsg("ContactService.add", array(
            php_xmlrpc_encode($key),        #The encrypted API key
            php_xmlrpc_encode($contact)     #The contact array
        ));
    $call2 = new xmlrpcmsg("APIEmailService.optIn", array(
            php_xmlrpc_encode($key),        #The encrypted API key
            php_xmlrpc_encode($email),     #The contact array
            php_xmlrpc_encode($optin_reason)     #The contact array
        ));


    ###Send the call###
        $result = $client->send($call);
        $result2 = $client->send($call2);

        if(!empty($tag_id)){
            $tags = explode(",",$tag_id);
            //var_dump($tags);
            foreach($tags as $t){
            $call3 = new xmlrpcmsg("ContactService.addToGroup", array(
                    php_xmlrpc_encode($key),        #The encrypted API key
                    php_xmlrpc_encode($result->value()),     #The contact ID
                    php_xmlrpc_encode($t)     #The Follow up sequence ID
                ));
            $result3 = $client->send($call3);
            }
            //var_dump($result3);
        }


    ###Check the returned value to see if it was successful and set it to a variable/display the results###
    if(!$result->faultCode()) {
        // if(!empty($enable_reflink)){
        //     seed_cspv4_emaillist_database_add_subscriber();
        // }
        if(empty($seed_cspv4_post_result['status']))
            $seed_cspv4_post_result['status'] ='200';
        // $conID = $result->value();
        // print "Contact added was " . $conID;
        // print "<BR>";
    } else {
        $seed_cspv4_post_result['status'] = '500';
        // print $result->faultCode() . "<BR>";
        // print $result->faultString() . "<BR>";die();
    }
}
