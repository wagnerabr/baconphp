<?php
	class Categories extends Controller
	{
		function index()
		{
			$array = model("categories")->all(null, false);

			out("fromModel", $array);
			
		}
	}
?>