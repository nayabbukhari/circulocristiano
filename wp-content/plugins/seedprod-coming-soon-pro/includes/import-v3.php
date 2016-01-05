<?php

function seed_cspv4_import_v3(){
$seed_cspv4_settings_map = array(
        // v4 => v3
        'status', //x
        'enable_maintenance_php', //x
        'api_key', //x
        'logo', //x
        'headline', //x
        'description', //x
        'emaillist', //x
        'thankyou_msg', //x
        'name_field', //x
        'share_buttons', //x
        'tweet_text', //x
        'facebook_thumbnail', //x
        'pinterest_thumbnail', //x
        'enable_reflink', //x
        'enable_progressbar', //x
        'progress_bar_start_date', //x
        'progress_bar_end_date', //x
        'progressbar_percentage', //x
        'progressbar_effect', //x
        'enable_countdown', //x
        'countdown_date', //x
        'countdown_time_hour', //x
        'countdown_time_minute', //x
        'countdown_format', //x
        'countdown_launch', //x
        'txt_countdown_days', //x
        'txt_countdown_day', //x
        'txt_countdown_hours', //x
        'txt_countdown_hour', //x
        'txt_countdown_minutes', //x
        'txt_countdown_minute', //x
        'txt_countdown_seconds', //x
        'txt_countdown_second', //x
        'social_profiles_type', //x
        'social_profiles_size', //x
        'social_profiles', //x
        'favicon', //x
        'seo_title', //x
        'seo_description', //x
        'ga_analytics', //x
        'footer_credit_img', //x
        'footer_credit_link', //x
        'background', //x
        'bg_slideshow', //x
        'bg_slideshow_randomize', //x
        'bg_slideshow_slide_speed', //x
        'bg_slideshow_slide_transition', //x
        'bg_slideshow_images', //x
        'bg_video', //x
        'bg_video_url', //x
        'headline_font', //x
        'text_font', //x
        'button_font', //x
        'typekit_id', //x
        'text_effects',//x
        'container_color', //x
        'container_position', //x
        'container_radius', //x
        'container_border',
        'container_effects',
        'custom_css', //x
        'txt_subscribe_button', //x
        'txt_email_field', //x
        'txt_name_field', //x
        'txt_success_msg', //x
        'txt_already_subscribed_msg', //x
        'txt_invalid_email_msg', //x
        'txt_api_error_msg', //x
        'client_view', //x
        'ip_access', //x
        'include_roles', //x
        'include_page', //x
        'include_url_pattern', //x
        'exclude_url_pattern', //x
        'enable_wp_head_footer', //x
        'header_scripts', //x
        'footer_scripts', //x
        'conversion_scripts', //x
        'mailchimp_api_key',
        'mailchimp_listid',
        'mailchimp_enable_double_optin',
        'mailchimp_welcome_email',
        'mailchimp_group_name',
        'mailchimp_groups',
        'mailchimp_replace_interests',
        'aweber_authorization_code',
        'aweber_listid',
        'campaignmonitor_api_key',
        'campaignmonitor_clientid',
        'campaignmonitor_listid',
        'constantcontact_username',
        'constantcontact_password',
        'constantcontact_listid',
        'database_notifications',
        'database_notifications_emails',
        'feedburner_addr',
        'feedburner_local',
        'getresponse_api_key',
        'getresponse_listid',
        'gravityforms_enable_thankyou_page',
        'gravityforms_form_id',
        'html_integration',
        'icontact_username',
        'icontact_password',
        'icontact_listid',
        'infusionsoft_app',
        'infusionsoft_api_key',
        'madmimi_api_key',
        'madmimi_username',
        'madmimi_listid',
        'mailpoet_list_id',
        'sendy_url',
        'sendy_list_id',
    );

    $font_map = array(
            '_arial'         => 'Arial, Helvetica, sans-serif',
            '_arial_black'    => "'Arial Black', Gadget, sans-serif",
            '_georgia'      => "Georgia, serif",
            '_helvetica_neue' => "Arial, Helvetica, sans-serif",
            '_impact'        => "Impact, Charcoal, sans-serif",
            '_lucida'        =>  "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
            '_palatino'       => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
            '_tahoma'         => "Tahoma,Geneva, sans-serif",
            '_times' => "'Times New Roman', Times,serif",
            '_trebuchet' => "'Trebuchet MS', Helvetica, sans-serif" ,
            '_verdana'=> "Verdana, Geneva, sans-serif",
        );

    // Get settings
    $s1 = get_option('seed_csp3_settings_1');
    $s2 = get_option('seed_csp3_settings_2');
    $s3 = get_option('seed_csp3_settings_4');
    if(empty($s1))
        $s1 = array();

    if(empty($s2))
        $s2 = array();

    if(empty($s3))
        $s3 = array();

    $options = $s1 + $s2 + $s3;
    $o3 = $options;
    $o4 = get_option('seed_cspv4');

    // Load v3 settings into v4
    if(!empty($o3)){
    foreach($seed_cspv4_settings_map as $k){
        extract($o3);
        switch($k){
            case 'api_key':
                $seed_csp3_settings_1 = get_option('seed_csp3_settings_1');
                $seed_csp3_api_key = '';
                if(isset($seed_csp3_settings_1['api_key'])){
                    $seed_csp3_api_key = $seed_csp3_settings_1['api_key'];
                }
                if(defined('SEED_CSP_API_KEY')){
                    $seed_csp3_api_key = SEED_CSP_API_KEY;
                }
                $o4[$k] = $seed_csp3_api_key;
                break;
            case 'logo':
                $o4[$k]['url'] = $logo;
                break;
            case 'name_field':
                if(!empty($fields))
                    $o4[$k] = 1;
                break;
            case 'share_buttons':
                $o4[$k] = array(
                    'twitter' => '',
                    'facebook' => '',
                    'googleplus' => '',
                    'linkedin' => '',
                    'pinterest' => '',
                    'tumblr' => '',
                );
                if(in_array('0',$share_buttons['buttons']))
                    $o4[$k]['twitter'] = '1';
                if(in_array('1',$share_buttons['buttons']))
                    $o4[$k]['facebook'] = '1';
                if(in_array('2',$share_buttons['buttons']))
                    $o4[$k]['googleplus'] = '1';
                if(in_array('3',$share_buttons['buttons']))
                    $o4[$k]['linkedin'] = '1';
                if(in_array('4',$share_buttons['buttons']))
                    $o4[$k]['pinterest'] = '1';
                if(in_array('5',$share_buttons['buttons']))
                    $o4[$k]['tumblr'] = '1';
                break;
            case 'tweet_text':
                if(!empty($share_buttons['tweet_text']))
                    $o4[$k] = $share_buttons['tweet_text'];
                break;
            case 'facebook_thumbnail':
                if(!empty($share_buttons['facebook_img'])){
                $o4['facebook_thumbnail'] = array(
                    'url' =>$share_buttons['facebook_img'],
                    'thumbnail' => $share_buttons['facebook_img'],
                    'id' => '',
                    'width' => '',
                    'height' => ''
                    );
                }
                break;
            case 'pinterest_thumbnail':
                if(!empty($share_buttons['pinterest_img'])){
                $o4['pinterest_thumbnail'] = array(
                    'url'=>$share_buttons['pinterest_img'],
                    'thumbnail' => $share_buttons['pinterest_img'],
                    'id' => '',
                    'width' => '',
                    'height' => ''
                    );
                }
                break;
            case 'bg_slideshow':
                if(!empty($bg_slideshow))
                    $o4[$k] = '1';
                break;
            case 'bg_video':
                if(!empty($bg_video))
                    $o4[$k] = '1';
                break;
            case 'progress_bar_start_date':
                if(!empty($progressbar_date_range['start_year']))
                    $o4[$k] = $progressbar_date_range['start_month'].'/'.$progressbar_date_range['start_day'].'/'.$progressbar_date_range['start_year'];
                break;
            case 'progress_bar_end_date':
                if(!empty($progressbar_date_range['end_year']))
                    $o4[$k] = $progressbar_date_range['end_month'].'/'.$progressbar_date_range['end_day'].'/'.$progressbar_date_range['end_year'];
                break;
            case 'countdown_date':
                if(!empty($countdown_date['hour']))
                    $o4[$k] = $countdown_date['month'].'/'.$countdown_date['day'].'/'.$countdown_date['year'];
                break;
            case 'countdown_time_hour':
                if(!empty($countdown_date['hour']))
                    $o4[$k] = $countdown_date['hour'];
                break;
            case 'countdown_time_minute':
                if(!empty($countdown_date['minute']))
                    $o4[$k] = $countdown_date['minute'];
                break;
            case 'social_profiles_type':
                    $o4[$k] = 'image';
                break;
            case 'social_profiles_size':
                    if($social_media_icon_size == '16')
                        $o4[$k] = 'small';
                    if($social_media_icon_size == '24')
                        $o4[$k] = 'medium';
                    if($social_media_icon_size == '32')
                        $o4[$k] = 'large';
                break;
            case 'social_profiles':
                    if(!empty($social_profiles['Facebook']))
                        $o4[$k]['facebook'] = $social_profiles['Facebook'];
                    if(!empty($social_profiles['Twitter']))
                        $o4[$k]['twitter'] = $social_profiles['Twitter'];
                    if(!empty($social_profiles['Linkedin']))
                        $o4[$k]['linkedin'] = $social_profiles['Linkedin'];
                    if(!empty($social_profiles['GooglePlus']))
                        $o4[$k]['googleplus'] = $social_profiles['GooglePlus'];
                    if(!empty($social_profiles['YouTube']))
                        $o4[$k]['youtube'] = $social_profiles['YouTube'];
                    if(!empty($social_profiles['Flickr']))
                        $o4[$k]['flickr'] = $social_profiles['Flickr'];
                    if(!empty($social_profiles['Vimeo']))
                        $o4[$k]['vimeo'] = $social_profiles['Vimeo'];
                    if(!empty($social_profiles['Pinterest']))
                        $o4[$k]['pinterest'] = $social_profiles['Pinterest'];
                    if(!empty($social_profiles['Instagram']))
                        $o4[$k]['instagram'] = $social_profiles['Instagram'];
                    if(!empty($social_profiles['Foursquare']))
                        $o4[$k]['foursquare'] = $social_profiles['Foursquare'];
                    // if(!empty($social_profiles['Skype']))
                    //     $o4[$k]['skype'] = $social_profiles['Skype'];
                    if(!empty($social_profiles['RSS']))
                        $o4[$k]['rss'] = $social_profiles['RSS'];
                    if(!empty($social_profiles['Email']))
                        $o4[$k]['email'] = $social_profiles['Email'];

                break;
            case 'favicon':
                if(!empty($favicon)){
                $o4['favicon'] = array(
                    'url'=>$favicon,
                    'thumbnail' => $favicon,
                    'id' => '',
                    'width' => '',
                    'height' => ''
                    );
                }
                break;
            case 'footer_credit_img':
                if(!empty($footer_credit_img)){
                $o4['footer_credit_img'] = array(
                    'url'=>$footer_credit_img,
                    'thumbnail' => $footer_credit_img,
                    'id' => '',
                    'width' => '',
                    'height' => ''
                    );
                }
                break;
            case 'ga_analytics':
                    if(!empty($ga_analytics))
                    $o4[$k] = "

                    <script>


                  var _gaq = _gaq || [];
				_gaq.push(['_setAccount', '".$ga_analytics."']);
				_gaq.push(['_setAllowLinker', true]);
				_gaq.push(['_trackPageview']);

				(function() {
					var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();

                </script>
                    ";
                break;
            case 'background':
                    $o4[$k]['background-color'] = $bg_color;
                    $o4[$k]['background-image'] = $bg_image;
                    $o4[$k]['background-repeat'] = $bg_repeat;
                    $o4[$k]['background-position'] = $bg_position;
                    $o4[$k]['background-attachment'] = $bg_attahcment;
                    if(!empty($bg_cover))
                        $o4[$k]['background-size'] = 'cover';
                break;

            case 'bg_slideshow_images':
                    $urls = explode("\n", str_replace("\r", "", $bg_slideshow_images));
                    $c = 0;
                    foreach($urls as $j){
                        $o4[$k][$c]['url'] = $j ;
                        $c++;
                    }

                break;
            case 'headline_font':
            if(array_key_exists($headline_font,$font_map)){
                $headline_font = $font_map[$headline_font];
            }
                    $o4[$k]['font-family'] = $headline_font;
                    $o4[$k]['color'] = $headline_color;
                break;
            case 'text_font':
            if(array_key_exists($text_font,$font_map)){
                $text_font = $font_map[$text_font];
            }

                $o4[$k]['font-family'] = $text_font;
                $o4[$k]['color'] = $text_color;
                break;
            case 'button_font':
            if(array_key_exists($button_font,$font_map)){
                $button_font = $font_map[$button_font];
            }

                $o4[$k]['font-family'] = $button_font;
                $o4[$k]['color'] = $link_color;
                break;

            case 'text_effects':
                if(!empty($text_effect))
                    $o4[$k] = array('inset');
                break;
            case 'container_color':
                $o4[$k] = array('alpha' => '','color'=> '');
                if(empty($enable_container)){
                    $o4[$k]['alpha'] = '0.00';
                }else{
                    $o4[$k]['alpha'] = $container_effect['opacity_level'];
                }
                $o4[$k]['color'] = $container_color;
                break;
            case 'container_radius':
                //var_dump($container_effect['radius']);
                if($container_effect['radius'] > 0)
                    $o4[$k] = $container_effect['radius'] + 1;
                break;
            case 'container_border':
                $o4[$k] = array('border-top' => '','border-right' => '','border-left' => '','border-bottom' => '','border-color' => '',);

                if($container_effect['thickness'] > 0){
                    $o4[$k]['border-top'] = $container_effect['thickness'];
                    $o4[$k]['border-right'] = $container_effect['thickness'];
                    $o4[$k]['border-left'] = $container_effect['thickness'];
                    $o4[$k]['border-bottom'] = $container_effect['thickness'];
                }else{
                    $o4[$k]['border-top'] ='0';
                    $o4[$k]['border-right'] = '0';
                    $o4[$k]['border-left'] = '0';
                    $o4[$k]['border-bottom'] = '0';
                }
                    if(!empty($container_effect['border_color']))
                        $o4[$k]['border-color'] = $container_effect['border_color'];
                break;
            case 'container_effects':
                if(in_array('0',$container_effect['effects']))
                    $o4[$k] = array('dropshadow');
                break;
                if(in_array('1',$container_effect['effects']))
                    $o4[$k] = array('glow');
                break;

            case 'txt_countdown_days':
                $o4[$k] = $countdown_days;
                break;
            case 'txt_countdown_day':
                $o4[$k] = $countdown_day;
                break;
            case 'txt_countdown_hours':
                $o4[$k] = $countdown_hours;
                break;
            case 'txt_countdown_hour':
                $o4[$k] = $countdown_hour;
                break;
            case 'txt_countdown_minutes':
                $o4[$k] = $countdown_minutes;
                break;
            case 'txt_countdown_minute':
                $o4[$k] = $countdown_minute;
                break;
            case 'txt_countdown_seconds':
                $o4[$k] = $countdown_seconds;
                break;
            case 'txt_countdown_second':
                $o4[$k] = $countdown_second;
                break;
            case 'txt_subscribe_button':
                $o4[$k] = $txt_1;
                break;
            case 'txt_email_field':
                $o4[$k] = $txt_2;
                break;
            case 'txt_name_field':
                $o4[$k] = $txt_fname;
                break;
            case 'txt_success_msg':
                $o4[$k] = $txt_5;
                break;
            case 'txt_already_subscribed_msg':
                $o4[$k] = $txt_3;
                break;
            case 'txt_invalid_email_msg':
                $o4[$k] = $txt_4;
                break;
            case 'txt_api_error_msg':
                $o4[$k] = $txt_6;
                break;
            default:
                if(!empty($o3[$k]))
                    $o4[$k] = $o3[$k];
        }

    }
    }

    // Update v4 setting
    update_option('seed_cspv4', $o4);
}
