<?php
	class Controller
	{
		/**
		 *	The controller's name.
		 */
		public $name = null;

		/**
		 *	The view that will be used to render the results.
		 */
		public $view = null;

		/**
		 *	The layout used in the view.
		 */
		public $layout = "default";

		/**
		 *	The models used (array) by the controller.
		 */
		public $models = null;

		/**
		 *	Intantiated models to be avalible to the view easily.
		 */
		public $model = array();

		/**
		 *	Components used by the controller.
		 */
		public $components = array();

		/**
		 *	Helpers used in controller views.
		 */
		public $helpers = array("html");

		/**
		 *	Current action. The action is basically the method that will run.
		 */
		public $action = "index";

		/**
		 *	Params that will be passed to the action (method)
		 */
		public $params = array();

		/**
		 *	Data that will be available to the view.
		 */
		public $out = array();

		/**
		 *	Initializes the controller.
		 *	
		 *	@param string Basically the method that will run.
		 */
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

		/**
		 *	Run the controller's action and render the final result
		 *	
		 */
		public function _run() {
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

		/**
		 *	Sends data to the view
		 *	out("username", "bob") will make "bob" to be available in the view through $out["username"]
		 *	
		 *	@param string Data name
		 *	@param string Data itself.
		 */
		public function out($name, $value)
		{
			$this->out[$name] = $value;
		}

		/**
		 *	Loads the helpers and executes rendering functions for view and layout.
		 *	
		 */
		private function _render()
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

		/**
		 *	Renders $this->layout
		 *	
		 */
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

		/**
		 *	Renders $this->view
		 *	
		 */
		private function _renderView() {
			
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
	}

	/**
	 *  Used to send data to the view easily.
	 *	out("username", "bob") will make "bob" to be available in the view through $out["username"]
	 *	
	 *	@param string Data name
	 *	@param string Data itself.
	 */
	function out($name, $value)
	{
		global $_ctrl;
		$_ctrl->out($name, $value);
	}

	/**
	 *  Used to access the instantiated models inside the controller methods easly.
	 *	If in the definition of your controller, you declare that it will use the user
	 *	model. An instance of the model will be available in the methods of the
	 *	controller via model("user").
	 *	<code>
	 *	//PurchaseController
	 *	class Purchase extends Controller
	 *	{
	 *		models = array("purchase", "bacon");
	 *	
	 *		function index()
	 *		{
	 *			$allPurchases = model("purchase")->all();
	 *			$firstbacon = model("bacon")->first();
	 *
	 *			out("everything",$allPurchases);
	 *			out("theTastyOne",$firstbacon);
	 *		}
	 *	}
	 *	</code>
	 *	@param string Model name. If it has been previously associated with the controller.
	 */
	function model($name)
	{
		global $_ctrl;
		return $_ctrl->model[$name];
	}
?>