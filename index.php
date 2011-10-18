<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Bacon Framework - It's tasty!</title>
    <style type="text/css">
	    body{
			font-family: "Gill Sans MT", "Trebuchet MS", "Arial", "Verdana";
			background: #151515;
			color: #FFFFFF;
			font-size: 12px;
			
			margin:0px;
			padding:0px;
		}

		img{  
			border-style: none;
		}

		h1, h2, h3{
			text-transform: uppercase;
		}

		a{
			color: #FF8400;
			text-decoration: inherit;
		}

		a:hover{
			color: #ffB440;
		}

		strong{
			color: #EEA800;
			margin: 0px;
			text-transform: uppercase;
		}

		i{
			color: #AEBFD9;
		}

		h1{
			color: #1A898A;
			line-height: 0px;
			padding-bottom: 15px;
			text-transform: uppercase;
		}

		table{
			background: #333333;
			border: 3px dashed #666666;
			padding: 10px;
			-webkit-box-shadow: 1px 1px 7px #000000; /* Safari */
			box-shadow: 1px 1px 7px #000000;
		}

		.middle{
			background: #222222;
			width: 571px;
			padding: 40px;
			margin: auto;
			text-align: justify;

			-webkit-box-shadow: 1px 1px 7px #000000; /* Safari */
			box-shadow: 1px 1px 7px #000000;
		}

		.notification{
			background: #333333;
			border: 3px dashed #666666;

			padding: 10px;
			margin-top: 20px;

			-webkit-box-shadow: 1px 1px 7px #000000; /* Safari */
			box-shadow: 1px 1px 7px #000000;
		}

		.error{
			background: #903131;
			border-color: #C15F5F;
		}
    </style>
</head>
<body>
	<div class="middle">
		<img src="resource\img\bacon_logo.jpg">
			<div class='notification error'>
				<?php if(!function_exists("apache_get_version"))
				{
	            echo "Bacon só é compatível com Apache";
	            }elseif(!in_array("mod_rewrite", apache_get_modules()))
	            {
	            echo "você precisa habilitar o módulo de reescrita de URL do Apache, o <strong>mod_rewrite</strong>.";
	            }else{
	            echo "É preciso definir a diretiva <strong>AllowOverride</strong> com pelo menos <strong>Options</strong> e <strong>FileInfo";
	        	}
	       		?>
			</div>
	   	</div>
</body>
</html>