<?php

	include "../view/layout/default.htm.php";
	$action = isset($_GET['action']) ? $_GET['action'] : FALSE;
	$params = explode("/",$action);
	
	echo "<pre>";
	print_r($params);
	echo "</pre>";

?>
<br>Went here