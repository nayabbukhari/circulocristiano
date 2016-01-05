<?php
function alimir_bootModal_enqueue_script() {

	//bootstrap check
	if (get_option( 'option_checkbox' ) != 1){
	//bootstrap.css file
	wp_enqueue_style( 'bootstrap', plugins_url('assets/css/bootstrap.min.css', dirname(__FILE__)) );
	//modal theme file
	select_modal_theme();
	//check rtl support
	if (is_rtl()):
	wp_enqueue_style( 'rtl-bootstrap', plugins_url('assets/css/rtl-bootstrap.min.css', dirname(__FILE__)) );
	endif;
	//bootstrap.js file
	wp_enqueue_script(
		'bootstrap',
		plugins_url( '/assets/js/bootstrap.min.js' , dirname(__FILE__) ),
		false,'',true
	);
	}//endif

	//jquery
	wp_enqueue_script('jquery');
	//bootmodal script
	wp_enqueue_script(
		'bootstrap-modal',
		plugins_url( '/assets/js/bootstrap-modal.js' , dirname(__FILE__) ),
		false,'',true
	);
}//end function
add_action( 'wp_enqueue_scripts', 'alimir_bootModal_enqueue_script' );

function alimir_bootModal_print_styles() {
	//Bootstrap 3 support
	if (get_option( 'option_bs3patch' ) == 1){
	wp_enqueue_style( 'bootstrap-bs3patch', plugins_url('assets/css/bootstrap-modal-bs3patch.css', dirname(__FILE__)));
	}
	//bootModal style
	wp_enqueue_style( 'bootstrap-modal', plugins_url('assets/css/bootstrap-modal.css', dirname(__FILE__)) );	
}
add_action( 'wp_print_styles', 'alimir_bootModal_print_styles' );
?>