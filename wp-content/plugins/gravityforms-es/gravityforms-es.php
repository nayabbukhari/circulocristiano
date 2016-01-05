<?php
/*
Plugin Name: Gravity Forms (Spanish)
Plugin URI: http://www.closemarketing.es/servicios/wordpress-plugins/gravity-forms-es/
Description: Extends the Gravity Forms plugin and add-ons with the Spanish language

Version: 1.8
Requires at least: 3.0

Author: Closemarketing
Author URI: http://www.closemarketing.es/

Text Domain: gravityforms
Domain Path: /languages/

License: GPL
*/

class GravityFormsESPlugin {
	/**
	 * The plugin file
	 *
	 * @var string
	 */
	private $file;

	////////////////////////////////////////////////////////////

	/**
	 * The current langauge
	 *
	 * @var string
	 */
	private $language;

	/**
	 * Flag for the dutch langauge, true if current langauge is dutch, false otherwise
	 *
	 * @var boolean
	 */
	private $is_spanish;

	////////////////////////////////////////////////////////////

	/**
	 * Construct and intialize
	 */
	public function __construct( $file ) {
		$this->file = $file;

		// Priority is set to 8, beceasu the Signature Add-On is using priority 9
		add_action( 'init', array( $this, 'init' ), 8 );

		add_filter( 'load_textdomain_mofile', array( $this, 'load_textdomain_mofile' ), 10, 2 );

		add_filter( 'gform_admin_pre_render',       array( $this, 'gform_admin_pre_render' ) );
		add_filter( 'gform_currencies',             array( $this, 'gform_currencies' ) );
		add_filter( 'gform_address_types',          array( $this, 'gform_address_types' ) );
		add_filter( 'gform_address_display_format', array( $this, 'gform_address_display_format' ) );

		add_action( 'wp_print_scripts', array( $this, 'wp_print_scripts' ) );

		/*
		 * @since Gravity Forms v1.6.12
		 *
		 * Gravity Forms don't execute the load_plugin_textdomain() in the 'init'
		 * action, therefor we have to make sure this plugin will load first
		 *
		 * @see http://stv.whtly.com/2011/09/03/forcing-a-wordpress-plugin-to-be-loaded-before-all-other-plugins/
		 */
		add_action( 'activated_plugin', array( $this, 'activated_plugin' ) );
	}

	////////////////////////////////////////////////////////////

	/**
	 * Activated plugin
	 */
	public function activated_plugin() {
		$path = str_replace( WP_PLUGIN_DIR . '/', '', $this->file );

		if ( $plugins = get_option( 'active_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );

				update_option( 'active_plugins', $plugins );
			}
		}

		if ( $plugins = get_site_option( 'active_sitewide_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );

				update_site_option( 'active_sitewide_plugins', $plugins );
			}
		}
	}

	////////////////////////////////////////////////////////////

	/**
	 * Initialize
	 */
	public function init() {
		$rel_path = dirname( plugin_basename( $this->file ) ) . '/languages/';

		// Determine language
		if ( $this->language == null ) {
			$this->language = get_option( 'WPLANG', WPLANG );
			$this->is_spanish = ( $this->language == 'es' || $this->language == 'es_ES' );
		}

		// The ICL_LANGUAGE_CODE constant is defined from an plugin, so this constant
		// is not always defined in the first 'load_textdomain_mofile' filter call
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$this->is_spanish = ( ICL_LANGUAGE_CODE == 'es' );
		}

		// Load plugin text domain - Gravity Forms (es)
		load_plugin_textdomain( 'gravityforms_es', false, $rel_path );

		// Load plugin text domain - Gravity Forms user registration Add-On
		load_plugin_textdomain( 'gravityformsuserregistration', false, $rel_path );
	}

	////////////////////////////////////////////////////////////

	/**
	 * Load text domain MO file
	 *
	 * @param string $moFile
	 * @param string $domain
	 */
	public function load_textdomain_mofile( $mo_file, $domain ) {
		// First do quick check if an Spanish .MO file is loaded
		if ( strpos( $mo_file, 'es_ES.mo' ) !== false ) {
			$domains = array(
				'gravityforms'                 => array(
					'languages/gravityforms-es_ES.mo'                 => 'gravityforms/es_ES.mo'
				),
				'gravityformscampaignmonitor'  => array(
					'languages/gravityformscampaignmonitor-es_ES.mo'  => 'gravityformscampaignmonitor/es_ES.mo'
				),
				'gravityformsmailchimp'        => array(
					'languages/gravityformsmailchimp-es_ES.mo'        => 'gravityformsmailchimp/es_ES.mo'
				),
				'gravityformspaypal'           => array(
					'languages/gravityformspaypal-es_ES.mo'           => 'gravityformspaypal/es_ES.mo'
				),
				'gravityformspolls'            => array(
					'languages/gravityformspolls-es_ES.mo'            => 'gravityformspolls/es_ES.mo'
				),
				'gravityformssignature'        => array(
					'languages/gravityformssignature-es_ES.mo'        => 'gravityformssignature/es_ES.mo'
				),
				'gravityformsuserregistration' => array(
					'languages/gravityformsuserregistration-es_ES.mo' => 'gravityformsuserregistration/es_ES.mo'
				),
				'gravityformsquiz' => array(
					'languages/gravityformsquiz-es_ES.mo' => 'gravityformsquiz/es_ES.mo'
				)
			);

			if ( isset( $domains[$domain] ) ) {
				$paths = $domains[$domain];

				foreach ( $paths as $path => $file ) {
					if ( substr( $mo_file, -strlen( $path ) ) == $path ) {
						$new_file = dirname( $this->file ) . '/languages/' . $file;

						if ( is_readable( $new_file ) ) {
							$mo_file = $new_file;
						}
					}
				}
			}
		}

		return $mo_file;
	}

	////////////////////////////////////////////////////////////

	/**
	 * Gravity Forms translate datepicker
	 */
	public function wp_print_scripts() {
		if ( $this->is_spanish ) {
			/**
			 * gforms_ui_datepicker » @since ?
			 * gforms_datepicker » @since Gravity Forms 1.7.5
			 */
			foreach ( array( 'gforms_ui_datepicker', 'gforms_datepicker' ) as $script_datepicker ) {
				if ( wp_script_is( $script_datepicker ) ) {
					// @see http://code.google.com/p/jquery-ui/source/browse/trunk/ui/i18n/jquery.ui.datepicker-nl.js
					// @see https://github.com/jquery/jquery-ui/blob/master/ui/i18n/jquery.ui.datepicker-nl.js
					$src = plugins_url( 'js/jquery.ui.datepicker-es.js', $this->file );

					wp_enqueue_script( 'gforms_ui_datepicker_es', $src, array( $script_datepicker ), false, true );
				}
			}
		}
	}

	////////////////////////////////////////////////////////////

	/**
	 * Gravity Forms admin pre render
	 */
	public function gform_admin_pre_render( $form ) {
		wp_register_script( 'gravityforms-es-forms', plugins_url( 'js/forms-es.js', $this->file ) );

		wp_localize_script( 'gravityforms-es-forms', 'gravityFormsNlL10n', array(
			'formTitle'           => __( 'Untitled Form', 'gravityforms_es' ) ,
			'formDescription'     => __( 'We would love to hear from you! Please fill out this form and we will get in touch with you shortly.', 'gravityforms_es' ) ,
			'confirmationMessage' => __( 'Thanks for contacting us! We will get in touch with you shortly.', 'gravityforms_es' ) ,
			'buttonText'          => __( 'Submit', 'gravityforms_es' )
		) );

		wp_print_scripts( array( 'gravityforms-es-forms' ) );

		return $form;
	}

	////////////////////////////////////////////////////////////

	/**
	 * Update currency
	 *
	 * @param array $currencies
	 */
	public function gform_currencies( $currencies ) {
		$currencies['EUR'] = array(
			'name'               => __( 'Euro', 'gravityforms_es' ),
			'symbol_left'        => '€',
			'symbol_right'       => '',
			'symbol_padding'     => ' ',
			'thousand_separator' => '.',
			'decimal_separator'  => ',',
			'decimals'           => 2
		);

		return $currencies;
	}

	////////////////////////////////////////////////////////////

	/**
	 * Address types
	 *
	 * @param array $address_types
	 */
	public function gform_address_types( $address_types ) {
		// @see http://www.gravityhelp.com/forums/topic/add-custom-field-to-address-field-set
		$address_types['dutch'] = array(
			'label'       => __( 'Dutch', 'gravityforms_es' ),
			'country'     => __( 'Netherlands', 'gravityforms_es' ),
			'zip_label'   => __( 'Postal Code', 'gravityforms_es' ),
			'state_label' => __( 'Province', 'gravityforms_es' ),
			'states'      => array_merge( array( '' ), self::get_spanish_provinces() )
		);

		return $address_types;
	}

	////////////////////////////////////////////////////////////

	/**
	 * Get list of Spanish provinces
	 *
	 * @return array
	 */
	public static function get_spanish_provinces() {
		return array(
			__( 'Albacete', 'gravityforms_es' ),
			__( 'Alicante', 'gravityforms_es' ),
			__( 'Almería', 'gravityforms_es' ),
			__( 'Asturias', 'gravityforms_es' ),
			__( 'Ávila', 'gravityforms_es' ),
			__( 'Badajoz', 'gravityforms_es' ),
			__( 'Barcelona', 'gravityforms_es' ),
			__( 'Burgos', 'gravityforms_es' ),
			__( 'Cáceres', 'gravityforms_es' ),
			__( 'Cádiz', 'gravityforms_es' ),
			__( 'Cantabria', 'gravityforms_es' ),
			__( 'Castellón', 'gravityforms_es' ),
			__( 'Ceuta', 'gravityforms_es' ),
			__( 'Ciudad Real', 'gravityforms_es' ),
			__( 'Córdoba', 'gravityforms_es' ),
			__( 'Coruña (La)', 'gravityforms_es' ),
			__( 'Cuenca', 'gravityforms_es' ),
			__( 'Girona', 'gravityforms_es' ),
			__( 'Granada', 'gravityforms_es' ),
			__( 'Guadalajara', 'gravityforms_es' ),
			__( 'Guipuzcoa', 'gravityforms_es' ),
			__( 'Huelva', 'gravityforms_es' ),
			__( 'Huesca', 'gravityforms_es' ),
			__( 'Islas Baleares', 'gravityforms_es' ),
			__( 'Jaén', 'gravityforms_es' ),
			__( 'León', 'gravityforms_es' ),
			__( 'Lleida', 'gravityforms_es' ),
			__( 'Lugo', 'gravityforms_es' ),
			__( 'Madrid', 'gravityforms_es' ),
			__( 'Málaga', 'gravityforms_es' ),
			__( 'Melilla', 'gravityforms_es' ),
			__( 'Murcia', 'gravityforms_es' ),
			__( 'Navarra', 'gravityforms_es' ),
			__( 'Orense', 'gravityforms_es' ),
			__( 'Palencia', 'gravityforms_es' ),
			__( 'Palmas (Las)', 'gravityforms_es' ),
			__( 'Pontevedra', 'gravityforms_es' ),
			__( 'provincia', 'gravityforms_es' ),
			__( 'Rioja (La)', 'gravityforms_es' ),
			__( 'Salamanca', 'gravityforms_es' ),
			__( 'Santa Cruz de Tenerife', 'gravityforms_es' ),
			__( 'Segovia', 'gravityforms_es' ),
			__( 'Sevilla', 'gravityforms_es' ),
			__( 'Soria', 'gravityforms_es' ),
			__( 'Tarragona', 'gravityforms_es' ),
			__( 'Teruel', 'gravityforms_es' ),
			__( 'Toledo', 'gravityforms_es' ),
			__( 'Valencia', 'gravityforms_es' ),
			__( 'Valladolid', 'gravityforms_es' ),
			__( 'Vizcaya', 'gravityforms_es' ),
			__( 'Zamora', 'gravityforms_es' ),
			__( 'Zaragoza', 'gravityforms_es' )
		);
	}

	////////////////////////////////////////////////////////////

	/**
	 * Address display format
	 *
	 * @see http://www.gravityhelp.com/documentation/page/Gform_address_display_format
	 * @param array $address_types
	 */
	public function gform_address_display_format( $format ) {
		if ( $this->is_spanish ) {
			return 'zip_before_city';
		}

		return $format;
	}
}

global $gravityforms_es_plugin;

$gravityforms_es_plugin = new GravityFormsESPlugin( __FILE__ );