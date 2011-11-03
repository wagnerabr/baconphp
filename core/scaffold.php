<?php
	/**
	 *
	 *	BaconPHP is provided under the terms of the zlib/png license. By using this software,
	 *	you agree with the terms of the zlib/png lisence. The license agreement extends to all
	 *	the files within this installation.
	 *
	 *	@copyright Copyright 2011 Luiz Fernando Alves da Silva
	 *
	 *	@license zlib/png license
	 *	This software is provided 'as-is', without any express or
	 *	implied warranty. In no event will the authors be held
	 *	liable for any damages arising from the use of this software.
	 *	
	 *	Permission is granted to anyone to use this software for any purpose,
	 *	including commercial applications, and to alter it and redistribute
	 *	it freely, subject to the following restrictions:
	 *	
	 *	1. The origin of this software must not be misrepresented;
	 *	   you must not claim that you wrote the original software.
	 *	   If you use this software in a product, an acknowledgment
	 *	   in the product documentation would be appreciated but
	 *	   is not required.
	 *	
	 *	2. Altered source versions must be plainly marked as such,
	 *	   and must not be misrepresented as being the original software.
	 *	
	 *	3. This notice may not be removed or altered from any
	 *	   source distribution.
	 *
	 */

	/**
	 *	Scaffold is responsible for generating content based in the models.
	 *
	 */
	class Scaffold
	{
		public function _run()
		{
			$obj = new Scaffold;
			$obj->make_scaffold("author");
		}

		public function make_scaffold($modelname)
		{
			$fullpath = MODEL_PATH.$modelname.".php";
			include_once($fullpath);

			$className = ucfirst($modelname)."Model";
			$model = new $className();
			
			$this->scaffold_controller($model);
		}

		private function scaffold_controller($model)
		{
			$controller = array();
			$controller["fileName"] = CONTROLLER_PATH.strtolower($model->name)."_scaff.php";
			$controller["className"] = $model->name;
			$controller["modelName"] = strtolower($model->name);

			$controller["modelHasPassword"] = false;
			foreach($model->schema as $mcollum)
			{
				if($mcollum[0] == "password")
				{
					$controller["modelHasPassword"] = true;
				}
			}

			$controller["methods"] = array(
				"index" => 	"function index()\n".
							"{\n".
							"	\$all = model('".$controller["modelName"]."')->all();\n".
							"	out(\"all\",\$all);\n".
							"}",
							
				"delete" => "function delete(\$id)\n".
							"{\n".
							"	\$record = model('".$controller["modelName"]."')->first(array(\"where\"=>\"id = \".\$id));\n".
							"	model('".$controller["modelName"]."')->delete(\$record);\n".
							"	header(\"Location: \".ROOT.\"author/\");\n".
							"}",

				"edit" => 	"function edit(\$id = \"\")\n".
							"{\n".
							"	\$post = isset(\$_POST) ? \$_POST : false;\n".
							"\n".
							"	if(\$post)\n".
							"	{\n".
							"		\$errors = component('form')->validate_form('".$controller["modelName"]."', \$post);\n".
							"\n".
							"		if(\$errors == null)\n".
							"		{\n".
							"			model('".$controller["modelName"]."')->save(\$post);\n".
							"			header(\"Location: \".ROOT.\"".$controller["modelName"]."/\");\n".
							"		}else{\n".
							($controller["modelHasPassword"] ? "			\$post['password'] = ''; //The user will have to retype the password\n" : "") .
							"			out(\"errors\",\$errors);\n".
							"			out(\"record\",\$post);\n".
							"		}\n".
							"	}else{\n".
							"		if(\$id != \"\"){\n".
							"			\$record = model('".$controller["modelName"]."')->first(array(\"where\"=>\"id = \".\$id));\n".
							"		}else{\n".
							"			\$record = model('".$controller["modelName"]."')->create();\n".
							"		}\n".
							"		out(\"record\",\$record);\n".
							"	}\n".
							"}"
			);

			DebugMsg($controller,true);

			$file = fopen($controller["fileName"],'w');
			fwrite($file, "<?php\n\tclass ".$controller["className"]." extends Controller\n\t{\n\n");
			fwrite($file, "\t\tvar \$components = \"form\";\n\n");
			foreach($controller["methods"] as $method)
			{
				fwrite($file, "\t\t".str_replace("\n","\n\t\t", $method)."\n\n");
			}
			fwrite($file,"\t}\n?>");
			fclose($file);
		}
	}
?>