<?php
/*
Plugin Name: SeedProd MyMail
Plugin URI: http://www.seedprod.com
Description: SeedProd MyMail Add On
Version:  1.0.0
Author: SeedProd
Author URI: http://www.seedprod.com
TextDomain: seedprod
License: GPLv2
Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
*/

define( 'SEED_CSPV4_MYMAIL_VERSION', '1.0.0' ); // Plugin Version Number. Recommend you use Semantic Versioning http://semver.org/

/**
 *  Add mymail to the menu
 */
add_filter('seed_cspv4_providers','seed_cspv4_mymail_providers');

function seed_cspv4_mymail_providers($v){
  $v['mymail'] = 'MyMail';
  return $v;
}

/**
 *  programatically enable wp head and footer
 */
add_filter('seed_cspv4_enable_wp_head_footer_list','seed_cspv4_enable_wp_head_footer_mymail');

function seed_cspv4_enable_wp_head_footer_mymail($arr){
  $arr[] = 'mymail';
  return $arr;
}
/**
 *  exclude confirmation page
 */
add_filter('seed_cspv4_default_excludes_pattern','seed_cspv4_maymail_default_excludes_pattern');
function seed_cspv4_maymail_default_excludes_pattern($pattern){
  $pattern .= '|confirm';
  return $pattern;
}

/**
 *  add form css
 */
add_filter('seed_cspv4_head','seed_cspv4_mymail_head');

function seed_cspv4_mymail_head($output){
  require_once(SEED_CSPV4_PLUGIN_PATH.'lib/seed_cspv4_lessc.inc.php');
  global $seed_cspv4;
  extract($seed_cspv4);

  $css = "
  @primaryColor: {$button_font['color']};
  @secondaryColor: darken(@primaryColor, 15%);
  .mymail-wrapper label{
    font-weight:normal;
  }
  .seed-csp4 input{
    border-width:1px;
    border-radius: 4px;
  }
  .submit-button:hover{
    background:@secondaryColor !important;
  }
  .submit-button{
    border-radius: 4px;
  }
  .mymail-form-info p{
    color: #fff !important;
  }
  .mymail-wrapper{
    margin-bottom:10px;
  }

  ";

  ob_start();
  $less = new seed_cspv4_lessc();
  $style = $less->parse($css);
  echo $style;
  $new_output = ob_get_clean();
  $output .= '<style>'.$new_output.'</style>';
  return $output;
}



/**
 *  Add mymail section to admin
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'mymail'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_mymail_section');
}

function seed_cspv4_mymail_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('MyMail', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to MyMail options. This email provider will bypass the coming soon page and referral tracking if enabled.</p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                // array(
                //     'id'        => 'mymail_enable_thankyou_page',
                //     'type'      => 'switch',
                //     'title'     => __( "Override MyMail's confirmation page", 'seedprod' ),
                //     'subtitle'  => __("Redirect to this plugin's Thank You page instead of the default MyMail's Confirmation page.", 'seedprod'),
                // ),
                array(
                    'id'        => 'mymail_form_id',
                    'type'      => 'select',
                    'title'     => __( "Form", 'seedprod' ),
                    'options'   => cspv4_get_mymail_forms()
                ),


        	)
    );

    return $sections;
}


/**
 *  Return List of forms
 */
function cspv4_get_mymail_forms(){
    if(class_exists('mymail')){
      $forms = array();
      $mforms = mymail_option('forms');
      foreach($mforms as $k=>$v){
        $forms[$v['id']] = $v['name'];
      }
    }else{
      $forms = array('-1'=> 'No Forms Found');
    }
    return $forms;
}


/**
 *  Output form on landing page
 */
add_filter('seed_cspv4_show_form_mymail', 'cspv4_show_form_mymail_shortcode',10,1);

function cspv4_show_form_mymail_shortcode($output){
    $seed_cspv4 = get_option('seed_cspv4');
    if(class_exists('mymail')){
        if(!empty($seed_cspv4['mymail_form_id'])){
            $output = mymail_form($seed_cspv4['mymail_form_id'], 100, false);
        }else{
            $output = mymail_form(0, 100, false);
        }

    }
    return $output;

}
