<?php      			// беда с кавычками
require_once 'header_lect.php';  // при пустой странице писать выберете группу
require_once 'show_error_message.php';
echo "<main>
	<div class = 'lect-rating'>";
show_form($user_email);
if (isset($_POST['ok'])) 
{
	$rating = get_rating($_POST['group'], $_POST['semester'], $_POST['subject']);
	if ($_POST['mode'] == 'alf')
		ksort($rating);
	else 
		arsort($rating);
	show_table_students($rating, $_POST['group'], get_subject( $_POST['subject']));
}
echo "</div></main></body> </html>	";
			
function show_table_students($rating, $group, $subject)
{
	echo " <div class='lect-rating-item'>
	$subject. Группа $group <br>
	<table>";
	echo '<tr> <td>Студент</td>
				<td>Средний балл</td>
						</tr>';
	foreach ($rating as $initials => $middle_mark)
	{
		echo ("<tr> <td> <span class = '" . get_class_color($middle_mark) . "'>" . $initials . ' ' . ' </span> </td>
			<td>' . $middle_mark . '</td>
			</tr>');
	}				
	echo '</table>';
	$total_middle_mark = culc_middle_value($rating);
	echo ("Среднеий балл по группе " . $total_middle_mark);
	echo '</div>';
}

function get_class_color($mark)
{
	if ($mark < 25)
		return 'mark2';
	elseif ($mark < 35)
		return 'mark3';
	elseif ($mark < 45)
		return 'mark4';
	else 
		return "mark5";
}

function get_rating($group, $semester, $subject_id)
{
	global $connection;	
	$query = " SELECT user.surname, user.name, AVG(mark.mark) average 
				   FROM (SELECT email, surname, name 
							FROM user 
							WHERE user.email IN 
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
	if (! $result) show_error_message();
	$result -> data_seek(0);
	$rating = array();
	while ($row = $result -> fetch_array(MYSQLI_ASSOC))
	{
		$rating[$row['surname'] . '. ' . substr($row['name'], 0, 2) . '.'] = $row['average'];
	}	
	return $rating;
}

function show_form($user_email)
{
	echo <<<_GRSEM
	<div class='lect-rating-item'>
	<div class='rating-form'>
		<form method="POST" action="lect_rating.php">
		<div class = 'rating-form-item'>
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
		<div class = 'rating-form-item'>
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
	echo ("<div class = 'rating-form-item'>
	Предмет <br>
	<select name = 'subject' size = '1' >");
	foreach ($subjects as $key => $value)
	{
		echo "<option value = ".$key.">"."$value"." </option>";
	}
	echo "</select> </div>";
	echo '<div class = "rating-form-item"> по алфавиту <input type = "radio" name = "mode" value = "alf" checked = "checked"> ';
	echo 'по среднему баллу <input type = "radio" name = "mode" value = "mark" > </div> ';
	echo '<div class = "rating-form-item"> <input type="submit" name="ok" value="Ok"> </div>';
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

function culc_middle_value($arr)
{ // деление на ноль
	$count = 0;
	foreach ($arr as $value)
		if (! empty($value))
			$count++;
	return array_sum($arr) / $count;
}

function get_subject($subject_id)
{
	global $connection;
	$query = "SELECT name 
				FROM subject 
				WHERE id = $subject_id;";
	$result = $connection->query($query);
	if (! $result) show_error_message();
	$result -> data_seek(0);
	$row = $result -> fetch_array(MYSQLI_ASSOC);
	return $row['name'];
}
 ?>

	  
  
  