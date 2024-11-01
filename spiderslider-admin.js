var colpad = 20;///padding on admin color bar

////transition direction input
var directions = new Array('dir0', 'dir1', 'dir2', 'dir3', 'dir4');/// dir
var dir_classes    = new Array('up', 'right', 'down', 'left', 'fade'); 
function   hh_this ( obj ){
	for ( var i = 0; i < directions.length; i++ ){
		( obj.id == directions[i] ) ? change_input( obj, i ): document.getElementById(directions[i]).className='arrow-'+dir_classes[i]; 
	}
} //fnc
function change_input( obj, i ){
    var updateTransOBJ = document.getElementById('spiderslider_direction');
    obj.className='arrow-'+dir_classes[i]+' on';
    updateTransOBJ.value = my_direction = i;
}//fnc
function load_this_dir(num){
    for (var i = 0 ; i < directions.length; i++){
       (num == i)? hh_this ( document.getElementById( directions[i]) ): num=num;
    }
}//fnc

function post_data( str ){
	if ( str ) {
		try{
			var ajax = new XMLHttpRequest();
			ajax.open("POST", ssite_url+'/wp-admin/options-general.php?page=spider_slider-options',false);
			ajax.setRequestHeader('Content-Type', 'application/upload');
			ajax.send( str );  
		}
	    catch(e){
	       msgMe('error saving', true);
 	    }	 
	 }
 }//fnc

create_node.prototype.slide_HTML = function(desc){
	if( this.desc == desc ){
		var tml  = '<div id="'+this.desc+'_Show" style="width:'+this.width+'px; height:'+this.height+'px; z-index:11; position:relative;top:0px; left:0px; ';  
		tml += ' border: solid transparent '+this.border+'px;  border-radius: '+this.brdr_radius+'px;';
		tml += 'background: transparent; position: relative; top: 0px; left:0px; padding:0px;  clip: rect(0px, '+(this.w + this.border)+'px, '+(this.h + this.border)+'px, 0px); overflow: hidden;"  ';
		tml += 'onmouseout="document.body.style.cursor=\'default\';"  onmouseover="document.body.style.cursor=\'pointer\';">';
		if ( this.imgs[0] ){
			tml += '<img src="'+this.imgs[0].source+'" alt="Slider Image" id="'+this.desc+'_Cover1"   style="opacity: 1; filter:alpha(opacity=100);position: absolute; top: 0px; left:0px; z-index:99; max-width: 100%;';
			tml += 'border-radius: '+this.brdr_radius+'px;  border: solid transparent 0px; ';
			tml += 'clip: rect(0px,'+this.w+'px, '+this.h+'px, 0px); overflow:hidden"  onclick="give_link(sentinel,\''+this.desc+'\'  );" />';
			tml += '<img src="'+this.imgs[0].source+'" alt="Slider Image" id="'+this.desc+'_Cover0"   style="position: absolute; top: 0px; left:0px;z-index:99;max-width: 100%; ';
			tml += 'border-radius: '+this.brdr_radius+'px;  border: solid transparent 0px; ';
			tml += 'clip: rect(0px,'+this.w+'px, '+this.h+'px, 0px); overflow:hidden" onclick="give_link(sentinel,\''+this.desc+'\'  );" />';
		}
		tml += '</div>';
		tml += '<div id="'+this.desc+'_link_string" class="link-string"></div>';	
		document.getElementById('admin-slide-display').innerHTML = tml;
		var outer_w = parseInt(this.width) + parseInt((this.border*2));
		document.getElementById('admin-slide-display').style.width = +outer_w+'px';
		this.place_triggers(link_dot_on, link_dot_off); 
	}else{
		document.getElementById('admin-slide-display').innerHTML = 'pick images';
	}
	if(this.left){this.left.slide_HTML(this.left.desc);} 
	if(this.right){this.right.slide_HTML(this.right.desc);} 
}//fnc


function insert_image(tree, desc, img ){
	spider(false);
	if ( tree ){
		if ( tree.desc == desc ) {  
			tree.imgs.push(img); 
			tree.track_options(desc);
			tree.image_list(desc);
		}
		insert_image(tree.left, desc, img);
		insert_image(tree.right, desc, img);
	}
}//fnc
	
function update_img_link(tree, desc, val, i ){
	spider(false);
	if ( tree ){
		if ( tree.desc == desc ) { 
			var test_val = null;
			if( ( val == test_val)||( val =='') ){ 
				val = '..';  
			}
			tree.imgs[i].lnk = val; 
			tree.track_options(desc);
		}
		update_img_link(tree.left, desc, val, i);
		update_img_link(tree.right, desc, val, i);
	}
}//fnc


function update_param ( tree, desc, val, prop){
	if ( tree ){
		if (tree.desc == desc ) {
			for (var key in tree) {
				if ((tree.hasOwnProperty(key))&&(key == prop)) {
					tree[key] = val; 
					spider(false)
				}
			}
			tree.track_options(desc);
		}		 
		update_param(tree.left, desc, val, prop);
		update_param(tree.right, desc,  val, prop);
	}
}//fnc

function remove_img (tree, id, desc){
	spider(false);
	if ( tree.desc == desc ) { 
		for (var i = 0; i <= tree.imgs.length; i++){
			if ( tree.imgs[i].id == id ) { 
				tree.imgs.splice(i, 1);
				if ( i == 0){ 
					this.imgs =  new Array();
				}
				tree.track_options(desc);
				clearTimeout( tree.timer );
				self = tree;
				tree.timer = window.setTimeout(function() { self.slider_carousel( self.desc+'_Cover0', self.desc+'_Cover1'  ); }, 0);
		    }
			tree.image_list(desc);
		}
		remove_img (tree.left, id, desc);
		remove_img (tree.right, id, desc);
	}
}//fnc

create_node.prototype.image_list = function(desc){
	if( this.desc == desc ){
		var lnk; 
		var s= '';
		for(var i = 0 ; i < this.imgs.length; i++){
			lnk = ( (this.imgs[i].lnk == null)||(this.imgs[i].lnk == '..') )? '': this.imgs[i].lnk ; 
			s+='<div class="img-list" style="top:'+(i*96)+'px;">';
			s+='<input type="image" src="'+this.imgs[i].source+'&x=1"  id="I_'+this.imgs[i].id+'" name="I_'+this.imgs[i].n+'" onclick="remove_img(sentinel, \''+this.imgs[i].id+'\', \''+this.desc+'\');" ';
			s+= 'onmouseout="document.body.style.cursor=\'default\';"  onmouseover="document.body.style.cursor=\'pointer\';" />';
			s+='<input type="text" name="Link_'+this.imgs[i].id+'" value="'+lnk+'" onchange="update_img_link(sentinel, \''+this.desc+'\', this.value,\''+i+'\' );" placeholder="http://"/>';
	

			if(navigator.appName =="Microsoft Internet Explorer"){
				s+='<span>Enter URL</span>';
			}
			s+='</div>';
		}
	 ////// 	s = '&nbsp;'+this.imgs.length +'Slider Image'+((this.imgs.length > 1)? 's': '') +' '+ s + '<br />';
			///document.getElementById('image_list').innerHTML = 'this is a test';
		document.getElementById('image-list').innerHTML = s;
		document.getElementById('image-list').style.height = (this.imgs.length * 96 )+"px";
			
		return; 
	}
	if ( this.left ){ this.left.image_list(desc); }
	if ( this.right){ this.right.image_list(desc); }
	return; 
}//fnc 
  
create_node.prototype.track_options = function(desc){
     if ( this.desc == desc ) { 
	     var sParams  =desc+'||'+this.speed+'|'+this.dir+'|'+this.width+'|'+this.height+'|'+this.red+'|'+this.green+'|'+this.blue+'|'+(rgb_to_hex(this.red,this.green,this.blue))+'|'+this.border+'|'+this.brdr_radius;
		 var sContent ='||';
         var lnk='';
		  if ( this.imgs.length > 0 ){
   		        for (var i = 0 ; i < this.imgs.length; i++ ){
		            lnk = ( this.imgs[i].lnk==null )?  '..' : this.imgs[i].lnk  ;
                    sContent+='I_'+this.imgs[i].id+'|'+lnk+'|';
		        }//F
		  }
        post_data(sParams+sContent);
	 }//I
	 if ( this.left  ){ this.left.track_options(desc);  }
	 if ( this.right ){ this.right.track_options(desc); }
}//fnc 

  
function  image_slide(img, id){
	this.img = img;
	this.id = id;
}///fnc

function refresh_img( w, h, r, g, b, str ){
	var i = str.split('&');
	i[3]= "w="+w;
	i[4]= "h="+h;
	i[5]= "r="+r;
	i[6]= "g="+g;
	i[7]= "b="+b;
	var s=''; 
	for (var j = 0 ; j < i.length; j++){
		s += ( j < (i.length-1) )? i[j]+"&":  i[j];
	}
	return s;
}//fnc



create_node.prototype.restart = function(desc){
	if( this.desc == desc ) { 
		clearTimeout( this.timer );
		spider( true );
		document.getElementById('admin-slide-display').innerHTML = '';
		document.getElementById('image-bay').innerHTML = '';
		document.getElementById('image-bay').style.width = '0px';
		document.getElementById('image-list').innerHTML = '';
		document.getElementById('x-ruler').style.width= parseInt(this.width)+ (this.border*2) +"px";
		document.getElementById('y-ruler').style.height= parseInt(this.height)+ (this.border*2) +"px";
		for( var i = 0 ; i < this.imgs.length; i++)  {
			this.imgs[i].source= refresh_img(this.width, this.height, this.red, this.green, this.blue, this.imgs[i].source);
		}
		this.image_list(desc);
		this.marq_index = 0;
		this.slide_HTML(desc);
		var self = this;
		this.timer = window.setTimeout(function() { self.slider_carousel( self.desc+'_Cover0', self.desc+'_Cover1'  );}, 0 );
	}
	if( this.left ){ this.left.restart(desc); }
	if( this.right ){ this.right.restart(desc); }
}//fnc  

function give_embed(name, url){
	var slider_embed = window.open("","Spiderslider "+name,"status,height=340,width=510");
	var img1 = url+'/grid.png';
	var pop_text = '<html><head>';
		pop_text += '<title>'+name+'</title>';
		pop_text += '<style type="text/css">';
		pop_text += 'body {	font-size: 12pt; font-family:Geneva, Arial, Helvetica, sans-serif; background: #ffffff; width: 510px;}';
		pop_text += '.grid { position: absolute; height: 340px; width: 510px; top: 0px; right: auto; background-image: url('+img1+'); opacity: 0.2; alpha:(opacity=20); z-index:-9999;}';
		pop_text += '.notes { margin: 0 auto; padding: 0px; line-height: 20px; font-weight: 400; text-align: left; width: 480px;}';
		pop_text += '.notes ul{ list-style-type:none; display: block;}';
		pop_text += '.notes li{ text-indent: 4px;   font-weight: 600; font-size: 11pt;}';
		pop_text += '.notes pre{ display: block; padding: 10px; font-size: 11pt; font-weight: 400; font-family:"Courier New", Courier, monospace; background: #ffffff; color: #EE3940; }';
		pop_text += '</style>';
		pop_text += '</head><body><div class="grid"></div><div class="notes">';
			pop_text += '<h2>Spidersliders in Wordpress Posts</h2>';	
			pop_text += '<ul>How to Insert';
				pop_text += '<li>1. Open up a Wordpress post, or page, for editing.</li>';
				pop_text += '<li>2. Select the grey tab, on the right, for HTML editing.</li>';
				pop_text += '<li>3. Select and copy the code below.<pre>&lt;!--spiderslider name="'+name+'"--&gt;</pre></li>';
				pop_text += '<li>4. Paste the code into your post where it should appear.</li>';
				pop_text += '<li>5. Save your changes and view the post.</li>';
		pop_text += '</ul></div></body></html>';
	slider_embed.document.write( pop_text );
}  




/* code from http://www.javascripter.net/faq/..*/
function rgb_to_hex(R,G,B) {
	return to_hex(R)+to_hex(G)+to_hex(B)
}
function to_hex(n) {
	n = parseInt(n,10);
	if (isNaN(n)) return "00";
	n = Math.max(0,Math.min(n,255));
	return "0123456789ABCDEF".charAt((n-n%16)/16)  + "0123456789ABCDEF".charAt(n%16);
}
function hex_to_r(h) {
	return parseInt((cut_hex(h)).substring(0,2),16)
}
function hex_to_g(h) {	
	return parseInt((cut_hex(h)).substring(2,4),16)
}
function hex_to_b(h) {
	return parseInt((cut_hex(h)).substring(4,6),16)
}
function cut_hex(h) {
	return (h.charAt(0)=="#") ? h.substring(1,7):h
}
/******************************************/

function decode_hex( h ){
	color_me( hex_to_b(h), 'b');
	color_me( hex_to_g(h), 'g');
	color_me( hex_to_r(h), 'r');
}


function validate_num(num){
	num = parseInt(num);
	if ( num > 255 ){ 
		num = 255;
	}
	if ( num < 0 ){ 
		num = 0;
	}
	return num;
}//fnc

function color_me(num, rgb){
	num = validate_num(num);
	var red_mk =  document.getElementById('r-marker') ;	
	var green_mk =  document.getElementById('g-marker') ;
	var blue_mk =  document.getElementById('b-marker') ;
	var inc = 255- parseInt(num) + colpad;
	var label;
	switch(rgb){
		case 'r':
			r = num;
			if ( red_mk.style.posLeft){ red_mk.style.posLeft = inc ; }
			else if ( red_mk.style.left){ red_mk.style.left =  inc +"px"; }  
			label = 'red';
		break;
		case 'g':
			g = num;
			if ( green_mk.style.posLeft){ green_mk.style.posLeft = inc ; }
			else if ( green_mk.style.left){ green_mk.style.left =  inc +"px"; }  
			label = 'green'; 
		break;
		case 'b':
			b = num;
			if ( blue_mk.style.posLeft){ blue_mk.style.posLeft = inc ; }
			else if ( blue_mk.style.left){ blue_mk.style.left =  inc +"px"; }  
			label = 'blue';
		break;
		default:
		;
	}
	document.getElementById('color-bar').style.background = marq_color = 'rgb('+r+','+g+','+b+')';
	document.getElementById('red_col').value = r;
	document.getElementById('green_col').value = g;
	document.getElementById('blue_col').value = b;
	var hex =  rgb_to_hex(r,g,b);
	document.getElementById('hex-code').value = "#"+hex;
	update_colors(label, num); 
}//fnc	
 
function make_color_range(){
	var i;
	var r_string="";
	var g_string="";
	var b_string="";
	var scale = 0;
	for (i = 255 ; i > 0 ; i--){
		scale++;
		r_string +="<div class=\"pointable\" style=\"background:rgb("+i+",0,0);   position:absolute; left:"+(scale+colpad)+"px; top:11px;  \" onclick=\"color_me( "+i+" , 'r' )\"></div>";
		g_string +="<div class=\"pointable\" style=\"background:rgb(0,"+i+",0);   position:absolute; left:"+(scale+colpad)+"px; top:22px;  \" onclick=\"color_me( "+i+" , 'g' )\"></div>";
		b_string +="<div class=\"pointable\" style=\"background:rgb(0,0,"+i+");   position:absolute; left:"+(scale+colpad)+"px; top:33px;  \" onclick=\"color_me( "+i+" , 'b' )\"></div>";
	}
	r_string += "<div  id=\"r-marker\"   style=\" top:11px; left:140px;\" ></div>";
	g_string += "<div  id=\"g-marker\"   style=\" top:22px; left:140px;\" ></div>";
	b_string += "<div  id=\"b-marker\"   style=\" top:33px; left:140px;\" ></div>";
	var fill_obj = document.getElementById('color-bar');
	fill_obj.style.width="295px";
	fill_obj.style.height="52px";
	fill_obj.innerHTML += r_string + b_string + g_string;
}//fnc 









var l_max, r_max, d_width;
var dragged_elem; 
var offset_x = 0;
var offset_y = 0;
var drag_class = "dragger";
var objID = "speed-dial";
var update_speed = "spiderslider_speed";
  


function add_event(elem, evt_type, func){
	if (elem.addEventListener){
		elem.addEventListener(evt_type, func, false);
	}else if (elem.attachEvent){
		elem.attachEvent("on" + evt_type, func);
	}else{
		elem["on" + evt_type] = func;
	}
}//fnc
	  
function removeEvent(elem, evt_type, func) {
	if (elem.removeEventListener) {
		elem.removeEventListener(evt_type, func, false);
	}else if (elem.detachEvent) {
		elem.detachEvent("on" + evt_type, func);
	}else{
		elem["on" + evt_type] = null;
	}
  }//fnc
 
function engage(evt) {
	evt = (evt) ? evt : (window.event) ? window.event : "";
	evt.cancelBubble = 'true';
	var targ_elem = (evt.target) ? evt.target : evt.srcElement;
	if( targ_elem.className == drag_class ){
		while ( targ_elem.id != objID  && targ_elem.parentNode ){
			targ_elem = targ_elem.parentNode;
		}
		if( targ_elem.id == objID){
			d_width = parseInt(targ_elem.style.width.replace("px", ""));
			l_max = get_x_pos(targ_elem.parentNode.id);
			r_max = l_max + parseInt(targ_elem.parentNode.style.width.replace("px", ""));
			speed_numb( targ_elem.id );
			add_event(document, "mouseup", release);
			add_event(document, "mousemove", drag_it);
			dragged_elem = targ_elem;
			if( evt.pageX ){
				offset_x = evt.pageX - targ_elem.offsetLeft;
				move_target((evt.pageX+(d_width/2)), true,  dragged_elem);
			}else{
				offset_x = evt.offset_x - document.body.scrollLeft+100;
				if (navigator.userAgent.indexOf("Win") == -1) {
					offset_x += document.body.scrollLeft;
				}
				move_target((evt.clientX+(d_width/2)), false,   dragged_elem);	
			}
		return false;
		}
	}
 }//fnc
     
function get_x_pos( obj ){
	var xx =  0;
	var offset_pointer = document.getElementById( obj ); // cElement;
	while ( offset_pointer ) {
		xx += offset_pointer.offsetLeft ;
		offset_pointer = offset_pointer.offsetParent;
	}
	if ( navigator.userAgent.indexOf("Mac" ) != -1 &&
		typeof document.body.leftMargin != "undefined") {
		xx += document.body.leftMargin;
	}
	return parseInt( xx );
}//fnc 
 
function get_y_pos( obj ){
	var yy =  0;
	var offset_pointer = document.getElementById( obj ); // cElement;
	while ( offset_pointer ) {
		yy += offset_pointer.offsetTop ;
		offset_pointer = offset_pointer.offsetParent;
	}
	if ( navigator.userAgent.indexOf("Mac" ) != -1 &&
		typeof document.body.topMargin != "undefined") {
		yy += document.body.topMargin;
	}
	return parseInt( yy );
}//fnc  
 
 
function speed_numb( obj ){
	document.getElementById( update_speed ).value = Math.abs( get_x_pos( obj )+(d_width/2) - ( l_max ) );
}//fnc


function release( evt ){
	try{
		removeEvent(document.getElementById("speed-dial"), "mousedown", engage);
		dragged_elem  = null;
		removeEvent(document, "mouseup", release);
		removeEvent(document, "mousemove", drag_it);
		speed_updater();
	}catch( e ){
		window.alert('Error on release of mouse drag');
	}finally{
		add_event(document.getElementById("speed-dial"), "mousedown", engage);
	}
}//fnc

function drag_it( evt ) {
	evt = (evt) ? evt : (window.event) ? window.event : "";
	var targ_elem = (evt.target) ? evt.target : evt.srcElement;
	if (dragged_elem) {
		targ_elem = dragged_elem;
		if (targ_elem.className == drag_class) {
			while( targ_elem.id != objID && targElem.id != targ_elem.parentNode) {
				targ_elem = targ_elem.parentNode;
			}
			if ( get_x_pos( targ_elem.id ) <= (l_max-(d_width/2)) ) {
				var mi = (l_max) + d_width*2;
				(evt.pageX)? move_target(mi, true,  targ_elem) :  move_target(mi, false,  targ_elem);
				removeEvent(document, "mousemove", drag_it);
				return;
			}else if( get_x_pos( targ_elem.id ) >= (r_max-(d_width/2)) ){
				var mx = (r_max) - d_width*2;
				(evt.pageX)? move_target(mx, true,  targ_elem) :  move_target(mx, false,  targ_elem);
				removeEvent(document, "mousemove", drag_it);
				return;
			}
			(evt.pageX)? move_target(evt.pageX, true,  targ_elem) :  move_target(evt.clientX, false,  targ_elem);
		}
	}
}//fnc
  
function move_target(dist, px, obj){
	var t = (px)? obj.style.left =  dist - offset_x-(d_width/2)+ "px" :  obj.style.posLeft =  dist - offset_x-(d_width/2) ;
	speed_numb( obj.id );
}//fnc
    
function manual_move( num , obj_id ){
	var n = parseInt(num);
	var obj = document.getElementById( obj_id );
    if ( n < 0 ){ 
		n = 0; 
	}
    if ( n > 100 ){ 
		n = 100;
	}
	document.getElementById(update_speed).value = n;
	d_width= (! d_width ) ?  parseInt(document.getElementById( obj_id).style.width.replace("px", "")): d_width;	
	if ( obj.style.left ) {
		obj.style.left = (((d_width/2)  - n)*-1)+"px";
	}else{
		obj.style.posLeft = ((d_width/2)  - n)*-1;
	}
}//fnc


function paginator( start , desc ){
	start = parseInt(start)
 	var st, lm;
	var s = image_bay.length+' Results Found';
	var low, high; 
	var offset = 0;
	if ( image_bay.length > 0 ){
		var max_pages = (image_bay.length-(image_bay.length%10))/ 10;
		for( var j = 0; ( j < 10) ; j++ ){
			if	( (start - int_start) > 0 ){///counting from incremental 100s
				if ( j < ( max_pages - ((start - int_start)/10) ) ){
					low = ((j*10)+parseInt(start))+1; 
					high = low+9;
					offset++;
					s += '<input type="submit" name="low_limit'+(j+(start - int_start))+'" id="low_limit'+(j+(start - int_start))+'" value="'+low+'-'+high+'" onclick="show_bay_images(\''+(j+((start - int_start)/10))+'\',  \''+desc+'\'); this.style.background=\'#000000\';" /><br />';
				}
			}else if ( j < max_pages ){///counting from 0
				low = ((j*10)+parseInt(start))+1; 
				high = low+9;
				s += '<input type="submit" name="low_limit'+j+'" id="low_limit'+(j)+'" value="'+low+'-'+high+'" onclick="show_bay_images(\''+(j)+'\',  \''+desc+'\'); this.style.background=\'#000000\';" /><br />';
				offset++;
			}		
		}
		if (( (image_bay.length - start) < 100 ) && (image_bay.length % 10 > 0 )){///have modulo
			low = (start>0)? ((offset)*10)+start+1 : ((offset)*10)+1 ;
			high = low+ ( image_bay.length % 10 )-1;
			s += '<input type="submit" name="low_limit'+( image_bay.length -(image_bay.length % 10) )+'" id="low_limit'+( image_bay.length -(image_bay.length % 10) )+'" value="'+low+'-'+high+'" onclick="show_bay_images(\''+(( image_bay.length -(image_bay.length % 10) )/10)+'\',  \''+desc+'\'); this.style.background=\'#000000\';" /><br />';
		}
		s +='<br />';
		if (( start > 0 )&&( start > int_start )){
			s += '<input type="submit"  name="decrementer"  class="pg-decrementer"   value="Less"  onclick="paginator('+(start-100)+', \''+desc+'\');"  />';
		} 
		if ( (start+100) < ( image_bay.length  + int_start ) ) {	
			s += '<input type="submit"  name="incrementer"  class="pg-incrementer"   value="More"  onclick="paginator('+(high)+', \''+desc+'\');"  />'; 
		}
	}
	window.document.getElementById('results-wrap').innerHTML = s;
}//fnc



function move_spider(){
	add_event( document.body, "mousemove",  check_coords );
	add_event( document.getElementById('reset-opts'), "mouseover", free_spider );
}
function free_spider(evt){
	if ( sp_timer ) { clearTimeout( sp_timer ); }
	removeEvent(document.body, "mousemove", check_coords);
}

var sp_timer;

function spider_run(targ_x, targ_y, pos_x, pos_y ){
	var spidey = document.getElementById('reset-opts');
	if ( sp_timer ) { clearTimeout(sp_timer); }
		if( ( pos_x != targ_x ) || ( pos_y != targ_y ) ){
			if (  pos_x > targ_x ){  pos_x = pos_x -( parseInt(pos_x - targ_x)/4 ); } 
			if (  pos_x < targ_x ){  pos_x = pos_x +( parseInt(targ_x - pos_x)/4); } 
			if (  pos_y > targ_y ){  pos_y = pos_y -( parseInt(pos_y - targ_y)/4 ); } 
			if (  pos_y < targ_y ){  pos_y = pos_y +( parseInt(targ_y - pos_y)/4); } 

			move_obj(spidey, pos_x, pos_y);
			sp_timer = setTimeout("spider_run("+targ_x+", "+targ_y+", "+pos_x+", "+pos_y+")",0);
		}
	return;	
}

function check_coords(evt){
	var spidey = document.getElementById('reset-opts');
	var left_offset = get_x_pos('reset-opts') - parseInt(spidey.style.left.replace("px", "")) + (parseInt(spidey.style.width.replace("px", ""))/2);
	var top_offset	= get_y_pos('reset-opts') - parseInt(spidey.style.top.replace("px", "")) + (parseInt(spidey.style.height.replace("px", ""))/2);
	removeEvent(document.body, "mousemove", check_coords);
	sp_timer = setTimeout("spider_run("+ (evt.pageX-left_offset )+", "+(evt.pageY-top_offset)+", "+get_x_pos('reset-opts')+",  "+get_y_pos('reset-opts')+")", 1000 );
}

function spider( bool ){
	free_spider();
	 if ( ! bool ) { move_spider(); }
	var s = document.getElementById('reset-opts').src.split('?');///spider
	document.getElementById('reset-opts').src = ( bool )? s[0]+'?r=225&g=255&b=255&p=0': s[0]+'?r=0&g=0&b=0&p=1';
	document.getElementById('reset-opts').style.left="100px";
	document.getElementById('reset-opts').style.top ="-500px";
}//fnc
