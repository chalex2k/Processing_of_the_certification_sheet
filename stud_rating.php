<?php
	$title = "Рейтинг";
	require_once 'head.php';
	$subject = $group = '';

	if (isset($_SESSION['username']))
	{
		$username = $_SESSION['username'];
		$email = $_SESSION['username'];
		require_once 'login.php';
		require_once 'functions.php';

		$connection = new mysqli($hostname, $username, $password, $database);
		if ($connection->connect_error) die($connection->connect_error);
		query_mysql($connection, "SET NAMES utf8");


		$user = get_user($connection, $email);
		if ($user['role'] == 'student')
		{
			$student = get_student($connection, $user['id']);			
			$semester = $student['semester'];
			echo "<h2>Семестр: " . $semester . "</h2>";
			echo "<h2>Группа: " . $student['_group'] . "</h2>";			
		}
		else
		{
			header('Location: index.php');
		}
	}
	else echo "Пожалуйста, <a href='continue.php'>войдите</a> в систему.";


	function get_student($connection, $user_id)
	{
		$result = query_mysql($connection, "SELECT * FROM student WHERE id = '$user_id'");
		if ($result->num_rows)
			return $result->fetch_assoc();
		else
			die ("Такого студента нет");
	}

	function get_user($connection, $email)
	{
		$result = query_mysql($connection, "SELECT * FROM user WHERE email = '$email'");
		if ($result->num_rows)
		{
			return $result->fetch_assoc();
		}
		else
			die ("Пользователь с таким e-mail не найден");
	}

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

	function new_query($connection, $subject, $semester, $group)
	{
		$query = "SELECT CONCAT_WS(' ', surname, user.name, middle_name) as 'Группа $group', subject.name as Предмет, mark.mark as 'Оценка', mark.attestation_number as 'Аттестация'
				  FROM user JOIN student
				  ON  user.id = student.id
				  JOIN subject_semester
				  ON student.semester = subject_semester.semester
                  JOIN subject
				  ON subject_semester.subject_id = subject.id
				  JOIN mark
				  ON subject_semester.subject_id = mark.subject_id AND student.id = mark.student_id
				  WHERE student.semester = '$semester' AND subject.id = '$subject' AND student._group = '$group'
				  ORDER BY surname";

		return $query;
	}

?>

<!DOCTYPE html>
<html>
<head>

</head>
<body>
	<a href="index.php">На главную</a>
	<form action="stud_ved.php" method="post">		
		<select name="group" size="1">
			<option selected>--Группа--</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
		</select>
		<?php
			$subjects = query_mysql($connection, "SELECT * FROM subject JOIN subject_semester ON subject_semester.subject_id = subject.id WHERE subject_semester.semester = '$semester'");			
		?>

		<select name="subject" size="1">
			<?php
			foreach ($subjects as $subject) 
			{
				echo "<option value='" . $subject['id'] . "'>" . $subject['name'] . "</option>";
			}
			?>
		</select>
		<input type="submit" value="Посмотреть результаты">
	</form>
	<?php
		if (isset($_POST['group']) && isset($_POST['subject']))
		{
			$subject = $_POST['subject'];
			$group = $_POST['group'];

			print_table($connection, new_query($connection, $subject, $semester, $group));
		}
	?>
</body>
</html>


