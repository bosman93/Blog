<?php
if(!defined("DIR_SEP")) define("DIR_SEP", DIRECTORY_SEPARATOR);

function get_last_msg_number($path){	// wyszukaj numer ostatniej wiadomosci
	$dir = opendir($path);
	$max = 0;
	
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')
			if($name > $max) $max = $name;
	return $max;
} 

function get_msg_list($path){	// pobierz liste wiadomosci
	$list = array();
	if(!file_exists($path))
		return $list;
		
	$dir = opendir($path);
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')	// pomin semafor, . i ..
			$list[] = $name;
	return $list;
}
	
//========================================================================
	
if(isset($_GET['last']) && isset($_GET['blog'])){
	$path = 'blogs_data'.DIR_SEP.$_GET['blog'].DIR_SEP.'komunikator';
	
	for ($i=0; $i<10; $i++) {
		$last = $_GET['last'];
		
		if($last == get_last_msg_number($path)){ // jesli brak zmian (otrzymany last == ostatni na serwerze)
			echo '<last>-1</last>';
		}
		else{
			echo '<last>';									
				echo get_last_msg_number($path)+'';			// element XML zawierajacy info o numerze wpisu
			echo '</last>';
			echo '<desc>';
				$msg_list = get_msg_list($path);
				sort($msg_list);
				foreach ($msg_list as $msg) {
					$fp = fopen($path.DIR_SEP.$msg, "r");
					
					if($fp){	
						while (! flock($fp, LOCK_SH)) {} // czekaj na dostep
						
						$nick = fgets($fp);
						$nick = str_replace("\r\n",'',$nick);
						
						$text = fread($fp, filesize($path.DIR_SEP.$msg));
						$text = str_replace("<", "&lt;", $text);
						$text = str_replace("\r\n",'',$text);
						
						echo $nick.': '.$text."\r\n";
																
						flock ($fp, LOCK_UN);
					}
					fclose($fp);
				}
			echo '</desc>';
	}
	
	ob_flush(); flush();
	sleep(1);
	}
}
?>