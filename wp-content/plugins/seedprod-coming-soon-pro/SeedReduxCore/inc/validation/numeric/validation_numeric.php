<?php

    if ( ! class_exists( 'SeedRedux_Validation_numeric' ) ) {
        class SeedRedux_Validation_numeric {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since SeedReduxFramework 1.0.0
             */
            function __construct( $parent, $field, $value, $current ) {

                $this->parent       = $parent;
                $this->field        = $field;
                $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : __( 'You must provide a numerical value for this option.', 'seedredux-framework' );
                $this->value        = $value;
                $this->current      = $current;

                $this->validate();
            } //function

            /**
             * Field Render Function.
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since SeedReduxFramework 1.0.0
             */
            function validate() {

                if ( ! is_numeric( $this->value ) ) {
                    $this->value = ( isset( $this->current ) ) ? $this->current : '';
                    $this->error = $this->field;
                }
            } //function
        } //class
    }