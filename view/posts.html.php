<?php
echo "<strong>Teste:</strong><br><br>";

echo "<strong>Controller Name: </strong>".$out['name']."<br>";
echo "<strong>View: </strong>". $out['view']."<br>";
echo "<strong>model: </strong>". $out['model'][0]."<br>";

echo "<strong>".$out['model'][0]."->all() result: </strong>";
echo $html->toTable($out['fromModel'], array("align" => "center"));

echo "<br><br>";
echo "<strong>".$out['model'][0]."->all() array: </strong>";
echo "Array Version:<br><pre>";
	  print_r($out['fromModel']);
	  echo "</pre><br>"; 

?>