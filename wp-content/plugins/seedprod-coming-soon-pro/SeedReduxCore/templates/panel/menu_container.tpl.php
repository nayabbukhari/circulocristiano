<?php
/**
 * The template for the menu container of the panel.
 *
 * Override this template by specifying the path where it is stored (templates_path) in your SeedRedux config.
 *
 * @author 		SeedRedux Framework
 * @package 	SeedReduxFramework/Templates
 * @version     3.4.3
 */
?>
<div class="seedredux-sidebar">
    <ul class="seedredux-group-menu">
        <?php
        foreach ( $this->parent->sections as $k => $section ) {
            $title = isset ( $section[ 'title' ] ) ? $section[ 'title' ] : '';

            $skip_sec = false;
            foreach ( $this->parent->hidden_perm_sections as $num => $section_title ) {
                if ( $section_title == $title ) {
                    $skip_sec = true;
                }
            }

            if ( isset ( $section[ 'customizer_only' ] ) && $section[ 'customizer_only' ] == true ) {
                continue;
            }

            if ( false == $skip_sec ) {
                echo $this->parent->section_menu ( $k, $section );
                $skip_sec = false;
            }
        }

        /**
         * action 'seedredux-page-after-sections-menu-{opt_name}'
         *
         * @param object $this SeedReduxFramework
         */
        do_action ( "seedredux-page-after-sections-menu-{$this->parent->args[ 'opt_name' ]}", $this );

        /**
         * action 'seedredux/page/{opt_name}/menu/after'
         *
         * @param object $this SeedReduxFramework
         */
        do_action ( "seedredux/page/{$this->parent->args[ 'opt_name' ]}/menu/after", $this );

        ?>

        <?php if ( $this->parent->args[ 'system_info' ] === true ) : ?>
            <li id="system_info_default_section_group_li" class="seedredux-group-tab-link-li">
                <?php
                if ( !empty ( $this->parent->args[ 'icon_type' ] ) && $this->parent->args[ 'icon_type' ] == 'image' ) {
                    $icon = (!isset ( $this->parent->args[ 'system_info_icon' ] ) ) ? '' : '<img src="' . $this->parent->args[ 'system_info_icon' ] . '" /> ';
                } else {
                    $icon_class = (!isset ( $this->parent->args[ 'system_info_icon_class' ] ) ) ? '' : ' ' . $this->parent->args[ 'system_info_icon_class' ];
                    $icon = (!isset ( $this->parent->args[ 'system_info_icon' ] ) ) ? '<i class="el el-info-circle' . $icon_class . '"></i>' : '<i class="icon-' . $this->parent->args[ 'system_info_icon' ] . $icon_class . '"></i> ';
                }
                ?>
                <a href="javascript:void(0);" id="system_info_default_section_group_li_a"
                   class="seedredux-group-tab-link-a custom-tab" data-rel="system_info_default"><?php echo $icon; ?><span
                        class="group_title"><?php _e ( 'System Info', 'seedredux-framework' ); ?></span></a>
            </li>
        <?php endif; ?>
    </ul>
</div>