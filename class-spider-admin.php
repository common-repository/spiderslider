<?php
class Spider_Admin{
	var $labels;
	var $stored;
	var $defaults;
	var $url;
	var $names;
	var $params;
	var $content;
	var $slider_list;
	var $activ_slider;
	var $int_start;
	var $int_limit;
	

	function Spider_Admin(){
		$this->labels   = array('spiderslider_speed', 'spiderslider_direction', 'spiderslider_width', 'spiderslider_height','red_col','green_col','blue_col','hex-code', 'spiderslider_border', 'spiderslider_border_radius' ); 
		$this->stored   = array('spiderslider_speed'=>'', 'spiderslider_direction'=>'', 'spiderslider_width'=>'', 'spiderslider_height'=>'','red_col'=>'','green_col'=>'','blue_col'=>'','hex-code'=>'', 'spiderslider_border'=>'', 'spiderslider_border_radius'=>'' ); 
		$this->defaults = array(50, 0, 200, 200, 140, 140, 140,'cccccc', 0, 0);
		$this->url      = plugins_url('spiderslider');
		$this->names    = array();
		$this->params    = array();
		$this->content    = array();
		$this->slider_list = explode( "||", get_option( 'spiderslider_named_list' ) );
		$dump = array_pop($this->slider_list);//lose empty index value
		if ( isset ($_POST[activ_slider])){ 
			$this->activ_slider = $_POST[activ_slider];
		}else{ 
			$this->activ_slider = (count($this->slider_list)>0)? $this->slider_list[count($this->slider_list)-1]: 'default';
		}
		$this->int_start =((isset($_POST['int-start']))&&(is_numeric( $_POST['int-start'])))?( $_POST['int-start'] *1): 0;
		$this->int_limit =((isset($_POST['int-limit']))&&(is_numeric( $_POST['int-limit'])))?( $_POST['int-limit'] *1): 300;
		
		
	}//constructor

	function handle_posts(){
		if( ( isset ( $_POST[spiderslider_name_lists]) )&&( $_POST[spiderslider_name_lists] >= 0 ) )   {
			$this->activ_slider=$this->slider_list[($_POST[spiderslider_name_lists])]; ///use name from list
		}else if(
			( isset ( $_POST[spiderslider_named_slider]) )
			&&( strlen ($_POST[spiderslider_named_slider]) >= 0 ) 
			&&(! isset($_POST[deleter]) ) 
		){
			$this->add_new_node_now(  $_POST[spiderslider_named_slider] );
		}
		if ( isset ($_POST['deleter'])){//delete sllider
			$this->delete_slider($_POST[spiderslider_named_slider]); 
		}
	}

	function add_new_node_now( $spiderslider_named_slider ){
		$replace =array(0=>	''); 
		$search = array(0=>	'%', '&', '+',  '|', '\\', '!', ')' , '(' , '#' , '*',	 '>' , '<' , '\'', '\"', '$_',  ']',  '^', '[' , '}' , '{' , '$', '?', 'spiderslider', 'namedLists','.',',',' '); 
		$cleanName = str_replace($search, $replace, $spiderslider_named_slider );
		$this->activ_slider= $cleanName;
		$this->slider_list[]=$this->activ_slider;///add to select
?>
		<script>
			var admin_slida_<?php echo $this->activ_slider; ?> = new Array();
			var temp = new create_node('<?php echo $this->activ_slider;?>', my_width, my_height, r, g, b, slider_border, border_radius, my_direction, my_speed, admin_slida_<?php echo $this->activ_slider; ?>);
			sentinel.add_nodes(temp);
			sentinel.track_options('<?php echo $this->activ_slider; ?>');
		</script>
<?php 
	}

	function make_name_list(){
		echo '<div class="slider-title" >';
			echo ' <form name="nameer" method="post" action="">';
				echo '<select name="spiderslider_name_lists"  onchange="submit()"/>';
					foreach($this->slider_list as $k => $v ){
						echo '<option value="'.$k.'">'.$v.'</option>';
					}
					echo '<option value="-1" selected>Saved Sliders Here</option>';
				echo '</select>'; 
				echo '<input type="text" name="spiderslider_named_slider" id="spiderslider_named_slider" value="'.$this->activ_slider.'" onchange="submit()";/></form>';
				echo ( $this->activ_slider == 'default' )?'': '<form name="deletes" method="post" action=""><input type="submit" name="deleter" id="deleter" value="delete" /><input type="hidden" name="spiderslider_named_slider" id="spiderslider_named_slider" value="'.$this->activ_slider.'" />'; 
			echo '</form>';
		echo '</div>';
	}

	function get_stored_data(){
		$use_this =  'spiderslider_'.$this->activ_slider ; 
		$stored =  explode ( '||', get_option( $use_this ) );
		$this->names   = explode( '|', $stored[0] );///names
		$this->params  = explode( '|', $stored[1] );///slider params
		$this->content = explode( '|', $stored[2] );///slider content
		foreach( $this->labels as $k => $v ){
			(  $this->params[$k] )? $this->stored[$v] = $this->params[$k]: $this->stored[$v]= $this->defaults[$k];  ///capture params
		}//f
		$d = array_pop($this->content); //drop last space
		for( $i = 0; $i < count($this->content); $i++ ){
			$this->content[$i] = ( $i % 2 )?  str_replace ( 'http://', '',$this->content[$i] )  :   str_replace ( 'I_', '', $this->content[$i] );  ///clean content
		}//f
 		if ( is_null($stored[1]) ){
			$this->defaults = $stored[1];
		}
		$this->init_javascript(); 
	}

	function color_picker(){
		echo '<div id="cl-options">';
		echo '<div id="color-bar"></div>';
		echo '<div class="colrgb">';
		echo 'R<input type="text" id="red_col" name="red_col" value="'.$this->stored['red_col'].'" onchange="color_me(this.value, \'r\'); update_param( sentinel,  \''.$this->activ_slider.'\', this.value, \'red\' ); ">
			 G<input type="text" id="green_col" name="green_col" value="'.$this->stored['green_col'].'" onchange="color_me(this.value, \'g\'); update_param( sentinel,  \''.$this->activ_slider.'\', this.value, \'green\' ); ">
			 B<input type="text" id="blue_col" name="blue_col" value="'.$this->stored['blue_col'].'" onchange="color_me(this.value, \'b\'); update_param( sentinel,  \''.$this->activ_slider.'\', this.value, \'blue\' ); ">'; 
		echo '</div>';
		echo '<div class="colhex">Hex<input type="text"  id="hex-code" name="hex-code" value="'.$this->stored['hex-code'].'"  onchange="decode_hex(this.value)"/></div>';
		echo '</div>';
		echo '<script>
			function update_colors(prop, val){
	 	            update_param( sentinel, \''.$this->activ_slider.'\', val , prop );
			}
			</script>';
	}

  function transition_selector(){
	echo '<div id="slider-trans">';
		echo 'Transition';
		echo '<input type="hidden"  name="spiderslider_direction" id="spiderslider_direction" value="'.$this->stored['spiderslider_direction'].'" />';
		echo '<div  class="arrow-up" value=""    id="dir0" onclick="hh_this( this ); update_param( sentinel,  \''.$this->activ_slider.'\', 0, \'dir\' ); "></div>';
		echo '<div  class="arrow-right" value="" id="dir1" onclick="hh_this( this ); update_param( sentinel,  \''.$this->activ_slider.'\', 1, \'dir\' ); "></div>';
		echo '<div  class="arrow-down" value=""  id="dir2" onclick="hh_this( this ); update_param( sentinel,  \''.$this->activ_slider.'\', 2, \'dir\' );"></div>';
		echo '<div  class="arrow-left" value=""  id="dir3" onclick="hh_this( this ); update_param( sentinel,  \''.$this->activ_slider.'\', 3, \'dir\' );"></div>';
		echo '<div  class="arrow-fade" value=""  id="dir4" onclick="hh_this( this ); update_param( sentinel,  \''.$this->activ_slider.'\', 4, \'dir\' );"></div>';
	echo '</div>';
	}

    function speed_selector(){
		echo '<div id="slider-speed">';
			echo 'Speed';
			echo '<div class="boundary">';
				echo '<span style="left: 1px">slow</span><span style=" right: 1px">fast</span>';
				echo '<div id="speeds" style="width:100px;" >';
					echo '<div  id="speed-dial" class="dragger" style="width:8px;left: 46px;" ></div>';   
				echo '</div>';
				echo '<input type="text" id="spiderslider_speed"  name="spiderslider_speed" value="'.$this->stored['spiderslider_speed'].'"   
		 		onchange=" update_param( sentinel,  \''.$this->activ_slider.'\', (this.value) , \'speed\' );  manual_move( this.value , \'speed-dial\' );" />';
				echo '<script>
				function speed_updater(){
					update_param( sentinel, \''.$this->activ_slider.'\', (document.getElementById(\'spiderslider_speed\').value) , \'speed\' );
				}
				</script>';
			echo '</div>';
		echo '</div>';
	}

	function slide_dimensions(){
		echo '<div id="slide-dimens">';
			echo '<div>Width<br /><input type="text" id="spiderslider_width" name="spiderslider_width" value="'.$this->stored['spiderslider_width'].'"  
				onchange=" my_width=this.value;  update_param(sentinel, \''.$this->activ_slider.'\', this.value, \'width\' );"></div>';
			echo '<div>Height<br /><input type="text" id="spiderslider_height" name="spiderslider_height" value="'.$this->stored['spiderslider_height'].'"  
	  			onchange=" my_height= this.value; update_param( sentinel,  \''.$this->activ_slider.'\', this.value, \'height\' );"></div>';
		echo '</div>';
	}

	function slide_shape(){
		echo '<div id="slide-shape">';
		echo '<div>Border<br /><input type="text"  id="spiderslider_border" name="spiderslider_border" value="'.$this->stored['spiderslider_border'].'"  
		onchange=" slider_border = this.value; update_param( sentinel,  \''.$this->activ_slider.'\', this.value, \'border\' );"></div>';
		echo '<div>Radius<br /><input type="text"  id="spiderslider_border_radius" name="spiderslider_border_radius" value="'.$this->stored['spiderslider_border_radius'].'"  
		onchange="border_radius = this.value; update_param( sentinel,  \''.$this->activ_slider.'\', this.value, \'brdr_radius\' );"></div>';
		echo '</div>';
	}

  function add_spider_button(){
		$u = plugins_url('class-spider.php?r=0&g=0&b=0&p=0', __FILE__);
		echo '<input type="image" src="'.$u.'" name="reset-opts" id="reset-opts" class="reset-opts" style="position: absolute; left:100px; top: -500px; width: 50px; height: 69px;" 
 		onclick="sentinel.restart(\''.$this->activ_slider.'\');" onmouseout="document.body.style.cursor=\'default\';"  />'; 
	}

	function embed_code(){
		echo '<input class="embedder" name="embed_code" value="Embed Code" type="submit" onclick="give_embed(\''.$this->activ_slider.'\', \''.$this->url.'\');" />';
	}

function init_javascript(){
?>
	<script>
		var ssite_url =  "<?php echo site_url(); ?>" ;
		var my_direction = <?php echo  $this->stored['spiderslider_direction']; ?>;
		var my_width = <?php echo  $this->stored['spiderslider_width']; ?>;
		var my_height = <?php echo  $this->stored['spiderslider_height']; ?>;
		var my_speed = <?php echo  $this->stored['spiderslider_speed']; ?>;
		var slider_border =  <?php echo  $this->stored['spiderslider_border']; ?>;
		var border_radius =  <?php echo  $this->stored['spiderslider_border_radius']; ?>;
		var r = <?php echo $this->stored['red_col'];?>;	 
		var g = <?php echo $this->stored['green_col'];?>;	 
		var b = <?php echo $this->stored['blue_col'];?>;	
		var admin_slida_<?php echo $this->activ_slider; ?> = new Array();
		var int_start = <?php echo $this->int_start; ?>;
		var int_limit = <?php echo $this->int_limit; ?>;
		

<?php
		for($i = 0 ; $i < count($this->content); $i= $i+2 ){
			$img = $this->get_this_image($this->content[$i]);
			$name = explode ('/', $img[0]->Image);
			$imgUrl = $this->url.'/class-slider-pic.php?scr='.$name[2].'&y='.$name[0].'&m='.$name[1].'&w='.$this->stored['spiderslider_width'].'&h='.$this->stored['spiderslider_height'].'&r='.$this->stored['red_col'].'&g='.$this->stored['green_col'].'&b='.$this->stored['blue_col'];
?>           
			admin_slida_<?php echo $this->activ_slider; ?>.push( new slide_img('<?php echo $imgUrl; ?>', '<?php echo $name[2]; ?>','<?php echo $this->content[$i+1]; ?>',<?php echo $this->content[$i];?>  ) )   ;
		
<?php	 }?>
	
		var sentinel = new create_node('<?echo $this->activ_slider;?>',my_width,my_height,r,g,b,slider_border,border_radius,my_direction, my_speed, admin_slida_<?php echo $this->activ_slider; ?>);
		var image_bay = new Array();

		function show_bay_images(pg, desc){
			document.getElementById('start-here').style.display = 'none';
			pg = pg*10;
			var src_small=''; var src_long=''; var s=''; 
			var imga = new Array();
			for ( var i = pg ; ((i < (pg+10))&&(i< image_bay.length) ) ;i++){
				imga = image_bay[i].img.split('/');
				src_small = '<?php echo $this->url;?>/class-slider-pic.php?scr='+imga[2]+'&amp;y='+imga[0]+'&amp;m='+imga[1]+''; 
				src_long =   src_small+'&amp;w='+my_width+'&amp;h='+my_height+'';
				s += "<br />";
				s += '<img src="'+src_small+'" alt="'+image_bay[i].id+'"    name="'+imga[2]+'" id="I_'+image_bay[i].id+'" ';
				s += 'onclick="insert_image(sentinel, \''+desc+'\', new slide_img(\''+src_long+'\', \''+imga[2]+'\', null , \''+image_bay[i].id+'\' ) );  sentinel.image_list(\''+desc+'\');  " />';
			}
		document.getElementById('image-bay').innerHTML = s+'<div id="click-down">SELECT</div>';
		document.getElementById('image-bay').style.width = "110px";
		
	}

	window.onload = function(){
		spider(false);
		add_event(document.getElementById("speed-dial"), "mousedown", engage);
		d_width = parseInt(document.getElementById(objID).style.width.replace("px", ""));
		load_this_dir(<?php echo $this->stored['spiderslider_direction'];?>);
		make_color_range(); 
		color_me(<?php echo $this->stored['red_col'];?>,'r'); 
		color_me(<?php echo $this->stored['green_col'];?>,'g'); 
		color_me(<?php echo $this->stored['blue_col'];?>,'b'); 
		manual_move(<?php echo $this->stored['spiderslider_speed'];?> , 'speed-dial' );
		sentinel.cache_imgs('<?php echo $this->activ_slider;?>');
		sentinel.slide_HTML('<?php echo $this->activ_slider;?>');
		sentinel.image_list('<?php echo $this->activ_slider;?>');
		paginator('<?php echo $this->int_start;?>' , '<?php echo $this->activ_slider;?>');
		spider(true);
	}	
	</script>
<?php }//fnc

	function get_this_image($id){
		global $wpdb;
		$attaches = $wpdb->prefix."postmeta";
		$query = "SELECT ".$attaches.".meta_value as 'Image'    
		FROM   ".$attaches."        
		WHERE ".$attaches.".meta_ID='".$id."' ";
		$my_img = $wpdb->get_results($query);
		return $my_img;
	}

	function get_images($COND, $bool){
		global $wpdb;
		$attaches = $wpdb->prefix."postmeta";
		$special_cond = (! $bool )?  " AND meta_value LIKE '%".trim($COND)."%' ORDER BY  ID DESC" : " ORDER BY ID DESC ";
		$query = "SELECT ".$attaches.".meta_value as 'Image',  ".$attaches.".meta_id as 'ID'    
		FROM   ".$attaches."        
		WHERE ".$attaches.".meta_key='_wp_attached_file' 
	". $special_cond." LIMIT ".$this->int_start." , ".$this->int_limit." ";		
		////". $special_cond."  ";
		$my_images = $wpdb->get_results($query);
		return $my_images;
	}

	function delete_slider( $sliderName ){
		$namedString = "spiderslider_".$sliderName;
		$n="";
		foreach($this->slider_list as $k => $v ){
			($v == $sliderName)? '': $n.= $v.'||';
		}
		update_option('spiderslider_named_list', $n);
		$test = delete_option(''.$namedString.'');
		if ( $test ) {   
			$this->slider_list = explode( "||", get_option( 'spiderslider_named_list' ) );
			$dump = array_pop($this->slider_list);
			$this->activ_slider = $this->slider_list[count($this->slider_list)-1];
		} 
	}

	function catch_me_changes(){
		if (isset($GLOBALS["HTTP_RAW_POST_DATA"])){ 
			$slide_data = $GLOBALS['HTTP_RAW_POST_DATA'];
			$temp = explode('||', $slide_data);
			$named_string = "spiderslider_".$temp[0];
			update_option($named_string, $slide_data);
			$n= '';
			$result = true;
			$result = in_array($temp[0], $this->slider_list);///does slider name exist
			if (! $result ){ 
				$this->slider_list[] = $temp[0];
			} 
			foreach($this->slider_list as $k => $v ){
				$n.= $v.'||';
			}
			update_option('spiderslider_named_list', $n);
		}
	}

	function image_bay( $all_images ){
		$this->push_images($all_images);
		echo '<div class="image-bay-wrap">';  
			echo '<div id="search-bay">';
				echo '<div id="start-here">START</div>';
				$totalPages = (count($all_images)-(count($all_images) % 10 ) )/10;
				echo '<form name="searcher" method="post" action="">
					<div class="search-wrap" >
						<input type="hidden" name="activ_slider" id="activ_slider" value="'.$this->activ_slider.'" />
						<input type="text" name="searchterm"  id="searchterm"  class="search-input"  value="'.$_POST[searchterm].'"  />
					</div>
					<div id="limits-wrap">
						<span>RANGE [start + #]  </span>

						<input type="text" id="int-start" name="int-start" value="'.$this->int_start.'"  />
						<input type="text" id="int-limit" name="int-limit" value="'.$this->int_limit.'"  />

						<input type="submit" name="get-results" id="get-results" value="go" />
					</div>				
				</form>';
				echo '<div id="results-wrap" >';		
				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div id="image-bay"  ondblclick="this.innerHTML=\'\'; this.style.width=\'0px\';"></div>';
		echo '<div id="image-list"></div>'; 			
	
	}

	function push_images($allImages){
?>
		<script>
		<?php foreach ( $allImages as $ind =>$obj ){?>
				image_bay.push(new image_slide('<?php echo $obj->Image;?>','<?php echo  $obj->ID;?> '));
		<?php }?>
		</script> <?php
	}

	function help_msg(){
		$u = plugins_url('class-spider.php?r=0&g=0&b=0&p=0', __FILE__);
		
 		
		$help_string = '<div id="helper" onclick="this.style.display=\'none\'"><strong class="righter">[close]</strong><h2>Help</h2>
		
		<p>The first time you open the admin page, a blank Spider Slider named &ldquo;default&rdquo; will be loaded.</p> 
<p><strong>creating</strong><br />
Enter a new name in the <span>red title field</span> to create a new blank slider of a different name.</p>
<p>
<strong>saving</strong><br />
When you make changes to settings on the Spider Slider admin page, your changes are automatically saved under the slider name shown in the title field.
</p><p>
Also, as you work in the admin page, a little black spider will appear when you make changes.<img src="'.$u.'" align="right" alt="spider"/> 
Clicking this spider refreshes the slider animation to show the effect of your latest changes.  
</p><p>
<br /><strong>selecting</strong><br />
Select images to use from the dark green sidebar at the left of the admin page. You can search images by title or, by selecting a numerical range. 
Images are bundled into groups of ten in order of most recent. Click a button to open up a group of ten.
</p><p>
<br /><strong>editing</strong><br />
When you click an image it is added to the slider and it will appear as a thumbnail next to the work area. There will also be a field for an optional hyperlink. 
When users click an image in the slider they will be sent to the location of the hyperlink. 
</p><p>
To remove an image from the slider, click the small thumbnail for it next to the work area. 
</p><p>
<br /><strong>using</strong><br />
Once you have configured your slider to your satisfaction, click the button for EMBED CODE. 
This will give you a special code that is inserted into a Wordpress Post, or Page. 
There is also a widget component available which you can use to place a Spider Slider in your theme&rsquo;s widget areas.
You can place multiple Spider Sliders on a page however no single one should appear twice. The results will be unreliable.
</p><p>
<br /><strong>deleting</strong><br />
Deleting a Spider Slider is really easy to do. Click the button for DELETE and it is done. There is no way to undo a delete so please be careful.
</p><p>
More information is available in the readme.txt file that is part of the plugin source code.</p>
<strong class="righter">[close]</strong>
</div>';
		return 	$help_string;
	}
	
	function license_msg(){
		$license_string = '<div id="licensor" onclick="this.style.display=\'none\'"><strong class="righter">[close]</strong>
		<h2>GNU General Public License (GPL) version 2</h2>';
		
		$license_string .='Spider Slider Image Carousel.
Copyright (C) 2012 Nick Andrews
<hr>
<p>
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or any later version.
</p><p>
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
</p><p>
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.</p>
<strong class="righter">[close]</strong></div>';
		
		return $license_string;
	}
	
	
	function about_msg(){
		$about_msg = '<div id="about-spiderslider" onclick="this.style.display=\'none\'"><strong class="righter">[close]</strong>
		<h2>Spider Slider Image Carousel</h2>';
	
	$about_msg .='
<p>Spider Slider comes with ABSOLUTELY NO WARRANTY;  This is free software, and you are welcome
to redistribute it.</p>
<p>The program was written by Nick Andrews and is intended to be a demonstration of programming using the Wordpress system.</p>
<p>Nick Andrews is a freelance software programmer based in downtown Toronto, Canada.</p>
<p>If you wish to comment on the program or,(yikes) report a problem, you can do so at Nick Andrews personal weblog: <a href="http://www.spiderdesign.ca/spiderslider/">www.spiderdesign.ca</a></p>
<strong class="righter">[close]</strong></div>';
	return $about_msg ;
	}

	
}//class

$ss = new Spider_Admin();
$ss->catch_me_changes(); 
$all_images =( ( isset($_POST['searchterm']) && (strlen($_POST['searchterm'])>0 ) ) )?  $ss->get_images($_POST['searchterm'], false):  $ss->get_images( false, true ) ;
$ss->handle_posts();
$ss->get_stored_data();

echo '<div class="slider-bay-wrap">';
	$ss->image_bay( $all_images );

	echo '<div class="slider-controls">';
		$ss->make_name_list();

		$ss->slide_dimensions();
		$ss->slide_shape();
		$ss->transition_selector();
		$ss->speed_selector();
		$ss->color_picker();
		$ss->embed_code();
	echo '</div>';
	echo '<div id="grid-space"></div>';
	echo '<div id="x-ruler" style="width:'.($ss->stored['spiderslider_width']+($ss->stored['spiderslider_border']*2)).'px;"></div>';
	echo '<div id="y-ruler" style="height:'.($ss->stored['spiderslider_height']+($ss->stored['spiderslider_border']*2)).'px;"></div>';
	echo '<div id="admin-slide-display"></div>';

	echo '<div  class="spider-admin-menu" onmouseout="document.body.style.cursor=\'default\';" onmouseover="document.body.style.cursor=\'pointer\';"  >'; 
		echo '<span onclick="document.getElementById(\'about-spiderslider\').style.display=\'block\'">about</span>';
		echo '<span onclick="document.getElementById(\'helper\').style.display=\'block\'">help</span>';
		echo '<span onclick="document.getElementById(\'licensor\').style.display=\'block\'">license</span>';
		echo '<div id="deBug"></div>';
	echo '</div>';
echo '</div>';
	$ss->add_spider_button();
	echo $ss->help_msg();
	echo $ss->about_msg();
	echo $ss->license_msg();
?>