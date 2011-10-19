<?php	
	function include_folder ($directory)
	{
		// create a handler for the directory
		$handler = opendir($directory);

		// open directory and walk through the filenames
		while ($file = readdir($handler)) {

			// if file isn't a directory exclude ir
			if ($file != "." && $file != ".." && substr($file, -3) == "php") {

				// include each file found
				include $directory."/".$file;
			}
		}

		// tidy up: close the handler
		closedir($handler);
	}

	function to_array($value)
	{
			return (array)$value;
	}
?>