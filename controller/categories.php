<?php
	class Categories extends Controller
	{
		function index()
		{
			$array = model("categories")->all(null, true);

			out("fromModel", $array);
			
		}
	}
?>