<?php
add_action('admin_menu', 'alimir_bootModal_create_menu');

function alimir_bootModal_create_menu() {
	add_options_page(__( 'BootModal Login Settings', 'alimir' ),__( 'BootModal Login Settings', 'alimir' ), 'administrator', __FILE__, 'alimir_bootModal_settings_page', __FILE__);
	add_action( 'admin_init', 'alimir_bootModal_register_mysettings' );
}


function alimir_bootModal_register_mysettings() {
	register_setting( 'alimir-bootModal-settings-group', 'option_checkbox' );	
	register_setting( 'alimir-bootModal-settings-group', 'option_bs3patch' );	
	register_setting( 'alimir-bootModal-settings-group', 'modal_theme' );	
	register_setting( 'alimir-bootModal-settings-group', 'option_usermodal' );	
	register_setting( 'alimir-bootModal-settings-group', 'can_register_option' );	
	register_setting( 'alimir-bootModal-settings-group', 'lable_setting' );	
	register_setting( 'alimir-bootModal-settings-group', 'button_text' );	
	register_setting( 'alimir-bootModal-settings-group', 'button_text2' );	
	register_setting( 'alimir-bootModal-settings-group', 'enable_login_captcha' );	
	register_setting( 'alimir-bootModal-settings-group', 'enable_register_captcha' );	
	register_setting( 'alimir-bootModal-settings-group', 'enable_lostpass_captcha' );	
	register_setting( 'alimir-bootModal-settings-group', 'captcha_font' );	
	register_setting( 'alimir-bootModal-settings-group', 'default_buttons' );
	register_setting( 'alimir-bootModal-settings-group', 'default_sizes' );
	register_setting( 'alimir-bootModal-settings-group', 'remove_btn_block' );
	register_setting( 'alimir-bootModal-settings-group', 'alimir_login_redirect' );
	register_setting( 'alimir-bootModal-settings-group', 'alimir_register_redirect' );
}

function alimir_bootModal_settings_page() {
?>
<div class="wrap">
<h2><?php _e('Alimir Ajax BootModal Login','alimir'); ?></h2>

<form method="post" action="options.php">
    <?php settings_fields( 'alimir-bootModal-settings-group' ); ?>
    <?php do_settings_sections( 'alimir-bootModal-settings-group' ); ?>
    <table class="form-table">
		<p class="update-nag"><?php _e('Using <code>[Alimir_BootModal_Login]</code> shortcode, to display ajax bootmodal login.','alimir'); ?></p>
		
        <tr valign="top">
        <th scope="row"><?php _e('Non-use BootStrap modules','alimir'); ?></th>
        <td>
		<input name="option_checkbox" type="checkbox" value="1" <?php checked( '1', get_option( 'option_checkbox' ) ); ?> />
		<p class="description"><?php _e('If your theme support bootstrap, Check this option.','alimir'); ?></p>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Do you use from bootstrap +3?','alimir'); ?></th>
        <td>
		<input name="option_bs3patch" type="checkbox" value="1" <?php checked( '1', get_option( 'option_bs3patch' ) ); ?> />
		<p class="description"><?php _e('If you use from bootstrap +3 in your theme, Check this option.','alimir'); ?></p>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Theme','alimir'); ?></th>
        <td>
		<select name="modal_theme">
            <option value="1" <?php selected( get_option( 'modal_theme' ), 1 ); ?>>Default</option>
            <option value="2" <?php selected( get_option( 'modal_theme' ), 2 ); ?>>Bootstrap UI Kit</option>
		</select>
		<p class="description"><?php _e('Select your favorite Theme for modal box.','alimir'); ?></p>
		</td>
        </tr>		
		
        <tr valign="top">
        <th scope="row"><?php _e('Deactivate User Panel','alimir'); ?></th>
        <td>
		<input name="option_usermodal" type="checkbox" value="1" <?php checked( '1', get_option( 'option_usermodal' ) ); ?> />
		<p class="description"><?php _e('This option disables user modal profile button.','alimir'); ?></p>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('disable user registration?','alimir'); ?></th>
        <td>
		<input name="can_register_option" type="checkbox" value="1" <?php checked( '1', get_option( 'can_register_option' ) ); ?> />
		<p class="description"><?php _e('This option disables user registration form.','alimir'); ?></p>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Label Setting','alimir'); ?></th>
		<td>
		<fieldset>
		<label><input name="lable_setting" type="radio" value="0" <?php checked( '0', get_option( 'lable_setting' ) ); ?> />Label</label>
		<label><input name="lable_setting" type="radio" value="1" <?php checked( '1', get_option( 'lable_setting' ) ); ?> />Placeholder</label>
		<p class="description"><?php _e('Select Your favorite Setting: Placeholder/Lable','alimir'); ?></p>
		</fieldset>
		</td>
        </tr>		
		
        <tr valign="top">
        <th scope="row"><?php _e('Login Button Text','alimir'); ?></th>
        <td><input type="text" name="button_text" value="<?php echo get_option('button_text'); ?>" /></td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Profile Button Text','alimir'); ?></th>
        <td><input type="text" name="button_text2" value="<?php echo get_option('button_text2'); ?>" /></td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Enable login captcha','alimir'); ?></th>
        <td>
		<input name="enable_login_captcha" type="checkbox" value="1" <?php checked( '1', get_option( 'enable_login_captcha' ) ); ?> />
		<p class="description"><?php _e('This option enables captcha on login form.','alimir'); ?></p>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Enable register captcha','alimir'); ?></th>
        <td>
		<input name="enable_register_captcha" type="checkbox" value="1" <?php checked( '1', get_option( 'enable_register_captcha' ) ); ?> />
		<p class="description"><?php _e('This option enables captcha on registration form.','alimir'); ?></p>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Enable lostpass captcha','alimir'); ?></th>
        <td>
		<input name="enable_lostpass_captcha" type="checkbox" value="1" <?php checked( '1', get_option( 'enable_lostpass_captcha' ) ); ?> />
		<p class="description"><?php _e('This option enables captcha on lostpass form.','alimir'); ?></p>
		</td>
        </tr>		
		
        <tr valign="top">
        <th scope="row"><?php _e('Captcha Font','alimir'); ?></th>
        <td>
		<select name="captcha_font">
            <option value="1" <?php selected( get_option( 'captcha_font' ), 1 ); ?>>Blackout</option>
            <option value="2" <?php selected( get_option( 'captcha_font' ), 2 ); ?>>1942</option>
            <option value="3" <?php selected( get_option( 'captcha_font' ), 3 ); ?>>Anagram</option>
            <option value="4" <?php selected( get_option( 'captcha_font' ), 4 ); ?>>axis</option>
            <option value="5" <?php selected( get_option( 'captcha_font' ), 5 ); ?>>BPdotsSquareBold</option>
            <option value="6" <?php selected( get_option( 'captcha_font' ), 6 ); ?>>GoodDog</option>
		</select>
		<p class="description"><?php _e('Select your favorite Captcha font.','alimir'); ?></p>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Default buttons','alimir'); ?></th>
		<td>
		<fieldset>
		<label><input name="default_buttons" type="radio" value="0" <?php checked( '0', get_option( 'default_buttons' ) ); ?> />btn-primary</label><br />
		<label><input name="default_buttons" type="radio" value="1" <?php checked( '1', get_option( 'default_buttons' ) ); ?> />btn-info</label><br />
		<label><input name="default_buttons" type="radio" value="2" <?php checked( '2', get_option( 'default_buttons' ) ); ?> />btn-success</label><br />
		<label><input name="default_buttons" type="radio" value="3" <?php checked( '3', get_option( 'default_buttons' ) ); ?> />btn-warning</label><br />
		<label><input name="default_buttons" type="radio" value="4" <?php checked( '4', get_option( 'default_buttons' ) ); ?> />btn-danger</label><br />
		<label><input name="default_buttons" type="radio" value="5" <?php checked( '5', get_option( 'default_buttons' ) ); ?> />btn-inverse</label>
		<p class="description"><?php _e('choose your button style.','alimir'); ?></p>
		</fieldset>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Button sizes','alimir'); ?></th>
		<td>
		<fieldset>
		<label><input name="default_sizes" type="radio" value="0" <?php checked( '0', get_option( 'default_sizes' ) ); ?> />btn-large</label><br />
		<label><input name="default_sizes" type="radio" value="1" <?php checked( '1', get_option( 'default_sizes' ) ); ?> />btn-small</label><br />
		<label><input name="default_sizes" type="radio" value="2" <?php checked( '2', get_option( 'default_sizes' ) ); ?> />btn-mini</label>
		<p class="description"><?php _e('choose your button size.','alimir'); ?></p>
		</fieldset>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Remove button block','alimir'); ?></th>
        <td>
		<input name="remove_btn_block" type="checkbox" value="1" <?php checked( '1', get_option( 'remove_btn_block' ) ); ?> />
		<p class="description"><?php _e('This option removes button block mode.','alimir'); ?></p>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('login redirect URL','alimir'); ?></th>
        <td><input type="text" name="alimir_login_redirect" value="<?php echo get_option('alimir_login_redirect'); ?>" /></td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('Registration redirect URL','alimir'); ?></th>
        <td><input type="text" name="alimir_register_redirect" value="<?php echo get_option('alimir_register_redirect'); ?>" /></td>
        </tr>
		
    </table>
	<div id="poststuff" class="ui-sortable meta-box-sortables">
		<div class="postbox">	
		<h3><?php _e('Like this plugin?','alimir'); ?></h3>
		<div class="inside">
		<p>
		<?php _e('Show your support by Rating 5 Star in <a href="http://wordpress.org/plugins/ajax-bootmodal-login"> Plugin Directory reviews</a>','alimir'); ?><br />
		<?php _e('Follow me on <a href="https://www.facebook.com/alimir.ir"> Facebook</a>','alimir'); ?><br />
		<?php _e('Plugin Author Blog: <a href="http://alimir.ir"> Wordpress & Programming World.</a>','alimir'); ?>
		</p>
		</div>
		</div>
	</div>	
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>