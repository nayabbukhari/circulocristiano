﻿<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
if(is_network_admin()){
	$post_type = $this->network_post_types[$_GET['ct_edit_post_type']];
	$custom_fields = get_site_option( 'ct_custom_fields' );
} else {
	$post_type = $this->post_types[$_GET['ct_edit_post_type']];
	$custom_fields = get_option( 'ct_custom_fields' );
}
if( !isset( $post_type['rewrite']['ep_mask'] ) ) {
	$post_type['rewrite']['ep_mask'] = EP_PERMALINK;
}

?>

<h3><?php esc_html_e('Edit Post Type', $this->text_domain); ?></h3>

<form action="#" method="post" class="ct-post-type">
	<div class="ct-wrap-left">
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Post Type', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Post Type', $this->text_domain) ?> <span class="ct-required">(<?php esc_html_e('required', $this->text_domain); ?>)</span></label>
					</th>
					<td>
						<input type="text" value="<?php if ( isset( $_GET['ct_edit_post_type'] ) ) echo esc_attr( $_GET['ct_edit_post_type'] ); ?>" disabled="disabled">
						<input type="hidden" id="post_type" name="post_type" value="<?php if ( isset( $_GET['ct_edit_post_type'] ) ) echo esc_attr( $_GET['ct_edit_post_type'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The new post type system name ( max. 20 characters ). Alphanumeric lower-case characters and underscores only. Min 2 letters. Once added the post type system name cannot be changed.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Supports', $this->text_domain) ?></h3>
			<table class="form-table supports">
				<tr>
					<th>
						<label><?php esc_html_e('Supports', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Register support of certain features for a post type.', $this->text_domain); ?></span></p>
						<label>
							<input type="checkbox" name="supports[title]" value="title" <?php checked(in_array('title', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Title', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[editor]" value="editor" <?php checked(in_array('editor', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Editor', $this->text_domain) ?></strong> - <?php esc_html_e('Content', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[author]" value="author" <?php checked(in_array('author', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Author', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[thumbnail]" value="thumbnail" <?php checked(in_array('thumbnail', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Thumbnail', $this->text_domain) ?></strong> - <?php esc_html_e('Featured Image - current theme must also support post-thumbnails.', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[excerpt]" value="excerpt" <?php checked(in_array('excerpt', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Excerpt', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[trackbacks]" value="trackbacks" <?php checked(in_array('trackbacks', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Trackbacks', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[custom_fields]" value="custom-fields" <?php checked(in_array('custom-fields', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Custom Fields', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[comments]" value="comments" <?php checked(in_array('comments', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Comments', $this->text_domain) ?></strong> - <?php esc_html_e('Also will see comment count balloon on edit screen.', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[revisions]" value="revisions" <?php checked(in_array('revisions', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Revisions', $this->text_domain) ?></strong> - <?php esc_html_e('Will store revisions.', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[page_attributes]" value="page-attributes" <?php checked(in_array('page-attributes', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Page Attributes', $this->text_domain) ?></strong> - <?php esc_html_e('Template and menu order - Hierarchical must be true!', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[post_formats]" value="post-formats" <?php checked(in_array('post-formats', $post_type['supports']) ); ?> />
							<span class="description"><strong><?php esc_html_e('Post Formats', $this->text_domain) ?></strong> - <?php esc_html_e('Add post formats.', $this->text_domain) ?></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Support Regular Taxonomies', $this->text_domain) ?></h3>
			<table class="form-table supports">
				<tr>
					<th>
						<label><?php esc_html_e('Support Regular Taxonomies', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Assign regular taxonomies to this Post Type.', $this->text_domain); ?></span></p>
						<?php if ( taxonomy_exists( 'category' ) ): ?>
						<label>
							<input type="checkbox" name="supports_reg_tax[category]" value="1" <?php checked(empty($post_type['supports_reg_tax']['category']), false); ?> />
							<span class="description"><strong><?php esc_html_e( 'Categories', $this->text_domain ) ?></strong></span>
						</label>
						<?php endif; ?>
						<br />
						<?php if ( taxonomy_exists( 'post_tag' ) ): ?>
						<label>
							<input type="checkbox" name="supports_reg_tax[post_tag]" value="1" <?php checked(empty($post_type['supports_reg_tax']['post_tag']), false); ?> />
							<span class="description"><strong><?php esc_html_e( 'Tags', $this->text_domain ) ?></strong></span>
						</label>
						<?php endif; ?>
					</td>
				</tr>
			</table>
		</div>

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Capability Type', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Capability Type', $this->text_domain) ?></label>
					</th>
					<td>
						<?php
						$capability_type = (isset( $post_type['capability_type'] ) ) ? $post_type['capability_type'] : 'post';
						?>
						<input type="text" name="capability_type" value="<?php echo esc_attr( $capability_type ); ?>" />
						<input type="checkbox" name="capability_type_edit" value="1" />
						<span class="description ct-capability-type-edit"><strong><?php esc_html_e('Edit' , $this->text_domain); ?></strong> (<?php esc_html_e('advanced' , $this->text_domain); ?>)</span>
						<br /><span class="description"><?php esc_html_e('The post type to use for checking read, edit, and delete capabilities. Default: "post".' , $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Map Meta Capabilities', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Map Meta Capabilities', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Use the internal default meta capability handling.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="map_meta_cap" value="1" <?php checked( ! isset($post_type['map_meta_cap']) || $post_type['map_meta_cap'] === true); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="map_meta_cap" value="0" <?php checked(isset($post_type['map_meta_cap']) && $post_type['map_meta_cap'] === false); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong> (default)</span>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e( 'Role Capabilities Settings', $this->text_domain ) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label for="roles"><?php esc_html_e( 'Assign Capabilities', $this->text_domain ) ?></label>
						<img id="ajax-loader" alt="ajax-loader" src="<?php echo set_url_scheme(admin_url() ) . 'images/loading.gif' ?>" />
					</th>
					<td>

						<select id="roles" name="roles">
							<?php
							global $wp_roles;
							$role_obj = $wp_roles->get_role( 'administrator' );
							foreach ( $wp_roles->role_names as $role => $name ): ?>
							<option value="<?php echo $role; ?>"><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
						<br /><span class="description"><?php echo sprintf(__('Select a role to which you want to assign %s capabilities.', $this->text_domain), $capability_type ); ?></span>
						<br /><br />
						<div id="capabilities">
							<?php

							$all_caps = $this->all_capabilities($_GET['ct_edit_post_type']);
							if( ! empty($all_caps) ){
								sort($all_caps);
								foreach ( $all_caps as $capability ):
									$description = ucfirst(str_replace('_', ' ',$capability));
									?>
									<input type="checkbox" name="capabilities[<?php echo $capability; ?>]" id="<?php echo $capability; ?>" value="1" />
									<label for="<?php echo $capability; ?>"><span class="description"><?php echo $description; ?></span></label>
									<br />
									<?php
								endforeach;
							}
							?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
					</th>
					<td>
						<input type="hidden" name="action" value="ct_save" />
						<input type="hidden" name="key" value="general_settings" />
						<input type="button" class="button" id="save_roles" name="save_roles" value="Save this Role's Changes" />
					</td>
				</tr>
			</table>
		</div>


		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Labels', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[name]" value="<?php if ( isset( $post_type['labels']['name'] ) ) echo esc_attr( $post_type['labels']['name'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('General name for the post type, usually plural.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Singular Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[singular_name]" value="<?php if ( isset( $post_type['labels']['singular_name'] ) ) echo esc_attr( $post_type['labels']['singular_name'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('Name for one object of this post type. Defaults to value of name.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Add New', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[add_new]" value="<?php if ( isset( $post_type['labels']['add_new'] ) ) echo esc_attr( $post_type['labels']['add_new'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The add new text. The default is Add New for both hierarchical and non-hierarchical types.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Add New Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[add_new_item]" value="<?php if ( isset( $post_type['labels']['add_new_item'] ) ) echo esc_attr( $post_type['labels']['add_new_item'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The add new item text. Default is Add New Post/Add New Page.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Edit Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[edit_item]" value="<?php if ( isset( $post_type['labels']['edit_item'] ) ) echo esc_attr( $post_type['labels']['edit_item'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The edit item text. Default is Edit Post/Edit Page.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('New Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[new_item]" value="<?php if ( isset( $post_type['labels']['new_item'] ) ) echo esc_attr( $post_type['labels']['new_item'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The new item text. Default is New Post/New Page.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('View Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[view_item]" value="<?php if ( isset( $post_type['labels']['view_item'] ) ) echo esc_attr( $post_type['labels']['view_item'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The view item text. Default is View Post/View Page.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Search Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[search_items]" value="<?php if ( isset( $post_type['labels']['search_items'] ) ) echo esc_attr( $post_type['labels']['search_items'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The search items text. Default is Search Posts/Search Pages.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Not Found', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[not_found]" value="<?php if ( isset( $post_type['labels']['not_found'] ) ) echo esc_attr( $post_type['labels']['not_found'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The not found text. Default is No posts found/No pages found.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Not Found In Trash', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[not_found_in_trash]" value="<?php if ( isset( $post_type['labels']['not_found_in_trash'] ) ) echo esc_attr( $post_type['labels']['not_found_in_trash'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The not found in trash text. Default is No posts found in Trash/No pages found in Trash.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Parent Item Colon', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[parent_item_colon]" value="<?php if ( isset( $post_type['labels']['parent_item_colon'] ) ) echo esc_attr( $post_type['labels']['parent_item_colon'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The parent text. This string isn\'t used on non-hierarchical types. In hierarchical ones the default is Parent Page', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Custom Fields block', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[custom_fields_block]" value="<?php if ( isset( $post_type['labels']['custom_fields_block'] ) ) echo esc_attr( $post_type['labels']['custom_fields_block'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('Title of Custom Fields block.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Description', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Description', $this->text_domain) ?></label>
					</th>
					<td>
						<textarea name="description" cols="52" rows="3"><?php if ( isset( $post_type['description'] ) ) echo esc_textarea( $post_type['description'] ); ?></textarea>
						<br /><span class="description"><?php esc_html_e('A short descriptive summary of what the post type is.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Menu Position', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Menu Position', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="menu_position" value="<?php if ( isset( $post_type['menu_position'] ) ) echo esc_attr( $post_type['menu_position'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('5 - below Posts; 10 - below Media; 20 - below Pages; 60 - below first separator; 100 - below second separator', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Menu Icon', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Menu Icon', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="menu_icon" value="<?php if ( isset( $post_type['menu_icon'] ) ) echo esc_attr( $post_type['menu_icon'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The url to the icon to be used for this menu.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Display Custom Fields columns', $this->text_domain) ?></h3>
			<table class="form-table supports">
				<tr>
					<th>
						<label><?php esc_html_e('Custom Fields', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('These Custom Fields will be display as columns on the custom post type screen.', $this->text_domain); ?></span></p>
						<?php

						if ( ! empty( $custom_fields ) ) {
							foreach ( $custom_fields as $custom_field ) {
								if ( false !== array_search( $_GET['ct_edit_post_type'], $custom_field['object_type'] ) ) {
									if ( isset( $post_type['cf_columns'][$custom_field['field_id']] ) && 1 == $post_type['cf_columns'][$custom_field['field_id']] )
									$checked = 'checked';
									else
									$checked = '';

									printf( '<label><input type="checkbox" name="cf_columns[%s]" value="1" %s />', esc_attr($custom_field['field_id']), $checked );
									printf( '<span class="description"><strong>%s</strong></span></label><br />', esc_html($custom_field['field_title']) );
								}
							}
						}
						if ( ! isset( $checked ) ) {
							echo '<span class="description"><strong>' . __('This custom post type does not use any Custom Fields', $this->text_domain) . '</strong></span><br />';
						}
						?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ct-wrap-right">
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Public', $this->text_domain) ?></h3>
			<table class="form-table publica">
				<tr>
					<th>
						<label><?php esc_html_e('Public', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Meta argument used to define default values for publicly_queriable, show_ui, show_in_nav_menus and exclude_from_search.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="public" value="1" <?php checked( isset( $post_type['public']) && $post_type['public'] === true); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description">
							<?php esc_html_e('Display a user-interface for this "post_type"', $this->text_domain);?><br /><code>( show_ui = TRUE )</code><br /><br />
							<?php esc_html_e('Show "post_type" for selection in navigation menus', $this->text_domain); ?><br /><code>( show_in_nav_menus = TRUE )</code><br /><br />
							<?php esc_html_e('"post_type" queries can be performed from the front-end', $this->text_domain); ?><br /><code>( publicly_queryable = TRUE )</code><br /><br />
							<?php esc_html_e('Exclude posts with this post type from search results', $this->text_domain); ?><br /><code>( exclude_from_search = FALSE )</code>
						</span>

						<br /><br />
						<label>
							<input type="radio" name="public" value="0" <?php checked( isset( $post_type['public'])  && $post_type['public'] === false); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description">
							<?php esc_html_e('Do not display a user-interface for this "post_type"', $this->text_domain);?><br /><code>( show_ui = FALSE )</code><br /><br />
							<?php esc_html_e('Hide "post_type" for selection in navigation menus', $this->text_domain); ?><br /><code>( show_in_nav_menus = FALSE )</code><br /><br />
							<?php esc_html_e('"post_type" queries cannot be performed from the front-end', $this->text_domain); ?><br /><code>( publicly_queryable = FALSE )</code><br /><br />
							<?php esc_html_e('Exclude posts with this post type from search results', $this->text_domain); ?><br /><code>( exclude_from_search = TRUE )</code>
						</span>

						<br /><br />
						<label>
							<input type="radio" name="public" value="advanced" <?php checked( ! isset($post_type['public']) || ($post_type['public'] !== true && $post_type['public'] !== false )); ?> />
							<span class="description"><strong><?php esc_html_e('Advanced', $this->text_domain); ?></strong></span>
						</label>
						- <span class="description"><?php esc_html_e('You can set each component manually.', $this->text_domain); ?></span>

					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Show UI', $this->text_domain) ?></h3>
			<table class="form-table show-ui">
				<tr>
					<th>
						<label><?php esc_html_e('Show UI', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Whether to generate a default UI for managing this post type. Note that built-in post types, such as post and page, are intentionally set to false.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="show_ui" value="1" <?php checked( isset( $post_type['show_ui'] ) && $post_type['show_ui'] === true); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<span class="description"> - <?php esc_html_e('Display a user-interface (admin panel) for this post type.', $this->text_domain); ?></span>

						<br />
						<label>
							<input type="radio" name="show_ui" value="0" <?php checked( isset( $post_type['show_ui'] ) && $post_type['show_ui'] === false); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<span class="description"> - <?php esc_html_e('Do not display a user-interface for this post type.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Show In Nav Menus ', $this->text_domain) ?></h3>
			<table class="form-table show-in-nav-menus">
				<tr>
					<th>
						<label><?php esc_html_e('Show In Nav Menus', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Whether post_type is available for selection in navigation menus.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="show_in_nav_menus" value="1" <?php checked( isset( $post_type['show_in_nav_menus'] ) && $post_type['show_in_nav_menus'] === true ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="show_in_nav_menus" value="0" <?php checked( isset( $post_type['show_in_nav_menus'] ) && $post_type['show_in_nav_menus'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Publicly Queryable', $this->text_domain) ?></h3>
			<table class="form-table public-queryable">
				<tr>
					<th>
						<label><?php esc_html_e('Publicly Queryable', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Whether post_type queries can be performed from the front end.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="publicly_queryable" value="1" <?php checked( isset( $post_type['publicly_queryable'] ) && $post_type['publicly_queryable'] === true ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="publicly_queryable" value="0" <?php checked( isset( $post_type['publicly_queryable'] ) && $post_type['publicly_queryable'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Exclude From Search', $this->text_domain) ?></h3>
			<table class="form-table exclude-from-search">
				<tr>
					<th>
						<label><?php esc_html_e('Exclude From Search', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Whether to exclude posts with this post type from search results.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="exclude_from_search" value="1" <?php checked(isset( $post_type['exclude_from_search'] ) && $post_type['exclude_from_search'] === true ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="exclude_from_search" value="0" <?php checked(isset( $post_type['exclude_from_search'] ) && $post_type['exclude_from_search'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Hierarchical', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Hierarchical', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Whether the post type is hierarchical. Allows Parent to be specified.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="hierarchical" value="1" <?php checked(isset( $post_type['hierarchical'] ) && $post_type['hierarchical'] === true ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="hierarchical" value="0" <?php checked( isset( $post_type['hierarchical'] ) && $post_type['hierarchical'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Has Archive', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Has Archive', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Enables post type archives. Will use string as archive slug. Will generate the proper rewrite rules if rewrite is enabled.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="has_archive" value="1" <?php checked( isset( $post_type['has_archive'] ) && $post_type['has_archive'] !== false ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="has_archive" value="0" <?php checked( isset( $post_type['has_archive'] ) && $post_type['has_archive'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br /><br />
						<span class="description"><strong><?php esc_html_e('Custom Slug', $this->text_domain); ?></strong></span>
						<br />
						<input type="text" name="has_archive_slug" value="<?php if ( is_string( $post_type['has_archive'] ) ) echo esc_attr( $post_type['has_archive'] ); ?>" />
						<br />
						<span class="description"><?php esc_html_e('Custom slug for post type archive.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Rewrite', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Rewrite', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Rewrite permalinks with this format.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="rewrite" value="1" <?php checked( isset( $post_type['rewrite'] ) && $post_type['rewrite'] !== false ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="rewrite" value="0" <?php checked( isset( $post_type['rewrite'] ) &&  $post_type['rewrite'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br /><br />
						<span class="description"><strong><?php esc_html_e('Custom Slug', $this->text_domain); ?></strong></span>
						<br />
						<input type="text" name="rewrite_slug" value="<?php if ( !empty( $post_type['rewrite']['slug'] ) ) echo esc_attr( $post_type['rewrite']['slug'] ); ?>" />
						<br />
						<span class="description"><?php esc_html_e('Prepend posts with this slug. If empty default will be used.', $this->text_domain); ?></span>
						<br /><br />
						<label>
							<input type="checkbox" name="rewrite_with_front" value="1" <?php checked( ! empty( $post_type['rewrite']['with_front'] )); ?> />
							<span class="description"><strong><?php esc_html_e('Allow Front Base', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php esc_html_e('Allowing permalinks to be prepended with front base.', $this->text_domain); ?></span>
						<br /><br />
						<label>
							<input type="checkbox" name="rewrite_feeds" value="1" <?php checked( ! empty( $post_type['rewrite']['feeds'] ) ); ?> />
							<span class="description"><strong><?php esc_html_e('Feeds', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php esc_html_e('Default will use has_archive.', $this->text_domain); ?></span>
						<br />
						<label>
							<input type="checkbox" name="rewrite_pages" value="1" <?php checked( ! empty( $post_type['rewrite']['pages'] ) ); ?> />
							<span class="description"><strong><?php esc_html_e('Pages', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php esc_html_e('Defaults to true.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('EP Mask', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('EP Mask', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php esc_html_e('Endpoint mask describing the places the endpoint should be added.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
							<input type="hidden" name="ep_mask[EP_NONE]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_NONE]" value="<?php echo EP_NONE; ?>" <?php checked( ! (bool) $post_type['rewrite']['ep_mask']); ?> />
							<span class="description"><strong><?php esc_html_e('EP_NONE: for default, nothing.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_PERMALINK]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_PERMALINK]" value="<?php echo EP_PERMALINK; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_PERMALINK, EP_PERMALINK); ?> />
							<span class="description"><strong><?php esc_html_e('EP_PERMALINK: for Permalink.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_ATTACHMENT]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_ATTACHMENT]" value="<?php echo EP_ATTACHMENT; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_ATTACHMENT, EP_ATTACHMENT); ?> />
							<span class="description"><strong><?php esc_html_e('EP_ATTACHMENT: for Attachment.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_DATE]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_DATE]" value="<?php echo EP_DATE; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_DATE, EP_DATE); ?> />
							<span class="description"><strong><?php esc_html_e('EP_DATE: for Date.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_YEAR]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_YEAR]" value="<?php echo EP_YEAR; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_YEAR, EP_YEAR); ?> />
							<span class="description"><strong><?php esc_html_e('EP_YEAR: for Year.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_MONTH]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_MONTH]" value="<?php echo EP_MONTH; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_MONTH, EP_MONTH); ?> />
							<span class="description"><strong><?php esc_html_e('EP_MONTH: for Month.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_DAY]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_DAY]" value="<?php echo EP_DAY; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_DAY, EP_DAY); ?> />
							<span class="description"><strong><?php esc_html_e('EP_DAY: for Day.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_ROOT]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_ROOT]" value="<?php echo EP_ROOT; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_ROOT, EP_ROOT); ?> />
							<span class="description"><strong><?php esc_html_e('EP_ROOT: for Root.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_COMMENTS]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_COMMENTS]" value="<?php echo EP_COMMENTS; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_COMMENTS, EP_COMMENTS); ?> />
							<span class="description"><strong><?php esc_html_e('EP_COMMENTS: for Comments.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_SEARCH]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_SEARCH]" value="<?php echo EP_SEARCH; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_SEARCH, EP_SEARCH); ?> />
							<span class="description"><strong><?php esc_html_e('EP_SEARCH: for Search.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_CATEGORIES]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_CATEGORIES]" value="<?php echo EP_CATEGORIES; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_CATEGORIES, EP_CATEGORIES); ?> />
							<span class="description"><strong><?php esc_html_e('EP_CATEGORIES: for Categories.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_TAGS]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EPEP_TAGS_DAY]" value="<?php echo EP_TAGS; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_TAGS, EP_TAGS); ?> />
							<span class="description"><strong><?php esc_html_e('EP_TAGS: for Tags.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_AUTHORS]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_AUTHORS]" value="<?php echo EP_AUTHORS; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_AUTHORS, EP_AUTHORS); ?> />
							<span class="description"><strong><?php esc_html_e('EP_AUTHORS: for Authors.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_PAGES]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_PAGES]" value="<?php echo EP_PAGES; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_PAGES, EP_PAGES); ?> />
							<span class="description"><strong><?php esc_html_e('EP_PAGES: for Pages.', $this->text_domain); ?></strong></span>
						</label>
						<br />
							<input type="hidden" name="ep_mask[EP_ALL]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_ALL]" value="<?php echo EP_ALL; ?>" <?php checked( $post_type['rewrite']['ep_mask'] & EP_ALL, EP_ALL ); ?> />
							<span class="description"><strong><?php esc_html_e('EP_ALL: for everything.', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Query var', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Query var', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Name of the query var to use for this post type. Defaults to the post type name.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="query_var" value="1" <?php checked( isset($post_type['query_var']) && $post_type['query_var'] !== false); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="query_var" value="0" <?php checked(isset($post_type['query_var']) && $post_type['query_var'] === false); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br /><br />
						<span class="description"><strong><?php esc_html_e('Custom Query Key', $this->text_domain); ?></strong></span>
						<br />
						<input type="text" name="query_var_key" value="<?php if ( is_string( $post_type['query_var'] ) ) echo esc_attr( $post_type['query_var'] ); ?>" />
						<br />
						<span class="description"><?php esc_html_e('Custom query var key.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Can Export', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Can Export', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php esc_html_e('Can this post_type be exported.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="can_export" value="1" <?php checked( !isset($post_type['can_export']) || $post_type['can_export'] === true); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="can_export" value="0" <?php checked(isset($post_type['can_export']) && $post_type['can_export'] === false); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>

	</div>
	<p class="submit ct-clear">
		<?php wp_nonce_field('submit_post_type'); ?>
		<input type="submit" class="button-primary" name="submit" value="Save Changes" />
	</p>
	<br /><br /><br /><br />
</form>