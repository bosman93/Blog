<?php
if(!defined("s")) define("s", DIRECTORY_SEPARATOR);

function get_last_msg_number($path){
	$dir = opendir($path);
	$max = 0;
	
	while(false !== ($name = readdir($dir)))
		if($name != '.' && $name != '..' && $name != '.progress')
			if($name > $max) $max = $name;
	return $max;
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
	
if(isset($_GET['last']) && isset($_GET['nazwa'])){
	$path = 'blogs_data'.s.$_GET['nazwa'].s.'komunikator';
	
	for ($i=0; $i<10; $i++) {
	//echo '<response>';
	$last = $_GET['last'];
	if($last == get_last_msg_number($path)){
		echo '<last>-1</last>';
	}else{
		echo '<last>';
			echo get_last_msg_number($path)+'';
		echo '</last>';
		echo '<desc>';
			$msg_list = get_msg_list($path);
			sort($msg_list);
			foreach ($msg_list as $msg) {
				$fp = fopen($path.s.$msg, "r");
				if($fp){	
					while (! flock($fp, LOCK_SH)) {}
					$nick = fgets($fp);
					$nick = str_replace("\r\n",'',$nick);
					
					$rest = fread($fp, filesize($path.s.$msg));
					$rest = str_replace("<", "&lt;", $rest);
					$rest = str_replace("\r\n",'',$rest);
					
					echo $nick.': '.$rest."\r\n";
															
					flock ($fp, LOCK_UN);
				}
				fclose($fp);
			}
		echo '</desc>';
	}
	//echo '</response>';
	
	ob_flush(); flush();
	sleep(1);
	}
}
?>