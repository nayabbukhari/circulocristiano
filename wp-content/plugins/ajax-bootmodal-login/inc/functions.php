<?php 
function login_button_text(){
	if (!is_user_logged_in()):
	if (get_option('button_text') == null):
		return __('Login','alimir');
	else:
		return get_option('button_text');
	endif;
	else:
	if (get_option('button_text2') == null):
		return __('Profile','alimir');
	else:
		return get_option('button_text2');
	endif;	
	endif;	
}

function default_buttons(){
	if (get_option( 'default_buttons' )==0)
	return 'btn-primary';
	else if (get_option( 'default_buttons' )==1)
	return 'btn-info';
	else if (get_option( 'default_buttons' )==2)
	return 'btn-success';
	else if (get_option( 'default_buttons' )==3)
	return 'btn-warning';
	else if (get_option( 'default_buttons' )==4)
	return 'btn-danger';
	else if (get_option( 'default_buttons' )==5)
	return 'btn-inverse';
}

function btn_block(){
	if (get_option( 'remove_btn_block' )==1)
	return '';
	else if (get_option( 'remove_btn_block' )==0)
	return 'btn-block';
}

function default_sizes(){
	if (get_option( 'default_sizes' )==0)
	return 'btn-large';
	else if (get_option( 'default_sizes' )==1)
	return 'btn-small';
	else if (get_option( 'default_sizes' )==2)
	return 'btn-mini';
}

function select_captcha_font(){
	$font_dir = 'fonts';
	if (get_option( 'captcha_font' ) == 1 || get_option( 'captcha_font' ) == null)
	return $font_dir.'/Blackout.ttf';
	else if (get_option( 'captcha_font' )==2)
	return $font_dir.'/1942.ttf';
	else if (get_option( 'captcha_font' )==3)
	return $font_dir.'/Anagram.ttf';
	else if (get_option( 'captcha_font' )==4)
	return $font_dir.'/axis.otf';
	else if (get_option( 'captcha_font' )==5)
	return $font_dir.'/BPdotsSquareBold.otf';
	else if (get_option( 'captcha_font' )==6)
	return $font_dir.'/GoodDog.otf';
}

function select_modal_theme(){
	if (get_option( 'modal_theme' ) == 1 || get_option( 'modal_theme' ) == null)
	return null;
	else if (get_option( 'modal_theme' )==2)
	return wp_enqueue_style( 'uikit', plugins_url('assets/css/uikit.css', dirname(__FILE__)) );
}

function print_placeholder_or_label($string,$type){
	if ((get_option( 'lable_setting' ) == 0 || get_option( 'lable_setting' ) == null) && $type == 'lable'){
	echo $string;
	}
	else if (get_option( 'lable_setting' ) == 1 && $type == 'placeholder'){
	echo 'placeholder="' . $string . '"';
	}
	else
	echo '';
}

// Update User View
function alimir_bootModal_update_user_view() {
	if (is_user_logged_in() && is_single()) :
		
		global $post;
		$user_id = get_current_user_id();
		$posts = get_user_meta( $user_id, 'alimir_viewed_posts', true );
		if (!is_array($posts)) $posts = array();
		if (sizeof($posts)>4) array_shift($posts);
		if (!in_array($post->ID, $posts)) $posts[] = $post->ID;
		update_user_meta( $user_id, 'alimir_viewed_posts', $posts );
		
	endif;
}
add_action('wp_head', 'alimir_bootModal_update_user_view');

?>