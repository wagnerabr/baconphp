<?php
	$errors = array();
	$warnings = array();
	$messages = array();

	function HandleError($where, $msg)
	{
		global $errors;

		$i = count($errors);

		$errors[$i] = $where.": ".$msg;
	}

	function ShowErrors()
	{
		global $errors;
		$ret = "";

		foreach($errors as $msg)
		{
			$ret = $ret.$msg."\n";;
		}

		return $ret;
	}

	function HandleWarning($where, $msg)
	{
		global $warnings;

		$i = count($warnings);

		$warnings[$i] = $where.": ".$msg;
	}

	function ShowWarnings()
	{
		global $warnings;
		$ret = "";

		foreach($warnings as $msg)
		{
			$ret = $ret.$msg."\n";;
		}

		return $ret;
	}

	function HandleMessage($where, $msg)
	{
		global $messages;

		$i = count($messages);

		$messages[$i] = $where.": ".$msg;
	}

	function ShowMessages()
	{
		global $messages;
		$ret = "";

		foreach($messages as $msg)
		{
			$ret = $ret.$msg."\n";;
		}

		return $ret;
	}
	function DebugMsg($msg)
	{
		echo "<div class='notification debug'>";
		echo $msg;
		echo "</div>";
	}
?>