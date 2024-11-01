<?php
/*
Plugin Name: Spider Slider Image Carousel
Plugin URI: http://spiderdesign.ca/spiderslider
Description: The Spider Slider is an image carousel tool that lets you format and insert multiple slideshows into posts, pages and widget areas.
Author: Nick Andrews
Version: 1.0
*/



///// widget component /////
add_action( 'widgets_init', 'add_slider_init' );
function add_slider_init() {
	register_widget( 'spiderslider_widget' );
}

class spiderslider_widget extends WP_Widget {

	function spiderslider_widget(){
        $widget_ops = array( 'classname' => 'spiderslider', 'description' => 'spiderslider widget' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'spiderslider' );
		/* Create the widget. */
		$this->WP_Widget( 'spiderslider', 'spiderslider widget', $widget_ops, $control_ops );
		$this->url      = plugins_url('spiderslider');
	}//constructor

	function widget( $args, $instance ) {
	    extract( $args );
		echo $before_widget;
		 $slider_name = trim($instance['slidername'] );
		/// STORED DATA => [0]:{string}name [1]:{int}width, height, red, green, blue, border, bRadius, direction, speed [2]:{objects} array
		echo '<div id="spiderslider_'.$slider_name.'"></div>';// place holder
		echo "\n";
		$use_this =  'spiderslider_'.$slider_name ; 
		$stored =  explode ( '||', get_option( $use_this ) );		
		if ( isset($stored[1]) ){///data exist?
			$dimensions  = explode('|', $stored[1]); /// params
			$im = explode('|', $stored[2]); /// images : I_ID# | Url link |... odd/even
    		$d = array_pop($im); //drop last space
			for( $i = 0; $i < count($im); $i++ ){
        	    $im[$i] = ($i % 2)? str_replace( 'http://', '',$im[$i] ) : str_replace( 'I_', '', $im[$i] );  ///xtract id's and links stored in odd/even positions
	 		}//f
		 ?>
		 <script> 
			var widget_slida_<?php echo $slider_name; ?> = new Array();
			<?php for ( $i = 0; $i < count($im); $i= $i+2 ){
				$img     = $this->get_this_image($im[$i]);
				$name    = explode( '/', $img[0]->Image );///avoiding passing slashes in _GET
				$img_url = $this->url.'/class-slider-pic.php?scr='.$name[2].'&y='.$name[0].'&m='.$name[1].'&w='.$dimensions[2].'&h='.$dimensions[3].'&r='.$dimensions[4].'&g='.$dimensions[5].'&b='.$dimensions[6];
				?>widget_slida_<?php echo $slider_name; ?>.push( new slide_img( '<?php echo $img_url; ?>', '<?php echo $name[2]; ?>', '<?php echo $im[$i+1]; ?>', <?php echo $im[$i];?>  ) );
 	    	<?php }//f
			?>try{
				sentinel.add_nodes(new create_node( '<?php echo $slider_name;?>', '<?php echo $dimensions[2]?>', '<?php echo $dimensions[3]?>', '<?php echo $dimensions[4]?>', '<?php echo $dimensions[5]?>', '<?php echo $dimensions[6]?>', '<?php echo $dimensions[8]?>', '<?php echo $dimensions[9]?>', '<?php echo $dimensions[1]?>', '<?php echo $dimensions[0]?>', widget_slida_<?php echo $slider_name;?> ) );
		 	}catch(e){
		        var sentinel = new create_node( '<?php echo $slider_name;?>', '<?php echo $dimensions[2]?>', '<?php echo $dimensions[3]?>', '<?php echo $dimensions[4]?>', '<?php echo $dimensions[5]?>', '<?php echo $dimensions[6]?>', '<?php echo$dimensions[8]?>', '<?php echo $dimensions[9]?>', '<?php echo $dimensions[1]?>', '<?php echo $dimensions[0]?>', widget_slida_<?php echo $slider_name;?> );
			}
			sentinel.slide_HTML2('<?php echo $slider_name;?>');
		</script><?php
		}//i
		echo $after_widget;
	 }//fnc
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags (if needed) and update the widget settings. */
		$instance['slidername'] = trim( strip_tags( $new_instance['slidername'] ) );
		return $instance;
	}//fnc

	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 'slidername'=> 'slidername' );
		$instance = wp_parse_args ( (array) $instance, $defaults );
		$sliders  = explode( "||", get_option( 'spiderslider_named_list' ) );
		?>
		<label for="<?php echo $this->get_field_id ( 'slidername' ); ?>">Spider Slider</label>
			<select   id="<?php echo $this->get_field_id ( 'slidername' ); ?>" name="<?php echo $this->get_field_name ( 'slidername' ); ?>" style="width:100%;" > 
				<option value=""><?php
			 		foreach ($sliders as $name) {
				  		if ( $instance['slidername'] == $name ){
							$option = '<option value="'.$instance['slidername'].'" SELECTED>';
	                		$option .= $instance['slidername'];
	                		$option .= '</option>';
				   		}else{
				    		$option = '<option value="'.$name.'" >';
	                		$option .= $name;
	                		$option .= '</option>';
						}
						echo $option;
					}
				?></select>
		<?php
	}///fnc


	
	function get_this_image($id){
	    global $wpdb;
		$attaches = $wpdb->prefix."postmeta";
		$query = "SELECT ".$attaches.".meta_value as 'Image'    
		FROM   ".$attaches."        
    	WHERE ".$attaches.".meta_ID='".$id."' ";
		/////print_r($query);
		$myImg = $wpdb->get_results($query);
    	return $myImg;
  }//fnc
	
}//class









///add_filter('the_editor_content', 'my_admin_menu', 10, 1);
////function my_admin_menu($menu ){
////$test = '<input type="submit" name="spider" value="spider"/>';
////return $menu.$test;
////}

require_once("class-front-end.php");
add_filter('the_content', 'my_slider', 10, 1);
function my_slider($content){
    global $post;
	$ff = new Front_End();
	$ff->parse_content($content);
	$s = $ff->insert_holders($content).$ff->insert_javascript();
	////$ff->assemble_javascript();
	return $s;
}

///function debug($obj){
//	echo "<p>";
//	foreach($obj as $k => $v){
 //		echo (is_array($v))?  debug($v) :  '<br />'.$k.'=> '.$v ; 
//	}//f
//	echo "</p>";	
///}////fnc


///public
add_action('wp_enqueue_scripts', 'slider_method');	
function slider_method() {/////loads front end
	wp_register_script( 'spiderslider_core', plugins_url('/spiderslider-core.js', __FILE__) );
	wp_enqueue_script( 'spiderslider_core' );
}
add_action( 'wp_enqueue_scripts', 'prefix_add_my_slidestyle' );
function prefix_add_my_slidestyle() {
	wp_register_style( 'prefix-style', plugins_url('slider.css', __FILE__) );
	wp_enqueue_style( 'prefix-style' );
}

	
	
	
////////admin
add_action('admin_enqueue_scripts', 'adminJs');
function adminJs(){
	/////wp_enqueue_script( 'spidersliderBar' );
	wp_enqueue_script( 'spiderslider_core' );
	wp_enqueue_script( 'spiderslider_admin' );
	
}
add_action( 'admin_init', 'spider_slider_init' );
function spider_slider_init() {
	///////wp_register_script( 'spidersliderBar', plugins_url('/sliderBar.js', __FILE__) , false , false ,false);
	wp_register_script( 'spiderslider_core', plugins_url('/spiderslider-core.js', __FILE__) , false , false ,false);
	wp_register_script( 'spiderslider_admin', plugins_url('/spiderslider-admin.js', __FILE__) , false , false ,false);
	
}
	
add_action( 'admin_menu', 'spider_slider_admin_menu' );
function spider_slider_admin_menu() {
	$page = add_submenu_page( 'options-general.php',
    					 __( 'The Spider Slider', 'spiderSlider' ),
						 __( 'The Spider Slider', 'spiderSlider' ),
						'manage_options',
						'spider_slider-options',
						'spider_slider_manage_menu'
						);
	add_action('admin_print_styles-' .$page, 'spider_slider_admin_styles');
}
function spider_slider_manage_menu() {
 /* Output our admin page */
	echo '<h2>Spider Slider</h2>';
	require_once("../wp-content/plugins/spiderslider/class-spider-admin.php");
}//fnc
function spider_slider_admin_styles() {
	wp_register_style( 'prefix-style', plugins_url('slider.css', __FILE__) );
	wp_enqueue_style( 'prefix-style' );
}

?>