<?php
// * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add GravityForms section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'gravityforms'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_gravityforms_section');
}

add_filter('seed_cspv4_enable_wp_head_footer_list','seed_cspv4_enable_wp_head_footer_gravityforms');

function seed_cspv4_enable_wp_head_footer_gravityforms($arr){
  $arr[] = 'gravityforms';
  return $arr;
}


function seed_cspv4_gravityforms_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('GravityForms', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to Gravity Forms options. <a href="http://support.seedprod.com/article/25-embedding-gravity-forms" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'gravityforms_enable_thankyou_page',
                    'type'      => 'switch',
                    'title'     => __( "Override Gravity Form's confirmation page", 'seedprod' ),
                    'subtitle'  => __("Redirect to this plugin's Thank You page instead of the default Gravity Forms Confirmation page.", 'seedprod'),
                ),
                array(
                    'id'        => 'gravityforms_form_id',
                    'type'      => 'select',
                    'title'     => __( "Form", 'seedprod' ),
                    'options'   => cspv4_get_gravityforms_forms()
                ),


        	)
    );

    return $sections;
}


function cspv4_get_gravityforms_forms(){
    if(class_exists('RGFormsModel')){
      $forms = array();
      $gforms = RGFormsModel::get_forms(null, "title");
      foreach($gforms as $k=>$v){
        $forms[$v->id] = $v->title;
      }
    }else{
      $forms = array('-1'=> 'No Forms Found');
    }
    return $forms;
}


add_action('gform_after_submission', 'cspv4_after_gravity_subscribed_record_record_into_cspv4', 11, 2);

function cspv4_after_gravity_subscribed_record_record_into_cspv4($entry, $form) {
    global $seed_cspv4,$seed_cspv4_post_result;
    $o = $seed_cspv4;
    $status = $o['status'];
    if(((!empty($status) && $status === '1') || (!empty($status) && $status === '2')) || (isset($_GET['seed_cspv4_preview']) && $_GET['seed_cspv4_preview'] == 'true')){
    if($form['id'] == $o['gravityforms_form_id']){

        if($o['gravityforms_enable_thankyou_page']){
            $data = array();
            foreach($form['fields'] as $k => $v){
                if($v['type'] == 'name'){
                    if(!empty($entry[$v['id'].'.3']))
                        $data['fname'] = $entry[$v['id'].'.3'];
                    if(!empty($entry[$v['id'].'.6']))
                    $data['lname'] = $entry[$v['id'].'.6'];
                }
                if($v['type'] == 'email'){
                    if(!empty($entry[$v['id']]))
                        $data['email'] = $entry[$v['id']];
                }
            }

            if(!empty($data)){
                $data['gf'] = '1';
            }

            $seed_cspv4_post_result['post'] = 'true';
            $seed_cspv4_post_result['status'] = '200';

            $url = $entry['source_url'];

            $query = http_build_query($data);

            $separator = (parse_url($url, PHP_URL_QUERY) == NULL) ? '?' : '&';
            $url .= $separator . $query;

            //wp_redirect($url);
            //exit();
        }
    }
    }
}
