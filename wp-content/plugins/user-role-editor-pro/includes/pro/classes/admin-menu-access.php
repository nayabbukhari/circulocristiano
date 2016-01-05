<?php

/*
 * User Role Editor WordPress plugin
 * Class URE_Admin_Menu_Access - prohibit selected menu items for role or user
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://role-editor.com
 * License: GPL v2+ 
 */

class URE_Admin_Menu_Access {

// reference to the code library object
    private $lib = null;    
    private $admin_menu = null;
    private $notice = '';

    public function __construct($lib) {
        
        $this->lib = $lib;
        $this->admin_menu = new URE_Admin_Menu($this->lib);
        
        add_action('ure_role_edit_toolbar_service', array(&$this, 'add_toolbar_buttons'));
        add_action('ure_load_js', array(&$this, 'add_js'));
        add_action('ure_dialogs_html', array(&$this, 'dialog_html'));
        add_action('ure_process_user_request', array(&$this, 'update_menu_access'));
        add_action('ure_process_user_request', array(&$this, 'update_menu_access_notification'));        
        add_action('admin_head', array($this, 'remove_blocked_menu_items'), 10);
        add_action('admin_head', array($this, 'redirect_blocked_urls'), 10);
        add_action( 'customize_controls_init', array($this, 'redirect_blocked_urls'), 10);  // Especially for the customize.php URL
        //add_action('wp_head', array($this, 'hide_admin_menu_bar'));
        add_action('wp_before_admin_bar_render', array($this, 'modify_admin_menu_bar'));
        add_filter('media_view_strings', array($this, 'block_media_upload'), 99);
    }
    // end of __construct()

    
    private function do_not_apply() {
        global $current_user;
        
        if (!$this->lib->multisite && $this->lib->user_has_capability($current_user, 'administrator')) {
            return true;
        }
        if ($this->lib->multisite && is_super_admin() && !$this->lib->raised_permissions) {
            return true;
        }
        
        return false;
    }
    // end of do_not_apply()
    
    
    public function add_toolbar_buttons() {
        
        if (!current_user_can('ure_admin_menu_access')) {
            return;
        }
        
        if ($this->do_not_apply()) {    // get menu copy from superadmin user
            $this->admin_menu->update_menu_copy();
        }
?>
                
        <button id="ure_admin_menu_access_button" class="ure_toolbar_button" title="Prohibit access to selected menu items">User Menu</button> 
               
<?php

    }
    // end of add_toolbar_buttons()


    public function add_js() {
        
        wp_register_script( 'ure-admin-menu-access', plugins_url( '/js/pro/ure-pro-admin-menu-access.js', URE_PLUGIN_FULL_PATH ) );
        wp_enqueue_script ( 'ure-admin-menu-access' );
        wp_localize_script( 'ure-admin-menu-access', 'ure_data_admin_menu_access', 
                array(
                    'admin_menu' => esc_html__('Admin Menu', 'user-role-editor'),
                    'dialog_title' => esc_html__('Admin menu', 'user-role-editor'),
                    'update_button' => esc_html__('Update', 'user-role-editor')
                    
                ));
        
    }
    // end of add_js()    
    
    
    public function dialog_html() {
        
?>
        <div id="ure_admin_menu_access_dialog" class="ure-modal-dialog">
            <div id="ure_admin_menu_access_container">
            </div>    
        </div>
<?php        
        
    }
    // end of dialog_html()

            
    public function update_menu_access() {
    
        if (!isset($_POST['action']) || $_POST['action']!=='ure_update_admin_menu_access') {            
            return;
        }
        
        if (!current_user_can('ure_admin_menu_access')) {
            $this->notice = esc_html__('URE: Insufficient permissions to use this add-on','user-role-editor');
            return;
        }
        
        $ure_object_type = filter_input(INPUT_POST, 'ure_object_type', FILTER_SANITIZE_STRING);
        if ($ure_object_type!=='role' && $ure_object_type!=='user') {
            $this->notice = esc_html__('URE: administrator menu access: Wrong object type. Data was not updated.', 'user-role-editor');
            return;
        }
        $ure_object_name = filter_input(INPUT_POST, 'ure_object_name', FILTER_SANITIZE_STRING);
        if (empty($ure_object_name)) {
            $this->notice = esc_html__('URE: administrator menu access: Empty object name. Data was not updated', 'user-role-editor');
            return;
        }
                        
        if ($ure_object_type=='role') {
            $this->admin_menu->save_menu_access_data_for_role($ure_object_name);
        } else {
            $this->admin_menu->save_menu_access_data_for_user($ure_object_name);
        }
        
    }
    // end of update_menu()
    
    
    public function update_menu_access_notification() {
        $this->lib->show_message($this->notice);
    }
    // end of update_menu_access_notification()
            
    
    public function remove_blocked_menu_items() {
        global $current_user, $menu, $submenu;
        
        if ($this->do_not_apply()) {
            return;
        }        
        
        $blocked = $this->admin_menu->load_menu_access_data_for_user($current_user);
        if (empty($blocked)) {
            return;
        }

        foreach($submenu as $key=>$menu_item) {
            foreach($menu_item as $key1=>$menu_item1) {
                $item_id = $this->admin_menu->calc_menu_item_id('submenu', $menu_item1[2]);
                if ( ($blocked['access_model']==1 && in_array($item_id, $blocked['data'])) ||
                     ($blocked['access_model']==2 && !in_array($item_id, $blocked['data'])) ) {
                    unset($submenu[$key][$key1]);
                }
            }    
        }        
        
        foreach($menu as $key=>$menu_item) {
            $item_id1 = $this->admin_menu->calc_menu_item_id('menu', $menu_item[2]);
            $item_id2 = $this->admin_menu->calc_menu_item_id('submenu', $menu_item[2]);
            if ($blocked['access_model']==1) {
                if (in_array($item_id1, $blocked['data']) ||
                    (in_array($item_id2, $blocked['data']) && count($submenu[$menu_item[2]])==0)) {                 
                    unset($submenu[$menu_item[2]]);
                    unset($menu[$key]);                    
                }
            } elseif ($blocked['access_model']==2) {
                if (!in_array($item_id1, $blocked['data']) && !in_array($item_id2, $blocked['data'])) {                    
                    unset($submenu[$menu_item[2]]);
                    unset($menu[$key]);
                }
            }
        }
        
    }
    // end of remove_blocked_menu_items()
    
    
    protected function extract_page_from_url_query($url_query) {
        $params = explode('&', $url_query);
        foreach($params as $param) {
            if (strpos($param, 'page=')!==false) {
                $args = explode('=', $param);                
                return $args[1];
            }
        }
        
        return $url_query;
    }
    // end of extract_page_from_url_query()
    
    
    
    protected function extract_command_from_url($url) {
        
        $path = parse_url($url, PHP_URL_PATH);
        $path_parts = explode('/', $path);
        $url_script = end($path_parts);
        $url_query = parse_url($url, PHP_URL_QUERY);
        
        if ($url_script=='admin.php') { 
            $command = $this->extract_page_from_url_query($url_query);
        } else {
            $command = $url_script;
            if (!empty($url_query)) {
                $command .= '?'. $url_query;
            }
        }
        $command = str_replace('&', '&amp;', $command);
        if (empty($command)) {
            $command = 'index.php';
        }
        
        return $command;
        
    }
    // end of extract_command_from_url()
    
    
    private function get_first_available_menu_item() {
    
        global $menu;
        
        $available = '';
        foreach ($menu as $menu_item) {
            if ($menu_item[4]!='wp-menu-separator') {
                $available = $menu_item[2];
                break;
            }
        }

        if (!empty($available)) {
            $available = '/wp-admin/'. $available;
        }
        

        return $available;
        
    }
    // end of get_first_available_menu()
    
    
    private function remove_welcome_panel($command, $blocked_data) {
        if ($command=='index.php' && in_array('71cf5c9f472f8adbfc847a3f71ce9f0e', $blocked_data) /* customize.php */ ) {
            // remove Welcome panel from the dashboard as 
            // it's not good to show direct links to WordPress functionality for restricted user
            remove_action('welcome_panel', 'wp_welcome_panel'); 
        }
    }
    // end of remove_welcome_panel()
    
    
    public function redirect_blocked_urls() {
        
        global $current_user;
        
        if ($this->do_not_apply()) {
            return;
        }        
        
        $url = strtolower($_SERVER['REQUEST_URI']);
        $command = $this->extract_command_from_url($url);
        $item_id1 = $this->admin_menu->calc_menu_item_id('menu', $command);
        $item_id2 = $this->admin_menu->calc_menu_item_id('submenu', $command);
        $blocked = $this->admin_menu->load_menu_access_data_for_user($current_user);
        if ($blocked['access_model']==1 && !in_array($item_id1, $blocked['data']) && !in_array($item_id2, $blocked['data'])) {
            $this->remove_welcome_panel($command, $blocked['data']);
            return;
        }
        if ($blocked['access_model']==2) {
            if (in_array($item_id1, $blocked['data']) || in_array($item_id2, $blocked['data']))  { 
                $this->remove_welcome_panel($command, $blocked['data']);
                return;
            }
            $menu_hashes = $this->admin_menu->get_menu_hashes();
            // if command was not selected but it does not mantch with any to admin menu (submenu) item - do not block it
            if (!isset($menu_hashes[$item_id1]) && !isset($menu_hashes[$item_id2])) {
                return;
            }
        }
                        
        if (headers_sent()) {            
            echo '<div style="width: 600px;margin-top: 50px;margin-left: auto;margin-right: auto;text-align: center;">';
            echo '<h2>'. esc_html__('You do not have sufficient permissions to access this page', 'user-role-editor') .'</h2>';
            echo '<a href="'.site_url().'/wp-admin">Return to the dashboard</a>';
            echo '</div>';
            die;
        } else {
            $menu_item = $this->get_first_available_menu_item();
            wp_redirect(get_option('siteurl') . $menu_item);
        }
        
    }
    // end of redirect_blocked_urls()

    
    /**
     * Hide WordPress admin menu bar for user with blocked WordPress admin menu items
     * @TODO: It is better to remove blocked menu items from the front end admin menu bar.
     * 
     * @global WP_User $current_user
     * @return void
     */
    public function hide_admin_menu_bar() {
        global $current_user;
        
        if ($this->do_not_apply()) {
            return;
        }        
        
        $blocked = $this->admin_menu->load_menu_access_data_for_user($current_user);
        if (!empty($blocked)) {
            show_admin_bar(false);
        }
        
    }
    // end of hide_admin_menu_bar()

    
    /**
     * Return admin menu bar command string, 
     * but false for admin bar menu items which should be ignored
     * 
     * @param object $menu_item
     * @return boolean
     */
    protected function get_admin_menu_bar_command($menu_item) {
        
        $ignore_list = array(
            'about.php',
            'index.php',
            'profile.php',
            'wp-login.php'
        );
        if (empty($menu_item->href)) {
            return false;
        }
        $command = $this->extract_command_from_url($menu_item->href);
        foreach($ignore_list as $skip_it) {
            if (strpos($command, $skip_it)!==false) {
                return false;
            }
        }
                
        return $command;
    }
    // end of get_admin_menu_bar_command()
    
    
    /**
     * For front-end only
     * 
     * @global WP_User $current_user
     * @global type $wp_admin_bar
     * @return void
     */
    public function modify_admin_menu_bar() {
        global $current_user, $wp_admin_bar;
                
        $nodes = $wp_admin_bar->get_nodes();
        if (empty($nodes)) {
            return;
        }
        
        if ($this->do_not_apply()) {
            return;
        }        
        
        // remove 'SEO' menu from top bar
        if (!current_user_can('manage_options')) {
            $wp_admin_bar->remove_menu('wpseo-menu');
        } 
        
        $blocked = $this->admin_menu->load_menu_access_data_for_user($current_user);
        if (empty($blocked)) {
            return;
        }                
        
        // if 'SEO' menu is blocked for the role, block it at top bar
        $seo_item_id = 'd140a27dd226c2e5671797ef3818bc68';        
        if ( ($blocked['access_model']==1 && in_array($seo_item_id, $blocked['data'])) ||
             ($blocked['access_model']==2 && !in_array($seo_item_id, $blocked['data'])) ) {
            $wp_admin_bar->remove_menu('wpseo-menu');
        }
        
        foreach($nodes as $key=>$menu_item) {
            $command = $this->get_admin_menu_bar_command($menu_item);
            if (empty($command)) {
                continue;
            }
            
            $item_id1 = $this->admin_menu->calc_menu_item_id('menu', $command);
            $item_id2 = $this->admin_menu->calc_menu_item_id('submenu', $command);
            
            if ($blocked['access_model']==1) {  // block selected
                if (in_array($item_id1, $blocked['data'])) {
                    $wp_admin_bar->remove_menu($menu_item->id);
                } elseif (in_array($item_id2, $blocked['data'])) {
                    $wp_admin_bar->remove_node($menu_item->id);
                }
            } elseif ($blocked['access_model']==2) {    // block not selected
                if (!in_array($item_id1, $blocked['data']) && !in_array($item_id2, $blocked['data'])) {
                    $wp_admin_bar->remove_menu($menu_item->id);                
                }
            }
        }
                
    }
    // end of modify_admin_menu_bar()
    
    
    public function block_media_upload($strings) {
        
        global $current_user;
        
        if ($this->do_not_apply()) {
            return $strings;
        }

        $blocked = $this->admin_menu->load_menu_access_data_for_user($current_user);
        if (empty($blocked)) {
            return $strings;
        }
        
        foreach($blocked['data'] as $menu_hash) {
            if ($menu_hash=='a6d96d2991e9d58c1d04ef3c2626da56') {  // Media -> Add New
                // Undocumented trick to remove "Upload Files" tab at the Post Editor "Add Media" popup window 
                // Be aware - it may stop working with next version of WordPress
                unset($strings['uploadFilesTitle']);    
                break;
            }
        }
                        
        return $strings;
    }
    // end of block_media_upload()
}
// end of URE_Admin_Menu_Access class
