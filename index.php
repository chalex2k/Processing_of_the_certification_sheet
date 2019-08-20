<?php
session_start();
	if (isset($_SESSION['username']))
	{
		$username = $_SESSION['username'];
		echo "<h2>Здесь $username</h2>";
		/*$who = $_SESSION['role'];
		switch ($who)
		{
	  		case "guest": $redirect_url = "/lal.php"; break;
	  		case "student": $redirect_url = "/author.html"; break;
		  	case "lecturer": $redirect_url = "/admin.html"; break;
		  	default: $redirect_url = "/registration.html";
		}

		header('HTTP/1.1 200 OK');
		header('Location: http://'.$_SERVER['HTTP_HOST'].$redirect_url);
		exit();*/
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Главная страница</title>
</head>
<body>
	<a href="continue.php">Войти</a><br>
	<a href="stud_ved.php">Посмотреть результаты</a><br>
	<a href="logout.php">Выйти</a>
</body>
</html>