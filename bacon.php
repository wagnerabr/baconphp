<?php
	require "core/core.php";

	$url = isset($_GET['action']) ? $_GET['action'] : "";

	$bacon = new Core;
	$bacon->run($url);
?>