<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<div class="wrap">

	<?php $this->render_admin( 'message' ); ?>

	<h3><?php esc_html_e( 'CustomPress Shortcodes', $this->text_domain ); ?></h3>


	<div class="postbox">
		<h3 class='hndle'><span><?php esc_html_e( 'CustomPress Shortcodes', $this->text_domain ) ?></span></h3>
		<div class="inside">
			<table class="form-table">
				<tr>
					<th>
						<?php esc_html_e('Custom Fields Input', $this->text_domain); ?>
					</th>
					<td>
						<p><?php esc_html_e('Used to embed the input fields of a set of custom fields for the post "ID" specifed. Must be used inside a &lt;form&gt; and after submit the receiving form code must call the global:', $this->text_domain); ?>
							<br /><code>global $CustomPress_Core;<br />$CustomPress_Core->save_custom_fields( $post_id );</code><br />
							<?php esc_html_e('to save the input back to the post.', $this->text_domain); ?>
						</p>
						<div class="embed-code-wrap">
							<?php esc_html_e('Basic shortcode', $this->text_domain); ?>
							<br /><code>[custom_fields_input post_id="post_id"]</code>
							<br /><span class="description"><?php esc_html_e('Returns a full set of input fields based on the post type of the post id provided.', $this->text_domain); ?></span>
							<br /><span class="description"><?php esc_html_e('if the post_id is left out, it assumes the current global $post as the post being operated on.', $this->text_domain); ?></span>

							<br /><br /><?php esc_html_e('or with field list', $this->text_domain); ?><br />
							<code>[custom_fields_input post_id="post_id"] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/custom_fields_input]</code>
							<br /><span class="description"><?php esc_html_e('Returns a set of input fields as supplied by the field id list in the shortcode. Any ids not associate with the post type will be ignored.', $this->text_domain); ?></span>

							<br /><br /><?php esc_html_e('or with field list filtered by category', $this->text_domain);?><br />
							<code>[custom_fields_input post_id="post_id"] [ct_filter terms="cat1, cat2,.."] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/ct_filter] [/custom_fields_input]</code>
							<br /><span class="description"><?php esc_html_e('Multiple filters may be used in one input block.', $this->text_domain); ?></span>

							<br /><br /><?php esc_html_e('or use [ct_in] for individual input fields to allow better postioning and styling.', $this->text_domain); ?><br />
							<code>[ct_in id="_ct_selectbox_4cf582bd61fa4" property="title | description | input" required="true | false" ]</code>
							<br /><span class="description"><?php esc_html_e('Leaving off the property attribute defaults to the input field html.', $this->text_domain); ?></span>
							<br /><span class="description"><?php esc_html_e('Leaving off the required attribute defaults to setting in custom fields settings. Otherwise override it.', $this->text_domain); ?></span>
							<br /><span class="description"><?php esc_html_e('Assumes the current global $post as the post being operated on.', $this->text_domain); ?></span>
							<br /><span class="description"><?php esc_html_e('The nomenclature "title | description | input" means use one of the choices. "title" or "description or "input".', $this->text_domain); ?></span>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e('Custom Fields Validate', $this->text_domain); ?>
					</th>
					<td>
						<p>
							<?php esc_html_e('When using the individual [ct_in] custom fields, this shortcode collects and adds the necessary script to run validation on the fields. ', $this->text_domain); ?>
							<?php esc_html_e('The [custom_fields_input] shortcode automatically includes this tag with the group of fields. It is only necessary for [ct_in] fields.', $this->text_domain); ?>
						</p>
						<div class="embed-code-wrap">
							<code>[ct_validate]</code>
							<br /><span class="description"><?php esc_html_e('Should be placed just before the closing &lt;/form&gt; tag of the form containg the fields to be validated.', $this->text_domain); ?></span>
						</div>
					</td>
				</tr>

				<tr>
					<th>
						<?php esc_html_e('Custom Fields Block', $this->text_domain); ?>
					</th>
					<td>
						<div>
							<p><?php esc_html_e('Used to embed the output of a set of custom fields for the current post. Must be used inside the loop.', $this->text_domain); ?>
							</p>
							<div class="embed-code-wrap">
								<?php esc_html_e('Basic shortcode', $this->text_domain); ?>
								<br /><code>[custom_fields_block]</code>
								<br /><span class="description"><?php esc_html_e('Returns a full set of input fields based on the post type of the post id provided.', $this->text_domain); ?></span>

								<br /><br /><?php esc_html_e('or with field list', $this->text_domain); ?><br />
								<code>[custom_fields_block] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/custom_fields_block]</code>
								<br /><span class="description"><?php esc_html_e('Returns a set of input fields as supplied by the field id list in the shortcode. Any ids not associate with the post type will be ignored.', $this->text_domain); ?></span>

								<br /><br /><?php esc_html_e('or with field list filtered by category', $this->text_domain);?>
								<br /><code>[custom_fields_block] [ct_filter terms="cat1, cat2,.."] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/ct_filter] [/custom_fields_block]</code>
								<br /><span class="description"><?php esc_html_e('Multiple filters may be used in one input block.', $this->text_domain); ?></span>
							</div>
							<p>
								<strong><?php esc_html_e('Attributes for the [custom_fields_block]', $this->text_domain); ?></strong>
								<br /><span class="description"><?php esc_html_e( 'wrap        = Wrap the fields in either a "table", a "ul" or a "div" structure.', $this->text_domain ) ?></span>
								<br /><strong><?php esc_html_e( 'The default wrap attributes may be overriden using the following individual attributes:', $this->text_domain); ?></strong>
								<br /><span class="description"><?php esc_html_e( 'open = HTML to begin the block with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php esc_html_e( 'close = HTML to end the block with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php esc_html_e( 'open_line = HTML to begin a line with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php esc_html_e( 'close_line = HTML to end a line with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php esc_html_e( 'open_title = HTML to begin the title with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php esc_html_e( 'close_title = HTML to end the title with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php esc_html_e( 'open_value = HTML to begin the value with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php esc_html_e( 'close_value = HTML to end the value with', $this->text_domain ) ?></span>
							</p>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e('Custom Field Filter', $this->text_domain); ?>
					</th>
					<td>
						<p><?php esc_html_e('Used to restrict the list of fields returned depending on the categories of the post. Multiple [ct_filter] shortcodes may be added to a [custom_field_input] or [custom_field_block] shortcode.', $this->text_domain); ?></p>
						<code>[ct_filter terms="cat1, cat2,.." not="true | false"] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/ct_filter]</code>
						<br /><strong><?php esc_html_e('Attributes for the [custom_field_filter]', $this->text_domain); ?></strong>
						<br /><span class="description"><?php esc_html_e('terms= Comma separated category list to filter on. Categories not associated with the post type of the current post will be ignored.', $this->text_domain); ?></span>
						<br /><span class="description"><?php esc_html_e('not= If true the filter will be inverted returning all the fields that were Not selected. Defaults to false.', $this->text_domain); ?></span>
						<br /><span class="description"><?php esc_html_e('The comma separated list of fields between the opening and closing tags will be returned if the categories match. Fields not associated with the post type of the current post will be ignored.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

</div>