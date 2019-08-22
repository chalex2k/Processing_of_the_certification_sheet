<?php
	$title = "Цели";
	require_once 'head.php';
	require_once 'login.php';
	require_once 'functions.php';
	require_once 'stud_functions.php';

	if (isset($_SESSION['username']))
	{
		$email = $_SESSION['username'];

		$connection = new mysqli($hostname, $username, $password, $database);
		if ($connection->connect_error) die($connection->connect_error);	
		query_mysql($connection, "SET NAMES utf8");
		
		$user = get_user($connection, $email);
		if ($user['role'] == 'student')
		{
			$student = get_student($connection, $user['id']);			
			$semester = $student['semester'];	


			$query = "SELECT * FROM subject_semester JOIN subject ON subject_semester.subject_id = subject.id WHERE subject_semester.semester = $semester";

			editable_table($connection, $query);

		}
		else
		{
			header('Location: index.php');
		}		
	}
	else 
	{
		echo "<div>Пожалуйста, <a href='authentication.html'>войдите</a> в систему.</div>";
	}

	function get_expected_marks($connection, $student_id, $semester)
	{
		//проработать для рейтинговой таблицы
		/*$query = "SELECT subject.name as 'Предмет',  att_1.mark as 'Аттестация 1', att_2.mark as 'Аттестация 2', att_3.mark as 'Аттестация 3'
				FROM subject_semester 
				JOIN subject ON subject_semester.subject_id = subject.id
				JOIN mark att_1
				JOIN mark att_2
				JOIN mark att_3 ON att_1.subject_id = subject_semester.subject_id
				WHERE att_1.attestation_number = '1' AND att_2.attestation_number = '2' AND att_3.attestation_number = '3' AND subject_semester.semester = '1' AND att_1.student_id = '4'";*/
		$query = "SELECT subject.name as 'Предмет', expected_mark.mark as 'Ожидаемый балл'
					FROM subject_semester 
					JOIN subject ON subject_semester.subject_id = subject.id
					JOIN expected_mark ON expected_mark.subject_id = subject_semester.subject_id
					WHERE subject_semester.semester = $semester AND expected_mark.student_id = $student_id";
		return $query;
	}

	function editable_table($connection, $query)
	{
		$result = query_mysql($connection, $query);

			echo '<table border="1">';

			while ($subject = $result->fetch_assoc())
			{
				echo '<tr><td>' . $subject['name'] . '</td><td><input type="text"></td></tr>';
			}

			echo '</table>';
	}

	function insert_data($connection, $query)
	{
		
	}
?>