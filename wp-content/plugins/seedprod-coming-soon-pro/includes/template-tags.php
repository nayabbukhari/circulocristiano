<?php
// Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

add_shortcode( 'seed_cspv4_subscriber_count', 'seed_cspv4_subscriber_count' );
function seed_cspv4_subscriber_count($echo = true){

	global $seed_cspv4,$wpdb ;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
	$sql = "select count(*) FROM $tablename";

	$subscriber_count =$wpdb->get_var($sql);

	$output = $subscriber_count;

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_bypass_url', 'seed_cspv4_bypass_url' );
function seed_cspv4_bypass_url($echo = true){

	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = home_url('/').'?bypass='.$client_view_url.'&return='.urlencode($_SERVER['REQUEST_URI']);

	$output = apply_filters('seed_cspv4_bypass_url', $output);

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}


add_shortcode( 'seed_cspv4_bypass_link', 'seed_cspv4_bypass_link' );
function seed_cspv4_bypass_link($atts,$echo = true){

	extract( shortcode_atts( array(
		'text' => 'Bypass',
		'class' => '',
	), $atts ) );

	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '<a href="'.seed_cspv4_bypass_url(false).'" class="'.$class.'">'.$text.'</a>';

	$output = apply_filters('seed_cspv4_bypass_link', $output);

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_title', 'seed_cspv4_title' );
function seed_cspv4_title($echo = true){

	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';
	if(!empty($seo_title)){
		$output = esc_html($seo_title);
	} else {
		$output = get_bloginfo( 'name', 'display' );
	}

	$output = apply_filters('seed_cspv4_title', $output);

	// extract( shortcode_atts( array(
	// 	'foo' => 'something',
	// 	'bar' => 'something else',
	// ), $atts ) );

	// ob_start();
	// echo 'I am sweet little shortcode';
	// $output = ob_get_clean();

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_metadescription', 'seed_cspv4_metadescription' );
function seed_cspv4_metadescription($echo = true){
	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';
	if(empty($seo_description)){
		$seo_description = '';
	}

	$output = '<meta name="description" content="'.esc_attr($seo_description).'">'.PHP_EOL;

	$output = apply_filters('seed_cspv4_metadescription', $output);

	if ( $echo ){
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_viewport', 'seed_cspv4_viewport' );
function seed_cspv4_viewport($echo = true){
	$output = '';
	if(0 == 0){
		$output = '<meta name="viewport" content="width=device-width, initial-scale=1.0">'.PHP_EOL;
	}

	$output = apply_filters('seed_cspv4_viewport', $output);

	if ( $echo ){
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_privacy', 'seed_cspv4_privacy' );
function seed_cspv4_privacy($echo = true){
	$output = '';
	if(get_option('blog_public') == 0){
		$output = "<meta name='robots' content='noindex,nofollow' />".PHP_EOL;
	}

	$output = apply_filters('seed_cspv4_privacy', $output);

	if ( $echo ){
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_favicon', 'seed_cspv4_favicon' );
function seed_cspv4_favicon($echo = true){
	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';
	if(!empty($favicon['url'])){
		$output .= "<!-- Favicon -->".PHP_EOL;
		$output .= '<link href="'.esc_attr($favicon['url']).'" rel="shortcut icon" type="image/x-icon" />'.PHP_EOL;
	}

	$output = apply_filters('seed_cspv4_favicon', $output);

	if ( $echo ){
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_customcss', 'seed_cspv4_customcss' );
function seed_cspv4_customcss($echo = true){
	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';
	if(!empty($custom_css)){
		$output = '<style type="text/css">'.$custom_css.'</style>';
	}

	$output = apply_filters('seed_cspv4_customcss', $output);

	if ( $echo ){
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_head', 'seed_cspv4_head' );
function seed_cspv4_head($echo = true){
	require_once(SEED_CSPV4_PLUGIN_PATH.'lib/seed_cspv4_lessc.inc.php');
	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';

	// Enable wp_head if GF is the selected list
	$enable_wp_head_footer_list = apply_filters('seed_cspv4_enable_wp_head_footer_list',array());
	if(in_array($emaillist,$enable_wp_head_footer_list)){
		$enable_wp_head_footer = '1';
	}

	// Check if wp_head is enabled
	if(!empty($enable_wp_head_footer)){
		$output .= "<!-- wp_head() -->\n";
		ob_start();

		if($emaillist == 'gravityforms'){
			if(class_exists('RGFormsModel')){
				gravity_form_enqueue_scripts($gravityforms_form_id, false);
			}
		}

		wp_enqueue_script('jquery');
		wp_head();

		$output = ob_get_clean();;
	}

	// Facebook Thumbnail

	if(!empty($seo_title)){
		$seo_title = esc_html($seo_title);
	} else {
		$seo_title = get_bloginfo( 'name', 'display' );
	}
	if(empty($seo_description)){
		$seo_description = '';
	}
	$output .= '<meta property="og:url"           content="'.seed_cspv4_ref_link().'" />';
    $output .= '<meta property="og:type"          content="website" />';
    $output .= '<meta property="og:title"         content="'.$seo_title.'" />';
    $output .= '<meta property="og:description"   content="'.$seo_description.'" />';
	if(!empty($facebook_thumbnail)){
		$output .= '<meta property="og:image" content="'.esc_url($facebook_thumbnail['url']).'" />'."\n";
	}

	// Output Google Font Links
	$output .= seed_cspv4_get_google_font_css(array($text_font,$headline_font,$button_font));


	// Output Font Awesome
	$output .= "<!-- Font Awesome CSS -->".PHP_EOL;
	$output .='<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">'.PHP_EOL;

	// Boostrap and default Styles
	$output .= "<!-- Bootstrap and default Style -->\n";
	$output .= '<link rel="stylesheet" href="'.SEED_CSPV4_PLUGIN_URL.'themes/default/bootstrap/css/bootstrap.min.css">'."\n";

	$output .= apply_filters('seed_cspv4_default_stylesheet','<link rel="stylesheet" href="'.SEED_CSPV4_PLUGIN_URL.'themes/default/style.css">'."\n");


	if(is_rtl()){
		$output .= '<link rel="stylesheet" href="'.SEED_CSPV4_PLUGIN_URL.'themes/default/rtl.css">'."\n";
	}


	// Animate
	if(!empty($container_effect_animation)){
		$output .= '<link rel="stylesheet" href="'.SEED_CSPV4_PLUGIN_URL.'themes/default/css/animate.min.css">'."\n";
	}

	// Calculated CSS
	$output .= '<!-- Calculated Styles -->'.PHP_EOL;
	$output .= '<style type="text/css">'.PHP_EOL;
	ob_start();

	$css = "
	@primaryColor: {$button_font['color']};
	@secondaryColor: darken(@primaryColor, 15%);
	#gradient {
		.vertical(@startColor: #555, @endColor: #333) {
		    background-color: mix(@startColor, @endColor, 60%);
		    background-image: -moz-linear-gradient(top, @startColor, @endColor); // FF 3.6+
		    background-image: -ms-linear-gradient(top, @startColor, @endColor); // IE10
		    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@startColor), to(@endColor)); // Safari 4+, Chrome 2+
		    background-image: -webkit-linear-gradient(top, @startColor, @endColor); // Safari 5.1+, Chrome 10+
		    background-image: -o-linear-gradient(top, @startColor, @endColor); // Opera 11.10
		    background-image: linear-gradient(top, @startColor, @endColor); // The standard
		    background-repeat: repeat-x;
		    filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(startColorstr='%d', endColorstr='%d', GradientType=0)\",@startColor,@endColor)); // IE9 and down
		}
	}

	.seed-csp4 .progress-bar{
		#gradient > .vertical(@primaryColor, @secondaryColor);
	}

	";

	try {
		if($progressbar_effect == 'basic'){
			$less = new seed_cspv4_lessc();
			$style = $less->parse($css);
			echo $style;
		}
	} catch (Exception $e) {
		_e('An error has occured. Please make sure you have entered the Button Color correctly.','seedprod');
		die();
	}


	$css = "
	@primaryColor: {$button_font['color']};
	@secondaryColor: darken(@primaryColor, 15%);
	#gradient {
		.vertical(@startColor: #555, @endColor: #333) {
		    background-color: mix(@startColor, @endColor, 60%);
		    background-image: -moz-linear-gradient(top, @startColor, @endColor); // FF 3.6+
		    background-image: -ms-linear-gradient(top, @startColor, @endColor); // IE10
		    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@startColor), to(@endColor)); // Safari 4+, Chrome 2+
		    background-image: -webkit-linear-gradient(top, @startColor, @endColor); // Safari 5.1+, Chrome 10+
		    background-image: -o-linear-gradient(top, @startColor, @endColor); // Opera 11.10
		    background-image: linear-gradient(top, @startColor, @endColor); // The standard
		    background-repeat: repeat-x;
		    filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(startColorstr='%d', endColorstr='%d', GradientType=0)\",@startColor,@endColor)); // IE9 and down
		}
	}

	.countdown_section{
		#gradient > .vertical(@primaryColor, @secondaryColor);
	}

	";


	try{
		if(isset($enable_countdown) && $enable_countdown){
			$less = new seed_cspv4_lessc();
			$style = $less->parse($css);
			echo $style;
		}
	} catch (Exception $e) {
		_e('An error has occured. Please make sure you have entered the Button Color correctly.','seedprod');
	}

	$output .= ".progress-striped .progress-bar, .progress.active .progress-bar{background-color:{$button_font['color']}}";
	?>

	/* Background Style */
	html{
		height:100%;
		<?php if(!empty($background['background-image'])): ;?>
				background: <?php echo $background['background-color']; ?> url('<?php echo $background['background-image'] ?>') <?php echo $background['background-repeat'] ?> <?php echo $background['background-position'] ?> <?php echo $background['background-attachment'] ?>;
				<?php if(!empty($background['background-size'])) : ?>
					-webkit-background-size: <?php echo $background['background-size'];?>;
					-moz-background-size: <?php echo $background['background-size'];?>;
					-o-background-size: <?php echo $background['background-size'];?>;
					background-size: <?php echo $background['background-size'];?>;
				<?php endif; ?>
	    <?php else: ?>
	    	background: <?php echo $background['background-color'];?>;
		<?php endif; ?>
	}

	.seed-csp4 body{
    	<?php if(is_array($bg_effects) && in_array('noise',$bg_effects) ) : ?>
			height:100%;
			background: transparent url('<?php echo SEED_CSPV4_PLUGIN_URL. 'themes/default/images/bg-noise.png' ; ?>') repeat;
		<?php else: ?>
			background: transparent;
		<?php endif; ?>
	}

    /* Text Styles */
    <?php if(!empty($text_font['font-family'])):?>
	    .seed-csp4 body, .seed-csp4 body p{
	        font-family: <?php echo $text_font['font-family']; ?>;
	        font-weight: <?php echo $text_font['font-weight']; ?>;
	        font-size: <?php echo $text_font['font-size']; ?>;
	        line-height: <?php echo $text_font['line-height']; ?>;
	        <?php if(!empty($text_font['color'])){ ?>
	        	color:<?php echo $text_font['color'];?>;
	        <?php } ?>
	    }


		::-webkit-input-placeholder { font-family:<?php echo $text_font['font-family']; ?>; }
		::-moz-placeholder { font-family:<?php echo $text_font['font-family']; ?>; } /* firefox 19+ */
		:-ms-input-placeholder { font-family:<?php echo $text_font['font-family']; ?>; } /* ie */
		input:-moz-placeholder { font-family:<?php echo $text_font['font-family']; ?>; }


    <?php endif;?>


    <?php if(!empty($headline_font['font-family'])):?>
	    .seed-csp4 h1, .seed-csp4 h2, .seed-csp4 h3, .seed-csp4 h4, .seed-csp4 h5, .seed-csp4 h6{
	        font-family: <?php echo $headline_font['font-family']; ?>;
	        <?php if(!empty($headline_font['color'])){ ?>
	        	color:<?php echo $headline_font['color'];?>;
	        <?php }?>
	    }
		#cspv4-headline{
			font-size: <?php echo $headline_font['font-size']; ?>;
			font-weight: <?php echo $headline_font['font-weight']; ?>;
		}
    <?php endif;?>

    <?php if(!empty($button_font['font-family'])):?>
	    .seed-csp4 button{
	        font-family: <?php echo $button_font['font-family']; ?>;
	        font-weight: <?php echo $button_font['font-weight']; ?>;
	        font-size: <?php echo $button_font['font-size']; ?>;
	    }
    <?php endif;?>

    /* Link Styles */
    <?php if(!empty($button_font['color'])){ ?>
		.seed-csp4 a, .seed-csp4 a:visited, .seed-csp4 a:hover, .seed-csp4 a:active{
			color:<?php echo $button_font['color'];?>;
		}

		<?php

		$css = "

		   #cspv4-socialprofiles a{
			color: {$text_font['color']};
		  }

		  .buttonBackground(@startColor, @endColor) {
		  // gradientBar will set the background to a pleasing blend of these, to support IE<=9
		  .gradientBar(@startColor, @endColor);
		  *background-color: @endColor; /* Darken IE7 buttons by default so they stand out more given they won't have borders */
		  .reset-filter();

		  // in these cases the gradient won't cover the background, so we override
		  &:hover, &:active, &.active, &.disabled, &[disabled] {
		    background-color: @endColor;
		    *background-color: darken(@endColor, 5%);
		  }

		  // IE 7 + 8 can't handle box-shadow to show active, so we darken a bit ourselves
		  &:active,
		  &.active {
		    background-color: darken(@endColor, 10%) e(\"\9\");
		  }
		}

		.reset-filter() {
		  filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(enabled = false)\"));
		}

		.gradientBar(@primaryColor, @secondaryColor) {
		  #gradient > .vertical(@primaryColor, @secondaryColor);
		  border-color: @secondaryColor @secondaryColor darken(@secondaryColor, 15%);
		  border-color: rgba(0,0,0,.1) rgba(0,0,0,.1) fadein(rgba(0,0,0,.1), 15%);
		}

		#gradient {
			.vertical(@startColor: #555, @endColor: #333) {
		    background-color: mix(@startColor, @endColor, 60%);
		    background-image: -moz-linear-gradient(top, @startColor, @endColor); // FF 3.6+
		    background-image: -ms-linear-gradient(top, @startColor, @endColor); // IE10
		    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@startColor), to(@endColor)); // Safari 4+, Chrome 2+
		    background-image: -webkit-linear-gradient(top, @startColor, @endColor); // Safari 5.1+, Chrome 10+
		    background-image: -o-linear-gradient(top, @startColor, @endColor); // Opera 11.10
		    background-image: linear-gradient(top, @startColor, @endColor); // The standard
		    background-repeat: repeat-x;
		    filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(startColorstr='%d', endColorstr='%d', GradientType=0)\",@startColor,@endColor)); // IE9 and down
		  }
		}
		.lightordark (@c) when (lightness(@c) >= 65%) {
			color: black;
			text-shadow: 0 -1px 0 rgba(256, 256, 256, 0.3);
		}
		.lightordark (@c) when (lightness(@c) < 65%) {
			color: white;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
		}
		@btnColor: {$button_font['color']};
		@btnDarkColor: darken(@btnColor, 15%);
		.seed-csp4 .btn-primary, .seed-csp4 .btn-primary:focus, .gform_button, #mc-embedded-subscribe, .mymail-wrapper .submit-button {
		  .lightordark (@btnColor);
		  .buttonBackground(@btnColor, @btnDarkColor);
		  border-color: darken(@btnColor, 0%);
		}

		#cspv4-progressbar span,.countdown_section{
			.lightordark (@btnColor);
		}

		.seed-csp4 .btn-primary:hover,.seed-csp4 .btn-primary:active {
		  .lightordark (@btnColor);
		  border-color: darken(@btnColor, 10%);
		}

		.seed-csp4 input[type='text']{
			border-color: @btnDarkColor @btnDarkColor darken(@btnDarkColor, 15%);
		}

		@hue: hue(@btnDarkColor);
		@saturation: saturation(@btnDarkColor);
		@lightness: lightness(@btnDarkColor);
		.seed-csp4 input[type='text']:focus {
			border-color: hsla(@hue, @saturation, @lightness, 0.8);
			webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);

		}

		";

	try{
		$less = new seed_cspv4_lessc();
		$style = $less->parse($css);
		echo $style;

	} catch (Exception $e) {
		_e('An error has occured. Please make sure you have entered the Text Color correctly.','seedprod');
		die();
	}

		?>
    <?php }

    //Text Effects
     if(is_array($text_effects) && in_array('inset',$text_effects)){
    				$css = "
		    		.lightordarkshadow (@c) when (lightness(@c) >= 65%) {
						text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.8);
					}
					.lightordarkshadow (@c) when (lightness(@c) < 65%) {
						text-shadow: 0 -1px 0 rgba(256, 256, 256, 0.8);
					}
    				@text_color: {$text_font['color']};
    				body{
    					.lightordarkshadow (@text_color);
    				}
    				@headline_color: {$headline_font['color']};
    				h1, h2, h3, h4, h5, h6{
    					.lightordarkshadow (@headline_color);
    				}
    				@link_color: {$button_font['color']};
    				a, a:visited, a:hover, a:active{
    					.lightordarkshadow (@link_color);
    				}
    				";
    	try{
		        	$less = new seed_cspv4_lessc();
					$style = $less->parse($css);
					echo $style;

		} catch (Exception $e) {
			_e('An error has occured. Please make sure you have entered the Text Color correctly.','seedprod');
			die();
		}
    }

    //Container
    $enable_container = true;
    if(!empty($enable_container)){
    	$dropshadow = 0;
    	if(is_array($container_effects) && in_array('dropshadow',$container_effects)){
    		$dropshadow = 1;
    	}

    	$glow = 0;
    	if(is_array($container_effects) && in_array('glow',$container_effects)){
    		$glow = 1;
    	}

    	$border = 0;
    	$thickness = 0;
    	$border_color = 0;
    	if(!empty($container_border)){
    		$border = 1;
    		$thickness = $container_border['border-top'];
    		$border_style = $container_border['border-style'];
    		if(empty($container_border['border-color'])){
    			$border_color = ($link_color['color']);
    		}else{
    			$border_color = ($container_border['border-color']);
    		}

    	}


    	$roundedcorners = 0;
    	$radius = 0;
    	if(!empty($container_radius)){
    		$roundedcorners = 1;
    		$radius = ($container_radius) .'px';
    	}

    	$opacity = 1;

    	if(empty($container_color['color'])){
    		$container_color['color'] = "#000000";
    	}    	

    	if(empty($container_color['alpha'])){
    		$container_color['alpha'] = "0";
    	}

		$container_color['alpha'] = $container_color['alpha'] * 100;
		
    	$css = "
    	@dropshadow: $dropshadow;
		.dropshadow() when (@dropshadow = 1){
			-moz-box-shadow:    0px 11px 15px -5px rgba(69, 69, 69, 0.8);
			-webkit-box-shadow: 0px 11px 15px -5px rgba(69, 69, 69, 0.8);
			box-shadow: 0px 11px 15px -5px rgba(69, 69, 69, 0.8);
  		}
  		@glow: $glow;
		.glow() when (@glow = 1){
			-moz-box-shadow:    0px 0px 50px 5px {$container_color['color']};
			-webkit-box-shadow: 0px 0px 50px 5px {$container_color['color']};
			box-shadow: 0px 0px 50px 15px {$container_color['color']};
  		}
  		@border: $border;
  		@thickness: $thickness;
		.border() when (@border = 1){
			border: @thickness $border_style $border_color;
  		}
  		@roundedcorners: $roundedcorners;
  		@radius: $radius;
		.roundedcorners() when (@roundedcorners = 1){
			-webkit-border-radius: $radius;
			border-radius: $radius;
			-moz-background-clip: padding; -webkit-background-clip: padding-box; background-clip: padding-box;
  		}
  		@opacity: $opacity;
		.opacity() when (@opacity = 1){
			background-color: fade({$container_color['color']},{$container_color['alpha']});
  		}
    	#cspv4-content{
  			max-width: {$container_width['width']};
    		background-color: {$container_color['color']};
    		float: $container_position;
    		text-align: $container_position;
    		.dropshadow(); /* dropshadow */
    		.glow(); /* glow */
    		.border(); /* border */
    		.roundedcorners(); /* rounded corners */
    		.opacity(); /* opacity */
		}";
	try{
    	$less = new seed_cspv4_lessc();
		$style = $less->parse($css);
		echo $style;
	} catch (Exception $e) {
		_e('An error has occured. Please make sure you have entered the Border Color correctly.','seedprod');
		die();
	}
    }


	$output .= ob_get_clean();

	ob_start();
	if($container_flat){

		$css = "
		@primaryColor: {$button_font['color']};
		.seed-csp4 .progress-bar, .countdown_section, .seed-csp4 .btn-primary, .btn-primary:focus, .gform_button{
			background-image:none;
			text-shadow:none;
		}

		.countdown_section, .seed-csp4 .progress-bar{
		-webkit-box-shadow:none;
		box-shadow:none;
		}

		.seed-csp4 input, .seed-csp4 input:focus {
			border-color:@primaryColor !important;
			-webkit-box-shadow:none !important;
			box-shadow:none !important;
		}

		";

		$less = new seed_cspv4_lessc();
		$style = $less->parse($css);
		echo $style;

		$output .= ob_get_clean();

	}

	// Set background to black if a video is being used
	if(!empty($bg_video)){
		$output .= 'html{background-color:#000 ;}';
	}

	$output .= '</style>'.PHP_EOL;


	if(!empty($theme)){
		if($theme != 'default'){
		if($theme != 'wp'){
		$output .= '<link rel="stylesheet" href="'.apply_filters('seed_cspv4_themes_url',SEED_CSPV4_PLUGIN_URL).'style.css">'."\n";
		}}
	}


	// Typekit
	if(!empty($typekit_id)){
		$output .= "<!-- Typekit -->".PHP_EOL;
		$output .= '<script type="text/javascript" src="//use.typekit.com/'.$typekit_id.'.js"></script>'.PHP_EOL;
		$output .= '<script type="text/javascript">try{Typekit.load();}catch(e){}</script>'.PHP_EOL;
	}

	// Include JS
	$output .= "<!-- JS -->".PHP_EOL;
	$include_url = trailingslashit(includes_url());

	//Include jQuery
	if(empty($enable_wp_head_footer)){
		$output .= '<script src="'.$include_url.'js/jquery/jquery.js"></script>'."\n";
	}
	$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/bootstrap/js/bootstrap.min.js"></script>'."\n";

	//Include fitvid
	if(!empty($enable_fitvidjs)){
		$output .= "<!-- FitVid -->\n";
		$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/js/jquery.fitvids.js"></script>'."\n";
	}

	//Include Retina JS
	if(!empty($enable_retinajs)){
		$output .= "<!-- RetinaJS -->\n";
		$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/js/retina.min.js"></script>'."\n";
	}

	// Background video
	if(!empty($bg_video)){
		$output .= "<!-- Background Video -->\n";
		$bg_video_url_arr = '';
		parse_str( parse_url( $bg_video_url, PHP_URL_QUERY ), $bg_video_url_arr );

		if(strpos($bg_video_url, 'mp4') !== false){
			$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/js/video.js"></script>'."\n";
			$output .= '<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>'."\n";
			$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/js/bigvideo.js"></script>'."\n";
		}elseif(!empty($bg_video_url_arr['v'])){
			$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/js/jquery.tubular.1.0.js"></script>'."\n";
		}else{
			$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/js/okvideo.js"></script>'."\n";
		}
	}

	// Scripts
	$output .= "<!-- Scripts -->\n";
	$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/js/script.js"></script>'."\n";

	// Header Scripts
	if(!empty($header_scripts)){
		$output .= "<!-- Header Scripts -->\n";
		$output .= $header_scripts;
	}

	// GA
	if(!empty($ga_analytics)){
		$output .= "<!-- Google Analytics -->\n";
		$output .= $ga_analytics;
	}

	$output .= "<!-- Modernizr -->\n";
	$output .= '<script src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/js/modernizr.min.js"></script>'."\n";

	$output = apply_filters('seed_cspv4_head', $output);

	if ( $echo ){
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_footer', 'seed_cspv4_footer' );
function seed_cspv4_footer($echo = true){
	global $seed_cspv4, $seed_cspv4_post_result;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);


	$output = '';

	// Check if postback
	$is_post = false;
	if(!empty($seed_cspv4_post_result['status']) && $seed_cspv4_post_result['status'] == '200'){
		$is_post = true;
	}

	// WP Footer
	$enable_wp_head_footer_list = apply_filters('seed_cspv4_enable_wp_head_footer_list',array());
	if(in_array($emaillist,$enable_wp_head_footer_list)){
		$enable_wp_head_footer = '1';
	}

	if(!empty($enable_wp_head_footer)){
		$output .= "<!-- wp_footer() -->\n";
		ob_start();
		wp_footer();
		$output= ob_get_clean();
		$include_theme_stylesheet = seed_get_plugin_api_value('include_theme_stylesheet');
		if(empty($include_theme_stylesheet)){
			$output .= "<script>\n";
			$output .= 'jQuery(\'link[href*="'.get_stylesheet_directory_uri().'"]\').remove();';
			$output .= "</script>\n";
		}
	}

	//WPML
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if (is_plugin_active('wpml-string-translation/plugin.php')) {
		//var_dump($display_lang_switcher);
		if(!empty($display_lang_switcher)){
			ob_start();
			do_action('icl_language_selector');
			$output .= ob_get_clean();
		}
	}

	// Fitvid
	if(!empty($enable_fitvidjs)){
		$output .= "<script>\n";
		$output .= 'jQuery(document).ready(function($){$("#cspv4-description,#cspv4-thankyoumsg").fitVids();});';
		$output .= "</script>\n";
	}

	// Animate
	if(!empty($container_effect_animation)){
		$output .= "<script>\n";
		$output .= 'jQuery(document).ready(function($){$("#cspv4-content").addClass(\'animated '.$container_effect_animation.'\');});';
		$output .= "</script>\n";
	}



	// Backgound IOS Fix

	if(empty($bg_slideshow)){
		if(empty($bg_video)){
		if($background['background-size'] == 'cover' && !empty($background['background-image'])){
			/*$output .= '<!--[if lt IE 9]>
			<script>
			jQuery(document).ready(function($){';


			$output .= '$.supersized({';
			$output .= "slides:[ {image : '{$background['background-image']}'} ]";
			$output .= '});';


			$output .= '});
			</script>
			<![endif]-->';*/
			//
			// $output .= '
			// <script>
			// jQuery(document).ready(function($){
			// if (Modernizr.touch == true) {
			// ';
			// $output .= '$.supersized({';
			// $output .= "slides:[ {image : '{$background['background-image']}'} ]";
			// $output .= '});';
			// $output .= '
			// }
			// });
			// </script>
			// ';

			//$ios_bg_cover_hack = seed_get_plugin_api_value('ios_bg_cover_hack');
			//if($ios_bg_cover_hack == false){
				$output .= '
				<style>
				html {
				height: 100%;
				overflow: hidden;
				}
				body
				{
				height:100%;
				overflow: scroll;
				-webkit-overflow-scrolling: touch;
				}
				</style>
				';
			//}

		}
		}
	}




	if(!empty($bg_slideshow)){
		$output .= '
		<!-- Slideshow -->
		<style>
		 	#supersized{
				display:block;
			}
		</style>
		<script>
		jQuery(document).ready(function($){';

		$output .= '$.supersized({';

		if(!empty($bg_slideshow_slide_speed) && absint($bg_slideshow_slide_speed)){
			$output .= 'slide_interval:'.$bg_slideshow_slide_speed.','.PHP_EOL;
		}else{
			$output .= 'slide_interval:3000,'.PHP_EOL;
		}
		if(!empty($bg_slideshow_slide_transition) && absint($bg_slideshow_slide_transition)){
			$output .= 'transition:'.$bg_slideshow_slide_transition.','.PHP_EOL;
		}else{
			$output .= 'transition:1,'.PHP_EOL;
		}


		$slideshow_api = seed_get_plugin_api_value('slideshow_api');


		if(!empty($slideshow_api)){
			$output .= $slideshow_api;
		}
		$output .= 'transition_speed:700,'.PHP_EOL;
		$output .= 'fit_landscape:0,'.PHP_EOL;


		if(!empty($bg_slideshow_randomize)){
			$output .= 'random:1,'.PHP_EOL;
		}

		$output .= "slides:[\n";
		if(!empty($bg_slideshow_images)){
			foreach($bg_slideshow_images as $k=>$v){
				if($k !== 0){
				$output .= ",".PHP_EOL;
				}
				if(!empty($v['url'])){
					$output .= " {image : '".trim($v['url'])."'}"."\n";
				}else{
					$output .= " {image : '".trim($v['image'])."'}"."\n";
				}

			}
		}

		$output .= "]";
		$output .= '});});';
		$output .= '</script>';

	}

	// Background Video
	//var_dump($bg_video);
	if(!empty($bg_video)){
		if(!empty($bg_video_url)){
			// $adproof = 'false';
			// if(!empty($bg_video_adproof)){
			// 	$adproof = 'true';
			// }
			// $hd = 'false';
			// if(!empty($bg_video_hd)){
			// 	$hd = 'true';
			// }
			$bg_video_url_arr = '';
			parse_str( parse_url( $bg_video_url, PHP_URL_QUERY ), $bg_video_url_arr );
			//$bg_video_url_arr['v'];
			// Output: C4kxS1ksqtw

			if(strpos($bg_video_url, 'mp4') !== false){
				if(empty($bg_video_audio)){
					$audio = 'true';
				}else{
					$audio = 'false';
				}

				if(empty($bg_video_loop)){
					$loop = 'false';
				}else{
					$loop = 'true';
				}
				$output .= '
				<script>
				jQuery(document).ready(function($){
				if (Modernizr.touch == false) {
				';


				$output .= "var BV = new $.BigVideo();";
				$output .= "BV.init();";
				$output .= "BV.show('".$bg_video_url."',{ambient:".$audio.",doLoop:".$loop."});";
				$output .= "$('#big-video-wrap').show()";



				$output .= '
				}
				});
				</script>
				';
			}elseif(!empty($bg_video_url_arr['v'])){
				if(empty($bg_video_audio)){
					$audio = 'true';
				}else{
					$audio = 'false';
				}

				if(empty($bg_video_loop)){
					$loop = 'false';
				}else{
					$loop = 'true';
				}
				$output .= '
				<script>
				jQuery(document).ready(function($){
				if (Modernizr.touch == false) {
				';
				$output .= "$('#cspv4-page').tubular({ ";
				$output .= "videoId: '".$bg_video_url_arr['v']."',";
				// $output .= "hd: ".$hd.",";
				// $output .= "adproof: ".$adproof."";
				$output .= "mute: ".$audio.",";
				$output .= "repeat: ".$loop."";
				$output .= " })";


				$output .= '
				}
				});
				</script>
				';



			}else{
				if(empty($bg_video_audio)){
					$audio = '0';
				}else{
					$audio = '100';
				}
				if(empty($bg_video_loop)){
					$loop = 'false';
				}else{
					$loop = 'true';
				}
				$output .= '
				<script>
				jQuery(document).ready(function($){
				if (Modernizr.touch == false) {
				';


				$output .= "$.okvideo({ ";
				$output .= "video: '".$bg_video_url."',";
				// $output .= "hd: ".$hd.",";
				// $output .= "adproof: ".$adproof."";
				$output .= "volume: ".$audio.",";
				$output .= "loop: ".$loop."";
				$output .= " })";


				$output .= '
				}
				});
				</script>
				';
			}
		}
	}

	// Footer Scripts
	if(!empty($footer_scripts)){
		$output .= "<!-- Footer Scripts -->\n";
		$output .= $footer_scripts;
	}


	// Conversion Scripts
	if(!empty($conversion_scripts) && $is_post){
		$output .= "<!-- Conversion Scripts -->\n";
		$output .= $conversion_scripts;
	}


	$output = apply_filters('seed_cspv4_footer', $output);

	if ( $echo ){
		echo $output;
	} else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_credit', 'seed_cspv4_credit' );
function seed_cspv4_credit($echo = true){
	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';

	if(!empty($footer_affiliate_link)){
		$output = '<div id="cspv4-credit" style="background-color: rgba(0,0,0,0.8);">';
		$output .= '<a target="_blank" href="'.esc_url($footer_affiliate_link).'"><img src="'.SEED_CSPV4_PLUGIN_URL.'themes/default/images/seedprod-credit.png"/></a>';
		$output .= '</div>';
	}else{
		if(!empty($footer_credit_img['url'])){
			$output = '<div id="cspv4-credit">';
			$output .= '<a target="_blank" href="'.esc_url($footer_credit_link).'"><img src="'.esc_url($footer_credit_img['url']).'" /></a>';
			$output .= '</div>';
		}elseif(!empty($footer_credit_text)){
			$output = '<div id="cspv4-credit">';
			if(empty($footer_credit_link)){
				$output .= $footer_credit_text;
			}else{
				$output .= '<a target="_blank" href="'.esc_url($footer_credit_link).'">'.$footer_credit_text.'</a>';
			}
			$output .= '</div>';
		}
	}

	$output = apply_filters('seed_cspv4_credit', $output);

	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_logo', 'seed_cspv4_logo' );
function seed_cspv4_logo($echo = true){
	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';

	if(!empty($logo['url'])){
		$output .= "<img id='cspv4-logo' src='".esc_attr($logo['url'])."'>";
	}

	$output = apply_filters('seed_cspv4_logo', $output);

	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_headline', 'seed_cspv4_headline' );
function seed_cspv4_headline($echo = true){
	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';

	if(!empty($headline)){
		$output .= '<h1 id="cspv4-headline">'.$headline.'</h1>';
	}

	$output = apply_filters('seed_cspv4_headline', $output);

	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_description', 'seed_cspv4_description' );
function seed_cspv4_description($echo = true){
	global $seed_cspv4,$seed_cspv4_post_result;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$is_post = false;
	if(!empty($seed_cspv4_post_result['status']) && $seed_cspv4_post_result['status'] == '200'){
		$is_post = true;
	}

	$output = '';

	if(!empty($description) && $is_post === false){
		$content = $description;
		if(!empty($enable_wp_head_footer)){
			$content = apply_filters('the_content', $content);
			//if(isset($GLOBALS['wp_embed'])){
			//	$content = $GLOBALS['wp_embed']->autoembed($content);
			//}
			//$content = do_shortcode(shortcode_unautop(wpautop(convert_chars(wptexturize($content)))));
		}else{
			if(isset($GLOBALS['wp_embed'])){
				$content = $GLOBALS['wp_embed']->autoembed($content);
			}
			$content = do_shortcode(shortcode_unautop(wpautop(convert_chars(wptexturize($content)))));
		}
		$output .= '<div id="cspv4-description">'.$content.'</div>';
	}

	$output = apply_filters('seed_cspv4_description', $output);

	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_progressbar', 'seed_cspv4_progressbar' );
function seed_cspv4_progressbar($echo = true){
	global $seed_cspv4,$seed_cspv4_post_result;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$is_post = false;
	if(!empty($seed_cspv4_post_result['status']) && $seed_cspv4_post_result['status'] == '200'){
		$is_post = true;
	}

	$output = '';

	if(!empty($enable_progressbar) && $is_post === false){
		$class = '';
		if($progressbar_effect == 'striped'){
			$class = 'progress-striped';
		}elseif($progressbar_effect == 'animated'){
			$class = 'progress-striped active';
		}

		if(empty($progressbar_percentage)){
			if(empty($progress_bar_start_date) || empty($progress_bar_end_date)){
			}else{
				$start_date = strtotime($progress_bar_start_date);
				$end_date = strtotime($progress_bar_end_date);
				$today = time();
				$diff = abs($end_date - $start_date); // 8
				$complete = abs($start_date - $today); //4

				if($diff !==  0)
					$progressbar_percentage = ($complete/$diff) * 100;

				if($progressbar_percentage > 100){
				 	$progressbar_percentage = '100';
				}elseif($progressbar_percentage < 0){
					$progressbar_percentage = '0';
				}

				$progressbar_percentage = round($progressbar_percentage);
			}
		}


		$output .= '<div id="cspv4-progressbar">';
		$output .= '<div class="progress '.$class.'">';
		$output .= '<div class="progress-bar" style="width: '.$progressbar_percentage.'%;"><span>'.$progressbar_percentage.'%</span></div>';
		$output .= '</div>';
		$output .= '</div>';
	}

	$output = apply_filters('seed_cspv4_progressbar', $output);

	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_countdown', 'seed_cspv4_countdown' );
function seed_cspv4_countdown($echo = true){
	global $seed_cspv4,$seed_cspv4_post_result;
	$seed_cspv4 = get_option('seed_cspv4');
	$o = $seed_cspv4;

	$is_post = false;
	if(!empty($seed_cspv4_post_result['status']) && $seed_cspv4_post_result['status'] == '200'){
		$is_post = true;
	}

	if(empty($o['countdown_time_hour'])){
		$o['countdown_time_hour'] = '0';
	}
	if(empty($o['countdown_time_minute'])){
		$o['countdown_time_minute'] = '0';
	}

	$output = '';

	if(!empty($o['enable_countdown']) && $is_post === false){
		// Calulate offset
		date_default_timezone_set('UTC');
		$dt = getdate(strtotime($o['countdown_date'] .$o['countdown_time_hour'].':'.$o['countdown_time_minute']. ' UTC'));

		$tz = get_option('timezone_string');

	    if(!empty($tz)){
		    date_default_timezone_set($tz);
			$now = new DateTime();
			$seconds = $now->getOffset();
			$offset = floor($seconds/ 3600);
		}

		$tz = get_option('gmt_offset');

	    if(!empty($tz)){
			$offset = $tz;
		}

		if(empty($offset)){
			$offset = 0;
		}


		if(!empty($o['countdown_launch'])){
			$o['countdown_launch'] = home_url();
		}

		// Language Strings
		if(empty($o['txt_countdown_days'])){
			$o['txt_countdown_days'] = 'Days';
		}

		if(empty($o['txt_countdown_hours'])){
			$o['txt_countdown_hours'] = 'Hours';
		}

		if(empty($o['txt_countdown_minutes'])){
			$o['txt_countdown_minutes'] = 'Minutes';
		}

		if(empty($o['txt_countdown_seconds'])){
			$o['txt_countdown_seconds'] = 'Seconds';
		}

		if(empty($o['txt_countdown_day'])){
			$o['txt_countdown_day'] = 'Day';
		}

		if(empty($o['txt_countdown_hour'])){
			$o['txt_countdown_hour'] = 'Hour';
		}

		if(empty($o['txt_countdown_minute'])){
			$o['txt_countdown_minute'] = 'Minute';
		}

		if(empty($o['txt_countdown_second'])){
			$o['txt_countdown_second'] = 'Second';
		}

		if(empty($o['countdown_format'])){
			$o['countdown_format'] = 'dHMS';
		}

		$expiryUrl = '';
		if(!empty($o['countdown_launch'])){
			$expiryUrl = "expiryUrl: '".$o['countdown_launch']."?".rand()."',";
		}

		$output ="
		<script>
		jQuery(document).ready(function($){
			var endDate = new Date();
			endDate= new Date('".$dt['year']."', '".($dt['mon'] - 1)."', '".$dt['mday']."', '".$dt['hours']."', '".$dt['minutes']."', '00');

			$('#cspv4-countdown').countdown({
				labels: ['Years', 'Months', 'Weeks', '".$o['txt_countdown_days']."', '".$o['txt_countdown_hours']."', '".$o['txt_countdown_minutes']."', '".$o['txt_countdown_seconds']."'],
				labels1: ['Years', 'Months', 'Weeks', '".$o['txt_countdown_day']."', '".$o['txt_countdown_hour']."', '".$o['txt_countdown_minute']."', '".$o['txt_countdown_second']."'],
				until: endDate,
				timezone:".$offset.",
				".$expiryUrl."
				format: '".$o['countdown_format']."'
			});

		});
		</script>";
		$output .= '<div id="cspv4-countdown"></div>';

	}

	$output = apply_filters('seed_cspv4_countdown', $output);

	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_form', 'seed_cspv4_form' );
function seed_cspv4_form($echo = true){
	global $seed_cspv4,$seed_cspv4_post_result;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$is_post = false;
	if(!empty($seed_cspv4_post_result['status']) && $seed_cspv4_post_result['status'] == '200'){
		$is_post = true;
	}

	$output = '';

	// Get the refrral url
	$ref = '';
	if(isset($_GET['ref'])){
		$ref = $_GET['ref'];
	}

	// Form
	if($is_post === false){
		$output = apply_filters( 'seed_cspv4_show_form_'.$emaillist , $output );
		if($emaillist == 'feedburner'){
			$output .= '<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open(\'http://feedburner.google.com/fb/a/mailverify?uri='.esc_attr($feedburner_address).'\', \'popupwindow\', \'scrollbars=yes,width=550,height=520\');return true">';
			$output .= '<input type="hidden" value="'.esc_attr($feedburner_addr).'" name="uri"/>';
			$output .= '<input type="hidden" name="loc" value="'.esc_attr($feedburner_loc).'"/>';
			// Output form fields
			$output .= '<div id="cspv4-field-wrapper">';
			$output .= '<div class="row">';
			if($btn_style){
				$output .= '<div class="col-md-12"><div class="input-group"><input id="cspv4-email" name="email" class="form-control input-lg" type="text" placeholder="'.esc_attr($txt_email_field).'"/>';
				$output .= '<span class="input-group-btn"><button id="cspv4-subscribe-btn" type="submit" class="btn btn-lg btn-primary">'.esc_html($txt_subscribe_button).'</button></span></div></div>';
			}else{
				$output .= '<div class="col-md-12 seperate"><div class="input-group"><input id="cspv4-email" name="email" class="form-control input-lg" type="text" placeholder="'.esc_attr($txt_email_field).'"/>';
				$output .= '<span class="input-group-btn"><button id="cspv4-subscribe-btn" type="submit" class="btn btn-lg btn-primary">'.esc_html($txt_subscribe_button).'</button></span></div></div>';
			}
			$output .= '</div>';

			$output .= '</div>';
			$output .= '</form>';
		}elseif($emaillist == 'gravityforms'){
			if(class_exists('RGFormsModel')){
				ob_start();
				gravity_form($gravityforms_form_id, false, false, false, '', apply_filters('seed_cspv4_gf_ajax', false));
				$dump = ob_get_contents();
				ob_end_clean();
				if($gravityforms_enable_thankyou_page){
				$output .= '<div id="cspv4-alert" class="alert"></div>';
				}
				$output .= $dump;
			}
		}elseif($emaillist == 'htmlwebform'){
			if(!empty($html_integration))
				$output .=  $html_integration;
		}elseif($emaillist != 'none' && !empty($emaillist) && empty($output)){
			if (!empty($_SERVER['QUERY_STRING'])){
				$querystring = str_replace('&a=thankyou','',$_SERVER['REQUEST_URI']);
				$post = $querystring.'&a=thankyou';
			}else{
				$post = $_SERVER['REQUEST_URI'].'?a=thankyou';
			}
			$output .= '<form id="cspv4-form" method="post">';
			$output .= '<input id="cspv4-ref" name="ref" type="hidden" value="'.$ref.'" />';
			$output .= '<input id="cspv4-comment" name="comment" type="hidden" value="" />';

			// Check for an alert message
			$alert = '';
			if(!empty($seed_cspv4_post_result['msg'])){
				$alert = $seed_cspv4_post_result['msg'];
			}
			$class = '';
			if(!empty($seed_cspv4_post_result['msg_class'])){
				$class = $seed_cspv4_post_result['msg_class'];
			}

			if(!empty($alert)){
				$output .= '<div id="cspv4-alert" class="alert '.$class.'">'.$alert.'</div>';
			}
			// Output form fields
			$output .= '<div id="cspv4-field-wrapper">';
			$output .= '<div class="row">';
			if(!empty($name_field)){
				$output .= '<div class="col-md-12"><input id="cspv4-name" name="name" class="form-control input-lg" type="text" placeholder="'.esc_attr($txt_name_field).'"/></div>';
			}
			if($btn_style){
				$output .= '<div class="col-md-12"><div class="input-group"><input id="cspv4-email" name="email" class="form-control input-lg" type="text" placeholder="'.esc_attr($txt_email_field).'"/>';
				$output .= '<span class="input-group-btn"><button id="cspv4-subscribe-btn" type="submit" class="btn btn-lg btn-primary">'.esc_html($txt_subscribe_button).'</button></span></div></div>';
			}else{
				$output .= '<div class="col-md-12 seperate"><div class="input-group"><input id="cspv4-email" name="email" class="form-control input-lg" type="text" placeholder="'.esc_attr($txt_email_field).'"/>';
				$output .= '<span class="input-group-btn"><button id="cspv4-subscribe-btn" type="submit" class="btn btn-lg btn-primary">'.esc_html($txt_subscribe_button).'</button></span></div></div>';
			}
			$output .= '</div>';

			$output .= '</div>';
			$output .= '</form>';
			if(!empty($privacy_policy_link_text)){
				$output .= '<span id="cspv4-privacy-policy-txt">'.$privacy_policy_link_text.'</span>';
			}
			if(!empty($privacy_policy)){
				$output .= '<span id="cspv4-privacy-policy">'.$privacy_policy.'</span>';
			}
		}
	}


	// After Form is Submitted
	if($is_post === true){
		$output .= '<div id="cspv4-afterform">';
		// Check for an alert message
		$alert = '';
		if(!empty($seed_cspv4_post_result['msg'])){
			$alert = $seed_cspv4_post_result['msg'];
		}
		$class = '';
		if(!empty($seed_cspv4_post_result['msg_class'])){
			$class = $seed_cspv4_post_result['msg_class'];
		}
		if(!empty($alert)){
			$output .= '<div id="cspv4-alert" class="alert '.$class.'">'.$alert.'</div>';
		}
		if(empty($class)){
		$output .= '<div id="cspv4-thankyoumsg">';
		if(!empty($thankyou_msg)){
			if(isset($GLOBALS['wp_embed'])){
				$thankyou_msg = $GLOBALS['wp_embed']->autoembed($thankyou_msg);
			}
			$output .= do_shortcode(shortcode_unautop(wpautop(convert_chars(wptexturize($thankyou_msg)))));
		}else{
			$output .= '<p>'.esc_html($txt_success_msg).'</p>';
		}
		$output .= '</div>';

		if(!empty($enable_reflink)){
			$output .= '<div id="cspv4-ref-out" class="well">';
			$output .= seed_cspv4_ref_link();
			$output .= '</div>';
		}
		}

		$output .= '</div>';
	}

	$output = apply_filters('seed_cspv4_form', $output);

	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_socialprofiles', 'seed_cspv4_socialprofiles' );
function seed_cspv4_socialprofiles($echo = true){
	global $seed_cspv4;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$output = '';

	$is_empty = true;
	if(!empty($social_profiles)){
	foreach($social_profiles as $v){
		if(!empty($v)){
			$is_empty = false;
		break;
		}

	}
	}
	if(!$is_empty){
		$output .= '<div id="cspv4-socialprofiles">';
		foreach($social_profiles as $k=>$v){
			$upload_dir = wp_upload_dir();
			$icon_type = $social_profiles_type; //font or image//


			if(empty($social_profiles_blank)){
				$target = '';
			}else{
				$target = ' target="_blank"';
			}

			if($icon_type == 'image'){

				$icon_size_map = apply_filters('seed_cspv4_icon_size_map',array(
					'small' => '16px',
					'medium' => '24px',
					'large' => '32px',
				));

				if(is_multisite()){
					$testpath = $upload_dir['baseurl'].'/seedprod-icons/'.get_current_blog_id().'/'.strtolower($k).'.png';
				}else{
					$testpath = $upload_dir['basedir'].'/seedprod-icons/'.strtolower($k).'.png';
				}

				if(file_exists($testpath)){
					if(is_multisite()){
						$path = $upload_dir['baseurl'].'/seedprod-icons/'.get_current_blog_id().'/';
					}else{
						$path = $upload_dir['baseurl'].'/seedprod-icons/';
					}
					$icon_size = '';
					$icon_type = 'image';
				}else{
					$path = SEED_CSPV4_PLUGIN_URL.'themes/default/images/icons/';
					$icon_size = 'width:'.$social_profile_size.'px';
					$icon_type = 'image';
				}
				if(!empty($v)){
					if($k == 'Email'){
						$output .= '<a href="'.'mailto:'.$v.'"><img style="width:'.$icon_size_map[$social_profiles_size].'" src="'.$path.strtolower($k).'.png" /></a>';
					}else{
						$output .= '<a href="'.$v.'" '.$target.'><img style="width:'.$icon_size_map[$social_profiles_size].'" src="'.$path.strtolower($k).'.png" /></a>';
					}
				}
			}elseif($icon_type == 'font'){
				$icon_size_map = apply_filters('seed_cspv4_icon_size_map',array(
					'small' => '',
					'medium' => 'fa-lg',
					'large' => 'fa-2x',
				));
				$icon_map = apply_filters('seed_cspv4_icon_map',array(
					'facebook' => "<i class='fa fa-facebook-square {$icon_size_map[$social_profiles_size]}'></i>",
					'twitter' => "<i class='fa fa-twitter {$icon_size_map[$social_profiles_size]}'></i>",
					'linkedin' => "<i class='fa fa-linkedin {$icon_size_map[$social_profiles_size]}'></i>",
					'googleplus' => "<i class='fa fa-google-plus {$icon_size_map[$social_profiles_size]}'></i>",
					'youtube' => "<i class='fa fa-youtube {$icon_size_map[$social_profiles_size]}'></i>",
					'flickr' => "<i class='fa fa-flickr {$icon_size_map[$social_profiles_size]}'></i>",
					'vimeo' => "<i class='fa fa-vimeo-square {$icon_size_map[$social_profiles_size]}'></i>",
					'pinterest' => "<i class='fa fa-pinterest {$icon_size_map[$social_profiles_size]}'></i>",
					'instagram' => "<i class='fa fa-instagram {$icon_size_map[$social_profiles_size]}'></i>",
					'foursquare' => "<i class='fa fa-foursquare {$icon_size_map[$social_profiles_size]}'></i>",
					'skype' => "<i class='fa fa-skype {$icon_size_map[$social_profiles_size]}'></i>",
					'tumblr' => "<i class='fa fa-tumblr {$icon_size_map[$social_profiles_size]}'></i>",
					'github' => "<i class='fa fa-github {$icon_size_map[$social_profiles_size]}'></i>",
					'dribbble' => "<i class='fa fa-dribbble {$icon_size_map[$social_profiles_size]}'></i>",
					'slack' => "<i class='fa fa-slack {$icon_size_map[$social_profiles_size]}'></i>",
					'rss' => "<i class='fa fa-rss {$icon_size_map[$social_profiles_size]}'></i>",
					'email' => "<i class='fa fa-envelope {$icon_size_map[$social_profiles_size]}'></i>",
					));
				if(!empty($v)){
					if($k == 'email'){
						$output .= '<a href="'.'mailto:'.$v.'">'.$icon_map[$k].'</a>';
					}else{
						$output .= '<a href="'.$v.'" '.$target.'>'.$icon_map[$k].'</a>';
					}
				}
			}
		}
		$output .= '</div>';
	}

	$output = apply_filters('seed_cspv4_socialprofiles', $output);

	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}

add_shortcode( 'seed_cspv4_socialshares', 'seed_cspv4_socialshares' );
function seed_cspv4_socialshares($echo = true){
	global $seed_cspv4,$seed_cspv4_post_result;
	$seed_cspv4 = get_option('seed_cspv4');
	extract($seed_cspv4);

	$is_post = false;
	if(!empty($seed_cspv4_post_result['status']) && $seed_cspv4_post_result['status'] == '200'){
		$is_post = true;
	}


	$output = '';

	if($is_post || $show_sharebutton_on_front){

		$ref_link = seed_cspv4_ref_link();

		if(!empty($share_buttons)){

			if((isset($share_buttons['facebook']) && $share_buttons['facebook'] == '1') || (isset($share_buttons['facebook_send']) && $share_buttons['facebook_send'] == '1') ){
			$output .= '
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, \'script\', \'facebook-jssdk\'));</script>
								';
			}
			$output .= '<ul id="cspv4-sharebuttons">';
			if(isset($share_buttons['twitter']) && $share_buttons['twitter'] == '1'){
				$output .= '<li id="share_twitter"><a id="twitter-tweet-btn" class="twitter-share-button" data-url="'.$ref_link.'" data-text="'.esc_attr($tweet_text).'" data-count="none">Tweet</a>';
				$output .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></li>';
			}
			if(isset($share_buttons['facebook']) && $share_buttons['facebook'] == '1'){
				$output .= '<li id="share_facebook"><div class="fb-share-button" data-href="'.$ref_link.'"  data-type="button"></div></li>';
			}

			if(isset($share_buttons['facebook_send']) && $share_buttons['facebook_send'] == '1'){
				$output .= '<span id="share_facebook_send"><div class="fb-send" data-href="'.$ref_link.'"  data-layout="button_count"></div></span>';
			}
			if($share_buttons['googleplus'] == '1'){
				$output .= '<li id="share_googleplus"><div class="g-plusone" data-size="medium" data-annotation="none" data-href="'.$ref_link.'"></div>';
				$output .=  '<script type="text/javascript">
	  (function() {
	    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
	    po.src = \'https://apis.google.com/js/platform.js\';
	    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script></li>';
			}
			if(isset($share_buttons['linkedin']) && $share_buttons['linkedin'] == '1'){
				$output .= '<li id="share_linkedin"><script src="//platform.linkedin.com/in.js" type="text/javascript"></script>';
				$output .= '<script type="IN/Share" data-url="'.$ref_link.'"></script></li>';
			}
			if(isset($share_buttons['pinterest']) && $share_buttons['pinterest'] == '1'){
				$output .= '<li id="share_pinterest"><a href="http://pinterest.com/pin/create/button/?url='.urlencode($ref_link).'&media='.esc_url($pinterest_thumbnail['url']).'&description='.esc_attr($seo_description).'" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
				$output .= '<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script></li>';
			}
		// 	if($share_buttons['stumbledupon'] == '1'){
		// 		$output .= '<li id="share_stumbledupon"><su:badge layout="3"></su:badge>';
		// 		$output .= '<script type="text/javascript">
		//   (function() {
		//     var li = document.createElement(\'script\'); li.type = \'text/javascript\'; li.async = true;
		//     li.src = \'https://platform.stumbleupon.com/1/widgets.js\';
		//     var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(li, s);
		//   })();
		// </script></li>';
		// 	}
			if(isset($share_buttons['tumblr']) && $share_buttons['tumblr'] == '1'){
				$output .= '<li id="share_tumblr">';
				$output .= '<a href="http://www.tumblr.com/share/link?url='.urlencode($ref_link).'" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:81px; height:20px; background:url(\'//platform.tumblr.com/v1/share_1.png\') top left no-repeat transparent;">Share on Tumblr</a><script type="text/javascript" src="//platform.tumblr.com/v1/share.js"></script></li>';
			}
			$output .= '</ul>';
		};
	}

	$output = apply_filters('seed_cspv4_socialshares', $output);


	if ( $echo )
		echo $output;
	else {
		return $output;
	}
}
