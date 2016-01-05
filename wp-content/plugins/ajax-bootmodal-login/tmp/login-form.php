<form id="login" action="login" method="post">
  <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&#215;</button>
    <h3><?php _e('User Login','alimir'); ?></h3>
  </div> 
  <div class="modal-body">
  	<div class="status"></div>
    <div class="row-fluid control-group">
		<label><?php print_placeholder_or_label(__('Username','alimir'),'lable'); ?>
        <input type="text" name="username" id="username" class="span12" value="" <?php print_placeholder_or_label(__('Username','alimir'),'placeholder'); ?>/></label>
		<label><?php print_placeholder_or_label( __('Password','alimir'),'lable'); ?>
        <input type="password" name="password" id="password" class="span12" value="" <?php print_placeholder_or_label( __('Password','alimir'),'placeholder'); ?>/></label>
		<?php if (get_option( 'enable_login_captcha' ) == 1):?>
		<label><?php print_placeholder_or_label(__('Captcha','alimir'),'lable'); ?>
		<img style="margin:5px auto;display:block;" alt="captcha" id="login_captcha_img" data-toggle="tooltip" title="<?php _e('Click to refresh captcha','alimir'); ?>" src="<?php echo plugins_url( 'captcha/log-captcha.php' , __FILE__ );?>" />
        <input type="text" name="login_captcha" id="login_captcha" class="span12" value="" <?php print_placeholder_or_label(__('Captcha','alimir'),'placeholder'); ?>/></label>
		<?php endif; ?>
		<label class="checkbox">
		<input name="rememberme" type="checkbox" id="rememberme" value="forever"><?php _e('Remember Me','alimir'); ?>
		</label>
		<?php if (get_option( 'can_register_option' ) != 1): ?>
		<span class="label label-important"><a href="#register_tab" data-toggle="tab"><?php _e('Not registered?','alimir'); ?></a></span>
		<?php endif; ?>
		<span class="label label-info"><a href="#lostpass_tab" data-toggle="tab"><?php _e('Lost your password?','alimir'); ?></a></span>	
    </div>
  </div>
  <div class="modal-footer">
        <button type="submit" name="submit" id="wp-submit" class="btn <?php echo default_buttons(); ?> <?php echo default_sizes(); ?> btn-block" data-loading-text="<?php _e('loading...','alimir'); ?>"><?php _e('Submit','alimir'); ?></button>
  </div>
   <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
</form>