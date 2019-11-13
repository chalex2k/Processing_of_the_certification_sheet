<?php
	session_start();
	require_once 'functions.php';

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
	if ($connection->connect_error) die("Fatal Error: $connection->connect_error"); // ссылка на страницу с ошибкой и завершение
	query_mysql($connection, "SET NAMES utf8");

echo <<<_INIT
<!DOCTYPE html> 
<html>
  <head>
	<title> $title$userstr </title>
//    <meta charset='utf-8'>
//    <meta name='viewport' content='width=device-width, initial-scale=1'> 

    <link rel='stylesheet' href='style.css' type='text/css'>
	<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.noty.js"></script>

<link rel="stylesheet" type="text/css" href="css/noty.css"/>
-->

	</head>
_INIT;

?>

