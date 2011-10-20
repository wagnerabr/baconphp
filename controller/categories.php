<?php
	class Categories extends Controller
	{
		function index()
		{
			out("fromModel",model("categories")->all(null, true));
		}
	}
?>