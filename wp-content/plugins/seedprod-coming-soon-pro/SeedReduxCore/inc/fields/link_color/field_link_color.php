<?php

/**
 * SeedRedux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * SeedRedux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SeedRedux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     SeedReduxFramework
 * @subpackage  Field_Color_Gradient
 * @author      Luciano "WebCaos" Ubertini
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'SeedReduxFramework_link_color' ) ) {

    /**
     * Main SeedReduxFramework_link_color class
     *
     * @since       1.0.0
     */
    class SeedReduxFramework_link_color {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;

            $defaults    = array(
                'regular' => true,
                'hover'   => true,
                'visited' => false,
                'active'  => true
            );
            $this->field = wp_parse_args( $this->field, $defaults );

            $defaults = array(
                'regular' => '',
                'hover'   => '',
                'visited' => '',
                'active'  => ''
            );

            $this->value = wp_parse_args( $this->value, $defaults );

            // In case user passes no default values.
            if ( isset( $this->field['default'] ) ) {
                $this->field['default'] = wp_parse_args( $this->field['default'], $defaults );
            } else {
                $this->field['default'] = $defaults;
            }
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

            if ( $this->field['regular'] === true && $this->field['default']['regular'] !== false ) {
                echo '<span class="linkColor"><strong>' . __( 'Regular', 'seedredux-framework' ) . '</strong>&nbsp;<input id="' . $this->field['id'] . '-regular" name="' . $this->field['name'] . $this->field['name_suffix'] . '[regular]' . '" value="' . $this->value['regular'] . '" class="seedredux-color seedredux-color-regular seedredux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['regular'] . '" /></span>';
            }

            if ( $this->field['hover'] === true && $this->field['default']['hover'] !== false ) {
                echo '<span class="linkColor"><strong>' . __( 'Hover', 'seedredux-framework' ) . '</strong>&nbsp;<input id="' . $this->field['id'] . '-hover" name="' . $this->field['name'] . $this->field['name_suffix'] . '[hover]' . '" value="' . $this->value['hover'] . '" class="seedredux-color seedredux-color-hover seedredux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['hover'] . '" /></span>';
            }

            if ( $this->field['visited'] === true && $this->field['default']['visited'] !== false ) {
                echo '<span class="linkColor"><strong>' . __( 'Visited', 'seedredux-framework' ) . '</strong>&nbsp;<input id="' . $this->field['id'] . '-hover" name="' . $this->field['name'] . $this->field['name_suffix'] . '[visited]' . '" value="' . $this->value['visited'] . '" class="seedredux-color seedredux-color-visited seedredux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['visited'] . '" /></span>';
            }

            if ( $this->field['active'] === true && $this->field['default']['active'] !== false ) {
                echo '<span class="linkColor"><strong>' . __( 'Active', 'seedredux-framework' ) . '</strong>&nbsp;<input id="' . $this->field['id'] . '-active" name="' . $this->field['name'] . $this->field['name_suffix'] . '[active]' . '" value="' . $this->value['active'] . '" class="seedredux-color seedredux-color-active seedredux-color-init ' . $this->field['class'] . '"  type="text" data-default-color="' . $this->field['default']['active'] . '" /></span>';
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {
            wp_enqueue_style( 'wp-color-picker' );
            
            wp_enqueue_script(
                'seedredux-field-link-color-js',
                SeedReduxFramework::$_url . 'inc/fields/link_color/field_link_color' . SeedRedux_Functions::isMin() . '.js',
                array( 'jquery', 'wp-color-picker', 'seedredux-js' ),
                time(),
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style( 'seedredux-color-picker-css' );

                wp_enqueue_style(
                    'seedredux-field-link_color-js',
                    SeedReduxFramework::$_url . 'inc/fields/link_color/field_link_color.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }

        public function output() {

            $style = array();

            if ( ! empty( $this->value['regular'] ) && $this->field['regular'] === true && $this->field['default']['regular'] !== false ) {
                $style[] = 'color:' . $this->value['regular'] . ';';
            }

            if ( ! empty( $this->value['visited'] ) && $this->field['visited'] === true && $this->field['default']['visited'] !== false ) {
                $style['visited'] = 'color:' . $this->value['visited'] . ';';
            }

            if ( ! empty( $this->value['hover'] ) && $this->field['hover'] === true && $this->field['default']['hover'] !== false ) {
                $style['hover'] = 'color:' . $this->value['hover'] . ';';
            }

            if ( ! empty( $this->value['active'] ) && $this->field['active'] === true && $this->field['default']['active'] !== false ) {
                $style['active'] = 'color:' . $this->value['active'] . ';';
            }

            if ( ! empty( $style ) ) {
                if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                    $styleString = "";

                    foreach ( $style as $key => $value ) {
                        if ( is_numeric( $key ) ) {
                            $styleString .= implode( ",", $this->field['output'] ) . "{" . $value . '}';
                        } else {
                            if ( count( $this->field['output'] ) == 1 ) {
                                $styleString .= $this->field['output'][0] . ":" . $key . "{" . $value . '}';
                            } else {
                                $blah = '';
                                foreach($this->field['output'] as $k => $sel) {
                                    $blah .= $sel . ':' . $key . ',';
                                }

                                $blah = substr($blah, 0, strlen($blah) - 1);
                                $styleString .= $blah . '{' . $value . '}';

                            }
                        }
                    }

                    $this->parent->outputCSS .= $styleString;
                }

                if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                    $styleString = "";

                    foreach ( $style as $key => $value ) {
                        if ( is_numeric( $key ) ) {
                            $styleString .= implode( ",", $this->field['compiler'] ) . "{" . $value . '}';

                        } else {
                            if ( count( $this->field['compiler'] ) == 1 ) {
                                $styleString .= $this->field['compiler'][0] . ":" . $key . "{" . $value . '}';
                            } else {
                                $blah = '';
                                foreach($this->field['compiler'] as $k => $sel) {
                                    $blah .= $sel . ':' . $key . ',';
                                }

                                $blah = substr($blah, 0, strlen($blah) - 1);
                                $styleString .= $blah . '{' . $value . '}';
                            }
                        }
                    }
                    $this->parent->compilerCSS .= $styleString;
                }
            }
        }
    }
}