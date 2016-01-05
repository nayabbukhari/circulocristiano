<?php
// Copyright 75nineteen Media LLC (scott@75nineteen.com)

/**
 *  Add Settings section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'followupemails'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_followupemails_section');
}

function seed_cspv4_followupemails_section($sections) {
    global $seed_cspv4;

    $sections[] = array(
        'title' => __('Follow-Up Emails', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to the WordPress Users Database to send Follow-up Emails after signing up. Requires <a href="http://www.75nineteen.com/woocommerce/follow-up-email-autoresponder/?utm_source=SeedProd&utm_medium=ComingSoonPro&utm_campaign=IntegrationLink">Follow-Up Emails</a> to be installed.</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
            array(
                'id'        => 'followupemails_email_id',
                'type'      => 'select',
                'title'     => __( "Email", 'seedprod' ),
                'options'   => cspv4_get_followupemails_signup_emails()
            ),


        )
    );

    return $sections;
}

add_action('seed_cspv4_emaillist_followupemails', 'seed_cspv4_emaillist_followupemails_queue_email');

function seed_cspv4_emaillist_followupemails_queue_email() {
    global $wpdb, $seed_cspv4, $seed_cspv4_post_result;
    extract($seed_cspv4);
    require_once( SEED_CSPV4_PLUGIN_PATH.'lib/nameparse.php' );

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

    if ( email_exists( $email ) ) {
        // Subscriber already exist show stats
        $seed_cspv4_post_result['status'] = '200';
        $seed_cspv4_post_result['msg'] = $txt_already_subscribed_msg;
        $seed_cspv4_post_result['msg_class'] = 'alert-info';
        $seed_cspv4_post_result['clicks'] = '0';
    } else {
        $user_id = wp_insert_user( array(
            'user_login'    => $email,
            'user_email'    => $email,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'user_pass'     => wp_generate_password()
        ) );

        if(empty($seed_cspv4_post_result['status']))
            $seed_cspv4_post_result['status'] = '200';


    }

}

function cspv4_get_followupemails_signup_emails(){
    global $wpdb;

    $emails = array();
    $email_rows = array();

    if (class_exists('FUE_Email')){
        $email_rows = $wpdb->get_results(
            "SELECT p.ID, p.post_title
                FROM {$wpdb->posts} p, {$wpdb->postmeta} pm
                WHERE p.post_type = 'follow_up_email'
                AND p.post_status = '". FUE_Email::STATUS_ACTIVE ."'
                AND pm.post_id = p.ID
                AND pm.meta_key = '_interval_type'
                AND pm.meta_value = 'signup'
                ORDER BY menu_order ASC"
        );
    }

    foreach ( $email_rows as $email ) {
        //$email = new FUE_Email( $email_id );
        $emails[ $email->ID ] = $email->post_title;
    }

    return $emails;
}
