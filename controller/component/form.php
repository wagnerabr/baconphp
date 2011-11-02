<?php

	class FormComponent extends Component
	{

		public function validate_form($model_name, &$record)
		{
			$passfiltered = $this->validate_form_pass($record);
			if($passfiltered == null)
			{
				$errors = "Passwords don't match";
			}else{
				$errors = model($model_name)->invalidField($record);
			}

			if($errors == null && $passfiltered != null)
			{
				$record = $passfiltered;
			}

			return $errors;
		}

		public function validate_form_pass($record)
		{
			if(array_key_exists('password', $record))
			{
				if($record['password'] == $record['password2'])
				{
					unset($record['password2']);
					if(trim ($record['password'],'*') == "")
					{
						unset($record['password']);
					}
				}else{
					return false;
				}
			}
			return $record;
		}

	}

?>