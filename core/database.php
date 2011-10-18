<?php
	require "notifications.php";

	class database
	{
		var $profile = "develop";
		var $db;
		var $conn;	
		
		function __construct()
		{
			$this->db["develop"]["server"] 		="localhost";
			$this->db["develop"]["database"] 	="myfolio";
			$this->db["develop"]["username"] 	="root";
			$this->db["develop"]["pass"] 		="";
			
			$this->db["online"]["server"] 		="localhost";
			$this->db["develop"]["database"] 	="myfolio";
			$this->db["online"]["username"] 	="";
			$this->db["online"]["pass"] 		="";
		}
		
		function setProfile($prof)
		{
			$this->profile = $prof;
		}
		
		function connect()
		{
			$this->conn = mysql_connect(
				$this->db[$this->profile]["server"], 
				$this->db[$this->profile]["username"], 
				$this->db[$this->profile]["pass"]
			);
			if(mysql_errno($conn) != 0)
				HandleError("database->connect", mysql_error($conn)."(".mysql_errno($conn).")");

			mysql_select_db($this->db["develop"]["database"], $this->conn);
			if(mysql_errno($conn) != 0)
				HandleError("database->connect", mysql_error($conn)."(".mysql_errno($conn).")");
			
			return $this->conn;
		}
		
		function query($strSQL)
		{
			$ret = mysql_query($strSQL);
			if(mysql_errno($conn) != 0)
				HandleError("database->query", mysql_error($conn)."(".mysql_errno($conn).")");
			return $ret;
		}
		
		function closeConnection()
		{
			mysql_close($this->conn);
		}
	}
?>