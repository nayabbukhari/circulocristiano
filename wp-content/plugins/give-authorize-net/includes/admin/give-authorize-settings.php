<?php
/**
 *  Give Authorize Settings
 *
 * @description:
 * @copyright  : http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      : 1.1
 * @created    : 11/16/2015
 */


// adds the settings to the Payment Gateways section
function give_add_authorize_settings( $settings ) {

	$give_settings = array(
		array(
			'name' => '<strong>' . __( 'Authorize.net Gateway', 'give-authorize' ) . '</strong>',
			'desc' => '<hr>',
			'type' => 'give_title',
			'id'   => 'give_title_authorize_net',
		),
		array(
			'id'   => 'give_api_login',
			'name' => __( 'Live API Login ID', 'give-authorize' ),
			'desc' => __( 'Please enter your authorize.net API login ID.', 'give-authorize' ),
			'type' => 'text'
		),
		array(
			'id'   => 'give_transaction_key',
			'name' => __( 'Live Transaction Key', 'give-authorize' ),
			'desc' => __( 'Please enter your authorize.net transaction key.', 'give-authorize' ),
			'type' => 'text'
		),
		array(
			'id'   => 'give_authorize_sandbox_api_login',
			'name' => __( 'Sandbox API Login ID', 'give-authorize' ),
			'desc' => __( 'Please enter your <em>sandbox</em> authorize.net API login ID for testing purposes.', 'give-authorize' ),
			'type' => 'text'
		),
		array(
			'id'   => 'give_authorize_sandbox_transaction_key',
			'name' => __( 'Sandbox Transaction Key', 'give-authorize' ),
			'desc' => __( 'Plase enter your <em>sandbox</em> authorize.net transaction key for testing purposes.', 'give-authorize' ),
			'type' => 'text'
		)
	);

	return array_merge( $settings, $give_settings );
}

add_filter( 'give_settings_gateways', 'give_add_authorize_settings' );
