<?php

	echo $html->toTable($out['fromModel'], array("align" => "center"));

	echo "<br><br>";
	echo "Array Version:<br><pre>";
		  print_r($out['fromModel']);
		  echo "</pre><br>"; 

?>