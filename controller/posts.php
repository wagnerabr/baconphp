<?php
	class Posts extends Controller
	{
		var $helpers = array("html");
		var $models = array("posts");

		function index()
		{
			out("model", $this->models);
			out("view", $this->view);
			out("name", $this->name);
			out("fromModel",model("posts")->all(array("order"=>"id ASC")));

			$xd = model("posts")->all(array("order"=>"id ASC"));
			model("posts")->save($xd);
		}
	}
?>