<?php

define('API_KEY', 'xy2wr7xfk89s9652szo2n01f');



// Make sure you define API_KEY to be your unique, registered key
$url = "http://openapi.etsy.com/v2/listings/active?limit=20&api_key=" . API_KEY;

if (isset($_GET['tags']) && !empty($_GET['tags']))
{
	$tags = $_GET['tags'];
	$url .= '&tags='.$_GET['tags'];
}

if ($_GET['offset'])
{
	$url .= '&offset='.$_GET['offset'];
	$offset = $_GET['offset'] + 20;
}
else
{
	$offset = 20;
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
		if (sizeof($imgarray) >= 18)
			break;
	}
}

$c = 0;
$ha = array();
	foreach($imgarray as $img)
	{
	$html = '<div class="container" id="'.$c.'" style="position:relative;"><canvas id="'.$img->listing_image_id.'" class="main_image" title="'.$img->url_fullxfull.'"></canvas><div class="info_box" style="display:block; float:none;"><div class="info_title">'.$objectarray[$c]->title.'</div><div class="info_main" style="display:none;"><div class="main_left"><strong>Materials</strong><ul>';
        
$g = sizeof($objectarray[$c]->materials) > 5 ? 5 :  sizeof($objectarray[$c]->materials) + 1;
				for($n=0; $n<$g; $n++) { 
$html .= '<li>'.$objectarray[$c]->materials[$n].'</li>';
}

$html .= '</ul><strong>Tags</strong><ul>';

$g = sizeof($objectarray[$c]->tags) > 5 ? 5 :  sizeof($objectarray[$c]->tags) + 1;
				for($n=0; $n<$g; $n++) { 
$html .= '<li>'.$objectarray[$c]->tags[$n].'</li>';
}

$html .=	'</ul></div><div class="main_right"><p>'.substr($objectarray[$c]->description, 0, 1000).'</p><p><a href="'.$objectarray[$c]->url.'" target="_blank">Find It on Etsy</a></p></div></div></div>';
$html .= '<form><input name="likes" type="hidden" value="'.$objectarray[$c]->listing_id.'" /><input name="tags" type="hidden" value="';
$ts=array(); foreach($objectarray[$c]->tags as $t) $ts[]=$t; $html .= implode(',',$ts);
$html .= '" /><input name="materials" type="hidden" value="';
$ms=array(); foreach($objectarray[$c]->materials as $m) $ms[]=$m; $html .= implode(',',$ms);
$html .= '" /></form></div>';
$c++;
$count++;
$ha[] = array('html' => $html);
}

echo json_encode(array('offset'=>$offset,'data'=>$ha));

?>