<?php
/**
 * Enqueue scripts and styles
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */
function seed_cspv4_scripts() {
    if(is_user_logged_in()){
        $css = SEED_CSPV4_PLUGIN_URL . 'includes/admin-bar.css';
        wp_register_style(
            'seed_cspv4-admin-bar-css',
            $css ,
            array(),
            time(),
            'all'
        );
        wp_enqueue_style('seed_cspv4-admin-bar-css');
    }
}

/**
 * Get Plugin API value
 */
function seed_get_plugin_api_value($k = null) {
    global $seed_cspv4;
    extract($seed_cspv4);
    if(!empty($plugin_api)){
        $plugin_api = str_replace(array("\n\r","\n"), "&", $plugin_api);
        parse_str($plugin_api, $plugin_api);
        if(array_key_exists($k, $plugin_api)){
            return $plugin_api[$k];
        }else{
            return false;
        }

    }
}


/**
 *  Get IP
 */
function seed_cspv4_get_ip(){
    $ip = '';
    if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) AND strlen($_SERVER['HTTP_X_FORWARDED_FOR'])>6 ){
        $ip = strip_tags($_SERVER['HTTP_X_FORWARDED_FOR']);
    }elseif( !empty($_SERVER['HTTP_CLIENT_IP']) AND strlen($_SERVER['HTTP_CLIENT_IP'])>6 ){
         $ip = strip_tags($_SERVER['HTTP_CLIENT_IP']);
    }elseif(!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR'])>6){
         $ip = strip_tags($_SERVER['REMOTE_ADDR']);
    }//endif
    if(!$ip) $ip="127.0.0.1";
    return strip_tags($ip);
}

/**
 *  Get IP
 */
function seed_cspv4_ref_link(){
    global $seed_cspv4_post_result;
    $ref_link = '';
    if(!empty($seed_cspv4_post_result['ref'])){
        $ref_url = $_SERVER["HTTP_REFERER"];
        if(!empty($ref_url)){
            $ref_url_parts = parse_url($ref_url);
            $port = '';
            if(!empty($ref_url_parts['port'])){
                $port = ':'.$ref_url_parts['port'];
            }
            if(!empty($ref_url_parts['port'])){
            if($ref_url_parts['port'] == '80'){
                $port = '';
            }
            }
            $ref_link = $ref_url_parts['scheme'].'://'.$ref_url_parts['host'].$port.$ref_url_parts['path'];
            $ref_link = $ref_link.'?ref='.$seed_cspv4_post_result['ref'];
        }else{
            $ref_link = $ref_url_parts['scheme'].'://'.$ref_url_parts['host'].$port.$ref_url_parts['path'];
        }
    }
    return $ref_link;
}


/**
 *  Landing Page Postback handler
 */
add_action('seed_cspv4_pre_render','seed_cspv4_postback');


function seed_cspv4_postback(){
    // Get Settings
    global $seed_cspv4;
    extract($seed_cspv4);
    $o = $seed_cspv4;

    //check if it's a post and set global
    global $seed_cspv4_post_result;
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // Spam check, this will be fined in if spam
        if(!empty($_REQUEST['message'])){
            return false;
        }


        // Check field values
        $email = '';
        if(!empty($_REQUEST['email'])){
            $email = $_REQUEST['email'];
        }

        $name = '';
        if(!empty($_REQUEST['name'])){
            $name = $_REQUEST['name'];
        }

        // Get subscribe method

        // Check it we need to validate email
        $bypassed_emaillist = apply_filters('seed_cspv4_bypassed_emaillist',array('gravityforms'));

        if(!in_array($emaillist, $bypassed_emaillist)){
            if(is_email($email) != $email || empty($email)){
                 $seed_cspv4_post_result['status'] = '400';
                 $seed_cspv4_post_result['msg'] = $txt_invalid_email_msg;
                 $seed_cspv4_post_result['msg_class'] = 'alert-danger';

                 $emaillist = '';
            }
        }

        // Check it we need to validate name
        if(!empty($name_field)){
            if(!empty($name_field_required)){
                if(empty($name)){
                     $seed_cspv4_post_result['status'] = '400';
                     $seed_cspv4_post_result['msg'] = $txt_invalid_name_msg;
                     $seed_cspv4_post_result['msg_class'] = 'alert-danger';

                     $emaillist = '';
                }
            }
        }

        // Do email list action
        if(!empty($emaillist)){
            do_action('seed_cspv4_emaillist_'.$emaillist,$_POST);
        }

        //var_dump($seed_cspv4_post_result);
        }
}


/**
  * Create Google Fonts CSS include
  */
function seed_cspv4_get_google_font_css($arg){
    //var_dump($arg);
    $preoutput = '';
    $font_list = '';
    $subset_list = '';
    $link = '';
    if(is_array($arg)){
        //Fonts
        foreach($arg as $v){
            $font_weight = '';
            $font_style = '';
            //var_dump($v);
            if(!empty($v['google']) && ($v['google'] == 'true' || $v['google'] == '1') || !empty($v['font-family']) && $v['font-family'] == 'Open Sans'){

                if(!empty($font_list)){
                    $font_list .= '|';
                }
                if(!empty($v['font-weight'])){
                    $font_weight .= $v['font-weight'];
                }
                if(!empty($v['font-style'])){
                    $font_style .= $v['font-style'];
                }
                if(!empty($v['subsets'])){
                    //$subsets .= $v['subsets'];
                }
                $font_list .= urlencode($v['font-family']).':'.$font_weight.$font_style;
            }
        }
        //Subsets
        foreach($arg as $v){
            if(!empty($v['google']) && $v['google'] == 'true'){
                $subsets = '';
                if(!empty($subset_list)){
                    $subset_list .= ',';
                }
                if(!empty($v['subsets'])){
                    $subsets .= $v['subsets'];
                }
                $subset_list .= $subsets;
            }
        }
    }
    $preoutput = '';
    if(!empty($font_list)){
        $preoutput .= "//fonts.googleapis.com/css?family=";
    }
    $postoutput = '';
    if(!empty($subset_list)){
        $postoutput = '&subsets='.$subset_list;
    }
    if(!empty($font_list)){
        $link = "<!-- Google Fonts CSS -->".PHP_EOL;
        $link .= '<link rel="stylesheet" id="options-google-fonts"  href="'.$preoutput.$font_list.$postoutput.'" type="text/css" media="all" />';
    }

    return $link;
}


function seed_cspv4_extensions() {

	$extensions = array(
		SEED_CSPV4_PLUGIN_PATH.'extentions/mailchimp/mailchimp.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/database/database.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/sendy/sendy.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/mailpoet/mailpoet.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/madmimi/madmimi.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/infusionsoft/infusionsoft.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/icontact/icontact.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/htmlwebform/htmlwebform.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/gravityforms/gravityforms.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/followupemails/followupemails.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/getresponse/getresponse.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/feedburner/feedburner.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/constantcontact/constantcontact.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/campaignmonitor/campaignmonitor.php',
		SEED_CSPV4_PLUGIN_PATH.'extentions/aweber/aweber.php',
        SEED_CSPV4_PLUGIN_PATH.'extentions/drip/drip.php',
        SEED_CSPV4_PLUGIN_PATH.'extentions/mymail/mymail.php',
        SEED_CSPV4_PLUGIN_PATH.'extentions/feedblitz/feedblitz.php',
	);

	$active_extensions = apply_filters( 'seed_cspv4_active_extensions', $extensions );

	foreach ( $active_extensions as $i ) {
		require_once( $i );
	}

} // END seed_cspv4_extensions()
