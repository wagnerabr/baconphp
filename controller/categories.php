<?php
	class Categories extends Controller
	{
		function index()
		{
			$array = model("categories")->all(null, true);
			out("fromModel", $array);

			$array[0]["posts"][0]["comments"][0]["author"] = "Mr. Ocaziz";
			$array[0]["posts"][1]["tittle"] = "Reborn++";
			model("categories")->save($array);
		}
	}
?>