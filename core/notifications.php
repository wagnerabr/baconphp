<?php
	function DebugMsg($msg, $var_dump = false)
	{
		echo "<div class='alert-message warning'>";
		if($var_dump)
		{
			echo "<pre>";
			print_r($msg);	
			echo "</pre>";
		}else{
			echo $msg;
		}
		echo "</div>";
	}
?>