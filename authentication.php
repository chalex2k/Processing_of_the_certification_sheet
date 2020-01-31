<html>
<head>
    <?php
    $title = "Авторизация";
    require_once 'head.php';
    ?>
    <script src="js/validate_forms.js"></script>
</head>
<body class="auth">

<?php
  require_once 'login.php';
  require_once 'encrypt.php';
  require_once 'functions.php';
  $connection = new mysqli($hostname, $username, $password, $database);

  if ($connection->connect_error) die("Fatal Error");

  query_mysql($connection, "SET NAMES utf8");

  $email = $password = $email_temp = $password_temp = '';
  $error = '';
  
  if (isset($_POST['email']) && isset($_POST['password']))
  {
    $email_temp = mysql_entities_fix_string($connection, $_POST['email']);
    $password_temp = mysql_entities_fix_string($connection, $_POST['password']);
    $query   = "SELECT * FROM user WHERE email='$email_temp'";
    $result  = $connection->query($query);
	
    if (!$result) $error = "Пользователь не найден";
    elseif ($result->num_rows)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);

      $result->close();
	  echo (new_token($password_temp)."<br>");
	  echo ($row['surname']."<br>");
      if (new_token($password_temp) == $row['password'])#(password_verify($password_temp, $row[1]))
      {
        session_start();
        $_SESSION["user_email"] = $email_temp;
        $_SESSION['role'] = $row['role'];
        $_SESSION['surname'] = $row['surname'];
        $_SESSION['name'] = $row['name'];
		#echo ("<br> $_SESSION['username']");
		header("Location: index.php");
      }
      else $error = "Неправильно введен логин и/или пароль";
    }
    else $error = "Неправильно введен логин и/или пароль";
  }/*
  else
  {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Please enter your username and password");
  }*/

  $connection->close();

  function mysql_entities_fix_string($connection, $string)
  {
    return htmlentities(mysql_fix_string($connection, $string));
  }	

  function mysql_fix_string($connection, $string)
  {
    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return $connection->real_escape_string($string);
  }
?>


<div class="auth">
    <form method="post" action="authentication.php" onsubmit="return validate(this)">
        <p class="str_input">
            <label for="email">Введите свой логин</label>
            <input type="text" name="email" required="required" value="<?php echo $email_temp?>">
        </p>
        <p class="str_input">
            <label for="password">Введите пароль</label>
            <input type="password" name="password" required="required" value="<?php echo $password_temp ?>">
        </p>
        <div class="errors">
            <?php echo $error ?>
        </div>
        <p class="button">
            <button type="submit">Войти</button>
            <a href="registration.php" class="btn">Зарегистрироваться</a>
        </p>
    </form>
</div>
</body>
</html>
