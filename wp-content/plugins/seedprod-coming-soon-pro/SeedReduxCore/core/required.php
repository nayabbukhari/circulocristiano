<?php

	if ( !defined ( 'ABSPATH' ) ) {
		exit;
	}

	if (!class_exists('seedreduxCoreRequired')){
		class seedreduxCoreRequired {
			public $parent      = null;

			public function __construct ($parent) {
				$this->parent = $parent;
				SeedRedux_Functions::$_parent = $parent;


				/**
				 * action 'seedredux/page/{opt_name}/'
				 */
				do_action( "seedredux/page/{$parent->args['opt_name']}/" );

			}


		}
	}