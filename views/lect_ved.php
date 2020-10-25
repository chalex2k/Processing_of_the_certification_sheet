<script src="../js/lect_ved.js"> </script>

<div class = 'lect-ved'>
	<div class='lect-ved-item'>
		<div class='ved-form'>
			<form method="POST" action="lect_ved.php">
				<div class = 'ved-form-item'>
				Семестр:
				</div>
				<div class = 'ved-form-item'>
					<select name = 'semester' size = '1'>
						<?php
						for ($i = 1; $i <= 9; $i++)
							echo("<option " . ($i==$semester? " selected " : " ") . " value = '$i'> $i </option>");
						?>
					</select>
				</div>
				<div class = 'ved-form-item'>
				Группа:
				</div>
				<div class = 'ved-form-item'>
					<select name = 'group' size = '1' >
						<?php
						for ($i = 1; $i <= 9; $i++)
							echo("<option " . ($i==$group? " selected " : " ") . " value = '$i'> $i </option>");
						?>
					</select>
				</div>
				<div class = 'ved-form-item'>
				Предмет:
				</div>
				<div class = 'ved-form-item'>
					<select name = 'subject' size = '1' >
					<?php foreach ($subjects as $key => $value)
					{
						echo "<option " ;
						if($subject == $value)
							echo "selected ";
						echo "value = " . $key . ">" . "$value" . " </option>";
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
		$cnt = count($list);
		echo ("<form method='POST' action='lect_ved.php'>
			<input type='hidden' name='count_of_students' id='count_of_students' value='$cnt'>
			<table class='ved'>");
				echo '<tr id="hat"><th id="subject">Студент</th><th>1</th><th>2</th><th>3</th></tr>';
				foreach ($list as $row) // если не все оценки стоят?
				{
					echo '<tr> <td id="subject">' . $row[0] -> initials . '</td>
						<td><input type="text" class="input-mark" name="att1" value="' . $row[1] -> value . '"></td>
						<td><input type="text" class="input-mark" name="att2" value="' . $row[2] -> value . '"></td>
						<td><input type="text" class="input-mark" name="att3" value="' . $row[3] -> value . '"></td>
						</tr>';		
				}	
	echo '
	</table>
	<div class="input-form" align="center">
		<input type="button" class = "save" onClick = "getdetails()"  name="save" value="Сохранить ведомость "/>
	</div>
	</form>
	 
	 </div>  
	 </div> </div>';

	echo '<div class= "input-form">
	<div class="ved-text">Импорт\Экспорт </div>';
	echo''    ;
	echo ('
		<form enctype="multipart/form-data" action="lect_ved.php" method="POST">
		<input type="button" class = "excel-btn" onClick = "getexcel()" name="load" value="Экспорт в excel"></button>   
		<!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
		<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
		<!-- Название элемента input определяет имя в массиве $_FILES -->
		| Загрузить из файла excel: <input name="user_file" type="file" class = "inputfile" />
		<input type="submit" name = "send" class = "excel-btn" value="Отправить файл" />
		</form>');
	echo '<div id="msg"></div>';
	echo "</div>";
	echo "</div>";
	}
	else{echo "<table class='ved'><tr> <td colspan='4'>Заполните форму справа</td> </tr></table>";}
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