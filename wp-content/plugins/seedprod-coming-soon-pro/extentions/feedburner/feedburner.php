<?php
//  * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
/**
 *  Add FeedBurner section
 */
$seed_cspv4 = get_option('seed_cspv4');
if($seed_cspv4['emaillist'] == 'feedburner'){
    add_filter('seedredux/options/seed_cspv4/sections', 'seed_cspv4_feedburner_section');
}

function seed_cspv4_feedburner_section($sections) {

	global $seed_cspv4;
	//var_dump($seed_cspv4['emaillist']);
    //$sections = array();
    $sections[] = array(
        'title' => __('FeedBurner', 'seedprod'),
        'desc' => __('<p class="description">Configure saving subscribers to FeedBurner options. <a href="http://support.seedprod.com/article/72-feedburner" target="_blank">Learn More</a></p>', 'seedprod'),
        'icon' => 'el-icon-envelope',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array(
                array(
                    'id'        => 'feedburner_addr',
                    'type'      => 'text',
                    'title'     => __( "Address", 'seedprod' ),
                    'subtitle'  => __( "Enter your FeedBurner address. http://feeds.feedburner.com/<i>YOURADDRESS</i>", 'seedprod' ),
                ),
                array(
                    'id'        => 'feedburner_local',
                    'type'      => 'text',
                    'title'     => __( 'Local', 'seedprod' ),
                    'subtitle'     => __( 'The language the FeedBurner form is displayed in. The default is English. <a href="http://support.google.com/feedburner/bin/answer.py?hl=en&answer=81423">Learn more</a>.', 'seedprod' ),
                    'default'   => 'EN',
                ),


        	)
    );

    return $sections;
}
