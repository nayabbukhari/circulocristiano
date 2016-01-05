<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if (!class_exists('seedreduxDashboardWidget')) {
        class seedreduxDashboardWidget {
            public function __construct () {
                add_action('wp_dashboard_setup', array($this,'add_seedredux_dashboard'));
            }

            public function add_seedredux_dashboard() {
                add_meta_box('id', 'SeedRedux Framework News', array($this,'seedredux_dashboard_widget'), 'dashboard', 'side', 'high');
            }

            public function seedredux_dashboard_widget() {
                echo '<div class="rss-widget">';
                wp_widget_rss_output(array(
                     'url'          => 'http://seedreduxframework.com/feed/',
                     'title'        => 'REDUX_NEWS',
                     'items'        => 3,
                     'show_summary' => 1,
                     'show_author'  => 0,
                     'show_date'    => 1
                ));
                echo '</div>';
            }
        }

        new seedreduxDashboardWidget();
    }
