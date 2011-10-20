<?php
	class Controller
	{
		public $name = null;
		public $view = null;
		public $layout = "default";
		public $models = null;
		public $model = array(); //instanciated models
		public $components = array();
		public $helpers = array("html");
		public $action = "index";
		public $params = array();
		public $out = array();

		public function __construct($action = "index") {

			$parsedAction 	= explode("/",$action,2);
			$action			= $parsedAction[0];
			$params 		= count($parsedAction)>1 ? $parsedAction[1] : "";

			if(substr_count($params,"/")>0)
			$params = explode("/",$params);

			if($this->name === null)
				$this->name = get_class($this);

			if($this->view === null)
				$this->view = strtolower($this->name);

			if(count($this->models) == null)
				$this->models = (array)strtolower($this->name);

			$this->action = $action;
			$this->params = $params;

			/* include the models */
			foreach($this->models as $theModel)
			{
				$fullpath = MODEL_PATH.$theModel.".php";
				if(file_exists($fullpath) == true)
				{
					include_once($fullpath);
					$className = ucfirst($theModel)."Model";
					$this->model[$theModel] = New $className();
				}else{
					HandleError("Controller".$this->name,"Model ".$theModel." not found.");
				}
			}


			HandleMessage("controller.php","Class constructed!");

		}

		function _run() {
			global $posts;

			if(method_exists($this, $this->action))
			{
				if(is_array($this->params))
				{
					call_user_func_array(array($this, $this->action), $this->params);
				}elseif($this->params != ""){
					call_user_func(array($this, $this->action), $this->params);
				}else{
					call_user_func(array($this, $this->action));
				}
				$this->_render();
			}else{
				//handle error
				echo "<br>method not found";
			}

		}

		function out($name, $value)
		{
			$this->out[$name] = $value;
		}

		function _render()
		{

			foreach($this->helpers as $helper)
			{
				$fullpath = HELPER_PATH.$helper.".php";
				if(file_exists($fullpath) == true)
				{
					include($fullpath);
					$theVar = $helper;
					$instance = New $theVar();
					global $$theVar;
					$$theVar = $instance;
				}else{
					HandleError("Controller".$this->name,"Helper ".$helper." not found.");
				}
			}

			if($this->layout != null)
			{
				$this->_renderLayout();
			}else{
				_renderView();
			}
		}

		private function _renderLayout()
		{

			foreach($this->helpers as $helper)
			{
				$theVar = $helper;
				global $$theVar;
			}

			$out = $this->out;
			ob_start();
				$this->_renderView();
			$view = ob_get_clean();

			include LAYOUT_PATH.$this->layout.".html.php";
		}

		function _renderView() {
			
			/* View's filename is <view>_<action>.html.php; Secundary is <view>.html.php */
			$filename = VIEW_PATH.strtolower($this->view)."_".$this->action.".html.php";
			if(!file_exists($filename))
			{
				$filename = VIEW_PATH.strtolower($this->view).".html.php";
			}

			if(file_exists($filename))
			{
				/* Load related helpers */
				foreach($this->helpers as $helper)
				{
					$theVar = $helper;
					global $$theVar;
				}

				global $out;
				$out = $this->out;

				/* Run file */
				include $filename;
			}else{
				echo "view ".$this->view." not found";
			}
		}

		private function _loadModel($modelName) {
			
		}
	}

	function out($name, $value)
	{
		global $_ctrl;
		$_ctrl->out($name, $value);
	}

	function model($name)
	{
		global $_ctrl;
		return $_ctrl->model[$name];
	}
?>