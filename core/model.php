<?php
	/**
	 *	Parent class for all models
	 *	Class that manages, manipulates and obtain records from the database.
	 *
	 */
	class Model
	{
		/**
		 *	The table layout
		 *	Perhaps the most important part of the model. The table layout and
		 *	structure of the model schema is defined in the schema variable.
		 *	<code>
		 *	class PostModel extends Model
		 *	{
		 *		var $schema = array(
		 *				"id" 			=> array("int"),
		 *				"created"		=> array("datetime"),
		 *				"updated"		=> array("datetime"),
		 *				"idcategorie"	=> array("int"),
		 *				"tittle" 		=> array("varchar(30)"),
		 *				"text"			=> array("text")
		 *			);
		 *	
		 *		var $belongsTo = "categorie";
		 *		var $hasMany = "comment";
		 *	}
		 *	</code>
		 */
		public $schema = array();

		/**
		 *	The modelname is automatically populated with the name of the model class
		 */
		public $name = null;

		/**
		 *	The name of the table is based on its model name. Also there is no need to define it.
		 */
		public $tablename = null;

		/**
		 *	Name of the primary key field. By default, a field called "id" is the
		 *	primary key. This should be an int and auto_increment. Also is recommended
		 *	that it is an unsigned field.
		 */
		public $primaryKey = null;

		/**
		 *	Very important attribute. It should contain the name or names an array
		 *	of other models that have reference to an element of the current model.
		 *	Um modelo conter a referencia de outro é quando este tem um campo cuja
		 *	o nome é <primaryKey><modelName> do mesmo tipo da <primaryKey> do módel
		 *	relacionado. Por exemplo:
		 *	<code>
		 *	//model/post.php
		 *	class PostModel extends Model
		 *	{
		 *		var $schema = array(
		 *				"id" 			=> array("int"),
		 *				"created"		=> array("datetime"),
		 *				"updated"		=> array("datetime"),
		 *				"tittle" 		=> array("varchar(30)"),
		 *				"text"			=> array("text")
		 *			);
		 *	
		 *		var $hasMany = "comment"; //name of a model which have an "idpost" field.
		 *	}
		 *
		 *	//model/comment.php
		 *	class CommentModel extends Model
		 *	{
		 *		public $schema = array(
		 *				"id" 		=> array("int"),
		 *				"created"	=> array("datetime"),
		 *				"updated"	=> array("datetime"),
		 *				"idpost" 	=> array("int"), // <primaryKey><modelName> = "id"(post model primary key) + "post"
		 *				"text"		=> array("text"),
		 *				"author" 	=> array("varchar(15)")
		 *			);
		 *	}
		 *	</code>
		 *	In this case, an post selection will bring the related comments. For example:
		 *	model("post")->all(); would bring the following result (array):
		 *	Array
		 *	(
		 *	    [0] => Array
		 *	        (
		 *	            [id] => 1
		 *	            [created] => 2011-10-18 10:44:54
		 *	            [updated] => 2011-10-20 07:39:47
		 *	            [idcategories] => 1
		 *	            [tittle] => "title"
		 *	            [text] => "Lorem ipsun"
		 *	            [comment] => Array //related comments
		 *	                (
		 *	                    [0] => Array
		 *	                        (
		 *	                            [id] => 1
		 *	                            [created] => 2011-10-19 10:19:00
		 *	                            [updated] => 2011-10-20 07:39:47
		 *	                            [idpost] => 1 //<-- this makes the association possible
		 *	                            [text] => "Great!!!"
		 *	                            [author] => "Author's name"
		 *	                        )
		 *	
		 *	                    [1] => Array
		 *	                        (
		 *	                            [id] => 2
		 *	                            [created] => 2011-10-19 10:19:00
		 *	                            [updated] => 2011-10-20 07:39:47
		 *	                            [idpost] => 1 //<-- this makes the association possible
		 *	                            [text] => "Hell yeah!"
		 *	                            [author] => "2nd author"
		 *	                        )
		 *	
		 *	                )
		 *			[...]
		 *	
		 */
		public $hasMany = null;

		/**
		 *	Much like hasMany. The main difference is that it is assumes that there
		 *	is only one related record. Therefore the related field (in a selection)
		 *	have the result itself and not an array [0], [1], [2]... with related records.
		 *	Following the above example, the result would be:
		 *	[...]
		 *	[comment] => Array //related comment
		 *	    (
		 *	        [id] => 1
		 *	        [created] => 2011-10-19 10:19:00
		 *	        [updated] => 2011-10-20 07:39:47
		 *	        [idpost] => 1 //<-- this makes the association possible
		 *	        [text] => "Great!!!"
		 *	        [author] => "Author's name"
		 *	    )
		 *	[...]
		 *
		 *	Instead of:
		 *
		 *	[...]
		 *	[comment] => Array //related comments
		 *	    (
		 *	        [0] => Array
		 *	            (
		 *	                [id] => 1
		 *	                [created] => 2011-10-19 10:19:00
		 *	                [updated] => 2011-10-20 07:39:47
		 *	                [idpost] => 1 //<-- this makes the association possible
		 *	                [text] => "Great!!!"
		 *	                [author] => "Author's name"
		 *	            )
		 *	        [1] => Array
		 *	            (
		 *	[...]
		 */
		public $hasOne = null;

		/**
		 *	This attribute has a role reversal, compared with the hasMany.
		 *	Following the hasMany example, we could have:
		 *	<code>
		 *	//model/comment.php
		 *	class CommentModel extends Model
		 *	{
		 *		public $schema = array(
		 *				"id" 		=> array("int"),
		 *				"created"	=> array("datetime"),
		 *				"updated"	=> array("datetime"),
		 *				"idpost" 	=> array("int"), // <primaryKey><modelName> = "id"(post model primary key) + "post"
		 *				"text"		=> array("text"),
		 *				"author" 	=> array("varchar(15)")
		 *			);
		 *
		 *	    var $belongsTo = "post";
		 *	}
		 *	</code>
		 *	That way we can get a result in the reverse relationship between posts
		 *	and commentaries. An model("comment")->all(); would bring the following
		 *	result (array):
		 *	Array
		 *	    (
		 *	        [0] => Array //<-- A comment retrieved.
		 *	            (
		 *	                [id] => 1
		 *	                [created] => 2011-10-19 10:19:00
		 *	                [updated] => 2011-10-20 07:39:47
		 *	                [idpost] => 1 //<-- this makes the association possible
		 *	                [text] => "Great!!!"
		 *	                [author] => "Author's name"
		 *	                [belongsTo] => Array 
		 *	                    (
		 *	                        [post] => Array //<-- the post where the comment belongs.
		 *	                            (
		 *	                                [id] => 1
		 *	                                [created] => 2011-10-18 10:44:54
		 *	                                [updated] => 2011-10-20 07:39:47
		 *	                                [idcategories] => 1
		 *	                                [tittle] => "title"
		 *	                                [text] => "Lorem ipsun"
		 *	                            )
		 *	                    )
		 *	            )
		 */
		public $belongsTo = null;

		/**
		 *	Store the collum names.
		 */
		private $collums = array();

		/**
		 *	Initializes the model and load all the related models (associated with
		 *	the hasMany, hasOne and belongsTo attributes).
		 *	
		 */
		public function __construct()
		{
			if($this->name === null)
				$this->name = substr(get_class($this),0,-5);

			if($this->tablename === null)
				$this->tablename = strtolower($this->name);

			if(count($this->schema)<1)
			{
				HandleError("model.php","Model ".$this->name." don't have a schema.");
			}else{
				if($this->primaryKey == null) 
				{
					if(array_key_exists("id",$this->schema))
					{
						$this->primaryKey = "id";
					}else{
						HandleError($this->name,"Model ".$this->name." don't have a Primary Key.");
						return;
					}
				}

				$this->hasMany = array_merge((array)$this->hasMany,(array)$this->hasOne);
				$this->belongsTo = (array)$this->belongsTo;

				if(count($this->hasMany) > 0 || count($this->belongsTo) > 0)
				{	
					$this->submodel(array_merge($this->hasMany, $this->belongsTo));
				}

				$this->collums = array_keys($this->schema);
				$this->conn = $this->db_connect();
			}
		}

		/**
		 *	Include related models
		 *	
		 *	@param string model name or model name array
		 */
		private function submodel($submodel)
		{
			if(is_array($submodel))
			{
				foreach($submodel as $thesubmodel)
				{
					$this->submodel($thesubmodel);
				}
			}elseif($submodel != null && $submodel != ""){
				$fullpath = MODEL_PATH.$submodel.".php";
				include_once($fullpath);
			}
		}

		/**
		 *	Open connection.
		 *	
		 */
		private function db_connect()
		{
			if(core::$conn == null)
			{
				global $dbconfig;
				$conn = mysql_connect($dbconfig["host"], $dbconfig["user"], $dbconfig["password"]);
				if(!$conn)
				{
					HandleError("model.php","Could not connect: ");
				}else{
					mysql_select_db($dbconfig["database"], $conn);
				}
				core::$conn = $conn;
			}
			return core::$conn;
		}

		/**
		 *	Executes a query.
		 *	
		 *	@param string The query itself.
		 */
		public function query($query)
		{
			return mysql_query($query, core::$conn);
		}

		/**
		 *	Framework's standard way to obtain records from the database.
		 *	model("post")->all();
		 *	will bring an array cointaining all the fields of all the post records
		 *	and it's relative associated (hasMany, hasOne and belongsTo) records.
		 *	Additionally, by setting the second parameter to false, the associations
		 *	will not be automatically considered. Ex:
		 *	model("post")->all(null, false);
		 *
		 *	The parameters array may have the keys: "fields", "where", "order", "limit" and "assoc"
		 *	for example:
		 *	<code>
		 *	$params = array();
		 *	$params["fields"] = array("id", "name"); //which fields will be selected.
		 *	$params["where"] = array("author = 'johnny'", "id > 5"); //where conditions
		 *	$params["order"] = "id DESC"; //order settings.
		 *	$params["limit"] = "5"; //how many lines records will be returned
		 *	$params["assoc"] = array("comments"); //which associations will be considered. (case $fullassoc parameter = false)
		 *
		 *	print_r( model("post")->all($params, false) );
		 *	</code>
		 *
		 *	Note that the primaryKey will be always in the selecion, even when
		 *	it's not specified in the $params["fields"].
		 *	For a belongTo association to work properly the corresponding foreignKey
		 *	( <primaryKey><modelName> field ) must be in the $params["fields"] or
		 *	$params["fields"] may not be specified.
		 *
		 *	@param array Selection parameters
		 *	@param string Consider associated records.
		 */
		public function all($params = array(), $fullassoc = true)
		{
			/* Strat select query */
			$query = "SELECT ";

			/* Prepare the fields to be selected. Add the primary key to selected fields */
			$params = (array)$params;
			if(array_key_exists("fields", $params))
			{
				$params["fields"] = (array)$params["fields"];
				if(!in_array($this->primaryKey,$params["fields"]))
				{
					$params["fields"] = array_merge((array)$this->primaryKey, $params["fields"]);
				}
				$query .= $this->param_to_fields($params, "fields");
			}else{
				foreach($this->collums as $colname)
				{
					$query .= "`".$colname."`, ";
				}
				$query = substr($query,0,-2);
			}

			/* FROM keyword. Especify the model's table */
			$query .=" FROM `".$this->tablename."`";
			$query .= $this->parametrize($params);

			/* Run query */
			$resource = $this->query($query, $this->conn);
			if(!$resource)
			{
				HandleError("model.php", "nothing returned.");
			}else{
				
				/* Organize the results array */
				$result = $this->organize_results($resource, (array_key_exists("fields", $params)) ? $params["fields"] : $this->collums);

				/* Check for associations and recursively select the related lines */

				if($fullassoc)
				{
					$params["assoc"] = array_merge($this->hasMany,$this->belongsTo);
				}

				if(array_key_exists("assoc", $params)){
					$i = 0;
					$result_assoc = array();
					foreach($result as $line)
					{
						$result_assoc[$i] = array();
						$line = array_merge($line, $this->getAssoc($params["assoc"], $line, true));
						$result_assoc[$i] = array_merge($result_assoc[$i], $line);
						$i++;
						unset($line);
					}
					unset($result); //free non associative result
					$result = $result_assoc; //replace with new array (with associations)
				}
				
				/* Return the processed array */
				return $result;
			}
		}

		/**
		 *	Processes the model associations to include the right records in a all() selection.
		 *	
		 *	@param string Associated model name
		 *	@param array Line where the associated records will be verified
		 *	@param bool Since this method is recursive, this indicate if it's the root run.
		 */
		private function getAssoc($assoc, $line, $root = false)
		{
			$answer = array();
			$type = null;
			if(is_array($assoc))
			{
				foreach($assoc as $theAssoc)
				{
					$answer = array_merge($answer, $this->getAssoc($theAssoc, $line));
				}
			}else{
				if(in_array($assoc, $this->hasMany) && $assoc != null && $assoc != "")
				{
					/* HasOne or HasMany */
					if(!array_key_exists($assoc, $answer))
						$answer[$assoc] = array();
					
					$className = ucfirst($assoc)."Model";
					$obj = new $className();
					$answer[$assoc] = array_merge($answer[$assoc], (array)$obj->all(array("where"=>$this->primaryKey.strtolower($this->name)."=".$line[$this->primaryKey])));
				}else{
					/* BelongsTo */
					if(!array_key_exists("belongsTo", $answer))
						$answer["belongsTo"] = array();

					if(!array_key_exists($assoc, $answer["belongsTo"]))
						$answer["belongsTo"][$assoc] = array();
					
					$className = ucfirst($assoc)."Model";
					$obj = new $className();
					$answer["belongsTo"][$assoc] = array_merge($answer["belongsTo"][$assoc], (array)$obj->all(array("where"=>$obj->primaryKey."=".$line[$obj->primaryKey.$assoc]), false));
					$answer["belongsTo"][$assoc] = $answer["belongsTo"][$assoc][0];
				}
			}

			if($root)
			{
				if(!array_key_exists(1,$answer) && in_array($assoc,(array)$this->hasOne))
				{
					$answer = $answer[0];
				}
			}

			return $answer;
		}

		/**
		 *	Similar to the method all (), but behind only the first record of the result.
		 *	Basically imposes $params["limit"] = 1
		 *
		 *	@param array Selection parameters
		 *	@param bool Consider associated records.
		 */
		public function first($params = array(), $fullassoc = true)
		{
			$params["limit"]="1";
			$result = $this->all($params, $fullassoc);

			if(array_key_exists(0,(array)$result))
				$result = $result[0];

			return $result;
		}

		/**
		 *	Save a record or an array of records into the database. For example:
		 *	<code>
		 *	$results = array();
		 *	$results = model("post")->all($params, false);
		 *
		 *	$results[0]["name"] = "Post's new name!";
		 *	$results[1]["name"] = "In this post too";
		 *	
		 *	model("post")->save($results); //Will save changes in both records
		 *	</code>
		 *	
		 *	It may also insert a new record (if primaryKey is unique or null).
		 *	For example:
		 *	<code>
		 *	$theUser = array();
		 *	$theUser = model("user")->create();
		 *
		 *	$theUser["name"] = "Zizaco";
		 *	$theUser["password"] = "muffin123";
		 *	
		 *	model("user")->save($theUser); //Will insert the new registry
		 *	</code>
		 *
		 *	Important: If there is a field called "updated" (datetime) this will
		 *	always be filled with the date and time of last save. The field "created"
		 *	works similar, but filled with the date and time the record was inserted.
		 *
		 *	Important²: If the record being saved (or array of records) have any
		 *	associated record fields. (Like a comments array inside a post model)
		 *	the save() will be recursive and will affect those related records.
		 *	(comments changes will be saved when running a save on posts, considering
		 *	that "post hasMany comment").
		 *
		 *	@param array Model's corresponding record or array of records.
		 *	@param bool Return the record's primaryKey (result of an primaryKey=null insert).
		 */
		public function save($line, $grabKey = false)
		{
			$resource = false;

			if(array_key_exists("0",$line))
			{
				foreach($line as $realline)
				{
					$resource = $this->save($realline);
				}
			}else{
				$keys = array_keys($line);
				$already_exists = $this->first(array("fields"=>$this->primaryKey, "where"=>$this->primaryKey."=".$line[$this->primaryKey]), false);
				if(count($already_exists)<1)
				{
					/* Entry doesn't exists. Create it */
					if(!$this->validate($line, true))
						return;

					$query = "INSERT INTO `".$this->tablename."` (";
					foreach($keys as $key)
					{
						$query .= " `".$key."`, ";
					}
					$query = substr($query,0,-2);
					$query .= " )";
					$query .= " VALUES (";
					foreach($keys as $key)
					{
						if(in_array($key, $this->hasMany))
						{
							$className = ucfirst($key)."Model";
							$obj = new $className();
							$assoc_element = (array)$line[$key];
							foreach($assoc_element as $element)
							{
								$obj->save($element);
							}
						}elseif($line[$key] == null || $line[$key] == ""){
							$query .= " NULL, ";
						}elseif($key == "created"){
							$query .= " NOW(), ";
						}else{
							global $dbconfig;
							if($dbconfig["utf8_encode"])
							{
								$query .= " '".utf8_decode(addslashes($line[$key]))."', ";
							}else{
								$query .= " '".addslashes($line[$key])."', ";
							}
						}
					}
					$query = substr($query,0,-2);
					$query .= " )";					
					
				}else{
					/* Entry exists. Update it */

					if(!$this->validate($line, true))
						return;

					$query = "UPDATE `".$this->tablename."`";
					$query .= " SET";
					foreach($keys as $key)
					{
						if(in_array($key, $this->hasMany))
						{
							$className = ucfirst($key)."Model";
							$obj = new $className();
							$assoc_element = (array)$line[$key];
							foreach($assoc_element as $element)
							{
								$obj->save($element);
							}
						}elseif($key != $this->primaryKey && $key != "updated" && $key != "belongsTo")
						{
							$query .= " `".$key."`='".addslashes($line[$key])."', ";
						}
					}
					if(array_key_exists("updated", $this->collums))
					{
						$query .= " `updated`=NOW(), ";
					}
					$query = substr($query,0,-2);
					$query .= " WHERE `".$this->primaryKey."`";
					$query .= " = '".$line[$this->primaryKey]."'";
				}
				
				$resource = $this->query($query, $this->conn);

				if($line[$this->primaryKey] == null && $grabKey && $this->validate($line))
				{
					$line[$this->primaryKey] = $this->first(array("fields"=>$this->primaryKey, "order"=>"`".$this->primaryKey."` DESC"), false);
					return $line[$this->primaryKey][$this->primaryKey];
				}
			}

			return $resource;
		}

		public function validate($line, $showError = false, $fieldName = false)
		{
			$valid = true;
			$wrongFields = "";

			if(array_key_exists("0",$line))
			{
				foreach($line as $realline)
				{
					$valid = $valid && $this->validate($realline);
				}
			}else{
				foreach($this->collums as $key)
				{
					if($key == "created" || $key == "updated")
					{
						continue;
					}
					if(array_key_exists($key, $line))
					{
						if(in_array("null", $this->schema[$key])==false && $key != $this->primaryKey)
						{
							if($line[$key] == "" || $line[$key] == null)
							{
								$valid = false;	
							}
						}
					}elseif($line[$this->primaryKey] != "" && $line[$this->primaryKey] != null){
						$valid = false;
					}
					if($valid == false)
					{
						if($showError)
						{
							$ex = new BaconException(721, "", "", "");
							$ex->showError("Model '".$this->name."'' validation '".$key."' cannot be null");
							break;
						}
						if($fieldName)
						{
							if(!is_array($wrongFields))
							{
								$wrongFields = array();
							}
							array_push($wrongFields, $key);
							$valid = true;
						}
					}
				}
			}

			if($fieldName == false)
			{
				return $valid;
			}else{
				return $wrongFields;
			}
		}

		public function invalidField($line)
		{
			if(array_key_exists("0",$line))
			{
				foreach($line as $realline)
				{
					$invalid = array_merge((array)$invalid, $this->invalidField($realline));
				}
			}else{
				$invalid = (array)$this->validate($line, false, true);
			}
			return $invalid;
		}

		/**
		 *	Returns an array containing the structure of a record of the model,
		 *	but completely empty.
		 *	To set default values ​​for the fields, you may override this method.
		 *
		 *	@return array Array containing the structure of a record
		 */
		public function create()
		{
			$theNew = array();
			foreach($this->collums as $colname)
			{
				$theNew[$colname] = null;
			}

			return $theNew;
		}

		/**
		 *	DELETE record (or array of records) based on the primaryKey of it.
		 *	If the second parameter is true, then delete the related records (hasMany
		 *	and hasOne) recursively. Yes it may be dangerous.
		 *
		 *	@param array Record or array of records that will be deleted.
		 *	@param bool If true, performs an recursively deletion within the related records (hasMany and hasOne)
		 */
		public function delete($line, $deleteAssociatedRecords = false)
		{
			if(array_key_exists("0",$line))
			{
				foreach($line as $realline)
				{
					$resource = $this->delete($realline);
				}
			}else{
				if(count($this->hasMany<1))
					$deleteAssociatedRecords = false;

				if($deleteAssociatedRecords)
				{
					$keys = array_keys($line);
					foreach($keys as $key)
					{
						if(in_array($key, $this->hasMany))
						{
							$className = ucfirst($key)."Model";
							$obj = new $className();
							$assoc_element = (array)$line[$key];
							foreach($assoc_element as $element)
							{
								$obj->delete($element);
							}
						}
					}
				}

				$query = "DELETE FROM `".$this->tablename."`";
				$query .= "WHERE `".$this->primaryKey."` = '".$line[$this->primaryKey]."'";
			}

			$resource = $this->query($query, $this->conn);
		}

		/**
		 *	Process the params array of the all() method.
		 *
		 */
		private function parametrize($params)
		{
			$sulfix = "";
			$sulfix .= $this->param_to_sulfix($params, "where", "WHERE");
			$sulfix .= $this->param_to_sulfix($params, "order", "ORDER BY");
			$sulfix .= $this->param_to_sulfix($params, "limit", "LIMIT");

			return $sulfix;
		}

		/**
		 *	Process the $params["fields"] array of the all() method.
		 *
		 */
		private function param_to_fields($params, $sulfix)
		{
			$ret = "";
			$params[$sulfix] = (array)$params[$sulfix];
			
			foreach($params[$sulfix] as $theField)
			{
				if(in_array($theField,$this->collums))
				{
					$ret .= "`".$theField."`, ";
				}else{
					$ex = new BaconException(711, "", "", "");
					$ex->showError("Field '".$theField,"'' don't exists in '".$this->name."'' model.");
				}
			}
			$ret = substr($ret,0,-2);
			
			return $ret;
		}

		/**
		 *	Process the params array of the all() method.
		 *
		 */
		private function param_to_sulfix($params, $sulfix, $syntax)
		{
			$ret = "";

			if(array_key_exists($sulfix,$params))
			{
				$params[$sulfix] = (array)$params[$sulfix];

				$ret .= " ".$syntax." ";
				foreach($params[$sulfix] as $cond)
				{
					$ret .= $cond.", ";
				}
				$ret = substr($ret,0,-2);
			}
			return $ret;
		}

		/**
		 *	Organize the SELECT query result into the framework's default structure: a very
		 *	clean and sleek array. =)
		 *
		 */
		private function organize_results($resource, $collums)
		{
			$result = array();
			$collums = (array)$collums;
			global $dbconfig;

			$i = 0;
			while($resarray = mysql_fetch_array($resource))
			{
				$result[$i] = array();
				foreach($collums as $colname)
				{
					if($dbconfig["utf8_encode"])
					{
						$resarray[$colname] = utf8_encode($resarray[$colname]);
					}
					$result[$i] = array_merge($result[$i], array($colname => $resarray[$colname]));
				}
				$i++;
			}

			return $result;
		}
	}
?>