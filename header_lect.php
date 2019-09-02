<?php
require_once 'check_access.php';
$res = check_role($connection, $user_email); 
if ($res == 'student') // студент пытается взломать нас
	echo "Students have not access to tis page";
else if ($res == -1)
	echo "error"; // ошибка, завершение программы

echo <<<_HEADER
    
	<div class="navigation">
		<a class="nav-item" href="#">Ведомость</a> 
		<a class="nav-item" href="#">Статистика</a> 
		<a class="nav-item" href="#">Мои предметы</a>
		<a class="nav-item" href="#">Сообщения</a>
	</div>
	</header>
_HEADER;
?>