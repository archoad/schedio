<?php
	$temp = explode(DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_NAME']);
	$url = '';
	for ($i=0; $i<=array_search('schedio', $temp); $i++) {
		$url .= $temp[$i].DIRECTORY_SEPARATOR;
	}
	session_destroy();
	unset($_SESSION);
	header('Location: '.$url);
?>
