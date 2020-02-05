<?php
    require_once("start.php");
    $title = 'Преподаватель';
    if ($who == 'student') // студент пытается взломать нас
        echo "Students have not access to tis page";
    else if ($who == 'guest')
        echo "error"; // ошибка, завершение программы
?>