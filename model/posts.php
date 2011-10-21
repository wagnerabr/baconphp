<?php
	class PostsModel extends Model
	{
		/*
		*  The first field in the schema will always be the primary key.
		*  If a field's name is 'id' then it will be auto_increment and unsigned.
		*/
		var $schema = array(
				"id" 			=> array("int"),
				"created"		=> array("datetime"),
				"updated"		=> array("datetime"),
				"idcategories"	=> array("int"),
				"tittle" 		=> array("varchar(30)"),
				"text"			=> array("text")
			);

		var $belongsTo = "categories";
		var $hasMany = "comments";
	}
?>