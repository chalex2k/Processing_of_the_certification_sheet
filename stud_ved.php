<!DOCTYPE html>
<html>
<head>
	<title>Учебная ведомость</title>
</head>
<body>

</body>
</html>

<?php
	require_once 'login.php';
	require_once 'functions.php';

	$connection = new mysqli($hostname, $username, $password, $database);
	if ($connection->connect_error) die($connection->connect_error);
	query_mysql($connection, "SET NAMES utf8");

	$subject = "История";
	$semester = 1;
	$group = 2;
	$att = array(1, 2, 3);

	print_table($connection, new_query($connection, $subject, $semester, $group, $att));

	function print_table($connection, $query)
	{
		$result = query_mysql($connection, $query);

		echo '<table border="1">';

		$row = $result->fetch_assoc();
		echo '<tr>';
		foreach ($row as $column => $value) {
			echo '<td>' . $column . '</td>';
		}
		echo '</tr>';

		$result->data_seek(0);
		while ($row = $result->fetch_array(MYSQLI_NUM))
		{
			echo '<tr>';
			foreach ($row as $value) {
			echo '<td>' . $value . '</td>';
		}
			echo '</tr>';
		}
		echo '</table>';
	}

	function new_query($connection, $subject, $semester, $group, $att)
	{
		if (isset($att)) $att = array(1,2,3);
		$query = "SELECT CONCAT_WS(' ', surname, user.name, middle_name) as ФИО, subject.name as Предмет, mark.mark as Оценка
				  FROM user JOIN student
				  ON  user.id = student.id
				  JOIN subject_semester
				  ON student.semester = subject_semester.semester
                  JOIN subject
				  ON subject_semester.subject_id = subject.id
				  JOIN mark
				  ON subject_semester.subject_id = mark.subject_id  
				  WHERE student.semester = '$semester' 
				  ORDER BY surname";

		return $query;
	}

?>