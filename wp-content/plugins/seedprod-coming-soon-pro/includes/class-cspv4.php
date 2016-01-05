<?php
/**
 * Plugin class logic goes here
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */
class SEED_CSPV4{

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

	private $landing_page_rendered = false;

	function __construct(){

			global $seed_cspv4;
			extract($seed_cspv4);

            // Actions & Filters if the landing page is active or being previewed
            if(((!empty($status) && $status === '1') || (!empty($status) && $status === '2') || (!empty($status) && $status === '3'))  || (isset($_GET['seed_cspv4_preview']) && $_GET['seed_cspv4_preview'] == 'true')){
            	if(function_exists('bp_is_active')){
                    add_action( 'template_redirect', array(&$this,'render_landing_page'),9);
                }else{
                    add_action( 'template_redirect', array(&$this,'render_landing_page'));
                    //add_action( 'init', array(&$this,'clear_cache') );
                }
                add_action( 'admin_bar_menu',array( &$this, 'admin_bar_menu' ), 1000 );
            }

            // Check License
            add_action( 'wp_ajax_seed_cspv4_check_license', array(&$this,'check_license'));

            // Upgrade & DB setup
            add_action( 'admin_init', array( &$this, 'upgrade' ), 0 );

            // Hook into plugin page
            add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );

            // Handle action post
            add_action( 'admin_init', array( &$this, 'subscriber_actions' ), 0 );

    }

    function clear_cache(){
        if (function_exists ('wp_cache_clear_cache')) {
            ob_end_clean();
            wp_cache_clear_cache();
        }

        if (function_exists ('w3tc_pgcache_flush')) {
            ob_end_clean();
            w3tc_pgcache_flush();
        }
        nocache_headers();
    }



    /**
     * Display settings link on plugin page
     */
    function plugin_action_links( $links, $file )
    {
        $plugin_file = SEED_CSPV4_FILE;

        if ( $file == $plugin_file ) {
            $settings_link = '<a href="options-general.php?page=seed_cspv4_options">Settings</a>';
            array_unshift( $links, $settings_link );
        }
        return $links;
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    /**
     * Display the default template
     */
    static function get_default_template(){
        $file = file_get_contents(SEED_CSPV4_PLUGIN_PATH.'/themes/default/index.php');
        return $file;
    }

    /**
     * Upgrade setting pages. This allows you to run an upgrade script when the version changes.
     *
     */
    function upgrade( )
    {
        // get current version
        $seed_cspv4_current_version = get_option( 'seed_cspv4_version' );
        $upgrade_complete = false;
        if ( empty( $seed_cspv4_current_version ) ) {
            $seed_cspv4_current_version = 0;
        }


        if ( version_compare( $seed_cspv4_current_version,SEED_CSPV4_VERSION) === -1) {
            // Upgrade db if new version
            $this->subscriber_database_setup();
            $upgrade_complete = true;

        }

        if($upgrade_complete){
            update_option( 'seed_cspv4_version', SEED_CSPV4_VERSION );
        }

    }

    /**
     * Display admin bar when active
     */
    function admin_bar_menu($str){
        global $wp_admin_bar,$seed_cspv4;
        extract($seed_cspv4);
        $msg = '';
        if($status == '1'){
            $msg = __('Coming Soon Mode Active','seedprod');
        }elseif($status == '2'){
            $msg = __('Maintenance Mode Active','seedprod');
        }
        elseif($status == '3'){
            $msg = __('Redirect Mode Active','seedprod');
        }

        if(isset($_GET['seed_cspv4_preview']) && $_GET['seed_cspv4_preview'] == 'true'){
            $msg = __('&#8592 Go Back | Coming Soon Pro Preview','seedprod');
        }

        //Add the main siteadmin menu item
        if(!empty($msg)){
            $wp_admin_bar->add_menu( array(
                'id'     => 'seed-cspv4-notice',
                'href' => admin_url().'options-general.php?page=seed_cspv4_options',
                'parent' => 'top-secondary',
                'title'  => $msg,
                'meta'   => array( 'class' => 'cspv4-mode-active' ),
            ) );
        }
    }


    /**
     *  Check License
     */
    function check_license(){
        if(check_ajax_referer('seed_cspv4_check_license')){
            global $seed_cspv4;
            extract($seed_cspv4);
            $api_key = $_GET['apikey'];
            $seed_emaillist = "";
            $seed_admin_email = get_option( 'admin_email','' );
            if(!empty($seed_cspv4['emaillist'])){
                $seed_emaillist = $seed_cspv4['emaillist'];
            }

            $data = array();
            $data['emaillist'] = $seed_emaillist;
            $data['admin_email'] = $seed_admin_email;

            $updater = new SellWP_UpdaterV2();
            $response = $updater->getRemote_information(
                $api_key,
                SEED_CSPV4_VERSION,
                'seedprod-coming-soon-pro/seedprod-coming-soon-pro.php',
                null,
                $data
                );

            echo json_encode($response);
            exit();
        }
    }

    /**
     * Create Database to Store Emails
     */
    function subscriber_database_setup() {
        global $wpdb;
        $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
        //if( $wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename ){
            $sql = "CREATE TABLE `$tablename` (
              id int(11) unsigned NOT NULL AUTO_INCREMENT,
              email varchar(100) DEFAULT NULL,
              fname varchar(100) DEFAULT NULL,
              lname varchar(100) DEFAULT NULL,
              clicks int(11) NOT NULL DEFAULT '0',
              conversions int(11) NOT NULL DEFAULT '0',
              referrer int(11) NOT NULL DEFAULT '0',
              ip varchar(40) DEFAULT NULL,
              created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY  (id)
            );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            dbDelta($sql);
        //}

    }

    function change_wp_cookie_logout( $expirein ) {
        global $seed_cspv4;
        extract($seed_cspv4);
        if(!empty($bypass_expires)){
            return $bypass_expires; // Modify the exire cookie
        }else{
            return $expirein;
        }
    }

    /**
     * Display the coming soon page
     */
    function render_landing_page() {
        // Get Settings
        global $seed_cspv4;
        extract($seed_cspv4);
        $o = $seed_cspv4;


        // Check if Preview
        $is_preview = false;
        if ((isset($_GET['seed_cspv4_preview']) && $_GET['seed_cspv4_preview'] == 'true')) {
            //show_admin_bar( false );
            $is_preview = true;
        }

        // Countdown Launch
        if($is_preview == false){
            if(!empty($countdown_date) && !empty($enable_countdown) && !empty($countdown_launch)){

            	if(empty($o['countdown_time_hour'])){
			$o['countdown_time_hour'] = '0';
			$countdown_time_hour = '0';
		}
		if(empty($o['countdown_time_minute'])){
			$o['countdown_time_minute'] = '0';
			$countdown_time_minute = '0';
		}

                $dt = getdate(strtotime($o['countdown_date'] .$o['countdown_time_hour'].':'.$o['countdown_time_minute']. ' UTC'));

                $tz = get_option('timezone_string');

                if(empty($tz)){
                    $offset = get_option('gmt_offset');
                    $tz = timezone_name_from_abbr("", ($offset * 3600), 0);
                }

                date_default_timezone_set($tz);
                $countdown_date = date_parse($countdown_date);
                $launch_date = new DateTime($countdown_date['year'].'-'.$countdown_date['month'].'-'.$countdown_date['day'].' '.$countdown_time_hour.':'.$countdown_time_minute.':00');

                // Launch this biatch
                if($launch_date <= new DateTime()){
                    // Email the admin the site has been launched
                    $message = __(sprintf('%s has been launched.',home_url()), 'seedprod');
                    $result = wp_mail( get_option('admin_email'), __(sprintf('%s has been launched.',home_url()), 'seedprod'), $message);

                    $o = get_option('seed_cspv4');
                    $o['status'] = 0;
                    update_option('seed_cspv4', $o);
                    return false;

                }
            }
        }

        //If Referrer record it
        if(isset($_GET['ref'])){
            $id = intval($_GET['ref'],36)-1000;

            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
            $sql = "UPDATE $tablename SET clicks = clicks + 1 WHERE id = %d";
            $safe_sql = $wpdb->prepare($sql,$id);
            $update_result =$wpdb->get_var($safe_sql);
        }

        // Exit if feed and feedburner is enabled.
        if(is_feed() && $emaillist == 'feedburner' ){
            return false;
        }

        if(empty($_GET['seed_cspv4_preview'])){
            $_GET['seed_cspv4_preview'] = false;
        }

        if(empty($_GET['bypass'])){
            $_GET['bypass'] = false;
        }

        $alt_bypass = seed_get_plugin_api_value('alt_bypass');

        if ( is_multisite() ||  $alt_bypass){

            // Multisite Clientview
            if(empty($_GET['bypass'])){
                $_GET['bypass'] = false;
            }

            if(empty($_GET['cs_preview'])){
                $_GET['cs_preview'] = false;
            }

            //Check for Client View
            if (isset($_COOKIE['wp-client-view']) && ((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($client_view_url))) || (strtolower($_GET['bypass']) == trim(strtolower($client_view_url))) ) && !empty($client_view_url)) {
                header( 'Location: '.home_url().'?'.rand() ) ;
            }

            // Don't show Coming Soon Page if client View is active
            $client_view_hash = md5($client_view_url . get_current_blog_id());
            if (isset($_COOKIE['wp-client-view']) && $_COOKIE['wp-client-view'] == $client_view_hash && $_GET['cs_preview'] != 'true' && !empty($client_view_url)) {
                nocache_headers();
                header( 'Cache-Control: max-age=0');
                return false;
            }else{
                setcookie("wp-client-view", "", time()-3600);
            }

            // If Client view is not empty and we are on the client view url set cookie.
            if(!empty($client_view_url)){
                if(empty($_GET['bypass'])){
                    $_GET['bypass'] = '';
                }

                if((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($client_view_url))) || (strtolower($_GET['bypass']) == trim(strtolower($client_view_url)))) {
                    setcookie("wp-client-view", $client_view_hash , time()+21600, COOKIEPATH, COOKIE_DOMAIN, false);
                    header( 'Location: '.home_url().'?'.rand() ) ;
                    exit();
                }
            }

        }else{
        // ClientView


        if(!empty($client_view_url)){

            if(empty($_GET['bypass'])){
                $_GET['bypass'] = '';
            }

            // If client view url is passed in log user in
            if((strtolower(basename($_SERVER['REQUEST_URI'])) == trim(strtolower($client_view_url))) || (strtolower($_GET['bypass']) == trim(strtolower($client_view_url)))) {


                if(!username_exists('seed_cspv4_clientview_'.$client_view_url)){
                    $user_id = wp_create_user('seed_cspv4_clientview_'.$client_view_url,wp_generate_password());
                    $user = new WP_User($user_id);
                    $user->set_role('none');
                }

                $client_view_hash = md5($client_view_url . get_current_blog_id());
                setcookie("wp-client-view", $client_view_hash , time()+ (int) $bypass_expires, COOKIEPATH, COOKIE_DOMAIN, false);

                add_filter( 'auth_cookie_expiration', array(&$this,'change_wp_cookie_logout') );

                // Log user in auto
                $username = 'seed_cspv4_clientview_'.$client_view_url;
                if ( !is_user_logged_in() ) {
                    $user = get_user_by( 'login', $username );
                    $user_id = $user->ID;
                    wp_set_current_user( $user_id, $username );
                    wp_set_auth_cookie( $user_id );
                    do_action( 'wp_login', $username );
                    update_user_meta($user_id, 'show_admin_bar_front', false);
                }



                if(!empty($_REQUEST['return'])){
                    header( 'Location: '.urldecode($_REQUEST['return']) ) ;
                    exit;
                }else{
                    header( 'Location: '.home_url().'?'.rand() ) ;
                    exit;
                }


            }
        }
        }



        // Check for excluded IP's
        if($is_preview == false){
            if(!empty($ip_access)){
                $ip = seed_cspv4_get_ip();
                $exclude_ips = explode("\r\n",$ip_access);
                if(is_array($exclude_ips) && in_array($ip,$exclude_ips)){
                    return false;
                }
            }
        }



        // Check for included pages
        if(!empty($include_url_pattern) && @preg_match("/{$include_url_pattern}/",$_SERVER['REQUEST_URI']) == 0 && $is_preview == false){
            return false;
        }

        // Check for excluded pages
        if(!empty($exclude_url_pattern) && @preg_match("/{$exclude_url_pattern}/",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
            return false;
        }

        // Exit if a custom login page
        if(empty($disable_default_excludes)){
            $default_excludes_pattern = apply_filters('seed_cspv4_default_excludes_pattern','login');
            if(preg_match("/$default_excludes_pattern/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
                return false;
            }
        }

        //Exit if wysija double opt-in
        if($emaillist == 'wysija' && preg_match("/wysija/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
            return false;
        }



        // Set values if not set
        if(empty($include_page))
            $include_page = '-1';
        if(empty($include_roles))
            $include_roles = '0';

        //Limit to one page

        if($is_preview === false){
            if($include_page != '-1'){
                $post_id = $include_page;
                $post = get_post($post_id);
                $slug = $post->post_name;

                $is_page = false;
                if(@preg_match("/{$slug}/",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
                    $is_page = true;
                }else{
                    //backup_method
                    if(!is_page($include_page)){
                        return false;
                    }
                }
            }
        }




        //Limit access by role
            if(!is_page($include_page)){
                if($is_preview === false){
                    if(!empty($include_roles) && !isset($_COOKIE['wp-client-view'])){

                        foreach($include_roles as $v){
                            if($v == '0' && is_user_logged_in()){
                                return false;
                            }
                            if(current_user_can($v)){
                                return false;
                            }
                        }
                    }elseif(is_user_logged_in()){
                        return false;
                    }
                }
            }




        // Set 503 Headers
        if($status == '2'){
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header('Status: 503 Service Temporarily Unavailable');
            header('Retry-After: 86400'); // retry in a day
        }elseif($status == '3'){
            if(!empty($redirect_url)){
                wp_redirect( $redirect_url );
                exit;
            }
        }else{
            header("HTTP/1.1 200 OK");
        }

        // Use maintenance.php
        $cspv4_maintenance_file = WP_CONTENT_DIR."/maintenance.php";
        if(!empty($enable_maintenance_php) and file_exists($cspv4_maintenance_file)){
            return $cspv4_maintenance_file;
        }

        do_action('seed_cspv4_pre_render');


        // Render Landing Page
        if ( empty($template) ) {
                $templates = new Seed_CSPV4_Template_Loader;
                if(!empty($theme) && $theme != 'default' ){
                    if(file_exists(apply_filters('seed_cspv4_themes_path',SEED_CSPV4_PLUGIN_PATH).'index.php')){
                         include(apply_filters('seed_cspv4_themes_path',SEED_CSPV4_PLUGIN_PATH).'index.php');
                         exit();
                    }else{
                        $templates->get_template_part( 'default/index' );
                        exit();
                    }

                }else{
                    $templates->get_template_part( 'default/index' );
                    exit();
                }
        } else {
            echo do_shortcode($template);
            exit();
        }
    }

    /*
     * Subscribers Actions
     */
    function subscriber_actions(){
            if(!empty($_POST['action'])){
                if($_POST['action'] == 'seed_cspv4_export'){
                     SEED_CSPV4::export_all_subscribers();
                }
            }
    }

    /*
     * Export Subscribers
     */
    static function export_all_subscribers(){
        if (ob_get_contents()) ob_clean();
        
        global $wpdb;
        $csv_output = '';
        $csv_output .= "ID,Email,Fname,Lname,Clicks,Conversions,City,Country,IP,Created,Referrer";
        $csv_output .= "\n";
        $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
        $sql = "SELECT * FROM " . $tablename;
        $results = $wpdb->get_results($sql);

        foreach ($results as $result) {
            if(!empty($result->location)){
                $location = json_decode($result->location,true);
                $city = $location['city'];
                $country = $location['country_name'];
            }else{
                $city = '';
                $country = '';
            }
           $csv_output .= $result->id ."," . $result->email ."," . $result->fname . ",". $result->lname . "," . $result->clicks . "," . $result->conversions . "," . $city . "," . $country . "," . $result->ip . "," . $result->created . "," . $result->referrer ."\n";
        }


        $filename = "subscribers_".date("Y-m-d_H-i",time());
        header("Content-type: text/plain");
        header("Content-disposition: attachment; filename=".$filename.".csv");
        print $csv_output;
        die();
    }

    /*
     * Delete Subscribers
     */
    static function delete_all_subscribers(){
        if (current_user_can( 'delete_users' )) {
            global $wpdb;
            $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
            $sql = "TRUNCATE " . $tablename;
            $result = $wpdb->query($sql);
            if($result){
                return true;
            }
        }
    }

    /*
     * Delete Selected Subscribers
     */
    static function delete_selected_subscribers($ids){
        if (current_user_can( 'list_users' )) {
            if(is_array($ids) && !empty($ids)){
                global $wpdb;
                $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
                $sql = "DELETE FROM " . $tablename . " WHERE id IN ( ".implode(",", $ids)." )";
                $result = $wpdb->query($sql);
                if($result){
                    return true;
                }
            }
        }
    }


    /*
     * Display Subscribers
     */
    static function display_subscribers(){

        ob_start();
            if(!empty($_POST['action'])){
                //$nonce = $_POST['_wpnonce'];
                //var_dump(wp_verify_nonce($nonce, 'buljk-toplevel_page_seed_cspv4'));

                // if($_POST['action'] == 'export'){
                //     SEED_CSPV4::export_all_subscribers();
                // }

                if($_POST['action'] == 'seed_cspv4_delete'){
                    if(SEED_CSPV4::delete_all_subscribers()){
                        echo '
                        <div id="setting-error-seedprod_error" class="error settings-error below-h2">
                        <p><strong>'.__('All subscribers deleted.','seedprod').'</strong></p></div>';
                    }
                }
                if($_POST['action'] == 'seed_cspv4_delete_selected'){
                    if(SEED_CSPV4::delete_selected_subscribers($_POST['subscriber'])){
                        echo '
                        <div id="setting-error-seedprod_error" class="error settings-error below-h2">
                        <p><strong>'.__('Selected subscribers deleted.','seedprod').'</strong></p></div>';
                    }
                }
            }


        // Render Subscriber
        $seed_cspv4_subscribers = new SEED_CSPV4_SUBSCRIBERS();
        $seed_cspv4_subscribers->prepare_items();
        echo '<form id="seed_cspv4_search"" method="post">';
        $seed_cspv4_subscribers->search_box('Search Emails', 'email');
        echo '</form>';
        echo '<form id="seed_cspv4_bulk_actions" method="post">';
        $seed_cspv4_subscribers->display();
        wp_nonce_field('seed_cspv4_subscribers');
        echo '</form>';

        ?>
        <script>
        jQuery(document).ready(function($){
            $(".bottom > .actions").hide();
            $("#doaction").click(function(event) {
                event.preventDefault();
                var action = $('select[name="action"]').val();
                if(action != '-1'){
                    if(action == 'delete'){
                        if(confirm(seed_cspv4_msgs.delete_confirm)){
                            $("#seed_cspv4_bulk_actions").submit();
                        }
                    }else{
                        $("#seed_cspv4_bulk_actions").submit();
                    }
                }
            });
        });
        </script>

        <?php
        $output = ob_get_clean();
        return $output;
    }




}



// Display Subscribers Class

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class SEED_CSPV4_SUBSCRIBERS extends WP_List_Table {
    function get_data($current_page,$per_page){
        // Get records
        global $wpdb;
        $l1 = ($current_page-1)* $per_page;
        $l2 = $per_page;
        $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
        $email = '%'.$_POST['s'].'%';
        $q = "WHERE email LIKE %s ";
        $sql = "SELECT * FROM $tablename $q LIMIT $l1,$l2";
        $safe_sql = $wpdb->prepare($sql,$email);
        $results = $wpdb->get_results($safe_sql);
        $data = array();
        foreach($results as $v){
            // Sep
            $sep = '';
            if($v->fname != '' || $v->lname != ''){
                $sep = '<br>';
            }
            // Format Date
            $date = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->created));

            // Get Gravatar
            $gravatar = '<img src="http://www.gravatar.com/avatar/'.md5($v->email) .'?s=36" alt="Gravatar" style="float:left;padding:2px;backgroun-color:#fff;border:1px solid #ccc;margin-right:8px">';

            // Format email
            $email = "<a href='mailto:{$v->email}'>{$v->email}</a>";

            $ref = $v->id+1000;
            $referrer_url = home_url() . '?ref='.base_convert($ref, 10, 36);

            // Subscriber
            $subscriber = $gravatar.$v->fname.' '.$v->lname.$sep.$email.' <br clear="both"><strong>Referrer URL</strong><br><a href="'.$referrer_url.'" traget="_blank">'.$referrer_url.'</a>';

            // Influence
            $influence = $v->conversions. ' of '. $v->clicks. ' referrals have subscribed to your list';

            $conversions = $v->conversions;
            if($v->conversions != 0){
                $conversion_rate = round(($v->conversions/$v->clicks) * 100).'%';
            }else{
                $conversion_rate = '0%';
            }
            $clicks = $v->clicks;

            $created  = $date;

            $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
            $sql = "SELECT email FROM $tablename WHERE id = %d";
            $safe_sql = $wpdb->prepare($sql,$v->referrer);
            $results = $wpdb->get_results($safe_sql);

            $referrer = null;
            if(!empty($results[0]->email))
            $referrer = $results[0]->email;

            // Load Data
            $data[] = array(
                'ID' => $v->id,
                'subscriber' => $subscriber,
                'clicks' => $clicks,
                'conversions' => $conversions,
                'conversion_rate' => $conversion_rate,
                'created' => $created,
                'referrer' => $referrer,
                );
        }
        return $data;
    }

    function get_data_total(){
        global $wpdb;
        if(empty($_POST['s']))
            $_POST['s'] = '';

        $tablename = $wpdb->prefix . SEED_CSPV4_TABLENAME;
        $email = '%'.$_POST['s'].'%';
        $q = "WHERE email LIKE %s ";
        $sql = "SELECT count(id) FROM $tablename $q";
        $safe_sql = $wpdb->prepare($sql,$email);
        $results = $wpdb->get_var($safe_sql);
        return $results;
    }

    function get_sortable_columns() {
      $sortable_columns = array(
        'clicks'  => array('clicks',false),
        'conversions' => array('conversions',false),
        'conversion_rate'   => array('conversion_rate',false),
        'created'   => array('created',false),
      );
      return $sortable_columns;
    }

    function usort_reorder( $a, $b ) {
      // If no sort, default to created
      $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'created';
      // If no order, default to asc
      $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
      // Determine sort order
      $result = strcmp( $a[$orderby], $b[$orderby] );
      // Send final sort direction to usort
      return ( $order === 'asc' ) ? $result : -$result;
    }

    function get_columns(){
      $columns = array(
        'cb'        => '<input type="checkbox" />',
        'subscriber' => __('Subscribers','seedprod'),
        'clicks'    => __('Clicks','seedprod'),
        'conversions'    => __('# People Signed Up','seedprod'),
        'conversion_rate'    => __('Conversion Rate','seedprod'),
        'created'      => __('Created','seedprod'),
        'referrer'      => __('Referrer','seedprod'),
      );
      return $columns;
    }
    function prepare_items() {
      $columns = $this->get_columns();
      $hidden = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array($columns, $hidden, $sortable);
      if(isset($_GET['pp']) && $_GET['pp']){
        $per_page = intval($_GET['pp']);
      }else{
        $per_page = 100;
      }
      $current_page = $this->get_pagenum();
      $total_items = $this->get_data_total();
      $this->set_pagination_args( array(
        'total_items' => $total_items,
        'per_page'    => $per_page
      ) );
      $data = $this->get_data($current_page,$per_page);
      usort( $data, array( &$this, 'usort_reorder' ) );
      $this->items = $data;
    }

    function column_default( $item, $column_name ) {
      switch( $column_name ) {
        case 'subscriber':
        case 'clicks':
        case 'conversions':
        case 'conversion_rate':
        case 'created':
        case 'referrer':
          return $item[ $column_name ];
        default:
          return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_bulk_actions() {
      $actions = array(
        'seed_cspv4_export'    => __('Export All','seedprod'),
        'seed_cspv4_delete'    => __('Delete All','seedprod'),
        'seed_cspv4_delete_selected'    => __('Delete Selected','seedprod'),
      );
      return $actions;
    }

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="subscriber[]" value="%s" />', $item['ID']
        );
    }

    function column_subscriber($item) {
      $actions = array(
                //'profile'      => sprintf('<a href="?page=%s&action=%s&book=%s">Profile</a>',$_REQUEST['page'],'profile',$item['ID']),
            );
      return sprintf('%1$s %2$s', $item['subscriber'], $this->row_actions($actions) );
    }
}
