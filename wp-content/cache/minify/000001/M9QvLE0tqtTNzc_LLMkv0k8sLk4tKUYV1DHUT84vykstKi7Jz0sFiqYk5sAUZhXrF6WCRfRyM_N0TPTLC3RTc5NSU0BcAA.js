
/* query-monitor.js */

/* 1   */ /*
/* 2   *| Copyright 2009-2015 John Blackbourn
/* 3   *| 
/* 4   *| This program is free software; you can redistribute it and/or modify
/* 5   *| it under the terms of the GNU General Public License as published by
/* 6   *| the Free Software Foundation; either version 2 of the License, or
/* 7   *| (at your option) any later version.
/* 8   *| 
/* 9   *| This program is distributed in the hope that it will be useful,
/* 10  *| but WITHOUT ANY WARRANTY; without even the implied warranty of
/* 11  *| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
/* 12  *| GNU General Public License for more details.
/* 13  *| 
/* 14  *| */
/* 15  */ 
/* 16  */ var QM_i18n = {
/* 17  */ 
/* 18  */ 	// http://core.trac.wordpress.org/ticket/20491
/* 19  */ 
/* 20  */ 	number_format : function( number, decimals ) {
/* 21  */ 
/* 22  */ 		if ( isNaN( number ) )
/* 23  */ 			return;
/* 24  */ 
/* 25  */ 		if ( !decimals )
/* 26  */ 			decimals = 0;
/* 27  */ 
/* 28  */ 		number = parseFloat( number );
/* 29  */ 
/* 30  */ 		var num_float = number.toFixed( decimals ),
/* 31  */ 			num_int   = Math.floor( number ),
/* 32  */ 			num_str   = num_int.toString(),
/* 33  */ 			fraction  = num_float.substring( num_float.indexOf( '.' ) + 1, num_float.length ),
/* 34  */ 			o = '';
/* 35  */ 
/* 36  */ 		if ( num_str.length > 3 ) {
/* 37  */ 			for ( i = num_str.length; i > 3; i -= 3 )
/* 38  */ 				o = qm_locale.number_format.thousands_sep + num_str.slice( i - 3, i ) + o;
/* 39  */ 			o = num_str.slice( 0, i ) + o;
/* 40  */ 		} else {
/* 41  */ 			o = num_str;
/* 42  */ 		}
/* 43  */ 
/* 44  */ 		if ( decimals )
/* 45  */ 			o = o + qm_locale.number_format.decimal_point + fraction;
/* 46  */ 
/* 47  */ 		return o;
/* 48  */ 
/* 49  */ 	}
/* 50  */ 

/* query-monitor.js */

/* 51  */ };
/* 52  */ 
/* 53  */ jQuery( function($) {
/* 54  */ 
/* 55  */ 	if ( !window.qm )
/* 56  */ 		return;
/* 57  */ 
/* 58  */ 	var is_admin = $('body').hasClass('wp-admin');
/* 59  */ 
/* 60  */ 	$('#qm').removeClass('qm-no-js').addClass('qm-js');
/* 61  */ 
/* 62  */ 	if ( $('#wp-admin-bar-query-monitor').length ) {
/* 63  */ 
/* 64  */ 		var container = document.createDocumentFragment();
/* 65  */ 
/* 66  */ 		$('#wp-admin-bar-query-monitor')
/* 67  */ 			.addClass(qm.menu.top.classname)
/* 68  */ 			.find('a').eq(0)
/* 69  */ 			.html(qm.menu.top.title)
/* 70  */ 		;
/* 71  */ 
/* 72  */ 		$.each( qm.menu.sub, function( i, el ) {
/* 73  */ 
/* 74  */ 			var new_menu = $('#wp-admin-bar-query-monitor-placeholder')
/* 75  */ 				.clone()
/* 76  */ 				.attr('id','wp-admin-bar-'+el.id)
/* 77  */ 			;
/* 78  */ 			new_menu
/* 79  */ 				.find('a').eq(0)
/* 80  */ 				.html(el.title)
/* 81  */ 				.attr('href',el.href)
/* 82  */ 			;
/* 83  */ 
/* 84  */ 			if ( ( typeof el.meta != 'undefined' ) && ( typeof el.meta.classname != 'undefined' ) )
/* 85  */ 				new_menu.addClass(el.meta.classname);
/* 86  */ 
/* 87  */ 			container.appendChild( new_menu.get(0) );
/* 88  */ 
/* 89  */ 		} );
/* 90  */ 
/* 91  */ 		$('#wp-admin-bar-query-monitor ul').append(container);
/* 92  */ 
/* 93  */ 		$('#wp-admin-bar-query-monitor').find('a').on('click',function(e){
/* 94  */ 			if ( is_admin ) {
/* 95  */ 				$('#wpfooter').css('position','relative');
/* 96  */ 			}
/* 97  */ 			if ( window.infinite_scroll && infinite_scroll.contentSelector ) {
/* 98  */ 
/* 99  */ 				$( infinite_scroll.contentSelector ).infinitescroll('pause');
/* 100 */ 

/* query-monitor.js */

/* 101 */ 				if ( window.console ) {
/* 102 */ 					console.log( qm_l10n.infinitescroll_paused );
/* 103 */ 				}
/* 104 */ 
/* 105 */ 			}
/* 106 */ 			$('#qm').show();
/* 107 */ 		});
/* 108 */ 
/* 109 */ 		$('#wp-admin-bar-query-monitor,#wp-admin-bar-query-monitor-default').show();
/* 110 */ 
/* 111 */ 	}
/* 112 */ 
/* 113 */ 	$('#qm').find('select.qm-filter').on('change',function(e){
/* 114 */ 
/* 115 */ 		var filter = $(this).attr('data-filter'),
/* 116 */ 			table  = $(this).closest('table'),
/* 117 */ 			tr     = table.find('tbody tr[data-qm-' + filter + ']'),
/* 118 */ 			val    = $(this).val().replace(/[[\]()'"]/g, "\\$&"),
/* 119 */ 			total  = tr.removeClass('qm-hide-' + filter).length,
/* 120 */ 			hilite = $(this).attr('data-highlight'),
/* 121 */ 			time   = 0;
/* 122 */ 
/* 123 */ 		if ( hilite ) {
/* 124 */ 			table.find('tr').removeClass('qm-highlight');
/* 125 */ 		}
/* 126 */ 
/* 127 */ 		if ( $(this).val() !== '' ) {
/* 128 */ 			if ( hilite ) {
/* 129 */ 				tr.filter('[data-qm-'+hilite+'*="' + val + '"]').addClass('qm-highlight');
/* 130 */ 			}
/* 131 */ 			tr.not('[data-qm-' + filter + '*="' + val + '"]').addClass('qm-hide-' + filter);
/* 132 */ 		}
/* 133 */ 
/* 134 */ 		var matches = tr.filter(':visible');
/* 135 */ 		matches.each(function(i){
/* 136 */ 			var row_time = $(this).attr('data-qm-time');
/* 137 */ 			if ( row_time )
/* 138 */ 				time += parseFloat( row_time );
/* 139 */ 		});
/* 140 */ 		if ( time )
/* 141 */ 			time = QM_i18n.number_format( time, 4 );
/* 142 */ 
/* 143 */ 		var results = table.find('.qm-items-shown').removeClass('qm-hide');
/* 144 */ 		results.find('.qm-items-number').text( QM_i18n.number_format( matches.length, 0 ) );
/* 145 */ 		results.find('.qm-items-time').text(time);
/* 146 */ 
/* 147 */ 		$(this).blur();
/* 148 */ 
/* 149 */ 	});
/* 150 */ 

/* query-monitor.js */

/* 151 */ 	$('#qm').find('.qm-toggle').on('click',function(e){
/* 152 */ 		var el = $(this);
/* 153 */ 		$(this).closest('td').find('.qm-toggled').slideToggle(100,function(){
/* 154 */ 			if ( el.attr('data-off') == el.text() )
/* 155 */ 				el.text(el.attr('data-on'));
/* 156 */ 			else
/* 157 */ 				el.text(el.attr('data-off'));
/* 158 */ 		});
/* 159 */ 		e.preventDefault();
/* 160 */ 	});
/* 161 */ 
/* 162 */ 	$('#qm').find('.qm-highlighter').on('mouseenter',function(e){
/* 163 */ 
/* 164 */ 		var subject = $(this).data('qm-highlight');
/* 165 */ 		var table   = $(this).closest('table');
/* 166 */ 
/* 167 */ 		if ( !subject ) {
/* 168 */ 			return;
/* 169 */ 		}
/* 170 */ 
/* 171 */ 		$(this).addClass('qm-highlight');
/* 172 */ 
/* 173 */ 		$.each( subject.split(' '), function( i, el ){
/* 174 */ 			table.find('tr[data-qm-subject="'+el+'"]').addClass('qm-highlight');
/* 175 */ 		});
/* 176 */ 
/* 177 */ 	}).on('mouseleave',function(e){
/* 178 */ 
/* 179 */ 		$(this).removeClass('qm-highlight');
/* 180 */ 		$(this).closest('table').find('tr').removeClass('qm-highlight');
/* 181 */ 
/* 182 */ 	});
/* 183 */ 
/* 184 */ 	$( document ).ajaxSuccess( function( event, response, options ) {
/* 185 */ 
/* 186 */ 		var errors = response.getResponseHeader( 'X-QM-error-count' );
/* 187 */ 
/* 188 */ 		if ( !errors )
/* 189 */ 			return event;
/* 190 */ 
/* 191 */ 		errors = parseInt( errors, 10 );
/* 192 */ 
/* 193 */ 		for ( var key = 1; key <= errors; key++ ) {
/* 194 */ 
/* 195 */ 			error = $.parseJSON( response.getResponseHeader( 'X-QM-error-' + key ) );
/* 196 */ 
/* 197 */ 			if ( window.console ) {
/* 198 */ 				console.debug( '=== ' + qm_l10n.ajax_error + ' ===' );
/* 199 */ 				console.debug( error );
/* 200 */ 			}

/* query-monitor.js */

/* 201 */ 
/* 202 */ 			if ( $('#wp-admin-bar-query-monitor').length ) {
/* 203 */ 				if ( ! qm.ajax_errors[error.type] ) {
/* 204 */ 					$('#wp-admin-bar-query-monitor')
/* 205 */ 						.addClass('qm-'+error.type)
/* 206 */ 						.find('a').first().append('<span class="ab-label qm-ajax-'+ error.type +'"> &nbsp; AJAX: '+ error.type +'</span>')
/* 207 */ 					;
/* 208 */ 				}
/* 209 */ 			}
/* 210 */ 
/* 211 */ 			qm.ajax_errors[error.type] = true;
/* 212 */ 
/* 213 */ 		}
/* 214 */ 
/* 215 */ 		return event;
/* 216 */ 
/* 217 */ 	} );
/* 218 */ 
/* 219 */ 	if ( is_admin ) {
/* 220 */ 		$('#qm').detach().appendTo('#wpwrap');
/* 221 */ 	}
/* 222 */ 
/* 223 */ 	$('.qm-auth').on('click',function(e){
/* 224 */ 		var action = $(this).data('action');
/* 225 */ 
/* 226 */ 		$.ajax(qm_l10n.ajaxurl,{
/* 227 */ 			type : 'POST',
/* 228 */ 			data : {
/* 229 */ 				action : 'qm_auth_' + action,
/* 230 */ 				nonce  : qm_l10n.auth_nonce[action]
/* 231 */ 			},
/* 232 */ 			success : function(response){
/* 233 */ 				alert( response.data );
/* 234 */ 			},
/* 235 */ 			dataType : 'json',
/* 236 */ 			xhrFields: {
/* 237 */ 				withCredentials: true
/* 238 */ 			}
/* 239 */ 		});
/* 240 */ 
/* 241 */ 		e.preventDefault();
/* 242 */ 	});
/* 243 */ 
/* 244 */ 	$.qm.tableSort({target: $('.qm-sortable'), debug: false});
/* 245 */ 
/* 246 */ } );
/* 247 */ 
/* 248 */ /**
/* 249 *|  * This is a modified version of:
/* 250 *|  * 

/* query-monitor.js */

/* 251 *|  * jQuery table-sort v0.1.1
/* 252 *|  * https://github.com/gajus/table-sort
/* 253 *|  *
/* 254 *|  * Licensed under the BSD.
/* 255 *|  * https://github.com/gajus/table-sort/blob/master/LICENSE
/* 256 *|  *
/* 257 *|  * Author: Gajus Kuizinas <g.kuizinas@anuary.com>
/* 258 *|  */
/* 259 */ (function ($) {
/* 260 */ 	$.qm = $.qm || {};
/* 261 */ 	$.qm.tableSort = function (options) {
/* 262 */ 		var settings = $.extend({
/* 263 */ 			'debug': false
/* 264 */ 		}, options);
/* 265 */ 		
/* 266 */ 		// @param	object	columns	NodeList table colums.
/* 267 */ 		// @param	integer	row_width	defines the number of columns per row.
/* 268 */ 		var table_to_array	= function (columns, row_width) {
/* 269 */ 			if (settings.debug) {
/* 270 */ 				console.time('table to array');
/* 271 */ 			}
/* 272 */ 		
/* 273 */ 			columns = Array.prototype.slice.call(columns, 0);
/* 274 */ 			
/* 275 */ 			var rows      = [];
/* 276 */ 			var row_index = 0;
/* 277 */ 			
/* 278 */ 			for (var i = 0, j = columns.length; i < j; i += row_width) {
/* 279 */ 				var row	= [];
/* 280 */ 				
/* 281 */ 				for (var k = 0, l = row_width; k < l; k++) {
/* 282 */ 					var e = columns[i+k];
/* 283 */ 					
/* 284 */ 					var data = e.dataset.qmSortWeight;
/* 285 */ 					
/* 286 */ 					if (data === undefined) {
/* 287 */ 						data = e.textContent || e.innerText;
/* 288 */ 					}
/* 289 */ 					
/* 290 */ 					var number = parseFloat(data);
/* 291 */ 					
/* 292 */ 					data = isNaN(number) ? data : number;
/* 293 */ 					
/* 294 */ 					row.push(data);
/* 295 */ 				}
/* 296 */ 				
/* 297 */ 				rows.push({index: row_index++, data: row});
/* 298 */ 			}
/* 299 */ 			
/* 300 */ 			if (settings.debug) {

/* query-monitor.js */

/* 301 */ 				console.timeEnd('table to array');
/* 302 */ 			}
/* 303 */ 			
/* 304 */ 			return rows;
/* 305 */ 		};
/* 306 */ 		
/* 307 */ 		if (!settings.target || !settings.target instanceof $) {
/* 308 */ 			throw 'Target is not defined or it is not instance of jQuery.';
/* 309 */ 		}
/* 310 */ 		
/* 311 */ 		settings.target.each(function () {
/* 312 */ 			var table = $(this);
/* 313 */ 			
/* 314 */ 			table.find('.qm-sort').on('click', function (e) {
/* 315 */ 				var desc = $(this).hasClass('qm-sort-desc');
/* 316 */ 				
/* 317 */ 				var index = $(this).closest('th').index();
/* 318 */  
/* 319 */ 				table.find('th').removeClass('qm-sorted-asc qm-sorted-desc');
/* 320 */ 
/* 321 */ 				if ( desc )
/* 322 */ 					$(this).closest('th').addClass('qm-sorted-desc');
/* 323 */ 				else
/* 324 */ 					$(this).closest('th').addClass('qm-sorted-asc');
/* 325 */ 
/* 326 */ 				table.find('tbody:not(.qm-sort-no)').each(function () {
/* 327 */ 					var tbody = $(this);
/* 328 */ 					
/* 329 */ 					var rows = this.rows;
/* 330 */ 					
/* 331 */ 					var anomalies = $(rows).has('[colspan]').detach();
/* 332 */ 					
/* 333 */ 					var columns = this.getElementsByTagName('td');
/* 334 */ 					
/* 335 */ 					if (this.data_matrix === undefined) {
/* 336 */ 						this.data_matrix = table_to_array(columns, $(rows[0]).find('td').length);
/* 337 */ 					}
/* 338 */ 					
/* 339 */ 					var data = this.data_matrix;
/* 340 */ 					
/* 341 */ 					if (settings.debug) {
/* 342 */ 						console.time('sort data');
/* 343 */ 					}
/* 344 */ 					
/* 345 */ 					data.sort(function (a, b) {
/* 346 */ 						if (a.data[index] == b.data[index]) {
/* 347 */ 							return 0;
/* 348 */ 						}
/* 349 */ 						
/* 350 */ 						return (desc ? a.data[index] > b.data[index] : a.data[index] < b.data[index]) ? -1 : 1;

/* query-monitor.js */

/* 351 */ 					});
/* 352 */ 										
/* 353 */ 					if (settings.debug) {
/* 354 */ 						console.timeEnd('sort data');
/* 355 */ 						console.time('build table');
/* 356 */ 					}
/* 357 */ 					
/* 358 */ 					// Will use this to re-attach the tbody object.
/* 359 */ 					var table = tbody.parent();
/* 360 */ 					
/* 361 */ 					// Detach the tbody to prevent unnecassy overhead related
/* 362 */ 					// to the browser environment.
/* 363 */ 					tbody = tbody.detach();
/* 364 */ 					
/* 365 */ 					// Convert NodeList into an array.
/* 366 */ 					rows = Array.prototype.slice.call(rows, 0);
/* 367 */ 					
/* 368 */ 					var last_row = rows[data[data.length-1].index];
/* 369 */ 					
/* 370 */ 					for (var i = 0, j = data.length-1; i < j; i++) {
/* 371 */ 						tbody[0].insertBefore(rows[data[i].index], last_row);
/* 372 */ 						
/* 373 */ 						// Restore the index.
/* 374 */ 						data[i].index = i;
/* 375 */ 					}
/* 376 */ 					
/* 377 */ 					// // Restore the index.
/* 378 */ 					data[data.length-1].index = data.length-1;
/* 379 */ 					
/* 380 */ 					tbody.prepend(anomalies);
/* 381 */ 					
/* 382 */ 					table.append(tbody);
/* 383 */ 					
/* 384 */ 					
/* 385 */ 					if (settings.debug) {
/* 386 */ 						console.timeEnd('build table');
/* 387 */ 					}
/* 388 */ 				});
/* 389 */ 				e.preventDefault();
/* 390 */ 			});
/* 391 */ 		});
/* 392 */ 	};
/* 393 */ })(jQuery);
/* 394 */ 

;
/* remodal.min.js */

/* 1  */ /*
/* 2  *|  *  Remodal - v1.0.3
/* 3  *|  *  Responsive, lightweight, fast, synchronized with CSS animations, fully customizable modal window plugin with declarative configuration and hash tracking.
/* 4  *|  *  http://vodkabears.github.io/remodal/
/* 5  *|  *
/* 6  *|  *  Made by Ilya Makarov
/* 7  *|  *  Under MIT License
/* 8  *|  */
/* 9  */ 
/* 10 */ !function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(c){return b(a,c)}):"object"==typeof exports?b(a,require("jquery")):b(a,a.jQuery||a.Zepto)}(this,function(a,b){"use strict";function c(a){if(w&&"none"===a.css("animation-name")&&"none"===a.css("-webkit-animation-name")&&"none"===a.css("-moz-animation-name")&&"none"===a.css("-o-animation-name")&&"none"===a.css("-ms-animation-name"))return 0;var b,c,d,e,f=a.css("animation-duration")||a.css("-webkit-animation-duration")||a.css("-moz-animation-duration")||a.css("-o-animation-duration")||a.css("-ms-animation-duration")||"0s",g=a.css("animation-delay")||a.css("-webkit-animation-delay")||a.css("-moz-animation-delay")||a.css("-o-animation-delay")||a.css("-ms-animation-delay")||"0s",h=a.css("animation-iteration-count")||a.css("-webkit-animation-iteration-count")||a.css("-moz-animation-iteration-count")||a.css("-o-animation-iteration-count")||a.css("-ms-animation-iteration-count")||"1";for(f=f.split(", "),g=g.split(", "),h=h.split(", "),e=0,c=f.length,b=Number.NEGATIVE_INFINITY;c>e;e++)d=parseFloat(f[e])*parseInt(h[e],10)+parseFloat(g[e]),d>b&&(b=d);return d}function d(){if(b(document.body).height()<=b(window).height())return 0;var a,c,d=document.createElement("div"),e=document.createElement("div");return d.style.visibility="hidden",d.style.width="100px",document.body.appendChild(d),a=d.offsetWidth,d.style.overflow="scroll",e.style.width="100%",d.appendChild(e),c=e.offsetWidth,d.parentNode.removeChild(d),a-c}function e(){var a,c,e=b("html"),f=k("is-locked");e.hasClass(f)||(c=b(document.body),a=parseInt(c.css("padding-right"),10)+d(),c.css("padding-right",a+"px"),e.addClass(f))}function f(){var a,c,e=b("html"),f=k("is-locked");e.hasClass(f)&&(c=b(document.body),a=parseInt(c.css("padding-right"),10)-d(),c.css("padding-right",a+"px"),e.removeClass(f))}function g(a,b,c,d){var e=k("is",b),f=[k("is",u.CLOSING),k("is",u.OPENING),k("is",u.CLOSED),k("is",u.OPENED)].join(" ");a.$bg.removeClass(f).addClass(e),a.$overlay.removeClass(f).addClass(e),a.$wrapper.removeClass(f).addClass(e),a.$modal.removeClass(f).addClass(e),a.state=b,!c&&a.$modal.trigger({type:b,reason:d},[{reason:d}])}function h(a,d,e){var f=0,g=function(a){a.target===this&&f++},h=function(a){a.target===this&&0===--f&&(b.each(["$bg","$overlay","$wrapper","$modal"],function(a,b){e[b].off(r+" "+s)}),d())};b.each(["$bg","$overlay","$wrapper","$modal"],function(a,b){e[b].on(r,g).on(s,h)}),a(),0===c(e.$bg)&&0===c(e.$overlay)&&0===c(e.$wrapper)&&0===c(e.$modal)&&(b.each(["$bg","$overlay","$wrapper","$modal"],function(a,b){e[b].off(r+" "+s)}),d())}function i(a){a.state!==u.CLOSED&&(b.each(["$bg","$overlay","$wrapper","$modal"],function(b,c){a[c].off(r+" "+s)}),a.$bg.removeClass(a.settings.modifier),a.$overlay.removeClass(a.settings.modifier).hide(),a.$wrapper.hide(),f(),g(a,u.CLOSED,!0))}function j(a){var b,c,d,e,f={};for(a=a.replace(/\s*:\s*/g,":").replace(/\s*,\s*/g,","),b=a.split(","),e=0,c=b.length;c>e;e++)b[e]=b[e].split(":"),d=b[e][1],("string"==typeof d||d instanceof String)&&(d="true"===d||("false"===d?!1:d)),("string"==typeof d||d instanceof String)&&(d=isNaN(d)?d:+d),f[b[e][0]]=d;return f}function k(){for(var a=q,b=0;b<arguments.length;++b)a+="-"+arguments[b];return a}function l(){var a,c,d=location.hash.replace("#","");if(d){try{c=b("[data-"+p+"-id="+d.replace(new RegExp("/","g"),"\\/")+"]")}catch(e){}c&&c.length&&(a=b[p].lookup[c.data(p)],a&&a.settings.hashTracking&&a.open())}else n&&n.state===u.OPENED&&n.settings.hashTracking&&n.close()}function m(a,c){var d=b(document.body),e=this;e.settings=b.extend({},t,c),e.index=b[p].lookup.push(e)-1,e.state=u.CLOSED,e.$overlay=b("."+k("overlay")),e.$overlay.length||(e.$overlay=b("<div>").addClass(k("overlay")+" "+k("is",u.CLOSED)).hide(),d.append(e.$overlay)),e.$bg=b("."+k("bg")).addClass(k("is",u.CLOSED)),e.$modal=a.addClass(q+" "+k("is-initialized")+" "+e.settings.modifier+" "+k("is",u.CLOSED)).attr("tabindex","-1"),e.$wrapper=b("<div>").addClass(k("wrapper")+" "+e.settings.modifier+" "+k("is",u.CLOSED)).hide().append(e.$modal),d.append(e.$wrapper),e.$wrapper.on("click."+q,"[data-"+p+'-action="close"]',function(a){a.preventDefault(),e.close()}),e.$wrapper.on("click."+q,"[data-"+p+'-action="cancel"]',function(a){a.preventDefault(),e.$modal.trigger(v.CANCELLATION),e.settings.closeOnCancel&&e.close(v.CANCELLATION)}),e.$wrapper.on("click."+q,"[data-"+p+'-action="confirm"]',function(a){a.preventDefault(),e.$modal.trigger(v.CONFIRMATION),e.settings.closeOnConfirm&&e.close(v.CONFIRMATION)}),e.$wrapper.on("click."+q,function(a){var c=b(a.target);c.hasClass(k("wrapper"))&&e.settings.closeOnOutsideClick&&e.close()})}var n,o,p="remodal",q=a.REMODAL_GLOBALS&&a.REMODAL_GLOBALS.NAMESPACE||p,r=b.map(["animationstart","webkitAnimationStart","MSAnimationStart","oAnimationStart"],function(a){return a+"."+q}).join(" "),s=b.map(["animationend","webkitAnimationEnd","MSAnimationEnd","oAnimationEnd"],function(a){return a+"."+q}).join(" "),t=b.extend({hashTracking:!0,closeOnConfirm:!0,closeOnCancel:!0,closeOnEscape:!0,closeOnOutsideClick:!0,modifier:""},a.REMODAL_GLOBALS&&a.REMODAL_GLOBALS.DEFAULTS),u={CLOSING:"closing",CLOSED:"closed",OPENING:"opening",OPENED:"opened"},v={CONFIRMATION:"confirmation",CANCELLATION:"cancellation"},w=function(){var a=document.createElement("div").style;return void 0!==a.animationName||void 0!==a.WebkitAnimationName||void 0!==a.MozAnimationName||void 0!==a.msAnimationName||void 0!==a.OAnimationName}();m.prototype.open=function(){var a,c=this;c.state!==u.OPENING&&c.state!==u.CLOSING&&(a=c.$modal.attr("data-"+p+"-id"),a&&c.settings.hashTracking&&(o=b(window).scrollTop(),location.hash=a),n&&n!==c&&i(n),n=c,e(),c.$bg.addClass(c.settings.modifier),c.$overlay.addClass(c.settings.modifier).show(),c.$wrapper.show().scrollTop(0),c.$modal.focus(),h(function(){g(c,u.OPENING)},function(){g(c,u.OPENED)},c))},m.prototype.close=function(a){var c=this;c.state!==u.OPENING&&c.state!==u.CLOSING&&(c.settings.hashTracking&&c.$modal.attr("data-"+p+"-id")===location.hash.substr(1)&&(location.hash="",b(window).scrollTop(o)),h(function(){g(c,u.CLOSING,!1,a)},function(){c.$bg.removeClass(c.settings.modifier),c.$overlay.removeClass(c.settings.modifier).hide(),c.$wrapper.hide(),f(),g(c,u.CLOSED,!1,a)},c))},m.prototype.getState=function(){return this.state},m.prototype.destroy=function(){var a,c=b[p].lookup;i(this),this.$wrapper.remove(),delete c[this.index],a=b.grep(c,function(a){return!!a}).length,0===a&&(this.$overlay.remove(),this.$bg.removeClass(k("is",u.CLOSING)+" "+k("is",u.OPENING)+" "+k("is",u.CLOSED)+" "+k("is",u.OPENED)))},b[p]={lookup:[]},b.fn[p]=function(a){var c,d;return this.each(function(e,f){d=b(f),null==d.data(p)?(c=new m(d,a),d.data(p,c.index),c.settings.hashTracking&&d.attr("data-"+p+"-id")===location.hash.substr(1)&&c.open()):c=b[p].lookup[d.data(p)]}),c},b(document).ready(function(){b(document).on("click","[data-"+p+"-target]",function(a){a.preventDefault();var c=a.currentTarget,d=c.getAttribute("data-"+p+"-target"),e=b("[data-"+p+"-id="+d+"]");b[p].lookup[e.data(p)].open()}),b(document).find("."+q).each(function(a,c){var d=b(c),e=d.data(p+"-options");e?("string"==typeof e||e instanceof String)&&(e=j(e)):e={},d[p](e)}),b(document).on("keydown."+q,function(a){n&&n.settings.closeOnEscape&&n.state===u.OPENED&&27===a.keyCode&&n.close()}),b(window).on("hashchange."+q,l)})});

;
/* wp-embed.min.js */

/* 1 */ !function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content"),j=b.querySelectorAll("blockquote.wp-embedded-content");for(c=0;c<j.length;c++)j[c].style.display="none";for(c=0;c<i.length;c++)if(d=i[c],d.style.display="",!d.getAttribute("data-secret")){if(f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f),g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}else;}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.style.display="","height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(200>~~g)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document);
