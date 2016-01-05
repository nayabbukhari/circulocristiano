<?php
/**
 * Class for dealing with destinations.
 *
 * @since 2.5
 *
 * @package Snapshot
 * @subpackage Model
 */

if ( ! class_exists( 'Snapshot_Model_Destination' ) ) {
	class Snapshot_Model_Destination {

		public $name_slug;
		public $name_display;

		private static $destinations;


		public function __construct() {

			if ( method_exists( $this, 'on_creation' ) ) {
				$this->on_creation();
			}

			if ( empty( $this->name_display ) || empty ( $this->name_slug ) ) {
				wp_die( __( 'You must override all required vars in your Snapshot Destination class!', SNAPSHOT_I18N_DOMAIN ) );
			}

		}

		public function display_listing_table( $destinations, $edit_url, $delete_url ) {
			wp_die( __( "You must override the function 'display_listing_table' in your Snapshot Destination class!", SNAPSHOT_I18N_DOMAIN ) );
		}

		public function sendfile_to_remote( $destination_info, $filename ) {
			wp_die( __( "You must override the function 'sendfile_to_remote' in your Snapshot Destination class!", SNAPSHOT_I18N_DOMAIN ) );
		}

		public function display_details_form( $item = 0 ) {
			wp_die( __( "You must override the function 'display_details_form' in your Snapshot Destination class!", SNAPSHOT_I18N_DOMAIN ) );
		}

		public static function load_destinations() {

			$dir = WPMUDEVSnapshot::instance()->get_plugin_path() . 'lib/Snapshot/Model/Destination';

			if ( ! defined( 'WPMUDEV_SNAPSHOT_DESTINATIONS_EXCLUDE' ) ) {
				define( 'WPMUDEV_SNAPSHOT_DESTINATIONS_EXCLUDE', '' );
			}

			//search the dir for files
			$snapshot_destination_files = array();
			if ( ! is_dir( $dir ) ) {
				return;
			}

			if ( ! $dh = opendir( $dir ) ) {
				return;
			}

			while ( ( $plugin = readdir( $dh ) ) !== false ) {
				if ( $plugin[0] == '.' ) {
					continue;
				}
				if ( $plugin[0] == '_' ) {
					continue;
				}    // Ignore this starting with underscore

				$_destination_dir = $dir . '/' . $plugin;
				if ( is_dir( $_destination_dir ) ) {
					$_destination_dir_file = $_destination_dir . "/index.php";
					if ( is_file( $_destination_dir_file ) ) {
						$snapshot_destination_files[] = $_destination_dir_file;
					}
				}
			}
			closedir( $dh );

			//echo "snapshot_destination_files<pre>"; print_r($snapshot_destination_files); echo "</pre>";
			if ( ( $snapshot_destination_files ) && ( count( $snapshot_destination_files ) ) ) {
				sort( $snapshot_destination_files );

				foreach ( $snapshot_destination_files as $file ) {
					//echo "file=[". $file ."]<br />";
					include( $file );
				}
			}
			do_action( 'snapshot_destinations_loaded' );
		}

		public static function get_object_from_type($type) {
			$destinationClasses = WPMUDEVSnapshot::instance()->get_setting('destinationClasses');
			if (isset($destinationClasses[$type]))
				return $destinationClasses[$type];
		}


		public static function get_destinations() {
			if ( empty ( self::$destinations ) ) {
				self::load_destinations();
			}

			return self::$destinations;
		}

		public static function show_destination_item_count( $destination_key ) {
			if ( isset( WPMUDEVSnapshot::instance()->config_data['items'] ) ) {
				$destination_count = 0;
				foreach ( WPMUDEVSnapshot::instance()->config_data['items'] as $snapshot_item ) {
					if ( ( isset( $snapshot_item['destination'] ) ) && ( $snapshot_item['destination'] == $destination_key ) ) {
						$destination_count += 1;
					}
				}
				if ( $destination_count ) {
					?><a href="<?php echo WPMUDEVSnapshot::instance()->get_setting('SNAPSHOT_MENU_URL');
					?>snapshots_edit_panel&amp;destination=<?php echo $destination_key; ?>"><?php echo $destination_count ?></a><?php
				} else {
					echo "0";
				}
			} else {
				echo "0";
			}
		}

	}
}