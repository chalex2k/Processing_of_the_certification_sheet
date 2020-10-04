<?php      					// todo сохранение с помощью ajax.  обработка ситуации, когда оценки за какую-то аттестацию нет.  Проверка оценок которые пришли от пользователя
			// todo если нет студентов, не отображать кнопку сохранить.  при сохранении поля в первой форме должны оставаться заполнены прошлыми данными.
			// todo удалять файл ведомости после обработки
	try
	{
		require_once 'header_lect.php';  
		require 'render.php' ;
		
		$ok = false;
		$send = false;
		$save = false;
		$subject = Null;
		$group = Null;
		$semester = Null;
		$list = Null;
		if (isset($_POST['ok'])) 
		{
			$ok = true;
			$group =  $_POST['group'];
			$semester = $_POST['semester'];
			$subject = $_POST['subject'];
			setcookie('group', $group);
			setcookie('semester', $semester);
			setcookie('subject_id', $subject);
			$list = get_list($group, $semester, $subject);
			$subject = get_subject_name($subject);
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
				$send = true;
				unlink($destiation_dir); //?
			}
		}
		if (isset($_POST['save']))
		{	
			$list = get_list($_COOKIE['group'], $_COOKIE['semester'], $_COOKIE['subject_id'], $connection);
			get_new_marks($list);
			update_DB($list);
			$save = true;
			set_new_marks($list);
			return "saved";
		}

		if (isset($_POST['load']))
		{
			$list = get_list($_COOKIE['group'], $_COOKIE['semester'], $_COOKIE['subject_id'], $connection);
			$file = create_file($list);
			//echo ("$file");
			setcookie('fn', $file);
		}
		$subjects = get_users_subjects($user_email);
		return render('layout',
			['title' => $title,
			'userstr' => $userstr,
			'header' => render('lect_header', []),
			'content' => render('lect_ved', ['subjects' => $subjects, 'semester' => $semester, 'subject' => $subject, 'group' => $group, 'table' => $ok, 'save' => $save, 'send' => $send, 'list' => $list])]);
	}
	catch(Exception $exp)
	{
		return render('error', ['error_msg' => $exp->getMessage()]);
	}

	function create_file($list)
	{
        	require_once "../phpexcel/Classes/PHPExcel.php";
                                                    
$document = new \PHPExcel();

$sheet = $document->setActiveSheetIndex(0); // Выбираем первый лист в документе

$columnPosition = 0; // Начальная координата x
$startLine = 2; // Начальная координата y

// Вставляем заголовок в "A2" 
//$sheet->setCellValueByColumnAndRow($columnPosition, $startLine, 'Our cats');

// Выравниваем по центру
//$sheet->getStyleByColumnAndRow($columnPosition, $startLine)->getAlignment()->setHorizontal(
//    PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// Объединяем ячейки "A2:C2"
//$document->getActiveSheet()->mergeCellsByColumnAndRow($columnPosition, $startLine, $columnPosition+2, $startLine);

// Перекидываем указатель на следующую строку
//$startLine++;

// Массив с названиями столбцов
$columns = ['Студент', '1', '2', '3'];

// Указатель на первый столбец
$currentColumn = $columnPosition;

// Формируем шапку
foreach ($columns as $column) {
    // Красим ячейку
    //$sheet->getStyleByColumnAndRow($currentColumn, $startLine)
     //   ->getFill()
     //  ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
     //  ->getStartColor()
     //   ->setRGB('4dbf62');

    $sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $column);

    // Смещаемся вправо
    $currentColumn++;
}


foreach ($list as $row) {
  $startLine++;
    // Указатель на первый столбец
    $currentColumn = $columnPosition;
    $sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $row[0] -> initials);
	$sheet->setCellValueByColumnAndRow($currentColumn+1, $startLine, $row[1] -> value);
$sheet->setCellValueByColumnAndRow($currentColumn+2, $startLine, $row[2] -> value);
$sheet->setCellValueByColumnAndRow($currentColumn+3, $startLine, $row[3] -> value);

}


$objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel5');
$filename = "List.xls";
$objWriter->save($filename);   
return $filename;
	}

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
		// done!
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
		if (! $result) throw new Exception('Ошибка при запосе к БД');
		$result -> data_seek(0);
		while ($row = $result -> fetch_array(MYSQLI_ASSOC))
		{
			$append = ($row['mark'] == 1) ? " (оценка)" : " (зачёт)";
			$subjects[$row['id']] = $row['name'] . $append;
		}
		return $subjects;
	}

	function get_list($group, $semester, $subject_id)
	{
		//echo "get_list: $group, $semester, $subject_id";
		global $connection;
		$query = "SELECT id, surname, name 
					FROM user 
					WHERE user.id IN 
						(SELECT id
							FROM student 
							WHERE semester = $semester AND _group = $group)";		
		$result = $connection -> query($query);
		if (! $result) throw new Exception('Ошибка при запосе к БД');
		$result -> data_seek(0);
		$list = array();
		echo($result->num_rows);
		while ($student = $result -> fetch_array(MYSQLI_ASSOC))
		{
			//echo var_dump($student);
			$row = array();
			$initials = $student['surname'] . '. ' . substr($student['name'], 0, 2) . '.';
			$row[0] =  new Student($student['id'], $initials);
			$student_id = $student['id']; 
			$query2 = " SELECT id, mark, attestation_number 
							FROM mark 
							WHERE subject_id = $subject_id AND student_id = '$student_id'";
			$result2 = $connection -> query($query2);
			if (! $result2) throw new Exception('Ошибка при запросе к БД');
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
	
	// Получает название предмета по id из БД и возвращает его.
	function get_subject_name($subject_id)
	{
		global $connection;
		$query = "SELECT name, mark
					FROM subject 
					WHERE id = $subject_id;";
		$result = $connection->query($query);
		if (! $result) throw new Exception('Ошибка при запосе к БД');
		$result -> data_seek(0);
		$row = $result -> fetch_array(MYSQLI_ASSOC);
		$append = ($row['mark'] == 1) ? " (оценка)" : " (зачёт)";
		return $row['name'] . $append;
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
						if (! $result) throw new Exception('Ошибка при запосе к БД');
					}
					elseif ($row[$i] -> new_value == '')
					{
						$id = $row[$i] -> id;
						$query = "DELETE FROM mark WHERE id = $id";
						$result = $connection -> query($query);
						if (! $result) throw new Exception('Ошибка при запосе к БД');
					}
					else
					{
						$id = $row[$i] -> id;
							$new_mark = $row[$i] -> new_value;
							$query = "UPDATE mark SET mark = $new_mark WHERE id = $id";
							$result = $connection -> query($query);
							if (! $result) throw new Exception('Ошибка при запосе к БД');
					}
				}	
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
		require_once "phpexcel/PHPExcel.php"; //подключаем фреймворк
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
				//echo "ячейка $cell <br>";
				//echo " len $len <br>";
				if ($MOST_SHORT_SURNAME <= $len and $len <= $MOST_LONG_SURNAME and ! (preg_match('*[0-9]*',$arr[$row][$col])))
				{  // это инициалы, отдельная ячейка
					//echo "это инициалы <br>";
					$count_string++;
					$sum_length += $len;
				}
			}
			//echo "количство инициалов в столбце $count_string <br>";
			if (! $count_string)
				continue;
			//echo "считаем среднюю длину фамилии<br>";
			$middle_len = $sum_length / $count_string;
			//echo "средняя длина инициалов $middle_len <br>";
			if ($MIDDLE_SHORT_SURNAME <= $middle_len and $middle_len <= $MIDDLE_LONG_SURNAME )
			{  // это инициалы, точно, весь столбец
				if ($count_string > $max_count_string_in_column){
					$max_count_string_in_column = $count_string;
					$ind_max_count_string = $col;}
			}
		}						
		//if ($ind_max_count_string != -1)
		//	echo "Фамилии найдены. Столбец с номером $ind_max_count_string <br>";
		//else echo "Студенты не найдены в ведомости <br>";
		
		$initials_index = $ind_max_count_string;
		$initials_count = $max_count_string_in_column;
		$marks = array();
		
		for($col = $initials_index + 1 ; $col < count($arr[0]); $col++)
		{
			$count_marks = 0;
			for ($row = 0; $row < count($arr); $row++)
			{
				$cell = $arr[$row][$col];
				//echo "ячейка $cell <br>";
				if ((int)$arr[$row][$col] >= 0 and (int)$arr[$row][$col] <= 50) 
				{  // это оценка, отдельная ячейка
					if ($arr[$row][$col] != ''){
					//echo "это оценка <br>";
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
			//echo " Найдено $cm аттестаций в <br>";
			//foreach( $marks as $key => $val)
			//	echo " $val			<br>";
			//echo "столбцах <br>";
		
		
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