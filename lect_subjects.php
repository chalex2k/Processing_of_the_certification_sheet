<?php 
// добавить всплывающие уведомления, выбор одного или нескольких предметов в добавлении , вместо кнопок удалить нужно использовать иконки корзины   					 
require_once 'header.php';
require_once 'header_lect.php';  // проверка роли(преподаватель)

if (isset($_POST['add']))
	add_subjects($user_email);
$subjects = get_users_subjects($user_email, $connection);
if (delete_subjects($subjects, $user_email))
	$subjects = get_users_subjects($user_email, $connection);			
echo <<<_MAIN
		<main>
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
	$query = "SELECT id, name 
				FROM subject 
				WHERE id IN
					(SELECT subject_id 
						FROM lecturer_subject 
						WHERE lecturer_id = '$user_email')
				ORDER BY name";                            // не сортируются они по алфавиту
	$result = $connection->query($query);
	if (! $result) die ("$connection -> connect_error"); // нормальное сообщение об ошибке и завршение программы
	$result -> data_seek(0);
	while ($row = $result -> fetch_array(MYSQLI_ASSOC))
		$subjects[$row['id']] = $row['name'];
	return $subjects;
}

function add_subjects($useremail)
{
	global $connection;
	$subj_id = $_POST['subject'];
	$query  = "SELECT * FROM lecturer_subject WHERE lecturer_id = '$useremail' AND subject_id = '$subj_id'";
	$result = $connection->query($query);
	if (!$result) die ("$connection->connect_error");
	$rows = $result->num_rows;
	if ($rows == 0)
	{
		$query  = "INSERT INTO lecturer_subject VALUES('$useremail', '$subj_id')";
		$result = $connection->query($query);
		if (!$result) die ("$connection->connect_error");
		echo "<br> Уведомление : Предмет добавлен <br>";
	}
		else echo "<br> Уведомление : предмет существует <br>";
}

function delete_subjects($subjects, $useremail)
{
	global $connection;
	$flag = False;
	foreach ($subjects as $id => $name)
	{
		if (isset($_POST["$id"])) 
		{
			$query  = "DELETE FROM lecturer_subject WHERE lecturer_id = '$useremail' AND subject_id = '$id' ";
			$result = $connection->query($query);
			if (!$result) die ("$connection->connect_error");
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
		echo ("<div class = 'subject'> $name <input type='submit' name='$id' value='Удалить'> </div>");
	}
}

function print_add()
{
	global $connection;
	//echo(" Добавить <br>");
			echo ("<div class = 'add-subject'> <select name = 'subject' size = '1' > ");
			$query  = "SELECT id, name FROM subject ORDER BY name";
			$result = $connection->query($query);
			if (!$result) die ("$connection->connect_error");
			$result->data_seek(0);
			while ($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				echo "<option value = ".$row['id'].">".$row['name']." </option>";
			}
			echo "</select>";
			echo '<input type="submit" name="add" value="Добавить"> </div>';
}
 ?>