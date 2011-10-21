<?php

	echo $html->toTable($out['fromModel'], array("align" => "center"));

	echo "<br><br>";
	echo "Array Version:<br><pre>";
		  var_dump($out['fromModel']);
		  echo "</pre><br>"; 

?>