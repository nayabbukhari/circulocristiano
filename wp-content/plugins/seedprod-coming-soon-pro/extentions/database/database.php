<?php
// * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
// Hook into the cspv4 to fulfill the action
add_action('seed_cspv4_emaillist_database', 'seed_cspv4_emaillist_database_add_subscriber');

function seed_cspv4_emaillist_database_add_subscriber(){
		require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );
	    // Get Settings
	    global $seed_cspv4, $seed_cspv4_post_result;
	    extract($seed_cspv4);
	    $o = $seed_cspv4;

	    // Record reference
	    $ref = '-1';
	    if(!empty($_REQUEST['ref'])){
	        $ref = intval($_REQUEST['ref'],36)-1000;
	    }

        $name = '';
        if(!empty($_REQUEST['name'])){
            $name = $_REQUEST['name'];
        }
        $email = strtolower($_REQUEST['email']);
        $fname = '';
        $lname = '';

        if(!empty($name)){
            $name = seed_cspv4_parse_name($name);
            $fname = $name['first'];
            $lname = $name['last'];
        }

	    // Record user in DB if they do not exist
        global $wpdb;
        $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;

        $sql = "SELECT * FROM $tablename WHERE email = %s";
        $safe_sql = $wpdb->prepare($sql,$email);
        $select_result =$wpdb->get_row($safe_sql);


        if(empty($select_result->email) || $select_result->email != $email){
            $values = array(
                'email' => $email,
                'referrer' => $ref,
                'ip' => seed_cspv4_get_ip(),
                'fname' => $fname,
                'lname' => $lname,
            );
            $format_values = array(
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
            );
            $insert_result = $wpdb->insert(
                $tablename,
                $values,
                $format_values
            );
            // Record ref
            if(!empty($ref)){

                $sql = "UPDATE $tablename SET conversions = conversions + 1 WHERE id = %d";
                $safe_sql = $wpdb->prepare($sql,$ref);
                $update_result =$wpdb->get_var($safe_sql);
            }

        }



        if(isset($insert_result) && $insert_result != false){
        	// Send notice if a new subscriber.
            if($emaillist == 'database' && !empty($database_notifications)){
                $message = home_url() . __(" You have a new email subscriber: ",'seedprod'). $fname.' '.$lname.' '.$email;
				$mresult = '';
                if(empty($database_notifications_emails)) {
                    $mresult = wp_mail( get_option('admin_email'), home_url() . __(' : New Email Subscriber', 'seedprod'), $message);
                }else{
                    $mresult = wp_mail( $database_notifications_emails, home_url() . __(' : New Email Subscriber', 'seedprod'), $message);
                }
            }
			//var_dump($mresult);

            if(empty($seed_cspv4_post_result['status']))
                $seed_cspv4_post_result['status'] = '200';
            $ref = $wpdb->insert_id+1000;
            $seed_cspv4_post_result['ref'] = base_convert($ref, 10, 36);

			$seed_cspv4_post_result['ref_url'] = seed_cspv4_ref_link();

        }else{
        	// Subscriber already exist show stats
            $seed_cspv4_post_result['status'] = '200';
			$seed_cspv4_post_result['msg'] = $txt_already_subscribed_msg;
			$seed_cspv4_post_result['msg_class'] = 'alert-info';
            $ref = $select_result->id+1000;
            $seed_cspv4_post_result['ref'] = base_convert($ref, 10, 36);
            $seed_cspv4_post_result['clicks'] = '0';
            if(!empty($select_result->clicks)){
                $seed_cspv4_post_result['clicks'] = $select_result->clicks;
            }
            $seed_cspv4_post_result['subscribers'] = '0';
            if(!empty($select_result->conversions)){
                $seed_cspv4_post_result['subscribers'] = $select_result->conversions;
            }

            // Conditional Stats
            $rf_url = '';
            $rf_stats = '';
            if($enable_reflink && !empty($seed_cspv4_post_result['ref'])){

                $rf_url = "<br><br>".$txt_stats_referral_url.'<br>'.seed_cspv4_ref_link();
                $rf_stats = '<br><br>'.$txt_stats_referral_stats.'<br>'.$txt_stats_referral_clicks.': '.$seed_cspv4_post_result['clicks'].'<br>'.$txt_stats_referral_subscribers.': '.$seed_cspv4_post_result['subscribers'];
                $seed_cspv4_post_result['msg'] .= $rf_url.$rf_stats;

            }
        }
}

$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'database'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_dynamic_section_database');
}

function seed_cspv4_dynamic_section_database($sections) {

    global $seed_cspv4;
    //var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('Database Options', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to the database options. <a target="_blank" href="http://support.seedprod.com/article/38-collecting-emails-in-the-database">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'database_notifications',
                    'type'      => 'switch',
                    'title'     => __( "Enable New Subscriber Notifications", 'seedprod' ),
                    'subtitle'  => __( 'Get an email notification when some subscribes.' , 'seedprod' ),
                ),
                array(
                    'id'        => 'database_notifications_emails',
                    'type'      => 'text',
                    'title'     => __( "Send Notifications to this Email", 'seedprod' ),
                    'subtitle'  => __( 'Separate multiple emails with a comma. If no email is defined, notifications while be sent to the admin email.' , 'seedprod' ),
                ),

            )
    );

    return $sections;
}
