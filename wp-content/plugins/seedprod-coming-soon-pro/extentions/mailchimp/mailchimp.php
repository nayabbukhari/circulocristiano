<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add MailChimp section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'mailchimp'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_mailchimp_section');
}

function seed_cspv4_mailchimp_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('MailChimp', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to MailChimp options. Save after you enter your api key to load your list. <a href="#">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'mailchimp_api_key',
                    'type'      => 'text',
                    'title'     => __( "API Key", 'seedprod' ),
                    'subtitle'  => __('Enter your API Key. <a target="_blank" href="http://admin.mailchimp.com/account/api-key-popup" target="_blank">Get your API key</a>', 'seedprod'),
                ),
                array(
                    'id'        => 'mailchimp_listid',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_mailchimp_lists()
                ),
                array(
                    'id'        => 'refresh_mailchimp',
                    'type'      => 'checkbox',
                    'title'     => __( "Refresh MailChimp Lists", 'seedprod' ),
                    'subtitle'  => __('Check and Save changes to have the lists refreshed above.', 'seedprod'),
                ),
                array(
                    'id'        => 'mailchimp_enable_double_optin',
                    'type'      => 'switch',
                    'title'     => __( "Enable Double Opt-In", 'seedprod' ),
                    'subtitle'  => __('Learn more about <a href="http://kb.mailchimp.com/article/how-does-confirmed-optin-or-double-optin-work">Double Opt-In</a>', 'seedprod'),
                ),
                array(
                    'id'        => 'mailchimp_welcome_email',
                    'type'      => 'switch',
                    'title'     => __( "Send Welcome Email", 'seedprod' ),
                    'subtitle'  => __("If your Double Opt-in is false and this is true, MailChimp will send your lists Welcome Email if this subscribe succeeds - this will not fire if MailChimp ends up updating an existing subscriber. If Double Opt-in is true, this has no effect. Learn more about <a href='http://blog.mailchimp.com/sending-welcome-emails-with-mailchimp/' target='_blank'>Welcome Emails</a>.", 'seedprod'),
                ),
                array(
                    'id'        => 'mailchimp_group_name',
                    'type'      => 'text',
                    'title'     => __( "Group Name", 'seedprod' ),
                    'subtitle'  => __('Optional: Enter the name of the group. Learn more about <a href="http://mailchimp.com/features/groups/" target="_blank">Groups</a>', 'seedprod'),
                ),
                array(
                    'id'        => 'mailchimp_groups',
                    'type'      => 'text',
                    'title'     => __( "Groups", 'seedprod' ),
                    'subtitle'  => __('Optional: Comma delimited list of interest groups to add the email to.', 'seedprod'),
                ),

                array(
                    'id'        => 'mailchimp_update_existing',
                    'type'      => 'switch',
                    'title'     => __( "Update Existing", 'seedprod' ),
                    'subtitle'  => __("Control whether existing subscribers should be updated instead of throwing an error.", 'seedprod'),
                ),
                array(
                    'id'        => 'mailchimp_replace_interests',
                    'type'      => 'switch',
                    'title'     => __( "Replace Interests", 'seedprod' ),
                    'subtitle'  => __("Whether MailChimp will replace the interest groups with the groups provided or add the provided groups to the member's interest groups.", 'seedprod'),
                ),

        	)
    );

    return $sections;
}



/**
 *  Get List from MailChimp
 */
function cspv4_get_mailchimp_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
    $lists = array();
    try{
        if($o['emaillist'] == 'mailchimp' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list')){
        $lists = maybe_unserialize(get_transient('seed_cspv4_mailchimp_lists'));
        if(empty($lists)){
            //var_dump('miss');
            require_once SEED_CSPV4_PLUGIN_PATH.'extentions/mailchimp/seed_cspv4_MCAPI.class.php';

            if(!isset($apikey) && isset($mailchimp_api_key)){
                $apikey = $mailchimp_api_key;
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
                $lists['false'] = __("Unable to load MailChimp lists, check your API Key.", 'seedprod');
            } else {

                foreach ($response['data'] as $k => $v){
                    $lists[$v['id']] = $v['name'];
                }
                if(!empty($lists)){
                   set_transient('seed_cspv4_mailchimp_lists',serialize( $lists ),86400);
                }
            }
        }}
    } catch (Exception $e) {}
    return $lists;
}


/**
 *  Subscribe MailChimp
 */
add_action('seed_cspv4_emaillist_mailchimp', 'seed_cspv4_emaillist_mailchimp_add_subscriber');

function seed_cspv4_emaillist_mailchimp_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
        require_once SEED_CSPV4_PLUGIN_PATH.'extentions/mailchimp/seed_cspv4_MCAPI.class.php';
        require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );

                // If tracking enabled
                if(!empty($enable_reflink)){
                    seed_cspv4_emaillist_database_add_subscriber();
                }

                $apikey = $mailchimp_api_key;
                $api = new seed_cspv4_MCAPI($apikey);
                $listId = $mailchimp_listid;


                if(!empty($mailchimp_enable_double_optin)){
                    $double_optin = true;
                }else{
                    $double_optin = false;
                }

                if(!empty($mailchimp_welcome_email)){
                    $welcome_email=true;
                }else{
                    $welcome_email=false;
                }
                if(!empty($mailchimp_replace_interests)){
                    $replace_interests=true;
                }else{
                    $replace_interests=false;
                }
                if(!empty($mailchimp_update_existing)){
                    $update_existing=true;
                }else{
                    $update_existing=false;
                }


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

                $merge_vars = array(
                    'FNAME'=>$fname,
                    'LNAME'=>$lname,
                    'REFID'=>$seed_cspv4_post_result['ref'],
                    'REFURL'=>$seed_cspv4_post_result['ref_url']
                    );
                
                if(!empty($mailchimp_groups) && !empty($mailchimp_group_name)){
                    $merge_vars['GROUPINGS'] = array(
                        array('name'=>$mailchimp_group_name, 'groups'=>$mailchimp_groups),
                        );
                }

                $retval = $api->listSubscribe( $listId, $email, apply_filters( 'seed_cspv4_mailchimp_merge_vars',$merge_vars),$email_type='html', $double_optin,$update_existing,$replace_interests,$welcome_email);

                if($retval == false){
                    if($api->errorCode == 214 && !empty($enable_reflink)){

                    }elseif(!empty($api->errorMessage)){
                        $seed_cspv4_post_result['msg'] = $api->errorMessage;
                        $seed_cspv4_post_result['msg_class'] = 'alert-info';
                    }
                }else {

                    if($seed_cspv4_post_result['status'] == '600')
                        $seed_cspv4_post_result['status'] ='200';

                    if(empty($seed_cspv4_post_result['status']))
                        $seed_cspv4_post_result['status'] ='200';

                }

}

// Hook into save

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_refresh_mailchimp_lists' );

function seed_csvp4_refresh_mailchimp_lists($value){
    if(!empty($value['refresh_mailchimp']) && $value['refresh_mailchimp'] == '1'){
        //Clear cache
        delete_transient('seed_cspv4_mailchimp_lists');
        cspv4_get_mailchimp_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('refresh_mailchimp', 0);
    }

}
