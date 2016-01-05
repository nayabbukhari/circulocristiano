<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to X in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );



// Additional Functions
// =============================================================================

/**
 * for adding level to every user
 * author: ajay3085006
 * dated: 24 nov 15
 * updated: 31 Dec 15
 */
 add_action('bp_directory_members_actions','add_donation_level');
 
 function add_donation_level(){
	 echo '<div class="generic-button"><a >';
	 _e(' Mana : ', 'give'); //Mana are number of points or dollar donated 1 Mana = 1 dollar and there is no plural for Mana
	 $donations = give_get_users_purchases( bp_get_member_user_id(), 20, true, 'any' );
		
		$doanttion_per_user[0]=0;
		if ( $donations ) :
		foreach ( $donations as $post ) : setup_postdata( $post );
		 $doanttion_per_user[] = give_get_payment_amount( $post->ID );
		endforeach;
		wp_reset_postdata();
		endif;
		
		echo array_sum($doanttion_per_user);
		echo "</a></div>";
 }

/**
 * For select church
 * @author: ajay3085006
 * @dated: 25 nov 15
 * updated: 11 Dec 15
 */
 function circulocristiana_choose_church( $atts, $content = "" ) {
	$sites		=	wp_get_sites();
	$user_id	=	get_current_user_id();
	$church_id	=	$_POST['church-name'];
	
	if(!empty($church_id) && $church_id !='default'){
		update_user_meta( $user_id, 'church-name', $church_id);
	}
	//set disable submit button
	$church_btn_enable	=	'';
	$en					=	get_user_meta( $user_id, 'church-name', true);
	if($en){
		$church_btn_enable	=	'disabled';
	}
	//echo $en;
	?>
	<!--<span class='error'> Note: Will not be able to change later.</span>-->
	
	<form method="post">
	<?php
	_e("Select Church","give");
	echo "<select name='church-name'>";
	echo '<option value="default">select church</option>';
		foreach ( $sites as $i => $site ) {
			
			//hide root blog/site
			if($site['blog_id']==1)
			continue;
		
			$blog_details = get_blog_details($site['blog_id'],true);
			$sb			=	$site['site_id'].'-'.$site['blog_id'];
			
			$blog_name	=	$blog_details->blogname;
			if(strlen($blog_name) >= 25){
				$blog_name	=	substr($blog_details->blogname, 0, 25).'...';
			}
			
			$selected	=	'';
			if($en==$sb){$selected='selected';}
			echo "<option value='".$sb."' $selected>".$blog_name."</option>";
		}
	echo "</select>";
	
	?>
	<input type="Submit" name="Submit" value="Submit" <?php echo $church_btn_enable; ?>/>
	
	</form>
	<?php
	
}
add_shortcode( 'circulocristiana-choose-church', 'circulocristiana_choose_church' );

/**
 * To add location field to church or setting page 
 * @author: ajay3085006
 * @dated: 4 Dec 15
 * updated: 11 Dec 15
 * @link : for example https://circulocristiana.com/dev/wp-admin/options-general.php and other sub sites
 */
add_filter('admin_init', 'location_general_settings_register_fields'); 

function location_general_settings_register_fields() { 
register_setting('general', 'location_message', 'esc_attr'); 
add_settings_field('location_message', '<label for="location_message">'.__('Location' , 'location_message' ).'</label>' , 'my_general_location_message', 'general'); } 

function my_general_location_message() { 
$location_message = get_option( 'location_message', '' ); 
//echo '<input id="location_message" style="width: 35%;" type="text" name="location_message" value="' . $location_message . '" />'; 
?>
<textarea id="location_message" rows="7" cols="50" type="text" name="location_message"> <?php echo $location_message; ?> </textarea>
<?php
}
/**
 * Add new tab for my mission
 * @author: ajay3085006
 * @dated: 14 Dec 15
 * @updated: 14 Dec 15
 */

function my_bp_nav_adder()
{
bp_core_new_nav_item(
array(
'name' => __('My Mission', 'buddypress'),
'slug' => 'my-mission',
'position' => 75,
'show_for_displayed_user' => false,
'screen_function' => 'all_conversations_link',
'default_subnav_slug' => 'inbox', 
'item_css_id' => 'all-conversations'
));
}
function all_conversations_link () {
	
//add title and content here - last is to call the members plugin.php template
add_action( 'bp_template_title', 'my_all_conversations_title' );
add_action( 'bp_template_content', 'my_all_conversations_content' );
bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function my_all_conversations_title() {
_e('Donation History','wpml');
}
function my_all_conversations_content() {
echo do_shortcode("[donation_history]");
}
add_action( 'bp_setup_nav', 'my_bp_nav_adder', 100 );

/**
 * Desc: To add Pastor role before username who has pastor role to them on admin bar
 * author: ajay3085006
 * dated: 16 Dec 2015
 * updated: 16 Dec 2015
 */
function howdy_message($translated_text, $text, $domain) {
	if ( is_user_logged_in() ) {
		$user_cid	=	get_current_user_id( );
		$user_info = get_userdata($user_cid);
		 
		$role_text	=	implode(', ', $user_info->roles);

		$new_text	=	"Howdy ( ".ucfirst($role_text)." )";
		$new_message = str_replace('Howdy', $new_text, $text);
		return $new_message;
	}
}
if ( is_user_logged_in() ) {
add_filter('gettext', 'howdy_message', 10, 3);
}

/**
 * Desc: To Hide Biographical Info
 * author: ajay3085006
 * dated: 16 Dec 2015
 * updated: 16 Dec 2015
 */ 
 function x_custom_user_profile_style(){
	 ?>
	 <style>
	 .user-description-wrap {
		  display: none;
		}
	 </style>
	 <?php
 }
add_action('show_user_profile', 'x_custom_user_profile_style');
add_action('edit_user_profile', 'x_custom_user_profile_style');
 
 /**
 * Desc: Add panels for pastor to dashboard
 * author: ajay3085006
 * dated: 17 Dec 2015
 * updated: 17 Dec 2015
 */

 // Function that outputs the contents of the dashboard widget
function dashboard_widget_function( $post, $callback_args ) {
	 
	$args = array(
		'role'     => 'ccmember' //role name 
	);
	$blogusers = get_users($args);
	
	?>
	
		<div class="main">
		<ul>
			<li>
				<i class="fa fa-user fa-fw lg-2x"></i> 
				<a href="#" id="pastor_user_popup_start"> <?php echo count($blogusers); _e(' Member(s)','x'); ?></a>
			</li>
			<li>
					<i class="fa fa-money fa-fw"></i> 
					0 <?php  _e('Mana','x'); ?>
				</li>
			<li>
					<i class="fa fa-ticket fa-fw"></i> 
					0 <?php  _e('Tickets','x'); ?>
				</li>
			<li>
					<i class="fa fa-circle-o fa-fw"></i> 
					0 <?php  _e('Missions','x'); ?>
			</li>
			<li>
					<i class="fa fa-envelope  fa-fw"></i> 
					0 <?php  _e('Newsletter','x'); ?>
			</li>
		
		</ul>
			
		</div>
		<div class="sub">
		</div>
<div id="dialog-pastor-popup" title="Users" style="display:none;">
  <div class="pastor_getter">
  <p>This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.
  </p>
  </div>
</div>
<?php     wp_enqueue_script('jquery'); ?>
<?php     wp_enqueue_script('jquery-ui-dialog'); ?>
<?php wp_enqueue_style (  'wp-jquery-ui-dialog'); ?>
<script>
  jQuery(function($) {
    $( "#dialog-pastor-popup" ).dialog({
		  buttons: [
			{
			  text: "Close",
			  click: function() {
				$( this ).dialog( "close" );
			  }
		 
			  // Uncommenting the following line would hide the text,
			  // resulting in the label being used as a tooltip
			  //showText: false
			}
		  ],
		minHeight: 400,
		maxHeight: 400,
		minWidth: 500,
		modal:true,
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "explode",
        duration: 1000
      }
    });
	$( "#pastor_user_popup_start" ).click(function(){
		$( "#dialog-pastor-popup" ).dialog( "open" );
		
		 var ajaxurl = '<?php bloginfo('home'); ?>/wp-admin/admin-ajax.php';
		var data = {
			'action': 'pastor_action',
			'whatever': 1234
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			
			 jQuery(".pastor_getter").html(response);
		});
		
	});
  });
  jQuery( "#dialog-pastor-popup" ).on( "dialogopen", function( event, ui ) {
	  //console.log("tab openend");
	  jQuery(".pastor_getter").html("this is new content");
	  var img	="<?php echo  site_url("/wp-admin/images/loading.gif"); ?>";
	  jQuery(".pastor_getter").html("<center>Loading ... <img src='"+img+"'/></center>");
	  jQuery('#dialog-pastor-popup').css('overflow','scroll');
	  
  } );
  </script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<style>
#dashboard_widget li {
  display: inline-block;
  width: 45%;
  margin-bottom:10px;
}
#dashboard_widget .main {
  width: 100%;
}
.pastor_getter th, .pastor_getter td {
  padding: 5px;
}
</style>

	<?php
}

// Function used in the action hook
function add_dashboard_widgets() {
	if ( is_user_logged_in() ) {
		$user_cid	=	get_current_user_id( );
		$user_info = get_userdata($user_cid);
		 
		$role_text	=	implode(', ', $user_info->roles);
		if('pastor'==$role_text){
			wp_add_dashboard_widget('dashboard_widget', 'Pastor Dashboard', 'dashboard_widget_function');
		}
	
	}
}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'add_dashboard_widgets' );


//receive ajax pastor action 
add_action( 'wp_ajax_pastor_action', 'pastor_action_callback' );

function pastor_action_callback() {
	global $wpdb; // this is how you get access to the database

	$whatever = intval( $_POST['whatever'] );
	
	
	$args = array(
		'role'     => 'ccmember' //role name 
	);
	$blogusers = get_users($args);
	
	?>
	<center>
	<table border="1">
		<tbody>
			<tr>
				<th>User ID</th>				
				<th>Login Name </th>				
				<th>Display Name</th>				
				<th>Registration Date </th>				
			</tr>
		<?php foreach ( $blogusers as $user ) {?>	
			<tr>
				<td> <?php echo $user->ID; ?> </td>
				<td> <?php echo $user->user_login; ?> </td>
				<td> <?php echo $user->display_name; ?> </td>
				<td> <?php echo $user->user_registered; ?> </td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	</center>
	<?php 
	wp_die(); // this is required to terminate immediately and return a proper response
}

 /**
 * Desc: To hide essential grid from pastor
 * author: ajay3085006
 * dated: 19 Dec 2015
 * updated: 19 Dec 2015
 */
add_action('in_admin_footer', 'hide_eg_from_pastor');
 function hide_eg_from_pastor(){
		$user_cid	=	get_current_user_id( );
		$user_info = get_userdata($user_cid);
		 
		$role_text	=	implode(', ', $user_info->roles);
		if('pastor'==$role_text){
			?>
			<style>
				#eg-meta-box {
				  display: none;
				}
			</style>
			<?php 
		}
}
 /**
 * Desc: Register pop up scripts
 * author: ajay3085006
 * dated: 23 Dec 2015
 * updated: 23 Dec 2015
 */
function ajax_auth_init(){	
	wp_register_style( 'ajax-auth-style', get_stylesheet_directory_uri() . '/css/ajax-auth-style.css' );
	wp_enqueue_style('ajax-auth-style');

	//bootstrap css 
	wp_register_style( 'ajax-auth-style-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css' );
	//wp_enqueue_style('ajax-auth-style-bootstrap');
	
	wp_register_script('validate-script', get_stylesheet_directory_uri() . '/js/jquery.validate.js', array('jquery') ); 
    wp_enqueue_script('validate-script');

    wp_register_script('ajax-auth-script', get_stylesheet_directory_uri() . '/js/ajax-auth-script.js', array('jquery') ); 
    wp_enqueue_script('ajax-auth-script');

    wp_register_script('ajax-auth-script-bootstrap', get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array('jquery') ); 
    //wp_enqueue_script('ajax-auth-script-bootstrap');

    wp_localize_script( 'ajax-auth-script', 'ajax_auth_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        /* 'redirecturl' => home_url(), */
        'redirecturl' => "http://circulocristiano.com/memberships/",
        'loadingmessage' => __('Sending user info, please wait...')
    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
	// Enable the user with no privileges to run ajax_register() in AJAX
	add_action( 'wp_ajax_nopriv_ajaxregister', 'ajax_register' );
}

// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    //add_action('init', 'ajax_auth_init');
    //add_action('wp_head', 'ajax_register_form');
}
function ajax_register_form(){
	?>
		
<!-- modal -->
		
						<?php
							if (!is_user_logged_in())
							{
							?>
							<form id="login" class="ajax-auth" action="login" method="post">
								<h3>New to site? <a id="pop_signup" href="">Create an Account</a></h3>
								<hr />
								<!--<h1>Login</h1>-->
								<p class="status"></p>  
								<?php wp_nonce_field('ajax-login-nonce', 'security'); ?>  
								<label for="username">Username</label>
								<input id="username" type="text" class="required" name="username">
								<label for="password">Password</label>
								<input id="password" type="password" class="required" name="password">
								<input class="submit_button" type="submit" value="LOGIN">
								<a class="close" href="">(close)</a>    
							</form>

							<form id="register" class="ajax-auth"  action="register" method="post">
								<!--<h3>Already have an account? <a id="pop_login_"  href="#">Login</a></h3>-->
								<h1>Signup</h1>
								<hr />
								
								<p class="status"></p>
								<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?>         
								<label for="signonname">Username</label>
								<input id="signonname" type="text" name="signonname" class="required">
								<label for="email">Email</label>
								<input id="email" type="text" class="required email" name="email">
								<label for="signonpassword">Password</label>
								<input id="signonpassword" type="password" class="required" name="signonpassword" >
								<label for="password2">Confirm Password</label>
								<input type="password" id="password2" class="required" name="password2">
								<?php
								$sites		=	wp_get_sites();
									_e("Select Church","x");
									echo "<select name='x_church_name' class='required' id='x_church_name'>";
									echo '<option value="">select church</option>';
										foreach ( $sites as $i => $site ) {
											
											//hide root blog/site
											if($site['blog_id']==1)
											continue;
										
											$blog_details = get_blog_details($site['blog_id'],true);
											$sb			=	$site['site_id'].'-'.$site['blog_id'];
											
											$blog_name	=	$blog_details->blogname;
											if(strlen($blog_name) >= 25){
												$blog_name	=	substr($blog_details->blogname, 0, 25).'...';
											}
											
											$selected	=	'';
											if($en==$sb){$selected='selected';}
											echo "<option value='".$sb."' $selected>".$blog_name."</option>";
										}
									echo "</select>";
									
									?>
								
								
								
								<input class="submit_button" type="submit" value="SIGNUP">
								<a class="close" href="">(close)</a>    
							</form>			

							<?php
							}
							?>
					
		<!-- /modal -->
	<?php 
	
}
  
function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
  	// Call auth_user_login
	auth_user_login($_POST['username'], $_POST['password'], 'Login'); 
	
    die();
}

function ajax_register(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-register-nonce', 'security' );
		
    // Nonce is checked, get the POST data and sign user on
    $info = array();
  	$info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['first_name'] = $info['user_login'] = sanitize_user($_POST['username']) ;
    $info['user_pass'] = sanitize_text_field($_POST['password']);
	$info['user_email'] = sanitize_email( $_POST['email']);
	$info['blog_id'] =  $_POST['blog_id'];
	$pieces = explode("-", $info['blog_id']);
	$info['blog_id']	=	$pieces[1]; // piece2
		
	// Register the user
	global $switched;
    switch_to_blog($info['blog_id']);
    $user_register = wp_insert_user( $info );
 	if ( is_wp_error($user_register) ){	
		$error  = $user_register->get_error_codes()	;
		
		if(in_array('empty_user_login', $error))
			echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login'))));
		elseif(in_array('existing_user_login',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('This username is already registered.')));
		elseif(in_array('existing_user_email',$error))
        echo json_encode(array('loggedin'=>false, 'message'=>__('This email address is already registered.')));
    } else {
		$role	=	'ccmember'; //please update it to role name need to assign to new user
		add_user_to_blog($info['blog_id'], $user_register, $role);
		auth_user_login($info['nickname'], $info['user_pass'], 'Registration');       
    }

    die();
}

function auth_user_login($user_login, $password, $login)
{
	$info = array();
    $info['user_login'] = $user_login;
    $info['user_password'] = $password;
    $info['remember'] = true;
	
	$user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
		echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
    } else {
		wp_set_current_user($user_signon->ID); 
        echo json_encode(array('loggedin'=>true, 'message'=>__($login.' successful, redirecting...')));
    }
	
	die();
}
 /**
 * Desc: Change name of meta box Give form
 * author: ajay3085006
 * dated: 24 Dec 2015
 * updated: 24 Dec 2015
 */
 function x_filter_meta_box_give($fields) {
	 
	 $fields[0]['name']		=	"Explain Better";
 
	return $fields;
}
add_filter('give_forms_content_options_metabox_fields', 'x_filter_meta_box_give');

/**
 * Desc: Sermon profile on sermon post sidebar
 * author: ajay3085006
 * dated: 25 Dec 2015
 * updated: 26 Dec 2015
 */
 function x_sermon_profile($fields) {
	 ?>
	   <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <div class="x_sermon_profile">
			<?php  $author_id	=  get_the_author_meta( 'ID'); 
			$blog_id = get_current_blog_id();
			$args = array(
				'role'     => 'ccmember' //role name 
			);
			$blogusers = get_users($args);
	?>
		<div class="item-wrap">
				<div class="item-cover">
					<div class="item-avatar">
						<a href="<?php 	echo   bp_core_get_user_domain( $author_id ); ?>">
						<?php echo get_avatar($author_id); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item-title">
						<a href="<?php 	echo   bp_core_get_user_domain( $author_id ); ?>"><?php the_author_meta( 'display_name'); ?></a>
					</div>
					<div class="item-meta"> 
						<a href="<?php echo get_blog_option( $blog_id, 'siteurl' );?>"><?php echo get_blog_option( $blog_id, 'blogname' );?></a>
					</div>
					<div class="item-meta">
						<span> 
						<!--<?php _e('Members :','x'); ?> <a href=""> 3 <?php //echo get_blog_option( $blog_id, 'blogname' );?></a>-->
							<i class="fa fa-user fa-fw lg-2x"></i> 
							<a href="<?php echo get_blog_option( $blog_id, 'siteurl' );?>/members/"> <?php echo count($blogusers); _e(' Member(s)','x'); ?></a>
						<span>
						<span class="x-seperater">|<span>
						<span>
							<i class="fa fa-money fa-fw"></i> 
							 <?php echo x_mana_by_blog_id($blog_id)." "; _e('Mana','x'); ?>
						</span>
					</div>
				</div><!-- end item -->
			</div>
          </div>
        </article>
	 <?php 
	 //eneque font-awesome
	wp_register_style('x-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css
' );
	wp_enqueue_style('x-font-awesome');
}
add_action('sermon_profile', 'x_sermon_profile');
/**
 * Desc:  Filter wp_nav_menu() to add profile link in a specific custom menu e.g. Login or logout
 * author: ajay3085006
 * dated: 27 Dec 2015
 * updated: 30 Dec 2015
 */
function x_nav_menu_signup_logout_link($menu, $args) {
	//print_r($args);
    if( is_user_logged_in() && $args->theme_location == 'primary' ){
        $profilelink = '<li><a href="' . wp_logout_url( get_permalink() ) . '">' . __('Logout', 'x') . '</a></li>';
        $menu = $menu . $profilelink;
    }else if($args->theme_location == 'primary'){
		
		$url = MS_Model_Pages::get_page_url( MS_Model_Pages::MS_PAGE_REGISTER );
		
		$profilelink = '<li><a href="'.$url.'?step=register" class="show-register" >' . __('Signup', 'x') . '</a></li>';
        $menu = $menu . $profilelink;
	}else{}
    return $menu;
}
add_filter( 'wp_nav_menu_items', 'x_nav_menu_signup_logout_link', 10, 2 );

/**
 * Desc:  To add pastor by mana level
 * author: ajay3085006
 * dated: 28 Dec 2015
 * updated: 28 Dec 2015
 */
function x_pastor_level_widget(){
//if($_SERVER['REMOTE_ADDR']=='124.253.198.108'){
		
	// get all blogs 
	$sites		=	wp_get_sites();
	foreach ( $sites as $i => $site ) {
		
		//hide root blog/site
		//if($site['blog_id']==1)
		//continue;
	
		$blog_details = get_blog_details($site['blog_id'],true);
		$sb			=	$site['site_id'].'-'.$site['blog_id'];
		$blog_id	=	$site['blog_id'];
		
		$blog_name	=	$blog_details->blogname;
		if(strlen($blog_name) >= 25){
			$blog_name	=	substr($blog_details->blogname, 0, 25).'...';
		}
				// get alluser per blog/site to calculate doantion = mana 
				$args = array(
						//'role'     => 'ccmember' //role name 
						'blog_id'      => $site['blog_id'],
					);
				$blogusers = get_users($args);
			
				
				$donation_per_site	=	0;
				foreach ( $blogusers as $user ) {
						//echo $user->ID." -- ".$user->display_name." -- ".$user->roles[0]."--".x_donation_by_user_id($user->ID)."<br/>";
						$mana	=	x_donation_by_user_id($user->ID);
						//echo "i only donate $mana <br/>";
						$donation_per_site	= $donation_per_site +	$mana;	
						//echo "new added value $donation_per_site";
				}
				//echo "total Donation: ".$donation_per_site;
				$mana_ps[$blog_id]	=	$donation_per_site;
				
	}
	
	// for sorting user by mana
	asort($mana_ps);
	
	if($_REQUEST['order']=='r'){
		arsort($mana_ps); // 0 1 ,2 
	}
		
	$ordered = array();
    foreach($mana_ps as $key => $value) {
        if(array_key_exists($key,$sites)) {
                $ordered[$key] = $sites[$key];
                unset($sites[$key]);
        }
    }
    $ordered =  $ordered + $sites;
	asort($ordered);
	if($_REQUEST['order']=='r'){
	arsort($ordered);
	}

	foreach ( $ordered as $i => $site ) {
		
		//hide root blog/site
		//if($site['blog_id']==1)
		//continue;
	
		$blog_details = get_blog_details($site['blog_id'],true);
		$sb			=	$site['site_id'].'-'.$site['blog_id'];
		$blog_id	=	$site['blog_id'];
		
		$blog_name	=	$blog_details->blogname;
		if(strlen($blog_name) >= 25){
			$blog_name	=	substr($blog_details->blogname, 0, 25).'...';
		}
		//get pastor from all blog and list there mana with name 
			$args = array(
					'role'     => 'pastor', //role name 
					'blog_id'      => $blog_id,
				);
			$blogusers = get_users($args);
			?>
			<ul class="x_pastor_conatiner">
			<?php 
			
			foreach ( $blogusers as $user ) {
				?>
				<li>
				<div class="item-cover">
					
					<div class="item-avatar">
						<a href="<?php 	echo   bp_core_get_user_domain( $user->ID ); ?>">
						
						<?php echo get_avatar($user->ID); ?>
						</a>
					</div>

				</div>

				<div class="item">

					<div class="item-title">
						<a href="<?php 	echo   bp_core_get_user_domain( $user->ID ); ?>"><?php //_e('Pastor ','x');
						the_author_meta( 'display_name', $user->ID); ?></a>
						<span><?php echo $blog_name;?></span>
					</div>
					<div class="item-meta"> 
						<?php _e('Mana : ','x'); echo $mana_ps[$blog_id]; ?>
					</div>
				</div>
				</li>
				<?php 			
			}
			?>
			</ul>
			<?php 
	}
//add_user_to_blog(2, 12, 'pastor');
//} //EO only ip 

 }
 add_action('x_pastor_widget', 'x_pastor_level_widget');
 //add_action('wp_footer', 'x_pastor_level_widget');

//get donation by user id
function x_donation_by_user_id($user_id){
	$donations = give_get_users_purchases( $user_id, 20, true, 'any' );
	
	$doanttion_per_user[0]=0;
	if ( $donations ) :
	foreach ( $donations as $post ) : setup_postdata( $post );
	 $doanttion_per_user[] = give_get_payment_amount( $post->ID );
	endforeach;
	wp_reset_postdata();
	endif;
	
	return array_sum($doanttion_per_user);
	
}
/**
 * Desc:  pastor widgets 
 * author: ajay3085006
 * dated: 28 Dec 2015
 * updated: 28 Dec 2015
 */
 
 class x_pastor_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'x_pastor_widget', // Base ID
            'X Pastor Widget', // Name
            array( 'description' => __( 'A pastors widget ranked by mana ', 'https://profiles.wordpress.org/ajay3085006/#content-plugins' ), ) // Args
        );
    }

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
         $text = apply_filters( 'widget_text', $instance['text'] );

        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];
         echo $args['before_text'] . $text . $args['after_text'];
       // echo __( 'scroll posts', 'text_domain' );
        echo $args['after_widget'];

		// output
		// add action for pastor widgets 
		do_action('x_pastor_widget');
		// output ends 
    }
   public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'Pastor List by Mana', 'x' );
        }
        
        ?>
        <p>
        <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
               <?php 
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] .= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
          return $instance;
    }
} 
function x_pastor_widget_reg() {
    register_widget( 'x_pastor_widget' );
}
add_action( 'widgets_init', 'x_pastor_widget_reg' );
/**
 * Desc:  Mana by blog id
 * author: ajay3085006
 * dated: 29 Dec 2015
 * updated: 29 Dec 2015
 */
function x_mana_by_blog_id($blog_id){
	// get alluser per blog/site to calculate doantion = mana 
	$args = array(
			//'role'     => 'ccmember' //role name 
			'blog_id'      => $blog_id,
		);
	$blogusers = get_users($args);
	
	$donation_per_site	=	0;
	foreach ( $blogusers as $user ) {
			$mana	=	x_donation_by_user_id($user->ID);
			$donation_per_site	= $donation_per_site +	$mana;	
	}
	return $donation_per_site;
}
/**
 * Add cutom field to registration form
 * author: ajay3085006
 * dated: 29 Dec 2015
 * updated: 29 Dec 2015
 */

add_action('signup_extra_fields','x_show_church_field'); //for multisite 
add_action('register_form','x_show_first_name_field');
add_action('register_post','x_check_fields',10,3);
add_action('user_register', 'x_register_extra_fields');

function x_show_church_field()
{
?>
    <!--<p>
    <label>Twitter<br/>
    <input id="twitter" type="text" tabindex="30" size="25" value="<?php echo $_POST['twitter']; ?>" name="twitter" />
    </label>
    </p>-->
	<div class="ms-form-element ms-form-element-church">
	<span class="wpmui-wrapper wpmui-input-wrapper ">
		
	<?php
	$sites		=	wp_get_sites();
		_e("Select Church","x");
		echo "<select name='x_church_name' class='required' id='x_church_name'>";
		echo '<option value="">select church</option>';
			foreach ( $sites as $i => $site ) {
				
				//hide root blog/site
				if($site['blog_id']==1)
				continue;
			
				$blog_details = get_blog_details($site['blog_id'],true);
				$sb			=	$site['site_id'].'-'.$site['blog_id'];
				
				$blog_name	=	$blog_details->blogname;
				if(strlen($blog_name) >= 25){
					$blog_name	=	substr($blog_details->blogname, 0, 25).'...';
				}
				
				$selected	=	'';
				if($en==$sb){$selected='selected';}
				echo "<option value='".$sb."' $selected>".$blog_name."</option>";
			}
		echo "</select>";
		
		?>
		<label id="church-error" class="ms-validation-error" for="church" style="display: none;"></label>
	</span>
	</div>
								
<?php
}

function x_check_fields ( $login, $email, $errors )
{
    global $x_church_name;
    if ( $_POST['x_church_name'] == '' )
    {
        $errors->add( 'empty_realname', "<strong>ERROR</strong>: Please Enter your twitter handle" );
    }
    else
    {
        $x_church_name = $_POST['x_church_name'];
    }
}

function x_register_extra_fields ( $user_id, $password = "", $meta = array() )
{
    //update_user_meta( $user_id, 'twitter', $_POST['twitter'] );
    //update_user_meta( $user_id, 'twitter', $_POST['x_church_name'] );
	$info['blog_id'] =  $_POST['x_church_name'];
	$pieces = explode("-", $info['blog_id']);
	$info['blog_id']	=	$pieces[1]; // piece2
	switch_to_blog($info['blog_id']);
	$role	=	'ccmember'; //please update it to role name need to assign to new user
	add_user_to_blog($info['blog_id'], $user_id, $role);
}
/**
 * Pastors need to look a certain way to members.  When a member sees the pastor's name, it should have the word "Pastor" in front if it. 
 * author: ajay3085006
 * dated: 31 Dec 2015
 * updated: 31 Dec 2015
 */
function x_pastor_dispay_name($bp_name){
	global $authordata;
	return x_roles_in_array($authordata->roles,$bp_name);
}
 
 add_filter('the_author','x_pastor_dispay_name',10,1);
 add_filter('the_author_display_name','x_pastor_dispay_name',10,1);
 
//in buddypress  change name 
function x_pastor_dispay_name_bp($bp_name){
	return x_pastor_dispay_name_callback($bp_name,bp_get_member_user_id() );
}
//for widget sidebar http://circulocristiano.com/blog/sermon/test-sermones-post/
add_filter('bp_get_member_name','x_pastor_dispay_name_bp',10,1);


/**
 * in buddypress  change name user page , for single member page e.g http://circulocristiano.com/members/lunes/
 * author: ajay3085006
 * dated: 31 Dec 2015
 * updated: 31 Dec 2015
 */
function x_pastor_dispay_name_single_member_page($bp_name){
	global $bp;
	return x_pastor_dispay_name_callback($bp_name,$bp->displayed_user->id );
}
//at members page  W
add_filter('bp_displayed_user_fullname','x_pastor_dispay_name_single_member_page',10,1);

/**
 * in buddypress  change name user page , for activity page e.g http://circulocristiano.com/members/lunes/
 * author: ajay3085006
 * dated: 31 Dec 2015
 * updated: 31 Dec 2015
 */
function x_pastor_dispay_name_activity_page($bp_name ,$user_id){
	return x_pastor_dispay_name_callback($bp_name,$user_id );
}
add_filter('bp_core_get_user_displayname','x_pastor_dispay_name_activity_page',10,2);

/**
 * in buddypress  change name user page comments  e.g http://circulocristiano.com/members/lunes/
 * author: ajay3085006
 * dated: 31 Dec 2015
 * updated: 31 Dec 2015
 */
function x_pastor_dispay_name_activity_page_comment($bp_name ){
	global $activities_template; 
	return x_pastor_dispay_name_callback($bp_name,$activities_template->activity->current_comment->user_id );
}
//for activiy comment page 
add_filter('bp_activity_comment_name','x_pastor_dispay_name_activity_page_comment',10,1);

/**
 * in buddypress  display name callback 
 * arg : display name and user role array
 * return : string 
 * author: ajay3085006
 * dated: 31 Dec 2015
 * updated: 31 Dec 2015
 */
function x_pastor_dispay_name_callback($bp_name,$user_id ){
	$authordata =	get_userdata(  $user_id );
	return x_roles_in_array($authordata->roles,$bp_name);
}
function x_roles_in_array($roles,$bp_name){
	if(is_array($roles)){
	if (in_array("pastor", $roles)) {
		return 'Pastor '.$bp_name;
	}
	}
	return $bp_name;
}

/**
 * in buddypress  profile page add child tab 
 * arg : tab and index
 * return : string 
 * author: ajay3085006
 * dated: 2 Jan 2016
 * updated: 2 Jan 2016
 */
 
function x_add_child_tab(){
bp_core_new_nav_item(
	array(
		'name' => __('My Childrens', 'buddypress'),
		'slug' => 'my-childrens',
		'position' => 75,
		'show_for_displayed_user' => false,
		'screen_function' => 'x_add_child_tab_template',
		'default_subnav_slug' => 'inbox', 
		'item_css_id' => 'all-conversations'
	));
}
function x_add_child_tab_template(){
	//add title and content here - last is to call the members plugin.php template
	add_action( 'bp_template_title', 'x_add_child_tab_title' );
	add_action( 'bp_template_content', 'x_add_child_tab_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function x_add_child_tab_title(){
	_e('My Childrens','x');
}
function x_add_child_tab_content(){
	wp_enqueue_script('jquery'); 
	wp_enqueue_script('jquery-ui-dialog'); 
	wp_enqueue_script('jquery-effects-fade'); 
	wp_enqueue_script('jquery-effects-fold'); 
	wp_enqueue_script('jquery-effects-explode'); 
	wp_enqueue_style (  'wp-jquery-ui-dialog'); ?>
	<input type="submit" value="Add a child account" id="x_add_child_pop_up_open"/>
	<?php
	$user_ID = get_current_user_id();
	$noem	=	get_user_meta($user_ID, 'numberofemails', true);
	$x_child_emails_1	=	get_user_meta($user_ID, 'x_child_emails_1', true);
	$x_child_emails_2	=	get_user_meta($user_ID, 'x_child_emails_2', true);
	$x_child_emails_3	=	get_user_meta($user_ID, 'x_child_emails_3', true);
	$x_child_emails_4	=	get_user_meta($user_ID, 'x_child_emails_4', true);
	$x_child_emails_5	=	get_user_meta($user_ID, 'x_child_emails_5', true);
	?>
	<?php if($x_child_emails_1): ?>
	<center>
	<table border="1">
		<tbody>
			<tr>
				<th>Emails</th>				
				
			</tr>
			<tr>
				<td> <?php echo $x_child_emails_1; ?> </td>
			</tr>
			<?php if($x_child_emails_2){ ?>
			<tr>
				<td> <?php echo $x_child_emails_2; ?> </td>
			</tr>
			<?php } if($x_child_emails_3){ ?>
			<tr>
				<td> <?php echo $x_child_emails_3; ?> </td>
			</tr>
			<?php } if($x_child_emails_4){ ?>
			<tr>
				<td> <?php echo $x_child_emails_4; ?> </td>
			</tr>
			<?php } if($x_child_emails_5){ ?>
			<tr>
				<td> <?php echo $x_child_emails_5; ?> </td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	</center>
	<?php endif; ?>
	
	<div id="x_add_child_pop_up" title="Add Childrens" style="display:none;">
		<div class="pastor_getter">
			<p>How many child accounts would you like to buy?
			<select id="x_number_childs" name="x_number_childs">
				<?php
				$noem	=	get_user_meta($user_ID, 'numberofemails', true);
				$number_of_child_remains	=	5 - $noem;
					for($i=0; $i<=$number_of_child_remains; $i++){
						echo "<option>$i</option>";
					}
				?>
			</select>
			</p>
			<div class="x_child_email_conatiner">
				<form id="X_child_form" method="post" action="#"/>
				<p class="status"></p>
				<?php wp_nonce_field('ajax-register-nonce', 'emailsecurity'); ?> 
					<input id="x_child_emails_1" type="email" required name='x_child_emails_1' placeholder='Email'/>
					<input id="x_child_emails_2" type="email" required name='x_child_emails_2' placeholder='Email'/>
					<input id="x_child_emails_3" type="email" required name='x_child_emails_3' placeholder='Email'/>
					<input id="x_child_emails_4" type="email" required name='x_child_emails_4' placeholder='Email'/>
					<input id="x_child_emails_5" type="email" required name='x_child_emails_5' placeholder='Email'/>
					<br/><input type='submit' value='Buy Now' id='x_child_buy_now'/>
				</form>
			</div>
		</div>
	</div>
	<?php 
	wp_register_script('validate-script', get_stylesheet_directory_uri() . '/js/jquery.validate.js', array('jquery') ); 
    wp_enqueue_script('validate-script');
	wp_register_style( 'ajax-auth-style', get_stylesheet_directory_uri() . '/css/ajax-auth-style.css' );
	wp_enqueue_style('ajax-auth-style');
	?>
<script>
  jQuery(function($) {
	  jQuery("#X_child_form").validate();
	 
	  for (i = 1; i <= 5; i++) {	$("#x_child_emails_"+i).hide();		}
    $( "#x_add_child_pop_up" ).dialog({
		  buttons: [
			{
			  text: "Close",
			  click: function() {
				$( this ).dialog( "close" );
			  },
		 
			  // Uncommenting the following line would hide the text,
			  // resulting in the label being used as a tooltip
			  //showText: false
			}
		  ],
		minHeight: 400,
		maxHeight: 400,
		minWidth: 500,
		modal:true,
      autoOpen: false,
      show: {
        effect: "fade",
        duration: 1000
      },
      hide: {
        effect: "fold",
        duration: 1000
      }
    });
	$( "#x_add_child_pop_up_open" ).click(function(){
		$( "#x_add_child_pop_up" ).dialog( "open" );
	});
	$("#x_number_childs").change(function(){
		 jQuery(".error").hide();
		var numberofemails	=	$( this ).val();
		for (i = 1; i <= 5; i++) {
			$("#x_child_emails_"+i).hide();
		}
		for (i = 1; i <= numberofemails; i++) {
			$("#x_child_emails_"+i).show();
		}
		if(numberofemails == 0){ $('x_child_buy_now').hide(); }else{ $('x_child_buy_now').show(); }
	});
	$( "#x_child_buy_now" ).click(function(e){
		 if (!$("form#X_child_form").valid()) return false;
		
		var numberofemails		=	$("#x_number_childs").val();
		var	x_child_emails_1	=	jQuery("#x_child_emails_1").val();
		var	x_child_emails_2	=	jQuery("#x_child_emails_2").val();
		var	x_child_emails_3	=	jQuery("#x_child_emails_3").val();
		var	x_child_emails_4	=	jQuery("#x_child_emails_4").val();
		var	x_child_emails_5	=	jQuery("#x_child_emails_5").val();
		var	emailsecurity	=	jQuery("#emailsecurity").val();
		//$("#x_number_childs").attr('disabled','disabled');
		//console.log(x_child_emails_1);
		
		if(numberofemails==0){ return false; 
		$('p.status').text("Please choose number of Account.");
		}
		$.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            data: {
                'action': "x_child_emails",
                'numberofemails': numberofemails,
                'x_child_emails_1': x_child_emails_1,
				'x_child_emails_2': x_child_emails_2,
				'x_child_emails_3': x_child_emails_3,
				'x_child_emails_4': x_child_emails_4,
				'x_child_emails_5': x_child_emails_5,
                'emailsecurity': emailsecurity
            },
            success: function (data) {
				$('p.status').text("Redirecting..");
				if (data.redirect == true) {
                    document.location.href = "<?php echo home_url('register'); ?>";
                }
            }
        });
		 e.preventDefault();
	});
  });
</script>
<?php 
}
add_action( 'bp_setup_nav', 'x_add_child_tab', 100 );
// Enable the user with no privileges 
add_action( 'wp_ajax_nopriv_x_child_emails', 'x_child_emails_callback' );
// Enable the user 
add_action( 'wp_ajax_x_child_emails', 'x_child_emails_callback' );
function x_child_emails_callback(){
    check_ajax_referer( 'ajax-register-nonce', 'emailsecurity' );
	$info['numberofemails'] =  $_POST['numberofemails'];
	$info['x_child_emails_1'] =  $_POST['x_child_emails_1'];
	$info['x_child_emails_2'] =  $_POST['x_child_emails_2'];
	$info['x_child_emails_3'] =  $_POST['x_child_emails_3'];
	$info['x_child_emails_4'] =  $_POST['x_child_emails_4'];
	$info['x_child_emails_5'] =  $_POST['x_child_emails_5'];
	
	$user_ID = get_current_user_id();
	 update_user_meta( $user_ID, 'x_child_emails_1', $info['x_child_emails_1'] );
	 update_user_meta( $user_ID, 'x_child_emails_2', $info['x_child_emails_2'] );
	 update_user_meta( $user_ID, 'x_child_emails_3', $info['x_child_emails_3'] );
	 update_user_meta( $user_ID, 'x_child_emails_4', $info['x_child_emails_4'] );
	 update_user_meta( $user_ID, 'x_child_emails_5', $info['x_child_emails_5'] );
	 
	  $noem	=	get_user_meta($user_ID, 'numberofemails', true);
	  $noem	=	$info['numberofemails'] + $noem; 
	  
	 update_user_meta($user_ID,'numberofemails',$noem);
	
	echo json_encode(array('redirect'=>true,'emails'=>$info));
	wp_die();
}

