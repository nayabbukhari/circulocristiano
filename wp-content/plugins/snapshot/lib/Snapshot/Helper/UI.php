<?php
/**
 * Snapshot utility class for creating common UI components.
 *
 * @since 2.5
 *
 * @package Snapshot
 * @subpackage Helper
 */

if ( ! class_exists( 'Snapshot_Helper_UI' ) ) {

	class Snapshot_Helper_UI {

		/**
		 * @param int $minute_value
		 */
		public static function form_show_minute_selector_options( $minute_value = 0 ) {
			$_minute = 0;

			while ( $_minute < 60 ) {
				?>
				<option value="<?php echo $_minute ?>" <?php
				if ( $_minute == $minute_value ) {
					echo ' selected="selected" ';
				} ?>><?php
				echo sprintf( "%02d", $_minute ) ?></option><?php
				$_minute += 1;
			}
		}

		/**
		 * @param int $hour_value
		 */
		public static function form_show_hour_selector_options( $hour_value = 0 ) {

			$_hour = 0;

			while ( $_hour < 24 ) {

				if ( $_hour == 0 ) {
					$_hour_label = __( "Midnight", SNAPSHOT_I18N_DOMAIN );
				} else if ( $_hour == 12 ) {
					$_hour_label = __( "Noon", SNAPSHOT_I18N_DOMAIN );
				} else if ( $_hour < 13 ) {
					$_hour_label = $_hour . __( "am", SNAPSHOT_I18N_DOMAIN );
				} else {
					$_hour_label = ( $_hour - 12 ) . __( "pm", SNAPSHOT_I18N_DOMAIN );
				}

				?>
				<option value="<?php echo $_hour ?>" <?php
				if ( $_hour == $hour_value ) {
					echo ' selected="selected" ';
				}
				?>><?php echo $_hour_label; ?></option><?php
				$_hour += 1;
			}
		}

		/**
		 * @param int $mday_value
		 */
		public static function form_show_mday_selector_options( $mday_value = 0 ) {

			$_dom = 1;

			while ( $_dom < 32 ) {
				?>
				<option value="<?php echo $_dom ?>" <?php
				if ( $_dom == $mday_value ) {
					echo ' selected="selected" ';
				}
				?>><?php echo $_dom ?></option><?php
				$_dom += 1;
			}
		}

		/**
		 * @param int $wday_value
		 */
		public static function form_show_wday_selector_options( $wday_value = 0 ) {

			$_dow = array(
				'0' => __( 'Sunday', SNAPSHOT_I18N_DOMAIN ),
				'1' => __( 'Monday', SNAPSHOT_I18N_DOMAIN ),
				'2' => __( 'Tuesday', SNAPSHOT_I18N_DOMAIN ),
				'3' => __( 'Wednesday', SNAPSHOT_I18N_DOMAIN ),
				'4' => __( 'Thursday', SNAPSHOT_I18N_DOMAIN ),
				'5' => __( 'Friday', SNAPSHOT_I18N_DOMAIN ),
				'6' => __( 'Saturday', SNAPSHOT_I18N_DOMAIN ),
			);

			foreach ( $_dow as $_key => $_label ) {
				?>
				<option value="<?php echo $_key ?>"<?php
				if ( $_key == $wday_value ) {
					echo ' selected="selected" ';
				}
				?>><?php echo $_label ?></option><?php
			}
		}

		/**
		 * Utility function to display the AJAX information elements above the
		 * Add New and Restore forms.
		 *
		 * @since 1.0.2
		 */
		public static function form_ajax_panels() {
			?>
			<div id="snapshot-ajax-warning" class="updated fade" style="display:none"></div>
			<div id="snapshot-ajax-error" class="error snapshot-error" style="display:none"></div>
			<div id="snapshot-progress-bar-container" style="display: none" class="hide-if-no-js"></div>
		<?php
		}

		/**
		 * @param $all_destinations
		 * @param string $selected_destination
		 * @param string $destinationClasses
		 */
		public static function destination_select_options_groups( $all_destinations, $selected_destination = '', $destinationClasses = '' ) {
			if ( ( isset( $all_destinations ) ) && ( count( $all_destinations ) ) ) {

				$destinations = array();
				foreach ( $all_destinations as $key => $destination ) {
					$destination['key'] = $key;

					$type = $destination['type'];
					if ( ! isset( $destinations[ $type ] ) ) {
						$destinations[ $type ] = array();
					}

					$name = $destination['name'];

					$destinations[ $type ][ $name ] = $destination;
				}

				//echo "destinations<pre>"; print_r($destinations); echo "</pre>";
				//echo "destinationClasses<pre>"; print_r($destinationClasses); echo "</pre>";
				//die();
				foreach ( $destinations as $type => $destination_items ) {
					if ( ( $type == 'local' ) || ( isset( $destinationClasses[ $type ] ) ) ) {


						if ( $type == 'local' ) {
							$type_name = $type;
						} else {
							$destinationClass = $destinationClasses[ $type ];
							$type_name        = $destinationClass->name_display;
						}
						?>
						<optgroup label="<?php echo $type_name ?>"><?php
						foreach ( $destination_items as $key => $destination ) {
							?>
							<option class="<?php echo $type ?>" value="<?php echo $destination['key']; ?>" <?php
							if ( $selected_destination == $destination['key'] ) {
								echo ' selected="selected" ';
								global $snapshot_destination_selected_type;
								$snapshot_destination_selected_type = $type;
							}
							?>><?php echo stripslashes( $destination['name'] ); ?></option><?php
						}
						?></optgroup><?php
					}
				}
			}
		}

		/**
		 *
		 */
		public static function show_panel_messages() {

			$session_save_path = session_save_path();
			//echo "session_save_path=[". $session_save_path ."]<br />";
			if ( ! file_exists( $session_save_path ) ) {
				WPMUDEVSnapshot::instance()->snapshot_admin_notices_proc( "error", sprintf( __( "<p>The session save path (%s) is not set to a valid directory. Check your PHP (php.ini) settings or contact your hosting provider.</p>", SNAPSHOT_I18N_DOMAIN ), $session_save_path ) );

			} else if ( ! is_writable( $session_save_path ) ) {
				WPMUDEVSnapshot::instance()->snapshot_admin_notices_proc( "error", sprintf( __( "<p>The session_save_path (%s) is not writeable. Check your PHP (php.ini) settings or contact your hosting provider.</p>", SNAPSHOT_I18N_DOMAIN ), $session_save_path ) );
			}
		}


	}
}