<?php
if(!defined("s")) define("s", DIRECTORY_SEPARATOR);


function get_msg_number($path){
	$dir = opendir($path);
	$i = 0;
	
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')
			$i++;
	return $i;
}

function get_first_msg_number($path){
	$dir = opendir($path);
	$min = -1;
	
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')
			if($name < $min || $min == -1) $min = $name;
	return $min;
}

function get_last_msg_number($path){
	$dir = opendir($path);
	$max = 0;
	
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')
			if($name > $max) $max = $name;
	return $max;
} 
function check_max_file($path){
	if(get_msg_number($path) > 5){
		$file = $path.s.get_first_msg_number($path);
		unlink($file);
	}
}

function get_msg_list($path){
	$list = array();
	if(!file_exists($path))
		return $list;
		
	$dir = opendir($path);
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')	
			$list[] = $name;
	return $list;
}
	
if(isset($_GET['name']) && isset($_GET['msg']) && isset($_GET['blog'])){
	$path = 'blogs_data'.s.$_GET['blog'].s.'komunikator';
	if(! file_exists($path)){
		mkdir ($path, 0777);
	}
	$cont = $_GET['name']."\r\n";
	$cont = $cont.$_GET['msg'];
	
	while(file_exists($path.s.'.progress')){}
	touch($path.s.'.progress');
	
	check_max_file($path);
	$file_path = $path.s.(get_last_msg_number($path)+1);
	
	$fp = fopen($file_path, "w");
	if($fp){
		flock($fp, LOCK_EX);
		fwrite($fp, $cont);
		flock($fp, LOCK_UN);
	}
	fclose($fp);
	unlink($path.s.'.progress');
}
?>