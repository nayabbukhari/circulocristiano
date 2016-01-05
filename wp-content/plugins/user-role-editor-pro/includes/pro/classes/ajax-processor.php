<?php

/*
 * User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Email: support@role-editor.com
 * License: GPLv2 or later
 */


/**
 * Process AJAX requrest from User Role Editor Pro
 */
class URE_Pro_Ajax_Processor extends URE_Ajax_Processor {
        
    
    protected function get_admin_menu() {
        $admin_menu = new URE_Admin_Menu($this->lib);
        $answer = $admin_menu->get_menu_html();
        
        return $answer;
    }
    // end of get_admin_menu()
    
    
    protected function get_widgets_list() {
        $widgets = new URE_Widgets($this->lib);
        $answer = $widgets->get_html();
        
        return $answer;
    }
    // end of get_widgets_list()
    
    
    protected function get_meta_boxes_list() {
        $meta_boxes = new URE_Meta_Boxes($this->lib);
        $answer = $meta_boxes->get_html();
        
        return $answer;
    }
    // end of get_widgets_list()
    
    
    protected function get_roles_list() {
        $other_roles = new URE_Other_Roles($this->lib);
        $answer = $other_roles->get_html();
        
        return $answer;
    }
    // end of get_widgets_list()
    
    
    protected function get_categories_list() {
        $posts_view = new URE_Posts_View($this->lib);
        $answer = $posts_view->get_html();
        
        return $answer;
    }
    // end of get_categories_list()

    
    protected function set_users_edit_restrictions() {
        
        $post_edit_access = new URE_Posts_Edit_Access($this->lib);
        $answer = $post_edit_access->set_users_edit_restrictions();
        
        return $answer;
    }
    // end of set_users_edit_restrictions()
    
    
    /**
     * AJAX requests dispatcher
     */    
    protected function _dispatch($action) {
        
        $answer = parent::_dispatch($action);
        if (substr($answer['message'], 0, 14)!='unknown action') {
            return $answer;
        }
        
        switch ($action) {            
            case 'get_admin_menu':
                $answer = $this->get_admin_menu();
                break;
            case 'get_widgets_list':
                $answer = $this->get_widgets_list();
                break;
            case 'get_meta_boxes_list':
                $answer = $this->get_meta_boxes_list();
                break;
            case 'get_roles_list':
                $answer = $this->get_roles_list();
                break;
            case 'get_categories_list':
                $answer = $this->get_categories_list();
                break;
            case 'set_users_edit_restrictions': {
                $answer = $this->set_users_edit_restrictions();
                break;
            }
          default:              
                $answer = array('result'=>'error', 'message'=>'unknown action "'. $action .'"');
        }
        
        return $answer;
    }    
    
}
// end of URE_Pro_Ajax_Processor
