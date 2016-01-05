/* global seedredux, confirm, relid:true, jsonView */

(function( $ ) {
    'use strict';

    $.seedredux = $.seedredux || {};

    $( document ).ready(
        function() {
            $.fn.isOnScreen = function() {
                if ( !window ) {
                    return;
                }

                var win = $( window );
                var viewport = {
                    top: win.scrollTop(),
                };

                viewport.right = viewport.left + win.width();
                viewport.bottom = viewport.top + win.height();

                var bounds = this.offset();

                bounds.right = bounds.left + this.outerWidth();
                bounds.bottom = bounds.top + this.outerHeight();

                return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
            };

            $.seedredux.hideFields();
            $.seedredux.checkRequired();
            $.seedredux.initEvents();
            $.seedredux.initQtip();
            $.seedredux.tabCheck();
            $.seedredux.notices();
            $.seedredux.tabControl();
            $.seedredux.devFunctions();

        }
    );

    $.seedredux.ajax_save = function( button ) {

        var overlay = $( document.getElementById( 'seedredux_ajax_overlay' ) );
        overlay.fadeIn();

        // Add the loading mechanism
        jQuery( '.seedredux-action_bar .spinner' ).show();
        jQuery( '.seedredux-action_bar input' ).attr( 'disabled', 'disabled' );
        var $notification_bar = jQuery( document.getElementById( 'seedredux_notification_bar' ) );
        $notification_bar.slideUp();
        jQuery( '.seedredux-save-warn' ).slideUp();

        var $parent = jQuery( document.getElementById( "seedredux-form-wrapper" ) );

        // Editor field doesn't auto save. Have to call it. Boo.
        if ( seedredux.fields.hasOwnProperty( "editor" ) ) {
            $.each(
                seedredux.fields.editor, function( $key, $index ) {
                    var editor = tinyMCE.get( $key );
                    if ( editor ) {
                        editor.save();
                    }
                }
            );
        }

        var $data = $parent.serialize();
        // add values for checked and unchecked checkboxes fields
        $parent.find( 'input[type=checkbox]' ).each(
            function() {
                if ( typeof $( this ).attr( 'name' ) !== "undefined" ) {
                    var chkVal = $( this ).is( ':checked' ) ? $( this ).val() : "0";
                    $data += "&" + $( this ).attr( 'name' ) + "=" + chkVal;
                }
            }
        );


        if ( button.attr( 'name' ) != "seedredux_save" ) {
            $data += "&" + button.attr( 'name' ) + "=" + button.val();
        }

        var $nonce = $parent.attr( "data-nonce" );

        jQuery.ajax(
            {
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: {
                    action: seedredux.args.opt_name + "_ajax_save",
                    nonce: $nonce,
                    'opt_name': seedredux.args.opt_name,
                    data: $data
                },
                error: function( response ) {
                    if ( !window.console ) console = {};
                    console.log = console.log || function( name, data ) {
                    };
                    console.log( seedredux.ajax.console );
                    console.log( response.responseText );
                    jQuery( '.seedredux-action_bar input' ).removeAttr( 'disabled' );
                    overlay.fadeOut( 'fast' );
                    jQuery( '.seedredux-action_bar .spinner' ).fadeOut( 'fast' );
                    alert( seedredux.ajax.alert );
                },
                success: function( response ) {
                    if ( response.action && response.action == "reload" ) {
                        location.reload();
                    } else if ( response.status == "success" ) {
                        jQuery( '.seedredux-action_bar input' ).removeAttr( 'disabled' );
                        overlay.fadeOut( 'fast' );
                        jQuery( '.seedredux-action_bar .spinner' ).fadeOut( 'fast' );
                        seedredux.options = response.options;
                        //seedredux.defaults = response.defaults;
                        seedredux.errors = response.errors;
                        seedredux.warnings = response.warnings;

                        $notification_bar.html( response.notification_bar ).slideDown( 'fast' );
                        if ( response.errors !== null || response.warnings !== null ) {
                            $.seedredux.notices();
                        }
                        var $save_notice = $( document.getElementById( 'seedredux_notification_bar' ) ).find( '.saved_notice' );
                        $save_notice.slideDown();
                        $save_notice.delay( 4000 ).slideUp();
                    } else {
                        jQuery( '.seedredux-action_bar input' ).removeAttr( 'disabled' );
                        jQuery( '.seedredux-action_bar .spinner' ).fadeOut( 'fast' );
                        overlay.fadeOut( 'fast' );
                        jQuery( '.seedredux_ajax_save_error' ).slideUp();
                        jQuery( '.wrap h2:first-child' ).parent().append( '<div class="error seedredux_ajax_save_error" style="display:none;"><p>' + response.status + '</p></div>' );
                        jQuery( '.seedredux_ajax_save_error' ).slideDown();
                        jQuery( "html, body" ).animate( {scrollTop: 0}, "slow" );
                    }
                }
            }
        );
        return false;
    };

    $.seedredux.initEvents = function() {
        $( '.seedredux-presets-bar' ).on(
            'click', function() {
                window.onbeforeunload = null;
            }
        );


        $( '#toplevel_page_' + seedredux.args.slug + ' .wp-submenu a, #wp-admin-bar-' + seedredux.args.slug + ' a.ab-item' ).click(
            function( e ) {

                if ( ( $( '#toplevel_page_' + seedredux.args.slug ).hasClass( 'wp-menu-open' ) || $( this ).hasClass( 'ab-item' ) ) && !$( this ).parents( 'ul.ab-submenu:first' ).hasClass( 'ab-sub-secondary' ) && $( this ).attr( 'href' ).toLowerCase().indexOf( seedredux.args.slug + "&tab=" ) >= 0 ) {
                    e.preventDefault();
                    var url = $( this ).attr( 'href' ).split( '&tab=' );
                    $( '#' + url[1] + '_section_group_li_a' ).click();
                    $( this ).parents( 'ul:first' ).find( '.current' ).removeClass( 'current' );
                    $( this ).addClass( 'current' );
                    $( this ).parent().addClass( 'current' );
                    return false;
                }
            }
        );

        // Save button clicked
        $( '.seedredux-action_bar input' ).on(
            'click', function( e ) {
                if ( $( this ).attr( 'name' ) == seedredux.args.opt_name + '[defaults]' ) {
                    // Defaults button clicked
                    if ( !confirm( seedredux.args.reset_confirm ) ) {
                        return false;
                    }
                } else if ( $( this ).attr( 'name' ) == seedredux.args.opt_name + '[defaults-section]' ) {
                    // Default section clicked
                    if ( !confirm( seedredux.args.reset_section_confirm ) ) {
                        return false;
                    }
                }

                window.onbeforeunload = null;

                if ( seedredux.args.ajax_save === true ) {
                    $.seedredux.ajax_save( $( this ) );
                    e.preventDefault();
                }
            }
        );
        //
        //// Default button clicked
        //$( 'input[name="' + seedredux.args.opt_name + '[defaults]"]' ).click(
        //    function() {
        //        if ( !confirm( seedredux.args.reset_confirm ) ) {
        //            return false;
        //        }
        //        window.onbeforeunload = null;
        //    }
        //);


        //$( 'input[name="' + seedredux.args.opt_name + '[defaults-section]"]' ).click(
        //    function() {
        //        if ( !confirm( seedredux.args.reset_section_confirm ) ) {
        //            return false;
        //        }
        //
        //        window.onbeforeunload = null;
        //    }
        //);
        //$( '.seedredux-save' ).click(
        //    function() {
        //        window.onbeforeunload = null;
        //    }
        //);

        $( '.expand_options' ).click(
            function( e ) {

                e.preventDefault();

                var container = $( '.seedredux-container' );
                if ( $( container ).hasClass( 'fully-expanded' ) ) {
                    $( container ).removeClass( 'fully-expanded' );

                    var tab = $.cookie( "seedredux_current_tab" );

                    $( '.seedredux-container:first' ).find( '#' + tab + '_section_group' ).fadeIn(
                        200, function() {
                            if ( $( '.seedredux-container:first' ).find( '#seedredux-footer' ).length !== 0 ) {
                                $.seedredux.stickyInfo(); // race condition fix
                            }
                            $.seedredux.initFields();
                        }
                    );
                }

                $.seedredux.expandOptions( $( this ).parents( '.seedredux-container:first' ) );

                return false;
            }
        );

        if ( $( '.saved_notice' ).is( ':visible' ) ) {
            $( '.saved_notice' ).slideDown();
        }

        $( document.body ).on(
            'change', '.seedredux-field input, .seedredux-field textarea, .seedredux-field select', function() {
                if ( !$( this ).hasClass( 'noUpdate' ) ) {
                    seedredux_change( $( this ) );
                }
            }
        );

        var stickyHeight = $( '#seedredux-footer' ).height();

        $( '#seedredux-sticky-padder' ).css(
            {
                height: stickyHeight
            }
        );
        $( '#seedredux-footer-sticky' ).removeClass( 'hide' );

        if ( $( '#seedredux-footer' ).length !== 0 ) {
            $( window ).scroll(
                function() {
                    $.seedredux.stickyInfo();
                }
            );

            $( window ).resize(
                function() {
                    $.seedredux.stickyInfo();
                }
            );
        }

        $( '.saved_notice' ).delay( 4000 ).slideUp();


    };

    $.seedredux.hideFields = function() {
        $( "label[for='seedredux_hide_field']" ).each(
            function( idx, val ) {
                var tr = $( this ).parent().parent();
                $( tr ).addClass( 'hidden' );
            }
        );
    };

    $.seedredux.checkRequired = function() {
        $.seedredux.required();

        $( "body" ).on(
            'change',
            '.seedredux-main select, .seedredux-main radio, .seedredux-main input[type=checkbox], .seedredux-main input[type=hidden]',
            function( e ) {
                $.seedredux.check_dependencies( this );
            }
        );

        $( "body" ).on(
            'check_dependencies', function( e, variable ) {
                $.seedredux.check_dependencies( variable );
            }
        );

        $( 'td > fieldset:empty,td > div:empty' ).parent().parent().hide();
    };

    $.seedredux.initQtip = function() {
        if ( $().qtip ) {
            // Shadow
            var shadow = '';
            var tip_shadow = seedredux.args.hints.tip_style.shadow;

            if ( tip_shadow === true ) {
                shadow = 'qtip-shadow';
            }

            // Color
            var color = '';
            var tip_color = seedredux.args.hints.tip_style.color;

            if ( tip_color !== '' ) {
                color = 'qtip-' + tip_color;
            }

            // Rounded
            var rounded = '';
            var tip_rounded = seedredux.args.hints.tip_style.rounded;

            if ( tip_rounded === true ) {
                rounded = 'qtip-rounded';
            }

            // Tip style
            var style = '';
            var tip_style = seedredux.args.hints.tip_style.style;

            if ( tip_style !== '' ) {
                style = 'qtip-' + tip_style;
            }

            var classes = shadow + ',' + color + ',' + rounded + ',' + style;
            classes = classes.replace( /,/g, ' ' );

            // Get position data
            var myPos = seedredux.args.hints.tip_position.my;
            var atPos = seedredux.args.hints.tip_position.at;

            // Gotta be lowercase, and in proper format
            myPos = $.seedredux.verifyPos( myPos.toLowerCase(), true );
            atPos = $.seedredux.verifyPos( atPos.toLowerCase(), false );

            // Tooltip trigger action
            var showEvent = seedredux.args.hints.tip_effect.show.event;
            var hideEvent = seedredux.args.hints.tip_effect.hide.event;

            // Tip show effect
            var tipShowEffect = seedredux.args.hints.tip_effect.show.effect;
            var tipShowDuration = seedredux.args.hints.tip_effect.show.duration;

            // Tip hide effect
            var tipHideEffect = seedredux.args.hints.tip_effect.hide.effect;
            var tipHideDuration = seedredux.args.hints.tip_effect.hide.duration;

            $( 'div.seedredux-hint-qtip' ).each(
                function() {
                    $( this ).qtip(
                        {
                            content: {
                                text: $( this ).attr( 'qtip-content' ),
                                title: $( this ).attr( 'qtip-title' )
                            },
                            show: {
                                effect: function() {
                                    switch ( tipShowEffect ) {
                                        case 'slide':
                                            $( this ).slideDown( tipShowDuration );
                                            break;
                                        case 'fade':
                                            $( this ).fadeIn( tipShowDuration );
                                            break;
                                        default:
                                            $( this ).show();
                                            break;
                                    }
                                },
                                event: showEvent,
                            },
                            hide: {
                                effect: function() {
                                    switch ( tipHideEffect ) {
                                        case 'slide':
                                            $( this ).slideUp( tipHideDuration );
                                            break;
                                        case 'fade':
                                            $( this ).fadeOut( tipHideDuration );
                                            break;
                                        default:
                                            $( this ).show( tipHideDuration );
                                            break;
                                    }
                                },
                                event: hideEvent,
                            },
                            style: {
                                classes: classes,
                            },
                            position: {
                                my: myPos,
                                at: atPos,
                            },
                        }
                    );
                }
            );
            // });

            $( 'input[qtip-content]' ).each(
                function() {
                    $( this ).qtip(
                        {
                            content: {
                                text: $( this ).attr( 'qtip-content' ),
                                title: $( this ).attr( 'qtip-title' )
                            },
                            show: 'focus',
                            hide: 'blur',
                            style: classes,
                            position: {
                                my: myPos,
                                at: atPos,
                            },
                        }
                    );
                }
            );
        }
    };

    $.seedredux.tabCheck = function() {
        $( '.seedredux-group-tab-link-a' ).click(
            function() {
                var link = $( this );
                if ( link.parent().hasClass( 'empty_section' ) && link.parent().hasClass( 'hasSubSections' ) ) {
                    var elements = $( this ).closest( 'ul' ).find( '.seedredux-group-tab-link-a' );
                    var index = elements.index( this );
                    link = elements.slice( index + 1, index + 2 );
                }
                var el = link.parents( '.seedredux-container:first' );
                var relid = link.data( 'rel' ); // The group ID of interest
                var oldid = el.find( '.seedredux-group-tab-link-li.active:first .seedredux-group-tab-link-a' ).data( 'rel' );

                //console.log('id: '+relid+' oldid: '+oldid);

                if ( oldid === relid ) {
                    return;
                }

                $( '#currentSection' ).val( relid );
                if ( !link.parents( '.postbox-container:first' ).length ) {
                    // Set the proper page cookie
                    $.cookie(
                        'seedredux_current_tab', relid, {
                            expires: 7,
                            path: '/'
                        }
                    );
                }

                if ( el.find( '#' + relid + '_section_group_li' ).parents( '.seedredux-group-tab-link-li' ).length ) {
                    var parentID = el.find( '#' + relid + '_section_group_li' ).parents( '.seedredux-group-tab-link-li' ).attr( 'id' ).split( '_' );
                    parentID = parentID[0];
                }

                el.find( '#toplevel_page_' + seedredux.args.slug + ' .wp-submenu a.current' ).removeClass( 'current' );
                el.find( '#toplevel_page_' + seedredux.args.slug + ' .wp-submenu li.current' ).removeClass( 'current' );

                el.find( '#toplevel_page_' + seedredux.args.slug + ' .wp-submenu a' ).each(
                    function() {
                        var url = $( this ).attr( 'href' ).split( '&tab=' );
                        if ( url[1] == relid || url[1] == parentID ) {
                            $( this ).addClass( 'current' );
                            $( this ).parent().addClass( 'current' );
                        }
                    }
                );

                if ( el.find( '#' + oldid + '_section_group_li' ).find( '#' + oldid + '_section_group_li' ).length ) {
                    //console.log('RELID is child of oldid');
                    el.find( '#' + oldid + '_section_group_li' ).addClass( 'activeChild' );
                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' );
                } else if ( el.find( '#' + relid + '_section_group_li' ).parents( '#' + oldid + '_section_group_li' ).length || el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).find( '#' + relid + '_section_group_li' ).length ) {
                    //console.log('RELID is sibling or child of OLDID');
                    if ( el.find( '#' + relid + '_section_group_li' ).parents( '#' + oldid + '_section_group_li' ).length ) {
                        //console.log('child of oldid');
                        el.find( '#' + oldid + '_section_group_li' ).addClass( 'activeChild' ).removeClass( 'active' );
                    } else {
                        //console.log('sibling');
                        el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                        el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                    }
                    el.find( '#' + relid + '_section_group_li' ).removeClass( 'activeChild' ).addClass( 'active' );
                } else {
                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();

                    if ( el.find( '#' + oldid + '_section_group_li' ).find( 'ul.subsection' ).length ) {
                        //console.log('oldid is parent');
                        //console.log('#' + relid + '_section_group_li');

                        el.find( '#' + oldid + '_section_group_li' ).find( 'ul.subsection' ).slideUp(
                            'fast', function() {
                                el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' ).removeClass( 'activeChild' );
                            }
                        );
                        var newParent = el.find( '#' + relid + '_section_group_li' ).parents( '.hasSubSections:first' );

                        if ( newParent.length > 0 ) {
                            el.find( '#' + relid + '_section_group_li' ).removeClass( 'active' );
                            relid = newParent.find( '.seedredux-group-tab-link-a:first' ).data( 'rel' );
                            //console.log(relid);
                            if ( newParent.hasClass( 'empty_section' ) ) {
                                newParent.find( '.subsection li:first' ).addClass( 'active' );
                                el.find( '#' + relid + '_section_group_li' ).removeClass( 'active' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                                newParent = newParent.find( '.subsection li:first' );
                                relid = newParent.find( '.seedredux-group-tab-link-a:first' ).data( 'rel' );
                                //console.log('Empty section, do the next one?');
                            } else {
                                el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                            }
                        }
                    } else if ( el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).length ) {
                        //console.log('oldid is a child');
                        if ( !el.find( '#' + oldid + '_section_group_li' ).parents( '#' + relid + '_section_group_li' ).length ) {
                            //console.log('oldid is child, but not of relid');
                            el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).slideUp(
                                'fast', function() {
                                    el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                                    el.find( '#' + oldid + '_section_group_li' ).parents( '.seedredux-group-tab-link-li' ).removeClass( 'active' ).removeClass( 'activeChild' );
                                    el.find( '#' + relid + '_section_group_li' ).parents( '.seedredux-group-tab-link-li' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                                }
                            );
                        } else {
                            //console.log('oldid is child, but not of relid2');
                            el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                        }
                    } else {
                        //console.log('Normal remove active from child');
                        el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                        if ( el.find( '#' + relid + '_section_group_li' ).parents( '.seedredux-group-tab-link-li' ).length ) {
                            //console.log('here');
                            el.find( '#' + relid + '_section_group_li' ).parents( '.seedredux-group-tab-link-li' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                            el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                        }
                    }
                }

                // Show the group
                el.find( '#' + oldid + '_section_group' ).hide();

                el.find( '#' + relid + '_section_group' ).fadeIn(
                    200, function() {
                        if ( el.find( '#seedredux-footer' ).length !== 0 ) {
                            $.seedredux.stickyInfo(); // race condition fix
                        }
                        $.seedredux.initFields();
                    }
                );
                $( '#toplevel_page_' + seedredux.args.slug ).find( '.current' ).removeClass( 'current' );

            }
        );

        var tab = decodeURI( (new RegExp( 'tab' + '=' + '(.+?)(&|$)' ).exec( location.search ) || [, ''])[1] );

        if ( tab !== "" ) {
            if ( $.cookie( "seedredux_current_tab_get" ) !== tab ) {
                $.cookie(
                    'seedredux_current_tab', tab, {
                        expires: 7,
                        path: '/'
                    }
                );
                $.cookie(
                    'seedredux_current_tab_get', tab, {
                        expires: 7,
                        path: '/'
                    }
                );

                $( '#' + tab + '_section_group_li' ).click();
            }
        } else if ( $.cookie( 'seedredux_current_tab_get' ) !== "" ) {
            $.removeCookie( 'seedredux_current_tab_get' );
        }

        var sTab = $( '#' + $.cookie( "seedredux_current_tab" ) + '_section_group_li_a' );

        // Tab the first item or the saved one
        if ( $.cookie( "seedredux_current_tab" ) === null || typeof ($.cookie( "seedredux_current_tab" )) === "undefined" || sTab.length === 0 ) {
            $( '.seedredux-container' ).find( '.seedredux-group-tab-link-a:first' ).click();
        } else {
            sTab.click();
        }

    };

    $.seedredux.initFields = function() {
        $( ".seedredux-group-tab:visible" ).find( ".seedredux-field-init:visible" ).each(
            function() {
                var type = $( this ).attr( 'data-type' );
                //console.log(type);
                if ( typeof seedredux.field_objects != 'undefined' && seedredux.field_objects[type] && seedredux.field_objects[type] ) {
                    seedredux.field_objects[type].init();
                }
                if ( $( this ).hasClass( 'seedredux_remove_th' ) ) {
                    var tr = $( this ).parents( 'tr:first' );
                    var th = tr.find( 'th:first' );
                    $( this ).prepend( th.html() );
                    $( this ).find( '.seedredux_field_th' ).css( 'padding', '0 0 10px 0' );
                    $( this ).parent().attr( 'colspan', '2' );
                    th.remove();
                }
            }
        );
    };

    $.seedredux.notices = function() {
        if ( seedredux.errors && seedredux.errors.errors ) {
            $.each(
                seedredux.errors.errors, function( sectionID, sectionArray ) {
                    $.each(
                        sectionArray.errors, function( key, value ) {
                            $( "#" + seedredux.args.opt_name + '-' + value.id ).addClass( "seedredux-field-error" );
                            if ( $( "#" + seedredux.args.opt_name + '-' + value.id ).parent().find( '.seedredux-th-error' ).length === 0 ) {
                                $( "#" + seedredux.args.opt_name + '-' + value.id ).append( '<div class="seedredux-th-error">' + value.msg + '</div>' );
                            } else {
                                $( "#" + seedredux.args.opt_name + '-' + value.id ).parent().find( '.seedredux-th-error' ).html(value.msg).css('display', 'block');
                            }
                        }
                    );
                }
            );
            $( '.seedredux-container' ).each(
                function() {
                    var container = $( this );
                    // Ajax cleanup
                    container.find( '.seedredux-menu-error' ).remove();
                    var totalErrors = container.find( '.seedredux-field-error' ).length;
                    if ( totalErrors > 0 ) {
                        container.find( ".seedredux-field-errors span" ).text( totalErrors );
                        container.find( ".seedredux-field-errors" ).slideDown();
                        container.find( '.seedredux-group-tab' ).each(
                            function() {
                                var total = $( this ).find( '.seedredux-field-error' ).length;
                                if ( total > 0 ) {
                                    var sectionID = $( this ).attr( 'id' ).split( '_' );
                                    sectionID = sectionID[0];
                                    container.find( '.seedredux-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="seedredux-menu-error">' + total + '</span>' );
                                    container.find( '.seedredux-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( "hasError" );
                                    var subParent = container.find( '.seedredux-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );
                                    if ( subParent ) {
                                        subParent.find( '.seedredux-group-tab-link-a:first' ).addClass( 'hasError' );
                                    }
                                }
                            }
                        );
                    }
                }
            );
        }
        if ( seedredux.warnings && seedredux.warnings.warnings ) {
            $.each(
                seedredux.warnings.warnings, function( sectionID, sectionArray ) {
                    $.each(
                        sectionArray.warnings, function( key, value ) {
                            $( "#" + seedredux.args.opt_name + '-' + value.id ).addClass( "seedredux-field-warning" );
                            if ( $( "#" + seedredux.args.opt_name + '-' + value.id ).parent().find( '.seedredux-th-warning' ).length === 0 ) {
                                $( "#" + seedredux.args.opt_name + '-' + value.id ).append( '<div class="seedredux-th-warning">' + value.msg + '</div>' );
                            } else {
                                $( "#" + seedredux.args.opt_name + '-' + value.id ).parent().find( '.seedredux-th-warning' ).html(value.msg ).css('display', 'block');
                            }
                        }
                    );
                }
            );
            $( '.seedredux-container' ).each(
                function() {
                    var container = $( this );
                    // Ajax cleanup
                    container.find( '.seedredux-menu-warning' ).remove();
                    var totalWarnings = container.find( '.seedredux-field-warning' ).length;
                    if ( totalWarnings > 0 ) {
                        container.find( ".seedredux-field-warnings span" ).text( totalWarnings );
                        container.find( ".seedredux-field-warnings" ).slideDown();
                        container.find( '.seedredux-group-tab' ).each(
                            function() {
                                var total = $( this ).find( '.seedredux-field-warning' ).length;
                                if ( total > 0 ) {
                                    var sectionID = $( this ).attr( 'id' ).split( '_' );
                                    sectionID = sectionID[0];
                                    container.find( '.seedredux-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="seedredux-menu-warning">' + total + '</span>' );
                                    container.find( '.seedredux-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( "hasWarning" );
                                    var subParent = container.find( '.seedredux-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );
                                    if ( subParent ) {
                                        subParent.find( '.seedredux-group-tab-link-a:first' ).addClass( 'hasWarning' );
                                    }
                                }
                            }
                        );
                    }
                }
            );
        }
    };

    $.seedredux.tabControl = function() {
        $( '.seedredux-section-tabs div' ).hide();
        $( '.seedredux-section-tabs div:first' ).show();
        $( '.seedredux-section-tabs ul li:first' ).addClass( 'active' );

        $( '.seedredux-section-tabs ul li a' ).click(
            function() {
                $( '.seedredux-section-tabs ul li' ).removeClass( 'active' );
                $( this ).parent().addClass( 'active' );

                var currentTab = $( this ).attr( 'href' );

                $( '.seedredux-section-tabs div' ).hide();
                $( currentTab ).fadeIn(
                    'medium', function() {
                        $.seedredux.initFields();
                    }
                );

                return false;
            }
        );
    };

    $.seedredux.devFunctions = function() {
        $( '#consolePrintObject' ).on(
            'click', function( e ) {
                e.preventDefault();
                console.log( $.parseJSON( $( "#seedredux-object-json" ).html() ) );
            }
        );

        if ( typeof jsonView === 'function' ) {
            jsonView( '#seedredux-object-json', '#seedredux-object-browser' );
        }
    };

    $.seedredux.required = function() {

        // Hide the fold elements on load ,
        // It's better to do this by PHP but there is no filter in tr tag , so is not possible
        // we going to move each attributes we may need for folding to tr tag
        $.each(
            seedredux.folds, function( i, v ) {
                var fieldset = $( '#' + seedredux.args.opt_name + '-' + i );

                fieldset.parents( 'tr:first' ).addClass( 'fold' );

                if ( v == "hide" ) {
                    fieldset.parents( 'tr:first' ).addClass( 'hide' );

                    if ( fieldset.hasClass( 'seedredux-container-section' ) ) {
                        var div = $( '#section-' + i );

                        if ( div.hasClass( 'seedredux-section-indent-start' ) ) {
                            $( '#section-table-' + i ).hide().addClass( 'hide' );
                            div.hide().addClass( 'hide' );
                        }
                    }

                    if ( fieldset.hasClass( 'seedredux-container-info' ) ) {
                        $( '#info-' + i ).hide().addClass( 'hide' );
                    }

                    if ( fieldset.hasClass( 'seedredux-container-divide' ) ) {
                        $( '#divide-' + i ).hide().addClass( 'hide' );
                    }

                    if ( fieldset.hasClass( 'seedredux-container-raw' ) ) {
                        var rawTable = fieldset.parents().find( 'table#' + seedredux.args.opt_name + '-' + i );
                        rawTable.hide().addClass( 'hide' );
                    }
                }
            }
        );
    };

    $.seedredux.get_container_value = function( id ) {
        var value = $( '#' + seedredux.args.opt_name + '-' + id ).serializeForm();

        if ( value !== null && typeof value === 'object' && value.hasOwnProperty( seedredux.args.opt_name ) ) {
            value = value[seedredux.args.opt_name][id];
        }
        if ( $( '#' + seedredux.args.opt_name + '-' + id ).hasClass( 'seedredux-container-media' ) ) {
            value = value.url;
        }
        return value;
    };

    $.seedredux.check_dependencies = function( variable ) {
        if ( seedredux.required === null ) {
            return;
        }
        var current = $( variable ),
            id = current.parents( '.seedredux-field:first' ).data( 'id' );
        if ( !seedredux.required.hasOwnProperty( id ) ) {
            return;
        }

        var container = current.parents( '.seedredux-field-container:first' ),
            is_hidden = container.parents( 'tr:first' ).hasClass( '.hide' ),
            hadSections = false;
        $.each(
            seedredux.required[id], function( child, dependents ) {

                var current = $( this ),
                    show = false,
                    childFieldset = $( '#' + seedredux.args.opt_name + '-' + child ),
                    tr = childFieldset.parents( 'tr:first' );

                if ( !is_hidden ) {
                    show = $.seedredux.check_parents_dependencies( child );
                }

                if ( show === true ) {
                    // Shim for sections
                    if ( childFieldset.hasClass( 'seedredux-container-section' ) ) {
                        var div = $( '#section-' + child );

                        if ( div.hasClass( 'seedredux-section-indent-start' ) && div.hasClass( 'hide' ) ) {
                            $( '#section-table-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                            div.fadeIn( 300 ).removeClass( 'hide' );
                        }
                    }

                    if ( childFieldset.hasClass( 'seedredux-container-info' ) ) {
                        $( '#info-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                    }

                    if ( childFieldset.hasClass( 'seedredux-container-divide' ) ) {
                        $( '#divide-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                    }

                    if ( childFieldset.hasClass( 'seedredux-container-raw' ) ) {
                        var rawTable = childFieldset.parents().find( 'table#' + seedredux.args.opt_name + '-' + child );
                        rawTable.fadeIn( 300 ).removeClass( 'hide' );
                    }

                    tr.fadeIn(
                        300, function() {
                            $( this ).removeClass( 'hide' );
                            if ( seedredux.required.hasOwnProperty( child ) ) {
                                $.seedredux.check_dependencies( $( '#' + seedredux.args.opt_name + '-' + child ).children().first() );
                            }
                            $.seedredux.initFields();
                        }
                    );
                    if ( childFieldset.hasClass( 'seedredux-container-section' ) || childFieldset.hasClass( 'seedredux-container-info' ) ) {
                        tr.css( {display: 'none'} );
                    }
                } else if ( show === false ) {
                    tr.fadeOut(
                        100, function() {
                            $( this ).addClass( 'hide' );
                            if ( seedredux.required.hasOwnProperty( child ) ) {
                                //console.log('Now check, reverse: '+child);
                                $.seedredux.required_recursive_hide( child );
                            }
                        }
                    );
                }

                current.find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
            }
        );
    };

    $.seedredux.required_recursive_hide = function( id ) {
        var toFade = $( '#' + seedredux.args.opt_name + '-' + id ).parents( 'tr:first' );

        toFade.fadeOut(
            50, function() {
                $( this ).addClass( 'hide' );

                if ( $( '#' + seedredux.args.opt_name + '-' + id ).hasClass( 'seedredux-container-section' ) ) {
                    var div = $( '#section-' + id );
                    if ( div.hasClass( 'seedredux-section-indent-start' ) ) {
                        $( '#section-table-' + id ).fadeOut( 50 ).addClass( 'hide' );
                        div.fadeOut( 50 ).addClass( 'hide' );
                    }
                }

                if ( $( '#' + seedredux.args.opt_name + '-' + id ).hasClass( 'seedredux-container-info' ) ) {
                    $( '#info-' + id ).fadeOut( 50 ).addClass( 'hide' );
                }

                if ( $( '#' + seedredux.args.opt_name + '-' + id ).hasClass( 'seedredux-container-divide' ) ) {
                    $( '#divide-' + id ).fadeOut( 50 ).addClass( 'hide' );
                }

                if ( $( '#' + seedredux.args.opt_name + '-' + id ).hasClass( 'seedredux-container-raw' ) ) {
                    var rawTable = $( '#' + seedredux.args.opt_name + '-' + id ).parents().find( 'table#' + seedredux.args.opt_name + '-' + id );
                    rawTable.fadeOut( 50 ).addClass( 'hide' );
                }

                if ( seedredux.required.hasOwnProperty( id ) ) {
                    $.each(
                        seedredux.required[id], function( child ) {
                            $.seedredux.required_recursive_hide( child );
                        }
                    );
                }
            }
        );
    };

    $.seedredux.check_parents_dependencies = function( id ) {
        var show = "";

        if ( seedredux.required_child.hasOwnProperty( id ) ) {
            $.each(
                seedredux.required_child[id], function( i, parentData ) {
                    if ( $( '#' + seedredux.args.opt_name + '-' + parentData.parent ).parents( 'tr:first' ).hasClass( '.hide' ) ) {
                        show = false;
                    } else {
                        if ( show !== false ) {
                            var parentValue = $.seedredux.get_container_value( parentData.parent );
                            show = $.seedredux.check_dependencies_visibility( parentValue, parentData );
                        }
                    }
                }
            );
        } else {
            show = true;
        }
        return show;
    };

    $.seedredux.check_dependencies_visibility = function( parentValue, data ) {
        var show = false,
            checkValue_array,
            checkValue = data.checkValue,
            operation = data.operation;

        switch ( operation ) {
            case '=':
            case 'equals':
                if ( $.isArray( parentValue ) ) {
                    $( parentValue[0] ).each(
                        function( idx, val ) {
                            if ( $.isArray( checkValue ) ) {
                                $( checkValue ).each(
                                    function( i, v ) {
                                        if ( val == v ) {
                                            show = true;
                                            return true;
                                        }
                                    }
                                );
                            } else {
                                if ( val == checkValue ) {
                                    show = true;
                                    return true;
                                }
                            }
                        }
                    );
                } else {
                    if ( $.isArray( checkValue ) ) {
                        $( checkValue ).each(
                            function( i, v ) {
                                if ( parentValue == v ) {
                                    show = true;
                                }
                            }
                        );
                    } else {
                        if ( parentValue == checkValue ) {
                            show = true;
                        }
                    }
                }
                break;

            case '!=':
            case 'not':
                if ( $.isArray( parentValue ) ) {
                    $( parentValue[0] ).each(
                        function( idx, val ) {
                            if ( $.isArray( checkValue ) ) {
                                $( checkValue ).each(
                                    function( i, v ) {
                                        if ( val != v ) {
                                            show = true;
                                            return true;
                                        }
                                    }
                                );
                            } else {
                                if ( val != checkValue ) {
                                    show = true;
                                    return true;
                                }
                            }
                        }
                    );
                } else {
                    if ( $.isArray( checkValue ) ) {
                        $( checkValue ).each(
                            function( i, v ) {
                                if ( parentValue != v ) {
                                    show = true;
                                }
                            }
                        );
                    } else {
                        if ( parentValue != checkValue ) {
                            show = true;
                        }
                    }
                }

                //                //if value was array
                //                if ( $.isArray( checkValue ) ) {
                //                    if ( $.inArray( parentValue, checkValue ) == -1 ) {
                //                        show = true;
                //                    }
                //                } else {
                //                    if ( parentValue != checkValue ) {
                //                        show = true;
                //                    } else if ( $.isArray( parentValue ) ) {
                //                        if ( $.inArray( checkValue, parentValue ) == -1 ) {
                //                            show = true;
                //                        }
                //                    }
                //                }
                break;

            case '>':
            case 'greater':
            case 'is_larger':
                if ( parseFloat( parentValue ) > parseFloat( checkValue ) ) {
                    show = true;
                }
                break;

            case '>=':
            case 'greater_equal':
            case 'is_larger_equal':
                if ( parseFloat( parentValue ) >= parseFloat( checkValue ) ) {
                    show = true;
                }
                break;

            case '<':
            case 'less':
            case 'is_smaller':
                if ( parseFloat( parentValue ) < parseFloat( checkValue ) ) {
                    show = true;
                }
                break;

            case '<=':
            case 'less_equal':
            case 'is_smaller_equal':
                if ( parseFloat( parentValue ) <= parseFloat( checkValue ) ) {
                    show = true;
                }
                break;

            case 'contains':
                if ( $.isArray( checkValue ) ) {
                    $( checkValue ).each(
                        function( idx, val ) {
                            if ( parentValue.toString().indexOf( val ) !== -1 ) {
                                show = true;
                            }
                        }
                    );
                } else {
                    if ( parentValue.toString().indexOf( checkValue ) !== -1 ) {
                        show = true;
                    }
                }
                break;

            case 'doesnt_contain':
            case 'not_contain':
                if ( $.isArray( checkValue ) ) {
                    $( checkValue ).each(
                        function( idx, val ) {
                            if ( parentValue.toString().indexOf( val ) === -1 ) {
                                show = true;
                            }
                        }
                    );
                } else {
                    if ( parentValue.toString().indexOf( checkValue ) === -1 ) {
                        show = true;
                    }
                }
                break;

            case 'is_empty_or':
                if ( parentValue === "" || parentValue == checkValue ) {
                    show = true;
                }
                break;

            case 'not_empty_and':
                if ( parentValue !== "" && parentValue != checkValue ) {
                    show = true;
                }
                break;

            case 'is_empty':
            case 'empty':
            case '!isset':
                if ( !parentValue || parentValue === "" || parentValue === null ) {
                    show = true;
                }
                break;

            case 'not_empty':
            case '!empty':
            case 'isset':
                if ( parentValue && parentValue !== "" && parentValue !== null ) {
                    show = true;
                }
                break;
        }
        return show;

    };

    $.seedredux.verifyPos = function( s, b ) {

        // trim off spaces
        s = s.replace( /^\s+|\s+$/gm, '' );

        // position value is blank, set the default
        if ( s === '' || s.search( ' ' ) == -1 ) {
            if ( b === true ) {
                return 'top left';
            } else {
                return 'bottom right';
            }
        }

        // split string into array
        var split = s.split( ' ' );

        // Evaluate first string.  Must be top, center, or bottom
        var paramOne = b ? 'top' : 'bottom';
        if ( split[0] == 'top' || split[0] == 'center' || split[0] == 'bottom' ) {
            paramOne = split[0];
        }

        // Evaluate second string.  Must be left, center, or right.
        var paramTwo = b ? 'left' : 'right';
        if ( split[1] == 'left' || split[1] == 'center' || split[1] == 'right' ) {
            paramTwo = split[1];
        }

        return paramOne + ' ' + paramTwo;
    };

    $.seedredux.stickyInfo = function() {
        var stickyWidth = $( '.seedredux-main' ).innerWidth() - 20;

        if ( !$( '#info_bar' ).isOnScreen() && !$( '#seedredux-footer-sticky' ).isOnScreen() ) {
            $( '#seedredux-footer' ).css(
                {
                    position: 'fixed',
                    bottom: '0',
                    width: stickyWidth,
                    right: 21
                }
            );
            $( '#seedredux-footer' ).addClass( 'sticky-footer-fixed' );
            $( '.seedredux-save-warn' ).css( 'left', $( '#seedredux-sticky' ).offset().left );
            $( '#seedredux-sticky-padder' ).show();
        } else {
            $( '#seedredux-footer' ).css(
                {
                    background: '#eee',
                    position: 'inherit',
                    bottom: 'inherit',
                    width: 'inherit'
                }
            );
            $( '#seedredux-sticky-padder' ).hide();
            $( '#seedredux-footer' ).removeClass( 'sticky-footer-fixed' );
        }
        if ( !$( '#info_bar' ).isOnScreen() ) {
            $( '#seedredux-sticky' ).addClass( 'sticky-save-warn' );
        } else {
            $( '#seedredux-sticky' ).removeClass( 'sticky-save-warn' );
        }
    };

    $.seedredux.expandOptions = function( parent ) {
        var trigger = parent.find( '.expand_options' );
        var width = parent.find( '.seedredux-sidebar' ).width() - 1;
        var id = $( '.seedredux-group-menu .active a' ).data( 'rel' ) + '_section_group';

        if ( trigger.hasClass( 'expanded' ) ) {
            trigger.removeClass( 'expanded' );
            parent.find( '.seedredux-main' ).removeClass( 'expand' );

            parent.find( '.seedredux-sidebar' ).stop().animate(
                {
                    'margin-left': '0px'
                }, 500
            );

            parent.find( '.seedredux-main' ).stop().animate(
                {
                    'margin-left': width
                }, 500, function() {
                    parent.find( '.seedredux-main' ).attr( 'style', '' );
                }
            );

            parent.find( '.seedredux-group-tab' ).each(
                function() {
                    if ( $( this ).attr( 'id' ) !== id ) {
                        $( this ).fadeOut( 'fast' );
                    }
                }
            );
            // Show the only active one
        } else {
            trigger.addClass( 'expanded' );
            parent.find( '.seedredux-main' ).addClass( 'expand' );

            parent.find( '.seedredux-sidebar' ).stop().animate(
                {
                    'margin-left': -width - 113
                }, 500
            );

            parent.find( '.seedredux-main' ).stop().animate(
                {
                    'margin-left': '-1px'
                }, 500
            );

            parent.find( '.seedredux-group-tab' ).fadeIn(
                'medium', function() {
                    $.seedredux.initFields();
                }
            );
        }
        return false;
    };


    $.seedredux.scaleToRatio = function( el, maxHeight, maxWidth ) {
        var ratio = 0;  // Used for aspect ratio

        var width = el.attr( 'data-width' );
        if ( !width ) {
            width = el.width();
            el.attr( 'data-width', width );
        }
        var height = el.attr( 'data-height' );
        var eHeight = el.height();
        if ( !height || eHeight > height ) {
            height = eHeight;
            el.attr( 'data-height', height );
            el.css( "width", 'auto' );
            el.attr( 'data-width', el.width() );
            width = el.width();
        }


        // Check if the current width is larger than the max
        if ( width > maxWidth ) {
            ratio = maxWidth / width;   // get ratio for scaling image
            el.css( "width", maxWidth ); // Set new width
            el.css( "height", height * ratio );  // Scale height based on ratio
            height = height * ratio;    // Reset height to match scaled image
            width = width * ratio;    // Reset width to match scaled image

        } else {
            el.css( "width", 'auto' );   // Set new height

        }

        // Check if current height is larger than max
        if ( height > maxHeight ) {
            ratio = maxHeight / height; // get ratio for scaling image
            el.css( "height", maxHeight );   // Set new height
            el.css( "width", width * ratio );    // Scale width based on ratio
            width = width * ratio;    // Reset width to match scaled image
            height = height * ratio;    // Reset height to match scaled image


        } else {
            el.css( "height", 'auto' );   // Set new height

        }

        var test = ($( document.getElementById( 'seedredux-header' ) ).height() - el.height()) / 2;
        if ( test > 0 ) {
            el.css( "margin-top", test );
        } else {
            el.css( "margin-top", 0 );
        }

        if ( $( '#seedredux-header .seedredux_field_search' ) ) {
            $( '#seedredux-header .seedredux_field_search' ).css( 'right', ($( el ).width() + 20) );
        }


    };
    $.seedredux.resizeAds = function() {
        var el = $( '#seedredux-header' );
        var rAds = el.find( '.rAds' );

        var maxHeight = el.height();
        var maxWidth = el.width() - el.find( '.display_header' ).width() - 30;
        $( rAds ).find( 'video' ).each(
            function() {
                $.seedredux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );
        $( rAds ).find( 'img' ).each(
            function() {
                $.seedredux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );
        $( rAds ).find( 'div' ).each(
            function() {
                $.seedredux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );

        if ( rAds.css( 'left' ) == "-99999px" ) {
            rAds.css( 'display', 'none' ).css( 'left', 'auto' );
        }
        rAds.fadeIn( 'slow' );
    };
    $( document ).ready(
        function() {
            if ( seedredux.rAds ) {
                setTimeout(
                    function() {
                        $( '#seedredux-header' ).append( '<div class="rAds"></div>' );
                        var el = $( '#seedredux-header' );
                        el.css( 'position', 'relative' );

                        el.find( '.rAds' ).attr(
                            'style',
                            'position:absolute; top: 6px; right: 6px; display:block !important;overflow:hidden;'
                        ).css( 'left', '-99999px' );
                        el.find( '.rAds' ).html( seedredux.rAds.replace( /<br\s?\/?>/, '' ) );
                        var rAds = el.find( '.rAds' );

                        var maxHeight = el.height();
                        var maxWidth = el.width() - el.find( '.display_header' ).width() - 30;

                        rAds.find( 'a' ).css( 'float', 'right' ).css( 'line-height', el.height() + 'px' ).css(
                            'margin-left', '5px'
                        );

                        $( document ).ajaxComplete(
                            function() {
                                rAds.find( 'a' ).hide();
                                setTimeout(
                                    function() {
                                        $.seedredux.resizeAds();
                                        rAds.find( 'a' ).fadeIn();
                                    }, 1400
                                );
                                setTimeout(
                                    function() {
                                        $.seedredux.resizeAds();

                                    }, 1500
                                );
                                $( document ).unbind( 'ajaxComplete' );
                            }
                        );

                        $( window ).resize(
                            function() {
                                $.seedredux.resizeAds();
                            }
                        );
                    }, 400
                );

            }
        }
    );
})( jQuery );

jQuery.noConflict();

var confirmOnPageExit = function( e ) {
    //return; // ONLY FOR DEBUGGING
    // If we haven't been passed the event get the window.event
    e = e || window.event;

    var message = seedredux.args.save_pending;

    // For IE6-8 and Firefox prior to version 4
    if ( e ) {
        e.returnValue = message;
    }

    window.onbeforeunload = null;

    // For Chrome, Safari, IE8+ and Opera 12+
    return message;
};

function seedredux_change( variable ) {

    jQuery( 'body' ).trigger( 'check_dependencies', variable );

    if ( variable.hasClass( 'compiler' ) ) {
        jQuery( '#seedredux-compiler-hook' ).val( 1 );
    }

    var rContainer = jQuery( variable ).parents( '.seedredux-container:first' );
    var parentID = jQuery( variable ).closest( '.seedredux-group-tab' ).attr( 'id' );
    // Let's count down the errors now. Fancy.  ;)
    var id = parentID.split( '_' );
    id = id[0];

    var th = rContainer.find( '.seedredux-group-tab-link-a[data-key="' + id + '"]' ).parents( '.seedredux-group-tab-link-li:first' );
    var subParent = jQuery( '#' + parentID + '_li' ).parents( '.hasSubSections:first' );

    if ( jQuery( variable ).parents( 'fieldset.seedredux-field:first' ).hasClass( 'seedredux-field-error' ) ) {
        jQuery( variable ).parents( 'fieldset.seedredux-field:first' ).removeClass( 'seedredux-field-error' );
        jQuery( variable ).parent().find( '.seedredux-th-error' ).slideUp();

        var errorCount = (parseInt( rContainer.find( '.seedredux-field-errors span' ).text() ) - 1);

        if ( errorCount <= 0 ) {
            //console.log('HERE');
            jQuery( '#' + parentID + '_li .seedredux-menu-error' ).fadeOut( 'fast' ).remove();
            jQuery( '#' + parentID + '_li .seedredux-group-tab-link-a' ).removeClass( 'hasError' );

            jQuery( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.seedredux-field-errors' ).slideUp();
            jQuery( variable ).parents( '.seedredux-container:first' ).find( '.seedredux-field-errors' ).slideUp();
            jQuery( '#seedredux_metaboxes_errors' ).slideUp();
        } else {

            var errorsLeft = (parseInt( th.find( '.seedredux-menu-error:first' ).text() ) - 1);
            if ( errorsLeft <= 0 ) {
                th.find( '.seedredux-menu-error:first' ).fadeOut().remove();
            } else {
                th.find( '.seedredux-menu-error:first' ).text( errorsLeft );
            }

            rContainer.find( '.seedredux-field-errors span' ).text( errorCount );
        }

        if ( subParent.length !== 0 ) {
            if ( subParent.find( '.seedredux-menu-error' ).length === 0 ) {
                subParent.find( '.hasError' ).removeClass( 'hasError' );
            }
        }
    }
    if ( jQuery( variable ).parents( 'fieldset.seedredux-field:first' ).hasClass( 'seedredux-field-warning' ) ) {
        jQuery( variable ).parents( 'fieldset.seedredux-field:first' ).removeClass( 'seedredux-field-warning' );
        jQuery( variable ).parent().find( '.seedredux-th-warning' ).slideUp();

        var warningCount = (parseInt( rContainer.find( '.seedredux-field-warnings span' ).text() ) - 1);

        if ( warningCount <= 0 ) {
            //console.log('HERE');
            jQuery( '#' + parentID + '_li .seedredux-menu-warning' ).fadeOut( 'fast' ).remove();
            jQuery( '#' + parentID + '_li .seedredux-group-tab-link-a' ).removeClass( 'hasWarning' );

            jQuery( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.seedredux-field-warnings' ).slideUp();
            jQuery( variable ).parents( '.seedredux-container:first' ).find( '.seedredux-field-warnings' ).slideUp();
            jQuery( '#seedredux_metaboxes_warnings' ).slideUp();
        } else {
            // Let's count down the warnings now. Fancy.  ;)

            var warningsLeft = (parseInt( th.find( '.seedredux-menu-warning:first' ).text() ) - 1);
            if ( warningsLeft <= 0 ) {
                th.find( '.seedredux-menu-warning:first' ).fadeOut().remove();
            } else {
                th.find( '.seedredux-menu-warning:first' ).text( warningsLeft );
            }

            rContainer.find( '.seedredux-field-warning span' ).text( warningCount );

        }
        if ( subParent.length !== 0 ) {
            if ( subParent.find( '.seedredux-menu-warning' ).length === 0 ) {
                subParent.find( '.hasWarning' ).removeClass( 'hasWarning' );
            }
        }
    }
    // Don't show the changed value notice while save_notice is visible.
    if ( rContainer.find( '.saved_notice:visible' ).length > 0 ) {
        return;
    }
    if ( !seedredux.args.disable_save_warn ) {
        rContainer.find( '.seedredux-save-warn' ).slideDown();
        window.onbeforeunload = confirmOnPageExit;
    }
}

function colorValidate( field ) {
    var value = jQuery( field ).val();

    var hex = colorNameToHex( value );
    if ( hex !== value.replace( '#', '' ) ) {
        return hex;
    }

    return value;
}

function colorNameToHex( colour ) {
    var tcolour = colour.replace( /^\s\s*/, '' ).replace( /\s\s*$/, '' ).replace( "#", "" );

    var colours = {
        "aliceblue": "#f0f8ff",
        "antiquewhite": "#faebd7",
        "aqua": "#00ffff",
        "aquamarine": "#7fffd4",
        "azure": "#f0ffff",
        "beige": "#f5f5dc",
        "bisque": "#ffe4c4",
        "black": "#000000",
        "blanchedalmond": "#ffebcd",
        "blue": "#0000ff",
        "blueviolet": "#8a2be2",
        "brown": "#a52a2a",
        "burlywood": "#deb887",
        "cadetblue": "#5f9ea0",
        "chartreuse": "#7fff00",
        "chocolate": "#d2691e",
        "coral": "#ff7f50",
        "cornflowerblue": "#6495ed",
        "cornsilk": "#fff8dc",
        "crimson": "#dc143c",
        "cyan": "#00ffff",
        "darkblue": "#00008b",
        "darkcyan": "#008b8b",
        "darkgoldenrod": "#b8860b",
        "darkgray": "#a9a9a9",
        "darkgreen": "#006400",
        "darkkhaki": "#bdb76b",
        "darkmagenta": "#8b008b",
        "darkolivegreen": "#556b2f",
        "darkorange": "#ff8c00",
        "darkorchid": "#9932cc",
        "darkred": "#8b0000",
        "darksalmon": "#e9967a",
        "darkseagreen": "#8fbc8f",
        "darkslateblue": "#483d8b",
        "darkslategray": "#2f4f4f",
        "darkturquoise": "#00ced1",
        "darkviolet": "#9400d3",
        "deeppink": "#ff1493",
        "deepskyblue": "#00bfff",
        "dimgray": "#696969",
        "dodgerblue": "#1e90ff",
        "firebrick": "#b22222",
        "floralwhite": "#fffaf0",
        "forestgreen": "#228b22",
        "fuchsia": "#ff00ff",
        "gainsboro": "#dcdcdc",
        "ghostwhite": "#f8f8ff",
        "gold": "#ffd700",
        "goldenrod": "#daa520",
        "gray": "#808080",
        "green": "#008000",
        "greenyellow": "#adff2f",
        "honeydew": "#f0fff0",
        "hotpink": "#ff69b4",
        "indianred ": "#cd5c5c",
        "indigo ": "#4b0082",
        "ivory": "#fffff0",
        "khaki": "#f0e68c",
        "lavender": "#e6e6fa",
        "lavenderblush": "#fff0f5",
        "lawngreen": "#7cfc00",
        "lemonchiffon": "#fffacd",
        "lightblue": "#add8e6",
        "lightcoral": "#f08080",
        "lightcyan": "#e0ffff",
        "lightgoldenrodyellow": "#fafad2",
        "lightgrey": "#d3d3d3",
        "lightgreen": "#90ee90",
        "lightpink": "#ffb6c1",
        "lightsalmon": "#ffa07a",
        "lightseagreen": "#20b2aa",
        "lightskyblue": "#87cefa",
        "lightslategray": "#778899",
        "lightsteelblue": "#b0c4de",
        "lightyellow": "#ffffe0",
        "lime": "#00ff00",
        "limegreen": "#32cd32",
        "linen": "#faf0e6",
        "magenta": "#ff00ff",
        "maroon": "#800000",
        "mediumaquamarine": "#66cdaa",
        "mediumblue": "#0000cd",
        "mediumorchid": "#ba55d3",
        "mediumpurple": "#9370d8",
        "mediumseagreen": "#3cb371",
        "mediumslateblue": "#7b68ee",
        "mediumspringgreen": "#00fa9a",
        "mediumturquoise": "#48d1cc",
        "mediumvioletred": "#c71585",
        "midnightblue": "#191970",
        "mintcream": "#f5fffa",
        "mistyrose": "#ffe4e1",
        "moccasin": "#ffe4b5",
        "navajowhite": "#ffdead",
        "navy": "#000080",
        "oldlace": "#fdf5e6",
        "olive": "#808000",
        "olivedrab": "#6b8e23",
        "orange": "#ffa500",
        "orangered": "#ff4500",
        "orchid": "#da70d6",
        "palegoldenrod": "#eee8aa",
        "palegreen": "#98fb98",
        "paleturquoise": "#afeeee",
        "palevioletred": "#d87093",
        "papayawhip": "#ffefd5",
        "peachpuff": "#ffdab9",
        "peru": "#cd853f",
        "pink": "#ffc0cb",
        "plum": "#dda0dd",
        "powderblue": "#b0e0e6",
        "purple": "#800080",
        "red": "#ff0000",
        "seedredux": "#01a3e3",
        "rosybrown": "#bc8f8f",
        "royalblue": "#4169e1",
        "saddlebrown": "#8b4513",
        "salmon": "#fa8072",
        "sandybrown": "#f4a460",
        "seagreen": "#2e8b57",
        "seashell": "#fff5ee",
        "sienna": "#a0522d",
        "silver": "#c0c0c0",
        "skyblue": "#87ceeb",
        "slateblue": "#6a5acd",
        "slategray": "#708090",
        "snow": "#fffafa",
        "springgreen": "#00ff7f",
        "steelblue": "#4682b4",
        "tan": "#d2b48c",
        "teal": "#008080",
        "thistle": "#d8bfd8",
        "tomato": "#ff6347",
        "turquoise": "#40e0d0",
        "violet": "#ee82ee",
        "wheat": "#f5deb3",
        "white": "#ffffff",
        "whitesmoke": "#f5f5f5",
        "yellow": "#ffff00",
        "yellowgreen": "#9acd32"
    };

    if ( colours[tcolour.toLowerCase()] !== 'undefined' ) {
        return colours[tcolour.toLowerCase()];
    }

    return colour;
}

