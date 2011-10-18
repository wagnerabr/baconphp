<?php
	class Home extends Controller
	{
		function index($foo = "BACON", $bar = "GOOD")
		{
			out("msg",$foo." is friggin ".$bar."!!!");
		}
	}
?>