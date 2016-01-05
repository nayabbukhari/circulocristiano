<?php

class WPML_ST_Admin_Option extends WPML_WPDB_And_SP_User {

	/** @var  WPML_String_Translation $st_instance */
	private $st_instance;

	private $option_name;

	public function __construct( &$wpdb, &$sitepress, &$st_instance, $option_name ) {
		parent::__construct( $wpdb, $sitepress );
		$this->st_instance = &$st_instance;
		$this->option_name = $option_name;
	}

	public function pre_update_option_settings_filter( $old_value, $new_value ) {
		
		// This is special handling for Blog Title and Tagline.
		
		global $switched;

		if ( ! $switched || ( $switched && wpml_get_setting_filter( false, 'setup_complete' ) ) ) {
			$current_language = $this->st_instance->get_current_string_language( $this->option_name );

			WPML_Config::load_config_run();
			$result = $this->update_translation( $this->option_name, $current_language, $new_value, ICL_TM_COMPLETE );
			if ( $result ) {

				return $old_value;
			}
		}

		return $new_value;
	}

	/**
	 *
	 * @param string $option_name
	 * @param string $language
	 * @param string $new_value
	 * @param int|bool $status
	 * @param int $translator_id
	 * @param int $rec_level
	 *
	 * @return boolean|mixed
	 */
	public function update_translation( $option_name, $language, $new_value = null, $status = false, $translator_id = null, $rec_level = 0 ) {

		$new_value = (array) $new_value;
		$updated   = array();

		foreach ( $new_value as $index => $value ) {
			if ( is_array( $value ) ) {
				$name      = "[" . $option_name . "][" . $index . "]";
				$result    = $this->update_translation( $name, $language, $value, $status, $translator_id, $rec_level + 1 );
				$updated[] = array_sum( explode( ",", $result ) );
			} else {
				if ( is_string( $index ) ) {
					$name = ( $rec_level == 0 ? "[" . $option_name . "]" : $option_name ) . $index;
				} else {
					$name = $option_name;
				}
				$original_string_id = $this->wpdb->get_var( $this->wpdb->prepare( "	SELECT id
																					FROM {$this->wpdb->prefix}icl_strings
																					WHERE name = %s
																						AND language != %s",
					$name, $language ) );
				if ( $original_string_id ) {
					$updated[] = icl_add_string_translation( $original_string_id, $language, $value, $status, $translator_id );
				}
			}
		}

		return array_sum( $updated ) > 0 ? join( ",", $updated ) : false;
	}
}