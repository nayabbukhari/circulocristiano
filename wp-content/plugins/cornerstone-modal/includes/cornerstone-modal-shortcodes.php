<?php 
/*========================================================================*
	Shortcode wrapper for horizontal scrolling element
 *========================================================================*/


function cornerstone_modal_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'id'    		=> '',
		'class' 		=> '',
		'style' 		=> '',
		'display_on' 	=> '',
		'btn_size' 		=> '',
		'btn_txt' 		=> '',
		'identifier'	=> '',
		'delay'			=> ''
	), $atts, 'cornerstone_modal' ) );

	$id     = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
	$class  = ( $class != '' ) ? 'class="cornerstone-modal remodal ' . esc_attr( $class ) . '"' : 'class="cornerstone-modal remodal"';
	$style  = ( $style != '' ) ? 'style="' . $style . '"' : '';

	$modal_id = rand( 0, 1000 );

	$output = "<div data-remodal-id='{$modal_id}' {$id} {$class} {$style}>";

		$output .= "<button data-remodal-action='close' class='remodal-close'></button>";

		$output .= do_shortcode( $content );

	$output .= "</div>";

	if($display_on == 'button') {

		if($btn_size != 'default') {
			$btn_class = 'x-btn ' . $btn_size;
		} else {
			$btn_class = 'x-btn';
		}

		$output .= '<a href="#' . $modal_id . '" class="' . $btn_class . '">' . $btn_txt . '</a>';

	} else {

		$output .= "<script>";
			$output .= "jQuery(document).ready(function($){";

				$output .= "var inst = $('[data-remodal-id={$modal_id}]').remodal();";

				if($display_on == 'element') {

					$output .= "$('{$identifier}').click(function() {";
						$output .= "inst.open();";
					$output .= "});";

				} else {

					$delay = $delay * 1000;

					$output .= "setTimeout(function() {";
						$output .= "inst.open();";
					$output .= "}, {$delay});";

				}

			$output .= "});";
		$output .= "</script>";

	}

	return $output;
}

add_shortcode( 'cornerstone_modal', 'cornerstone_modal_shortcode' );

