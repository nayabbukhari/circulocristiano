<?php
/**
 * SeedRedux Overrides
 * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
 */

 // Remove Dashboard Widget
add_action('wp_dashboard_setup', 'seed_cspv4_remove_seedredux_dashboard',999);
function seed_cspv4_remove_seedredux_dashboard() {
    remove_meta_box('id', 'dashboard', 'side');
}

add_action( 'admin_menu', 'seed_cspv4_adjust_the_wp_menu', 999 );
function seed_cspv4_adjust_the_wp_menu() {
    remove_submenu_page( 'tools.php', 'seedredux-about' );
}


add_action( 'seedredux/page/seed_cspv4/form/after', 'seed_cspv4_addButtons' );

function seed_cspv4_addButtons() {
    echo '
    <script>
    jQuery( document ).ready(function($) {
        $( "<a target=\'_blank\' class=\'seed_csvp4_preview button button-primary\' href=\''.home_url().'?seed_cspv4_preview=true\' style=\'margin-right:3px;\'>'.__('Preview','seedprod').'</a>" ).prependTo( ".seedredux-action_bar" );
        $( "#3_section_group_li_a" ).click(function() {
            //window.location = \'options.php?page=seed_cspv4_subscribers\';
        });
    });
    </script>
    ';
}

add_action( 'seedredux/page/seed_cspv4/form/after', 'seed_cspv4_highlightTheme' );

function seed_cspv4_highlightTheme() {
    $seed_cspv4 = get_option('seed_cspv4');
    extract($seed_cspv4);
    echo '
    <script>
    jQuery( document ).ready(function($) {
        $("[value=\''.$theme.'\']").parent().addClass(\'seedredux-image-select-selected\');
    });
    </script>
    ';
}

add_action( 'seedredux/page/seed_cspv4/enqueue', 'seed_cspv4_addPanelCSS' );

function seed_cspv4_addPanelCSS() {
    $css = SEED_CSPV4_PLUGIN_URL . 'includes/settings-style.css';
    wp_register_style(
        'seed_cspv4-custom-css',
        $css ,
        array( 'seedredux-css' ), // Be sure to include seedredux-css so it's appended after the core css is applied
        time(),
        'all'
    );
    wp_enqueue_style('seed_cspv4-custom-css');
}

add_filter( "seedredux/seed_cspv4/field/class/icon_sortable", "seed_cspv4_overload_icon_sortable_field_path" );

function seed_cspv4_overload_icon_sortable_field_path($field) {

    return SEED_CSPV4_PLUGIN_PATH . 'includes/fields/icon_sortable.php';
}


add_filter( "seedredux/seed_cspv4/field/class/client_view", "seed_cspv4_overload_client_view_field_path" );

function seed_cspv4_overload_client_view_field_path($field) {
    return SEED_CSPV4_PLUGIN_PATH . 'includes/fields/client_view.php';
}



add_filter( "seedredux/seed_cspv4/field/class/license_check", "seed_cspv4_overload_license_check_field_path" );

function seed_cspv4_overload_license_check_field_path($field) {

    return SEED_CSPV4_PLUGIN_PATH . 'includes/fields/license_check.php';
}


add_filter( "seedredux/seed_cspv4/field/class/time_select", "seed_cspv4_overload_time_select_field_path" );

function seed_cspv4_overload_time_select_field_path($field) {

    return SEED_CSPV4_PLUGIN_PATH . 'includes/fields/time_select.php';
}

add_filter( "seedredux/seed_cspv4/field/class/theme_select", "seed_cspv4_overload_theme_select_field_path" );

function seed_cspv4_overload_theme_select_field_path($field) {

    return SEED_CSPV4_PLUGIN_PATH . 'includes/fields/theme_select.php';
}

if (!function_exists('seed_cspv4_seedredux_my_custom_field')):
function seed_cspv4_seedredux_my_custom_field($field, $value) {
    print_r($field);
    echo '<br/>kjhjkh';
    print_r($value);
}
endif;

function seed_cspv4_add_maintenenace_field($fields){
    $maintenance_file = WP_CONTENT_DIR."/maintenance.php";
    if (file_exists($maintenance_file)) {
        $field = array(array(
            'id'        => 'enable_maintenance_php',
            'type'      => 'switch',
            'title'     => __('Use maintenance.php', 'seedprod'),
            'subtitle'      => __('maintenance.php detected, would you like to use this for your landing page?', 'seedprod'),
            'default'   => '0'// 1 = on | 0 = off
        ));

        array_splice($fields, 1, 0, $field);
    }
    return $fields;
}

add_filter('seed_cspv4_general_fields','seed_cspv4_add_maintenenace_field');

// Subscriber page
add_action('admin_menu', 'seed_cspv4_register_subscribers_page');

function seed_cspv4_register_subscribers_page() {
    add_submenu_page( 'options.php', 'Subscribers', 'Subscribers', 'manage_options', 'seed_cspv4_subscribers', 'seed_cspv4_subscribers_callback' );
}

// Migration page
add_action('admin_menu', 'seed_cspv4_register_migration_page');

function seed_cspv4_register_migration_page() {
    add_submenu_page( 'options.php', 'Migration', 'Migration', 'manage_options', 'seed_cspv4_migration', 'seed_cspv4_migration_callback' );
}

function seed_cspv4_subscribers_callback() {
    echo '<div class="wrap">';
    echo '<h2>Coming Soon Pro > '.__('Subscribers', 'seedprod').' <a href="options-general.php?page=seed_cspv4_options" class="add-new-h2">'.__('Back to Options','seedprod').'</a></h2>';
    echo SEED_CSPV4::display_subscribers();
    echo '</div>';

}

function seed_cspv4_migration_callback() {
    echo '<div class="wrap">';
    echo '<h2>Coming Soon Pro >'.__('Migration', 'seedprod').' <a href="options-general.php?page=seed_cspv4_options" class="add-new-h2">'.__('Back to Options','seedprod').'</a></h2>';
    if (wp_verify_nonce($_GET['migrate'], 'migrate')) {
        seed_cspv4_import_v3();
        echo '<p>'.__('Migration Complete! Please double check your Settings and Landing Page to ensure it is working properly.', 'seedprod').'<br><a href="options-general.php?page=seed_cspv4_options">'.__('Go Back to Options.','seedprod').'</a><p>';

    }
    else{
        $url = wp_nonce_url(admin_url('options.php?page=seed_cspv4_migration'), 'migrate','migrate');
        echo __('<p>Warming! This will erase you current settings in the plugin. If you are unsure please make a back up of your database before continuing.','seedprod');
        echo '<br><br><a href="'.$url.'" class="button button-primary">'.__('Start Migration from Version 3','seedprod').'</a>';
        echo '</div>';
    }

}

function seed_cspv4_subscribers_page(){
    ob_start();
    echo '<a target="_blank" href="http://support.seedprod.com/article/70-subscribers">Learn More</a><br><br><a href="options.php?page=seed_cspv4_subscribers" class="button button-primary">'.__('View Subscribers','seedprod').'</a>';
    $output = ob_get_clean();
    return $output;
}

function seed_cspv4_support_page(){
    ob_start();
    echo '<a target="_blank" href="http://www.seedprod.com/support" class="button button-primary">'.__('View Support Docs or Open a Ticket','seedprod').'</a>';
    //echo '<br><br><a href="https://app.sellwp.co/seedprod/ticket" class="button button-primary">'.__('Open a Support Ticket','seedprod').'</a>';
    if(get_option('seed_csp3_settings_1') !== false){
        echo '<br><br><a href="options.php?page=seed_cspv4_migration" class="button button-primary">'.__('Migrate Settings from Version 3','seedprod').'</a>';
    }
    echo '<br><br><iframe width="560" height="315" src="//www.youtube.com/embed/Z2I5Oy-LZRc" frameborder="0" allowfullscreen></iframe>';
    $output = ob_get_clean();
    return $output;
}

// Hook into save

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_import_template_code' );

function seed_csvp4_import_template_code($value){
    if($value['custom_code'] == '1'){
        //Import template code flagged
        $code = SEED_CSPV4::get_default_template();
        $code = str_replace("<?php ", "[", $code);
        $code = str_replace("() ?>", "]", $code);
        // Set code field
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('template', $code);
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('custom_code', 0);
    }

}

add_action('seedredux/options/seed_cspv4/saved',  'seed_csvp4_theme_select' );

function seed_csvp4_theme_select($value){
    if(!empty($value['theme'])){
        update_option('seed_csvp4_theme',$value['theme']);
    }else{
        $theme = get_option('seed_csvp4_theme');
        global $seed_cspv4_seedreduxConfig;
        $seed_cspv4_seedreduxConfig->SeedReduxFramework->set('theme', $theme);
    }

}
