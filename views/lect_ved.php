<div class = 'lect-ved'>
	<div class='lect-ved-item'>
		<div class='ved-form'>
			<form method="POST" action="lect_ved.php">
				<div class = 'ved-form-item'>
				Семестр:
				</div>
				<div class = 'ved-form-item'>
					<select name = 'semester' size = '1'>
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
				<div class = 'ved-form-item'>
				Группа:
				</div>
				<div class = 'ved-form-item'>
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
				<div class = 'ved-form-item'>
				Предмет:
				</div>
				<div class = 'ved-form-item'>
					<select name = 'subject' size = '1' >
					<?php foreach ($subjects as $key => $value)
					{
						echo "<option value = " . $key . ">" . "$value" . " </option>";
					} ?>
					</select>
				</div>
				<div class = "ved-form-item">
					<button type="submit" name="ok" value="Ok">Ok</button>
				</div>
			</form>
		</div>
	</div>

	
	<div class='lect-ved-item2'>
		<?php if($table)
	{
	echo '<form method="POST" action="lect_ved.php">
		<table class="ved">';
	echo '<tr id="hat"><th id="subject">Студент</th><th>1</th><th>2</th><th>3</th></tr>';
	foreach ($list as $row) // если не все оценки стоят?
	{
		echo '<tr> <td id="subject">' . $row[0] -> initials . '"></td>
				   <td><input type="text" class="input-mark" name="att1[]" value="' . $row[1] -> value . '"></td>
				   <td><input type="text" class="input-mark" name="att2[]" value="' . $row[2] -> value . '"></td>
				   <td><input type="text" class="input-mark" name="att3[]" value="' . $row[3] -> value . '"></td>
			 </tr>';		}	
	echo '
	</table>
	
	   <button class = "save" type="submit" name="save" value="save">Сохранить</button>
       
	   </form>
	   </div> </div>';

	echo '<div class= "input-form">';
	echo ('<!-- Тип кодирования данных, enctype, ДОЛЖЕН БЫТЬ указан ИМЕННО так -->
<form enctype="multipart/form-data" action="lect_ved.php" method="POST">
    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Название элемента input определяет имя в массиве $_FILES -->
    Загрузить из файла excel: <input name="user_file" type="file" />
    <input type="submit" name = "send" value="Отправить файл" />
</form>');
	echo "</div>";
	echo "</div>";
	}
	
	if($send)
	{
		echo "Уведомление: оценки установлены";
	}
	
	if($save)
	{
		echo "Уведомление: Данные сохранены.";
	}
	
 ?>
 
 </div>