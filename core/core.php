<?php
	/**
	 *
	 *	BaconPHP is provided under the terms of the zlib/png license. By using this software,
	 *	you agree with the terms of the zlib/png lisence. The license agreement extends to all
	 *	the files within this installation.
	 *
	 *	@copyright Copyright 2011 Luiz Fernando Alves da Silva
	 *
	 *	@license zlib/png license
	 *	This software is provided 'as-is', without any express or
	 *	implied warranty. In no event will the authors be held
	 *	liable for any damages arising from the use of this software.
	 *	
	 *	Permission is granted to anyone to use this software for any purpose,
	 *	including commercial applications, and to alter it and redistribute
	 *	it freely, subject to the following restrictions:
	 *	
	 *	1. The origin of this software must not be misrepresented;
	 *	   you must not claim that you wrote the original software.
	 *	   If you use this software in a product, an acknowledgment
	 *	   in the product documentation would be appreciated but
	 *	   is not required.
	 *	
	 *	2. Altered source versions must be plainly marked as such,
	 *	   and must not be misrepresented as being the original software.
	 *	
	 *	3. This notice may not be removed or altered from any
	 *	   source distribution.
	 *
	 */

	require "exception.php";
	require "definitions.php";
	require "notifications.php";
	require "controller.php";
	require "model.php";
	require "helper.php";
	require "config/database.php";
	
	set_error_handler(array("BaconException","throwError"), E_ALL );

	/**
	 *	@global Controller a global pointer to the current controller.
	 */
	$_ctrl;

	/**
	 *	@global Used to send data to the view easily.
	 */
	$out;

	/**
	 *	Core is responsible for managing all activities of the framework.
	 *
	 */
	class Core
	{
		/**
		 *	The controller that was accessed and that should run.
		 */
		private $current_controller;

		/**
		 *	The connection that will be established with the database.
		 */
		public static $conn;

		/**
		 *	Initializes and runs the framework using the provided link.
		 *	
		 *	@param string URL accessed
		 */
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

		/**
		 *	Loads and runs the appropriate controller
		 *	
		 *	@param string Controller name
		 *	@param string Action and parameters
		 */
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