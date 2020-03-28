<div class = 'lect-ved'>
	<div class='lect-ved-item'>
		<div class='ved-form'>
			<form method="POST" action="lect_rating.php">
				<div class="ved-form-item">
					<select name = 'semester' size = '1'>
						<option selected >Семестр</option>
						<option value = '1'> 1 </option>
						<option value = '2'> 2 </option>
						<option value = '3'> 3 </option>
						<option value = '4'> 4 </option>
						<option value = '5'> 5 </option>
						<option value = '6'> 6 </option>
						<option value = '7'> 7 </option>
						<option value = '8'> 8 </option>
					</select>
				</div>
				<div class="ved-form-item">
					<select name = 'group' size = '1' >
						<option selected >Группа</option>
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
				<div class="ved-form-item">
					<select name = 'subject' size = '1' >
					<option selected >Предмет</option>
					<?php foreach ($subjects as $key => $value)
					{
						echo "<option value = " . $key . ">" . "$value" . " </option>";
					} ?>
					</select>
				</div>
				<div class="ved-form-item">
					
						по алфавиту <input type = "radio" name = "mode" value = "alph" checked = "checked">
						по среднему баллу <input type = "radio" name = "mode" value = "mark" >
					
				</div>
				<div class="ved-form-item">
					<button type="submit" name="ok" value="Ok">Ok</button>
				</div>
			</form>
		</div>
	</div>
	
	
	
	<div class='lect-ved-item2'>
	<table class="ved">
	<?php if($table)
	{
		echo "<tr> <td colspan='2'>$subject. Группа $group</td> </tr>";
		if (count($rating) == 0)
		{
			echo "<tr> <td colspan='2'>Никаких студентов не существует! Это всё выдумки.</td> </tr>";
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
			echo ("<tr> <td>Группа</td> <td> $total_middle_mark </td> </tr>");
			
		}
	}
	else
	{
		echo "<tr> <td colspan='2'>заполните форму справа.</td> </tr>";
	}?>
	</table>
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
