<?php
    require_once "render.php";
    require_once "start.php";
	require_once '../service/encrypt.php';

	try {

        $error = $name = $surname = $midname = $email = $password = $role = "";

        if (isset($_POST['name']) &&
            isset($_POST['surname']) &&
            isset($_POST['midname']) &&
            isset($_POST['email']) &&
            isset($_POST['password']) &&
            isset($_POST['role'])) {
            $name = sanitize_string($connection, $_POST['name']);
            $surname = sanitize_string($connection, $_POST['surname']);
            $midname = sanitize_string($connection, $_POST['midname']);
            $email = sanitize_string($connection, $_POST['email']);
            $password = sanitize_string($connection, $_POST['password']);
            $role = $_POST['role'];

            $token = new_token($password);

            if (isset($_POST['semester']) && isset($_POST['group'])) {
                $group = $_POST['group'];
                if (date('d/m') < '01/02') {
                    $semester = $_POST['semester'] * 2 - 1;
                } else {
                    $semester = $_POST['semester'] * 2;
                }
            }

            $result = query_mysql($connection, "SELECT * FROM user WHERE email='$email'");
            if ($result->num_rows)
                $error = "Этот e-mail уже зарегистрирован";
            else {
                query_mysql($connection, "INSERT INTO user(email, password, surname, name, middle_name, role) VALUES('$email', '$token',
															 '$surname', '$name',
															 '$midname', '$role')");
                if ($role == 'student') {
                    $result = query_mysql($connection, "SELECT id FROM user WHERE email='$email'");
                    if (!$result) die ("Пользователь не найден");
                    else {
                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $id = $row['id'];
                        query_mysql($connection, "INSERT INTO student VALUES('$id', '$semester', '$group')");
                    }
                }
                header("Location: index.php");
            }
        }
        $connection->close();
        return render('registration',
            [   'title' => 'Регистрация',
                'userstr' => $userstr,
                'error' => $error,
                'surname' => $surname,
                'name' => $name,
                'midname' => $midname,
                'email' => $email   ] );
    }
    catch (Exception $exc) {
        return render('error', []);
    }

?>