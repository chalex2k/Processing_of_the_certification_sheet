<?php
	// добавить всплывающие уведомления, вместо кнопок удалить можно использовать иконки корзины, может убрать функции, которые и обращаются в базу и генерируют html, некотороые ошибки фатальные, при которых программа не может дальше работать, а после некоторых можно продолжить выполнение.		 
	require_once 'header_lect.php';  
	require_once 'show_error_message.php';
	
	echo "<main>";
	$subjects = get_users_subjects($user_email);
	if (add_subject($user_email) || delete_subject($subjects, $user_email))
		$subjects = get_users_subjects($user_email);
	echo <<<_MAIN
		
        <div class='lect-subjects'>
		<form method="POST" action="lect_subjects.php">
_MAIN;
	print_subjects($subjects);
	print_add();
	echo "</form> </div> </main>
		<script>
		// ex1 - сообщение
			//$('.ex1.alert').click(function() {
			noty({text: 'noty - плагин jQuery для вывода уведомлений!'});
			//});
			//alert('ao!');
		</script>
	</body> </html>";


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
	function add_subject($user_email)
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
			echo "<br> Уведомление : Предмет добавлен <br>";
			return True;
		}
		else
		{		
			echo "<br> Уведомление : Предмет существует <br>";
			return False;
		}
	}

	// Удаляет предмет из БД, если его id указан в POST. Возвращает True, если предмет был удалён, False иначе.
	function delete_subject($subjects, $user_email)
	{
		global $connection;
		foreach ($subjects as $id => $name)
		{
			if (isset($_POST["del$id"])) 
			{
				$query  = "DELETE FROM lecturer_subject WHERE lecturer_id = '$user_email' AND subject_id = '$id' ";
				$result = $connection -> query($query);
				if (! $result) show_error_message();
				echo("<br> Уведомление: предмет удалён $id <br>");
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

	function print_subjects($subjects)
	{
		foreach ($subjects as $id => $name)
		{
			echo "<div class = 'subject'> $name <input type='submit' name='del$id' value='Удалить'> </div>";
		}
	}

	function print_add()
	{
		global $connection;
		$all_subj = get_all_subjects();
		echo "<div class = 'add-subject'> Добавить предмет: <br> <select name = 'subject' size = '1' > ";
		foreach ($all_subj as $id => $name)
		{
			echo "<option value = " . $id . ">" . $name . " </option>";
		}
		echo "</select>";
		echo "<input type='submit' name='add' value='Добавить'> </div>";
	}
 ?>