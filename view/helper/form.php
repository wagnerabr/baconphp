<?php
	class FormHelper extends Helper
	{

		private $formaction = "";
		private $goback = "";
		private $errors = null;

		function OpenForm($controller, $action, $errors)
		{
			?>
				<script>
					$(document).ready(function()
					{
						$("[clean_on_focus]").attr("onclick", '$("[clean_on_focus]").attr("value","").removeAttr("clean_on_focus");');
					});
				</script>
			<?php
			$this->formaction = ROOT.$controller."/".$action."/";
			$this->goback = ROOT.$controller."/";
			$this->errors = (array)$errors;
			return "<form name='author' method='post' action='".$this->formaction."' >\n";
		}

		function closeForm()
		{
			$this->formaction = "";
			$this->goback = "";
			return "</form'>".$this->ShowErrors()."\n";
		}

		function InputHidden($name, $value = '')
		{
			return "<input type='hidden' name='".$name."' value='".$value."' />\n";
		}

		function InputText($name, $value = '')
		{
			return "<input type='text' name='".$name."' value='".$value."' />".$this->CheckError($name)."\n";
		}

		function InputPassword($name, $value = '')
		{
			if($value != "" || $value != null)
				$value = "********";
			return "<input clean_on_focus type='password' name='".$name."' value='".$value."' />".$this->CheckError($name)."\n";
		}

		function InputTextArea($name, $value = '')
		{
			return "<textarea name='".$name."'>".$value."</textarea>".$this->CheckError($name)."\n";
		}

		function ButtonSubmit($text = null)
		{
			if($text == null)
			{
				return "<input class='btn primary' type='submit' />\n";
			}else{
				return "<input class='btn primary' type='submit' value='".$text."' />\n";
			}
		}

		function ButtonDiscard($text = 'Discard changes')
		{
			return "<a href='".$this->goback."' ><button type='button' onclick='window.location=\"".$this->goback."\"' class='btn'>".$text."</button></a>\n";
		}

		private function CheckError($fieldname)
		{
			$ret = "";
			foreach($this->errors as $err)
			{
				if($err == $fieldname)
				{
					$ret .= "<div data-alert class='alert-message error'>\n";
					$ret .= "<a class='close' href='#'>&times;</a>\n";
					$ret .= "<strong>Invalid value:</strong> ".ucfirst($fieldname)."\n";
					$ret .= "</div>\n\n";
					unset($this->errors[array_search($fieldname, $this->errors)]);
				}
			}
			return $ret;
		}

		public function ShowErrors()
		{
			$ret = "";
			foreach($this->errors as $err)
			{
				$ret .= "<div data-alert class='alert-message error'>\n";
				$ret .= "<a class='close' href='#'>&times;</a>\n";
				$ret .= ucfirst($err)."\n";
				$ret .= "</div>\n\n";
				unset($this->errors[array_search($err, $this->errors)]);
			}
			return $ret;
		}
	}
?>