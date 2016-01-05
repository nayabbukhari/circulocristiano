<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add MailPoet section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'mailpoet'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_mailpoet_section');
}

function seed_cspv4_mailpoet_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('MailPoet', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to MailPoet options. <a href="http://support.seedprod.com/article/77-collecting-emails-with-mailpoet" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'mailpoet_list_id',
                    'type'      => 'select',
                    'title'     => __( "List", 'seedprod' ),
                    'options'   => cspv4_get_mailpoet_lists()
                ),


        	)
    );

    return $sections;
}



/**
 *  Get List from MailPoet
 */
function cspv4_get_mailpoet_lists($apikey = null){
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if(is_plugin_active('wysija-newsletters/index.php')){
        //get the lists and ids
        global $wpdb;
        $wlists = array();
        $tablename = $wpdb->prefix . 'wysija_list';
        if( $wpdb->get_var("SHOW TABLES LIKE '$tablename'") == $tablename ){
            $sql = "SELECT list_id,name FROM $tablename WHERE is_enabled = 1";
            $wlists = $wpdb->get_results($sql);
        }

        $lists = array();

        foreach($wlists as $k=>$v){
            $lists[$v->list_id] = $v->name;
        }
    }else{
      $lists = array('-1'=> 'No Lists Found');
    }
    return $lists;
}


/**
 *  Subscribe MailPoet
 */
add_action('seed_cspv4_emaillist_mailpoet', 'seed_cspv4_emaillist_mailpoet_add_subscriber');

function seed_cspv4_emaillist_mailpoet_add_subscriber($args){
    global $seed_cspv4,$seed_cspv4_post_result;
    extract($seed_cspv4);
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );
    if(is_plugin_active('wysija-newsletters/index.php') && class_exists('WYSIJA')){
        $list_id = $mailpoet_list_id;

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

        //check if the email address is recorded in wysija
        $modelUser= WYSIJA::get('user','model');
        $userData=$modelUser->getOne(array('user_id'),array('email'=>$email));

        if(!$userData){
            //record the email in wysija
            $userHelper= WYSIJA::get('user','helper');
            $data=array('user'=>array('email'=>$email,'firstname'=>$fname,'lastname'=>$lname),'user_list'=>array('list_ids'=>array($list_id)));
            $test = $userHelper->addSubscriber($data);
            if(!empty($enable_reflink)){
                seed_cspv4_emaillist_database_add_subscriber();
            }
            if(empty($seed_cspv4_post_result['status']))
                $seed_cspv4_post_result['status'] ='200';
        }else{
            $user_id=$userData['user_id'];
            $userHelper= WYSIJA::get('user','helper');
            $userHelper->addToLists(array($list_id), $user_id);
            if(!empty($enable_reflink)){
                seed_cspv4_emaillist_database_add_subscriber();
            }
            if(empty($seed_cspv4_post_result['status'])){
                $seed_cspv4_post_result['status'] ='200';
                $seed_cspv4_post_result['msg'] = $txt_already_subscribed_msg;
                $seed_cspv4_post_result['msg_class'] = 'alert-info';
            }
        }
    }
}
