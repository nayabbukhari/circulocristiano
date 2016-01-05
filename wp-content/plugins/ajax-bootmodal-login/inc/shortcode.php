<?php
function  alimir_bootModal_login_shortcode(){
	add_action( 'wp_footer', 'alimir_bootModal_form' );
	if (!is_user_logged_in()) :
	return '
		<button type="button" class="btn '.default_buttons().' '.btn_block().' '.default_sizes().'" data-toggle="modal" data-target="#alimir_bootmodal">'.login_button_text().'</button>
	';
	else:
	if (get_option( 'option_usermodal' ) != 1):
	return '
		<button type="button" class="btn '.default_buttons().' '.btn_block().' '.default_sizes().'" data-toggle="modal" data-target="#alimir_bootmodal">'. login_button_text() .'</button>
	';
	else:
	return '
		<button type="button" class="btn '.default_buttons().' '.btn_block().' '.default_sizes().' disabled" disabled="disabled">'. login_button_text() .'</button>
	';	
	endif;
	endif;
}
add_shortcode( 'Alimir_BootModal_Login', 'alimir_bootModal_login_shortcode' );
?>