<?php

define('DIR_SEP', DIRECTORY_SEPARATOR);
define('MIN', 4);
define('MAX', 32);

function str_len_between($str, $min_length, $max_length)
{
	if (strlen($str)> $min_length && strlen($str) <= $max_length) {
		return true;
	} 
	else {
		return false;
	}
}

?>