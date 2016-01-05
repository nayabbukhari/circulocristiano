/*
 Field Border (border)
 */

/*global seedredux_change, wp, seedredux*/

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.border = seedredux.field_objects.border || {};

    $( document ).ready(
        function() {
            
        }
    );

    seedredux.field_objects.border.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( ".seedredux-group-tab:visible" ).find( '.seedredux-container-border:visible' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;
                
                if ( !el.hasClass( 'seedredux-field-container' ) ) {
                    parent = el.parents( '.seedredux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                if ( parent.hasClass( 'seedredux-field-init' ) ) {
                    parent.removeClass( 'seedredux-field-init' );
                } else {
                    return;
                }
                el.find( ".seedredux-border-top, .seedredux-border-right, .seedredux-border-bottom, .seedredux-border-left, .seedredux-border-all" ).numeric({
                    allowMinus: false
                });

                var default_params = {
                    triggerChange: true,
                    allowClear: true
                };

                var select2_handle = el.find( '.seedredux-container-border' ).find( '.select2_params' );

                if ( select2_handle.size() > 0 ) {
                    var select2_params = select2_handle.val();

                    select2_params = JSON.parse( select2_params );
                    default_params = $.extend( {}, default_params, select2_params );
                }

                el.find( ".seedredux-border-style" ).select2( default_params );

                el.find( '.seedredux-border-input' ).on(
                    'change', function() {
                        var units = $( this ).parents( '.seedredux-field:first' ).find( '.field-units' ).val();
                        if ( $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-border-units' ).length !== 0 ) {
                            units = $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-border-units option:selected' ).val();
                        }
                        var value = $( this ).val();
                        if ( typeof units !== 'undefined' && value ) {
                            value += units;
                        }
                        if ( $( this ).hasClass( 'seedredux-border-all' ) ) {
                            $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-border-value' ).each(
                                function() {
                                    $( this ).val( value );
                                }
                            );
                        } else {
                            $( '#' + $( this ).attr( 'rel' ) ).val( value );
                        }
                    }
                );
        
                el.find( '.seedredux-border-units' ).on(
                    'change', function() {
                        $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-border-input' ).change();
                    }
                );

                el.find( '.seedredux-color-init' ).wpColorPicker({
                    change: function( u ) {
                        seedredux_change( $( this ) );
                        el.find( '#' + u.target.getAttribute( 'data-id' ) + '-transparency' ).removeAttr( 'checked' );
                    },
                    
                    clear: function() {
                        seedredux_change( $( this ).parent().find( '.seedredux-color-init' ) );
                    }
                });

                el.find( '.seedredux-color' ).on(
                    'keyup', function() {
                        var color = colorValidate( this );

                        if ( color && color !== $( this ).val() ) {
                            $( this ).val( color );
                        }
                    }
                );

                // Replace and validate field on blur
                el.find( '.seedredux-color' ).on(
                    'blur', function() {
                        var value = $( this ).val();

                        if ( colorValidate( this ) === value ) {
                            if ( value.indexOf( "#" ) !== 0 ) {
                                $( this ).val( $( this ).data( 'oldcolor' ) );
                            }
                        }
                    }
                );

                // Store the old valid color on keydown
                el.find( '.seedredux-color' ).on(
                    'keydown', function() {
                        $( this ).data( 'oldkeypress', $( this ).val() );
                    }
                );
            }
        );
    };
})( jQuery );