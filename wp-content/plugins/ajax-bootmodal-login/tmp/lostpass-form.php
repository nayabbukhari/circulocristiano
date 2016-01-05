<form id="passform" action="lostpassword" method="post">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&#215;</button>
    <h3><?php _e('User LostPassword','alimir'); ?></h3>
  </div> 
  <div class="modal-body">
  <p class="alert" data-alert="alert"><?php _e('Please enter your username or email address. You will receive a link to create a new password via email.'); ?></p>
  	<div class="status"></div>
    <div class="row-fluid control-group">
		<label><?php print_placeholder_or_label(__('Username or Email','alimir'),'lable'); ?>
        <input type="text" name="lost_pass" id="lost_pass" class="span12" value="" <?php print_placeholder_or_label(__('Username or Email','alimir'),'placeholder'); ?>/></label>
		<?php if (get_option( 'enable_lostpass_captcha' ) == 1):?>
		<label><?php print_placeholder_or_label(__('Captcha','alimir'),'lable'); ?>
		<img style="margin:5px auto;display:block;" alt="captcha" id="lostpass_captcha_img" data-toggle="tooltip" title="<?php _e('Click to refresh captcha','alimir'); ?>" src="<?php echo plugins_url( 'captcha/lost-captcha.php' , __FILE__ );?>" />
        <input type="text" name="lostpass_captcha" id="lostpass_captcha" class="span12" value="" <?php print_placeholder_or_label(__('Captcha','alimir'),'placeholder'); ?>/></label>
		<?php endif; ?>
		<span class="label label-info"><a href="#login_tab" data-toggle="tab"><?php _e('Log in','alimir'); ?></a></span>
    </div>
  </div>
  <div class="modal-footer">
        <button type="submit" name="user-sumbit" id="user-submit" class="btn <?php echo default_buttons(); ?> <?php echo default_sizes(); ?> btn-block" data-loading-text="<?php _e('loading...','alimir'); ?>"><?php _e('Submit','alimir'); ?></button>
		<input type="hidden" name="forgotten" value="true" />
  </div>
   <?php wp_nonce_field( 'ajax-form-nonce', 'security3' ); ?>
</form>