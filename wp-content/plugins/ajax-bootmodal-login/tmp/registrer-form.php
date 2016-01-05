<form id="regform" action="register" method="post">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&#215;</button>
    <h3><?php _e('User Registration','alimir'); ?></h3>
  </div> 
  <div class="modal-body">
  	<div class="status"></div>
    <div class="row-fluid control-group">
		<label><?php print_placeholder_or_label(__('Username','alimir'),'lable'); ?>
        <input type="text" name="user_login" id="user_login" class="span12" value="" <?php print_placeholder_or_label(__('Username','alimir'),'placeholder'); ?>/></label>
		<label><?php print_placeholder_or_label(__('E-mail','alimir'),'lable'); ?>
        <input type="text" name="user_email" id="user_email" class="span12" value="" <?php print_placeholder_or_label(__('E-mail','alimir'),'placeholder'); ?>/></label>
		<?php if (get_option( 'enable_register_captcha' ) == 1):?>
		<label><?php print_placeholder_or_label(__('Captcha','alimir'),'lable'); ?>
		<img style="margin:5px auto;display:block;" alt="captcha" id="register_captcha_img" data-toggle="tooltip" title="<?php _e('Click to refresh captcha','alimir'); ?>" src="<?php echo plugins_url( 'captcha/reg-captcha.php' , __FILE__ );?>" />
        <input type="text" name="register_captcha" id="register_captcha" class="span12" value="" <?php print_placeholder_or_label(__('Captcha','alimir'),'placeholder'); ?>/></label>
		<?php endif; ?>
		<span class="label label-info"><a href="#login_tab" data-toggle="tab"><?php _e('Already registered? Login','alimir'); ?></a></span>
		<span class="label"><?php echo _e('A password will be emailed to you.','alimir') ?></span>
    </div>
  </div>
  <div class="modal-footer">
        <button type="submit" name="pass-sumbit" id="pass-submit" class="btn <?php echo default_buttons(); ?> <?php echo default_sizes(); ?> btn-block" data-loading-text="<?php _e('loading...','alimir'); ?>"><?php _e('Submit','alimir'); ?></button>
		<input type="hidden" name="register" value="true" />
  </div>
   <?php wp_nonce_field( 'ajax-form-nonce', 'security2' ); ?>
</form>