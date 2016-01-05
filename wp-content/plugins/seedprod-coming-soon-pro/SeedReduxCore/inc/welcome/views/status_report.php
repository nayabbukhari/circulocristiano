<?php
    /**
     * Admin View: Page - Status Report
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    global $wpdb;

    function seedredux_get_support_object() {
        $obj = array();

    }

    function seedredux_clean( $var ) {
        return sanitize_text_field( $var );
    }

    $sysinfo = SeedRedux_Helpers::compileSystemStatus( false, true );

?>
<div class="wrap about-wrap seedredux-status">
    <h1><?php _e( 'SeedRedux Framework - System Status', 'seedredux-framework' ); ?></h1>

    <div
        class="about-text"><?php _e( 'Our core mantra at SeedRedux is backwards compatibility. With hundreds of thousands of instances worldwide, you can be assured that we will take care of you and your clients.', 'seedredux-framework' ); ?></div>
    <div
        class="seedredux-badge"><i
            class="el el-seedredux"></i><span><?php printf( __( 'Version %s', 'seedredux-framework' ), SeedReduxFramework::$_version ); ?></span>
    </div>

    <?php $this->actions(); ?>
    <?php $this->tabs(); ?>

    <div class="updated seedredux-message">
        <p><?php _e( 'Please copy and paste this information in your ticket when contacting support:', 'seedredux-framework' ); ?> </p>

        <p class="submit"><a href="#"
                             class="button-primary debug-report"><?php _e( 'Get System Report', 'seedredux-framework' ); ?></a>
            <a class="skip button-primary"
               href="http://docs.seedreduxframework.com/core/support/understanding-the-seedredux-framework-system-status-report/"
               target="_blank"><?php _e( 'Understanding the Status Report', 'seedredux-framework' ); ?></a></p>

        <div id="debug-report">
            <textarea readonly="readonly"></textarea>

            <p class="submit">
                <button id="copy-for-support" class="button-primary seedredux-hint-qtip" href="#"
                        qtip-content="<?php _e( 'Copied!', 'seedredux-framework' ); ?>"><?php _e( 'Copy for Support', 'seedredux-framework' ); ?></button>
            </p>
        </div>
    </div>
    <br/>
    <table class="seedredux_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3"
                data-export-label="WordPress Environment"><?php _e( 'WordPress Environment', 'seedredux-framework' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Home URL"><?php _e( 'Home URL', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The URL of your site\'s homepage.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['home_url']; ?></td>
        </tr>
        <tr>
            <td data-export-label="Site URL"><?php _e( 'Site URL', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The root URL of your site.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['site_url']; ?></td>
        </tr>
        <tr>
            <td data-export-label="SeedRedux Version"><?php _e( 'SeedRedux Version', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The version of SeedRedux Framework installed on your site.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['seedredux_ver']; ?></td>
        </tr>
        <tr>
            <td data-export-label="SeedRedux Data Directory Writable"><?php _e( 'SeedRedux Data Directory Writable', 'seedredux-framework' ); ?>
                :
            </td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'SeedRedux and its extensions write data to the <code>uploads</code> directory. This directory must be writable.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php
                    if ( $sysinfo['seedredux_data_writeable'] == true ) {
                        echo '<mark class="yes">' . '&#10004; <code>' . $sysinfo['seedredux_data_dir'] . '</code></mark> ';
                    } else {
                        printf( '<mark class="error">' . '&#10005; ' . __( 'To allow data saving, make <code>%s</code> writable.', 'seedredux-framework' ) . '</mark>', $sysinfo['seedredux_data_dir'] );
                    }
                ?></td>
        </tr>
        <tr>
            <td data-export-label="WP Content URL"><?php _e( 'WP Content URL', 'seedredux-framework' ); ?>
                :
            </td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The location of Wordpress\'s content URL.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php
                echo '<code>' . $sysinfo['wp_content_url'] . '</code> ';
                ?></td>
        </tr>        
        <tr>
            <td data-export-label="WP Version"><?php _e( 'WP Version', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The version of WordPress installed on your site.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php bloginfo( 'version' ); ?></td>
        </tr>
        <tr>
            <td data-export-label="WP Multisite"><?php _e( 'WP Multisite', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Whether or not you have WordPress Multisite enabled.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php if ( $sysinfo['wp_multisite'] == true ) {
                    echo '&#10004;';
                } else {
                    echo '&ndash;';
                } ?></td>
        </tr>
        <tr>
            <td data-export-label="Permalink Structure"><?php _e( 'Permalink Structure', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The current permalink structure as defined in Wordpress Settings->Permalinks.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['permalink_structure']; ?></td>
        </tr>
        <?php $sof = $sysinfo['front_page_display']; ?>
        <tr>
            <td data-export-label="Front Page Display"><?php _e( 'Front Page Display', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The current Reading mode of Wordpress.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sof; ?></td>
        </tr>

        <?php
            if ( $sof == 'page' ) {
                ?>
                <tr>
                    <td data-export-label="Front Page"><?php _e( 'Front Page', 'seedredux-framework' ); ?>:</td>
                    <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The currently selected page which acts as the site\'s Front Page.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                    <td><?php echo $sysinfo['front_page']; ?></td>
                </tr>
                <tr>
                    <td data-export-label="Posts Page"><?php _e( 'Posts Page', 'seedredux-framework' ); ?>:</td>
                    <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The currently selected page in where blog posts are displayed.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                    <td><?php echo $sysinfo['posts_page']; ?></td>
                </tr>
            <?php
            }
        ?>
        <tr>
            <td data-export-label="WP Memory Limit"><?php _e( 'WP Memory Limit', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The maximum amount of memory (RAM) that your site can use at one time.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php
                    $memory = $sysinfo['wp_mem_limit']['raw'];

                    if ( $memory < 40000000 ) {
                        echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 40MB. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'seedredux-framework' ), $sysinfo['wp_mem_limit']['size'], 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
                    } else {
                        echo '<mark class="yes">' . $sysinfo['wp_mem_limit']['size'] . '</mark>';
                    }
                ?></td>
        </tr>
        <tr>
            <td data-export-label="Database Table Prefix"><?php _e( 'Database Table Prefix', 'seedredux-framework' ); ?>:
            </td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The prefix structure of the current Wordpress database.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['db_table_prefix']; ?></td>
        </tr>
        <tr>
            <td data-export-label="WP Debug Mode"><?php _e( 'WP Debug Mode', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Displays whether or not WordPress is in Debug Mode.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php if ( $sysinfo['wp_debug'] == true ) {
                    echo '<mark class="yes">' . '&#10004;' . '</mark>';
                } else {
                    echo '<mark class="no">' . '&ndash;' . '</mark>';
                } ?></td>
        </tr>
        <tr>
            <td data-export-label="Language"><?php _e( 'Language', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The current language used by WordPress. Default = English', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['wp_lang'] ?></td>
        </tr>
        </tbody>
    </table>
    <table class="seedredux_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3" data-export-label="Browser"><?php _e( 'Browser', 'seedredux-framework' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Browser Info"><?php _e( 'Browser Info', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Information about web browser current in use.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php
                    foreach ( $sysinfo['browser'] as $key => $value ) {
                        echo '<strong>' . ucfirst( $key ) . '</strong>: ' . $value . '<br/>';
                    }
                ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table class="seedredux_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3"
                data-export-label="Server Environment"><?php _e( 'Server Environment', 'seedredux-framework' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Server Info"><?php _e( 'Server Info', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Information about the web server that is currently hosting your site.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['server_info']; ?></td>
        </tr>
        <tr>
            <td data-export-label="Localhost Environment"><?php _e( 'Localhost Environment', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Is the server running in a localhost environment.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php
                if ( true == $sysinfo['localhost'] ) {
                    echo '<mark class="yes">' . '&#10004;' . '</mark>';
                } else {
                    echo '<mark class="no">' . '&ndash;' . '</mark>';
                }?>            
            </td>
        </tr>
        <tr>
            <td data-export-label="PHP Version"><?php _e( 'PHP Version', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The version of PHP installed on your hosting server.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['php_ver']; ?></td>
        </tr>
        <tr>
            <td data-export-label="ABSPATH"><?php _e( 'ABSPATH', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The ABSPATH variable on the server.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo '<code>' . $sysinfo['abspath'] . '</code>'; ?></td>
        </tr>
        
        <?php if ( function_exists( 'ini_get' ) ) { ?>
            <tr>
                <td data-export-label="PHP Memory Limit"><?php _e( 'PHP Memory Limit', 'seedredux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The largest filesize that can be contained in one post.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                <td><?php echo $sysinfo['php_mem_limit']; ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Post Max Size"><?php _e( 'PHP Post Max Size', 'seedredux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The largest filesize that can be contained in one post.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                <td><?php echo $sysinfo['php_post_max_size']; ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Time Limit"><?php _e( 'PHP Time Limit', 'seedredux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                <td><?php echo $sysinfo['php_time_limit']; ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Max Input Vars"><?php _e( 'PHP Max Input Vars', 'seedredux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                <td><?php echo $sysinfo['php_max_input_var']; ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Display Errors"><?php _e( 'PHP Display Errors', 'seedredux-framework' ); ?>:</td>
                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Determines if PHP will display errors within the browser.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                <td><?php
                        if ( true == $sysinfo['php_display_errors'] ) {
                            echo '<mark class="yes">' . '&#10004;' . '</mark>';
                        } else {
                            echo '<mark class="no">' . '&ndash;' . '</mark>';
                        }
                    ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td data-export-label="SUHOSIN Installed"><?php _e( 'SUHOSIN Installed', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Suhosin is an advanced protection system for PHP installations. It was designed to protect your servers on the one hand against a number of well known problems in PHP applications and on the other hand against potential unknown vulnerabilities within these applications or the PHP core itself.  If enabled on your server, Suhosin may need to be configured to increase its data submission limits.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php if ( $sysinfo['suhosin_installed'] == true ) {
                    echo '<mark class="yes">' . '&#10004;' . '</mark>';
                } else {
                    echo '<mark class="no">' . '&ndash;' . '</mark>';
                } ?></td>
        </tr>

        <tr>
            <td data-export-label="MySQL Version"><?php _e( 'MySQL Version', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The version of MySQL installed on your hosting server.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['mysql_ver']; ?></td>
        </tr>
        <tr>
            <td data-export-label="Max Upload Size"><?php _e( 'Max Upload Size', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The largest filesize that can be uploaded to your WordPress installation.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['max_upload_size']; ?></td>
        </tr>
        <tr>
            <td data-export-label="Default Timezone is UTC"><?php _e( 'Default Timezone is UTC', 'seedredux-framework' ); ?>
                :
            </td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The default timezone for your server.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php
                    if ( $sysinfo['def_tz_is_utc'] == false ) {
                        echo '<mark class="error">' . '&#10005; ' . sprintf( __( 'Default timezone is %s - it should be UTC', 'seedredux-framework' ), date_default_timezone_get() ) . '</mark>';
                    } else {
                        echo '<mark class="yes">' . '&#10004;' . '</mark>';
                    } ?>
            </td>
        </tr>
        <?php
            $posting = array();

            // fsockopen/cURL
            $posting['fsockopen_curl']['name'] = 'fsockopen/cURL';
            $posting['fsockopen_curl']['help'] = '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Used when communicating with remote services with PHP.', 'seedredux-framework' ) . '">[?]</a>';

            if ( $sysinfo['fsockopen_curl'] == true ) {
                $posting['fsockopen_curl']['success'] = true;
            } else {
                $posting['fsockopen_curl']['success'] = false;
                $posting['fsockopen_curl']['note']    = __( 'Your server does not have fsockopen or cURL enabled - cURL is used to communicate with other servers. Please contact your hosting provider.', 'seedredux-framework' ) . '</mark>';
            }

            /*
            // SOAP
            $posting['soap_client']['name'] = 'SoapClient';
            $posting['soap_client']['help'] = '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Some webservices like shipping use SOAP to get information from remote servers, for example, live shipping quotes from FedEx require SOAP to be installed.', 'seedredux-framework' ) . '">[?]</a>';

            if ( $sysinfo['soap_client'] == true ) {
                $posting['soap_client']['success'] = true;
            } else {
                $posting['soap_client']['success'] = false;
                $posting['soap_client']['note']    = sprintf( __( 'Your server does not have the <a href="%s">SOAP Client</a> class enabled - some gateway plugins which use SOAP may not work as expected.', 'seedredux-framework' ), 'http://php.net/manual/en/class.soapclient.php' ) . '</mark>';
            }

            // DOMDocument
            $posting['dom_document']['name'] = 'DOMDocument';
            $posting['dom_document']['help'] = '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'HTML/Multipart emails use DOMDocument to generate inline CSS in templates.', 'seedredux-framework' ) . '">[?]</a>';

            if ( $sysinfo['dom_document'] == true ) {
                $posting['dom_document']['success'] = true;
            } else {
                $posting['dom_document']['success'] = false;
                $posting['dom_document']['note']    = sprintf( __( 'Your server does not have the <a href="%s">DOMDocument</a> class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'seedredux-framework' ), 'http://php.net/manual/en/class.domdocument.php' ) . '</mark>';
            }
            */

            //// GZIP
            //$posting['gzip']['name'] = 'GZip';
            //$posting['gzip']['help'] = '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'GZip (gzopen) is used to open the GEOIP database from MaxMind.', 'seedredux-framework' ) . '">[?]</a>';
            //
            //if ( $sysinfo['gzip'] == true ) {
            //    $posting['gzip']['success'] = true;
            //} else {
            //    $posting['gzip']['success'] = false;
            //    $posting['gzip']['note']    = sprintf( __( 'Your server does not support the <a href="%s">gzopen</a> function - this is required to use the GeoIP database from MaxMind. The API fallback will be used instead for geolocation.', 'seedredux-framework' ), 'http://php.net/manual/en/zlib.installation.php' ) . '</mark>';
            //}

            // WP Remote Post Check
            $posting['wp_remote_post']['name'] = __( 'Remote Post', 'seedredux-framework' );
            $posting['wp_remote_post']['help'] = '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Used to send data to remote servers.', 'seedredux-framework' ) . '">[?]</a>';

            if ( $sysinfo['wp_remote_post'] == true ) {
                $posting['wp_remote_post']['success'] = true;
            } else {
                $posting['wp_remote_post']['note'] = __( 'wp_remote_post() failed. Many advanced features may not function. Contact your hosting provider.', 'seedredux-framework' );

                if ( $sysinfo['wp_remote_post_error'] ) {
                    $posting['wp_remote_post']['note'] .= ' ' . sprintf( __( 'Error: %s', 'seedredux-framework' ), rexux_clean( $sysinfo['wp_remote_post_error'] ) );
                }

                $posting['wp_remote_post']['success'] = false;
            }

            // WP Remote Get Check
            $posting['wp_remote_get']['name'] = __( 'Remote Get', 'seedredux-framework' );
            $posting['wp_remote_get']['help'] = '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Used to grab information from remote servers for updates updates.', 'seedredux-framework' ) . '">[?]</a>';

            if ( $sysinfo['wp_remote_get'] == true ) {
                $posting['wp_remote_get']['success'] = true;
            } else {
                $posting['wp_remote_get']['note'] = __( 'wp_remote_get() failed. This is needed to get information from remote servers. Contact your hosting provider.', 'seedredux-framework' );
                if ( $sysinfo['wp_remote_get_error'] ) {
                    $posting['wp_remote_get']['note'] .= ' ' . sprintf( __( 'Error: %s', 'seedredux-framework' ), seedredux_clean( $sysinfo['wp_remote_get_error'] ) );
                }

                $posting['wp_remote_get']['success'] = false;
            }

            $posting = apply_filters( 'seedredux_debug_posting', $posting );

            foreach ( $posting as $post ) {
                $mark = ! empty( $post['success'] ) ? 'yes' : 'error';
                ?>
                <tr>
                    <td data-export-label="<?php echo esc_html( $post['name'] ); ?>"><?php echo esc_html( $post['name'] ); ?>
                        :
                    </td>
                    <td><?php echo isset( $post['help'] ) ? $post['help'] : ''; ?></td>
                    <td class="help">
                        <mark class="<?php echo $mark; ?>">
                            <?php echo ! empty( $post['success'] ) ? '&#10004' : '&#10005'; ?>
                            <?php echo ! empty( $post['note'] ) ? wp_kses_data( $post['note'] ) : ''; ?>
                        </mark>
                    </td>
                </tr>
            <?php
            }
        ?>
        </tbody>
    </table>
    <table class="seedredux_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3"
                data-export-label="Active Plugins (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)"><?php _e( 'Active Plugins', 'seedredux-framework' ); ?>
                (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
            foreach ( $sysinfo['plugins'] as $name => $plugin_data ) {
                $version_string = '';
                $network_string = '';

                if ( ! empty( $plugin_data['Name'] ) ) {
                    // link the plugin name to the plugin url if available
                    $plugin_name = esc_html( $plugin_data['Name'] );

                    if ( ! empty( $plugin_data['PluginURI'] ) ) {
                        $plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage', 'seedredux-framework' ) . '">' . $plugin_name . '</a>';
                    }
                    ?>
                    <tr>
                        <td><?php echo $plugin_name; ?></td>
                        <td class="help">&nbsp;</td>
                        <td><?php echo sprintf( _x( 'by %s', 'by author', 'seedredux-framework' ), $plugin_data['Author'] ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?></td>
                    </tr>
                <?php
                }
            }
        ?>
        </tbody>
    </table>
    <?php
        if ( ! empty( $sysinfo['seedredux_instances'] ) && is_array( $sysinfo['seedredux_instances'] ) ) {
            foreach ( $sysinfo['seedredux_instances'] as $inst => $data ) {
                $inst_name = ucwords( str_replace( array( '_', '-' ), ' ', $inst ) );
                $args      = $data['args'];
                ?>
                <table class="seedredux_status_table widefat" cellspacing="0" id="status">
                    <thead>
                    <tr>
                        <th colspan="3"
                            data-export-label="SeedRedux Instance: <?php echo $inst_name; ?>"><?php _e( 'SeedRedux Instance: ', 'seedredux-framework' );
                                echo $inst_name; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td data-export-label="opt_name">opt_name:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The opt_name argument for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo $args['opt_name']; ?></td>
                    </tr>
                    <?php
                        if ( isset( $args['global_variable'] ) && $args['global_variable'] != '' ) {
                            ?>
                            <tr>
                                <td data-export-label="global_variable">global_variable:</td>
                                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The global_variable argument for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                                <td><?php echo $args['global_variable']; ?></td>
                            </tr>
                        <?php
                        }
                    ?>
                    <tr>
                        <td data-export-label="dev_mode">dev_mode:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Indicates if developer mode is enabled for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo true == $args['dev_mode'] ? '<mark class="yes">' . '&#10004;' . '</mark>' : '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="ajax_save">ajax_save:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Indicates if ajax based saving is enabled for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo true == $args['ajax_save'] ? '<mark class="yes">' . '&#10004;' . '</mark>' : '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="page_slug">page_slug:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The page slug denotes the string used for the options panel page for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo $args['page_slug']; ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="page_permissions">page_permissions:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The page permissions variable sets the permission level required to access the options panel for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo $args['page_permissions']; ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="menu_type">menu_type:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'This variable set whether or not the menu is displayed as an admin menu item for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo $args['menu_type']; ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="page_parent">page_parent:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The page parent variable sets where the options menu will be placed on the WordPress admin sidebar for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo $args['page_parent']; ?></td>
                    </tr>

                    <tr>
                        <td data-export-label="compiler">compiler:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Indicates if the compiler flag is enabled for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo true == $args['compiler'] ? '<mark class="yes">' . '&#10004;' . '</mark>' : '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="output">output:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Indicates if output flag for globally shutting off all CSS output is enabled for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo true == $args['output'] ? '<mark class="yes">' . '&#10004;' . '</mark>' : '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="output_tag">output_tag:</td>
                        <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The output_tag variable sets whether or not dynamic CSS will be generated for the customizer and Google fonts for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                        <td><?php echo true == $args['output_tag'] ? '<mark class="yes">' . '&#10004;' . '</mark>' : '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
                    </tr>

                    <?php
                        if ( isset( $args['templates_path'] ) && $args['templates_path'] != '' ) {
                            ?>
                            <tr>
                                <td data-export-label="template_path">template_path:</td>
                                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The specified template path containing custom template files for this instance of SeedRedux.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                                <td><?php echo '<code>' . $args['templates_path'] . '</code>'; ?></td>
                            </tr>
                            <tr>
                                <td data-export-label="Templates">Templates:</td>
                                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'List of template files overriding the default SeedRedux template files.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                                <?php
                                    $found_files = $data['templates'];
                                    if ( $found_files ) {
                                        foreach ( $found_files as $plugin_name => $found_plugin_files ) {
                                            ?>
                                            <td><?php echo implode( ', <br/>', $found_plugin_files ); ?></td>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <td>&ndash;</td>
                                    <?php
                                    }
                                ?>
                            </tr>
                        <?php
                        }
                        $ext = $data['extensions'];
                        if ( ! empty( $ext ) && is_array( $ext ) ) {
                            ?>
                            <tr>
                                <td data-export-label="Extensions">Extensions</td>
                                <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Indicates the installed SeedRedux extensions and their version numbers.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                                <td>
                                    <?php
                                        foreach ( $ext as $name => $arr ) {
                                            $ver = SeedRedux::getFileVersion( $arr['path'] );
                                            ?>
                                            <?php

                                            echo '<a href="http://seedreduxframework.com/extensions/' . str_replace( array(
                                                    '_',
                                                ), '-', $name ) . '" target="blank">' . ucwords( str_replace( array(
                                                    '_',
                                                    '-'
                                                ), ' ', $name ) ) . '</a> - ' . $ver; ?><br/>
                                        <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            <?php
            }
        }
    ?>
    <table class="seedredux_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3" data-export-label="Theme"><?php _e( 'Theme', 'seedredux-framework' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Name"><?php _e( 'Name', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The name of the current active theme.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['theme']['name']; ?></td>
        </tr>
        <tr>
            <td data-export-label="Version"><?php _e( 'Version', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The installed version of the current active theme.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php
                    echo $sysinfo['theme']['version'];

                    if ( ! empty( $theme_version_data['version'] ) && version_compare( $theme_version_data['version'], $active_theme->Version, '!=' ) ) {
                        echo ' &ndash; <strong style="color:red;">' . $theme_version_data['version'] . ' ' . __( 'is available', 'seedredux-framework' ) . '</strong>';
                    }
                ?></td>
        </tr>
        <tr>
            <td data-export-label="Author URL"><?php _e( 'Author URL', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The theme developers URL.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php echo $sysinfo['theme']['author_uri']; ?></td>
        </tr>
        <tr>
            <td data-export-label="Child Theme"><?php _e( 'Child Theme', 'seedredux-framework' ); ?>:</td>
            <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'Displays whether or not the current theme is a child theme.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
            <td><?php
                    echo is_child_theme() ? '<mark class="yes">' . '&#10004;' . '</mark>' : '&#10005; <br /><em>' . sprintf( __( 'If you\'re modifying SeedRedux Framework or a parent theme you didn\'t build personally, we recommend using a child theme. See: <a href="%s" target="_blank">How to create a child theme</a>', 'seedredux-framework' ), 'http://codex.wordpress.org/Child_Themes' ) . '</em>';
                ?></td>
        </tr>
        <?php

            if ( is_child_theme() ) {
                ?>
                <tr>
                    <td data-export-label="Parent Theme Name"><?php _e( 'Parent Theme Name', 'seedredux-framework' ); ?>:
                    </td>
                    <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The name of the parent theme.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                    <td><?php echo $sysinfo['theme']['parent_name']; ?></td>
                </tr>
                <tr>
                    <td data-export-label="Parent Theme Version"><?php _e( 'Parent Theme Version', 'seedredux-framework' ); ?>
                        :
                    </td>
                    <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The installed version of the parent theme.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                    <td><?php echo $sysinfo['theme']['parent_version']; ?></td>
                </tr>
                <tr>
                    <td data-export-label="Parent Theme Author URL"><?php _e( 'Parent Theme Author URL', 'seedredux-framework' ); ?>
                        :
                    </td>
                    <td class="help"><?php echo '<a href="#" class="seedredux-hint-qtip" qtip-content="' . esc_attr__( 'The parent theme developers URL.', 'seedredux-framework' ) . '">[?]</a>'; ?></td>
                    <td><?php echo $sysinfo['theme']['parent_author_uri']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <script type="text/javascript">
        jQuery( 'a.seedredux-hint-qtip' ).click(
            function() {
                return false;
            }
        );

        jQuery( 'a.debug-report' ).click(
            function() {
                var report = '';

                jQuery( '#status thead, #status tbody' ).each(
                    function() {
                        if ( jQuery( this ).is( 'thead' ) ) {
                            var label = jQuery( this ).find( 'th:eq(0)' ).data( 'export-label' ) || jQuery( this ).text();
                            report = report + "\n### " + jQuery.trim( label ) + " ###\n\n";
                        } else {
                            jQuery( 'tr', jQuery( this ) ).each(
                                function() {
                                    var label = jQuery( this ).find( 'td:eq(0)' ).data( 'export-label' ) || jQuery( this ).find( 'td:eq(0)' ).text();
                                    var the_name = jQuery.trim( label ).replace( /(<([^>]+)>)/ig, '' ); // Remove HTML
                                    var the_value = jQuery.trim( jQuery( this ).find( 'td:eq(2)' ).text() );
                                    var value_array = the_value.split( ', ' );

                                    if ( value_array.length > 1 ) {
                                        // If value have a list of plugins ','
                                        // Split to add new line
                                        var output = '';
                                        var temp_line = '';
                                        jQuery.each(
                                            value_array, function( key, line ) {
                                                temp_line = temp_line + line + '\n';
                                            }
                                        );

                                        the_value = temp_line;
                                    }

                                    report = report + '' + the_name + ': ' + the_value + "\n";
                                }
                            );
                        }
                    }
                );

                try {
                    jQuery( "#debug-report" ).slideDown();
                    jQuery( "#debug-report textarea" ).val( report ).focus().select();
                    jQuery( this ).fadeOut();

                    return false;
                } catch ( e ) {
                    console.log( e );
                }

                return false;
            }
        );

        jQuery( document ).ready(
            function( $ ) {
                $( 'body' ).on(
                    'copy', '#copy-for-support', function( e ) {
                        e.clipboardData.clearData();
                        e.clipboardData.setData( 'text/plain', $( '#debug-report textarea' ).val() );
                        e.preventDefault();
                    }
                );
            }
        );
    </script>
</div>