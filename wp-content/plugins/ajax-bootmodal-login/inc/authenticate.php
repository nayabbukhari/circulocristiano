<?php
//ajax init

function alimir_bootModal_ajax_login_init(){
	if (!is_admin()) :
	wp_register_script(
		'ajax-login-script',
		plugins_url( '/assets/js/scripts.js' , dirname(__FILE__) ),
		array( 'jquery' )
	);
	wp_enqueue_script( 'ajax-login-script' );
	endif;

    wp_localize_script( 'ajax-login-script', 'ajax_login_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'loginRedirectURL' => get_option('alimir_login_redirect')=='' ? '' : get_option('alimir_login_redirect'),
        'registerRedirectURL' => get_option('alimir_register_redirect')=='' ? '' : get_option('alimir_register_redirect'),
        'captchaLink' => plugins_url( 'tmp/captcha/' , dirname(__FILE__) )
    ));

    add_action( 'wp_ajax_nopriv_ajaxregister', 'alimir_bootModal_ajax_registration' );
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'alimir_bootModal_ajax_login' );
    add_action( 'wp_ajax_nopriv_ajaxlostpass', 'alimir_bootModal_ajax_lostPassword' );
}
add_action('init', 'alimir_bootModal_ajax_login_init');

// Login Process

function alimir_bootModal_ajax_login(){

    check_ajax_referer( 'ajax-login-nonce', 'security' );

    $credentials = array();
    $credentials['user_login'] = $_POST['username'];
    $credentials['user_password'] = $_POST['password'];
	$login_captcha = '';
	if(isset($_POST['login_captcha']))
    $login_captcha = $_POST['login_captcha'];
	$rememberme = $_POST['rememberme'];
	
	if ($rememberme=="forever"):
    $credentials['remember'] = true;
	else:
	$credentials['remember'] = false;
	endif;
	
	if ( $credentials['user_login'] == null || $credentials['user_password'] == null || (get_option( 'enable_login_captcha' ) == 1 and $login_captcha == null) ){
		echo json_encode(array('loggedin'=>false, 'message'=>__('<p class="alert alert-info" data-alert="alert">Please fill all the fields.</p>','alimir')));
	}
	else if( get_option( 'enable_login_captcha' ) == 1 and !strCmp(strToUpper($_SESSION['login_captcha']),strToUpper($login_captcha)) == 0 ){
		echo json_encode(array('loggedin'=>false, 'message'=>__('<p class="alert alert-error" data-alert="alert">captcha invalid.</p>','alimir')));
	}
	else{
	if ($credentials['user_login'] != null && $credentials['user_password'] != null){
    $errors = wp_signon( $credentials, false );
	}
    if ( is_wp_error($errors) ){
		$display_errors = __('<p class="alert alert-error" data-alert="alert"><strong>ERROR</strong>: Wrong username or password.</p>','alimir');
        echo json_encode(array('loggedin'=>false, 'message'=>$display_errors));
	}
    else{
        echo json_encode(array('loggedin'=>true, 'message'=>__('<p class="alert alert-success" data-alert="alert">Login successful, redirecting...</p>','alimir')));
	}
	}
    die();
}

// Register Process

function alimir_bootModal_ajax_registration() {

	check_ajax_referer( 'ajax-form-nonce', 'security2' );

	$user_login = $_POST['user_login'];
	$user_email = $_POST['user_email'];
	$register_captcha ='';
	if(isset($_POST['register_captcha']))
	$register_captcha = $_POST['register_captcha'];
	
	if ( $user_login == null || $user_email == null|| (get_option( 'enable_register_captcha' ) == 1 and $register_captcha == null)){
		echo json_encode(array('registered'=>false, 'message'=>__('<p class="alert alert-info" data-alert="alert">Please fill all the fields.</p>','alimir')));
	}
	else if(get_option( 'enable_register_captcha' ) == 1 and !strCmp(strToUpper($_SESSION['register_captcha']),strToUpper($register_captcha)) == 0){
		echo json_encode(array('registered'=>false, 'message'=>__('<p class="alert alert-error" data-alert="alert">captcha invalid.</p>','alimir')));
	}
	else{
	$errors = register_new_user($user_login, $user_email);	
	if ( is_wp_error( $errors ) ) {

		$registration_error_messages = $errors->errors;
		$display_errors = '<div class="alert alert-error" data-alert="alert">';
		foreach($registration_error_messages as $error){
			$display_errors .= '<div>'.$error[0].'</div>';
		}
		$display_errors .= '</div>';
		echo json_encode( array(
			'registered' => false,
			'message'  => $display_errors,
		) );

	} else {
		echo json_encode( array(
			'registered' => true,
			'message'  => __( '<p class="alert alert-success" data-alert="alert">Registration complete. Please check your e-mail.</p>', 'alimir' ),
		) );
	}
	}
	die();

}

// Lost Password

function alimir_bootModal_ajax_lostPassword() {

	check_ajax_referer( 'ajax-form-nonce', 'security3' );

	$lost_pass = $_POST['lost_pass'];
	
	$lostpass_captcha = '';
	if(isset($_POST['lostpass_captcha']))
	$lostpass_captcha = $_POST['lostpass_captcha'];
	
	
	if ( $lost_pass == null || (get_option( 'enable_lostpass_captcha' ) == 1 and $lostpass_captcha == null )){
		echo json_encode(array('reset'=>false, 'message'=>__('<p class="alert alert-info" data-alert="alert">Please fill all the fields.</p>','alimir')));
	}
	else if(get_option( 'enable_lostpass_captcha' ) == 1 and !strCmp(strToUpper($_SESSION['lostpass_captcha']),strToUpper($lostpass_captcha)) == 0){
		echo json_encode(array('reset'=>false, 'message'=>__('<p class="alert alert-error" data-alert="alert">captcha invalid.</p>','alimir')));
	}
	else{
	if ( is_email( $lost_pass ) ) {
		$username = sanitize_email( $lost_pass );
	} else {
		$username = sanitize_user( $lost_pass );
	}

	$user_forgotten = alimir_bootModal_ajax_lostPassword_retrieve( $username );
	
	if ( is_wp_error( $user_forgotten ) ) {
	
		$lostpass_error_messages = $user_forgotten->errors;
		$display_errors = '<div class="alert alert-error" data-alert="alert">';
		foreach($lostpass_error_messages as $error){
			$display_errors .= '<div>'.$error[0].'</div>';
		}
		$display_errors .= '</div>';
		
		echo json_encode( array(
			'reset' 	 => false,
			'message' => $display_errors,
		) );
	} else {
		echo json_encode( array(
			'reset'   => true,
			'message' => __( '<p class="alert alert-success" data-alert="alert">Password Reset. Please check your email.</p>', 'alimir' ),
		) );
	}
	}
	
	die();
}

function alimir_bootModal_ajax_lostPassword_retrieve( $user_data ) {
	
	global $wpdb, $current_site, $wp_hasher;

	$errors = new WP_Error();

	if ( empty( $user_data ) ) {
		$errors->add( 'empty_username', __( 'Please enter a username or e-mail address.', 'alimir' ) );
	} else if ( strpos( $user_data, '@' ) ) {
		$user_data = get_user_by( 'email', trim( $user_data ) );
		if ( empty( $user_data ) )
			$errors->add( 'invalid_email', __( 'There is no user registered with that email address.', 'alimir'  ) );
	} else {
		$login = trim( $user_data );
		$user_data = get_user_by( 'login', $login );
	}

	if ( $errors->get_error_code() )
		return $errors;

	if ( ! $user_data ) {
		$errors->add( 'invalidcombo', __( 'Invalid username or e-mail.', 'alimir' ) );
		return $errors;
	}

	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	do_action( 'retrieve_password', $user_login );

	$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

	if ( ! $allow )
		return new WP_Error( 'no_password_reset', __( 'Password reset is not allowed for this user', 'alimir' ) );
	else if ( is_wp_error( $allow ) )
		return $allow;

	$key = wp_generate_password( 20, false );
	do_action( 'retrieve_password_key', $user_login, $key );
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . 'wp-includes/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );
	
	$message = __( 'Someone requested that the password be reset for the following account:', 'alimir' ) . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
	$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'alimir' ) . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:', 'alimir' ) . "\r\n\r\n";
	$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n\r\n";
	$message .= __( "powered by < http://wordpress.org/plugins/ajax-bootmodal-login >", 'alimir' ) . "\r\n";
	$message .= __( "Ali Mizraei < http://alimir.ir >", 'alimir' ) . "\r\n";
	
	if ( is_multisite() ) {
		$blogname = $GLOBALS['current_site']->site_name;
	} else {
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	$title   = sprintf( __( '[%s] Password Reset' ), $blogname );
	$title   = apply_filters( 'retrieve_password_title', $title );
	$message = apply_filters( 'retrieve_password_message', $message, $key );

	if ( $message && ! wp_mail( $user_email, $title, $message ) ) {
		$errors->add( 'noemail', __( 'The e-mail could not be sent.<br />Possible reason: your host may have disabled the mail() function.', 'alimir' ) );

		return $errors;

		wp_die();
	}

	return true;
}
?>