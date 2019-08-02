<?php 
  require_once 'login.php';
  $connection = new mysqli($hostname, $username, $password, $database);

  if ($connection->connect_error) die("Fatal Error");

  $email = $password = '';
  
  if (isset($_POST['email']) && isset($_POST['password']))
  {
	echo($_POST['email']);
	echo($_POST['password']);
    $email_temp = mysql_entities_fix_string($connection, $_POST['email']);
    $password_temp = mysql_entities_fix_string($connection, $_POST['password']);
    $query   = "SELECT * FROM user WHERE email='$email_temp'";
    $result  = $connection->query($query);
	
    if (!$result) die("User not found");
    elseif ($result->num_rows)
    {
      $row = $result->fetch_array(MYSQLI_NUM);

      $result->close();
      if ($password_temp == $row[1])#(password_verify($password_temp, $row[1]))
      {
        session_start();
        $_SESSION["username"] = $email_temp;
		#echo ("<br> $_SESSION['username']");
        die ("<p><a href='continue.php'>Click here to continue</a></p>");
      }
      else die("Invalid username/password combination");
    }
    else die("Invalid username/password combination a lot");
  }
  else
  {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Please enter your username and password");
  }

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
