<?php
// Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
class SeedReduxFramework_time_select {

    /**
     * Field Constructor.
     *
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @since SeedReduxFramework 1.0.0
     */
    function __construct($field = array(), $value = '', $parent) {

        //parent::__construct( $parent->sections, $parent->args );
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * Field Render Function.
     *
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @since SeedReduxFramework 1.0.0
     */
    function render() {

            echo '<select ' . $multi . ' id="' . $this->field['id']['hour'] . '-select" data-placeholder="' . $placeholder . '" name="' . $this->field['name']['hour'] . '' . $nameBrackets . $this->field['name_suffix'] . '" class="seedredux-select-item ' . $this->field['class'] . $sortable . '"' . $width . ' rows="2">';
            echo '<option></option>';




            foreach ($this->field['options'] as $k => $v) {
                if (is_array($this->value)) {
                    $selected = (is_array($this->value) && in_array($k, $this->value)) ? ' selected="selected"' : '';
                } else {
                    $selected = selected($this->value, $k, false);
                }
                echo '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';
            }//foreach
            echo '</select>';

    }



}

//class
