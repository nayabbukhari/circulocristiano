
/* shortcode.js */

/* 1   */ 
/* 2   */ //object to store map data
/* 3   */ //can be used in the future to store more map specific data
/* 4   */ var iwmMapObj = [];
/* 5   */ 
/* 6   */ 
/* 7   */ var apiversion = iwmparam[0]['apiversion'];
/* 8   */ //var apiversion = "1.1";
/* 9   */ 
/* 10  */ //google.load('visualization', apiversion , {'packages': ['geochart']});
/* 11  */ //google.setOnLoadCallback(iwmDrawVisualization);
/* 12  */ 
/* 13  */ var options = {packages: ['geochart'], callback : iwmDrawVisualization};
/* 14  */ google.load('visualization', '1', options);
/* 15  */ 
/* 16  */ 
/* 17  */ function iwmDrawVisualization(skipNotVisible) {
/* 18  */ 
/* 19  */ 	var geocharts = {};
/* 20  */     var data = {};
/* 21  */     var values = {};
/* 22  */     var listener_actions = {};
/* 23  */     var listener_custom = {};
/* 24  */     var identifier = {};
/* 25  */ 
/* 26  */ 	for (var key in iwmparam) {	
/* 27  */ 
/* 28  */ 		if(skipNotVisible && iwmMapObj[key] && !iwmMapObj[key].div.is(':visible')) {
/* 29  */ 			continue;
/* 30  */ 		}
/* 31  */ 
/* 32  */ 
/* 33  */ 		if(iwmparam[key]['region']) {
/* 34  */ 
/* 35  */ 
/* 36  */ 
/* 37  */ 			var usehtml = parseInt(iwmparam[key]['usehtml']);
/* 38  */ 
/* 39  */ 			/* Disable HTML tooltips on iOS */
/* 40  */ 			/*if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
/* 41  *| 				usehtml = 0;
/* 42  *| 				
/* 43  *| 
/* 44  *| 			}*/
/* 45  */ 
/* 46  */ 			var iwmid = parseInt(iwmparam[key]['id']);
/* 47  */ 			var bgcolor = iwmparam[key]['bgcolor'];  
/* 48  */ 			var stroke = parseInt(iwmparam[key]['stroke']);
/* 49  */ 			var bordercolor = iwmparam[key]['bordercolor']; 
/* 50  */ 			var incolor = iwmparam[key]['incolor']; 

/* shortcode.js */

/* 51  */ 			var actcolor = iwmparam[key]['actcolor']; 
/* 52  */ 			var width = parseInt(iwmparam[key]['width']); 
/* 53  */ 			var height = parseInt(iwmparam[key]['height']);
/* 54  */ 			var ratio = (iwmparam[key]['aspratio'] === '1');
/* 55  */ 			var interactive = (iwmparam[key]['interactive'] === 'true');
/* 56  */ 			var toolt = iwmparam[key]['tooltip'];
/* 57  */ 			var region = iwmparam[key]['region']; 
/* 58  */ 			var resolution = iwmparam[key]['resolution']; 
/* 59  */ 			var markersize = parseInt(iwmparam[key]['markersize']); 
/* 60  */ 			var displaymode = iwmparam[key]['displaymode'];  
/* 61  */ 			var placestxt =  iwmparam[key]['placestxt']; 
/* 62  */ 			var projection = iwmparam[key]['projection']; 
/* 63  */ 
/* 64  */ 			placestxt = placestxt.replace(/^\s+|\s+$/g,'');
/* 65  */ 
/* 66  */ 			var action = iwmparam[key]['action']; 
/* 67  */ 			var customaction = iwmparam[key]['custom_action']; 
/* 68  */ 
/* 69  */ 			identifier[key] = iwmid;
/* 70  */ 			listener_actions[key] = action;
/* 71  */ 			listener_custom[key] = customaction;
/* 72  */ 
/* 73  */ 			var places = placestxt.split(";");
/* 74  */ 
/* 75  */ 			
/* 76  */ 		
/* 77  */ 					
/* 78  */ 			
/* 79  */ 		   data[key] = new google.visualization.DataTable();
/* 80  */ 		   
/* 81  */ 		   	if(displaymode == "markers02" || displaymode == "text02") {
/* 82  */ 
/* 83  */ 		   		 
/* 84  */ 
/* 85  */ 			     data[key].addColumn('number', 'Lat');                                
/* 86  */ 			     data[key].addColumn('number', 'Long');
/* 87  */ 			 }
/* 88  */ 		   
/* 89  */ 		   
/* 90  */ 			data[key].addColumn('string', 'Country'); // Implicit domain label col.
/* 91  */ 			data[key].addColumn('number', 'Value'); // Implicit series 1 data col.
/* 92  */ 			data[key].addColumn({type:'string', role: 'tooltip', p:{html:true}}); // 
/* 93  */ 				
/* 94  */ 				var colorsmap = [];
/* 95  */ 				var colorsmapecho = "";		
/* 96  */ 				
/* 97  */ 			values[key] = {};
/* 98  */ 
/* 99  */ 				//places.length-1 to eliminate empty value at the end
/* 100 */ 			for (var i = 0; i < places.length-1; i++) {

/* shortcode.js */

/* 101 */ 				var entry = places[i].split(",");
/* 102 */ 				
/* 103 */ 				var ttitle = entry[1].replace(/&#59/g,";");
/* 104 */ 				ttitle = ttitle.replace(/&#44/g,",");
/* 105 */ 				var ttooltip = entry[2].replace(/&#59/g,";");
/* 106 */ 				ttooltip = ttooltip.replace(/&#44/g,",");
/* 107 */ 
/* 108 */ 				/* Disable HTML content in tooltips on iOS */
/* 109 */ 				/*if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
/* 110 *| 					
/* 111 *| 					ttooltip = ittooltip.replace(/(<([^>]+)>)/ig,"");
/* 112 *| 
/* 113 *| 				}*/
/* 114 */ 
/* 115 */ 				var iwmcode = entry[0];
/* 116 */ 				iwmcode = iwmcode.replace(/^\s+|\s+$/g,'');
/* 117 */ 				
/* 118 */ 				
/* 119 */ 				//If data != markers02
/* 120 */ 				if(displaymode != "markers02" && displaymode != "text02") {
/* 121 */ 				
/* 122 */ 
/* 123 */ 					data[key].addRows([[{v:iwmcode,f:ttitle},i,ttooltip]]);
/* 124 */ 					var index = iwmcode;
/* 125 */ 
/* 126 */ 					}
/* 127 */ 
/* 128 */ 				else {
/* 129 */ 
/* 130 */ 					var trim = entry[0].replace(/^\s+|\s+$/g,"");
/* 131 */ 					var latlon = trim.split(" ");
/* 132 */ 					var lat = parseFloat(latlon[0]);
/* 133 */ 					var lon = parseFloat(latlon[1]);
/* 134 */ 								
/* 135 */ 							
/* 136 */ 					//data[key].addRows([[lat,lon,ttitle,i,ttooltip]]);
/* 137 */ 					data[key].addRows([[lat,lon,ttitle,i,ttooltip]]);		
/* 138 */ 					
/* 139 */ 					var index = lat;
/* 140 */ 
/* 141 */ 
/* 142 */ 					//finally set dislay mode of markers02 to proper value
/* 143 */ 					//displaymode = "markers";
/* 144 */ 					
/* 145 */ 					}
/* 146 */ 
/* 147 */ 
/* 148 */ 				var colori = entry[4];
/* 149 */ 				
/* 150 */ 				values[key][index] = entry[3].replace(/&#59/g,";");

/* shortcode.js */

/* 151 */ 				values[key][index] = values[key][index].replace(/&#44/g,",");	
/* 152 */ 				
/* 153 */ 				colorsmapecho = colorsmapecho + "'"+colori+"',";
/* 154 */ 				colorsmap.push(colori);
/* 155 */ 				
/* 156 */ 				}
/* 157 */ 				
/* 158 */ 				
/* 159 */ 			defmaxvalue = 0;
/* 160 */ 			if ((places.length-2) > 0) {
/* 161 */ 			defmaxvalue = places.length-2;	
/* 162 */ 			}
/* 163 */ 			
/* 164 */ 			if(displaymode=="markers02"){
/* 165 */ 				displaymode="markers";
/* 166 */ 			}
/* 167 */ 			if(displaymode=="text02"){
/* 168 */ 				displaymode="text";
/* 169 */ 			}	 	 	
/* 170 */ 
/* 171 */ 			var htmltooltip = false;
/* 172 */ 			if(usehtml==1) {
/* 173 */ 				htmltooltip = true;
/* 174 */ 			}
/* 175 */ 
/* 176 */ 			var options = {
/* 177 */ 				projection: projection,
/* 178 */ 				backgroundColor: {fill:bgcolor,stroke:bordercolor ,strokeWidth:stroke },
/* 179 */ 				colorAxis:  {minValue: 0, maxValue: defmaxvalue,  colors: colorsmap},
/* 180 */ 				legend: 'none',	
/* 181 */ 				backgroundColor: {fill:bgcolor,stroke:bordercolor ,strokeWidth:stroke },		
/* 182 */ 				datalessRegionColor: incolor,
/* 183 */ 				displayMode: displaymode, 
/* 184 */ 				enableRegionInteractivity: interactive,
/* 185 */ 				resolution: resolution,
/* 186 */ 				sizeAxis: {minValue: 1, maxValue:1,minSize:markersize,  maxSize: markersize},
/* 187 */ 				region:region,
/* 188 */ 				keepAspectRatio: ratio,
/* 189 */ 				width:width,
/* 190 */ 				height:height,
/* 191 */ 				magnifyingGlass: {enable: true, zoomFactor: 5.0},
/* 192 */ 				tooltip: {trigger:toolt, isHtml: htmltooltip},
/* 193 */ 				//domain: 'IN'		
/* 194 */ 				};
/* 195 */ 
/* 196 */ 			var divid = "map_canvas_"+iwmid;  	
/* 197 */ 
/* 198 */ 		    geocharts[key] = new google.visualization.GeoChart(document.getElementById(divid));
/* 199 */ 			
/* 200 */ 			

/* shortcode.js */

/* 201 */ 			if(action!="none") {
/* 202 */ 
/* 203 */ 				 google.visualization.events.addListener(geocharts[key], 'select', (function(x) {
/* 204 */ 	             return function () {
/* 205 */ 
/* 206 */ 	                var selection = geocharts[x].getSelection();
/* 207 */ 
/* 208 */ 	                if (selection.length == 1) {
/* 209 */ 	                    var selectedRow = selection[0].row;
/* 210 */ 	                    var selectedRegion = data[x].getValue(selectedRow, 0);
/* 211 */ 	                    
/* 212 */ 	                    if(values[x][selectedRegion]!=""){
/* 213 */ 	                   
/* 214 */ 	                   		iwm_run_action(selectedRegion,values[x][selectedRegion],identifier[x],listener_actions[x],listener_custom[x]);
/* 215 */ 	                    
/* 216 */ 	                    }
/* 217 */ 	                }
/* 218 */ 	            }
/* 219 */ 	        })(key));
/* 220 */ 
/* 221 */ 			}
/* 222 */ 			
/* 223 */ 			geocharts[key].draw(data[key], options);
/* 224 */ 
/* 225 */ 			//code to console log the image url data
/* 226 */ 			/* 
/* 227 *| 			google.visualization.events.addListener(geocharts[key], 'ready', function () {
/* 228 *| 				 var imgurl = geocharts[key].getImageURI();
/* 229 *| 		         console.log(imgurl);
/* 230 *| 		    });
/* 231 *| 			*/
/* 232 */ 
/* 233 */ 
/* 234 */ 			//Create a new object for this map
/* 235 */ 
/* 236 */ 			if(!iwmMapObj[key]) {
/* 237 */ 
/* 238 */ 				iwmMapObj[key] = {
/* 239 */ 					div: jQuery('#'+divid)
/* 240 */ 				};
/* 241 */ 
/* 242 */ 			} 
/* 243 */ 
/* 244 */ 			iwmMapObj[key].lastWidth = iwmMapObj[key].div.parent().width();
/* 245 */ 
/* 246 */ 			
/* 247 */ 
/* 248 */ 
/* 249 */ 		}
/* 250 */ 	}

/* shortcode.js */

/* 251 */ 
/* 252 */ 
/* 253 */ }
/* 254 */ 
/* 255 */ 
/* 256 */ function iwm_run_action(selected,value,id,action,customaction) {
/* 257 */ 
/* 258 */ 	//console.log('values for action:'+selected+';'+value+';'+id+';'+action+';'+customaction);
/* 259 */ 
/* 260 */ 	if(action == 'i_map_action_open_url') {	
/* 261 */ 		document.location = value; 
/* 262 */ 	}
/* 263 */ 		
/* 264 */ 	if(action == 'i_map_action_alert') {
/* 265 */ 		
/* 266 */ 		alert(value); 
/* 267 */ 	}
/* 268 */ 
/* 269 */ 	if(action == 'i_map_action_open_url_new') {
/* 270 */ 		
/* 271 */ 		window.open(value); 
/* 272 */ 	}
/* 273 */ 
/* 274 */ 	if(action == 'i_map_action_content_below' || action == 'i_map_action_content_above' ) {
/* 275 */ 		document.getElementById('imap'+id+'message').innerHTML = value;
/* 276 */ 
/* 277 */ 		//we check if there's a dropdown so we set selection to be the same as region clicked
/* 278 */ 		var dropdown = document.getElementById('imap-dropdown-'+id);
/* 279 */ 		if(dropdown) {
/* 280 */ 			document.getElementById('imap'+id+'-'+selected).selected=true;
/* 281 */ 		}
/* 282 */ 
/* 283 */ 	}
/* 284 */ 
/* 285 */ 	if(action == 'i_map_action_custom') {
/* 286 */ 
/* 287 */ 		var name = "iwm_custom_action_"+id;
/* 288 */ 		window[name](value);
/* 289 */ 	}
/* 290 */ }
/* 291 */ 
/* 292 */ jQuery(document).ajaxSuccess(function($) {
/* 293 */ 
/* 294 */ 	//iwmDrawVisualization(); 
/* 295 */ 		
/* 296 */ });
/* 297 */ 

;
/* responsive.js */

/* 1  */ 
/* 2  */ function iwm_resize(map_divs,skipcheck) {
/* 3  */ 
/* 4  */ 	
/* 5  */ 	jQuery.each( map_divs, function( index, element ) {
/* 6  */ 		    
/* 7  */ 		    //we access the .iwm_map_canvas
/* 8  */ 		    var map_div = element.div.parent();
/* 9  */ 
/* 10 */ 			if(!map_div.is(':visible')) {
/* 11 */ 
/* 12 */ 				if(!skipcheck){
/* 13 */ 					setTimeout(function(){  iwm_resize(map_divs,true); }, 1);
/* 14 */ 				}
/* 15 */ 			}
/* 16 */ 
/* 17 */ 			else {
/* 18 */ 
/* 19 */ 				//we check the parent width - site layout - for changes
/* 20 */ 				var width = map_div.parent().width();
/* 21 */ 				//if it's the same we terminate function
/* 22 */ 				if(width==element.lastWidth) {
/* 23 */ 
/* 24 */ 					return; 
/* 25 */ 
/* 26 */ 				}
/* 27 */ 
/* 28 */ 
/* 29 */ 				//else we redraw the map
/* 30 */ 				delay(function(){
/* 31 */ 
/* 32 */ 				     if (typeof iwmDrawVisualization == 'function') {
/* 33 */ 
/* 34 */ 				     		 map_div.animate({
/* 35 */ 							    opacity: 0,
/* 36 */ 							  }, 100, function() {
/* 37 */ 							    iwmDrawVisualization(true);
/* 38 */ 			
/* 39 */ 							  });
/* 40 */ 
/* 41 */ 				     		 map_div.animate({
/* 42 */ 							    opacity: 1,
/* 43 */ 							  }, 50, function() {
/* 44 */ 							    // Animation complete.
/* 45 */ 							  }); 
/* 46 */ 						     
/* 47 */ 							  		 
/* 48 */ 					} 
/* 49 */ 
/* 50 */ 				}, 200);

/* responsive.js */

/* 51 */ 	
/* 52 */ 
/* 53 */ 			//end else
/* 54 */ 			}
/* 55 */ 
/* 56 */ 		 	
/* 57 */ 		//end each
/* 58 */ 		});
/* 59 */ 
/* 60 */ }
/* 61 */ 
/* 62 */ 
/* 63 */ 
/* 64 */ var delay = (function(){
/* 65 */   var timer = 0;
/* 66 */   return function(callback, ms){
/* 67 */     clearTimeout (timer);
/* 68 */     timer = setTimeout(callback, ms);
/* 69 */   };
/* 70 */ })();
/* 71 */ 
/* 72 */ 
/* 73 */ 
/* 74 */ 
/* 75 */ jQuery(window).on('resize orientationchange', function() {	
/* 76 */ 
/* 77 */ 		iwm_resize(iwmMapObj);
/* 78 */ 	
/* 79 */ });
/* 80 */ 
/* 81 */ //solution for tabbed content (custom - needs to target tab links)
/* 82 */ /*
/* 83 *| jQuery('.ult_tab_li').click(function(){
/* 84 *|     iwm_resize(iwmMapObj);
/* 85 *| });
/* 86 *| */
