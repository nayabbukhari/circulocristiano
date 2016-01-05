<?php

// =============================================================================
// VIEWS/ADMIN/OPTIONS-PAGE-MAIN.PHP
// -----------------------------------------------------------------------------
// Plugin options page main content.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Main Content
// =============================================================================

// Main Content
// =============================================================================

?>

<div id="post-body-content">
  <div class="meta-box-sortables ui-sortable">

    <!--
    ENABLE
    -->

    <div id="meta-box-enable" class="postbox">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Enable', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select the checkbox below to enable the plugin.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_white_label_enable">
                <strong><?php _e( 'Enable White Label', '__x__' ); ?></strong>
                <span><?php _e( 'Select to enable the plugin and display options below.', '__x__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="x_white_label_enable" id="x_white_label_enable" value="1" <?php checked( ! isset( $x_white_label_enable ) ? '0' : $x_white_label_enable, '1', true ); ?>>
              </fieldset>
            </td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    SETTINGS
    -->

    <div id="meta-box-settings" class="postbox" style="display: <?php echo ( isset( $x_white_label_enable ) && $x_white_label_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Settings', '__x__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select your plugin settings below.', '__x__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="x_white_label_login_image">
                <strong><?php _e( 'Login Image', '__x__' ); ?></strong>
                <span><?php _e( 'Enter the URL to an image that you would like to use in place of the standard WordPress login image (must be less than 320px wide).', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_white_label_login_image" id="x_white_label_login_image" type="text" value="<?php echo ( isset( $x_white_label_login_image ) ) ? $x_white_label_login_image : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_white_label_addons_home_heading">
                <strong><?php _e( 'Addons Home Heading', '__x__' ); ?></strong>
                <span><?php _e( 'Enter an alternate heading for the Addons Home page.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_white_label_addons_home_heading" id="x_white_label_addons_home_heading" type="text" value="<?php echo ( isset( $x_white_label_addons_home_heading ) ) ? $x_white_label_addons_home_heading : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_white_label_addons_home_subheading">
                <strong><?php _e( 'Addons Home Subheading', '__x__' ); ?></strong>
                <span><?php _e( 'Enter an alternate subheading for the Addons Home page.', '__x__' ); ?></span>
              </label>
            </th>
            <td><input name="x_white_label_addons_home_subheading" id="x_white_label_addons_home_subheading" type="text" value="<?php echo ( isset( $x_white_label_addons_home_subheading ) ) ? $x_white_label_addons_home_subheading : ''; ?>" class="large-text"></td>
          </tr>

          <tr>
            <th>
              <label for="x_white_label_addons_home_content">
                <strong><?php _e( 'Addons Home Content', '__x__' ); ?></strong>
                <span><?php _e( 'Enter alternate content for the Addons Home page.', '__x__' ); ?></span>
              </label>
            </th>
            <td><textarea name="x_white_label_addons_home_content" id="x_white_label_addons_home_content" class="code"><?php echo ( isset( $x_white_label_addons_home_content ) ) ? esc_textarea( $x_white_label_addons_home_content ) : ''; ?></textarea>
            </td>
          </tr>

        </table>
      </div>
    </div>

  </div>
</div>