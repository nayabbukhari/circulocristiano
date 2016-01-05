<?php

/*
 * User Role Editor WordPress plugin
 * Prohibit/Allow view of posts of selected categories for selected role - at User Role Editor dialog
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v2+ 
 */

class URE_Meta_Boxes_Access {

    const meta_boxes_access_cap = 'ure_meta_boxes_access';
    
    // reference to the code library object
    private $lib = null;        
    private $objects = null;
    private $notice = '';

    public function __construct($lib) {
        
        $this->lib = $lib;
        $this->objects = new URE_Meta_Boxes($this->lib);
        
        add_action('ure_role_edit_toolbar_service', array($this, 'add_toolbar_buttons'));
        add_action('ure_load_js', array($this, 'add_js'));
        add_action('ure_dialogs_html', array($this, 'dialog_html'));
        add_action('ure_process_user_request', array($this, 'update_access'));
        add_action('ure_process_user_request', array($this, 'update_access_notification'));                    

    }
    // end of __construct()


    public function add_toolbar_buttons() {
        if (!current_user_can('ure_meta_boxes_access')) {
            return;
        }
        // get full meta_boxes list copy from superadmin user
        $this->objects->update_meta_boxes_list_copy();        
?>                
        <button id="ure_meta_boxes_access_button" class="ure_toolbar_button" 
                title="<?php esc_html_e('Prohibit access to selected meta_boxes', 'user-role-editor');?>">
                    <?php esc_html_e('Meta Boxes', 'user-role-editor');?></button>
<?php

    }
    // end of add_toolbar_buttons()
    
    
    public function add_js() {
        wp_register_script( 'ure-meta_boxes-access', plugins_url( '/js/pro/meta-boxes-access.js', URE_PLUGIN_FULL_PATH ) );
        wp_enqueue_script ( 'ure-meta_boxes-access' );
        wp_localize_script( 'ure-meta_boxes-access', 'ure_data_meta_boxes_access',
                array(
                    'meta_boxes' => esc_html__('Meta Boxes', 'user-role-editor'),
                    'dialog_title' => esc_html__('Meta Boxes', 'user-role-editor'),
                    'update_button' => esc_html__('Update', 'user-role-editor'),
                    'edit_posts_required' => esc_html__('Turn ON at least "edit_posts" capability to manage access to meta_boxes for this role', 'user-role-editor')
                ));
    }
    // end of add_js()    
    
    
    public function dialog_html() {
        
?>
        <div id="ure_meta_boxes_access_dialog" class="ure-modal-dialog">
            <div id="ure_meta_boxes_access_container">
            </div>    
        </div>
<?php        
        
    }
    // end of dialog_html()

    
    public function update_access() {
    
        if (!isset($_POST['action']) || $_POST['action']!=='ure_update_meta_boxes_access') {
            return;
        }
        
        if (!current_user_can('ure_meta_boxes_access')) {
            $this->notice = esc_html__('URE: you do not have enough permissions to access this module.', 'user-role-editor');
            return;
        }
        
        $ure_object_type = filter_input(INPUT_POST, 'ure_object_type', FILTER_SANITIZE_STRING);
        if ($ure_object_type!=='role' && $ure_object_type!=='user') {
            $this->notice = esc_html__('URE: widgets access: Wrong object type. Data was not updated.', 'user-role-editor');
            return;
        }
        $ure_object_name = filter_input(INPUT_POST, 'ure_object_name', FILTER_SANITIZE_STRING);
        if (empty($ure_object_name)) {
            $this->notice = esc_html__('URE: widgets access: Empty object name. Data was not updated', 'user-role-editor');
            return;
        }
                        
        if ($ure_object_type=='role') {
            $this->objects->save_access_data_for_role($ure_object_name);
        } else {
            $this->objects->save_access_data_for_user($ure_object_name);
        }
        
    }
    // end of update_access()
    
    public function update_access_notification() {
        $this->lib->show_message($this->notice);
    }
    // end of update_menu_access_notification()
    
    
}	// end of URE_Metaboxes_Access class