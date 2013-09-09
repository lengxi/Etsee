<?php

$con = mysql_connect("localhost","german_beta","h3@^M8cH") or die(mysql_error());
mysql_select_db("german_x") or die(mysql_error());

function getUser($id, $type)
{
	if ($type == 0)
	{
		$result = mysql_query("SELECT * FROM users WHERE id = ".$id);
	}
	else
	{
		$result = mysql_query("SELECT * FROM users WHERE user_id = '".$id."'");
	}
	echo json_encode(mysql_fetch_array($result));
}

function insertUser($name, $password)
{
	mysql_query("INSERT INTO users (id, name, password) VALUES (null, '".$name."','".sha1($password)."')");                                
	login($name, $password);
}

function insertMeta($id, $likes, $tags, $materials)
{
	if (!empty($likes))
	{
		$result = mysql_query("SELECT likes FROM users WHERE id = ".$id);
		if ($result)
		{
		$r = mysql_fetch_array($result);
		$l = explode(',',$r['likes']);
		if (!in_array($likes, $l))
			$l[] = $likes;
		if (empty($l[0])) $l=array_slice($l, 1);
		mysql_query("UPDATE users SET likes = '".implode(',',$l)."' WHERE id = ".$id);
		}
	}
	
	if (!empty($tags))
	{
		if ($result)
		{
		$result = mysql_query("SELECT tags FROM users WHERE id = ".$id);
		$r = mysql_fetch_array($result);
		$t = explode(',',$r['tags']);
		foreach(explode(',',$tags) as $tag)
		{
			if (!in_array($tag, $t) && !empty($tag))
				$t[] = $tag;
		}
		if (empty($t[0])) $t=array_slice($t, 1);
		mysql_query("UPDATE users SET tags = '".implode(',',$t)."' WHERE id = ".$id);
		}
	}
	
	if (!empty($materials))
	{
		if ($result)
		{
		$result = mysql_query("SELECT materials FROM users WHERE id = ".$id);
		$r = mysql_fetch_array($result);
		$m = explode(',',$r['materials']);
		foreach(explode(',',$materials) as $material)
		{
			if (!in_array($material, $m) && !empty($material))
				$m[] = $material;
		}
		if (empty($m[0])) $m=array_slice($m, 1);
		mysql_query("UPDATE users SET materials = '".implode(',',$m)."' WHERE id = ".$id);
		}
	}
	echo json_encode(array('success'=>1));
}

function removeMeta($id, $likes, $dislikes, $tags, $materials)
{
	if (!empty($likes))
	{
		$result = mysql_query("SELECT likes FROM users WHERE id = ".$id);
		if ($result)
		{
		$r = mysql_fetch_array($result);
		$l = explode(',',$r['likes']);
		if (in_array($likes, $l))
			unset($l[array_search($likes, $l)]);
		mysql_query("UPDATE users SET likes = '".implode(',',$l)."' WHERE id = ".$id);
		}
	}
	
	if (!empty($dislikes))
	{
		$result = mysql_query("SELECT dislikes FROM users WHERE id = ".$id);
		if ($result)
		{
		$r = mysql_fetch_array($result);
		$d = explode(',',$r['dislikes']);
		if (!in_array($dislikes, $d))
			$d[] = $dislikes;
		if (empty($d[0])) $d=array_slice($d, 1);
		mysql_query("UPDATE users SET dislikes = '".implode(',',$d)."' WHERE id = ".$id);
		}
	}
	
	if (!empty($tags))
	{
		if ($result)
		{
		$result = mysql_query("SELECT tags FROM users WHERE id = ".$id);
		$r = mysql_fetch_array($result);
		$t = explode(',',$r['tags']);
		foreach(explode(',',$tags) as $tag)
		{
			if (in_array($tag, $t))
				unset($t[array_search($tag, $t)]);
		}
		mysql_query("UPDATE users SET tags = '".implode(',',$t)."' WHERE id = ".$id);
		}
	}
	
	if (!empty($materials))
	{
		if ($result)
		{
		$result = mysql_query("SELECT materials FROM users WHERE id = ".$id);
		$r = mysql_fetch_array($result);
		$m = explode(',',$r['materials']);
		foreach(explode(',',$materials) as $material)
		{
			if (in_array($material, $m))
				unset($m[array_search($material, $m)]);
		}
		mysql_query("UPDATE users SET materials = '".implode(',',$m)."' WHERE id = ".$id);
		}
	}
	echo json_encode(array('success'=>1));
}


function login($name, $password)
{
	$hash = sha1($password);
	$result = mysql_query("SELECT id, name FROM users WHERE password = '".$hash."'");
	if ($result)
	{
		$r = mysql_fetch_array($result);
		if (strcmp($name, $r['name']) == 0)
		{
			echo json_encode(array('success' => '1', 'name' => $r['name'], 'html' => '<div id="uid">'.$r['id'].'</div>'));
		}
		return;
	}
	echo json_encode(array('success' => '0'));
}

if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    switch($action) {
        case 'getUser' : getUser($_POST['id'], 0);break;
        case 'insertUser' : insertUser($_POST['name'], $_POST['password']);break;
        case 'insertMeta' : insertMeta($_POST['id'], $_POST['likes'], $_POST['tags'], $_POST['materials']);break;
        case 'removeMeta' : removeMeta($_POST['id'], $_POST['likes'], $_POST['likes'], $_POST['tags'], $_POST['materials']);break; 
    	case 'login' : login($_POST['name'], $_POST['password']); break;
    }
}

if(isset($_GET['action']) && !empty($_GET['action'])) {
    $action = $_GET['action'];
    switch($action) {
        case 'getUser' : getUser($_GET['id'], 0);break;
        case 'insertUser' : insertUser($_GET['name'], $_GET['password']);break;
        case 'insertMeta' : insertMeta($_GET['id'], $_GET['likes'], $_GET['tags'], $_GET['materials']);break;
        case 'removeMeta' : removeMeta($_GET['id'], $_GET['likes'], $_GET['likes'], $_GET['tags'], $_GET['materials']);break; 
    	case 'login' : login($_GET['name'], $_GET['password']); break;
    }
}


mysql_close($con);
?>