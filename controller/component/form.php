<?php

	class FormComponent extends Component
	{

		public function validate_form($model_name, &$record)
		{
			$imagefiltered = $this->validate_form_image($model_name, $record);
			if($imagefiltered == null)
			{
				$errors = "Error while uploading image";
			}else{
				$passfiltered = $this->validate_form_pass($imagefiltered);
				if($passfiltered == null)
				{
					$errors = "Passwords don't match";
				}else{
					$errors = model($model_name)->invalidField($record);
				}
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

		public function validate_form_image($model_name, &$record)
		{
			$theModel = model($model_name);
			if(is_array($theModel->image))
			{
				$record["image"]=model($model_name)->image[0]."/default.jpg";
			}else{
				return $record;
			}

			if(isset($_FILES) && $_FILES['image']['name'] != "")
			{
				try{

					$ext = substr($_FILES['image']['name'],-3);
					$destiny = IMAGE_PATH.$theModel->image[0]."/pending.jpg";
					$source_image = $_FILES['image']['tmp_name'];
					if($ext == "jpg" || $ext == "peg")
					{
						$source_image = imagecreatefromjpeg($source_image);
					}elseif($ext == "png"){
						$source_image = imagecreatefrompng($source_image);
					}elseif($ext == "gif"){
						$source_image = imagecreatefromgif($source_image);
					}elseif($ext == "bmp"){
						$source_image = imagecreatefromwbmp($source_image);
					}else{
						return false;
					}

					if($ext != "jpg" && $ext != "png" && $ext != "bmp" && $ext != "gif")
					{
						return false;
					}

					list($width, $height) = getimagesize($_FILES['image']['tmp_name']);
					$smalldimen = $width < $height ? $width : $height;

					$xx = ($width / 2) - $smalldimen / 2;
					$yy = ($height / 2) - $smalldimen / 2;

					$resized = imagecreatetruecolor($theModel->image[1], $theModel->image[2]);
					if(!imagecopyresampled($resized, $source_image, 0, 0, $xx, $yy, $theModel->image[1], $theModel->image[2], $smalldimen, $smalldimen))
					{
						return false;
					}

					if(!imagejpeg($resized, $destiny, 100))
					{
						return false;
					}
				}catch(BaconException $ex){
					$ex->showError("Error while uploading image.");
					return false;
				}
			}else{
				return $record;
			}
		}
	}

?>