<?php
/*
 * Stuff specific for User Role Editor Pro WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * 
*/

class Ure_Lib_Pro extends Ure_Lib {

    /**
     * Is this the Pro version?
     * @return boolean
     */ 
    public function is_pro() {
        return true;
    }
    // end of is_it_pro()
    
            
    protected function get_ure_caps() {
        $ure_caps = parent::get_ure_caps();
        
        $ure_caps['ure_export_roles'] = 1;
        $ure_caps['ure_import_roles'] = 1;
        $ure_caps['ure_admin_menu_access'] = 1;
        $ure_caps['ure_widgets_access'] = 1;
        $ure_caps['ure_meta_boxes_access'] = 1;
        $ure_caps['ure_other_roles_access'] = 1;
        $ure_caps['ure_edit_posts_access'] = 1;
        $ure_caps['ure_plugins_activation_access'] = 1;   
        $ure_caps['ure_view_posts_access'] = 1;
        if ($this->multisite) {
            $ure_caps['ure_themes_access'] = 1;
        }
        
        return $ure_caps;
    }
    // end of get_ure_caps()
    
    /**
     * return key capability to have access to User Role Editor Plugin
     * override the same method at UreLib to support custom key capability set by the user
     * 
     * @return string
     */
    public function get_key_capability() {
        $ure_key_capability = $this->get_option('ure_key_capability');
        if (!$this->multisite) {
            $key_capability = empty($ure_key_capability) ? URE_KEY_CAPABILITY : $ure_key_capability;
        } else {
            $enable_simple_admin_for_multisite = $this->get_option('enable_simple_admin_for_multisite', 0);
            if ( (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE == 1) || 
                 $enable_simple_admin_for_multisite) {
                $key_capability = empty($ure_key_capability) ? URE_KEY_CAPABILITY : $ure_key_capability;
            } else {
                $key_capability = 'manage_network_users';
            }
        }
        
        return $key_capability;
    }
    // end of get_key_capability()    
    
    
    /**
     * if returns true - make full syncronization of roles for all sites with roles from the main site
     * else - only currently selected role update is replicated
     * 
     * @return boolean
     */
    public function is_full_network_synch() {
        
        if (is_network_admin()) {
            $result = true;
        } else {
            $result = parent::is_full_network_synch();
        }
        
        return $result;
    }
    // end of is_full_network_synch()
       
    
    public function user_can_which($user, $caps) {
    
        foreach($caps as $cap){
            if ($this->user_has_capability($user, $cap)) {
                return $cap;
            }
        }

        return "";
        
    }
    // end of user_can_which()
 
    
    /**
     * if existing user was not added to the current blog - add him
     * @global type $blog_id
     * @param type $user
     * @return bool
     */
    protected function check_blog_user($user) {
        global $blog_id;
        
        $result = true;
        if (is_network_admin()) {
            if (!array_key_exists($blog_id, get_blogs_of_user($user->ID)) ) {
                $result = add_existing_user_to_blog( array( 'user_id' => $user->ID, 'role' => 'subscriber' ) );
            }
        }

        return $result;
    }
    // end of check_blog_user()
    
    
    /** Get user roles and capabilities from the main blog
     * 
     * @param int $user_id
     * @return boolean
     */
    protected function get_user_caps_from_main_blog($user_id) {
        global $wpdb;
        
        $meta_key = $wpdb->prefix.'capabilities';
        $query = "select meta_value
                    from $wpdb->usermeta
                    where user_id=$user_id and meta_key='$meta_key' limit 0, 1";
        $user_caps = $wpdb->get_var($query);
        if (empty($user_caps)) {
            return false;
        }
        return $user_caps;      
     
    }
    // end of get_user_caps_from_main_blog()
    
    
    protected function update_user_caps_for_blog($blog_id, $user_id, $user_caps) {
        global $wpdb;
        
        $meta_key = $wpdb->prefix.$blog_id.'_capabilities';
        $query = "update $wpdb->usermeta
                    set meta_value='$user_caps'
                    where user_id=$user_id and meta_key='$meta_key' limit 1";
        $result = $wpdb->query($query);
        
        return $result;
    }
    // end of update_user_caps_for_blog()
    
    
    protected function network_update_user($user) {        
                        
        $user_caps = $this->get_user_caps_from_main_blog($user->ID);
        $user_blogs = get_blogs_of_user($user->ID); // list of blogs, where user was registered           
        $blog_ids = $this->blog_ids;    // full list of blogs
        unset($blog_ids[0]);  // do not touch the main blog, it was updated already
        foreach($blog_ids as $blog_id) {
            if (!array_key_exists($blog_id, $user_blogs)) {
                $result = add_user_to_blog($blog_id, $user->ID, 'subscriber');
                if (!$result) {
                   return false;
                }
                do_action('added_existing_user', $user->ID, $result);                
            }
            $result = $this->update_user_caps_for_blog($blog_id, $user->ID, $user_caps);
            if (!$result) {
                return false;
            }
        }
        
        return true;
    }
    // end of network_update_user()

    
    public function init_result() {
        
        $result = new stdClass();
        $result->success = false;
        $result->message = '';
        
        return $result;
    }
    // end of init_result()
    
    
    /**
     * return addition to the array of built-in WP capabilities (WP 3.1 wp-admin/includes/schema.php) 
     * 
     * @return array 
     */
    public function get_built_in_wp_caps() {
        
        $caps = parent::get_built_in_wp_caps();
        $activate_create_post_capability = $this->get_option('activate_create_post_capability', false);
        if ($activate_create_post_capability) {
            $caps['create_posts'] = 1;
            $caps['create_pages'] = 1;            
        }
        
        return $caps;
    }
    // end of get_built_in_wp_caps()
            
         
    /**
     * Initializes roles and capabiliteis list if it is not done yet
     * 
     */
    protected function init_caps() {
        if (empty($this->full_capabilities)) {
            $this->roles = $this->get_user_roles();
            $this->init_full_capabilities();
        }        
    }
    // end of init_caps()
    
    
    public function build_html_caps_blocked_for_single_admin() {
        $this->init_caps();
        $allowed_caps = $this->get_option('caps_allowed_for_single_admin', array());
        $html = '';
        // Core capabilities list
        foreach ($this->full_capabilities as $capability) {
            if (!$capability['wp_core']) { // show WP built-in capabilities 1st
                continue;
            }
            if (!in_array($capability['inner'], $allowed_caps)) {
                $html .= '<option value="' . $capability['inner'] . '">' . $capability['inner'] . '</option>' . "\n";
            }
        }
        // Custom capabilities
        $quant = count($this->full_capabilities) - count($this->get_built_in_wp_caps());
        if ($quant > 0) {            
            // Custom capabilities list
            foreach ($this->full_capabilities as $capability) {
                if ($capability['wp_core']) { // skip WP built-in capabilities 1st
                    continue;
                }
                if (!in_array($capability['inner'], $allowed_caps)) {
                    $html .= '<option value="' . $capability['inner'] . '" style="color: blue;">' . $capability['inner'] . '</option>' . "\n";
                }
            }
        }

        return $html;
    }
    // end of build_html_caps_blocked_for_single_admin()


    public function build_html_caps_allowed_for_single_admin() {
        $allowed_caps = $this->get_option('caps_allowed_for_single_admin', array());
        if (count($allowed_caps)==0) {
            return '';
        }
        $this->init_caps();
        $build_in_wp_caps = $this->get_built_in_wp_caps();
        $html = '';
        // Core capabilities list
        foreach ($allowed_caps as $cap) {
            if (!isset($build_in_wp_caps[$cap])) { // show WP built-in capabilities 1st
                continue;
            }
            $html .= '<option value="' . $cap . '">' . $cap . '</option>' . "\n";
        }
        // Custom capabilities
        $quant = count($this->full_capabilities) - count($this->get_built_in_wp_caps());
        if ($quant > 0) {
            // Custom capabilities list
            foreach ($allowed_caps as $cap) {
                if (isset($build_in_wp_caps[$cap])) { // skip WP built-in capabilities 1st
                    continue;
                }
                $html .= '<option value="' . $cap . '" style="color: blue;">' . $cap . '</option>' . "\n";
            }
        }

        return $html;
    }
    // end of build_html_caps_allowed_for_single_admin()
    

    /**
     * Exclude unexisting capabilities
     * @param string $user_caps_array - name of POST variable with array of capabilities from user input
     */
    public function filter_existing_caps_input($user_caps_array) {
        
        if (isset($_POST[$user_caps_array]) && is_array($_POST[$user_caps_array])) {
            $user_caps = $_POST[$user_caps_array];
        } else {
            $user_caps = array();
        }
        if (count($user_caps)) {
            $this->init_caps();            
            foreach ($user_caps as $cap) {
                if (!isset($this->full_capabilities[$cap])) {
                    unset($user_caps[$cap]);
                }
            }
        }

        return $user_caps;
    }
    // end of filter_existing_caps_input()
    
    
    public function filter_int_array($input_array) {
        
        $output_arr = array();
        foreach($input_array as $value) {
            $int_value = (int) $value;  // save interger values only
            if ($int_value>0) {
                $output_arr[] = $int_value;
            }
        }    
        
        return $output_arr;
    }
    // end of filter_int_array()
    
    
    public function filter_int_array_to_str($input_array) {
        
        $output_arr = $this->filter_int_array($input_array);        
        $output_str = implode(', ', $output_arr);
        
        return $output_str;
    }
    // end of filter_int_array_to_str()
    
    
    
    public function get_edit_custom_post_type_caps() {
        $caps = wp_cache_get('ure_edit_custom_post_type_caps');
        if (empty($caps)) {
            $post_types = get_post_types(array('public'=>true, 'show_ui'=>true,), 'objects');
            $caps = array();
            foreach($post_types as $post_type) {
                if (!in_array($post_type->cap->edit_post, $caps)) {
                    $caps[] = $post_type->cap->edit_post;
                }
                if (!in_array($post_type->cap->edit_posts, $caps)) {
                    $caps[] = $post_type->cap->edit_posts;
                }
            }
            wp_cache_add('ure_edit_custom_post_type_caps', $caps);
        }
        
        return $caps;
    }
    // end of get_edit_custom_post_type_caps()
    
    
    // replicate addons access data from the main site to the whole network
    protected function network_replicate_full_addons_data() {
        global $wpdb;
        
        $activate_widgets_access_module = $this->get_option('activate_widgets_access_module', false);
        if (!$activate_widgets_access_module) {
            return true;
        }
        
        $replicate_widgets_access_restrictions = filter_input(INPUT_POST, 'ure_replicate_widgets_access_restrictions', FILTER_SANITIZE_NUMBER_INT);
        if (empty($replicate_widgets_access_restrictions)) {
            return true;
        }
    
        $widgets_access_restrictions = get_option(URE_WIDGETS::ACCESS_DATA_KEY);
        $serialized_war = serialize($widgets_access_restrictions);        
        
        foreach ($this->blog_ids as $blog_id) {
            $prefix = $wpdb->get_blog_prefix($blog_id);
            $options_table_name = $prefix . 'options';
            
            $query1 = "select option_id from $options_table_name where option_name='". URE_WIDGETS::ACCESS_DATA_KEY ."' limit 0,1";
            $option_id = $wpdb->get_var($query1);
            if ($option_id>0) {
                $query = "update $options_table_name
                            set option_value='$serialized_war'
                            where option_id=$option_id limit 1";
            } else {
                $query = "insert into $options_table_name
                            set option_name='". URE_WIDGETS::ACCESS_DATA_KEY ."',
                                option_value='$serialized_war'";
            }
            $wpdb->query($query);
            if ($wpdb->last_error) {
                $this->log_event($wpdb->last_error, true);
                return false;
            }
        }   // foreach()
        
        return true;
    }
    // end of direct_network_access_from_add_ons_update()

    
    // create assign_role object
    public function get_assign_role() {
        $assign_role = new URE_Assign_Role_Pro($this);
        
        return $assign_role;
    }
    // end of get_assign_role()

    
    /**
     * Return all available post types except non-public WordPress built-in post types
     * 
     * @return array
     */
    public function _get_post_types() {
        $all_post_types = get_post_types();
        $internal_post_types = get_post_types(array('public'=>false, '_builtin'=>true));
        $post_types = array_diff($all_post_types, $internal_post_types);
        
        return $post_types;
    }
    // end of get_post_types()
    
    
    public function about() {
?>       
        <h2>User Role Editor Pro</h2>
        <table>
            <tr>
                <td>
                    <strong>Version:</strong>
                </td> 
                <td>
                    <?php echo URE_VERSION ;?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Plugin URL:</strong>
                </td> 
                <td>
                    <a href="https://www.role-editor.com">www.role-editor.com</a>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Dowload URL:</strong>
                </td> 
                <td>
                    <a href="https://www.role-editor.com/download-plugin">www.role-editor.com/download-plugin</a>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Author:</strong>
                </td> 
                <td>
                    <a href="https://www.role-editor.com/about">Vladimir Garagulya</a>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <a href="mailto:support@role-editor.com" target="_top">Send support question</a>
                </td>
            </tr>
        </table>        
<?php        
    }
    // end of about()

}
// end of Ure_Lib_Pro()