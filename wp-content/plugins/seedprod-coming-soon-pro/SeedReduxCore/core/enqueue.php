<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'seedreduxCoreEnqueue' ) ) {
        class seedreduxCoreEnqueue {
            public $parent = null;

            private $min = '';
            private $timestamp = '';

            public function __construct( $parent ) {
                $this->parent = $parent;

                SeedRedux_Functions::$_parent = $parent;
            }

            public function init() {
                $this->min = SeedRedux_Functions::isMin();

                $this->timestamp = SeedReduxFramework::$_version;
                if ( $this->parent->args['dev_mode'] ) {
                    $this->timestamp .= '.' . time();
                }

                $this->register_styles();
                $this->register_scripts();

                add_thickbox();

                $this->enqueue_fields();

                $this->set_localized_data();

                /**
                 * action 'seedredux-enqueue-{opt_name}'
                 *
                 * @deprecated
                 *
                 * @param  object $this SeedReduxFramework
                 */
                do_action( "seedredux-enqueue-{$this->parent->args['opt_name']}", $this->parent ); // REMOVE

                /**
                 * action 'seedredux/page/{opt_name}/enqueue'
                 */
                do_action( "seedredux/page/{$this->parent->args['opt_name']}/enqueue" );
            }

            private function register_styles() {

                //*****************************************************************
                // SeedRedux Admin CSS
                //*****************************************************************
                wp_enqueue_style(
                    'seedredux-admin-css',
                    SeedReduxFramework::$_url . 'assets/css/seedredux-admin.css',
                    array(),
                    $this->timestamp,
                    'all'
                );

                //*****************************************************************
                // SeedRedux Fields CSS
                //*****************************************************************
                if ( ! $this->parent->args['dev_mode'] ) {
                    wp_enqueue_style(
                        'seedredux-fields-css',
                        SeedReduxFramework::$_url . 'assets/css/seedredux-fields.css',
                        array(),
                        $this->timestamp,
                        'all'
                    );
                }

                //*****************************************************************
                // Select2 CSS
                //*****************************************************************
                wp_register_style(
                    'select2-css',
                    SeedReduxFramework::$_url . 'assets/js/vendor/select2/select2.css',
                    array(),
                    $this->timestamp,
                    'all'
                );

                //*****************************************************************
                // Spectrum CSS
                //*****************************************************************
                wp_register_style(
                    'seedredux-spectrum-css',
                    SeedReduxFramework::$_url . 'assets/css/vendor/spectrum/seedredux-spectrum.css',
                    array(),
                    $this->timestamp,
                    'all'
                );

                //*****************************************************************
                // Elusive Icon CSS
                //*****************************************************************
                wp_enqueue_style(
                    'seedredux-elusive-icon',
                    SeedReduxFramework::$_url . 'assets/css/vendor/elusive-icons/elusive-icons.css',
                    array(),
                    $this->timestamp,
                    'all'
                );

                //*****************************************************************
                // QTip CSS
                //*****************************************************************
                wp_enqueue_style(
                    'qtip-css',
                    SeedReduxFramework::$_url . 'assets/css/vendor/qtip/jquery.qtip.css',
                    array(),
                    $this->timestamp,
                    'all'
                );

                //*****************************************************************
                // JQuery UI CSS
                //*****************************************************************
                wp_enqueue_style(
                    'jquery-ui-css',
                    apply_filters( "seedredux/page/{$this->parent->args['opt_name']}/enqueue/jquery-ui-css", SeedReduxFramework::$_url . 'assets/css/vendor/jquery-ui-bootstrap/jquery-ui-1.10.0.custom.css' ),
                    array(),
                    $this->timestamp,
                    'all'
                );

                //*****************************************************************
                // Iris CSS
                //*****************************************************************
                wp_enqueue_style( 'wp-color-picker' );

                if ( $this->parent->args['dev_mode'] ) {

                    //*****************************************************************
                    // Color Picker CSS
                    //*****************************************************************
                    wp_register_style(
                        'seedredux-color-picker-css',
                        SeedReduxFramework::$_url . 'assets/css/color-picker/color-picker.css',
                        array( 'wp-color-picker' ),
                        $this->timestamp,
                        'all'
                    );

                    //*****************************************************************
                    // Media CSS
                    //*****************************************************************
                    wp_enqueue_style(
                        'seedredux-field-media-css',
                        SeedReduxFramework::$_url . 'assets/css/media/media.css',
                        array(),
                        time(),
                        'all'
                    );
                }

                //*****************************************************************
                // RTL CSS
                //*****************************************************************
                if ( is_rtl() ) {
                    wp_enqueue_style(
                        'seedredux-rtl-css',
                        SeedReduxFramework::$_url . 'assets/css/rtl.css',
                        array( 'seedredux-admin-css' ),
                        $this->timestamp,
                        'all'
                    );
                }

            }

            private function register_scripts() {
                //*****************************************************************
                // JQuery / JQuery UI JS
                //*****************************************************************
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'jquery-ui-dialog' );

                //*****************************************************************
                // Select2 Sortable JS
                //*****************************************************************
                wp_register_script(
                    'seedredux-select2-sortable-js',
                    SeedReduxFramework::$_url . 'assets/js/vendor/seedredux.select2.sortable' . $this->min . '.js',
                    array( 'jquery' ),
                    $this->timestamp,
                    true
                );

                //*****************************************************************
                // Select2 JS
                //*****************************************************************
                wp_register_script(
                    'select2-js',
                    SeedReduxFramework::$_url . 'assets/js/vendor/select2/select2.js',
                    array( 'jquery', 'seedredux-select2-sortable-js' ),
                    $this->timestamp,
                    true
                );

                $depArray = array( 'jquery' );

                //*****************************************************************
                // Vendor JS
                //*****************************************************************
                if ( $this->parent->args['dev_mode'] ) {
                    wp_register_script(
                        'seedredux-vendor',
                        SeedReduxFramework::$_url . 'assets/js/vendor.min.js',
                        array( 'jquery' ),
                        $this->timestamp,
                        true
                    );

                    array_push( $depArray, 'seedredux-vendor' );
                }

                //*****************************************************************
                // SeedRedux JS
                //*****************************************************************
                wp_register_script(
                    'seedredux-js',
                    SeedReduxFramework::$_url . 'assets/js/seedredux' . $this->min . '.js',
                    $depArray,
                    $this->timestamp,
                    true
                );

                wp_enqueue_script(
                    'webfontloader',
                    'https://ajax.googleapis.com/ajax/libs/webfont/1.5.0/webfont.js',
                    array( 'jquery' ),
                    '1.5.0',
                    true
                );
            }

            private function enqueue_fields() {
                foreach ( $this->parent->sections as $section ) {
                    if ( isset( $section['fields'] ) ) {
                        foreach ( $section['fields'] as $field ) {
                            // TODO AFTER GROUP WORKS - Revert IF below
                            // if( isset( $field['type'] ) && $field['type'] != 'callback' ) {
                            if ( isset( $field['type'] ) && $field['type'] != 'callback' ) {

                                $field_class = 'SeedReduxFramework_' . $field['type'];

                                /**
                                 * Field class file
                                 * filter 'seedredux/{opt_name}/field/class/{field.type}
                                 *
                                 * @param       string        field class file path
                                 * @param array $field        field config data
                                 */
                                $class_file = apply_filters( "seedredux/{$this->parent->args['opt_name']}/field/class/{$field['type']}", SeedReduxFramework::$_dir . "inc/fields/{$field['type']}/field_{$field['type']}.php", $field );
                                if ( $class_file ) {
                                    if ( ! class_exists( $field_class ) ) {
                                        if ( file_exists( $class_file ) ) {
                                            require_once( $class_file );
                                        }
                                    }

                                    if ( ( method_exists( $field_class, 'enqueue' ) ) || method_exists( $field_class, 'localize' ) ) {

                                        if ( ! isset( $this->parent->options[ $field['id'] ] ) ) {
                                            $this->parent->options[ $field['id'] ] = "";
                                        }
                                        $theField = new $field_class( $field, $this->parent->options[ $field['id'] ], $this->parent );

                                        // Move dev_mode check to a new if/then block
                                        if ( ! wp_script_is( 'seedredux-field-' . $field['type'] . '-js', 'enqueued' ) && class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
                                            $theField->enqueue();
                                        }

                                        if ( method_exists( $field_class, 'localize' ) ) {
                                            $params = $theField->localize( $field );
                                            if ( ! isset( $this->parent->localize_data[ $field['type'] ] ) ) {
                                                $this->parent->localize_data[ $field['type'] ] = array();
                                            }
                                            $this->parent->localize_data[ $field['type'] ][ $field['id'] ] = $theField->localize( $field );
                                        }

                                        unset( $theField );
                                    }
                                }
                            }
                        }
                    }
                }
            }

            public function get_warnings_and_errors_array() {
                // Construct the errors array.
                if ( isset( $this->parent->transients['last_save_mode'] ) && ! empty( $this->parent->transients['notices']['errors'] ) ) {
                    $theTotal  = 0;
                    $theErrors = array();

                    foreach ( $this->parent->transients['notices']['errors'] as $error ) {
                        $theErrors[ $error['section_id'] ]['errors'][] = $error;

                        if ( ! isset( $theErrors[ $error['section_id'] ]['total'] ) ) {
                            $theErrors[ $error['section_id'] ]['total'] = 0;
                        }

                        $theErrors[ $error['section_id'] ]['total'] ++;
                        $theTotal ++;
                    }

                    $this->parent->localize_data['errors'] = array( 'total' => $theTotal, 'errors' => $theErrors );
                    unset( $this->parent->transients['notices']['errors'] );
                }

                // Construct the warnings array.
                if ( isset( $this->parent->transients['last_save_mode'] ) && ! empty( $this->parent->transients['notices']['warnings'] ) ) {
                    $theTotal    = 0;
                    $theWarnings = array();

                    foreach ( $this->parent->transients['notices']['warnings'] as $warning ) {
                        $theWarnings[ $warning['section_id'] ]['warnings'][] = $warning;

                        if ( ! isset( $theWarnings[ $warning['section_id'] ]['total'] ) ) {
                            $theWarnings[ $warning['section_id'] ]['total'] = 0;
                        }

                        $theWarnings[ $warning['section_id'] ]['total'] ++;
                        $theTotal ++;
                    }

                    unset( $this->parent->transients['notices']['warnings'] );
                    $this->parent->localize_data['warnings'] = array(
                        'total'    => $theTotal,
                        'warnings' => $theWarnings
                    );
                }

                if ( empty( $this->parent->transients['notices'] ) ) {
                    unset( $this->parent->transients['notices'] );
                }
            }

            private function set_localized_data() {
                $this->parent->localize_data['required']       = $this->parent->required;
                $this->parent->localize_data['fonts']          = $this->parent->fonts;
                $this->parent->localize_data['required_child'] = $this->parent->required_child;
                $this->parent->localize_data['fields']         = $this->parent->fields;

                if ( isset( $this->parent->font_groups['google'] ) ) {
                    $this->parent->localize_data['googlefonts'] = $this->parent->font_groups['google'];
                }

                if ( isset( $this->parent->font_groups['std'] ) ) {
                    $this->parent->localize_data['stdfonts'] = $this->parent->font_groups['std'];
                }

                if ( isset( $this->parent->font_groups['customfonts'] ) ) {
                    $this->parent->localize_data['customfonts'] = $this->parent->font_groups['customfonts'];
                }

                $this->parent->localize_data['folds'] = $this->parent->folds;

                // Make sure the children are all hidden properly.
                foreach ( $this->parent->fields as $key => $value ) {
                    if ( in_array( $key, $this->parent->fieldsHidden ) ) {
                        foreach ( $value as $k => $v ) {
                            if ( ! in_array( $k, $this->parent->fieldsHidden ) ) {
                                $this->parent->fieldsHidden[] = $k;
                                $this->parent->folds[ $k ]    = "hide";
                            }
                        }
                    }
                }

                if ( isset( $this->parent->args['dev_mode'] ) && $this->parent->args['dev_mode'] == true ) {
                    $nonce                               = wp_create_nonce( 'seedredux-ads-nonce' );
                    $base                                = admin_url( 'admin-ajax.php' ) . '?action=seedredux_p&nonce=' . $nonce . '&url=';
                    $url                                 = $base . urlencode( 'http://ads.seedreduxframework.com/api/index.php?js&g&1&v=2' ) . '&proxy=' . urlencode( $base ) . '';
                    $this->parent->localize_data['rAds'] = '<span data-id="1" class="mgv1_1"><script type="text/javascript">(function(){if (mysa_mgv1_1) return; var ma = document.createElement("script"); ma.type = "text/javascript"; ma.async = true; ma.src = "' . $url . '"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ma, s) })();var mysa_mgv1_1=true;</script></span>';
                }

                $this->parent->localize_data['fieldsHidden'] = $this->parent->fieldsHidden;
                $this->parent->localize_data['options']      = $this->parent->options;
                $this->parent->localize_data['defaults']     = $this->parent->options_defaults;

                /**
                 * Save pending string
                 * filter 'seedredux/{opt_name}/localize/save_pending
                 *
                 * @param       string        save_pending string
                 */
                $save_pending = apply_filters( "seedredux/{$this->parent->args['opt_name']}/localize/save_pending", __( 'You have changes that are not saved. Would you like to save them now?', 'seedredux-framework' ) );

                /**
                 * Reset all string
                 * filter 'seedredux/{opt_name}/localize/reset
                 *
                 * @param       string        reset all string
                 */
                $reset_all = apply_filters( "seedredux/{$this->parent->args['opt_name']}/localize/reset", __( 'Are you sure? Resetting will lose all custom values.', 'seedredux-framework' ) );

                /**
                 * Reset section string
                 * filter 'seedredux/{opt_name}/localize/reset_section
                 *
                 * @param       string        reset section string
                 */
                $reset_section = apply_filters( "seedredux/{$this->parent->args['opt_name']}/localize/reset_section", __( 'Are you sure? Resetting will lose all custom values in this section.', 'seedredux-framework' ) );

                /**
                 * Preset confirm string
                 * filter 'seedredux/{opt_name}/localize/preset
                 *
                 * @param       string        preset confirm string
                 */
                $preset_confirm = apply_filters( "seedredux/{$this->parent->args['opt_name']}/localize/preset", __( 'Your current options will be replaced with the values of this preset. Would you like to proceed?', 'seedredux-framework' ) );
                global $pagenow;
                $this->parent->localize_data['args'] = array(
                    'save_pending'          => $save_pending,
                    'reset_confirm'         => $reset_all,
                    'reset_section_confirm' => $reset_section,
                    'preset_confirm'        => $preset_confirm,
                    'please_wait'           => __( 'Please Wait', 'seedredux-framework' ),
                    'opt_name'              => $this->parent->args['opt_name'],
                    'slug'                  => $this->parent->args['page_slug'],
                    'hints'                 => $this->parent->args['hints'],
                    'disable_save_warn'     => $this->parent->args['disable_save_warn'],
                    'class'                 => $this->parent->args['class'],
                    'ajax_save'             => $this->parent->args['ajax_save'],
                    'menu_search'           => $pagenow . '?page=' . $this->parent->args['page_slug'] . "&tab="
                );

                $this->parent->localize_data['ajax'] = array(
                    'console' => __( 'There was an error saving. Here is the result of your action:', 'seedredux-framework' ),
                    'alert'   => __( 'There was a problem with your action. Please try again or reload the page.', 'seedredux-framework' ),
                );


                $this->get_warnings_and_errors_array();

                wp_localize_script(
                    'seedredux-js',
                    'seedredux',
                    $this->parent->localize_data
                );

                wp_enqueue_script( 'seedredux-js' ); // Enque the JS now

            }
        }
    }