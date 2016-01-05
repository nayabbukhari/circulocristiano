/**
 * SeedRedux Editor on change callback
 * Dependencies        : jquery
 * Feature added by    : Dovy Paukstys
 *                     : Kevin Provance (who helped)  :P
 * Date                : 07 June 2014
 */

/*global seedredux_change, wp, tinymce, seedredux*/
(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.editor = seedredux.field_objects.editor || {};

    $( document ).ready(
        function() {
            //seedredux.field_objects.editor.init();
        }
    );

    seedredux.field_objects.editor.init = function( selector ) {
        setTimeout(
            function() {
                for ( var i = 0; i < tinymce.editors.length; i++ ) {
                    seedredux.field_objects.editor.onChange( i );
                }
            }, 1000
        );
    };

    seedredux.field_objects.editor.onChange = function( i ) {
        tinymce.editors[i].on(
            'change', function( e ) {
                var el = jQuery( e.target.contentAreaContainer );
                if ( el.parents( '.seedredux-container-editor:first' ).length !== 0 ) {
                    seedredux_change( $( '.wp-editor-area' ) );
                }
            }
        );
    };
})( jQuery );
