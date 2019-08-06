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
			echo '<form method="POST" action="lect_rating.php">';
			echo("<br> Семестр  ");
			echo ("<select name = 'semester' size = '1' >
			<option value = '1'> 1 </option>
			<option value = '2'> 2 </option>
			<option value = '3'> 3 </option>
			<option value = '4'> 4 </option>
			<option value = '5'> 5 </option>
				</select>
			");
			echo("<br> Группа  ");
			echo ("<select name = 'group' size = '1' >
			<option value = '1'> 1 </option>
			<option value = '2'> 2 </option>
			<option value = '3'> 3 </option>
			<option value = '4'> 4 </option>
			<option value = '5'> 5 </option>
			<option value = '6'> 6 </option>
			<option value = '7'> 7 </option>
			<option value = '8'> 8 </option>
			<option value = '9'> 9 </option>
				</select>
			");
		
			$query  = "SELECT * FROM lecturer_subject WHERE lecturer_id = '$useremail'";
			$result = $connection->query($query);
			if (!$result) die ("Database access failed");

			$rows = $result->num_rows;
			echo("<br> Предмет ");
			echo ("<select name = 'subject' size = '1' >");
			//echo($rows);
			for ($j = 0 ; $j < $rows ; ++$j)
			{
				$result->data_seek($j);
				$lect_subj = $result->fetch_array(MYSQLI_NUM);
				
				$query2 = " SELECT name FROM subject WHERE id = $lect_subj[1]";
				$result2 = $connection->query($query2);
				if (!$result2) die ("Database access failed  2");
				
				$name_subj = $result2->fetch_array(MYSQLI_NUM);
				echo "<option value = ".$lect_subj[1].">"."$name_subj[0]"." </option>";
			}
			echo "</select>";
			echo 'по алфавиту <input type = "radio" name = "mode" value = "1"> ';
			echo 'по среднему баллу <input type = "radio" name = "mode" value = "2" checked = "checked"> ';
			echo '<br> <input type="submit" name="find" value="find">';
			
			echo '</form>';
			
			if (isset($_POST['find'])) 
			{
				$gr =  $_POST['group'] ;
				$se = $_POST['semester'];
				$sb = $_POST['subject'];

				$ved = get_list($gr, $se, $sb, $connection);
				$rating = get_rating($ved, $connection);
				if ($_POST['mode'] == '2')
				{ arsort($rating);}
				else {ksort($rating);}
				$total_middle_mark = culc_middle_value($rating);
				//echo ("count of items in ved ".count($ved));
				
				print_form_students_marks($rating);
				echo ("Среднеий балл по группе ".$total_middle_mark);
			}
	   
			
			
		}
	}
}
else 
	echo "Please <a href=authentication.html>click here</a> to log in.";


function print_form_students_marks($rating)
{
	echo ' <table>';
	echo '<tr> <td>Студент</td>
				<td>Средний балл</td>
						</tr>';
				foreach ($rating as $initials => $middle_mark)
				{
						echo '<tr> <td>'.$initials.' '.'</td>
						<td>'.$middle_mark.'</td>
						</tr>';
				}				
				echo '
       <tr><td>
	   
	   </td></tr>
       </table>';
}

function get_list($group, $semester, $subject_id, $connection)
{
	$query3  = "SELECT id FROM student WHERE semester = $semester AND _group = $group ";
	$result3 = $connection->query($query3);
	if (!$result3) die ("Database access failed 3");
	$rows3 = $result3->num_rows;
	//echo "count of students ".$rows3;
	$ved = array();
	for ($j = 0 ; $j < $rows3 ; ++$j)
	{
		$result3->data_seek($j);
		$stud_id = $result3->fetch_array(MYSQLI_NUM);
	
		//echo "<br> stud_id[0] - student_id  ".$stud_id[0];
		$ved[$stud_id[0]] = array();
		$query4 = " SELECT * FROM mark WHERE subject_id = $subject_id AND student_id = '$stud_id[0]'";
		$result4 = $connection->query($query4);
		if (!$result4) die ("Database access failed  4");
		$rows4 = $result4->num_rows;
		//echo " marks count". $rows4;
		
		for ($k = 0 ; $k < $rows4 ; ++$k)
		{
			$result4->data_seek($k);
			$marks = $result4->fetch_array(MYSQLI_NUM);
			$ved[$stud_id[0]][$marks[4]] = $marks[3];
		}	
	}
	return $ved;
}

function get_rating($ved, $connection)
{
	$rating = array();
	foreach ($ved as $user_email => $marks)
	{
		$query   = "SELECT * FROM user WHERE email = '$user_email'" ;
		$result  = $connection->query($query);
		if (!$result) die("User not found");
		elseif ($result->num_rows)  // если нашли юзера с таким имейлом
		{
			$users_count = $result->num_rows;
			$user = $result->fetch_array(MYSQLI_NUM);
			$result->close();
			$initials = "$user[2]".". ".substr("$user[3]", 0, 2).'.';
			$rating["$initials"] = culc_middle_value($marks);
		}
	}
	return $rating;
}


function culc_middle_value($arr)
{
	$sum = 0;
	foreach ($arr as $value)
	{
		$sum += $value;
	}
	return $sum / count($arr);
}
 ?>

	  
  
  