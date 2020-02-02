<?php
    $title = "Регистрация";
    require_once 'head.php';
?>
<body class="auth">
    <div class="reg">
        <form method="post" action="registration.php">
            <div class="str-input-reg">
                <label for="surname">Фамилия</label>
                <input type="text" name="surname" maxlength="32">
            </div>
            <div class="str-input-reg">
                <label for="name">Имя</label>
                <input type="text" name="name" maxlength="32">
            </div>
            <div class="str-input-reg">
                <label for="midname">Отчество</label>
                <input type="text" name="midname" maxlength="32">
            </div>
            <div class="str-input-reg">
                <label for="email">E-mail</label>
                <input type="email" name="email" maxlength="50">
            </div>
            <div class="str-input-reg">
                <label for="password">Пароль</label>
                <input type="password" name="password" maxlength="50">
            </div>
            <div class="list">
                <select name="role" size="1">
                    <option selected>Выберите...</option>
                    <option value="lecturer">Преподаватель</option>
                    <option value="student">Студент</option>
                </select>
            </div>
            <div class="list">
                <select name="semester" size="1">
                    <option selected>Курс...</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <div class="list">
                <select name="group" size="1">
                    <option selected>Группа...</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <div class="btn-reg" id="at-reg">
                <button type="submit">Зарегистрироваться</button>
                <a href="authentication.php" class="to-auth">Войти</a>
            </div>
        </form>
    </div>
</body>

<?php
	require_once 'login.php';
	require_once 'encrypt.php';
	require_once 'functions.php';
	$connection = new mysqli($hostname, $username, $password, $database);
	if ($connection->connect_error) die($connection->connect_error);

	query_mysql($connection, "SET NAMES utf8");

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

		$token = new_token($password);

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
			query_mysql($connection, "INSERT INTO user(email, password, surname, name, middle_name, role) VALUES('$email', '$token',
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

?>