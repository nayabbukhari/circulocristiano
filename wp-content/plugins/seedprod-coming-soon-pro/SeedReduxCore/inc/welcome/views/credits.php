<div class="wrap about-wrap">
    <h1><?php _e( 'SeedRedux Framework - A Community Effort', 'seedredux-framework' ); ?></h1>

    <div
        class="about-text"><?php _e( 'We recognize we are nothing without our community. We would like to thank all of those who help SeedRedux to be what it is. Thank you for your involvement.', 'seedredux-framework' ); ?></div>
    <div
        class="seedredux-badge"><i
            class="el el-seedredux"></i><span><?php printf( __( 'Version %s', 'seedredux-framework' ), SeedReduxFramework::$_version ); ?></span>
    </div>

    <?php $this->actions(); ?>
    <?php $this->tabs(); ?>

    <p class="about-description"><?php _e( 'SeedRedux is created by a community of developers world wide. Want to have your name listed too? <a href="https://github.com/seedreduxframework/seedredux-framework/blob/master/CONTRIBUTING.md" target="_blank">Contribute to SeedRedux</a>.', 'seedredux-framework' ); ?></p>

    <?php echo $this->contributors(); ?>
</div>