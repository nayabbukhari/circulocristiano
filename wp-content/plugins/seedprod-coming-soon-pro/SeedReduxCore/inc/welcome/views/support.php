<div class="wrap about-wrap" xmlns="http://www.w3.org/1999/html">
    <h1><?php _e( 'SeedRedux Framework - Support', 'seedredux-framework' ); ?></h1>

    <div
        class="about-text"><?php printf( __( 'We are an open source project used by developers to make powerful control panels.', 'seedredux-framework' ), $this->display_version ); ?></div>
    <div
        class="seedredux-badge"><i
            class="el el-seedredux"></i><span><?php printf( __( 'Version %s', 'seedredux-framework' ), SeedReduxFramework::$_version ); ?></span>
    </div>

    <?php $this->actions(); ?>
    <?php $this->tabs(); ?>

    <div id="support_div" class="support">

        <!-- multistep form -->
        <form id="supportform">

            <ul id="progressbar" class=" breadcrumb">
                <li class="active"><?php _e( 'Generate a Support URL', 'seedredux-framework' ); ?></li>
                <li href="#"><?php _e( 'Select Support Type', 'seedredux-framework' ); ?></li>
                <li href="#"><?php _e( 'How to Get Support', 'seedredux-framework' ); ?></li>
            </ul>

            <!-- fieldsets -->
            <fieldset>
                <h2 class="fs-title"><?php _e( 'Submit a Support Request', 'seedredux-framework' ); ?></h2>

                <h3 class="fs-title"
                    style="margin-top:0;"><?php _e( 'To get started, we will need to generate a support hash.', 'seedredux-framework' ); ?></h3>

                <p><?php
                        echo sprintf( wp_kses( __( 'This will provide to your developer all the information they may need to remedy your issue. This action WILL send information securely to a remote server. To see the type of information sent, please look at the  <a href="%s">Status tab</a>.', 'seedredux-framework' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'tools.php?page=seedredux-status' ) ) );
                    ?>
                </p>

                <p><a href="#" class="docs button button-primary button-large seedredux_support_hash"><?php _e( 'Generate
                        a Support URL', 'seedredux-framework' ); ?></a></p>
                <input type="button" name="next" class="next hide action-button"
                       value="Next"/>
            </fieldset>

            <fieldset>
                <h2 class="fs-title"><?php _e( 'Select Your Support Type', 'seedredux-framework' ); ?></h2>

                <h3 class="fs-subtitle" style="text-align: center;">
                    <?php _e( 'What type of user are you?', 'seedredux-framework' ); ?>
                </h3>

                <table id="user_type">
                    <tr>
                        <td id="is_user"><i class="el el-user"></i><br/><?php _e( 'User', 'seedredux-framework' ); ?><br/>
                            <small><?php _e( 'I am a user, using a pre-built product.', 'seedredux-framework' ); ?></small>
                        </td>
                        <td id="is_developer"><i
                                class="el el-github"></i><br/><?php _e( 'Developer', 'seedredux-framework' ); ?><br/>
                            <small><?php _e( 'I am a developer, building a product using SeedRedux.', 'seedredux-framework' ); ?></small>
                        </td>
                    </tr>
                </table>

                <input type="button" name="next" class="next action-button hide" value="Next"/>
            </fieldset>
            <fieldset id="final_support">
                <h2 class="fs-title"><?php _e( 'How to Get Support', 'seedredux-framework' ); ?></h2>

                <div class="is_developer">
                    <p><?php _e( 'Please proceed to the SeedRedux Framework issue tracker and supply us with your support URL below. Please also provide any information that will help us to reproduce your issue.', 'seedredux-framework' ); ?></p>
                    <a href="https://github.com/seedreduxframework/seedredux-framework/issues" target="_blank"><h4>
                            https://github.com/seedreduxframework/seedredux-framework/issues</h4></a>

                </div>
                <div class="is_user">
                    <p align="left"><?php _e( 'Listed below are the Wordpress plugins and/or theme installed on your site that utilize SeedRedux Framework. We do not directly support products created with our framework.  It is the responsibility of the plugin or theme developer to support their work. You will need to contact the author(s) of the products listed below with your support questions.', 'seedredux-framework' ); ?></p>

                    <p>
                        <strong><?php _e( 'Please be sure to include for your developer - via cut and paste - the Support URL in the box below.', 'seedredux-framework' ); ?></strong>
                    </p>
                    <?php

                        $seedredux = SeedReduxFrameworkInstances::get_all_instances();
                        if ( ! empty( $seedredux ) ) {
                            echo '<code style="line-height: 30px;">';
                            foreach ( $seedredux as $panel ) {
                                echo '&nbsp;' . $panel->args['display_name'] . '';
                                if ( ! empty( $panel->args['display_version'] ) ) {
                                    echo ' v' . $panel->args['display_version'];
                                }
                                echo '&nbsp;<br />';
                            }
                            echo '</code><br />';
                        }


                        //
                        //    $t            = wp_get_theme();
                        //    $active_theme = array(
                        //        'name'       => $t->get( 'Name' ),
                        //        'version'    => $t->get( 'Version' ),
                        //        'template'   => $t->get( 'Template' ),
                        //        'productURL' => $t->get( 'ThemeURI' ),
                        //        'author'     => $t->get( 'Author' ),
                        //        'authorURL'  => $t->get( 'AuthorURI' ),
                        //    );
                        //    $parent_theme = array();
                        //
                        //    if ( ! empty( $active_theme['Template'] ) ) {
                        //        $pt           = wp_get_theme( $active_theme['Template'] );
                        //        $parent_theme = array(
                        //            'name'       => $pt->get( 'Name' ),
                        //            'version'    => $pt->get( 'Version' ),
                        //            'template'   => $pt->get( 'Template' ),
                        //            'productURL' => $pt->get( 'ThemeURI' ),
                        //            'author'     => $pt->get( 'Author' ),
                        //            'authorURL'  => $pt->get( 'AuthorURI' ),
                        //        );
                        //    }
                        //
                        //
                        //    $products = array( 'plugins' => array(), 'theme' => array(), 'unknown' => array() );
                        //
                        //    $active_plugins = (array) get_option( 'active_plugins', array() );
                        //
                        //    if ( is_multisite() ) {
                        //        $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
                        //    }
                        //
                        //    $plugins = array();
                        //
                        //    foreach ( $active_plugins as $plugin ) :
                        //        $plugin_data                     = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
                        //        $plugins[ $plugin_data['Name'] ] = array(
                        //            'name'       => $plugin_data['Name'],
                        //            'title'      => $plugin_data['Title'],
                        //            'version'    => $plugin_data['Version'],
                        //            'productURL' => $plugin_data['PluginURI'],
                        //            'author'     => $plugin_data['Author'],
                        //            'authorURL'  => $plugin_data['AuthorURI'],
                        //        );
                        //    endforeach;
                        //
                        //
                        //    foreach ( $seedredux as $panel ) {
                        //        if ( $active_theme == $panel->args['display_name'] ) {
                        //            $products['theme'] = $active_theme;
                        //        } else if ( $parent_theme == $panel->args['display_name'] ) {
                        //            $products['theme'] = $parent_theme;
                        //        } else if ( isset( $plugins[ $panel->args['display_name'] ] ) ) {
                        //            $products['plugins'][] = array(
                        //                'name'    => $panel->args['display_name'],
                        //                'version' => $panel->args['display_version']
                        //            );
                        //        } else {
                        //            $products['unknown'][] = array(
                        //                'name'    => $panel->args['display_name'],
                        //                'version' => $panel->args['display_version']
                        //            );
                        //        }
                        //    }
                        //    print_r( $products );
                        //?>
                    <!---->
                    <!--<ul id="toDebug">-->
                    <!--    --><?php
                        //
                        //        $active_plugins = (array) get_option( 'active_plugins', array() );
                        //
                        //        if ( is_multisite() ) {
                        //            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
                        //        }
                        //
                        //        foreach ( $active_plugins as $plugin ) :
                        //            $plugin_data           = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
                        //            $dirname               = dirname( $plugin );
                        //            $plugin_data['folder'] = dirname( $plugin );
                        //
                        //            ?>
                    <!--            <li><input class="checkbox plugins" name="type[]" id="p-->
                    <?php //echo $dirname; ?><!--"-->
                    <!--                       type="checkbox"-->
                    <!--                       value="-->
                    <?php //echo urlencode( json_encode( $plugin_data ) ); ?><!--"><label-->
                    <!--                    for="p-->
                    <?php //echo $dirname; ?><!--"><strong>--><?php //_e( 'Plugin', 'seedredux-framework' );?>
                    <!--                        : </strong> --><?php //echo $plugin_data['Name']; ?>
                    <!--                    <small>v--><?php //echo $plugin_data['Version']; ?><!--</small>-->
                    <!--                </label>-->
                    <!--            </li>-->
                    <!---->
                    <!--        --><?php
                        //        endforeach;
                        //
                        //        $active_theme = wp_get_theme();
                        //    ?>
                    <!--    <li><input class="checkbox theme" name="type[]"-->
                    <!--               id="--><?php //echo sanitize_html_class( $active_theme->Name ); ?><!--"-->
                    <!--               type="checkbox" data='--><?php //echo json_encode( $active_theme ); ?><!--'-->
                    <!--               value="-->
                    <?php //echo urlencode( json_encode( $active_theme ) ); ?><!--"><label-->
                    <!--            for="-->
                    <?php //echo sanitize_html_class( $active_theme->Name ); ?><!--"><strong>--><?php //_e( 'Theme', 'seedredux-framework' ); ?>
                    <!--                : </strong> --><?php //echo $active_theme->Name; ?>
                    <!--            <small>v--><?php //echo $active_theme->Version; ?><!--</small>-->
                    <!--        </label>-->
                    <!--    </li>-->
                    <!--</ul>-->


                </div>
                <textarea type="text" id="support_hash" name="hash" placeholder="Support Hash" readonly="readonly"
                          class="hash" value="http://support.seedredux.io/"></textarea>

                <p><em>
                        <?php
                            echo sprintf( wp_kses( __( 'Should the developer not be responsive, read the <a href="%s" target="_blank">following article</a> before asking for support from us directly.', 'seedredux-framework' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'http://docs.seedreduxframework.com/core/support-defined/' ) );
                        ?>
                    </em>


                </p>
                <input type="button" name="previous" class="previous action-button" value="Go Back"/>
            </fieldset>
        </form>


        <div class="clear" style="clear:both;"></div>
    </div>

</div>