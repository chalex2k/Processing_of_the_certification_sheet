<?php      					// !!! сохранение с помощью ajax.  обработка ситуации, когда оценки за какую-то аттестацию нет.  Проверка оценок которые пришли от пользователя
			// !!! если нет студентов, не отображать кнопку сохранить.  при сохранении поля в первой форме должны оставаться заполнены прошлыми данными.
require_once 'header_lect.php';  
require_once 'show_error_message.php';
echo "<main>
	<div class = 'lect-ved'>";
show_form($user_email);

if (isset($_POST['ok'])) 
{
	$group =  $_POST['group'];
	$semester = $_POST['semester'];
	$subject = $_POST['subject'];
	setcookie('group', $group);
	setcookie('semester', $semester);
	setcookie('subject_id', $subject);
	$list = get_list($group, $semester, $subject);
	//var_dump($list);
	print_list($list);
}		
if (isset($_POST['send']))
{	
	if(isset($_FILES) && $_FILES['user_file']['error'] == 0)
	{ // Проверяем, загрузил ли пользователь файл
		$destiation_dir = dirname(__FILE__) .'/' . $_FILES['user_file']['name']; // Директория для размещения файла
		move_uploaded_file($_FILES['user_file']['tmp_name'], $destiation_dir ); // Перемещаем файл в желаемую директорию
		//echo 'File Uploaded';  // Оповещаем пользователя об успешной загрузке файла
		$arr = read_exel_file($_FILES['user_file']['name']);
		// echo("<br>". $_FILES['user_file']['name']);
		$list = get_list($_COOKIE['group'], $_COOKIE['semester'], $_COOKIE['subject_id']);	
		update_list_from_file($arr, $list);
		set_new_marks($list);
		echo "Уведомление: оценки установлены";
		unlink($destiation_dir);
		print_list($list);
	}
}
if (isset($_POST['save']))
{	
	$list = get_list($_COOKIE['group'], $_COOKIE['semester'], $_COOKIE['subject_id'], $connection);
	get_new_marks($list);
	if (update_DB($list))
		echo "Уведомление: Данные сохранены.";
	else
		echo "Уведомление: Произошла ошибка. Данные не сохранены.";
	set_new_marks($list);
	print_list($list);	
}
echo "</div></main></body> </html>";

function get_new_marks($list)
{
	$attestations[1] = "att1";
	$attestations[2] = "att2";
	$attestations[3] = "att3";
	for ($i = 1; $i <= 3; ++$i)
		for ($j = 0; $j < count($list); $j++)
		{
			$mark = $_POST["$attestations[$i]"][$j];
			if ( trim($mark) == '' or (int)$mark >= 0 and (int)$mark <= 50)
				$list[$j][$i] -> new_value = $mark;
		}
}

function show_form($user_email)
{
	echo <<<_GRSEM
	<div class='lect-ved-item'>
	<div class='ved-form'>
		<form method="POST" action="lect_ved.php">
		<div class = 'ved-form-item'>
			Семестр <br>
			<select name = 'semester' size = '1'>
				<option value = '1'> 1 </option>
				<option value = '2'> 2 </option>
				<option selected value = '3'> 3 </option>
				<option value = '4'> 4 </option>
				<option value = '5'> 5 </option>
				<option value = '6'> 6 </option>
				<option value = '7'> 7 </option>
				<option value = '8'> 8 </option>
			</select>
		</div>
		<div class = 'ved-form-item'>
		Группа <br>
		<select name = 'group' size = '1' >
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
		</div>
_GRSEM;
		
	$subjects = get_users_subjects($user_email);
	echo ("<div class = 'ved-form-item'>
	Предмет <br>
	<select name = 'subject' size = '1' >");
	foreach ($subjects as $key => $value)
		echo "<option value = ".$key.">"."$value"." </option>";
	echo "</select> </div>";
	echo '<div class = "ved-form-item"> <input type="submit" name="ok" value="Ok"> </div>';
	echo '</form>
	</div></div>';
}

function get_users_subjects($user_email)
{
	global $connection;
	$subjects = array();
	$query = "SELECT id, name, mark 
				FROM subject 
				WHERE id IN
					(SELECT subject_id 
						FROM lecturer_subject 
						WHERE lecturer_id = '$user_email')
				ORDER BY name";
	$result = $connection->query($query);
	if (! $result) show_error_message();
	$result -> data_seek(0);
	while ($row = $result -> fetch_array(MYSQLI_ASSOC))
	{
		$append = ($row['mark'] == 1) ? " (оценка)" : " (зачёт)";
		$subjects[$row['id']] = $row['name'] . $append;
	}
	return $subjects;
}

function print_list($list)
{
	echo " <div class='lect-ved-item'>
		   <div class='ved-list>
			<div class='ved-list-item>";
	echo '<form method="POST" action="lect_ved.php">
		<table>';
	foreach ($list as $row) // если не все оценки стоят?
	{
		echo '<tr> <td><input type="text" name="list[]" value = "' . $row[0] -> initials . '"></td>
				   <td><input type="text" name="att1[]" value="' . $row[1] -> value . '"></td>
				   <td><input type="text" name="att2[]" value="' . $row[2] -> value . '"></td>
				   <td><input type="text" name="att3[]" value="' . $row[3] -> value.'"></td>
	</tr>';		}	
	echo '
	</table>
       <br>
	   <input type="submit" name="save" value="save" />
       
	   </form>
	   </div>';

		echo ("<div class='ved-list-item'>");
	echo ('<!-- Тип кодирования данных, enctype, ДОЛЖЕН БЫТЬ указан ИМЕННО так -->
<form enctype="multipart/form-data" action="lect_ved.php" method="POST">
    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Название элемента input определяет имя в массиве $_FILES -->
    Отправить этот файл: <input name="user_file" type="file" />
    <input type="submit" name = "send" value="Отправить файл" />
</form>');

	echo "</div>";
	echo "</div></div>";
}

function get_list($group, $semester, $subject_id)
{
	global $connection;
	$query = "SELECT email, surname, name 
				FROM user 
				WHERE user.email IN 
					(SELECT id 
						FROM student 
						WHERE semester = $semester AND _group = $group)";		
	$result = $connection -> query($query);
	if (! $result) show_error_message();
	$result -> data_seek(0);
	$list = array();
	while ($student = $result -> fetch_array(MYSQLI_ASSOC))
	{
		$row = array();
		$initials = $student['surname'] . '. ' . substr($student['name'], 0, 2) . '.';
		$row[0] =  new Student($student['email'], $initials);
		$student_id = $student['email']; 
		$query2 = " SELECT id, mark, attestation_number 
						FROM mark 
						WHERE subject_id = $subject_id AND student_id = '$student_id'";
		$result2 = $connection -> query($query2);
		if (! $result2) show_error_message($connection -> error);
		$result2 -> data_seek(0);
		while ($mark = $result2 -> fetch_array(MYSQLI_ASSOC))
			$row[$mark['attestation_number']] = new Mark($mark['id'], $mark['mark']);
		$list[] = $row;
	}
	for($j=0;$j<count($list);$j++)
		for ($i = 1; $i <= 3; $i++)
			if (! isset($list[$j][$i]))
				$list[$j][$i] = new Mark('', '');
	usort($list, "cmp_rows");
	return $list;
}

function cmp_rows($row1, $row2)
{
	if ($row1[0] -> initials > $row2[0] -> initials)
		return 1;
	elseif($row1[0] -> initials < $row2[0] -> initials)
		return -1;
	return 0;
}

function update_DB($list)
{
	global $connection;
	foreach($list as $row) 
		for ($i = 1; $i <=3; $i++)
			if (isset($row[$i] -> new_value) and $row[$i] -> new_value != $row[$i] -> value)
			{
				if ($row[$i] -> value == '')
				{
					$stud_id = $row[0] -> email;
					$new_mark = $row[$i] -> new_value;
					$subj_id = $_COOKIE['subject_id'];
					$query = "INSERT INTO mark VALUES(NULL, '$stud_id', '$subj_id', '$new_mark', '$i')";
					$result = $connection -> query($query);
					if (! $result) return false;
				}
				elseif ($row[$i] -> new_value == '')
				{
					$id = $row[$i] -> id;
					$query = "DELETE FROM mark WHERE id = $id";
					$result = $connection -> query($query);
					if (! $result) return false;
				}
				else
				{
					$id = $row[$i] -> id;
						$new_mark = $row[$i] -> new_value;
						$query = "UPDATE mark SET mark = $new_mark WHERE id = $id";
						$result = $connection -> query($query);
						if (! $result) return false;
				}
			}	
	return true;
}

function set_new_marks($list)
{
	foreach($list as $row)
		for ($i = 1; $i <= 3; $i++)
			if (isset($row[$i] -> new_value) and ($row[$i] -> new_value != $row[$i] -> value))
				$row[$i] -> value = $row[$i] -> new_value;
}

function read_exel_file($file_path)
{
	require_once "phpexcel/PHPExcel.php"; //подключаем наш фреймворк
	$ar = array(); // инициализируем массив
	$input_file_type = PHPExcel_IOFactory :: identify($file_path);  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
	$reader = PHPExcel_IOFactory :: createReader($input_file_type); // создаем объект для чтения файла
	$obj_PHP_Excel = $reader -> load($file_path); // загружаем данные файла в объект
	$ar = $obj_PHP_Excel -> getActiveSheet() -> toArray(); // выгружаем данные из объекта в массив
	return $ar; //возвращаем массив
}

function update_list_from_file($arr, $list)
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
	
	
	foreach ($list as $index => $row)
	{
		for($row_i = 0; $row_i < count($arr); $row_i++)
		{
			if ($arr[$row_i][$initials_index] == $row[0] -> initials)
			{
				for( $i = 0; $i <= 2; $i++)
				{
					$mark = $arr[$row_i][$marks[$i]];
					if ( trim($mark) == '' or (int)$mark >= 0 and (int)$mark <= 50)
						$row[$i+1] -> new_value = $mark;
				}
			}
		}
	}
}

class Student
{
	public $email;
	public $initials;
	
	function __construct($email, $initials)
	{
		$this -> email = $email;
		$this -> initials = $initials;
	}
}

class Mark
{
	public $id;
	public $value;
	public $new_value;
	
	function __construct($id, $value)
	{
		$this -> id = $id;
		$this -> value = $value;
	}
}
 ?>