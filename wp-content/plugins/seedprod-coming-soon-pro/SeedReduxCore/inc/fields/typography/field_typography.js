/*global seedredux_change, seedredux*/

/**
 * Typography
 * Dependencies:        google.com, jquery, select2
 * Feature added by:    Dovy Paukstys - http://simplerain.com/
 * Date:                06.14.2013
 *
 * Rewrite:             Kevin Provance (kprovance)
 * Date:                May 25, 2014
 */

(function( $ ) {
    "use strict";

    seedredux.field_objects = seedredux.field_objects || {};
    seedredux.field_objects.typography = seedredux.field_objects.typography || {};

    var selVals = [];
    var isSelecting = false;

    var default_params = {
        width: 'resolve',
        triggerChange: true,
        allowClear: true
    };

    $( document ).ready(
        function() {
            //seedredux.field_objects.typography.init();
        }
    );

    seedredux.field_objects.typography.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".seedredux-group-tab:visible" ).find( '.seedredux-container-typography:visible' );
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

                var fontClear;

                el.each(
                    function() {
                        // init each typography field
                        $( this ).find( '.seedredux-typography-container' ).each(
                            function() {
                                var family = $( this ).find( '.seedredux-typography-family' );

                                if ( family.data( 'value' ) === undefined ) {
                                    family = $( this );
                                } else if ( family.data( 'value' ) !== "" ) {
                                    $( family ).val( family.data( 'value' ) );
                                }

                                var select2_handle = $( this ).find( '.select2_params' );
                                if ( select2_handle.size() > 0 ) {
                                    var select2_params = select2_handle.val();

                                    select2_params = JSON.parse( select2_params );
                                    default_params = $.extend( {}, default_params, select2_params );
                                }

                                fontClear = Boolean( $( this ).find( '.seedredux-font-clear' ).val() );

                                seedredux.field_objects.typography.select( family );

                                window.onbeforeunload = null;
                            }
                        );

                        //init when value is changed
                        $( this ).find( '.seedredux-typography' ).on(
                            'change', function() {
                                seedredux.field_objects.typography.select( $( this ) ); //.parents('.seedredux-container-typography:first'));
                            }
                        );

                        //init when value is changed
                        $( this ).find( '.seedredux-typography-size, .seedredux-typography-height, .seedredux-typography-word, .seedredux-typography-letter, .seedredux-typography-align, .seedredux-typography-transform, .seedredux-typography-font-variant, .seedredux-typography-decoration' ).keyup(
                            function() {
                                seedredux.field_objects.typography.select( $( this ).parents( '.seedredux-container-typography:first' ) );
                            }
                        );

                        // Have to redeclare the wpColorPicker to get a callback function
                        $( this ).find( '.seedredux-typography-color' ).wpColorPicker(
                            {
                                change: function( event, ui ) {
                                    seedredux_change( $( this ) );
                                    $( this ).val( ui.color.toString() );
                                    seedredux.field_objects.typography.select( $( this ) );
                                }
                            }
                        );

                        // Don't allow negative numbers for size field
                        $( this ).find( ".seedredux-typography-size" ).numeric(
                            {
                                allowMinus: false
                            }
                        );

                        // Allow negative numbers for indicated fields
                        $( this ).find( ".seedredux-typography-height, .seedredux-typography-word, .seedredux-typography-letter" ).numeric(
                            {
                                allowMinus: true
                            }
                        );

                        // select2 magic, to load font-family dynamically
                        var data = [{id: 'none', text: 'none'}];

                        $( this ).find( ".seedredux-typography-family" ).select2(
                            {
                                matcher: function( term, text ) {
                                    return text.toUpperCase().indexOf( term.toUpperCase() ) === 0;
                                },

                                query: function( query ) {
                                    return window.Select2.query.local( data )( query );
                                },

                                initSelection: function( element, callback ) {
                                    var data = {id: element.val(), text: element.val()};
                                    callback( data );
                                },
                                allowClear: fontClear,
                                // when one clicks on the font-family select box
                            }
                        ).on(
                            "select2-opening", function( e ) {

                                // Get field ID
                                var thisID = $( this ).parents( '.seedredux-container-typography:first' ).attr( 'data-id' );

                                // User included fonts?
                                var isUserFonts = $( '#' + thisID + ' .seedredux-typography-font-family' ).data( 'user-fonts' );
                                isUserFonts = isUserFonts ? 1 : 0;

                                // Google font isn use?
                                var usingGoogleFonts = $( '#' + thisID + ' .seedredux-typography-google' ).val();
                                usingGoogleFonts = usingGoogleFonts ? 1 : 0;

                                // Set up data array
                                var buildData = [];

                                // If custom fonts, push onto array
                                if ( seedredux.customfonts !== undefined ) {
                                    buildData.push( seedredux.customfonts );
                                }

                                // If standard fonts, push onto array
                                if ( seedredux.stdfonts !== undefined && isUserFonts === 0 ) {
                                    buildData.push( seedredux.stdfonts );
                                }

                                // If user fonts, pull from localize and push into array
                                if ( isUserFonts == 1 ) {
                                    var fontKids = [];

                                    // <option>
                                    for ( var key in seedredux.typography[thisID] ) {
                                        var obj = seedredux.typography[thisID].std_font;

                                        for ( var prop in obj ) {
                                            if ( obj.hasOwnProperty( prop ) ) {
                                                fontKids.push(
                                                    {
                                                        id: prop,
                                                        text: prop,
                                                        'data-google': 'false'
                                                    }
                                                );
                                            }
                                        }
                                    }

                                    // <optgroup>
                                    var fontData = {
                                        text: 'Standard Fonts',
                                        children: fontKids
                                    };

                                    buildData.push( fontData );
                                }

                                // If googfonts on and had data, push into array
                                if ( usingGoogleFonts == 1 || usingGoogleFonts === true && seedredux.googlefonts !== undefined ) {
                                    buildData.push( seedredux.googlefonts );
                                }

                                // output data to drop down
                                data = buildData;

                                // get placeholder
                                var selFamily = $( '#' + thisID + ' #' + thisID + '-family' ).attr( 'placeholder' );
                                if ( !selFamily ) {
                                    selFamily = null;
                                }

                                // select current font
                                $( '#' + thisID + " .seedredux-typography-family" ).select2( 'val', selFamily );

                                // When selection is made.
                            }
                        ).on(
                            'select2-selecting', function( val, object ) {
                                var fontName = val.object.text;
                                var thisID = $( this ).parents( '.seedredux-container-typography:first' ).attr( 'data-id' );

                                $( '#' + thisID + ' #' + thisID + '-family' ).data( 'value', fontName );
                                $( '#' + thisID + ' #' + thisID + '-family' ).attr( 'placeholder', fontName );

                                // option values
                                selVals = val;
                                isSelecting = true;

                                seedredux_change( $( this ) );
                            }
                        ).on(
                            'select2-clearing', function( val, choice ) {
                                var thisID = $( this ).parents( '.seedredux-container-typography:first' ).attr( 'data-id' );

                                $( '#' + thisID + ' #' + thisID + '-family' ).attr( 'data-value', '' );
                                $( '#' + thisID + ' #' + thisID + '-family' ).attr( 'placeholder', 'Font Family' );

                                $( '#' + thisID + ' #' + thisID + '-google-font' ).val( 'false' );

                                seedredux_change( $( this ) );
                            }
                        );

                        var xx = el.find( ".seedredux-typography-family" );
                        if ( !xx.hasClass( 'seedredux-typography-family' ) ) {
                            el.find( ".seedredux-typography-style" ).select2( default_params );
                        }

                        // Init select2 for indicated fields
                        el.find( ".seedredux-typography-family-backup, .seedredux-typography-align, .seedredux-typography-transform, .seedredux-typography-font-variant, .seedredux-typography-decoration" ).select2( default_params );

                    }
                );
            }
        );
    };

    // Return font size
    seedredux.field_objects.typography.size = function( obj ) {
        var size = 0,
            key;

        for ( key in obj ) {
            if ( obj.hasOwnProperty( key ) ) {
                size++;
            }
        }

        return size;
    };

    // Return proper bool value
    seedredux.field_objects.typography.makeBool = function( val ) {
        if ( val == 'false' || val == '0' || val === false || val === 0 ) {
            return false;
        } else if ( val == 'true' || val == '1' || val === true || val == 1 ) {
            return true;
        }
    };

    seedredux.field_objects.typography.contrastColour = function( hexcolour ) {
        // default value is black.
        var retVal = '#444444';

        // In case - for some reason - a blank value is passed.
        // This should *not* happen.  If a function passing a value
        // is canceled, it should pass the current value instead of
        // a blank.  This is how the Windows Common Controls do it.  :P
        if ( hexcolour !== '' ) {

            // Replace the hash with a blank.
            hexcolour = hexcolour.replace( '#', '' );

            var r = parseInt( hexcolour.substr( 0, 2 ), 16 );
            var g = parseInt( hexcolour.substr( 2, 2 ), 16 );
            var b = parseInt( hexcolour.substr( 4, 2 ), 16 );
            var res = ((r * 299) + (g * 587) + (b * 114)) / 1000;

            // Instead of pure black, I opted to use WP 3.8 black, so it looks uniform.  :) - kp
            retVal = (res >= 128) ? '#444444' : '#ffffff';
        }

        return retVal;
    };


    //  Sync up font options
    seedredux.field_objects.typography.select = function( selector ) {

        // Main id for selected field
        var mainID = $( selector ).parents( '.seedredux-container-typography:first' ).attr( 'data-id' );

        // Set all the variables to be checked against
        var family = $( '#' + mainID + ' #' + mainID + '-family' ).val();

        if ( !family ) {
            family = null; //"inherit";
        }

        var familyBackup = $( '#' + mainID + ' select.seedredux-typography-family-backup' ).val();
        var size = $( '#' + mainID + ' .seedredux-typography-size' ).val();
        var height = $( '#' + mainID + ' .seedredux-typography-height' ).val();
        var word = $( '#' + mainID + ' .seedredux-typography-word' ).val();
        var letter = $( '#' + mainID + ' .seedredux-typography-letter' ).val();
        var align = $( '#' + mainID + ' select.seedredux-typography-align' ).val();
        var transform = $( '#' + mainID + ' select.seedredux-typography-transform' ).val();
        var fontVariant = $( '#' + mainID + ' select.seedredux-typography-font-variant' ).val();
        var decoration = $( '#' + mainID + ' select.seedredux-typography-decoration' ).val();
        var style = $( '#' + mainID + ' select.seedredux-typography-style' ).val();
        var script = $( '#' + mainID + ' select.seedredux-typography-subsets' ).val();
        var color = $( '#' + mainID + ' .seedredux-typography-color' ).val();
        var units = $( '#' + mainID ).data( 'units' );

        //var output = family;

        // Is selected font a google font?
        var google;
        if ( isSelecting === true ) {
            google = seedredux.field_objects.typography.makeBool( selVals.object['data-google'] );
            $( '#' + mainID + ' .seedredux-typography-google-font' ).val( google );
        } else {
            google = seedredux.field_objects.typography.makeBool( $( '#' + mainID + ' .seedredux-typography-google-font' ).val() ); // Check if font is a google font
        }

        // Page load. Speeds things up memory wise to offload to client
        if ( !$( '#' + mainID ).hasClass( 'typography-initialized' ) ) {
            style = $( '#' + mainID + ' select.seedredux-typography-style' ).data( 'value' );
            script = $( '#' + mainID + ' select.seedredux-typography-subsets' ).data( 'value' );

            if ( style !== "" ) {
                style = String( style );
            }

            if ( typeof (script) !== undefined ) {
                script = String( script );
            }
        }

        // Something went wrong trying to read google fonts, so turn google off
        if ( seedredux.fonts.google === undefined ) {
            google = false;
        }

        // Get font details
        var details = '';
        if ( google === true && ( family in seedredux.fonts.google) ) {
            details = seedredux.fonts.google[family];
        } else {
            details = {
                '400': 'Normal 400',
                '700': 'Bold 700',
                '400italic': 'Normal 400 Italic',
                '700italic': 'Bold 700 Italic'
            };
        }

        if ( $( selector ).hasClass( 'seedredux-typography-subsets' ) ) {
            $( '#' + mainID + ' input.typography-subsets' ).val( script );
        }

        // If we changed the font
        if ( $( selector ).hasClass( 'seedredux-typography-family' ) ) {
            var html = '<option value=""></option>';

            // Google specific stuff
            if ( google === true ) {

                // STYLES
                var selected = "";
                $.each(
                    details.variants, function( index, variant ) {
                        if ( variant.id === style || seedredux.field_objects.typography.size( details.variants ) === 1 ) {
                            selected = ' selected="selected"';
                            style = variant.id;
                        } else {
                            selected = "";
                        }

                        html += '<option value="' + variant.id + '"' + selected + '>' + variant.name.replace(
                            /\+/g, " "
                        ) + '</option>';
                    }
                );

                // destroy select2
                $( '#' + mainID + ' .seedredux-typography-style' ).select2( "destroy" );

                // Instert new HTML
                $( '#' + mainID + ' .seedredux-typography-style' ).html( html );

                // Init select2
                $( '#' + mainID + ' .seedredux-typography-style' ).select2( default_params );


                // SUBSETS
                selected = "";
                html = '<option value=""></option>';

                $.each(
                    details.subsets, function( index, subset ) {
                        if ( subset.id === script || seedredux.field_objects.typography.size( details.subsets ) === 1 ) {
                            selected = ' selected="selected"';
                            script = subset.id;
                            $( '#' + mainID + ' input.typography-subsets' ).val( script );
                        } else {
                            selected = "";
                        }

                        html += '<option value="' + subset.id + '"' + selected + '>' + subset.name.replace(
                            /\+/g, " "
                        ) + '</option>';
                    }
                );

                //if (typeof (familyBackup) !== "undefined" && familyBackup !== "") {
                //    output += ', ' + familyBackup;
                //}

                // Destroy select2
                $( '#' + mainID + ' .seedredux-typography-subsets' ).select2( "destroy" );

                // Inset new HTML
                $( '#' + mainID + ' .seedredux-typography-subsets' ).html( html );

                // Init select2
                $( '#' + mainID + ' .seedredux-typography-subsets' ).select2( default_params );

                $( '#' + mainID + ' .seedredux-typography-subsets' ).parent().fadeIn( 'fast' );
                $( '#' + mainID + ' .typography-family-backup' ).fadeIn( 'fast' );
            } else {
                if ( details ) {
                    $.each(
                        details, function( index, value ) {
                            if ( index === style || index === "normal" ) {
                                selected = ' selected="selected"';
                                $( '#' + mainID + ' .typography-style .select2-chosen' ).text( value );
                            } else {
                                selected = "";
                            }

                            html += '<option value="' + index + '"' + selected + '>' + value.replace(
                                '+', ' '
                            ) + '</option>';
                        }
                    );

                    // Destory select2
                    $( '#' + mainID + ' .seedredux-typography-style' ).select2( "destroy" );

                    // Insert new HTML
                    $( '#' + mainID + ' .seedredux-typography-style' ).html( html );

                    // Init select2
                    $( '#' + mainID + ' .seedredux-typography-style' ).select2( default_params );

                    // Prettify things
                    $( '#' + mainID + ' .seedredux-typography-subsets' ).parent().fadeOut( 'fast' );
                    $( '#' + mainID + ' .typography-family-backup' ).fadeOut( 'fast' );
                }
            }

            $( '#' + mainID + ' .seedredux-typography-font-family' ).val( family );
        } else if ( $( selector ).hasClass( 'seedredux-typography-family-backup' ) && familyBackup !== "" ) {
            $( '#' + mainID + ' .seedredux-typography-font-family-backup' ).val( familyBackup );
        }

        // Check if the selected value exists. If not, empty it. Else, apply it.
        if ( $( '#' + mainID + " select.seedredux-typography-style option[value='" + style + "']" ).length === 0 ) {
            style = "";
            $( '#' + mainID + ' select.seedredux-typography-style' ).select2( 'val', '' );
        } else if ( style === "400" ) {
            $( '#' + mainID + ' select.seedredux-typography-style' ).select2( 'val', style );
        }

        // Handle empty subset select
        if ( $( '#' + mainID + " select.seedredux-typography-subsets option[value='" + script + "']" ).length === 0 ) {
            script = "";
            $( '#' + mainID + ' select.seedredux-typography-subsets' ).select2( 'val', '' );
            $( '#' + mainID + ' input.typography-subsets' ).val( script );
        }

        var _linkclass = 'style_link_' + mainID;

        //remove other elements crested in <head>
        $( '.' + _linkclass ).remove();
        if ( family !== null && family !== "inherit" && $( '#' + mainID ).hasClass( 'typography-initialized' ) ) {

            //replace spaces with "+" sign
            var the_font = family.replace( /\s+/g, '+' );
            if ( google === true ) {

                //add reference to google font family
                var link = the_font;

                if ( style && style !== "" ) {
                    link += ':' + style.replace( /\-/g, " " );
                }

                if ( script && script !== "" ) {
                    link += '&subset=' + script;
                }

                if ( typeof (WebFont) !== "undefined" && WebFont ) {
                    WebFont.load( {google: {families: [link]}} );
                }

                $( '#' + mainID + ' .seedredux-typography-google' ).val( true );
            } else {
                $( '#' + mainID + ' .seedredux-typography-google' ).val( false );
            }
        }

        // Weight and italic
        if ( style.indexOf( "italic" ) !== -1 ) {
            $( '#' + mainID + ' .typography-preview' ).css( 'font-style', 'italic' );
            $( '#' + mainID + ' .typography-font-style' ).val( 'italic' );
            style = style.replace( 'italic', '' );
        } else {
            $( '#' + mainID + ' .typography-preview' ).css( 'font-style', "normal" );
            $( '#' + mainID + ' .typography-font-style' ).val( '' );
        }

        $( '#' + mainID + ' .typography-font-weight' ).val( style );

        if ( !height ) {
            height = size;
        }

        if ( size === '' ) {
            $( '#' + mainID + ' .typography-font-size' ).val( '' );
        } else {
            $( '#' + mainID + ' .typography-font-size' ).val( size + units );
        }

        if ( height === '' ) {
            $( '#' + mainID + ' .typography-line-height' ).val( '' );
        } else {
            $( '#' + mainID + ' .typography-line-height' ).val( height + units );
        }

        if ( word === '' ) {
            $( '#' + mainID + ' .typography-word-spacing' ).val( '' );
        } else {
            $( '#' + mainID + ' .typography-word-spacing' ).val( word + units );
        }

        if ( letter === '' ) {
            $( '#' + mainID + ' .typography-letter-spacing' ).val( '' );
        } else {
            $( '#' + mainID + ' .typography-letter-spacing' ).val( letter + units );
        }

        // Show more preview stuff
        if ( $( '#' + mainID ).hasClass( 'typography-initialized' ) ) {
            var isPreviewSize = $( '#' + mainID + ' .typography-preview' ).data( 'preview-size' );

            if ( isPreviewSize == '0' ) {
                $( '#' + mainID + ' .typography-preview' ).css( 'font-size', size + units );
            }

            $( '#' + mainID + ' .typography-preview' ).css( 'font-weight', style );

            //show in the preview box the font
            $( '#' + mainID + ' .typography-preview' ).css( 'font-family', family + ', sans-serif' );

            if ( family === 'none' && family === '' ) {
                //if selected is not a font remove style "font-family" at preview box
                $( '#' + mainID + ' .typography-preview' ).css( 'font-family', 'inherit' );
            }

            $( '#' + mainID + ' .typography-preview' ).css( 'line-height', height + units );
            $( '#' + mainID + ' .typography-preview' ).css( 'word-spacing', word + units );
            $( '#' + mainID + ' .typography-preview' ).css( 'letter-spacing', letter + units );

            if ( color ) {
                $( '#' + mainID + ' .typography-preview' ).css( 'color', color );
                $( '#' + mainID + ' .typography-preview' ).css(
                    'background-color', seedredux.field_objects.typography.contrastColour( color )
                );
            }

            $( '#' + mainID + ' .typography-style .select2-chosen' ).text( $( '#' + mainID + ' .seedredux-typography-style option:selected' ).text() );
            $( '#' + mainID + ' .typography-script .select2-chosen' ).text( $( '#' + mainID + ' .seedredux-typography-subsets option:selected' ).text() );

            if ( align ) {
                $( '#' + mainID + ' .typography-preview' ).css( 'text-align', align );
            }

            if ( transform ) {
                $( '#' + mainID + ' .typography-preview' ).css( 'text-transform', transform );
            }

            if ( fontVariant ) {
                $( '#' + mainID + ' .typography-preview' ).css( 'font-variant', fontVariant );
            }

            if ( decoration ) {
                $( '#' + mainID + ' .typography-preview' ).css( 'text-decoration', decoration );
            }
            $( '#' + mainID + ' .typography-preview' ).slideDown();
        }
        // end preview stuff

        // if not preview showing, then set preview to show
        if ( !$( '#' + mainID ).hasClass( 'typography-initialized' ) ) {
            $( '#' + mainID ).addClass( 'typography-initialized' );
        }

        isSelecting = false;

    };
})( jQuery );