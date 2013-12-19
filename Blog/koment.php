<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  	<link rel="stylesheet" href="style.css" type="text/css" />
	<title>Nowy komentarz</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml;	charset=UTF-8"/>
</head>

<body>

<div class="header">
	<strong>Dodaj komentarz</strong>
</div>
<div class="main">
<?php 

	require 'general_functions.php';
	require 'menu.php';
	
	$message = '';
	
	if(isset($_POST['send'])) 
	{
		if(isset($_GET['blog']) && isset($_GET['id']))
		{
			$blog_name = $_GET['blog'];
			$entry_name = $_GET['id'];
			$kom_dir =  'blogs_data'.DIR_SEP.$blog_name.DIR_SEP.$entry_name.'.k'; // sciezka do folderu z komentarzami
			
			$nick = htmlspecialchars(trim($_POST['nick']), ENT_QUOTES);
			$post = htmlspecialchars(trim($_POST['content']), ENT_QUOTES);
			
			if(str_len_between($nick, 1, MAX) && strlen($post) > 0)
			{			
				if(!file_exists($kom_dir))
				{
					mkdir($kom_dir, 0777, true);
					chmod($kom_dir, 0777);
				}
				$i = 0;
				while(file_exists($kom_dir.DIR_SEP.$i)) // jesli istnieje plik o zadanym id - zwieksz identifier
				{
					$i += 1;
				}
				$text = implode( PHP_EOL, array($_POST['type'],  date("Y-m-d",time()).','.date("H:i:s",time()), $nick, $post) );
				@file_put_contents($kom_dir.DIR_SEP.$i , $text, LOCK_EX );
				
				$blog_name = str_replace(' ', '%20', $blog_name);
				$message = 'Dodano komentarz.<br/><a href=blog.php?nazwa='.$blog_name.'>Powrót</a>';
			}
			else
			{
				$message = 'Podano błędne dane. Pozostawiono puste pole lub podano nick dłuższy niż 32 znaki.';
			}
		}
		else
		{
			$message = "Błąd! Nie otrzymano nazwy wpisu do skomentowania!";
		}
	}
	
?>
	<div class="form_main">
<form action='' method="post">

	
		<div class="form_field">
		
			<div class="form_caption">Nick: </div>
			<input type="text" name="nick" 
				maxlength="<?php echo MAX ?>" size="<?php echo MAX + 3 ?>"/>
			
		</div>

		<div class="form_field">
		
			<div class="form_caption">Zawartość:  </div>
			<textarea rows="10" cols="40" name="content"></textarea>
				
		</div>
		
		<div class="form_field">
		
			<div class="form_caption">Rodzaj: </div>
			<select name="type">
			
				<option label="positive" value="positive">
					Pozytywny
				</option>
				
				<option label="neutral" value="neutral" selected="selected" >
					Neutralny
				</option>
				
				<option label="negative" value="negative">
					Negatywny
				</option>
				
			</select>
			
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