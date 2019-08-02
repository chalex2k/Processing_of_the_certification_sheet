<?php      					// !!! сохранение с помощью ajax.  обработка ситуации, когда оценки за какую-то аттестацию нет. 
session_start();				// !!! если нет студентов, не отображать кнопку сохранить.  при сохранении поля в первой форме должны оставаться заполнены прошлыми данными.
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
			echo '<form method="POST" action="lect_ved.php">';
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
			echo '<br> <input type="submit" name="find" value="find">';
			echo '</form>';
			
			if (isset($_POST['find'])) 
			{
				$gr =  $_POST['group'] ;
				setcookie('group', $gr);
				$se = $_POST['semester'];
				setcookie('semester', $se);
				$sb = $_POST['subject'];
				setcookie('subject_id', $sb);

				$ved = get_list($gr, $se, $sb, $connection);
				
				//echo ("count of items in ved ".count($ved));
				
				print_form_students_marks($ved);
			}
	   
			if (isset($_POST['save']))
			{	
				$ved = get_list($_COOKIE['group'], $_COOKIE['semester'], $_COOKIE['subject_id'], $connection);	
				//echo ("count of items in ved".count($ved));
				echo "<br>";
				$attestations[1] = "att1";
				$attestations[2] = "att2";
				$attestations[3] = "att3";
				for ($i = 1; $i <= 3; ++$i)
				{	
					foreach($_POST["$attestations[$i]"] as $key => $value) 
					{
						$stud_id = $_POST['list'][$key];
						$last = $ved[$_POST['list'][$key]][$i]['mark'];
						$id = $ved[$_POST['list'][$key]][$i]['id'];
						if ($value != $last)
						{
							//echo "Изменено $key - $value  Было $last  Стало  $value <br>\n";
							if ($last != "")
							{
								$query = " UPDATE mark SET mark = $value WHERE id = $id";
								$result5 = $connection->query($query);
								if (!$result5) die ("Сбой при обновлении данных".$connection->error());
								else
								{ 
									echo "Данные успешно сохранены";
									$ved[$_POST['list'][$key]][$i]['mark'] = $value;
							}	}
							else
							{
								$subj_id = $_COOKIE['subject_id'];
								$query = "INSERT INTO mark VALUES(NULL,'$stud_id', '$subj_id', '$value', '$i')";
								$result = $connection->query($query);
								if (!$result) die($connection->error);
								else{
									echo "Данные успешно сохранены";
								$ved[$_POST['list'][$key]][$i]['mark'] = $value;}
							}
						}
					}
				}
	
				print_form_students_marks($ved);				
			}
			
		}
	}
}
else 
	echo "Please <a href=authentication.html>click here</a> to log in.";


function print_form_students_marks($ved)
{
	echo '<form method="POST" action="lect_ved.php"> <table>';
				foreach ($ved as $user => $row)
				{
						echo '<tr> <td><input type="text" name="list[]" value = '.$user.'></td>
						<td><input type="text" name="att1[]" value="'.$row[1]['mark'].'"></td>
						<td><input type="text" name="att2[]" value="'.$row[2]['mark'].'"></td>
						<td><input type="text" name="att3[]" value="'.$row[3]['mark'].'"></td>
						</tr>';
				}				
				echo '
       <tr><td>
	   <input type="submit" name="save" value="save" />
	   </td></tr>
       </table>
	   </form>';
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
			$ved[$stud_id[0]][$marks[4]]['mark'] = $marks[3];
			$ved[$stud_id[0]][$marks[4]]['id'] = $marks[0];	
		}	
	}
	return $ved;
}
 ?>

	  
  
  