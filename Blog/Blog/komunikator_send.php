<?php
if(!defined("DIR_SEP")) define("DIR_SEP", DIRECTORY_SEPARATOR);
if(!defined("NUMBER_OF_ENTRIES")) define("NUMBER_OF_ENTRIES", 5);



function get_msg_number($path){	// zlicz wszystkie wiadomosci w folderze o zadanej sciezce
	$dir = opendir($path);
	$i = 0;
	
	while(false !== ($name = readdir($dir)))	// iteruj po zawartosci folderu
		if($name != '.' && $name != '..' && $name != '.progress')
			$i++;
	return $i;
}

function get_first_msg_number($path){	// znajdz numer pierwszej wiadomosci w folderze
	$dir = opendir($path);
	$min = -1;
	
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')
			if($name < $min || $min == -1) $min = $name;	// jesli zamien jesli znaleziony nr jest mniejszy od obecnego
	return $min;
}

function get_last_msg_number($path){	// znajdz numer ostatniej wiadomosci w folderze
	$dir = opendir($path);
	$max = 0;
	
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')
			if($name > $max) $max = $name;
	return $max;
} 

function check_max_file($path){		// sprawdzenie ilosci wpisow (jesli za duzo, usun najstarszy)
	if(get_msg_number($path) > NUMBER_OF_ENTRIES){
		$file = $path.DIR_SEP.get_first_msg_number($path);
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
	
//============================================================================

if(isset($_GET['name']) && isset($_GET['msg']) && isset($_GET['blog'])){
	$path = 'blogs_data'.DIR_SEP.$_GET['blog'].DIR_SEP.'komunikator';
	
	if(! file_exists($path)){
		mkdir ($path, 0755);
	}
	
	$cont = $_GET['name']."\r\n";
	$cont = $cont.$_GET['msg'];
	
	while(file_exists($path.DIR_SEP.'.progress')){ } // czekaj na zdjęcie semafora (plik .progress)
	
	touch($path.DIR_SEP.'.progress');
	
	check_max_file($path);
	$file_path = $path.DIR_SEP.(get_last_msg_number($path)+1);
	
	$fp = fopen($file_path, "w");
	
	if($fp){
		flock($fp, LOCK_EX);
		fwrite($fp, $cont);
		flock($fp, LOCK_UN);
	}
	
	fclose($fp);
	unlink($path.DIR_SEP.'.progress');	// zdejmij semafor
}
?>