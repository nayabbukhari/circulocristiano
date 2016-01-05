<?php
	/**
	 * The template for the header sticky bar.
	 *
	 * Override this template by specifying the path where it is stored (templates_path) in your SeedRedux config.
	 *
	 * @author 		SeedRedux Framework
	 * @package 	SeedReduxFramework/Templates
	 * @version     3.4.3
	 */
?>
<div id="seedredux-sticky">
	<div id="info_bar">

		<a href="javascript:void(0);"
		   class="expand_options<?php echo ( $this->parent->args['open_expanded'] ) ? ' expanded' : ''; ?>"<?php echo $this->parent->args['hide_expand'] ? ' style="display: none;"' : '' ?>><?php _e( 'Expand', 'seedredux-framework' ); ?></a>

		<div class="seedredux-action_bar">
			<span class="spinner"></span>
			<?php submit_button( __( 'Save Changes', 'seedredux-framework' ), 'primary', 'seedredux_save', false  ); ?>
			<?php if ( false === $this->parent->args['hide_reset'] ) : ?>
				<?php submit_button( __( 'Reset Section', 'seedredux-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults-section]', false ); ?>
				<?php submit_button( __( 'Reset All', 'seedredux-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults]', false ); ?>
			<?php endif; ?>
		</div>
		<div class="seedredux-ajax-loading" alt="<?php _e( 'Working...', 'seedredux-framework' ) ?>">&nbsp;</div>
		<div class="clear"></div>
	</div>

	<!-- Notification bar -->
	<div id="seedredux_notification_bar">
		<?php $this->notification_bar(); ?>
	</div>


</div>