<?php
session_start();
if (! isset($_SESSION['user_email'])) 
	echo "Please <a href=authentication.html>click here</a> to log in."; // Ссылка на нормальную страницу авторизации и завершение программы
$user_email = $_SESSION['user_email'];

require_once 'login.php';
$connection = new mysqli($hostname, $username, $password, $database);
if ($connection->connect_error) die("Fatal Error: $connection->connect_error"); // ссылка на страницу с ошибкой и завершение

echo <<<_INIT
<!DOCTYPE html> 
<html>
  <head>
	<title> НАЗВАНИЕ </title>	//
//    <meta charset='utf-8'>
//    <meta name='viewport' content='width=device-width, initial-scale=1'> 
//    <link rel='stylesheet' href='jquery.mobile-1.4.5.min.css'>
    <link rel='stylesheet' href='style.css' type='text/css'>
//    <script src='javascript.js'></script>
//    <script src='jquery-2.2.4.min.js'></script>
//    <script src='jquery.mobile-1.4.5.min.js'></script>
	</head>
	  <body>

	  <header>
	  <div class = "logout">
		$user_email | <a class="nav-item" href="#"> выйти</a> 
	  </div>
_INIT;











?>