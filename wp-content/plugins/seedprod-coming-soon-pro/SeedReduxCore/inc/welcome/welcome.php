<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }


    class SeedRedux_Welcome {

        /**
         * @var string The capability users should have to view the page
         */
        public $minimum_capability = 'manage_options';
        public $display_version = "";
        public $seedredux_loaded = false;

        /**
         * Get things started
         *
         * @since 1.4
         */
        public function __construct() {

            add_action( 'seedredux/loaded', array( $this, 'init' ) );

            add_action( 'wp_ajax_seedredux_support_hash', array( $this, 'support_hash' ) );

        }

        public function init() {

            if ( $this->seedredux_loaded ) {
                return;
            }
            $this->seedredux_loaded = true;
            add_action( 'admin_menu', array( $this, 'admin_menus' ) );

            if ( isset( $_GET['page'] ) ) {
                if ( substr( $_GET['page'], 0, 6 ) == "seedredux-" ) {
                    $version               = explode( '.', SeedReduxFramework::$_version );
                    $this->display_version = $version[0] . '.' . $version[1];
                    add_filter( 'admin_footer_text', array( $this, 'change_wp_footer' ) );
                    add_action( 'admin_head', array( $this, 'admin_head' ) );
                } else {
                    $this->check_version();
                }
            } else {
                $this->check_version();
            }
            update_option( 'seedredux_version_upgraded_from', SeedReduxFramework::$_version );
            set_transient( '_seedredux_activation_redirect', true, 30 );

        }


        public function check_version() {
            global $pagenow;

            if ( $pagenow == "admin-ajax.php" || ( $GLOBALS['pagenow'] == "customize" && isset( $_GET['theme'] ) && ! empty( $_GET['theme'] ) ) ) {
                return;
            }

            $saveVer = SeedRedux_Helpers::major_version( get_option( 'seedredux_version_upgraded_from' ) );
            $curVer  = SeedRedux_Helpers::major_version( SeedReduxFramework::$_version );
            $compare = false;

            if ( SeedRedux_Helpers::isLocalHost() ) {
                $compare = true;
            } else if ( class_exists( 'SeedReduxFrameworkPlugin' ) ) {
                $compare = true;
            } else {
                $seedredux = SeedReduxFrameworkInstances::get_all_instances();
                foreach ( $seedredux as $panel ) {
                    if ( $panel->args['dev_mode'] == 1 ) {
                        $compare = true;
                        break;
                    }
                }
            }

            if ( $compare ) {
                $redirect = false;
                if ( empty( $saveVer ) ) {
                    $redirect = true; // First time
                } else if ( version_compare( $curVer, $saveVer, '>' ) ) {
                    $redirect = true; // Previous version
                }
                if ( $redirect ) {
                    //wp_safe_redirect( admin_url( 'tools.php?page=seedredux-about' ) );
                    //exit();
                }
            }
        }

        public function change_wp_footer() {
            echo 'If you like <strong>SeedRedux</strong> please leave us a <a href="https://wordpress.org/support/view/plugin-reviews/seedredux-framework?filter=5#postform" target="_blank" class="seedredux-rating-link" data-rated="Thanks :)">★★★★★</a> rating. A huge thank you from SeedRedux in advance!';
        }

        public function support_hash() {

            if ( ! wp_verify_nonce( $_POST['nonce'], 'seedredux-support-hash' ) ) {
                die();
            }

            $data          = get_option( 'seedredux_support_hash' );
            $data          = wp_parse_args( $data, array( 'check' => '', 'identifier' => '' ) );
            $generate_hash = true;
            $system_info   = SeedRedux_Helpers::compileSystemStatus();
            $newHash       = md5( json_encode( $system_info ) );
            $return        = array();
            if ( $newHash == $data['check'] ) {
                unset( $generate_hash );
            }
            $post_data = array(
                'hash'          => md5( network_site_url() . '-' . $_SERVER['REMOTE_ADDR'] ),
                'site'          => esc_url( home_url( '/' ) ),
                'tracking'      => SeedRedux_Helpers::getTrackingObject(),
                'system_status' => $system_info,
            );
            //$post_data = json_encode( $post_data );
            $post_data = serialize( $post_data );

            if ( isset( $generate_hash ) && $generate_hash ) {
                $data['check']      = $newHash;
                $data['identifier'] = "";
                $response           = wp_remote_post( 'http://support.seedredux.io/v1/', array(
                        'method'      => 'POST',
                        'timeout'     => 65,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'compress'    => true,
                        'headers'     => array(),
                        'body'        => array(
                            'data'      => $post_data,
                            'serialize' => 1
                        )
                    )
                );

                if ( is_wp_error( $response ) ) {
                    echo json_encode( array(
                        'status'  => 'error',
                        'message' => $response->get_error_message()
                    ) );
                    die( 1 );
                } else {
                    $response_code = wp_remote_retrieve_response_code( $response );
                    if ( $response_code == 200 ) {
                        $response = wp_remote_retrieve_body( $response );
                        $return   = json_decode( $response, true );
                        if ( isset( $return['identifier'] ) ) {
                            $data['identifier'] = $return['identifier'];
                            update_option( 'seedredux_support_hash', $data );
                        }
                    } else {
                        $response = wp_remote_retrieve_body( $response );
                        echo json_encode( array(
                            'status'  => 'error',
                            'message' => $response
                        ) );
                    }
                }
            }

            if ( ! empty( $data['identifier'] ) ) {
                $return['status']     = "success";
                $return['identifier'] = $data['identifier'];
            } else {
                $return['status']  = "error";
                $return['message'] = __( "Support hash could not be generated. Please try again later.", 'seedredux-framework' );
            }

            echo json_encode( $return );

            die( 1 );
        }

        /**
         * Register the Dashboard Pages which are later hidden but these pages
         * are used to render the Welcome and Credits pages.
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function admin_menus() {

            // About Page
            add_management_page(
                __( 'Welcome to SeedRedux Framework', 'seedredux-framework' ), __( 'SeedRedux Framework', 'seedredux-framework' ), $this->minimum_capability, 'seedredux-about', array(
                    $this,
                    'about_screen'
                )
            );

            // Changelog Page
            add_management_page(
                __( 'SeedRedux Framework Changelog', 'seedredux-framework' ), __( 'SeedRedux Framework Changelog', 'seedredux-framework' ), $this->minimum_capability, 'seedredux-changelog', array(
                    $this,
                    'changelog_screen'
                )
            );

            // Support Page
            add_management_page(
                __( 'Get Support', 'seedredux-framework' ), __( 'Get Support', 'seedredux-framework' ), $this->minimum_capability, 'seedredux-support', array(
                    $this,
                    'get_support'
                )
            );

            // Support Page
            add_management_page(
                __( 'SeedRedux Extensions', 'seedredux-framework' ), __( 'SeedRedux Extensions', 'seedredux-framework' ), $this->minimum_capability, 'seedredux-extensions', array(
                    $this,
                    'seedredux_extensions'
                )
            );


            // Credits Page
            add_management_page(
                __( 'The people that develop SeedRedux Framework', 'seedredux-framework' ), __( 'The people that develop SeedRedux Framework', 'seedredux-framework' ), $this->minimum_capability, 'seedredux-credits', array(
                    $this,
                    'credits_screen'
                )
            );

            // Status Page
            add_management_page(
                __( 'SeedRedux Framework Status', 'seedredux-framework' ), __( 'SeedRedux Framework Status', 'seedredux-framework' ), $this->minimum_capability, 'seedredux-status', array(
                    $this,
                    'status_screen'
                )
            );

            //remove_submenu_page( 'tools.php', 'seedredux-about' );
            remove_submenu_page( 'tools.php', 'seedredux-status' );
            remove_submenu_page( 'tools.php', 'seedredux-changelog' );
            remove_submenu_page( 'tools.php', 'seedredux-getting-started' );
            remove_submenu_page( 'tools.php', 'seedredux-credits' );
            remove_submenu_page( 'tools.php', 'seedredux-support' );
            remove_submenu_page( 'tools.php', 'seedredux-extensions' );


        }

        /**
         * Hide Individual Dashboard Pages
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function admin_head() {

            // Badge for welcome page
            $badge_url = SeedReduxFramework::$_url . 'assets/images/seedredux-badge.png';
            ?>

            <script
                id="seedredux-qtip-js"
                src='<?php echo SeedReduxFramework::$_url ?>assets/js/vendor/qtip/jquery.qtip.js'>
            </script>

            <script
                id="seedredux-welcome-admin-js"
                src='<?php echo SeedReduxFramework::$_url ?>inc/welcome/js/seedredux-welcome-admin.js'>
            </script>

            <?php
            if ( isset ( $_GET['page'] ) && $_GET['page'] == "seedredux-support" ) :
                ?>
                <script
                    id="jquery-easing"
                    src='<?php echo SeedReduxFramework::$_url ?>inc/welcome/js/jquery.easing.min.js'>
                </script>
            <?php endif; ?>

            <script
                id="seedredux-zero-clipboard-js"
                src='<?php echo SeedReduxFramework::$_url ?>inc/welcome/js/zeroclipboard/jquery.zeroclipboard.min.js'>
            </script>

            <link rel='stylesheet' id='seedredux-qtip-css'
                  href='<?php echo SeedReduxFramework::$_url ?>assets/css/vendor/qtip/jquery.qtip.css'
                  type='text/css' media='all'/>

            <link rel='stylesheet' id='elusive-icons'
                  href='<?php echo SeedReduxFramework::$_url ?>assets/css/vendor/elusive-icons/elusive-icons.css'
                  type='text/css' media='all'/>

            <link rel='stylesheet' id='seedredux-welcome-css'
                  href='<?php echo SeedReduxFramework::$_url ?>inc/welcome/css/seedredux-welcome.css'
                  type='text/css' media='all'/>
            <style type="text/css">
                .seedredux-badge:before {
                <?php echo is_rtl() ? 'right' : 'left'; ?> : 0;
                }

                .about-wrap .seedredux-badge {
                <?php echo is_rtl() ? 'left' : 'right'; ?> : 0;
                }

                .about-wrap .feature-rest div {
                    padding- <?php echo is_rtl() ? 'left' : 'right'; ?>: 100px;
                }

                .about-wrap .feature-rest div.last-feature {
                    padding- <?php echo is_rtl() ? 'right' : 'left'; ?>: 100px;
                    padding- <?php echo is_rtl() ? 'left' : 'right'; ?>: 0;
                }

                .about-wrap .feature-rest div.icon:before {
                    margin: <?php echo is_rtl() ? '0 -100px 0 0' : '0 0 0 -100px'; ?>;
                }
            </style>
        <?php
        }

        /**
         * Navigation tabs
         *
         * @access public
         * @since  1.9
         * @return void
         */
        public function tabs() {
            $selected = isset ( $_GET['page'] ) ? $_GET['page'] : 'seedredux-about';
            $nonce    = wp_create_nonce( 'seedredux-support-hash' );
            ?>
            <input type="hidden" id="seedredux_support_nonce" value="<?php echo $nonce; ?>"/>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab <?php echo $selected == 'seedredux-about' ? 'nav-tab-active' : ''; ?>"
                   href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'seedredux-about' ), 'tools.php' ) ) ); ?>">
                    <?php _e( "What's New", 'seedredux-framework' ); ?>
                </a>
                <a class="nav-tab <?php echo $selected == 'seedredux-extensions' ? 'nav-tab-active' : ''; ?>"
                   href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'seedredux-extensions' ), 'tools.php' ) ) ); ?>">
                    <?php _e( 'Extensions', 'seedredux-framework' ); ?>
                </a>
                <a class="nav-tab <?php echo $selected == 'seedredux-changelog' ? 'nav-tab-active' : ''; ?>"
                   href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'seedredux-changelog' ), 'tools.php' ) ) ); ?>">
                    <?php _e( 'Changelog', 'seedredux-framework' ); ?>
                </a>
                <a class="nav-tab <?php echo $selected == 'seedredux-credits' ? 'nav-tab-active' : ''; ?>"
                   href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'seedredux-credits' ), 'tools.php' ) ) ); ?>">
                    <?php _e( 'Credits', 'seedredux-framework' ); ?>
                </a>
                <a class="nav-tab <?php echo $selected == 'seedredux-support' ? 'nav-tab-active' : ''; ?>"
                   href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'seedredux-support' ), 'tools.php' ) ) ); ?>">
                    <?php _e( 'Support', 'seedredux-framework' ); ?>
                </a>
                <a class="nav-tab <?php echo $selected == 'seedredux-status' ? 'nav-tab-active' : ''; ?>"
                   href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'seedredux-status' ), 'tools.php' ) ) ); ?>">
                    <?php _e( 'Status', 'seedredux-framework' ); ?>
                </a>
            </h2>
        <?php
        }

        /**
         * Render About Screen
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function about_screen() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            include_once( 'views/about.php' );

        }

        /**
         * Render Changelog Screen
         *
         * @access public
         * @since  2.0.3
         * @return void
         */
        public function changelog_screen() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            include_once( 'views/changelog.php' );

        }

        /**
         * Render Changelog Screen
         *
         * @access public
         * @since  2.0.3
         * @return void
         */
        public function seedredux_extensions() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            include_once( 'views/extensions.php' );

        }


        /**
         * Render Get Support Screen
         *
         * @access public
         * @since  1.9
         * @return void
         */
        public function get_support() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            include_once( 'views/support.php' );

        }

        /**
         * Render Credits Screen
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function credits_screen() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            include_once( 'views/credits.php' );

        }

        /**
         * Render Status Report Screen
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function status_screen() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            include_once( 'views/status_report.php' );

        }

        /**
         * Parse the SeedRedux readme.txt file
         *
         * @since 2.0.3
         * @return string $readme HTML formatted readme file
         */
        public function parse_readme() {
            if ( file_exists( SeedReduxFramework::$_dir . 'inc/fields/raw/parsedown.php' ) ) {
                require_once SeedReduxFramework::$_dir . 'inc/fields/raw/parsedown.php';
                $Parsedown = new Parsedown();

                return $Parsedown->text( trim( str_replace( '# SeedRedux Framework Changelog', '', wp_remote_retrieve_body( wp_remote_get( SeedReduxFramework::$_url . '../CHANGELOG.md' ) ) ) ) );
            }

            return '<script src="http://gist-it.appspot.com/https://github.com/seedreduxframework/seedredux-framework/blob/master/CHANGELOG.md?slice=2:0&footer=0">// <![CDATA[// ]]></script>';

        }

        public function actions() {
            ?>
            <p class="seedredux-actions">
                <a href="http://docs.seedreduxframework.com/" class="docs button button-primary">Docs</a>
                <a href="https://wordpress.org/plugins/seedredux-framework/" class="review-us button button-primary"
                   target="_blank">Review Us</a>
                <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MMFMHWUPKHKPW"
                   class="review-us button button-primary" target="_blank">Donate</a>
                <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://seedreduxframework.com"
                   data-text="Reduce your dev time! SeedRedux is the most powerful option framework for WordPress on the web"
                   data-via="SeedReduxFramework" data-size="large" data-hashtags="SeedRedux">Tweet</a>
                <script>!function( d, s, id ) {
                        var js, fjs = d.getElementsByTagName( s )[0], p = /^http:/.test( d.location ) ? 'http' : 'https';
                        if ( !d.getElementById( id ) ) {
                            js = d.createElement( s );
                            js.id = id;
                            js.src = p + '://platform.twitter.com/widgets.js';
                            fjs.parentNode.insertBefore( js, fjs );
                        }
                    }( document, 'script', 'twitter-wjs' );</script>
            </p>
        <?php
        }

        /**
         * Render Contributors List
         *
         * @since 1.4
         * @uses  SeedRedux_Welcome::get_contributors()
         * @return string $contributor_list HTML formatted list of all the contributors for SeedRedux
         */
        public function contributors() {
            $contributors = $this->get_contributors();

            if ( empty ( $contributors ) ) {
                return '';
            }

            $contributor_list = '<ul class="wp-people-group">';

            foreach ( $contributors as $contributor ) {
                $contributor_list .= '<li class="wp-person">';
                $contributor_list .= sprintf( '<a href="%s" title="%s" target="_blank">', esc_url( 'https://github.com/' . $contributor->login ), esc_html( sprintf( __( 'View %s', 'seedredux-framework' ), $contributor->login ) )
                );
                $contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
                $contributor_list .= '</a>';
                $contributor_list .= sprintf( '<a class="web" href="%s" target="_blank">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
                $contributor_list .= '</a>';
                $contributor_list .= '</li>';
            }

            $contributor_list .= '</ul>';

            return $contributor_list;
        }

        /**
         * Retreive list of contributors from GitHub.
         *
         * @access public
         * @since  1.4
         * @return array $contributors List of contributors
         */
        public function get_contributors() {
            $contributors = get_transient( 'seedredux_contributors' );

            if ( false !== $contributors ) {
                return $contributors;
            }

            $response = wp_remote_get( 'https://api.github.com/repos/SeedReduxFramework/seedredux-framework/contributors', array( 'sslverify' => false ) );

            if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
                return array();
            }

            $contributors = json_decode( wp_remote_retrieve_body( $response ) );

            if ( ! is_array( $contributors ) ) {
                return array();
            }

            set_transient( 'seedredux_contributors', $contributors, 3600 );

            return $contributors;
        }
    }

    new SeedRedux_Welcome();

