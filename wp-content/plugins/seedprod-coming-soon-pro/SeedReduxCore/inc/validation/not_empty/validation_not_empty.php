<?php

    if ( ! class_exists( 'SeedRedux_Validation_not_empty' ) ) {
        class SeedRedux_Validation_not_empty {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since SeedReduxFramework 1.0.0
             */
            function __construct( $parent, $field, $value, $current ) {

                $this->parent       = $parent;
                $this->field        = $field;
                $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : __( 'This field cannot be empty. Please provide a value.', 'seedredux-framework' );
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

                if ( ! isset( $this->value ) || empty( $this->value ) ) {
                    $this->value = ( isset( $this->current ) ) ? $this->current : '';
                    $this->error = $this->field;
                }
            } //function
        } //class
    }