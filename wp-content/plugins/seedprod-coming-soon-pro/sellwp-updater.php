<?php
// Make sure this file is in the root of your theme or plugin.

// Prevent loading this file directly and/or if the class is already defined
if ( ! defined( 'ABSPATH' ) ) {
    return;
}


/**
 *
 *
 * @version 2.0
 * @author John Turner <john@seedprod.com>
 * @link http://sellwp.co
 * @package SellWP_Updater
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @copyright Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 *
 * GNU General Public License, Free Software Foundation
 * <http://creativecommons.org/licenses/GPL/2.0/>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
if ( !class_exists( 'SellWP_UpdaterV2' ) ) {
    class SellWP_UpdaterV2 {
        /**
         * Unique theme or plugin id found in SellWP
         *
         * @var string
         */
        public $uuid;

        /**
         * The plugin current version
         *
         * @var string
         */
        public $current_version;

        /**
         * Plugin Slug (plugin_directory/plugin_file.php)
         *
         * @var string
         */
        public $plugin_slug;

        /**
         * Plugin Slug (plugin_directory/)
         *
         * @var string
         */
        public $slug;

        /**
         * The license key for the plugin
         *
         * @var string
         */
        public $lisense_key;

        /**
         * The domain the plugin is installed on
         *
         * @var string
         */
        public $domain;

        /**
         * The plugin update api url
         *
         * @var string
         */
        public $api_url;


        /**
         * Initialize a new instance of the WordPress Auto-Update class
         *
         * @param string  $current_version
         * @param string  $slug
         * @param string  $license_key
         * @param string  $domain
         * @param string  $api_url
         */
        function __construct( $license_key= null, $current_version = null, $uuslug = null, $uuid = null, $data = null ) {

            // Set the class public variables
            $this->current_version = $current_version;
            $this->uuid            = $uuid;
            $this->data            = $data;
            $this->license_key     = $license_key;
            $this->domain          = home_url();
            $this->api_url         = 'http://api.sellwp.co/v2/update';
            $this->plugin_slug     = $uuslug;
            $this->slug            = '';
            if ( !empty( $uuslug ) ) {
                list ( $t1, $t2 )        = explode( '/', $this->plugin_slug );
                $this->slug            = str_replace( '.php', '', $t2 );
            }

            // Define the alternative API for updating checking
            add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );

            // Define the alternative response for information checking
            add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );
        }

        /**
         * Inject the sellwp auto-update plugin to the filter transient
         *
         * @param unknown $transient
         * @return object $ transient
         */
        public function check_update( $transient ) {

            if ( empty( $transient->checked ) ) {
                return $transient;
            }

            // Get the remote version
            $remote_version = $this->getRemote_version();


            // If a newer version is available, add the update
            if(!empty($remote_version->new_version)){
                if ( version_compare( $this->current_version, $remote_version->new_version, '<' ) ) {
                    $obj                                     = new stdClass();
                    $obj->slug                               = $this->slug;
                    $obj->new_version                        = $remote_version->new_version;
                    $obj->package                            = $remote_version->download_link;
                    $obj->upgrade_notice                     = $remote_version->upgrade_notice;
                    $transient->response[$this->plugin_slug] = $obj;
                }
            }

            return $transient;
        }

        /**
         * Add our self-hosted description to the filter
         *
         * @param boolean $false
         * @param array   $action
         * @param object  $arg
         * @return bool|object
         */
        public function check_info( $false, $action, $arg ) {

        if ( !empty( $arg->slug ) ) {
        if ( !empty( $this->slug ) ) {
            if ( $arg->slug === $this->slug ) {
                $information = $this->getRemote_information();
                return $information;
            }
        }
        }

            return $false;
        }

        /**
         * Return the remote version
         *
         * @return bool|object
         */
        public function getRemote_version() {
            // Make the request
            //if ( !empty( $this->license_key ) ) {
                $request = wp_remote_post(
                    $this->api_url,
                    array(
                        'timeout'           => 15,
                        'body'              => array(
                            'action'            => 'version',
                            'uuid'              => $this->uuid,
                            'slug'              => $this->plugin_slug,
                            'license_key'       => $this->license_key,
                            'domain'            => $this->domain,
                            'installed_version' => $this->current_version,
                            'data'              => $this->data
                        ) ) );
                //var_dump($request);
                // Check for error and process
                if ( !is_wp_error( $request ) ) {
                    if ( wp_remote_retrieve_response_code( $request ) == 200 ) {

                        $response = json_decode( wp_remote_retrieve_body( $request ) );

                        if ( isset( $response->message ) )
                            update_option( basename( $this->slug ).'_update_msg', $response->message );
                        if ( isset( $response->code ) )
                            update_option( basename( $this->slug ).'_update_code', $response->code );

                        return $response;
                    }
                }

            return false;
        }

        /**
         * Get information about the remote version
         *
         * @return bool|object
         */
        public function getRemote_information( $license_key = null, $current_version = null, $uuslug = null, $uuid = null, $data  = null ) {

            if ( !empty( $this->license_key ) ) {
                $license_key = $this->license_key;
            }
            if ( !empty( $this->plugin_slug ) ) {
                $uuslug = $this->plugin_slug;
            }
            if ( !empty( $this->current_version ) ) {
                $current_version = $this->current_version;
            }

            if ( !empty( $this->data ) ) {
                $data = $this->data;
            }



                $request = wp_remote_post(
                    $this->api_url,
                    array( 'timeout'         => 15,
                        'body'              => array(
                            'action'            => 'info',
                            'uuid'              => $this->uuid,
                            'slug'              => $uuslug,
                            'license_key'       => $license_key,
                            'domain'            => $this->domain,
                            'installed_version' => $current_version,
                            'data'              => $data,
                        ) ) );

                // Check for error and process
                if ( !is_wp_error( $request ) ) {
                    if ( wp_remote_retrieve_response_code( $request ) == 200 ) {

                        $response = json_decode( wp_remote_retrieve_body( $request ) );

                        if ( isset( $response->message ) )
                            update_option( basename( $uuslug, '.php' ).'_update_msg', $response->message );
                        if ( isset( $response->code ) )
                            update_option( basename( $uuslug, '.php' ).'_update_code', $response->code );

                        if ( $response && isset( $response->sections ) )
                            $response->sections = (array) $response->sections;

                        return $response;
                    }
                }



                return false;

        }

    }
}
