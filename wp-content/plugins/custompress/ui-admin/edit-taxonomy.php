<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$post_types = get_post_types('','names');

if(is_network_admin()){
	$taxonomy = $this->network_taxonomies[$_GET['ct_edit_taxonomy']]['args'];
} else {
	$taxonomy = isset( $this->taxonomies[$_GET['ct_edit_taxonomy']]['args'] ) ? $this->taxonomies[$_GET['ct_edit_taxonomy']]['args'] : array();
}

if( !isset( $taxonomy['rewrite']['ep_mask'] ) ) {
	$taxonomy['rewrite']['ep_mask'] = EP_PERMALINK;
}

//var_dump($this->taxonomies);

global $wp_post_types, $wp_taxonomies;

//var_dump($wp_post_types);


?>

<h3><?php esc_html_e('Edit Taxonomy', $this->text_domain); ?></h3>
<form action="#" method="post" class="ct-taxonomy">
	<?php wp_nonce_field( 'ct_submit_taxonomy_verify', 'ct_submit_taxonomy_secret' ); ?>
	<div class="ct-wrap-left">
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Taxonomy', $this->text_domain) ?></h3>
			<table class="form-table <?php do_action('ct_invalid_field_taxonomy_class'); ?>">
				<tr>
					<th>
						<label><?php esc_html_e('Taxonomy', $this->text_domain) ?> (<span class="ct-required"> required </span>)</label>
					</th>
					<td>
						<input type="text" value="<?php echo esc_attr( $_GET['ct_edit_taxonomy'] ); ?>" disabled="disabled">
						<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $_GET['ct_edit_taxonomy'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The system name of the taxonomy. Alphanumeric lower case characters and underscores only. Min 2 letters. Once added the taxonomy system name cannot be changed.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Post Type', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php esc_html_e('Post Type', $this->text_domain) ?> (<span class="ct-required"> <?php esc_html_e( 'required', $this->text_domain ); ?> </span>)</label>
					</th>
					<td>
						<select name="object_type[]" multiple="multiple" class="ct-object-type">
							<?php if ( !empty( $post_types ) ): ?>
							<?php foreach( $post_types as $post_type ): ?>
							<option value="<?php echo esc_attr( $post_type ); ?>" <?php selected( is_object_in_taxonomy( $post_type, $_GET['ct_edit_taxonomy'] )); ?> ><?php echo esc_html( $post_type ); ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<br /><span class="description"><?php esc_html_e('Select one or more post types to add this taxonomy to', $this->text_domain); ?></span>
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
						<input type="text" name="labels[name]" value="<?php if ( isset( $taxonomy['labels']['name'] ) ) echo esc_attr( $taxonomy['labels']['name'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('General name for the taxonomy, usually plural.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Singular Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[singular_name]" value="<?php if ( isset( $taxonomy['labels']['singular_name'] ) ) echo esc_attr( $taxonomy['labels']['singular_name'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('Name for one object of this taxonomy. Defaults to value of name.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Add New Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[add_new_item]" value="<?php if ( isset( $taxonomy['labels']['add_new_item'] ) ) echo esc_attr( $taxonomy['labels']['add_new_item'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The add new item text. Default is "Add New Tag" or "Add New Category".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('New Item Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[new_item_name]" value="<?php if ( isset( $taxonomy['labels']['new_item_name'] ) ) echo esc_attr( $taxonomy['labels']['new_item_name'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The new item name text. Default is "New Tag Name" or "New Category Name".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Edit Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[edit_item]" value="<?php if ( isset( $taxonomy['labels']['edit_item'] ) ) echo esc_attr( $taxonomy['labels']['edit_item'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The edit item text. Default is "Edit Tag" or "Edit Category".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Update Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[update_item]" value="<?php if ( isset( $taxonomy['labels']['update_item'] ) ) echo esc_attr( $taxonomy['labels']['update_item'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The update item text. Default is "Update Tag" or "Update Category".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Search Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[search_items]" value="<?php if ( isset( $taxonomy['labels']['search_items'] ) ) echo esc_attr( $taxonomy['labels']['search_items'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The search items text. Default is "Search Tags" or "Search Categories".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Popular Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[popular_items]" value="<?php if ( isset( $taxonomy['labels']['popular_items']  ) ) echo esc_attr( $taxonomy['labels']['popular_items'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The popular items text. Default is "Popular Tags" or null.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('All Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[all_items]" value="<?php if ( isset( $taxonomy['labels']['all_items'] ) ) echo esc_attr( $taxonomy['labels']['all_items'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The all items text. Default is "All Tags" or "All Categories".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Parent Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[parent_item]" value="<?php if ( isset( $taxonomy['labels']['parent_item'] ) ) echo esc_attr( $taxonomy['labels']['parent_item'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The parent item text. This string is not used on non-hierarchical taxonomies such as post tags. Default is null or "Parent Category".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Parent Item Colon', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[parent_item_colon]" value="<?php if ( isset( $taxonomy['labels']['parent_item_colon'] ) ) echo esc_attr( $taxonomy['labels']['parent_item_colon'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The same as parent_item, but with colon : in the end null, "Parent Category:".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Add Or Remove Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[add_or_remove_items]" value="<?php if ( isset( $taxonomy['labels']['add_or_remove_items'] ) ) echo esc_attr( $taxonomy['labels']['add_or_remove_items'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The add or remove items text is used in the meta box when JavaScript is disabled. This string isn\'t used on hierarchical taxonomies. Default is "Add or remove tags" or null.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Separate Items With Commas', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[separate_items_with_commas]" value="<?php if ( isset( $taxonomy['labels']['separate_items_with_commas'] ) ) echo esc_attr( $taxonomy['labels']['separate_items_with_commas'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The separate item with commas text used in the taxonomy meta box. This string isn\'t used on hierarchical taxonomies. Default is "Separate tags with commas", or null.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Choose From Most Used', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[choose_from_most_used]" value="<?php if ( isset( $taxonomy['labels']['choose_from_most_used'] ) ) echo esc_attr( $taxonomy['labels']['choose_from_most_used'] ); ?>" />
						<br /><span class="description"><?php esc_html_e('The choose from most used text used in the taxonomy meta box. This string isn\'t used on hierarchical taxonomies. Default is "Choose from the most used tags" or null.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Show Admin Column', $this->text_domain) ?></h3>
			<table class="form-table show_admin_column">
				<tr>
					<th>
						<label><?php esc_html_e('Show Admin Column', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php esc_html_e('Whether to allow automatic creation of taxonomy columns on associated post-types.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="show_admin_column" value="1" <?php checked( isset( $taxonomy['show_admin_column'] ) && $taxonomy['show_admin_column'] === true ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="show_admin_column" value="0" <?php checked( isset( $taxonomy['show_admin_column'] ) && $taxonomy['show_admin_column'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
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
						<span class="description"><?php esc_html_e('Should this taxonomy be exposed in the admin UI.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="public" value="1" <?php checked( isset( $taxonomy['public']) && $taxonomy['public'] === true); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="public" value="0" <?php checked( isset( $taxonomy['public']) && $taxonomy['public'] === false); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="public" value="advanced" <?php checked( isset( $taxonomy['public']) && $taxonomy['public'] !== true && $taxonomy['public'] !== false ); ?> />
							<span class="description"><strong><?php esc_html_e('ADVANCED', $this->text_domain); ?></strong></span>
						</label>
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
						<span class="description"><?php esc_html_e('Whether to generate a default UI for managing this taxonomy.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="show_ui" value="1" <?php checked( isset( $taxonomy['show_ui'] ) && $taxonomy['show_ui'] === true); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="show_ui" value="0" <?php checked( isset( $taxonomy['show_ui'] ) && $taxonomy['show_ui'] === false); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php esc_html_e('Show Tagcloud', $this->text_domain) ?></h3>
			<table class="form-table show_tagcloud">
				<tr>
					<th>
						<label><?php esc_html_e('Show Tagcloud', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php esc_html_e('Whether to show a tag cloud in the admin UI for this taxonomy.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="show_tagcloud" value="1" <?php checked( isset( $taxonomy['show_tagcloud'] ) && $taxonomy['show_tagcloud'] === true ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="show_tagcloud" value="0" <?php checked( isset( $taxonomy['show_tagcloud'] ) && $taxonomy['show_tagcloud'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
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
						<span class="description"><?php esc_html_e('Whether to make this taxonomy available for selection in navigation menus.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="show_in_nav_menus" value="1" <?php checked( isset( $taxonomy['show_in_nav_menus'] ) && $taxonomy['show_in_nav_menus'] === true ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="show_in_nav_menus" value="0" <?php checked( isset( $taxonomy['show_in_nav_menus'] ) && $taxonomy['show_in_nav_menus'] === false ); ?> />
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
						<span class="description"><?php esc_html_e('Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="hierarchical" value="1" <?php checked(isset( $taxonomy['hierarchical'] ) && $taxonomy['hierarchical'] === true ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="hierarchical" value="0" <?php checked( isset( $taxonomy['hierarchical'] ) && $taxonomy['hierarchical'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
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
						<span class="description"><?php esc_html_e('Set to false to prevent rewrite, or true to customize.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="rewrite" value="1" <?php checked( isset( $taxonomy['rewrite'] ) && $taxonomy['rewrite'] !== false ); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php esc_html_e('Default will use query var.', $this->text_domain); ?></span>
						<br />
						<label>
							<input type="radio" name="rewrite" value="0" <?php checked( isset( $taxonomy['rewrite'] ) &&  $taxonomy['rewrite'] === false ); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php esc_html_e('Prevent rewrite.', $this->text_domain); ?></span>
						<br /><br />

						<span class="description"><strong><?php esc_html_e('Custom Slug', $this->text_domain); ?></strong></span>
						<br />
						<input type="text" name="rewrite_slug" value="<?php if ( !empty( $taxonomy['rewrite']['slug'] ) ) echo esc_attr( $taxonomy['rewrite']['slug'] ); ?>" />
						<br />
						<span class="description"><?php esc_html_e('Prepend posts with this slug. If empty default will be used.', $this->text_domain); ?></span>
						<br /><br />
						<label>
							<input type="checkbox" name="rewrite_with_front" value="1" <?php checked( ! isset($taxonomy['rewrite']['with_front']) || ! empty( $taxonomy['rewrite']['with_front'] )); ?> />
							<span class="description"><strong><?php esc_html_e('Allow Front Base', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php esc_html_e('Allowing permalinks to be prepended with front base.', $this->text_domain); ?></span>
						<br /><br />
						<label>
							<input type="checkbox" name="rewrite_hierarchical" value="1" <?php checked( !empty( $taxonomy['rewrite']['hierarchical'] ) ); ?> />
							<span class="description"><strong><?php esc_html_e('Hierarchical URLs', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php esc_html_e('Allow hierarchical urls. Applicable for hierarchical taxonomies.', $this->text_domain); ?></span>
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
						<?php $taxonomy['rewrite']['ep_mask'] = empty($taxonomy['rewrite']['ep_mask']) ? 0 : $taxonomy['rewrite']['ep_mask']; ?>
						<input type="hidden" name="ep_mask[EP_NONE]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_NONE]" value="<?php echo EP_NONE; ?>" <?php checked( empty($taxonomy['rewrite']['ep_mask']) ); ?> />
							<span class="description"><strong><?php esc_html_e('EP_NONE: for default, nothing.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_PERMALINK]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_PERMALINK]" value="<?php echo EP_PERMALINK; ?>" <?php checked( ($taxonomy['rewrite']['ep_mask'] & EP_PERMALINK), EP_PERMALINK) ; ?> />
							<span class="description"><strong><?php esc_html_e('EP_PERMALINK: for Permalink.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_ATTACHMENT]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_ATTACHMENT]" value="<?php echo EP_ATTACHMENT; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_ATTACHMENT, EP_ATTACHMENT); ?> />
							<span class="description"><strong><?php esc_html_e('EP_ATTACHMENT: for Attachment.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_DATE]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_DATE]" value="<?php echo EP_DATE; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_DATE, EP_DATE); ?> />
							<span class="description"><strong><?php esc_html_e('EP_DATE: for Date.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_YEAR]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_YEAR]" value="<?php echo EP_YEAR; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_YEAR, EP_YEAR); ?> />
							<span class="description"><strong><?php esc_html_e('EP_YEAR: for Year.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_MONTH]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_MONTH]" value="<?php echo EP_MONTH; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_MONTH, EP_MONTH); ?> />
							<span class="description"><strong><?php esc_html_e('EP_MONTH: for Month.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_DAY]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_DAY]" value="<?php echo EP_DAY; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_DAY, EP_DAY); ?> />
							<span class="description"><strong><?php esc_html_e('EP_DAY: for Day.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_ROOT]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_ROOT]" value="<?php echo EP_ROOT; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_ROOT, EP_ROOT); ?> />
							<span class="description"><strong><?php esc_html_e('EP_ROOT: for Root.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_COMMENTS]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_COMMENTS]" value="<?php echo EP_COMMENTS; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_COMMENTS, EP_COMMENTS); ?> />
							<span class="description"><strong><?php esc_html_e('EP_COMMENTS: for Comments.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_SEARCH]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_SEARCH]" value="<?php echo EP_SEARCH; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_SEARCH, EP_SEARCH); ?> />
							<span class="description"><strong><?php esc_html_e('EP_SEARCH: for Search.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_CATEGORIES]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_CATEGORIES]" value="<?php echo EP_CATEGORIES; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_CATEGORIES, EP_CATEGORIES); ?> />
							<span class="description"><strong><?php esc_html_e('EP_CATEGORIES: for Categories.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_TAGS]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EPEP_TAGS_DAY]" value="<?php echo EP_TAGS; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_TAGS, EP_TAGS); ?> />
							<span class="description"><strong><?php esc_html_e('EP_TAGS: for Tags.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_AUTHORS]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_AUTHORS]" value="<?php echo EP_AUTHORS; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_AUTHORS, EP_AUTHORS); ?> />
							<span class="description"><strong><?php esc_html_e('EP_AUTHORS: for Authors.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_PAGES]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_PAGES]" value="<?php echo EP_PAGES; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_PAGES, EP_PAGES); ?> />
							<span class="description"><strong><?php esc_html_e('EP_PAGES: for Pages.', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<input type="hidden" name="ep_mask[EP_ALL]" value="0" />
						<label>
							<input type="checkbox" name="ep_mask[EP_ALL]" value="<?php echo EP_ALL; ?>" <?php checked( $taxonomy['rewrite']['ep_mask'] & EP_ALL, EP_ALL ); ?> />
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
						<p><span class="description"><?php esc_html_e('Name of the query var to use for this post type. False to prevent queries, or string to customize query var. Defaults to the post type name.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="query_var" value="1" <?php checked( isset($taxonomy['query_var']) && $taxonomy['query_var'] !== false); ?> />
							<span class="description"><strong><?php esc_html_e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="query_var" value="0" <?php checked(isset($taxonomy['query_var']) && $taxonomy['query_var'] === false); ?> />
							<span class="description"><strong><?php esc_html_e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br /><br />
						<span class="description"><strong><?php esc_html_e('Custom Query Key', $this->text_domain); ?></strong></span>
						<br />
						<input type="text" name="query_var_key" value="<?php if ( is_string( $taxonomy['query_var'] ) ) echo esc_attr( $taxonomy['query_var'] ); ?>" />
						<br />
						<span class="description"><?php esc_html_e('Custom query var key.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="clear"></div>
	<p class="submit">
		<?php wp_nonce_field('submit_taxonomy'); ?>
		<input type="submit" class="button-primary" name="submit" value="Save Changes" />
	</p>
	<br /><br /><br /><br />
</form>