<?php 
// Creating the widget 
class alimir_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'ajax_bootmodal', 
			__('Ajax BootModal Login', 'alimir'), 
			array( 'description' => __( 'Ajax BootModal Login is a WordPress plugin that is powered by bootstrap and ajax for better login, registration or lost password.', 'alimir' ))
			);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		echo do_shortcode('[Alimir_BootModal_Login]');
		echo $args['after_widget'];
	}
			
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Login', 'alimir' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}

function alimir_load_widget() {
	register_widget( 'alimir_widget' );
}
add_action( 'widgets_init', 'alimir_load_widget' );
?>