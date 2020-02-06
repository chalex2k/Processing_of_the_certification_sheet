 <div class = 'lect-rating'>
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
						<option selected value = '3'> 3 </option>
						<option value = '4'> 4 </option>
						<option value = '5'> 5 </option>
						<option value = '6'> 6 </option>
						<option value = '7'> 7 </option>
						<option value = '8'> 8 </option>
						<option value = '9'> 9 </option>
					</select>
				</div>
				<div class = 'rating-form-item'>
					Предмет <br>
					<select name = 'subject' size = '1' >
					<?php foreach ($subjects as $key => $value)
					{
						echo "<option value = " . $key . ">" . "$value" . " </option>";
					} ?>
					</select>
				</div>
				<div class = "rating-form-item">
					по алфавиту <input type = "radio" name = "mode" value = "alph" checked = "checked">
					по среднему баллу <input type = "radio" name = "mode" value = "mark" >
				</div>
				<div class = "rating-form-item">
					<input type="submit" name="ok" value="Ok"> 
				</div>
			</form>
		</div>
	</div>

	<?php if($table)
	{
	echo("<div class='lect-rating-item'>");
		echo("$subject. Группа $group <br>");
		echo("<table>");
		if (count($rating) == 0)
		{
			echo ("Студентов не существует!");
			return; 		// из-за этого слетает вёрстка
		}
		else
		{
			echo '<tr> <td>Студент</td> <td>Средний балл</td> </tr>';
			foreach ($rating as $initials => $middle_mark)
			{
				echo ("<tr> <td> <span class = '" . get_class_color($middle_mark) . "'>" . $initials . ' ' . ' </span> </td>
					<td>' . $middle_mark . '</td>
					</tr>');
			}				
			echo '</table>';
			echo ("Среднеий балл по группе " . $total_middle_mark);
			echo '';
		}
	echo('</div>');
	} ?>
</div>

<?php 
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
?>
