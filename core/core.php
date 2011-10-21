<?php
	require "definitions.php";
	require "util.php";
	require "notifications.php";
	include "controller.php";
	include "model.php";
	include "helper.php";
	include "config/database.php";

	$_ctrl;
	$out;

	/**
	 *  Core é responsavel por gerenciar todas as ações do framework
	 */
	class Core
	{

		private $current_controller;
		public static $conn;

		public function run($url)
		{
			$parsedAction 	= explode("/",$url,2);
			$controller 	= $parsedAction[0];
			$action 		= count($parsedAction)>1 ? $parsedAction[1] : "";

			if($controller == "")
				$controller = "home";
			if($action == "")
				$action = "index";

			$this->run_controller($controller, $action);
		}

		private function run_controller($controller, $action)
		{
			$fullpath = CONTROLLER_PATH.$controller.".php";
			if(file_exists($fullpath) == true)
			{
				include($fullpath);
				$this->current_controller = New $controller($action);
				global $_ctrl;
				$_ctrl = $this->current_controller;

				$this->current_controller->_run();
			}else{
				HandleError("Application","Controller ".$controller." not found.");
			}

			if(core::$conn != null)
			{
				mysql_close(core::$conn);
				core::$conn = null;
			}
		}
	}

?>