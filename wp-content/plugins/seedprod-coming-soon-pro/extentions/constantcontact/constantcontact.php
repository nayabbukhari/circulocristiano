<?php
/**
 *  Add ConstantContact section
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'constantcontact'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_constantcontact_section');
}

function seed_cspv4_constantcontact_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('ConstantContact', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to Constant Contact options. Save after you enter your username and password to load your list. <a href="http://support.seedprod.com/article/35-collecting-emails-with-constant-contact" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'constantcontact_username',
                    'type'      => 'text',
                    'title'     => __( "Username", 'seedprod' ),
                    'subtitle'  => __('Enter your Constant Contact username.', 'seedprod'),
                ),
                array(
                    'id'        => 'constantcontact_password',
                    'type'      => 'password',
                    'title'     => __( "Password", 'seedprod' ),
                    'subtitle'  => __('Enter your Constant Contact password.', 'seedprod'),
                ),
                array(
                    'id'        => 'constantcontact_listid',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_constantcontact_lists()
                ),
                array(
                    'id'        => 'refresh_constantcontact',
                    'type'      => 'checkbox',
                    'title'     => __( "Refresh Constant Contact Lists", 'seedprod' ),
                    'subtitle'  => __('Check and Save changes to have the lists refreshed above.', 'seedprod'),
                ),

        	)
    );

    return $sections;
}



/**
 *  Get List from ConstantContact
 */
function cspv4_get_constantcontact_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
        $lists = array();
        if($o['emaillist'] == 'constantcontact' || ( defined('DOING_AJAX') && DOING_AJAX && isset($_GET['action']) && $_GET['action'] == 'seed_cspv4_refresh_list' )){
        $lists = maybe_unserialize(get_transient('seed_cspv4_constantcontact_lists'));
        if(empty($lists)){
            //var_dump('miss');
            if (class_exists('cc')) {
                //trigger_error("Duplicate: Another Constant Contact client library is already in scope.", E_USER_WARNING);
            }
            else {
                require_once SEED_CSPV4_PLUGIN_PATH.'extentions/constantcontact/seed_cspv4_class.cc.php';
            }


            if(!isset($username) && isset($constantcontact_username)){
                $username = $constantcontact_username;
                $password = $constantcontact_password;
            }

            if(empty($username) || empty($password)){
                return array();
            }

            $api = new cc($username, $password);

            $response = $api->get_all_lists();
            if($response){
                foreach($response as $k => $v){
                    $lists[$v['id']] = $v['Name'];
                }
                if(!empty($lists)){
                   set_transient('seed_cspv4_constantcontact_lists',serialize( $lists ),86400);
                }
            }else{
                $lists['false'] = __("Unable to load Constant Contact lists", 'seedprod');
            }

        }}
        return $lists;
}


/**
 *  Subscribe ConstantContact
 */
add_action('seed_cspv4_emaillist_constantcontact', 'seed_cspv4_emaillist_constantcontact_add_subscriber');

function seed_cspv4_emaillist_constantcontact_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
                if (class_exists('cc')) {
                    //trigger_error("Duplicate: Another Constant Contact client library is already in scope.", E_USER_WARNING);
                }
                else {
                    require_once SEED_CSPV4_PLUGIN_PATH.'extentions/constantcontact/seed_cspv4_class.cc.php';
                }
                require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );

                // If tracking enabled
                if(!empty($enable_reflink)){
                    seed_cspv4_emaillist_database_add_subscriber();
                }

                $username = $constantcontact_username;
                $password = $constantcontact_password;

                $api = new cc($username, $password);
                $listId = $constantcontact_listid;

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

                $contact_list = $listId;
                $extra_fields = array();

                // check if the contact exists
                $contact = $api->query_contacts($email);

                // uncomment this line if the user makes the action themselves
                $api->set_action_type('contact');
                $extra_fields = array(
                    'FirstName' => $fname,
                    'LastName' => $lname,
                );
                if($contact){
                    $contact_ext = $api->get_contact($contact['id']);
                    if (in_array($contact_list, $contact_ext['lists'])) {
                        $seed_cspv4_post_result['status'] = '500';
                        $seed_cspv4_post_result['msg'] = $txt_already_subscribed_msg;
                        $seed_cspv4_post_result['msg_class'] = 'alert-info';
                    }
                    $lists = $contact_ext['lists'] + array($contact_list);
                    $updated = $api->update_contact($contact['id'],$email, $lists, $extra_fields);
                    if($updated){
                        //$this->add_subscriber($email,$fname,$lname);
                        if(empty($seed_cspv4_post_result['status']))
                            $seed_cspv4_post_result['status'] ='200';
                    }else{
                        $seed_cspv4_post_result['status'] = '500';
                        $seed_cspv4_post_result['msg'] = $txt_already_subscribed_msg;
                        $seed_cspv4_post_result['msg_class'] = 'alert-info';
                    };
                }else{
                    $new_id = $api->create_contact($email, $contact_list, $extra_fields);
                    if($new_id){
                        // if(!empty($enable_reflink)){
                        //     seed_cspv4_emaillist_database_add_subscriber();
                        // }
                        if(empty($seed_cspv4_post_result['status']))
                            $seed_cspv4_post_result['status'] ='200';
                    }else{
                        $seed_cspv4_post_result['status'] = '500';
                        $seed_cspv4_post_result['msg'] = $txt_already_subscribed_msg;
                        $seed_cspv4_post_result['msg_class'] = 'alert-info';
                    };
                };
}


// Hook into save

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_refresh_constantcontact_lists' );

function seed_csvp4_refresh_constantcontact_lists($value){
    if(!empty($value['refresh_constantcontact']) && $value['refresh_constantcontact'] == '1'){
        //Clear cache
        delete_transient('seed_cspv4_constantcontact_lists');
        cspv4_get_constantcontact_lists();
        // Reset Field
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('refresh_constantcontact', 0);
    }

}
