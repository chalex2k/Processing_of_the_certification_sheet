<!DOCTYPE html>
<html>
<head>
	<title>Регистрация</title>
</head>
<body>
	<form method="post" action="registration.php">
		<input type="text" name="surname" maxlength="32" placeholder="Фамилия"><br>
		<input type="text" name="name" maxlength="32" placeholder="Имя" autofocus="autofocus"><br>		
		<input type="text" name="midname" maxlength="32" placeholder="Отчество"><br>
		<input type="email" name="email" maxlength="50" placeholder="E-mail"><br>
		<input type="text" name="password" maxlength="50" placeholder="Пароль"><br>
		<select name="role" size="1">
			<option selected value="lecturer">Преподаватель</option>
			<option value="student">Студент</option>
		</select><br>
		<select name="semester" size="1">
			<option selected>--Семестр--</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
		</select><br>
		<select name="group" size="1">
			<option selected>--Группа--</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
		</select><br>
		<input type="submit" value="Зарегистрироваться">
	</form>
</body>
</html>

<?php
	require_once 'login.php';
	$connection = new mysqli($hostname, $username, $password, $database);
	if ($connection->connect_error) die($connection->connect_error);

	$error = $name = $surname = $midname = $email = $password = $role = "";
	#$semester = $group = NULL;

	if (isset($_POST['name']) &&
		isset($_POST['surname']) &&
		isset($_POST['midname']) &&
		isset($_POST['email']) &&
		isset($_POST['password']))
	{
		$name = sanitize_string($connection, $_POST['name']);
		$surname = sanitize_string($connection, $_POST['surname']);
		$midname = sanitize_string($connection, $_POST['midname']);
		$email = sanitize_string($connection, $_POST['email']);
		$password = sanitize_string($connection, $_POST['password']);
		$role = $_POST['role'];

		if (isset($_POST['semester']) && isset($_POST['group']))
		{
			$semester = $_POST['semester'];
			$group = $_POST['group'];
		}

		$result = query_mysql($connection, "SELECT * FROM user WHERE email='$email'");
		if ($result->num_rows)
			echo $error = "Этот e-mail уже зарегистрирован<br><br>";
		else
		{
			query_mysql($connection, "INSERT INTO user(email, password, surname, name, middle_name, role) VALUES('$email', '$password',
															 '$surname', '$name',
															 '$midname', '$role')");
			if ($role == 'student')
			{
				$result = query_mysql($connection, "SELECT id FROM user WHERE email='$email'");
				if (!$result) die ("Пользователь не найден");
				else
				{
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$id = $row['id'];
					query_mysql($connection, "INSERT INTO student VALUES('$id', '$semester', '$group')");
				}
			}

			die("Аккаунт успешно создан.<p><a href='continue.php'>Войти на сайт</a></p>");
		}
	}

	$connection->close();

	function query_mysql($connection, $query)
	{
		$result = $connection->query($query);
		if (!$result) die ($connection->error);
		return $result;
	}

	function sanitize_string($connection, $str)
	{
		$str = strip_tags($str);
		$str = htmlentities($str);
		$str = stripslashes($str);
		return $connection->real_escape_string($str);
	}

?>