<?php

    /**
     * SeedReduxFrameworkInstances Functions
     *
     * @package     SeedRedux_Framework
     * @subpackage  Core
     */
    if ( ! function_exists( 'get_seedredux_instance' ) ) {

        /**
         * Retreive an instance of SeedReduxFramework
         *
         * @param  string $opt_name the defined opt_name as passed in $args
         *
         * @return object                SeedReduxFramework
         */
        function get_seedredux_instance( $opt_name ) {
            return SeedReduxFrameworkInstances::get_instance( $opt_name );
        }
    }

    if ( ! function_exists( 'get_all_seedredux_instances' ) ) {

        /**
         * Retreive all instances of SeedReduxFramework
         * as an associative array.
         *
         * @return array        format ['opt_name' => $SeedReduxFramework]
         */
        function get_all_seedredux_instances() {
            return SeedReduxFrameworkInstances::get_all_instances();
        }
    }