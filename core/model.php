<?php
	class Model
	{
		public $schema = array();
		public $name = null;
		public $tablename = null;
		public $primaryKey = null;
		public $conn = null;
		private $collums = array();

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
				$this->collums = array_keys($this->schema);
				$this->conn = $this->db_connect();
			}
		}

		private function db_connect()
		{
			global $dbconfig;
			$conn = mysql_connect($dbconfig["host"], $dbconfig["user"], $dbconfig["password"]);
			if(!$conn)
			{
				HandleError("model.php","Could not connect: ");
			}else{
				mysql_select_db($dbconfig["database"], $conn);
			}
			$this->conn = $conn;
			return $conn;
		}

		private function db_disconnect()
		{
			mysql_close($this->conn);
		}

		function __destruct() {
			$this->db_disconnect();
		}

		public function query($query)
		{
			return mysql_query($query, $this->conn);
		}

		public function all($params = array())
		{
			$query = "SELECT ";
			if(array_key_exists("fields", $params))
			{
				$query .= $this->param_to_fields($params, "fields");
			}else{
				foreach($this->collums as $colname)
				{
					$query .= "`".$colname."`, ";
				}
				$query = substr($query,0,-2);
			}
			$query .=" FROM `".$this->tablename."`";
			$query .= $this->parametrize($params);

			$resource = $this->query($query, $this->conn);
			if(!$resource)
			{
				HandleError("model.php", "nothing returned.");
			}else{
						
				$result = $this->organize_results($resource, (array_key_exists("fields", $params)) ? $params["fields"] : $this->collums);

				return $result;
			}
		}

		public function first($params = array())
		{
			$params["limit"]="1";
			$result = $this->all($params);

			return $result[0];
		}

		public function save($line) // NEED TO USE MAGIC QUOTES THREATMENT!!!
		{
			if(array_key_exists("0",$line))
			{
				foreach($line as $realline)
				{
					$resource = $this->save($realline);
				}
			}else{
				$keys = array_keys($line);

				$query = "UPDATE `".$this->tablename."`";
				$query .= " SET";
				foreach($keys as $key)
				{
					if($key != $this->primaryKey && $key != "updated")
					{
						$query .= " `".$key."`='".$line[$key]."', ";
					}elseif($key == "updated"){
						$query .= " `".$key."`=NOW(), ";
					}
				}
				$query = substr($query,0,-2);
				$query .= " WHERE `".$this->primaryKey."`";
				$query .= " = '".$line[$this->primaryKey]."'";
				
				echo $query."\n";
				$resource = $this->query($query, $this->conn);
			}

			return $resource;
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