<?php
	// добавить всплывающие уведомления, вместо кнопок удалить можно использовать иконки корзины, может убрать функции, которые и обращаются в базу и генерируют html, некотороые ошибки фатальные, при которых программа не может дальше работать, а после некоторых можно продолжить выполнение.		 
	require('header_lect.php');
	require('render.php');

	$subjects = get_users_subjects($user_email);
	$notice = '';
	if (add_subject($user_email, $notice) || delete_subject($subjects, $user_email, $notice))
		 $subjects = get_users_subjects($user_email);
	$all_subj = get_all_subjects();

	return render('layout',
		['title' => $title,
		 'userstr' => $userstr,
		 'header' => render('lect_header', []),
		 'content' => render('lect_subjects', ['subjects' => $subjects, 'all_subjects' => $all_subj, 'notice' => $notice])]
		);


	// Читает из БД все предметы пользователя. Возвращает массив [id_предмета] => название_предмета (оценка)|(зачёт).
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

	// Добавляет в БД пользователю предмет с id из массива POST (если он сущствует). Возвращает True, если предмет был добавлен, False иначе. 
	function add_subject($user_email, &$notice)
	{
		if (! isset($_POST['add']))
			return False;
		global $connection;
		$subj_id = $_POST['subject'];
		$query  = "SELECT * FROM lecturer_subject WHERE lecturer_id = '$user_email' AND subject_id = '$subj_id'";
		$result = $connection -> query($query);
		if (! $result) show_error_message();
		$rows = $result->num_rows;
		if ($rows == 0)
		{
			$query = "INSERT INTO lecturer_subject VALUES('$user_email', '$subj_id')";
			$result = $connection -> query($query);
			if (! $result) show_error_message();
			$notice = "Предмет добавлен";
			return True;
		}
		else
		{		
			$notice = "Предмет существует";
			return False;
		}
	}

	// Удаляет предмет из БД, если его id указан в POST. Возвращает True, если предмет был удалён, False иначе.
	function delete_subject($subjects, $user_email, &$notice)
	{
		global $connection;
		foreach ($subjects as $id => $name)
		{
			if (isset($_POST["del$id"])) 
			{
				$query  = "DELETE FROM lecturer_subject WHERE lecturer_id = '$user_email' AND subject_id = '$id' ";
				$result = $connection -> query($query);
				if (! $result) show_error_message();
				$notice = "предмет удалён $id";
				return True;
			}
		}
		return False;
	}

	// Читает из БД все предметы. Возвращает массив [id_предмета] => название_предмета (оценка)|(зачёт).
	function get_all_subjects()
	{
		global $connection;
		$subjects = array();
		$query = "SELECT id, name, mark FROM subject ORDER BY name";
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
 ?>