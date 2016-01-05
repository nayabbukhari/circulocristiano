<div class="wrap about-wrap">
    <h1><?php _e( 'SeedRedux Framework - Changelog', 'seedredux-framework' ); ?></h1>

    <div
        class="about-text"><?php _e( 'Our core mantra at SeedRedux is backwards compatibility. With hundreds of thousands of instances worldwide, you can be assured that we will take care of you and your clients.', 'seedredux-framework' ); ?></div>
    <div
        class="seedredux-badge"><i
            class="el el-seedredux"></i><span><?php printf( __( 'Version %s', 'seedredux-framework' ), SeedReduxFramework::$_version ); ?></span>
    </div>

    <?php $this->actions(); ?>
    <?php $this->tabs(); ?>

    <div class="changelog">
        <div class="feature-section">
            <?php echo $this->parse_readme(); ?>
        </div>
    </div>

</div>