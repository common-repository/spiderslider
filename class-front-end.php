<?php

class  Front_End{
	var $sliders;
	var $starts;
	var $ends;
	var $spider_content;
	var $start;
	var $finish;
	var $place;
	var $stored;
	var $url;
	
	function Front_End(){
		$this->sliders 			= array();
		$this->starts  			= array();
		$this->ends    			= array();
		$this->stored   		= array();
		$this->spider_content	='';
		$this->start 			= '<!--spiderslider name=' ;///length 22
		$this->finish			= '-->';///length 3
		$this->place			= null;
		$this->url      		= plugins_url('spiderslider');
	}///class

	function parse_content( $content ){///loop content and populate position mapping arrays
		$test = ( $this->place < strlen($content) ) ?  strpos( $content, $this->start , $this->place): null ;///test for existence
		if ( $test ){ 
			$word_start = strpos( $content, $this->start, $this->place )+ strlen($this->start) +1  ;//added int for "
    	    $word_length = strpos( $content, $this->finish, $this->place ) - $word_start -1 ;///subtract int for "
		    $word = substr( $content, $word_start, $word_length );
			$holder_start = strpos( $content, $this->start, $this->place );
			$holder_end = strpos( $content, $this->start, $this->place ) + $word_length + strlen($this->start) + strlen($this->finish) +2 ; ///added int for "" ""
			$this->sliders[] = $word;
    		$this->starts[] = $holder_start; ///holder
        	$this->ends[] = $this->place = $holder_end ;///holder end
			$this->parse_content($content);///recurse
		}
		return;
	}//fnc

	function insert_holders($content){
		krsort ($this->sliders);
		foreach ( $this->sliders  as $k => $v ){
			$replace = '<div id="spiderslider_'.$this->sliders[$k].'"></div>';	
			$content = substr_replace ( $content, $replace, $this->starts[$k], $this->ends[$k] - $this->starts[$k]  );
		    $this->stored[$k] = $this->get_saved_data( $this->sliders[$k] );
		}///f
		
		
		
		
	 return $content;
	}//fnc

	function get_saved_data( $slider_name ){
		$use_this =  'spiderslider_'.$slider_name ; 
		$stored =  explode ( '||', get_option(   $use_this  ) );
		return  $stored ;
	}

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
	
	function add_new_node_js( $slider_name ){
		$index = array_search($slider_name,  $this->sliders);
		$data =  $this->stored[$index]; 
		$dimensions  = explode ('|', $this->stored[$index][1]); ///slider params
	 	$im = explode('|', $this->stored[$index][2]);///slider images 
     	$d = array_pop($im); //drop last space
	 	for( $i = 0; $i < count($im); $i++ ){
			$im[$i] = ( $i % 2 )?  str_replace ( 'http://', '',$im[$i] )  :   str_replace ( 'I_', '', $im[$i] );  ///clean content
		}//f
		
		$j_string = "
			<script>
			var slida_".$slider_name." = new Array();
		";

		for($i = 0 ; $i < count($im); $i= $i+2 ){
				$img = $this->get_this_image($im[$i]);
				$name = explode ('/', $img[0]->Image);
				$imgUrl = $this->url.'/class-slider-pic.php?scr='.$name[2].'&y='.$name[0].'&m='.$name[1].'&w='.$dimensions[2].'&h='.$dimensions[3].'&r='.$dimensions[4].'&g='.$dimensions[5].'&b='.$dimensions[6];
				$j_string .= "slida_".$this->sliders[$index].".push( new slide_img( '".$imgUrl."', '".$name[2]."','".$im[$i+1]."',".$im[$i]." ) );
				";
		}//f
		$j_string .= "
			try{
				sentinel.add_nodes(new create_node( '".$this->sliders[$index]."', '".$dimensions[2]."', '". $dimensions[3]."', '".$dimensions[4]."', '".$dimensions[5]."', '".$dimensions[6]."', '".$dimensions[8]."', '".$dimensions[9]."', '".$dimensions[1]."', '".$dimensions[0]."', slida_".$this->sliders[$index].") );
			}catch(e){
				var sentinel = new create_node( '".$this->sliders[$index]."', '".$dimensions[2]."', '". $dimensions[3]."', '".$dimensions[4]."', '".$dimensions[5]."', '".$dimensions[6]."', '".$dimensions[8]."', '".$dimensions[9]."', '".$dimensions[1]."', '".$dimensions[0]."', slida_".$this->sliders[$index].");
			}
			sentinel.slide_HTML2('".$this->sliders[$index]."');
			</script>
			";
		return 	$j_string;
	}

	function insert_javascript(){
		if (  count($this->stored ) > 0 ) { 
			$j_string = "
			<script>
					if( typeof(sentinel) == 'undefined'){
						var blank_slida = new Array();
						var sentinel = new create_node( 'my_sentinel', 0, 0, 0, 0, 0, 0, 0, 0, 0, blank_slida );
					}
			</script>";		
			}
			for ($j = 0; $j < count($this->sliders); $j++ ){
				if  ( $this->stored[$j][1] ) { 
					$j_string.= $this->add_new_node_js( $this->sliders[$j]);
				} 
		}
		return $j_string;
	}///fnc
}///class

?>