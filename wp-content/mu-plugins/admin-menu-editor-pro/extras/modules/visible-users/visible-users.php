<?php

class ameVisibleUsers {
	/**
	 * @var WPMenuEditor
	 */
	private $wp_menu_editor;

	public function __construct($wp_menu_editor) {
		$this->wp_menu_editor = $wp_menu_editor;

		add_action('wp_ajax_ws_ame_search_users', array($this, 'ajax_search_users'));

		add_action('admin_menu_editor-register_scripts', array($this, 'register_scripts'));
		add_filter('admin_menu_editor-script_data', array($this, 'add_script_data'));
		add_filter('admin_menu_editor-editor_script_dependencies', array($this, 'add_editor_script'));
	}

	public function ajax_search_users() {
		global $wpdb; /** @var wpdb $wpdb */
		global $wp_roles;

		if ( !$this->wp_menu_editor->current_user_can_edit_menu() ) {
			die($this->wp_menu_editor->json_encode(array(
				'error' => __("You don't have permission to use Admin Menu Editor Pro.", 'admin-menu-editor')
			)));
		}

		if ( !check_ajax_referer('search_users', false, false) ){
			die($this->wp_menu_editor->json_encode(array(
				'error' => __("Access denied. Invalid nonce.", 'admin-menu-editor')
			)));
		}

		$query = strval($_GET['query']);
		$limit = intval($_GET['limit']);
		if ( $limit > 50 ) {
			$limit = 50;
		}

		$capability_key = $wpdb->prefix . 'capabilities';
		$sql =
			"SELECT ID, user_login, display_name, meta_value as capabilities
			 FROM {$wpdb->users} LEFT JOIN {$wpdb->usermeta}
			 ON ({$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key = \"$capability_key\") ";

		if ( !empty($query) ) {
			$like = '%' . $wpdb->esc_like($query) . '%';
			$sql .= $wpdb->prepare(
				' WHERE (user_login LIKE %s) OR (display_name LIKE %s) ',
				$like, $like
			);
		}

		$sql .= ' LIMIT ' . ($limit + 1); //Ask for +1 result so that we know if there are additional results.

		$users = $wpdb->get_results($sql, ARRAY_A);

		$is_multisite = is_multisite();
		if ( !isset($wp_roles) ) {
			$wp_roles = new WP_Roles();
		}

		$results = array();
		foreach($users as $user) {
			//Capabilities (when present) are stored as serialized PHP arrays.
			if ( !empty($user['capabilities']) ) {
				$capabilities = unserialize($user['capabilities']);
			} else {
				$capabilities = array();
			}

			//Get roles from capabilities.
			$roles = array_filter(array_keys($capabilities), array($wp_roles, 'is_role'));

			$results[] = array(
				'id' => $user['ID'],
				'user_login' => $user['user_login'],
				'capabilities' => $capabilities,
				'roles' => $roles,
				'is_super_admin' => $is_multisite && is_super_admin($user['ID']),
				'display_name' => $user['display_name'],
			);
		}

		$more_results_available = false;
		if ( count($results) > $limit ) {
			$more_results_available = true;
			array_pop($results);
		}

		$response = array(
			'users' => $results,
			'moreResultsAvailable' => $more_results_available,
		);
		die($this->wp_menu_editor->json_encode($response));
	}

	public function register_scripts() {
		wp_register_auto_versioned_script(
			'ame-visible-users',
			plugins_url('extras/modules/visible-users/visible-users.js', $this->wp_menu_editor->plugin_file),
			array('jquery', 'ame-lodash')
		);
	}

	public function add_script_data($data) {
		$data['searchUsersNonce'] = wp_create_nonce('search_users');
		return $data;
	}

	public function add_editor_script($dependencies) {
		$dependencies[] = 'ame-visible-users';
		return $dependencies;
	}
}