<?php

    if ( ! class_exists( 'SeedRedux_Validation_html_custom' ) ) {
        class SeedRedux_Validation_html_custom {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since SeedReduxFramework 1.0.0
             */
            function __construct( $parent, $field, $value, $current ) {

                $this->parent  = $parent;
                $this->field   = $field;
                $this->value   = $value;
                $this->current = $current;

                $this->validate();
            } //function

            /**
             * Field Render Function.
             * Takes the vars and validates them
             *
             * @since SeedReduxFramework 1.0.0
             */
            function validate() {

                $this->value = wp_kses( $this->value, $this->field['allowed_html'] );
            } //function
        } //class
    }