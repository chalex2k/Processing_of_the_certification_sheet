<?php
require_once 'login.php';
$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) die("Fatal Error");

$query = "SET NAMES utf8";
$result = $conn->query($query);
if (!$result) die ('<br> Ошибка при установке кодировки');

$subjects = array(		# в масссиве предметы(предмет, оценка), которые нe повторяются в разных семестрах
		array('name' => "Теоретические основы информатики",
			  'mark' => 1,
			  'semester' => 1),
		array('name' => "История",
			  'mark' => 1,
			  'semester' => 1),
		array('name' => "Математический анализ",
			  'mark' => 1,
			  'semester' => 1),
		array('name' => "Введение в программирование",
			  'mark' => 1,
			  'semester' => 1),
		array('name' => "Русский язык для устной и письменной коммуникации",
			  'mark' => 0,
			  'semester' => 1),
		array('name' => "Основы речевого воздействия",
			  'mark' => 0,
			  'semester' => 1),	  
		array('name' => "СПЭД",
			  'mark' => 0,
			  'semester' => 1),
			  
		array('name' => "Архитектура ЭВМ",
			  'mark' => 1,
			  'semester' => 2),
		array('name' => "Алгоритмы и структуры данных",
			  'mark' => 1,
			  'semester' => 2),
		array('name' => "Алгебра и геометрия",
			  'mark' => 1,
			  'semester' => 2),
		array('name' => "Дискретная математика",
			  'mark' => 0,
			  'semester' => 2),
		array('name' => "Язык Си",
			  'mark' => 0,
			  'semester' => 2),
		array('name' => "Веб-технологии",
			  'mark' => 0,
			  'semester' => 2),
			  
		array('name' => "Философия",
			  'mark' => 1,
			  'semester' => 3),
		array('name' => "Дискретная математика",
			  'mark' => 1,
			  'semester' => 3),
		array('name' => "Управление данными",
			  'mark' => 1,
			  'semester' => 3),
		array('name' => "Языки и системы программирования",
			  'mark' => 0,
			  'semester' => 3),
		array('name' => "Компьютерная графика",
			  'mark' => 1,
			  'semester' => 3),	  
		array('name' => "Теория вероятностей и математическая статистика",
			  'mark' => 0,
			  'semester' => 3),
		array('name' => "Дифференциальные уравнения",
			  'mark' => 0,
			  'semester' => 3),
			  
		array('name' => "Объектно-ориентированное программирование",
			  'mark' => 1,
			  'semester' => 4),
		array('name' => "Английский язык",
			  'mark' => 1,
			  'semester' => 4),
		array('name' => "Механика и оптика",
			  'mark' => 1,
			  'semester' => 4),
		array('name' => "Уравнения математической физики",
			  'mark' => 1,
			  'semester' => 4),
		array('name' => "Методы вычислений",
			  'mark' => 0,
			  'semester' => 4),
		array('name' => "ОС Unix",
			  'mark' => 1,
			  'semester' => 4),
		array('name' => "Электроника",
			  'mark' => 0,
			  'semester' => 4),
		array('name' => "Теория информационных процессов и систем",
			  'mark' => 0,
			  'semester' => 4)
	);
	
foreach($subjects as $s)
{
	$query = "INSERT INTO subject VALUES(NULL, '".$s['name']."','".$s['mark']."' )";
	$result = $conn->query($query);
	if (!$result) die ("Error on access database " . $conn->error());
  	$query = "INSERT INTO subject_semester VALUES('".$s['semester']."',$conn->insert_id)";
	$result = $conn->query($query);
	if (!$result) die ("Error on access database " . $conn->error());
}

# теперь вручную добавим повторяющиеся предметы в разных семестрах		  
$query = "INSERT INTO subject VALUES(NULL, 'Физическая культура', 0)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$id = $conn->insert_id;
$query = "INSERT INTO subject_semester VALUES(1, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$query = "INSERT INTO subject_semester VALUES(2, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$query = "INSERT INTO subject_semester VALUES(3, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$query = "INSERT INTO subject_semester VALUES(4, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());

$query = "INSERT INTO subject VALUES(NULL, 'Английский язык', 0)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$id = $conn->insert_id;
$query = "INSERT INTO subject_semester VALUES(1, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$query = "INSERT INTO subject_semester VALUES(2, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$query = "INSERT INTO subject_semester VALUES(3, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());

$query = "INSERT INTO subject VALUES(NULL, 'ТФКП', 1)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$id = $conn->insert_id;
$query = "INSERT INTO subject_semester VALUES(2, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
$query = "INSERT INTO subject_semester VALUES(3, $id)";
$result = $conn->query($query);
if (!$result) die ("Error on access database " . $conn->error());
 
echo("data inserted");
?>  