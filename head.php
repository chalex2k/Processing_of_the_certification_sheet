<?php
	session_start();
	require_once 'functions.php';

	$userstr = " | Гость";
	$who = 'guest';

	if (isset($_SESSION['role']))
	{		
		$loggedin = TRUE;

		$who = $_SESSION['role'];
		$username = $_SESSION['username'];
		$surname = $_SESSION['surname'];
		$name = $_SESSION['name'];

		$userstr = " | $surname $name";
	}
	else
		$loggedin = FALSE;

	echo "<title>$title$userstr</title>";

?>

