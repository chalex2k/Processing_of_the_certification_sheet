<?php
	function new_token($password)
	{
		$salt1 = "1w&?";
		$salt2 = "o9@^";

		return $token = hash("ripemd128", "$salt1$password$salt2");
	}
?>