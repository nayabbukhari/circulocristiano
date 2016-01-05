/*global seedredux_change, seedredux*/

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.select_image = seedredux.field_objects.select_image || {};

    $( document ).ready(
        function() {
            //seedredux.field_objects.select_image.init();
        }
    );

    seedredux.field_objects.select_image.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".seedredux-group-tab:visible" ).find( '.seedredux-container-select_image:visible' );
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

                var select2_handle = el.find( '.seedredux-container-select_image' ).find( '.select2_params' );

                if ( select2_handle.size() > 0 ) {
                    var select2_params = select2_handle.val();

                    select2_params = JSON.parse( select2_params );
                    default_params = $.extend( {}, default_params, select2_params );
                }

                el.find( 'select.seedredux-select-images' ).select2( default_params );

                el.find( '.seedredux-select-images' ).on(
                    'change', function() {
                        var preview = $( this ).parents( '.seedredux-field:first' ).find( '.seedredux-preview-image' );

                        if ( $( this ).val() === "" ) {
                            preview.fadeOut(
                                'medium', function() {
                                    preview.attr( 'src', '' );
                                }
                            );
                        } else {
                            preview.attr( 'src', $( this ).val() );
                            preview.fadeIn().css( 'visibility', 'visible' );
                        }
                    }
                );
            }
        );
    };
})( jQuery );