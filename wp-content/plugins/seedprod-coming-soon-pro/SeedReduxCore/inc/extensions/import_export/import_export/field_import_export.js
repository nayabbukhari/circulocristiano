/*global seedredux_change, seedredux*/

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.import_export = seedredux.field_objects.import_export || {};

    $( document ).ready(
        function() {
            seedredux.field_objects.import_export.init();
        }
    );

    seedredux.field_objects.import_export.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.seedredux-container-import_export' );
        }

        var parent = selector;

        if ( !selector.hasClass( 'seedredux-field-container' ) ) {
            parent = selector.parents( '.seedredux-field-container:first' );
        }

        if ( parent.hasClass( 'seedredux-field-init' ) ) {
            parent.removeClass( 'seedredux-field-init' );
        } else {
            return;
        }

        $( '#seedredux-import' ).click(
            function( e ) {
                if ( $( '#import-code-value' ).val() === "" && $( '#import-link-value' ).val() === "" ) {
                    e.preventDefault();
                    return false;
                }
                window.onbeforeunload = null;
                seedredux.args.ajax_save = false;
            }
        );

        $( '#seedredux-import-code-button' ).click(
            function() {
                var $el = $( '#seedredux-import-code-wrapper' );
                if ( $( '#seedredux-import-link-wrapper' ).is( ':visible' ) ) {
                    $( '#import-link-value' ).text( '' );
                    $( '#seedredux-import-link-wrapper' ).slideUp(
                        'fast', function() {
                            $el.slideDown(
                                'fast', function() {
                                    $( '#import-code-value' ).focus();
                                }
                            );
                        }
                    );
                } else {
                    if ( $el.is( ':visible' ) ) {
                        $el.slideUp();
                    } else {
                        $el.slideDown(
                            'medium', function() {
                                $( '#import-code-value' ).focus();
                            }
                        );
                    }
                }
            }
        );

        $( '#seedredux-import-link-button' ).click(
            function() {
                var $el = $( '#seedredux-import-link-wrapper' );
                if ( $( '#seedredux-import-code-wrapper' ).is( ':visible' ) ) {
                    $( '#import-code-value' ).text( '' );
                    $( '#seedredux-import-code-wrapper' ).slideUp(
                        'fast', function() {
                            $el.slideDown(
                                'fast', function() {
                                    $( '#import-link-value' ).focus();
                                }
                            );
                        }
                    );
                } else {
                    if ( $el.is( ':visible' ) ) {
                        $el.slideUp();
                    } else {
                        $el.slideDown(
                            'medium', function() {
                                $( '#import-link-value' ).focus();
                            }
                        );
                    }
                }
            }
        );

        $( '#seedredux-export-code-copy' ).click(
            function() {
                var $el = $( '#seedredux-export-code' );
                if ( $( '#seedredux-export-link-value' ).is( ':visible' ) ) {
                    $( '#seedredux-export-link-value' ).slideUp(
                        'fast', function() {
                            $el.slideDown(
                                'medium', function() {
                                    var options = seedredux.options;
                                    options['seedredux-backup'] = 1;
                                    $( this ).text( JSON.stringify( options ) ).focus().select();
                                }
                            );
                        }
                    );
                } else {
                    if ( $el.is( ':visible' ) ) {
                        $el.slideUp().text( '' );
                    } else {
                        $el.slideDown(
                            'medium', function() {
                                var options = seedredux.options;
                                options['seedredux-backup'] = 1;
                                $( this ).text( JSON.stringify( options ) ).focus().select();
                            }
                        );
                    }
                }
            }
        );

        $( '.seedredux-container-import_export textarea' ).focusout(
            function() {
                var $id = $( this ).attr( 'id' );
                var $el = $( this );
                var $container = $el;
                if ( $id == "import-link-value" || $id == "import-code-value" ) {
                    $container = $( this ).parent();
                }
                $container.slideUp(
                    'medium', function() {
                        if ( $id != "seedredux-export-link-value" ) {
                            $el.text( '' );
                        }
                    }
                );
            }
        );


        $( '#seedredux-export-link' ).click(
            function() {
                var $el = $( '#seedredux-export-link-value' );
                if ( $( '#seedredux-export-code' ).is( ':visible' ) ) {
                    $( '#seedredux-export-code' ).slideUp(
                        'fast', function() {
                            $el.slideDown().focus().select();
                        }
                    );
                } else {
                    if ( $el.is( ':visible' ) ) {
                        $el.slideUp();
                    } else {
                        $el.slideDown(
                            'medium', function() {
                                $( this ).focus().select();
                            }
                        );
                    }

                }
            }
        );

        var textBox1 = document.getElementById( "seedredux-export-code" );
        textBox1.onfocus = function() {
            textBox1.select();
            // Work around Chrome's little problem
            textBox1.onmouseup = function() {
                // Prevent further mouseup intervention
                textBox1.onmouseup = null;
                return false;
            };
        };
        var textBox2 = document.getElementById( "import-code-value" );
        textBox2.onfocus = function() {
            textBox2.select();
            // Work around Chrome's little problem
            textBox2.onmouseup = function() {
                // Prevent further mouseup intervention
                textBox2.onmouseup = null;
                return false;
            };
        };
    };
})( jQuery );