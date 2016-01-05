<?php
/*
 * User Role Editor WordPress plugin
 * Class URE_Posts_View - "Posts View Access" add-on support
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v2+ 
 */

class URE_Posts_View {

    private $lib = null;
    const ACCESS_DATA_KEY = 'ure_posts_view_access_data';
    
    
    public function __construct($lib) {
        
        $this->lib = $lib;
        
    }
    // end of __construct()
    
        
    /**
     * Load access data for role
     * @param string $role_id
     * @return array
     */
    public function load_access_data_for_role($role_id) {
        
        $access_data = get_option(self::ACCESS_DATA_KEY);
        if (is_array($access_data) && array_key_exists($role_id, $access_data)) {
            $result =  $access_data[$role_id];
            if (!isset($result['access_model'])) {
                $result['data'] = $result;
                $result['access_model'] = 1;
                $result['access_error_action'] = 1;
            }
        } else {
            $result = array(
                'access_model'=>1,
                'access_error_action'=>1,
                'data'=>array());
        }
        
        return $result;
    }
    // end of load_access_data_for_role()
    
    
    public function load_access_data_for_user($user) {
    
        if (is_object($user)) {
            $id = $user->ID;
        } else if (is_int($user)) {
            $id = $user;
            $user = get_user_by('id', $user);
        } else {
            $user = get_user_by('login', $user);
            $id = $user->ID;
        }
        
        $blocked = get_user_meta($user->ID, self::ACCESS_DATA_KEY, true);
        if (!is_array($blocked)) {
            $blocked = array(
                'access_model'=>0, 
                'access_error_action'=>0, 
                'data'=>array());
        }
        
        $access_data = get_option(self::ACCESS_DATA_KEY);
        if (empty($access_data)) {
            $access_data = array();
        }
        
        foreach ($user->roles as $role) {
            if (isset($access_data[$role])) {
                if (!isset($access_data[$role]['access_model'])) { // for backward compatibility
                    $access_model = 1;   // Use default (block selected) access model
                    $data = $access_data[$role];
                } else {
                    $access_model = $access_data[$role]['access_model'];
                    $data = $access_data[$role]['data'];
                }
                if (!isset($access_data[$role]['access_error_action'])) {
                    $access_error_action = 1;
                } else {
                    $access_error_action = $access_data[$role]['access_error_action'];
                }
                if (empty($blocked['access_model'])) {  
                    $blocked['access_model'] = $access_model;    // take the 1st found role's access model as the main one                    
                }
                if (empty($blocked['access_error_action'])) {  
                    $blocked['access_error_action'] = $access_error_action;    // take the 1st found role's access error action as the main one                    
                }
                // take into account data with the same access model only as the 1st one found
                if ($access_model==$blocked['access_model']) {
                    $blocked['data'] = array_merge($blocked['data'], $data);
                }
            }
        }
        
        if (empty($blocked['access_model'])) {
            $blocked['access_model'] = 1; // use default value
        }
        if (empty($blocked['access_error_action'])) {
            $blocked['access_error_action'] = 1; // use default value
        }
        //$blocked['data'] = array_unique ($blocked['data']);
        
        return $blocked;
    }
    // end of load_access_data_for_user()

    
    protected function get_access_data_from_post() {
        
        $keys_to_skip = array(
            'action', 
            'ure_nonce', 
            '_wp_http_referer', 
            'ure_object_type', 
            'ure_object_name', 
            'user_role', 
            'ure_access_model',
            'ure_posts_list');
        $access_model = $_POST['ure_access_model'];
        if ($access_model!=1 && $access_model!=2) { // got invalid value
            $access_model = 1;  // use default value
        }        
        $access_error_action = $_POST['ure_post_access_error_action'];
        if ($access_error_action!=1 && $access_error_action!=2) { // got invalid value
            $access_error_action = 1;  // use "return 404 HTTP error" as a default value
        }
        $access_data = array(
            'access_model'=>$access_model, 
            'access_error_action'=>$access_error_action,
            'data'=>array('terms'=>array(), 'posts'=>array()));
        foreach (array_keys($_POST) as $key) {
            if (in_array($key, $keys_to_skip)) {
                continue;
            }
            $value = filter_var($key, FILTER_SANITIZE_STRING);
            $values = explode('_', $value);
            $term_id = $values[1];
            if ($term_id>0) {
                $access_data['data']['terms'][] = $term_id;
            }
        }
        
        if (!empty($_POST['ure_posts_list'])) {
            $posts_list = explode(',', trim($_POST['ure_posts_list']));
            if (count($posts_list)>0) {                
                $access_data['data']['posts'] = $this->lib->filter_int_array($posts_list);
            }            
        }
        
        return $access_data;
    }
    // end of get_access_data_from_post()
        
    
    public function save_access_data_for_role($role_id) {
        $access_for_role = $this->get_access_data_from_post();
        $access_data = get_option(self::ACCESS_DATA_KEY);        
        if (!is_array($access_data)) {
            $access_data = array();
        }
        if (count($access_for_role)>0) {
            $access_data[$role_id] = $access_for_role;
        } else {
            unset($access_data[$role_id]);
        }
        update_option(self::ACCESS_DATA_KEY, $access_data);
    }
    // end of save_access_data_for_role()
    
    
    public function save_access_data_for_user($user_login) {
        $access_for_user = $this->get_access_data_from_post();
        // TODO ...
    }
    // end of save_menu_access_data_for_role()   
                    
    
    protected function get_allowed_roles($user) {
        $allowed_roles = array();
        if (empty($user)) {   // request for Role Editor - work with currently selected role
            $current_role = filter_input(INPUT_POST, 'current_role', FILTER_SANITIZE_STRING);
            $allowed_roles[] = $current_role;
        } else {    // request from user capabilities editor - work with that user roles
            $allowed_roles = $user->roles;
        }
        
        return $allowed_roles;
    }
    // end of get_allowed_roles()
                            
    
    public function get_html($user=null) {        
                        
        $allowed_roles = $this->get_allowed_roles($user); 
        $taxonomies = get_taxonomies(
                array('public'=>true,
                      'show_ui'=>true), 
                'objects');
                
        if (empty($user)) {
            $ure_object_type = 'role';
            $ure_object_name = $allowed_roles[0];
            $blocked_items = $this->load_access_data_for_role($ure_object_name);
        } else {
            $ure_object_type = 'user';
            $ure_object_name = $user->user_login;
            $blocked_items = $this->load_access_data_for_user($ure_object_name);
        }

        $terms = array();
        if (isset($blocked_items['data']['terms']) && is_array($blocked_items['data']['terms'])) {
            $terms = $blocked_items['data']['terms'];
        }    
        
        $posts_list = '';
        if (isset($blocked_items['data']['posts']) && is_array($blocked_items['data']['posts'])) {
            $posts_list = implode(', ', $blocked_items['data']['posts']);
        }        
        
        ob_start();
?>
<form name="ure_posts_view_access_form" id="ure_posts_view_access_form" method="POST"
      action="<?php echo URE_WP_ADMIN_URL . URE_PARENT.'?page=users-'.URE_PLUGIN_FILE;?>" >
    <span style="font-weight: bold;"><?php echo esc_html_e('Block:', 'user-role-editor');?></span>&nbsp;&nbsp;
    <input type="radio" name="ure_access_model" id="ure_access_model_selected" value="1" 
        <?php echo ($blocked_items['access_model']==1) ? 'checked="checked"' : '';?> > <label for="ure_access_model_selected"><?php esc_html_e('Selected', 'user-role-editor');?></label> 
    <input type="radio" name="ure_access_model" id="ure_access_model_not_selected" value="2" 
        <?php echo ($blocked_items['access_model']==2) ? 'checked="checked"' : '';?> > <label for="ure_access_model_not_selected"><?php esc_html_e('Not Selected', 'user-role-editor');?></label>
    <hr/>
    <input type="radio" id="ure_return_http_error_404" name="ure_post_access_error_action" value="1" 
        <?php echo ($blocked_items['access_error_action']==1) ? 'checked="checked"' : '';?>>
    <label for="ure_return_http_error_404">Return HTTP 404 error</label>&nbsp;&nbsp;
    <input type="radio" id="ure_show_post_access_error_message" name="ure_post_access_error_action" value="2" 
           <?php echo ($blocked_items['access_error_action']==2) ? 'checked="checked"' : '';?>>
    <label for="ure_show_post_access_error_message">Show access error message</label>
    <hr/>
    <span style="font-weight: bold;"><?php echo esc_html_e('Posts ID list (comma separated)', 'user-role-editor');?>:</span>
    <input type="text" id="ure_posts_list" name="ure_posts_list" value="<?php echo $posts_list;?>" style="width: 300px;" />
    <hr/>
<?php
    foreach($taxonomies as $tax_id=>$tax_obj) {
?>
    <span style="font-weight: bold;"><?php echo $tax_obj->labels->name;?></span>
<table id="ure_posts_view_access_table">
    <th><input type="checkbox" id="ure_cb_select_all"></th>
    <th style="min-width: 30px;"><?php esc_html_e('ID','user-role-editor');?></th>
    <th><?php echo $tax_obj->labels->singular_name;?></th>
<?php
        $categories = get_categories(array(
            'type' => 'post',
            'child_of' => 0,
            'parent' => '',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'number' => '',
            'taxonomy' => $tax_id,
            'pad_counts' => false
        ));
    
        foreach($categories as $category) {
?>
    <tr>
        <td>   
<?php     
        $checked = in_array($category->term_id, $terms) ? 'checked' : '';
        $cb_class = 'ure-cb-column';
        $disabled = '';
        $category_name = ($category->parent>0 ? ' - ':'') . $category->name;
?>
            <input type="checkbox" name="<?php echo 'cat_'. $category->term_id;?>" id="<?php echo 'cat_'. $category->term_id;?>" class="<?php echo $cb_class;?>" <?php echo $checked .' '. $disabled;?> />
        </td>
        <td><?php echo $category->term_id;?></td>
        <td title="<?php echo $category->description;?>"><?php echo $category_name;?></td>
    </tr> 
<?php
        }   // foreach($categories...
?>
    </tr>        
</table> 
<hr/>
<?php
    }   // foreach($taxonomies...
?>
    <input type="hidden" name="action" id="action" value="ure_update_posts_view_access" />
    <input type="hidden" name="ure_object_type" id="ure_object_type" value="<?php echo $ure_object_type;?>" />
    <input type="hidden" name="ure_object_name" id="ure_object_name" value="<?php echo $ure_object_name;?>" />
<?php
    if ($ure_object_type=='role') {
?>
    <input type="hidden" name="user_role" id="ure_role" value="<?php echo $ure_object_name;?>" />
<?php
    }
?>
    <?php wp_nonce_field('user-role-editor', 'ure_nonce'); ?>
</form>    
<?php    
        $html = ob_get_contents();
        ob_end_clean();
        
        if (!empty($user)) {
            $current_object = $user->user_login;
        } else {
            $current_object = $allowed_roles[0];
        }
     
        return array('result'=>'success', 'message'=>'Posts view permissions for '+ $current_object, 'html'=>$html);
    }
    // end of get_html()

}
// end of URE_Posts_View class
