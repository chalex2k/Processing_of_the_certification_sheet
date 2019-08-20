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
			
			

			if (isset($_POST['send']))
			{	
				if(isset($_FILES) && $_FILES['userfile']['error'] == 0){ // Проверяем, загрузил ли пользователь файл
				$destiation_dir = dirname(__FILE__) .'/'.$_FILES['userfile']['name']; // Директория для размещения файла
				move_uploaded_file($_FILES['userfile']['tmp_name'], $destiation_dir ); // Перемещаем файл в желаемую директорию
				echo 'File Uploaded';  // Оповещаем пользователя об успешной загрузке файла
				$arr = readExelFile($_FILES['userfile']['name']);
				echo("<br>". $_FILES['userfile']['name']);
				
				if(isset($_COOKIE['group']) and isset($_COOKIE['semester']) and isset($_COOKIE['subject_id']))
				{
					$ved = get_list($_COOKIE['group'], $_COOKIE['semester'], $_COOKIE['subject_id'], $connection);	
					update_ved_from_file($arr, $ved);
					setNewMarks($ved);
					print_form_students_marks($ved);
				}
				
				
				
				// удалить файл после
			}}
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
			
					for ($j = 0; $j < count($ved); $j++)
					{
					$ved[$j][$i]->new_value = $_POST["$attestations[$i]"][$j];
					}
				}
				updateDB($ved, $connection);
				setNewMarks($ved);
				print_form_students_marks($ved);	// cooooool!!! it is works!!!!			
			}
			
		}
	}
}
else 
	echo "Please <a href=authentication.html>click here</a> to log in.";


function print_form_students_marks($ved)
{
	echo '<form method="POST" action="lect_ved.php"> <table>';
				foreach ($ved as $num_rows => $row)
				{
						echo '<tr> <td><input type="text" name="list[]" value = '.$row[0]->initials.'></td>
						<td><input type="text" name="att1[]" value="'.$row[1]->value.'"></td>
						<td><input type="text" name="att2[]" value="'.$row[2]->value.'"></td>
						<td><input type="text" name="att3[]" value="'.$row[3]->value.'"></td>
						</tr>';
				}				
				echo '
       <tr><td>
	   <input type="submit" name="save" value="save" />
	   <input type="submit" name="load" value="load" />
	   </td></tr>
       </table>
	   </form>';
	echo ('<!-- Тип кодирования данных, enctype, ДОЛЖЕН БЫТЬ указан ИМЕННО так -->
<form enctype="multipart/form-data" action="lect_ved.php" method="POST">
    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Название элемента input определяет имя в массиве $_FILES -->
    Отправить этот файл: <input name="userfile" type="file" />
    <input type="submit" name = "send" value="Отправить файл" />
</form>');
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
		
		//$ved[$stud_id[0]] = array();
		
		$row = array();
		$student;
		$query5   = "SELECT * FROM user WHERE email = '$stud_id[0]'" ;
		$result5  = $connection->query($query5);
		if (!$result5) die("User not found");
		if (!$result5->num_rows) die("User not found"); // если нашли юзера с таким имейлом
		$user = $result5->fetch_array(MYSQLI_NUM);
		$result5->close();
		//$initials = "$user[2]".". ".substr("$user[3]", 0, 2).'.';
		$initials = (string)$user[2];
		$row[] = new Student($stud_id[0], $initials);
		//$ved[$stud_id[0]]['initials'] = $initials;
		
		$query4 = " SELECT * FROM mark WHERE subject_id = $subject_id AND student_id = '$stud_id[0]'";
		$result4 = $connection->query($query4);
		if (!$result4) die ("Database access failed  4");
		$rows4 = $result4->num_rows;
		//echo " marks count". $rows4;
		
		for ($k = 0 ; $k < $rows4 ; ++$k)
		{
			$result4->data_seek($k);
			$marks = $result4->fetch_array(MYSQLI_NUM);
			$row[$marks[4]] = new Mark($marks[0], $marks[3]);
			//$ved[$stud_id[0]][$marks[4]]['id'] = $marks[0];	
		}	
		$ved[] = $row;
	}
	return $ved;
}

function updateDB($ved, $connection)
{
	foreach($ved as $index => $row) 
	{
		for ($i = 1; $i <=3; $i++)
		{
			if (isset($row[$i]->new_value) and ($row[$i]->new_value != $row[$i]->value))
			{
				if (isset($row[$i]->value))
				{		
					$id = $row[$i]->id;
					$new_mark = $row[$i]->new_value;
					$query = " UPDATE mark SET mark = $new_mark WHERE id = $id";
					$result = $connection->query($query);
					if (!$result) die ("Сбой при обновлении данных".$connection->error());
					else echo "Данные успешно сохранены";	
				}
				else                  // сократить, одинаковый код
				{
					$stud_id = $row[0]->email;
					$new_mark = $row[$i]->new_mark;
					$subj_id = $_COOKIE['subject_id'];
					$query = "INSERT INTO mark VALUES(NULL,'$stud_id', '$subj_id', '$new_mark', '$i')";
					$result = $connection->query($query);
					if (!$result) die($connection->error);
					else echo "Данные успешно сохранены";
				}
			}
		}
	}
}

function setNewMarks($ved)
{
	foreach($ved as $index => $row) 
	{
		for ($i = 1; $i <=3; $i++)
		{
			if (isset($row[$i]->new_value) and ($row[$i]->new_value != $row[$i]->value))
			{
				$row[$i]->value = $row[$i]->new_value;
			}
		}
	}
}

function readExelFile($filepath)
{
	require_once "phpexcel/PHPExcel.php"; //подключаем наш фреймворк

	$ar = array(); // инициализируем массив

	$inputFileType = PHPExcel_IOFactory::identify($filepath);  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
	$objReader = PHPExcel_IOFactory::createReader($inputFileType); // создаем объект для чтения файла
	$objPHPExcel = $objReader->load($filepath); // загружаем данные файла в объект
	$ar = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные из объекта в массив

	return $ar; //возвращаем массив
}

function update_ved_from_file($arr, $ved)
{
	 $MOST_SHORT_SURNAME = 2;
	 $MOST_LONG_SURNAME = 30;
	 $MIDDLE_SHORT_SURNAME = 5;
	 $MIDDLE_LONG_SURNAME = 15;
	
	$max_count_string_in_column = 0;
	$ind_max_count_string = -1;
	
	for($col = 0; $col < count($arr[0]); $col++)
	{
		$sum_length = 0;
		$count_string = 0;
		for ($row = 0; $row < count($arr); $row++)
		{
			$len = mb_strlen((string)$arr[$row][$col]); 
			$cell = $arr[$row][$col];
			echo "ячейка $cell <br>";
			echo " len $len <br>";
			if ($MOST_SHORT_SURNAME <= $len and $len <= $MOST_LONG_SURNAME and ! (preg_match('*[0-9]*',$arr[$row][$col])))
			{  // это инициалы, отдельная ячейка
				echo "это инициалы <br>";
				$count_string++;
				$sum_length += $len;
			}
		}
		echo "количство инициалов в столбце $count_string <br>";
		if (! $count_string)
			continue;
		echo "считаем среднюю длину фамилии<br>";
		$middle_len = $sum_length / $count_string;
		echo "средняя длина инициалов $middle_len <br>";
		if ($MIDDLE_SHORT_SURNAME <= $middle_len and $middle_len <= $MIDDLE_LONG_SURNAME )
		{  // это инициалы, точно, весь столбец
			if ($count_string > $max_count_string_in_column){
				$max_count_string_in_column = $count_string;
				$ind_max_count_string = $col;}
		}
	}						
	if ($ind_max_count_string != -1)
		echo "Фамилии найдены. Столбец с номером $ind_max_count_string <br>";
	else echo "Студенты не найдены в ведомости <br>";
	
	$initials_index = $ind_max_count_string;
	$initials_count = $max_count_string_in_column;
	$marks = array();
	
	for($col = $initials_index + 1 ; $col < count($arr[0]); $col++)
	{
		$count_marks = 0;
		for ($row = 0; $row < count($arr); $row++)
		{
			$cell = $arr[$row][$col];
			echo "ячейка $cell <br>";
			if ((int)$arr[$row][$col] >= 0 and (int)$arr[$row][$col] <= 50) 
			{  // это оценка, отдельная ячейка
				if ($arr[$row][$col] != ''){
				echo "это оценка <br>";
				$count_marks++;}
			}
			else goto m1;
		}
		if ($count_marks >= $initials_count/2)
		{  // это оценки, точно, весь столбец
				$marks[] = $col;
				if (count($marks) == 3)
					break;
		}
m1:		
	}					
		$cm = count($marks);
		echo " Найдено $cm аттестаций в <br>";
		foreach( $marks as $key => $val)
			echo " $val			<br>";
		echo "столбцах <br>";
	
	
	foreach ($ved as $index => $row)
	{
		for($row_i = 0; $row_i < count($arr); $row_i++)
		{
			if ($arr[$row_i][$initials_index] == $row[0]->initials)
			{
				for( $i = 0; $i <= 2; $i++){
				if ($arr[$row_i][$marks[$i]] != "")
					$row[$i+1]->new_value = $arr[$row_i][$marks[$i]];
			}}
		}
	}
}

class Student
{
	public $email;
	public $initials;
	
	function __construct($_email, $_initials)
	{
		$this->email = $_email;
		$this->initials = $_initials;
	}
}

class Mark
{
	public $id;
	public $value;
	public $new_value;
	
	function __construct($id, $value)
	{
		$this->id = $id;
		$this->value = $value;
	}
}
 ?>