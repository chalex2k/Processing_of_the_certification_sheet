<!DOCTYPE html>
<html>
<head>
<?php 
	$title = "Главная страница";
	require_once 'head.php';
?>
</head>
<body>
	<div>
		<a href="authentication.html">Войти</a>
	</div>
	<div>
		<a href="registration.php">Зарегистрироваться</a>
	</div>	
</body>
</html>

<?php

	switch ($who)
	{
		case 'guest': $redirect_url = ''; break;
		case 'student': $redirect_url = "stud_main.php"; break;
		case 'lecturer': $redirect_url = "lect_ved.php"; break;
		default: $redirect_url = "index.php";
	}

	header('HTTP/1.1 200 OK');
	if ($redirect_url)
		header("Location: $redirect_url");
	exit();

?>