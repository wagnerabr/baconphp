<?php
	class BaconException extends Exception
	{

		public $cod		= 0;
		public $msg		= "";
		public $file 		= "";
		public $line 		= 0;
		private $friendlyMsg	= "";

		public function __construct( $cod = 0, $msg, $file, $line )
		{
			$chopfile = strrchr($file, "\\");
			if($chopfile == false)
				$chopfile = strrchr($file, "/");

			$this->cod = $cod;
			$this->msg = $msg;
			$this->file = $chopfile;
			$this->line = $line;
			$this->friendlyMsg = "";

			parent::__construct($msg,$cod);
		}

		public function showError($friendlyMsg = "")
		{
			$this->friendlyMsg = $friendlyMsg;

			echo "<div class='alert-message error'>";
			if($this->friendlyMsg != "")
			{
				echo "Error: #".$this->cod." - (".$this->file.") ".$this->friendlyMsg;	
			}else{
				echo "Error: #".$this->cod." - (".$this->file." line ".$this->line.")".$this->msg;	
			}
			echo "</div>";
		}

		public function writeLog()
		{
			$file = "bacon_error.txt";
			$text = "";
			if(!file_exists($file)){
				$text .= "#########################################\n";
				$text .= "#           BaconPHP ErrorLog           #\n";
				$text .= "#                                       #\n";
				$text .= "#########################################\n\n";	
			}

			$text .= date("d/m/Y H:i")."________________\n";
			if($this->friendlyMsg != "")
			{
				$text .= "Error: #".$this->cod." - (".$file." line ".$line.")".$this->friendlyMsg."\n\n";
			}else{
				$text .= "Error: #".$this->cod." - (".$file." line ".$line.")".$this->msg;	
			}

			if($stream = fopen($file))
			{
				fwrite( $stream, $text );
				fclose( $stream );
			}
		}

		static public function throwError( $cod, $msg, $file, $line )
		{
			throw new BaconException($cod, $msg, $file, $line);
		}

		static public function throwException( $ex )
		{
			$ex->showError();
		}
	}

	set_error_handler(array("BaconException","throwError"), E_ALL );
	set_exception_handler(array("BaconException","throwException"));
?>