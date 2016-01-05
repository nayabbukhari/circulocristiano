jQuery(document).ready(function() {
	var marker = jQuery('#x_starfield_marker');
	if (marker != '') {
        var section = marker.closest('.x-section');
        section.css('overflow','hidden');
	}
});