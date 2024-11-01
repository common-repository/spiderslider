<?php
class Spider{
	var $norm_width;
	var $norm_height;
	var $header_string;

	function Spider(){
		$this->norm_width = 50;
		$this->norm_height = 85;
		$this->header_string = 'Content-Type: image/png';
		$this->pose	=	$_GET[p];
	}///constructor
}///class

$img =  new Spider();
header( $img->header_string );
header( "Content-Disposition: inline; filename=Spider" );
$im = @imagecreatefrompng( "transparent.png" );
$white = imagecolorallocate( $im, 255, 255, 255 );
$col = imagecolorallocate( $im, $_GET[r], $_GET[g], $_GET[b] );
imagecolortransparent( $im, $white );

///spider head thorax abdomen shape
$points = array( 23, 27, 22, 27, 22, 32, 21, 33, 21, 35, 21, 38, 20, 41, 17, 47, 17, 50, 18, 53, 20, 55, 23, 57, 23, 
	57, 25, 58, 25, 57, 28, 55, 31, 52, 32, 49, 32, 46, 31, 44, 28, 41, 28, 38, 27, 35, 27, 33, 26, 32, 26, 30,
	26, 27 , 25, 27 , 25, 28 , 25, 29 , 25, 30 , 24, 29 , 24, 31 , 24, 31 , 24, 29 , 23, 30 , 22, 28 , 23, 28 , 23, 27 );
imagefilledpolygon( $im, $points, (count($points)/2), $col );

///8 spiderlegs

imageline($im,18,23,21,32,$col); 

imageline($im,15,28,21,34,$col);

imageline($im,15,39,21,37,$col);

imageline($im,12,48,21,39, $col);

imageline($im,36,47,28,39, $col);

imageline($im,34,40,27,37, $col);

imageline($im,33,29,27,35, $col);

imageline($im,30,23,27,33, $col);


switch ($img->pose){
	case "0":
		imageline($im,12,1,18,23, $col); ///L1
		imageline($im,6,11,15,28,$col); ///L2
		imageline($im,1,52,15,39,$col);///L3
		imageline($im,7,65,12,48, $col);///L4
		imageline($im,41,66,36,47, $col); ///R4
		imageline($im,47,51,34,40, $col); ///R3
		imageline($im,42,11,33,29, $col);///R2
		imageline($im,35,1,30,23, $col);//R1
	break;
	case "1":
		imageline($im,14,3,18,23, $col); ///L1
		imageline($im,7,8,15,28,$col); ///L2
		imageline($im,2,51,15,39,$col);///L3
		imageline($im,8,66,12,48, $col);///L4
		imageline($im,38,69,36,47, $col); ///R4
		imageline($im,46,50,34,40, $col); ///R3
		imageline($im,48,10,33,29, $col);///R2
		imageline($im,31,4,30,23, $col);//R1
	break;
	default:
	break;	
}



imagepng($im, null, 1);
?> 

