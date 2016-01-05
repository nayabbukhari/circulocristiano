/*global seedredux_change, wp, seedredux*/

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.slides = seedredux.field_objects.slides || {};

    $( document ).ready(
        function() {
            //seedredux.field_objects.slides.init();
        }
    );

    seedredux.field_objects.slides.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".seedredux-group-tab:visible" ).find( '.seedredux-container-slides:visible' );
        }

        $( selector ).each(
            function() {
                var el = $( this );

                seedredux.field_objects.media.init(el);

                var parent = el;
                if ( !el.hasClass( 'seedredux-field-container' ) ) {
                    parent = el.parents( '.seedredux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                
                if ( parent.hasClass( 'seedredux-container-slides' ) ) {
                    parent.addClass( 'seedredux-field-init' );    
                }
                
                if ( parent.hasClass( 'seedredux-field-init' ) ) {
                    parent.removeClass( 'seedredux-field-init' );
                } else {
                    return;
                }

                el.find( '.seedredux-slides-remove' ).live(
                    'click', function() {
                        seedredux_change( $( this ) );

                        $( this ).parent().siblings().find( 'input[type="text"]' ).val( '' );
                        $( this ).parent().siblings().find( 'textarea' ).val( '' );
                        $( this ).parent().siblings().find( 'input[type="hidden"]' ).val( '' );

                        var slideCount = $( this ).parents( '.seedredux-container-slides:first' ).find( '.seedredux-slides-accordion-group' ).length;

                        if ( slideCount > 1 ) {
                            $( this ).parents( '.seedredux-slides-accordion-group:first' ).slideUp(
                                'medium', function() {
                                    $( this ).remove();
                                }
                            );
                        } else {
                            var content_new_title = $( this ).parent( '.seedredux-slides-accordion' ).data( 'new-content-title' );

                            $( this ).parents( '.seedredux-slides-accordion-group:first' ).find( '.remove-image' ).click();
                            $( this ).parents( '.seedredux-container-slides:first' ).find( '.seedredux-slides-accordion-group:last' ).find( '.seedredux-slides-header' ).text( content_new_title );
                        }
                    }
                );

                //el.find( '.seedredux-slides-add' ).click(
                el.find( '.seedredux-slides-add' ).off('click').click(
                    function() {
                        var newSlide = $( this ).prev().find( '.seedredux-slides-accordion-group:last' ).clone( true );

                        var slideCount = $( newSlide ).find( '.slide-title' ).attr( "name" ).match( /[0-9]+(?!.*[0-9])/ );
                        var slideCount1 = slideCount * 1 + 1;

                        $( newSlide ).find( 'input[type="text"], input[type="hidden"], textarea' ).each(
                            function() {

                                $( this ).attr(
                                    "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 )
                                ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 ) );
                                $( this ).val( '' );
                                if ( $( this ).hasClass( 'slide-sort' ) ) {
                                    $( this ).val( slideCount1 );
                                }
                            }
                        );

                        var content_new_title = $( this ).prev().data( 'new-content-title' );

                        $( newSlide ).find( '.screenshot' ).removeAttr( 'style' );
                        $( newSlide ).find( '.screenshot' ).addClass( 'hide' );
                        $( newSlide ).find( '.screenshot a' ).attr( 'href', '' );
                        $( newSlide ).find( '.remove-image' ).addClass( 'hide' );
                        $( newSlide ).find( '.seedredux-slides-image' ).attr( 'src', '' ).removeAttr( 'id' );
                        $( newSlide ).find( 'h3' ).text( '' ).append( '<span class="seedredux-slides-header">' + content_new_title + '</span><span class="ui-accordion-header-icon ui-icon ui-icon-plus"></span>' );
                        $( this ).prev().append( newSlide );
                    }
                );

                el.find( '.slide-title' ).keyup(
                    function( event ) {
                        var newTitle = event.target.value;
                        $( this ).parents().eq( 3 ).find( '.seedredux-slides-header' ).text( newTitle );
                    }
                );


                el.find( ".seedredux-slides-accordion" )
                    .accordion(
                    {
                        header: "> div > fieldset > h3",
                        collapsible: true,
                        active: false,
                        heightStyle: "content",
                        icons: {
                            "header": "ui-icon-plus",
                            "activeHeader": "ui-icon-minus"
                        }
                    }
                )
                    .sortable(
                    {
                        axis: "y",
                        handle: "h3",
                        connectWith: ".seedredux-slides-accordion",
                        start: function( e, ui ) {
                            ui.placeholder.height( ui.item.height() );
                            ui.placeholder.width( ui.item.width() );
                        },
                        placeholder: "ui-state-highlight",
                        stop: function( event, ui ) {
                            // IE doesn't register the blur when sorting
                            // so trigger focusout handlers to remove .ui-state-focus
                            ui.item.children( "h3" ).triggerHandler( "focusout" );
                            var inputs = $( 'input.slide-sort' );
                            inputs.each(
                                function( idx ) {
                                    $( this ).val( idx );
                                }
                            );
                        }
                    }
                );
            }
        );
    };
})( jQuery );