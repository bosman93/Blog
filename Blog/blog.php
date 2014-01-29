<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="Główny"/>
	<link rel="alternate stylesheet" href="alternative.css" type="text/css" media="screen" title="Alternatywny"/>
	<title>Blog </title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml;	charset=UTF-8"/>
    
    <script src="wybor_styli.js" type="text/JavaScript" > </script>
	<script type="text/JavaScript">
		window.onload = function(){ 
			styleInit();
		};
	</script>
    
</head>

<body>

<div class="main">

<?php 
	require 'general_functions.php';
	error_reporting(0);

	$blog_names_file = explode(PHP_EOL, file_get_contents('blogs_data'.DIR_SEP.'log', LOCK_SH));

	foreach($blog_names_file as $line) // wczytanie wszystkich nazw blogow z pliku log
	{
		$temp = explode(';', $line);
		
		if($temp[0] != '')
			$tab[$temp[0]] = $temp[1];
	}
	
	if(!isset($_GET['nazwa']))
	{	
?>

	<div class="header">
		<strong>Dostępne blogi</strong>
	</div>
    
        <div id="style"></div>
    <script type="text/JavaScript">
		addStyleSelector(getStyleList());
	</script>
	
<?php require 'menu.php';?>
	
	<div class="entry">
	<table>
<?php 	//--wydruk-tabeli-dostepnych-blogow-----------------------------------
	
		foreach ($tab as $owner => $blog_name) {
							
			$path = 'blog.php?nazwa='.str_replace(' ', '%20', $blog_name);
			
?>

		<tr>
			<td><?php echo "<a href='$path'>$blog_name</a>";?> </td>
			<td> Autor:</td>
			<td><?php echo $owner;?></td>
		</tr>

	
<?php 	} // zamkniecie foreach ?>

	</table>

	</div>
	
<?php 	//--------------------------------------------------------------------
	}
	else	// wykonaj jesli przeslano 'nazwa'
	{
		$blog_name = $_GET['nazwa'];
		$author = array_search($blog_name, $tab);
		
		if(array_search($blog_name, $tab)) // jesli istnieje blog o danej nazwie
		{

?> 
	<div class="header">								<!--Nagłówek strony-->
		<strong><?php echo $blog_name; ?></strong>
	</div>
            <div id="style"></div>
    <script type="text/JavaScript">
		addStyleSelector(getStyleList());
	</script>
	
	<div class="blog_header">
		<strong>Autor: <?php echo $author;?></strong>	<!--Autor i opis blogu-->
		<p>	
			Opis:
			<?php 
				$file_content = explode(PHP_EOL, file_get_contents('blogs_data'.DIR_SEP.$blog_name.DIR_SEP.'info', LOCK_SH));
				if($file_content[2]!= '')
					echo $file_content[2];
			?>
		</p>
	</div>

<?php 		/* wypisanie zawartosci blogu */

			require 'menu.php';
		
			$files = scandir('blogs_data'.DIR_SEP.$blog_name);  	// wylistowanie wszystkich plikow w folderze blogu
			$files = array_diff($files, array('info', '.', '..')); 	// wykluczenie pliku info oraz . i ..
			
			if(empty($files))	// brak innych plikow == brak wpisow
			{
				echo '<div class="entry">Brak wpisów.</div>';
			}
			else 
			{
				$files = array_reverse($files);		// odwrocenie listy (najnowsze wpisy na gorze strony)
				
				foreach($files as $filename) 		// obsluga kazdego wpisu
				{
					$file_path = 'blogs_data'.DIR_SEP.$blog_name.DIR_SEP.$filename;	// aktualizacja sciezki do obecnego pliku

					if(!is_dir($file_path) && strlen($filename)==16)				// jesli nie jest folderem i jest zgodny z szablonem
					{
						$file_content = explode(PHP_EOL, file_get_contents($file_path, LOCK_SH)); // wydobycie daty z nazwy pliku
						$temp = str_split($filename, 2);										  // podzial stringu na czesci po 2 znaki
						
						$date = $temp[0].$temp[1].'-'.$temp[2].'-'.$temp[3]; 	// sklejenie sformatowanej daty z powstalych czesci
						$time = $temp[4].':'.$temp[5];
											
?>
	<div class="entry">	
	
		<h5>Data dodania: <?php echo $date.' | '.$time;?></h5> 	<!--data wstawienia wpisu-->
		<p> <?php echo $file_content[0]; ?></p> 				<!--tresc wpisu-->

<?php 
						$j = 1; 										// wyszukanie wszystkich zalacznikow
						for ($i=1; $i <= 3; $i++) 
						{
							// glob szuka plikow spelniajacych pattern

							foreach (glob($file_path.$i.".*") as $uploaded_file) 	// wypisywanie hiperlaczy do zalacznikow
							{
								echo '<div class="file_class"><a href="'.$uploaded_file.'">Załącznik '.$j.'</a></div>';
								$j += 1;
							}
						}
					
						$comments_counter = 0;				// licznik komentarzy
						if(file_exists($file_path.'.k')) 	// sprawdzenie istnienia jakichkolwiek komentarzy do wpisu
						{
							$comm_list = array_diff(scandir($file_path.'.k'), array('.', '..')); // lista folderow komentarzy
							$comments_counter = count($comm_list);
						}
						
						echo "<a class='comm_ref' href='koment.php?blog=$blog_name&amp;id=$filename'> Dodaj komentarz </a>";
						if($comments_counter == 0)
						{
							echo "<h5> Nie dodano jeszcze żadnych komentarzy! Bądź pierwszy!</h5>";
						}
						else
						{
							$comm_list = array_reverse($comm_list);
							foreach($comm_list as $single_comm) // wypisanie komentarzy - typ komentarza = kolor tla
							{
									$file_content = explode(PHP_EOL, file_get_contents($file_path.'.k'.DIR_SEP.$single_comm, LOCK_SH));
			
			// formatowanie komentarza, klasa komentarza = nazwa typu komentarza (negatywny itp)						
?>	
		<div class="comment">	<!--wypisanie komentarzy w petli-->					
			<div class="<?php echo $file_content[0]; ?>">
			
				<h5>Autor komentarza: <?php echo $file_content[2]; ?> | <?php echo $file_content[1]; ?></h5>
				<p><?php echo $file_content[3]; ?></p>
				
			</div>
		</div>
<?php 
											
							} // koniec wypisywania dla komentarzy
						}
?>
		</div>
<?php 
					} // koniec obslugi pojedynczego wpisu
				} 	// petla - dla kazdego wpisu	
			}
		}
		else 	// jesli nie istnieje blog o zadanej nazwie 
		{ 
			echo '<div class="entry">Blog o nazwie '.$_GET['nazwa'].' nie istnieje.</div>';
			require 'menu.php';
		}	
	}
?>

</div>

</body>

</html>
