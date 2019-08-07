<?php      					 
session_start();				
if (isset($_SESSION['username'])) // если установлена сессия
{
	$useremail = $_SESSION['username'];
	require_once 'login.php';
	$connection = new mysqli($hostname, $username, $password, $database);
	if ($connection->connect_error) die("Fatal Error");
	
	echo "$useremail <br>";
	$query   = "SELECT * FROM user WHERE email = '$useremail'" ;
    $result  = $connection->query($query);
    if (!$result) die("User not found");
    
	elseif ($result->num_rows)  // если нашли юзера с таким имейлом
    {
		$users_count = $result->num_rows;
		$user = $result->fetch_array(MYSQLI_NUM);

		$result->close();
		if ($user[5] == 'lecturer')
		{
			
			if (isset($_POST['add']))
			{
				$subj_id = $_POST['subject'];
				$query  = "SELECT * FROM lecturer_subject WHERE lecturer_id = '$useremail' AND subject_id = '$subj_id'";
				$result = $connection->query($query);
				if (!$result) die ("Database access failed");
				$rows = $result->num_rows;
				if ($rows == 0)
				{
					$query  = "INSERT INTO lecturer_subject VALUES('$useremail', '$subj_id')";
					$result = $connection->query($query);
					if (!$result) die ("Database access failed");
					echo "Предмет добавлен";
				}
				else echo "предмет существует";
			}
			
			echo '<form method="POST" action="lect_subjects.php">';
			$subjects = get_subjects($useremail, $connection);
			$flag = False;
			foreach ($subjects as $id => $name)
			{
				if (isset($_POST["$id"])) 
				{
				$query  = "DELETE FROM lecturer_subject WHERE lecturer_id = '$useremail' AND subject_id = '$id' ";
				$result = $connection->query($query);
				if (!$result) die ("Database access failed 2");
				$flag = True;
				echo("$id");
				}
			}
			if ($flag)
				$subjects = get_subjects($useremail, $connection);
			print_subjects($subjects);
			echo(" Добавить <br>");
			echo ("<select name = 'subject' size = '1' >");
			$query  = "SELECT * FROM subject ";
			$result = $connection->query($query);
			if (!$result) die ("Database access failed");
			$rows = $result->num_rows;
			for ($j = 0 ; $j < $rows ; ++$j)
			{
				$result->data_seek($j);
				$subject = $result->fetch_array(MYSQLI_NUM);
				echo "<option value = "."$subject[0]".">"."$subject[1]"." </option>";
			}
			echo "</select>";
			echo '<br> <input type="submit" name="add" value="Добавить">';
			echo "</form>";
			
			
			// обработать добавление и удаление предметов 
			
			
			
			
			
			
		}
	}
}
else 
	echo "Please <a href=authentication.html>click here</a> to log in.";


function get_subjects($useremail, $connection)
{
	$subjects = array();
	$query  = "SELECT * FROM lecturer_subject WHERE lecturer_id = '$useremail'";
			$result = $connection->query($query);
			if (!$result) die ("Database access failed");
			$rows = $result->num_rows;
			for ($j = 0 ; $j < $rows ; ++$j)
			{
				$result->data_seek($j);
				$lect_subj = $result->fetch_array(MYSQLI_NUM);
				
				$query2 = " SELECT * FROM subject WHERE id = $lect_subj[1]";
				$result2 = $connection->query($query2);
				if (!$result2) die ("Database access failed  2");
				$subj = $result2->fetch_array(MYSQLI_NUM);
				$subjects["$lect_subj[1]"] = $subj[1];
			}
	return $subjects;
}

function print_subjects($subjects)
{
	foreach ($subjects as $id => $name)
	{
		echo ("$name <input type='submit' name='$id' value='удалить'> <br>");
	}
}
 ?>