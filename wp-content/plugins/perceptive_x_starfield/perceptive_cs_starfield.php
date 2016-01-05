<?php

class Cornerstone_Starfield extends Cornerstone_Element_Base {

    public function data() {
        return array(
            'name'        => 'cornerstone-starfield',
            'title'       => __( 'Starfield', csl18n() ),
            'section'     => 'content',
            'description' => __( 'Adds a starfield background to your page.', csl18n() ),
            'supports'    => array( ),
        );
    }

    public function render( $atts ) {

        $shortcode = "[x-starfield-perceptive][/x-starfield-perceptive]";
        return $shortcode;

    }

}

?>