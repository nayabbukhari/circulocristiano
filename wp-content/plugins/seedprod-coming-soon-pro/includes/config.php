<?php

/**
 * SeedProd Config File
 * */

if ( !class_exists( 'seed_cspv4_config' ) ) {

    class seed_cspv4_config {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $SeedReduxFramework;

        public function __construct() {

            if ( !class_exists( 'SeedReduxFramework' ) ) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            // if (true == SeedRedux_Helpers::isChildTheme() || true == SeedRedux_Helpers::isParentTheme()) {
            //     $this->initSettings();
            // } else {
            add_action( 'plugins_loaded', array( $this, 'initSettings' ), 10 );
            // }

        }

        public function initSettings() {

            // Set the default arguments
            $this->setArguments();

            // Create the sections and fields
            $this->setSections();

            if ( !isset( $this->args['opt_name'] ) ) { // No errors please
                return;
            }

            $this->SeedReduxFramework = new SeedReduxFramework( $this->sections, $this->args );
        }

        // Remove the demo link and the notice of integrated demo from the seedredux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when SeedRedux is a plugin.
            if ( class_exists( 'SeedReduxFrameworkPlugin' ) ) {
                remove_filter( 'plugin_row_meta', array( SeedReduxFrameworkPlugin::instance(), 'plugin_metalinks' ), null, 2 );

                // Used to hide the activation notice informing users of the demo panel. Only used when SeedRedux is a plugin.
                remove_action( 'admin_notices', array( SeedReduxFrameworkPlugin::instance(), 'admin_notices' ) );
            }
        }

        public function setSections() {

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
                'icon'      => 'el el-icon-cogs',
                'title'     => __( 'Page Settings', 'seedprod' ),
                'fields'    => apply_filters( 'seed_cspv4_general_fields', array(

                        array(
                            'id' => 'section-status',
                            'type' => 'section',
                            'subtitle' => __( 'Enable or Disable the Coming Soon Page. <a href="http://support.seedprod.com/article/53-page-settings" target="_blank">Learn More</a>', 'seedprod' ),
                            'indent' => true
                        ),
                        array(
                            'id'        => 'status',
                            'type'      => 'radio',
                            'title'     => __( 'Status', 'seedprod' ),
                            'subtitle'      => __( "When you are logged in you'll see the normal website. Logged out visitors will see the Coming Soon or Maintenance page. Coming Soon Mode will be available to search engines if your site is not private. Maintenance Mode will notify search engines that the site is unavailable. Redirect Mode will allow you to redirect to another page.", 'seedprod' ),

                            //Must provide key => value pairs for radio options
                            'options'   => array(
                                '0' => __( 'Disabled', 'seedprod' ),
                                '1' => __( 'Enable Coming Soon Mode', 'seedprod' ),
                                '2' => __( 'Enable Maintenance Mode', 'seedprod' ),
                                '3' => __( 'Enable Redirect Mode', 'seedprod' ),
                            ),
                            'default'   => '0'
                        ),
                        array(
                            'id'        => 'redirect_url',
                            'type'      => 'text',
                            'title'     => __( "Redirect URL", 'seedprod' ),
                            'subtitle'      => __( "Enter the url you'd like to redirect to.", 'seedprod' ),
                            'default'   => '',
                            'required'  => array( 'status', '=', '3')
                        ),

                        array(
                            'id'        => 'api_key',
                            'type'      => 'license_check',
                            'title'     => __( "License Key", 'seedprod' ),
                            'subtitle'      => __( "Enter your <a href='http://www.seedprod.com/members' target='_blank'>License Key</a> to receive automatic plugin updates.", 'seedprod' ),
                            'default'   => '',
                        ),

                        array(
                            'id' => 'section_content',
                            'type' => 'section',
                            'title' => __( 'Content Settings', 'seedprod' ),
                            'subtitle' => __( 'Enter you main content. <a href="http://support.seedprod.com/article/54-content-settings" target="_blank">Learn More</a>', 'seedprod' ),
                            'indent' => true
                        ),

                        array(
                            'id'        => 'logo',
                            'type'      => 'media',
                            'readonly'  => false,
                            'url'       => true,
                            'title'     => __( "Logo", 'seedprod' ),
                            //'compiler'  => 'true',
                            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                            'subtitle'      => __( 'Upload a logo or teaser image (or) enter the url to your image.', 'seedprod' ),
                            //'hint'      => array(
                            //    'title'     => 'Hint Title',
                            //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                            //)

                        ),
                        array(
                            'id'        => 'headline',
                            'type'      => 'text',
                            'title'     => __( "Headline", 'seedprod' ),
                            'subtitle'  => __( "Enter a headline for your page. Replace the default headline if it exists.", 'seedprod' ),
                            'default'   => __( "Coming Soon Pro by SeedProd", 'seedprod' ),
                        ),
                        array(
                            'id'        => 'description',
                            'type'      => 'editor',
                            'args'   => array(
                                'teeny'            => false
                            ),
                            'title'     => __( "Description", 'seedprod' ),
                            'subtitle'  => __( "Tell the visitor what to expect from your site. Also supports WordPress shortcodes and <a href='http://codex.wordpress.org/Embeds' target='_target'>video embeds</a>. Most shortcodes require that you enable 'Enable 3rd Party Plugins' which can be found under the advanced tab.", 'seedprod' ),
                            'default'   => __( "Replace this description with your own content.", 'seedprod' )
                        ),

                        array(
                            'id' => 'section-form',
                            'type' => 'section',
                            'title' => __( 'Form Settings', 'seedprod' ),
                            'subtitle' => __( 'Set up your optin form. <a href="http://support.seedprod.com/article/55-form-settings" target="_blank">Learn More</a>', 'seedprod' ),
                            'indent' => true
                        ),


                        array(
                            'id'        => 'emaillist',
                            'type'      => 'select',
                            'title'     => __( "Save subscribers to:", 'seedprod' ),
                            'description'     => __( "<span style='background:#FCF8E3'><strong>Important!</strong> After you select a provider and click <strong>Save Changes</strong>, refresh the page and look for the providers configuration tab on the left menu.</span>", 'seedprod' ),
                            //'subsection' => true,
                            //Must provide key => value pairs for select options
                            'options'   => apply_filters( 'seed_cspv4_providers', array(
                                    'none' => __( 'Do not display an Email SignUp', 'seedprod' ),
                                    'database' => __( 'Database', 'seedprod' ),
                                    'feedblitz' => 'FeedBlitz',
                                    'feedburner' => 'FeedBurner',
                                    'aweber' => 'Aweber',
                                    'campaignmonitor' => 'Campaign Monitor',
                                    'constantcontact' => 'Constant Contact',
                                    'getresponse' => 'Get Response',
                                    'gravityforms' => 'Gravity Forms',
                                    'followupemails' => 'Follow-Up Emails',
                                    'icontact' => 'iContact',
                                    'infusionsoft' => 'Infusionsoft',
                                    'madmimi' => 'Mad Mimi',
                                    'mailchimp' => 'MailChimp',
                                    'sendy' => 'Sendy',
                                    'mailpoet' => 'MailPoet',
                                    'htmlwebform' => 'HTML Web Form',
                                ) ),
                            'default'   => 'none'
                        ),

                        array(
                            'id'        => 'name_field',
                            'type'      => 'switch',
                            'title'     => __( "Name Field", 'seedprod' ),
                            'subtitle'  => __( 'Ask for the visitors for their name.', 'seedprod' ),
                            'default'   => false,
                            'required'  => array(
                                array( 'emaillist', '!=', 'feedburner' ),
                                array( 'emaillist', '!=', 'gravityforms' ),
                            )
                        ),
                        array(
                            'id'        => 'name_field_required',
                            'type'      => 'switch',
                            'title'     => __( "Name Field Required", 'seedprod' ),
                            'subtitle'  => __( 'Require the name.', 'seedprod' ),
                            'default'   => false,
                            'required'  => array(
                                array( 'name_field', '=', 1 ),
                            )
                        ),

                        array(
                            'id'        => 'privacy_policy_link_text',
                            'type'      => 'text',
                            'title'     => __( "Privacy Policy Text", 'seedprod' ),
                            'subtitle'  => __( 'Add an optional Privacy Policy text.', 'seedprod' ),
                            'default'   => __( 'We promise to never spam you.', 'seedprod' ),
                        ),

                        // array(
                        //     'id'        => 'privacy_policy',
                        //     'type'      => 'textarea',
                        //     'title'     => __( "Privicy Policy", 'seedprod' ),
                        //     'subtitle'  => __('Add an optional Privacy Policy', 'seedprod' ),
                        // ),

                        array(
                            'id' => 'section-thank-you-page',
                            'type' => 'section',
                            'title' => __( 'Thank You Page Settings', 'seedprod' ),
                            'subtitle' => __( 'This is what the user sees after they optin. <a href="http://support.seedprod.com/article/56-thank-you-page-settings" target="_blank">Learn More</a>', 'seedprod' ),
                            'indent' => true
                        ),

                        array(
                            'id'        => 'thankyou_msg',
                            'type'      => 'editor',
                            'args'   => array(
                                'teeny'            => false
                            ),
                            'title'     => __( "Thank You or Incentive Message", 'seedprod' ),
                            'subtitle'  =>  __( "Leave a thank you or incentive information after the user has subscribed. This will override the default success message.", 'seedprod' ),
                        ),

                        array(
                            'id'        => 'share_buttons',
                            'type'      => 'checkbox',
                            'title'     => __( "Share Buttons", 'seedprod' ),
                            "subtitle"      => __( 'Select the checkboxes above to display Social Share Buttons after you capture an email.', 'seedprod' ),
                            //Must provide key => value pairs for select options
                            "options" => apply_filters( 'seed_cspv4_share_buttons', array(
                                    'twitter' => __( 'Twitter', 'seedprod' ),
                                    'facebook' => __( 'Facebook Share', 'seedprod' ),
                                    'facebook_send' => __( 'Facebook Send', 'seedprod' ),
                                    'googleplus' => __( 'Google Plus', 'seedprod' ),
                                    'linkedin' => __( 'LinkedIn', 'seedprod' ),
                                    'pinterest' => __( 'PinIt', 'seedprod' ),
                                    'tumblr' => __( 'Tumblr', 'seedprod' ),
                                ) )
                        ),

                        array(
                            'id'        => 'tweet_text',
                            'type'      => 'text',
                            'title'     => __( "Optional Tweet Text", 'seedprod' ),
                            //'required'  => array('share_buttons', "contains", 'twitter'),
                            'subtitle'      => __( 'Applicable if Twitter is checked on the Share Buttons.', 'seedprod' ),
                        ),

                        array(
                            'id'        => 'facebook_thumbnail',
                            'type'      => 'media',
                            'readonly'  => false,
                            'url'       => true,
                            'title'     => __( "Facebook Thumbnail", 'seedprod' ),
                            //'compiler'  => 'true',
                            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                            'subtitle'      => __( 'Applicable if Facebook is checked on the Share or Send Buttons.Image for Facebook. Optimal Size: 200px x 200px', 'seedprod' ),
                            //'hint'      => array(
                            //    'title'     => 'Hint Title',
                            //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                            //)

                        ),

                        array(
                            'id'        => 'pinterest_thumbnail',
                            'type'      => 'media',
                            'readonly'  => false,
                            'url'       => true,
                            'title'     => __( "Pinterest Thumbnail", 'seedprod' ),
                            //'compiler'  => 'true',
                            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                            'subtitle'      => __( 'Applicable if Pinterest is checked on the Share Buttons. Image for Pinterest.', 'seedprod' ),
                            //'hint'      => array(
                            //    'title'     => 'Hint Title',
                            //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                            //)

                        ),

                        array(
                            'id'        => 'show_sharebutton_on_front',
                            'type'      => 'switch',
                            'title'     => __( "Display the Share Buttons on Front Page", 'seedprod' ),
                            "subtitle"      => __( 'By default Share Buttons are only shown after the user subscribes. This allows you to show them on the front page as well.', 'seedprod' ),
                            //Must provide key => value pairs for select options
                            'default' => false
                        ),

                        array(
                            'id'        => 'enable_reflink',
                            'type'      => 'switch',
                            'title'     => __( "Display Referrer Link", 'seedprod' ),
                            "subtitle"      => __( 'The referrer link is a special link that you can encourage your subscribers to share so you track who referred who.', 'seedprod' ),
                            //Must provide key => value pairs for select options
                            'default' => false
                        ),

                        array(
                            'id' => 'section_progress',
                            'type' => 'section',
                            'title' => __( 'Progress Bar Settings', 'seedprod' ),
                            'subtitle' => __( '<a href="http://support.seedprod.com/article/57-progress-bar-settings" target="_blank">Learn More</a>', 'seedprod' ),
                            'indent' => true
                        ),
                        array(
                            'id'       => 'enable_progressbar',
                            'type'     => 'switch',
                            'title'    => __( "Enable Progress Bar", 'seedprod' ),
                            'subtitle' => __( 'Displays a progress bar on your site.', 'seedprod' ),
                            'default'  => false,
                        ),

                        array(
                            'id'        => 'progress_bar_start_date',
                            'type'      => 'date',
                            'title'     => __( 'Start Date', 'seedprod' ),
                            'subtitle'  => __( 'The percent complete will be automatically calculated if you enter a start and end date.', 'seedprod' ),
                            'required'  => array( 'enable_progressbar', "=", 1 ),
                        ),

                        array(
                            'id'        => 'progress_bar_end_date',
                            'type'      => 'date',
                            'title'     => __( 'End Date', 'seedprod' ),
                            'subtitle'  => __( 'The percent complete will be automatically calculated if you enter a start and end date.', 'seedprod' ),
                            'required'  => array( 'enable_progressbar', "=", 1 ),
                        ),

                        array(
                            'id'            => 'progressbar_percentage',
                            'type'          => 'slider',
                            'title'         => __( "Percent Complete Override", 'seedprod' ),
                            'subtitle'      => __( "This will override the date calculation above. Leave at 0 to disable.", 'seedprod' ),
                            'default'       => 0,
                            'min'           => 0,
                            'step'          => 1,
                            'max'           => 100,
                            'display_value' => 'label',
                            'required'  => array( 'enable_progressbar', "=", 1 ),
                        ),

                        array(
                            'id'        => 'progressbar_effect',
                            'type'      => 'radio',
                            'title'     => __( "Progress Bar Effect", 'seedprod' ),
                            'subtitle'  => __( 'Striped and Animated are not supported in Internet Explorer', 'seedprod' ),

                            //Must provide key => value pairs for radio options
                            'options'   => array(
                                'basic' => __( 'Basic', 'seedprod' ),
                                'striped' => __( 'Striped', 'seedprod' ),
                                'animated' => __( 'Animated', 'seedprod' ),
                            ),
                            'default'   => 'basic',
                            'required'  => array( 'enable_progressbar', "=", 1 ),
                        ),


                        array(
                            'id' => 'section_countdown',
                            'type' => 'section',
                            'title' => __( 'Countdown Settings', 'seedprod' ),
                            'subtitle' => __( '<a href="http://support.seedprod.com/article/58-countdown-settings" target="_blank">Learn More</a>', 'seedprod' ),
                            'indent' => true
                        ),

                        array(
                            'id'       => 'enable_countdown',
                            'type'     => 'switch',
                            'title'    => __( "Enable Countdown", 'seedprod' ),
                            'subtitle' => __( 'Displays a countdown on your site.', 'seedprod' ),
                            'default'  => false,
                        ),

                        array(
                            'id'        => 'countdown_date',
                            'type'      => 'date',
                            'title'     => __( "End Date", 'seedprod' ),
                            'subtitle'  => __( 'Enter the date to countdown to.', 'seedprod' ),
                            'required'  => array( 'enable_countdown', "=", 1 ),
                        ),

                        array(
                            'id'        => 'countdown_time_hour',
                            'type'      => 'select',
                            'title'     => __( "End Time Hour", 'seedprod' ),
                            'subtitle'  => __( 'Enter the time to countdown to.', 'seedprod' ),
                            'required'  => array( 'enable_countdown', "=", 1 ),
                            'default'   => '12 am',
                            'options'   => array(
                                '12 am',
                                '1 am',
                                '2 am',
                                '3 am',
                                '4 am',
                                '5 am',
                                '6 am',
                                '7 am',
                                '8 am',
                                '9 am',
                                '10 am',
                                '11 am',
                                '12 pm',
                                '1 pm',
                                '2 pm',
                                '3 pm',
                                '4 pm',
                                '5 pm',
                                '6 pm',
                                '7 pm',
                                '8 pm',
                                '9 pm',
                                '10 pm',
                                '11 pm',
                            )
                        ),

                        array(
                            'id'        => 'countdown_time_minute',
                            'type'      => 'select',
                            'title'     => __( "End Time Minute", 'seedprod' ),
                            'subtitle'  => __( 'Enter the time to countdown to.', 'seedprod' ),
                            'required'  => array( 'enable_countdown', "=", 1 ),
                            'default'   => '00',
                            'options'   => array(
                                '00',
                                '01',
                                '02',
                                '03',
                                '04',
                                '05',
                                '06',
                                '07',
                                '08',
                                '09',
                                '10',
                                '11',
                                '12',
                                '13',
                                '14',
                                '15',
                                '16',
                                '17',
                                '18',
                                '19',
                                '20',
                                '21',
                                '22',
                                '23',
                                '24',
                                '25',
                                '26',
                                '27',
                                '28',
                                '29',
                                '30',
                                '31',
                                '32',
                                '33',
                                '34',
                                '35',
                                '36',
                                '37',
                                '38',
                                '39',
                                '40',
                                '41',
                                '42',
                                '43',
                                '44',
                                '45',
                                '46',
                                '47',
                                '48',
                                '49',
                                '50',
                                '51',
                                '52',
                                '53',
                                '54',
                                '55',
                                '56',
                                '57',
                                '58',
                                '59',
                            )
                        ),

                        array(
                            'id'        => 'countdown_format',
                            'type'      => 'text',
                            'title'     => __( "Format", 'seedprod' ),
                            'subtitle'  => __( "Optional Format for display - upper case for always, lower case only if non-zero, 'D' days, 'H' hours, 'M' minutes, 'S' seconds. Default: dHMS", 'seedprod' ),
                            'required'  => array( 'enable_countdown', "=", 1 ),
                            'default'   => 'dHMS',
                        ),

                        array(
                            'id'       => 'countdown_launch',
                            'type'     => 'switch',
                            'title'    => __( "Auto Launch", 'seedprod' ),
                            'subtitle' => __( 'This will automatically launch your site when countdown reaches the end. The Admin will receive an email when the site is launched.', 'seedprod' ),
                            'default'  => false,
                            'required'  => array( 'enable_countdown', "=", 1 ),
                        ),

                        array(
                            'id' => 'section_social_media',
                            'type' => 'section',
                            'title' => __( "Social Media Profiles", 'seedprod' ),
                            'subtitle' => __( "Social Profiles display an icon on your page with a link to it's respective profile. <a href='http://support.seedprod.com/article/59-social-media-profiles' target='_blank'>Learn More</a>", 'seedprod' ),
                            'indent' => true
                        ),

                        array(
                            'id'       => 'social_profiles_type',
                            'type'     => 'radio',
                            'title'    => __( "Type", 'seedprod' ),
                            'subtitle' => __( 'Select from Font or Image icons.', 'seedprod' ),
                            'default'  => 'font',
                            'options'  => array(
                                'font' => 'Font (Retina Ready)',
                                'image' => 'Image',
                            ),
                        ),
                        array(
                            'id'       => 'social_profiles_size',
                            'type'     => 'radio',
                            'title'    => __( "Size", 'seedprod' ),
                            'subtitle' => __( 'Select a size.', 'seedprod' ),
                            'default'  => 'large',
                            'options'  => array(
                                'small' => 'Small',
                                'medium' => 'Medium',
                                'large' => 'Large',
                            ),
                        ),

                        array(
                            'id'        => 'social_profiles_blank',
                            'type'      => 'switch',
                            'title'     => __( "New Window", 'seedprod' ),
                            'subtitle'  => __( 'Opens social profile links in a new window or tab.', 'seedprod' ),
                            'default'   => false,
                        ),

                        array(
                            'id'        => 'social_profiles',
                            'type'      => 'sortable',
                            'mode'      => 'text', // checkbox or text
                            'title'     => __( "Social Profiles", 'seedprod' ),
                            'subtitle'     => __( "Enter the urls to the social profiles you want to have displayed. Leave blank to disable. Use the cross hair icons to drag and drop the order. <a href='http://support.seedprod.com/article/43-adding-custom-icons'>Learn how to use custom icons.</a>", 'seedprod' ),
                            'label'     => true,
                            'options'   => array(
                                'facebook' => '',
                                'twitter' => '',
                                'linkedin' => '',
                                'googleplus' => '',
                                'youtube' => '',
                                'flickr' => '',
                                'vimeo' => '',
                                'pinterest' => '',
                                'instagram' => '',
                                'foursquare' => '',
                                'skype' => '',
                                'tumblr' => '',
                                'github' => '',
                                'dribbble' => '',
                                'slack' => '',
                                'rss' => '',
                                'email' => '',
                            )
                        ),


                        // array(
                        //     'id'        => 'opt-custom-callback',
                        //     'type'      => 'callback',
                        //     'title'     => __('Custom Field Callback', 'seedprod' ),
                        //     'subtitle'  => __('This is a completely unique field type', 'seedprod' ),
                        //     'desc'      => __('This is created with a callback function, so anything goes in this field. Make sure to define the function though.', 'seedprod' ),
                        //     'callback'  => 'seed_cspv4_seedredux_my_custom_field'
                        // ),


                        array(
                            'id' => 'section_head',
                            'type' => 'section',
                            'title' => __( "SEO", 'seedprod' ),
                            'subtitle' => __( 'The content in this section are displayed within the head tag on the page. <a href="http://support.seedprod.com/article/60-seo-settings" target="_blank">Learn More</a>', 'seedprod' ),
                            'indent' => true
                        ),


                        array(
                            'id'        => 'favicon',
                            'type'      => 'media',
                            'readonly'  => false,
                            'url'       => true,
                            'title'     => __( "Favicon", 'seedprod' ),
                            'preview'  => false,
                            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                            'subtitle'      => __( 'Favicons are displayed in a browser tab. Need Help <a href="http://tools.dynamicdrive.com/favicon/" target="_blank">creating a favicon</a>?', 'seedprod' ),
                            //'hint'      => array(
                            //    'title'     => 'Hint Title',
                            //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                            //)
                        ),

                        array(
                            'id'        => 'seo_title',
                            'type'      => 'text',
                            'title'     => __( "SEO Title", 'seedprod' ),
                            'subtitle'  => __( 'Enter a seo title.', 'seedprod' ),
                        ),

                        array(
                            'id'        => 'seo_description',
                            'type'      => 'textarea',
                            'title'     => __( "SEO Meta Description", 'seedprod' ),
                            'subtitle'  => __( 'Enter a seo description.', 'seedprod' ),
                        ),

                        array(
                            'id'        => 'ga_analytics',
                            'type'      => 'textarea',
                            'title'     => __( "Analytics Code", 'seedprod' ),
                            'subtitle'  => __( 'Paste in your <a href="http://www.google.com/analytics/" target="_blank">Google Analytics</a> code. Include the &lt;script&gt; tags.', 'seedprod' ),
                        ),

                        array(
                            'id' => 'section_footer',
                            'type' => 'section',
                            'title' => __( "Footer Credit", 'seedprod' ),
                            'subtitle' => __( 'The footer credit shows up in the bottom right corner of the page. <a href="http://support.seedprod.com/article/61-footer-credit" target="_blank">Learn More</a>', 'seedprod' ),
                            'indent' => true
                        ),

                        array(
                            'id'        => 'footer_credit_text',
                            'type'      => 'text',
                            'title'     => __( "Credit Text", 'seedprod' ),
                            'subtitle'  => __( 'Text to be used for your footer credit.', 'seedprod' ),
                        ),

                        array(
                            'id'        => 'footer_credit_img',
                            'type'      => 'media',
                            'readonly'  => false,
                            'url'       => true,
                            'title'     => __( "Credit Image", 'seedprod' ),
                            //'compiler'  => 'true',
                            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                            'subtitle'      => __( 'Use an image to add a footer credit. This will override the text.', 'seedprod' ),
                            //'hint'      => array(
                            //    'title'     => 'Hint Title',
                            //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                            //)
                        ),

                        array(
                            'id'        => 'footer_credit_link',
                            'type'      => 'text',
                            'title'     => __( "Credit Link", 'seedprod' ),
                            'subtitle'  => __( 'Link to be used for your footer credit.', 'seedprod' ),
                        ),

                        array(
                            'id'        => 'footer_affiliate_link',
                            'type'      => 'text',
                            'title'     => __( "Affiliate Link", 'seedprod' ),
                            //'description'     => __( "Enter your affiliate link.", 'seedprod' ),
                            'subtitle'  => __( 'Make money by being an affiliate! This will override any footer credit settings above. <a href="http://www.seedprod.com/affiliates" target="_blank">Learn More</a>.', 'seedprod' ),
                        ),

                    ) )
            );


            $this->sections[] = array(
                'icon'      => 'el el-icon-tint',
                'title'     => __( 'Design Settings', 'seedprod' ),
                'fields'    => array(

                    array(
                        'id'        => 'theme',
                        'type'      => 'image_select',
                        //'tiles'     => true,
                        'presets'   => true,
                        'title'     => __( 'Theme', 'seed-cspv4' ),
                        'subtitle'     => __( "Themes will override any settings below with presets. After you import a theme you can customize with the settings below. <br><a href='http://www.seedprod.com/themes'>Get more Themes</a><br><a href='http://support.seedprod.com/article/62-themes'>Learn more about Themes</a>", 'seedprod' ),
                        'default'   => '',
                        'options'   => apply_filters( 'seed_cspv4_themes', array(
                                'default'         => array( 'alt' => 'Default', 'img' => SEED_CSPV4_PLUGIN_URL . 'themes/default/screenshot.png',
                                    'presets' => '{"background":{"background-color":"#ffffff","background-repeat":"no-repeat","background-size":"cover","background-attachment":"fixed","background-position":"center top","background-image":"","media":{"id":"","height":"","width":"","thumbnail":""}},"bg_slideshow":"","bg_slideshow_slide_speed":"3000","bg_slideshow_slide_transition":"1","bg_slideshow_images":[{"title":"","description":"","url":"","sort":"0","attachment_id":"","thumb":"","image":"","height":"","width":""}],"bg_video":"","bg_video_url":"","headline_font":{"font-family":"Open Sans","font-options":"","google":"true","font-weight":"700","font-style":"","subsets":"","font-size":"32px","color":"#999999"},"text_font":{"font-family":"Open Sans","font-options":"","google":"true","font-weight":"400","font-style":"","subsets":"","font-size":"16px","line-height":"18px","color":"#999999"},"button_font":{"font-family":"Open Sans","font-options":"","google":"true","font-weight":"400","font-style":"","subsets":"","font-size":"16px","color":"#2ecc71"},"typekit_id":"","container_color":{"color":"#fafafa","alpha":"1.0"},"container_position":"none","container_width":{"width":"600px","units":"px"},"container_radius":"0","container_border":{"border-top":"0","border-right":"0","border-bottom":"0","border-left":"0","border-style":"solid","border-color":"#ffffff"},"container_flat":"1","btn_style":"","container_effect_animation":""}' ),
                                'wp'         => array( 'alt' => 'WP', 'img' => SEED_CSPV4_PLUGIN_URL . 'themes/wp/screenshot.png',
                                    'presets' => '{"background":{"background-color":"#f1f1f1","background-repeat":"no-repeat","background-size":"cover","background-attachment":"fixed","background-position":"center top","background-image":"","media":{"id":"","height":"","width":"","thumbnail":""}},"bg_slideshow":"","bg_slideshow_slide_speed":"3000","bg_slideshow_slide_transition":"1","bg_slideshow_images":[{"title":"","description":"","url":"","sort":"0","attachment_id":"","thumb":"","image":"","height":"","width":""}],"bg_video":"","bg_video_url":"","headline_font":{"font-family":"Open Sans","font-options":"","google":"true","font-weight":"700","font-style":"","subsets":"","font-size":"32px","color":"#777777"},"text_font":{"font-family":"Open Sans","font-options":"","google":"true","font-weight":"400","font-style":"","subsets":"","font-size":"16px","line-height":"18px","color":"#777777"},"button_font":{"font-family":"Open Sans","font-options":"","google":"true","font-weight":"400","font-style":"","subsets":"","font-size":"16px","color":"#1e8cbe"},"typekit_id":"","container_color":{"color":"#ffffff","alpha":"1"},"container_position":"none","container_width":{"width":"600px","units":"px"},"container_radius":"0","container_border":{"border-top":"0","border-right":"0","border-bottom":"0","border-left":"0","border-style":"solid","border-color":"#ffffff"},"container_flat":"0","btn_style":"1","container_effect_animation":""}' ),
                            ) ),
                    ),


                    array(
                        'id' => 'section_text',
                        'type' => 'section',
                        'title' => __( "Background Settings", 'seedprod' ),
                        'subtitle' => __( 'Customize the background. <a href="http://support.seedprod.com/article/63-background-settings" target="_blank">Learn More</a>', 'seedprod' ),
                        'indent' => true
                    ),

                    array(
                        'id'        => 'background',
                        'type'      => 'background',
                        'title'     => __( "Background", 'seedprod' ),
                        'transparent' => false,
                        'preview'   => false,
                        'subtitle'  => __( 'Body background color and or image. The default settings are responsive.', 'seedprod' ),
                        'default'   => array(
                            'background-color' => '#ffffff',
                            'background-size'  => 'cover',
                            'background-repeat'  => 'no-repeat',
                            'background-position'  => 'center top',
                            'background-attachment'  => 'fixed',
                        ),
                    ),

                    array(
                        'id'        => 'bg_effects',
                        'type'      => 'select',
                        'title'     => __( "Background Effects", 'seedprod' ),
                        'subtitle'  => __( "Option effects that will overlay on the background image.", 'seedprod' ),
                        'multi'     => true,
                        'options'   => array( 'noise' => 'Noise' ),
                        'default'   => false
                    ),

                    array(
                        'id'       => 'bg_slideshow',
                        'type'     => 'switch',
                        'title'    => __( "Background Slideshow", 'seedprod' ),
                        'subtitle' => __( "This will override the setting above and create a background slideshow.", 'seedprod' ),
                        'default'  => false,
                    ),

                    array(
                        'id'        => 'bg_slideshow_randomize',
                        'type'      => 'checkbox',
                        'title'     => __( "Slideshow Randomize", 'seedprod' ),
                        'subtitle'  => __( "This will display a random slideshow each time as opposed to the order listed below.", 'seedprod' ),
                        //'options' => array('on', 'off'),
                        'default'   => false,
                        'required'  => array( 'bg_slideshow', "=", 1 ),
                    ),

                    array(
                        'id'        => 'bg_slideshow_slide_speed',
                        'type'      => 'text',
                        'title'     => __( "Slide Speed", 'seedprod' ),
                        'subtitle'  => __( "This will determine how fast slides change in milliseconds. 3000 = 3 seconds", 'seedprod' ),
                        'default'   => '3000',
                        'required'  => array( 'bg_slideshow', "=", 1 ),
                    ),
                    array(
                        'id'        => 'bg_slideshow_slide_transition',
                        'type'      => 'select',
                        'title'     => __( "Slide Transition", 'seedprod' ),
                        'subtitle'  => __( "This will determine the transition between slides.", 'seedprod' ),

                        //Must provide key => value pairs for select options
                        'options'   => array(
                            '1' => __( 'Fade', 'seedprod' ),
                            '2' => __( 'Slide Top', 'seedprod' ),
                            '3' => __( 'Slide Right', 'seedprod' ),
                            '4' => __( 'Slide Bottom', 'seedprod' ),
                            '5' => __( 'Slide Left', 'seedprod' ),
                            '6' => __( 'Carousel Right', 'seedprod' ),
                            '7' => __( 'Carousel Left', 'seedprod' ),
                        ),
                        'default'   => '1',
                        'required'  => array( 'bg_slideshow', "=", 1 ),
                    ),



                    array(
                        'id'        => 'bg_slideshow_images',
                        'type'      => 'slides',
                        'title'     => __( "Slideshow Images", 'seedprod' ),
                        'subtitle'  => __( 'Paste in the urls to the images you like to use for your slideshow one per line. <a href="http://www.seedprod.com/knowledge-base/create-background-slideshow/" target="_blank">Learn How</a>', 'seedprod' ),
                        'required'  => array( 'bg_slideshow', "=", 1 ),
                    ),


                    array(
                        'id'       => 'bg_video',
                        'type'     => 'switch',
                        'title'    => __( "Background Video", 'seedprod' ),
                        'subtitle' => __( "This will override the setting above and create a background video. Mobile devices will not auto play video so the background image above will be used instead.", 'seedprod' ),
                        'default'  => false,
                    ),

                    array(
                        'id'        => 'bg_video_url',
                        'type'      => 'text',
                        'title'     => __( "Background Video URL", 'seedprod' ),
                        'subtitle'  => __( "Enter the YouTube, Vimeo or MP4 url.", 'seedprod' ),
                        'required'  => array( 'bg_video', "=", 1 ),
                    ),

                    array(
                        'id'       => 'bg_video_audio',
                        'type'      => 'checkbox',
                        'title'    =>  __( "Enable Video Audio", 'seedprod' ),
                        'subtitle' => __( "By default no audio is played.", 'seedprod' ),
                        'default'  => false,
                        'required'  => array( 'bg_video', "=", 1 ),
                    ),

                    array(
                        'id'       => 'bg_video_loop',
                        'type'      => 'checkbox',
                        'title'    =>  __( "Enable Video Loop", 'seedprod' ),
                        'subtitle' => __( "By default the video will be looped.", 'seedprod' ),
                        'default'  => true,
                        'required'  => array( 'bg_video', "=", 1 ),
                    ),




                    array(
                        'id' => 'section_text',
                        'type' => 'section',
                        'title' => __( "Typography", 'seedprod' ),
                        'subtitle' => __( 'Customize the typography. <a href="http://support.seedprod.com/article/64-typography" target="_blank">Learn More</a>', 'seedprod' ),
                        'indent' => true
                    ),

                    array(
                        'id'        => 'headline_font',
                        'type'      => 'typography',
                        'title'     => __( 'Headline', 'seedprod' ),
                        'subtitle'  => __( 'Specify the headline font properties.', 'seedprod' ),
                        'google'    => true,
                        'text-align'=> false,
                        'line-height' => false,
                        'default'   => array(
                            'color'         => '#999999',
                            'font-size'     => '32px',
                            'font-family'   => 'Open Sans',
                            'font-weight'   => '700',
                        ),
                    ),

                    array(
                        'id'        => 'text_font',
                        'type'      => 'typography',
                        'title'     => __( 'Text', 'seedprod' ),
                        'subtitle'  => __( 'Specify the body font properties.', 'seedprod' ),
                        'google'    => true,
                        'text-align'=> false,
                        'line-height' => true,
                        'default'   => array(
                            'color'         => '#999999',
                            'font-size'     => '16px',
                            'font-family'   => 'Open Sans',
                            'font-weight'   => '400',
                            'line-height'   => '18px',
                        ),
                    ),

                    // array(
                    //     'id'        => 'link_font',
                    //     'type'      => 'typography',
                    //     'title'     => __('Link Font', 'seedprod' ),
                    //     'subtitle'  => __('Specify the body font properties.', 'seedprod' ),
                    //     'google'    => true,
                    //     'text-align'=> false,
                    //     'line-height' => false,
                    //     'default'   => array(
                    //         'color'         => '#999999',
                    //         'font-size'     => '16px',
                    //         'font-family'   => 'Arial, Helvetica, sans-serif',
                    //         'font-weight'   => '400',
                    //     ),
                    // ),

                    array(
                        'id'        => 'button_font',
                        'type'      => 'typography',
                        'title'     => __( 'Buttons', 'seedprod' ),
                        'subtitle'  => __( 'Specify the button font / button and link colors', 'seedprod' ),
                        'google'    => true,
                        'text-align'=> false,
                        'line-height' => false,
                        'default'   => array(
                            'color'         => '#2ecc71',
                            'font-size'     => '16px',
                            'font-family'   => 'Open Sans',
                            'font-weight'   => '400',
                        ),
                    ),



                    array(
                        'id'        => 'typekit_id',
                        'type'      => 'text',
                        'title'     => __( "Typekit Kit ID", 'seedprod' ),
                        'subtitle'  => __( 'Enter your <a href="https://typekit.com" target="_blank">Typekit</a> Kit ID. This will override the fonts above.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'text_effects',
                        'type'      => 'select',
                        'multi'     => true,
                        'title'     => __( "Text Effects (experimental)", 'seedprod' ),
                        'subtitle'     => __( "Option text effects.", 'seedprod' ),
                        'options'   => array(
                            'inset' => 'Inset',
                        ),
                        'default'   => false
                    ),

                    array(
                        'id' => 'section_container',
                        'type' => 'section',
                        'title' => __( "Container", 'seedprod' ),
                        'subtitle' => __( 'The container is the box that wraps your content. <a href="http://support.seedprod.com/article/65-container-settings" target="_blank">Learn More</a>', 'seedprod' ),
                        'indent' => true
                    ),


                    array(
                        'id'        => 'container_color',
                        'type'      => 'color_rgba',
                        'subtitle'=> '<strong>Note:</strong> The opacity slider is to the right of the color slider. <a href="http://support.seedprod.com/article/93-how-to-change-the-containers-opacity-or-transparency" target="_blank">Learn More</a>.',
                        'title'     => __( "Container Color &amp; Opacity", 'seedprod' ),
                        'default'  => array(
                            'color' => '#fafafa',
                            'alpha' => '1.0'
                        ),
                        //'validate'  => 'color',
                    ),

                    array(
                        'id'        => 'container_position',
                        'type'      => 'button_set',
                        'title'     => __( "Container Position", 'seedprod' ),

                        //Must provide key => value pairs for radio options
                        'options'   => array(
                            'left' => __( 'Left', 'seedprod' ),
                            'none' => __( 'Center', 'seedprod' ),
                            'right' => __( 'Right', 'seedprod' ),
                        ),
                        'default'   => 'none'
                    ),

                    array(
                        'id'                => 'container_width',
                        'type'              => 'dimensions',
                        'height'              => false,
                        'title'             => __( 'Max Width', 'seedprod' ),
                        'default'           => array(
                            'width'     => '600px',
                        )
                    ),

                    array(
                        'id'        => 'container_radius',
                        'type'      => 'select',
                        'title'     => __( "Border Radius", 'seedprod' ),
                        'default'   => 0,
                        'options'   => array(
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100
                        )

                    ),

                    array(
                        'id'        => 'container_border',
                        'type'      => 'border',
                        'title'     => __( "Border", 'seedprod' ),
                        'subtitle'  => 'Width, Style, Color',
                        'default'   => array(
                            'border-color'  => '#ffffff',
                            'border-style'  => 'solid',
                            'border-top'    => '0px',
                            'border-right'  => '0px',
                            'border-bottom' => '0px',
                            'border-left'   => '0px'
                        )
                    ),

                    array(
                        'id'       => 'container_flat',
                        'type'     => 'switch',
                        'title'    => __( "Use Flat Colors", 'seedprod' ),
                        'subtitle' => __( "This will remove gradients and make element colors flat.", 'seedprod' ),
                        'default'  => true,
                    ),

                    array(
                        'id'       => 'btn_style',
                        'type'     => 'switch',
                        'title'    => __( "Connect Subscribe Button", 'seedprod' ),
                        'subtitle' => __( "By default the email field and subscribe buttons are seperate elements. This will make the subscribe button appear to be connected to the email field.", 'seedprod' ),
                        'default'  => false,
                    ),
                    array(
                        'id'        => 'container_effects',
                        'type'      => 'select',
                        'subtitle'  => 'Optional Effects',
                        'multi'     => true,
                        'title'     => __( "Container Effects", 'seedprod' ),
                        'options'   => array(
                            'dropshadow' => 'Drop Shadow',
                            'glow' => 'Glow',
                        ),
                        'default'   => false
                    ),


                    array(
                        'id'        => 'container_effect_animation',
                        'type'      => 'select',
                        'title'     => __( "Container Animation", 'seedprod' ),
                        'options'   => array(
                            'bounce' => 'bounce',
                            'flash' => 'flash',
                            'pulse' => 'pulse',
                            'rubberBand' => 'rubberBand',
                            'shake' => 'shake',
                            'swing' => 'swing',
                            'tada' => 'tada',
                            'wobble' => 'wobble',
                            'bounceIn' => 'bounceIn',
                            'bounceInDown' => 'bounceInDown',
                            'bounceInLeft' => 'bounceInLeft',
                            'bounceInRight' => 'bounceInRight',
                            'bounceInUp' => 'bounceInUp',
                            'fadeIn' => 'fadeIn',
                            'fadeInDown' => 'fadeInDown',
                            'fadeInDownBig' => 'fadeInDownBig',
                            'fadeInLeft' => 'fadeInLeft',
                            'fadeInLeftBig' => 'fadeInLeftBig',
                            'fadeInRight' => 'fadeInRight',
                            'fadeInRightBig' => 'fadeInRightBig',
                            'fadeInUp' => 'fadeInUp',
                            'fadeInUpBig' => 'fadeInUpBig',
                            'flip' => 'flip',
                            'flipInX' => 'flipInX',
                            'flipInY' => 'flipInY',
                            'lightSpeedIn' => 'lightSpeedIn',
                            'rotateIn' => 'rotateIn',
                            'rotateInDownLeft' => 'rotateInDownLeft',
                            'rotateInDownRight' => 'rotateInDownRight',
                            'rotateInUpLeft' => 'rotateInUpLeft',
                            'rotateInUpRight' => 'rotateInUpRight',
                            'slideInDown' => 'slideInDown',
                            'slideInLeft' => 'slideInLeft',
                            'slideInRight' => 'slideInRight',
                            'rollIn' => 'rollIn',
                        ),
                        'default'   => '',

                    ),




                    array(
                        'id' => 'section_template',
                        'type' => 'section',
                        'title' => __( "Template", 'seedprod' ),
                        'subtitle' => __( 'Advanced users can customize the template even more below. <a href="http://support.seedprod.com/article/66-template" target="_blank">Learn More</a>', 'seedprod' ),
                        'indent' => true
                    ),

                    array(
                        'id'        => 'custom_css',
                        'type'      => 'textarea',
                        'title'     => __( "Custom CSS", 'seedprod' ),
                        'subtitle'  => __( 'Need to tweaks the styles? Add your custom CSS here.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'custom_code',
                        'type'      => 'checkbox',
                        'title'     => __( "Customize Template Code", 'seedprod' ),
                        'subtitle'     => __( "This will copy in the current template's code into the field below so you can add your own custom html. Click 'Save Changes' after you change this and the code will be imported into the text area below so it can be modified.", 'seedprod' ),
                        'default'   => false
                    ),

                    array(
                        'id'        => 'template',
                        'type'      => 'textarea',
                        'title'     => __( "Code", 'seedprod' ),
                        'subtitle'  => __( 'If you need to make some advanced changes to the template you can edit the code directly. To use the default template delete all the code in this textarea.', 'seedprod' ),
                    ),


                )
            );

            $this->sections[] = array(
                'icon'      => 'el el-icon-flag',
                'title'     => __( 'Language Strings', 'seedprod' ),
                'desc'     => __( 'Translate or alter language string that show up within the landing page. <a href="http://support.seedprod.com/article/67-language-strings" target="_blank">Learn More</a>', 'seedprod' ),
                'fields'    => array(
                    array(
                        'id'        => 'txt_subscribe_button',
                        'type'      => 'text',
                        'title'     => __( "Subscribe Button", 'seedprod' ),
                        'default'   => __( 'Notify Me', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_email_field',
                        'type'      => 'text',
                        'title'     => __( "Subscribe Field", 'seedprod' ),
                        'default'   => __( 'Enter Your Email', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_name_field',
                        'type'      => 'text',
                        'title'     => __( "Enter Your Name", 'seedprod' ),
                        'default'   => __( 'Name', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_success_msg',
                        'type'      => 'text',
                        'title'     => __( "Success", 'seedprod' ),
                        'default'   => __( "Thank you! You'll be notified soon.", 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_already_subscribed_msg',
                        'type'      => 'text',
                        'title'     => __( "Already Subscribed", 'seedprod' ),
                        'default'   => __( "You're already subscribed.", 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_invalid_email_msg',
                        'type'      => 'text',
                        'title'     => __( "Invalid Email", 'seedprod' ),
                        'default'   => __( 'Please enter a valid email.', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_invalid_name_msg',
                        'type'      => 'text',
                        'title'     => __( "Invalid Name", 'seedprod' ),
                        'default'   => __( 'Please enter a name.', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_api_error_msg',
                        'type'      => 'text',
                        'title'     => __( "API Error", 'seedprod' ),
                        'default'   => __( 'Error, please try again.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'txt_stats_referral_url',
                        'type'      => 'text',
                        'title'     => __( "Referral URL Message", 'seedprod' ),
                        'default'   => __( 'Your Referral URL is:', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'txt_stats_referral_stats',
                        'type'      => 'text',
                        'title'     => __( "Referral Stats Message", 'seedprod' ),
                        'default'   => __( 'Your Referral Stats', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'txt_stats_referral_clicks',
                        'type'      => 'text',
                        'title'     => __( "Referral Stats Clicks", 'seedprod' ),
                        'default'   => __( 'Clicks', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'txt_stats_referral_subscribers',
                        'type'      => 'text',
                        'title'     => __( "Referral Stats Subscribers", 'seedprod' ),
                        'default'   => __( 'Subscribers', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'section-txt-countdown',
                        'type'      => 'section',
                        'title'     => __( 'Countdown', 'seedprod' ),
                        'subtitle'  => __( 'Language strings used within the countdown.', 'seedprod' ),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'txt_countdown_days',
                        'type'      => 'text',
                        'title'     => __( "Days", 'seedprod' ),
                        'default'   => __( 'Days', 'seedprod' ),
                        'subtitle'  => __( 'Plural', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_countdown_day',
                        'type'      => 'text',
                        'title'     => __( "Day", 'seedprod' ),
                        'default'   => __( "Day", 'seedprod' ),
                        'subtitle'  => __( 'Singular', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_countdown_hours',
                        'type'      => 'text',
                        'title'     => __( "Hours", 'seedprod' ),
                        'default'   => __( 'Hours', 'seedprod' ),
                        'subtitle'  => __( 'Plural', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_countdown_hour',
                        'type'      => 'text',
                        'title'     => __( "Hour", 'seedprod' ),
                        'default'   => __( 'Hour', 'seedprod' ),
                        'subtitle'  => __( 'Singular', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'txt_countdown_minutes',
                        'type'      => 'text',
                        'title'     => __( "Minutes", 'seedprod' ),
                        'default'   => __( 'Minutes', 'seedprod' ),
                        'subtitle'  => __( 'Plural', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_countdown_minute',
                        'type'      => 'text',
                        'title'     => __( "Minute", 'seedprod' ),
                        'default'   => __( 'Minute', 'seedprod' ),
                        'subtitle'  => __( 'Singular', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_countdown_seconds',
                        'type'      => 'text',
                        'title'     => __( "Seconds", 'seedprod' ),
                        'default'   => __( 'Seconds', 'seedprod' ),
                        'subtitle'  => __( 'Plural', 'seedprod' ),
                    ),
                    array(
                        'id'        => 'txt_countdown_second',
                        'type'      => 'text',
                        'title'     => __( "Second", 'seedprod' ),
                        'default'   => __( 'Second', 'seedprod' ),
                        'subtitle'  => __( 'Singular', 'seedprod' ),
                    ),


                )
            );

            $this->sections[] = array(
                'icon'      => 'el el-icon-cog',
                'title'     => __( 'Advanced Settings', 'seedprod' ),
                'fields'    => array(

                    array(
                        'id' => 'section_advanced',
                        'type' => 'section',
                        'subtitle' => __( '<a href="http://support.seedprod.com/article/68-access-controls" target="_blank">Learn More</a>', 'seedprod' ),
                        'indent' => true
                    ),
                    array(
                        'id'        => 'client_view_url',
                        'type'      => 'client_view',
                        'title'     => __( "Bypass URL", 'seedprod' ),
                        'subtitle'  => __( "Enter a phrase above and give your client a secret url that will allow them to bypass the Coming Soon page. Note this will create generic wordpress user with no privilages, <a href='http://support.seedprod.com/article/99-how-the-bypass-url-works' target='_blank'>learn more</a>. After the cookie expires the user will need to revisit the bypass url to regain access. Use only letter numbers and dashes.<br>Available shortcodes:<br>[seed_cspv4_bypass_link text='Text to be Displayed']<br>[seed_cspv4_bypass_url]", 'seedprod' ),
                        'validate'  => 'no_special_chars'

                    ),

                    array(
                        'id'        => 'bypass_expires',
                        'type'      => 'text',
                        'title'     => __( "Bypass Expires", 'seedprod' ),
                        'subtitle'  => __( 'Set how long the user has access in seconds. The default is 2 days.', 'seedprod' ),
                        'default'   => '172800',
                        'validate' => 'numeric'
                    ),

                    array(
                        'id'        => 'ip_access',
                        'type'      => 'textarea',
                        'title'     => __( "Access by IP", 'seedprod' ),
                        'subtitle'  => __( "All visitors from certain IP's to bypass the Coming Soon page. Put each IP on it's own line. Your current IP is: ", 'seedprod' ). seed_cspv4_get_ip(),
                    ),


                    array(
                        'id'        => 'include_roles',
                        'type'      => 'select',
                        'multi'     => true,
                        'data'      => 'roles',
                        'title'     => __( "Access by Role", 'seedprod' ),
                        'subtitle'  => __( 'By default anyone logged in will see the regular site and not the coming soon page. To override this select Roles that will be given access to see the regular site.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'include_page',
                        'type'      => 'select',
                        'data'      => 'pages',
                        'title'     => __( "Landing Page Mode", 'seedprod' ),
                        'subtitle'  =>__( 'Only display the coming soon page on one page of your site and use it as a landing page. Leave unselected if you want the coming soon page to be display on your entire site.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'include_url_pattern',
                        'type'      => 'text',
                        'title'     => __( "Include URL Pattern", 'seedprod' ),
                        'subtitle'  => __( 'Include certain urls to only diplay the coming soon page on a section of your site using <a href="http://en.wikipedia.org/wiki/Regex" target="_blank">regular expressions</a>. For example enter "blog" will include pages with the url: http://example.org/blog/*. <a href="http://support.seedprod.com/article/37-exclude-and-include-urls-pattern-examples" target="_blank">See more examples</a> and learn how to include multiple pages. This is useful if you are working on a new section of your site.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'exclude_url_pattern',
                        'type'      => 'text',
                        'title'     => __( "Exclude URL Pattern", 'seedprod' ),
                        'subtitle'  => __( 'Exclude certain urls from displaying the Coming Soon page using <a href="http://en.wikipedia.org/wiki/Regex" target="_blank">regular expressions</a>. For example enter "blog" will exclude the url: http://example.org/blog/. <a href="http://support.seedprod.com/article/37-exclude-and-include-urls-pattern-examples" target="_blank">See more examples</a> and learn how to exclue multiple pages. This is useful if a section of you site is ready to be viewed or you have a login page on the frontend.', 'seedprod' ),
                    ),

                    array(
                        'id' => 'section_scripts',
                        'type' => 'section',
                        'title' => __( "Scripts", 'seedprod' ),
                        'subtitle' => __( '<a href="http://support.seedprod.com/article/69-scripts" target="_blank">Learn More</a>', 'seedprod' ),
                        'indent' => true
                    ),

                    // array(
                    //     'id'        => 'enable_responsiveness',
                    //     'type'      => 'switch',
                    //     'title'     => __( "Enable Responsiveness", 'seedprod' ),
                    //     'subtitle'  => __('Makes the page responsive.', 'seedprod' ),
                    //     'default'   => true,
                    // ),

                    array(
                        'id'        => 'enable_fitvidjs',
                        'type'      => 'switch',
                        'title'     => __( "Enable FitVid", 'seedprod' ),
                        'subtitle'  => __( 'Makes your videos responsive.', 'seedprod' ),
                        'default'   => true,
                    ),

                    array(
                        'id'        => 'enable_retinajs',
                        'type'      => 'switch',
                        'title'     => __( "Enable Retina JS", 'seedprod' ),
                        'subtitle'  => __( ' Serve high-resolution images to devices with retina displays if available.', 'seedprod' ),
                        'default'   => false,
                    ),

                    array(
                        'id'        => 'enable_wp_head_footer',
                        'type'      => 'switch',
                        'title'     => __( "Enable 3rd Party Plugins", 'seedprod' ),
                        'subtitle'  => __( 'This allows other plugins to work inside the landing page. If you are unsure do not enable it. This can cause styling issues with the page.', 'seedprod' ),
                        'default'   => false,
                    ),

                    array(
                        'id'        => 'disable_default_excludes',
                        'type'      => 'switch',
                        'title'     => __( "Disable Default Excluded URLs", 'seedprod' ),
                        'subtitle'  => __( 'By defautl urls with the term "login" in it are excluded so users don\'t get locked out if using a custom login page.', 'seedprod' ),
                        'default'   => false,
                    ),

                    array(
                        'id'       => 'display_lang_switcher',
                        'type'     => 'switch',
                        'title'    => __( "Display Language Switcher", 'seedprod' ),
                        'subtitle' => __( "The setting requires the WPML plugin and will turn on and off the language switcher.", 'seedprod' ),
                        'default'  => false,
                    ),

                    array(
                        'id'        => 'header_scripts',
                        'type'      => 'textarea',
                        'title'     => __( "Header Scripts", 'seedprod' ),
                        'subtitle'  => __( 'Enter any custom scripts. You can enter Javascript or CSS. This will be rendered before the closing head tag.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'footer_scripts',
                        'type'      => 'textarea',
                        'title'     => __( "Footer Scripts", 'seedprod' ),
                        'subtitle'  => __( 'Enter any custom scripts. This will be rendered before the closing body tag.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'conversion_scripts',
                        'type'      => 'textarea',
                        'title'     => __( "Conversion Scripts", 'seedprod' ),
                        'subtitle'  => __( 'This will render only after the form has been submitted. This will be rendered before the closing body tag.', 'seedprod' ),
                    ),

                    array(
                        'id'        => 'plugin_api',
                        'type'      => 'textarea',
                        'title'     => __( "Plugin API", 'seedprod' ),
                        'subtitle'  => __( "This allows to change the plugin's behavior. Leave this blank if you are not sure.", 'seedprod' ),
                    ),
                )


            );

            $this->sections[] = array(
                'icon'      => 'el el-icon-user',
                'title'     => __( 'Subscribers', 'seedprod' ),
                'fields'    => array(
                    array(
                        'id'        => 'opt-raw-info',
                        'type'      => 'raw',
                        'content'   => seed_cspv4_subscribers_page(),
                    )
                ),
            );


            $this->sections[] = array(
                'type' => 'divide',
            );

            $this->sections[] = array(
                'title'     => __( 'Import / Export', 'seedprod' ),
                'desc'      => __( 'Import and Export your SeedRedux Framework settings from file, text or URL.', 'seedprod' ),
                'icon'      => 'el el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your SeedRedux options',
                        'full_width'    => false,
                    ),
                ),
            );

            $this->sections[] = array(
                'icon'      => 'el el-icon-question-sign',
                'title'     => __( 'Support', 'seedprod' ),
                'fields'    => array(
                    array(
                        'id'        => 'opt-raw-info',
                        'type'      => 'raw',
                        'content'   => seed_cspv4_support_page(),
                    )
                ),
            );








        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'seedredux-help-tab-1',
                'title'     => __( 'Theme Information 1', 'seedprod' ),
                'content'   => __( '<p>This is the tab content, HTML is allowed.</p>', 'seedprod' )
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'seedredux-help-tab-2',
                'title'     => __( 'Theme Information 2', 'seedprod' ),
                'content'   => __( '<p>This is the tab content, HTML is allowed.</p>', 'seedprod' )
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'seedprod' );
        }

        /**
         * All the possible arguments for SeedRedux.
         * For full documentation on arguments, please refer to: https://github.com/SeedReduxFramework/SeedReduxFramework/wiki/Arguments
         * */
        public function setArguments() {

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'seed_cspv4',            // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => 'Coming Soon Pro',     // Name that appears at the top of your panel
                'display_version'   => SEED_CSPV4_VERSION,  // Version that appears at the top of your panel
                'menu_type'         => 'submenu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
                'menu_title'        => __( 'Coming Soon Pro', 'seedprod' ),
                'page_title'        => __( 'Coming Soon Pro', 'seedprod' ),
                //'footer_credit'        => __('Coming Soon Pro', 'seedprod' ),

                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '1', // Must be defined to add google fonts to the typography module

                'async_typography'  => false,                    // Use a asynchronous font on the front end or font string
                'admin_bar'         => false,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                    // Show the time the page took to load, etc
                'customizer'        => false,                    // Enable basic customizer support

                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'options-general.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '', //SEED_CSPV4_PLUGIN_URL.'images/favicon',                      // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => 'seed_cspv4_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,                   // Shows the Import/Export panel when not used as a field.

                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                'footer_credit'     => ' ',                   // Disable the footer credit of SeedRedux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE
                'ajax_save'                 => false, // disabled the AJAX save

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'http://twitter.com/seedprod',
                'title' => 'Follow us on Twitter',
                'icon'  => 'el el-icon-twitter'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://facebook.com/seedprodwp',
                'title' => 'Like us on Facebook',
                'icon'  => 'el el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://plus.google.com/+Seedprod/',
                'title' => 'Follow us on Google Plus',
                'icon'  => 'el el-icon-googleplus'
            );

            // Panel Intro text -> before the form
            if ( !isset( $this->args['global_variable'] ) || $this->args['global_variable'] !== false ) {
                if ( !empty( $this->args['global_variable'] ) ) {
                    $v = $this->args['global_variable'];
                } else {
                    $v = str_replace( '-', '_', $this->args['opt_name'] );
                }
                //$this->args['intro_text'] = sprintf(__('<p>Did you know that SeedRedux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'seedprod' ), $v);
            } else {
                //$this->args['intro_text'] = __('<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'seedprod' );
            }

            // Add content after the form.
            //$this->args['footer_text'] = __('<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', 'seedprod' );
        }

    }

    global $seed_cspv4_seedreduxConfig;
    $seed_cspv4_seedreduxConfig = new seed_cspv4_config();

}
