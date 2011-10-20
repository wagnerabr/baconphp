<?php
	class CategoriesModel extends Model
	{
		var $schema = array(
				"id" 		=> array("int"),
				"created"	=> array("datetime"),
				"updated"	=> array("datetime"),
				"name" 		=> array("varchar(30)")
			);

		var $hasMany = "posts";
	}
?>