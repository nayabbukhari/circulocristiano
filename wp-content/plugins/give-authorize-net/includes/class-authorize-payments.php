<?php

/**
 *  Give_Authorize_Payments
 *
 * @description:
 * @copyright  : http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      : 1.1
 */
class Give_Authorize_Payments {

	/**
	 * Give_Authorize_Payments constructor.
	 */
	function __construct() {

		add_action( 'give_gateway_authorize', array( $this, 'give_process_authorize_net_payment' ), 10, 1 );

	}

	/**
	 * Authorize.net Payments
	 *
	 * @param $purchase_data
	 */
	public function give_process_authorize_net_payment( $purchase_data ) {

		if ( ! isset( $_POST['card_number'] ) || $_POST['card_number'] == '' ) {
			give_set_error( 'empty_card', __( 'You must enter a card number', 'give' ) );
		}
		if ( ! isset( $_POST['card_name'] ) || $_POST['card_name'] == '' ) {
			give_set_error( 'empty_card_name', __( 'You must enter the name on your card', 'give' ) );
		}
		if ( ! isset( $_POST['card_exp_month'] ) || $_POST['card_exp_month'] == '' ) {
			give_set_error( 'empty_month', __( 'You must enter an expiration month', 'give' ) );
		}
		if ( ! isset( $_POST['card_exp_year'] ) || $_POST['card_exp_year'] == '' ) {
			give_set_error( 'empty_year', __( 'You must enter an expiration year', 'give' ) );
		}
		if ( ! isset( $_POST['card_cvc'] ) || $_POST['card_cvc'] == '' || strlen( $_POST['card_cvc'] ) < 3 ) {
			give_set_error( 'empty_cvc', __( 'You must enter a valid CVC', 'give' ) );
		}

		$errors = give_get_errors();

		//No errors: Continue with payment processing
		if ( ! $errors ) {

			//Include Authorize SDK
			require_once( GIVE_AUTHORIZE_PLUGIN_DIR . '/includes/anet_php_sdk/AuthorizeNet.php' );
			if ( ! give_is_test_mode() ) {
				//LIVE:
				$authorize_api_login = give_get_option( 'give_api_login' );
				$authorize_trans_key = give_get_option( 'give_transaction_key' );
			} else {
				//SANDBOX
				$authorize_api_login = give_get_option( 'give_authorize_sandbox_api_login' );
				$authorize_trans_key = give_get_option( 'give_authorize_sandbox_transaction_key' );
			}
			//Check for credentials entered
			if ( empty( $authorize_api_login ) || empty( $authorize_trans_key ) ) {

				give_set_error( 'error_id_here', __( 'Error: Missing API Login or Transaction key. Please enter them in the plugin settings.', 'give-authorize' ) );

				return;

			}

			//Proceed with Authorize AIM
			$transaction              = new AuthorizeNetAIM($authorize_api_login, $authorize_trans_key );
			$transaction->VERIFY_PEER = false;

			//Sandbox or not?
			if ( give_is_test_mode() ) {
				$transaction->setSandbox( true );
			} else {
				$transaction->setSandbox( false );
			}

			$card_info  = $purchase_data['card_info'];
			$card_names = explode( ' ', $card_info['card_name'] );
			$first_name = isset( $card_names[0] ) ? $card_names[0] : $purchase_data['user_info']['first_name'];
			if ( ! empty( $card_names[1] ) ) {
				unset( $card_names[0] );
				$last_name = implode( ' ', $card_names );
			} else {

				$last_name = $purchase_data['user_info']['last_name'];

			}

			$transaction->amount    = $purchase_data['price'];
			$transaction->card_num  = strip_tags( trim( $card_info['card_number'] ) );
			$transaction->card_code = strip_tags( trim( $card_info['card_cvc'] ) );
			$transaction->exp_date  = strip_tags( trim( $card_info['card_exp_month'] ) ) . '/' . strip_tags( trim( $card_info['card_exp_year'] ) );

			$transaction->description = give_get_purchase_summary( $purchase_data );
			$transaction->first_name  = $first_name;
			$transaction->last_name   = $last_name;

			$transaction->address = $card_info['card_address'] . ' ' . $card_info['card_address_2'];
			$transaction->city    = $card_info['card_city'];
			$transaction->country = $card_info['card_country'];
			$transaction->state   = $card_info['card_state'];
			$transaction->zip     = $card_info['card_zip'];

			$transaction->customer_ip = give_get_ip();
			$transaction->email       = $purchase_data['user_email'];
			$transaction->invoice_num = $purchase_data['purchase_key'];

			try {

				$response = $transaction->authorizeAndCapture();

				if ( $response->approved ) {

					$payment_data = array(
						'price'           => $purchase_data['price'],
						'give_form_title' => $purchase_data['post_data']['give-form-title'],
						'give_form_id'    => intval( $purchase_data['post_data']['give-form-id'] ),
						'price_id'        => isset( $purchase_data['post_data']['give-price-id'] ) ? intval( $purchase_data['post_data']['give-price-id'] ) : '',
						'date'            => $purchase_data['date'],
						'user_email'      => $purchase_data['user_email'],
						'purchase_key'    => $purchase_data['purchase_key'],
						'currency'        => give_get_currency(),
						'user_info'       => $purchase_data['user_info'],
						'status'          => 'pending',
						'gateway'         => 'authorizenet'
					);


					$payment = give_insert_payment( $payment_data );
					if ( $payment ) {
						give_update_payment_status( $payment, 'publish' );
						give_send_to_success_page();
					} else {
						give_set_error( 'authorize_error', __( 'Error: your payment could not be recorded. Please try again', 'give' ) );
						give_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['give-gateway'] );
					}
				} else {

					if ( isset( $response->response_reason_text ) ) {
						$error = $response->response_reason_text;
					} elseif ( isset( $response->error_message ) ) {
						$error = $response->error_message;
					} else {
						$error = '';
					}

					if ( strpos( strtolower( $error ), 'the credit card number is invalid' ) !== false ) {
						give_set_error( 'invalid_card', __( 'Your card number is invalid', 'give' ) );
					} elseif ( strpos( strtolower( $error ), 'this transaction has been declined' ) !== false ) {
						give_set_error( 'invalid_card', __( 'Your card has been declined', 'give' ) );
					} elseif ( isset( $response->response_reason_text ) ) {
						give_set_error( 'api_error', $response->response_reason_text );
					} elseif ( isset( $response->error_message ) ) {
						give_set_error( 'api_error', $response->error_message );
					} else {
						give_set_error( 'api_error', sprintf( __( 'An error occurred. Error data: %s', 'give' ), print_r( $response, true ) ) );
					}

					give_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['give-gateway'] );
				}
			}
			catch ( AuthorizeNetException $e ) {
				give_set_error( 'request_error', $e->getMessage() );

				give_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['give-gateway'] );
			}

		} else {
			give_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['give-gateway'] );
		}
	}


}

