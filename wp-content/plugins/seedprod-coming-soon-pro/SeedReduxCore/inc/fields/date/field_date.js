/*global jQuery, document, seedredux*/

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.date = seedredux.field_objects.date || {};

    $( document ).ready(
        function() {
            //seedredux.field_objects.date.init();
        }
    );

    seedredux.field_objects.date.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( '.seedredux-container-date:visible' );
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
                el.find( '.seedredux-datepicker' ).each( function() {
                    
                    $( this ).datepicker({
                        beforeShow: function(textbox, instance){
                            var el = $('#ui-datepicker-div');
                            //$('#ui-datepicker-div').remove();
                            //$('.seedredux-main:first').append(el);
                            //instance.dpDiv.css({marginTop: -31 + 'px', marginLeft: -200 + 'px'});
                        } 
                    });
                });
            }
        );


    };
})( jQuery );