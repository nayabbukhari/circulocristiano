<?php

class Cornerstone_Splash extends Cornerstone_Element_Base {

    public function data() {
        return array(
            'name'        => 'cornerstone-splash',
            'title'       => __( 'Splash', csl18n() ),
            'section'     => 'content',
            'description' => __( 'Converts a section to a splash page.', csl18n() ),
            'supports'    => array( ),
        );
    }

    public function render( $atts ) {

        $shortcode = "[x-splash-perceptive][/x-splash-perceptive]";
        return $shortcode;

    }

}

?>