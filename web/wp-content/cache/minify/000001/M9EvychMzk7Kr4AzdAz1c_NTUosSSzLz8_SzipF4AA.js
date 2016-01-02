
/* thickbox.js */

/* 1   */ /*
/* 2   *|  * Thickbox 3.1 - One Box To Rule Them All.
/* 3   *|  * By Cody Lindley (http://www.codylindley.com)
/* 4   *|  * Copyright (c) 2007 cody lindley
/* 5   *|  * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
/* 6   *| */
/* 7   */ 
/* 8   */ if ( typeof tb_pathToImage != 'string' ) {
/* 9   */ 	var tb_pathToImage = thickboxL10n.loadingAnimation;
/* 10  */ }
/* 11  */ 
/* 12  */ /*!!!!!!!!!!!!!!!!! edit below this line at your own risk !!!!!!!!!!!!!!!!!!!!!!!*/
/* 13  */ 
/* 14  */ //on page load call tb_init
/* 15  */ jQuery(document).ready(function(){
/* 16  */ 	tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
/* 17  */ 	imgLoader = new Image();// preload image
/* 18  */ 	imgLoader.src = tb_pathToImage;
/* 19  */ });
/* 20  */ 
/* 21  */ /*
/* 22  *|  * Add thickbox to href & area elements that have a class of .thickbox.
/* 23  *|  * Remove the loading indicator when content in an iframe has loaded.
/* 24  *|  */
/* 25  */ function tb_init(domChunk){
/* 26  */ 	jQuery( 'body' )
/* 27  */ 		.on( 'click', domChunk, tb_click )
/* 28  */ 		.on( 'thickbox:iframe:loaded', function() {
/* 29  */ 			jQuery( '#TB_window' ).removeClass( 'thickbox-loading' );
/* 30  */ 		});
/* 31  */ }
/* 32  */ 
/* 33  */ function tb_click(){
/* 34  */ 	var t = this.title || this.name || null;
/* 35  */ 	var a = this.href || this.alt;
/* 36  */ 	var g = this.rel || false;
/* 37  */ 	tb_show(t,a,g);
/* 38  */ 	this.blur();
/* 39  */ 	return false;
/* 40  */ }
/* 41  */ 
/* 42  */ function tb_show(caption, url, imageGroup) {//function called when the user clicks on a thickbox link
/* 43  */ 
/* 44  */ 	try {
/* 45  */ 		if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
/* 46  */ 			jQuery("body","html").css({height: "100%", width: "100%"});
/* 47  */ 			jQuery("html").css("overflow","hidden");
/* 48  */ 			if (document.getElementById("TB_HideSelect") === null) {//iframe to hide select elements in ie6
/* 49  */ 				jQuery("body").append("<iframe id='TB_HideSelect'>"+thickboxL10n.noiframes+"</iframe><div id='TB_overlay'></div><div id='TB_window' class='thickbox-loading'></div>");
/* 50  */ 				jQuery("#TB_overlay").click(tb_remove);

/* thickbox.js */

/* 51  */ 			}
/* 52  */ 		}else{//all others
/* 53  */ 			if(document.getElementById("TB_overlay") === null){
/* 54  */ 				jQuery("body").append("<div id='TB_overlay'></div><div id='TB_window' class='thickbox-loading'></div>");
/* 55  */ 				jQuery("#TB_overlay").click(tb_remove);
/* 56  */ 				jQuery( 'body' ).addClass( 'modal-open' );
/* 57  */ 			}
/* 58  */ 		}
/* 59  */ 
/* 60  */ 		if(tb_detectMacXFF()){
/* 61  */ 			jQuery("#TB_overlay").addClass("TB_overlayMacFFBGHack");//use png overlay so hide flash
/* 62  */ 		}else{
/* 63  */ 			jQuery("#TB_overlay").addClass("TB_overlayBG");//use background and opacity
/* 64  */ 		}
/* 65  */ 
/* 66  */ 		if(caption===null){caption="";}
/* 67  */ 		jQuery("body").append("<div id='TB_load'><img src='"+imgLoader.src+"' width='208' /></div>");//add loader to the page
/* 68  */ 		jQuery('#TB_load').show();//show loader
/* 69  */ 
/* 70  */ 		var baseURL;
/* 71  */ 	   if(url.indexOf("?")!==-1){ //ff there is a query string involved
/* 72  */ 			baseURL = url.substr(0, url.indexOf("?"));
/* 73  */ 	   }else{
/* 74  */ 	   		baseURL = url;
/* 75  */ 	   }
/* 76  */ 
/* 77  */ 	   var urlString = /\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/;
/* 78  */ 	   var urlType = baseURL.toLowerCase().match(urlString);
/* 79  */ 
/* 80  */ 		if(urlType == '.jpg' || urlType == '.jpeg' || urlType == '.png' || urlType == '.gif' || urlType == '.bmp'){//code to show images
/* 81  */ 
/* 82  */ 			TB_PrevCaption = "";
/* 83  */ 			TB_PrevURL = "";
/* 84  */ 			TB_PrevHTML = "";
/* 85  */ 			TB_NextCaption = "";
/* 86  */ 			TB_NextURL = "";
/* 87  */ 			TB_NextHTML = "";
/* 88  */ 			TB_imageCount = "";
/* 89  */ 			TB_FoundURL = false;
/* 90  */ 			if(imageGroup){
/* 91  */ 				TB_TempArray = jQuery("a[rel="+imageGroup+"]").get();
/* 92  */ 				for (TB_Counter = 0; ((TB_Counter < TB_TempArray.length) && (TB_NextHTML === "")); TB_Counter++) {
/* 93  */ 					var urlTypeTemp = TB_TempArray[TB_Counter].href.toLowerCase().match(urlString);
/* 94  */ 						if (!(TB_TempArray[TB_Counter].href == url)) {
/* 95  */ 							if (TB_FoundURL) {
/* 96  */ 								TB_NextCaption = TB_TempArray[TB_Counter].title;
/* 97  */ 								TB_NextURL = TB_TempArray[TB_Counter].href;
/* 98  */ 								TB_NextHTML = "<span id='TB_next'>&nbsp;&nbsp;<a href='#'>"+thickboxL10n.next+"</a></span>";
/* 99  */ 							} else {
/* 100 */ 								TB_PrevCaption = TB_TempArray[TB_Counter].title;

/* thickbox.js */

/* 101 */ 								TB_PrevURL = TB_TempArray[TB_Counter].href;
/* 102 */ 								TB_PrevHTML = "<span id='TB_prev'>&nbsp;&nbsp;<a href='#'>"+thickboxL10n.prev+"</a></span>";
/* 103 */ 							}
/* 104 */ 						} else {
/* 105 */ 							TB_FoundURL = true;
/* 106 */ 							TB_imageCount = thickboxL10n.image + ' ' + (TB_Counter + 1) + ' ' + thickboxL10n.of + ' ' + (TB_TempArray.length);
/* 107 */ 						}
/* 108 */ 				}
/* 109 */ 			}
/* 110 */ 
/* 111 */ 			imgPreloader = new Image();
/* 112 */ 			imgPreloader.onload = function(){
/* 113 */ 			imgPreloader.onload = null;
/* 114 */ 
/* 115 */ 			// Resizing large images - original by Christian Montoya edited by me.
/* 116 */ 			var pagesize = tb_getPageSize();
/* 117 */ 			var x = pagesize[0] - 150;
/* 118 */ 			var y = pagesize[1] - 150;
/* 119 */ 			var imageWidth = imgPreloader.width;
/* 120 */ 			var imageHeight = imgPreloader.height;
/* 121 */ 			if (imageWidth > x) {
/* 122 */ 				imageHeight = imageHeight * (x / imageWidth);
/* 123 */ 				imageWidth = x;
/* 124 */ 				if (imageHeight > y) {
/* 125 */ 					imageWidth = imageWidth * (y / imageHeight);
/* 126 */ 					imageHeight = y;
/* 127 */ 				}
/* 128 */ 			} else if (imageHeight > y) {
/* 129 */ 				imageWidth = imageWidth * (y / imageHeight);
/* 130 */ 				imageHeight = y;
/* 131 */ 				if (imageWidth > x) {
/* 132 */ 					imageHeight = imageHeight * (x / imageWidth);
/* 133 */ 					imageWidth = x;
/* 134 */ 				}
/* 135 */ 			}
/* 136 */ 			// End Resizing
/* 137 */ 
/* 138 */ 			TB_WIDTH = imageWidth + 30;
/* 139 */ 			TB_HEIGHT = imageHeight + 60;
/* 140 */ 			jQuery("#TB_window").append("<a href='' id='TB_ImageOff'><span class='screen-reader-text'>"+thickboxL10n.close+"</span><img id='TB_Image' src='"+url+"' width='"+imageWidth+"' height='"+imageHeight+"' alt='"+caption+"'/></a>" + "<div id='TB_caption'>"+caption+"<div id='TB_secondLine'>" + TB_imageCount + TB_PrevHTML + TB_NextHTML + "</div></div><div id='TB_closeWindow'><a href='#' id='TB_closeWindowButton'><span class='screen-reader-text'>"+thickboxL10n.close+"</span><div class='tb-close-icon'></div></a></div>");
/* 141 */ 
/* 142 */ 			jQuery("#TB_closeWindowButton").click(tb_remove);
/* 143 */ 
/* 144 */ 			if (!(TB_PrevHTML === "")) {
/* 145 */ 				function goPrev(){
/* 146 */ 					if(jQuery(document).unbind("click",goPrev)){jQuery(document).unbind("click",goPrev);}
/* 147 */ 					jQuery("#TB_window").remove();
/* 148 */ 					jQuery("body").append("<div id='TB_window'></div>");
/* 149 */ 					tb_show(TB_PrevCaption, TB_PrevURL, imageGroup);
/* 150 */ 					return false;

/* thickbox.js */

/* 151 */ 				}
/* 152 */ 				jQuery("#TB_prev").click(goPrev);
/* 153 */ 			}
/* 154 */ 
/* 155 */ 			if (!(TB_NextHTML === "")) {
/* 156 */ 				function goNext(){
/* 157 */ 					jQuery("#TB_window").remove();
/* 158 */ 					jQuery("body").append("<div id='TB_window'></div>");
/* 159 */ 					tb_show(TB_NextCaption, TB_NextURL, imageGroup);
/* 160 */ 					return false;
/* 161 */ 				}
/* 162 */ 				jQuery("#TB_next").click(goNext);
/* 163 */ 
/* 164 */ 			}
/* 165 */ 
/* 166 */ 			jQuery(document).bind('keydown.thickbox', function(e){
/* 167 */ 				if ( e.which == 27 ){ // close
/* 168 */ 					tb_remove();
/* 169 */ 
/* 170 */ 				} else if ( e.which == 190 ){ // display previous image
/* 171 */ 					if(!(TB_NextHTML == "")){
/* 172 */ 						jQuery(document).unbind('thickbox');
/* 173 */ 						goNext();
/* 174 */ 					}
/* 175 */ 				} else if ( e.which == 188 ){ // display next image
/* 176 */ 					if(!(TB_PrevHTML == "")){
/* 177 */ 						jQuery(document).unbind('thickbox');
/* 178 */ 						goPrev();
/* 179 */ 					}
/* 180 */ 				}
/* 181 */ 				return false;
/* 182 */ 			});
/* 183 */ 
/* 184 */ 			tb_position();
/* 185 */ 			jQuery("#TB_load").remove();
/* 186 */ 			jQuery("#TB_ImageOff").click(tb_remove);
/* 187 */ 			jQuery("#TB_window").css({'visibility':'visible'}); //for safari using css instead of show
/* 188 */ 			};
/* 189 */ 
/* 190 */ 			imgPreloader.src = url;
/* 191 */ 		}else{//code to show html
/* 192 */ 
/* 193 */ 			var queryString = url.replace(/^[^\?]+\??/,'');
/* 194 */ 			var params = tb_parseQuery( queryString );
/* 195 */ 
/* 196 */ 			TB_WIDTH = (params['width']*1) + 30 || 630; //defaults to 630 if no parameters were added to URL
/* 197 */ 			TB_HEIGHT = (params['height']*1) + 40 || 440; //defaults to 440 if no parameters were added to URL
/* 198 */ 			ajaxContentW = TB_WIDTH - 30;
/* 199 */ 			ajaxContentH = TB_HEIGHT - 45;
/* 200 */ 

/* thickbox.js */

/* 201 */ 			if(url.indexOf('TB_iframe') != -1){// either iframe or ajax window
/* 202 */ 					urlNoQuery = url.split('TB_');
/* 203 */ 					jQuery("#TB_iframeContent").remove();
/* 204 */ 					if(params['modal'] != "true"){//iframe no modal
/* 205 */ 						jQuery("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton'><span class='screen-reader-text'>"+thickboxL10n.close+"</span><div class='tb-close-icon'></div></a></div></div><iframe frameborder='0' hspace='0' allowTransparency='true' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW + 29)+"px;height:"+(ajaxContentH + 17)+"px;' >"+thickboxL10n.noiframes+"</iframe>");
/* 206 */ 					}else{//iframe modal
/* 207 */ 					jQuery("#TB_overlay").unbind();
/* 208 */ 						jQuery("#TB_window").append("<iframe frameborder='0' hspace='0' allowTransparency='true' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW + 29)+"px;height:"+(ajaxContentH + 17)+"px;'>"+thickboxL10n.noiframes+"</iframe>");
/* 209 */ 					}
/* 210 */ 			}else{// not an iframe, ajax
/* 211 */ 					if(jQuery("#TB_window").css("visibility") != "visible"){
/* 212 */ 						if(params['modal'] != "true"){//ajax no modal
/* 213 */ 						jQuery("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton'><div class='tb-close-icon'></div></a></div></div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px'></div>");
/* 214 */ 						}else{//ajax modal
/* 215 */ 						jQuery("#TB_overlay").unbind();
/* 216 */ 						jQuery("#TB_window").append("<div id='TB_ajaxContent' class='TB_modal' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>");
/* 217 */ 						}
/* 218 */ 					}else{//this means the window is already up, we are just loading new content via ajax
/* 219 */ 						jQuery("#TB_ajaxContent")[0].style.width = ajaxContentW +"px";
/* 220 */ 						jQuery("#TB_ajaxContent")[0].style.height = ajaxContentH +"px";
/* 221 */ 						jQuery("#TB_ajaxContent")[0].scrollTop = 0;
/* 222 */ 						jQuery("#TB_ajaxWindowTitle").html(caption);
/* 223 */ 					}
/* 224 */ 			}
/* 225 */ 
/* 226 */ 			jQuery("#TB_closeWindowButton").click(tb_remove);
/* 227 */ 
/* 228 */ 				if(url.indexOf('TB_inline') != -1){
/* 229 */ 					jQuery("#TB_ajaxContent").append(jQuery('#' + params['inlineId']).children());
/* 230 */ 					jQuery("#TB_window").bind('tb_unload', function () {
/* 231 */ 						jQuery('#' + params['inlineId']).append( jQuery("#TB_ajaxContent").children() ); // move elements back when you're finished
/* 232 */ 					});
/* 233 */ 					tb_position();
/* 234 */ 					jQuery("#TB_load").remove();
/* 235 */ 					jQuery("#TB_window").css({'visibility':'visible'});
/* 236 */ 				}else if(url.indexOf('TB_iframe') != -1){
/* 237 */ 					tb_position();
/* 238 */ 					jQuery("#TB_load").remove();
/* 239 */ 					jQuery("#TB_window").css({'visibility':'visible'});
/* 240 */ 				}else{
/* 241 */ 					var load_url = url;
/* 242 */ 					load_url += -1 === url.indexOf('?') ? '?' : '&';
/* 243 */ 					jQuery("#TB_ajaxContent").load(load_url += "random=" + (new Date().getTime()),function(){//to do a post change this load method
/* 244 */ 						tb_position();
/* 245 */ 						jQuery("#TB_load").remove();
/* 246 */ 						tb_init("#TB_ajaxContent a.thickbox");
/* 247 */ 						jQuery("#TB_window").css({'visibility':'visible'});
/* 248 */ 					});
/* 249 */ 				}
/* 250 */ 

/* thickbox.js */

/* 251 */ 		}
/* 252 */ 
/* 253 */ 		if(!params['modal']){
/* 254 */ 			jQuery(document).bind('keydown.thickbox', function(e){
/* 255 */ 				if ( e.which == 27 ){ // close
/* 256 */ 					tb_remove();
/* 257 */ 					return false;
/* 258 */ 				}
/* 259 */ 			});
/* 260 */ 		}
/* 261 */ 
/* 262 */ 	} catch(e) {
/* 263 */ 		//nothing here
/* 264 */ 	}
/* 265 */ }
/* 266 */ 
/* 267 */ //helper functions below
/* 268 */ function tb_showIframe(){
/* 269 */ 	jQuery("#TB_load").remove();
/* 270 */ 	jQuery("#TB_window").css({'visibility':'visible'}).trigger( 'thickbox:iframe:loaded' );
/* 271 */ }
/* 272 */ 
/* 273 */ function tb_remove() {
/* 274 */  	jQuery("#TB_imageOff").unbind("click");
/* 275 */ 	jQuery("#TB_closeWindowButton").unbind("click");
/* 276 */ 	jQuery("#TB_window").fadeOut("fast",function(){jQuery('#TB_window,#TB_overlay,#TB_HideSelect').trigger("tb_unload").unbind().remove();});
/* 277 */ 	jQuery( 'body' ).removeClass( 'modal-open' );
/* 278 */ 	jQuery("#TB_load").remove();
/* 279 */ 	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
/* 280 */ 		jQuery("body","html").css({height: "auto", width: "auto"});
/* 281 */ 		jQuery("html").css("overflow","");
/* 282 */ 	}
/* 283 */ 	jQuery(document).unbind('.thickbox');
/* 284 */ 	return false;
/* 285 */ }
/* 286 */ 
/* 287 */ function tb_position() {
/* 288 */ var isIE6 = typeof document.body.style.maxHeight === "undefined";
/* 289 */ jQuery("#TB_window").css({marginLeft: '-' + parseInt((TB_WIDTH / 2),10) + 'px', width: TB_WIDTH + 'px'});
/* 290 */ 	if ( ! isIE6 ) { // take away IE6
/* 291 */ 		jQuery("#TB_window").css({marginTop: '-' + parseInt((TB_HEIGHT / 2),10) + 'px'});
/* 292 */ 	}
/* 293 */ }
/* 294 */ 
/* 295 */ function tb_parseQuery ( query ) {
/* 296 */    var Params = {};
/* 297 */    if ( ! query ) {return Params;}// return empty object
/* 298 */    var Pairs = query.split(/[;&]/);
/* 299 */    for ( var i = 0; i < Pairs.length; i++ ) {
/* 300 */       var KeyVal = Pairs[i].split('=');

/* thickbox.js */

/* 301 */       if ( ! KeyVal || KeyVal.length != 2 ) {continue;}
/* 302 */       var key = unescape( KeyVal[0] );
/* 303 */       var val = unescape( KeyVal[1] );
/* 304 */       val = val.replace(/\+/g, ' ');
/* 305 */       Params[key] = val;
/* 306 */    }
/* 307 */    return Params;
/* 308 */ }
/* 309 */ 
/* 310 */ function tb_getPageSize(){
/* 311 */ 	var de = document.documentElement;
/* 312 */ 	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
/* 313 */ 	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
/* 314 */ 	arrayPageSize = [w,h];
/* 315 */ 	return arrayPageSize;
/* 316 */ }
/* 317 */ 
/* 318 */ function tb_detectMacXFF() {
/* 319 */   var userAgent = navigator.userAgent.toLowerCase();
/* 320 */   if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1) {
/* 321 */     return true;
/* 322 */   }
/* 323 */ }
/* 324 */ 

;
/* moderation.js */

/* 1 */ function moderation_submit() {
/* 2 */ 	jQuery('#moderation-report').load( moderation_ajaxurl, jQuery('form#moderation-report-form').serializeArray(), function() {
/* 3 */ 		jQuery('#moderation-report').append('<p>Press ESC or click anywhere outside this box to close it.</p>');
/* 4 */ 	} );
/* 5 */ 	return false;
/* 6 */ }
