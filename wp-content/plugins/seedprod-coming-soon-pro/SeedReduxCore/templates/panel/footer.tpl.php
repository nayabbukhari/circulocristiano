<?php
	/**
	 * The template for the panel footer area.
	 *
	 * Override this template by specifying the path where it is stored (templates_path) in your SeedRedux config.
	 *
	 * @author 		SeedRedux Framework
	 * @package 	SeedReduxFramework/Templates
	 * @version     3.4.3
	 */
?>
<div id="seedredux-sticky-padder" style="display: none;">&nbsp;</div>
<div id="seedredux-footer-sticky">
	<div id="seedredux-footer">

		<?php if ( isset( $this->parent->args['share_icons'] ) ) : ?>
			<div id="seedredux-share">
				<?php foreach ( $this->parent->args['share_icons'] as $link ) : ?>
					<?php
					// SHIM, use URL now
					if ( isset( $link['link'] ) && ! empty( $link['link'] ) ) {
						$link['url'] = $link['link'];
						unset( $link['link'] );
					}
					?>

					<a href="<?php echo $link['url'] ?>" title="<?php echo $link['title']; ?>" target="_blank">

						<?php if ( isset( $link['icon'] ) && ! empty( $link['icon'] ) ) : ?>
							<i class="<?php echo $link['icon'] ?>"></i>
						<?php else : ?>
							<img src="<?php echo $link['img'] ?>"/>
						<?php endif; ?>

					</a>
				<?php endforeach; ?>

			</div>
		<?php endif; ?>

		<div class="seedredux-action_bar">
			<span class="spinner"></span>
			<?php submit_button( __( 'Save Changes', 'seedredux-framework' ), 'primary', 'seedredux_save', false ); ?>

			<?php if ( false === $this->parent->args['hide_reset'] ) : ?>
				<?php submit_button( __( 'Reset Section', 'seedredux-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults-section]', false ); ?>
				<?php submit_button( __( 'Reset All', 'seedredux-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults]', false ); ?>
			<?php endif; ?>

		</div>

		<div class="seedredux-ajax-loading" alt="<?php _e( 'Working...', 'seedredux-framework' ) ?>">&nbsp;</div>
		<div class="clear"></div>

	</div>
</div>
