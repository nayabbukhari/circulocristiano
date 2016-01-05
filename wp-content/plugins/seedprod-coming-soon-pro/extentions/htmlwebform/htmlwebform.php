<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add HTMLwebform section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'htmlwebform'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_htmlwebform_section');
}

function seed_cspv4_htmlwebform_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('HTMLwebform', 'seedprod'),
        'desc' => __('<p class="description">Enter a HTML web form from any provider. This will bypass the Thank You page. <a href="http://support.seedprod.com/article/79-use-any-3rd-party-script-or-web-form" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'html_integration',
                    'type'      => 'textarea',
                    'title'     => __( "HTML Web Form", 'seedprod' ),
                    'subtitle'  => __('Enter the html provided from the 3rd party service.', 'seedprod'),
                ),


        	)
    );

    return $sections;
}
