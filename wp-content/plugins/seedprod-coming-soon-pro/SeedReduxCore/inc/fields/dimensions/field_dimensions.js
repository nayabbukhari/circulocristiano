
/*global jQuery, document, seedredux*/

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.dimensions = seedredux.field_objects.dimensions || {};

    $( document ).ready(
        function() {
            //seedredux.field_objects.dimensions.init();
        }
    );

    seedredux.field_objects.dimensions.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.seedredux-container-dimensions:visible' );
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

                el.find( ".seedredux-dimensions-units" ).select2( default_params );

                el.find( '.seedredux-dimensions-input' ).on(
                    'change', function() {
                        var units = $( this ).parents( '.seedredux-field:first' ).find( '.field-units' ).val();
                        if ( $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-dimensions-units' ).length !== 0 ) {
                            units = $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-dimensions-units option:selected' ).val();
                        }
                        if ( typeof units !== 'undefined' ) {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() + units );
                        } else {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() );
                        }
                    }
                );

                el.find( '.seedredux-dimensions-units' ).on(
                    'change', function() {
                        $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-dimensions-input' ).change();
                    }
                );
            }
        );


    };
})( jQuery );