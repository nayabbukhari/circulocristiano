jQuery(document).ready(function() {
    var hdr_bs = jQuery('.x-navbar').css('box-shadow');
    var hdr_bb = jQuery('.x-navbar').css('border-bottom');
	var marker = jQuery('#x_splash_marker');
	if (marker != '') {
    	jQuery('.x-main').css('background','none');
    	jQuery('#top').attr('style', 'background-color: transparent !important');
    	section_resize();
    	jQuery(window).resize(function(){
        	section_resize();
        	nav_bar();
    	});
	}
	function section_resize() {
    	var marker = jQuery('#x_splash_marker');
    	var section = marker.closest('.x-section');
    	var sh = section.height();
    	var ph = jQuery(window).height();
    	var nh = jQuery('.x-navbar').outerHeight(true);
    	if (jQuery( window ).width() > 979) {
            var ch = (ph - nh - sh);
            if (ch >= 0) {
                ch = ch / 2; //balance 
                section.css('padding-top', ch + 'px');
                section.css('padding-bottom', ch + 'px');
            }
            nav_bar();
            jQuery(document).on('scroll',function(){
                nav_bar();   
            });
    	}    	
	}
	function nav_bar() {
    	if (jQuery( window ).width() > 979) {
    	    var p  = jQuery(window).scrollTop();
            if (p == 0) {
                jQuery('.x-navbar').attr('style', 'background-color: transparent !important');
                jQuery('.x-navbar').css('border-bottom','0');
                jQuery('.x-navbar').css('box-shadow','none');
    	    } else {
        	    jQuery('.x-navbar').attr('style', 'background-color: intial !important');
                jQuery('.x-navbar').css('border-bottom',hdr_bb);
                jQuery('.x-navbar').css('box-shadow',hdr_bs);        	    
    	    }
    	} else {
        	jQuery('.x-navbar').attr('style', 'background-color: intial !important');
    	}
	}
});