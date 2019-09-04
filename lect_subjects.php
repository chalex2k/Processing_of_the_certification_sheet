<?php 
// добавить всплывающие уведомления, вместо кнопок удалить можно использовать иконки корзины, может убрать функции, которые и обращаются в базу и генерируют html, некотороые ошибки фатальные, при которых программа не может дальше работать, а после некоторых можно продолжить выполнение.		 
require_once 'header_lect.php';  
require_once 'show_error_message.php';
echo "<main>";
if (isset($_POST['add']))
	add_subjects($user_email);
$subjects = get_users_subjects($user_email, $connection);
if (delete_subjects($subjects, $user_email))
	$subjects = get_users_subjects($user_email, $connection);			
echo <<<_MAIN
		
        <div class='lect-subjects'>
		<form method="POST" action="lect_subjects.php">
_MAIN;
print_subjects($subjects);
print_add();
echo "</form> </div> </main> </body> </html>";


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

function add_subjects($user_email)
{
	global $connection;
	$subj_id = $_POST['subject'];
	$query  = "SELECT * FROM lecturer_subject WHERE lecturer_id = '$user_email' AND subject_id = '$subj_id'";
	$result = $connection -> query($query);
	if (! $result) show_error_message();
	$rows = $result->num_rows;
	if ($rows == 0)
	{
		$query  = "INSERT INTO lecturer_subject VALUES('$user_email', '$subj_id')";
		$result = $connection -> query($query);
		if (! $result) show_error_message();
		echo "<br> Уведомление : Предмет добавлен <br>";
	}
		else echo "<br> Уведомление : предмет существует <br>";
}

function delete_subjects($subjects, $user_email)
{
	global $connection;
	$flag = False;
	foreach ($subjects as $id => $name)
	{
		if (isset($_POST["$id"])) 
		{
			$query  = "DELETE FROM lecturer_subject WHERE lecturer_id = '$user_email' AND subject_id = '$id' ";
			$result = $connection -> query($query);
			if (! $result) show_error_message();
			$flag = True;
			echo("<br> Уведомление: предмет удалён $id <br>");
		}
	}
	return $flag;
}

function print_subjects($subjects)
{
	foreach ($subjects as $id => $name)
	{
		echo "<div class = 'subject'> $name <input type='submit' name='$id' value='Удалить'> </div>";
	}
}

function print_add()
{
	global $connection;
	echo "<div class = 'add-subject'> Добавить предмет: <br> <select name = 'subject' size = '1' > ";
	$query  = "SELECT id, name, mark FROM subject ORDER BY name";
	$result = $connection -> query($query);
	if (! $result) show_error_message();
	$result -> data_seek(0);
	while ($row = $result -> fetch_array(MYSQLI_ASSOC))
	{
		$append = ($row['mark'] == 1) ? " (оценка)" : " (зачёт)";
		echo "<option value = " . $row['id'] . ">" . $row['name'] . $append . " </option>";
	}
	echo "</select>";
	echo "<input type='submit' name='add' value='Добавить'> </div>";
}
 ?>