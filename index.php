<!DOCTYPE html>
<html>
<head>
<?php 
	$title = "Главная страница";
	require_once 'head.php';
?>
</head>
<body class="main">
    <div class="greeting">
        <p>Приветствуем тебя, странник!</p>
        <p>Студент ФКН? Регистрируйся и следи за процессом обучения и индивидуальными успехами.
            Преподаватель? Здесь легко управлять заполнением ведомостей в личном кабинете.</p>
    </div>
	<div class="link">
		<a href="authentication.php">Войти</a>
		<a href="registration.php">Зарегистрироваться</a>
        <hr>
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