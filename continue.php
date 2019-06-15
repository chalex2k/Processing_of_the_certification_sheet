<?php // continue.php
  session_start();

  if (isset($_SESSION['username']))
  {
	$forename = $_SESSION['username'];

    echo "Welcome back $forename.<br>";
          
  }
  else 
	  echo "Please <a href=authentication.html>click here</a> to log in.";
?>
