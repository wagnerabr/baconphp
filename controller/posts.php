<?php
	class Posts extends Controller
	{
		var $helpers = array("html");
		var $models = array("posts");
		var $components = array("test");

		function index()
		{
			out("model", $this->models);
			out("view", $this->view);
			out("name", $this->name);
			out("fromModel",model("posts")->all(array("fields"=>array("tittle","idcategories"),"order"=>"id ASC", "assoc"=>"comments"))/*model("posts")->all(array("order"=>"id ASC"))*/);

			$xd = model("posts")->all(array("fields"=>array("id","tittle","text","idcategories"),"order"=>"id ASC", "assoc"=>"comments"));
			echo component("test")->ThaFunc();
			//model("posts")->save($xd);
		}
	}
?>