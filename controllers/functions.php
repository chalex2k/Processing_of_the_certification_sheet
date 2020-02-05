<?php
	function query_mysql($connection, $query)
	{
		$result = $connection->query($query);
		if (!$result) die ($connection->error);
		return $result;
	}

	function sanitize_string($connection, $str)
	{
		$str = strip_tags($str);
		$str = htmlentities($str);
		$str = stripslashes($str);
		return $connection->real_escape_string($str);
	}
?>