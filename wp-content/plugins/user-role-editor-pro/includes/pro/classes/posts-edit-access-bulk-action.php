<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-post-edit-access-bulk-action
 *
 * @author vladimir
 */
class URE_Posts_Edit_Access_Bulk_Action {
    
    public function __construct() {

        add_action( 'admin_init', array($this, 'add_css') );
        add_action( 'admin_footer', array($this, 'add_js') );        

    }
    // end of __construct()
    
    
    public function add_css() {
        
        if (stripos($_SERVER['REQUEST_URI'], 'wp-admin/edit.php') === false) {
            return;
        }
        
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_style('ure-admin-css', URE_PLUGIN_URL . 'css/ure-admin.css', array(), false, 'screen');
    }
    // end of add_css()



    public function add_js() {
        global $post_type;
        
        if ( stripos($_SERVER['REQUEST_URI'], 'wp-admin/edit.php')===false ) {
            return;
        }
        
        if ( !in_array($post_type, array('post', 'page')) ) {
            return;
        }

?>
        <div id="ure_bulk_post_edit_access_dialog" class="ure-dialog">
            <div id="ure_bulk_post_edit_access_content" style="padding: 10px;">
                <span class="bold">What to do:</span>&nbsp;<input type="radio" name="ure_what_todo" id="ure_what_todo1" value="1" checked >
                <label for="ure_what_todo1">Add to existing data</label>
                <input type="radio" name="ure_what_todo" id="ure_what_todo2" value="2"  >
                <label for="ure_what_todo2">Replace existing data</label>
                <hr/>
                <input type="radio" name="ure_posts_restriction_type" id="ure_posts_restriction_type1" value="1" checked >
                <label for="ure_posts_restriction_type1">Allow</label>
                <input type="radio" name="ure_posts_restriction_type" id="ure_posts_restriction_type2" value="2"  >
                <label for="ure_posts_restriction_type2">Prohibit</label><br>
                edit these Posts (comma separated list of IDs):<br>
                <textarea name="ure_posts" id="ure_posts" rows="2" cols="50"></textarea><br/>
                for these Users (comma separated list of IDs):<br/>
                <textarea name="ure_users" id="ure_users" rows="2" cols="50"></textarea>
            </div>                
        </div>
<?php
        
        wp_enqueue_script('jquery-ui-dialog', false, array('jquery-ui-core','jquery-ui-button', 'jquery') );
        wp_register_script( 'ure-bulk-edit-access', plugins_url( '/js/pro/ure-bulk-edit-access.js', URE_PLUGIN_FULL_PATH ) );
        wp_enqueue_script ( 'ure-bulk-edit-access' );      
        wp_localize_script( 'ure-bulk-edit-access', 'ure_bulk_edit_access_data', array(
            'wp_nonce' => wp_create_nonce('user-role-editor'),
            'action_title' => esc_html__('Edit Access', 'user-role-editor'),
            'dialog_title' => esc_html(__('Editor Restrictions Helper', 'user-role-editor')),
            'apply' => esc_html(__('Apply', 'user-role-editor')),
            'provide_user_ids' => esc_html(__('Provide list of users ID', 'user-role-editor'))
              ));
        
    }
    // end of add_js()
        
}
// end of class URE_Posts_Edit_Access_Bulk_Action
