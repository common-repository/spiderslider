<?php

class Slider_Pic{
	var $width;
	var $height;
	var $year;
	var $month;
	var $my_file_name;
	var $my_file_type;
	var $render_quality;
	var $ratio;
	var $image_types;
	var $header_types;
	var $header_string; 
	var $image_type_key;
	var $dir_locale;
	var $img_size;
	var $file_url;
	var $r;
	var $g;
	var $b;
	var $x; /// small image only

	function Slider_Pic(){
		( is_numeric($_GET["w"]) )? $this->width = $_GET["w"]:  $this->width  = 80;
		( is_numeric($_GET["h"]) )? $this->height = $_GET["h"]:  $this->height  = 80;
		( is_numeric($_GET["x"]) )?  $this->x = $this->width = $this->height = 50 : $this->x = false ;
		$this->r = $_GET["r"];
		$this->g = $_GET["g"];
		$this->b = $_GET["b"];
		$this->image_types = array( 0=>'jpeg','jpg', 'gif', 'png' );  
		$this->header_types = array( 0=>'image/jpeg', 'image/jpg', 'image/gif', 'image/png' );  
		$this->dir_locale = "../../../wp-content/uploads/";
		$this->render_quality= 100;
		if( isset($_GET["scr"]) ){
			( is_numeric( $_GET["y"]) )? $this->year = $_GET["y"]:  $this->year  = NULL;
			( is_numeric( $_GET["m"]) )? $this->month = $_GET["m"]: $this->month = NULL;
			$temp = explode ( ".", trim($_GET["scr"]));
			$this->my_file_name = $temp[0];
			$this->my_file_type = $temp[1];
		}//i
	}///constructor

  	function make_header(){
		$this->header_string = "Content-type: text/html";
		foreach ( $this->image_types  as $ind_key => $img_val){
			if ( $img_val ==   $this->my_file_type){ 
				$this->header_string = "Content-type: ".$this->header_types[$ind_key];
				$this->image_type_key = $ind_key;
			}//i
		}///f 
	}///func
   
	function valid_pick(){
		$this->file_url = $this->dir_locale.$this->year."/".$this->month."/".$this->my_file_name.".".$this->my_file_type;
		if( ( file_exists( $this->file_url ))&& (exif_imagetype($this->file_url))  ){
			$this->img_size = getimagesize( $this->file_url  );
			return true;
		}
		return false;
	}//func

	function re_size(){//sizes to center point
		if ( $this->img_size[0] > $this->img_size[1] ){ //// width > height
			$ratio =  $this->height / $this->img_size[1] ;
			$nuheight = $this->height;
			$nuwidth = round ( $this->img_size[0] * $ratio );
			$left_offset = (($nuwidth - $this->width)/2) * -1;
			$top_offset  = 0;
		}else{
			$ratio =  $this->width / $this->img_size[0] ;
			$nuwidth = $this->width;
			$nuheight = round ( $this->img_size[1] * $ratio );
			$left_offset = 0;
			$top_offset  = (($nuheight - $this->height)/2) * -1;
		}//i
		$sizer[] = $left_offset;
		$sizer[] = $top_offset;
		$sizer[] = $nuwidth;
		$sizer[] = $nuheight;
		return $sizer;
	}//fnc
  
  
}///class
$my_img = new Slider_Pic();
$myQ = $my_img->render_quality;
if ( $my_img->valid_pick() ){
	$my_img->make_header();
	header( $my_img->header_string );
	header(  "Content-Disposition: inline; filename='.$my_img->my_file_name.'" );
		$im = @imagecreate(100, 100)
	or die("Cannot Initialize new GD image stream");
	$im = imagecreatetruecolor( $my_img->width, $my_img->height );
	$sizer = $my_img->re_size();
	$background = imagecolorallocate ( $im , $my_img->r, $my_img->g, $my_img->b );
	imagefilledrectangle( $im, 0, 0, $my_img->width, $my_img->height, $background );
		switch( $my_img->image_type_key ){
			case '0';
			case '1';
				$image = imagecreatefromjpeg( $my_img->file_url );
				imagecopyresampled( $im, $image, $sizer[0], $sizer[1], 0, 0, $sizer[2], $sizer[3], $my_img->img_size[0], $my_img->img_size[1] );
				imagejpeg( $im, null,  $my_img->render_quality );
			break;
			case '2':
				$image = imagecreatefromgif( $my_img->file_url );
				imagecopyresampled( $im, $image, $sizer[0], $sizer[1], 0, 0, $sizer[2], $sizer[3], $my_img->img_size[0], $my_img->img_size[1] );					
				imagegif($im);
			break;
			case '3':
				$image = imagecreatefrompng( $my_img->file_url );
				imagecopyresampled( $im, $image, $sizer[0], $sizer[1], 0, 0, $sizer[2], $sizer[3], $my_img->img_size[0], $my_img->img_size[1] );
				imagepng($im, NULL,  round($my_img->render_quality/10)-1);///q must be 0-9 
			break;
		}//sw
	imagedestroy($im);
}//i
?>