/*global seedredux*/

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.spacing = seedredux.field_objects.spacing || {};

    $( document ).ready(
        function() {
            //seedredux.field_objects.spacing.init();
        }
    );

    seedredux.field_objects.spacing.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".seedredux-group-tab:visible" ).find( '.seedredux-container-spacing:visible' );
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
                var default_params = {
                    width: 'resolve',
                    triggerChange: true,
                    allowClear: true
                };

                var select2_handle = el.find( '.select2_params' );
                if ( select2_handle.size() > 0 ) {
                    var select2_params = select2_handle.val();

                    select2_params = JSON.parse( select2_params );
                    default_params = $.extend( {}, default_params, select2_params );
                }

                el.find( ".seedredux-spacing-units" ).select2( default_params );

                el.find( '.seedredux-spacing-input' ).on(
                    'change', function() {
                        var units = $( this ).parents( '.seedredux-field:first' ).find( '.field-units' ).val();

                        if ( $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-spacing-units' ).length !== 0 ) {
                            units = $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-spacing-units option:selected' ).val();
                        }

                        var value = $( this ).val();

                        if ( typeof units !== 'undefined' && value ) {
                            value += units;
                        }

                        if ( $( this ).hasClass( 'seedredux-spacing-all' ) ) {
                            $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-spacing-value' ).each(
                                function() {
                                    $( this ).val( value );
                                }
                            );
                        } else {
                            $( '#' + $( this ).attr( 'rel' ) ).val( value );
                        }
                    }
                );

                el.find( '.seedredux-spacing-units' ).on(
                    'change', function() {
                        $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-spacing-input' ).change();
                    }
                );
            }
        );
    };
})( jQuery );