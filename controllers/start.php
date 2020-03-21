<?php
	session_start();
	require_once '../service/functions.php';

	$userstr = " | Гость";
	$who = 'guest';

	if (isset($_SESSION['role']))
	{		
		$loggedin = TRUE;

		$who = $_SESSION['role'];
		$user_email = $_SESSION['user_email'];
		$surname = $_SESSION['surname'];
		$name = $_SESSION['name'];
		
		$userstr = " | $surname $name";
	}
	else
		$loggedin = FALSE;
	
	require_once 'login.php';
	$connection = new mysqli($hostname, $username, $password, $database);
	if ($connection->connect_error) throw new Exception("Ошибка при запосе к БД $connection->connect_error");
	query_mysql($connection, "SET NAMES utf8");

?>