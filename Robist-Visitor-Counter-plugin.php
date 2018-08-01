<?php
/*
Plugin Name: Robist Visitor Counter
Description: This plugin shows you visitor count of your website. This is the effort of Robist Group and developed by Shaon Majumder. And this is the first theme of Robist. So, support us by using this theme.
For any information and feature update , email us at wordpressthemes@robist.com.

*/
/* Start Adding Functions Below this Line */
define( 'PLUGIN_PATH', __DIR__ );
define( 'FILE_NAME', "robist_visitor_counter.log" );
define( 'FILE_PATHNAME' , PLUGIN_PATH."/".FILE_NAME) ;

if(isset($_POST) && $_POST['name'] == "download_counter_backup"){
	counter_file_force_download(FILE_PATHNAME);
	exit;
}

// Register and load the widget
function robist_visitorcounter_load_widget() {
    register_widget( 'robist_visitorcounter_widget' );
}
add_action( 'widgets_init', 'robist_visitorcounter_load_widget' );


//admin page
add_action('admin_menu', 'test_plugin_setup_menu');
 
function test_plugin_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Robist Visitor Counter', 'manage_options', 'test-plugin', 'robist_visitorcounter_admin_page' );
}
 
function robist_visitorcounter_admin_page(){
        ?>
        <h1>Robist Visitor Counter - Admin Area</h1>
        <button class="robist_visitorcounter_button">Backup Counter</button>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript">
        	$('.robist_visitorcounter_button').click(function() {
				$.ajax({
					type: "POST",
					url: "",
					data: { name: "download_counter_backup" }
				}).done(function( msg ) {
					alert( "Data Saved: " + msg );
				});
			});
        </script>
        <?php
}

function counter_file_force_download($file){

	if (file_exists($file)) {
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.basename($file).'"');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    readfile($file);
	    exit;
	}
}
//admin page
 
// Creating the widget 
class robist_visitorcounter_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'robist_visitorcounter_widget', 

			// Widget name will appear in UI
			__('Robist Visitor Counter', 'robist_visitorcounter_widget_domain'), 

			// Widget description
			array( 'description' => __( 'Visitor Counter for your website', 'robist_visitorcounter_widget_domain' ), ) 
		);
	}

	
	
	
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
		echo '<ul><li>';
		echo __( 'Visitors: ', 'robist_visitorcounter_widget_domain' );
		echo $this->visitor_count();
		echo '</li></ul>';
		
		echo $args['after_widget'];
		
	}

	public function counter_initialization(){
		if(!file_exists(FILE_PATHNAME)){
			$myfile = fopen(FILE_PATHNAME, "w") or die("Unable to open file!");
			$txt = "0";
			fwrite($myfile, $txt);
			fclose($myfile);
		}
	}
	
	public function visitor_count() {
		$this->counter_initialization();
		
		//$count_number = file_get_contents(FILE_PATHNAME);
		$myfile = fopen(FILE_PATHNAME, "r") or die("Unable to open file!");
		$count_number = fread($myfile,filesize(FILE_PATHNAME));
		fclose($myfile);

		$myfile = fopen(FILE_PATHNAME, "w") or die("Unable to open file!");
		$txt = ++$count_number;
		fwrite($myfile, $txt);
		fclose($myfile);

		return $count_number;

	}
	
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'robist_visitorcounter_widget_domain' );
		}
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // Class robist_visitorcounter_widget ends here
/* Stop Adding Functions Below this Line */
?>