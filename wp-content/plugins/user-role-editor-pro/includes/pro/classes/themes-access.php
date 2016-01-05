<?php
/*
 * Access restriction to themes activation on per user basis
 * part of User Role Editor Pro plugin
 * Author: Vladimir Garagulya
 * email: vladimir@shinephp.com
 * 
 */

class URE_Themes_Access {
    
    private $lib = null;
    private $user_meta_key = '';
    
    public function __construct(Ure_Lib $lib) {
    
        global $wpdb;
        
        $this->lib = $lib;
        $this->user_meta_key = $wpdb->prefix . 'ure_allow_themes';
        
        add_action( 'edit_user_profile', array($this, 'edit_user_allowed_themes_list'), 10, 2);
        add_action( 'profile_update', array($this, 'save_user_allowed_themes_list'), 10);
        add_action( 'admin_head', array($this, 'prohibited_links_redirect'));        
        add_action('admin_init', array($this, 'set_final_hooks'));
        add_action( 'admin_enqueue_scripts', array($this, 'admin_load_js' ));
        add_action( 'admin_print_styles-user-edit.php', array($this, 'admin_css_action'));
    }
    // end of __construct()
    
    
    // checks if user can activate plugins
    protected function user_can_activate_themes($user) {
        
        $result = $this->lib->user_has_capability($user, 'switch_themes');        
        
        return $result;
    }
    // end of user_can_activate_plugins()
    
    
    public function set_final_hooks() {
        global $current_user;
        
        $ure_key_capability = $this->lib->get_key_capability();
        if ( $this->lib->user_has_capability($current_user, $ure_key_capability) ) {    // this is URE admin - no limits
            return;
        }
                
        if ( $this->user_can_activate_themes($current_user) ) {
            $allowed_themes_list = $this->get_allowed_themes_list();
            if (count($allowed_themes_list)>0) {
                add_filter('wp_prepare_themes_for_js', array(&$this, 'restrict_themes_list' ));
            }
        }
        
    }
    // end of set_final_hooks()
    
    
    protected function get_allowed_themes_names($allow_themes) {

        $allowed_themes_list = explode(',', $allow_themes);
        $themes = wp_prepare_themes_for_js();
        $allowed_themes_names = '';
        foreach ($themes as $theme) {
          if (in_array($theme['id'], $allowed_themes_list)) {
            if (!empty($allowed_themes_names)) {
                $allowed_themes_names .= "\n";
            }
            $allowed_themes_names = $allowed_themes_names .$theme['name'];
          }
        }

        return $allowed_themes_names;
    }
    // end of get_allowed_themes_names()

    
    protected function user_profile_themes_select() {
                
        $all_themes = wp_prepare_themes_for_js();
        echo 'Open drop-down list and turn On/Off checkboxes:<br>'."\n";
        echo '<select multiple="multiple" id="ure_select_allowed_themes" name="ure_select_allowed_themes" style="width: 500px;" >'."\n";
        foreach($all_themes as $theme) {
            echo '<option value="'. $theme['id'] .'" >'. $theme['name'] .'</option>'."\n";
        }   // foreach()
        echo '</select><br>'."\n";
        
    }
    // end of user_profile_themes_select()
    
    
    public function edit_user_allowed_themes_list($user) {

        global $current_user;

        $result = stripos($_SERVER['REQUEST_URI'], 'network/user-edit.php');
        if ($result !== false) {  // exit, this code just for single site user profile only, not for network admin center
            return;
        }
        
        $ure_key_capability = $this->lib->get_key_capability();        
        if (!$this->lib->user_has_capability($current_user, $ure_key_capability)) { // you can not edit allowed themes list
            return;
        }
        
        if ($this->lib->user_has_capability($user, $ure_key_capability)) {  // he can edit, do not restrict him
            return;
        }
        
        // if edited user can not activate themes, do not show allowed themes input field
        if ( !$this->user_can_activate_themes($user) ) {
            return;
        }
        
        $allow_themes = get_user_meta($user->ID, $this->user_meta_key, true);
        if (empty($allow_themes)) {
            $allow_themes = '';
        }
        $show_allowed_themes = $this->get_allowed_themes_names($allow_themes);
?>        
        <h3><?php _e('Themes available for activation', 'user-role-editor'); ?></h3>
        <?php $this->user_profile_themes_select();?>
        <textarea name="show_allowed_themes" id="show_allowed_themes" cols="80" rows="5" readonly="readonly" /><?php echo $show_allowed_themes; ?></textarea>
        <input type="hidden" name="ure_allow_themes" id="ure_allow_themes" value="<?php echo $allow_themes; ?>" />
        <input type="hidden" name="ure_user_id" id="ure_user_id" value="<?php echo $user->ID; ?>" />
<?php        
    }
    // end of edit_user_allowed_themes_list()

    
   /**
     * Load plugin javascript stuff
     * 
     * @param string $hook_suffix
     */
    public function admin_load_js($hook_suffix) {
        
        if ($hook_suffix === 'user-edit.php') {
            wp_enqueue_script('jquery-ui-dialog', false, array('jquery-ui-core', 'jquery-ui-button', 'jquery'));
            wp_register_script('ure-jquery-multiple-select', plugins_url('/js/jquery.multiple.select.js', URE_PLUGIN_FULL_PATH));
            wp_enqueue_script('ure-jquery-multiple-select');
            wp_register_script('ure-user-profile-themes', plugins_url('/js/pro/ure-pro-user-profile-themes.js', URE_PLUGIN_FULL_PATH));
            wp_enqueue_script('ure-user-profile-themes');
            wp_localize_script('ure-user-profile-themes', 'ure_pro_data_themes', array(
                'wp_nonce' => wp_create_nonce('user-role-editor'),
                'edit_allowed_themes' => __('Edit Themes List', 'user-role-editor'),
                'edit_allowed_themes_title' => __('Themes list you allow this user to activate/deactivate', 'user-role-editor'),
                'save_themes_list' => __('Save', 'user-role-editor'),
                'close' => __('Close', 'user-role-editor'),
            ));
        }
    }
    // end of admin_load_js()
    
    
    public function admin_css_action() {        
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_style('ure-jquery-multiple-select', plugins_url('/css/pro/multiple-select.css', URE_PLUGIN_FULL_PATH), array(), false, 'screen');
    }
    // end of admin_css_action()
                        
    
    // returns installed themes list in the form of associative array, indexed by theme's slug
    protected function get_installed_themes_assoc() {
    
        $themes = wp_prepare_themes_for_js();
        $themes_assoc = array();
        foreach($themes as $theme) {
            $themes_assoc[$theme['id']] = 1;
        }
        
        return $themes_assoc;
    }
    // end of get_installed_theme_assoc()
    
        
    // save additional allowed for activation themes list when user profile is updated, 
    // as WordPress itself doesn't know about them
    public function save_user_allowed_themes_list($user_id) {

        if (!current_user_can('switch_themes', $user_id)) {
            return;
        }
        
        $themes_list_str = '';                
        // update themes list access restriction: comma separated themes names list
        if (isset($_POST['ure_allow_themes'])) {
            $themes_list = explode(',', $_POST['ure_allow_themes']);
            if (count($themes_list)>0) {
                $installed_themes = $this->get_installed_themes_assoc();
                $validated_list = array();
                foreach($themes_list as $theme) {
                    $slug = trim($theme);
                    if (isset($installed_themes[$slug])) {
                        $validated_list[] = $slug;
                    }
                }
                $themes_list_str = implode(',', $validated_list);
            }            
        }
        update_user_meta($user_id, $this->user_meta_key, $themes_list_str);
    }
    // end of save_allowed_themes_list()    
    
    
    private function get_allowed_themes_list($user_id=0) {
        
        global $current_user;
    
        if (empty($user_id)) {  //  return data for current user
            $user_id = $current_user->ID;
        }
        $data = trim(get_user_meta($user_id, $this->user_meta_key, true));
        if (empty($data)) {
            $allowed_themes_list = array();
        } else {
            $allowed_themes_list = explode(',', $data);
        }
            
        return $allowed_themes_list;
    }
    // end of get_allowed_themes_list()
    
        
    public function prohibited_links_redirect() {
        
        global $current_user;
        
        if (!$this->user_can_activate_themes($current_user)) {        
            return;   
        }
            
        if ( stripos($_SERVER['REQUEST_URI'], 'wp-admin/themes.php?action')===false ) {
            return;
        }    

        $allowed_themes_list = $this->get_allowed_themes_list($current_user);
        if (count($allowed_themes_list)==0) {
            return;
        }
        // extract theme slug
        $args = wp_parse_args($_SERVER['REQUEST_URI'], array() );    
        if ( isset($args['stylesheet']) ) {            
            if ( !in_array($args['stylesheet'], $allowed_themes_list) ) {    // access to this themes is prohibited - redirect user back to the themes list
                // its late to use wp_redirect() as WP sent some headers already, so use JavaScript for redirection
?>
        <script>
            document.location.href = '<?php echo get_option('siteurl') . '/wp-admin/themes.php'; ?>';
        </script>
<?php                    
                die;
            }
        }
                                    
    }
    // end of prohibited_links_redirect()

                
  /** 
   * Filter out URE plugin from not superadmin users
   * @param type array $plugins plugins list
   * @return type array $plugins updated plugins list
   */
  public function restrict_themes_list($themes) {
    global $current_user;

    $ure_key_capability = $this->lib->get_key_capability();
    // if multi-site, then allow plugin activation for network superadmins and, if that's specially defined, - for single site administrators too    
    if ($this->lib->user_has_capability($current_user, $ure_key_capability)) {    
      return $themes;
    }
    
    $allowed_themes_list = $this->get_allowed_themes_list();
    // exclude prohibited themes from themes list
    foreach ($themes as $key => $value) {
      if (!in_array($key, $allowed_themes_list)) {
        unset($themes[$key]);
      }
    }

    return $themes;
  }
  // end of restrict_themes_list()
  
}
// end of URE_Themes_Access
