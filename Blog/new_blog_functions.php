<?php

function createNewBlog($blog_name, $usr_name, $md5_passwd, $description) 
{
	$blog_dir 	= 'blogs_data'.DIR_SEP.$blog_name;
	$log_dir 	= 'blogs_data'.DIR_SEP.'log';
	
	if(!file_exists('blogs_data')) 		//sprawdzenie istnienia glownego folderu z blogami
		mkdir('blogs_data', 0777, true);
		chmod('blogs_data', 0777);
	
				
	if(!ifHaveBlog($usr_name))	//sprawdzenie czy dany uzytkownik posiada swoj blog (ograniczenie do 1go)
	{ 			
		if (!file_exists($blog_dir)) //sprawdzenie istnienia blogu o zadanej nazwie
		{		
			mkdir($blog_dir, 0777, true);
			chmod($blog_dir, 0777);

			$text =implode(PHP_EOL, array($usr_name, $md5_passwd, $description));

			// uzupelnienie plikow
			if(	@file_put_contents($blog_dir.DIR_SEP.'info',$text,LOCK_EX )) // plik info
				if(@file_put_contents($log_dir, $usr_name.';'.$blog_name.PHP_EOL, FILE_APPEND|LOCK_EX))	// log
				{ 
					return array(true, '');
				}
		}
		else 
		{
			$message = 'Blog o zadanej nazwie już istnieje!';
			return array(false, $message);
		}
	}
	else
	{
		$message = 'Użytkownik posiada już swój blog!';
		return array(false, $message); // zwraca stan i komunikat
	}	
}

function ifHaveBlog($user_name) // sprawdza czy uzytkownik o danym nicku nie posiada juz blogu (true jesli posiada)
{
	if($file = @fopen('blogs_data'.DIR_SEP.'log', "r"))
	{
		if(flock($file, LOCK_SH))
		{
			while($line = fgets($file))
			{
				$current_name = explode(';', $line); // podziel wiersz na czesci ( nazwa_usera;nazwa_blogu )
				$current_name = $current_name[0];
				
				if($current_name == $user_name)
					return true;
			}
			fclose($file);
			return false;
		}
	}
	return NULL; // jesli nie udalo sie otworzyc pliku
}

?>