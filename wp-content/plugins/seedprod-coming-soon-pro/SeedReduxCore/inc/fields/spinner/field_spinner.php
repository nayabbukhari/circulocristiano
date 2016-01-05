<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'SeedReduxFramework_spinner' ) ) {
    class SeedReduxFramework_spinner {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since SeedReduxFramework 3.0.0
         */
        function __construct( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;
        } //function

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since SeedReduxFramework 3.0.0
         */
        function render() {

            $params = array(
                'min'     => '',
                'max'     => '',
                'step'    => '',
                'default' => '',
            );

            $this->field = wp_parse_args( $this->field, $params );
            $data_string = "";
            foreach($this->field as $key => $val) {
                if (in_array($key, array('min', 'max', 'step', 'default'))) {
                    $data_string.= " data-".$key.'="'.$val.'"';
                }
            }
            $data_string .= ' data-val="'.$val.'"';


            // Don't allow input edit if there's a step
            $readonly = "";
            if ( isset( $this->field['edit'] ) && $this->field['edit'] == false ) {
                $readonly = ' readonly="readonly"';
            }


            echo '<div id="' . $this->field['id'] . '-spinner" class="seedredux_spinner" rel="' . $this->field['id'] . '">';
            echo '<input type="text" '.$data_string.' name="' . $this->field['name'] . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '" value="' . $this->value . '" class="mini spinner-input' . $this->field['class'] . '"' . $readonly . '/>';
            echo '</div>';
        } //function

        /**
         * Clean the field data to the fields defaults given the parameters.
         *
         * @since SeedRedux_Framework 3.1.1
         */
        function clean() {

            if ( empty( $this->field['min'] ) ) {
                $this->field['min'] = 0;
            } else {
                $this->field['min'] = intval( $this->field['min'] );
            }

            if ( empty( $this->field['max'] ) ) {
                $this->field['max'] = intval( $this->field['min'] ) + 1;
            } else {
                $this->field['max'] = intval( $this->field['max'] );
            }

            if ( empty( $this->field['step'] ) || $this->field['step'] > $this->field['max'] ) {
                $this->field['step'] = 1;
            } else {
                $this->field['step'] = intval( $this->field['step'] );
            }

            if ( empty( $this->value ) && ! empty( $this->field['default'] ) && intval( $this->field['min'] ) >= 1 ) {
                $this->value = intval( $this->field['default'] );
            }

            if ( empty( $this->value ) && intval( $this->field['min'] ) >= 1 ) {
                $this->value = intval( $this->field['min'] );
            }

            if ( empty( $this->value ) ) {
                $this->value = 0;
            }

            // Extra Validation
            if ( $this->value < $this->field['min'] ) {
                $this->value = intval( $this->field['min'] );
            } else if ( $this->value > $this->field['max'] ) {
                $this->value = intval( $this->field['max'] );
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since SeedReduxFramework 3.0.0
         */
        function enqueue() {

            wp_enqueue_script(
                'seedredux-field-spinner-custom-js',
                SeedReduxFramework::$_url . 'inc/fields/spinner/vendor/spinner_custom.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_script(
                'seedredux-field-spinner-js',
                SeedReduxFramework::$_url . 'inc/fields/spinner/field_spinner' . SeedRedux_Functions::isMin() . '.js',
                array(
                    'jquery',
                    'seedredux-field-spinner-custom-js',
                    'jquery-ui-core',
                    'jquery-ui-dialog',
                    'seedredux-js'
                ),
                time(),
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'seedredux-field-spinner-css',
                    SeedReduxFramework::$_url . 'inc/fields/spinner/field_spinner.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }
    }
}