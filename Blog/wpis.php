<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  	<link rel="stylesheet" href="style.css" type="text/css">
	<title>Nowy wpis</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml;	charset=UTF-8"/>
</head>

<body>
<div class="header">
	<strong>Dodaj wpis</strong>
</div>
<div class="main">

<?php	
	require 'general_functions.php';
	require 'menu.php';

	$log_dir = 'blogs_data'.DIR_SEP.'log';
	
	$message = '';
	
	if(isset($_POST['send'])) 
	{
		$user_name	= trim($_POST["user_name"]);
		$password	= md5(trim($_POST["user_password"]));
		$content 	= trim($_POST["content"]);
		
		if(str_len_between($user_name, 1, MAX) && strlen($content) > 0) // walidacja danych z formularza
		{
			$file_content = explode(PHP_EOL, file_get_contents($log_dir, LOCK_SH));
			
			$blog_name = '';
			foreach ($file_content as $line) // wyszukanie nazwy bloga  danego usera
			{	
				$tab = explode(';', $line);	// wyszukanie nazwy blogu w pliku log
				if($tab[0] == $user_name)
				{
					$blog_name = $tab[1];
				}
			}
			
			if($blog_name != '') // jesli blog istnieje
			{
				$blog_dir = 'blogs_data'.DIR_SEP.$blog_name;
				
				$file_content = explode(PHP_EOL, file_get_contents($blog_dir.DIR_SEP.'info', LOCK_SH));
				if($password === $file_content[1])
				{
					
					$date = str_replace('-', '', $_POST['date']);		// wyciecie znakow specjalnych z daty
					$time = str_replace(':', '', $_POST['time'].date('s'));
					
					$file_path = $blog_dir.DIR_SEP.$date.$time;			// utworzenie pierwszej czesci nazwy pliku
					
					$identifier = 0;						// identyfikator (gdy w tym samym czasie powstanie kilka plikow
					while(file_exists($file_path.$identifier)) // jesli istnieje plik o zadanym id - zwieksz identifier
					{						
						$identifier += 1;
					}	
					
					if($identifier < 10) 	// aby nazwa pliku posiadala 2 cyfrowy id
						$file_path .= '0';
					
						@file_put_contents($file_path.$identifier, $content, LOCK_EX ); // zapisz zawartosc wpisu do pliku

					
					// zapis zauploadowanych plikow na serwerze
				
					for( $i = 1; $i <= 3; $i++)
					{
						if (!empty($_FILES['file_'.$i]['name'])) 
						{
							$pathinfo = pathinfo($_FILES['file_'.$i]['name']);
							$extention = $pathinfo['extension'];		// wydobycie rozszerzenia pliku
							
							$target_path = $file_path.$identifier.$i.'.'.$extention; // utworzenie sciezki
							move_uploaded_file($_FILES['file_'.$i]['tmp_name'], $target_path); // zapis na serwerze
							chmod ( $target_path , 0777 );
						}	
					}
					$message = 'Dodano wpis.';
				}
				else
				{
					$message = 'Podano nieprawidłowe hasło.';
				}
			}
			else
			{
				$message = 'Aby dodać wpis musisz najpierw założyć blog.';
			}
		}
		else
		{
			$message = 'Podano nieprawidłowe dane. Żadne pole nie może pozostać puste!';
		}
	}

?>
	<div class="form_main">
<form method="post" enctype="multipart/form-data">


	
		<div class="form_field">
		
			<div class="form_caption">Nazwa użytkownika: </div>
			<input type="text" name="user_name" 
				maxlength="<?php echo MAX ?>" size="<?php echo MAX + 3 ?>" />
			
		</div>

		<div class="form_field">
		
			<div class="form_caption">Hasło:  </div>
			<input type="password" name="user_password"
				maxlength="<?php echo MAX ?>" size="<?php echo MAX + 3 ?>" />
				
		</div>
		
		<div class="form_field">
		
			<div class="form_caption">Treść: </div>
			<textarea rows="10" cols="40" name="content"></textarea>
			
		</div>
		
		<div class="form_field">
		
			<div class="form_caption">Data: </div>
			<input type="text" name="date" value="<?php echo date("Y-m-d",time())?>" readonly="readonly" tabindex="-1"  />
			
		</div>
		
		<div class="form_field"> 
		
			<div class="form_caption">Godzina: </div>
			<input type="text" name="time" value="<?php echo date("H:i",time())?>" readonly="readonly" tabindex="-1"  /> 
			
						
		</div>
		
		<div class="form_field">
		
			<div class="form_caption">Załączniki: </div>

					<input type="file" name="file_1"  />  <br/>

					<input type="file" name="file_2"  /> <br/>

					 <input type="file" name="file_3"  /> <br/>

				
		</div>
		
		<div class="form_field_buttons">
		
			<input type="submit" name="send" value="Dodaj"/>	
			<input type="reset" name="clear" value="Wyczyść"/>
					
		</div>


</form>

	<?php echo "<br/>".$message;?>
	</div> 	
</div>
</body>

</html>