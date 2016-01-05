/*global seedredux_change, seedredux*/

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.spinner = seedredux.field_objects.spinner || {};

    $( document ).ready(
        function() {
            //seedredux.field_objects.spinner.init();
        }
    );

    seedredux.field_objects.spinner.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".seedredux-group-tab:visible" ).find( '.seedredux-container-spinner:visible' );
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
                el.find( '.seedredux_spinner' ).each(
                    function() {
                        //slider init
                        var spinner = $( this ).find( '.spinner-input' ).data();
                        spinner.id = $( this ).find( '.spinner-input' ).attr( 'id' );

                        el.find( "#" + spinner.id ).spinner(
                            {
                                value: parseFloat( spinner.val, null ),
                                min: parseFloat( spinner.min, null ),
                                max: parseFloat( spinner.max, null ),
                                step: parseFloat( spinner.step, null ),
                                range: "min",

                                slide: function( event, ui ) {
                                    var input = $( "#" + spinner.id );
                                    input.val( ui.value );
                                    seedredux_change( input );
                                }
                            }
                        );

                        // Limit input for negative
                        var neg = false;
                        if ( parseInt( spinner.min, null ) < 0 ) {
                            neg = true;
                        }

                        //el.find( "#" + spinner.id ).numeric(
                        //    {
                        //        allowMinus: neg,
                        //        min: spinner.min,
                        //        max: spinner.max
                        //    }
                        //);

                    }
                );

                // Update the slider from the input and vice versa
                el.find( ".spinner-input" ).keyup(
                    function() {
                        $( this ).addClass( 'spinnerInputChange' );
                    }
                );

                el.find( ".spinner-input" ).focus(
                    function() {
                        seedredux.field_objects.spinner.clean(
                            $( this ).val(), $( this )
                        );
                    }
                );

                el.find( '.spinner-input' ).typeWatch(
                    {
                        callback: function( value ) {
                            seedredux.field_objects.spinner.clean(
                                value, $( this )
                            );
                        },

                        wait: 500,
                        highlight: false,
                        captureLength: 1
                    }
                );
            }
        );
    };

    seedredux.field_objects.spinner.clean = function( value, selector ) {

        if ( !selector.hasClass( 'spinnerInputChange' ) ) {
            return;
        }
        selector.removeClass( 'spinnerInputChange' );

        var spinner = selector.data();
        value = parseFloat( value );

        if ( value === "" || value === null ) {
            value = spinner.min;
        } else if ( value >= parseInt( spinner.max ) ) {
            value = spinner.max;
        } else if ( value <= parseInt( spinner.min ) ) {
            value = spinner.min;
        } else {
            value = Math.round( value / spinner.step ) * spinner.step;
        }
        selector.val( value );
    };

})( jQuery );