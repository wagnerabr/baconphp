<?php

	/*
	*  Bacon core
	*/
	define("BACON_VERSION", "0.1");
	define("ROOT", substr($_SERVER["PHP_SELF"],0,-9));
	define("APP", "../");

	/*
	*  Most important paths
	*/
	define("MODEL_PATH", "model/");
	define("VIEW_PATH", "view/");
	define("CONTROLLER_PATH", "controller/");
	define("RESOURCE_PATH", "resource/");
	define("RESOURCE", RESOURCE_PATH);

	/*
	*  Sub paths
	*/
	define("LAYOUT_PATH", "view/layout/");
	define("HELPER_PATH", "view/helper/");
	define("IMAGE_PATH", "resource/img/");
?>