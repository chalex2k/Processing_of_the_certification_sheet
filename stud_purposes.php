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
			$stud_id = $student['id'];	


			foreach ($_POST as $subj_id => $mark) {				
				set_expected_mark($connection, $stud_id, $subj_id);
			}
			get_expected_marks($connection, $stud_id, $semester);			
			print_table($connection, $stud_id, $semester);


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
		$query = "SELECT subject.id, subject.name as 'Предмет', expected_mark.mark as 'Ожидаемый балл'
					FROM subject_semester 
					JOIN subject ON subject_semester.subject_id = subject.id
					JOIN expected_mark ON expected_mark.subject_id = subject_semester.subject_id
					WHERE subject_semester.semester = $semester AND expected_mark.student_id = $student_id";
		$result = query_mysql($connection, $query);
		$expected_marks = [];
		while ($row = $result->fetch_assoc())
		{
			$expected_marks[$row['id']] = $row['Ожидаемый балл'];
		}

		return $expected_marks;
	}

	function print_table($connection, $stud_id, $semester)
	{
		$query = "SELECT * FROM subject_semester JOIN subject ON subject_semester.subject_id = subject.id WHERE subject_semester.semester = $semester";
		$result = query_mysql($connection, $query);
		$expected_marks = get_expected_marks($connection, $stud_id, $semester);
			echo "<form action='stud_purposes.php' method='post'>";
			echo '<table border="1">';

			while ($subject = $result->fetch_assoc())
			{
				echo "<tr><td>" . $subject['name'] . "</td><td><input type='text' name='" . $subject['id'] . "' value='" . $expected_marks[$subject['id']] . "'></td></tr>";
			}

			echo '</table>';
			echo "<input type='submit' value='Сохранить''>";
			echo "</form>";
	}

	function set_expected_mark($connection, $stud_id, $subj_id)
	{
		if (isset($_POST[$subj_id]))
		{
			$check_query = "SELECT * FROM expected_mark WHERE student_id = '$stud_id' AND subject_id = '$subj_id'";
			$result = query_mysql($connection, $check_query);
			if ($result->num_rows)
			{
				$query = "UPDATE expected_mark SET mark = '$_POST[$subj_id]' WHERE student_id = '$stud_id' AND subject_id = '$subj_id'";
				$result = query_mysql($connection, $query);
			}
			else
			{
				$query = "INSERT INTO expected_mark VALUES(NULL, '$stud_id', '$subj_id', '$_POST[$subj_id]')";
				$result = query_mysql($connection, $query);
			}
		}
	}
?>