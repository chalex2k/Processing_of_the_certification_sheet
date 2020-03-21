<?php
	try
	{
		require('header_lect.php');// при пустой странице писать выберите группу
		require('render.php');// сохранение данных формы при обновлении страницы
		$subjects = get_users_subjects($user_email);
		$ok = false;
		$rating = Null;
		$middle_mark = Null;
		$total_middle_mark = Null;
		$subject = Null;
		$group = Null;
		if (isset($_POST['ok']))
		{
			$ok = true;
			$rating = get_rating($_POST['group'], $_POST['semester'], $_POST['subject']);
			if ($_POST['mode'] == 'alph')
				ksort($rating);
			else
				arsort($rating);
			$group =  $_POST['group'];
			$subject = get_subject_name($_POST['subject']);
			$temp_rating = $rating;
			delete_emty($temp_rating);
			$total_middle_mark = (count($temp_rating) > 0) ? array_sum($temp_rating) / count($temp_rating) : '-';
		}
		return render('layout',
			['title' => $title,
			'userstr' => $userstr,
			'header' => render('lect_header', []),
			'content' => render('lect_rating', ['subjects' => $subjects, 'subject' => $subject, 'group' => $group, 'table' => $ok, 'rating' => $rating, 'middle_mark' => $middle_mark, 'total_middle_mark' => $total_middle_mark])]);
	}
	catch(Exception $exp)
	{
		return render('error', ['error_msg' => $exp->getMessage()]);
	}
	
	
	// Получает средние оценки студентов в группе. Возвращает массив ['Фамилия. И.'] => средний балл.
	function get_rating($group, $semester, $subject_id)
	{
		global $connection;	
		$query = " SELECT user.surname, user.name, ROUND(AVG(mark.mark),2) average 
					FROM (SELECT email, surname, name 
								FROM user 
								WHERE user.id IN 
									(SELECT id 
										FROM student 
										WHERE semester = $semester AND _group = $group)) AS user 
						LEFT OUTER JOIN 
						(SELECT * 
							FROM mark 
							WHERE mark.subject_id = $subject_id) AS mark 
						ON user.email = mark.student_id
						GROUP BY user.surname, user.name, user.email";
		$result = $connection -> query($query);
		if (! $result) throw new Exception('Ошибка при запосе к БД');
		$result -> data_seek(0);
		$rating = array();
		while ($row = $result -> fetch_array(MYSQLI_ASSOC))
		{
			$rating[$row['surname'] . '. ' . substr($row['name'], 0, 2) . '.'] = $row['average'] != '' ? (double)$row['average'] : $row['average'];
		}	
		return $rating;
	}

	// Читает из БД все предметы пользователя. Возвращает массив [id_предмета] => название_предмета (оценка)|(зачёт).
	function get_users_subjects($user_email) // Такая же есть в lect_subjects.
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

	// Удаляет из массива пустые строки
	function delete_emty(&$arr)
	{
		foreach ($arr as $key => $value)
			if ((string)$value == '')
				unset($arr[$key]);
	}

	// Получает название предмета по id из БД и возвращает его.
	function get_subject_name($subject_id)
	{
		global $connection;
		$query = "SELECT name 
					FROM subject 
					WHERE id = $subject_id;";
		$result = $connection->query($query);
		if (! $result) throw new Exception('Ошибка при запосе к БД');
		$result -> data_seek(0);
		$row = $result -> fetch_array(MYSQLI_ASSOC);
		return $row['name'];
	}
 ?>