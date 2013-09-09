<html>
<head>
<title>Etsee</title>
<link href="stylee.css" media="screen" rel="stylesheet" type="text/css" />
<link href="facebox.css" media="screen" rel="stylesheet" type="text/css"/>
</head>
<body>

<?php

define('API_KEY', 'iw2o9cm04xxdzts1qep4f1jy');

// Make sure you define API_KEY to be your unique, registered key
$url = "http://openapi.etsy.com/v2/listings/active?limit=12&api_key=" . API_KEY;
if (isset($_GET['offset']))
{
	$url .= $_GET['offset'];
	$_GET['offset'] += 12;
}
else
{
$offset = 12;
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response_body = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if (intval($status) != 200) throw new Exception("HTTP $status\n$response_body");

$response = json_decode($response_body);
$imgarray = array();
$objectarray = array();

foreach($response->results as $o)
{
	$url = "http://openapi.etsy.com/v2/listings/".$o->listing_id."/images?api_key=" . API_KEY;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response_body = curl_exec($ch);
	
	$i = json_decode($response_body);
	if ($i->count > 0)
	{
		foreach ($i->results as $img)
		{
			if ($img->full_width >= 600 && $img->full_height >= 400 && $img->full_height <= $img->full_width)
				{ $imgarray[] = $img; $objectarray[] = $o; break; }
		}
	}
	if (sizeof($imgarray) >= 9)
	break;
}

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
<script src="http://cdn.jquerytools.org/1.2.6/jquery.tools.min.js"></script>
<script src="facebox.js" type="text/javascript"></script>

<div class="prev button left_b">
<div style='background:url("https://d1qpxsaxxi4rth.cloudfront.net/site_assets/deck/food-left-button.png");'>
<img src="https://d1qpxsaxxi4rth.cloudfront.net/site_assets/deck/food-left-button.png"/>
</div>
</div>

<div class="next button right_b">
<div style='background:url("https://d1qpxsaxxi4rth.cloudfront.net/site_assets/deck/food-right-button.png");'>
<img src="https://d1qpxsaxxi4rth.cloudfront.net/site_assets/deck/food-right-button.png"/>
</div>
</div>

<div id="toolbar">
	<div class="logo">
		<a href="">ETSEE</a>
	</div>
	
	<div class="share">
		<span class='st_sharethis' displayText='SHARE'></span>
	</div>
	
	<div class="account">
		<a href="#disp" rel="facebox">LOGIN</a>
	</div>
	
	<div class="dislike">
		DISLIKE
	</div>
	
	<div class="like">
		LIKE
	</div>
	
	<div class="form" style="border-left:0">
		<form id="search_form">
			IM INSPIRED BY
			<input type="text" name="tags" placeholder="Enter a Tag" value=""/>
			<input type="hidden" name="offset" value="<?php echo $offset; ?>" />
		</form>
	</div>

</div>

<div class="scrollable">   
   <div class="items">
	<?php $c = 0;
	foreach($imgarray as $img)
	{
	?>
	<div class="container" id="<?php echo $c; ?>" style="position:relative;">
		<canvas id="<?php echo $img->listing_image_id; ?>" class="main_image" title="<?php echo $img->url_fullxfull; ?>"></canvas>
		<div class="info_box" style="display:block; float:none;">
			<div class="info_title">
				<?php 
				//$t = explode(' ', $objectarray[$c]->title);
				echo $objectarray[$c]->title; //implode(' ', array_slice($t, 0, 5)); 
				?>
			</div>
			<div class="info_main" style="display:none;">
			<div class="main_left">
				<strong>Materials</strong>
				<ul>
				<?php $g = sizeof($objectarray[$c]->materials) > 5 ? 5 :  sizeof($objectarray[$c]->materials);
				for($n=0; $n<$g; $n++) { ?>
					<li><?php echo $objectarray[$c]->materials[$n]; ?></li>
				<?php } ?>
				</ul><br />
				<strong>Tags</strong>
				<ul>
				<?php $g = sizeof($objectarray[$c]->tags) > 5 ? 5 :  sizeof($objectarray[$c]->tags);
				for($n=0; $n<$g; $n++) { ?>
					<li><?php echo $objectarray[$c]->tags[$n]; ?></li>
				<?php } ?>
				</ul>
			</div>
			<div class="main_right">
				<p><?php echo substr($objectarray[$c]->description, 0, 1000); ?></p>
				<p><a href="<?php echo $objectarray[$c]->url; ?>" target="_blank">Find It on Etsy</a></p>
			</div>
			</div>
		</div>
		<form>
		<input name="likes" type="hidden" value="<?php echo $objectarray[$c]->listing_id; ?>" />
		<input name="tags" type="hidden" value="<?php $ts=array(); foreach($objectarray[$c]->tags as $t) $ts[]=$t; echo implode(',',$ts);?>" />
		<input name="materials" type="hidden" value="<?php $ms=array(); foreach($objectarray[$c]->materials as $m) $ms[]=$m; echo implode(',',$ms); ?>" />
		</form>
	</div>
	<?php $c++;
	}
	?>
   </div>
</div>

<div id="disp" style="display:none;">
<div class="form_left">
<form class="sign_in" method="post">
<strong>Sign In</strong><br/><br/>
Username: <input type="text" name="name"/><br/>
Password: <input type="password" name="password"/><br/>
<input type="submit" />
</form>
</div>
<div class="form_right">
<form class="register" method="post">
<strong>Register</strong><br/><br/>
Username: <input type="name" name="name" /><br/>
Password: <input type="password" name="password" /><br/>
<input type="submit" />
</form>
</div>
</div>

<div id="load" style="display:none;">
</div>

<div id="loading"  style="display:none;">
<img src="http://www.gojee.com/images/middle-loader.gif" />
</div>

<script type="text/javascript">

	function Carver(canvasId, url) {
		// create canvas & drawing context
		this.canvas = document.getElementById(canvasId);
		this.context = this.canvas.getContext('2d');

		var carver = this;
		this.removed = 0;

		// load image
		var img = new Image();
		img.onload = function() {
			w = this.width;
			h = this.height;
			carver.canvas.width = w;
			carver.canvas.height = h;
			carver.context.drawImage(img, 0, 0, w, h);

			carver.w = w;
			carver.h = h;

			carver.preload();
		};
		img.src = url; // trigger image loading

		this.preload = function() {
			// get raw data
			var raw = this.context.getImageData(0, 0, this.canvas.width, this.canvas.height);
			var copy = this.context.createImageData(this.canvas.width, this.canvas.height);
			for(k = 0; k < raw.data.length; k++) {
				copy.data[k] = raw.data[k];
			}

			// difference between two pixels (color vector distance)
			var pixel_diff = function(x0,y0, x1,y1) {

				var offset0 = 4 * (carver.canvas.width * y0 + x0);
				var r0 = copy.data[offset0];
				var g0 = copy.data[offset0 + 1];
				var b0 = copy.data[offset0 + 2];

				var offset1 = 4 * (carver.canvas.width * y1 + x1);
				var r1 = copy.data[offset1];
				var g1 = copy.data[offset1 + 1];
				var b1 = copy.data[offset1 + 2];

				return Math.sqrt(Math.pow(r0 - r1, 2) +
					Math.pow(g0 - g1, 2) +
					Math.pow(b0 - b1, 2));
			};

			this.scores = new Array();

			for(i = 0; i < this.w; ++i) { // for each column
				var score = 0;	// column score

				var path = new Array(); // storing the points to be removed.
				for(j = 0; j < this.h; j++) {
					var lots = Math.pow(10, 30);
					var delta_left = lots, delta_here = lots, delta_right = lots;

					if(j == this.h - 1) {
						break;
					}

					// comparison between the current pixel and the one under
					score += pixel_diff(i, j, i, j+1);
				}
				this.scores.push([score, i]);
			}
			this.scores.sort();
		};

		this.shrink = function() {
			// get raw data
			var raw = this.context.getImageData(0, 0, this.canvas.width, this.canvas.height);

			var selected = this.scores.shift(); // current column to remove
			var x = selected[1];

			// shift the block that is to the right of the deleted column one position left.
			this.context.putImageData(this.context.getImageData(x+1, 0, this.canvas.width - x - 1, this.h), x, 0);

			// add a column of white pixels to the right.
			this.context.putImageData(this.context.createImageData(1, this.h), this.w-1, 0);

			// shift all columns that are to the right of the one we've deleted by one pixel to the left.
			for(i = 0; i < this.scores.length; i++) {
				if(this.scores[i].column >= x) {
					this.scores[i].column--;
				}
			}

			// now dealing with one less column
			this.w--;

			// resize the other image block using the browser
			var naiveImgResize = document.getElementById("naive");
			naiveImgResize.setAttribute("width", this.w);
			naiveImgResize.setAttribute("height", this.h);
		};
	}

</script>

<script>
$(function() {
	$.fn.exists = function () 
	{
		return this.length !== 0;
	}
	
	var flag = 0;
	
	function search()
	{
		jQuery('#stOverlay').show();
		jQuery('#loading').show();
		var api = $(".scrollable").data("scrollable");
		api.getItems().remove();
		addItems();
		
	}
	
	function addItems()
	{	
		var dataString = $('#search_form').serialize();
		jQuery.ajax({  
		  type: "GET",  
		  url: "get.php",  
		  data: dataString,  
		  dataType: "json",
		  success: function(msg) { 
			if (msg)
			{ 
				flag = 0;
				$('#search_form input[name="offset"]').attr('value', msg.offset);
				var api = $(".scrollable").data("scrollable");
				$.each(msg.data, function(i, item) {
					$('#load').html(msg.data[i].html);
					if(!($('.scrollable .items .container .main_image[title="' + $('#load div .main_image').attr('title') + '"]').exists())) 
					{
					$('#load .container').attr('id', $(".scrollable .container").length);
					$('#load div.container').height($(window).height());
					$('#load div.container').width($(window).width());
					$('#load div .main_image').height($(window).height());
					$('#load div .main_image').width($(window).width());
					var d = new Carver($("#load .container .main_image").attr('id'), $("#load .container .main_image").attr('title'));
					api.addItem($("#load").children());
					}
				});
				jQuery('#stOverlay').hide();
				jQuery('#loading').hide();
			}
		  }  
		});
	}
	
	function like()
	{
		if ($('#uid').exists())
		{
			var api = $(".scrollable").data("scrollable");
			var ind = api.getIndex();
			var dataString = $(".scrollable .container form").eq(ind).serialize() + '&action=insertMeta' + '&id=' + $('#uid').html();
			
			jQuery.ajax({  
			  type: "POST",  
			  url: "data.php",  
			  data: dataString,  
			  dataType: "json",
			  success: function(msg) { 
			  if (msg.success == 1) {
			  	$(".scrollable .container .info_title").eq(ind).append('<span class="liked">Liked</span>');
			  }}
			 });
		 }
		 else
		 {
		 	jQuery.facebox({ div: '#disp' });
		 }
	}
	
	function dislike()
	{
		if ($('#uid').exists())
		{
			var api = $(".scrollable").data("scrollable");
			var ind = api.getIndex();
			var dataString = $(".scrollable .container form").eq(ind).serialize() + '&action=removeMeta' + '&id=' + $('#uid').html();
			
			jQuery.ajax({  
			  type: "POST",  
			  url: "data.php",  
			  data: dataString,  
			  dataType: "json",
			  success: function(msg) { 
			  if (msg.success == 1) {
			  	$(".scrollable .container .info_title").eq(ind).append('<span class="liked">Disliked</span>');
			  }}
			 });
		 }
		 else
		 {
		 	jQuery.facebox({ div: '#disp' });
		 }
	}
	
	function login()
	{	
		var dataString = $('#facebox .popup .content form.sign_in').serialize() + '&action=login';

		jQuery.ajax({  
		  type: "POST",  
		  url: "data.php",  
		  data: dataString,  
		  dataType: "json",
		  success: function(msg) { 
			  if (msg.success == 1) {
				 $('body').append(msg.html);
				 $('.account a').html(msg.name);
				 jQuery(document).trigger('close.facebox');
			  }
		  }
		 });
	}
	
	function register()
	{	
		var dataString = $('#facebox .popup .content form.register').serialize() + '&action=insertUser';

		jQuery.ajax({  
		  type: "POST",  
		  url: "data.php",  
		  data: dataString,  
		  dataType: "json",
		  success: function(msg) { 
			  if (msg.success == 1) {
				 $('body').append(msg.html);
				 $('.account a').html(msg.name);
				 jQuery(document).trigger('close.facebox');
			  }
		  }
		 });
	}
	
	$('.items div.container').height($(window).height());
	$('.items div.container').width($(window).width());
	$('.items div .main_image').height($(window).height());
	$('.items div .main_image').width($(window).width());
	
	$.each($('.items div .main_image'), function() {
		var d = new Carver($(this).attr('id'), $(this).attr('title'));
	});
	
	$('.items div .info_box').live({mouseenter: function(){$(this).fadeTo(100, 0.4);}, mouseleave: function(){$(this).fadeTo(100, 0.7);}});
	$('.items div .info_box').live('click', function() {jQuery('.info_main', $(this)).slideToggle(); $(this).fadeTo(100, 0.7);});
	
	/* $.each($('.items div .info_box'), function() { 
		//$(this).css('top', $(window).height()-2*$(this).height()+'px'); 
		if (500 > $(this).width())
		{
			var add = (500 - $(this).width)/2;
			$(this).css('padding-left', "+=" + add);
			$(this).css('padding-right', "+=" + add);
		}
		$('.info_main', $(this)).width($(this).width());
	}); */

	// initialize scrollable
	$(".scrollable").scrollable({
		onBeforeSeek:  function(event, i) {
			if (i + 5 >= this.getSize() && flag == 0)
			{
				flag = 1;
				addItems();
			}
			}
	}).focus();
	
	$(window).resize(function() { 
		$('.items div.container').height($(window).height());
		$('.items div.container').width($(window).width());
		$('.items div .main_image').height($(window).height());
		$('.items div .main_image').width($(window).width()); 
		//$.each($('.items div .info_box'), function() { 
		//	$(this).css('bottom', '0');  
		//});
	});
	
	 $('a[rel*=facebox]').facebox();
	 $(document).bind('afterReveal.facebox', function() {
		 $('#facebox .popup .content form.sign_in').unbind('submit').submit(function(e)
		 	{e.preventDefault();
		 	login();
		 });
		 $('#facebox .popup .content form.register').unbind('submit').submit(function(e)
		 	{e.preventDefault();
		 	register();
		 });
	 });
	 $('.like').click(function(){
	 	like();
	 });
	 $('.dislike').click(function(){
	 	dislike();
	 });
	 $('#search_form').submit(function(e){e.preventDefault(); search(); });
});
</script>
<script type="text/javascript">var switchTo5x=true;</script><script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:'195e984f-3a90-4a17-9c4b-001b2c63c591'});$('.share .st_sharethis .stButton').attr('style', 'font-weight:bold; color:#fff;');</script>
</body>
</html>