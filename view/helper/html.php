<?php
	class HtmlHelper extends Helper
	{
		public function startTag($name, $attributes = array(), $selfclose = false)
		{
			$output = "<".$name;
			$attributename = array_keys($attributes);
			$i = 0;
			foreach($attributes as $value)
			{
				if($value != ""){
					$output .=" ".$attributename[$i]."='".$value."'";
				}else{
					$output .=" ".$attributename[$i];
				}
				$i++;
			}

			if($selfclose){
				$output .=" />";
			}else{
				$output .=">";
			}

			return $output;
		}

		public function endTag($name)
		{
			$output = "</".$name.">";
			return $output;
		}

		public function favicon($href)
		{
			$attributes = array( "rel" => "shortcut icon", "href" => ROOT.RESOURCE_PATH.$href, "type" => "image/vnd.microsoft.icon");
			return $this->startTag("link",$attributes,true)."\n";
		}

		public function stylesheet($href)
		{
			$attributes = array( "rel" => "stylesheet", "type" => "text/css", "href" => ROOT.RESOURCE_PATH.$href);
			return $this->startTag("link",$attributes,true)."\n";
		}

		public function image($src, $name = "image", $full = false)
		{
			$attributes = array( "src" => ROOT.IMAGE_PATH.$src, "alt" => $name);
			return $this->startTag("img",$attributes,true);
		}

		public function notifications()
		{
			global $errors, $warnings, $messages;

			if(count($errors)>0)
			{
				echo $this->startTag("div",array("class" => "notification error"))."\n";
			    echo ShowErrors();
			    echo $this->endTag("div")."\n";
		    }
		    
		    if(count($warnings)>0)
			{
				echo $this->startTag("div",array("class" => "notification warning"))."\n";
			    echo ShowWarnings();
			    echo $this->endTag("div")."\n";
		    }

		    if(count($messages)>0)
			{
				echo $this->startTag("div",array("class" => "notification"));
			    echo ShowMessages();
			    echo $this->endTag("div")."\n";
		    }
		}

		public function toTable($array, $attributes, $recursive = false)
		{
			if(is_array($array) && array_key_exists(0,$array))
			{
				$keys = array_keys($array[0]);

				$table  = $this->startTag("table", $attributes)."\n";
				$table .= $this->startTag("tr")."\n";
				foreach($keys as $key)
				{
					$table .= $this->startTag("td", array("class"=>"tableheader"));
					$table .= $this->startTag("strong");
					$table .= $key;
					$table .= $this->endTag("strong");
					$table .= $this->endTag("td")."\n";
				}
				$table .= $this->endTag("tr")."\n";
				foreach($array as $line)
				{
					$table .= $this->startTag("tr")."\n";
					foreach($keys as $key)
					{
						$table .= $this->startTag("td", array("class"=>"tablecell"));
						if(is_array($line[$key]))
						{
							if($recursive)
							{
								$table .= $this->toTable($line[$key], $attributes, true);
							}else{
								$table .= count($line[$key])." elements";
							}

						}else{
							$table .= $line[$key];	
						}
						$table .= $this->endTag("td")."\n";
					}
					$table .= $this->endTag("tr")."\n";
				}
				$table .= $this->endTag("table")."\n";

			}else{
				$table  = $this->startTag("table", $attributes)."\n";
				$table .= $this->startTag("tr")."\n";
				$table .= $this->startTag("td", array("class"=>"tableheader"));
				$table .= $this->startTag("strong");
				$table .= "NULL";
				$table .= $this->endTag("strong");
				$table .= $this->endTag("td")."\n";
				$table .= $this->endTag("table")."\n";
			}

			return $table;
		}
	}
?>