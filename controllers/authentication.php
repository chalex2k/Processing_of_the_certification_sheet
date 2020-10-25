<?php
require_once "../service/render.php";
require_once "start.php";
require_once "../service/encrypt.php";

$email = $password = $email_temp = $password_temp = '';
$error = '';
try {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email_temp = sanitize_string($connection, $_POST['email']);
        $password_temp = sanitize_string($connection, $_POST['password']);
        $query = "SELECT * FROM user WHERE email='$email_temp'";
        $result = $connection->query($query);

        if (!$result) $error = "Пользователь не найден";
        elseif ($result->num_rows) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->close();
            if (new_token($password_temp) == $row['password']) {
                session_start();
                $_SESSION["user_email"] = $email_temp;
                $_SESSION['role'] = $row['role'];
                $_SESSION['surname'] = $row['surname'];
                $_SESSION['name'] = $row['name'];
                header("Location: index.php");
            } else $error = "Неправильно введен логин и/или пароль";
        } else $error = "Неправильно введен логин и/или пароль";
    }
    $connection->close();
    return render('authentication',
        [   'title' => 'Авторизация',
            'userstr' => $userstr,
            'email_temp' => $email_temp,
            'password_temp' => $password_temp,
            'error' => $error]
    );
}
catch (Exception $exc) {
    return render('error', []);
}
