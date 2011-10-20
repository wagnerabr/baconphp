<?php
	class Model
	{
		public $schema = array();
		public $name = null;
		public $tablename = null;
		public $primaryKey = null;
		public $hasMany = null;
		public $hasOne = null;
		public $belongsTo = null;

		private $collums = array();
		private static $conn = null;

		public function __construct($action = "index")
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
				if($this->hasMany != null && count($this->hasMany) > 0)
				{	
					$this->submodel($this->hasMany);
				}

				$this->collums = array_keys($this->schema);
				$this->conn = $this->db_connect();
			}
		}

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

		private function db_disconnect()
		{
			/*mysql_close(core::$conn);
			core::$conn = null;*/
		}

		function __destruct() {
			$this->db_disconnect();
		}

		public function query($query)
		{
			return mysql_query($query, core::$conn);
		}

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
					$params["assoc"] = $this->hasMany;
				}

				if(array_key_exists("assoc", $params)){
					$i = 0;
					$result_assoc = array();
					foreach($result as $line)
					{
						$result_assoc[$i] = array();
						$line = array_merge($line, $this->getAssoc($params["assoc"], $line[$this->primaryKey], true));
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

		private function getAssoc($assoc, $id, $root = false)
		{
			$answer = array();
			$type = null;
			if(is_array($assoc))
			{
				foreach($assoc as $theAssoc)
				{
					$answer = array_merge($answer, $this->getAssoc($theAssoc, $id));
				}
			}else{
				if(in_array($assoc, $this->hasMany) && $assoc != null && $assoc != "")
				{
					if(!array_key_exists($assoc, $answer))
						$answer[$assoc] = array();
					
					$className = ucfirst($assoc)."Model";
					$obj = new $className();
					$answer[$assoc] = array_merge($answer[$assoc], (array)$obj->all(array("where"=>$this->primaryKey.strtolower($this->name)."=".$id)));
				}else{
					/**
						belongsTo
					*/
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

		public function first($params = array(), $fullassoc = true)
		{
			$params["limit"]="1";
			$result = $this->all($params, $fullassoc);

			if(array_key_exists(0,(array)$result))
				$result = $result[0];

			return $result;
		}

		public function save($line, $grabKey = false)
		{
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
						if($line[$key] == null || $line[$key] == "")
						{
							$query .= " NULL, ";
						}elseif($key == "created"){
							$query .= " NOW(), ";
						}else{
							$query .= " '".addslashes($line[$key])."', ";
						}
					}
					$query = substr($query,0,-2);
					$query .= " )";					
					
				}else{
					/* Entry exists. Update it */

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
						}elseif($key != $this->primaryKey && $key != "updated")
						{
							$query .= " `".$key."`='".addslashes($line[$key])."', ";
						}elseif($key == "updated"){
							$query .= " `".$key."`=NOW(), ";
						}
					}
					$query = substr($query,0,-2);
					$query .= " WHERE `".$this->primaryKey."`";
					$query .= " = '".$line[$this->primaryKey]."'";
				}
				
				$resource = $this->query($query, $this->conn);

				if($line[$this->primaryKey] == null && $grabKey)
				{
					$line[$this->primaryKey] = $this->first(array("fields"=>$this->primaryKey, "order"=>"`".$this->primaryKey."` DESC"), false);
					return $line[$this->primaryKey][$this->primaryKey];
				}
			}

			return $resource;
		}

		public function create()
		{
			$theNew = array();
			foreach($this->collums as $colname)
			{
				$theNew[$colname] = null;
			}

			return $theNew;
		}

		public function delete($line)
		{
			if(array_key_exists("0",$line))
			{
				foreach($line as $realline)
				{
					$resource = $this->delete($realline);
				}
			}else{
				$query = "DELETE FROM `".$this->tablename."`";
				$query = "WHERE `".$this->primaryKey."` = '".$line[$this->primaryKey]."'";
			}

			$resource = $this->query($query, $this->conn);
		}

		private function parametrize($params)
		{
			$sulfix = "";
			$sulfix .= $this->param_to_sulfix($params, "where", "WHERE");
			$sulfix .= $this->param_to_sulfix($params, "order", "ORDER BY");
			$sulfix .= $this->param_to_sulfix($params, "limit", "LIMIT");

			return $sulfix;
		}

		private function param_to_fields($params, $sulfix)
		{
			$ret = "";

			if(array_key_exists($sulfix,$params))
			{
				if(is_array($params[$sulfix]))
				{
					foreach($params[$sulfix] as $cond)
					{
						$ret .= "`".$cond."`, ";
					}
					$ret = substr($ret,0,-2);
				}else{
					$ret .= "`".$params[$sulfix]."`";
				}
			}
			return $ret;
		}

		private function param_to_sulfix($params, $sulfix, $syntax)
		{
			$ret = "";

			if(array_key_exists($sulfix,$params))
			{
				$ret .= " ".$syntax." ";
				if(is_array($params[$sulfix]))
				{
					foreach($params[$sulfix] as $cond)
					{
						$ret .= $cond.", ";
					}
					$ret = substr($ret,0,-2);
				}else{
					$ret .= $params[$sulfix];
				}
			}
			return $ret;
		}

		private function organize_results($resource, $collums)
		{
			$result = array();

			$i = 0;
			while($resarray = mysql_fetch_array($resource))
			{
				$result[$i] = array();
				if(is_array($collums))
				{
					foreach($collums as $colname)
					{
						$result[$i] = array_merge($result[$i], array($colname => $resarray[$colname]));
					}
				}else{
					$result[$i] = array_merge($result[$i], array($collums => $resarray[$collums]));
				}
				$i++;
			}

			return $result;
		}
	}
?>