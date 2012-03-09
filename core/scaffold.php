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
			$obj->make_scaffold("user");
		}

		public function make_scaffold($modelname)
		{
			$fullpath = MODEL_PATH.$modelname.".php";
			include_once($fullpath);

			$className = ucfirst($modelname)."Model";
			$model = new $className();
			
			//$this->scaffold_controller($model);
			//$this->scaffold_db($model);
			$this->scaffold_view($model);
		}

		private function scaffold_db($model)
		{
			$migration = array();
			$migration["fileName"] = MIGRATION_PATH.date("YmdHis")."_".strtolower($model->name).".php";
			$migration["modelName"] = strtolower($model->name);
			$migration["primaryKey"] = $model->primaryKey;

			$migration["start"] = "CREATE TABLE IF NOT EXISTS `".$migration['modelName']."` (\n";
			$migration["fields"] = array();
			$keys = array_keys($model->schema);
			$i = 0;
			foreach($keys as $mcollum)
			{
				$type = ""; $enum = false;
				foreach($model->schema[$mcollum] as $def)
				{
					if($enum){
						foreach($def as $subdef){
							$type .= " \'".$subdef."\',";
						}
						$type = substr($type,0,-1);
						$type .= " )";
						$enum = false;
					}elseif($def == "int")
						$type .= " int(10)";
					elseif($def == "varchar")
						$type .= " varchar(20)";
					elseif($def == "password")
						$type .= " varchar(32)";
					elseif($def == "enum"){
						$type .= " enum(";
						$enum = true;
					}else{
						$type .= " ".$def;
					}
				}
				$migration["fields"][$i] = "	`".$mcollum."`".$type;
				if($mcollum == $migration["primaryKey"]){
					$migration["fields"][$i] .= " AUTO_INCREMENT";
				}
				$migration["fields"][$i] .= ",\n";
				$i++;
			}
			$migration["end"]  = "		PRIMARY KEY(`".$migration["primaryKey"]."`)\n";
			$migration["end"] .= "		) AUTO_INCREMENT=1 ;";

			$file = fopen($migration["fileName"],'w');
			fwrite($file, "<?php\n\t\$sql = '".$migration["start"]."\t");
			foreach($migration["fields"] as $field)
			{
				fwrite($file, "\t".str_replace("\n","\n\t", $field));
			}
			fwrite($file, $migration["end"]."';\n");
			fwrite($file, "\n\t\$clear = 'DROP TABLE IF EXISTS `".$migration['modelName']."`;';\n?>");
			fclose($file);

			DebugMsg($migration,true);

			include $migration["fileName"];
			$model->query($clear);
			$model->query($sql);
			DebugMsg(mysql_error());

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

		private function scaffold_view($model, $show_created = false, $show_updated = false, $show_id = false)
		{
			$object["fields"] = array();
			$keys = array_keys($model->schema);
			$i = 0;
			foreach($keys as $mcollum)
			{
				$formMethod = ""; $enum = false; $listParams = "";
				foreach($model->schema[$mcollum] as $def)
				{
					if($enum){
						foreach($def as $subdef){
							$listParams .= " '".$subdef."',";
						}
						$listParams = substr($listParams,0,-1);
						$enum = false;
					}elseif($def == "password"){
						$formMethod .= "InputPassword";
					}elseif($def == "enum"){
						$formMethod .= "InputRadio";
						$enum = true;
					}else{
						$formMethod .= "InputText";
					}
				}

				$object["headers"][$i] = $mcollum;
				$object["fields"][$i] = "";
				if($formMethod == "InputRadio")
				{
					$object["fields"][$i] = "\t\t<tr>\n".
											"\t\t\t<td>".ucfirst($mcollum)."</td>\n".
											"\t\t\t<td><?php echo \$form->".$formMethod."(\"".$mcollum."\", array(".$listParams."), \$r[\"".$mcollum."\"]) ?></td>\n".
											"\t\t</tr>\n";
				}elseif($formMethod == "InputPassword"){
					$object["fields"][$i] = "\t\t<tr>\n".
											"\t\t\t<td>".ucfirst($mcollum)."</td>\n".
											"\t\t\t<td><?php echo \$form->".$formMethod."(\"".$mcollum."\", \$r[\"".$mcollum."\"]) ?></td>\n".
											"\t\t</tr>\n".
											"\t\t<tr>\n".
											"\t\t\t<td>Verify ".ucfirst($mcollum)."</td>\n".
											"\t\t\t<td><?php echo \$form->".$formMethod."(\"".$mcollum."2\", \$r[\"".$mcollum."\"]) ?></td>\n".
											"\t\t</tr>\n";
				}elseif($mcollum != 'created' && $mcollum != 'updated' && $mcollum != 'id'){
					$object["fields"][$i] = "\t\t<tr>\n".
											"\t\t\t<td>".ucfirst($mcollum)."</td>\n".
											"\t\t\t<td><?php echo \$form->".$formMethod."(\"".$mcollum."\", \$r[\"".$mcollum."\"]) ?></td>\n".
											"\t\t</tr>\n";
				}
				$i++;
			}

			$view_index["fileName"] = VIEW_PATH.strtolower($model->name)."_scaff.html.php";
			$view_index["start"] =	"<?php echo \$modal->SetupModal(); ?>\n".
									"<table class='table table-striped table-bordered'>\n".
									"	<?php if(count(\$out[\"all\"]) < 1) { ?>\n".
									"		<tr>\n".
									"			<td>No records were found</td>\n".
									"		</tr>\n".
									"	<?php }else{ ?>\n";

			$view_index["middle"] =	"		<tr>\n";
			foreach($object["headers"] as $header)
			{									
				if($show_created && $header == 'created' || $show_updated && $header == 'updated' || $show_id && $header == 'id' || $header != 'created' && $header != 'updated' && $header != 'id' && $header != 'password')
					$view_index["middle"] .= "			<th>".ucfirst($header)."</th>\n";
			}
			$view_index["middle"] .= "			<th>Action</th>\n".
									 "		</tr>\n".
									 "		<?php foreach( \$out[\"all\"] as \$i){ ?>\n".
									 "			<tr>\n";
			foreach($object["headers"] as $header)
			{
				if($show_created && $header == 'created' || $show_updated && $header == 'updated' || $show_id && $header == 'id' || $header != 'created' && $header != 'updated' && $header != 'id' && $header != 'password')									
					$view_index["middle"] .= "				<td><?php echo \$i['".$header."']; ?></td>\n";
			}
			$view_index["middle"] .= "				<td>\n".
									 "					<?php echo \$html->link(\"".strtolower($model->name)."/edit/\".\$i[\"id\"],\"Edit\", array(\"class\"=>\"btn btn-small\")); ?>\n".
									 "					\n".
									 "					<?php echo \$html->link(\"#the_modal_form\",\"Delete\", array(\"class\"=>\"btn btn-small\", \"onclick\"=>\$modal->ShowModal(\"".strtolower($model->name)."/delete/\".\$i[\"id\"], \"Warning\", \"Are you sure you want to delete \".\$i[\"name\"].\".\", \"Yes\", \"btn btn-danger\"),'data-toggle'=>'modal')); ?>\n".
									 "				</td>\n".
									 "			</tr>\n";
		
			$view_index["end"] =	"		<?php } ?>\n".
									"	<?php } ?>\n".
									"</table>\n".
									"<?php echo \$html->link('".$model->name."/edit',\"<i class='icon-plus icon-white'></i> Create new\",array('class'=>'btn btn-primary')); ?>\n";

			$file = fopen($view_index["fileName"],'w');
			fwrite($file, $view_index["start"]);
			fwrite($file, $view_index["middle"]);
			fwrite($file, $view_index["end"]);
			fclose($file);

			$view_edit["fileName"] = VIEW_PATH.strtolower($model->name)."_edit_scaff.html.php";
			$view_edit["start"] = 	"<?php \$r = \$out[\"record\"]; ?>\n".
									"<?php echo \$form->OpenForm(\"user\",\"edit\", isset(\$out[\"errors\"]) ? \$out[\"errors\"] : null); ?>\n".
									"	<?php echo \$form->InputHidden(\"id\", \$r[\"id\"]); ?>\n".
									"	<table class='table' style='width:400px; margin:auto;'>\n".

			$view_edit["middle"] = "";
			foreach($object["fields"] as $field)
			{
				$view_edit["middle"] .= $field;
			}

			$view_edit["end"] = 	"		<tr>\n".
									"			<td colspan='2' class='well'>\n".
									"				<?php echo \$form->ButtonSubmit() ?>\n".
									"				<?php echo \$form->ButtonDiscard(\"Cancelar\") ?>\n".
									"			</td>\n".
									"		</tr>\n".
									"	</table>\n".
									"<?php echo \$form->closeForm(); ?>\n";

			$file = fopen($view_edit["fileName"],'w');
			fwrite($file, $view_edit["start"]);
			fwrite($file, $view_edit["middle"]);
			fwrite($file, $view_edit["end"]);
			fclose($file);
		}
	}
?>