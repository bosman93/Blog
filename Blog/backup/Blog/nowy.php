<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  	<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="Główny"/>
	<link rel="alternate stylesheet" href="alternative.css" type="text/css" media="screen" title="Alternatywny"/>
	<title>Nowy blog</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml;	charset=UTF-8"/>
    <script src="wybor_styli.js" type="text/JavaScript" > </script>
	<script type="text/JavaScript">
		window.onload = function(){ 
			styleInit();
		};
	</script>
</head>

<body>

	<div class="header">
		<strong>Stwórz własnego bloga</strong>
	</div>
    
        <div id="style"></div>
    <script type="text/JavaScript">
		addStyleSelector(getStyleList());
	</script>
	

<?php

	require 'new_blog_functions.php';
	require 'general_functions.php';
	require 'menu.php';
		
	$message = '';
	
	if(isset($_POST['send']))
	{
		$user_name	= htmlspecialchars(trim($_POST['user_name']), ENT_QUOTES);
		$blog_name	= htmlspecialchars(trim($_POST['blog_name']), ENT_QUOTES);
		$password	= trim($_POST["user_password"]);
		$about		= htmlspecialchars(trim($_POST['about']), ENT_QUOTES);
	
		if(	str_len_between($user_name, 1, MAX) && str_len_between($blog_name, 1, MAX) &&	strlen($password) > 3 )
		{
			$blog_name = strtolower($blog_name);
			$temp = createNewBlog($blog_name, $user_name, md5($password), $about);
			if($temp[0])
			{
				$message = "Dodano blog.";
			}
			else
			{
				$message = "Błąd: ".$temp[1];
				
			}
		}
		else
		{
			$message = 'Podano nieprawidłowe dane. Żadne pole nie może pozostać puste, a hasło musi być dłuższe niż 4 znaki!';
		}
	}

	?>

<div class="main">
<div class="form_main">
<form action='' method="post"> 

		<div class="form_field">
		
			<div class="form_caption">Nazwa bloga: </div>			
			<input type="text" name="blog_name" value="Nowy blog"
					maxlength="<?php echo MAX ?>" size="<?php echo MAX + 3 ?>"/>
	
		</div>
	
		<div class="form_field">
		
			<div class="form_caption">Nazwa użytkownika:</div>			
			<input type="text" name="user_name" 
					maxlength="<?php echo MAX ?>" size="<?php echo MAX + 3 ?>" />

		</div>
		
		<div class="form_field">
		
			<div class="form_caption">Hasło: </div>
			<input type="password" name="user_password" 
					maxlength="<?php echo MAX ?>" size="<?php echo MAX + 3 ?>"/> 
			
		</div>
		
		<div class="form_field">
		
			<div class="form_caption">Opis: </div> 
			<textarea rows="10" cols="40" name="about"></textarea> 
			
		</div>

		<div class="form_field_buttons">
		
			<input type="submit" name="send" value="Dodaj"/>	
			<input type="reset" name="clear" value="Wyczyść"/>
					
		</div>
		

</form>
	<?php 	echo "<br/>".$message;?>

</div> 


</div>
</body>

</html>
