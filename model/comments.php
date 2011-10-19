<?php
	class CommentsModel extends Model
	{
		/*
		*  The first field in the schema will always be the primary key.
		*  If a field's name is 'id' then it will be auto_increment and unsigned.
		*/
		public $schema = array(
				"id" 		=> array("int"),
				"created"	=> array("datetime"),
				"updated"	=> array("datetime"),
				"idposts" 	=> array("int"),
				"text"		=> array("text"),
				"author" 	=> array("varchar(15)")
			);
	}
?>