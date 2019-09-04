<?php
require_once("head.php");
if ($who == 'student') // студент пытается взломать нас
	echo "Students have not access to tis page";
else if ($who == 'guest')
	echo "error"; // ошибка, завершение программы

echo <<<_HEADER
    <body>
	<header>
	<div class="navigation">
		<a class="nav-item" href="lect_ved.php">Ведомость</a> 
		<a class="nav-item" href="lect_rating.php">Статистика</a> 
		<a class="nav-item" href="lect_subjects.php">Мои предметы</a>
		<a class="nav-item" href="#">Сообщения</a>
		<a class="nav-item" href="logout.php">Выйти</a>
	</div>
	</header>
_HEADER;
?>