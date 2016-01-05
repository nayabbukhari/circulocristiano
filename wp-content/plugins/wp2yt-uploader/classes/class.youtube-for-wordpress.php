<?php

if( !class_exists( "YT4WPBase" ) ) {

	class YT4WPBase {
		
			/**
			*	Variables
			*/
			private	$error		    	= false;
			private	$errorMsg	    	= '';
			public	$sessName	    	= 'ytplus_sess';
			public	$optionVal			= false;
		
			/*
			*  construct
			*  
			*  initialize the main class on construct
			*  @since 2.0
			*/
			public function __construct() {
					YT4WPBase::initialize();
				}
				
			/*
			*  destruct
			*  unset the main class on destruct
			*
			*  @since 2.0
			*/			
			public function __destruct() {
					unset($this);
				}
			 
			/*
			*  activate
			*  add our options, do our redirect on plugin activate
			*
			*  @since 2.0
			*/			
			public function activate() {
					// redirect the user on plugin activation
					// to our MailChimp settings page
					add_option('youtube_for_wordpress_do_activation_redirect', true);
					
					// add a new option to store the plugin activation date/time
					// @since v2.0.3
					// this is used to notify the user that they should review after 2 weeks
					if ( !get_option( 'yt4wp_activation_date' ) ) {
						$now = strtotime( "now" );
						add_option( 'yt4wp_activation_date', $now );
					}
					
					// create our refresh token option
					// this is used to store our returned refresh token from the YouTube API
					// used in nearly all API calls
					if( !get_option( 'yt4wp_user_refresh_token' ) ) {
						add_option('yt4wp_user_refresh_token' , '');
					}
										
				}
				
			/*
			*  deactivate 
			*  fired on deactivation
			*
			*  @since 2.0
			*/			
			public function deactivate() {
				}
							
			/*
			*  uninstall 
			*  delete our installed options on plugin uninstall
			*
			*  @since 2.0
			*/
			public function uninstall() { 
					// delete options on plugin uninstall
					delete_option(YT4WP_OPTION);
					delete_option('yt4wp_user_refresh_token');
					delete_option( 'yt4wp_activation_date' );				
					delete_option( 'youtube_for_wordpress_do_activation_redirect' );				
				}
			
		/***** INITIAL SETUP
		 ****************************************************************************************************/
			/*
			*  initialize
			*  Initialize our session and enqueue
			*	  the necessary scripts, styles, shortcodes, filters, hooks etc.
			*
			*  @since 2.0
			*/
			public function initialize() {
			
					// If it's not already set up, initialize our yt4wp session
					if(session_id() == '') session_start();
					
					if(!is_array(@$_SESSION[$this->sessName])) {
							$_SESSION[$this->sessName]	= array();
						}
														 
					// Add the CSS/JS files
					add_action('admin_enqueue_scripts',		array(&$this, 'addStyles'));
					add_action('admin_enqueue_scripts',		array(&$this, 'addScripts'));
					
					// setup our plugin activation redirect
					add_action('admin_init', array( &$this, 'yt_plus_plugin_activation_redirect' ) );
					
					// Setup the administration menus
					add_action('admin_menu', array( &$this, 'addAdministrationMenu' ) );
							
					// load our frontend styles and scripts
					if( !is_admin() ) {
							add_action('wp_enqueue_scripts', array(&$this, 'addStyles_frontend'));
							add_action('wp_enqueue_scripts', array(&$this, 'addScripts_frontend'));
						}
							
					// Add Custom Insert Media Button
					add_action('media_buttons_context',  array(&$this , 'youtube_for_wordpress_insert_button' ) );
					
					// Add YouTube for WordPress Pop Up Window to Admin Footer
					add_action( 'admin_footer',  array(&$this , 'add_yt4wp_popup' ) );
					
					// Add custom RSS feed function
					add_action('init', array( &$this , 'youtubePlusCustomRSS' ) );
					
					// Custom Responsive YouTube Shortcode
					add_shortcode( 'yt4wp-video', array(&$this , 'youtube_for_wordpress_responsive_video_shortcode' ) );
					add_shortcode( 'yt4wp-playlist', array(&$this , 'youtube_for_wordpress_playlist_shortcode' ) );
					add_shortcode( 'yt4wp-grid', array(&$this , 'youtube_for_wordpress_grid_layout' ) );
										
					/*
					*  Custom Button Filters
					*  Hookable filters to allow users to extend the base button set
					*  and add custom functionality
					*
					*  @since 2.0
					*/
					
					// browse buttons hook
					add_filter( 'yt4wp_browse_buttons', array( &$this , 'youtube_for_wordpress_filter_browse_buttons' ) );
					// playlist buttons hook
					add_filter( 'yt4wp_playlist_buttons', array( &$this , 'youtube_for_wordpress_filter_playlist_buttons' ) );
					// user playlist items buttons hook
					add_filter( 'yt4wp_user_playlist_items_buttons', array( &$this , 'youtube_for_wordpress_filter_user_playlist_item_buttons' ) );
					// likes, faves, watch history buttons hook
					add_filter( 'yt4wp_likes_favs_history_buttons', array( &$this , 'youtube_for_wordpress_filter_likes_favs_history_buttons' ) );
					// user watch later buttons hook
					add_filter( 'yt4wp_watch_later_buttons', array( &$this , 'youtube_for_wordpress_filter_watch_later_buttons' ) );
					// channel search results filter
					add_filter( 'yt4wp_channel_results_buttons', array( &$this , 'youtube_for_wordpress_filter_channel_results_buttons' ) );
					// playlist search results filter
					add_filter( 'yt4wp_playlist_results_buttons', array( &$this , 'youtube_for_wordpress_filter_playlist_results_buttons' ) );		
					/* end extensable filter hooks */

					// Make sure the option exists
					if( !$this->optionVal ) {
						$this->getOptionValue();
					}
					
					// Register Our Widget 
					$this->registerYouTubePlusWidgets();
					
					// check the users plugin installation date
					add_action( 'admin_init', array( &$this , 'yt4wp_check_installation_date' ) );
					// dismissable notice admin side
					add_action( 'admin_init', array( &$this , 'yt4wp_stop_bugging_me' ), 5 );
					
					/* Check if the user wants to kep YouTube for WordPress auto updates @since v2.0.3 */
					if ( isset( $this->optionVal['yt4wp-auto-background-updates'] ) ) {
						if ( $this->optionVal['yt4wp-auto-background-updates'] == 1 ) {
							add_filter( 'auto_update_plugin', array( &$this , 'include_youtube_for_wordpress_in_auto_updates' ), 10, 2 );	
						}
					}
					
					// enqueue admin icon styles in the header
					add_action( 'admin_enqueue_scripts' , array( &$this , 'styleYT4WP_Admin_Icon' ) );
					
					// Add extra link on plugin install/uninstall page	
					add_filter( 'plugin_action_links_wp2yt-uploader/youtube-for-wordpress.php', array( &$this , 'yt4wp_custom_action_links' ) );
					
					// Do any update tasks if needed
					// to add new/missing options :)
					$this->runUpdateCheck();	
					
				}
				
			/* Add link on plugin page to plugin settings page */
			public function yt4wp_custom_action_links( $links ) {
			   $links[] = '<a title="YouTube for WordPress Settings" href="'. get_admin_url(null, 'admin.php?page=youtube-for-wordpress-settings') .'">Settings</a>';
			   $links[] = '<a title="YouTube for WordPress Add Ons" href="http://www.yt4wp.com/?utm_source=dashboard-plugins-page&utm_medium=text-link&utm_campaign=dashboard-plugins-page" target="_blank">Add Ons</a>';
			   $links[] = '<a title="YouTube for WordPress Support" href="http://www.yt4wp.com/support/?utm_source=dashboard-plugins-page&utm_medium=text-link&utm_campaign=dashboard-plugins-page" target="_blank">Support</a>';
			   if ( $this->optionVal['yt4wp-license-key'] == '' ) {
					$links[] = '<a title="YouTube for WordPress Licensing" href="#" onclick="return false;" target="_blank">Purchase a License</a>';
			   }
			   return $links;
			}
				
			/* 
			*	styleYT4WP_Admin_Icons()
			*	style the yt4wp icon with a nice red gradient
			*	@since v2.3
			*/
			public function styleYT4WP_Admin_Icon() {
					echo '<style>body.wp-admin .wp-menu-image.dashicons-before.dashicons-video-alt3:before, body.wp-admin *[id*="yt_plus_widget"] > div.widget-top > div.widget-title > h4:before {
								background: -webkit-linear-gradient(top, #E13838, #411);
								background: linear-gradient(top, #E13838, #411);
								-webkit-background-clip: text;
								-webkit-text-fill-color: transparent;}</style>';
				}
			
			/* 
			*	include_youtube_for_wordpress_in_auto_updates();
			*
			*	Keep this plugin automatically updated!
			*	since @v2.0.3
			*/
			public function include_youtube_for_wordpress_in_auto_updates( $update, $item ) {
					return ( in_array( $item->slug, array(
						'wp2yt-uploader',
					) ) );
				}
			
			/*
				yt4wp_check_installation_date()
				checks the user installation date, and adds our action if it's past to ask the user for a review :)
				@since v2.0.3
			*/
			public function yt4wp_check_installation_date() {
					$stop_bugging_me = "";
					$stop_bugging_me = get_option('yt4wp_review_stop_bugging_me');
					if( !$stop_bugging_me ) {
						$install_date = get_option( 'yt4wp_activation_date' );
						$past_date = strtotime( '-14 days' );
						if ( $past_date >= $install_date && current_user_can( 'install_plugins' ) ) {
							add_action( 'admin_notices', array( &$this , 'yt4wp_display_review_us_notice' ) );
						}
					}
				}
			
			/* 
				Display our admin notification
				asking for a review, and for user feedback 
				@since v2.0.3
			*/
			public function yt4wp_display_review_us_notice() {	
					/* Lets only display our admin notice on YT4WP pages to not annoy the hell out of people :) */
					if ( in_array( get_current_screen()->base , array( 'toplevel_page_youtube-for-wordpress' , 'youtube-for-wp_page_youtube-for-wordpress-settings' , 'youtube-for-wp_page_youtube-for-wordpress-add-ons' , 'youtube-for-wp_page_youtube-for-wordpress-support' , 'post' ) ) ) {
						// Review URL - Change to the URL of your plugin on WordPress.org
						$reviewurl = 'http://wordpress.org/support/view/plugin-reviews/wp2yt-uploader';
						$user_feedback_url = 'http://www.yt4wp.com/user-feedback-survey/';
						$http_https = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
						$current_url = "$http_https$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
						$nobugurl = add_query_arg( 'yt4wpnobug', '1', $current_url );
						global $current_user;
						get_currentuserinfo();
						if ( isset( $current_user->user_firstname ) ) {
							$review_message = sprintf( __( "Hey " . $current_user->user_firstname . ", You've been using <strong>YouTube for WordPress</strong> for 2 weeks now. We hope you're enjoying the power and all the features packed into the free version.  If so, please leave us a review, we'd love to hear what you have to say. <br /><br /> <a href='%s' target='_blank' class='button-secondary'>Leave A Review</a> <a href='%s?utm_source=yt4wp-2week-notice&utm_medium=button&utm_campaign=yt4wp-2week-notice' target='_blank' class='button-secondary'>Fill Out Our User Feedback Survey</a> <a href='%s' class='button-secondary'>Dismiss</a>" ), $reviewurl, $user_feedback_url, $nobugurl );
						} else {
							$review_message = sprintf( __( "You have been using <strong>YouTube for WordPress</strong> for 2 weeks now. We hope you're enjoying the power and all the features packed into the free version.  If so, please leave us a review, we'd love to hear what you have to say. <br /><br /> <a href='%s' target='_blank' class='button-secondary'>Leave A Review</a> <a href='%s?utm_source=yt4wp-2week-notice&utm_medium=button&utm_campaign=yt4wp-2week-notice' target='_blank' class='button-secondary'>Fill Out Our User Feedback Survey</a> <a href='%s' class='button-secondary'>Dismiss</a>" ), $reviewurl, $user_feedback_url, $nobugurl );
						}
						echo '<style>#yt_plus_review_this_plugin_container{display:none;}</style>'; 
						echo '<div class="updated"><p style="font-size:15px;">';
							echo $review_message;
						echo "</p></div>";
					}
				}
			

			/* 
				Remove the Review us notification when user clicks 'Dismiss'
				@since v2.0.3
			*/
			public function yt4wp_stop_bugging_me() {
				$nobug = "";
				if ( isset( $_GET['yt4wpnobug'] ) ) {
						$nobug = esc_attr( $_GET['yt4wpnobug'] );
					}
				if ( 1 == $nobug ) {
						add_option( 'yt4wp_review_stop_bugging_me', TRUE );
					}
			}
			
			/*
			*  getOptionValue()
			*  return the option array
			*  used to retrieve a specific option
			*
			*  ie : $this->optionVal['yt4wp-api-key']	
			*
			*  @since 2.0
			*/
			public function getOptionValue() {
					$defaultVals	= array(
						'version'	=> YT4WP_VERSION_CURRENT,
						'yt4wp-api-key'	=> '',
						'yt4wp-oauth2-key'	=> '',
						'yt4wp-oauth2-secret'	=> '',
						'yt4wp-embed-player-style' 	=> 'wp-mediaelement',
						'yt4wp-include-stat-count-in-query'	=> 'stat-count-disabled',
						'yt4wp-region' => 'US',
						'yt4wp-language' => 'en',
						'yt4wp-license-key'	=> '',
						'yt4wp-limit-error-log-count' => '5',
						'yt4wp-auto-background-updates' => '1'
					);
					$ov = get_option(YT4WP_OPTION, $defaultVals);
					$this->optionVal	= $ov;
					return $ov;
				}
			
			
			/*
			*  runUpdateCheck()
			*  Add missing or new options when a new version is released
			*  Runs on initial activation/update
			*
			*  @since 2.0
			*/			
			private function runUpdateCheck() {
			
					/* add new yt4wp-auto-background-updates option
					* ie user updates from version < 2.0.2.1
					*
					* @since v2.0.2.1
					*/
					if ( !$this->optionVal['yt4wp-auto-background-updates'] ) {
						$this->optionVal['yt4wp-auto-background-updates'] = '1';
						update_option(YT4WP_OPTION, $this->optionVal);
					}
					
					/* add new yt4wp-limit-error-log-count options
					* ie user updates from version < 2.0.2
					*
					* @since v2.0.2
					*/
					if ( !$this->optionVal['yt4wp-limit-error-log-count'] ) {
						!$this->optionVal['yt4wp-limit-error-log-count'] = '5'; 
					}
					
				}

		/***** YouTube for WordPress Functions
		 ****************************************************************************************************/
			/*
			*  getBrowser()
			*
			*  Get the current users browser information
			*	used on the debug settings page
			*
			*  @since 2.0
			*/		
			public function getBrowser() { 
					$u_agent	= $_SERVER['HTTP_USER_AGENT']; 
					$bname		= 'Unknown';
					$platform	= 'Unknown';
					$version	= "";
					//First get the platform?
					if(preg_match('/linux/i', $u_agent)) {
						$platform = 'Linux';
					} else if ( preg_match( '/macintosh|mac os x/i' , $u_agent ) ) {
						$platform = 'Mac';
					} else if( preg_match( '/windows|win32/i' , $u_agent ) ) {
						$platform = 'Windows';
					}
					
					// Next get the name of the useragent yes seperately and for good reason
					if( preg_match( '/MSIE/i' , $u_agent ) && !preg_match( '/Opera/i' , $u_agent ) ) { 
						$bname = 'Internet Explorer'; 
						$ub = "MSIE"; 
					}  else if( preg_match( '/Firefox/i' , $u_agent ) ) { 
						$bname = 'Mozilla Firefox'; 
						$ub = "Firefox"; 
					} else if( preg_match( '/Chrome/i' , $u_agent ) ) { 
						$bname = 'Google Chrome'; 
						$ub = "Chrome"; 
					} else if( preg_match( '/Safari/i' , $u_agent ) ) { 
						$bname = 'Apple Safari'; 
						$ub = "Safari"; 
					} else if( preg_match( '/Opera/i' , $u_agent ) ) { 
						$bname = 'Opera'; 
						$ub = "Opera"; 
					} else if( preg_match( '/Netscape/i' , $u_agent ) ) { 
						$bname = 'Netscape'; 
						$ub = "Netscape"; 
					} 
					
					// finally get the correct version number
					$known = array( 'Version' , $ub , 'other' );
					$pattern = '#(?<browser>' . join( '|' , $known ) .
						')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
					if( !preg_match_all( $pattern , $u_agent , $matches ) ) {
						// we have no matching number just continue
					}
					
					// see how many we have
					$i = count( $matches['browser'] );
					if($i != 1) {
						//we will have two since we are not using 'other' argument yet
						//see if version is before or after the name
						if( strripos( $u_agent , "Version" ) < strripos( $u_agent , $ub ) ) {
								$version= $matches['version'][0];
							} else {
								$version= $matches['version'][1];
							}
						} else {
							$version= $matches['version'][0];
						}
					
					// check if we have a number
					if( $version==null || $version=="" ) { 
						$version="?"; 
					}
					
					return array(
							'userAgent' => $u_agent,
							'name'      => $bname,
							'version'   => $version,
							'platform'  => $platform,
							'pattern'    => $pattern
					);
				}

		/***** CONFIGURATION
		 ****************************************************************************************************/
			/*
			*  updateOptions()
			*  Updates the various options on the 
			*	main settings page
			*
			*  @since 2.0
			*/
			public function updateOptions($p) {
				if(!empty($p['form_data']))
					{
					
						parse_str($p['form_data'], $fd);
						
						$this->optionVal['yt4wp-oauth2-key']	= trim($fd['yt4wp-oauth2-key']);
						$this->optionVal['yt4wp-oauth2-secret']	= trim($fd['yt4wp-oauth2-secret']);
						$this->optionVal['yt4wp-api-key']	= trim($fd['yt4wp-api-key']);
						$this->optionVal['yt4wp-embed-player-style'] = $fd['yt4wp-embed-player-style'];
						$this->optionVal['yt4wp-include-stat-count-in-query'] = $fd['yt4wp-include-stat-count-in-query'];
						$this->optionVal['yt4wp-auto-background-updates'] = isset( $fd['yt4wp-auto-background-updates'] ) ? $fd['yt4wp-auto-background-updates'] : '';
						
						// only save the region and language options once the user has sucessfully authed
						if ( get_option( 'yt4wp_user_refresh_token' ) != '' ) {
							$this->optionVal['yt4wp-region'] = isset( $fd['yt4wp-region'] ) ? $fd['yt4wp-region'] : '';
							$this->optionVal['yt4wp-language'] = isset( $fd['yt4wp-language'] ) ? $fd['yt4wp-language'] : '';
						} else {
							$this->optionVal['yt4wp-region'] = 'US';
							$this->optionVal['yt4wp-language'] = 'en';
						}
						
						return update_option(YT4WP_OPTION, $this->optionVal);
					
					}
				return false;
				}
				
			/*
			*  updateErrorLogCountOption()
			*  Updates the number of items stored in our error log
			*
			*  @since 2.0.2
			*/
			public function updateErrorLogCountOption( $new_count ) {
					if( $new_count < 5 ) { // min 5
						$new_count = 5;
					} else if ( $new_count > 20 ) { // max 20
						$new_count = 20;
					}
					$this->optionVal['yt4wp-limit-error-log-count'] = $new_count;
				}
		
			/*
			*  updateLicenseOptions()
			*  Updates the various options on the 
			*  license settings page
			*
			*  @since 2.0
			*/
			public function updateLicenseOptions( $p ) {
					if( !empty( $p['form_data'] ) ) {
							parse_str( $p['form_data'] , $fd );
							$this->optionVal['yt4wp-license-key'] = $fd['yt4wp-license-key'];
							return update_option( YT4WP_OPTION , $this->optionVal );
						}
					return false;
				}
					
			
		/***** YouTube for WordPress Actions
		 ****************************************************************************************************/		 
			/*
			*  yt_plus_display_upsell_banner()
			*  Display the upsell banner on various settings pages
			*
			*  @since 2.0
			*/	
		public function yt_plus_display_upsell_banner() { 
				?>
					<div id="yt4wp-settings-sidebar">
					<?php 
						// referrel variable
						if ( isset( $_GET['tab'] ) ) {
							$referrel_page = $_GET['page'] . '-' . $_GET['tab'] . '-';
						} else {
							if ( isset( $_GET['page'] ) ) {
								$referrel_page = $_GET['page'] . '-';
							} else {
								$referrel_page = get_current_screen()->base; 
							}
						}
						$page_base = get_current_screen()->base; 
						if ( $page_base == 'admin_page_youtube-for-wordpress-welcome' && get_option( 'yt4wp_user_refresh_token' ) == '' ) { ?>
							<div class="cta padding-bottom">
								<h3><div class="dashicons dashicons-admin-settings" style="line-height:.85;"></div> Get Started</h3>
								<hr />
						  <a class="visit" target="blank" href="<?php echo admin_url("admin.php?page=youtube-for-wordpress-settings"); ?>"><i class="dashicons dashicons-arrow-right" style="line-height:.6"></i> Enter Your Settings</a>
						</div>
					<?php } ?>
					<!-- support info -->						
						<div class="cta padding-bottom">
							<h3><div class="dashicons dashicons-format-status"></div> Support</h3>
							<hr />
							<ul>
							   <li>
								 <p>
								  <strong>Looking for Support?</strong><br/>
								  <a href="https://yt4wp.com/support/?utm_source=<?php echo $referrel_page; ?>yt4wp-sidebar&utm_medium=text-link&utm_campaign=yt4wp-sidebar" target="blank">Support</a> - Purchase a license for access to our support ticketing system and 24/7 email support.
								 </p>
							   </li>
							   <li>
								  <p>
									  <strong>Looking for something else?</strong><br/>
									  <ul>
										<li><a href="http://www.yt4wp.com/?utm_source=<?php echo $referrel_page; ?>yt4wp-sidebar&utm_medium=text-link&utm_campaign=yt4wp-sidebar" target="blank">Tutorials</a> - Learn more about the plugin through the various tutorials setup on the site.</li>
										<li><a href="http://www.yt4wp.com/documentation/?utm_source=<?php echo $referrel_page; ?>yt4wp-sidebar&utm_medium=text-link&utm_campaign=yt4wp-sidebar" target="blank">Documentation</a> - Get help with the initial setup and tips and tricks on certain functionality of YouTUbe for WordPress.</li>
									  </ul>
								  </p>
							   </li>
						  </ul>
						  <a class="visit" target="blank" href="http://www.yt4wp.com/buy-yt4wp/?utm_source=<?php echo $referrel_page; ?>yt4wp-sidebar&utm_medium=cta-button&utm_campaign=yt4wp-sidebar"><i class="dashicons dashicons-arrow-right" style="line-height:.6"></i> Purchase a License</a>
						</div>
					<!-- upsell add-ons info -->
						<div class="cta padding-bottom">
							<h3><div class="dashicons dashicons-plus" style="line-height:1;"></div> Premium Add-Ons</h3>
							<hr />
							<ul>
							   <li>
								<p>
								  <strong>Extend. Expand. Upgrade. </strong><br/>
								  Premium add-ons extend YouTube for WordPress beyond what it is capeable outside of the box. Check out some of the current <a href="http://www.yt4wp.com/?utm_source=<?php echo $referrel_page; ?>yt4wp-sidebar&utm_medium=text-link&utm_campaign=yt4wp-sidebar" target="_blank">Add Ons</a> as well as some planned for future release.
								</p>
							   </li>
							   <li>
								<p>
								  <strong>Developers </strong><br/>
								  You too can develop premium add-ons to sell in our digital marketplace. <a href="http://www.yt4wp.com/support/documentation?utm_source=<?php echo $referrel_page; ?>yt4wp-sidebar&utm_medium=text-link&utm_campaign=yt4wp-sidebar" onclick="return false;">learn more</a>
								</p> 
							   </li>
						  </ul>
						  <a class="visit" target="blank" href="http://www.yt4wp.com/add-ons-overview/?utm_source=<?php echo $referrel_page; ?>yt4wp-sidebar&utm_medium=cta-button&utm_campaign=yt4wp-sidebar"><i class="dashicons dashicons-arrow-right" style="line-height:.6"></i> View Add-Ons</a>
						</div>
					<!-- support the development info -->
						<div class="cta">
							<h3><div class="dashicons dashicons-star-filled" style="line-height:.85;"></div> Share, Rate and Support</h3>
							<hr />
							<ul>
							   <li>
								 <p>If you're not interested in purchasing an add-on or a support license, why not share and rate the plugin?</p>
								 <span class="yt4wp-srs-button-container"><a href="https://wordpress.org/support/view/plugin-reviews/wp2yt-uploader" target="_blank" class="button-secondary yt4wp-srs-button">Review</a><a href="https://twitter.com/yt4wp" target="_blank" class="button-secondary yt4wp-srs-button">Follow</a><a class="button-secondary yt4wp-srs-button" href="http://www.twitter.com/yt4wp" target="_blank">Share</a></span>
							   </li>
						  </ul>
						</div>
					</div>
				<?php
			}
				
			/*
			*  yt_plus_contact_support_banner()
			*  Display the contact/support banner on 
			*  the main plugin page
			*
			*  @since 2.0
			*/			
			function yt_plus_contact_support_banner() {
					// referrel variable
					if ( isset( $_GET['tab'] ) ) {
						$referrel_page = $_GET['page'] . '-' . $_GET['tab'] . '-';
					} else {
						if ( isset( $_GET['page'] ) ) {
							$referrel_page = $_GET['page'] . '-';
						} else {
							$referrel_page = get_current_screen()->base; 
						}
					}
				?>
					<!-- yt4wp logo on all settings pages -->
					<div id="yt_plus_review_this_plugin_container">
						<a href="http://www.yt4wp.com/support/?utm_source=<?php echo $referrel_page; ?>&utm_medium=header-image&utm_campaign=review-header-box" target="_blank">
							<span class="yt_plus_need_support">
								<strong>
									<?php _e( 'Need Help?', 'youtube-for-wordpress' ); ?> <br />
									<?php _e( 'Get Support!', 'youtube-for-wordpress' ); ?> <br />
									<div class="dashicons dashicons-plus-alt"></div>
								</strong>
							</span>
						</a>
						<a href="https://wordpress.org/support/view/plugin-reviews/wp2yt-uploader" target="_blank">
							<span class="yt_plus_leave_us_a_review">
								<strong>
									<?php _e( 'Enjoying the plugin?', 'youtube-for-wordpress' ); ?> <br />
									<?php _e( 'Leave us a review', 'youtube-for-wordpress' ); ?> <br />
									<div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div>
								</strong>
							</span>
						</a>
						<a href="http://www.yt4wp.com/?utm_source=<?php echo $referrel_page; ?>&utm_medium=header-image&utm_campaign=review-header-box" target="_blank" class="yt4wp_header_logo">
							<img src="<?php echo plugins_url().'/wp2yt-uploader/images/evan_herman_logo.png'; ?>" alt="YouTube for WordPress Site" width=80 style="margin-top:.75em;" title="YouTube for WordPress Site" />
						</a>
					</div>
				<?php
				}
	 
			/*
			*  searchYouTube()
			*  Search YouTube ajax functionality
			*  returns search results
			*
			*  Parameters : search_term , max_results , search_type , sort_results_by 
			*  advanced_search_checkbox, upload_date_timeframe, screen_base
			*
			*  @since 2.0
			*/			
			function searchYouTube($search_term,$max_results,$search_type,$sort_results_by,$advanced_search_checkbox,$upload_date_timeframe,$screen_base) {	

					$htmlBody = '';

					// This code will execute if the user entered a search query in the form
					// and submitted the form. Otherwise, the page displays the form above.
					if ( isset( $search_term ) ) {
						// load thickbox styles etc.
						add_thickbox();
							
						// include the required php files - containers api key
						include_once YT4WP_PATH.'lib/google_api_wrapper_api_key.php';
					  
					  try {
						
						// if advanced search was not checked
						if ( $advanced_search_checkbox != 'true' ) {
							// default search
								// search term - custom
								// display - 25 results
								// order - relevance
								// region - from settings
								// type - video
							$searchResponse = $youtube->search->listSearch('id,snippet', array(
							  'q' => stripslashes( $search_term ),
							  'maxResults' => '25',
							  'order' => 'relevance',
							  'regionCode' => $this->optionVal['yt4wp-region'],
							  'type' => 'video'
							));
							
							$search_type = 'video';
							
						} else {
						
							// advanced search!	
							if ( $upload_date_timeframe == 'all_time' ) {
								
								if ( $search_type == 'channel' ) {
									
									// Call the search.list method to retrieve results matching the specified
									// query term.
									$searchResponse = $youtube->search->listSearch('id,snippet', array(
										  'q' => stripslashes( $search_term ),
										  'maxResults' => $max_results,
										  'order' => $sort_results_by,
										  'regionCode' => $this->optionVal['yt4wp-region'],
										  'type' => $search_type
										));
									
								} else {
								
									// Call the search.list method to retrieve results matching the specified
									// query term.
									$searchResponse = $youtube->search->listSearch('id,snippet', array(
										  'q' => stripslashes( $search_term ),
										  'maxResults' => $max_results,
										  'order' => $sort_results_by,
										  'regionCode' => $this->optionVal['yt4wp-region'],
										  'type' => $search_type
										));
								}
								
							} else {
							
								// set up our timeframe variables 
								$cur_time = current_time( 'H:i:s' );	 
								$pub_before = date("Y-m-d\TH:i:s\Z", strtotime($cur_time) );
								if ( $upload_date_timeframe == 'past_hour' ) {
									$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-60 minutes', strtotime($cur_time) ) ); // 60 minutes ago
								} else if ( $upload_date_timeframe == 'past_day' ) { 
									$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-1 day', strtotime($cur_time) ) ); // 1 day ago ago				 
								} else if ( $upload_date_timeframe == 'past_week' ) { 
									$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-1 week', strtotime($cur_time) ) ); // 1 week ago
								} else if ( $upload_date_timeframe == 'past_month' ) { 
									$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-1 month', strtotime($cur_time) ) ); // 1 month ago
								} else if ( $upload_date_timeframe == 'past_year' ) { 
									$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-1 year', strtotime($cur_time) ) ); // 1 year ago
								}
								
								if ( $search_type == 'channel' ) {
									
									// Call the search.list method to retrieve results matching the specified
									// query term.
									$searchResponse = $youtube->search->listSearch('id,snippet', array(
										  'q' => stripslashes( $search_term ),
										  'maxResults' => $max_results,
										  'order' => $sort_results_by,
										  'regionCode' => $this->optionVal['yt4wp-region'],
										  'type' => $search_type
										));
									
								} else {
									
									// Call the search.list method to retrieve results matching the specified
									// query term.
									$searchResponse = $youtube->search->listSearch('id,snippet', array(
										  'q' => stripslashes( $search_term ),
										  'maxResults' => $max_results,
										  'order' => $sort_results_by,
										  'regionCode' => $this->optionVal['yt4wp-region'],
										  'publishedAfter' => $adjusted_time,
										  'type' => $search_type
										));
									
								}

							}
							
						}
						
						$videos = '';
						$channels = '';
						$playlists = '';

						// set dialog drawer
						$dialog_drawer = '<section class="dialog_message_drawer"></section>';
						
						// looping!	
						foreach ($searchResponse['items'] as $searchResult) {
							
							// check if the setting is set, so we don't run unecessary API requests
								if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
								
									if ( isset( $searchResult['id']['videoId'] ) ) {
									// loop to get video statistics...
										$video_statistics = $youtube->videos->listVideos('statistics', array(
											'id' => $searchResult['id']['videoId']
										 ));
										
										// build the video stats array
										foreach( $video_statistics as $stat ) {
											// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
											$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
											$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
											$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
											$view_count = number_format( $stat['statistics']['viewCount'] );
											
											$stat_container = '<span class="yt-plus-stats-container">';
												$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
												$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
											$stat_container .= '</span>';	
										}
									}	
								} else { // if the setting is disabled
									// set the stat container to none
									$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
								}		
							
						  
							  // echo $subscriptionsItem['snippet']['resourceId']['channelId'];
									if($searchResult['modelData']['snippet']['description']) {
										// trim the description
										// if there are more than 400 characters
										if(strlen($searchResult['modelData']['snippet']['description']) > 325) {
											$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($searchResult['modelData']['snippet']['description'], 0, 400).'...'; 
										} else {
											$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$searchResult['modelData']['snippet']['description']; 
										}
									} else {
										$video_description = ''; 
									}
							// display different content based on the search type
							// videos, channels, or playlists
							switch ( $search_type ) {
							
								// video search
								case 'video':	
									  
									$videos .= '<li class="youtube-plus-video-single-list-item">';
										$videos .= '<input type="hidden" class="video_id" value="' . $searchResult['id']['videoId'] . '">';
											$videos .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $searchResult['id']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';	
											$videos .= '<section class="yt-plus-outside-hidden">' . $dialog_drawer . '<img class="youtube-plus-video-thumbnail" src="' . $searchResult['snippet']['thumbnails']['high']['url'] . '"></a>'; 
											$videos .= '<section class="drawer">' . apply_filters( 'yt4wp_likes_favs_history_buttons', $this->yt4wp_get_likes_favs_history_buttons($screen_base) ) . '</section></section>' . $stat_container; 
											$videos .= '<h3 class="youtube-plus-video-title-container">' . $searchResult['snippet']['title'] . '</h3>'; 
										$videos .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
									$videos .= '</li>';
									  
								break;
								
								// channel search
								case 'channel':
									
									$videos .= '<li class="youtube-plus-video-single-list-item">';
										$videos .= '<input type="hidden" class="channel_id" value="' . $searchResult['modelData']['id']['channelId'] . '">';
										$videos .= '<section class="yt-plus-outside-hidden">';
											$videos .= '<a class="view-subscription-videos-btn" alt="' . $searchResult['modelData']['id']['channelId'] . '" title="' . $searchResult['modelData']['snippet']['title'] . '" href="#" onclick="return false;">' . $dialog_drawer; 
												$videos .= '<img class="youtube-plus-video-thumbnail" src="' . $searchResult['modelData']['snippet']['thumbnails']['medium']['url'] . '">';
											$videos .= '</a><section class="drawer">' . apply_filters( 'yt4wp_channel_results_buttons', $this->yt4wp_get_channel_results_buttons( $screen_base ) ) . '</section>';
										$videos .= '</section>' . $stat_container; 
										$videos .= '<h3>' . $searchResult['modelData']['snippet']['title'] . '</h3>' . $video_description; 
									$videos .= '</li>';
									
								break;
								
								case 'playlist':	
									
									// resetup the insert buttons 
									// specific to playlists
										$videos .= '<li class="youtube-plus-video-single-list-item">';
											$videos .= '<input type="hidden" class="playlist_id" value="' . $searchResult['modelData']['id']['playlistId'] . '">';
											$videos .= '<a class="youtube-plus-view-playlist" href="#" onclick="return false;">';
												$videos .= '<section class="yt-plus-outside-hidden"><img class="youtube-plus-video-thumbnail" src="' . $searchResult['modelData']['snippet']['thumbnails']['high']['url'] . '"></a>';
												$videos .= '<section class="drawer">' . apply_filters( 'yt4wp_playlist_results_buttons', $this->yt4wp_get_playlist_results_buttons( $screen_base ) ) . '</section></section>' . $stat_container; 
											$videos .= '<h3>' . $searchResult['modelData']['snippet']['title'] . '</h3>' . $searchResult['modelData']['snippet']['description']; 
										$videos .= '</li>';
										
								break;

							}
						}

						$results = $search_term;
						
						// if the search returns results from Youtube
						if ( $videos ) {
							if ( $search_term == '' ) {
								$videos_container = sprintf("<h3 class='youtube-plus-video-preview-title'>Trending %ss</h3>%s<ul id='masonry-container' class='youtube-plus-video-list'>%s</ul>%s" , ucwords( $search_type ), $this->yt4wp_pagination( $searchResponse , 'search' ), $videos, $this->yt4wp_pagination( $searchResponse , 'search' ) );
							} else {
								$videos_container = sprintf("<h3 class='youtube-plus-video-preview-title'>Showing %ss for term '%s'</h3>%s<ul id='masonry-container' class='youtube-plus-video-list'>%s</ul>%s" , $search_type, stripslashes( $search_term ) , $this->yt4wp_pagination( $searchResponse , 'search' ) , $videos, $this->yt4wp_pagination( $searchResponse , 'search' ) );			
							}
						} else {
							$videos_container = sprintf('<h4>It looks like no videos were found for "%s".' , stripslashes( $search_term ) );
						}	

						$htmlBody .= $videos_container;

					  } catch (Google_ServiceException $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());						
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
					  
					  } catch (Google_Exception $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
					 
					 }
					
					}
					?>

						<?php echo $htmlBody; ?>

					<?php
				}
			
			/*
			*  searchUserUploads()
			*  Search the current users uploads
			*  returns videos that match search term
			*
			*  Parameters : search_term , channel_id , screen_base 
			*
			*  @since 2.0
			*/		
			function searchUserUploads($search_term,$channel_id,$screen_base) {	

					$htmlBody = '';

					// This code will execute if the user entered a search query in the form
					// and submitted the form. Otherwise, the page displays the form above.
					if ( isset( $search_term ) ) {
						// load thickbox styles etc.
						add_thickbox();
							
						// include the required php files - containers api key
						include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					  
						  try {
								
								// Call the search.list method to retrieve results matching the specified
								// query term.
								$searchResponse = $youtube->search->listSearch('id,snippet', array(
								  'q' => $search_term,
								  'forMine' => 'true',
								  'type' => 'video',
								  'maxResults' => 50
								));
							
							$videos = '';
							
							$dialog_drawer = '<section class="dialog_message_drawer"></section>';
							
							// looping!
							foreach ($searchResponse['items'] as $searchResult) {
								
								// check if the setting is set, so we don't run unecessary API requests
									if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
									
										if ( isset( $searchResult['id']['videoId'] ) ) {
										// loop to get video statistics...
											$video_statistics = $youtube->videos->listVideos('statistics', array(
												'id' => $searchResult['id']['videoId']
											 ));
											
											// build the video stats array
											foreach( $video_statistics as $stat ) {
												// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
												$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
												$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
												$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
												$view_count = number_format( $stat['statistics']['viewCount'] );
												
												$stat_container = '<span class="yt-plus-stats-container">';
													$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
													$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
												$stat_container .= '</span>';	
											}
										}	
									} else { // if the setting is disabled
										// set the stat container to none
										$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
									}		
								
										if($searchResult['modelData']['snippet']['description']) {
											// trim the description
											// if there are more than 400 characters
											if(strlen($searchResult['modelData']['snippet']['description']) > 325) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($searchResult['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$searchResult['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}
										
									// display different content based on the search type
									// videos, channels, or playlists
									$videos .= '<li class="youtube-plus-video-single-list-item">';
										$videos .= '<input type="hidden" class="video_id" value="' . $searchResult['id']['videoId'] . '">';
										$videos .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $searchResult['id']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';
										$videos .= '<section class="yt-plus-outside-hidden">' . $dialog_drawer . '<img class="youtube-plus-video-thumbnail" src="' . $searchResult['snippet']['thumbnails']['high']['url'] . '"></a>';
										$videos .= '<section class="drawer">' . apply_filters( 'yt4wp_browse_buttons', $this->yt4wp_get_browse_buttons($screen_base) ) . '</section></section>' . $stat_container; 
										$videos .= '<h3 class="youtube-plus-video-title-container">' . $searchResult['snippet']['title'] . '</h3>'; 
										$videos .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
									$videos .= '</li>';
									
							}
							
							$results = $search_term;
							
							// if the search returns results from Youtube 
							// to do - fix pagination user browse
							if ( $videos ) {
								$videos_container = sprintf( '%s' , $videos );
							} else {
								$videos_container = sprintf('<h4>It looks like there were no videos found for "%s".' , stripslashes( $search_term ) );
							}
							
							$htmlBody .= $videos_container;

					  } catch (Google_ServiceException $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  } catch (Google_Exception $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  }
					  
					}
					?>

						<?php echo $htmlBody; ?>

					<?php
				}

			/*
			*  paginate_youtube_search()
			*  Paginate search results
			*  returns next/previous results
			*
			*  Parameters : search_term , max_results , search_type , sort_results_by 
			*  advanced_search_checkbox, upload_date_timeframe, screen_base
			*
			*  @since 2.0
			*/			
			function paginate_youtube_search($page_token,$search_term,$max_results,$search_type,$sort_results_by,$advanced_search_checkbox,$upload_date_timeframe,$screen_base) {	
					// This code will execute if the user entered a search query in the form
					// and submitted the form. Otherwise, the page displays the form above.
					if ( isset($search_term) && isset($search_term) ) {
						// load thickbox styles etc.
						add_thickbox();
						
						// include the required php files - containers api key
						include_once YT4WP_PATH.'lib/google_api_wrapper_api_key.php';

					  try {
					  
						$htmlBody = '';
						
						// if advanced search was not checked
						if ( $advanced_search_checkbox != 'true' ) {
							// default search
								// options =>
									// search term - custom
									// 25 results
									// order by relevance
									// region - from settings
									// type - video
							$searchResponse = $youtube->search->listSearch('id,snippet', array(
							  'q' => $search_term,
							  'maxResults' => '25',
							  'order' => 'relevance',
							  'regionCode' => $this->optionVal['yt4wp-region'],
							  'pageToken' => $page_token,
							  'type' => 'video'
							));
							$search_type = 'video';
						} else {
							// start working
								if ( $upload_date_timeframe == 'all_time' ) {
									if ( $search_type == 'channel' ) {
										// Call the search.list method to retrieve results matching the specified
										// query term.
										$searchResponse = $youtube->search->listSearch('id,snippet', array(
										  'q' => stripslashes( $search_term ),
										  'maxResults' => $max_results,
										  'order' => $sort_results_by,
										  'regionCode' => $this->optionVal['yt4wp-region'],
										  'pageToken' => $page_token,
										  'type' => $search_type
										));
									} else {
										// Call the search.list method to retrieve results matching the specified
										// query term.
										$searchResponse = $youtube->search->listSearch('id,snippet', array(
										  'q' => stripslashes( $search_term ),
										  'maxResults' => $max_results,
										  'order' => $sort_results_by,
										  'regionCode' => $this->optionVal['yt4wp-region'],
										  'pageToken' => $page_token,
										  'type' => $search_type
										));
									}
								} else {
									// set up our timeframe variables
									// echo current_time("Y-m-d\TH:i:s\Z"); 
									$cur_time = current_time( 'H:i:s' );	 
									if ( $upload_date_timeframe == 'past_hour' ) {
										$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-60 minutes', strtotime($cur_time) ) ); // 60 minutes ago
									} else if ( $upload_date_timeframe == 'past_day' ) { 
										$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-1 day', strtotime($cur_time) ) ); // 1 day ago ago				 
									} else if ( $upload_date_timeframe == 'past_week' ) { 
										$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-1 week', strtotime($cur_time) ) ); // 1 week ago
									} else if ( $upload_date_timeframe == 'past_month' ) { 
										$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-1 month', strtotime($cur_time) ) ); // 1 month ago
									} else if ( $upload_date_timeframe == 'past_year' ) { 
										$adjusted_time = date("Y-m-d\TH:i:s\Z", strtotime( '-1 year', strtotime($cur_time) ) ); // 1 year ago
									}
									
									if ( $search_type == 'channel' ) {
										// Call the search.list method to retrieve results matching the specified
										// query term.
										$searchResponse = $youtube->search->listSearch('id,snippet', array(
										  'q' => stripslashes( $search_term ),
										  'maxResults' => $max_results,
										  'order' => $sort_results_by,
										  'regionCode' => $this->optionVal['yt4wp-region'],
										   'pageToken' => $page_token,
										  'type' => $search_type
										));
									} else {
										// Call the search.list method to retrieve results matching the specified
										// query term.
										$searchResponse = $youtube->search->listSearch('id,snippet', array(
										  'q' => stripslashes( $search_term ),
										  'maxResults' => $max_results,
										  'order' => $sort_results_by,
										  'regionCode' => $this->optionVal['yt4wp-region'],
										  'publishedAfter' => $adjusted_time,
										   'pageToken' => $page_token,
										  'type' => $search_type
										));
									}
									// test the time frame variable
									// echo $adjust_time; 
								}
							// end 
						}
						
						$videos = '';
						$channels = '';
						$playlists = '';

						// add dialog drawer
						$dialog_drawer = '<section class="dialog_message_drawer"></section>';
						
						foreach ($searchResponse['items'] as $searchResult) {
						
							// check if the setting is set, so we don't run unecessary API requests
								if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
								
								// loop to get video statistics...
									$video_statistics = $youtube->videos->listVideos('statistics', array(
										'id' => $searchResult['id']['videoId']
									 ));
									
									// build the video stats array
									foreach( $video_statistics as $stat ) {
										// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
										$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
										$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
										$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
										$view_count = number_format( $stat['statistics']['viewCount'] );
										
										$stat_container = '<span class="yt-plus-stats-container">';
											$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
											$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
										$stat_container .= '</span>';	
									}
										
								} else { // if the setting is disabled
									// set the stat container to none
									$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
								}
							
								 // echo $subscriptionsItem['snippet']['resourceId']['channelId'];
									if($searchResult['modelData']['snippet']['description']) {
										// trim the description
										// if there are more than 400 characters
										if(strlen($searchResult['modelData']['snippet']['description']) > 325) {
											$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($searchResult['modelData']['snippet']['description'], 0, 400).'...'; 
										} else {
											$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$searchResult['modelData']['snippet']['description']; 
										}
									} else {
										$video_description = ''; 
									}
							
							switch ( $search_type ) {
								// video search
								case 'video':	
									  
									$videos .= '<li class="youtube-plus-video-single-list-item">';
										$videos .= '<input type="hidden" class="video_id" value="' . $searchResult['id']['videoId'] . '">';
											$videos .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $searchResult['id']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';	
											$videos .= '<section class="yt-plus-outside-hidden">' . $dialog_drawer . '<img class="youtube-plus-video-thumbnail" src="' . $searchResult['snippet']['thumbnails']['high']['url'] . '"></a>'; 
											$videos .= '<section class="drawer">' . apply_filters( 'yt4wp_likes_favs_history_buttons', $this->yt4wp_get_likes_favs_history_buttons($screen_base) ) . '</section></section>' . $stat_container; 
											$videos .= '<h3 class="youtube-plus-video-title-container">' . $searchResult['snippet']['title'] . '</h3>'; 
										$videos .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
									$videos .= '</li>';
									  
								break;
								
								// channel search
									// evan herman here
								case 'channel':	
								
									$videos .= '<li class="youtube-plus-video-single-list-item">';
										$videos .= '<input type="hidden" class="channel_id" value="' . $searchResult['modelData']['id']['channelId'] . '">';
										$videos .= '<section class="yt-plus-outside-hidden">';
											$videos .= '<a class="view-subscription-videos-btn" alt="' . $searchResult['modelData']['id']['channelId'] . '" title="' . $searchResult['modelData']['snippet']['title'] . '" href="#" onclick="return false;">' . $dialog_drawer; 
												$videos .= '<img class="youtube-plus-video-thumbnail" src="' . $searchResult['modelData']['snippet']['thumbnails']['high']['url'] . '">';
											$videos .= '</a><section class="drawer">' . apply_filters( 'yt4wp_channel_results_buttons', $this->yt4wp_get_channel_results_buttons($screen_base) ) . '</section>';
										$videos .= '</section>' . $stat_container; 
										$videos .= '<h3>' . $searchResult['modelData']['snippet']['title'] . '</h3>' . $video_description; 
									$videos .= '</li>';
										
								break;
								
								case 'playlist':	
									// resetup the insert buttons 
									// specific to playlists
													
										$videos .= '<li class="youtube-plus-video-single-list-item">';
											$videos .= '<input type="hidden" class="playlist_id" value="' . $searchResult['modelData']['id']['playlistId'] . '">';
											$videos .= '<a class="youtube-plus-video-preview-btn youtube-plus-view-playlist" href="#" onclick="return false;">';
												$videos .= '<section class="yt-plus-outside-hidden"><img class="youtube-plus-video-thumbnail" src="' . $searchResult['modelData']['snippet']['thumbnails']['high']['url'] . '"></a>';
												$videos .= '<section class="drawer">' . apply_filters( 'yt4wp_playlist_results_buttons', $this->yt4wp_get_playlist_results_buttons($screen_base) ) . '</section></section>' . $stat_container; 
											$videos .= '<h3>' . $searchResult['modelData']['snippet']['title'] . '</h3>' . $searchResult['modelData']['snippet']['description']; 
										$videos .= '</li>';
									
								break;
							}
						}
							
						$results = $search_term;
						// if the search returns results from Youtube
						if ( $videos ) {
							if ( $search_term == '' ) {
								$videos_container = sprintf("<h3 class='youtube-plus-video-preview-title'>Trending %ss</h3>%s<ul id='masonry-container' class='youtube-plus-video-list'>%s</ul>%s" , ucwords( $search_type ),  $this->yt4wp_pagination( $searchResponse , 'search' ) , $videos,  $this->yt4wp_pagination( $searchResponse , 'search' ) );
							} else {
								$videos_container = sprintf("<h3 class='youtube-plus-video-preview-title'>Showing %ss for term '%s'</h3>%s<ul id='masonry-container' class='youtube-plus-video-list'>%s</ul>%s" , $search_type, stripslashes( $search_term ) ,  $this->yt4wp_pagination( $searchResponse , 'search' ) , $videos,  $this->yt4wp_pagination( $searchResponse , 'search' ) );			
							}
						} else {
							$videos_container = sprintf('<h4>It looks like there were no videos found for "%s".' , stripslashes( $search_term ) );
						}
						$htmlBody .= $videos_container;
					  } catch (Google_ServiceException $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  } catch (Google_Exception $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  }
					}
					?>

					<?php echo $htmlBody; ?>

				<?php
				}
								
			/*
			*  paginate_youtube_browse()
			*  Paginate user uploads (uploads, playlists, likes etc.)
			*  returns next/previous results
			*
			*  Parameters : page_token , screen_base , clicked_button , playlist_id 
			*
			*  @since 2.0
			*/
			function paginate_youtube_browse( $page_token , $screen_base , $clicked_button , $playlist_id ) {
					
					// include the required php files - containers client_id and client_secret
					@include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
					// Call set_include_path() as needed to point to your client library.
					require_once YT4WP_PATH.'inc/Google/Client.php';
					require_once YT4WP_PATH.'inc/Google/Service/YouTube.php';
					/* 
					* Check if session has started 
					* On callback, it tries to restart the session
					* Throwing an error
					*/
					if(!isset($_SESSION)) { 
							session_start();
						}
					$ytPlusBase = new YT4WPBase();
					$OAUTH2_CLIENT_ID = $ytPlusBase->optionVal['yt4wp-oauth2-key'];
					$OAUTH2_CLIENT_SECRET = $ytPlusBase->optionVal['yt4wp-oauth2-secret'];
					$client = new Google_Client();
					$client->setClientId($OAUTH2_CLIENT_ID);
					$client->setClientSecret($OAUTH2_CLIENT_SECRET);
					$client->setScopes('https://www.googleapis.com/auth/youtube');
					$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?page=youtube-for-wordpress', FILTER_SANITIZE_URL);
					$client->setRedirectUri($redirect);
					// Define an object that will be used to make all API requests.
					$youtube = new Google_Service_YouTube($client);
						if ( !isset( $screen_base ) || $screen_base == '' ) {
							$screen_base = get_current_screen();
							if ( is_object( $screen_base ) ) {
								$screen_base = get_current_screen()->base;
							} else {
								$screen_base = $screen_base['base'];
							}
						}
						
						if ($client->isAccessTokenExpired()) {
								// check if the user has ANY data stored in the token session
								// if they do, but the token has expired, we simply call a refresh
								// on the refresh token to retreive new access tokens
								//
								// we do this to avoid re-authorizing every time you use
								// the plugin -- this is all done behind the scenes ;)
								if ( get_option( 'yt4wp_user_refresh_token' ) != '' ) {
								
									$client->refreshToken( get_option( 'yt4wp_user_refresh_token' ) );
									$_SESSION['token'] = $client->getAccessToken();
									
								} else {
									
									$htmlBody = '';
									// If the user hasn't authorized the app, initiate the OAuth flow
									$state = mt_rand();
									$client->setState($state);
									$_SESSION['state'] = $state;
									$authUrl = $client->createAuthUrl();
									$htmlBody .= '<div class="error" style="margin-top:2em;">
										<h3>'.__("YouTube Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
										<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
									</div>'; 
								
								}
								
							}
								
						try {
							  
							// Call the channels.list method to retrieve information about the
							// currently authenticated user's channel.
							$channelsResponse = $youtube->channels->listChannels('contentDetails,snippet', array(
								  'mine' => 'true'
								));
							$htmlBody = '';
							// set up the dialog drawer
							$dialog_drawer = '<section class="dialog_message_drawer"></section>';
							  
							// we can use the channelsResponse to get the playlist ID's for --
								/*
									- likes
									- favorites
									- uploads
									- watch history
									- watch later
									- google plus user id (not a playlist id, just useable to link to google+)
								*/
							foreach ($channelsResponse['items'] as $playlist) {
							
							  // Extract the unique playlist ID that identifies the list of videos
							  // uploaded to the channel, and then call the playlistItems.list method
							  // to retrieve that list.
							  $uploadsListId = $playlist['contentDetails']['relatedPlaylists']['uploads'];
							  $likesListId = $playlist['contentDetails']['relatedPlaylists']['likes'];
							  $watchHistoryListId = $playlist['contentDetails']['relatedPlaylists']['watchHistory'];
							  $watchLaterListId = $playlist['contentDetails']['relatedPlaylists']['watchLater'];
							  $favoritesListId = $playlist['contentDetails']['relatedPlaylists']['favorites'];
								
								
								if ( $clicked_button == '' ) { // default query on page load
									 $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('status,snippet', array(
											'playlistId' =>  $uploadsListId,
											// maybe set up pagination here, for users
											// who have more than 50 vids
											'maxResults' => 50,
											'pageToken' => $page_token
										));
									  $pagination = $this->yt4wp_pagination( $playlistItemsResponse , 'browse' );
								} else if ( $clicked_button == 'playlists' ) { // query a specific playlist
									$playlistsResponse = $youtube->playlists->listPlaylists('status,snippet,contentDetails', array(
											'mine' =>  'true',
											// maybe set up pagination here, for users
											// who have more than 50 vids
											'maxResults' => 50,
											'pageToken' => $page_token
										));
									 $pagination = $this->yt4wp_pagination( $playlistsResponse , 'browse' );
								} else { // all other queries
									$playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('status,snippet', array(
											'playlistId' =>  $playlist_id,
											// maybe set up pagination here, for users
											// who have more than 50 vids
											'maxResults' => 50,
											'pageToken' => $page_token
										 ));
									  $pagination = $this->yt4wp_pagination( $playlistItemsResponse , 'browse' );
								}
									
									
								if ( isset( $playlistItemsResponse ) ) {
									if ( count ( $playlistItemsResponse['items'] ) > 0 ) {
										$channel_name = $playlistItemsResponse['items'][0]['modelData']['snippet']['channelTitle'];
									} else {
										$channel_name = '';
									}
								}
								
								
							  if ( $clicked_button == 'Uploads' || $clicked_button == '' || !isset($clicked_button) ) {
								// if items are found in the playlist			
								if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {		
									$search_my_videos_button = '<form id="search_my_playlists_form"><input type="search" id="yt-plus-upload-search" class="module-search" placeholder="Search Your Uploads"><label for="yt-plus-upload-search">Search</label></form>';
									$video_count = count($playlistItemsResponse['items']);
									$htmlBody .= "<h3>Channel : $channel_name</h3><h4 class='yt4wp-video-count'>$video_count Videos</h4>" . $search_my_videos_button . $pagination . "<ul id='masonry-container'>";
								} else {
									$htmlBody .= "<h3>Channel : $channel_name</h3><h4 class='yt4wp-video-count'>0 Videos Found</h4><ul id='masonry-container'>";
								}
							  } else {
								$htmlBody .= "<h3>Your $clicked_button</h3>" . $pagination . "<ul id='masonry-container'>";
							  }
							  
							  
							  // Browse => Playlists
							  if ( $clicked_button == 'Playlists' ) {
								
								// add empty stat container to unify styles throughout the plugin
								if ( count( $playlistsResponse['items'] ) > 0 ) {
									 foreach ($playlistsResponse['items'] as $playlist) {
										// Grab Video Count
										$number_of_videos = $playlist['contentDetails']['itemCount'];
										
										// add a video count to our stat container for playlists
										$stat_container = '<span class="yt-plus-stats-container number_of_videos_in_playlist">'.$number_of_videos.' videos</span>';
										
											// build our playlist UI
											$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
												$htmlBody .= '<input type="hidden" class="playlist_id" value="' . $playlist['id'] . '">';
													$htmlBody .= '<a class="youtube-plus-view-playlist" href="#" onclick="return false;">';
														$htmlBody .= '<section class="yt-plus-outside-hidden">';
														$htmlBody .= '<img class="youtube-plus-video-thumbnail" src="' .  $playlist['modelData']['snippet']['thumbnails']['high']['url'] . '">';
													$htmlBody .= '</a>';
													$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_playlist_buttons', $this->yt4wp_get_playlist_buttons($screen_base) ) . '</section>';
														$htmlBody .= '</section>' . $stat_container;
													$htmlBody .= '<h3><span class="playlist_title">' . $playlist['modelData']['snippet']['title'] . '</span></h3>';
													$htmlBody .= '<p class="youtube-plus-video-description">' . $playlist['modelData']['snippet']['description'] . '</p>'; 
											$htmlBody .= '</li>';
									  
									  }
								 } else {
									?>
									<style>#masonry-container{height:auto !important;}</style>
									<?php
									$htmlBody .= '<span class="no_content_found_error">You have not created any playlists yet. Why not <a href="#" style="margin-top:-.5em;" disabled="disabled" title="Create New Playlist (add on not installed)" class="button-secondary yt-plus-create-new-playlist">Create One</a> now?</span>';
								 }
								 
								 // Browse => Uploads
							  } else if ( $clicked_button == 'Uploads' || $clicked_button == '' ) {
											
								// the first item in this list is the privacy status of the video
								// we need to display the lock icon etc for private vs non-private videos
								
								// if items are found in the playlist
								if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {						
												
									foreach ($playlistItemsResponse['items'] as $playlistItem) {	
									
										// check if the setting is set, so we don't run unecessary API requests
										if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
									
											// loop over each video to get statistics...
												$video_statistics = $youtube->videos->listVideos('statistics', array(
													'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
												 ));
												
												// build the video stats array
												foreach( $video_statistics as $stat ) {
													// $comment_count = $stat['statistics']['commentCount']; // not used but could be in future releases...
													$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
													$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
													$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
													$view_count = number_format( $stat['statistics']['viewCount'] );
													
													$stat_container = '<span class="yt-plus-stats-container">';
														$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
														$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
													$stat_container .= '</span>';	
												}
											
										} else { // if the setting is disabled
											// set the stat container to none
											$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										}
										
										// grab the privacy settings
										$privacy_settings = $playlistItem['modelData']['status']['privacyStatus'];
										
										// To Do
											// display the icon on the users videos
										if ( $privacy_settings == 'private' ) { // private
											$privacy_setting_icon = '<div class="dashicons dashicons-lock"></div>';
										} else if ( $privacy_settings == 'public' ) { // public
											$privacy_setting_icon = '<div class="dashicons dashicons-admin-site"></div>';
										} else { // unlisted
											$privacy_setting_icon = '<span class="yt-plus-unlisted-icon"><div class="dashicons dashicons-no-alt"></div><div class="dashicons dashicons-admin-site"></div></span>';
										}
										
										// If a description is set for the video
										if($playlistItem['modelData']['snippet']['description']) {	
											// trim the description
											// if there are more than 400 characters
											if(strlen($playlistItem['modelData']['snippet']['description']) > 325) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}	
										
										/** 
										*	Build the users uploads UI
										**/
										$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
										$htmlBody .= '<input type="hidden" class="full_video_description" value="' . $playlistItem['modelData']['snippet']['description'] . '">';
										$htmlBody .= '<input type="hidden" class="video_privacy_status" value="' . $playlistItem['modelData']['status']['privacyStatus'] . '">';
											$htmlBody .= '<input type="hidden" class="video_id" value="' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '">';
												$htmlBody .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';
													$htmlBody .= '<section class="yt-plus-outside-hidden">';
														$htmlBody .= $dialog_drawer;
													$htmlBody .= '<img class="youtube-plus-video-thumbnail" src="' . $playlistItem['modelData']['snippet']['thumbnails']['high']['url'] . '">';
												$htmlBody .= '</a>';
												$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_browse_buttons', $this->yt4wp_get_browse_buttons($screen_base) ) . '</section>';
												$htmlBody .= '</section>' .  $stat_container;
												$htmlBody .= '<h3><span class="youtube-plus-video-title-container">' . $playlistItem['modelData']['snippet']['title']. '</span> </h3>';
												$htmlBody .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
										$htmlBody .= '</li>';
								
									}
								
								} else {
								
									// user hasn't uploaded any videos yet
									// no videos found in "Uploads"
									?><style>#masonry-container{height:auto !important;}</style><?php
									$htmlBody .= '<span class="no_content_found_error" style="line-height:2.8;">You have not uploaded any videos to your account yet. Why not <a href="' . admin_url() . 'admin.php?page=youtube-for-wordpress&tab=youtube_plus_upload" class="button-secondary">upload one</a> now?</span>';
								
								}
								
								// Browse => Watch Later
							  } elseif ( $clicked_button == 'Watch Later' ) {
								// if items are found in the watch later playlist
								if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {
									
									foreach ($playlistItemsResponse['items'] as $playlistItem) {
									
										// check if the setting is set, so we don't run unecessary API requests
										if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
									
											// loop to get video statistics...
												$video_statistics = $youtube->videos->listVideos('statistics', array(
													'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
												 ));
												
												// build the video stats array
												foreach( $video_statistics as $stat ) {
													// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
													$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
													$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
													$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
													$view_count = number_format( $stat['statistics']['viewCount'] );
													
													$stat_container = '<span class="yt-plus-stats-container">';
														$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
														$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
													$stat_container .= '</span>';	
													
												}
											
										} else { // if the setting is disabled
											// set the stat container to none
											$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										}
														
										// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
										if($playlistItem['modelData']['snippet']['description']) {
											// trim the description
											// if there are more than 400 characters
											if(strlen($playlistItem['modelData']['snippet']['description']) > 325) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}	
										
										// grab the privacy settings
										$privacy_setting = $playlistItem['modelData']['status']['privacyStatus'];
										
										// store the thumbnail URL
										if ( isset( $playlistItem['modelData']['snippet']['thumbnails'] ) ) {
											$thumbnail_url = $playlistItem['modelData']['snippet']['thumbnails']['high']['url'];
										}
										// display only public videos in your favorites list
										if ( $playlistItem['modelData']['snippet']['title'] != 'Deleted video' ) {
											
											// Build the Watch Later UI
											$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
												$htmlBody .= '<input type="hidden" class="playlistItem_id" value="' . $playlistItem['id'] . '">';
												$htmlBody .= '<input type="hidden" class="video_id" value="' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '">';
												$htmlBody .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';
													$htmlBody .= '<section class="yt-plus-outside-hidden">' . $dialog_drawer . '<img class="youtube-plus-video-thumbnail" src="' . $thumbnail_url . '">';
												$htmlBody .= '</a>';  
													$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_watch_later_buttons', $this->yt4wp_get_watch_later_buttons( $screen_base ) ) . '</section>';
													$htmlBody .= '</section>';
												$htmlBody .= '<h3>' . $stat_container . '<span class="youtube-plus-video-title-container">' . $playlistItem['modelData']['snippet']['title'] .'</span> </h3>'; 
												$htmlBody .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
											$htmlBody .= '</li>';
											
										}
										
									  }
								} else {
								
									?><style>#masonry-container{height:auto !important;}</style><?php
									$htmlBody .= '<span class="no_content_found_error">You have not added any videos to your "Watch Later" playlist yet.</span>';
									
								}
								
								// Browse => Likes
								// Browse => Favorites
								// Browse => Watch History
							  } else {
								// if the playlist has items
								if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {
							
									foreach ($playlistItemsResponse['items'] as $playlistItem) {
									
										// check if the setting is set, so we don't run unecessary API requests
										if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
									
											// loop to get video statistics...
												$video_statistics = $youtube->videos->listVideos('statistics', array(
													'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
												 ));
												
												// build the video stats array
												foreach( $video_statistics as $stat ) {
													// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
													$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
													$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
													$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
													$view_count = number_format( $stat['statistics']['viewCount'] );
													
													$stat_container = '<span class="yt-plus-stats-container">';
														$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
														$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
													$stat_container .= '</span>';	
												}
											
										} else { // if the setting is disabled
											// set the stat container to none
											$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										}
														
										// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
										if( $playlistItem['modelData']['snippet']['description'] ) {
											// trim the description
											// if there are more than 325 characters
											if( strlen( $playlistItem['modelData']['snippet']['description'] ) > 325 ) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}	
										
										// grab the privacy settings
										$privacy_setting = $playlistItem['modelData']['status']['privacyStatus'];
										
										// store the thumbnail URL
										if ( isset( $playlistItem['modelData']['snippet']['thumbnails'] ) ) {
											$thumbnail_url = $playlistItem['modelData']['snippet']['thumbnails']['high']['url'];
										}
										// display only public videos in your favorites list
										if ( $privacy_setting == 'public' && $playlistItem['modelData']['snippet']['title'] != 'Deleted video' ) {
											
											$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
												$htmlBody .= '<input type="hidden" class="playlistItem_id" value="' . $playlistItem['id'] . '">';
												$htmlBody .= '<input type="hidden" class="video_id" value="' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '">';
												$htmlBody .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';
													$htmlBody .= '<section class="yt-plus-outside-hidden">' . $dialog_drawer . '<img class="youtube-plus-video-thumbnail" src="' . $thumbnail_url . '">';
												$htmlBody .= '</a>';  
													$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_likes_favs_history_buttons', $this->yt4wp_get_likes_favs_history_buttons($screen_base) ) . '</section>';
													$htmlBody .= '</section>';
												$htmlBody .= '<h3>' . $stat_container . '<span class="youtube-plus-video-title-container">' . $playlistItem['modelData']['snippet']['title'] .'</span> </h3>'; 
												$htmlBody .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
											$htmlBody .= '</li>';
											
										}
										
									  }
								} else {
									
									?><style>#masonry-container{height:auto !important;}</style><?php
									if ( $clicked_button == 'Likes' ) {
										$htmlBody .= '<span class="no_content_found_error">You have not liked any videos yet.</span>';
									} else {
										$htmlBody .= '<span class="no_content_found_error">You currently have no ' . $clicked_button . ' videos.</span>';
									}
									
								}
							  }
							  
							  $htmlBody .= '</ul>';
							
							}
								
						  // Catch any errors thrown by the API
						  } catch (Google_ServiceException $e) {
						  
							$htmlBody = '';
							$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
							  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
						  } catch (Google_Exception $e) {
						  
							$htmlBody = '';
							// print a nicer error message for messages we can decipher
							if (strpos($e->getMessage(),'(401) Invalid Credentials') !== false) { 
								$error_message = 'There was an issue with your credentials. Please check your OAUTH2 API Key, OAUTH2 Secret and that you have authorized.';
								 $htmlBody .= sprintf('<p>A client error occurred: %s</p>',
													htmlspecialchars($error_message));
									/* Write the error to our error log */
									$this->writeErrorToErrorLog( $error_message , $e->getMessage() );
						
							} elseif ( strpos($e->getMessage(),'(404) Not Found') !== false && $clicked_button == 'Favorites' ) { // display an error message much like the others to avoid the ugly error message (when no favorites yet exist)
								
								?>
									<style>#masonry-container{height:auto !important;}</style>
								<?php
								 $error_message = '<h3>Your Favorites</h3><ul id="masonry-container" style="position: relative; height: auto;"><span class="no_content_found_error">You have not favorited any videos yet.</span></ul>';
								 $htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
									htmlspecialchars($e->getMessage()),$e->getCode());
								/* Write the error to our error log */
								$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
							} else {
								
								$error_message = $e->getMessage();
								$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
									htmlspecialchars($e->getMessage()),$e->getCode());
								/* Write the error to our error log */
								$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
							}
						   
						  }
						  
						  $_SESSION['token'] = $client->getAccessToken();
						
						} else {
						
						  $htmlBody = '';
						  $state = mt_rand();
						  $client->setState($state);
						  $_SESSION['state'] = $state;
						  $authUrl = $client->createAuthUrl();
						  // this runs only the first time the user ever installs the plugin
							 $htmlBody .= '<div class="error" style="margin-top:5.3em;position:absolute;width:96.5%;">
									<h3>'.__("YouTube Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
									<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
									</div>'; 
									
						}
						?>
							<?php echo $htmlBody; ?>
						<?php
				}
				
			/*
			*  paginate_youtube_subscriptions()
			*  Paginate user subscriptions (uploads, playlists, likes etc.)
			*  returns next/previous batch of subscriptions
			*
			*  Parameters : page_token , screen_base
			*
			*  @since 2.0
			*/			
			function paginate_youtube_subscriptions( $page_token , $screen_base) {
										
					// include the required php files - containers api key
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
						  try {
						   
							$htmlBody = '';
						  
							// Call the channels.list method to retrieve information about the
							// currently authenticated user's channel.
							$subscriptionsResponse = $youtube->subscriptions->listSubscriptions('snippet,id', array(
							  'mine' => 'true',
							  'maxResults' => 50,
							  'order' => 'alphabetical',
							  'pageToken' => $page_token
							));
							
								if ( count( $subscriptionsResponse['items'] ) > 0 ) {		
									
									$htmlBody .= '<h3>Your Subscriptions</h3>';
									
									$htmlBody .= $this->yt4wp_pagination( $subscriptionsResponse , 'browse' );
									// end set up pagination
									
									$htmlBody .= '<ul id="masonry-container">';
											
									// add an empty stat container to unify styles throughout plugin
									$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										
									  foreach ($subscriptionsResponse['items'] as $subscriptionsItem) {	
										// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
										if($subscriptionsItem['modelData']['snippet']['description']) {
											// trim the description
											// if there are more than 400 characters
											if(strlen($subscriptionsItem['modelData']['snippet']['description']) > 325) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($subscriptionsItem['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$subscriptionsItem['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}
										$htmlBody .= sprintf('<li class="youtube-plus-video-single-list-item subscribtions"><div class="dashicons dashicons-dismiss yt-plus-unsubscribe-to-channel"></div><input type="hidden" class="subscription_id" value="%s"><input type="hidden" class="channel_id" value="%s"><a class="view-subscription-videos-btn" href="#" alt="%s" title="%s" target="_blank"><img class="youtube-plus-video-thumbnail subscription" src="%s"></a> %s <h3>%s</h3> %s </li>',
											  $subscriptionsItem['id'], $subscriptionsItem['snippet']['resourceId']['channelId'], $subscriptionsItem['snippet']['resourceId']['channelId'], $subscriptionsItem['modelData']['snippet']['title'], $subscriptionsItem['snippet']['thumbnails']['high']['url'], $stat_container, $subscriptionsItem['modelData']['snippet']['title'], $video_description );
									  }
									  $htmlBody .= '</ul>';
								
								} else {
									$error_message = '<h3>Your Subscriptions</h3><ul id="masonry-container" style="position: relative; height: auto;"><span class="no_content_found_error">You have not subscribed to any channels yet.</span></ul>';
									$htmlBody .= $error_message;  
								}
							
						  } catch (Google_ServiceException $e) {
							
							$htmlBody = '';
							$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
							  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
								$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
						  } catch (Google_Exception $e) {
							
							$htmlBody = '';
							$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
							  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
								$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
						  }
					  $_SESSION['token'] = $client->getAccessToken();
					
					} else {
					
						  $state = mt_rand();
						  $client->setState($state);
						  $_SESSION['state'] = $state;
						  $authUrl = $client->createAuthUrl();
						  $htmlBody = '';
						  
						  // this runs only the first time the user ever installs the plugin
							 echo '<div class="error" style="margin-top:2em;">
										<h3>'.__("YouTube Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
										<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
									</div>'; 
					}
					?>
						<?php echo $htmlBody; ?>
					<?php
				}
			
			/*
			*  changeQueriedPlaylist()
			*  Change the user playlist we are querying
			*  'Uploads' , 'Browse' , 'Search' etc.
			*
			*  Parameters : selected_list , clicked_button , screen_base
			*
			*  @since 2.0
			*/		
			function changeQueriedPlaylist( $selected_list , $clicked_button , $screen_base) {	
					if ( get_option( 'yt4wp_user_refresh_token' ) == '' ) {
						?><style>.yt4wp-error-alert:before { padding:0 !important; line-height: 1 !important; padding-right: 5px !important; }</style><?php
						wp_die( '<span id="response_message" class="yt4wp-error-alert"><strong>Woah Woah Woah...</strong> It looks like you haven\'t authenticated yet. You\'ll first need to authenticate yourself before you can go any further.</span>');
					}
					// include the required php files - containers client_id and client_secret
					@include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						// Call set_include_path() as needed to point to your client library.
						require_once YT4WP_PATH.'inc/Google/Client.php';
						require_once YT4WP_PATH.'inc/Google/Service/YouTube.php';
						/* 
						* Check if session has started 
						* On callback, it tries to restart the session
						* Throwing an error
						*/
						if (session_id() == PHP_SESSION_NONE) {
							session_start();
						}
							
						/*
						 * You can acquire an OAuth 2.0 client ID and client secret from the
						 * Google Developers Console <https://console.developers.google.com/>
						 * For more information about using OAuth 2.0 to access Google APIs, please see:
						 * <https://developers.google.com/youtube/v3/guides/authentication>
						 * Please ensure that you have enabled the YouTube Data API for your project.
						 */
						$ytPlusBase = new YT4WPBase();
						$OAUTH2_CLIENT_ID = $ytPlusBase->optionVal['yt4wp-oauth2-key'];
						$OAUTH2_CLIENT_SECRET = $ytPlusBase->optionVal['yt4wp-oauth2-secret'];
						$client = new Google_Client();
						$client->setClientId($OAUTH2_CLIENT_ID);
						$client->setClientSecret($OAUTH2_CLIENT_SECRET);
						$client->setScopes('https://www.googleapis.com/auth/youtube');
						if ( is_ssl () ) {
							$redirect_prefix = 'https://';
						} else {
							$redirect_prefix = 'http://';
						}
						
						$redirect = filter_var( $redirect_prefix . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?page=youtube-for-wordpress&tab=youtube_plus_browse' , FILTER_SANITIZE_URL );
						$client->setRedirectUri($redirect);
						// Define an object that will be used to make all API requests.
						$youtube = new Google_Service_YouTube($client);
						if ( !isset( $screen_base ) || $screen_base == '' ) {
							$screen_base = get_current_screen();
							if ( is_object( $screen_base ) ) {
								$screen_base = get_current_screen()->base;
							} else {
								$screen_base = $screen_base['base'];
							}
						}
						
						if ($client->isAccessTokenExpired()) {
							// check if the user has ANY data stored in the token session
							// if they do, but the token has expired, we simply call a refresh
							// on the refresh token to retreive new access tokens
							//
							// we do this to avoid re-authorizing every time you use
							// the plugin -- this is all done behind the scenes ;)
							if ( get_option( 'yt4wp_user_refresh_token' ) != '' ) {
							
								$client->refreshToken( get_option( 'yt4wp_user_refresh_token' ) );
								$_SESSION['token'] = $client->getAccessToken();
								
							} else {
							
								 $htmlBody = '';
								// If the user hasn't authorized the app, initiate the OAuth flow
								  $state = mt_rand();
								  $client->setState($state);
								  $_SESSION['state'] = $state;
									$authUrl = $client->createAuthUrl();
									$htmlBody .= '<div class="error" style="margin-top:2em;">
									<h3>'.__("YouTube Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
									<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
									</div>'; 
						
							}
						
						}
							
						  try {
							  
							// Call the channels.list method to retrieve information about the
							// currently authenticated user's channel.
							$channelsResponse = $youtube->channels->listChannels('contentDetails', array(
							  'mine' => 'true'
							));
							$htmlBody = '';
							// set up the dialog drawer
							$dialog_drawer = '<section class="dialog_message_drawer"></section>';
							  
							// we can use the channelsResponse to get the playlist ID's for --
								/*
									- likes
									- favorites
									- uploads
									- watch history
									- watch later
									- google plus user id (not a playlist id, just useable to link to google+)
								*/
							foreach ($channelsResponse['items'] as $playlist) {
							
							  // Extract the unique playlist ID that identifies the list of videos
							  // uploaded to the channel, and then call the playlistItems.list method
							  // to retrieve that list.
							  $uploadsListId = $playlist['contentDetails']['relatedPlaylists']['uploads'];
							  $likesListId = $playlist['contentDetails']['relatedPlaylists']['likes'];
							  $watchHistoryListId = $playlist['contentDetails']['relatedPlaylists']['watchHistory'];
							  $watchLaterListId = $playlist['contentDetails']['relatedPlaylists']['watchLater'];
							  $favoritesListId = $playlist['contentDetails']['relatedPlaylists']['favorites'];
								
								if ( $selected_list == '' ) { // default query on page load
									 $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('status,snippet', array(
										'playlistId' =>  $uploadsListId,
										// maybe set up pagination here, for users
										// who have more than 50 vids
										'maxResults' => 50
									 ));
								} else if ( $selected_list == 'playlists' ) { // query a specific playlist
									$playlistsResponse = $youtube->playlists->listPlaylists('status,snippet,contentDetails', array(
										'mine' =>  'true',
										// maybe set up pagination here, for users
										// who have more than 50 vids
										'maxResults' => 50
									 ));
								} else { // all other queries
									$playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('status,snippet', array(
										'playlistId' =>  $selected_list,
										// maybe set up pagination here, for users
										// who have more than 50 vids
										'maxResults' => 50
									 ));
								}
							if ( isset( $playlistItemsResponse ) ) {
								if ( count ( $playlistItemsResponse['items'] ) > 0 ) {
									$channel_name = $playlistItemsResponse['items'][0]['modelData']['snippet']['channelTitle'];
								} else {
									$channel_name = '';
								}
							}
							
							
							// set up the pagination based on the cliced button
							// should move to it's own function to display on each page - To Do
							if ( $clicked_button == 'Playlists' ) {
								// set up pagination
								// if items > 0
								if ( count ( $playlistsResponse['items'] ) > 0 ) {
								
									if ( isset( $playlistsResponse['nextPageToken'] ) ) {
										// if ( $playlistsResponse['modelData']['pageInfo']['resultsPerPage'] == count($playlistsResponse['items'] ) ) {
											$nextPageToken = $playlistsResponse['nextPageToken'];
										// }	
									}
									
									if ( isset( $playlistsResponse['prevPageToken'] ) ) {
										$previousPageToken = $playlistsResponse['prevPageToken'];
									}
									// pagination button definitions
									if ( isset ( $previousPageToken ) ) {
										$previous_pagination = '<a href="#" class="pagination_page_browse button-secondary" onclick="return false;" alt="'.$previousPageToken.'" alt="Previous Page" title="Previous"><div class="dashicons dashicons-arrow-left-alt2" style="line-height:1"></div></a>';
									} else {
										$previous_pagination = '<a href="#" class="pagination_page_browse_disabled button-secondary" onclick="return false;" alt="" alt="Previous Page" title="Previous"><div class="dashicons dashicons-arrow-left-alt2" style="line-height:1"></div></a>';
									}
									if ( isset( $nextPageToken ) ) {
										$next_pagination =  '<a href="#" class="pagination_page_browse button-secondary" onclick="return false;" alt="'.$nextPageToken.'" alt="Next Page" title="Next"><div class="dashicons dashicons-arrow-right-alt2" style="line-height:1.3"></div></a>';
									} else {
										$next_pagination =  '<a href="#" class="pagination_page_browse_disabled button-secondary" onclick="return false;" alt="" alt="Next Page" title="Next"><div class="dashicons dashicons-arrow-right-alt2" style="line-height:1.3"></div></a>';
									}
								
								} else {
									$previous_pagination = '';
									$next_pagination = '';
								}
								// end set up pagination
							} else {
								
								if ( count ( $playlistItemsResponse['items'] ) > 0 ) {
							
									// set up pagination
										if ( isset( $playlistItemsResponse['nextPageToken'] ) ) {
											// if ( $playlistItemsResponse['modelData']['pageInfo']['resultsPerPage'] == count($playlistItemsResponse['items'] ) ) {
												$nextPageToken = $playlistItemsResponse['nextPageToken'];
											// }	
										}
										
										if ( isset( $playlistItemsResponse['prevPageToken'] ) ) {
											$previousPageToken = $playlistItemsResponse['prevPageToken'];
										}
										// pagination button definitions
										if ( isset ( $previousPageToken ) ) {
											$previous_pagination = '<a href="#" class="pagination_page_browse button-secondary" onclick="return false;" alt="'.$previousPageToken.'" alt="Previous Page" title="Previous"><div class="dashicons dashicons-arrow-left-alt2" style="line-height:1"></div></a>';
										} else {
											$previous_pagination = '<a href="#" class="pagination_page_browse_disabled button-secondary" onclick="return false;" alt="" alt="Previous Page" title="Previous"><div class="dashicons dashicons-arrow-left-alt2" style="line-height:1"></div></a>';
										}
										if ( isset( $nextPageToken ) ) {
											$next_pagination =  '<a href="#" class="pagination_page_browse button-secondary" onclick="return false;" alt="'.$nextPageToken.'" alt="Next Page" title="Next"><div class="dashicons dashicons-arrow-right-alt2" style="line-height:1.3"></div></a>';
										} else {
											$next_pagination =  '<a href="#" class="pagination_page_browse_disabled button-secondary" onclick="return false;" alt="" alt="Next Page" title="Next"><div class="dashicons dashicons-arrow-right-alt2" style="line-height:1.3"></div></a>';
										}
									
								} else {
									$previous_pagination = '';
									$next_pagination = '';
								}
								// end set up pagination
							}
							
							  // $htmlBody .= "<h3>Videos in list $uploadsListId</h3><ul>";
							  // delete buttons, ekep htmlBody h3 decleration
							  if ( $clicked_button == 'Uploads' || $clicked_button == '' || !isset($clicked_button) ) {
								// if items are found in the playlist			
								if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {		
									$video_count = count($playlistItemsResponse['items']);
									$search_my_videos_button = '<form id="search_my_playlists_form"><input type="search" id="yt-plus-upload-search" class="module-search" placeholder="Search Your Uploads" autocomplete="off"><label for="yt-plus-upload-search">Search</label></form>';
									$htmlBody .= "<h3>Channel : $channel_name</h3><h4 class='yt4wp-video-count'>$video_count Videos</h4>" . $search_my_videos_button . $previous_pagination . $next_pagination . "<ul id='masonry-container'>";
								} else {
									$htmlBody .= "<h3>Channel : $channel_name</h3><h4 class='yt4wp-video-count'>0 Videos Found</h4><ul id='masonry-container'>";
								}
							  } else {
							  
								$htmlBody .= "<h3>Your $clicked_button</h3>" . $previous_pagination . $next_pagination . "<ul id='masonry-container'>";
							  }
							  
							  
							  // Browse => Playlists
							  if ( $clicked_button == 'Playlists' ) {
								
								// add empty stat container to unify styles throughout the plugin
								if ( count( $playlistsResponse['items'] ) > 0 ) {
									 foreach ($playlistsResponse['items'] as $playlist) {
										// Grab Video Count
										$number_of_videos = $playlist['contentDetails']['itemCount'];
										
										// add a video count to our stat container for playlists
										$stat_container = '<span class="yt-plus-stats-container number_of_videos_in_playlist">'.$number_of_videos.' videos</span>';
										
											// build our playlist UI
											$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
												$htmlBody .= '<input type="hidden" class="playlist_id" value="' . $playlist['id'] . '">';
													$htmlBody .= '<a class="youtube-plus-view-playlist" href="#" onclick="return false;">';
														$htmlBody .= '<section class="yt-plus-outside-hidden">';
														$htmlBody .= '<img class="youtube-plus-video-thumbnail" src="' .  $playlist['modelData']['snippet']['thumbnails']['high']['url'] . '">';
													$htmlBody .= '</a>';
													$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_playlist_buttons', $this->yt4wp_get_playlist_buttons($screen_base) ) . '</section>';
														$htmlBody .= '</section>' . $stat_container;
													$htmlBody .= '<h3><span class="playlist_title">' . $playlist['modelData']['snippet']['title'] . '</span></h3>';
													$htmlBody .= '<p class="youtube-plus-video-description">' . $playlist['modelData']['snippet']['description'] . '</p>'; 
											$htmlBody .= '</li>';
									  
									  }
								 } else {
									?>
									<style>#masonry-container{height:auto !important;}</style>
									<?php
									$htmlBody .= '<span class="no_content_found_error">You have not created any playlists yet. Why not <a href="#" style="margin-top:-.5em;" disabled="disabled" title="Create New Playlist (add on not installed)" class="button-secondary yt-plus-create-new-playlist">Create One</a> now?</span>';
								 }
								 
								 // Browse => Uploads
							  } else if ( $clicked_button == 'Uploads' || $clicked_button == '' ) {
											
								// the first item in this list is the privacy status of the video
								// we need to display the lock icon etc for private vs non-private videos
								
								// if items are found in the playlist
								if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {						
												
									foreach ($playlistItemsResponse['items'] as $playlistItem) {	
										
										// check if the setting is set, so we don't run unecessary API requests
										if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
									
											// loop over each video to get statistics...
												$video_statistics = $youtube->videos->listVideos('statistics', array(
													'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
												 ));
												
												// build the video stats array
												foreach( $video_statistics as $stat ) {
													// $comment_count = $stat['statistics']['commentCount']; // not used but could be in future releases...
													$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
													$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
													$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
													$view_count = number_format( $stat['statistics']['viewCount'] );
													
													$stat_container = '<span class="yt-plus-stats-container">';
														$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
														$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
													$stat_container .= '</span>';	
												}
											
										} else { // if the setting is disabled
											// set the stat container to none
											$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										}
										
										// grab the privacy settings
										$privacy_settings = $playlistItem['modelData']['status']['privacyStatus'];
										
										// To Do
											// display the icon on the users videos
										if ( $privacy_settings == 'private' ) { // private
											$privacy_setting_icon = '<div class="dashicons dashicons-lock"></div>';
										} else if ( $privacy_settings == 'public' ) { // public
											$privacy_setting_icon = '<div class="dashicons dashicons-admin-site"></div>';
										} else { // unlisted
											$privacy_setting_icon = '<span class="yt-plus-unlisted-icon"><div class="dashicons dashicons-no-alt"></div><div class="dashicons dashicons-admin-site"></div></span>';
										}
										
										// If a description is set for the video
										if($playlistItem['modelData']['snippet']['description']) {	
											// trim the description
											// if there are more than 400 characters
											if(strlen($playlistItem['modelData']['snippet']['description']) > 325) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}	
										
										/** 
										*	Build the users uploads UI
										**/
										$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
										$htmlBody .= '<input type="hidden" class="full_video_description" value="' . $playlistItem['modelData']['snippet']['description'] . '">';
										$htmlBody .= '<input type="hidden" class="video_privacy_status" value="' . $playlistItem['modelData']['status']['privacyStatus'] . '">';
											$htmlBody .= '<input type="hidden" class="video_id" value="' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '">';
												$htmlBody .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';
													$htmlBody .= '<section class="yt-plus-outside-hidden">';
														$htmlBody .= $dialog_drawer;
													$htmlBody .= '<img class="youtube-plus-video-thumbnail" src="' . $playlistItem['modelData']['snippet']['thumbnails']['high']['url'] . '">';
												$htmlBody .= '</a>';
												$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_browse_buttons', $this->yt4wp_get_browse_buttons( $screen_base ) ) . '</section>';
												$htmlBody .= '</section>' .  $stat_container;
												$htmlBody .= '<h3><span class="youtube-plus-video-title-container">' . $playlistItem['modelData']['snippet']['title']. '</span> </h3>';
												$htmlBody .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
										$htmlBody .= '</li>';
								
									}
								
								} else {
								
									// user hasn't uploaded any videos yet
									// no videos found in "Uploads"
									?><style>#masonry-container{height:auto !important;}</style><?php
									if ( isset( $screen_base ) ) { // we are on the main admin page, not edit.php or post-new.php
										$htmlBody .= '<span class="no_content_found_error" style="line-height:2.8;">You have not added uploaded any videos to your account yet. Why not <a href="' . admin_url() . '/admin.php?page=youtube-for-wordpress&tab=youtube_plus_upload" class="button-secondary">upload one</a> now?</span>';
									} else { // this is edit.php or post-new.php
										$htmlBody .= '<span class="no_content_found_error" style="line-height:2.8;">You have not added uploaded any videos to your account yet. Why not <a href="#" class="button-secondary yt4wp-no-uploaded-content" onclick="return false;">upload one</a> now?</span>';
									}
								}
								
								// Browse => Watch Later
							  } elseif ( $clicked_button == 'Watch Later' ) {
							
								// if items are found in the playlist
								if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {
												
									foreach ($playlistItemsResponse['items'] as $playlistItem) {
									
										// check if the setting is set, so we don't run unecessary API requests
										if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
									
											// loop to get video statistics...
												$video_statistics = $youtube->videos->listVideos('statistics', array(
													'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
												 ));
												
												// build the video stats array
												foreach( $video_statistics as $stat ) {
													// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
													$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
													$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
													$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
													$view_count = number_format( $stat['statistics']['viewCount'] );
													
													$stat_container = '<span class="yt-plus-stats-container">';
														$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
														$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
													$stat_container .= '</span>';	
												}
											
										} else { // if the setting is disabled
											// set the stat container to none
											$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										}
														
										// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
										if($playlistItem['modelData']['snippet']['description']) {
											// trim the description
											// if there are more than 400 characters
											if(strlen($playlistItem['modelData']['snippet']['description']) > 325) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}	
										
										// grab the privacy settings
										$privacy_setting = $playlistItem['modelData']['status']['privacyStatus'];
										
										// store the thumbnail URL
										if ( isset( $playlistItem['modelData']['snippet']['thumbnails'] ) ) {
											$thumbnail_url = $playlistItem['modelData']['snippet']['thumbnails']['high']['url'];
										}
										// display only public videos in your favorites list
										if ( $privacy_setting != 'private' && $playlistItem['modelData']['snippet']['title'] != 'Deleted video' ) {
											
											// Build the Watch Later UI
											$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
												$htmlBody .= '<input type="hidden" class="playlistItem_id" value="' . $playlistItem['id'] . '">';
												$htmlBody .= '<input type="hidden" class="video_id" value="' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '">';
												$htmlBody .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';
													$htmlBody .= '<section class="yt-plus-outside-hidden">' . $dialog_drawer . '<img class="youtube-plus-video-thumbnail" src="' . $thumbnail_url . '">';
												$htmlBody .= '</a>';  
													$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_watch_later_buttons', $this->yt4wp_get_watch_later_buttons($screen_base) ) . '</section>';
													$htmlBody .= '</section>';
												$htmlBody .= '<h3>' . $stat_container . '<span class="youtube-plus-video-title-container">' . $playlistItem['modelData']['snippet']['title'] .'</span> </h3>'; 
												$htmlBody .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
											$htmlBody .= '</li>';
											
										}
										
									  }
								} else {
								
									?><style>#masonry-container{height:auto !important;}</style><?php
									$htmlBody .= '<span class="no_content_found_error">You have not added any videos to your "Watch Later" playlist yet.</span>';
									
								}
								
								// Browse => Likes
								// Browse => Favorites
								// Browse => Watch History
							  } else {
								// if the playlist has items
								if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {
							
									foreach ($playlistItemsResponse['items'] as $playlistItem) {
									
										// check if the setting is set, so we don't run unecessary API requests
										if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
									
											// loop to get video statistics...
												$video_statistics = $youtube->videos->listVideos('statistics', array(
													'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
												 ));
												
												// build the video stats array
												foreach( $video_statistics as $stat ) {
													// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
													$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
													$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
													$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
													$view_count = number_format( $stat['statistics']['viewCount'] );
													
													$stat_container = '<span class="yt-plus-stats-container">';
														$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
														$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
													$stat_container .= '</span>';	
												}
											
										} else { // if the setting is disabled
											// set the stat container to none
											$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										}
														
										// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
										if( $playlistItem['modelData']['snippet']['description'] ) {
											// trim the description
											// if there are more than 325 characters
											if( strlen( $playlistItem['modelData']['snippet']['description'] ) > 325 ) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}	
										
										// grab the privacy settings
										$privacy_setting = $playlistItem['modelData']['status']['privacyStatus'];
										
										// store the thumbnail URL
										if ( isset( $playlistItem['modelData']['snippet']['thumbnails'] ) ) {
											$thumbnail_url = $playlistItem['modelData']['snippet']['thumbnails']['high']['url'];
										}
										// display only public videos in your favorites list
										if ( $privacy_setting == 'public' && $playlistItem['modelData']['snippet']['title'] != 'Deleted video' ) {
											
											$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
												$htmlBody .= '<input type="hidden" class="playlistItem_id" value="' . $playlistItem['id'] . '">';
												$htmlBody .= '<input type="hidden" class="video_id" value="' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '">';
												$htmlBody .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';
													$htmlBody .= '<section class="yt-plus-outside-hidden">' . $dialog_drawer . '<img class="youtube-plus-video-thumbnail" src="' . $thumbnail_url . '">';
												$htmlBody .= '</a>';  
													$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_likes_favs_history_buttons', $this->yt4wp_get_likes_favs_history_buttons($screen_base) ) . '</section>';
													$htmlBody .= '</section>';
												$htmlBody .= '<h3>' . $stat_container . '<span class="youtube-plus-video-title-container">' . $playlistItem['modelData']['snippet']['title'] .'</span> </h3>'; 
												$htmlBody .= '<span class="youtube-plus-video-description-container">' . $video_description . '</span>';
											$htmlBody .= '</li>';
											
										}
										
									  }
								} else {
												
									?><style>#masonry-container{height:auto !important;}</style><?php
									if ( $clicked_button == 'Likes' ) {
										$htmlBody .= '<span class="no_content_found_error">You have not liked any videos yet.</span>';
									} else {
										$htmlBody .= '<span class="no_content_found_error">You currently have no ' . $clicked_button . ' videos.</span>';
									}
									
								}
							  }
							  
							  $htmlBody .= '</ul>';
							
							}
							?>
							<!-- end logout and revoke script -->
							<div id="profile_sub_navigation">
									<ul>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Uploads' || $clicked_button == '' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $uploadsListId; ?>">Uploads</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Playlists' ? 'sub-nav-button-active' : ''; ?>" alt="playlists">Playlists</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Likes' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $likesListId; ?>">Likes</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Favorites' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $favoritesListId; ?>">Favorites</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Watch History' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $watchHistoryListId; ?>">Watch History</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Watch Later' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $watchLaterListId; ?>">Watch Later</a></li>
										<input type="hidden" id="watch_later_list_id" value="<?php echo $this->getUserWatchLaterListId(); ?>">
									</ul>
									<?php if ( $clicked_button == 'Playlists' ) { ?>	
									<ul id="create_new_playlist_ul">
										<li><a href="#" disabled="disabled" title="Create New Playlist (add on not installed)" class="button-secondary yt-plus-create-new-playlist"><div class="dashicons dashicons-plus" style="line-height:1.5"></div>Create New Playlist</a></li>
									</ul>
									<?php } else if ( $clicked_button == 'Watch Later' ) { 
										// set up the hide variable if no videos are returned
										if ( $playlistItemsResponse && count($playlistItemsResponse['items']) > 0 ) {
												$hide_if_no_videos = '';
											} else {
												$hide_if_no_videos = 'style="display:none;"';
											}
									?>
									<ul id="clear_watch_later_playlist">	
										<li><a href="#" title="Empty Watch Later Playlist" class="button-secondary yt-plus-clear-entire-playlist" <?php echo $hide_if_no_videos; ?>><div class="dashicons dashicons-no-alt" style="line-height:1.3;"></div>Clear Playlist</a></li>
									</ul>
									<?php } ?>
								</div>
							<?php
						
					  // Catch any errors thrown by the API
					  } catch (Google_ServiceException $e) {
						
						$htmlBody = '';
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()));
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
					  } catch (Google_Exception $e) {
						
						$htmlBody = '';
						// print a nicer error message for messages we can decipher
						if (strpos($e->getMessage(),'(401) Invalid Credentials') !== false) {
						
							$error_message = 'There was an issue with your credentials. Please check your OAUTH2 API Key, OAUTH2 Secret and that you have authorized.';
							 $htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered a serious error : %s. Please refresh the page and try again. <p>&nbsp;</p><p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team.</p></p></span>',
								htmlspecialchars($error_message));
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $error_message , $e->getMessage() );
									
						} elseif ( strpos($e->getMessage(),'(404) Not Found') !== false && $clicked_button == 'Favorites' ) { // display an error message much like the others to avoid the ugly error message (when no favorites yet exist)
							?>
							
							<style>#masonry-container{height:auto !important;}</style>
							<div id="profile_sub_navigation">
									<ul>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Uploads' || $clicked_button == '' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $uploadsListId; ?>">Uploads</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Playlists' ? 'sub-nav-button-active' : ''; ?>" alt="playlists">Playlists</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Likes' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $likesListId; ?>">Likes</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Favorites' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $favoritesListId; ?>">Favorites</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Watch History' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $watchHistoryListId; ?>">Watch History</a></li>
										<li><a href="#" class="button-secondary sub-nav-button <?php echo $clicked_button == 'Watch Later' ? 'sub-nav-button-active' : ''; ?>" alt="<?php echo $watchLaterListId; ?>">Watch Later</a></li>
										<input type="hidden" id="watch_later_list_id" value="<?php $this->getUserWatchLaterListId(); ?>">
									</ul>
									<?php if ( $clicked_button == 'Playlists' ) { ?>	
									<ul id="create_new_playlist_ul">	
											<li><a href="#" disabled="disabled" title="Create New Playlist (add on not installed)" class="button-secondary yt-plus-create-new-playlist"><div class="dashicons dashicons-plus" style="line-height:1.5"></div>Create New Playlist</a></li>
									</ul>
									<?php } else if ( $clicked_button == 'Watch Later' ) { ?>
									<ul id="clear_watch_later_playlist">	
											<li><a href="#" title="Empty Watch Later Playlist" class="button-secondary yt-plus-clear-entire-playlist"><div class="dashicons dashicons-no-alt" style="line-height:1.3;"></div>Clear Playlist</a></li>
									</ul>
									<?php } ?>
								</div>
							<?php
							 $error_message = '<h3>Your Favorites</h3><ul id="masonry-container" style="position: relative; height: auto;"><span class="no_content_found_error">You have not favorited any videos yet.</span></ul>';
							 $htmlBody .= $error_message;
								htmlspecialchars($error_message);
								htmlspecialchars($error_message);
						
						} else {
							
							$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><strong>Oh No!</strong> We have encountered an error : %s. If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</span>',
								  htmlspecialchars($e->getMessage()),$e->getCode());	
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
						}
					   
					  }
					  
					  $_SESSION['token'] = $client->getAccessToken();
					  
					} else {
					  
					  $htmlBody = '';
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 // this runs only the first time the user ever installs the plugin
						 $htmlBody .= '<div class="error" style="margin-top:8.3em;position:absolute;width:96.5%;">
								<h3>'.__("YouTube Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
								<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
							</div>'; 
					}
					?>
						<?php echo $htmlBody; ?>
					<?php
				}
			/*
			*  getUsersPlaylistItems()
			*  Get the users selected playlist items
			*  Browse > Playlists > Click a playlist
			*
			*  Parameters : playlist_id , playlist_title , screen_base , current_tab
			*
			*  @since 2.0
			*/		
			function getUsersPlaylistItems( $playlist_id , $playlist_title , $screen_base , $current_tab ) {		
				
						// include the required php files - containers client_id and client_secret
						include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
						
						// delete
						// set up the Insert button depending on what page we are on
						if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
							$insert_button = '<a class="button-secondary insert_video_button"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
						} else {
							$insert_button = '';
						}
						
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
						try {
								
								// Call the channels.list method to retrieve information about the
								// currently authenticated user's channel.
								$playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('status,snippet', array(
										'playlistId' =>  $playlist_id,
										// maybe set up pagination here, for users
										// who have more than 50 vids
										'maxResults' => 50
									 ));
								$htmlBody = '';
								
								// set up the dialog drawer
								$dialog_drawer = '<section class="dialog_message_drawer"></section>';
								
								// replace the 'inser' and 'view playlist' text
								// that gets passed into the title
								$htmlBody .= "<h3>".stripslashes( trim( str_replace( 'view playlist' , '' , str_replace( 'insert' , '' , $playlist_title ) ) ) )." Playlist</h3><ul id='masonry-container'>";
								
								if ( $playlistItemsResponse['items'] ) {
									// create variable for original position
									$i =0;
									
									// do not display private/deleted items in the playlist
									foreach ($playlistItemsResponse['items'] as $playlistItem) {
										// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
										
										// check if the setting is set, so we don't run unecessary API requests
										if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
									
											// loop to get video statistics...
												$video_statistics = $youtube->videos->listVideos('statistics', array(
													'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
												 ));
												
												// print_r($video_statistics);
												// build the video stats array
												foreach( $video_statistics as $stat ) {
													// $comment_count = $stat['statistics']['commentCount']; // not used but could be in future releases...
													$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
													$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
													$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
													$view_count = number_format( $stat['statistics']['viewCount'] );
													
													$stat_container = '<span class="yt-plus-stats-container">';
														$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
														$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
													$stat_container .= '</span>';	
												}
											
										} else { // if the setting is disabled
											// set the stat container to none
											$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										}
										
										// grab the privacy settings
										$privacy_setting = $playlistItem['modelData']['status']['privacyStatus'];
										
										if($playlistItem['modelData']['snippet']['description']) {
											// trim the description
											// if there are more than 400 characters
											if( strlen( $playlistItem['modelData']['snippet']['description'] ) > 325 ) {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
											} else {
												$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
											}
										} else {
											$video_description = ''; 
										}
										if ( $privacy_setting == 'public' && $playlistItem['modelData']['snippet']['title'] != 'Deleted video' ) {
											
											$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
												$htmlBody .= '<input type="hidden" class="original_item_position" value="' . $i . '">';
												$htmlBody .= '<input type="hidden" class="item_id" value="' . $playlistItem['id'] . '">';
												$htmlBody .= '<input type="hidden" class="video_privacy_status" value="' . $privacy_setting . '">';
												$htmlBody .= '<input type="hidden" class="video_id" value="' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '">';
												$htmlBody .= '<a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/' . $playlistItem['modelData']['snippet']['resourceId']['videoId'] . '?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank">';
													$htmlBody .= '<section class="yt-plus-outside-hidden">';
													$htmlBody .= $dialog_drawer;
													$htmlBody .= '<img class="youtube-plus-video-thumbnail" src="' . $playlistItem['modelData']['snippet']['thumbnails']['high']['url'] . '">';
													$htmlBody .= '</a><section class="drawer">' . apply_filters( 'yt4wp_user_playlist_items_buttons', $this->yt4wp_get_users_playlist_item_buttons($screen_base,$current_tab) ) . '</section>';
													$htmlBody .= '</section>' . $stat_container . '<h3>' . $playlistItem['modelData']['snippet']['title'] . '</h3>' . $video_description;
											$htmlBody .= '</li>';
																
											$i++;
										}
									}
								} else {
									$htmlBody .= '<h4 class="youtube-plus-no-data-found">Their are currently no videos in this playlist.</h4>';
								}
							  $htmlBody .= '</ul>';
						
						} catch (Google_ServiceException $e) {
							
							$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
							  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
						} catch (Google_Exception $e) {
							
							$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
							  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
						}
					  $_SESSION['token'] = $client->getAccessToken();
					  
					} else {
					  
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 
					}
					
						if ( $current_tab == 'Browse' ) { ?>
							<!-- navigation items to change the playlist were pulling from -->
							<div id="profile_sub_navigation">
								<ul>
									<li><a href="#" class="button-secondary youtube-plus-back-to-playlists" title="Back to Playlists">Back to My Playlists</a></li>
									<li><a href="#" class="button-secondary youtube-plus-edit-playlist-button" title="Re-arrange Playlist (addon not installed)" disabled="disabled"><div class="dashicons dashicons-randomize"  style="line-height:1.3"></div></a></li>
								</ul>
								<input type="hidden" id="watch_later_list_id" class="sub-nav-button" alt="<?php echo $this->getUserWatchLaterListId(); ?>">
							</div>
						<?php } ?>
						
						<?php echo $htmlBody; ?>

					<?php
				}
			/*
			*  getSubscriptionPlaylistItems()
			*  Get a subscriptions playlists
			*  Subscriptions > Browse
			*
			*  Parameters : playlist_id , playlist_title , screen_base
			*
			*  @since 2.0
			*/		
			function getSubscriptionPlaylistItems($playlist_id,$playlist_title,$screen_base) {		
				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					
					// set up the Insert button depending on what page we are on
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$insert_button = '<a class="button-secondary insert_video_button"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
					} else {
						$insert_button = '';
					}
					
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
						
							// Call the channels.list method to retrieve information about the
							// currently authenticated user's channel.
							$playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('status,snippet', array(
									'playlistId' =>  $playlist_id,
									// maybe set up pagination here, for users
									// who have more than 50 vids
									'maxResults' => 50
								 ));
							$htmlBody = '';
							
							$create_post_button = '<a class="button-secondary create_video_post_button" disabled="disabled" title="Create Post From This Video (add on not installed)"><div class="dashicons dashicons-media-text" style="line-height:1.3"></div></a>';
							
							$add_to_watch_later = '<a class="button-secondary add_to_watch_later" title="Add to Watch Later"><div class="dashicons dashicons-clock" style="line-height:1.3"></div></a>';		
							
							// replace the 'inser' and 'view playlist' text
							// that gets passed into the title
							$htmlBody .= "<h3>".stripslashes( trim( str_replace( 'view playlist' , '' , str_replace( 'insert' , '' , $playlist_title ) ) ) )." Playlist</h3><ul id='masonry-container'>";
							
							if ( $playlistItemsResponse['items'] ) {
								// create variable for original position
								$i =0;
								// do not display private/deleted items in the playlist
								foreach ($playlistItemsResponse['items'] as $playlistItem) {
									// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
									
									// check if the setting is set, so we don't run unecessary API requests
									if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
								
										// loop to get video statistics...
											$video_statistics = $youtube->videos->listVideos('statistics', array(
												'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
											 ));
											
											// print_r($video_statistics);
											// build the video stats array
											foreach( $video_statistics as $stat ) {
												// $comment_count = $stat['statistics']['commentCount']; // not used but could be in future releases...
												$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
												$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
												$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
												$view_count = number_format( $stat['statistics']['viewCount'] );
																			
												$stat_container = '<span class="yt-plus-stats-container">';
													$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
													$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
												$stat_container .= '</span>';	
											}
										
									} else { // if the setting is disabled
										// set the stat container to none
										$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
									}
									
									// grab the privacy settings
									$privacy_setting = $playlistItem['modelData']['status']['privacyStatus'];
									// add our dialog drawr for error message responses
									$dialog_drawer = '<section class="dialog_message_drawer"></section>';
									// set up send to playlist button
									$send_to_playlist_button = '<a class="button-secondary send_to_playlist_button" disabled="disabled" title="Add Video To Playlist (add on not installed)"><div class="dashicons dashicons-editor-ol" style="line-height:1.3"></div></a>';
							 
									if($playlistItem['modelData']['snippet']['description']) {
										// trim the description
										// if there are more than 400 characters
										if(strlen($playlistItem['modelData']['snippet']['description']) > 325) {
											$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
										} else {
											$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
										}
									} else {
										$video_description = ''; 
									}
									if ( $privacy_setting == 'public' && $playlistItem['modelData']['snippet']['title'] != 'Deleted video' ) {
										$htmlBody .= sprintf('<li class="youtube-plus-video-single-list-item"><input type="hidden" class="original_item_position" value="%s"><input type="hidden" class="item_id" value="%s"><input type="hidden" class="video_id" value="%s"><a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/%s?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank"><section class="yt-plus-outside-hidden">%s<img class="youtube-plus-video-thumbnail" src="%s"></a><section class="drawer">%s %s %s %s</section></section> %s <h3>%s</h3>  %s </li>',
											  $i, $playlistItem['id'], $playlistItem['modelData']['snippet']['resourceId']['videoId'], $playlistItem['modelData']['snippet']['resourceId']['videoId'], $dialog_drawer, $playlistItem['modelData']['snippet']['thumbnails']['high']['url'], $insert_button, $add_to_watch_later, $create_post_button, $send_to_playlist_button, $stat_container, $playlistItem['modelData']['snippet']['title'], $video_description );
									  $i++;
									}
								}
							} else {
								$htmlBody .= '<h4 class="youtube-plus-no-data-found">Their are currently no videos in this playlist.</h4>';
							}
						  $htmlBody .= '</ul>';
						
					  } catch (Google_ServiceException $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
								
					  } catch (Google_Exception $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					} else {
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 // this runs only the first time the user ever installs the plugin
					}
					?>
						<?php echo $htmlBody; ?>
					<?php
				}
		
			/*
			*  getUserWatchLaterListId()
			*  Return the users watch later list ID
			*  used to add a video to the watch later play list
			*
			*  @since 2.0
			*/				
			public function getUserWatchLaterListId() {
					// include the required php files - containers client_id and client_secret
					// Call set_include_path() as needed to point to your client library.
					require_once YT4WP_PATH.'inc/Google/Client.php';
					require_once YT4WP_PATH.'inc/Google/Service/YouTube.php';
					/* 
					* Check if session has started 
					* On callback, it tries to restart the session
					* Throwing an error
					*/
					if (session_id() == PHP_SESSION_NONE) {
						session_start();
					}
					// check if yt plus session is started
					
					/*
					 * You can acquire an OAuth 2.0 client ID and client secret from the
					 * Google Developers Console <https://console.developers.google.com/>
					 * For more information about using OAuth 2.0 to access Google APIs, please see:
					 * <https://developers.google.com/youtube/v3/guides/authentication>
					 * Please ensure that you have enabled the YouTube Data API for your project.
					 */
					$ytPlusBase = new YT4WPBase();
					$OAUTH2_CLIENT_ID = $ytPlusBase->optionVal['yt4wp-oauth2-key'];
					$OAUTH2_CLIENT_SECRET = $ytPlusBase->optionVal['yt4wp-oauth2-secret'];
					$client = new Google_Client();
					$client->setClientId($OAUTH2_CLIENT_ID);
					$client->setClientSecret($OAUTH2_CLIENT_SECRET);
					$client->setScopes('https://www.googleapis.com/auth/youtube');
					$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?page=youtube-for-wordpress', FILTER_SANITIZE_URL);
					$client->setRedirectUri($redirect);
					// Define an object that will be used to make all API requests.
					$youtube = new Google_Service_YouTube($client);
					if ( isset( $screen_base ) && $screen_base == '' ) {
						$screen_base = get_current_screen()->base;
					}
					
					if ($client->isAccessTokenExpired()) {
						// check if the user has ANY data stored in the token session
						// if they do, but the token has expired, we simply call a refresh
						// on the refresh token to retreive new access tokens
						//
						// we do this to avoid re-authorizing every time you use
						// the plugin -- this is all done behind the scenes ;)
						if ( get_option( 'yt4wp_user_refresh_token' ) != '' ) {
						
							$client->refreshToken( get_option( 'yt4wp_user_refresh_token' ) );
							$_SESSION['token'] = $client->getAccessToken();
							$client->setAccessToken($client->getAccessToken());

						} else {
						
							 $htmlBody = '';
							// If the user hasn't authorized the app, initiate the OAuth flow
							  $state = mt_rand();
							  $client->setState($state);
							  $_SESSION['state'] = $state;
								$authUrl = $client->createAuthUrl();
								$htmlBody .= '<div class="error" style="margin-top:2em;">
								<h3>'.__("YouTube Access Token Has Expired - Please ReAuthenticate", "yt-plus-translation-text-domain") .'</h3>
								<p>'.__("You need to", "yt-plus-translation-text-domain") .' <a href="'.$authUrl.'">'.__("authorize access", "yt-plus-translation-text-domain") .'</a> '.__("before proceeding.", "yt-plus-translation-text-domain") .'<p>
								</div>'; 
					
						}
					
					}
						
					try {	
						// get and store our 'Watch Later' playlist ID for passing along
						$channelsResponse = $youtube->channels->listChannels('contentDetails', array(
						  'mine' => 'true'
						));
						
						 foreach ($channelsResponse['items'] as $playlist) {
							return $playlist['contentDetails']['relatedPlaylists']['watchLater'];
						 }
					} catch( Exception $e ) {
						 $htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
							htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					}
				}
			/*
			*  addVideoToWatchLaterList()
			*  
			*  Add a specific video to the users watch later playlist
			*
			*  Parameters : watch_later_list_id , video_id
			*
			*  @since 2.0
			*/
			
			function addVideoToWatchLaterList($watch_later_list_id,$video_id) {
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					try {
						$resourceId = new Google_Service_YouTube_ResourceId();
							$resourceId->setVideoId($video_id);
							$resourceId->setKind('youtube#video');
							
						$playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
							$playlistItemSnippet->setPlaylistId($watch_later_list_id);
							$playlistItemSnippet->setResourceId($resourceId);
							
						$playlistItem = new Google_Service_YouTube_PlaylistItem();
							$playlistItem->setSnippet($playlistItemSnippet);
						
						$insertVideoToWatchLaterPlaylist = $youtube->playlistItems->insert(
							'snippet,contentDetails', 
							$playlistItem, 
							array()
						);
						
						if ( $insertVideoToWatchLaterPlaylist ) {
							echo 'Success : Video added to Watch Later playlist';
						}
						
					} catch (Google_ServiceException $e) {
						echo $e->getMessage();	
					} catch (Google_Exception $e) {
						if ( strpos($e->getMessage(),'(403) Playlist contains maximum number of items.') !== false ) {
							echo 'Error : Video already exists in Watch Later playlist';
						}
					}
						
				}
			
			/*
			*  addChannelToSubscriptions()
			*  
			*  Subscribe to a new channel , add them
			*  to your subscription list
			*
			*  Parameters : channel_id
			*
			*  @since 2.0
			*/					
			function addChannelToSubscriptions($channel_id) {
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					try {
						$resourceId = new Google_Service_YouTube_ResourceId();
							$resourceId->setChannelId($channel_id);
							$resourceId->setKind('youtube#channel');
							
						$subscriptionSnippet = new Google_Service_YouTube_SubscriptionSnippet();
							$subscriptionSnippet->setResourceId($resourceId);
						
						$subscription = new Google_Service_YouTube_Subscription();
							$subscription->setSnippet($subscriptionSnippet);
							
						$subscriptionResponse = $youtube->subscriptions->insert('id,snippet',
						$subscription, array());
								
						if ( $subscriptionResponse ) {
							// ajax to return our unsubscribe ID (which differs from the subscribe ID)
							// Check if the user is subscribed to this channel
							$is_user_subscribed = $youtube->subscriptions->listSubscriptions('id', array(
									'mine' =>  'true',
									// maybe set up pagination here, for users
									// who have more than 50 vids
									'forChannelId' => $channel_id,
									'maxResults' => 1
								 ));
								
								if ( $is_user_subscribed['items'] ) {
									$unsubscribe_id = $is_user_subscribed['items'][0]['id'];
								}
							echo $unsubscribe_id;
						}
						
					} catch (Google_ServiceException $e) {
						echo '<span id="response_message" class="yt4wp-error-alert"><strong>Oh No!</strong> We have encountered a serious error : <code> ' . $e->getMessage() . '</code>'. $e->getCode();	
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					} catch (Google_Exception $e) {
						echo '<span id="response_message" class="yt4wp-error-alert"><strong>Oh No!</strong> We have encountered a serious error : <code> ' . $e->getMessage() . '</code>'. $e->getCode();
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					}
						
				}
				
			/*
			*  removeChannelSubscription()
			*  
			*  UnSubscribe from a channel previously subscribed too
			*  Removes the channel completely from the list
			*
			*  Parameters : channel_id
			*
			*  @since 2.0
			*/				
			function removeChannelSubscription($channel_id) {
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					try {
									
						$unSubscribeResponse = $youtube->subscriptions->delete("snippet,status",
							array('id' => $channel_id));
								
						if ( $unSubscribeResponse ) {
							echo 'success';
						}
						
					} catch (Google_ServiceException $e) {
						echo '<span id="response_message" class="yt4wp-error-alert"><strong>Oh No!</strong> We have encountered a serious error : <code> ' . $e->getMessage() . '</code>'. $e->getCode();	
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
					} catch (Google_Exception $e) {
						echo '<span id="response_message" class="yt4wp-error-alert"><strong>Oh No!</strong> We have encountered a serious error : <code> ' . $e->getMessage() . '</code>'. $e->getCode();	
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
					}
						
				}
			
			/*
			*  getUsersLikesAndFavoritesVideos()
			*  
			*  Get the specified channel (likes, favorites, watch history)
			*
			*  Parameters : playlist_id , playlist_title , screen_base
			*
			*  @since 2.0
			*/	
			function getUsersLikesAndFavoritesVideos($playlist_id,$playlist_title,$screen_base) {		
			
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
						
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
							// Call the channels.list method to retrieve information about the
							// currently authenticated user's channel.
							$playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('status,snippet', array(
									'playlistId' =>  $playlist_id,
									// maybe set up pagination here, for users
									// who have more than 50 vids
									'maxResults' => 50
								 ));
							$htmlBody = '';
							
							 // add our dialog drawr for error message responses
							 $dialog_drawer = '<section class="dialog_message_drawer"></section>';
									
							// replace the 'inser' and 'view playlist' text
							// that gets passed into the title
							$htmlBody .= "<h3 id='browse_page_title' class='search_browse_page_title'>".stripslashes($playlist_title)." Playlist</h3>";
							
							$htmlBody .= $this->yt4wp_pagination( $playlistItemsResponse , 'browse' );
							
							$htmlBody .= "<ul id='masonry-container'>";
							
							if ( $playlistItemsResponse['items'] ) {	
								
								foreach ($playlistItemsResponse['items'] as $playlistItem) {
								
									// check if the setting is set, so we don't run unecessary API requests
										if ( $this->optionVal['yt4wp-include-stat-count-in-query'] == 'stat-count-enabled' ) {
										
										// loop to get video statistics...
											$video_statistics = $youtube->videos->listVideos('statistics', array(
												'id' => $playlistItem['modelData']['snippet']['resourceId']['videoId']
											 ));
											
											// build the video stats array
											foreach( $video_statistics as $stat ) {
												// $comment_count = $stat['statistics']['commentCount']; // not used, but could be in future releases...
												$dislike_count = '<img src="' . YT4WP_URL . '/images/thumbs-down.svg" alt="dislikes" class="yt-plus-dislikes-icon"> ' . number_format( $stat['statistics']['dislikeCount'] );
												$like_count = '<img src="' . YT4WP_URL . '/images/thumbs-up.svg" alt="likes" class="yt-plus-likes-icon"> ' . number_format( $stat['statistics']['likeCount'] );
												$favorite_count = number_format( $stat['statistics']['favoriteCount'] );
												$view_count = number_format( $stat['statistics']['viewCount'] );
												
												$stat_container = '<span class="yt-plus-stats-container">';
													$stat_container .= '<span class="yt-plus-stats-view-count">' . $view_count . ' views</span>';
													$stat_container .= '<span class="yt-plus-stats-likes-dislikes-favorites-count">' . $dislike_count . '  ' . $like_count . '  <div class="dashicons dashicons-heart"></div>' . $favorite_count . '</span>';
												$stat_container .= '</span>';	
											}
												
										} else { // if the setting is disabled
											// set the stat container to none
											$stat_container = '<span class="yt-plus-stats-container" style="height:0px !important"></span>';
										}	
									
									// grab the privacy settings
									$privacy_setting = $playlistItem['modelData']['status']['privacyStatus'];
								
									// echo $subscriptionsItem['snippet']['resourceId']['channelId'];
									if($playlistItem['modelData']['snippet']['description']) {
										// trim the description
										// if there are more than 400 characters
										if(strlen($playlistItem['modelData']['snippet']['description']) > 325) {
											$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.substr($playlistItem['modelData']['snippet']['description'], 0, 400).'...'; 
										} else {
											$video_description = '<b class="youtube-plus-video-description" style="text-decoration:underline;">Description</b> <br />'.$playlistItem['modelData']['snippet']['description']; 
										}
									} else {
										$video_description = ''; 
									}
									if ( $privacy_setting == 'public' && $playlistItem['modelData']['snippet']['title'] != 'Deleted video' ) {
										$htmlBody .= sprintf('<li class="youtube-plus-video-single-list-item"><input type="hidden" class="video_id" value="%s"><a class="youtube-plus-video-preview-btn thickbox" href="https://www.youtube.com/embed/%s?autoplay=1&?TB_iframe=true&width=600&height=550" target="_blank"><section class="yt-plus-outside-hidden"> %s <img class="youtube-plus-video-thumbnail" src="%s"></a><section class="drawer">%s</section></section> %s <h3>%s</h3> %s </li>',
											  $playlistItem['modelData']['snippet']['resourceId']['videoId'], $playlistItem['modelData']['snippet']['resourceId']['videoId'], $dialog_drawer, $playlistItem['modelData']['snippet']['thumbnails']['high']['url'], apply_filters( 'yt4wp_likes_favs_history_buttons', $this->yt4wp_get_likes_favs_history_buttons($screen_base) ) , $stat_container, $playlistItem['modelData']['snippet']['title'], $video_description );
									}
								}
							} else {
								$htmlBody .= '<h4 class="youtube-plus-no-data-found">Their are currently videos in this playlist.</h4>';
							}
						  $htmlBody .= '</ul>';
						
					  } catch (Google_ServiceException $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
					  } catch (Google_Exception $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					} else {
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 // this runs only the first time the user ever installs the plugin
					}
					?>
						<?php echo $htmlBody; ?>
					<?php
				}
			
			/*
			*  backToPlaylists()
			*  
			*  Back to all play lists (reload users playlists)
			*  fires when user clicks the 'Back to Play list' button
			*
			*  Parameters : screen_base
			*
			*  @since 2.0
			*/			
			function backToPlaylists($screen_base) {
							
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
							
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
							
							$htmlBody = '';
						
							$my_playlists = $youtube->playlists->listPlaylists('snippet,contentDetails', array(
								'mine' =>  'true',
								// maybe set up pagination here, for users
								// who have more than 50 vids
								'maxResults' => 50
							 ));
							
							  $htmlBody .= '<h3>Your Playlists</h3><ul id="masonry-container">'; 
							
							if ( count( $my_playlists['items'] ) > 0 ) {
								foreach ($my_playlists['items'] as $playlist) {
									// Grab Video Count
									$number_of_videos = $playlist['contentDetails']['itemCount'];
									// add empty stat container to unify styles throughout the plugin
									$stat_container = '<span class="yt-plus-stats-container number_of_videos_in_playlist">'.$number_of_videos.' videos</span>';
									
									$htmlBody .= '<li class="youtube-plus-video-single-list-item">';
											$htmlBody .= '<input type="hidden" class="playlist_id" value="' . $playlist['id'] . '">';
											$htmlBody .= '<a class="youtube-plus-video-preview-btn youtube-plus-view-playlist" href="#" onclick="return false;">';
												$htmlBody .= '<section class="yt-plus-outside-hidden">';
												$htmlBody .= '<img class="youtube-plus-video-thumbnail" src="' .  $playlist['modelData']['snippet']['thumbnails']['high']['url'] . '">';
											$htmlBody .= '</a>';
											$htmlBody .= '<section class="drawer">' . apply_filters( 'yt4wp_playlist_buttons', $this->yt4wp_get_playlist_buttons($screen_base) ) . '</section>';
												$htmlBody .= '</section>' . $stat_container;
											$htmlBody .= '<h3><span class="playlist_title">' . $playlist['modelData']['snippet']['title'] . '</span></h3>';
											$htmlBody .= '<p class="youtube-plus-video-description">' . $playlist['modelData']['snippet']['description'] . '</p>'; 
									$htmlBody .= '</li>';
										
								}
							 } else {
								?>
								<style>#masonry-container{height:auto !important;}</style>
								<?php
								$htmlBody .= '<span class="no_content_found_error">You have not created any playlists yet. Why not <a href="#" disabled="disabled" title="Create New Playlist (add on not installed)" class="button-secondary yt-plus-create-new-playlist"><div class="dashicons dashicons-plus" style="line-height:1.5"></div>Create A New Playlist</a> now?</span>';
							 }
							  
						  $htmlBody .= '</ul>';
						
					  } catch (Google_ServiceException $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  } catch (Google_Exception $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					} else {
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 // this runs only the first time the user ever installs the plugin
					}
					$channelsResponse = $youtube->channels->listChannels('contentDetails', array(
						 'mine' => 'true'
					));
					foreach ($channelsResponse['items'] as $playlist) {
						// Extract the unique playlist ID that identifies the list of videos
						// uploaded to the channel, and then call the playlistItems.list method
						// to retrieve that list.
						$uploadsListId = $playlist['contentDetails']['relatedPlaylists']['uploads'];
						$likesListId = $playlist['contentDetails']['relatedPlaylists']['likes'];
						$watchHistoryListId = $playlist['contentDetails']['relatedPlaylists']['watchHistory'];
						$watchLaterListId = $playlist['contentDetails']['relatedPlaylists']['watchLater'];
						$favoritesListId = $playlist['contentDetails']['relatedPlaylists']['favorites'];
					}
					?>
						<!-- navigation items to change the playlist were pulling from -->
						<div id="profile_sub_navigation">
							<ul>
								<li><a href="#" class="button-secondary sub-nav-button" alt="<?php echo $uploadsListId; ?>">Uploads</a></li>
								<li><a href="#" class="button-secondary sub-nav-button sub-nav-button-active" alt="playlists">Playlists</a></li>
								<li><a href="#" class="button-secondary sub-nav-button" alt="<?php echo $likesListId; ?>">Likes</a></li>
								<li><a href="#" class="button-secondary sub-nav-button" alt="<?php echo $favoritesListId; ?>">Favorites</a></li>
								<li><a href="#" class="button-secondary sub-nav-button" alt="<?php echo $watchHistoryListId; ?>">Watch History</a></li>
								<li><a href="#" class="button-secondary sub-nav-button" alt="<?php echo $watchLaterListId; ?>">Watch Later</a></li>				
							</ul>
							<ul id="create_new_playlist_ul">	
									<li><a href="#" disabled="disabled" title="Create New Playlist (add on not installed)" class="button-secondary yt-plus-create-new-playlist"><div class="dashicons dashicons-plus" style="line-height:1.5"></div>Create New Playlist</a></li>
							</ul>
							<input type="hidden" id="watch_later_list_id" value="<?php $this->getUserWatchLaterListId(); ?>">
						</div>
						 
						<?php echo $htmlBody; ?>
						
					<?php
				}
			/*
			*  getSubscriptionPlaylists()
			*  Get a selected subscriptions play lists
			*
			*  Parameters : clicked_subscription , channel_id , screen_base
			*
			*  @since 2.0
			*/	
			function getSubscriptionPlaylists($clicked_subscription,$channel_id,$screen_base) {
								
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// set up the Insert button depending on what page we are on
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$insert_button = '<a class="button-secondary insert_playlist_button" title="Insert Playlist To Post"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
					} else {
						$insert_button = '';
					}
						
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
						
							$htmlBody = '';
							
							// Send an API request to get the users account channels
								$subscriptionData = $youtube->channels->listChannels('contentDetails', array(
									'id' =>  $channel_id,
									// maybe set up pagination here, for users
									// who have more than 50 vids
									'maxResults' => 50
								 ));
							
							// store the subscription upload playlist
							$subscription_uploads_playlist_id = $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
							
							if ( isset( $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['likes'] ) ) {
								$subscription_likes_playlist_id = $subscriptionData['items'][0]['contentDetails']['relatedPlaylists']['likes'];
							} else {
								$subscription_likes_playlist_id = '';
							}
								
							$subscriptionPlaylist = $youtube->playlists->listPlaylists('snippet,contentDetails', array(
									'channelId' =>  $channel_id,
									'maxResults' => 50
								));
							
							
							$htmlBody .= '<h3 id="browse_page_title">'.trim(stripslashes($clicked_subscription)).' Playlists</h3>';
								
							$htmlBody .= $this->yt4wp_pagination( $subscriptionPlaylist , 'browse' );	
							
							$htmlBody .= '<ul id="masonry-container">';
								
							// loop over each item returned in the playlist
							 foreach ($subscriptionPlaylist['items'] as $subscription_playlist_item) {	
								// Grab Video Count
								$number_of_videos = $subscription_playlist_item['contentDetails']['itemCount'];
								// add empty stat container to unify styles throughout the plugin
								$stat_container = '<span class="yt-plus-stats-container number_of_videos_in_playlist">'.$number_of_videos.' videos</span>';
								$htmlBody .= sprintf('<li class="youtube-plus-video-single-list-item"><input type="hidden" class="playlist_id" value="%s"><a class="youtube-plus-video-preview-btn youtube-plus-view-playlist" href="#" onclick="return false;"><section class="yt-plus-outside-hidden"><img class="youtube-plus-video-thumbnail" src="%s"></a><section class="drawer">%s</section></section> %s <h3>%s</h3> %s </li>',
									  $subscription_playlist_item['id'], $subscription_playlist_item['modelData']['snippet']['thumbnails']['high']['url'], $insert_button, $stat_container, $subscription_playlist_item['modelData']['snippet']['title'], $subscription_playlist_item['modelData']['snippet']['description'] );
							  }
							  
						  $htmlBody .= '</ul>';
						  
					  } catch (Google_ServiceException $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );						
					  } catch (Google_Exception $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					  
					} else {
					  
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					
					}
					?>
						<?php echo $htmlBody; ?>
					<?php
				}
			/*
			*  getSubscriptionVideos() 
			*  Get a selected subscriptions uploads
			*
			*  Parameters : channel_id , clicked_subscription , user_name , screen_base , current_tab
			*
			*  @since 2.0
			*/			
			function getSubscriptionVideos($channel_id,$clicked_subscription,$user_name,$screen_base,$current_tab) {
					$screen_base = $screen_base;
					include_once YT4WP_PATH.'templates/browse_subscription_videos.php';
				}
				
			/*
			*  getChannelPlaylists()
			*  Get a selected subscriptions play lists
			*
			*  Parameters : channel_id , clicked_subscription , user_name , screen_base
			*
			*  @since 2.0
			*/	
			function getChannelPlaylists($channel_id,$clicked_subscription,$user_name,$screen_base) {
					$screen_base = $screen_base;
					include_once YT4WP_PATH.'templates/browse_channel_videos.php';
				}
				
			/*
			*  reloadSubscriptions()
			*  reload user subscriptions, when user clicks
			*  'Back to Subscriptions'
			*
			*  @since 2.0
			*/	
			function reloadSubscriptions() {		
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'inc/youtube_subscriptions.php';
				}
				
			/*
			*  updateVideoContainer()
			*  update the single video container after a user
			*  updates video info ( Browse > 'pencil icon' )
			*
			*  @since 2.0
			*/	
			function updateVideoContainer($video_id,$screen_base) {		
				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
						   try {
								
								$videoResponse = $youtube->videos->listVideos("snippet,status",
									array('id' => $video_id));
								// include the required php files - containers client_id and client_secret
								include_once YT4WP_PATH.'templates/single_video_container.php';
							
						} catch (Google_ServiceException $e) {
						
							$htmlBody .= sprintf('<p>%s</p>',
								htmlspecialchars($e->getMessage()));
					 
						} catch (Google_Exception $e) {
						
							$htmlBody .= sprintf('<p>%s</p>',
								htmlspecialchars($e->getMessage()));
					  
						}
					  $_SESSION['token'] = $client->getAccessToken();
					
					} else {
					  
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 
					}
					?>
						<?php echo $htmlBody; ?>

					<?php
				}
			
			/*
			*  updatePlaylistContainer()
			*  update the single playlist container after a user
			*  updates playlist info ( Browse > Playlists > 'pencil icon' )
			*
			*  Parameters : playlist_id , screen_base
			*
			*  @since 2.0
			*/					
			function updatePlaylistContainer($playlist_id,$screen_base) {		
				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					   try {
							
							$playlistResponse = $youtube->playlists->listPlaylists("snippet,status,contentDetails",
								array('id' => $playlist_id));
							// include the required php files - containers client_id and client_secret
							include_once YT4WP_PATH.'templates/single_playlist_container.php';
						
					  } catch (Google_ServiceException $e) {
						$htmlBody .= sprintf('<p><code>%s</code></p>',
						  htmlspecialchars($e->getMessage()));
					  } catch (Google_Exception $e) {
						$htmlBody .= sprintf('<p><code>%s</code></p>',
						  htmlspecialchars($e->getMessage()));
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					} else {
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 // this runs only the first time the user ever installs the plugin
					}
					?>

						<!-- return something //
						// to our ajax function -->
						<?php echo $htmlBody; ?>

					<?php
				}
			/*
			*  generateVideoCategoryDropdown()
			*  generate a dropdown of all possible video categories
			*  to choose from when uploading new content
			*
			*  @since 2.0
			*/				
			function generateVideoCategoryDropdown() {		
				
					// include the required php files - containers client_id and client_secret
					include YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					   try {
							
							$videoCategories = $youtube->videoCategories->listVideoCategories("snippet",
								array( 'regionCode' => $this->optionVal['yt4wp-region'], 'hl' => $this->optionVal['yt4wp-language'] ));
						
							$category_dropdown = '<select name="video-category" id="video-category">';	
								foreach ( $videoCategories as $category ) {
									// only display our assignable categories
									// differs between countries!
									$assignable = $category["snippet"]["assignable"];
									if ( isset( $assignable ) && $assignable == '1' ) {
										$category_dropdown .= '<option value="' . $category["id"] . '" >' . $category["snippet"]["title"] . '</option>';	
									}
								}
							$category_dropdown .= '</select>';
						
					  } catch (Google_ServiceException $e) {
						
						$videoCategories .= sprintf('<p><code>%s</code></p>',
						  htmlspecialchars($e->getMessage()));
					  
					  } catch (Google_Exception $e) {
						
						$videoCategories .= sprintf('<p><code>%s</code></p>',
						  htmlspecialchars($e->getMessage()));
					  
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					
					} else {
					  
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 
					}
					?>
						<?=$category_dropdown?>
					<?php
				}
			/*
			*  generateRegionDropdown() 
			*  generate a dropdown of all possible regions
			*  to choose from on the settings page (dictates possible selectable categories)
			*
			*  @since 2.0
			*/					
			function generateRegionDropdown() {		
				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// if $errors are set, we've ancountered an error
					// in the above file, so we need to abort mission at all costs!
					if ( !isset( $errors ) ) {
					
						// Check to ensure that the access token was successfully acquired.
						if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
							
						   try {
								
								$i18nregions = $youtube->i18nRegions->listI18nRegions("snippet");
								
								$country_dropdown = '<select name="yt4wp-region" id="yt4wp-region">';	
									foreach ( $i18nregions as $region ) {
										if ( $this->optionVal["yt4wp-region"] == $region["id"] ) {
											$country_dropdown .= '<option value="' . $region["id"] . '" selected="selected" >' . $region["snippet"]["name"] . '</option>';
										} else {
											$country_dropdown .= '<option value="' . $region["id"] . '" >' . $region["snippet"]["name"] . '</option>';
										}
									}
								$country_dropdown .= '</select>';
								
						  } catch (Google_ServiceException $e) {
							echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> ' . $e->getMessage() . '.<p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';
						  } catch (Google_Exception $e) {
							echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> ' . $e->getMessage() . '.<p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';
						 }
						  $_SESSION['token'] = $client->getAccessToken();
						} else {
						  $state = mt_rand();
						  $client->setState($state);
						  $_SESSION['state'] = $state;
						  $authUrl = $client->createAuthUrl();
						 
						}
						
						echo $country_dropdown;
					
					}
				}
			/*
			*  generateRegionDropdown()
			*  generate a dropdown of all possible regions
			*  to choose from on the settings page (dictates possible selectable categories)
			*
			*  @since 2.0
			*/				
			function generateLanguageDropdown() {		
				
					// include the required php files - containers client_id and client_secret
					include YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					
					// if $errors are set, we've ancountered an error
					// in the above file, so we need to abort mission at all costs!
					if ( ! isset( $errors ) ) {
					
						// Check to ensure that the access token was successfully acquired.
						if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
							
						   try {
								
								$i18nLanguages = $youtube->i18nLanguages->listI18nLanguages("snippet");
								
								$language_dropdown = '<select name="yt4wp-language" id="yt4wp-language">';
									foreach ( $i18nLanguages as $language ) {
										if ( $this->optionVal["yt4wp-language"] == $language["id"] ) {
											$language_dropdown .= '<option value="' . $language["id"] . '" selected="selected" >' . $language["snippet"]["name"] . '</option>';
										} else {
											$language_dropdown .= '<option value="' . $language["id"] . '" >' . $language["snippet"]["name"] . '</option>';
										}
									}
								$language_dropdown .= '</select>';
								
						  } catch (Google_ServiceException $e) {						
								echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> ' . $e->getMessage() . '<p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';
								/* Write the error to our error log */
								$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						  } catch (Google_Exception $e) {							
								echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> ' . $e->getMessage() . '<p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';
								/* Write the error to our error log */
								$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );			
						  }
						
						  $_SESSION['token'] = $client->getAccessToken();
						
						} else {
						  $state = mt_rand();
						  $client->setState($state);
						  $_SESSION['state'] = $state;
						  $authUrl = $client->createAuthUrl();
						
						}
						echo $language_dropdown;
					}
				}
			/*
			*  generateEditVideoForm() 
			*  generate our edit video form
			* form displays when user clicks the pencil icon 'Browse'
			*
			*  @since 2.0
			*/					
			function generateEditVideoForm() {
					?>
					<div id="edit-video-dialog-form" title="Edit Video" style="display:none;">
					  <p class="validateTips">All form fields are required.</p>
					  <form>
						<fieldset>
						  <label for="video_title">Video Title</label><br />
						  <input type="text" name="video_title" id="video_title" class="text ui-widget-content ui-corner-all edit_video_title"><br />
						  <label for="video_description">Video Description</label><br />
						  <textarea name="video_description" id="video_description" class="text ui-widget-content ui-corner-all edit_video_description"></textarea><br />
						  <label for="video_tags">Video Tags</label><br />
						  <input type="text" name="video_tags" id="video_tags" class="text ui-widget-content ui-corner-all edit_video_tags"><br />
						  <label for="video_category">Video Category</label><br />
						  <span style="display:block; margin-bottom:1em;"><?php $this->generateVideoCategoryDropdown(); ?></span>
						  <label for="video_privacy">Video Privacy</label><br />
						  <label><input type="radio" name="video_privacy[]"  value="public" class="text ui-widget-content ui-corner-all edit_video_privacy public">Public</label>
						 <label><input type="radio" name="video_privacy[]"  value="private" class="text ui-widget-content ui-corner-all edit_video_privacy private">Private</label>
						  <label><input type="radio" name="video_privacy[]"  value="unlisted" class="text ui-widget-content ui-corner-all edit_video_privacy unlisted">Unlisted</label>
						  <!-- the video id -->
						  <input type="hidden" class="video_id" id="video_id">
						  <!-- Allow form submission with keyboard without duplicating the dialog button -->
						  <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
						  
						</fieldset>
					  </form>
					</div>
					<?php
				}
				
			/*
			*  generateEditPlaylistForm() 
			*  generate our edit play list form
			*  form displays when user clicks the pencil icon 'Browse'
			*
			*  @since 2.0
			*/				
			function generateEditPlaylistForm() {
					?>
					<div id="edit-playlist-dialog-form" title="Edit Playlist" style="display:none;">
					  <p class="validateTips">All form fields are required.</p>
					  <form>
						<fieldset>
						  <!-- need to populate this with the possible thumbnails for each playlist -->
						  <label for="playlist_title">Playlist Title</label><br />
						  <input type="text" name="playlist_title" id="playlist_title" class="text ui-widget-content ui-corner-all edit_playlist_title"><br />
						  <label for="playlist_description">Playlist Description</label><br />
						  <textarea name="playlist_description" id="playlist_description" class="text ui-widget-content ui-corner-all edit_playlist_description"></textarea><br />
						  <label for="playlist_tags">Playlist Tags</label><br />
						  <input type="text" name="playlist_tags" id="playlist_tags" class="text ui-widget-content ui-corner-all edit_playlist_tags"><br />
						  <label for="playlist_privacy">Playlist Privacy</label><br />
						  <label><input type="radio" name="playlist_privacy[]"  value="public" class="text ui-widget-content ui-corner-all edit_playlist_privacy public">Public</label>
						 <label><input type="radio" name="playlist_privacy[]"  value="private" class="text ui-widget-content ui-corner-all edit_playlist_privacy private">Private</label>
						  <label><input type="radio" name="playlist_privacy[]"  value="unlisted" class="text ui-widget-content ui-corner-all edit_playlist_privacy unlisted">Unlisted</label>
						  <!-- the video id -->
						  <input type="hidden" class="playlist_id" id="playlist_id">
						  <!-- Allow form submission with keyboard without duplicating the dialog button -->
						  <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
						</fieldset>
					  </form>
					</div>
					<?php
				}
			/*
			*  getSelectedVideoResponse() 
			*  get the selected video data, to populate
			*  the edit video form with the correct data
			*
			*  @since 2.0
			*/					
			function getSelectedVideoResponse($video_id) {				
				// include the required php files - containers client_id and client_secret
				include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
				// Check to ensure that the access token was successfully acquired.
				if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
					
				  try {
					
					$listResponse = $youtube->videos->listVideos("snippet,status",
						array('id' => $video_id));
							
						echo json_encode($listResponse[0]['modelData']);
					
				  } catch (Google_ServiceException $e) {
					
					$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
					  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
				  } catch (Google_Exception $e) {
					
					$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
					  htmlspecialchars($e->getMessage()),$e->getCode());
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
				  }
				  $_SESSION['token'] = $client->getAccessToken();
				
				} else {
				  
				  $state = mt_rand();
				  $client->setState($state);
				  $_SESSION['state'] = $state;
				  $authUrl = $client->createAuthUrl();
				 
				 
				}
			}
			
			/*
			*  editExistingVideo() 
			*  update an existing video with new data
			*  
			*  Paramaters : screen_base , video_title , video_description , video_id , video_tags , video_category , video_privacy
			*
			*  @since 2.0
			*/					
			function editExistingVideo($screen_base,$video_title,$video_description,$video_id,$video_tags,$video_category,$video_privacy) {				
				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
						
							$listResponse = $youtube->videos->listVideos("snippet,status",
								array('id' => $video_id));
								
							$new_video = $listResponse[0];
										
							$new_video['snippet']['tags'] = explode( ',' , stripslashes( $video_tags ) );
							
							$new_video['snippet']['categoryId'] = $video_category;
							
							$new_video['snippet']['title'] = stripslashes( $video_title );
							
							$new_video['snippet']['description'] = stripslashes( $video_description );
							
							$new_video['status']['privacyStatus'] = $video_privacy;
							
							
							$update_video_response = $youtube->videos->update('snippet,status', $new_video );
										
					  } catch (Google_ServiceException $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  } catch (Google_Exception $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					} else {
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 // this runs only the first time the user ever installs the plugin
					}
				}
			/*
			*  getSelectedPlaylistResponse()  
			*  get the current set data for a clicked playlist (used to populate the edit playlist modal with data)
			*  
			*  @since 2.0
			*/			
			function getSelectedPlaylistResponse($playlist_id) {				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
						
						$playlistResponse = $youtube->playlists->listPlaylists("snippet,status",
								array('id' => $playlist_id));
								
							echo json_encode($playlistResponse[0]['modelData']);
						
					  } catch (Google_ServiceException $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );						
					  } catch (Google_Exception $e) {
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );		
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					} else {
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 // this runs only the first time the user ever installs the plugin
					}
				}
				
			/*
			*  editExistingPlaylist()  
			*  update a previously set playlist with the newly specified data
			*
			*  Paramaters : screen_base , playlist_title , playlist_description , playlist_id , playlist_tags , video_privacy	
			*  @since 2.0
			*/					
			function editExistingPlaylist($screen_base,$playlist_title,$playlist_description,$playlist_id,$playlist_tags,$playlist_privacy) {				
				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
						
							$listResponse = $youtube->playlists->listPlaylists("snippet,status", 
								array('id' => $playlist_id));
								
							$new_playlist_data = $listResponse[0];
										
							$new_playlist_data['snippet']['tags'] = explode( ',' , stripslashes( $playlist_tags ) );
							
							$new_playlist_data['snippet']['title'] = stripslashes( $playlist_title );
							
							$new_playlist_data['snippet']['description'] = stripslashes( $playlist_description );
							
							$new_playlist_data['status']['privacyStatus'] = $playlist_privacy;
									
							$update_playlist_response = $youtube->playlists->update('snippet,status', $new_playlist_data );
										
					  } catch (Google_ServiceException $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  } catch (Google_Exception $e) {
						
						$htmlBody .= sprintf('<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : %s. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #%s</p></p></span>',
						  htmlspecialchars($e->getMessage()),$e->getCode());
							/* Write the error to our error log */
							$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
						
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					
					} else {
					  
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					
					}
				}
			
			/*
			*  deleteUserVideo()
			*  completely delete an existing video from the users channel
			*
			*  Parameters : video_id
			*
			*  @since 2.0
			*/				
			function deleteUserVideo( $video_id ) {				
				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
						
							$listResponse = $youtube->videos->delete("snippet,status",
								array('id' => $video_id));
													
					  } catch (Google_ServiceException $e) {
						echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : ' .  htmlspecialchars($e->getMessage()) . '. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );						
					  } catch (Google_Exception $e) {
						echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : ' .  htmlspecialchars($e->getMessage()) . '. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
					 }
					  $_SESSION['token'] = $client->getAccessToken();
					} else {
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					 // this runs only the first time the user ever installs the plugin
					}
				}
				
			/*
			*  deleteVideoFromPlaylist() 
			*  completely remove a video from a given playlist on the users channel
			*  also used to remove videos from the watch later playlist
			*
			*  Parameters : playlistItemId
			*
			*  @since 2.0
			*/		
			function deleteVideoFromPlaylist($playlistItemId) {				
				
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					// Check to ensure that the access token was successfully acquired.
					if ( get_option( 'yt4wp_user_refresh_token' ) != '' && isset($_SESSION["token"]) && $_SESSION["token"] != '' ) {
						
					  try {
						
							$deletePlaylistItem = $youtube->playlistItems->delete('status,snippet', array(
									'id' =>  $playlistItemId // playlist Item ID
								 ));	
													
					  } catch (Google_ServiceException $e) {
						echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : ' .  htmlspecialchars($e->getMessage()) . '. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';					  
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );		
					  } catch (Google_Exception $e) {					
						echo '<span id="response_message" class="yt4wp-error-alert"><p><strong>Oh No!</strong> We have encountered an error : ' .  htmlspecialchars($e->getMessage()) . '. Please refresh the page and try again. <p>If the error persits please <a href="http://www.yt4wp.com/support/?utm_source=ytwp-admin-error-alert&utm_medium=text-link&utm_campaign=open-support-ticket" target="_blank" title="Open a Ticket">open a support ticket</a> with the YouTube for WordPress support team and reference the following error number: Error #' . $e->getCode() . '</p></p></span>';	  
						/* Write the error to our error log */
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );	
					  }
					  $_SESSION['token'] = $client->getAccessToken();
					
					} else {  
					  $state = mt_rand();
					  $client->setState($state);
					  $_SESSION['state'] = $state;
					  $authUrl = $client->createAuthUrl();
					}
				}
			
			/*
			*  youtubePlusCustomRSS()
			*  add a custom RSS feed to display content in a sidebar
			*
			*  @since 2.0
			*/
			function youtubePlusCustomRSS(){
					add_feed( 'youtube_user_feed', 'youtubePlusCustomRSSFunc');
				} 
	
			/*
			*  youtubePlusCustomRSSFunc()
			*  include our custom RSS feed template file
			*
			*  @since 2.0
			*/	
			function youtubePlusCustomRSSFunc(){
					include_once YT4WP_PATH.'templates/rss-youtube-feed-template.php';
				}
			/*
			*  logOutAndRevokeAccessToken()
			*  logout and revoke access token from the currently authenticated user
			*  this will force the user to have to re-grant access to the application
			*
			*  @since 2.0
			*/				
			function logOutAndRevokeAccessToken($access_token) {
					// include the required php files - containers client_id and client_secret
					include_once YT4WP_PATH.'lib/google_api_wrapper_clientid_clientsecret.php';
					update_option('yt4wp_user_refresh_token',''); // clear the refresh token
					unset($_SESSION);
					echo $client->revokeToken(); // revoke access token	
				}
		
		/**************************************************************************/
		/*					Generate YouTube for WordPressButtons			*/
			/*
			*  yt4wp_get_browse_buttons()
			*  generate the buttons that appear in the drawer
			*  on the browse page
			*
			*  Parameters : screen_base
			*
			*  @since 2.0
			*/	
			public function yt4wp_get_browse_buttons($screen_base) {
					// conditionally build and return our button array
					// we can use this for extending functionality and adding custom buttonse;				
					// global buttons
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$line_height = '1.2';
					} else {
						$line_height = '1.3';
					}
					$browse_buttons = '';
					$browse_buttons .= '<a class="button-secondary edit_video_button" href="#" onclick="return false;" title="Edit Video Details"><div class="dashicons dashicons-edit" style="line-height:'.$line_height.'"></div></a>';
					$browse_buttons .= '<a class="button-secondary delete_video_button" href="#" onclick="return false;" title="Delete This Video"><div class="dashicons dashicons-trash" style="line-height:1.3"></div></a>';
					$browse_buttons .= '<a class="button-secondary add_to_watch_later" title="Add to Watch Later"><div class="dashicons dashicons-clock" style="line-height:1.3"></div></a>';				
					// admin page specific
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$browse_buttons .= '<a class="button-secondary insert_video_button" title="Insert Video To Post"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
					} else { // other (post.php page)
						$browse_buttons .= '<a class="button-secondary create_video_post_button" disabled="disabled" title="Create Post From This Video (add on not installed)"><div class="dashicons dashicons-media-text" style="line-height:1.3"></div></a>';
					}					
					return $browse_buttons;						
				}
			
			/*
			*  yt4wp_get_browse_buttons()
			*  generate the buttons that appear in the drawer
			*  on the browse page
			*
			*  Parameters : screen_base
			*
			*  @since 2.0
			*/		
			public function yt4wp_get_playlist_buttons($screen_base) {
					// conditionally build and return our button array
					// we can use this for extending functionality and adding custom buttons
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$line_height = '1.2';
					} else {
						$line_height = '1.3';
					}
					// global buttons
					$playlist_buttons = '';
					$playlist_buttons .= '<a class="button-secondary edit_playlist_button" href="#" onclick="return false;" title="Edit Playlist Details"><div class="dashicons dashicons-edit" style="line-height:'.$line_height.'"></div></a>';
					$playlist_buttons .= '<a class="button-secondary youtube-plus-delete-playlist-button" title="Delete Playlist (add on not installed)" disabled="disabled" href="#" onclick="return false;"><div class="dashicons dashicons-trash" style="line-height:1.3;"></div></a>';					
					// admin page specific
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$playlist_buttons .= '<a class="button-secondary insert_playlist_button" title="Insert Playlist To Post"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
					} else { // other (post.php page)					
						$playlist_buttons = '';
					}						
					return $playlist_buttons;					
				}
				
			/*
			*  yt4wp_get_users_playlist_item_buttons()
			*  generate the buttons that appear in the drawer
			*  on the browse page for a play list
			*
			*  Parameters : screen_base , current_tab
			*
			*  @since 2.0
			*/	
			// note : these buttons are displayed when you are browsing
			// through one of YOUR OWN play lists ( not another users )
			public function yt4wp_get_users_playlist_item_buttons( $screen_base , $current_tab ) {
					// conditionally build and return out button array
					// we can use this for extending functionality and adding custom buttons
					
					$user_playlist_item_buttons = '';
					// if we're on the search page,
					// users shouldn't have the option to 
					// edit the video
					if ( $current_tab != 'Search' ) {
						$user_playlist_item_buttons .= '<a class="button-secondary edit_video_button" href="#" onclick="return false;" title="Edit Video Details"><div class="dashicons dashicons-edit" style="line-height:1.3"></div></a>';
					}	
					
					$user_playlist_item_buttons .= '<a class="button-secondary add_to_watch_later" title="Add to Watch Later"><div class="dashicons dashicons-clock" style="line-height:1.3"></div></a>';	
					$user_playlist_item_buttons .= '<a class="button-secondary youtube-plus-delete-video-button" disabled="disabled" title="Remove Video From This Playlist (add on not installed)" href="#" onclick="return false;"><div class="dashicons dashicons-trash" style="line-height:1.3;"></div></a>';		
					
					// admin page specific
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$user_playlist_item_buttons .= '<a class="button-secondary insert_video_button"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
					} else { // other (post.php page)
						$user_playlist_item_buttons .= '<a class="button-secondary create_video_post_button" disabled="disabled" title="Create Post From This Video (add on not installed)"><div class="dashicons dashicons-media-text" style="line-height:1.3"></div></a>';
					}
						
					return $user_playlist_item_buttons;
						
				}
				
			
			/*
			*  yt4wp_get_likes_favs_history_buttons()
			*  generate the buttons that appear in the drawer
			*  on the users likes, favorites and watch history page
			*
			*  Parameters : screen_base
			*
			*  @since 2.0
			*/	
			public function yt4wp_get_likes_favs_history_buttons($screen_base) {
					// conditionally build and return out button array
					// we can use this for extending functionality and adding custom buttons
					$likes_favs_history_buttons = '';
					$likes_favs_history_buttons .= '<a class="button-secondary add_to_watch_later" title="Add to Watch Later"><div class="dashicons dashicons-clock" style="line-height:1.3"></div></a>';	
					$likes_favs_history_buttons .= '<a class="button-secondary send_to_playlist_button" disabled="disabled" title="Add Video To Playlist (add on not installed)"><div class="dashicons dashicons-editor-ol" style="line-height:1.3"></div></a>';
					// admin page specific
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$likes_favs_history_buttons .= '<a class="button-secondary insert_video_button"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
					} else { // other (post.php page)
						$likes_favs_history_buttons .= '<a class="button-secondary create_video_post_button" disabled="disabled" title="Create Post From This Video (add on not installed)"><div class="dashicons dashicons-media-text" style="line-height:1.3"></div></a>';
					}
						
					return $likes_favs_history_buttons;
						
				}
				
			/*
			*  yt4wp_get_watch_later_buttons()
			*  generate the buttons that appear in the drawer
			*  on the watch later videos
			*
			*  Parameters : screen_base
			*
			*  @since 2.0
			*/					
			public function yt4wp_get_watch_later_buttons($screen_base) {
					// conditionally build and return out button array
					// we can use this for extending functionality and adding custom buttons
					$watch_later_buttons = '';
					$watch_later_buttons .= '<a class="button-secondary remove_video_from_watch_later" title="Remove Video from Watch Later"><div class="dashicons dashicons-no" style="line-height: 2.2;position: absolute;color: rgb(248, 47, 47);font-size: 17px;margin-left: 7px;"></div><div class="dashicons dashicons-clock" style="line-height: 1.1;font-size: 25px;margin-left: -2px;"></div></a>';
					// admin page specific
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$watch_later_buttons .= '<a class="button-secondary insert_video_button"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
					} else { // other (post.php page)
						$watch_later_buttons .= '<a class="button-secondary create_video_post_button" disabled="disabled" title="Create Post From This Video (add on not installed)"><div class="dashicons dashicons-media-text" style="line-height:1.3"></div></a>';
					}
						
					return $watch_later_buttons;
						
				}
			
			/*
			*  yt4wp_get_channel_results_buttons()
			*  generate the buttons that appear in the drawer
			*  on the channel results page
			*
			*  Parameters : screen_base
			*
			*  @since 2.0
			*/			
			public function yt4wp_get_channel_results_buttons($screen_base) {
				// conditionally build and return out button array
				// we can use this for extending functionality and adding custom buttons				
				$channel_results_buttons = '';
				// admin page specific
				if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
					
				} else { // other (post.php page)
					
				}					
				return $channel_results_buttons;			
			}
			
			/*
			*  yt4wp_get_playlist_results_buttons()
			*  generate the buttons that appear in the drawer
			*  on the play lists search results
			*
			*  Parameters : screen_base
			*
			*  @since 2.0
			*/	
			public function yt4wp_get_playlist_results_buttons($screen_base) {
					// conditionally build and return out button array
					// we can use this for extending functionality and adding custom buttons
					// get the screen base (dictates what buttons will appear where)
					// $screen_base = get_current_screen()->base;
					$playlist_results_buttons = '';
					// admin page specific
					if ( $screen_base != 'toplevel_page_youtube-for-wordpress' ) { 
						$playlist_results_buttons .= '<a class="button-secondary insert_playlist_button" title="Insert Playlist To Post"><div class="dashicons dashicons-external" style="line-height:1.3"></div></a>';
					} else { // other (post.php page)
						
					}						
					return $playlist_results_buttons;						
				}
				
			/*
			*  yt4wp_generate_error_log_table()
			*  generate our erorr log table on the options settings page
			*
			*  @since 2.0.2
			*/	
			public function yt4wp_generate_error_log_table() {					
					$error_log_contents = file_get_contents( YT4WP_PATH . 'inc/error_log/yt4wp_error_log.php' , true );					
					if ( $error_log_contents != '' ) {
						return $error_log_contents;
					}			
				}	
				
			/* 
			* clear_error_log() 
			* clears the erorr log back to empty
			*
			* @since 2.0.2
			*/
			public function clear_yt4wp_error_log() {
					try {
						$clear_contents = file_put_contents( YT4WP_PATH . 'inc/error_log/yt4wp_error_log.php' , '' );
					} catch ( Exception $e ) {
						return $e->getMessage();
						$this->writeErrorToErrorLog( $e->getMessage() , $e->getCode() );
					}
				}
			
			/* 
			* writeErrorToErrorLog() 
			* parameters: $error_message , $error_code
			*
			* writes a returned API error to our log for display
			*
			* @since 2.0.2
			*/
			public function writeErrorToErrorLog( $error_message , $error_code ) {
				// don't write the error if our settings are empty, this probably means they just installed things
				if( $this->optionVal['yt4wp-oauth2-key'] == '' && $this->optionVal['yt4wp-oauth2-secret'] == '' && $this->optionVal['yt4wp-api-key'] == '' ) {
					return;
				}
				// make sure file_get_contents and file_put_contents are available
				if ( function_exists( 'file_get_contents' ) && function_exists( 'file_put_contents' ) ) {	
					$error_occurance_time = current_time( 'M' ) . '. ' . current_time( 'jS' ) . ', ' . current_time( 'Y' ) . ' - ' . current_time( 'g:i:sa' );
					$error_log_location = YT4WP_PATH . 'inc/error_log/yt4wp_error_log.php';
					$current_contents = file_get_contents( $error_log_location );
					// get total count of errors, we only want to limit to 8 latest errors
					$total_errors = explode( '<tr>' , $current_contents );
					$error_array = array();
					$i = 0;
					foreach( $total_errors as $error ) {	
						$error_array[] = $error;
						// limit the error log to the latest 5 errors (or whatever the user has set)
						if ( ++$i == $this->optionVal['yt4wp-limit-error-log-count'] ) {
							break;
						}
					}
					$new_content = '<tr>
						<td>' . $error_message . '</td>
						<td>#' . $error_code . '</td>
						<td>' . $error_occurance_time . '</td>
					</tr>' . implode( '<tr>' , $error_array );
					file_put_contents( $error_log_location , $new_content );
				}
			}				
		/*					         End YouTube Buttons	    				*/
		/*******************************************************************/
		/***
		** Custom/Hookable Filters
		***/
			/*
			*  youtube_for_wordpress_filter_browse_buttons()
			*  custom filter to enable extending the browse buttons or adding a custom button
			*  @since 2.0
			*/	
			public function youtube_for_wordpress_filter_browse_buttons( $browse_buttons ) {
					return $browse_buttons;
				}
			
			/*
			*  youtube_for_wordpress_filter_playlist_buttons()
			*  custom filter to enable extending the playlist buttons or adding a custom button
			*  @since 2.0
			*/	
			public function youtube_for_wordpress_filter_playlist_buttons( $playlist_buttons ) {
					return $playlist_buttons;
				}
			
			/*
			*  youtube_for_wordpress_filter_user_playlist_item_buttons()
			*  custom filter to enable extending your own playlist buttons or adding a custom button ( not search results , but personal playlist )
			*  @since 2.0
			*/	
			public function youtube_for_wordpress_filter_user_playlist_item_buttons( $user_playlist_item_buttons ) {
					return $user_playlist_item_buttons;
				}		
			
			/*
			*  youtube_for_wordpress_filter_likes_favs_history_buttons()
			*  custom filter to enable extending the Likes, Favorites and Watch History buttons or adding custom buttons
			*  @since 2.0
			*/	
			public function youtube_for_wordpress_filter_likes_favs_history_buttons( $user_likes_favs_history_buttons ) {
					return $user_likes_favs_history_buttons;
				}
			
			/*
			*  youtube_for_wordpress_filter_watch_later_buttons()
			*  custom filter to enable extending the Watch Later buttons or adding custom buttons
			*  @since 2.0
			*/
			public function youtube_for_wordpress_filter_watch_later_buttons( $user_watch_later_buttons ) {
					return $user_watch_later_buttons;
				}
			
			/*
			*  youtube_for_wordpress_filter_channel_results_buttons()
			*  custom filter to enable extending the channels buttons or adding custom buttons
			*  @since 2.0
			*/
			public function youtube_for_wordpress_filter_channel_results_buttons( $channel_results_buttons ) {
					return $channel_results_buttons;
				}
			
			/*
			*  youtube_for_wordpress_filter_playlist_results_buttons()
			*  custom filter to enable extending the playlists buttons or adding custom buttons on the search results page only!
			*  @since 2.0
			*/
			public function youtube_for_wordpress_filter_playlist_results_buttons( $playlist_results_buttons ) {
					return $playlist_results_buttons;
				}
			
			/*
			*  yt4wp_addon_tabs()
			*
			*  custom filter to enable extending the tabs 
			*  used on options page, but should also be extended too
			*  target the main page of the plugin
			*
			*  @since 2.0
			*/
			public function yt4wp_addon_tabs( $addon_tabs ) {
					return $addon_tabs;
				}
		  
		 /**
			**	End YouTube Hookable Filters
		 **/ 
		  
			/*
			*  yt4wp_pagination()
			*  generate the pagination button across all pages
			*
			*  Parameters : youtube_api_data ( pass in the entire array of returned YouTub0e API data )
			*
			*  @since 2.0
			*/		
			public function yt4wp_pagination( $youtube_api_data , $tab ) {
		  		  
					// set up + store next page token variable
					if ( isset( $youtube_api_data['nextPageToken'] ) ) {
						if ( $youtube_api_data['modelData']['pageInfo']['resultsPerPage'] == count($youtube_api_data['items'] ) ) {
							$nextPageToken = $youtube_api_data['nextPageToken'];
						}	
					}
					// set up + store previous page token variable
					if ( isset( $youtube_api_data['prevPageToken'] ) ) {
						$previousPageToken = $youtube_api_data['prevPageToken'];
					}
				  
				  
					// set up the pagination buttons
					if ( isset ( $previousPageToken ) ) {
						$previous_pagination = '<a href="#" class="pagination_page_'.$tab.' button-secondary" onclick="return false;" alt="'.$previousPageToken.'" alt="Previous Page" title="Previous"><div class="dashicons dashicons-arrow-left-alt2" style="line-height:1"></div></a>';
					} else {
						$previous_pagination = '<a href="#" class="pagination_page_'.$tab.'_disabled button-secondary" onclick="return false;" alt="Previous Page" title="Previous"><div class="dashicons dashicons-arrow-left-alt2" style="line-height:1"></div></a>';
					}
					if ( isset( $nextPageToken ) ) {
						$next_pagination =  '<a href="#" class="pagination_page_'.$tab.' button-secondary" onclick="return false;" alt="'.$nextPageToken.'" alt="Next Page" title="Next"><div class="dashicons dashicons-arrow-right-alt2" style="line-height:1.3"></div></a>';
					} else {
						$next_pagination =  '<a href="#" class="pagination_page_'.$tab.'_disabled button-secondary" onclick="return false;" alt="Next Page" title="Next"><div class="dashicons dashicons-arrow-right-alt2" style="line-height:1.3"></div></a>';
					}
					
					// return our pagination buttons
					return $previous_pagination . $next_pagination;
		  
				}
		  	  
		/*
		**	End YouTube Plus Functions
		*/ 
		 
		/***** SCRIPTS/STYLES
		 ****************************************************************************************************/
			/*
			*  addStyles()
			*  enqueue our styles into the dashboard	
			*
			*  @since 2.0
			*/
			public function addStyles($hook) {
								
				// set up an array of pages to enqueue our scripts on
				$page_array = array( 'toplevel_page_youtube-for-wordpress' , 'edit.php' , 'post-new.php' , 'post.php' , 'youtube-for-wp_page_youtube-for-wordpress-settings' );
				
				$all_yt4wp_pages = array( 'toplevel_page_youtube-for-wordpress' , 'edit.php' , 'post-new.php' , 'post.php' , 'widgets.php' , 'youtube-for-wp_page_youtube-for-wordpress-settings' , 'youtube-for-wp_page_youtube-for-wordpress-support' , 'youtube-for-wp_page_youtube-for-wordpress-add-ons' , 'admin_page_youtube-for-wordpress-welcome' );
				
				// load our admin styles on all yt4wp pages
				if ( in_array( $hook , $all_yt4wp_pages ) ) {
					// wp_register_style( 'yt4wp_sess-css-base' , YT4WP_URL . 'css/yt4wp-admin.css' , array() , '1.0.0' , 'all' );
					wp_register_style( 'yt4wp_sess-css-base' , YT4WP_URL . 'css/yt4wp-admin.min.css' , array() , '1.0.0' , 'all' );
					wp_enqueue_style( 'yt4wp_sess-css-base' );
				}
				
				// only load our css files if we're on one of the pages above
				if ( in_array( $hook ,  $page_array ) ) {
				
					// Register Styles
										
					// register settings sidebar styles
					wp_register_style( 'yt4wp-settings-sidebar-css' , YT4WP_URL . '/css/yt4wp-settings-sidebar.css' , array() , 'all' );
					
					// register the error log table styles
					wp_register_style( 'yt4wp-error-log-table-css' , YT4WP_URL . '/css/yt4wp-error-table.css' , array() , 'all' );
					
					// Enqueue Styles	
					wp_enqueue_style( 'thickbox' );
					wp_enqueue_style( 'jqt-jquery-ui-style' , YT4WP_URL . 'css/jquit-jquery-ui-style.min.css' );
					wp_enqueue_style( 'yt4wp-settings-sidebar-css' );
					wp_enqueue_style( 'yt4wp-error-log-table-css' );
					
					// custom action hook for extension to hook into frontend side style enqueueing
					do_action( 'yt4wp-enqueue-styles-dashboard' );	 
					
				}
				
				// load our settings sidebar and addons css on the welcome page and on the add-ons page
				if ( in_array( $hook , array( 'youtube-for-wp_page_youtube-for-wordpress-add-ons' , 'admin_page_youtube-for-wordpress-welcome' ) ) ) {
					// register settings sidebar styles
					wp_register_style( 'yt4wp-settings-sidebar-css' , YT4WP_URL . '/css/yt4wp-settings-sidebar.css' , array() , 'all' );
					wp_register_style( 'yt4wp-addons-css' , YT4WP_URL . '/css/yt4wp-addons-styles.css' , array() , 'all' );
					
					wp_enqueue_style( 'yt4wp-settings-sidebar-css' );
					wp_enqueue_style( 'yt4wp-addons-css' );
				}
				
				// load our support page styles 
				if ( in_array( $hook , array( 'youtube-for-wp_page_youtube-for-wordpress-support' ) ) ) {
					wp_register_style( 'yt4wp-settings-sidebar-css' , YT4WP_URL . '/css/yt4wp-settings-sidebar.css' , array() , 'all' );
					wp_enqueue_style( 'yt4wp-settings-sidebar-css' );
				}
						
			}
			
			
			/*
			*  addStyles_frontend()
			*  enqueue our styles onto the frontend of the site	
			*
			*  @since 2.0
			*/
			public function addStyles_frontend() {
					// Register Styles
					// wp_register_style( 'yt4wp_sess-front-end-css-base' , YT4WP_URL . 'css/yt4wp-frontend.css' , array() , '1.0.0' , 'all' );
					wp_register_style( 'yt4wp_sess-front-end-css-base' , YT4WP_URL . 'css/yt4wp-frontend.min.css' , array() , '1.0.0' , 'all' );
					// Enqueue Styles
					wp_enqueue_style('yt4wp_sess-front-end-css-base');
					// enqueue fancybox for widget
					wp_enqueue_style( 'fancy-css' , '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css' );
					
						// check if there is a mediaelement_custom.css file
						// in theme/media-element/mediaelement_custom.css
							// allows users to define their own media element styles
						if ( file_exists ( get_stylesheet_directory()."/media-element/mediaelement_custom.css" ) ) {
							// de-register default styles
							wp_deregister_style( 'mediaelement' );
							wp_deregister_style( 'wp-mediaelement' );
							// enqueue theme defined media element
							wp_enqueue_style( 
								'media_element_custom', 
								get_template_directory_uri()."/media-element/mediaelement_custom.css",
								null,
								'1.0.0'
							);						
						}					
						// custom action hook for extension to hook into frontend side style enqueueing
						do_action( 'yt4wp-enqueue-styles-frontend' );
				}
			
			/*
			*  addStyles_frontend()
			*  enqueue our scripts onto the dashboard	
			*
			*  @since 2.0
			*/			
			public function addScripts($hook) {		
					// set up an array of pages to enqueue our scripts on
					$page_array = array( 'toplevel_page_youtube-for-wordpress' , 'edit.php' , 'post-new.php' , 'post.php' );
	
					// only load our js files if we're on one of the pages above
					if ( in_array( $hook ,  $page_array ) ) {
						
						// Global Localized Data Array
						// passed into our various JS files
						$localized_array = array( 'screen_base' => get_current_screen()->base, 'admin_ajax_url' => admin_url("admin-ajax.php") , 'preloader_url' => admin_url("/images/wpspin_light.gif") );
					
						// Enqueue the entire jquery ui library
						// enqueuing WordPress jquery ui causes too many issues
						wp_enqueue_script( 'jquery-ui-cdn' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js' , array( 'jquery' ) );
						wp_enqueue_script( 'thickbox' );
						
						wp_enqueue_script( 'jquery-form' , YT4WP_URL . 'js/jquery.form.min.js' ,	array( 'jquery' ) );
						
						// enqueue masonry on the dashboard for layouts
						wp_enqueue_script( 'masonry' , array('jquery') );
						
						// YouTube required file upload files
						wp_localize_script( 'js-file_upload', 'localized_data', $localized_array );
						wp_enqueue_script( 'js-file_upload', YT4WP_URL . 'js/upload_page/jquery_fileupload.min.js' , array( 'jquery' ) );
						
						wp_enqueue_script( 'js-iframe_transport', YT4WP_URL . 'js/upload_page/jquery-iframe-transport.min.js' , array( 'jquery' ) );
						wp_enqueue_script( 'js-knob' , YT4WP_URL . 'js/upload_page/jquery.knob.min.js' ,	array( 'jquery' ) );
						wp_enqueue_script( 'js-tagit' ,	 YT4WP_URL . 'js/upload_page/tagit.min.js' , array( 'jquery' ) );
						
									
						// register the file upload page script
						// wp_register_script( 'js-upload_page_script' , YT4WP_URL . 'js/upload_page/file_upload_script.js' , array( 'jquery' ) );
						wp_register_script( 'js-upload_page_script' , YT4WP_URL . 'js/upload_page/file_upload_script.min.js' , array( 'jquery' ) );
						
						// get the max file size and pass it into file_upload_script
						$chunk_max_size = wp_max_upload_size(); 
						$formatted_max_size = esc_html( size_format( wp_max_upload_size() ) );
						wp_localize_script( 'js-upload_page_script' , 'local_data' , array( 'chunk_max_size' => $chunk_max_size , 'current_page' => get_current_screen()->base , 'formatted_max_upload_size' => $formatted_max_size ) );
						wp_enqueue_script( 'js-upload_page_script');
												
						// register + localize the global Minified YouTube for WordPress Script File
						
							wp_register_script('youtube_plus.min.js',	YT4WP_URL.'js/youtube_plus.min.js', array('jquery'));
							wp_localize_script( 'youtube_plus.min.js', 'localized_data', $localized_array );	
							wp_enqueue_script( 'youtube_plus.min.js' );
						
						/*	un-minified version for development purposes
						wp_register_script( 'youtube_plus.js' , YT4WP_URL . 'js/youtube_plus.js' , array( 'jquery' ) );
						wp_localize_script( 'youtube_plus.js' , 'localized_data' , $localized_array );	
					
						wp_enqueue_script( 'youtube_plus.js' );
						*/
					
						// custom action hook for extension to hook into admin side script enqueueing
						do_action( 'yt4wp-enqueue-scripts-admin' , $hook );	
						
					}
					
				}
			
			/*
			*  yt_plus_plugin_activation_redirect()
			*  do our plugin activation hook, to redirect the user
			*  to the welcome page
			*
			*  @since 2.0
			*/
			// redirect the user to the settings page on initial activation
			function yt_plus_plugin_activation_redirect() {
					if (get_option('youtube_for_wordpress_do_activation_redirect', false)) {
						delete_option('youtube_for_wordpress_do_activation_redirect');
						// redirect to settings page
						wp_redirect(admin_url('/admin.php?page=youtube-for-wordpress-welcome'));
					}
				}
								
			/*
			*  addScripts_frontend()
			*  register and enqueue scripts on the front end
			*
			*  @since 2.0
			*/
			public function addScripts_frontend() {
					// Everything else
					// modal's and popups
					wp_enqueue_script( 'jquery-ui-core' );
					wp_register_script( 'fancybox-js' , '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js' , array('jquery') );
					wp_enqueue_script( 'fancybox-js' , array( 'jquery' ) );
					// for tiled gallery
					wp_enqueue_script( 'jquery-masonry', array( 'jquery' ) );
					
					// custom action hook for extension to hook into front end script enqueueing
					do_action( 'yt-plus-enqueue-scripts-frontend' );
				}
		/***** ADMINISTRATION MENUS
		 ****************************************************************************************************/
		 
			/*
			*  addAdministrationMenu() 
			*  register our YouTube for WordPress menu items 
			*
			*  @since 2.0
			*/
			public function addAdministrationMenu() {
					// Top Level Menu 
					add_menu_page( __('YouTube for WP','youtube-for-wordpress'), __('YouTube for WP','youtube-for-wordpress'), 'manage_options', 'youtube-for-wordpress', array(&$this, 'generateYouTubeDetails'), 'dashicons-video-alt3', 500);
					// Sub Items
					add_submenu_page('youtube-for-wordpress', __('YouTube for WP','youtube-for-wordpress'), __('YouTube for WP','youtube-for-wordpress'), 'manage_options', 'youtube-for-wordpress', array(&$this, 'generateYouTubeDetails'));	
					add_submenu_page('youtube-for-wordpress', __('Settings','youtube-for-wordpress'), __('Settings','youtube-for-wordpress'), 'manage_options', 'youtube-for-wordpress-settings', array(&$this, 'generatePageOptions'));
					add_submenu_page('youtube-for-wordpress', __('Add Ons','youtube-for-wordpress'), __('Add Ons','youtube-for-wordpress'), 'manage_options', 'youtube-for-wordpress-add-ons', array(&$this, 'generatePageAddOns'));
					add_submenu_page('youtube-for-wordpress', __('Support','youtube-for-wordpress'), __('Support','youtube-for-wordpress'), 'manage_options', 'youtube-for-wordpress-support', array(&$this, 'generatePageSupport'));
					// hidden pages, still directly accessable
					add_submenu_page( 'options.php', __('Welcome','youtube-for-wordpress'), __('Welcome','youtube-for-wordpress'), 'administrator', 'youtube-for-wordpress-welcome', array(&$this, 'generateWelcomePage'));
				}
			/***** ADMINISTRATION PAGES
			 ****************************************************************************************************/
			/*
			*  generatePageOptions()
			*  generate the YouTube for WordPress settings page
			*
			*  @since 2.0
			*/
			public function generatePageOptions() {
					require_once YT4WP_PATH.'pages/options.php'; // include our options page
				}
				
			/*
			*  generateYouTubeDetails()
			*  generate the main YouTube for WordPress page
			*
			*  @since 2.0
			*/		
			public function generateYouTubeDetails() {
					require_once YT4WP_PATH.'pages/yt4wp_main.php'; // include our options page
				}
						
			/*
			*  generatePageAddOns()
			*  generate the main YouTube for WordPress add-ons page
			*
			*  @since 2.0
			*/
			public function generatePageAddOns() {
					require_once YT4WP_PATH.'pages/addons.php'; // include our about page
				}
				
			/*
			*  generateWelcomePage()
			*  generate the YouTube for WordPress welcome page
			*  users are redirected too on plugin activation ( page is hidden from the menu )
			*
			*  @since 2.0
			*/
			public function generateWelcomePage() {
					require_once YT4WP_PATH.'templates/admin/welcome.php'; // include our about page
				}
				
			/*
			*  generatePageSupport()
			*  generate the YouTube for WordPress support page
			*
			*  @since 2.0.2
			*/
			public function generatePageSupport() {
					require_once YT4WP_PATH.'templates/admin/support.php'; // include our about page
				}
			
			/*
			*  registerYouTubePlusWidgets()
			*  register our various custom YouTube for WordPress widgets
			*
			*  @since 2.0
			*/		
			public function registerYouTubePlusWidgets() {	
					require_once YT4WP_PATH.'templates/yt4wp-rss-feed-widget.php'; // include our RSS feed widget
					require_once YT4WP_PATH.'templates/yt4wp-upload-widget.php'; // include our user upload widget
				}		
			/***** FORM DATA
			 ****************************************************************************************************/	 
			/*
			*  yt_plus_resetPluginSettings()
			*  reset the plugin settings ( ajax request sent from options.php )
			*
			*  @since 2.0
			*/		
			public function yt_plus_resetPluginSettings() {
					// reset the plugin settings back to defaults
					$this->logOutAndRevokeAccessToken($this->optionVal['yt4wp-oauth2-key']);
					$this->optionVal['yt4wp-oauth2-key']	= '';
					$this->optionVal['yt4wp-oauth2-secret']	= '';
					$this->optionVal['yt4wp-api-key']	= '';
					$this->optionVal['yt4wp-embed-player-style'] = 'wp-mediaelement';
					$this->optionVal['yt4wp-include-stat-count-in-query']	= 'stat-count-disabled';
					$this->optionVal['yt4wp-region'] = "US";
					$this->optionVal['yt4wp-language'] = "en";
					$this->optionVal['yt4wp-license-key'] = "";
					$this->optionVal['yt4wp-limit-error-log-count'] = '5';
					update_option('yt4wp_user_refresh_token' , '');
					// erase the contents of the error log
					$this->clear_yt4wp_error_log();
					return update_option(YT4WP_OPTION, $this->optionVal);	
				} 
						 
			/***** Adding Custom Add/Insert Media Button For YouTube for WordPress
			 ****************************************************************************************************/			 
			/*
			*  youtube_for_wordpress_insert_button()
			*  add a custom YouTube for WordPress button
			*  next to the 'Insert Media' button
			*
			*  @since 2.0
			*/	
			function youtube_for_wordpress_insert_button( $context ) {
				  //our popup's title
				  $title = 'YouTube for WordPress';
				  // button is disabled until the user authenticates properly
				  if ( get_option( 'yt4wp_user_refresh_token' ) == '' ) {
				  
					 $context .= "<a href='#' onclick='return false;' title='Error : Not Connected to YouTube' href='#' class='button youtube-plus' disabled='disabled'>
						<span class='dashicons dashicons-format-video' style='font-size:18px;line-height:1.5;padding-right:.25em;color:#888;'></span>YouTube for WP</a>";
						
				  } else {
				  
					 $context .= "<a href='#TB_inline?width=900&height=600&inlineId=youtube-plus-container' title='{$title}' href='#' class='button thickbox youtube-plus'>
						<span class='dashicons dashicons-format-video' style='font-size:18px;line-height:1.5;padding-right:.25em;color:#888;'></span>YouTube for WP</a>";
						
				  }
				  return $context;
				}
			/***** YouTube for WordPRess Pop Up for insert media 
			 ****************************************************************************************************/		 
			/*
			*  add_yt4wp_popup()
			*  add our custom popup modal
			*  function is hooked into admin_footer
			*
			*  @since 2.0
			*/			
			function add_yt4wp_popup() {
					// load the youtube_plus_pro_main popup
					// on all pages and posts pages
					// new and edit
					$page_base = get_current_screen()->base;
					if ( $page_base == 'post' ) {
					?>
						<script type="text/javascript">
							jQuery(document).ready(function() {
							
								/** Find Masonry and Initialize It **/
								function find_and_initialize_masonry() {
									var find_masonry = setInterval(function() {
										if ( jQuery( '#masonry-container' ).is( ':visible' ) ) {
											/* Masonry The Results */
											jQuery('#masonry-container').masonry({
												itemSelector: '.youtube-plus-video-single-list-item',
												columnWidth: '.youtube-plus-video-single-list-item',
												// options...
												isAnimated: true,
												animationOptions: {
													duration: 250,
													easing: 'linear',
													queue: false
												}
											});
											setTimeout(function() {
												clearInterval(find_masonry);
												console.log('timer cleared');
											}, 1000);
										}
										console.log('running');
									}, 800);
								}
							
							
								jQuery('.nav-tab').click(function() {
									var clicked_tab_no_hash = jQuery(this).attr('alt');
									var current_screen = "<?php echo $screen_base = get_current_screen()->base; ?>";
									if ( clicked_tab_no_hash != 'upload_content' ) {
										// remove uploader from dom
										jQuery("#yt4wp-upload-form").fileupload('destroy');
										
										jQuery.ajax({
											type: 'POST',
											url: ajaxurl,
												data: {
													action: 'yt_plus_settings_form',
													form_action: 'load_tab_content',
													clicked_tab_no_hash : clicked_tab_no_hash
												},
												dataType: 'html',
												success: function (response) {
													find_and_initialize_masonry();
													// print back Playlist Updated
													jQuery('#'+clicked_tab_no_hash).html(response);
													// remove the disabled-tab class
													jQuery( '.nav-tab' ).each(function() {
														jQuery(this).removeClass('disabled-tab');
													});
												},
												error : function(error_response) {
													console.log('Error Updating Playlist Order');
													console.log(error_response);
												}
										});
									} else {
										setTimeout(function() {
											jQuery( '.youTube_api_key_preloader' ).hide().promise().done(function() {		
												// re-initialize uploader
												<?php $this->initialize_yt4wp_uploader(); ?>
												// reset file
												jQuery("#yt4wp-upload-form").find('input[name="videolocation"]').empty();
												// reset form
												jQuery( '#video-details' ).val('');
												jQuery( '#video-description-div' ).hide();
												jQuery( '#video-title-div' ).hide();
												jQuery( '#video-category option:first-child' ).attr( 'selected' , 'selected' );
												jQuery( 'select[name="video-playlist-setting"] option:first-child' ).attr( 'selected' , 'selected' );
												jQuery( '.advanced-settings' ).hide();
												jQuery( '.tagsinput' ).find('.tag').each(function() {
													jQuery(this).remove();
												});
												jQuery( 'input[value="public"]' ).prop('checked',true);
												jQuery( 'input[name="video-tags"]' ).val('');
												jQuery( '.video-upload-advanced-settings-toggle' ).hide();
												jQuery( '#video-submission-button' ).hide();	
												jQuery( '#drop' ).show();
												// remove disabled
												jQuery( '.nav-tab' ).each(function() {
													jQuery(this).removeClass('disabled-tab');
												});
												// show the upload form
												jQuery( '#upload_content' ).show();
											});
										},1200);
									}
									
								});
							});
						</script>
						<div id="youtube-plus-container" style="display:none;">
							<?php
								include YT4WP_PATH . 'pages/yt4wp_main.php';
							?>
						</div>
					<?php
					}
				}
			
			/*
			*  initialize_yt4wp_uploader()
			*  re-initialize the file uploader on tab change/modal close
			*
			*  @since 2.0
			*/		
			function initialize_yt4wp_uploader() {
					?>
					// Initialize the jQuery File Upload plugin
						jQuery('#yt4wp-upload-form').fileupload({
									
							progressInterval: 10,
							
							maxChunkSize: '10MB' , // 10 MB
						
							// This element will accept file drag/drop uploading
							dropZone: jQuery('#drop'),

							// This function is called when a file is added to the queue;
							// either via the browse button, or via drag/drop:
							add: function (e, data) {
											
								if (!(/\.(mov|mpeg4|mp4|avi|wmv|mpegps|3gpp|webm|mts)$/i).test(data.files[0].name)) {
									// if an unacceptable file type was dropped in
									
										if ( jQuery('#acceptable_filetypes').is(':visible') ) {
											return;
										} else {	
											jQuery('#drop').after('<div id="acceptable_filetypes" style="display:none;"><h2 style="color:red;">Error - Please use an acceptable file type :</h2><ul><li>.MOV</li><li>.MPEG4</li><li>.MP4</li><li>.AVI</li><li>.WMV</li><li>.MPEGPS</li><li>.FLV</li><li>.3GPP</li><li>.WebM</li><li>.MTS</li></ul></div>');
											jQuery('#acceptable_filetypes').fadeIn();
										}	
									return;
									
								} else {
									// if an acceptable file type was used
									jQuery('#acceptable_filetypes').remove();
								}
											
											
								var file_name = data.files[0].name;
								var video_title = file_name.split('.')[0];
								
								jQuery('#drop').fadeOut('fast', function() {
									// create a submit button
									jQuery('#video-submission-button').html('<input type="submit" value="Upload" class="button-primary submit-video-button" onclick="return false;">');
									jQuery('#video-title').val(video_title);
									jQuery('#video-title-div').fadeIn();
									jQuery('#video-description-div').fadeIn();
									jQuery('.video-upload-advanced-settings-toggle').fadeIn();
									jQuery('#video-submission-button').fadeIn();
								});

								
								jQuery("#yt4wp-upload-form").undelegate(".submit-video-button","click").delegate(".submit-video-button","click", function () {
									data.submit();
									// initialize the knob plugin
									jQuery('#upload_form_container').find('#progress_container').fadeIn().find('input').knob({"readOnly":true,'inputColor':'#818181','font':'"Open Sans"'}).trigger(
										'configure',
										{
											'fgColor' : '#66CC66',
											'angleOffset' : '-125'
										}
									);
									return false;
								});
								
											
							},
							
							start: function(e, data) {
								// disable the input fields on form submission
								jQuery('#yt4wp-upload-form').find('input').attr('disabled','disabled');
								jQuery('#yt4wp-upload-form').find('textarea').attr('disabled','disabled');
								jQuery('.video-upload-advanced-settings-toggle').removeClass('video-upload-advanced-settings-toggle').addClass('disabled-advanced-settings-toggle').fadeTo('fast',.5);
								jQuery('#yt4wp-upload-form').fadeOut('fast');
							},

							fail:function(e, data){
								// Something has gone wrong!
								// data.context.addClass('error');
								console.log(data);
								alert('There was an error uploading content. Try again. If the error persists, please contact YouTube for WordPress Support.');
								jQuery('#yt4wp-upload-form').find('input').removeAttr('disabled');
								jQuery('#yt4wp-upload-form').find('textarea').removeAttr('disabled');
								jQuery('.disabled-advanced-settings-toggle').removeClass('disabled-advanced-settings-toggle').addClass('video-upload-advanced-settings-toggle').fadeTo('fast',1);
								jQuery('#yt4wp-upload-form').fadeIn('fast');
							},
							
							done:function(e, data){
								
								jQuery('#progress_container').hide();
								
								if ( current_screen != 'toplevel_page_youtube-for-wordpress' ) {
									jQuery('#video_success_response').html('<h3>Content Successfuly Uploaded</h3><p>Your new content should now be viewable from within the <a style="margin-top:-6px;" class="button-secondary nav-tab" onclick=jQuery("a[alt=browse_user_content]").click(); alt="browse_user_content" href="#">browse</a> tab, but may be unavailable until it completes processing.</p><br /><a href="#" class="upload_another_video button-secondary">Upload Another</a>').show();
								}
							}

						});
					<?php
				}
				
			/*
			*  loadTabDataAjax()
			*  load the selected tab, ajax function fired from within
			*  yt4wp_main.php
			*
			*  @since 2.0
			*/				
			function loadTabDataAjax($clicked_tab) {
					include YT4WP_PATH . 'inc/' . $clicked_tab . '.php';
				}
			/***** Shortcodes 
			 ***************************************************************************************************
			/*
			*  youtube_for_wordpress_responsive_video_shortcode()
			*  create custom shortcode for all single videos
			*  embeds videos using mediaelement.js, unless on mobile
			*
			*  @since 2.0
			*/			
			function youtube_for_wordpress_responsive_video_shortcode( $atts ) {
				   
				   // extract the shortcode options
				   extract( shortcode_atts( array (
						'video_id' => '',
						'poster' => '',
						'shadow' => '',
						'autoplay' => '',
						'loop' => '',
						'preload' => ''
					), $atts ) );
						$video_width = 900;
						$video_height = ( $video_width / 1.8 );
						// recalculate the video width
						// move this to it's own JS file 
						// and enqueue it on all shortcode useage
						?>
							<script type="text/javascript">
								// resize yt plus videos function
								// called below
								function resize_yt_plus_videos() {
									jQuery('.wp-video').each(function() {
										var video_width = jQuery(this).width();
										var new_height = ( video_width / 1.8 );
										jQuery(this).css( 'height' , new_height );
									});
								}
								
								jQuery(document).ready(function() {
									
									// load funciton to prevent wierdness
									resize_yt_plus_videos();
									
									// resize function
									jQuery(window).resize(function() {
										resize_yt_plus_videos();
									});
									
								});
							</script>
						<?php
					
					// extract the variables from the shortcode
					$auto_play = isset( $autoplay ) ? $autoplay : '0';
					$loop = isset( $loop ) ? $loop : '0';
					$post = isset( $poster ) ? $poster : '';
					$preload = isset( $preload ) ? $preload : 'metadata';
					
					// detect if we're on a mobile device (wp_video_shortcode breaks on mobile, so we'll use a standard iframe)
					if ( !wp_is_mobile() ) {
						
						if ( $this->optionVal['yt4wp-embed-player-style'] == 'wp-mediaelement' || is_admin() ) {
							// embed the player using mediaelement
							$attr = array(
								'src'      => esc_url( 'https://www.youtube.com/watch?v=' . $video_id ),
								'poster'	=> $poster,
								'width' => $video_width,
								'height' => $video_height,
								'autoplay' => $auto_play,
								'loop' => $loop,
								'preload' => $preload
							);
							// wrap our embedded video in a shadow container
							if ( $shadow == "1" ) {
								return '<div class="video-container-shadow">' . wp_video_shortcode( $attr ) . '</div><!--YouTube-for-WordPress-video-container-- http://www.yt4wp.com -->';
							} else {
								return wp_video_shortcode( $attr ) . '<!--YouTube-for-WordPress-video-container-- http://www.yt4wp.com -->';
							}
						} else {
							// embed the player with a standard youtube iframe
							return '<div class="ytplus-video-container"><iframe src="//www.youtube.com/embed/' . $video_id . '?autoplay='.$auto_play . '" allowfullscreen="" frameborder="0"></iframe></div>
							<!--YouTube-For-WordPress-Container--http://www.yt4wp.com -->';
						}		
					} else {
						/* Mobile Devices use standard IFRAME */
						return '<div class="ytplus-video-container mobile"><iframe src="//www.youtube.com/embed/' . $video_id . '?autoplay='.$auto_play . '" allowfullscreen="" frameborder="0"></iframe></div>
						<!--YouTube-for-WordPress-video-container--http://www.yt4wp.com -->';
					}
				}
			
			/*
			*  youtube_for_wordpress_playlist_shortcode()
			*  create custom shortcode for all playlist videos
			*  embeds playlists using standard iframe (mediaelement.js does not support playlists at the moment)
			*
			*  @since 2.0
			*/	
			function youtube_for_wordpress_playlist_shortcode( $atts ) {
					
					extract( shortcode_atts( array (
						'playlist_id' => ''
					), $atts ) );
					 
					return '<div class="ytplus-video-container"><iframe src="http://www.youtube.com/embed?listType=playlist&amp;list=' . $playlist_id . '" allowfullscreen="" frameborder="0"></iframe></div>
					<!--YouTube-for-WordPress-video-container-- http://www.yt4wp.com -->';
				}
				
			/*
			*  youtube_for_wordpress_grid_layout()
			*  create custom grid shortcode for displaying playlists, channels and searches
			*  on the front end
			*
			*  @since 2.0
			*/			
			function youtube_for_wordpress_grid_layout( $atts ) {
				
					extract( shortcode_atts( array (
						'playlist_id' => '',
						'channel_id' => '' ,
						'search_term' => '',
					), $atts ) );
					
					// if more than one parameter is set at a time,
					// lets return a friendly error
					if ( count( $atts ) > 1 ) {
						return 'Too many parameters set in the shortcode. You may only set one parameter at a time.';
					}
					
					if ( isset( $playlist_id ) && $playlist_id != '' ) { // playlist ID set
					
						$playlist_grid = include_once YT4WP_PATH.'templates/grid-layout-templates/grid-layout-playlist.php';
						return $playlist_grid;
						
					} else if ( isset( $channel_id ) && $channel_id != '' ) { // channel ID set
					
						$channel_grid = include_once YT4WP_PATH.'templates/grid-layout-templates/grid-layout-channel.php';
						return $channel_grid;
						
					} else if ( isset( $search_term ) && $search_term != '' ) { // search term set
					
						$search_grid = include_once YT4WP_PATH.'templates/grid-layout-templates/grid-layout-search.php';
						return $search_grid;
						
					} else {
						if ( isset( $playlist_id ) ) {
							$term = 'playlist ID';
						} else if ( isset( $channel_id ) ) {
							$term = 'channel ID';
						}  else if ( isset( $search_term ) ) {
							$term = 'search term';
						}
						return 'There was an error in your shortcode. Double check that you set a ' . $term.'.';
					}
					
				}
		/***** => END Shortcodes 
		 ****************************************************************************************************/
		 			
		 
		/*******************************************************************************/
		/**						Server Configuration Checks							**/ 
			/*
			*  yt4wp_is_user_localhost()
			*  
			*  check if the user is on a localhost installation
			*  if so, we need to return true to display our warning messages
			*
			*  @since 2.0
			*/		
			function yt4wp_is_user_localhost() {
					$whitelist = array( '127.0.0.1', '::1' );
						if( in_array( $_SERVER['REMOTE_ADDR'], $whitelist) )
							return true;
				}
		
	} // end class
	
} // end class check
?>