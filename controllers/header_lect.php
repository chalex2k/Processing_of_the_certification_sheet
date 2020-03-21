<?php
    require_once("start.php");
    $title = 'Преподаватель';
	if ($who != 'lecturer')
		throw new Exception('Вы не имеете доступ к этой странице');
?>