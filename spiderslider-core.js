var link_dot_on  = "slider-on";
var link_dot_off = "slider-off";
var me = test_client_speed();

function test_client_speed()
{
	var start_now = new Date();
	var t1 = start_now.getTime() ;
	for( var i=0; i<1000000; i++ ){   }//loop one million times
	var stop_now = new Date();
	var t2 = stop_now.getTime();
  	return (t2-t1)+1;
 }
 
function switch_index(ci, maxn){
	if  ( ci < maxn ){ 
		ci++;
	}else{
		ci = 0;
	} 
	return ci;
}

function move_obj(obj, x , y){
	if ( ! obj.style.posTop){
		obj.style.left = x+"px";
		obj.style.top = y+"px";
  	}else{
		obj.style.posLeft = x;
		obj.style.posTop = y;
	}
}

function reset_image(objID){
	if (navigator.appName !="Microsoft Internet Explorer"){
		window.document.getElementById(objID).style.opacity = 1;
	}else{
		window.document.getElementById(objID).style.filter = "alpha( opacity=100 )";
	}
}
 
function create_node(desc, w, h, r, g, b, br, brad, direction, speed, arr){
	this.desc        = desc;
	this.marq_index   = 0;
	this.width       = parseInt(w);
    this.height      = parseInt(h);
    this.red         = parseInt(r);
    this.green       = parseInt(g);
    this.blue        = parseInt(b);
	this.border      = parseInt(br);
	this.brdr_radius = parseInt(brad);
	this.left        = false;
	this.right       = false;
	this.dir         = parseInt(direction);
	this.speed       = parseInt(speed);
	this.imgs        = arr;
	var self         = this;
	this.timer       = window.setTimeout(function() { self.slider_carousel( self.desc+'_Cover0', self.desc+'_Cover1'  ); }, (arr.length*1000)+1000  );
}//fnc

function slide_img(sr, n, l, id){
	this.source  = sr;
	this.name   =n;
	this.lnk=l;
	this.id = id;
}//fnc

create_node.prototype.slide_HTML2 = function(desc){
	var test_obj = document.getElementById( desc+'_Show');
	if( ( this.desc == desc ) && ( typeof(sentinel) != 'undefined') && ( this.desc != 'my_sentinel' )  ){
     	var tml  = '<div id="'+this.desc+'_Show" style="width:'+this.width+'px; height:'+this.height+'px; z-index:11; ';  
		tml += 'border-radius: '+this.brdr_radius+'px;  border: solid transparent '+this.border+'px;';  
		tml += 'background: transparent; position: relative; top: 0px; left:0px; padding:0px;  clip: rect(0px, '+(this.w + this.border)+'px, '+(this.h + this.border)+'px, 0px); overflow: hidden;"  ';
		tml += 'onmouseout="document.body.style.cursor=\'default\';"  onmouseover="document.body.style.cursor=\'pointer\';">';
		if ( this.imgs[0] ){
			tml += '<img src="'+this.imgs[0].source+'" alt="Slider Image" id="'+this.desc+'_Cover1"   style="opacity: 1; filter:alpha(opacity=100);position: absolute; top: 0px; left:0px; z-index:99;max-width: 100%;';
			tml += 'border-radius: '+this.brdr_radius+'px;  border: solid transparent 0px; ';
			tml += 'clip: rect(0px,'+this.w+'px, '+this.h+'px, 0px); overflow:hidden"  onclick="give_link(sentinel,\''+this.desc+'\'  );" />';
			tml += '<img src="'+this.imgs[0].source+'" alt="Slider Image" id="'+this.desc+'_Cover0"   style="position: absolute; top: 0px; left:0px;z-index:99;max-width: 100%; ';
			tml += 'border-radius: '+this.brdr_radius+'px;  border: solid transparent 0px; ';
			tml += 'clip: rect(0px,'+this.w+'px, '+this.h+'px, 0px); overflow:hidden" onclick="give_link(sentinel,\''+this.desc+'\'  );" />';
		}
		tml += '</div>';
		tml += '<div id="'+this.desc+'_link_string" class="link-string"></div>';
		document.getElementById('spiderslider_'+desc).innerHTML = tml;
		var outer_w = parseInt(this.width) + parseInt((this.border*2));
		document.getElementById('spiderslider_'+desc).style.width = +outer_w+'px';
		this.place_triggers(link_dot_on, link_dot_off); 
	}else{
	
	
		if ( this.right){
			this.right.slide_HTML2(desc);} 
		if ( this.left ) {
			this.left.slide_HTML2(desc);}
	}
}//fnc

create_node.prototype.slider_carousel = function(imgID0, imgID1){
	var marq_color = "rgb("+this.red+", "+this.green+", "+this.blue+")";
	var slideWrapID = this.desc+'_Show';
	document.getElementById(slideWrapID).style.background = marq_color;
	document.getElementById(slideWrapID).style.borderColor = marq_color;
	if ( this.imgs.length > 1 ){
		force_pic(imgID0, this.imgs[this.marq_index].source );
		var self = this;
		this.timer = window.setTimeout(function() { self.load_slide(); }, (100-self.speed)*100);
	}///i
}//fnc
 
create_node.prototype.load_slide= function(){
	var obj_1 = this.desc+"_Cover1";
	var obj_0 = this.desc+"_Cover0";
	move_obj(document.getElementById( obj_0 ), 00 , 00);
	var self = this; 
	switch ( this.dir ){ 
		case 0:
		///scrollUp	
			var pos = this.height;
			move_obj( document.getElementById(obj_1), 0, pos );
			this.marq_index = switch_index (this.marq_index, (this.imgs.length-1) ); 
			force_pic( obj_1, this.imgs[this.marq_index].source );
			this.timer = window.setTimeout( function() { self.scroll_up(pos); }, (100-self.speed)*100 );
		break;
		case 1:
		///wipe right
			var pos = 0-this.width;
			move_obj( document.getElementById(obj_1), pos, 0 );
			this.marq_index = switch_index (this.marq_index, (this.imgs.length-1) ); 
			force_pic( obj_1, this.imgs[this.marq_index].source );
			this.timer = window.setTimeout( function() { self.wipe_right(pos); }, (100-self.speed)*100 );
		break;
		case 2:
        ///scrolldown
			var pos = 0-this.height;
			move_obj( document.getElementById(obj_1), 0, pos );
			this.marq_index = switch_index (this.marq_index, (this.imgs.length-1) ); 
			force_pic( obj_1, this.imgs[this.marq_index].source );
			this.timer = window.setTimeout( function() { self.scroll_down(pos); }, (100-self.speed)*100 );
		break;
		case 3:
		///wipeLeft
			var pos = this.width;
			move_obj( document.getElementById(obj_1), pos, 0 );
			this.marq_index = switch_index (this.marq_index, (this.imgs.length-1) ); 
			force_pic( obj_1, this.imgs[this.marq_index].source );
			this.timer = window.setTimeout( function() { self.wipe_left(pos); }, (100-self.speed)*100 );
		break;
		case 4:
		///fade   
			var opac = 100;
			reset_image( obj_0 );
			this.marq_index = switch_index (this.marq_index, (this.imgs.length-1) ); 
			force_pic( obj_1, this.imgs[this.marq_index].source );
			this.timer = window.setTimeout( function() { self.cross_fade(opac); }, (100-self.speed)*100 );
		break;
		default:
		return;
		} 
 }///fnc
 
 
create_node.prototype.cross_fade= function ( opac ){
	var obj_1 = this.desc+"_Cover1";
	var obj_0 = this.desc+"_Cover0";
///	if (this.timer){
///			clearTimeout(this.timer);
///	}
	opac = parseInt(opac);
	if ( opac >= 1 ){ 
		opac -= (opac /6)+1;
		if (navigator.appName !="Microsoft Internet Explorer"){
			window.document.getElementById(obj_0).style.opacity= opac/100;
			window.document.getElementById(obj_1).style.opacity= 1 - ( opac/100 );
		}else{
			window.document.getElementById(obj_0).style.filter="alpha(opacity="+ opac +")";
			window.document.getElementById(obj_1).style.filter="alpha(opacity="+ (100-opac) +")";
		}
		var self = this;
		this.timer = window.setTimeout(function() { self.cross_fade(opac); }, me );
	}else{
		this.place_triggers(link_dot_on, link_dot_off); 
		var self = this;
		this.timer = window.setTimeout(function() { self.slider_carousel( self.desc+"_Cover0", self.desc+"_Cover1" ); }, 0);
	}
}//fnc

create_node.prototype.scroll_up= function( pos ){
	var obj_0 = this.desc+'_Cover0';
	var obj_1 = this.desc+'_Cover1';
	pos = parseInt(pos);
	if (this.timer){
			clearTimeout(this.timer);
	}
	if ( pos > 1  ){ 
		pos -= (pos/6)+1;
		if ( ! document.getElementById(obj_0).style.posTop){
			window.document.getElementById(obj_0).style.top= (pos-parseInt(this.height))+"px";
			window.document.getElementById(obj_1).style.top= pos+"px";
		}else{
			window.document.getElementById(obj_0).style.posTop= pos-parseInt(this.height);
			window.document.getElementById(obj_1).style.posTop= pos;
		}
		var self = this;
		this.timer = window.setTimeout(function() { self.scroll_up(pos); }, me );
	}else{
		move_obj(document.getElementById(obj_1), 0, 0);
        this.place_triggers(link_dot_on, link_dot_off); 
		var self = this;
		this.timer = window.setTimeout(function() { self.slider_carousel(self.desc+"_Cover0", self.desc+"_Cover1" ); }, 0);
	}
}//fnc
 
create_node.prototype.scroll_down= function( pos ){
	var obj_0 = this.desc+'_Cover0';
	var obj_1 = this.desc+'_Cover1';
	if (this.timer){
		clearTimeout(this.timer);
	}
	pos = parseInt(pos);
	if ( pos < -1 ){ 
		pos += ( pos/-6 )+1;
		if ( ! document.getElementById(obj_0).style.posTop){
			window.document.getElementById(obj_0).style.top=(pos+parseInt(this.height))+"px";
			window.document.getElementById(obj_1).style.top=pos+"px";
		}else{
			window.document.getElementById(obj_0).style.posTop=pos+parseInt(this.height);
			window.document.getElementById(obj_1).style.posTop=pos;
		}
		var self = this;
		this.timer = window.setTimeout(function() { self.scroll_down(pos); }, me );
	}else{
		move_obj(document.getElementById(obj_1), 0, 0);
		this.place_triggers(link_dot_on, link_dot_off); 
		var self = this;
		this.timer = window.setTimeout(function() { self.slider_carousel(self.desc+"_Cover0", self.desc+"_Cover1" ); }, 0);
	}
}//fnc


create_node.prototype.wipe_left= function( pos ){
	var obj_0 = this.desc+'_Cover0';
	var obj_1 = this.desc+'_Cover1';
	if (this.timer){
		clearTimeout(this.timer);
	}
	pos = parseInt(pos);
	if ( pos > 1 ){ 
		pos-= ( pos/6 ) +1;
		if ( ! document.getElementById(obj_0).style.posLeft){
			window.document.getElementById(obj_0).style.left=(pos-parseInt(this.width))+"px";
			window.document.getElementById(obj_1).style.left=pos+"px";
		}else{
			window.document.getElementById(obj_0).style.posLeft=pos-parseInt(this.width);
			window.document.getElementById(obj_1).style.posLeft=pos;
		}
		var self = this;
		this.timer = window.setTimeout(function() { self.wipe_left(pos); }, me );
	}else{
		move_obj(document.getElementById(obj_1), 0, 0);
		this.place_triggers(link_dot_on, link_dot_off); 
		var self = this;
		this.timer = window.setTimeout(function() { self.slider_carousel(self.desc+"_Cover0", self.desc+"_Cover1" ); }, 0);
	}
}//fnc

 
create_node.prototype.wipe_right= function( pos ){
	var obj_0 = this.desc+'_Cover0';
	var obj_1 = this.desc+'_Cover1';
	if (this.timer){
		clearTimeout(this.timer);
	}
	pos = parseInt(pos);
	if ( pos < -1 ){
		pos+= (pos/-6)+1;
		
		if ( ! document.getElementById(obj_0).style.posLeft){
			window.document.getElementById(obj_0).style.left=(pos+parseInt(this.width))+"px";
			window.document.getElementById(obj_1).style.left=pos+"px";
		}else{
			window.document.getElementById(obj_0).style.posLeft=pos+parseInt(this.width);
			window.document.getElementById(obj_1).style.posLeft=pos;
		}
		var self = this;
		this.timer = window.setTimeout(function() { self.wipe_right(pos); }, me );
		
	}else{
		move_obj(document.getElementById(obj_1), 0, 0);
		this.place_triggers(link_dot_on, link_dot_off); 
		var self = this;
		this.timer = window.setTimeout(function() { self.slider_carousel(self.desc+"_Cover0", self.desc+"_Cover1" ); }, 0);
	}
}///wipe right
	
create_node.prototype.walk_tree = function(tree){

	if ( tree ) {
		tree.slide_HTML2(tree.desc);
		if ( tree.left ){ 
			tree.left.walk_tree( tree.left );
		}
		if ( tree.right ){ 
			tree.right.walk_tree( tree.right ); 
		}
	}
}

create_node.prototype.add_nodes = function( new_node ){
	if (! this.desc ){ this.desc = new_node.desc; return; } 
	var t = compare_nodes(this.desc, new_node.desc);
	switch(t){
		case 1:
			(! this.left)? this.left = new_node: this.left.add_nodes( new_node );   
		break;
		case 0:
			return;
		break;
		case -1:
			(! this.right)? this.right = new_node: this.right.add_nodes( new_node );    
		break;
		default:
			return;
		break;
	} 
 }//fnc

function compare_nodes(a, b){
	return ((a.toLowerCase()) > (b.toLowerCase()) )? -1: ((a.toLowerCase()) == (b.toLowerCase()) )? 0: 1; 
}//fnc

 
create_node.prototype.cache_imgs = function(desc){  
	if ( this.desc == desc ) { 
		var temp = new Array();
		if ( this.imgs.length > 0 ){
			for (var i = 0 ; i < this.imgs.length; i++ ){
				temp[i] = new Image();
				temp[i].src = this.imgs[i].source;
			}
		}
	}
	if ( this.left ){ this.left.cache_imgs(this.left.desc); }
	if ( this.right){ this.right.cache_imgs(this.right.desc); }
}//fnc
  
create_node.prototype.place_triggers= function(classOn, classOff){
	var slide_dots = "";
	
	for (var i=0; i < this.imgs.length; i++ ){
		if ( i == this.marq_index ){
			slide_dots += '<span class="'+classOn+'" onclick="sentinel.jump_index(\''+this.desc+'\', '+i+')" /></span>';
		}else{
			slide_dots += '<span class="'+classOff+'" onclick="sentinel.jump_index(\''+this.desc+'\', '+i+')" /></span>';
		}
	}
	var linkobj = document.getElementById(this.desc+'_link_string');
	move_obj( linkobj, 0, ((parseInt(this.border) + 24)*-1) );
	document.getElementById(this.desc+'_link_string').style.width = this.imgs.length*21+"px" ;
	document.getElementById(this.desc+'_link_string').innerHTML =  slide_dots ;
}//fnc

create_node.prototype.jump_index = function(desc, index){
	if ( this.desc == desc ){ 
		clearTimeout(this.timer); 
		this.marq_index= index;
		this.place_triggers(link_dot_on, link_dot_off);
		var obj_0 = this.desc+'_Cover0';
		var obj_1 = this.desc+'_Cover1';
		reset_image(obj_0); 
		reset_image(obj_1);
		move_obj(document.getElementById(obj_0), 00, 00);
		move_obj(document.getElementById(obj_1), 00, 00);
		document.getElementById(obj_0).src = this.imgs[index].source;
		document.getElementById(obj_1).src = this.imgs[index].source;
   		if (navigator.appName !="Microsoft Internet Explorer"){///is this neccessary?
			document.getElementById(obj_0).style.opacity= 0.0;/* opac down back image*/
		}else{
			document.getElementById(obj_1).style.filter="alpha(opacity="+100+")";
		}
	}
	if( this.left ){ this.left.jump_index (desc, index);}
	if( this.right ){ this.right.jump_index (desc, index);}
}//fnc

function give_link(tree, desc){
	if ( tree ){
		if ( tree.desc == desc ){ 
			clearTimeout(tree.timer); 
			var test_val = null;
			if((tree.imgs[tree.marq_index].lnk != '..')&&(tree.imgs[tree.marq_index].lnk != test_val)&&(tree.imgs[tree.marq_index].lnk != '') ){
				window.location.href="http://"+tree.imgs[tree.marq_index].lnk;
			}
		}
		give_link(tree.left, desc);
		give_link(tree.right, desc);
	} 
 }//fnc
  

function force_pic(imgID, new_src){
	try{
		var this_img= window.document.getElementById(imgID);
		this_img.src=new_src;
	}catch(e){
		force_pic(imgID, new_src);
	}
	return true;
}//fnc


