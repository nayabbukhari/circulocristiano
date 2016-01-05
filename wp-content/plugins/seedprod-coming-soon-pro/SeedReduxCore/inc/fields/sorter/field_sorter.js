/*global seedredux, seedredux_opts*/
/*
 * Field Sorter jquery function
 * Based on
 * [SMOF - Slightly Modded Options Framework](http://aquagraphite.com/2011/09/slightly-modded-options-framework/)
 * Version 1.4.2
 */

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.sorter = seedredux.field_objects.sorter || {};

    var scroll = '';

    $( document ).ready(
        function() {
            //seedredux.field_objects.sorter.init();
        }
    );

    seedredux.field_objects.sorter.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".seedredux-group-tab:visible" ).find( '.seedredux-container-sorter:visible' );
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
                
                /**    Sorter (Layout Manager) */
                el.find( '.seedredux-sorter' ).each(
                    function() {
                        var id = $( this ).attr( 'id' );

                        el.find( '#' + id ).find( 'ul' ).sortable(
                            {
                                items: 'li',
                                placeholder: "placeholder",
                                connectWith: '.sortlist_' + id,
                                opacity: 0.8,
                                scroll: false,
                                out: function( event, ui ) {
                                    if ( !ui.helper ) return;
                                    if ( ui.offset.top > 0 ) {
                                        scroll = 'down';
                                    } else {
                                        scroll = 'up';
                                    }
                                    seedredux.field_objects.sorter.scrolling( $( this ).parents( '.seedredux-field-container:first' ) );

                                },
                                over: function( event, ui ) {
                                    scroll = '';
                                },

                                deactivate: function( event, ui ) {
                                    scroll = '';
                                },

                                stop: function( event, ui ) {
                                    var sorter = seedredux.sorter[$( this ).attr( 'data-id' )];
                                    var id = $( this ).find( 'h3' ).text();

                                    if ( sorter.limits && id && sorter.limits[id] ) {
                                        if ( $( this ).children( 'li' ).length >= sorter.limits[id] ) {
                                            $( this ).addClass( 'filled' );
                                            if ( $( this ).children( 'li' ).length > sorter.limits[id] ) {
                                                $( ui.sender ).sortable( 'cancel' );
                                            }
                                        } else {
                                            $( this ).removeClass( 'filled' );
                                        }
                                    }
                                },

                                update: function( event, ui ) {
                                    var sorter = seedredux.sorter[$( this ).attr( 'data-id' )];
                                    var id = $( this ).find( 'h3' ).text();

                                    if ( sorter.limits && id && sorter.limits[id] ) {
                                        if ( $( this ).children( 'li' ).length >= sorter.limits[id] ) {
                                            $( this ).addClass( 'filled' );
                                            if ( $( this ).children( 'li' ).length > sorter.limits[id] ) {
                                                $( ui.sender ).sortable( 'cancel' );
                                            }
                                        } else {
                                            $( this ).removeClass( 'filled' );
                                        }
                                    }

                                    $( this ).find( '.position' ).each(
                                        function() {
                                            //var listID = $( this ).parent().attr( 'id' );
                                            var listID = $( this ).parent().attr( 'data-id' );
                                            var parentID = $( this ).parent().parent().attr( 'data-group-id' );

                                            seedredux_change( $( this ) );

                                            var optionID = $( this ).parent().parent().parent().attr( 'id' );

                                            $( this ).prop(
                                                "name",
                                                seedredux.args.opt_name + '[' + optionID + '][' + parentID + '][' + listID + ']'
                                            );
                                        }
                                    );
                                }
                            }
                        );
                        el.find( ".seedredux-sorter" ).disableSelection();
                    }
                );
            }
        );
    };

    seedredux.field_objects.sorter.scrolling = function( selector ) {
        if (selector === undefined) {
            return;
        }
        
        var scrollable = selector.find( ".seedredux-sorter" );

        if ( scroll == 'up' ) {
            scrollable.scrollTop( scrollable.scrollTop() - 20 );
            setTimeout( seedredux.field_objects.sorter.scrolling, 50 );
        } else if ( scroll == 'down' ) {
            scrollable.scrollTop( scrollable.scrollTop() + 20 );
            setTimeout( seedredux.field_objects.sorter.scrolling, 50 );
        }
    };

})( jQuery );